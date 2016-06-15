<?php
require_once('helpers.php');
require_once('connections.php');

require_once('facebook-php-sdk-v4/src/Facebook/autoload.php');
require_once('google-api-php-client/src/Google/autoload.php');

session_start();

// First figure out who posted this.
// If it is a new user, get their info,
// figure out if they're authenticating
// through Facebook or Google, and add it
// all to the database.
if(isset($_SESSION['fb_access_token'])){
    // Get user info from facebook
    $userNode = get_fb_info();

    // Assign user email to a variable
    // so we know to skip this one if
    // we need to send notifications later.
    $user_email = $userNode['email'];
    $medium = "facebook";

} else if(isset($_SESSION['google_access_token'])){
    $userNode = get_google_info();
    $medium = "google";
} else {
    setcookie('message', 'ERROR: User Authentication Error', time() + 10, '/');
    header("Location: ".$page.'../list.php');
}

// Check if user is in database
$sql = "SELECT * FROM users WHERE name=?";
$query = $conn->prepare($sql);
$query->bindParam(1,$userNode['name'], PDO::PARAM_STR);
$query->execute();
$result = $query->fetchAll();


// Build query to update users
if(!$result && $medium == "facebook"){
    // User isn't in the database
    // so add it.
    $sql = "INSERT INTO users (name, email, facebook, facebook_id, google, google_id) VALUES (?, ?, ?, ?, ?, ?)";
    $facebookId = $userNode['id'];
    $facebook = 1;
    $google = 0;
    $googleId = 0;
} else if(!$result && $medium == "google") {
    $sql = "INSERT INTO users (name, email, facebook, facebook_id, google, google_id) VALUES (?, ?, ?, ?, ?, ?)";
    // User has been here before, but through gmail.
    $facebookId = 0;
    $facebook = 0;
    $google = 1;
    $googleId = $userNode['id'];
} else if($result != NULL){
    // User has been here before through both mediums
    $sql = "UPDATE users SET name=?, email=?, facebook=?, facebook_id=?, google=?, google_id=? WHERE user_id=?;";
    $facebook = $result['facebook'];
    $facebookId = $result['facebook_id'];
    $google = $result['google'];
    $googleId = $result['google_id'];
} else {
    // This case should never get hit.
    $sql = "UPDATE users SET name=?, email=?, facebook=?, facebook_id=?, google=?, google_id=? WHERE user_id=?;";
    $facebook = 0;
    $facebookId = 0;
    $google = 0;
    $googleId = 0;
}

// Bind parameters and run the query
$query = $conn->prepare($sql);
$query->bindParam(1, $userNode['name'], PDO::PARAM_STR);
$query->bindParam(2, $userNode['email'], PDO::PARAM_STR);
$query->bindParam(3, $facebook, PDO::PARAM_INT);
$query->bindParam(4, $facebookId, PDO::PARAM_STR);
$query->bindParam(5, $google, PDO::PARAM_INT);
$query->bindParam(6, $googleId, PDO::PARAM_INT);
if($result){
    $query->bindParam(7, $result['user_id'], PDO::PARAM_STR);
}
$query->execute();

// Now we can move on to actually posting
// the tree story.
$data = escape_html($_POST["data"]);

// Clean up the data that was submitted by the user.
$treestory = escape_html($_POST["treestory"]);

// Add image data
if($_POST["image-filepath"] != Null){
    $image_url = $_POST["image-filepath"];
}


// If a new user posted this, we need to get their id.
if(!$result['user_id']){
    $sql = "SELECT user_id FROM users WHERE name=?;";
    $query = $conn->prepare($sql);
    $query->bindParam(1,$userNode['name'], PDO::PARAM_STR);
    $query->execute();
    $created_by = $query->fetchAll()[0]['user_id'];
} else {
    $created_by = $result['user_id'];
}
// Remember who posted
$_SESSION["treestories_user_id"] = $created_by;

// Give the post a timestamp
$created_on = date("Y-m-d H:i:s");

// Parse out tree information
$this_tree = explode(', ', $data, 2)[0];
$tree_id = explode(' ', $this_tree, 2)[0];
$tree_location = explode(' ', $this_tree, 2)[1];

// Now form the querystring,
// bind the parameters, and
// execute the query.
if($treestory){
    $sql = "INSERT INTO posts (content, image_url, created_by, created_on, tree_id, tree_location) VALUES (?, ?, ?, ?, ?, ?)";
    $query = $conn->prepare($sql);
    $query->bindParam(1, $treestory, PDO::PARAM_STR);
    $query->bindParam(2, $image_url, PDO::PARAM_STR);
    $query->bindParam(3, $created_by, PDO::PARAM_STR);
    $query->bindParam(4, $created_on, PDO::PARAM_STR);
    $query->bindParam(5, $tree_id, PDO::PARAM_INT);
    $query->bindParam(6, $tree_location, PDO::PARAM_STR);
    try {
        $query->execute();
    } catch(PDOException $e){
        exit("Database Error: ".$e);
    }

    // Now we must send an update to all users whose tree is within
    // the same neighborhood as the tree that was just posted about.
    $sql = "SELECT DISTINCT u.email FROM users u, posts p WHERE (p.tree_location=? AND p.created_by=u.user_id) OR (u.is_admin=1)";
    $query = $conn->prepare($sql);
    $query->bindParam(1, $tree_location, PDO::PARAM_STR);
    try {
        $query->execute();
    } catch(PDOException $e) {
        exit("Database Error: ".$e);
    }
    $emails = $query->fetchAll();

    // Now lets notify the users
    // with trees in the area.
    foreach($emails as $email){
        if($email != $user_email){
            $subject = "A nearby tree now has a story!";
            $title = "Test Email";
            $message = "
                <html>
                <head>
                    <title>".$title."</title>
                </head>
                <body>
                <p>Hello!</p>
                <p>Someone just posted about a tree in your area. Here's what they had to say:</p>
                ";
            if(isset($image_url)){
		$message.="<img src='".rtrim($page, "/").BASE_DIR.$image_url."' alt='Image of tree' width='200px'></img>";
	    }
	    $message .= "<p>".$treestory."</p>
                </body>
                </html>
                ";
            $headers = "MIME-Version: 1.0" . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: ".$email_address."\r\n";
            mail($email['email'], $subject, $message, $headers);
        }
    }
} else {
    setcookie('message', 'ERROR: Cannot post empty story', time() + 10, '/');
}
// And redirect to the list page
header("Location: ".rtrim($page, "/").BASE_DIR.'list.php');

?>

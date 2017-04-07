<?php
session_start();
include('libs/helpers.php');
include_once('libs/connections.php');
include("libs/card.php");
include_once("libs/facebook-php-sdk-v4/src/Facebook/autoload.php");
include_once("libs/google-api-php-client/src/Google/autoload.php");


$title = "Posts";

// Define everything having to do with pagination.
$posts_per_page = 10;
$offset = 0;
$page_num = 0;
if(isset($_REQUEST['p'])){
    $page_num = $_REQUEST['p'];
}
$next = $page_num + 1;
$previous = $page_num - 1;
$offset = $page_num * $posts_per_page;

// Request header will hold onto query information between pages.
$request_header = "";


// Case 1: User is searching for something
if(isset($_REQUEST['searchform'])){
    $querystring = "%".escape_html($_REQUEST['searchform'])."%";

    $sql = "SELECT p.post_id, p.tree_id, p.tree_location, p.content, p.image_url, u.name, u.email, u.user_id, p.created_on, u.facebook, u.google FROM posts p, users u WHERE u.user_id = p.created_by";
    $sql .= " AND (p.content LIKE ? OR p.tree_location LIKE ? OR p.created_on LIKE ? OR u.name LIKE ?)";
    $sql .= " ORDER BY p.created_on DESC";
    $result = $conn->prepare($sql);
    $result->bindParam(1, $querystring, PDO::PARAM_STR);
    $result->bindParam(2, $querystring, PDO::PARAM_STR);
    $result->bindParam(3, $querystring, PDO::PARAM_STR);
    $result->bindParam(4, $querystring, PDO::PARAM_STR);

    $request_header = "&searchform=".escape_html($_REQUEST['searchform']);

// Case 2: User is just navigating to the page
} else {
    // Make the sql query
    $sql = "SELECT p.post_id, p.tree_id, p.tree_location, p.content, p.image_url, u.name, u.email, u.user_id, p.created_on, u.facebook, u.google FROM posts p, users u WHERE u.user_id = p.created_by";
    $order =  " ORDER BY p.created_on DESC";
    // Handle cases where search criteria is passed into form via requests.
    if(isset($_GET['location']) && isset($_GET['id'])){
        $sql .= " AND (p.tree_location LIKE ? AND p.tree_id LIKE ?)";
        $sql .= $order;
        $result = $conn->prepare($sql);
        $result->bindParam(1, escape_html($_GET['location']), PDO::PARAM_STR);
        $result->bindParam(2, escape_html($_GET['id']), PDO::PARAM_STR);
    } else if(isset($_GET['location'])){
        $sql .= " AND (p.tree_location LIKE ?)";
        $sql .= $order;
        $result = $conn->prepare($sql);
        $result->bindParam(1, escape_html($_GET['location']), PDO::PARAM_STR);
    } else if(isset($_GET['id'])){
        $sql .= " AND (p.post_id LIKE ?)";
        $sql .= $order;
        $result = $conn->prepare($sql);
        $result->bindParam(1, escape_html($_GET['id']), PDO::PARAM_INT);
    } else {
        $sql .= $order;
        // Handle case where user is just navigating to the page.
        $result = $conn->prepare($sql);
    }
}

// Execute the query
$result->execute();
$outcome = $result->fetchAll();


$total_posts = sizeof($outcome);
$max_pages = ceil($total_posts/$posts_per_page);

$displayed = [];

for($i = $offset; $i < ($offset + $posts_per_page); $i++){
    if(isset($outcome[$i])){
        array_push($displayed, $outcome[$i]);
    }
}

messages();


if(check_login($conn, rtrim($page, "/").BASE_DIR.'list.php')){
    $logout = "<a href='".BASE_DIR."libs/logout.php' id='logout' class='btn btn-default pull-right'>Logout</a>";
} else {
    $logout = "<a href='".BASE_DIR."login.php' id='logout' class='btn btn-default pull-right'>Login</a>";
}

// Include the header
include('libs/light_header.php');
?>
<div id="list-background"></div>
<div id="list-container" class="container">
    <div class="row" id="list_header">
        <?php echo $admin_button.$logout; ?>
        <div class="col-md-12 text-center"><h1><a id="title-link" href="/"><?php echo $site_name; ?></a></h1><p/>
        <h4>What&rsquo;s new in your neck of the woods?</h4></div>
    </div>
    <div class="row" id="search">
        <div class="col-md-12">
            <form action="" class="form-inline" method="post">
                <input type="text" class="form-control" name="searchform" id="searchform" placeholder="Seach for a Tree Story">
                <div class="btn-group" id="search-buttons">
                    <button type="submit" class="btn btn-default">Search</button>
                    <a href='<?php echo BASE_DIR; ?>list.php' class='btn btn-warning'>Clear</a>
                    <a href="<?php echo BASE_DIR; ?>" class="btn btn-default" id="list_back_button">Back</a>
                </div>
            </form>
        </div>
    </div>
    <div class="row" id="table-info"><div class="col-md-12">
    </div></div>
</div>
<div id="cards-container" class="container-fluid">
<?php
foreach($displayed as $row){
    addCard($row, $conn);
}
?>
</div>
<div id="page-buttons" class="text-center">
<?php
    if($previous >= 0){
        echo "<a href=".BASE_DIR."list.php?p=".$previous.$request_header."' class='btn btn-default'><span class='glyphicon glyphicon-arrow-left'></span></a>";
    }
    if($next < $max_pages){
        echo " <a href=".BASE_DIR."list.php?p=".$next.$request_header."' class='btn btn-default'><span class='glyphicon glyphicon-arrow-right'></span></a>";
    }
?>
</div>
<script src="<?php echo BASE_DIR; ?>js/list-ui.js" type="text/javascript"></script>
</body>
</html>

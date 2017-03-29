<?php session_start(); ?>
<div id="popup-form">
<?php
require_once('helpers.php');
require_once('connections.php');
require_once('facebook-php-sdk-v4/src/Facebook/autoload.php');
require_once('google-api-php-client/autoload.php');

$page = rtrim($page, "/");

// Facebook
$fb = new Facebook\Facebook([
    'app_id' => $fb_app_id,
    'app_secret' => $fb_app_secret,
    'default_graph_version' => 'v2.5',
]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email'];

$loginUrl = $helper->getLoginUrl($page.BASE_DIR.'libs/fb-callback.php', $permissions);


// save the facebook info as a session variable since it seems to unset itself.
foreach($_SESSION as $k=>$v){
    if(strpos($k, "FBRLH_")!==FALSE){
	if(!setcookie($k, $v)){
	    //
	} else {
	    $_COOKIE[$k]=$v;
	}
    }
}

// Google
$client = new Google_Client();
$client->setApplicationName("Tree Stories Client");
$client->setDeveloperKey($google_api_key);
$client->setClientId($google_app_id);
$client->setClientSecret($google_app_secret);
$client->setRedirectUri($page.BASE_DIR."libs/google-callback.php");
$client->setScopes(array("https://www.googleapis.com/auth/urlshortener", 'email', 'profile'));

$service = new Google_Service_Urlshortener($client);

if(isset($_REQUEST['logout'])){
    unset($_SESSION['google_access_token']);
}

if(isset($_SESSION['google_access_token']) && $_SESSION['google_access_token']){
    $client->setAccessToken($_SESSION['google_access_token']);
} else {
    $authUrl = $client->createAuthUrl();
}

if($client->getAccessToken() && isset($_GET['url'])){
    $url = new Google_Service_Urlshortener_Url();
    $url->longUrl = $_GET['url'];
    $short = $service->url->insert($url);
    $_SESSION['google_access_token'] = $client->getAccessToken();
}

if(strpos($google_app_id, "googleusercontent") == false){
    echo missingClientSecretsWarning();
    exit;
}
?>

<div class="box">
    <div class="request">
<?php

// Google login
if(isset($authUrl)){
    echo "<a class='login' href='" . $authUrl . "'><img src='".$page.BASE_DIR."pictures/google-button.png'></img></a>";
} else {
  echo '
    <form id="url" method="GET" action="{$_SERVER["PHP_SELF"]}">
      <input name="url" class="url" type="text">
      <input type="submit" value="Shorten">
    </form>
    <a class="logout" href="?logout">Logout</a>';
}
// Facebook login
echo '<a href="'.escape_html($loginUrl).'"><img src="'.$page.BASE_DIR.'pictures/facebook-button.png"></img></a>';

?>
</div>
</div>

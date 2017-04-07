<?php
/*
 * Database & server connection variables
 * are defined here.
 *
 */

// Define a ROOT filepath
define('ROOT', realpath(__DIR__ . "/../"));

set_include_path(get_include_path() . PATH_SEPARATOR . ROOT);

// Define the facebook directory (for the php API)
define('FACEBOOK_SDK_V4_SRC_DIR', ROOT.'/libs/facebook-php-sdk-v4/src/Facebook/');

if(!file_exists(ini_get('session.save_path'))){
    mkdir(ini_get('session.save_path'), 0777, true);
}

// Parse the config file for database credentials
$_config = parse_ini_file(ROOT ."/config.ini");

date_default_timezone_set('America/Los_Angeles');

// Get mySQL stuff
ini_set("mysql.allow_persistent", "Off");
$hostname = $_config['db_host'];
$database = $_config['db_name'];
$username = $_config['db_user'];
$password = $_config['db_password'];

// Get Facebook API stuff
define('FACEBOOK_APP_ID', $_config['fb_app_id']);

$fb_app_id = FACEBOOK_APP_ID;
define('FACEBOOK_APP_SECRET', $_config['fb_app_secret']);
define('FACEBOOK_ACCESS_TOKEN', $_config['fb_access_token']);
$fb_app_secret = FACEBOOK_APP_SECRET;
$fb_access_token = $_config['fb_access_token'];

$google_app_id = $_config['google_app_id'];
$google_app_secret = $_config['google_app_secret'];
$google_api_key = $_config['google_api_key'];

$site_name = $_config['site_name'];

// Create a PDO object for database connections.
$conn = new PDO("mysql:host=$hostname;dbname=$database;charset=utf8", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

define('CONN', $conn);

// Create a variable to handle http/https urls.
$page = "http" . ($_SERVER["HTTPS"] == "on" ? "s": "") . "://" . $_SERVER['HTTP_HOST'] . '/';

$email_address = $_config['email_address'];

$base_dir = $_config['base_dir'];
define('BASE_DIR', $base_dir);

?>

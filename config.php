<?php

/**********************
 * Framework Settings *
 **********************/

/**
 * Do not edit this part unless you know what you're doing
 */

/**
 * Include user's custom config file
 */
include("config.user.php");


/**
 * If config.user.php is missing, apply the following defaults 
 */

if(!isset($config['website']['is_online'])) $config['website']['is_online'] = true;
if(!isset($config['website']['display_credits'])) $config['website']['display_credits'] = false;
if(!isset($config['website']['display_stats'])) $config['website']['display_stats'] = false;
if(!isset($config['DEBUG']['enabled'])) $config['DEBUG']['enabled'] = false; 
if(!isset($config['controller']['loadLastKnownController'])) $config['controller']['loadLastKnownController'] = true;
if(!isset($config['security']['skipLocalChecks'])) $config['security']['skipLocalChecks'] = false;
if(!isset($config['security']['displayExplicitErrors'])) $config['security']['displayExplicitErrors'] = false;
if(!defined('ROOT_DIR')) define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT'] . '/');

/**
 * Version numbers
 */
$config['framework']['version'] = 0.33;
$config['framework']['branch'] = "beta";
$config['framework']['PHP_version_required'] = "5.3";

/**
 * Non-configurable Security settings
 */
$config['security']['allowed_characters']['request_uri'] = "/[^A-Za-z0-9\.\/?=&-]/";
$config['security']['allowed_characters']['inputs'] = "/[^A-Za-z0-9]/";


/**
 * Path definitions
 */
define("FRAMEWORK_DIR", ROOT_DIR . "framework/");
define("USER_DIR", ROOT_DIR . "user/");

define("FUNCTIONS_DIR", FRAMEWORK_DIR . "functions/");
define("CONTROLLERS_DIR", USER_DIR . "website/");
define("CLASSES_DIR", FRAMEWORK_DIR . "core/");
define("HTML_DIR", USER_DIR . "html/");
define("APPLICATION_DIR", USER_DIR . "application/");



/**
 * Filetypes definition
 */
$config['HTML']['filetypes']=array(
	"css" 	=> "text/css",
	"js"	=> "application/javascript",
	"csv"	=> "text/csv",
	"svg"	=> "image/svg+xml");
	// "ico"	=> "image/vnd.microsoft.icon",
	// "gif"	=> "image/gif",
	// "jpeg"	=> "image/jpeg",
	// "jpg"	=> "image/jpeg", 
	// "png"	=> "image/png",
	// "tiff"	=> "image/tiff");


/**
 * error messages
 */
$config['errors']['http']["404"] = "Page not found";

$config['errors']['framework']['10'] = "You must specify a template in config.php";
$config['errors']['framework']['11'] =  "Specified template doesn't exist !";
$config['errors']['framework']['12'] = "Specified template can't be read !";
$config['errors']['framework']['100'] = "Selected database driver wasn't found. Available drivers are : ";
$config['errors']['framework']['404'] = "Controller not found";
$config['errors']['framework']['501'] = "Unable to load controller";
$config['errors']['framework']['502'] = "Exception thrown in loaded controller";
$config['errors']['framework']['503'] = "Exception thrown by database";

?>

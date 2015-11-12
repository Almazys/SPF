<?phP

session_start();

/**
 * Loads global configuration
 */
require_once("config.php");

/**
 * Managing includes
 */
foreach(glob(CLASSES_DIR . "*.class.php") as $filename)
	require_once($filename);
foreach(glob(FUNCTIONS_DIR . "*.php") as $filename)
	require_once($filename);

set_include_path(APPLICATION_DIR);

/**
 * If site is set as offline in config.php, display an error page
 */
if ($GLOBALS['config']['website']['is_online']===false)
	Site::error(Site::app_error, "Website offline", "The requested website is currently offline. Please try again later.");


/**
 * If the running php version is too old, stop here
 */
if (version_compare(phpversion(), $GLOBALS['config']['framework']['PHP_version_required'], "<"))
	Site::error(Site::app_error, "PHP Version error", "Your version of PHP is too old : " . PHP_VERSION . "<br />The required version is : " . $GLOBALS['config']['framework']['PHP_version_required']);


/**
 * Managing Debug
 */
if(isset($GLOBALS['config']['DEBUG']['enabled']) && $GLOBALS['config']['DEBUG']['enabled'] == true)
	Debug::build();



/**
 * Security checks
 * Keep in mind it just does a BASIC check on world-permission on files/folders under DOCUMENT_ROOT ; and a quick check on php.ini
 */

if($GLOBALS['config']['security']['skipLocalChecks']===false) {

	//Setup
	CoreController::startCapturing();

	$successful_Check = true;
	

	/**
	 * php.ini config
	 */
	echo "<u>Checking php.ini file ...</u><br />";
	php_iniChecks();


	echo "******************<br />";


	/**
	 * File rights
	 */
	echo "<u>Checking file permissions ...</u><br />";
	localSecurityChecks(ROOT_DIR); // Can be found in FUNCTIONS_DIR . security.php


	//Cleaning
	$output = CoreController::stopCapturing();

	if($GLOBALS["successful_Check"]===false)
		Site::error(Site::app_error, "Some misconfiguration were detected. <br />Please fix them in order to run this framework safely", $output);

	unset($successful_Check, $checks);


}

?>

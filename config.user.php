<?php

/*******************
 * User's Settings *
 *******************/

/**
 * Sets website prefs
 */
$config['website']['is_online'] = true; // If you need to put your website offline for some time, put this to false.
$config['website']['name'] = "MyWebsite"; // Name of your website
$config['website']['version'] = 1.0; // Version of your website. Will be display on bottom of your pages if display_credits is on
$config['website']['branch'] = "RC"; // Branch of your website. Will be display on bottom of your pages if display_credits is on
$config['website']['display_credits'] = true; // Displays credits on the bottom of all pages. (Can break the DOM)
$config['website']['display_stats'] = true; // Displays stats (number of SQL requests, time of generation) on the bottom of all pages. (Can break the DOM)



/**
 * DEBUG
 */
$config['DEBUG']['enabled'] = false; // Enables the debug mode. Breaks the DOM by adding usefull information in bottom of all pages. 
$config['DEBUG']['verbosity'] = 2; //0 (very little verbose), 1(moderate) or 2(big)
$config['DEBUG']['out'] = "both"; // can be 'stdout', 'file', or 'both'
$config['DEBUG']['outfile'] = "/opt/lampp/temp/website.debug"; // absolute path to file (used if out is 'file' or 'both')


/**
 * Logging
 */
$config['log']['file'] = "/opt/lampp/temp/website.log"; // absolute path to logfile

/**
 * Database credentials
 */
$config['bdd']['driver'] = "mysql"; // poo drivers installed with PHP 
$config['bdd']['hostname'] = "localhost"; // address of database
$config['bdd']['database'] = "example"; // name of db
$config['bdd']['username'] = "root"; // user to access db ...
$config['bdd']['password'] = ""; // ... and its associated password
$config['bdd']['encoding'] = "UTF8"; // charset encoding

/**
 * Options
 */
$config['controller']['loadLastKnownController'] = true; //if true, calling /section/blog/2015-01-23/ when only blog exists, will load blog.class.php
$config['security']['skipLocalChecks'] = true; // False : more security


/**
 * Security settings
 */
$config['security']['displayExplicitErrors'] = true; // If false, display generic error, to prevent leakage


/**
 * Paths definitions
 */
define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT'] . '/');


/**
 * default HTML template
 */
$config['HTML']['template']="example_template"; // name of HTML template



/*****************************
 * (common HTML ressources)  *
 * it can be a simple string *
 * or an array               *
 *****************************/

$config['HTML']['content']['GLOBAL TITLE'] = "##HOME PAGE##";
$config['HTML']['content']['MAIN MENU ITEM'][1] = array(
	"section" 	=> '/',
	"meta"		=> 'class="current-page-item"',
	"text" 		=> '##TITLE 1##');
$config['HTML']['content']['MAIN MENU ITEM'][2] = array(
	"section" 	=> '/manage', 
	"meta"		=> 'class="current-page-item"',
	"text" 		=> '##TITLE 2##');
$config['HTML']['content']['MAIN MENU ITEM'][3] = array(
	"section" 	=> '/contact',
	"meta"		=> 'class="current-page-item"',
	"text" 		=> '##TITLE 3##');
$config['HTML']['content']['SITE CREDITS'] = 'Some people | people@somepeople.com';


?>

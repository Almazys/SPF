# SPF
Simple PHP Framework that does (tries at least) the job

The aim of SPF is to let developers create their websites in a very fast and efficient way. Here are some of the main features :

	- basic HTML template manager
	- URL customization
	- Default security settings
	- Debug mode
	- Logging facilities
	- ...


***************************
I) How does all this work ?
***************************

With the provided .htaccess (your presentation server needs to take it into account), every request will be caught by index.php, at the DOCUMENT_ROOT of the web server.
The framework will sanitize inputs (get, post, session, cookies), parse the request, and load your specific controller corresponding to the requested URL.


***************************
II) Setup
***************************

The framework configuration is split into two separated config files : 

	- config.php 		(do not touch unless you know what you're doing, framework declarations and settings)
	- config.user.php 	(User configuration, where you can setup your preferences)

Basically, you have two things to do : 

	- Set the values you want in config.user.php
	- Put a HTML template in the HTML_DIR (but we'll come to it later on.)

Since these two operations are done, use the script 'create.py' to create a simple controller, and you're gone. 

CONTROLLERS_DIR (/website by default) is the place to put all your controllers. For instance, if a controller named Test.class.php (with a class Test in it ; inherited from WebController) is placed into this folder, it will be executed through the URI /text


**********************
III) config.user.php
**********************

The file config.user.php will by default contain the following elements : 

```php
/*******************
 * User's Settings *
 *******************/

/**
 * Sets website prefs
 */
$config['website']['is_online'] // If you need to put your website offline for some time, put this to false.
$config['website']['name'] // Name of your website
$config['website']['version'] // Version of your website. Will be display on bottom of your pages if display_credits is on
$config['website']['branch'] // Branch of your website. Will be display on bottom of your pages if display_credits is on
$config['website']['display_credits'] // Displays credits on the bottom of all pages. (Can break the DOM)
$config['website']['display_stats'] // Displays stats (number of SQL requests, time of generation) on the bottom of all pages. (Can break the DOM)



/**
 * DEBUG
 */
$config['DEBUG']['enabled'] // Enables the debug mode. Breaks the DOM by adding usefull information in bottom of all pages. 
$config['DEBUG']['verbosity'] //0 (very little verbose), 1(moderate) or 2(big)
$config['DEBUG']['out'] // can be 'stdout', 'file', or 'both'
$config['DEBUG']['outfile'] // absolute path to file (used if out is 'file' or 'both')


/**
 * Logging
 */
$config['log']['file'] // absolute path to logfile

/**
 * Database credentials
 */
$config['bdd']['driver'] // can be mysql, pgsql, or sqlite for example. 
$config['bdd']['hostname'] // address of database
$config['bdd']['database'] // name of db
$config['bdd']['username'] // user to access db ...
$config['bdd']['password'] // ... and its associated password


/**
 * Options
 */
$config['controller']['loadLastKnownController'] //if true, calling /section/blog/2015-01-23/ when only blog exists, will load blog.class.php
$config['security']['skipLocalChecks'] // False : more security


/**
 * Security settings
 */
$config['security']['displayExplicitErrors'] // If false, display generic error, to prevent leakage


/**
 * Paths definitions
 */
define("DIR_OFFSET", "/"); // If your website is under a subfolder in DOCUMENT_ROOT, specify it here. Else, just put '/'.


/**
 * default HTML template
 */
$config['HTML']['template'] // name of HTML template



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

```


**********************
IV) Template definition
**********************

Template has to be defined into config.user.php, under the line  

```php
$config['HTML']['template']="example_template"; 
```

where example_template will be your folder under /html/.

If you don't specify any other information in your controller, the framework will search for a 'default template file' in /html/example_template/example_template.template
Here, a template is an HTML file, with some specific characters strings, like %%MY PATTERN%% or ##MY TEXT##.




***************
V) How to use your controllers ? 
***************

TODO : hierarchie
TOOD: change script c
todo : mettre dans chaque controller 


***************
VI) What to do in our controllers ? 
***************

In your controllers, you'll be able to replace patterns with strings, to change the HTML layout to another specific template, or to XXXTODOXXX

_a) Specifying a template to use_

You can specify a particular template to use in your controller, by using :

```php
$this->view->setTemplate("other_template");
```

With this line, framework will search for the file : /html/example_template/other_template.template


_b) Replacing simple Strings_

The simpliest example is to replace a simple string ...:
in index.template : 

```html
<h1>%%WEBSITE TITLE%%</h1>
```

In config.user.php : 

```php
$config['HTML']['content']['WEBSITE TITLE'] = "My super website";
```

will give a important title containing "My Super website". 

Another option is to set this string in your controller : 
in index.template : 

```
%%IDENTIFIER%%
```

in controller : 

```php
$this->view->setContent('IDENTIFIER', "<p>My paragraph</p>");
```


_c) Replacing lists_

You might want to have a list of link like this : 

```html
<a href="http://www.link1.com">My Link 1</a>
<a href="http://www.link2.com">My Link 2</a>
<a href="http://www.link3.com">My Link 3</a>
```

You can make one big array LINKS :

```php
$config['HTML']['content']['LINKS'][1] = array(
	"section"	=> 'href="http://www.link1.com"',
	"text" 		=> 'My Link 1');

$config['HTML']['content']['LINKS'][2] = array(
	"section"	=> 'href="http://www.link2.com"',
	"text" 		=> 'My Link 2');

$config['HTML']['content']['LINKS'][3] = array(
	"section"	=> 'href="http://www.link3.com"',
	"text" 		=> 'My Link 3');
```

in template, this is what it looks like :

```html
<a %%LINK FOR LINKS 1%%>%%LINKS 1%%</a>
<a %%LINK FOR LINKS 1%%>%%LINKS 2%%</a>
<a %%LINK FOR LINKS 1%%>%%LINKS 3%%</a>
```


_d) Replacing code_

If you don't wanna mess with javascript, you can also add some HTML code depending on the page requested. Let's imagine you have this kind of HTML code : 

```html
<a class="current-page-item" href="/">Home</a>
```

Here, 3 things will have to be dynamic : 

	- class="current-page-item"
	- href="/"
	- Home

To tell the framework what to do, you'll have to create an array in config.user.php.

in index.template : 

```html
<a href="%%LINK FOR MAIN MENU ITEM%%" %%META FOR MAIN MENU ITEM%%>%%MAIN MENU ITEM%%</a>
```

in config.user.php :

```php
$config['HTML']['content']['MAIN MENU ITEM'] = array(
	"section" 	=> '/blog/mypage', //tells the link to the ressource
	"meta"		=> 'class="current-page-item"', // meta information that can be class, id, style, or anything you want to be applied when on the URL defined just before
	"text" 		=> 'Home'); // text to display
```

In this example, 'MAIN MENU ITEM' => "Home", and if the current URL is /blog/mypage, then the class="current-page-item" is applied in the DOM. Note that the framework makes the match through the prefix LINK FOR and META FOR.


_e) Replacing main text through controller_

By default, if you echo some text in your controller, it will be inserted in the template, and replace the special %%@MAIN CONTENT%% tag.
So in your template, put %%@MAIN CONTENT%% somewhere, and just echo some text strings in your controller.

****************
VII) Locale files
****************

You can put text to repl@ace into the global.locale file in HTML_DIR (/html/ by default).
Any text in the HTML template that is under this form : ##IDENTIFIER## will be replaced with the corresponding string in the .locale file

HTML File : 

```
##IDENTIFIER##
```

global.locale :

```
IDENTIFIER; This is a text example
```



Remember that this kind of text is replaced LAST, so you can setup something like this : 


HTML File : 

```
%%IDENTIFIER%%
```

config.php

```php
$config['HTML']['content']['IDENTIFIER'] = "##HOME PAGE##";
```


global.locale :

```
HOME PAGE; This is my home page
```


****************
VIII) Debugging and logging
****************

The debug mod can be useful in the following cases :

	- Some HTML patterns in your template haven't been replaced
	- URI rewriting isn't doing good, you don't know what is really taken into account
	- Execution thread isn't finishing, or some things are strange

When enabled, some strings will be added at the end of your webpages, giving you some elements that can help you debug.
You can also manually add some debug string in your controllers, by calling :

```php
Debug::write("Your message", 0)
```

where 0 stands for high priority. (0 : high, 1 : medium, 2 : low). The verbosity can be configured in config.user.php

SPF also contains a logging tool, to log any information you want. In the same way Debug work, you can call : 


```php
Site::log("This is a message i'd like to log")
```

and a file containing this string will be written. (the path to this file can be configured in config.user.php)

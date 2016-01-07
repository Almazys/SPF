<?php

class Site {

	/**
	 * URI parsing
	 * @var [string] $request 	[contains filtered REQUEST_URI]
	 * @var [array] $sections 	[contains categories]
	 * @var [string] $page 		[contains requested page name]
	 * @var [array] $get 	[contains filtered _GET]
	 * @var [array] $post 	[contains filtered _POST]
	 */
	protected static $request;
	protected static $sections;
	protected static $get;
	protected static $post;

	protected static $path_to_controller;			//	/path/to/section/
	protected static $controller_file_to_load;		//	Controller.class.php
	protected static $controller_name_to_load;		//	Controller

	/**
	 * enums for error() 
	 */
	const HTTP_error = 0;
	const app_error = 1;
	const site_error = 2;

	public function __construct() {
		Debug::write("Building website ...", 1);
		$this->sanitizeInputs();
		$this->parseURL();
		$this->autoLoad();
		$this->loadController();
	}

	/**
	 * [remove all unwanted characters from request and args]
	 * @return [void]
	 */
	protected function sanitizeInputs() {
		/**
		 * GET & POST
		 * TODO: improve ?
		 */
		foreach($_POST as &$post) {
			$key = preg_replace($GLOBALS['config']['security']['allowed_characters']['inputs'],"",array_search($post, $_POST));
			self::$post[$key] = preg_replace($GLOBALS['config']['security']['allowed_characters']['inputs'],"",urldecode($post));
			unset($_POST[array_search($post, $_POST)]);
		}
		foreach($_GET as &$get) {
			$key = preg_replace($GLOBALS['config']['security']['allowed_characters']['inputs'],"",array_search($get, $_GET));
			self::$get[$key] = preg_replace($GLOBALS['config']['security']['allowed_characters']['inputs'],"",urldecode($get));
			unset($_GET[array_search($get, $_GET)]);
		}

		//TODO : sanitize cookie & session

		/**
		 * Deleting $_REQUEST variables : elements are already in self::$get & self::$post 
		 */
		foreach($_REQUEST as &$req)
			unset($_REQUEST[array_search($req, $_REQUEST)]);

		/**
		 * REQUEST_URI
		 */
		self::$request = preg_replace($GLOBALS['config']['security']['allowed_characters']['request_uri'],"",urldecode($_SERVER['REQUEST_URI']));
		unset($_SERVER['REQUEST_URI']);
		Debug::write("Request_URI requested (after sanitizing) : '" . self::$request . "'  from IP : '" . $_SERVER["HTTP_HOST"] . "'",0);
	}

	/**
	 * [setting self::$sections]
	 * @return [void]
	 */
	protected function parseURL() {
		$request = explode("?", preg_replace('/^\//', '', self::$request));
		self::$sections = array_filter(explode("/", array_shift($request)));

		if(defined('BASE_URL') && BASE_URL !== "" && BASE_URL !== "/") { 
            		$_baseURL = array_filter(explode('/', BASE_URL)); 
            		foreach ($_baseURL as $index => $element) { 
                	if(self::$sections[$index-1] === $element) 
             		       	unset(self::$sections[$index-1]); 
            		} 
        	} 
	}

	/**
	 * [auto-includes files located in APPLICATION_DIR]
	 * @return [type] [description]
	 */
	protected function autoLoad() {
		if(!isset($GLOBALS['config']['PHP']['includes']) || !is_array($GLOBALS['config']['PHP']['includes']))
			return;
		foreach ($GLOBALS['config']['PHP']['includes'] as $key)	{
			if(file_exists(APPLICATION_DIR . $key))
				include_once(APPLICATION_DIR . $key);
			else
				Debug::write("Autoload of file " . APPLICATION_DIR . $key . " failed.",0);
		}
	}

	/**
	 * [Load correct controller from sections]
	 * @return [void]
	 */
	protected function loadController() {

		/**
		 * First, if it's an embed HTML request (js, css, others...), 
		 * then load directly requested file, not a controller.
		 */
		
		preg_match_all("/\.[^\.]*$/", (empty(self::$sections) ? "index.php" : array_reverse(self::$sections)[0]), $file_Extention);

		if(!empty($file_Extention[0]) && !in_array(str_replace(".", "", $file_Extention[0][0]), array('php', 'html', 'phtml', '.locale', '.template'))) {

			//Logging request
			self::log(date("Y/m/d-H:i:s") . " - Ressrce request from " . $_SERVER['HTTP_HOST'] . " - " . Site::getRequest());

			$sections = self::$sections;

			// UGLY Label ... To improve later with a dedicated recursive function
			LABEL_loadController_ReduceSectionsByOne: //TODO : PHP<5.3 ?
			$fileToLoad = HTML_DIR . $GLOBALS['config']['HTML']['template'] . "/";

			foreach( $sections as $section ) {
				if(empty($section))
					continue;
				if (is_dir($fileToLoad . $section))
					$fileToLoad .= $section . "/";
				elseif (is_file($fileToLoad . $section) && $section == (empty(self::$sections) ? "index.php" : array_reverse(self::$sections)[0])) {
					$fileToLoad .= $section;
					break;
				}
			}

			if(file_exists($fileToLoad) && is_file($fileToLoad)) {
				$contentType = array_pop(explode('.', self::$request));

				if(array_key_exists(strtolower($contentType), $GLOBALS['config']['HTML']['filetypes']))
					header("Content-type: " . $GLOBALS['config']['HTML']['filetypes'][strtolower($contentType)]);
				else
					header("Content-type: " . finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fileToLoad));
				echo file_get_contents($fileToLoad);
			}
			elseif (count($sections)>1) {
				$sections = array_slice($sections, 1);
				goto LABEL_loadController_ReduceSectionsByOne;			
			}
			else
				Site::error(Site::HTTP_error, "404", $GLOBALS['config']['errors']['http']['404']);


		/** 
		 * Second, if the ressource requested isn't an html/css whatsoever file, 
		 * load the corresponding PHP controller
		 */
		} else {

			$this->findController();
			
			//Logging request
			self::log(date("Y/m/d-H:i:s") . " - Primary request from " . $_SERVER['HTTP_HOST'] . " - " . Site::getRequest());

			if(file_exists(self::$path_to_controller . self::$controller_file_to_load)) {
				
				/**
				 * Quick check on controller's syntax. 
				 * TODO : manage includes / requires
				 */
				$shell = shell_exec(PHP_BINDIR . '/php -l "' . self::$path_to_controller . self::$controller_file_to_load . '"');
				$error_msg = preg_replace("/Errors parsing.*$/", "", $shell, -1, $count);

				if($shell === NULL)
					Debug::write("PHP binary couldn't be found. Can't check requested controller.",0);
				else
					if($count > 0)
						Site::error(Site::app_error, "Syntax error in controller", trim($error_msg));


				/** 
				 * If syntax checks are ok, loads controller
				 */
				include(self::$path_to_controller . self::$controller_file_to_load); // TODO : check syntax

				try {
					$controller = new self::$controller_name_to_load(); // FIXME: PHP<5.3?		
				} catch (PDOException $e) {
					CoreController::stopCapturing();
					Site::error(Site::app_error, "Database error", 
                		($GLOBALS['config']['security']['displayExplicitErrors']===true ? $e->getMessage() : $GLOBALS['config']['errors']['framework']['503']));
					exit;

				/* TODO : do not catch general exceptions ? */
				} catch (Exception $e) {
					CoreController::stopCapturing();
					Site::error(Site::site_error, "Exception thrown in loaded controller", 
						($GLOBALS['config']['security']['displayExplicitErrors']===true ? $e->getMessage() : $GLOBALS['config']['errors']['framework']['502']));
					exit;
				}

				$controller->displayView();

			} else {
				$this->error(Site::app_error, "501", $GLOBALS['config']['errors']['framework']['501'] . " " . self::$controller_name_to_load);
			}
		}
	}



	/**
	 * [From request, find what controller file is concerned]
	 */
	protected function findController() {
		self::$path_to_controller = CONTROLLERS_DIR; // initialize path
		if(!empty(self::$sections)) {
			foreach(self::$sections as $section) {
				if(is_dir(self::$path_to_controller . strtolower($section))) {
					self::$path_to_controller .= strtolower($section) . "/";
				} elseif (file_exists(self::$path_to_controller . ucwords(strtolower($section)) . ".class.php")) {
					self::$controller_file_to_load= ucwords(strtolower($section)) . ".class.php";
				} else {
					if($GLOBALS['config']['controller']['loadLastKnownController']===false)
						Site::error(Site::HTTP_error, "404", $GLOBALS['config']['errors']['framework']['404']); 
					else
						break;
				}
			}
		}

		if(empty(self::$controller_file_to_load))
			self::$controller_file_to_load = "Index.class.php";

		//TODO : change .class.php to .php if not poo
		self::$controller_name_to_load = str_replace(".class.php", "", self::$controller_file_to_load);
	}


	/**
	 * [Displaying error]
	 * @param  [const] $_type		[HTTP or application]
	 * @param  [int] $_code			[HTTP or application error code]
	 * @param  [string] $_message	[error message]
	 * @return [void]
	 */
	public static function error($_type, $_title = null, $_message = null) {

		if($_type == Site::HTTP_error) { // vert
			$background_color = "#7FFF77";
			$border_color = "#0B9F0A";
		} elseif ($_type == Site::app_error) { //bleu
			$background_color = "#C7EDFF";
			$border_color = "#4CBDE8";
		} else { //rouge
			$background_color = "#FF7E7E"; 
			$border_color = "#FF0000";
		}

		echo '<html>
	<head>
		<title>Framework error</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="Framework error" />
		<meta name="keywords" content="error" />
	</head>
	<body>
	<table style="margin: 30px auto auto auto; border-radius: 5px; border: solid ' . $border_color . '; 3px">
		<tr style="background-color: ' . $background_color . ';"><th>' . $_title . '</th></tr>
		<tr><td>' . $_message . '</td></tr>
	</table>
	</body>';

		// } else {
		// 	//rouge
		// 	echo "Unknown error.";
		// }

		echo '</body>
</html>';
		exit;
		
	}


	public static function log($_msg) {
		if(!isset($GLOBALS['config']['log']['file']) || !file_exists($GLOBALS['config']['log']['file']))
			return;

		$file = fopen($GLOBALS['config']['log']['file'], 'a');

		if(!$file) {
			Debug::write("Couldn't log message : " . $_msg, 0);
			return;
		}

		fwrite($file, $_msg . PHP_EOL);

		fclose($file);
	}


	/**
	 * [get URL in an array)]
	 * @return [array] [sections to load]
	 */
	public static function getSections() {
		return self::$sections;
	}

	/**
	 * [ get section with an URL format]]
	 * @return [String] [URL formatted with sections like : news/topic/20150120 ]
	 */
	public static function getRequest() {
		// return "/" . implode("/", self::$sections);
		return self::$request;
	}

	/**
	 * [get static attribute get]
	 * @return [array] [get args]
	 */
	public static function get_GET() {
		return self::$get;
	}

	/**
	 * [get static attribute post]
	 * @return [array] [post args]
	 */
	public static function get_POST() {
		return self::$post;
	}

	/**
	 * [get post / get args in an aray]
	 * @return [array] [get and post args]
	 */
	public static function get_args() {
		$_["GET"] = Site::get_GET();
		$_["POST"] = Site::get_POST();

		$base_URL = str_replace(".class.php", "", strtolower(Site::get_Controller()));
		$_["URL"] = array_filter(explode("/", str_replace("/" . str_replace(CONTROLLERS_DIR, "", $base_URL), "", Site::getRequest())));

		return $_; 
	}

	/**
	 * [get full path to controller]
	 * @return [string] [/path/to/Controller.class.php]
	 */
	public static function get_Controller() {
		return self::$path_to_controller . self::$controller_file_to_load;
	}
}

?>

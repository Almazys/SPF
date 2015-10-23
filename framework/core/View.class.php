<?php

/**
 * vim /opt/lampp.old/backup/dezonage/core/view.class.php
 */


class View {
	
	/**
	 * [Contains the locale file to load]
	 * @var string
	 */
	protected $locale = "";

	/**
	 * [contains the content of the page to display]
	 * @var [string]
	 */
	private $content;
	private $template = "";

	private $items;

	public function __construct() {
		Debug::write("Building view ...", 1);
	}


	/**
	 * [Set a template from it's file name (without .template)]
	 * @param [string] $_name [filename of template without extention]
	 */
	public function setTemplate($_name) {
		$this->template = HTML_DIR . $GLOBALS['config']['HTML']['template'] . "/" . $_name . ".template";
	}

	/**
	 * [Set a locale file to load]
	 * @param [string] $_locale [filename of locale without extention]
	 */
	public function setLocale($_locale) {
		$this->locale = $_locale;
	}

	/**
	 * [Sets template's specific strings from associated .locale file]
	 * @param [type] $pattern     [description]
	 * This input is HTML free ('<p>Hello</p>' will be 'Hello')
	 */
	public function setLocaleContent($_locale_file) { 
		if(!is_readable(HTML_DIR  . $_locale_file . ".locale") || !file_exists(HTML_DIR  . $_locale_file . ".locale")) {
			Debug::write("Requested locale file " . $_locale_file ." can't be read !",0);
			return;
		}
		Debug::write('Opening locale file ' . HTML_DIR  . $_locale_file . '.locale', 1);
		$f = @fopen(HTML_DIR  . $_locale_file . ".locale", 'r');
		if(empty($f))
			Debug::write("Requested locale file " . $_locale_file ." can't be loaded !",0);


		//Search in locale file if there are strings to replace
		while (!feof($f)) {
        	$buffer = fgetss($f, 4096);

        	//End of file
        	if(strlen($buffer)===0)
        		continue;

        	// Comments on locale file
        	if (preg_match("#^//#", $buffer) > 0)
        		continue;
        	elseif (preg_match("/^#/", $buffer) > 0)
        		continue;
        	
        	//Invalid line
        	elseif(count(explode(";", $buffer))<2) {
        		Debug::write("Invalid line '" . $buffer . "' has been ignored. No ';' found", 1);
        		continue;
        	}

        	$parsed_line = explode(';', $buffer);
        	$key=array_shift($parsed_line);
        	$this->setContent("##" . $key . "##", preg_replace("#^" . $key . ";#", "", $buffer));
    	}
    	fclose($f);
	}


	/**
	 * [Loads preconfigured content from config file]
	 * $GLOBALS['config']['HTML']['content'] can load string or arrays
	 * if string, it's just text to replace in template
	 * if array, see README for more informations
	 */
	protected function replaceDefaultUserContent() {
		if(!isset($GLOBALS['config']['HTML']['content']) || empty($GLOBALS['config']['HTML']['content']))
			return;
		foreach($GLOBALS['config']['HTML']['content'] as $key => $value) {
			if(is_array($value)) {
				foreach($value as $subKey => $subValue) {
					if(is_array($subValue)) {
						$this->setContent("%%" . $key . " " . $subKey . "%%", $subValue['text']);
						if(array_key_exists("section", $subValue))
							$this->setContent("%%" . "LINK FOR " . $key . " " . $subKey . "%%", 
								$subValue['section']);
						
						if(array_key_exists("meta", $subValue))
							/* if current controller is Index (/), change it to '/RootDIR' before grep_matching */
							$this->setContent("%%" . "META FOR " . $key . " " . $subKey . "%%",
								(preg_match("#^" . ($subValue['section'] == "/" ? "/RootDIR" : $subValue['section']) . "#", (Site::get_Controller() == CONTROLLERS_DIR . 'Index.class.php' ? "/RootDIR" : Site::getRequest())) ? $subValue['meta'] : ""));

					}
					else
						$this->setContent("%%" . $key . " " . $subKey . "%%", $subValue);
				}
			}
			else
				$this->setcontent("%%" . $key . "%%", $value);
		}

		$this->replaceAllContent();
	}


	/**
	 * [Put an entry into the queue ($this->items) to replace it later in the HTML code]
	 * @param [string] $pattern
	 * @param [string] $replacement
	 */
	public function setContent($pattern, $replacement) {
		$this->items[$pattern] = $replacement;
	}

	/**
	 * [replace all items in queue from $this->items into HTML content]
	 * TODO : alert with debug when a pattern isn't found 
	 */
	public function replaceAllContent() {
		if(!isset($this->items) || empty($this->items))
			return;
		foreach ($this->items as $pattern => $replacement) {
			$this->content = preg_replace("/" . $pattern . "/", $replacement, $this->content);
			unset($this->items[$pattern]);
		}
	}

	/**
	 * [A kind of destructor.
	 *  loads template, replace patterns and display the content to the web browser]
	 */
	public function display() {

		if(empty($this->template)) {
			if(!isset($GLOBALS['config']['HTML']['template']) || empty($GLOBALS['config']['HTML']['template']))
				Site::error(Site::app_error, "10", $GLOBALS['config']['errors']['framework']['10']);
			elseif(!is_dir(HTML_DIR . $GLOBALS['config']['HTML']['template']))
				Site::error(Site::app_error, "11", $GLOBALS['config']['errors']['framework']['11']);

			$this->template = HTML_DIR . $GLOBALS['config']['HTML']['template'] . "/" . $GLOBALS['config']['HTML']['template'] . ".template";
		}

		if(is_readable($this->template))
			$this->content = file_get_contents($this->template);
		else
			Site::error(Site::app_error, "12", $GLOBALS['config']['errors']['framework']['12']);

		if(empty($this->items))
			Debug::write("No pattern to be replaced were found in this template !", 0);

		$this->replaceDefaultUserContent();	
		
		$this->setLocaleContent("global");
		if($this->locale)
			$this->setLocaleContent($this->locale);

		$this->replaceAllContent();



		/**
		 * look for unreplaced patterns
		 */
		preg_match_all("/%%[^%]*%%/", $this->content, $ressources_config_file);
		preg_match_all("/##[^#]*##/", $this->content, $ressources_locale);

		$unreplaced_patterns = array_merge($ressources_locale[0], $ressources_config_file[0]);

		if(!empty($unreplaced_patterns)) {
			foreach($unreplaced_patterns as $pattern) {
				$current = "'" . $pattern . "' ";
				$msg = (empty($msg) ? $current : $msg . $current);
				$this->setContent($pattern, "");
			}
			$msg = preg_replace("/%/", "", $msg);
			Debug::write("One or several HTML field hasn't been replaced : " . htmlentities($msg), 0);

			$this->replaceAllContent(); //delete unreplaced patterns from HTML template

		}

		echo $this->content;
	}
}

?>

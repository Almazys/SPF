<?php

abstract class CoreController {

	/**
	 * Detect instance 
	 * @var [object] 
	 */
	protected static $instance;

	/**
	 * [containing the view]
	 * @var [View]
	 */
	protected static $view = null; //contains the view
	
	/**
	 * [containing the database]
	 * @var [Database]
	 */
	protected static $db = null;

	/**
	 * [containing automaticaly loaded classes
	 * @var [array]
	 */
	protected $classes = array();

	/**
	 * Specify a main work function for childs
	 */
	abstract public function work();


	public function __construct() {
		Debug::write("Building controller " . get_class($this) . " ...", 0);
		self::$instance = $this;
		
		self::$view = new View();

		//TODO: move check to Database.class.php
		if(isset($GLOBALS['config']['bdd']['hostname']) && !empty($GLOBALS['config']['bdd']['hostname']))
			self::$db = new Database();

		foreach ($GLOBALS['config']['PHP']['includes'] as $key => $value) {	
			require_once($key);
			if(!empty($value))
				foreach ($value as $class)
					$this->classes[$key] = new $class();
		}

		/**
		 * start capturating controller's output
		 */
		self::startCapturing();

	}

	public static function startCapturing() {
		ob_start();
	}

	public static function stopCapturing() {
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * [Kind of controller's destructor. Called when child's work is done]
	 * @return [type] [description]
	 */
	public function displayView() {
		$output = self::stopCapturing();

		if(strlen($output)===0)
			Debug::write("No output from controller was detected. No additionnal action will be done on HTML content", 0);
		else
			self::$view->setContent("%%@MAIN CONTENT%%", $output);
		
		self::$view->display();

		/**
		 * Breaking the DOM for credits or stats
		 */
		if($GLOBALS['config']['website']['display_credits'] === true || $GLOBALS['config']['website']['display_stats'] === true) {
			echo '<div style="border-top: 1px ridge black;"><p style="text-align:center;font-size:12px;">';
			if($GLOBALS['config']['website']['display_credits'] === true)
				echo $GLOBALS['config']['website']['name'] . ' v' . (is_numeric($GLOBALS['config']['website']['version']) ? number_format($GLOBALS['config']['website']['version'], 1) : $GLOBALS['config']['website']['version']) . ' ' . $GLOBALS['config']['website']['branch'] 
			. ', powered by SPF v' . (is_numeric($GLOBALS['config']['framework']['version']) ? number_format($GLOBALS['config']['framework']['version'],1) : $GLOBALS['config']['framework']['version']) . ' ' . $GLOBALS['config']['framework']['branch'] . '<br />';
			if($GLOBALS['config']['website']['display_stats'] === true) {
				$time = round(Timer::getTimeFrom("Start"), 5);

				if($time >= 1) {
					$unit = 's';
					$time = round($time, 1);
				} else {
					$unit = 'ms';
					$time = round($time * 1000, 5);
				}
				if(self::$db !== null)
					echo 'Number of SQL requests : ' . self::$db->getStats() . ' - Page generated in ' . $time . $unit . '<br />';
			}
			echo '</p></div>';
		}
	}

	/**
	 * [Returns instance of class if exists or NULL]
	 * @return [object] [Controller or NULL]
	 */
	public static function getInstance() {
		return self::$instance;
	}

	/**
	 * [Returns current instance of view if exists or NULL]
	 * @return [object] [view or NULL]
	 */
	public static function getView() {
		return self::$view;
	}

	/**
	 * [Returns instance of database if exists or NULL]
	 * @return [object] [DB or NULL]
	 */
	public static function getDB() {
		return self::$db;
	}	
	
}

?>

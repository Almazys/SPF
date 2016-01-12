<?php

class Debug {
	protected static $enabled;
	protected static $out;
	protected static $outfile;
	protected static $verbosity; //0 : Website functionnalities, 1 : global technical debug, 2 : technical details

	protected static $content = array();

	public static function build() {
		self::$enabled = $GLOBALS['config']['DEBUG']['enabled'];
		self::$out = $GLOBALS['config']['DEBUG']['out'];
		self::$outfile = $GLOBALS['config']['DEBUG']['outfile'];
		self::$verbosity = $GLOBALS['config']['DEBUG']['verbosity'];

		if(self::$enabled && !is_writable(self::$outfile) && (self::$out==="file" || self::$out==="both")) {
			self::$out = "stdout";
			self::write("Debug outfile is not writable. Switching back to stdout", 0);
		}

		if(self::$enabled===true)
			self::write(date('Ymd-Gis - ') . "Debug mode enabled. Use in development env. only!", 0);

	}

	public static function write($msg, $level=0) {
		if(self::$enabled && $level<=self::$verbosity)
			array_push(self::$content, $msg);
	}

	public static function display() {
		if(self::$enabled && CoreController::getInstance() != NULL) { // keeps only main request with controller
		// if(self::$enabled) { // all requests (if uncommented, html ressources might fail if stdout enabled)
			if(self::$out === "stdout" || self::$out === "both")
				foreach(self::$content as $msg)
					echo "Debug: " . $msg . "<br />";
			if(self::$out === "file" || self::$out === "both") {
				$file = fopen(self::$outfile, 'a');

				if(!$file)
					return;

				foreach(self::$content as $msg)
					fwrite($file, $msg . PHP_EOL);

				fclose($file);
			}
		}
	}

}

?>

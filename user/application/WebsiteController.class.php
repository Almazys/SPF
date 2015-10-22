<?php

abstract class WebsiteController extends CoreController {

	public function __construct() {
		parent::__construct();
		Debug::write("Building common WebsiteController ...", 0);
		/**
		 * Do common stuff concerning your website here
		 */
	}

	public function test() {
		echo "test";
	}
}

?>

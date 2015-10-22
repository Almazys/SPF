<?php

class Index extends WebsiteController {

	public function __construct() {
		parent::__construct();
		Debug::write("Executing Index work() method...", 1);
		$this->work();
	}

	public function work() {
		// $this->view->setTemplate("no_side");
		// $this->view->setLocale("en");

		echo '<h1>Welcome on this example website</h1>
		<p>Here, you\'ll be able to do pretty much nothing, just stare at empty pages.<br />';

	}

}

?>

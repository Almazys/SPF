<?php

class Movies extends WebsiteController {

	public function __construct() {
		parent::__construct();
		$this->work();
	}

	public function work() {
		echo "<h2>List of movies</h2>
				<p> Come later !</p>";
	}

}

?>

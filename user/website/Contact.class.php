<?php

class Contact extends WebsiteController {

	public function __construct() {
		parent::__construct();
		$this->work();
	}

	public function work() {

		echo "<h1> List of people</h1>
		<p> 
			<ul>
				<li>Alice</li>
				<li>Bob</li>
				<li>Caroline</li>
			</ul>
		</p>";
	}

}

?>

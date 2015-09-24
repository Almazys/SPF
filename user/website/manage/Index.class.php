<?php

class Index extends CoreController {

	public function __construct() {
		parent::__construct();
		$this->work();
	}

	public function work() {
		echo '<h2>List of stuff</h2>
							<p>
								<a href="/manage/Pictures">Pictures</a>
								<a href="/manage/Movies">Movies</a>
							</p>';

	}

}

?>

<?php

/**
 * TODO : clean statics
 * TODO : give the choice of OOP or not
 * TODO : update readme
TODO : erase the @MAIN CONTENT if not used
TODO : set replaceAllContent protected (and clean all others ?)
TODO : create user space to put other controllers
 */

require("init.php");
Timer::setMark("Start");

$site=new Site();

Timer::setMark("End");

Debug::write("Page rendered in " . Timer::getTimeBetween("Start", "End") . " secs", 0);
Debug::display();

?>

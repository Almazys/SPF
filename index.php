<?php

/**
 * TODO : clean statics
 * TODO : give the choice of OOP or not
 * TODO : update readme
 * 
 * TODO : erase the @MAIN CONTENT if not used
 * TODO : set replaceAllContent protected (and clean all others ?)
 */

require("init.php");
Timer::setMark("Start");

$site=new Site();

Timer::setMark("End");

Debug::write("Page rendered in " . Timer::getTimeBetween("Start", "End") . " secs", 0);
Debug::display();

?>

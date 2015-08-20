<?php

/**
 * TODO : clean statics
 * TODO : give the choice of OOP or not
 * TODO : update readme
 * 
 * TODO : set replaceAllContent protected (and clean all others ?)
 * TODO : add IP who's not online
 * TODO : move WebsiteController to user/application
 */

require("init.php");
Timer::setMark("Start");

$site=new Site();

Timer::setMark("End");

Debug::write("Page rendered in " . Timer::getTimeBetween("Start", "End") . " secs", 0);
Debug::display();

?>

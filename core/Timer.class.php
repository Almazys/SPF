<?php

class Timer {
	
	protected static $timeArray;

	public static function setMark($mark) {
		self::$timeArray[$mark] = self::getCurrentTime();
	}

	public static function getTimeFrom($mark) {
		return self::getCurrentTime() - self::$timeArray[$mark];
	}

	public static function getTimeBetween($start, $end = null) {
		return ($end == null ? self::getTimeFrom($start) : abs(self::$timeArray[$end] - self::$timeArray[$start]));
	}

	protected static function getCurrentTime() {
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}
}

<?php
/**
 * Classe regroupant les fonctions sur les dates
 * Afin de profiter pleinement des mises à jours de cette librarie, il est fortement recommandé de ne pas la modifier
 * @author Jeremy Humbert <jeremy@immodvisor.com>
 * @copyright 2019 immodvisor
 */
namespace immodvisor;

final class Date {
	
	private static $time = null;
	private static $date = null;
	private static $datetime = null;
	
	private static function init() {
		if(self::$time !== null) {
			return;
		}
		self::$time = time();
		self::$date = date("Y-m-d", self::$time);
		self::$datetime = date("Y-m-d H:i:s", self::$time);
	}
	
	public static function getTime() {
		self::init();
		return self::$time;
	}
	
	public static function getDate() {
		self::init();
		return self::$date;
	}
	
	public static function getDatetime() {
		self::init();
		return self::$datetime;
	}
	
	public static function isDate($date) {
		if(!is_string($date)) {
			return false;
		}
		if(strlen($date) != 10) {
			return false;
		}
		if(!preg_match('`^[0-9]{4}-[0-9]{2}-[0-9]{2}$`', $date)) {
			return false;
		}
		$y = substr($date, 0, 4);
		$m = substr($date, 5, 2);
		$d = substr($date, 8, 2);
		if(!checkdate($m, $d, $y)) {
			return false;
		}
		return true;
	}
	
	public static function isDatetime($datetime) {
		if(!is_string($datetime)) {
			return false;
		}
		if(strlen($datetime) != 19) {
			return false;
		}
		if(!self::isDate(substr($datetime, 0, 10))) {
			return false;
		}
		$reste = substr($datetime, 10);
		if(!preg_match('`^ [0-9]{2}:[0-9]{2}:[0-9]{2}$`', $reste)) {
			return false;
		}
		$h = substr($reste, 1, 2);
		$m = substr($reste, 4, 2);
		$s = substr($reste, 7, 2);
		if($h >= 24 || $m >= 60 || $s >= 60) {
			return false;
		}
		return true;
	}
}
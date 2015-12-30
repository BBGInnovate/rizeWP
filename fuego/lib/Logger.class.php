<?php namespace OpenFuego\lib;

class Logger {
	
	public static $verbose = \OpenFuego\VERBOSE;
	public static $logToFile = \OpenFuego\LOG_TO_FILES;
	public static $logPath = \OpenFuego\LOG_PATH;
	public static $tmp;
	
	public static function debug($message) {
		
		$messageFormatted = self::getTimestamp() . $message . "\n";

		if (self::$verbose) {
			echo $messageFormatted;
		}
		if (self::$logToFile) {
			//the debug logs have too much ... gonna ignore them for now
			//file_put_contents(self::$logPath . "debug.log", $messageFormatted, FILE_APPEND);
		}
	}
	
	public static function info($message) {

		$messageFormatted = self::getTimestamp() . $message . "\n";

		if (self::$verbose) {
			echo $messageFormatted;
		}
		if (self::$logToFile) {
			file_put_contents(self::$logPath . "info.log", $messageFormatted, FILE_APPEND);
		}
		
		// write to log
	}

	public static function error($message) {

		$messageFormatted = self::getTimestamp() . $message . "\n";

		if (self::$verbose) {
			echo $messageFormatted;
		}
		if (self::$logToFile) {
			file_put_contents(self::$logPath . "error.log", $messageFormatted, FILE_APPEND);
		}
		
		// write to log
	}
	
	public static function fatal($message) {
		
		$messageFormatted = self::getTimestamp() . $message . "\n";
		
		if (self::$verbose) {
			echo $messageFormatted;
		}

		else {
			$subject = "OpenFuego encountered a fatal error";
			self::notify($subject, $messageFormatted);	
		}
		if (self::$logToFile) {
			file_put_contents(self::$logPath, $messageFormatted . "fatal.log", FILE_APPEND);
		}

		// write to log
	}
	
	private static function notify($subject, $message) {
		mail(\OpenFuego\WEBMASTER, $subject, $message, 'From: ' . \OpenFuego\POSTMASTER);
	}
	
	private static function getTimestamp() {
		return '[' . date('Y-m-d H:i:s') . ']: ';
	}
}
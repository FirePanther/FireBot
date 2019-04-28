<?php
namespace debug;

class Log {
	// quickly log via telegram (for debugging)
	public static function message($text) {
		if (defined('DEBUG') && DEBUG) {
			\util\File::write(MAIN_DIR.'/log/last-manual-log', $text);
			$response = new \telegram\Response($text);
			$response->emit(defined('ADMIN_USER_ID') ? ADMIN_USER_ID : null);
		}
	}
	
	public static function text($text) {
		self::message($text);
	}
	
	public static function error($text) {
		self::message('❌ '.$text);
	}
	
	public static function warning($text) {
		self::message('⚠️ '.$text);
	}
	
	public static function success($text) {
		self::message('✅ '.$text);
	}
	
	public static function info($text) {
		self::message('ℹ️ '.$text);
	}
}

<?php
namespace debug;

class Log {
	// quickly log via telegram (for debugging)
	public static function message($text, $icon = false) {
		if (defined('DEBUG') && DEBUG) {
			$text = is_array($text) ? print_r($text, 1) : $text;
			\util\File::write(MAIN_DIR.'/log/last-manual-log', $text);
			$response = new \telegram\Response(($icon ? $icon.' ' : '').$text);
			$response->setDebug(true);
			$response->emit(defined('ADMIN_USER_ID') ? ADMIN_USER_ID : null);
		}
	}
	
	public static function text($text) {
		self::message($text);
	}
	
	public static function error($text) {
		self::message($text, '❌');
	}
	
	public static function warning($text) {
		self::message($text, '⚠️');
	}
	
	public static function success($text) {
		self::message($text, '✅');
	}
	
	public static function info($text) {
		self::message($text, 'ℹ️');
	}
}

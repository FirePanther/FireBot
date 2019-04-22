<?php
namespace system;

class Handler {
	public static function error(int $errno, string $errstr, string $errfile, int $errline, array $errcontext) {
		\util\File::append('log/error-'.date('Y-m'), [
			date('[d H:i:s]').": #$errno, $errstr [$errfile:$errline]",
			// print_r(debug_backtrace(), 1)
		]);
	}
	
	public static function exception(\Throwable $e) {
		\util\File::append('log/exception'.date('Y-m'), [
			date('[d H:i:s]').': '.$e->getMessage(),
			print_r(debug_backtrace(), 1)
		]);
		return false;
	}
	
	public static function autoload($class) {
		require_once dirname(__DIR__).'/'.str_replace('\\', '/', $class.'.class.php');
	}
	
	public static function destruct() {}
}

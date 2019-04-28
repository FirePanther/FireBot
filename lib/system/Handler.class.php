<?php
namespace system;

use \telegram\Response;

class Handler {
	// log the error
	public static function error(int $errno, string $errstr, string $errfile, int $errline, array $errcontext) {
		echo "$errstr\n";
		\util\File::append('log/error-'.date('Y-m'), [
			date('[d H:i:s]').": #$errno, $errstr [$errfile:$errline]",
			print_r(debug_backtrace(1), 1)
		]);
		
		if (REPORT_ERRORS) {
			$response = new Response('❌ '.$errstr);
			$response->emit();
		}
	}
	
	// log the exception
	public static function exception(\Throwable $e) {
		echo $e->getMessage()."\n";
		\util\File::append('log/exception'.date('Y-m'), [
			date('[d H:i:s]').': '.$e->getMessage(),
			print_r(debug_backtrace(1), 1)
		]);
		
		if (REPORT_ERRORS) {
			$response = new Response('❌ '.$e->getMessage());
			$response->emit();
		}
		
		return false;
	}
	
	// automatically include necessary class files
	public static function autoload($class) {
		require_once dirname(__DIR__).'/'.str_replace('\\', '/', $class.'.class.php');
	}
	
	public static function destruct() {}
}

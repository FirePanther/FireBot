<?php
namespace app;

use \telegram\Request;

class FireBot {
	private static $telegramRequest = false;
	
	function __construct(array $request = []) {
		if ($request) $this->parseRequest($request);
	}
	
	// parses the webhook request
	public function parseRequest(array $request) {
		self::$telegramRequest = new Request($request);
	}
	
	// get the webhook request
	public static final function getRequest() {
		return self::$telegramRequest;
	}
	
	// creates an object for the requested action
	public function exec() {
		if (self::$telegramRequest !== false) {
			$className = ucfirst(self::$telegramRequest->getPart(0));
			if (preg_match('~^\w+$~', $className) && file_exists(MAIN_DIR."/lib/action/$className.class.php")) {
				$classNamespace = "\\action\\$className";
				new $classNamespace(self::$telegramRequest);
			}
		}
	}
}

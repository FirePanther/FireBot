<?php
namespace app;

use \telegram\Request;

class FireBot {
	private static $telegramRequest = false;
	
	function __construct($request = null) {
		if ($request !== null) $this->parseRequest($request);
	}
	
	// parses the webhook request
	public function parseRequest($request) {
		self::$telegramRequest = new Request($request);
	}
	
	// get the webhook request
	public static final function getRequest() {
		return self::$telegramRequest;
	}
	
	// creates an object for the requested action
	public function exec() {
		if (self::$telegramRequest !== false) {
			// if request is a bot command, search for the action/command class
			if (self::$telegramRequest->isBotCommand()) {
				$className = preg_replace_callback('~(?:^|_)(\w)~', function($m) {
					return strtoupper($m[1]);
				}, self::$telegramRequest->getCommand());
				
				if (preg_match('~^\w+$~', $className) && file_exists(MAIN_DIR."/lib/action/$className.class.php")) {
					$classNamespace = "\\action\\$className";
					$action = new $classNamespace(self::$telegramRequest);
					$action->emit();
				} else \debug\Log::warning('Invalid class name: '.$className);
			} else \debug\Log::info('Message has no command');
		} else \debug\Log::error('Invalid request');
	}
}

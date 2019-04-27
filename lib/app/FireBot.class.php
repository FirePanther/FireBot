<?php
namespace app;

use \telegram\Request;

class FireBot {
	private $telegramRequest = false;
	
	function __construct(array $request = []) {
		if ($request) $this->parseRequest($request);
	}
	
	// parses the webhook request
	public function parseRequest(array $request) {
		$this->telegramRequest = new Request($request);
	}
	
	// creates an object for the requested action
	public function exec() {
		if ($this->telegramRequest !== false) {
			$className = ucfirst($this->telegramRequest->getPart(0));
			if (preg_match('~^\w+$~', $className) && file_exists(MAIN_DIR."/lib/action/$className.class.php")) {
				$classNamespace = "\\action\\$className";
				new $classNamespace($this->telegramRequest);
			}
		}
	}
}

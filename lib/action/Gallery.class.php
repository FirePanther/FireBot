<?php
namespace action;

class Gallery implements IAction {
	function __construct($telegramRequest) {
		
	}
	
	public function emit() {
		$this->response->emit();
	}
	
	// configurations for this action, for /help
	public static function config() {
		return [
			'command' => [
				'name' => 'gallery',
				'description' => 'Add, edit, or delete galleries and gallery media'
			]
		];
	}
}

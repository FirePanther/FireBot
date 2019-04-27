<?php
namespace action;

class Gallery {
	function __construct($telegramRequest) {
		echo "hey, i am the gallery :)";
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

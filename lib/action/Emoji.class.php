<?php
namespace action;

class Emoji {
	function __construct($telegramRequest) {}
	
	// configurations for this action, for /help
	public static function config() {
		return [
			'command' => [
				'name' => 'emoji',
				'description' => 'Quickly find emojis'
			]
		];
	}
}

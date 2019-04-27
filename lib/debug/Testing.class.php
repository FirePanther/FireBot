<?php
namespace debug;

class Testing {
	// just for debugging, simulate a webhook request
	public static function request($options = []) {
		return [
			'message_id' => microtime(true) * 1000 | 0,
			'from' => [
				'id' => 1,
				'username' => 'suat'
			],
			'chat' => [
				'id' => 1,
				'type' => isset($options['public']) && $options['public'] ? 'public' : 'private'
			],
			'text' => isset($options['text']) ? $options['text'] : ''
		];
	}
}

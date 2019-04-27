<?php
namespace telegram;

class Request {
	private $messageId;
	private $from = [];
	private $chat = [];
	private $text = '';
	private $textSanitized = '';
	private $parts = [];
	
	function __construct(array $request = []) {
		if ($request) $this->parseRequest($request);
	}
	
	// parses all necessary values from the webhook request
	public function parseRequest(array $request) {
		$this->messageId = $request['message_id'];
		$this->from = [
			$request['from']['id'],
			$request['from']['username']
		];
		$this->chat = [
			$request['chat']['id'],
			$request['chat']['type'] === 'private'
		];
		if (isset($request['text'])) {
			$this->text = $request['text'];
			$this->textSanitized = $this->sanitize($this->text);
			$this->parts = explode(' ', $this->textSanitized);
		}
		return $this;
	}
	
	public function getChat() {
		return $this->chat;
	}
	
	public function getChatId() {
		return $this->chat[0];
	}
	
	// get a single "chunk" of the action
	public function getPart(int $i = null, bool $array = false) {
		if ($i === null) return $this->parts;
		elseif (isset($this->parts[$i])) {
			return $array ? array_slice($this->parts, $i) : $this->parts[$i];
		} else return $array ? [] : '';
	}
	
	// the received request sanitized (lower case separated with single spaces)
	private function sanitize($text) {
		// lower case
		$text = strtolower($text);
		// whitespace
		$text = trim(preg_replace('~\s+~', ' ', $text));
		return $text;
	}
}

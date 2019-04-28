<?php
namespace telegram;

class Request {
	private $updateId;
	private $messageId;
	
	private $from;
	private $chat;
	private $date;
	
	private $text;
	private $textSanitized;
	private $parts;
	
	private $entities;
	
	function __construct($request = null) {
		if ($request !== null) $this->parseRequest($request);
	}
	
	// parses all necessary values from the webhook request
	public function parseRequest($request) {
		if (is_string($request)) {
			$request = @json_decode($request, true);
			if (!$request) {
				throw new \Exception('Invalid request parameter');
			}
		}
		
		\util\File::write(MAIN_DIR.'/log/last-message', print_r($request, 1));
		
		$this->updateId = $request['update_id'];
		
		$requestMessage = $request['message'];
		$this->messageId = $requestMessage['message_id'];
		
		// from name (single person or bot)
		$requestFrom = $requestMessage['from'];
		$this->from = [
			'id' => $requestFrom['id'],
			'name' => $requestFrom['first_name'],
			'username' => $requestFrom['username'],
			'isBot' => $requestFrom['is_bot'],
			'language' => $requestFrom['language_code'],
		];
		
		// current chat (private chat or group)
		$requestChat = $requestMessage['chat'];
		$this->chat = [
			'id' => $requestChat['id'],
			'name' => $requestChat['first_name'],
			'username' => $requestChat['username'],
			'isPrivate' => $requestChat['type'] === 'private',
		];
		
		$this->date = $requestMessage['date'];
		
		// message text
		$this->text = $requestMessage['text'];
		$this->textSanitized = $this->sanitize($this->text);
		$this->parts = explode(' ', $this->textSanitized);
		
		$this->entities = isset($requestMessage['entities']) ? $requestMessage['entities'] : [];
		
		return $this;
	}
	
	// - getter
	
	public function getMessageId() {
		return $this->messageId;
	}
	
	public function getUpdateId() {
		return $this->updateId;
	}
	
	public function getDate() {
		return $this->date;
	}
	
	public function getFrom($arrayKey = false) {
		return $arrayKey === false ? $this->from : $this->from[$arrayKey];
	}
	
	public function getChat($arrayKey = false) {
		return $arrayKey === false ? $this->chat : $this->chat[$arrayKey];
	}
	
	public function getText($sanitized = false) {
		return $sanitized ? $this->textSanitized : $this->text;
	}
	
	// returns the command without the slash at the beginning
	public function getCommand() {
		if ($this->entities) {
			return substr(
				$this->text,
				$this->entities[0]['offset'] + 1,
				$this->entities[0]['length'] - 1
			);
		}
		return '';
	}
	
	// get a single "chunk" of the action
	public function getPart(int $i = null, bool $array = false) {
		if ($i === null) return $this->parts;
		elseif (isset($this->parts[$i])) {
			return $array ? array_slice($this->parts, $i) : $this->parts[$i];
		} else return $array ? [] : '';
	}
	
	public function isAdmin() {
		return ADMIN_USER_ID === $this->from->id;
	}
	
	public function isBotCommand() {
		return $this->entities ? ($this->entities[0]['type'] === 'bot_command') : false;
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

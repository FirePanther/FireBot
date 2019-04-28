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
		
		if (isset($request['update_id'])) $this->updateId = $request['update_id'];
		
		if (isset($request['message'])) {
			$requestMessage = $request['message'];
			$this->messageId = $requestMessage['message_id'];
			
			// from name (single person or bot)
			if (isset($requestMessage['from'])) {
				$requestFrom = $requestMessage['from'];
				$this->from = [
					'id' => $requestFrom['id'],
					'isBot' => $requestFrom['is_bot'],
					'language' => isset($requestFrom['language_code']) ? $requestFrom['language_code'] : '',
				];
				if (isset($requestFrom['username'])) $this->from['username'] = $requestFrom['username'];
				if (isset($requestFrom['first_name'])) {
					$this->from['name'] = $requestFrom['first_name'].(isset($requestFrom['last_name']) ? ' '.$requestFrom['last_name'] : '');
				}
			}
			
			// current chat (private chat or group)
			if (isset($requestMessage['chat'])) {
				$requestChat = $requestMessage['chat'];
				$this->chat = [
					'id' => $requestChat['id'],
					'isPrivate' => $requestChat['type'] === 'private',
				];
				if (isset($requestChat['username'])) $this->chat['username'] = $requestChat['username'];
				if (isset($requestChat['first_name'])) {
					$this->chat['name'] = $requestChat['first_name'].(isset($requestChat['last_name']) ? ' '.$requestChat['last_name'] : '');
				}
			}
			
			$this->date = $requestMessage['date'];
			
			// message text
			if (isset($requestMessage['text'])) {
				$this->text = $requestMessage['text'];
				$this->textSanitized = $this->sanitize($this->text);
				$this->parts = explode(' ', $this->textSanitized);
			}
			
			$this->entities = isset($requestMessage['entities']) ? $requestMessage['entities'] : [];
		}
		
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
	
	public function getParameters() {
		if ($this->entities) {
			$beforeCommand = substr($this->text, 0, $this->entities[0]['offset']);
			$afterCommand = substr($this->text, $this->entities[0]['offset'] + $this->entities[0]['length']);
			return trim($beforeCommand.$afterCommand);
		}
		return $this->text;
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

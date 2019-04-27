<?php
namespace telegram;

class Response {
	const BOLD = '*';
	const ITALIC = '_';
	const MONOSPACE = '`';
	
	// escape functional chars
	const ESCAPE_PATTERN = '~(_|\*|\`|\[)~';
	
	// lines queue
	private $lines = [];
	
	function __construct($text = null, $format = null) {
		if ($text !== null) $this->addLine($text, $format);
	}
	
	// adds a new formated line into the queue for responding
	// * the first argument may be a multidimensional array for multiple formats
	// * in a single line, e.g.: [['bold', '*'], [' text']] for: *bold* text
	public function addLine($text = '', $format = null) {
		if (is_array($text)) {
			// single line with multiple formats
			$line = [];
			foreach ($text as $chunkArray) {
				if (!is_array($chunkArray)) $chunkArray = [$chunkArray];
				$line[] = $this->escapeText(
					$chunk[0],
					isset($chunk[1]) ? $chunk[1] : null
				);
			}
			$this->lines[] = implode('', $line);
		} else {
			$this->lines[] = $this->escapeText($text, $format);
		}
	}
	
	// add one or more lines at once
	public function addLines($textArray, $format = null) {
		foreach ($textArray as $text) {
			$this->addLine($text, $format);
		}
	}
	
	// add a line with text followed by an empty line
	public function addParagraph($text, $format = null) {
		$this->addLine($text, $format);
		$this->addEmptyLine();
	}
	
	// add an empty line into the queue, duh
	public function addEmptyLine() {
		$this->addLine();
	}
	
	// get all lines from the queue as array
	public function getLines() {
		return $this->lines;
	}
	
	// resets the lines queue
	public function resetLines() {
		$this->lines = [];
	}
	
	// escape the text, no unintended formatting or errors for invalid formatting
	public function escapeText($text, $format = null) {
		if ($format === null) $format = '';
		return $format.
			preg_replace(
				self::ESCAPE_PATTERN,
				"$format\\\\\$1$format",
				$text
			).$format;
	}
	
	// finally respond
	public function emit($chatId = null) {
		if ($chatId === null) {
			$request = \app\FireBot::getRequest();
			if ($request !== false) {
				$chatId = $request->getChat('id');
			} else {
				throw new \Exception('No chatId defined');
			}
		}
		
		Bot::sendMessage([
			'chat_id' => $chatId,
			'text' => implode("\n", $this->lines),
			'parse_mode' => 'Markdown'
		]);
	}
}

<?php
namespace telegram;

class Response {
	const BOLD = '*';
	const ITALIC = '_';
	const MONOSPACE = '`';
	const CODE = '```';
	
	// escape functional chars
	const ESCAPE_PATTERN = '~(_|\*|\`|\[)~';
	
	// lines queue
	private $lines = [];
	
	private $insideCode = false;
	
	protected $sendMessageParameters = [];
	
	function __construct($text = null, $format = null) {
		if ($text !== null) $this->addLine($text, $format);
	}
	
	// adds a new formated line into the queue for responding
	// * the first argument may be a multidimensional array for multiple formats
	// * in a single line, e.g.: [['bold', '*'], [' text']] for: *bold* text
	public function addLine($text = '', $format = null, $escape = true) {
		if ($this->insideCode) $escape = false;
		
		if (is_array($text)) {
			// single line with multiple formats
			$line = [];
			foreach ($text as $chunkArray) {
				if (!is_array($chunkArray)) $chunkArray = [$chunkArray];
				if ($escape) {
					$chunk[0] = $this->escapeText(
						$chunk[0],
						isset($chunk[1]) ? $chunk[1] : null
					);
				}
				$line[] = $chunk[0];
			}
			$this->lines[] = implode('', $line);
		} else {
			if ($escape) $text = $this->escapeText($text, $format);
			$this->lines[] = $text;
		}
	}
	
	
	public function addUnescapedLine($text = '') {
		$this->addLine($text, null, false);
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
		
		Bot::sendMessage($this->sendMessageParameters + [
			'chat_id' => $chatId,
			'text' => implode("\n", $this->lines),
			'parse_mode' => 'Markdown'
		]);
	}
	
	public function startCode() {
		$this->insideCode = true;
		$this->addUnescapedLine(self::CODE);
	}
	
	public function endCode() {
		$this->insideCode = false;
		$this->addUnescapedLine(self::CODE);
	}
}

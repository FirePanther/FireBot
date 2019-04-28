<?php
namespace action;

use \telegram\Reply;

class Emoji implements IAction {
	const TITLE_PATTERN = '~<h2>(.*?)</h2>~';
	const NAME_PATTERN = '~^<a.*?</span>\s*(.*)</.*$~';
	const ICON_PATTERN = '~<span class="emoji">(.*?)</span>~';
	const DESCRIPTION_PATTERN = '~<p>(.*?)</p>~';
	
	private $response;
	private $searchQuery;
	
	function __construct($telegramRequest) {
		$this->response = new Reply();
		
		$this->searchQuery = $telegramRequest->getParameters();
		$emojis = $this->searchForEmoji($this->searchQuery);
		$this->addLinesToResponse($emojis);
	}
	
	public function emit() {
		$this->response->emit();
	}
	
	public static function searchForEmoji($query) {
		$source = file_get_contents('https://emojipedia.org/search/?q='.urlencode($query));
		
		// extract search results list
		$source = substr($source, strpos($source, '<ol class="search-results">'));
		$source = substr($source, 0, strpos($source, '</ol>'));
		
		$emojis = explode('</li>', $source);
		$searchResults = [];
		foreach ($emojis as $emoji) {
			if (
				preg_match(self::TITLE_PATTERN, $emoji, $titleMatch)
				&& preg_match(self::ICON_PATTERN, $titleMatch[1], $iconMatch)
				&& preg_match(self::DESCRIPTION_PATTERN, $emoji, $descriptionMatch)
			) {
				$name = html_entity_decode(preg_replace(self::NAME_PATTERN, '$1', $titleMatch[1]));
				$searchResults[] = [
					'icon' => $iconMatch[1],
					'name' => html_entity_decode($name),
					'description' => $descriptionMatch[1]
				];
			}
		}
		return $searchResults;
	}
	
	public function addLinesToResponse($emojis) {
		if ($emojis) {
			foreach ($emojis as $emoji) {
				// first line with icon and name
				$this->response->addLine([
					// monospace to quickly copy by clicking
					[$emoji['icon'], Reply::MONOSPACE],
					' ',
					[$emoji['name'], Reply::BOLD]
				]);
				// second line with the description
				$this->response->addParagraph($emoji['description'], Reply::ITALIC);
			}
		} else $this->response->addLine('No results for «'.$this->searchQuery.'»', Reply::ITALIC);
	}
	
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

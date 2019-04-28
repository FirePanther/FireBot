<?php
namespace action;

class Help implements IAction {
	private $response;
	
	// builds a help message
	function __construct($telegramRequest) {
		$this->response = new \telegram\Reply();
		$this->response->addParagraph(BOT_NAME.' Help');
		
		$helpCategories = $this->parseActions();
		$this->addCategoriesToResponse($helpCategories, $this->response);
	}
	
	public function emit() {
		$this->response->emit();
	}
	
	// configurations for this action, for /help
	public static function config() {
		return [
			'command' => [
				'name' => 'help',
				'description' => 'Shows a list of commands'
			]
		];
	}
	
	// searches for action php class files and retrieves the configurations
	public static function parseActions($addLeadingSlashes = true, $filterCategories = []) {
		$helpCategories = [
			'commands' => []
		];
		
		if ($filterCategories) {
			if (!is_array($filterCategories)) $filterCategories = [$filterCategories];
			array_filter($helpCategories, function($helpCategory) use ($filterCategories) {
				return in_array($helpCategory, $filterCategories);
			}, ARRAY_FILTER_USE_KEY);
		}
		
		$botActionClasses = glob(__DIR__.'/*.class.php');
		foreach ($botActionClasses as $botActionClass) {
			$botActionClassName = basename($botActionClass, '.class.php');
			$classNamespace = "\\action\\$botActionClassName";
			if (method_exists($classNamespace, 'config')) {
				$botActionConfig = $classNamespace::config();
				self::parseActionConfig($botActionConfig, $helpCategories, $addLeadingSlashes);
			}
		}
		
		return $helpCategories;
	}
	
	// parses the action configurations as human readable lines
	public static function parseActionConfig($botActionConfig, &$helpCategories, $addLeadingSlashes = true) {
		if ($botActionConfig['command']) {
			$command = $botActionConfig['command'];
			if (!empty($command['name']) && !empty($command['description'])) {
				$helpCategories['commands'][$command['name']] = ($addLeadingSlashes ? '/' : '').
					"{$command['name']} - {$command['description']}";
			}
		}
	}
	
	// adds the parsed categories from the actions as response lines
	public static function addCategoriesToResponse($categories, $response, $printHeadlines = null) {
		$printHeadlines = $printHeadlines === null ? (count($categories) !== 1) : $printHeadlines;
		foreach ($categories as $category => $categoryLines) {
			ksort($categoryLines);
			if ($printHeadlines) $response->addLine($category, \telegram\Response::BOLD);
			foreach ($categoryLines as $categoryLine) {
				$response->addLine($categoryLine);
			}
		}
	}
}

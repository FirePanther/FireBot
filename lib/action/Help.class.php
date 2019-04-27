<?php
namespace action;

use \telegram\Response;

class Help {
	// builds a help message
	function __construct($telegramRequest) {
		$response = new Response();
		$response->addParagraph(BOT_NAME.' Help');
		
		$helpCategories = $this->parseActions($response);
		print_r($helpCategories);
		$this->addCategoriesToResponse($response, $helpCategories);
		
		$response->emit(33357188);
	}
	
	// adds the parsed categories from the actions as response lines
	public function addCategoriesToResponse($response, $categories) {
		foreach ($categories as $category => $categoryLines) {
			ksort($categoryLines);
			$response->addLine($category, Response::BOLD);
			foreach ($categoryLines as $categoryLine) {
				$response->addLine($categoryLine);
			}
		}
	}
	
	// searches for action php class files and retreives the configurations
	public function parseActions($response) {
		$helpCategories = [
			'commands' => []
		];
		
		$botActionClasses = glob(__DIR__.'/*.class.php');
		foreach ($botActionClasses as $botActionClass) {
			$botActionClassName = basename($botActionClass, '.class.php');
			$classNamespace = "\\action\\$botActionClassName";
			if (method_exists($classNamespace, 'config')) {
				$botActionConfig = $classNamespace::config();
				$this->parseActionConfig($botActionConfig, $helpCategories);
			}
		}
		
		return $helpCategories;
	}
	
	// parses the action configurations as human readable lines
	public function parseActionConfig($botActionConfig, &$helpCategories) {
		if ($botActionConfig['command']) {
			$command = $botActionConfig['command'];
			if (!empty($command['name']) && !empty($command['description'])) {
				$helpCategories['commands'][$command['name']] = "/{$command['name']} - {$command['description']}";
			}
		}
	}
}

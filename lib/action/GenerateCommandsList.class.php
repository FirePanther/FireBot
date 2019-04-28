<?php
namespace action;

class GenerateCommandsList implements IAction {
	private $response;
	
	function __construct($telegramRequest) {
		$this->response = new \telegram\Reply();
		
		$this->response->startCode();
		
		$helpCategories = Help::parseActions(false, 'commands');
		Help::addCategoriesToResponse($helpCategories, $this->response);
		
		$this->response->endCode();
	}
	
	public function emit() {
		$this->response->emit();
	}
	
	// configurations for this action, for /help
	public static function config() {
		return [
			'command' => [
				'name' => 'generate_commands_list',
				'description' => 'Generate a commands list for the @BotFather (/setcommands)'
			]
		];
	}
}

<?php
namespace action;

interface IAction {
	function __construct(\telegram\Request $telegramRequest);
	public function emit();
}

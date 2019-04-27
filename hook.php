<?php
namespace {
	define('MAIN_DIR', __DIR__);
	chdir(MAIN_DIR);
	require_once 'config.inc.php';
	require_once 'lib/functions.inc.php';
}

namespace app {
	header('Content-Type: text/plain; charset=utf-8');
	
	// testing
	$_POST['message'] = \debug\Testing::request([
		'text' => 'help'
	]);
	
	if (isset($_POST['message'])) {
		$fireBot = new FireBot($_POST['message']);
		$fireBot->exec();
	}
}

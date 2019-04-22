<?php
namespace {
	define('MAIN_DIR', __DIR__);
	chdir(MAIN_DIR);
	require_once 'config.inc.php';
	require_once 'lib/functions.inc.php';
}

namespace app {
	// testing
	$_POST['message'] = \debug\Testing::request([
		'text' => 'gallery add'
	]);
	
	if (isset($_POST['message'])) {
		$fireBot = new FireBot($_POST['message']);
		$fireBot->exec();
	}
}

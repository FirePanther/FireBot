<?php
namespace {
	define('MAIN_DIR', __DIR__);
	chdir(MAIN_DIR);
	require_once 'config.inc.php';
	require_once 'lib/functions.inc.php';
}

namespace app {
	header('Content-Type: text/plain; charset=utf-8');

	$fireBot = new FireBot(file_get_contents('php://input'));
	$fireBot->exec();
}

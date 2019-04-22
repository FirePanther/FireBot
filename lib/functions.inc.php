<?php
use system\Handler;

require_once __DIR__.'/system/Handler.class.php';

set_error_handler([ Handler::class, 'error' ]);
set_exception_handler([ Handler::class, 'exception' ]);
spl_autoload_register([ Handler::class, 'autoload' ]);
register_shutdown_function([ Handler::class, 'destruct' ]);

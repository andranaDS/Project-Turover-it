<?php

use App\Kernel;
use Blackfire\Client;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

// debug
if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

// blackfire
if (isset($_SERVER['HTTP_BLACKFIRETRIGGER'])) {
    $blackfire = new Client();
    $probe = $blackfire->createProbe();
    register_shutdown_function(static function () use ($blackfire, $probe) {
        $blackfire->endProbe($probe);
    });
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool)$_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

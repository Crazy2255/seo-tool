<?php

// Clean any existing output first
while (ob_get_level()) {
    ob_end_clean();
}

// Start fresh output buffering
ob_start();

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Handle the request properly
$request = Request::capture();
$response = $app->handle($request);

// Clean up any stray output before sending response
while (ob_get_level()) {
    ob_end_clean();
}

// Send the clean response
$response->send();

// Terminate the request properly
$app->terminate($request, $response);

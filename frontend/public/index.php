<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/../backend/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require dirname(__DIR__, 1) . '/../backend/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once dirname(__DIR__, 1) . '/../backend/bootstrap/app.php')
    ->handleRequest(Request::capture());

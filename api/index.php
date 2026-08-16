<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Prepare writable directories in /tmp for Vercel serverless environment
$tmpStorage = '/tmp/storage';
$dirs = [
    $tmpStorage . '/app/public',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/logs',
    '/tmp/bootstrap/cache'
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
    }
}

putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath($tmpStorage);

$app->handleRequest(Request::capture());

<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Prepare writable storage & database directories in Vercel serverless environment (/tmp)
$tmpStorage = '/tmp/storage';
$tmpDatabase = '/tmp/database';
$dirs = [
    $tmpStorage . '/app/public',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/logs',
    '/tmp/bootstrap/cache',
    $tmpDatabase,
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Handle SQLite database in /tmp for Vercel
$localSqlite = __DIR__ . '/../database/database.sqlite';
$writableSqlite = $tmpDatabase . '/database.sqlite';

if (!file_exists($writableSqlite)) {
    if (file_exists($localSqlite)) {
        @copy($localSqlite, $writableSqlite);
    } else {
        @touch($writableSqlite);
    }
}

// If DB_CONNECTION is sqlite or unset, point DB_DATABASE to writable /tmp SQLite
$dbConn = env('DB_CONNECTION');
if (empty($dbConn) || $dbConn === 'sqlite') {
    putenv("DB_CONNECTION=sqlite");
    putenv("DB_DATABASE={$writableSqlite}");
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $writableSqlite;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $writableSqlite;
}

putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");

try {
    // Register Composer autoloader
    require __DIR__ . '/../vendor/autoload.php';

    /** @var Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $app->useStoragePath($tmpStorage);

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Laravel Application Error (Vercel)</h1>";
    echo "<p><strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>";
    echo "<hr><h3>Stack Trace:</h3>";
    echo "<pre style='background:#f4f4f4;padding:15px;border-radius:8px;overflow-x:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

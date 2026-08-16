<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Register Composer autoloader first
require __DIR__ . '/../vendor/autoload.php';

// Serve static assets directly if routed to api/index.php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));
$publicFile = __DIR__ . '/../public' . $uri;

if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    $extension = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    $mimes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'json'  => 'application/json',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'webp'  => 'image/webp',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
    ];
    $mime = $mimes[$extension] ?? (function_exists('mime_content_type') ? mime_content_type($publicFile) : 'application/octet-stream');
    header("Content-Type: {$mime}");
    readfile($publicFile);
    exit;
}

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

// Check DB_CONNECTION using native PHP getenv / $_ENV
$dbConn = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? null);
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

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

// Ensure APP_KEY has a valid fallback key if missing in Vercel env
$appKey = getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? $_SERVER['APP_KEY'] ?? null);
if (empty($appKey)) {
    $fallbackKey = 'base64:nVFhQRk5Qcd5C42t47/VAJaLcvCnUeOIgyr/+gBKUZY=';
    putenv("APP_KEY={$fallbackKey}");
    $_ENV['APP_KEY'] = $fallbackKey;
    $_SERVER['APP_KEY'] = $fallbackKey;
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

// Use 'cookie' or 'file' for SESSION_DRIVER to prevent database session table dependency errors
$sessionDriver = getenv('SESSION_DRIVER') ?: ($_ENV['SESSION_DRIVER'] ?? $_SERVER['SESSION_DRIVER'] ?? null);
if (empty($sessionDriver) || $sessionDriver === 'database') {
    putenv("SESSION_DRIVER=cookie");
    $_ENV['SESSION_DRIVER'] = 'cookie';
    $_SERVER['SESSION_DRIVER'] = 'cookie';
}

$cacheStore = getenv('CACHE_STORE') ?: ($_ENV['CACHE_STORE'] ?? $_SERVER['CACHE_STORE'] ?? null);
if (empty($cacheStore) || $cacheStore === 'database') {
    putenv("CACHE_STORE=file");
    $_ENV['CACHE_STORE'] = 'file';
    $_SERVER['CACHE_STORE'] = 'file';
}

putenv("APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php");
putenv("APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php");
putenv("APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php");
putenv("APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php");
putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");

try {
    /** @var Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $app->useStoragePath($tmpStorage);

    // Ensure app.debug, view compiled path, session driver, and session storage use /tmp
    if ($app->bound('config')) {
        $config = $app->make('config');
        $config->set('app.debug', true);
        $config->set('view.compiled', $tmpStorage . '/framework/views');
        $config->set('session.files', $tmpStorage . '/framework/sessions');
        $config->set('session.driver', 'cookie');
        $config->set('cache.default', 'file');
    }

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Laravel Application Error (Vercel)</h1>";

    $current = $e;
    $count = 1;
    while ($current) {
        echo "<div style='background:#fff;border:1px solid #e1e4e8;padding:16px;margin-bottom:16px;border-radius:8px;font-family:sans-serif;'>";
        echo "<h3 style='color:#d73a49;margin-top:0;'>Exception #{$count}: " . htmlspecialchars(get_class($current)) . "</h3>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($current->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($current->getFile()) . " (Line " . $current->getLine() . ")</p>";
        echo "<pre style='background:#f6f8fa;padding:12px;border-radius:6px;overflow-x:auto;font-size:12px;'>" . htmlspecialchars($current->getTraceAsString()) . "</pre>";
        echo "</div>";
        $current = $current->getPrevious();
        $count++;
    }
}

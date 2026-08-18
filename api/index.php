<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// --- Vercel Environment Flags ---
putenv("VERCEL=1");
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

putenv("LOG_CHANNEL=stderr");
$_ENV['LOG_CHANNEL']  = 'stderr';
$_SERVER['LOG_CHANNEL'] = 'stderr';

// Force HTTPS (Vercel terminates SSL at the edge)
putenv("HTTPS=on");
$_ENV['HTTPS']  = 'on';
$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = '443';
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';

// Detect actual hostname for APP_URL (support custom domains too)
$host = $_SERVER['HTTP_HOST'] ?? 'apvisuals.vercel.app';
putenv("APP_URL=https://{$host}");
$_ENV['APP_URL']    = "https://{$host}";
$_SERVER['APP_URL'] = "https://{$host}";

// Cloudinary URL for media uploads on Vercel
$cloudinaryUrl = getenv('CLOUDINARY_URL') ?: ($_ENV['CLOUDINARY_URL'] ?? 'cloudinary://734915755182871:IEJROZnMx30vNa21EYcOSdyF6XE@ttyvzu53');
putenv("CLOUDINARY_URL={$cloudinaryUrl}");
$_ENV['CLOUDINARY_URL']    = $cloudinaryUrl;
$_SERVER['CLOUDINARY_URL'] = $cloudinaryUrl;

// Set production env on Vercel
putenv("APP_ENV=production");
$_ENV['APP_ENV']    = 'production';
$_SERVER['APP_ENV'] = 'production';

// ---- SESSION: Cookie driver (stateless, works across ALL Vercel lambda instances) ----
// putenv() is the CORRECT way to override config on Vercel — these values are read by
// config/session.php via env() BEFORE the app bootstraps. $config->set() runs too late.
putenv("SESSION_DRIVER=cookie");       // Stateless: data lives in the browser cookie
putenv("SESSION_COOKIE=apv_sess_v4");  // v4: fresh name, forces new cookie, no stale data
putenv("SESSION_ENCRYPT=false");       // Cookie driver auto-encrypts; do NOT double-encrypt
putenv("SESSION_LIFETIME=120");
putenv("SESSION_PATH=/");
putenv("SESSION_DOMAIN=");             // Empty = use current request domain
putenv("SESSION_SECURE_COOKIE=true");  // HTTPS-only (Vercel is always HTTPS)
putenv("SESSION_SAME_SITE=lax");       // lax = cookie sent on top-level navigation/redirects

$_ENV['SESSION_DRIVER']        = 'cookie';
$_ENV['SESSION_COOKIE']        = 'apv_sess_v4';
$_ENV['SESSION_ENCRYPT']       = 'false';
$_ENV['SESSION_LIFETIME']      = '120';
$_ENV['SESSION_PATH']          = '/';
$_ENV['SESSION_DOMAIN']        = '';
$_ENV['SESSION_SECURE_COOKIE'] = 'true';
$_ENV['SESSION_SAME_SITE']     = 'lax';

$_SERVER['SESSION_DRIVER']        = 'cookie';
$_SERVER['SESSION_COOKIE']        = 'apv_sess_v4';
$_SERVER['SESSION_ENCRYPT']       = 'false';
$_SERVER['SESSION_LIFETIME']      = '120';
$_SERVER['SESSION_PATH']          = '/';
$_SERVER['SESSION_DOMAIN']        = '';
$_SERVER['SESSION_SECURE_COOKIE'] = 'true';
$_SERVER['SESSION_SAME_SITE']     = 'lax';

// Force BCRYPT_ROUNDS to integer 10 (prevents password_hash invalid cost error)
putenv("BCRYPT_ROUNDS=10");
$_ENV['BCRYPT_ROUNDS']  = 10;
$_SERVER['BCRYPT_ROUNDS'] = 10;

// Use file cache in /tmp (no DB needed for cache)
putenv("CACHE_STORE=file");
$_ENV['CACHE_STORE']  = 'file';
$_SERVER['CACHE_STORE'] = 'file';

// Remove stale bootstrap cache (leftover from local dev)
@unlink(__DIR__ . '/../bootstrap/cache/services.php');
@unlink(__DIR__ . '/../bootstrap/cache/packages.php');
@unlink(__DIR__ . '/../bootstrap/cache/config.php');
@unlink(__DIR__ . '/../bootstrap/cache/routes.php');
@unlink(__DIR__ . '/../bootstrap/cache/routes-v7.php');

// Register Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// --- Serve static public assets directly ---
$uri        = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));
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
        'mp4'   => 'video/mp4',
    ];
    $mime = $mimes[$extension] ?? (function_exists('mime_content_type') ? mime_content_type($publicFile) : 'application/octet-stream');
    header("Content-Type: {$mime}");
    readfile($publicFile);
    exit;
}

// --- Prepare /tmp writable directories for Vercel ---
$tmpStorage = '/tmp/storage';
$dirs = [
    $tmpStorage . '/app/public',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/logs',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// --- Determine Database ---
// If no external DB_HOST is set (i.e. still localhost), switch to SQLite in /tmp
$dbConn = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'mysql');
$dbHost = getenv('DB_HOST')       ?: ($_ENV['DB_HOST'] ?? '127.0.0.1');

if ($dbConn !== 'mysql' || $dbHost === '127.0.0.1' || $dbHost === 'localhost') {
    $tmpDatabase    = '/tmp/database';
    $writableSqlite = $tmpDatabase . '/database.sqlite';

    @mkdir($tmpDatabase, 0755, true);

    $remoteDbUrl = 'https://res.cloudinary.com/ttyvzu53/raw/upload/apvisuals/db/database.sqlite';

    if (!file_exists($writableSqlite)) {
        // Try fetching the synced SQLite database from Cloudinary CDN
        $remoteData = @file_get_contents($remoteDbUrl);
        if ($remoteData && strlen($remoteData) > 1000) {
            @file_put_contents($writableSqlite, $remoteData);
        } else {
            // Local fallback from committed repository file
            $localSqlite = __DIR__ . '/../database/database.sqlite';
            if (file_exists($localSqlite)) {
                @copy($localSqlite, $writableSqlite);
            } else {
                @touch($writableSqlite);
            }
        }
    }

    putenv("DB_CONNECTION=sqlite");
    putenv("DB_DATABASE={$writableSqlite}");
    $_ENV['DB_CONNECTION']  = 'sqlite';
    $_ENV['DB_DATABASE']    = $writableSqlite;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE']   = $writableSqlite;
}

// --- APP_KEY fallback (should be set in Vercel env vars) ---
$appKey = getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? null);
if (empty($appKey)) {
    $fallbackKey = 'base64:nVFhQRk5Qcd5C42t47/VAJaLcvCnUeOIgyr/+gBKUZY=';
    putenv("APP_KEY={$fallbackKey}");
    $_ENV['APP_KEY']  = $fallbackKey;
    $_SERVER['APP_KEY'] = $fallbackKey;
}

// --- Path overrides for Vercel ---
putenv("APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php");
putenv("APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php");
putenv("APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php");
putenv("APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php");
putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");

// --- Boot Laravel ---
try {
    /** @var Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $app->useStoragePath($tmpStorage);

    // Apply config overrides after container is available
    if ($app->bound('config')) {
        $config = $app->make('config');

        $config->set('app.debug',          true);
        $config->set('app.env',            'production');
        $config->set('app.url',            "https://{$host}");
        $config->set('logging.default',    'stderr');
        $config->set('view.compiled',      $tmpStorage . '/framework/views');
        $config->set('cache.default',      'file');
        $config->set('cache.stores.file.path', $tmpStorage . '/framework/cache/data');
        $config->set('hashing.driver',     'bcrypt');
        $config->set('hashing.bcrypt.rounds', 10);

        // ---- SESSION config (backup via config->set, primary via putenv above) ----
        $config->set('session.driver',    'cookie');
        $config->set('session.cookie',    'apv_sess_v4');
        $config->set('session.lifetime',  120);
        $config->set('session.secure',    true);
        $config->set('session.same_site', 'lax');
        $config->set('session.domain',    null);
        $config->set('session.http_only', true);
        $config->set('session.encrypt',   false);

    }

    $app->handleRequest(Request::capture());

} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1 style='font-family:sans-serif;color:#d73a49'>Laravel Application Error</h1>";

    $current = $e;
    $count   = 1;
    while ($current) {
        echo "<div style='background:#fff8f8;border:1px solid #f0c0c0;padding:16px;margin-bottom:16px;border-radius:8px;font-family:monospace;font-size:13px'>";
        echo "<strong style='color:#d73a49'>Exception #{$count}: " . htmlspecialchars(get_class($current)) . "</strong><br>";
        echo "<em>" . htmlspecialchars($current->getMessage()) . "</em><br><br>";
        echo htmlspecialchars($current->getFile()) . " (line " . $current->getLine() . ")<br>";
        echo "<pre style='background:#f6f8fa;padding:10px;border-radius:4px;overflow:auto;font-size:11px'>" . htmlspecialchars($current->getTraceAsString()) . "</pre>";
        echo "</div>";
        $current = $current->getPrevious();
        $count++;
    }
}

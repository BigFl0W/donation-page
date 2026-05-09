<?php
declare(strict_types=1);

/*
 * ─── AUTOLOADER ─────────────────────────────────────
 * PSR-4–style autoloader for the App\ namespace.
 * Maps  App\ClassName  →  src/App/ClassName.php
 */
spl_autoload_register(function (string $class): void {
    $prefix = "App\\";
    $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "App" . DIRECTORY_SEPARATOR;

    if (strncmp($class, $prefix, strlen($prefix)) === 0) {
        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace("\\", DIRECTORY_SEPARATOR, $relativeClass) . ".php";
        if (is_file($file)) {
            require_once $file;
        }
        return;
    }

    $pmPrefix = "PHPMailer\\PHPMailer\\";
    $pmBaseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "PHPMailer" . DIRECTORY_SEPARATOR;
    if (strncmp($class, $pmPrefix, strlen($pmPrefix)) === 0) {
        $relativeClass = substr($class, strlen($pmPrefix));
        $file = $pmBaseDir . str_replace("\\", DIRECTORY_SEPARATOR, $relativeClass) . ".php";
        if (is_file($file)) {
            require_once $file;
        }
    }
});

/*
 * ─── BOOTSTRAP ──────────────────────────────────────
 */

// 1. Load environment variables
\App\Env::load();

// 2. Load application config (used by Helpers, etc.)
//    The config array is loaded lazily by App\Helpers::appConfig()

// 3. Set timezone
$tz = \App\Env::get("APP_TIMEZONE", "UTC");
date_default_timezone_set($tz);

// 4. Configure session
$sessionName = \App\Env::get("SESSION_NAME", "gracious_session");
session_name($sessionName);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 5. CSRF token
if (empty($_SESSION["_csrf_token"])) {
    $_SESSION["_csrf_token"] = bin2hex(random_bytes(32));
}

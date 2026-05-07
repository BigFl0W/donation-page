<?php

declare(strict_types=1);

require_once __DIR__ . "/env.php";

$envPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . ".env";
load_env_file($envPath);

$app = require __DIR__ . "/app.php";

date_default_timezone_set($app["timezone"] ?? "UTC");

session_name($app["session_name"] ?? "gracious_session");

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . "/helpers.php";
require_once __DIR__ . "/database.php";
require_once __DIR__ . "/content.php";
require_once __DIR__ . "/auth.php";

<?php

declare(strict_types=1);

$app = require __DIR__ . "/app.php";

date_default_timezone_set($app["timezone"] ?? "UTC");

session_name($app["session_name"] ?? "gracious_session");

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . "/helpers.php";
require_once __DIR__ . "/database.php";
require_once __DIR__ . "/auth.php";

<?php

declare(strict_types=1);

function asset_url(string $path): string
{
    return "../assets/" . ltrim($path, "/");
}

function app_config(?string $key = null, mixed $default = null): mixed
{
    static $config;

    if ($config === null) {
        $config = require __DIR__ . "/app.php";
    }

    if ($key === null) {
        return $config;
    }

    return $config[$key] ?? $default;
}

function admin_url(string $path = ""): string
{
    $base = rtrim((string) app_config("admin_path", "/donation-page/admin"), "/");

    if ($path === "") {
        return $base . "/";
    }

    return $base . "/" . ltrim($path, "/");
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

function is_active_admin_route(string $route): bool
{
    $current = $_GET["page"] ?? "dashboard";
    return $current === $route;
}

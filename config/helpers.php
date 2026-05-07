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

function site_url(string $path = ""): string
{
    $base = rtrim((string) app_config("app_url", "http://localhost/donation-page"), "/");

    if ($path === "") {
        return $base;
    }

    return $base . "/" . ltrim($path, "/");
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    $value = trim($value, '-');

    return $value !== '' ? $value : 'item';
}

function is_active_admin_route(string $route): bool
{
    $current = $_GET["page"] ?? "dashboard";
    return $current === $route;
}

function build_post_permalink(string $categorySlug, string $postSlug): string
{
    $cleanCategory = slugify($categorySlug !== "" ? $categorySlug : "updates");
    $cleanSlug = slugify($postSlug);

    return "blog/" . $cleanCategory . "/" . $cleanSlug;
}

function post_public_url(array $post): string
{
    $permalink = trim((string) ($post["permalink_path"] ?? ""));

    if ($permalink === "") {
        $permalink = build_post_permalink(
            (string) ($post["category_slug"] ?? $post["category"] ?? "updates"),
            (string) ($post["slug"] ?? "story")
        );
    }

    return site_url($permalink);
}

function split_tag_list(string $value): array
{
    $parts = preg_split('/\s*,\s*/', trim($value)) ?: [];
    $parts = array_values(array_filter(array_map(static fn(string $item): string => trim($item), $parts), static fn(string $item): bool => $item !== ""));

    return array_values(array_unique($parts));
}

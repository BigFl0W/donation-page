<?php
declare(strict_types=1);

namespace App;

class Helpers
{
    public static function appConfig(?string $key = null, mixed $default = null): mixed
    {
        static $config = null;
        if ($config === null) {
            $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "app.php";
            $config = is_file($path) ? (require $path) : [];
        }
        if ($key === null) return $config;
        return $config[$key] ?? $default;
    }

    public static function e(string|int|float|null $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
    }

    public static function siteUrl(string $path = ""): string
    {
        $base = rtrim((string)self::appConfig("app_url", ""), "/");
        if ($base === "") {
            $base = self::detectBaseUrl();
        }
        return $path !== "" ? $base . "/" . ltrim($path, "/") : $base;
    }

    public static function adminUrl(string $path = ""): string
    {
        $adminPath = trim((string)self::appConfig("admin_path", "admin"), "/");
        $base = rtrim((string)self::appConfig("app_url", ""), "/");
        if ($base === "") {
            $base = self::detectBaseUrl();
        }
        $adminBase = $base . "/" . $adminPath;
        return $path !== "" ? $adminBase . "/" . ltrim($path, "/") : $adminBase;
    }

    public static function assetUrl(string $path): string
    {
        return self::siteUrl($path);
    }

    public static function slugify(string $value): string
    {
        $value = strip_tags($value);
        $value = preg_replace('/[^\p{L}\p{N}\s-]/u', "", $value) ?? "";
        $value = preg_replace('/[\s-]+/', "-", $value) ?? "";
        $value = trim($value, "-");
        return mb_strtolower($value, "UTF-8");
    }

    public static function fmt(float $amount, string $currency = "USD"): string
    {
        if ($amount >= 1_000_000) {
            return "$" . number_format($amount / 1_000_000, 1) . "M";
        }
        if ($amount >= 1_000) {
            return "$" . number_format($amount / 1_000, 1) . "K";
        }
        return "$" . number_format($amount);
    }

    public static function initls(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $parts = array_slice($parts, 0, 2);
        $initials = "";
        foreach ($parts as $p) {
            if ($p !== "") $initials .= mb_strtoupper(mb_substr($p, 0, 1));
        }
        return $initials !== "" ? $initials : "AD";
    }

    public static function ta(?string $dt): string
    {
        if ($dt === null || $dt === "") return "Recently";
        $timestamp = strtotime($dt);
        if ($timestamp === false) return "Recently";
        $diff = time() - $timestamp;
        return match (true) {
            $diff < 60     => "Just now",
            $diff < 3600   => floor($diff / 60) . "m ago",
            $diff < 86400  => floor($diff / 3600) . "h ago",
            $diff < 604800 => floor($diff / 86400) . "d ago",
            default        => date("M j", $timestamp),
        };
    }

    public static function bc(string $input, ?float $pct = null): string
    {
        if ($pct !== null) {
            return match (true) {
                $pct < 25  => "#dc2626",
                $pct < 50  => "#d97706",
                $pct < 75  => "#2563eb",
                default    => "#059669",
            };
        }
        $palette = ["#0f766e","#2563eb","#d97706","#7c3aed","#dc2626","#0891b2","#059669","#9333ea"];
        return $palette[crc32($input) % count($palette)];
    }

    public static function buildPostPermalink(string $categorySlug, string $postSlug): string
    {
        return "blog/" . $categorySlug . "/" . $postSlug;
    }

    public static function postPublicUrl(array $post): string
    {
        $catSlug = self::slugify($post["category"] ?? "general");
        $postSlug = $post["slug"] ?? self::slugify($post["title"] ?? "post");
        return self::siteUrl("blog/" . $catSlug . "/" . $postSlug);
    }

    public static function splitTagList(string $value): array
    {
        $tags = array_map("trim", explode(",", $value));
        return array_values(array_filter($tags, fn($t) => $t !== ""));
    }

    public static function redirect(string $url): never
    {
        header("Location: " . $url);
        exit;
    }

    private static function detectBaseUrl(): string
    {
        $scheme = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"] ?? "localhost";
        return "{$scheme}://{$host}";
    }
}

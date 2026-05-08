<?php
declare(strict_types=1);

namespace App;

class Helpers
{
    private static ?array $settingsCache = null;

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

    public static function setting(string $key, mixed $default = null): mixed
    {
        if (self::$settingsCache === null) {
            self::$settingsCache = [];
            if (Database::available() && Database::tableExists("settings")) {
                $rows = Database::fetchAll("SELECT setting_key, setting_value FROM settings");
                foreach ($rows as $row) {
                    $settingKey = (string) ($row["setting_key"] ?? "");
                    if ($settingKey !== "") {
                        self::$settingsCache[$settingKey] = $row["setting_value"] ?? null;
                    }
                }
            }
        }

        return self::$settingsCache[$key] ?? $default;
    }

    public static function forgetSettingsCache(): void
    {
        self::$settingsCache = null;
    }

    public static function brandName(string $default = "Gracious Charity"): string
    {
        $value = (string) self::setting("site_name", $default);
        return trim($value) !== "" ? $value : $default;
    }

    public static function brandLogoPath(string $default = "assets/images/logo_dark.svg"): string
    {
        // Use the 'brand_logo' setting key used by the admin branding form
        $value = (string) self::setting("brand_logo", $default);
        return trim($value) !== "" ? $value : $default;
    }

    public static function brandFaviconPath(string $default = "assets/images/favicon.ico"): string
    {
        $value = (string) self::setting("site_favicon", $default);
        return trim($value) !== "" ? $value : $default;
    }

    public static function slugify(string $value): string
    {
        $value = strip_tags($value);
        $value = preg_replace('/[^\p{L}\p{N}\s-]/u', "", $value) ?? "";
        $value = preg_replace('/[\s-]+/', "-", $value) ?? "";
        $value = trim($value, "-");
        return mb_strtolower($value, "UTF-8");
    }

    public static function fmt(float $amount, string $currency = "NGN"): string
    {
        $symbol = ($currency === "NGN" || $currency === "NG") ? "₦" : "$";
        return $symbol . number_format($amount);
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

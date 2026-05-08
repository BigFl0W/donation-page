<?php
declare(strict_types=1);

namespace App;

class Env
{
    private static bool $loaded = false;

    public static function load(?string $path = null): void
    {
        if (self::$loaded) return;
        self::$loaded = true;

        $path ??= dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . ".env";

        if (!is_file($path) || !is_readable($path)) return;

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) return;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === "" || str_starts_with($line, "#")) continue;

            $separator = strpos($line, "=");
            if ($separator === false) continue;

            $key = trim(substr($line, 0, $separator));
            $value = trim(substr($line, $separator + 1));

            if ($key === "") continue;

            if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
                $value = substr($value, 1, -1);
            } elseif (str_starts_with($value, "'") && str_ends_with($value, "'")) {
                $value = substr($value, 1, -1);
            }

            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === null) return $default;

        $lower = strtolower((string)$value);
        return match ($lower) {
            "true", "(true)" => true,
            "false", "(false)" => false,
            "null", "(null)" => null,
            default => $value,
        };
    }

    public static function isLoaded(): bool
    {
        return self::$loaded;
    }
}

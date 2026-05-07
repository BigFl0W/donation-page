<?php

declare(strict_types=1);

function load_env_file(?string $path = null): void
{
    static $loaded = false;

    if ($loaded) {
        return;
    }

    $path = $path ?? dirname(__DIR__) . DIRECTORY_SEPARATOR . ".env";

    if (!is_file($path) || !is_readable($path)) {
        $loaded = true;
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines === false) {
        $loaded = true;
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if ($trimmed === "" || str_starts_with($trimmed, "#")) {
            continue;
        }

        [$name, $value] = array_pad(explode("=", $trimmed, 2), 2, "");
        $name = trim($name);

        if ($name === "") {
            continue;
        }

        $value = trim($value);

        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;

        if (getenv($name) === false) {
            putenv($name . "=" . $value);
        }
    }

    $loaded = true;
}

function env(string $key, mixed $default = null): mixed
{
    load_env_file();

    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if ($value === false || $value === null || $value === "") {
        return $default;
    }

    $normalized = strtolower((string) $value);

    return match ($normalized) {
        "true" => true,
        "false" => false,
        "null" => null,
        default => $value,
    };
}

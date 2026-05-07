<?php

declare(strict_types=1);

function database_config(): array
{
    return [
        "driver" => "mysql",
        "host" => "127.0.0.1",
        "port" => 3306,
        "database" => "donation_page",
        "username" => "root",
        "password" => "",
        "charset" => "utf8mb4",
    ];
}

function database_dsn(array $config): string
{
    return sprintf(
        "%s:host=%s;port=%d;dbname=%s;charset=%s",
        $config["driver"],
        $config["host"],
        $config["port"],
        $config["database"],
        $config["charset"]
    );
}

function database_connection(): ?PDO
{
    static $pdo = false;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = database_config();

    try {
        $pdo = new PDO(
            database_dsn($config),
            $config["username"],
            $config["password"],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (Throwable $exception) {
        $pdo = null;
    }

    return $pdo;
}

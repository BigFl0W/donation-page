<?php

declare(strict_types=1);

function database_config(): array
{
    return [
        "driver" => (string) env("DB_DRIVER", "mysql"),
        "host" => (string) env("DB_HOST", "127.0.0.1"),
        "port" => (int) env("DB_PORT", 3306),
        "database" => (string) env("DB_DATABASE", "donation_page"),
        "username" => (string) env("DB_USERNAME", "root"),
        "password" => (string) env("DB_PASSWORD", ""),
        "charset" => (string) env("DB_CHARSET", "utf8mb4"),
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

function database_available(): bool
{
    return database_connection() instanceof PDO;
}

function db_fetch_all(string $sql, array $params = []): array
{
    $pdo = database_connection();

    if (!$pdo) {
        return [];
    }

    $statement = $pdo->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function db_fetch_one(string $sql, array $params = []): ?array
{
    $pdo = database_connection();

    if (!$pdo) {
        return null;
    }

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    $row = $statement->fetch();

    return $row ?: null;
}

function db_execute(string $sql, array $params = []): bool
{
    $pdo = database_connection();

    if (!$pdo) {
        return false;
    }

    $statement = $pdo->prepare($sql);

    return $statement->execute($params);
}

function db_last_insert_id(): ?string
{
    $pdo = database_connection();

    if (!$pdo) {
        return null;
    }

    return $pdo->lastInsertId();
}

function db_table_exists(string $table): bool
{
    $pdo = database_connection();

    if (!$pdo) {
        return false;
    }

    $statement = $pdo->prepare("SHOW TABLES LIKE :table_name");
    $statement->execute(["table_name" => $table]);

    return (bool) $statement->fetchColumn();
}

<?php
declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function config(): array
    {
        return [
            "driver"   => Env::get("DB_DRIVER", "mysql"),
            "host"     => Env::get("DB_HOST", "127.0.0.1"),
            "port"     => Env::get("DB_PORT", "3306"),
            "database" => Env::get("DB_DATABASE", "donation_page"),
            "username" => Env::get("DB_USERNAME", "root"),
            "password" => Env::get("DB_PASSWORD", ""),
            "charset"  => Env::get("DB_CHARSET", "utf8mb4"),
        ];
    }

    public static function dsn(?array $config = null): string
    {
        $config ??= self::config();
        return sprintf(
            "%s:host=%s;port=%s;dbname=%s;charset=%s",
            $config["driver"],
            $config["host"],
            $config["port"],
            $config["database"],
            $config["charset"]
        );
    }

    public static function connection(): ?PDO
    {
        if (self::$connection !== null) return self::$connection;

        try {
            $config = self::config();
            self::$connection = new PDO(
                self::dsn($config),
                $config["username"],
                $config["password"],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException) {
            self::$connection = null;
        }

        return self::$connection;
    }

    public static function available(): bool
    {
        return self::connection() !== null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        $db = self::connection();
        if ($db === null) return [];

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll() ?: [];
        } catch (PDOException) {
            return [];
        }
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $db = self::connection();
        if ($db === null) return null;

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch();
            return $row !== false ? $row : null;
        } catch (PDOException) {
            return null;
        }
    }

    public static function execute(string $sql, array $params = []): bool
    {
        $db = self::connection();
        if ($db === null) return false;

        try {
            $stmt = $db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException) {
            return false;
        }
    }

    public static function lastInsertId(): ?string
    {
        $db = self::connection();
        if ($db === null) return null;

        $id = $db->lastInsertId();
        return $id !== false ? (string)$id : null;
    }

    public static function tableExists(string $table): bool
    {
        $db = self::connection();
        if ($db === null) return false;

        try {
            $stmt = $db->prepare(
                "SELECT 1
                 FROM information_schema.tables
                 WHERE table_schema = DATABASE() AND table_name = :table
                 LIMIT 1"
            );
            $stmt->execute(["table" => $table]);
            return $stmt->fetch() !== false;
        } catch (PDOException) {
            return false;
        }
    }
}

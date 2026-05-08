<?php
declare(strict_types=1);

namespace App;

class Auth
{
    public static function databaseReady(): bool
    {
        return Database::available()
            && Database::tableExists("admins")
            && Database::tableExists("roles");
    }

    public static function count(): int
    {
        if (!Database::available()) return 0;
        $row = Database::fetchOne("SELECT COUNT(*) AS cnt FROM admins");
        return $row !== null ? (int)$row["cnt"] : 0;
    }

    public static function setupRequired(): bool
    {
        return !Database::available() || self::count() === 0;
    }

    public static function current(): ?array
    {
        return $_SESSION["admin_user"] ?? null;
    }

    public static function isLoggedIn(): bool
    {
        return self::current() !== null;
    }

    public static function attempt(string $email, string $password): bool
    {
        if (!Database::available()) {
            return self::attemptFallback($email, $password);
        }

        $user = Database::fetchOne(
            "SELECT a.id, a.full_name, a.email, a.password_hash, a.status, a.avatar, r.name AS role_name
             FROM admins a
             JOIN roles r ON r.id = a.role_id
             WHERE a.email = :email
             LIMIT 1",
            ["email" => $email]
        );

        if ($user && password_verify($password, $user["password_hash"])) {
            if (($user["status"] ?? "active") !== "active") return false;
            $_SESSION["admin_user"] = [
                "id"    => (int)$user["id"],
                "name"  => $user["full_name"],
                "email" => $user["email"],
                "role"  => $user["role_name"],
                "status" => $user["status"],
                "avatar" => $user["avatar"],
            ];
            Database::execute(
                "UPDATE admins SET last_login_at = NOW() WHERE id = :id",
                ["id" => $user["id"]]
            );
            return true;
        }

        return false;
    }

    public static function createAccount(string $name, string $email, string $password, string $roleName = "admin"): array
    {
        $errors = [];

        if (trim($name) === "") $errors[] = "Full name is required";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
        if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters";

        if ($errors) {
            return ["success" => false, "errors" => $errors];
        }

        $role = Database::fetchOne("SELECT id FROM roles WHERE name = :name", ["name" => $roleName]);
        if (!$role) {
            return ["success" => false, "errors" => ["Role not found"]];
        }

        $existing = Database::fetchOne("SELECT id FROM admins WHERE email = :email", ["email" => $email]);
        if ($existing) {
            return ["success" => false, "errors" => ["An admin with this email already exists"]];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        Database::execute(
            "INSERT INTO admins (full_name, email, password_hash, role_id, status, created_at)
             VALUES (:name, :email, :hash, :role_id, 'active', NOW())",
            ["name" => $name, "email" => $email, "hash" => $hash, "role_id" => $role["id"]]
        );

        return ["success" => true, "id" => Database::lastInsertId()];
    }

    public static function requireLogin(): void
    {
        if (self::isLoggedIn()) return;

        if (self::setupRequired()) {
            Helpers::redirect(Helpers::adminUrl("setup.php"));
        }

        Helpers::redirect(Helpers::adminUrl("login.php"));
    }

    public static function logout(): void
    {
        unset($_SESSION["admin_user"]);
        session_regenerate_id(true);
    }

    private static function attemptFallback(string $email, string $password): bool
    {
        $defaults = Helpers::appConfig("default_admins", []);
        foreach ($defaults as $admin) {
            if ($admin["email"] === $email && $admin["password"] === $password) {
                $_SESSION["admin_user"] = [
                    "id"     => 0,
                    "name"   => $admin["name"] ?? "Admin",
                    "email"  => $admin["email"],
                    "role"   => "super_admin",
                    "status" => "active",
                ];
                return true;
            }
        }
        return false;
    }
}

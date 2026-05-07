<?php

declare(strict_types=1);

function current_admin(): ?array
{
    return $_SESSION["admin_user"] ?? null;
}

function is_admin_logged_in(): bool
{
    return current_admin() !== null;
}

function attempt_admin_login(string $email, string $password): bool
{
    $admins = app_config("default_admins", []);

    foreach ($admins as $admin) {
        if (strcasecmp($admin["email"], $email) === 0 && $admin["password"] === $password) {
            $_SESSION["admin_user"] = [
                "id" => $admin["id"],
                "name" => $admin["name"],
                "email" => $admin["email"],
                "role" => $admin["role"],
                "status" => $admin["status"],
            ];

            return true;
        }
    }

    return false;
}

function require_admin_login(): void
{
    if (!is_admin_logged_in()) {
        header("Location: " . admin_url("login.php"));
        exit;
    }
}

function logout_admin(): void
{
    unset($_SESSION["admin_user"]);
}

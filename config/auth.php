<?php

declare(strict_types=1);

function admin_database_ready(): bool
{
    return database_available() && db_table_exists("admins") && db_table_exists("roles");
}

function admin_count(): int
{
    if (!admin_database_ready()) {
        return 0;
    }

    $result = db_fetch_one("SELECT COUNT(*) AS total FROM admins");

    return (int) ($result["total"] ?? 0);
}

function admin_setup_required(): bool
{
    return admin_database_ready() && admin_count() === 0;
}

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
    if (admin_database_ready()) {
        $admin = db_fetch_one(
            "SELECT a.id, a.full_name, a.email, a.password_hash, a.status, r.name AS role_name
             FROM admins a
             INNER JOIN roles r ON r.id = a.role_id
             WHERE a.email = :email
             LIMIT 1",
            ["email" => $email]
        );

        if ($admin && $admin["status"] === "active" && password_verify($password, $admin["password_hash"])) {
            $_SESSION["admin_user"] = [
                "id" => $admin["id"],
                "name" => $admin["full_name"],
                "email" => $admin["email"],
                "role" => $admin["role_name"],
                "status" => $admin["status"],
            ];

            return true;
        }
    }

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

function create_admin_account(string $fullName, string $email, string $password, string $roleName = "super_admin"): array
{
    if (!admin_database_ready()) {
        return [false, "Database is not ready for admin setup yet."];
    }

    if (admin_count() > 0) {
        return [false, "Admin setup has already been completed."];
    }

    $fullName = trim($fullName);
    $email = trim($email);

    if ($fullName === "" || $email === "" || $password === "") {
        return [false, "Full name, email, and password are required."];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [false, "Enter a valid email address."];
    }

    if (strlen($password) < 8) {
        return [false, "Password must be at least 8 characters long."];
    }

    $role = db_fetch_one(
        "SELECT id, name FROM roles WHERE name = :role_name LIMIT 1",
        ["role_name" => $roleName]
    );

    if (!$role) {
        return [false, "The required admin role is missing from the database."];
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $created = db_execute(
        "INSERT INTO admins (role_id, full_name, email, password_hash, status)
         VALUES (:role_id, :full_name, :email, :password_hash, 'active')",
        [
            "role_id" => $role["id"],
            "full_name" => $fullName,
            "email" => $email,
            "password_hash" => $passwordHash,
        ]
    );

    if (!$created) {
        return [false, "Admin setup could not be completed."];
    }

    return [true, ""];
}

function require_admin_login(): void
{
    if (!is_admin_logged_in()) {
        if (admin_setup_required()) {
            header("Location: " . admin_url("setup.php"));
            exit;
        }

        header("Location: " . admin_url("login.php"));
        exit;
    }
}

function logout_admin(): void
{
    unset($_SESSION["admin_user"]);
}

<?php

declare(strict_types=1);

return [
    "app_name" => (string) env("APP_NAME", "Gracious Charity Platform"),
    "app_url" => (string) env("APP_URL", "http://localhost/donation-page"),
    "admin_path" => (string) env("ADMIN_PATH", "/donation-page/admin"),
    "session_name" => (string) env("SESSION_NAME", "gracious_admin_session"),
    "timezone" => (string) env("APP_TIMEZONE", "Africa/Lagos"),
    "default_admins" => [
        [
            "id" => 1,
            "name" => (string) env("DEFAULT_ADMIN_NAME", "Super Admin"),
            "email" => (string) env("DEFAULT_ADMIN_EMAIL", "admin@graciouscharity.org"),
            "password" => (string) env("DEFAULT_ADMIN_PASSWORD", "ChangeMe123!"),
            "role" => "super_admin",
            "status" => "active",
        ],
    ],
    "roles" => [
        "super_admin" => "Full access, including admin management and settings",
        "admin" => "Manage content, donations, and programme records",
        "editor" => "Manage pages, FAQs, gallery, and partners",
        "finance" => "View donations, payment logs, and reports",
    ],
    "stats" => [
        "total_donations" => "NGN 4.8M",
        "monthly_donations" => "NGN 860K",
        "active_programmes" => 12,
        "partners" => 18,
        "pending_enquiries" => 9,
        "admins" => 4,
    ],
];

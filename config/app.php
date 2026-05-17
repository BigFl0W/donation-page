<?php

declare(strict_types=1);

return [
    "app_name" => (string) \App\Env::get("APP_NAME", "Friends At Heart Welfare Initiative"),
    "app_url" => (string) \App\Env::get("APP_URL", "http://localhost/donation-page"),
    "admin_path" => (string) \App\Env::get("ADMIN_PATH", "admin"),
    "session_name" => (string) \App\Env::get("SESSION_NAME", "gracious_admin_session"),
    "timezone" => (string) \App\Env::get("APP_TIMEZONE", "Africa/Lagos"),
    "default_admins" => [
        [
            "id" => 1,
            "name" => (string) \App\Env::get("DEFAULT_ADMIN_NAME", "Super Admin"),
            "email" => (string) \App\Env::get("DEFAULT_ADMIN_EMAIL", "admin@graciouscharity.org"),
            "password" => (string) \App\Env::get("DEFAULT_ADMIN_PASSWORD", "ChangeMe123!"),
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

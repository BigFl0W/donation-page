<?php

declare(strict_types=1);

return [
    "app_name" => "Gracious Charity Platform",
    "app_url" => "http://localhost/donation-page",
    "admin_path" => "/donation-page/admin",
    "session_name" => "gracious_admin_session",
    "timezone" => "Africa/Lagos",
    "default_admins" => [
        [
            "id" => 1,
            "name" => "Super Admin",
            "email" => "admin@graciouscharity.org",
            "password" => "ChangeMe123!",
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

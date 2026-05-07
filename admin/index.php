<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/bootstrap.php";

require_admin_login();

$page = $_GET["page"] ?? "dashboard";
$allowedPages = [
    "dashboard" => __DIR__ . "/modules/dashboard/index.php",
    "admins" => __DIR__ . "/modules/admins/index.php",
    "content" => __DIR__ . "/modules/content/index.php",
    "donations" => __DIR__ . "/modules/donations/index.php",
    "settings" => __DIR__ . "/modules/settings/index.php",
];

$view = $allowedPages[$page] ?? $allowedPages["dashboard"];
$pageTitles = [
    "dashboard" => "Dashboard",
    "admins" => "Admins & Roles",
    "content" => "Content Management",
    "donations" => "Donations & Gateways",
    "settings" => "Settings",
];
$pageDescriptions = [
    "dashboard" => "Monitor donations, content activity, payment channels, and admin operations.",
    "admins" => "Manage admin accounts, roles, and platform access.",
    "content" => "Control public-facing pages, Explore content, and reusable content blocks.",
    "donations" => "Track donations from Paystack and Stripe in one place.",
    "settings" => "Centralize platform identity, credentials, and global configuration.",
];
$pageTitle = $pageTitles[$page] ?? $pageTitles["dashboard"];
$pageDescription = $pageDescriptions[$page] ?? $pageDescriptions["dashboard"];

require __DIR__ . "/includes/header.php";
require __DIR__ . "/includes/sidebar.php";
?>
<main class="admin-main">
    <?php require $view; ?>
<?php require __DIR__ . "/includes/footer.php"; ?>

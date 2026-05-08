<?php
declare(strict_types=1);
use App\Helpers;

$currentAdmin = current_admin();
$pageTitle = $pageTitle ?? "Admin Dashboard";
$pageDescription = $pageDescription ?? "Manage the donation platform from one place.";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($pageTitle); ?> | Admin</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo Helpers::e(Helpers::siteUrl(Helpers::brandFaviconPath())); ?>">
    <link href="../assets/library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/library/icofont/icofont.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin-body">

<div id="wpadminbar" class="wp-admin-bar">
    <div class="quicklinks">
        <ul id="wp-admin-bar-root-default" class="ab-top-menu">
            <li id="wp-admin-bar-menu-toggle">
                <a class="ab-item" href="javascript:void(0)" id="admin-sidebar-toggle">
                    <i class="icofont-navigation-menu"></i>
                </a>
            </li>
            <li id="wp-admin-bar-site-name" class="menupop">
                <a class="ab-item" href="../index.php" target="_blank">
                    <i class="icofont-web"></i>
                    <span class="ab-label">Gracious Donation</span>
                </a>
            </li>
        </ul>
        <ul id="wp-admin-bar-top-secondary" class="ab-top-secondary ab-top-menu">
            <li id="wp-admin-bar-my-account" class="menupop with-avatar">
                <a class="ab-item" href="javascript:void(0)">
                    Howdy, <?php echo e($currentAdmin["name"] ?? "Admin"); ?>
                    <img src="https://secure.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=26&d=mm&r=g" class="avatar avatar-26 photo" height="26" width="26" loading="lazy">
                </a>
                <div class="ab-sub-wrapper">
                    <ul class="ab-submenu">
                        <li><a class="ab-item" href="<?php echo e(admin_url("logout.php")); ?>">Log Out</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>

<div class="admin-shell">
<div class="admin-sidebar-backdrop"></div>

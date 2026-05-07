<?php

declare(strict_types=1);

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
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon.ico">
    <link href="../assets/library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/library/icofont/icofont.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin-body">
<div class="admin-shell">

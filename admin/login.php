<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/bootstrap.php";

if (is_admin_logged_in()) {
    header("Location: " . admin_url("index.php"));
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim((string) ($_POST["email"] ?? ""));
    $password = (string) ($_POST["password"] ?? "");

    if (!attempt_admin_login($email, $password)) {
        $error = "Login failed. Use the scaffold admin account until database auth is wired.";
    } else {
        header("Location: " . admin_url("index.php"));
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | Gracious</title>
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon.ico">
    <link href="../assets/library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin-body">
<div class="admin-auth-wrap">
    <div class="admin-auth-card">
        <div class="admin-auth-brand">
            <img src="../assets/images/logo_dark.svg" alt="Gracious">
            <div>
                <h1>Gracious Admin</h1>
                <div class="admin-auth-meta">Secure dashboard access for content, donations, and admin control.</div>
            </div>
        </div>

        <?php if ($error !== ""): ?>
            <div class="admin-alert error"><?php echo e($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="admin-form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="admin@graciouscharity.org" required>
            </div>
            <div class="admin-form-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" value="ChangeMe123!" required>
            </div>
            <button class="admin-btn primary" type="submit">Sign In</button>
        </form>

        <p class="admin-helper mt-3 mb-0">
            Scaffold account: <strong>admin@graciouscharity.org</strong> / <strong>ChangeMe123!</strong>
        </p>
    </div>
</div>
</body>
</html>

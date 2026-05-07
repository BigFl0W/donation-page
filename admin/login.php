<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/bootstrap.php";

if (is_admin_logged_in()) {
    header("Location: " . admin_url("index.php"));
    exit;
}

if (admin_setup_required()) {
    header("Location: " . admin_url("setup.php"));
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim((string) ($_POST["email"] ?? ""));
    $password = (string) ($_POST["password"] ?? "");

    if (!attempt_admin_login($email, $password)) {
        $error = "Login failed. Check your email and password, or complete admin setup first.";
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin-body">
<div class="admin-auth-wrap">
    <div class="admin-auth-shell admin-auth-shell-single">
        <section class="admin-auth-card admin-auth-form-panel">
            <div class="admin-auth-brand admin-auth-brand-center">
                <img src="../assets/images/logo_dark.svg" alt="Gracious">
                <div>
                    <div class="admin-auth-kicker">Administration</div>
                    <h1 class="admin-auth-title admin-auth-title-dark">Sign In</h1>
                </div>
            </div>
            <div class="admin-auth-form-head">
                <p>Use your administrator credentials to continue.</p>
            </div>

            <?php if ($error !== ""): ?>
                <div class="admin-alert error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="admin-form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="" required>
                </div>
                <div class="admin-form-group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" value="" required>
                </div>
                <button class="admin-btn primary" type="submit">Sign In</button>
            </form>

            <p class="admin-helper admin-auth-footnote mb-0">
                Need a first account? <a href="<?php echo e(admin_url("setup.php")); ?>">Open admin setup</a>.
            </p>
        </section>
    </div>
</div>
</body>
</html>

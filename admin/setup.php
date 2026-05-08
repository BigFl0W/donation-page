<?php
declare(strict_types=1);

require_once __DIR__ . "/../config/autoload.php";

use App\Auth;
use App\Helpers;

if (Auth::isLoggedIn()) {
    Helpers::redirect(Helpers::adminUrl("index.php"));
}

$databaseReady = Auth::databaseReady();
$setupRequired = Auth::setupRequired();
$brandName = Helpers::brandName("Gracious Charity");
$brandLogo = Helpers::brandLogoPath("assets/images/logo_dark.svg");
$brandFavicon = Helpers::brandFaviconPath("assets/images/favicon.ico");
$error = "";
$success = "";
$form = [
    "full_name" => "",
    "email" => "",
];

if ($_SERVER["REQUEST_METHOD"] === "POST" && $setupRequired) {
    // CSRF
    $csrfToken = (string)($_POST["_csrf_token"] ?? "");
    if ($csrfToken === "" || !hash_equals($_SESSION["_csrf_token"] ?? "", $csrfToken)) {
        $error = "Invalid form token. Please reload and try again.";
    } else {
        $form["full_name"] = trim((string) ($_POST["full_name"] ?? ""));
        $form["email"] = trim((string) ($_POST["email"] ?? ""));
        $password = (string) ($_POST["password"] ?? "");
        $passwordConfirm = (string) ($_POST["password_confirm"] ?? "");

        if ($password !== $passwordConfirm) {
            $error = "Passwords do not match.";
        } else {
            $result = Auth::createAccount($form["full_name"], $form["email"], $password);
            if ($result["success"]) {
                $success = "Your admin account is ready. You can sign in now.";
                $setupRequired = false;
            } else {
                $error = implode(" ", $result["errors"] ?? ["Account creation failed."]);
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Setup | <?php echo Helpers::e($brandName); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="../<?php echo Helpers::e($brandFavicon); ?>">
    <link href="../assets/library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin-body">
<div class="admin-auth-wrap">
    <div class="admin-auth-shell admin-auth-shell-single">
        <section class="admin-auth-card admin-auth-form-panel">
            <div class="admin-auth-brand admin-auth-brand-center">
                <img src="../<?php echo Helpers::e($brandLogo); ?>" alt="<?php echo Helpers::e($brandName); ?>">
                <div>
                    <div class="admin-auth-kicker">Administration</div>
                    <h1 class="admin-auth-title admin-auth-title-dark">Admin Setup</h1>
                </div>
            </div>
            <div class="admin-auth-form-head">
                <p>Create the first administrator account for secure dashboard access.</p>
            </div>

            <?php if ($error !== ""): ?>
                <div class="admin-alert error"><?php echo Helpers::e($error); ?></div>
            <?php endif; ?>

            <?php if ($success !== ""): ?>
                <div class="admin-alert success"><?php echo Helpers::e($success); ?></div>
            <?php endif; ?>

            <?php if (!$databaseReady): ?>
                <div class="admin-alert error">The database is not ready yet. Import the schema first, then return here to create the first admin.</div>
                <a class="admin-btn light" href="<?php echo Helpers::e(Helpers::adminUrl("login.php")); ?>">Back to Sign In</a>
            <?php elseif ($setupRequired): ?>
                <form method="post">
                    <input type="hidden" name="_csrf_token" value="<?php echo Helpers::e($_SESSION["_csrf_token"] ?? ""); ?>">
                    <div class="admin-auth-grid">
                        <div class="admin-form-group">
                            <label for="full-name">Full Name</label>
                            <input id="full-name" name="full_name" type="text" value="<?php echo Helpers::e($form["full_name"]); ?>" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="setup-email">Email</label>
                            <input id="setup-email" name="email" type="email" value="<?php echo Helpers::e($form["email"]); ?>" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="setup-password">Password</label>
                            <input id="setup-password" name="password" type="password" minlength="8" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="setup-password-confirm">Confirm Password</label>
                            <input id="setup-password-confirm" name="password_confirm" type="password" minlength="8" required>
                        </div>
                    </div>
                    <button class="admin-btn primary" type="submit">Create Admin Account</button>
                </form>
                <p class="admin-helper admin-auth-footnote mb-0">This page is available only until the first admin account is created.</p>
            <?php else: ?>
                <div class="admin-alert success">Admin setup is already complete. Sign in with your existing admin account.</div>
                <a class="admin-btn primary" href="<?php echo Helpers::e(Helpers::adminUrl("login.php")); ?>">Go to Sign In</a>
            <?php endif; ?>
        </section>
    </div>
</div>
</body>
</html>

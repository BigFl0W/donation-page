<?php
declare(strict_types=1);

require_once __DIR__ . "/../config/autoload.php";

use App\Auth;
use App\Helpers;

if (Auth::isLoggedIn()) {
    Helpers::redirect(Helpers::adminUrl("index.php"));
}

if (Auth::setupRequired()) {
    Helpers::redirect(Helpers::adminUrl("setup.php"));
}

$error = "";
$brandName = Helpers::brandName("Gracious Charity");
$brandLogo = Helpers::brandLogoPath("assets/images/logo_dark.svg");
$brandFavicon = Helpers::brandFaviconPath("assets/images/favicon.ico");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // CSRF
    $csrfToken = (string)($_POST["_csrf_token"] ?? "");
    if ($csrfToken === "" || !hash_equals($_SESSION["_csrf_token"] ?? "", $csrfToken)) {
        $error = "Invalid form token. Please reload and try again.";
    } else {
        $email = trim((string) ($_POST["email"] ?? ""));
        $password = (string) ($_POST["password"] ?? "");

        if (!Auth::attempt($email, $password)) {
            $error = "Login failed. Check your email and password, or complete admin setup first.";
        } else {
            Helpers::redirect(Helpers::adminUrl("index.php"));
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In | <?php echo Helpers::e($brandName); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="../<?php echo Helpers::e($brandFavicon); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --brand:#0f766e;--brand-light:#14b8a6;--brand-dim:#ccfbf1;
            --amber:#d97706;--rose:#dc2626;--dark:#0c1220;--mid:#374151;
            --muted:#6b7280;--surface:#f9fafb;--white:#ffffff;--border:#e5e7eb;
            --shadow-lg:0 8px 32px rgba(0,0,0,.12);
        }
        body{
            font-family:'Plus Jakarta Sans',sans-serif;
            background:linear-gradient(135deg,#f0fdfa 0%,#f9fafb 50%,#eff6ff 100%);
            min-height:100vh;display:flex;align-items:center;justify-content:center;
            padding:1.5rem;margin:0;
        }
        .auth-wrap{width:100%;max-width:420px}
        .auth-card{
            background:var(--white);border-radius:20px;
            padding:2.5rem 2rem 2rem;
            box-shadow:var(--shadow-lg);
            border:1px solid var(--border);
        }
        .auth-brand{text-align:center;margin-bottom:1.75rem}
        .brand-icon{display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px}
        .brand-icon img{max-width:220px;height:auto;display:block}
        .auth-title{
            font-family:'Instrument Serif',serif;
            font-size:1.6rem;color:var(--dark);line-height:1.2;margin-bottom:4px;
        }
        .auth-sub{font-size:.85rem;color:var(--muted)}
        .auth-error{
            display:flex;align-items:center;gap:8px;
            padding:11px 14px;border-radius:10px;
            background:#fef2f2;border:1px solid #fecaca;
            color:#991b1b;font-size:.82rem;margin-bottom:1.25rem;
        }
        .auth-error i{flex-shrink:0;font-size:.9rem}
        .form-group{margin-bottom:1.1rem}
        .form-group label{
            display:block;font-size:.78rem;font-weight:600;
            color:var(--mid);margin-bottom:5px;
        }
        .form-input{
            width:100%;padding:11px 14px;
            border:1.5px solid var(--border);border-radius:11px;
            font-family:'Plus Jakarta Sans',sans-serif;font-size:.88rem;
            color:var(--dark);outline:none;transition:all .2s;
            background:var(--surface);
        }
        .form-input:focus{
            border-color:var(--brand-light);
            box-shadow:0 0 0 3px rgba(20,184,166,.12);
        }
        .form-input::placeholder{color:var(--soft)}
        .btn-submit{
            width:100%;padding:12px;border:none;border-radius:11px;
            background:var(--brand);color:#fff;
            font-family:'Plus Jakarta Sans',sans-serif;
            font-size:.9rem;font-weight:600;cursor:pointer;
            transition:background .18s,transform .1s;
            display:flex;align-items:center;justify-content:center;gap:8px;
            margin-top:.5rem;
        }
        .btn-submit:hover{background:var(--brand-light)}
        .btn-submit:active{transform:scale(.98)}
        .auth-foot{text-align:center;margin-top:1.25rem;font-size:.8rem;color:var(--muted)}
        .auth-foot a{color:var(--brand);font-weight:600;text-decoration:none}
        .auth-foot a:hover{text-decoration:underline}
        .auth-divider{
            display:flex;align-items:center;gap:12px;margin:1.25rem 0;
            color:var(--soft);font-size:.75rem;
        }
        .auth-divider::before,.auth-divider::after{content:'';flex:1;height:1px;background:var(--border)}
        .alert-banner{
            display:flex;align-items:center;gap:8px;
            padding:10px 14px;border-radius:10px;
            background:var(--brand-bg);border:1px solid var(--brand-dim);
            color:var(--brand);font-size:.78rem;margin-bottom:1.25rem;
        }
        .alert-banner i{flex-shrink:0}
    </style>
</head>
<body>
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="brand-icon"><img src="../<?php echo Helpers::e($brandLogo); ?>" alt="<?php echo Helpers::e($brandName); ?>"></div>
                <div class="auth-title">Welcome Back</div>
                <div class="auth-sub">Sign in to manage <?php echo Helpers::e($brandName); ?></div>
            </div>

            <?php if ($error !== ""): ?>
                <div class="auth-error"><i class="fas fa-circle-exclamation"></i><?php echo Helpers::e($error); ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="_csrf_token" value="<?php echo Helpers::e($_SESSION["_csrf_token"] ?? ""); ?>">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" name="email" type="email" class="form-input" placeholder="admin@example.org" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" class="form-input" placeholder="Enter your password" required>
                </div>
                <button class="btn-submit" type="submit"><i class="fas fa-arrow-right-to-bracket"></i> Sign In</button>
            </form>

            <div class="auth-foot">
                Need a first account? <a href="<?php echo Helpers::e(Helpers::adminUrl("setup.php")); ?>">Create one</a>
            </div>
        </div>
    </div>
</body>
</html>

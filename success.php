<?php
declare(strict_types=1);

require_once __DIR__ . '/config/autoload.php';

$brandName = \App\Helpers::brandName('Friends At Heart Welfare Initiative');
$brandFavicon = \App\Helpers::brandFaviconPath('assets/images/favicon.ico');
$amount = (float)($_GET['amount'] ?? 0);
$currency = strtoupper((string)($_GET['currency'] ?? 'NGN'));
$reference = trim((string)($_GET['ref'] ?? ''));
$campaign = trim((string)($_GET['campaign'] ?? ''));

function successCurrencyLabel(string $currency): string
{
    return in_array($currency, ['NGN', 'NG'], true)
        ? '&#8358;'
        : htmlspecialchars($currency, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Donation Successful | <?php echo htmlspecialchars($brandName, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($brandFavicon, ENT_QUOTES, 'UTF-8'); ?>">
    <link href="assets/library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(180deg, #f8f3ea 0%, #f7f9fc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }
        .success-card {
            background: white;
            padding: 42px;
            border-radius: 24px;
            box-shadow: 0 18px 45px rgba(17, 24, 39, 0.12);
            text-align: center;
            max-width: 560px;
            width: 100%;
        }
        .success-icon {
            width: 86px;
            height: 86px;
            background: #0f8a5f;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 20px;
            animation: scaleIn 0.4s ease-out;
        }
        @keyframes scaleIn {
            from { transform: scale(0.85); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .success-kicker {
            color: #0f8a5f;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-size: 0.8rem;
            margin-bottom: 8px;
        }
        .success-title {
            font-size: 2rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 12px;
        }
        .success-copy {
            color: #667085;
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 22px;
        }
        .campaign {
            color: #475569;
            font-size: 0.95rem;
            margin-bottom: 10px;
        }
        .amount {
            font-size: 2.2rem;
            font-weight: 800;
            color: #111827;
            margin: 10px 0 8px;
        }
        .ref {
            font-family: Consolas, Monaco, monospace;
            color: #6b7280;
            font-size: 0.88rem;
            margin-bottom: 28px;
            word-break: break-word;
        }
        .btn-home {
            background: #011B33;
            color: white;
            padding: 13px 32px;
            border-radius: 12px;
            text-decoration: none;
            display: inline-block;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(1, 27, 51, 0.18);
            color: white;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon"><i class="fas fa-check"></i></div>
        <div class="success-kicker">Donation Received</div>
        <div class="success-title">Thank you for your generosity</div>
        <p class="success-copy">Your payment was successful and your support has been recorded. A receipt will be sent to your email address automatically.</p>

        <?php if ($campaign !== ''): ?>
            <div class="campaign">Support received for: <strong><?php echo htmlspecialchars($campaign, ENT_QUOTES, 'UTF-8'); ?></strong></div>
        <?php endif; ?>

        <div class="amount"><?php echo successCurrencyLabel($currency); ?> <?php echo number_format($amount, 2); ?></div>
        <div class="ref">Reference: <?php echo htmlspecialchars($reference !== '' ? $reference : '—', ENT_QUOTES, 'UTF-8'); ?></div>

        <a href="<?php echo htmlspecialchars(\App\Helpers::siteUrl(), ENT_QUOTES, 'UTF-8'); ?>" class="btn-home">Return to Home</a>
    </div>
</body>
</html>

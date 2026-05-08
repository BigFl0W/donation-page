<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Donation Successful | Gracious Charity</title>
    <link href="../assets/library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .success-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); text-align: center; max-width: 500px; width: 100%; }
        .success-icon { width: 80px; height: 80px; background: #059669; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 20px; animation: scaleIn 0.5s ease-out; }
        @keyframes scaleIn { from { transform: scale(0); } to { transform: scale(1); } }
        .amount { font-size: 32px; font-weight: 700; color: #111827; margin: 10px 0; }
        .ref { font-family: monospace; color: #6b7280; font-size: 14px; margin-bottom: 30px; }
        .btn-home { background: #011B33; color: white; padding: 12px 30px; border-radius: 10px; text-decoration: none; display: inline-block; transition: transform 0.2s; }
        .btn-home:hover { transform: translateY(-2px); color: white; }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon"><i class="fas fa-check"></i></div>
        <h2 class="fw-bold">Payment Successful!</h2>
        <p class="text-muted">Thank you for your generous donation. Your support makes a real difference.</p>
        
        <div class="amount"><?php 
            $curr = $_GET['currency'] ?? 'NGN';
            echo ($curr === 'NGN' || $curr === 'NG') ? '₦' : htmlspecialchars($curr);
        ?> <?php echo number_format((float)($_GET['amount'] ?? 0), 2); ?></div>
        <div class="ref">Ref: <?php echo htmlspecialchars($_GET['ref'] ?? '—'); ?></div>
        
        <a href="../index.php" class="btn-home">Return to Home</a>
    </div>
</body>
</html>

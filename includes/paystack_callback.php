<?php
declare(strict_types=1);
require_once __DIR__ . "/../config/autoload.php";

use App\Payment;
use App\Helpers;

$reference = $_GET['reference'] ?? "";

if ($reference === "") {
    die("No reference supplied");
}

$res = Payment::verify($reference);

if ($res['status'] && $res['data']['status'] === 'success') {
    $data = $res['data'];
    $amount = $data['amount'] / 100;
    
    // Get metadata
    $meta = $data['metadata'] ?? [];
    $donorName = $meta['donor_name'] ?? "Anonymous";

    Payment::recordDonation([
        'donor_name' => $donorName,
        'donor_email' => $data['customer']['email'],
        'amount' => $amount,
        'currency' => $data['currency'],
        'reference' => $reference,
        'status' => 'successful',
        'metadata' => $data,
        'paid_at' => date("Y-m-d H:i:s", strtotime($data['paid_at']))
    ]);

    // Redirect to success page
    header("Location: success.php?amount=" . $amount . "&currency=" . $data['currency'] . "&ref=" . $reference);
    exit;
} else {
    // Update status to failed
    Payment::recordDonation([
        'donor_name' => "Unknown",
        'donor_email' => "unknown@email.com",
        'amount' => 0,
        'reference' => $reference,
        'status' => 'failed'
    ]);
    die("Payment verification failed or was cancelled.");
}

function number_export($n) { return number_format((float)$n, 2); }

<?php
declare(strict_types=1);
require_once __DIR__ . "/../config/autoload.php";

use App\Payment;
use App\Env;

// Only respond to POST requests from Paystack
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$input = file_get_contents('php://input');
$event = json_decode($input, true);

// Verify signature
$paystackSignature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? "";
$secretKey = (string) Env::get("PAYSTACK_SECRET_KEY", "");

if ($paystackSignature !== hash_hmac('sha512', $input, $secretKey)) {
    exit;
}

http_response_code(200); // Acknowledge receipt

if ($event['event'] === 'charge.success') {
    $data = $event['data'];
    $reference = $data['reference'];
    $amount = $data['amount'] / 100;
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

    // Send Receipt Email
    Payment::sendReceipt([
        'donor_name' => $donorName,
        'donor_email' => $data['customer']['email'],
        'amount' => $amount,
        'currency' => $data['currency'],
        'reference' => $reference,
        'paid_at' => date("M j, Y H:i", strtotime($data['paid_at']))
    ]);
}

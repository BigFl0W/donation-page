<?php
require_once 'config/autoload.php';

use App\Database;
use App\Payment;

$action = $_GET['action'] ?? '';

if ($action === 'verify_payment') {
    $input = json_decode(file_get_contents('php://input'), true);
    $reference = (string)($input['reference'] ?? '');
    $cause_id = (int)($input['cause_id'] ?? 0);
    $amount = (float)($input['amount'] ?? 0);

    if ($reference === '') {
        echo json_encode(['status' => 'error', 'message' => 'No reference provided']);
        exit;
    }

    $verification = Payment::verify($reference);
    if (!($verification['status'] ?? false)) {
        echo json_encode(['status' => 'error', 'message' => 'API returned error: ' . ($verification['message'] ?? 'Unable to verify payment')]);
        exit;
    }

    $tranx = $verification['data'] ?? [];
    if (($tranx['status'] ?? '') !== 'success') {
        echo json_encode(['status' => 'error', 'message' => 'Transaction status: ' . ($tranx['status'] ?? 'unknown')]);
        exit;
    }

    $verifiedAmount = isset($tranx['amount']) ? ((float)$tranx['amount'] / 100) : $amount;
    $donorEmail = (string)($tranx['customer']['email'] ?? '');
    $metadata = is_array($tranx['metadata'] ?? null) ? $tranx['metadata'] : [];
    $donorName = trim((string)($metadata['donor_name'] ?? ''));
    if ($donorName === '') {
        $donorName = 'Anonymous Supporter';
    }
    $paidAtDb = !empty($tranx['paid_at']) ? date("Y-m-d H:i:s", strtotime((string)$tranx['paid_at'])) : date("Y-m-d H:i:s");
    $paidAtReceipt = !empty($tranx['paid_at']) ? date("M j, Y H:i", strtotime((string)$tranx['paid_at'])) : date("M j, Y H:i");

    if ($cause_id > 0) {
        Database::execute(
            "UPDATE programmes SET raised_amount = raised_amount + :amount WHERE id = :id",
            ['amount' => $verifiedAmount, 'id' => $cause_id]
        );
    }

    $c = $cause_id > 0 ? Database::fetchOne("SELECT title FROM programmes WHERE id = :id", ['id' => $cause_id]) : null;
    $campaign = $c ? (string)$c['title'] : 'General Donation';

    try {
        Payment::recordDonation([
            'donor_name' => $donorName,
            'donor_email' => $donorEmail,
            'amount' => $verifiedAmount,
            'currency' => (string)($tranx['currency'] ?? 'NGN'),
            'reference' => $reference,
            'status' => 'successful',
            'metadata' => array_merge($tranx, ['campaign' => $campaign, 'cause_id' => $cause_id]),
            'paid_at' => $paidAtDb,
        ]);

        Payment::sendReceiptIfNeeded([
            'donor_name' => $donorName,
            'donor_email' => $donorEmail,
            'amount' => $verifiedAmount,
            'currency' => (string)($tranx['currency'] ?? 'NGN'),
            'reference' => $reference,
            'paid_at' => $paidAtReceipt,
        ]);
    } catch (\Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => 'Could not finalize donation record']);
        exit;
    }

    echo json_encode(['status' => 'success']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);

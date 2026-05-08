<?php
require_once 'config/autoload.php';
use App\Database;

$data = [
    'donor_name' => 'Test User',
    'donor_email' => 'test@example.com',
    'amount' => 10.00,
    'currency' => 'NGN',
    'reference' => 'test_ref_' . time(),
    'status' => 'successful',
    'metadata' => ['test' => true],
    'paid_at' => date("Y-m-d H:i:s")
];

$db = Database::connection();
$sql = "INSERT INTO donations (donor_name, donor_email, amount, currency, gateway, payment_reference, status, metadata, paid_at, created_at)
             VALUES (:name, :email, :amount, :currency, 'paystack', :ref, :status, :meta, :paid_at, NOW())
             ON DUPLICATE KEY UPDATE status = VALUES(status), paid_at = VALUES(paid_at)";

try {
    $stmt = $db->prepare($sql);
    $params = [
        'name' => $data['donor_name'],
        'email' => $data['donor_email'],
        'amount' => $data['amount'],
        'currency' => $data['currency'],
        'ref' => $data['reference'],
        'status' => $data['status'],
        'meta' => json_encode($data['metadata']),
        'paid_at' => $data['paid_at']
    ];
    $res = $stmt->execute($params);
    echo "Result: " . ($res ? "Success" : "Fail") . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

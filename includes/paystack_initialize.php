<?php
declare(strict_types=1);
require_once __DIR__ . "/../config/autoload.php";

use App\Payment;
use App\Helpers;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? "");
    $amount = (float)($_POST['amount'] ?? 0);
    $firstName = trim($_POST['first_name'] ?? "");
    $lastName = trim($_POST['last_name'] ?? "");
    $fullName = trim($firstName . ' ' . $lastName);

    if ($email === "" || $amount <= 0) {
        die("Invalid donation details");
    }

    $res = Payment::initialize([
        'email' => $email,
        'amount' => $amount,
        'metadata' => [
            'donor_name' => $fullName,
            'custom_fields' => [
                ['display_name' => "Donor Name", 'variable_name' => "donor_name", 'value' => $fullName]
            ]
        ]
    ]);

    if ($res['status'] && isset($res['data']['authorization_url'])) {
        // Record as pending
        Payment::recordDonation([
            'donor_name' => $fullName,
            'donor_email' => $email,
            'amount' => $amount,
            'reference' => $res['data']['reference'],
            'status' => 'pending'
        ]);

        header("Location: " . $res['data']['authorization_url']);
        exit;
    } else {
        die("Could not initialize Paystack payment: " . ($res['message'] ?? "Unknown error"));
    }
}

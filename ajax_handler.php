<?php
require_once 'config/autoload.php';

$action = $_GET['action'] ?? '';

if ($action === 'verify_payment') {
    $input = json_decode(file_get_contents('php://input'), true);
    $reference = $input['reference'] ?? '';
    $cause_id = (int)($input['cause_id'] ?? 0);
    $amount = (float)($input['amount'] ?? 0);

    if (empty($reference)) {
        echo json_encode(['status' => 'error', 'message' => 'No reference provided']);
        exit;
    }

    $secret_key = $_ENV['PAYSTACK_SECRET_KEY'] ?? '';
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . $secret_key,
            "Cache-Control: no-cache",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo json_encode(['status' => 'error', 'message' => 'Curl error: ' . $err]);
        exit;
    }

    $tranx = json_decode($response);

    if (!$tranx->status) {
        echo json_encode(['status' => 'error', 'message' => 'API returned error: ' . $tranx->message]);
        exit;
    }

    if ('success' == $tranx->data->status) {
        // Payment is valid!
        // Update the cause raised_amount
        if ($cause_id > 0) {
            \App\Database::execute(
                "UPDATE programmes SET raised_amount = raised_amount + :amount WHERE id = :id",
                ['amount' => $amount, 'id' => $cause_id]
            );
            
            // Get cause title for the donation record
            $c = \App\Database::fetchOne("SELECT title FROM programmes WHERE id = :id", ['id' => $cause_id]);
            $campaign = $c ? $c['title'] : 'General Donation';

            // Log the donation in donations table
            try {
                \App\Database::execute(
                    "INSERT INTO donations (donor_email, campaign, currency, amount, gateway, status, payment_reference, paid_at, created_at) 
                     VALUES (:email, :campaign, 'NGN', :amt, 'paystack', 'successful', :ref, NOW(), NOW())",
                    [
                        'email' => $tranx->data->customer->email,
                        'campaign' => $campaign,
                        'amt' => $amount,
                        'ref' => $reference
                    ]
                );

                // Send Notification to Admin
                \App\Database::execute(
                    "INSERT INTO admin_notifications (title, message, icon, link, created_at)
                     VALUES (:title, :msg, :icon, :link, NOW())",
                    [
                        'title' => 'New Donation Received!',
                        'msg' => "A donation of ₦" . number_format($amount) . " was received from " . $tranx->data->customer->email . " for " . $campaign,
                        'icon' => 'fas fa-hand-holding-dollar',
                        'link' => 'admin/index.php?page=donations'
                    ]
                );
            } catch (\Exception $e) {
                // Ignore error if insert fails
            }
        }

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Transaction status: ' . $tranx->data->status]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);

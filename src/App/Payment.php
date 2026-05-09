<?php
declare(strict_types=1);

namespace App;

class Payment
{
    private static function getSecretKey(): string
    {
        return (string) Env::get("PAYSTACK_SECRET_KEY", "");
    }

    public static function initialize(array $data): array
    {
        $url = "https://api.paystack.co/transaction/initialize";
        $fields = [
            'email' => $data['email'],
            'amount' => $data['amount'] * 100, // Paystack uses kobo/cents
            'callback_url' => $data['callback_url'] ?? (Helpers::siteUrl() . '/includes/paystack_callback.php'),
            'metadata' => json_encode($data['metadata'] ?? [])
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . self::getSecretKey(),
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?: [];
    }

    public static function verify(string $reference): array
    {
        $url = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . self::getSecretKey(),
            "Cache-Control: no-cache"
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?: [];
    }

    public static function recordDonation(array $data): bool
    {
        return Database::execute(
            "INSERT INTO donations (donor_name, donor_email, amount, currency, gateway, payment_reference, status, metadata, paid_at, created_at)
             VALUES (:name, :email, :amount, :currency, 'paystack', :ref, :status, :meta, :paid_at, NOW())
             ON DUPLICATE KEY UPDATE status = VALUES(status), paid_at = VALUES(paid_at)",
            [
                'name' => $data['donor_name'],
                'email' => $data['donor_email'],
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'NGN',
                'ref' => $data['reference'],
                'status' => $data['status'],
                'meta' => json_encode($data['metadata'] ?? []),
                'paid_at' => $data['paid_at'] ?? null
            ]
        );
    }

    public static function sendReceipt(array $donation): bool
    {
        $to = $donation['donor_email'];
        $subject = "Donation Receipt - " . $donation['reference'];
        $amount = number_format((float)$donation['amount'], 2);
        $currency = ($donation['currency'] === 'NGN' || $donation['currency'] === 'NG') ? '₦' : $donation['currency'];
        
        $message = "
        <html>
        <head>
            <style>
                .receipt { font-family: sans-serif; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
                .header { text-align: center; border-bottom: 2px solid #011B33; padding-bottom: 10px; margin-bottom: 20px; }
                .amount { font-size: 24px; font-weight: bold; color: #011B33; text-align: center; margin: 20px 0; }
                .footer { font-size: 12px; color: #777; margin-top: 30px; text-align: center; }
            </style>
        </head>
        <body>
            <div class='receipt'>
                <div class='header'>
                    <h2>Gracious Charity Receipt</h2>
                </div>
                <p>Hello <strong>{$donation['donor_name']}</strong>,</p>
                <p>Thank you for your generous donation. Your support helps us continue our mission.</p>
                
                <div class='amount'>{$currency} {$amount}</div>
                
                <table width='100%'>
                    <tr><td><strong>Reference:</strong></td><td>{$donation['reference']}</td></tr>
                    <tr><td><strong>Date:</strong></td><td>{$donation['paid_at']}</td></tr>
                    <tr><td><strong>Status:</strong></td><td>Successful</td></tr>
                </table>
                
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Gracious Charity. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return Mailer::send($to, $subject, $message);
    }

    public static function getTotalDonations(): float
    {
        $result = Database::fetchOne(
            "SELECT SUM(amount) as total FROM donations WHERE status = 'success' OR status = 'paid'"
        );
        return (float) ($result['total'] ?? 0);
    }
}

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
            'amount' => $data['amount'] * 100,
            'callback_url' => $data['callback_url'] ?? (Helpers::siteUrl() . '/includes/paystack_callback.php'),
            'metadata' => json_encode($data['metadata'] ?? []),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . self::getSecretKey(),
            "Cache-Control: no-cache",
            "Content-Type: application/json",
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
            "Cache-Control: no-cache",
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?: [];
    }

    public static function recordDonation(array $data): bool
    {
        $success = Database::execute(
            "INSERT INTO donations (donor_name, donor_email, amount, currency, gateway, payment_reference, status, metadata, paid_at, created_at)
             VALUES (:name, :email, :amount, :currency, 'paystack', :ref, :status, :meta, :paid_at, NOW())
             ON DUPLICATE KEY UPDATE donor_name = VALUES(donor_name), donor_email = VALUES(donor_email),
             amount = VALUES(amount), currency = VALUES(currency), status = VALUES(status),
             metadata = VALUES(metadata), paid_at = VALUES(paid_at)",
            [
                'name' => $data['donor_name'],
                'email' => $data['donor_email'],
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'NGN',
                'ref' => $data['reference'],
                'status' => $data['status'],
                'meta' => json_encode($data['metadata'] ?? []),
                'paid_at' => $data['paid_at'] ?? null,
            ]
        );

        if ($success && in_array((string) $data['status'], ['success', 'successful'], true)) {
            Database::execute(
                "INSERT INTO admin_notifications (title, message, icon, link, created_at)
                 VALUES (:title, :msg, :icon, :link, NOW())",
                [
                    'title' => 'New Donation!',
                    'msg' => "A donation of " . ($data['currency'] ?? 'NGN') . ' ' . number_format((float) $data['amount']) . " was received from " . $data['donor_name'],
                    'icon' => 'fas fa-hand-holding-heart',
                    'link' => '?page=donations',
                ]
            );
        }

        return $success;
    }

    public static function sendReceiptIfNeeded(array $donation): bool
    {
        $reference = (string) ($donation['reference'] ?? '');
        if ($reference === '') {
            return false;
        }

        $row = Database::fetchOne(
            "SELECT metadata FROM donations WHERE payment_reference = :ref LIMIT 1",
            ['ref' => $reference]
        );

        $metadata = [];
        if (!empty($row['metadata'])) {
            $decoded = json_decode((string) $row['metadata'], true);
            if (is_array($decoded)) {
                $metadata = $decoded;
            }
        }

        if (!empty($metadata['receipt_sent_at'])) {
            return true;
        }

        $sent = self::sendReceipt($donation);
        if (!$sent) {
            return false;
        }

        $metadata['receipt_sent_at'] = date('c');
        $metadata['receipt_recipient'] = (string) ($donation['donor_email'] ?? '');

        Database::execute(
            "UPDATE donations SET metadata = :meta WHERE payment_reference = :ref",
            ['meta' => json_encode($metadata), 'ref' => $reference]
        );

        return true;
    }

    public static function sendReceipt(array $donation): bool
    {
        $to = (string) ($donation['donor_email'] ?? '');
        $subject = "Donation Receipt - " . (string) ($donation['reference'] ?? '');
        $amount = number_format((float) ($donation['amount'] ?? 0), 2);
        $currencyCode = (string) ($donation['currency'] ?? 'NGN');
        $currencyLabel = in_array($currencyCode, ['NGN', 'NG'], true) ? 'NGN' : $currencyCode;
        $brandName = Helpers::brandName('Friends At Heart Welfare Initiative');
        $receiptDate = (string) ($donation['paid_at'] ?? date('M j, Y H:i'));
        $donorName = (string) ($donation['donor_name'] ?? 'Supporter');
        $reference = (string) ($donation['reference'] ?? '');

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
                    <h2>{$brandName} Donation Receipt</h2>
                </div>
                <p>Hello <strong>{$donorName}</strong>,</p>
                <p>Thank you for your generous donation. Your support helps us continue serving children, families and communities in need.</p>
                <div class='amount'>{$currencyLabel} {$amount}</div>
                <table width='100%'>
                    <tr><td><strong>Reference:</strong></td><td>{$reference}</td></tr>
                    <tr><td><strong>Date:</strong></td><td>{$receiptDate}</td></tr>
                    <tr><td><strong>Status:</strong></td><td>Successful</td></tr>
                </table>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " {$brandName}. All rights reserved.</p>
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
            "SELECT SUM(amount) as total FROM donations WHERE status = 'success' OR status = 'successful' OR status = 'paid'"
        );

        return (float) ($result['total'] ?? 0);
    }
}

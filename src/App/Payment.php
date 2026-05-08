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
}

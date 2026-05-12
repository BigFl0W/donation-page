<?php
require_once __DIR__ . '/config/autoload.php';

$targetUrl = trim((string)($_GET['url'] ?? ''));
$source = trim((string)($_GET['source'] ?? 'partner_link'));

if ($targetUrl !== '' && filter_var($targetUrl, FILTER_VALIDATE_URL)) {
    \App\Database::execute(
        "INSERT INTO settings (setting_group, setting_key, setting_value)
         VALUES ('partners', 'partners_registration_clicks', '1')
         ON DUPLICATE KEY UPDATE setting_group = VALUES(setting_group), setting_value = CAST(setting_value AS UNSIGNED) + 1"
    );

    $sourceKey = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($source));
    if ($sourceKey !== '') {
        \App\Database::execute(
            "INSERT INTO settings (setting_group, setting_key, setting_value)
             VALUES ('partners', :key, '1')
             ON DUPLICATE KEY UPDATE setting_group = VALUES(setting_group), setting_value = CAST(setting_value AS UNSIGNED) + 1",
            ['key' => 'partners_clicks_' . $sourceKey]
        );
    }

    header('Location: ' . $targetUrl, true, 302);
    exit;
}

header('Location: partners-sponsors.php', true, 302);
exit;

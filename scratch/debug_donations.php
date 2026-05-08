<?php
require_once 'config/autoload.php';
use App\Database;
$res = Database::fetchAll("SELECT * FROM donations");
echo "Donations:\n";
foreach($res as $r) {
    echo "- ID: {$r['id']}, Name: {$r['donor_name']}, Amount: {$r['amount']}, Status: {$r['status']}, Ref: {$r['payment_reference']}\n";
}

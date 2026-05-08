<?php
require_once 'config/autoload.php';
use App\Database;
$res = Database::fetchAll("DESCRIBE donations");
echo "Donations Schema:\n";
foreach($res as $r) echo "- {$r['Field']} ({$r['Type']})\n";

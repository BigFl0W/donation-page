<?php
require_once 'config/autoload.php';
use App\Database;
$res = Database::fetchAll("DESCRIBE donations");
foreach($res as $r) {
    echo "Field: {$r['Field']}, Null: {$r['Null']}, Default: {$r['Default']}\n";
}

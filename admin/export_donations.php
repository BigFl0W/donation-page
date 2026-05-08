<?php
declare(strict_types=1);
require_once __DIR__ . "/../config/autoload.php";

use App\Database;
use App\Auth;

// Protect the route
if (!Auth::check()) {
    die("Unauthorized");
}

$fStatus = $_GET['status'] ?? '';
$fGateway = $_GET['gateway'] ?? '';
$fSearch = $_GET['search'] ?? '';

$sql = "SELECT donor_name, donor_email, amount, currency, gateway, payment_reference, status, COALESCE(paid_at,created_at) AS dt FROM donations WHERE 1=1";
$p = [];

if ($fStatus) { $sql .= " AND status = :st"; $p['st'] = $fStatus; }
if ($fGateway) { $sql .= " AND gateway = :gw"; $p['gw'] = $fGateway; }
if ($fSearch) { 
    $sql .= " AND (donor_name LIKE :s OR payment_reference LIKE :s OR donor_email LIKE :s)"; 
    $p['s'] = "%$fSearch%"; 
}

$sql .= " ORDER BY dt DESC";
$donations = Database::fetchAll($sql, $p);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=donations_export_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Donor Name', 'Donor Email', 'Amount', 'Currency', 'Gateway', 'Reference', 'Status', 'Date']);

foreach ($donations as $row) {
    fputcsv($output, [
        $row['donor_name'],
        $row['donor_email'],
        $row['amount'],
        $row['currency'],
        $row['gateway'],
        $row['payment_reference'],
        $row['status'],
        $row['dt']
    ]);
}

fclose($output);
exit;

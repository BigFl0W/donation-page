<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/autoload.php';

use App\Helpers;

$query = $_SERVER['QUERY_STRING'] ?? '';
$target = Helpers::siteUrl('success');

if ($query !== '') {
    $target .= '?' . $query;
}

header('Location: ' . $target, true, 302);
exit;

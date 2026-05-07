<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/bootstrap.php";

logout_admin();

header("Location: " . admin_url("login.php"));
exit;

<?php

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/TenantRepository.php';

// Simple .env loader
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Default values if not set
$_ENV['APP_BASE_DOMAIN'] = $_ENV['APP_BASE_DOMAIN'] ?? 'admin.localhost';
$_ENV['DB_PATH'] = $_ENV['DB_PATH'] ?? __DIR__ . '/../database/database.sqlite';

use App\Database;
use App\TenantRepository;

$db = Database::getConnection();
$tenantRepo = new TenantRepository($db);

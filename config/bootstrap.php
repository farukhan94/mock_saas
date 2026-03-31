<?php

// 1. Try to load the Composer autoloader first (for Production/Vercel)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    // 2. Manual fallback for local legacy development
    require_once __DIR__ . '/../src/Database.php';
    require_once __DIR__ . '/../src/CloudflareD1Handler.php';
    require_once __DIR__ . '/../src/TenantRepository.php';
    require_once __DIR__ . '/../src/CloudflareSaasService.php';
}

// Simple .env loader
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, '#') === 0 || empty($line))
            continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Default values
$_ENV['APP_ENV'] = $_ENV['APP_ENV'] ?? 'local';
$_ENV['APP_BASE_DOMAIN'] = $_ENV['APP_BASE_DOMAIN'] ?? 'admin.localhost';
$_ENV['DB_PATH'] = $_ENV['DB_PATH'] ?? __DIR__ . '/../database/database.sqlite';

use App\Database;
use App\TenantRepository;
use App\CloudflareSaasService;

$db = Database::getConnection();
$tenantRepo = new TenantRepository($db);

$cloudflare = new CloudflareSaasService(
    $_ENV['CLOUDFLARE_API_TOKEN'] ?? '',
    $_ENV['CLOUDFLARE_ACCOUNT_ID'] ?? '',
    $_ENV['CLOUDFLARE_ZONE_ID'] ?? ''
);

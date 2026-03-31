<?php

namespace App;

use PDO;
use PDOException;

/**
 * Hybrid Database class that automatically switches between 
 * local SQLite and Cloudflare D1 based on the environment.
 */
class Database
{
    private static $instance = null;

    /**
     * @return PDO|CloudflareD1Handler
     */
    public static function getConnection()
    {
        if (self::$instance === null) {
            $isProduction = ($_ENV['APP_ENV'] ?? 'local') === 'production';

            if ($isProduction && !empty($_ENV['CLOUDFLARE_D1_ID'])) {
                // Return our custom D1 HTTP bridge for Vercel
                self::$instance = new CloudflareD1Handler(
                    $_ENV['CLOUDFLARE_API_TOKEN'],
                    $_ENV['CLOUDFLARE_ACCOUNT_ID'],
                    $_ENV['CLOUDFLARE_D1_ID']
                );
            } else {
                // Traditional PDO for local development
                self::$instance = self::createLocalPdo();
            }
        }

        return self::$instance;
    }

    private static function createLocalPdo(): PDO
    {
        $dbPath = $_ENV['DB_PATH'] ?? __DIR__ . '/../database/database.sqlite';

        // Ensure directory exists
        $dir = dirname($dbPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        try {
            $pdo = new PDO("sqlite:$dbPath");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Fix for Docker on Windows
            $pdo->exec('PRAGMA journal_mode = MEMORY;');
            $pdo->exec('PRAGMA synchronous = OFF;');

            return $pdo;
        } catch (PDOException $e) {
            die("Could not connect to the local database: " . $e->getMessage());
        }
    }
}

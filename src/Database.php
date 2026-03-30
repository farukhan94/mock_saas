<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dbPath = $_ENV['DB_PATH'] ?? __DIR__ . '/../database/database.sqlite';

            // Ensure directory exists
            $dir = dirname($dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            try {
                self::$instance = new PDO("sqlite:$dbPath");
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Fix for Docker on Windows: Use memory for journals to avoid "readonly database" errors
                self::$instance->exec('PRAGMA journal_mode = MEMORY;');
                self::$instance->exec('PRAGMA synchronous = OFF;');

            } catch (PDOException $e) {
                die("Could not connect to the database: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}

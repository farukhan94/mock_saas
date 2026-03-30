<?php

require_once __DIR__ . '/../config/bootstrap.php';

$schema = file_get_contents(__DIR__ . '/../sql/schema.sql');

try {
    $db->exec($schema);

    // Resolve the absolute path to the database file
    $dbPath = realpath(__DIR__ . '/../database/database.sqlite');

    if ($dbPath) {
        // Ensure the database file and directory are writable by the Docker container (www-data)
        chmod(dirname($dbPath), 0777);
        chmod($dbPath, 0777);
        echo "Database initialized and permissions set successfully at: $dbPath\n";
    } else {
        echo "Warning: Database file was initialized but the path could not be resolved for chmod. You may need to set permissions manually.\n";
    }
} catch (Exception $e) {
    echo "Error initializing database: " . $e->getMessage() . "\n";
}

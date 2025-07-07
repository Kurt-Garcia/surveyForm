#!/usr/bin/env php
<?php

/**
 * This script creates the improvement area tables directly with SQL
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Get database configuration
$dbConfig = config('database.connections.mysql');

echo "Creating improvement area tables...\n";

try {
    // Connect to the database
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']}",
        $dbConfig['username'],
        $dbConfig['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/create_improvement_tables.sql');
    
    // Execute the SQL
    $pdo->exec($sql);
    
    echo "Tables created successfully!\n";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
    exit(1);
}

<?php

echo "Running database migration...\n\n";

// Read SQL file
$sql = file_get_contents(__DIR__ . '/database/migrations/create_anime_table.sql');

// Database configuration from .env
$env = parse_ini_file(__DIR__ . '/.env');

try {
    // Connect without database first to create it
    $pdo = new PDO(
        "mysql:host={$env['DB_HOST']}",
        $env['DB_USERNAME'],
        $env['DB_PASSWORD']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL server.\n";

    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && strpos($stmt, '--') !== 0;
        }
    );

    foreach ($statements as $statement) {
        // Skip comments
        if (empty($statement) || strpos(trim($statement), '--') === 0) {
            continue;
        }

        try {
            $pdo->exec($statement);

            // Show what was executed
            $firstLine = strtok($statement, "\n");
            if (stripos($firstLine, 'CREATE DATABASE') !== false) {
                echo "✓ Database created/verified\n";
            } elseif (stripos($firstLine, 'USE') !== false) {
                echo "✓ Database selected\n";
            } elseif (stripos($firstLine, 'CREATE TABLE') !== false) {
                echo "✓ Table 'anime' created/verified\n";
            } elseif (stripos($firstLine, 'INSERT INTO') !== false) {
                echo "✓ Sample data inserted\n";
            }
        } catch (PDOException $e) {
            // Ignore "already exists" errors
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "Warning: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\n✓ Migration completed successfully!\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

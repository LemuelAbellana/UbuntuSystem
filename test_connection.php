<?php
echo "Testing MySQL connection...\n";

$env = parse_ini_file(__DIR__ . '/.env');

try {
    $pdo = new PDO(
        "mysql:host={$env['DB_HOST']}",
        $env['DB_USERNAME'],
        $env['DB_PASSWORD'],
        [PDO::ATTR_TIMEOUT => 5]
    );
    echo "✓ Connected successfully!\n";

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$env['DB_DATABASE']}");
    echo "✓ Database created/verified: {$env['DB_DATABASE']}\n";

    // Select database
    $pdo->exec("USE {$env['DB_DATABASE']}");
    echo "✓ Database selected\n";

    // Create table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS anime (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            genre VARCHAR(255),
            episodes INT,
            status ENUM('Ongoing', 'Completed', 'Upcoming') DEFAULT 'Ongoing',
            rating DECIMAL(3,1),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "✓ Table 'anime' created/verified\n";

    // Insert sample data (ignore duplicates)
    $pdo->exec("
        INSERT IGNORE INTO anime (id, title, genre, episodes, status, rating) VALUES
        (1, 'Demon Slayer', 'Action, Supernatural', 26, 'Completed', 8.7),
        (2, 'My Hero Academia', 'Action, Superhero', 113, 'Ongoing', 8.4),
        (3, 'Steins;Gate', 'Sci-Fi, Thriller', 24, 'Completed', 9.1)
    ");
    echo "✓ Sample data inserted\n";

    echo "\n✓ Migration completed successfully!\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

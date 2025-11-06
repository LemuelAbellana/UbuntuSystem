<?php

echo "=== Database Setup Script ===\n\n";

// Read .env file
if (!file_exists('.env')) {
    echo "Error: .env file not found!\n";
    echo "Please copy .env.example to .env first.\n";
    exit(1);
}

$env = parse_ini_file('.env');

// Prompt for MySQL root password
echo "Enter MySQL root password (leave empty if none): ";
$rootPassword = trim(fgets(STDIN));

try {
    // Connect as root
    $dsn = "mysql:host={$env['DB_HOST']}";
    $pdo = new PDO($dsn, 'root', $rootPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✓ Connected to MySQL as root\n\n";

    // Create database
    echo "Creating database '{$env['DB_DATABASE']}'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$env['DB_DATABASE']}");
    echo "✓ Database created\n\n";

    // Create user
    echo "Creating user '{$env['DB_USERNAME']}'...\n";
    $pdo->exec("CREATE USER IF NOT EXISTS '{$env['DB_USERNAME']}'@'localhost' IDENTIFIED BY '{$env['DB_PASSWORD']}'");
    echo "✓ User created\n\n";

    // Grant privileges
    echo "Granting privileges...\n";
    $pdo->exec("GRANT ALL PRIVILEGES ON {$env['DB_DATABASE']}.* TO '{$env['DB_USERNAME']}'@'localhost'");
    $pdo->exec("FLUSH PRIVILEGES");
    echo "✓ Privileges granted\n\n";

    // Select database
    $pdo->exec("USE {$env['DB_DATABASE']}");

    // Create table
    echo "Creating anime table...\n";
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
    echo "✓ Table created\n\n";

    // Insert sample data
    echo "Inserting sample data...\n";
    $pdo->exec("
        INSERT INTO anime (title, genre, episodes, status, rating) VALUES
        ('Demon Slayer', 'Action, Supernatural', 26, 'Completed', 8.7),
        ('My Hero Academia', 'Action, Superhero', 113, 'Ongoing', 8.4),
        ('Steins;Gate', 'Sci-Fi, Thriller', 24, 'Completed', 9.1)
        ON DUPLICATE KEY UPDATE id=id
    ");
    echo "✓ Sample data inserted\n\n";

    // Test connection with new user
    echo "Testing connection with new user...\n";
    $testPdo = new PDO(
        "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']}",
        $env['DB_USERNAME'],
        $env['DB_PASSWORD']
    );
    $result = $testPdo->query("SELECT COUNT(*) as count FROM anime");
    $count = $result->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✓ Connection successful! Found {$count} anime records\n\n";

    echo "==================================\n";
    echo "✓ Database setup completed!\n";
    echo "==================================\n\n";
    echo "Database: {$env['DB_DATABASE']}\n";
    echo "User: {$env['DB_USERNAME']}\n";
    echo "Host: {$env['DB_HOST']}\n\n";
    echo "You can now start your server:\n";
    echo "  cd public\n";
    echo "  php -S localhost:8000\n\n";
    echo "Then visit: http://localhost:8000/anime\n";

} catch (PDOException $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n\n";

    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "Troubleshooting:\n";
        echo "1. Make sure MySQL is running\n";
        echo "2. Check if root password is correct\n";
        echo "3. Try: mysql -u root -p (and enter your password)\n";
    }

    exit(1);
}

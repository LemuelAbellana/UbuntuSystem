<?php
if (file_exists('.env')) {
    $config = parse_ini_file('.env');
    echo "✓ .env file found and loaded successfully\n";
    echo "  Database: {$config['DB_DATABASE']}\n";
    echo "  Host: {$config['DB_HOST']}\n";
    echo "  Username: {$config['DB_USERNAME']}\n";
} else {
    echo "✗ ERROR: .env file not found\n";
    exit(1);
}

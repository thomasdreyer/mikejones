<?php
// Database setup script
$config = require __DIR__ . '/includes/config.php';

try {
    // Connect to MySQL without specifying database
    $dsn = "mysql:host={$config['db_host']};charset=utf8";
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['db_name']}`");
    $pdo->exec("USE `{$config['db_name']}`");

    // Create contacts table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Create users table for future authentication enhancement
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    // Create data directory if it doesn't exist
    $dataDir = __DIR__ . '/data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }

    // Create sample data
    $stmt = $pdo->prepare("INSERT IGNORE INTO contacts (name, email, phone) VALUES (?, ?, ?)");
    $sampleContacts = [
        ['John Doe', 'john@example.com', '+1-555-0123'],
        ['Jane Smith', 'jane@example.com', '+1-555-0456'],
        ['Bob Johnson', 'bob@example.com', null]
    ];

    foreach ($sampleContacts as $contact) {
        $stmt->execute($contact);
    }

    echo "✅ Database setup completed successfully!\n";
    echo "📊 Created database: {$config['db_name']}\n";
    echo "📋 Created tables: contacts, users\n";
    echo "📁 Created data directory: /data\n";
    echo "👥 Added sample contacts\n\n";
    echo "🔗 Access your application:\n";
    echo "   Frontend: http://localhost/frontend/index.html\n";
    echo "   Dashboard: http://localhost/dashboard.php\n";
    echo "   Login: http://localhost/login.php\n\n";
    echo "🔐 Demo Credentials:\n";
    echo "   Username: admin\n";
    echo "   Password: admin123\n";

} catch (Exception $e) {
    echo "❌ Setup failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>

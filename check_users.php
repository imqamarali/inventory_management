<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'inventory_system';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if system_users table exists
    $result = $pdo->query("SHOW TABLES LIKE 'system_users'");
    if ($result->rowCount() == 0) {
        echo "system_users table does not exist. Creating it...\n";
        // Create a basic system_users table
        $pdo->exec("CREATE TABLE IF NOT EXISTS system_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            is_active TINYINT DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Create a default admin user
        $hashedPassword = password_hash('admin', PASSWORD_BCRYPT, ['cost' => 12]);
        $pdo->exec("INSERT INTO system_users (username, email, password, first_name, last_name, is_active)
                    VALUES ('admin', 'admin@example.com', '$hashedPassword', 'Admin', 'User', 1)");
        echo "Created system_users table and admin user\n";
    } else {
        echo "system_users table exists\n";
        $users = $pdo->query("SELECT id, username, email FROM system_users")->fetchAll(PDO::FETCH_ASSOC);
        echo "Current users:\n";
        foreach ($users as $u) {
            echo "  - ID: {$u['id']}, Username: {$u['username']}, Email: {$u['email']}\n";
        }

        // If no users exist, create default admin
        if (count($users) == 0) {
            $hashedPassword = password_hash('admin', PASSWORD_BCRYPT, ['cost' => 12]);
            $pdo->exec("INSERT INTO system_users (username, email, password, first_name, last_name, is_active)
                        VALUES ('admin', 'admin@example.com', '$hashedPassword', 'Admin', 'User', 1)");
            echo "Created default admin user (username: admin, password: admin)\n";
        }
    }

    // Check if default accounts are set in settings
    $result = $pdo->query("SELECT setting_key, setting_value FROM inventory_settings WHERE setting_key IN ('default_sales_account', 'default_purchase_account')")->fetchAll(PDO::FETCH_ASSOC);
    echo "\nDefault Account Settings:\n";
    foreach ($result as $row) {
        echo "  - {$row['setting_key']}: {$row['setting_value']}\n";
    }

    echo "\n✓ User check completed!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

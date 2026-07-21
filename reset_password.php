<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'inventory_system';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Reset superadmin password to 'superadmin'
    $hashedPassword = password_hash('superadmin', PASSWORD_BCRYPT, ['cost' => 12]);
    $pdo->exec("UPDATE system_users SET password = '$hashedPassword' WHERE username = 'superadmin'");

    echo "✓ Superadmin password reset to: superadmin\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

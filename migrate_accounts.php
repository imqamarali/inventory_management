<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'inventory_system';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if account_id column exists in inventory_sales_invoices
    $result = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='inventory_sales_invoices' AND COLUMN_NAME='account_id'");
    if ($result->rowCount() == 0) {
        // Add account_id column to inventory_sales_invoices
        $pdo->exec("ALTER TABLE inventory_sales_invoices ADD COLUMN account_id INT NULL AFTER warehouse_id");
        echo "Added account_id column to inventory_sales_invoices\n";

        // Add index and foreign key
        $pdo->exec("ALTER TABLE inventory_sales_invoices ADD INDEX(account_id)");
        $pdo->exec("ALTER TABLE inventory_sales_invoices ADD CONSTRAINT fk_sales_inv_account FOREIGN KEY(account_id) REFERENCES inventory_accounts(id) ON UPDATE CASCADE");
        echo "Added index and foreign key for account_id in inventory_sales_invoices\n";
    } else {
        echo "account_id column already exists in inventory_sales_invoices\n";
    }

    // Check if account_id column exists in inventory_purchase_invoices
    $result = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='inventory_purchase_invoices' AND COLUMN_NAME='account_id'");
    if ($result->rowCount() == 0) {
        // Add account_id column to inventory_purchase_invoices
        $pdo->exec("ALTER TABLE inventory_purchase_invoices ADD COLUMN account_id INT NULL AFTER supplier_id");
        echo "Added account_id column to inventory_purchase_invoices\n";

        // Add index and foreign key
        $pdo->exec("ALTER TABLE inventory_purchase_invoices ADD INDEX(account_id)");
        $pdo->exec("ALTER TABLE inventory_purchase_invoices ADD CONSTRAINT fk_purchase_inv_account FOREIGN KEY(account_id) REFERENCES inventory_accounts(id) ON UPDATE CASCADE");
        echo "Added index and foreign key for account_id in inventory_purchase_invoices\n";
    } else {
        echo "account_id column already exists in inventory_purchase_invoices\n";
    }

    echo "\n✓ Database migration completed successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

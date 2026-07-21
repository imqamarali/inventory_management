<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'inventory_system';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== SALES INVOICES VERIFICATION ===\n";
    echo "Checking if account_id is properly set in sales invoices:\n\n";

    $result = $pdo->query("
        SELECT si.id, si.invoice_no, si.account_id, ia.account_code, ia.account_name
        FROM inventory_sales_invoices si
        LEFT JOIN inventory_accounts ia ON si.account_id = ia.id
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        echo "Sales Invoices:\n";
        foreach ($result as $row) {
            echo "  - ID: {$row['id']}, Invoice: {$row['invoice_no']}, Account ID: " . ($row['account_id'] ?: 'NULL') .
                 ", Account: {$row['account_code']} - {$row['account_name']}\n";
        }
    } else {
        echo "No sales invoices found.\n";
    }

    echo "\n=== PURCHASE INVOICES VERIFICATION ===\n";
    echo "Checking if account_id is properly set in purchase invoices:\n\n";

    $result = $pdo->query("
        SELECT pi.id, pi.invoice_no, pi.account_id, ia.account_code, ia.account_name
        FROM inventory_purchase_invoices pi
        LEFT JOIN inventory_accounts ia ON pi.account_id = ia.id
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        echo "Purchase Invoices:\n";
        foreach ($result as $row) {
            echo "  - ID: {$row['id']}, Invoice: {$row['invoice_no']}, Account ID: " . ($row['account_id'] ?: 'NULL') .
                 ", Account: {$row['account_code']} - {$row['account_name']}\n";
        }
    } else {
        echo "No purchase invoices found.\n";
    }

    echo "\n=== SETTINGS VERIFICATION ===\n";
    echo "Default Accounts Configuration:\n\n";

    $result = $pdo->query("
        SELECT setting_key, setting_value
        FROM inventory_settings
        WHERE setting_key IN ('default_sales_account', 'default_purchase_account')
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        $accountId = $row['setting_value'];
        $account = $pdo->query("SELECT account_code, account_name FROM inventory_accounts WHERE id = $accountId")->fetch(PDO::FETCH_ASSOC);
        echo "  - {$row['setting_key']}: ID {$accountId} ({$account['account_code']} - {$account['account_name']})\n";
    }

    echo "\n✓ Verification completed!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

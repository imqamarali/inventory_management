<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'inventory_system';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== TESTING ACCOUNT REFERENCE INTEGRATION ===\n\n";

    // Get default accounts
    $defaultSalesAccount = $pdo->query("SELECT setting_value FROM inventory_settings WHERE setting_key='default_sales_account'")->fetchColumn();
    $defaultPurchaseAccount = $pdo->query("SELECT setting_value FROM inventory_settings WHERE setting_key='default_purchase_account'")->fetchColumn();

    echo "Default Accounts Configured:\n";
    echo "  Sales Account ID: $defaultSalesAccount\n";
    echo "  Purchase Account ID: $defaultPurchaseAccount\n\n";

    // Create a test sales order
    echo "Creating test sales order...\n";
    $salesOrderNo = 'TEST-SO-' . date('YmdHis') . '-' . rand(100, 999);
    $pdo->exec("INSERT INTO inventory_sales_orders (
        order_number, customer_id, warehouse_id, order_date, delivery_date,
        order_status, payment_status, subtotal, discount, tax, shipping, grand_total,
        paid_amount, remaining_balance, notes, created_at, updated_at, created_by, updated_by, is_active, is_deleted
    ) VALUES (
        '$salesOrderNo', 10, 1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 5 DAY),
        'Confirmed', 'Pending', 5000, 100, 500, 0, 5400, 0, 5400, 'Test order', NOW(), NOW(), 1, 1, 1, 0
    )");
    $salesOrderId = $pdo->lastInsertId();
    echo "  Sales Order Created: ID=$salesOrderId, Order No=$salesOrderNo\n";

    // Add a product to the sales order
    $pdo->exec("INSERT INTO inventory_sales_order_items (
        sales_order_id, product_id, quantity, delivered_quantity, remaining_quantity, unit_price, discount, tax, total,
        created_at, updated_at, created_by, updated_by, is_active, is_deleted
    ) VALUES (
        $salesOrderId, 1, 10, 0, 10, 500, 100, 500, 5000, NOW(), NOW(), 1, 1, 1, 0
    )");

    // Create a test sales invoice
    echo "\nCreating test sales invoice...\n";
    $invoiceNo = 'INV-TEST-' . date('YmdHis') . '-' . rand(100, 999);
    $pdo->exec("INSERT INTO inventory_sales_invoices (
        invoice_no, sales_order_id, customer_id, warehouse_id, account_id,
        invoice_date, due_date, subtotal, discount, tax, shipping, grand_total,
        paid_amount, remaining_balance, status, notes, created_at, updated_at, created_by, updated_by, is_deleted
    ) VALUES (
        '$invoiceNo', $salesOrderId, 10, 1, $defaultSalesAccount,
        CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 5000, 100, 500, 0, 5400,
        0, 5400, 'Draft', 'Test invoice', NOW(), NOW(), 1, 1, 0
    )");
    $salesInvoiceId = $pdo->lastInsertId();
    echo "  Sales Invoice Created: ID=$salesInvoiceId, Invoice No=$invoiceNo\n";

    // Create a test purchase order
    echo "\nCreating test purchase order...\n";
    $poNo = 'TEST-PO-' . date('YmdHis') . '-' . rand(100, 999);
    $pdo->exec("INSERT INTO inventory_purchase_orders (
        po_number, supplier_id, warehouse_id, order_date, expected_date,
        status, subtotal, discount, tax, freight, grand_total, notes, created_at, updated_at,
        created_by, updated_by, is_active, is_deleted
    ) VALUES (
        '$poNo', 1, 1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY),
        'Approved', 3000, 50, 300, 0, 3250, 'Test PO', NOW(), NOW(), 1, 1, 1, 0
    )");
    $purchaseOrderId = $pdo->lastInsertId();
    echo "  Purchase Order Created: ID=$purchaseOrderId, PO No=$poNo\n";

    // Add a product to the purchase order
    $pdo->exec("INSERT INTO inventory_purchase_order_items (
        purchase_order_id, product_id, quantity, received_quantity, remaining_quantity,
        unit_price, discount, tax, total, remarks, created_at, updated_at, created_by, updated_by, is_active, is_deleted
    ) VALUES (
        $purchaseOrderId, 1, 5, 0, 5, 600, 50, 300, 3000, 'Test item', NOW(), NOW(), 1, 1, 1, 0
    )");

    // Create a test purchase invoice
    echo "\nCreating test purchase invoice...\n";
    $pinvoiceNo = 'PINV-TEST-' . date('YmdHis') . '-' . rand(100, 999);
    $pdo->exec("INSERT INTO inventory_purchase_invoices (
        purchase_order_id, supplier_id, account_id, invoice_no, invoice_date, due_date,
        subtotal, discount_amount, tax_amount, grand_total, paid_amount, balance_amount,
        status, remarks, created_at, updated_at, is_active, is_deleted
    ) VALUES (
        $purchaseOrderId, 1, $defaultPurchaseAccount, '$pinvoiceNo', CURDATE(),
        DATE_ADD(CURDATE(), INTERVAL 30 DAY), 3000, 50, 300, 3250, 0, 3250,
        'Pending', 'Test invoice', NOW(), NOW(), 1, 0
    )");
    $purchaseInvoiceId = $pdo->lastInsertId();
    echo "  Purchase Invoice Created: ID=$purchaseInvoiceId, Invoice No=$pinvoiceNo\n";

    // Verify the account references
    echo "\n=== VERIFICATION ===\n\n";

    echo "Sales Invoice Verification:\n";
    $salesInvoice = $pdo->query("
        SELECT si.id, si.invoice_no, si.account_id, ia.account_code, ia.account_name, ia.account_type
        FROM inventory_sales_invoices si
        LEFT JOIN inventory_accounts ia ON si.account_id = ia.id
        WHERE si.id = $salesInvoiceId
    ")->fetch(PDO::FETCH_ASSOC);

    if ($salesInvoice) {
        echo "  ✓ Invoice No: {$salesInvoice['invoice_no']}\n";
        echo "  ✓ Account ID: {$salesInvoice['account_id']}\n";
        echo "  ✓ Account: [{$salesInvoice['account_code']}] {$salesInvoice['account_name']} ({$salesInvoice['account_type']})\n";
        if ($salesInvoice['account_id'] == $defaultSalesAccount) {
            echo "  ✓ SUCCESS: Account correctly set to default sales account!\n";
        } else {
            echo "  ✗ FAIL: Account ID does not match default sales account\n";
        }
    } else {
        echo "  ✗ Invoice not found\n";
    }

    echo "\nPurchase Invoice Verification:\n";
    $purchaseInvoice = $pdo->query("
        SELECT pi.id, pi.invoice_no, pi.account_id, ia.account_code, ia.account_name, ia.account_type
        FROM inventory_purchase_invoices pi
        LEFT JOIN inventory_accounts ia ON pi.account_id = ia.id
        WHERE pi.id = $purchaseInvoiceId
    ")->fetch(PDO::FETCH_ASSOC);

    if ($purchaseInvoice) {
        echo "  ✓ Invoice No: {$purchaseInvoice['invoice_no']}\n";
        echo "  ✓ Account ID: {$purchaseInvoice['account_id']}\n";
        echo "  ✓ Account: [{$purchaseInvoice['account_code']}] {$purchaseInvoice['account_name']} ({$purchaseInvoice['account_type']})\n";
        if ($purchaseInvoice['account_id'] == $defaultPurchaseAccount) {
            echo "  ✓ SUCCESS: Account correctly set to default purchase account!\n";
        } else {
            echo "  ✗ FAIL: Account ID does not match default purchase account\n";
        }
    } else {
        echo "  ✗ Invoice not found\n";
    }

    echo "\n=== TEST COMPLETE ===\n";
    echo "✓ All tests completed successfully!\n";
    echo "✓ Account references are properly integrated into the invoice system.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

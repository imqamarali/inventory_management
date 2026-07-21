<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'inventory_system';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== CHECKING INVOICE: INV-20260721102602-637 ===\n\n";

    // Check if invoice exists
    $invoice = $pdo->query("
        SELECT * FROM inventory_sales_invoices
        WHERE invoice_no='INV-20260721102602-637'
    ")->fetch(PDO::FETCH_ASSOC);

    if ($invoice) {
        echo "✓ Invoice Found!\n\n";
        echo "Invoice Details:\n";
        echo "  ID: {$invoice['id']}\n";
        echo "  Invoice No: {$invoice['invoice_no']}\n";
        echo "  Sales Order ID: {$invoice['sales_order_id']}\n";
        echo "  Customer ID: {$invoice['customer_id']}\n";
        echo "  Account ID: {$invoice['account_id']}\n";
        echo "  Invoice Date: {$invoice['invoice_date']}\n";
        echo "  Due Date: {$invoice['due_date']}\n";
        echo "  Subtotal: {$invoice['subtotal']}\n";
        echo "  Discount: {$invoice['discount']}\n";
        echo "  Tax: {$invoice['tax']}\n";
        echo "  Shipping: {$invoice['shipping']}\n";
        echo "  Grand Total: {$invoice['grand_total']}\n";
        echo "  Paid Amount: {$invoice['paid_amount']}\n";
        echo "  Remaining Balance: {$invoice['remaining_balance']}\n";
        echo "  Status: {$invoice['status']}\n";
        echo "  is_deleted: {$invoice['is_deleted']}\n";
        echo "  Created At: {$invoice['created_at']}\n";
        echo "  Created By: {$invoice['created_by']}\n";

        echo "\n" . str_repeat("=", 80) . "\n\n";

        // Check if status affects dashboard query
        echo "Dashboard Query Filters Check:\n\n";

        // Check is_deleted filter
        if ($invoice['is_deleted'] == 0) {
            echo "✓ is_deleted = 0 (PASS) - Will be included in dashboard\n";
        } else {
            echo "✗ is_deleted = 1 (FAIL) - Will NOT be included in dashboard\n";
        }

        // Check if account_id is set
        if ($invoice['account_id']) {
            $account = $pdo->query("SELECT * FROM inventory_accounts WHERE id=" . $invoice['account_id'])->fetch(PDO::FETCH_ASSOC);
            echo "✓ account_id = {$invoice['account_id']} (PASS) - Linked to account: [{$account['account_code']}] {$account['account_name']}\n";
        } else {
            echo "⚠ account_id = NULL (WARNING) - Not linked to any account\n";
        }

        // Check status
        echo "✓ Status: {$invoice['status']}\n";

        // Get customer info
        $customer = $pdo->query("SELECT * FROM inventory_customers WHERE id={$invoice['customer_id']}")->fetch(PDO::FETCH_ASSOC);
        $customerName = !empty($customer['company_name']) ? $customer['company_name'] : ($customer['first_name'] . ' ' . $customer['last_name']);
        echo "✓ Customer: {$customer['customer_code']} - $customerName\n";

        echo "\n" . str_repeat("=", 80) . "\n\n";

        // Check what the dashboard query would return
        echo "Dashboard Query Results:\n\n";

        $dashboardResult = $pdo->query("
            SELECT
                si.id,
                si.invoice_no,
                si.invoice_date,
                si.grand_total,
                si.paid_amount,
                si.remaining_balance,
                si.status,
                c.customer_code,
                COALESCE(CONCAT(c.first_name, ' ', c.last_name), c.company_name) customer_name,
                a.account_code,
                a.account_name
            FROM inventory_sales_invoices si
            LEFT JOIN inventory_customers c ON si.customer_id = c.id
            LEFT JOIN inventory_accounts a ON si.account_id = a.id
            WHERE si.is_deleted=0 AND si.invoice_no='INV-20260721102602-637'
        ")->fetch(PDO::FETCH_ASSOC);

        if ($dashboardResult) {
            echo "✓ Invoice WILL appear in dashboard query result:\n";
            foreach ($dashboardResult as $key => $value) {
                echo "  $key: $value\n";
            }
        } else {
            echo "✗ Invoice will NOT appear in dashboard query (filtered out)\n";
        }

        echo "\n" . str_repeat("=", 80) . "\n\n";

        // Check other invoices for comparison
        echo "Other Sales Invoices for Comparison:\n\n";
        $allInvoices = $pdo->query("
            SELECT invoice_no, status, grand_total, paid_amount, remaining_balance, account_id, is_deleted
            FROM inventory_sales_invoices
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($allInvoices as $inv) {
            $status = ($inv['is_deleted'] == 0) ? '✓' : '✗';
            echo "$status {$inv['invoice_no']}: Status={$inv['status']}, Amount={$inv['grand_total']}, Paid={$inv['paid_amount']}, Account={$inv['account_id']}, Deleted={$inv['is_deleted']}\n";
        }

    } else {
        echo "✗ Invoice NOT Found in database!\n";
        echo "Invoice No: INV-20260721102602-637\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

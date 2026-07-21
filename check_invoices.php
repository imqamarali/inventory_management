<?php
require_once 'config/db.php';
$config = require 'config/db.php';

try {
    $db = new \yii\db\Connection($config);
    $db->open();
    
    echo "=== SALES ORDERS & INVOICES CHECK ===\n\n";
    
    // Check duplicate invoices
    echo "1. CHECKING FOR DUPLICATE INVOICES:\n";
    $duplicates = $db->createCommand("
        SELECT 
            sales_order_id, 
            COUNT(*) as invoice_count,
            GROUP_CONCAT(id) as invoice_ids,
            GROUP_CONCAT(invoice_no) as invoice_nos
        FROM inventory_sale_invoices
        WHERE is_deleted = 0
        GROUP BY sales_order_id
        HAVING COUNT(*) > 1
    ")->queryAll();
    
    if (empty($duplicates)) {
        echo "✓ No duplicate invoices found - All good!\n\n";
    } else {
        echo "✗ DUPLICATE INVOICES FOUND:\n";
        foreach ($duplicates as $dup) {
            echo "  Order ID: {$dup['sales_order_id']}, Invoice Count: {$dup['invoice_count']}\n";
            echo "  Invoice IDs: {$dup['invoice_ids']}\n";
            echo "  Invoice Numbers: {$dup['invoice_nos']}\n\n";
        }
    }
    
    // Show recent orders and invoices
    echo "2. RECENT SALES ORDERS & INVOICES:\n";
    $orders = $db->createCommand("
        SELECT 
            so.id,
            so.order_number,
            so.grand_total,
            so.paid_amount,
            so.order_status,
            so.payment_status,
            COUNT(DISTINCT si.id) as invoice_count,
            MAX(si.invoice_no) as invoice_no
        FROM inventory_sales_orders so
        LEFT JOIN inventory_sale_invoices si ON si.sales_order_id = so.id AND si.is_deleted = 0
        WHERE so.is_deleted = 0
        GROUP BY so.id
        ORDER BY so.id DESC
        LIMIT 10
    ")->queryAll();
    
    foreach ($orders as $order) {
        echo "Order: {$order['order_number']} (ID: {$order['id']}) - Invoice Count: {$order['invoice_count']}\n";
        echo "  Invoice: {$order['invoice_no']}\n";
        echo "  Status: {$order['order_status']} / {$order['payment_status']}\n";
        echo "  Total: {$order['grand_total']}, Paid: {$order['paid_amount']}\n\n";
    }
    
    // Check invoice items
    echo "3. INVOICE ITEMS COUNT:\n";
    $itemCounts = $db->createCommand("
        SELECT 
            si.id,
            si.invoice_no,
            COUNT(sii.id) as item_count
        FROM inventory_sale_invoices si
        LEFT JOIN inventory_sale_invoice_items sii ON sii.sales_invoice_id = si.id AND sii.is_deleted = 0
        WHERE si.is_deleted = 0
        ORDER BY si.id DESC
        LIMIT 5
    ")->queryAll();
    
    foreach ($itemCounts as $inv) {
        echo "Invoice {$inv['invoice_no']} (ID: {$inv['id']}): {$inv['item_count']} items\n";
    }
    
    $db->close();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<?php
/**
 * Backfill script to create payment records for existing invoices with payments
 * This ensures all invoices have payment history records for audit trail
 */

$host = 'localhost';
$db = 'inventory_system';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "\n╔════════════════════════════════════════════════════════════════╗\n";
    echo "║           BACKFILL: CREATE MISSING PAYMENT RECORDS             ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n\n";

    // Find invoices with paid_amount > 0 but no payment records
    $stmt = $pdo->query("
        SELECT
            si.id,
            si.invoice_no,
            si.paid_amount,
            si.created_at,
            (SELECT COUNT(*) FROM inventory_sale_invoice_payments WHERE sale_invoice_id = si.id) as payment_count
        FROM inventory_sales_invoices si
        WHERE si.paid_amount > 0
        AND si.is_deleted = 0
        AND NOT EXISTS (
            SELECT 1 FROM inventory_sale_invoice_payments
            WHERE sale_invoice_id = si.id
        )
        ORDER BY si.id
    ");
    $invoicesToBackfill = $stmt->fetchAll();

    echo "Found " . count($invoicesToBackfill) . " invoices with payments but no payment records:\n\n";

    $recordsCreated = 0;

    foreach ($invoicesToBackfill as $invoice) {
        // Create payment record for existing paid amount
        $stmt = $pdo->prepare("
            INSERT INTO inventory_sale_invoice_payments
            (sale_invoice_id, paid_amount, payment_date, remarks, created_at, created_by)
            VALUES (?, ?, ?, ?, NOW(), ?)
        ");

        $stmt->execute([
            $invoice['id'],
            $invoice['paid_amount'],
            date('Y-m-d'),
            'Backfill: Payment proof for existing payment',
            1  // admin user
        ]);

        $recordsCreated++;

        echo "✓ Invoice " . $invoice['invoice_no'] . ": Created payment record for PKR " . number_format($invoice['paid_amount'], 2) . "\n";
    }

    echo "\n╔════════════════════════════════════════════════════════════════╗\n";
    echo "║                    BACKFILL COMPLETE ✅                         ║\n";
    echo "║                                                                ║\n";
    echo "║  Records Created: " . str_pad($recordsCreated, 55, " ") . "║\n";
    echo "║  All invoices now have payment history!                        ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n\n";

    // Verify the backfill
    echo "Verification:\n";
    echo "─────────────\n\n";

    $stmt = $pdo->query("
        SELECT
            COUNT(*) as total_invoices,
            SUM(CASE WHEN paid_amount > 0 THEN 1 ELSE 0 END) as invoices_with_payment,
            SUM(CASE WHEN EXISTS(
                SELECT 1 FROM inventory_sale_invoice_payments
                WHERE sale_invoice_id = inventory_sales_invoices.id
            ) THEN 1 ELSE 0 END) as invoices_with_records
        FROM inventory_sales_invoices
        WHERE is_deleted = 0
    ");
    $stats = $stmt->fetch();

    echo "Total Invoices: " . $stats['total_invoices'] . "\n";
    echo "Invoices with Payments: " . ($stats['invoices_with_payment'] ?? 0) . "\n";
    echo "Invoices with Payment Records: " . ($stats['invoices_with_records'] ?? 0) . "\n\n";

    if ($stats['invoices_with_payment'] == $stats['invoices_with_records']) {
        echo "✅ All paid invoices now have payment records!\n\n";
    } else {
        $remaining = ($stats['invoices_with_payment'] ?? 0) - ($stats['invoices_with_records'] ?? 0);
        echo "⚠ Still " . $remaining . " invoices with payments but no records\n\n";
    }

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

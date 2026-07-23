<?php
/**
 * Database Seeding Script - Version 2
 * Adds comprehensive test data for system performance testing
 */

$dsn = 'mysql:host=localhost;dbname=inventory_system';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Starting data seeding...\n\n";

    // 1. Disable foreign key checks for seeding
    echo "Clearing previous test data...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("TRUNCATE TABLE inventory_warehouses");
    $pdo->exec("TRUNCATE TABLE inventory_suppliers");
    $pdo->exec("TRUNCATE TABLE inventory_customers");
    $pdo->exec("TRUNCATE TABLE inventory_products");
    $pdo->exec("TRUNCATE TABLE inventory_stock");
    $pdo->exec("TRUNCATE TABLE inventory_purchase_invoices");
    $pdo->exec("TRUNCATE TABLE inventory_purchase_invoice_items");
    $pdo->exec("TRUNCATE TABLE inventory_sales_invoices");
    $pdo->exec("TRUNCATE TABLE inventory_sales_invoice_items");
    $pdo->exec("TRUNCATE TABLE inventory_sales_orders");
    $pdo->exec("TRUNCATE TABLE system_contracts");
    $pdo->exec("TRUNCATE TABLE system_invoices");
    $pdo->exec("TRUNCATE TABLE inventory_categories");
    $pdo->exec("TRUNCATE TABLE inventory_brands");

    // 2. Add Warehouse (1 only)
    echo "Adding warehouse...\n";
    $pdo->exec("INSERT INTO inventory_warehouses (warehouse_name, warehouse_code, address, city, province, country, contact_person, phone, email, is_active, is_deleted, created_at, created_by)
               VALUES ('Main Warehouse', 'WH-001', 'Main Street 123', 'Islamabad', 'Federal', 'Pakistan', 'Ahmed Khan', '03001234567', 'warehouse@example.com', 1, 0, NOW(), 1)");
    $warehouseId = $pdo->lastInsertId();

    // 3. Add Categories
    echo "Adding categories...\n";
    $categories = [
        'Electronics', 'Furniture', 'Supplies', 'Equipment', 'Appliances',
        'Hardware', 'Software', 'Accessories', 'Tools', 'Materials'
    ];

    $categoryIds = [];
    foreach ($categories as $cat) {
        $pdo->prepare("INSERT INTO inventory_categories (category_name, created_at, created_by, is_active, is_deleted)
                      VALUES (:name, NOW(), 1, 1, 0)")
            ->execute([':name' => $cat]);
        $categoryIds[] = $pdo->lastInsertId();
    }

    // 4. Add Brands
    echo "Adding brands...\n";
    $brands = ['Samsung', 'LG', 'Sony', 'Apple', 'Dell', 'HP', 'Asus', 'Lenovo', 'IKEA', 'Premier', 'Standard', 'Elite', 'Pro'];
    $brandIds = [];
    foreach ($brands as $brand) {
        $pdo->prepare("INSERT INTO inventory_brands (brand_name, created_at, created_by, is_active, is_deleted)
                      VALUES (:name, NOW(), 1, 1, 0)")
            ->execute([':name' => $brand]);
        $brandIds[] = $pdo->lastInsertId();
    }

    // 5. Add Suppliers (50+)
    echo "Adding 50 suppliers...\n";
    $supplierTemplates = [
        'Global Supplies Co', 'Tech Imports Ltd', 'Premier Distributors', 'Quality Products Inc', 'Metro Electronics',
        'Elite Supplies', 'Standard Goods LLC', 'Interstate Trading', 'Pinnacle Distributors', 'Crown Suppliers',
        'Expert Suppliers', 'Trusted Goods Co', 'Summit Trading', 'Valley Distributors', 'Rainbow Supplies',
        'Phoenix Imports', 'Classic Goods', 'Venture Supplies', 'Unity Trading', 'Zenith Distributors',
        'Alpha Electronics', 'Beta Supplies', 'Gamma Imports', 'Delta Trading', 'Epsilon Goods',
        'Zeta Distributors', 'Eta Supplies', 'Theta Trading', 'Iota Electronics', 'Kappa Imports',
        'Lambda Goods', 'Mu Supplies', 'Nu Trading', 'Xi Distributors', 'Omicron Imports',
        'Pi Supplies', 'Rho Trading', 'Sigma Goods', 'Tau Electronics', 'Upsilon Imports',
        'Phi Supplies', 'Chi Trading', 'Psi Distributors', 'Omega Goods', 'Asia Traders',
        'Pak Supplies', 'Global Trade', 'Continental', 'Universal Supplies', 'Prime Goods'
    ];

    $supplierIds = [];
    $cities = ['Islamabad', 'Karachi', 'Lahore', 'Peshawar', 'Quetta', 'Multan', 'Faisalabad', 'Rawalpindi'];

    foreach ($supplierTemplates as $i => $name) {
        $stmt = $pdo->prepare("INSERT INTO inventory_suppliers (supplier_code, company_name, contact_person, email, phone, mobile, address, city, province, country, is_active, is_deleted, created_at, created_by)
                              VALUES (:code, :name, :contact, :email, :phone, :mobile, :address, :city, 'Punjab', 'Pakistan', 1, 0, NOW(), 1)");
        $stmt->execute([
            ':code' => 'SUP-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
            ':name' => $name,
            ':contact' => 'Contact ' . ($i + 1),
            ':email' => 'supplier' . ($i + 1) . '@example.com',
            ':phone' => '042-' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
            ':mobile' => '030' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
            ':address' => 'Address ' . ($i + 1),
            ':city' => $cities[$i % count($cities)]
        ]);
        $supplierIds[] = $pdo->lastInsertId();
    }

    // 6. Add Customers (100+)
    echo "Adding 100 customers...\n";
    $customerIds = [];
    for ($i = 1; $i <= 100; $i++) {
        $stmt = $pdo->prepare("INSERT INTO inventory_customers (customer_code, customer_type, company_name, first_name, last_name, email, phone, mobile, address, city, province, country, is_active, is_deleted, created_at, created_by)
                              VALUES (:code, :type, :company, :fname, :lname, :email, :phone, :mobile, :address, :city, 'Punjab', 'Pakistan', 1, 0, NOW(), 1)");
        $stmt->execute([
            ':code' => 'CUST-' . str_pad($i, 5, '0', STR_PAD_LEFT),
            ':type' => $i % 2 == 0 ? 'corporate' : 'individual',
            ':company' => 'Company ' . $i,
            ':fname' => 'Customer',
            ':lname' => 'Name ' . $i,
            ':email' => 'customer' . $i . '@example.com',
            ':phone' => '042-' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
            ':mobile' => '030' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
            ':address' => 'Address ' . $i,
            ':city' => $cities[$i % count($cities)]
        ]);
        $customerIds[] = $pdo->lastInsertId();
    }

    // 7. Add Products (200+)
    echo "Adding 200+ products...\n";
    $productTemplates = [
        'LED Monitor 24"', 'LED Monitor 27"', 'LED Monitor 32"',
        'Keyboard Wireless', 'Keyboard Mechanical', 'Keyboard USB',
        'Mouse Optical', 'Mouse Wireless', 'Mouse Gaming',
        'USB Cable 1m', 'USB Cable 2m', 'USB Cable 5m',
        'HDMI Cable', 'Network Cable', 'Power Cable',
        'Office Chair', 'Standing Desk', 'Computer Table',
        'Desk Lamp', 'Table Fan', 'Water Dispenser',
        'Printer Laser', 'Printer Inkjet', 'Scanner'
    ];

    $productIds = [];
    for ($p = 0; $p < 9; $p++) {
        foreach ($productTemplates as $i => $template) {
            $productName = $template . ' - Variant ' . ($p + 1);
            $categoryId = $categoryIds[$i % count($categoryIds)];
            $brandId = $brandIds[$i % count($brandIds)];

            $stmt = $pdo->prepare("INSERT INTO inventory_products (category_id, brand_id, product_name, sku, description, purchase_price, selling_price, minimum_stock, reorder_level, is_active, is_deleted, created_at, created_by)
                                  VALUES (:cat_id, :brand_id, :name, :sku, :desc, :pp, :sp, :min, :reorder, 1, 0, NOW(), 1)");
            $stmt->execute([
                ':cat_id' => $categoryId,
                ':brand_id' => $brandId,
                ':name' => $productName,
                ':sku' => 'SKU-' . str_pad($p * 1000 + $i + 1, 6, '0', STR_PAD_LEFT),
                ':desc' => 'Product: ' . $productName,
                ':pp' => rand(1000, 50000),
                ':sp' => rand(2000, 60000),
                ':min' => rand(5, 20),
                ':reorder' => rand(5, 15)
            ]);
            $productIds[] = $pdo->lastInsertId();
        }
    }

    // 8. Add Stock
    echo "Adding stock records for " . count($productIds) . " products...\n";
    foreach ($productIds as $productId) {
        $stmt = $pdo->prepare("INSERT INTO inventory_stock (warehouse_id, product_id, quantity, reserved_quantity, available_quantity, is_active, is_deleted, created_at, created_by)
                              VALUES (:warehouse_id, :product_id, :qty, 0, :qty, 1, 0, NOW(), 1)");
        $stmt->execute([
            ':warehouse_id' => $warehouseId,
            ':product_id' => $productId,
            ':qty' => rand(50, 500)
        ]);
    }

    // 9. Add System Contracts
    echo "Adding 5 contracts with start date 2025-01-01...\n";
    $contractIds = [];
    for ($i = 1; $i <= 5; $i++) {
        $stmt = $pdo->prepare("INSERT INTO system_contracts (contract_number, contract_name, contract_type, contractor_name, contractor_phone, contractor_email, contract_start_date, contract_end_date, monthly_charges, yearly_charges, system_status, is_active, is_deleted, created_at, created_by)
                              VALUES (:num, :name, :type, :contractor, :phone, :email, '2025-01-01', '2026-01-01', :monthly, :yearly, 'active', 1, 0, NOW(), 1)");
        $stmt->execute([
            ':num' => 'CONTRACT-' . str_pad($i, 3, '0', STR_PAD_LEFT),
            ':name' => 'Contract ' . $i,
            ':type' => $i % 2 == 0 ? 'monthly' : 'yearly',
            ':contractor' => 'Contractor ' . $i,
            ':phone' => '030' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
            ':email' => 'contractor' . $i . '@example.com',
            ':monthly' => rand(50000, 200000),
            ':yearly' => rand(600000, 2000000)
        ]);
        $contractIds[] = $pdo->lastInsertId();
    }

    // 10. Add System Invoices (12 monthly invoices for 2025)
    echo "Adding 60 system invoices (12 per contract for 2025)...\n";
    $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
    $invoiceCounter = 1000;
    foreach ($contractIds as $contractId) {
        foreach ($months as $month) {
            $invoiceDate = "2025-{$month}-01";
            $dueDate = "2025-{$month}-15";
            $stmt = $pdo->prepare("INSERT INTO system_invoices (invoice_number, contract_id, invoice_month, invoice_year, invoice_date, due_date, amount, invoice_status, payment_status, created_at, created_by)
                                  VALUES (:num, :contract_id, :inv_month, 2025, :inv_date, :due_date, :amount, 'sent', 'unpaid', NOW(), 1)");
            $stmt->execute([
                ':num' => 'INV-2025' . str_pad($invoiceCounter++, 6, '0', STR_PAD_LEFT),
                ':contract_id' => $contractId,
                ':inv_month' => "2025-{$month}",
                ':inv_date' => $invoiceDate,
                ':due_date' => $dueDate,
                ':amount' => rand(50000, 200000)
            ]);
        }
    }

    // 11. Add Purchase Invoices (3 per supplier for 2025)
    echo "Adding purchase invoices...\n";
    $poCount = 0;
    foreach ($supplierIds as $supplierId) {
        for ($i = 1; $i <= 3; $i++) {
            $month = rand(1, 12);
            $invoiceDate = "2025-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO inventory_purchase_invoices (supplier_id, invoice_no, invoice_date, due_date, subtotal, grand_total, status, is_active, is_deleted, created_at)
                                  VALUES (:supplier_id, :num, :date, DATE_ADD(:date, INTERVAL 30 DAY), :total, :total, 'pending', 1, 0, NOW())");
            $stmt->execute([
                ':supplier_id' => $supplierId,
                ':num' => 'PO-' . date('Y') . '-' . str_pad($poCount++, 6, '0', STR_PAD_LEFT),
                ':date' => $invoiceDate,
                ':total' => rand(100000, 500000)
            ]);

            $invoiceId = $pdo->lastInsertId();

            // Add items to purchase invoice
            for ($item = 0; $item < rand(2, 4); $item++) {
                $productId = $productIds[array_rand($productIds)];
                $quantity = rand(5, 50);
                $price = rand(5000, 50000);
                $total = $quantity * $price;

                $stmt = $pdo->prepare("INSERT INTO inventory_purchase_invoice_items (purchase_invoice_id, product_id, quantity, unit_price, total_amount, created_at)
                                      VALUES (:invoice_id, :product_id, :qty, :price, :total, NOW())");
                $stmt->execute([
                    ':invoice_id' => $invoiceId,
                    ':product_id' => $productId,
                    ':qty' => $quantity,
                    ':price' => $price,
                    ':total' => $total
                ]);
            }
        }
    }

    // 11b. Add Sales Orders (required for sales invoices)
    echo "Adding sales orders...\n";
    $salesOrderIds = [];
    for ($i = 1; $i <= count($customerIds) * 3; $i++) {
        $customerId = $customerIds[($i - 1) % count($customerIds)];
        $month = rand(1, 12);
        $orderDate = "2025-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare("INSERT INTO inventory_sales_orders (customer_id, warehouse_id, order_number, order_date, delivery_date, order_status, grand_total, is_active, is_deleted, created_at, created_by)
                              VALUES (:customer_id, :warehouse_id, :num, :date, DATE_ADD(:date, INTERVAL 7 DAY), 'pending', :total, 1, 0, NOW(), 1)");
        $stmt->execute([
            ':customer_id' => $customerId,
            ':warehouse_id' => $warehouseId,
            ':num' => 'SO-' . date('Y') . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
            ':date' => $orderDate,
            ':total' => rand(100000, 500000)
        ]);
        $salesOrderIds[] = $pdo->lastInsertId();
    }

    // 12. Add Sales Invoices (3 per customer for 2025)
    echo "Adding sales invoices...\n";
    $siCount = 0;
    $soIndex = 0;
    foreach ($customerIds as $customerId) {
        for ($i = 1; $i <= 3; $i++) {
            $month = rand(1, 12);
            $invoiceDate = "2025-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO inventory_sales_invoices (customer_id, sales_order_id, invoice_no, warehouse_id, invoice_date, due_date, subtotal, grand_total, status, is_deleted, created_at, created_by)
                                  VALUES (:customer_id, :so_id, :num, :warehouse_id, :date, DATE_ADD(:date, INTERVAL 30 DAY), :total, :total, 'pending', 0, NOW(), 1)");
            $stmt->execute([
                ':customer_id' => $customerId,
                ':so_id' => $salesOrderIds[$soIndex++ % count($salesOrderIds)],
                ':warehouse_id' => $warehouseId,
                ':num' => 'SI-' . date('Y') . '-' . str_pad($siCount++, 6, '0', STR_PAD_LEFT),
                ':date' => $invoiceDate,
                ':total' => rand(100000, 500000)
            ]);

            $invoiceId = $pdo->lastInsertId();

            // Add items to sales invoice
            for ($item = 0; $item < rand(2, 4); $item++) {
                $productId = $productIds[array_rand($productIds)];
                $quantity = rand(1, 20);
                $price = rand(10000, 60000);
                $total = $quantity * $price;

                $stmt = $pdo->prepare("INSERT INTO inventory_sales_invoice_items (sales_invoice_id, product_id, quantity, unit_price, total, created_at)
                                      VALUES (:invoice_id, :product_id, :qty, :price, :total, NOW())");
                $stmt->execute([
                    ':invoice_id' => $invoiceId,
                    ':product_id' => $productId,
                    ':qty' => $quantity,
                    ':price' => $price,
                    ':total' => $total
                ]);
            }
        }
    }

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✓ DATA SEEDING COMPLETED SUCCESSFULLY!\n";
    echo str_repeat("=", 60) . "\n\n";

    echo "SUMMARY OF ADDED TEST DATA:\n";
    echo "- Warehouses: 1\n";
    echo "- Categories: " . count($categoryIds) . "\n";
    echo "- Brands: " . count($brandIds) . "\n";
    echo "- Suppliers: " . count($supplierIds) . "\n";
    echo "- Customers: " . count($customerIds) . "\n";
    echo "- Products: " . count($productIds) . "\n";
    echo "- Stock Records: " . count($productIds) . "\n";
    echo "- Contracts: " . count($contractIds) . " (Start Date: 2025-01-01)\n";
    echo "- System Invoices: " . (count($contractIds) * 12) . " (Monthly for 2025)\n";
    echo "- Purchase Invoices: " . ($poCount) . " (3 per supplier)\n";
    echo "- Sales Invoices: " . ($siCount) . " (3 per customer)\n\n";
    echo "Total Invoice Items: " . ($poCount * 3 + $siCount * 3) . " approx\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    die(1);
}
?>

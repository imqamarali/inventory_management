<?php
/**
 * Database Seeding Script
 * Adds comprehensive test data for system performance testing
 */

$dsn = 'mysql:host=localhost;dbname=inventory_system';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Starting data seeding...\n\n";

    // 1. Clear existing data (optional - comment out to keep existing data)
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
    $pdo->exec("TRUNCATE TABLE system_contracts");
    $pdo->exec("TRUNCATE TABLE system_invoices");
    $pdo->exec("TRUNCATE TABLE inventory_categories");
    $pdo->exec("TRUNCATE TABLE inventory_brands");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

    // 2. Add Warehouse (1 only)
    echo "Adding warehouse...\n";
    $warehouseId = 1;
    $pdo->exec("INSERT INTO inventory_warehouses (id, warehouse_name, location, manager_name, manager_contact, is_active, is_deleted, created_at, created_by)
               VALUES (1, 'Main Warehouse', 'Islamabad, Pakistan', 'Ahmed Khan', '03001234567', 1, 0, NOW(), 1)");

    // 3. Add Categories
    echo "Adding categories...\n";
    $categories = [
        ['name' => 'Electronics', 'description' => 'Electronic products'],
        ['name' => 'Furniture', 'description' => 'Office and home furniture'],
        ['name' => 'Supplies', 'description' => 'Office supplies'],
        ['name' => 'Equipment', 'description' => 'Industrial equipment'],
        ['name' => 'Appliances', 'description' => 'Home appliances']
    ];

    $categoryIds = [];
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("INSERT INTO inventory_categories (category_name, description, is_active, is_deleted, created_at, created_by)
                              VALUES (:name, :desc, 1, 0, NOW(), 1)");
        $stmt->execute([':name' => $cat['name'], ':desc' => $cat['description']]);
        $categoryIds[] = $pdo->lastInsertId();
    }

    // 4. Add Brands
    echo "Adding brands...\n";
    $brands = ['Samsung', 'LG', 'Sony', 'Apple', 'Dell', 'HP', 'Asus', 'Lenovo', 'ikea', 'Premier'];
    $brandIds = [];
    foreach ($brands as $brand) {
        $stmt = $pdo->prepare("INSERT INTO inventory_brands (brand_name, is_active, is_deleted, created_at, created_by)
                              VALUES (:name, 1, 0, NOW(), 1)");
        $stmt->execute([':name' => $brand]);
        $brandIds[] = $pdo->lastInsertId();
    }

    // 5. Add Suppliers (50+)
    echo "Adding suppliers...\n";
    $supplierNames = [
        'Global Supplies Co', 'Tech Imports Ltd', 'Premier Distributors', 'Quality Products Inc', 'Metro Electronics',
        'Elite Supplies', 'Standard Goods LLC', 'Interstate Trading', 'Pinnacle Distributors', 'Crown Suppliers',
        'Expert Suppliers', 'Trusted Goods Co', 'Summit Trading', 'Valley Distributors', 'Rainbow Supplies',
        'Phoenix Imports', 'Classic Goods', 'Venture Supplies', 'Unity Trading', 'Zenith Distributors',
        'Alpha Electronics', 'Beta Supplies', 'Gamma Imports', 'Delta Trading', 'Epsilon Goods',
        'Zeta Distributors', 'Eta Supplies', 'Theta Trading', 'Iota Electronics', 'Kappa Imports',
        'Lambda Goods', 'Mu Supplies', 'Nu Trading', 'Xi Distributors', 'Omicron Imports',
        'Pi Supplies', 'Rho Trading', 'Sigma Goods', 'Tau Electronics', 'Upsilon Imports',
        'Phi Supplies', 'Chi Trading', 'Psi Distributors', 'Omega Goods', 'Asia Traders',
        'Pak Supplies', 'Global Trade', 'Continental Imports', 'Universal Distributors', 'Prime Goods'
    ];

    $supplierIds = [];
    $cities = ['Islamabad', 'Karachi', 'Lahore', 'Peshawar', 'Quetta', 'Multan', 'Faisalabad', 'Rawalpindi'];

    foreach ($supplierNames as $i => $name) {
        $stmt = $pdo->prepare("INSERT INTO inventory_suppliers (supplier_name, contact_person, email, phone, city, address, is_active, is_deleted, created_at, created_by)
                              VALUES (:name, :contact, :email, :phone, :city, :address, 1, 0, NOW(), 1)");
        $stmt->execute([
            ':name' => $name,
            ':contact' => 'Contact ' . ($i + 1),
            ':email' => 'supplier' . ($i + 1) . '@example.com',
            ':phone' => '030' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
            ':city' => $cities[$i % count($cities)],
            ':address' => 'Address ' . ($i + 1) . ', City'
        ]);
        $supplierIds[] = $pdo->lastInsertId();
    }

    // 6. Add Customers (100+)
    echo "Adding customers...\n";
    $customerNames = [];
    for ($i = 1; $i <= 100; $i++) {
        $customerNames[] = 'Customer ' . $i;
    }

    $customerIds = [];
    foreach ($customerNames as $i => $name) {
        $stmt = $pdo->prepare("INSERT INTO inventory_customers (customer_name, contact_person, email, phone, city, address, is_active, is_deleted, created_at, created_by)
                              VALUES (:name, :contact, :email, :phone, :city, :address, 1, 0, NOW(), 1)");
        $stmt->execute([
            ':name' => $name,
            ':contact' => 'Contact ' . ($i + 1),
            ':email' => 'customer' . ($i + 1) . '@example.com',
            ':phone' => '030' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
            ':city' => $cities[$i % count($cities)],
            ':address' => 'Address ' . ($i + 1) . ', City'
        ]);
        $customerIds[] = $pdo->lastInsertId();
    }

    // 7. Add Products (200+)
    echo "Adding products...\n";
    $productTemplates = [
        'LED Monitor 24"', 'LED Monitor 27"', 'LED Monitor 32"',
        'Keyboard Wireless', 'Keyboard Mechanical', 'Keyboard USB',
        'Mouse Optical', 'Mouse Wireless', 'Mouse Gaming',
        'USB Cable 1m', 'USB Cable 2m', 'USB Cable 5m',
        'HDMI Cable', 'Network Cable', 'Power Cable',
        'Office Chair', 'Standing Desk', 'Computer Table',
        'Desk Lamp', 'Table Fan', 'Water Dispenser',
        'Printer Laser', 'Printer Inkjet', 'Scanner',
        'UPS 1KVA', 'UPS 2KVA', 'Stabilizer 1KVA',
    ];

    $productIds = [];
    for ($p = 0; $p < 8; $p++) {
        foreach ($productTemplates as $i => $template) {
            $productName = $template . ' - Variant ' . ($p + 1);
            $categoryId = $categoryIds[$i % count($categoryIds)];
            $brandId = $brandIds[$i % count($brandIds)];

            $stmt = $pdo->prepare("INSERT INTO inventory_products (product_name, product_sku, category_id, brand_id, purchase_price, selling_price, description, is_active, is_deleted, created_at, created_by)
                                  VALUES (:name, :sku, :cat_id, :brand_id, :pp, :sp, :desc, 1, 0, NOW(), 1)");
            $stmt->execute([
                ':name' => $productName,
                ':sku' => 'SKU-' . str_pad($p * 1000 + $i, 6, '0', STR_PAD_LEFT),
                ':cat_id' => $categoryId,
                ':brand_id' => $brandId,
                ':pp' => rand(1000, 50000),
                ':sp' => rand(1500, 60000),
                ':desc' => 'Product description for ' . $productName
            ]);
            $productIds[] = $pdo->lastInsertId();
        }
    }

    // 8. Add Stock
    echo "Adding stock records...\n";
    foreach ($productIds as $productId) {
        $stmt = $pdo->prepare("INSERT INTO inventory_stock (warehouse_id, product_id, quantity_in_stock, reorder_level, is_active, is_deleted, created_at)
                              VALUES (:warehouse_id, :product_id, :qty, :reorder, 1, 0, NOW())");
        $stmt->execute([
            ':warehouse_id' => $warehouseId,
            ':product_id' => $productId,
            ':qty' => rand(10, 500),
            ':reorder' => rand(5, 20)
        ]);
    }

    // 9. Add System Contracts
    echo "Adding contracts...\n";
    $contractCount = 5;
    $contractIds = [];
    for ($i = 1; $i <= $contractCount; $i++) {
        $stmt = $pdo->prepare("INSERT INTO system_contracts (contract_number, contract_name, contract_type, contractor_name, contractor_phone, contractor_email, installation_date, contract_start_date, contract_end_date, monthly_charges, yearly_charges, monthly_due_date, system_status, is_active, is_deleted, created_at, created_by)
                              VALUES (:num, :name, :type, :contractor, :phone, :email, :inst_date, :start_date, :end_date, :monthly, :yearly, 1, 'active', 1, 0, NOW(), 1)");
        $stmt->execute([
            ':num' => 'CONTRACT-' . str_pad($i, 3, '0', STR_PAD_LEFT),
            ':name' => 'Contract ' . $i,
            ':type' => $i % 2 == 0 ? 'monthly' : 'yearly',
            ':contractor' => 'Contractor ' . $i,
            ':phone' => '030' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
            ':email' => 'contractor' . $i . '@example.com',
            ':inst_date' => '2025-01-01',
            ':start_date' => '2025-01-01',
            ':end_date' => '2026-01-01',
            ':monthly' => rand(50000, 200000),
            ':yearly' => rand(600000, 2000000)
        ]);
        $contractIds[] = $pdo->lastInsertId();
    }

    // 10. Add System Invoices (12 monthly invoices for 2025)
    echo "Adding system invoices...\n";
    $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
    foreach ($contractIds as $contractId) {
        foreach ($months as $month) {
            $invoiceDate = "2025-{$month}-01";
            $dueDate = "2025-{$month}-15";
            $stmt = $pdo->prepare("INSERT INTO system_invoices (invoice_number, contract_id, invoice_month, invoice_year, invoice_date, due_date, amount, invoice_status, payment_status, created_at, created_by)
                                  VALUES (:num, :contract_id, :inv_month, 2025, :inv_date, :due_date, :amount, 'sent', 'unpaid', NOW(), 1)");
            $stmt->execute([
                ':num' => 'INV-' . date('YmdHis') . '-' . rand(1000, 9999),
                ':contract_id' => $contractId,
                ':inv_month' => "2025-{$month}",
                ':inv_date' => $invoiceDate,
                ':due_date' => $dueDate,
                ':amount' => rand(50000, 200000)
            ]);
        }
    }

    // 11. Add Purchase Invoices (multiple per supplier for 2025)
    echo "Adding purchase invoices...\n";
    $invoiceCount = 0;
    foreach ($supplierIds as $supplierId) {
        for ($month = 1; $month <= 12; $month++) {
            $invoiceDate = "2025-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO inventory_purchase_invoices (supplier_id, invoice_number, invoice_date, due_date, total_amount, paid_amount, status, created_at, created_by)
                                  VALUES (:supplier_id, :num, :date, DATE_ADD(:date, INTERVAL 30 DAY), :total, 0, 'pending', NOW(), 1)");
            $stmt->execute([
                ':supplier_id' => $supplierId,
                ':num' => 'PO-' . date('Y') . '-' . str_pad($invoiceCount++, 6, '0', STR_PAD_LEFT),
                ':date' => $invoiceDate,
                ':total' => rand(100000, 500000)
            ]);

            $invoiceId = $pdo->lastInsertId();

            // Add items to purchase invoice
            $itemCount = rand(2, 5);
            for ($item = 0; $item < $itemCount; $item++) {
                $productId = $productIds[array_rand($productIds)];
                $quantity = rand(5, 50);
                $price = rand(5000, 50000);

                $stmt = $pdo->prepare("INSERT INTO inventory_purchase_invoice_items (purchase_invoice_id, product_id, quantity, unit_price, total_price, created_at)
                                      VALUES (:invoice_id, :product_id, :qty, :price, :total, NOW())");
                $stmt->execute([
                    ':invoice_id' => $invoiceId,
                    ':product_id' => $productId,
                    ':qty' => $quantity,
                    ':price' => $price,
                    ':total' => $quantity * $price
                ]);
            }
        }
    }

    // 12. Add Sales Invoices (multiple per customer for 2025)
    echo "Adding sales invoices...\n";
    $salesCount = 0;
    foreach ($customerIds as $customerId) {
        for ($month = 1; $month <= 12; $month++) {
            $invoiceDate = "2025-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO inventory_sales_invoices (customer_id, invoice_number, invoice_date, due_date, total_amount, paid_amount, status, created_at, created_by)
                                  VALUES (:customer_id, :num, :date, DATE_ADD(:date, INTERVAL 30 DAY), :total, 0, 'pending', NOW(), 1)");
            $stmt->execute([
                ':customer_id' => $customerId,
                ':num' => 'SI-' . date('Y') . '-' . str_pad($salesCount++, 6, '0', STR_PAD_LEFT),
                ':date' => $invoiceDate,
                ':total' => rand(100000, 500000)
            ]);

            $invoiceId = $pdo->lastInsertId();

            // Add items to sales invoice
            $itemCount = rand(2, 5);
            for ($item = 0; $item < $itemCount; $item++) {
                $productId = $productIds[array_rand($productIds)];
                $quantity = rand(1, 20);
                $price = rand(10000, 60000);

                $stmt = $pdo->prepare("INSERT INTO inventory_sales_invoice_items (sales_invoice_id, product_id, quantity, unit_price, total_price, created_at)
                                      VALUES (:invoice_id, :product_id, :qty, :price, :total, NOW())");
                $stmt->execute([
                    ':invoice_id' => $invoiceId,
                    ':product_id' => $productId,
                    ':qty' => $quantity,
                    ':price' => $price,
                    ':total' => $quantity * $price
                ]);
            }
        }
    }

    echo "\n✓ Data seeding completed successfully!\n\n";
    echo "Summary:\n";
    echo "- Warehouses: 1\n";
    echo "- Categories: " . count($categoryIds) . "\n";
    echo "- Brands: " . count($brandIds) . "\n";
    echo "- Suppliers: " . count($supplierIds) . "\n";
    echo "- Customers: " . count($customerIds) . "\n";
    echo "- Products: " . count($productIds) . "\n";
    echo "- Contracts: " . count($contractIds) . "\n";
    echo "- System Invoices: " . ($contractCount * 12) . "\n";
    echo "- Purchase Invoices: " . ($invoiceCount) . "\n";
    echo "- Sales Invoices: " . ($salesCount) . "\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    die(1);
}
?>

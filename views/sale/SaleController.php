<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class SaleController extends Controller
{
    private function currentUserId()
    {
        $user_array = Yii::$app->session->get('user_array');
        return $user_array['id'] ?? null;
    }
    private function jsonResponse($success, $message, $data = [])
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return array_merge([
            'success' => $success,
            'message' => $message,
        ], $data);
    }
    private function generateDocNo($prefix)
    {
        return $prefix . '-' . date('Ymd') . '-' . date('His') . '-' . mt_rand(100, 999);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (Yii::$app->session->has('user_array') == NULL) {
            $this->redirect(['site/index']);
            return false;
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    
    public function actionSales()
    {
        $modules = [
            ['name' => 'Sales Dashboard', 'controller' => 'sale/salesdashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Sale Order', 'controller' => 'sale/salesorders', 'icon' => 'fa fa-shopping-bag', 'badge' => 'Unified Order & POS'],
            ['name' => 'Sales Invoices', 'controller' => 'sale/salesinvoices', 'icon' => 'fa fa-file-text'],
            ['name' => 'Pending Orders', 'controller' => 'sale/pendingorders', 'icon' => 'fa fa-clock-o'],
            ['name' => 'Delivered Orders', 'controller' => 'sale/deliveredorders', 'icon' => 'fa fa-check-circle'],
            ['name' => 'Cancelled Orders', 'controller' => 'sale/cancelledorders', 'icon' => 'fa fa-ban'],
            ['name' => 'Sales Returns', 'controller' => 'sale/salesreturns', 'icon' => 'fa fa-reply'],
            ['name' => 'Customer Payments', 'controller' => 'sale/customerpayments', 'icon' => 'fa fa-money'],
            ['name' => 'Sales Reports', 'controller' => 'sale/salesreports', 'icon' => 'fa fa-line-chart'],
        ];

        return $this->render('sales', compact('modules'));
    }

    
    public function actionSalesdashboard()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            try {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Data Saved successfully!'
                ];
            } catch (\Exception $e) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Failed to update data. Please try again.'
                ];
            }
        }

        return $this->renderPartial('salesdashboard');
    }

    public function actionSalesorders()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            try {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Data Saved successfully!'
                ];
            } catch (\Exception $e) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Failed to update data. Please try again.'
                ];
            }
        }

        return $this->renderPartial('salesorders');
    }

    public function actionPossales()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            try {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Data Saved successfully!'
                ];
            } catch (\Exception $e) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Failed to update data. Please try again.'
                ];
            }
        }

        return $this->renderPartial('possales');
    }

    public function actionCreatesale()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            try {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Data Saved successfully!'
                ];
            } catch (\Exception $e) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Failed to update data. Please try again.'
                ];
            }
        }

        return $this->renderPartial('createsale');
    }

    public function actionSalesinvoices()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            try {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Data Saved successfully!'
                ];
            } catch (\Exception $e) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Failed to update data. Please try again.'
                ];
            }
        }

        return $this->renderPartial('salesinvoices');
    }

    public function actionPendingorders()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            try {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Data Saved successfully!'
                ];
            } catch (\Exception $e) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Failed to update data. Please try again.'
                ];
            }
        }

        return $this->renderPartial('pendingorders');
    }

    public function actionDeliveredorders()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            try {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Data Saved successfully!'
                ];
            } catch (\Exception $e) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Failed to update data. Please try again.'
                ];
            }
        }

        return $this->renderPartial('deliveredorders');
    }

    public function actionCancelledorders()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            try {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Data Saved successfully!'
                ];
            } catch (\Exception $e) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Failed to update data. Please try again.'
                ];
            }
        }

        return $this->renderPartial('cancelledorders');
    }

    public function actionSalesreturns()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            try {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Data Saved successfully!'
                ];
            } catch (\Exception $e) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Failed to update data. Please try again.'
                ];
            }
        }

        return $this->renderPartial('salesreturns');
    }


    public function actionSalesreports()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            try {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Data Saved successfully!'
                ];
            } catch (\Exception $e) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Failed to update data. Please try again.'
                ];
            }
        }

        return $this->renderPartial('salesreports');
    }


    public function actionInjectdb()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_categories ( 
                id INT AUTO_INCREMENT PRIMARY KEY,
                parent_id INT NULL,
                category_name VARCHAR(150) NOT NULL,
                category_code VARCHAR(50) UNIQUE,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT NULL,
                updated_by INT NULL,
                is_active TINYINT(1) DEFAULT 1,
                is_deleted TINYINT(1) DEFAULT 0,
                INDEX(parent_id)

            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_brands ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                brand_name VARCHAR(150) NOT NULL,
                brand_code VARCHAR(50), 
                website VARCHAR(255),
                email VARCHAR(150),
                phone VARCHAR(30), 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_units ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                unit_name VARCHAR(100),
                short_name VARCHAR(20), 
                description TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_products ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                category_id INT,
                brand_id INT,
                unit_id INT, 
                product_name VARCHAR(200) NOT NULL,
                sku VARCHAR(100) UNIQUE,
                barcode VARCHAR(150), 
                description TEXT, 
                purchase_price DECIMAL(15,2) DEFAULT 0,
                selling_price DECIMAL(15,2) DEFAULT 0, 
                minimum_stock DECIMAL(15,2) DEFAULT 0,
                maximum_stock DECIMAL(15,2) DEFAULT 0,
                reorder_level DECIMAL(15,2) DEFAULT 0, 
                product_image VARCHAR(255), 
                weight DECIMAL(10,2),
                length DECIMAL(10,2),
                width DECIMAL(10,2),
                height DECIMAL(10,2), 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0, 
                INDEX(category_id),
                INDEX(brand_id),
                INDEX(unit_id), 
                FOREIGN KEY(category_id)
                    REFERENCES inventory_categories(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(brand_id)
                    REFERENCES inventory_brands(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(unit_id)
                    REFERENCES inventory_units(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_warehouses ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                warehouse_name VARCHAR(200), 
                warehouse_code VARCHAR(50), 
                address TEXT, 
                city VARCHAR(100),
                province VARCHAR(100),
                country VARCHAR(100), 
                contact_person VARCHAR(150),
                phone VARCHAR(50),
                email VARCHAR(150), 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                warehouse_id INT NOT NULL,
                product_id INT NOT NULL, 
                quantity DECIMAL(15,2) DEFAULT 0, 
                reserved_quantity DECIMAL(15,2) DEFAULT 0, 
                available_quantity DECIMAL(15,2) DEFAULT 0, 
                average_cost DECIMAL(15,2) DEFAULT 0, 
                last_purchase_price DECIMAL(15,2) DEFAULT 0, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0, 
                UNIQUE(product_id,warehouse_id), 
                INDEX(product_id),
                INDEX(warehouse_id), 
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            echo "Part 1 database created successfully.";

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_suppliers ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                supplier_code VARCHAR(50) UNIQUE, 
                company_name VARCHAR(200) NOT NULL, 
                contact_person VARCHAR(150), 
                email VARCHAR(150), 
                phone VARCHAR(50), 
                mobile VARCHAR(50), 
                website VARCHAR(255), 
                tax_number VARCHAR(100), 
                payment_terms INT DEFAULT 30, 
                credit_limit DECIMAL(15,2) DEFAULT 0, 
                opening_balance DECIMAL(15,2) DEFAULT 0, 
                current_balance DECIMAL(15,2) DEFAULT 0, 
                address TEXT, 
                city VARCHAR(100), 
                province VARCHAR(100), 
                country VARCHAR(100), 
                postal_code VARCHAR(20), 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP,
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_supplier_contacts ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                supplier_id INT NOT NULL, 
                contact_name VARCHAR(150), 
                designation VARCHAR(100), 
                phone VARCHAR(50), 
                mobile VARCHAR(50), 
                email VARCHAR(150), 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(supplier_id), 
                FOREIGN KEY(supplier_id)
                    REFERENCES inventory_suppliers(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
                CREATE TABLE IF NOT EXISTS inventory_supplier_documents (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    supplier_id INT NOT NULL,
                    document_type VARCHAR(100),
                    document_name VARCHAR(255) NOT NULL,
                    document_file VARCHAR(500),
                    expiry_date DATE NULL,
                    remarks TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                        ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT NULL,
                    updated_by INT NULL,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(supplier_id),
                    FOREIGN KEY(supplier_id)
                        REFERENCES inventory_suppliers(id)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
                ) ENGINE=InnoDB;
                ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_purchase_orders ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                po_number VARCHAR(100) UNIQUE, 
                supplier_id INT NOT NULL, 
                warehouse_id INT NOT NULL, 
                order_date DATE, 
                expected_date DATE, 
                status ENUM( 
                    'Draft', 
                    'Approved', 
                    'Partially Received', 
                    'Completed', 
                    'Cancelled' 
                ) DEFAULT 'Draft', 
                subtotal DECIMAL(15,2) DEFAULT 0, 
                discount DECIMAL(15,2) DEFAULT 0, 
                tax DECIMAL(15,2) DEFAULT 0, 
                freight DECIMAL(15,2) DEFAULT 0, 
                grand_total DECIMAL(15,2) DEFAULT 0, 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(supplier_id), 
                INDEX(warehouse_id), 
                FOREIGN KEY(supplier_id)
                    REFERENCES inventory_suppliers(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_purchase_order_items ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                purchase_order_id INT NOT NULL, 
                product_id INT NOT NULL, 
                quantity DECIMAL(15,2) DEFAULT 0, 
                received_quantity DECIMAL(15,2) DEFAULT 0, 
                remaining_quantity DECIMAL(15,2) DEFAULT 0, 
                unit_price DECIMAL(15,2) DEFAULT 0, 
                discount DECIMAL(15,2) DEFAULT 0, 
                tax DECIMAL(15,2) DEFAULT 0, 
                total DECIMAL(15,2) DEFAULT 0, 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(purchase_order_id), 
                INDEX(product_id), 
                FOREIGN KEY(purchase_order_id)
                    REFERENCES inventory_purchase_orders(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE, 
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_customers ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                customer_code VARCHAR(50) UNIQUE, 
                customer_type ENUM(
                    'Individual',
                    'Company'
                ) DEFAULT 'Individual', 
                company_name VARCHAR(200), 
                first_name VARCHAR(100), 
                last_name VARCHAR(100), 
                email VARCHAR(150), 
                phone VARCHAR(50), 
                mobile VARCHAR(50), 
                tax_number VARCHAR(100), 
                credit_limit DECIMAL(15,2) DEFAULT 0, 
                opening_balance DECIMAL(15,2) DEFAULT 0, 
                current_balance DECIMAL(15,2) DEFAULT 0, 
                payment_terms INT DEFAULT 0, 
                address TEXT, 
                city VARCHAR(100), 
                province VARCHAR(100), 
                country VARCHAR(100), 
                postal_code VARCHAR(20), 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_customer_contacts ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                customer_id INT NOT NULL, 
                contact_name VARCHAR(150), 
                designation VARCHAR(100), 
                email VARCHAR(150), 
                phone VARCHAR(50), 
                mobile VARCHAR(50), 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(customer_id), 
                FOREIGN KEY(customer_id)
                    REFERENCES inventory_customers(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_sales_orders ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                order_number VARCHAR(100) UNIQUE, 
                customer_id INT NOT NULL, 
                warehouse_id INT NOT NULL, 
                order_date DATE, 
                delivery_date DATE, 
                order_status ENUM( 
                    'Draft', 
                    'Confirmed', 
                    'Packed', 
                    'Dispatched',  
                    'Delivered', 
                    'Cancelled' 
                ) DEFAULT 'Draft', 
                payment_status ENUM( 
                    'Pending', 
                    'Partial', 
                    'Paid' 
                ) DEFAULT 'Pending', 
                subtotal DECIMAL(15,2) DEFAULT 0, 
                discount DECIMAL(15,2) DEFAULT 0, 
                tax DECIMAL(15,2) DEFAULT 0, 
                shipping DECIMAL(15,2) DEFAULT 0, 
                grand_total DECIMAL(15,2) DEFAULT 0, 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(customer_id), 
                INDEX(warehouse_id), 
                FOREIGN KEY(customer_id)
                    REFERENCES inventory_customers(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_sales_order_items ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                sales_order_id INT NOT NULL, 
                product_id INT NOT NULL, 
                quantity DECIMAL(15,2) DEFAULT 0, 
                delivered_quantity DECIMAL(15,2) DEFAULT 0, 
                remaining_quantity DECIMAL(15,2) DEFAULT 0, 
                unit_price DECIMAL(15,2) DEFAULT 0, 
                discount DECIMAL(15,2) DEFAULT 0, 
                tax DECIMAL(15,2) DEFAULT 0, 
                total DECIMAL(15,2) DEFAULT 0, 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(sales_order_id), 
                INDEX(product_id), 
                FOREIGN KEY(sales_order_id)
                    REFERENCES inventory_sales_orders(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE, 
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_movements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                movement_no VARCHAR(50) UNIQUE,
                warehouse_id INT NOT NULL,
                product_id INT NOT NULL,
                reference_type ENUM(
                    'Purchase',
                    'Sale',
                    'Transfer In',
                    'Transfer Out',
                    'Adjustment',
                    'Return Purchase',
                    'Return Sale',
                    'Opening Stock',
                    'Stock Audit'
                ) NOT NULL,
                reference_id INT NULL,
                movement_type ENUM('IN','OUT') NOT NULL,
                quantity DECIMAL(15,2) NOT NULL,
                unit_cost DECIMAL(15,2) DEFAULT 0,
                total_cost DECIMAL(15,2) DEFAULT 0,
                remarks TEXT,
                movement_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(product_id),
                INDEX(warehouse_id),
                INDEX(reference_id),
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
                    ON UPDATE CASCADE,
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_adjustments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                adjustment_no VARCHAR(50) UNIQUE,
                warehouse_id INT NOT NULL,
                adjustment_date DATE,
                adjustment_type ENUM('Increase','Decrease') NOT NULL,
                reason ENUM('Damage','Expired','Lost','Correction','Other'),
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(warehouse_id) REFERENCES inventory_warehouses(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_adjustment_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                adjustment_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity DECIMAL(15,2),
                unit_cost DECIMAL(15,2),
                total_cost DECIMAL(15,2),
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(adjustment_id)
                    REFERENCES inventory_stock_adjustments(id)
                    ON DELETE CASCADE,
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_transfers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transfer_no VARCHAR(50) UNIQUE,
                from_warehouse INT NOT NULL,
                to_warehouse INT NOT NULL,
                transfer_date DATE,
                status ENUM('Pending','In Transit','Completed','Cancelled') DEFAULT 'Pending',
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(from_warehouse) REFERENCES inventory_warehouses(id),
                FOREIGN KEY(to_warehouse) REFERENCES inventory_warehouses(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_transfer_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transfer_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity DECIMAL(15,2),
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(transfer_id)
                    REFERENCES inventory_stock_transfers(id)
                    ON DELETE CASCADE,
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_audits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                audit_no VARCHAR(50) UNIQUE,
                warehouse_id INT,
                audit_date DATE,
                status ENUM('Open','Completed') DEFAULT 'Open',
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_audit_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                audit_id INT NOT NULL,
                product_id INT NOT NULL,
                system_quantity DECIMAL(15,2),
                physical_quantity DECIMAL(15,2),
                variance DECIMAL(15,2),
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(audit_id)
                    REFERENCES inventory_stock_audits(id)
                    ON DELETE CASCADE,
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_accounts ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                parent_id INT NULL, 
                account_code VARCHAR(50) UNIQUE, 
                account_name VARCHAR(200) NOT NULL, 
                account_type ENUM('Asset','Liability','Equity','Income','Expense') NOT NULL,
                opening_balance DECIMAL(15,2) DEFAULT 0,
                current_balance DECIMAL(15,2) DEFAULT 0,
                remarks TEXT,

                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,

                created_by INT,
                updated_by INT,

                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,

                INDEX(parent_id),

                FOREIGN KEY(parent_id)
                    REFERENCES inventory_accounts(id)
                    ON UPDATE CASCADE
                    ON DELETE SET NULL

            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transaction_no VARCHAR(100) UNIQUE,
                transaction_date DATE,
                reference_type ENUM('Purchase','Sale','Payment','Receipt','Expense','Adjustment'),
                reference_id INT,
                account_id INT NOT NULL,
                transaction_type ENUM('Debit','Credit'
                ) NOT NULL,
                amount DECIMAL(15,2) NOT NULL,
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(account_id),
                FOREIGN KEY(account_id)
                    REFERENCES inventory_accounts(id)
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_payments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                payment_no VARCHAR(100) UNIQUE,
                payment_date DATE,
                payment_type ENUM('Receive','Pay'),
                reference_type ENUM('Customer','Supplier'),
                reference_id INT,
                payment_method ENUM('Cash','Bank','Cheque','Online'),
                account_id INT,
                amount DECIMAL(15,2),
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(account_id)
                    REFERENCES inventory_accounts(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                title VARCHAR(255),
                message TEXT,
                notification_type ENUM('Info','Success','Warning','Error') DEFAULT 'Info',
                is_read TINYINT DEFAULT 0,
                read_at DATETIME NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(user_id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_logs (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                module VARCHAR(100),
                action VARCHAR(100),
                table_name VARCHAR(100),
                record_id BIGINT,
                old_data LONGTEXT,
                new_data LONGTEXT,
                ip_address VARCHAR(50),
                user_agent TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX(user_id),
                INDEX(module),
                INDEX(table_name),
                INDEX(record_id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_reports (
                id INT AUTO_INCREMENT PRIMARY KEY,
                report_name VARCHAR(200),
                report_type VARCHAR(100),
                generated_by INT,
                filters JSON,
                file_path VARCHAR(500),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                is_deleted TINYINT DEFAULT 0
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_vehicle_makes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                make_name VARCHAR(150) NOT NULL,
                make_code VARCHAR(50) UNIQUE,
                country VARCHAR(100),
                website VARCHAR(255),
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_vehicle_models (
                id INT AUTO_INCREMENT PRIMARY KEY,
                make_id INT NOT NULL,
                model_name VARCHAR(150) NOT NULL,
                model_code VARCHAR(50),
                model_year VARCHAR(50),
                engine_type VARCHAR(100),
                engine_capacity VARCHAR(50),
                fuel_type ENUM(
                    'Petrol',
                    'Diesel',
                    'Hybrid',
                    'Electric',
                    'CNG'
                ) DEFAULT 'Petrol',
                transmission ENUM(
                    'Manual',
                    'Automatic',
                    'CVT'
                ) DEFAULT 'Manual',
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(make_id),
                FOREIGN KEY(make_id)
                    REFERENCES inventory_vehicle_makes(id)
                    ON UPDATE CASCADE
                    ON DELETE CASCADE
            ) ENGINE=InnoDB;
            ")->execute();

            $transaction->commit();

            echo "Database created successfully.";
            exit;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo "Error: " . $e->getMessage();
            exit;
        }
    }
}

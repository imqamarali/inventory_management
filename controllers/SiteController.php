<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class SiteController extends Controller
{
    private $maxLoginAttempts = 5;
    private $lockoutDuration = 300; // 5 minutes

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'index'],
                'rules' => [
                    [
                        'actions' => ['logout'],
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
        $this->enableCsrfValidation = true;
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        if (Yii::$app->session->has('user_array') && Yii::$app->user->id !== null) {
            return $this->redirect(['inventory/dashboard']);
        }
        return $this->redirect(['site/login']);
    }

    public function actionLogin()
    {
        if (Yii::$app->session->has('user_array') && Yii::$app->user->id !== null) {
            return $this->redirect(['inventory/dashboard']);
        }

        $ip = Yii::$app->request->userIP;
        $lockKey = "login_lockout_$ip";
        $attemptsKey = "login_attempts_$ip";

        if (Yii::$app->cache->get($lockKey)) {
            // Calculate remaining lockout time
            $lockoutTime = Yii::$app->cache->get($lockKey . '_time');
            $remainingSeconds = max(0, $lockoutTime - time());

            Yii::$app->session->setFlash('error', 'Too many login attempts. Please try again later.');
            $this->layout = false;
            return $this->render('login', [
                'model' => new LoginForm(),
                'isLockedOut' => true,
                'lockoutEndTime' => $lockoutTime,
                'remainingSeconds' => $remainingSeconds
            ]);
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->login()) {
                Yii::$app->cache->delete($attemptsKey);
                $user = Yii::$app->user->identity;

                $session = Yii::$app->session;
                $session->open();
                $session['user_array'] = [
                    'id' => $user->id,
                    'referance' => $user->referance ?? null,
                    'username' => $user->username,
                    'email' => $user->email ?? null,
                    'first_name' => $user->first_name ?? null,
                    'last_name' => $user->last_name ?? null,
                    'phone' => $user->phone ?? null,
                    'role_id' => $user->role_id ?? null,
                    'profile_picture' => $user->profile_picture ?? null,
                    'address' => $user->address ?? null,
                    'date_of_birth' => $user->date_of_birth ?? null,
                    'created_at' => $user->created_at ?? null,
                    'gender' => $user->gender ?? null,
                    'last_login' => $user->last_login ?? null,
                    'school_id' => $user->school_id ?? null
                ];

                Yii::$app->db->createCommand()
                    ->update('system_users', ['last_login' => date('Y-m-d H:i:s')], ['id' => $user->id])
                    ->execute();

                // Check for pending invoices and access restrictions
                $paymentStatus = $this->checkPaymentStatus($user->id, $user->role_id ?? null);
                if ($paymentStatus['status'] === 'restricted' && !$paymentStatus['is_super_admin']) {
                    Yii::$app->session->setFlash('error', 'System access restricted. Please update your payment status.');
                    Yii::$app->user->logout();
                    return $this->redirect(['site/login']);
                }

                // Store pending invoice info in session for modal display
                Yii::$app->session['pending_invoice_info'] = $paymentStatus['pending_info'] ?? null;

                return $this->redirect(['inventory/dashboard']);
            } else {
                $attempts = (int)Yii::$app->cache->get($attemptsKey) + 1;
                Yii::$app->cache->set($attemptsKey, $attempts, 3600);

                if ($attempts >= $this->maxLoginAttempts) {
                    $lockoutEndTime = time() + $this->lockoutDuration;
                    Yii::$app->cache->set($lockKey, true, $this->lockoutDuration);
                    Yii::$app->cache->set($lockKey . '_time', $lockoutEndTime, $this->lockoutDuration);
                    Yii::$app->session->setFlash('error', 'Account temporarily locked. Try again in 5 minutes.');
                } else {
                    $remaining = $this->maxLoginAttempts - $attempts;
                    Yii::$app->session->setFlash('error', "Invalid credentials. Attempts remaining: $remaining");
                }
            }
        }

        $model->password = '';
        $this->layout = false;
        return $this->render('login', [
            'model' => $model,
            'isLockedOut' => false,
            'lockoutEndTime' => null,
            'remainingSeconds' => 0
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout(false);
        Yii::$app->session->destroy();
        return $this->redirect(['site/login']);
    }

    public function actionProfile()
    {
        if (!Yii::$app->session->has('user_array')) {
            return $this->redirect(['site/login']);
        }

        $userId = Yii::$app->session->get('user_array')['id'];
        $user = Yii::$app->db->createCommand(
            "SELECT system_users.*, roles.name as role_name FROM system_users
            LEFT JOIN roles ON system_users.role_id = roles.id
            WHERE system_users.id = :id"
        )->bindValue(':id', $userId)->queryOne();

        if (!$user) {
            Yii::$app->session->setFlash('error', 'User not found.');
            return $this->redirect(['inventory/dashboard']);
        }

        $this->layout = false;
        return $this->render('profile', ['user' => $user]);
    }

    public function actionUpdateProfile()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->session->has('user_array')) {
            return ['success' => false, 'message' => 'User not authenticated'];
        }

        $userId = Yii::$app->session->get('user_array')['id'];

        if (isset($_FILES['profile_file']) && $_FILES['profile_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_file'];
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($file['tmp_name']);

            if (!in_array(strtolower($fileType), $allowedTypes)) {
                return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed.'];
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
            }

            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $uploadDir = Yii::getAlias('@webroot/documents/profiles/');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = 'profile_' . $userId . '_' . time() . '.' . $fileExtension;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $oldPicture = Yii::$app->db->createCommand("SELECT profile_picture FROM system_users WHERE id = :id")
                    ->bindValue(':id', $userId)->queryScalar();

                if ($oldPicture && file_exists(Yii::getAlias('@webroot/' . $oldPicture))) {
                    @unlink(Yii::getAlias('@webroot/' . $oldPicture));
                }

                Yii::$app->db->createCommand()->update('system_users', [
                    'profile_picture' => 'documents/profiles/' . $newFileName
                ], ['id' => $userId])->execute();

                $session = Yii::$app->session->get('user_array');
                $session['profile_picture'] = 'documents/profiles/' . $newFileName;
                Yii::$app->session->set('user_array', $session);

                return ['success' => true, 'message' => 'Profile picture updated successfully!'];
            }
            return ['success' => false, 'message' => 'Failed to upload file'];
        }

        $postData = Yii::$app->request->post();
        if (empty($postData)) {
            return ['success' => false, 'message' => 'No data provided'];
        }

        $updateData = array_filter($postData, function ($key) {
            return $key !== 'csrf_token';
        }, ARRAY_FILTER_USE_KEY);

        if (Yii::$app->db->createCommand()->update('system_users', $updateData, ['id' => $userId])->execute()) {
            return ['success' => true, 'message' => 'Profile updated successfully.'];
        }
        return ['success' => false, 'message' => 'Failed to update profile.'];
    }

    public function actionForgotPassword()
    {
        if (Yii::$app->user->id) {
            return $this->redirect(['site/index']);
        }

        if (Yii::$app->request->isPost) {
            $email = Yii::$app->request->post('email');

            if (empty($email)) {
                Yii::$app->session->setFlash('error', 'Email is required.');
                $this->layout = false;
                return $this->render('forgot-password');
            }

            $user = Yii::$app->db->createCommand(
                "SELECT id, username, email FROM system_users WHERE email = :email LIMIT 1"
            )->bindValue(':email', $email)->queryOne();

            if (!$user) {
                Yii::$app->session->setFlash('error', 'Email not found in system.');
                $this->layout = false;
                return $this->render('forgot-password');
            }

            $tempPassword = bin2hex(random_bytes(8));
            $hashedPassword = password_hash($tempPassword, PASSWORD_BCRYPT, ['cost' => 12]);

            Yii::$app->db->createCommand()->update('system_users', [
                'password' => $hashedPassword
            ], ['id' => $user['id']])->execute();

            $message = "Your temporary password is: {$tempPassword}\n\nPlease login and change it immediately.";
            if (mail($user['email'], 'Password Reset - Inventory System', $message)) {
                Yii::$app->session->setFlash('success', 'New password sent to your email. Please check your inbox.');
                $this->layout = false;
                return $this->render('forgot-password');
            } else {
                Yii::$app->db->createCommand()->update('system_users', [
                    'password' => $tempPassword
                ], ['id' => $user['id']])->execute();
                Yii::$app->session->setFlash('success', "Password reset to: {$tempPassword}. Please login and change it.");
                $this->layout = false;
                return $this->render('forgot-password');
            }
        }

        $this->layout = false;
        return $this->render('forgot-password');
    }

    public function actionInjectdb()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            $tables = [
                "CREATE TABLE IF NOT EXISTS inventory_categories (
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
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_brands (
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
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_units (
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
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_products (
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
                    FOREIGN KEY(category_id) REFERENCES inventory_categories(id) ON UPDATE CASCADE,
                    FOREIGN KEY(brand_id) REFERENCES inventory_brands(id) ON UPDATE CASCADE,
                    FOREIGN KEY(unit_id) REFERENCES inventory_units(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_warehouses (
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
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_stock (
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
                    FOREIGN KEY(product_id) REFERENCES inventory_products(id) ON UPDATE CASCADE,
                    FOREIGN KEY(warehouse_id) REFERENCES inventory_warehouses(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_suppliers (
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
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_supplier_contacts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    supplier_id INT NOT NULL,
                    contact_name VARCHAR(150),
                    designation VARCHAR(100),
                    phone VARCHAR(50),
                    mobile VARCHAR(50),
                    email VARCHAR(150),
                    notes TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(supplier_id),
                    FOREIGN KEY(supplier_id) REFERENCES inventory_suppliers(id) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_supplier_documents (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    supplier_id INT NOT NULL,
                    document_type VARCHAR(100),
                    document_name VARCHAR(255) NOT NULL,
                    document_file VARCHAR(500),
                    expiry_date DATE NULL,
                    remarks TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT NULL,
                    updated_by INT NULL,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(supplier_id),
                    FOREIGN KEY(supplier_id) REFERENCES inventory_suppliers(id) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_purchase_orders (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    po_number VARCHAR(100) UNIQUE,
                    supplier_id INT NOT NULL,
                    warehouse_id INT NOT NULL,
                    order_date DATE,
                    expected_date DATE,
                    status ENUM('Draft','Approved','Partially Received','Completed','Cancelled') DEFAULT 'Draft',
                    subtotal DECIMAL(15,2) DEFAULT 0,
                    discount DECIMAL(15,2) DEFAULT 0,
                    tax DECIMAL(15,2) DEFAULT 0,
                    freight DECIMAL(15,2) DEFAULT 0,
                    grand_total DECIMAL(15,2) DEFAULT 0,
                    notes TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(supplier_id),
                    INDEX(warehouse_id),
                    FOREIGN KEY(supplier_id) REFERENCES inventory_suppliers(id) ON UPDATE CASCADE,
                    FOREIGN KEY(warehouse_id) REFERENCES inventory_warehouses(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_purchase_order_items (
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
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(purchase_order_id),
                    INDEX(product_id),
                    FOREIGN KEY(purchase_order_id) REFERENCES inventory_purchase_orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
                    FOREIGN KEY(product_id) REFERENCES inventory_products(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_customers (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    customer_code VARCHAR(50) UNIQUE,
                    customer_type ENUM('Individual','Company') DEFAULT 'Individual',
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
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_customer_contacts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    customer_id INT NOT NULL,
                    contact_name VARCHAR(150),
                    designation VARCHAR(100),
                    email VARCHAR(150),
                    phone VARCHAR(50),
                    mobile VARCHAR(50),
                    notes TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(customer_id),
                    FOREIGN KEY(customer_id) REFERENCES inventory_customers(id) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_sales_orders (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    order_number VARCHAR(100) UNIQUE,
                    customer_id INT NOT NULL,
                    warehouse_id INT NOT NULL,
                    order_date DATE,
                    delivery_date DATE,
                    order_status ENUM('Draft','Confirmed','Packed','Dispatched','Delivered','Cancelled') DEFAULT 'Draft',
                    payment_status ENUM('Pending','Partial','Paid') DEFAULT 'Pending',
                    subtotal DECIMAL(15,2) DEFAULT 0,
                    discount DECIMAL(15,2) DEFAULT 0,
                    tax DECIMAL(15,2) DEFAULT 0,
                    shipping DECIMAL(15,2) DEFAULT 0,
                    grand_total DECIMAL(15,2) DEFAULT 0,
                    paid_amount DECIMAL(15,2) DEFAULT 0,
                    remaining_balance DECIMAL(15,2) DEFAULT 0,
                    notes TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(customer_id),
                    INDEX(warehouse_id),
                    FOREIGN KEY(customer_id) REFERENCES inventory_customers(id) ON UPDATE CASCADE,
                    FOREIGN KEY(warehouse_id) REFERENCES inventory_warehouses(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_sales_order_items (
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
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(sales_order_id),
                    INDEX(product_id),
                    FOREIGN KEY(sales_order_id) REFERENCES inventory_sales_orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
                    FOREIGN KEY(product_id) REFERENCES inventory_products(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_sales_invoices (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    invoice_no VARCHAR(100) UNIQUE,
                    sales_order_id INT NOT NULL,
                    customer_id INT NOT NULL,
                    warehouse_id INT NOT NULL,
                    account_id INT NULL,
                    invoice_date DATE,
                    due_date DATE,
                    subtotal DECIMAL(15,2) DEFAULT 0,
                    discount DECIMAL(15,2) DEFAULT 0,
                    tax DECIMAL(15,2) DEFAULT 0,
                    shipping DECIMAL(15,2) DEFAULT 0,
                    grand_total DECIMAL(15,2) DEFAULT 0,
                    paid_amount DECIMAL(15,2) DEFAULT 0,
                    remaining_balance DECIMAL(15,2) DEFAULT 0,
                    status ENUM('Draft','Issued','Paid','Partially Paid','Cancelled') DEFAULT 'Draft',
                    notes TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(sales_order_id),
                    INDEX(customer_id),
                    INDEX(warehouse_id),
                    INDEX(account_id),
                    FOREIGN KEY(sales_order_id) REFERENCES inventory_sales_orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
                    FOREIGN KEY(customer_id) REFERENCES inventory_customers(id) ON UPDATE CASCADE,
                    FOREIGN KEY(warehouse_id) REFERENCES inventory_warehouses(id) ON UPDATE CASCADE,
                    FOREIGN KEY(account_id) REFERENCES inventory_accounts(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_sale_invoice_items (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    sales_invoice_id INT NOT NULL,
                    product_id INT NOT NULL,
                    quantity DECIMAL(15,2) DEFAULT 0,
                    unit_price DECIMAL(15,2) DEFAULT 0,
                    discount DECIMAL(15,2) DEFAULT 0,
                    tax DECIMAL(15,2) DEFAULT 0,
                    total DECIMAL(15,2) DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(sales_invoice_id),
                    INDEX(product_id),
                    FOREIGN KEY(sales_invoice_id) REFERENCES inventory_sales_invoices(id) ON DELETE CASCADE ON UPDATE CASCADE,
                    FOREIGN KEY(product_id) REFERENCES inventory_products(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_sale_invoice_payments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    sale_invoice_id INT NOT NULL,
                    paid_amount DECIMAL(15,2) NOT NULL,
                    payment_date DATE NOT NULL,
                    remarks TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    created_by INT,
                    INDEX(sale_invoice_id),
                    FOREIGN KEY(sale_invoice_id) REFERENCES inventory_sales_invoices(id) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_stock_movements (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    movement_no VARCHAR(50) UNIQUE,
                    warehouse_id INT NOT NULL,
                    product_id INT NOT NULL,
                    reference_type ENUM('Purchase','Sale','Transfer In','Transfer Out','Adjustment','Return Purchase','Return Sale','Opening Stock','Stock Audit') NOT NULL,
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
                    FOREIGN KEY(product_id) REFERENCES inventory_products(id) ON UPDATE CASCADE,
                    FOREIGN KEY(warehouse_id) REFERENCES inventory_warehouses(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_stock_adjustments (
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
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_stock_adjustment_items (
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
                    FOREIGN KEY(adjustment_id) REFERENCES inventory_stock_adjustments(id) ON DELETE CASCADE,
                    FOREIGN KEY(product_id) REFERENCES inventory_products(id)
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_stock_transfers (
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
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_stock_transfer_items (
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
                    FOREIGN KEY(transfer_id) REFERENCES inventory_stock_transfers(id) ON DELETE CASCADE,
                    FOREIGN KEY(product_id) REFERENCES inventory_products(id)
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_stock_audits (
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
                    FOREIGN KEY(warehouse_id) REFERENCES inventory_warehouses(id)
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_stock_audit_items (
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
                    FOREIGN KEY(audit_id) REFERENCES inventory_stock_audits(id) ON DELETE CASCADE,
                    FOREIGN KEY(product_id) REFERENCES inventory_products(id)
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_accounts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    parent_id INT NULL,
                    account_code VARCHAR(50) UNIQUE,
                    account_name VARCHAR(200) NOT NULL,
                    account_type ENUM('Asset','Liability','Equity','Income','Expense') NOT NULL,
                    opening_balance DECIMAL(15,2) DEFAULT 0,
                    current_balance DECIMAL(15,2) DEFAULT 0,
                    remarks TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(parent_id),
                    FOREIGN KEY(parent_id) REFERENCES inventory_accounts(id) ON UPDATE CASCADE ON DELETE SET NULL
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_transactions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    transaction_no VARCHAR(100) UNIQUE,
                    transaction_date DATE,
                    reference_type ENUM('Purchase','Sale','Payment','Receipt','Expense','Adjustment'),
                    reference_id INT,
                    account_id INT NOT NULL,
                    transaction_type ENUM('Debit','Credit') NOT NULL,
                    amount DECIMAL(15,2) NOT NULL,
                    remarks TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(account_id),
                    FOREIGN KEY(account_id) REFERENCES inventory_accounts(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_payments (
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
                    FOREIGN KEY(account_id) REFERENCES inventory_accounts(id)
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_notifications (
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
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_logs (
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
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_settings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    setting_key VARCHAR(150) UNIQUE NOT NULL,
                    setting_value LONGTEXT,
                    setting_type VARCHAR(50) DEFAULT 'text',
                    description TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(setting_key)
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_tax_rates (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    tax_name VARCHAR(100) NOT NULL,
                    tax_percentage DECIMAL(5,2) DEFAULT 0,
                    description TEXT,
                    is_default TINYINT DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(is_default),
                    INDEX(is_active)
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_events (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    start_datetime DATETIME NOT NULL,
                    end_datetime DATETIME NULL,
                    location VARCHAR(255),
                    event_color VARCHAR(20) DEFAULT '#3fb50f',
                    event_type VARCHAR(50),
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(start_datetime),
                    INDEX(event_type)
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_email_config (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    smtp_host VARCHAR(255),
                    smtp_port INT DEFAULT 587,
                    smtp_username VARCHAR(255),
                    smtp_password VARCHAR(500),
                    from_address VARCHAR(255),
                    from_name VARCHAR(255),
                    encryption VARCHAR(20) DEFAULT 'tls',
                    is_enabled TINYINT DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS inventory_sms_config (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    api_provider VARCHAR(100),
                    api_key VARCHAR(500),
                    api_secret VARCHAR(500),
                    sender_id VARCHAR(255),
                    is_enabled TINYINT DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS system_contracts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    contract_number VARCHAR(100) UNIQUE NOT NULL,
                    contract_name VARCHAR(255) NOT NULL,
                    contract_type ENUM('monthly','yearly') DEFAULT 'monthly',
                    contractor_name VARCHAR(255),
                    contractor_cnic VARCHAR(50),
                    contractor_phone VARCHAR(50),
                    contractor_email VARCHAR(150),
                    contractor_address TEXT,
                    installation_date DATE,
                    contract_start_date DATE,
                    contract_end_date DATE,
                    monthly_charges DECIMAL(15,2) DEFAULT 0,
                    yearly_charges DECIMAL(15,2) DEFAULT 0,
                    monthly_due_date INT DEFAULT 1,
                    maximum_extension_days INT DEFAULT 15,
                    system_status ENUM('active','inactive','suspended','expired') DEFAULT 'active',
                    contract_description LONGTEXT,
                    policy_description LONGTEXT,
                    contractor_info LONGTEXT,
                    full_description LONGTEXT,
                    attachment_file VARCHAR(500),
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(contract_number),
                    INDEX(system_status)
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS system_invoices (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    invoice_number VARCHAR(100) UNIQUE NOT NULL,
                    contract_id INT NOT NULL,
                    invoice_month VARCHAR(7),
                    invoice_year INT,
                    invoice_date DATE,
                    due_date DATE,
                    extended_due_date DATE,
                    amount DECIMAL(15,2) DEFAULT 0,
                    description TEXT,
                    invoice_status ENUM('draft','sent','pending','partial','paid','overdue','cancelled') DEFAULT 'pending',
                    payment_status ENUM('unpaid','partial','pending_approval','paid') DEFAULT 'unpaid',
                    paid_amount DECIMAL(15,2) DEFAULT 0,
                    remaining_amount DECIMAL(15,2) DEFAULT 0,
                    invoice_file VARCHAR(500),
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(contract_id),
                    INDEX(invoice_number),
                    INDEX(invoice_status),
                    INDEX(payment_status),
                    INDEX(due_date),
                    FOREIGN KEY(contract_id) REFERENCES system_contracts(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS system_payment_proofs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    invoice_id INT NOT NULL,
                    proof_number VARCHAR(100) UNIQUE NOT NULL,
                    proof_date DATE,
                    amount DECIMAL(15,2),
                    payment_method VARCHAR(50),
                    bank_name VARCHAR(150),
                    transaction_id VARCHAR(100),
                    document_file VARCHAR(500),
                    document_name VARCHAR(255),
                    document_type VARCHAR(50),
                    description TEXT,
                    verification_status ENUM('pending','verified','rejected') DEFAULT 'pending',
                    verified_by INT,
                    verified_at DATETIME,
                    rejection_reason TEXT,
                    comments LONGTEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(invoice_id),
                    INDEX(proof_number),
                    INDEX(verification_status),
                    FOREIGN KEY(invoice_id) REFERENCES system_invoices(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;",

                "CREATE TABLE IF NOT EXISTS system_payments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    payment_number VARCHAR(100) UNIQUE NOT NULL,
                    invoice_id INT NOT NULL,
                    payment_date DATE,
                    payment_method VARCHAR(50),
                    amount DECIMAL(15,2),
                    reference_number VARCHAR(100),
                    bank_name VARCHAR(150),
                    transaction_id VARCHAR(100),
                    notes TEXT,
                    payment_status ENUM('pending','completed','failed','cancelled') DEFAULT 'pending',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT,
                    updated_by INT,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(invoice_id),
                    INDEX(payment_number),
                    INDEX(payment_status),
                    FOREIGN KEY(invoice_id) REFERENCES system_invoices(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;"
            ];

            foreach ($tables as $sql) {
                $db->createCommand($sql)->execute();
            }

            // Alter tables for migrations/updates
            try {
                // Add 'pending_approval' to payment_status ENUM if not exists
                $db->createCommand(
                    "ALTER TABLE system_invoices MODIFY payment_status ENUM('unpaid','partial','pending_approval','paid') DEFAULT 'unpaid'"
                )->execute();
            } catch (\Exception $e) {
                // Ignore if column doesn't exist or modification fails
            }

            try {
                // Add 'comments' column to system_payment_proofs if not exists
                $db->createCommand(
                    "ALTER TABLE system_payment_proofs ADD COLUMN comments LONGTEXT AFTER rejection_reason"
                )->execute();
            } catch (\Exception $e) {
                // Ignore if column already exists
            }

            // Update all empty or NULL payment_status values to 'unpaid'
            try {
                $db->createCommand(
                    "UPDATE system_invoices SET payment_status = 'unpaid' WHERE payment_status IS NULL OR payment_status = ''"
                )->execute();
            } catch (\Exception $e) {
                // Ignore if update fails
            }

            // Insert default settings data
            $this->insertDefaultSettings($db);

            // Insert default tax rates
            $this->insertDefaultTaxRates($db);

            // Insert Super Admin role
            $this->insertSuperAdminRole($db);

            // Insert default system plan
            $this->insertDefaultSystemPlan($db);

            $transaction->commit();
            echo json_encode(['success' => true, 'message' => 'All inventory tables created successfully with default data.']);
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    private function insertDefaultSettings($db)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $adminId = 1;

            $settings = [
                ['setting_key' => 'app_name', 'setting_value' => 'Inventory System', 'setting_type' => 'text', 'description' => 'Application name'],
                ['setting_key' => 'app_version', 'setting_value' => '1.0.0', 'setting_type' => 'text', 'description' => 'Application version'],
                ['setting_key' => 'support_email', 'setting_value' => 'support@example.com', 'setting_type' => 'email', 'description' => 'Support email address'],
                ['setting_key' => 'support_phone', 'setting_value' => '+1-555-0000', 'setting_type' => 'text', 'description' => 'Support phone number'],
                ['setting_key' => 'company_name', 'setting_value' => 'Your Company Name', 'setting_type' => 'text', 'description' => 'Company legal name'],
                ['setting_key' => 'company_address', 'setting_value' => '123 Business Street, City, Country', 'setting_type' => 'text', 'description' => 'Company address'],
                ['setting_key' => 'company_phone', 'setting_value' => '+1-555-0000', 'setting_type' => 'text', 'description' => 'Company phone'],
                ['setting_key' => 'company_email', 'setting_value' => 'info@example.com', 'setting_type' => 'email', 'description' => 'Company email'],
                ['setting_key' => 'company_website', 'setting_value' => 'https://example.com', 'setting_type' => 'text', 'description' => 'Company website'],
                ['setting_key' => 'tax_number', 'setting_value' => 'TAX123456789', 'setting_type' => 'text', 'description' => 'Tax ID or VAT number'],
                ['setting_key' => 'currency', 'setting_value' => 'USD', 'setting_type' => 'text', 'description' => 'Default currency'],
                ['setting_key' => 'currency_symbol', 'setting_value' => '$', 'setting_type' => 'text', 'description' => 'Currency symbol'],
                ['setting_key' => 'fiscal_year_start', 'setting_value' => '01', 'setting_type' => 'text', 'description' => 'Fiscal year start month'],
                ['setting_key' => 'company_logo', 'setting_value' => '', 'setting_type' => 'text', 'description' => 'Company logo path'],
                ['setting_key' => 'theme', 'setting_value' => 'light', 'setting_type' => 'text', 'description' => 'UI theme (light/dark/auto)'],
                ['setting_key' => 'items_per_page', 'setting_value' => '25', 'setting_type' => 'number', 'description' => 'Default pagination size'],
                ['setting_key' => 'enable_sidebar', 'setting_value' => '1', 'setting_type' => 'boolean', 'description' => 'Show sidebar by default'],
                ['setting_key' => 'show_tooltips', 'setting_value' => '1', 'setting_type' => 'boolean', 'description' => 'Show help tooltips'],
                ['setting_key' => 'language', 'setting_value' => 'en', 'setting_type' => 'text', 'description' => 'Default language'],
                ['setting_key' => 'timezone', 'setting_value' => 'UTC', 'setting_type' => 'text', 'description' => 'System timezone'],
                ['setting_key' => 'date_format', 'setting_value' => 'Y-m-d', 'setting_type' => 'text', 'description' => 'Date format'],
                ['setting_key' => 'time_format', 'setting_value' => 'H:i:s', 'setting_type' => 'text', 'description' => 'Time format'],
                ['setting_key' => 'maintenance_mode', 'setting_value' => '0', 'setting_type' => 'boolean', 'description' => 'Enable maintenance mode'],
                ['setting_key' => 'debug_mode', 'setting_value' => '0', 'setting_type' => 'boolean', 'description' => 'Enable debug mode'],
                ['setting_key' => 'enable_audit_log', 'setting_value' => '1', 'setting_type' => 'boolean', 'description' => 'Enable audit logging'],
                ['setting_key' => 'email_smtp_host', 'setting_value' => '', 'setting_type' => 'text', 'description' => 'SMTP server host'],
                ['setting_key' => 'email_smtp_port', 'setting_value' => '587', 'setting_type' => 'number', 'description' => 'SMTP server port'],
                ['setting_key' => 'email_smtp_username', 'setting_value' => '', 'setting_type' => 'text', 'description' => 'SMTP username'],
                ['setting_key' => 'email_smtp_password', 'setting_value' => '', 'setting_type' => 'text', 'description' => 'SMTP password'],
                ['setting_key' => 'email_from_address', 'setting_value' => '', 'setting_type' => 'email', 'description' => 'Email from address'],
                ['setting_key' => 'email_from_name', 'setting_value' => 'Inventory System', 'setting_type' => 'text', 'description' => 'Email from name'],
                ['setting_key' => 'email_encryption', 'setting_value' => 'tls', 'setting_type' => 'text', 'description' => 'Email encryption (tls/ssl/none)'],
                ['setting_key' => 'email_smtp_enabled', 'setting_value' => '0', 'setting_type' => 'boolean', 'description' => 'Enable SMTP email'],
                ['setting_key' => 'sms_api_provider', 'setting_value' => '', 'setting_type' => 'text', 'description' => 'SMS API provider'],
                ['setting_key' => 'sms_api_key', 'setting_value' => '', 'setting_type' => 'text', 'description' => 'SMS API key'],
                ['setting_key' => 'sms_api_secret', 'setting_value' => '', 'setting_type' => 'text', 'description' => 'SMS API secret'],
                ['setting_key' => 'sms_sender_id', 'setting_value' => 'Inventory', 'setting_type' => 'text', 'description' => 'SMS sender ID'],
                ['setting_key' => 'sms_enabled', 'setting_value' => '0', 'setting_type' => 'boolean', 'description' => 'Enable SMS notifications'],
            ];

            foreach ($settings as $setting) {
                $existing = $db->createCommand(
                    "SELECT id FROM inventory_settings WHERE setting_key = :key"
                )->bindValue(':key', $setting['setting_key'])->queryScalar();

                if (!$existing) {
                    $db->createCommand()->insert('inventory_settings', [
                        'setting_key' => $setting['setting_key'],
                        'setting_value' => $setting['setting_value'],
                        'setting_type' => $setting['setting_type'],
                        'description' => $setting['description'],
                        'created_at' => $now,
                        'updated_at' => $now,
                        'created_by' => $adminId,
                        'updated_by' => $adminId,
                        'is_deleted' => 0
                    ])->execute();
                }
            }
        } catch (\Exception $e) {
            // Silently fail - settings may not exist yet
        }
    }

    private function insertDefaultTaxRates($db)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $adminId = 1;

            $taxRates = [
                ['tax_name' => 'No Tax', 'tax_percentage' => 0.00, 'is_default' => 1],
                ['tax_name' => 'Standard VAT', 'tax_percentage' => 15.00, 'is_default' => 0],
                ['tax_name' => 'Reduced VAT', 'tax_percentage' => 7.00, 'is_default' => 0],
                ['tax_name' => 'Sales Tax', 'tax_percentage' => 8.00, 'is_default' => 0],
                ['tax_name' => 'Service Tax', 'tax_percentage' => 5.00, 'is_default' => 0],
                ['tax_name' => 'GST', 'tax_percentage' => 10.00, 'is_default' => 0],
            ];

            foreach ($taxRates as $tax) {
                $existing = $db->createCommand(
                    "SELECT id FROM inventory_tax_rates WHERE tax_name = :name"
                )->bindValue(':name', $tax['tax_name'])->queryScalar();

                if (!$existing) {
                    if ($tax['is_default'] == 1) {
                        // Unset all other defaults
                        $db->createCommand()->update('inventory_tax_rates', ['is_default' => 0])->execute();
                    }

                    $db->createCommand()->insert('inventory_tax_rates', [
                        'tax_name' => $tax['tax_name'],
                        'tax_percentage' => $tax['tax_percentage'],
                        'is_default' => $tax['is_default'],
                        'created_at' => $now,
                        'updated_at' => $now,
                        'created_by' => $adminId,
                        'updated_by' => $adminId,
                        'is_active' => 1,
                        'is_deleted' => 0
                    ])->execute();
                }
            }
        } catch (\Exception $e) {
            // Silently fail - tax rates may not exist yet
        }
    }

    private function insertSuperAdminRole($db)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $adminId = 1;

            // Check if Super Admin role exists
            $existing = $db->createCommand(
                "SELECT id FROM rolesWHERE role_name = 'Super Admin'"
            )->queryScalar();

            if (!$existing) {
                $db->createCommand()->insert('system_roles', [
                    'role_name' => 'Super Admin',
                    'role_description' => 'Super Administrator with full system access and payment management',
                    'created_at' => $now,
                    'updated_at' => $now,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                    'is_active' => 1,
                    'is_deleted' => 0
                ])->execute();

                $superAdminRoleId = $db->getLastInsertID();

                // Get all modules
                $modules = $db->createCommand("SELECT id FROM modules WHERE is_deleted = 0")->queryAll();

                // Grant all permissions to Super Admin
                foreach ($modules as $module) {
                    $moduleId = $module['id'];

                    // Check if permission already exists
                    $existingPerm = $db->createCommand(
                        "SELECT id FROM permissions WHERE role_id = :role_id AND module_id = :module_id"
                    )->bindValues([':role_id' => $superAdminRoleId, ':module_id' => $moduleId])->queryScalar();

                    if (!$existingPerm) {
                        $db->createCommand()->insert('permissions', [
                            'role_id' => $superAdminRoleId,
                            'module_id' => $moduleId,
                            'can_view' => 1,
                            'can_add' => 1,
                            'can_edit' => 1,
                            'can_delete' => 1,
                            'created_at' => $now,
                            'updated_at' => $now,
                            'created_by' => $adminId,
                            'updated_by' => $adminId,
                            'is_deleted' => 0
                        ])->execute();
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fail - role may not exist yet
        }
    }

    private function checkPaymentStatus($userId, $roleId = null)
    {
        $db = Yii::$app->db;
        $today = date('Y-m-d');

        // Check if user is Super Admin
        $isSuperAdmin = $db->createCommand(
            "SELECT COUNT(*) FROM roles sr
             JOIN system_users su ON su.role_id = sr.id
             WHERE sr.name = 'Super Admin' AND su.id = :user_id"
        )->bindValue(':user_id', $userId)->queryScalar() > 0;

        // Get active contract
        $contract = $db->createCommand(
            "SELECT * FROM system_contracts WHERE system_status = 'active' AND is_deleted = 0 ORDER BY created_at DESC LIMIT 1"
        )->queryOne();

        if (!$contract) {
            return ['status' => 'ok', 'is_super_admin' => $isSuperAdmin];
        }

        // Generate monthly invoices if they don't exist (for non-Super Admin users)
        if (!$isSuperAdmin && $contract['contract_type'] === 'monthly') {
            $this->generateMonthlyInvoiceIfNeeded($contract);
        }

        // Get pending/overdue invoices
        $pendingInvoices = $db->createCommand(
            "SELECT * FROM system_invoices
             WHERE contract_id = :contract_id AND payment_status != 'paid' AND is_deleted = 0
             ORDER BY due_date ASC"
        )->bindValue(':contract_id', $contract['id'])->queryAll();

        if (empty($pendingInvoices)) {
            return ['status' => 'ok', 'is_super_admin' => $isSuperAdmin];
        }

        $isOverdue = false;
        $pendingInfo = [];

        foreach ($pendingInvoices as $invoice) {
            // $extendedDate = strtotime($invoice['extended_due_date']);
            $extendedDate = date('Y-m-d', strtotime($invoice['due_date'] . ' +5 days'));
            if (strtotime($today) > $extendedDate) {
                $isOverdue = true;
                break;
            }

            $pendingInfo[] = [
                'invoice_id' => $invoice['id'],
                'invoice_number' => $invoice['invoice_number'],
                'invoice_month' => $invoice['invoice_month'],
                'amount' => $invoice['amount'],
                'due_date' => $invoice['due_date'],
                'extended_due_date' => $invoice['extended_due_date'],
                'remaining_amount' => $invoice['remaining_amount'],
                'days_remaining' => ceil((strtotime($invoice['extended_due_date']) - strtotime($today)) / 86400)
            ];
        }

        $status = $isOverdue ? 'restricted' : 'warning';

        return [
            'status' => $status,
            'is_super_admin' => $isSuperAdmin,
            'pending_info' => $pendingInfo,
            'contract_id' => $contract['id'],
            'is_overdue' => $isOverdue
        ];
    }

    private function generateMonthlyInvoiceIfNeeded($contract)
    {
        $db = Yii::$app->db;
        $currentYear = date('Y');
        $currentMonth = date('m');
        $currentMonthStr = date('Y-m');

        // Check if invoice for current month already exists
        $existingInvoice = $db->createCommand(
            "SELECT id FROM system_invoices
             WHERE contract_id = :contract_id
             AND invoice_month = :invoice_month
             AND invoice_year = :invoice_year
             AND is_deleted = 0"
        )
            ->bindValue(':contract_id', $contract['id'])
            ->bindValue(':invoice_month', $currentMonthStr)
            ->bindValue(':invoice_year', $currentYear)
            ->queryScalar();

        if ($existingInvoice) {
            return; // Invoice already exists for this month
        }

        // Generate new invoice
        $invoiceNumber = 'INV-' . date('YmdHis') . '-' . random_int(1000, 9999);
        $invoiceDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('first day of next month', strtotime($invoiceDate)));
        $extendedDueDate = date('Y-m-d', strtotime('+' . $contract['maximum_extension_days'] . ' days', strtotime($dueDate)));

        $db->createCommand(
            "INSERT INTO system_invoices
            (contract_id, invoice_number, invoice_month, invoice_year, invoice_date, due_date, extended_due_date,
             amount, invoice_status, payment_status, created_by, created_at)
            VALUES
            (:contract_id, :invoice_number, :invoice_month, :invoice_year, :invoice_date, :due_date, :extended_due_date,
             :amount, 'sent', 'unpaid', 1, NOW())"
        )
            ->bindValue(':contract_id', $contract['id'])
            ->bindValue(':invoice_number', $invoiceNumber)
            ->bindValue(':invoice_month', $currentMonthStr)
            ->bindValue(':invoice_year', $currentYear)
            ->bindValue(':invoice_date', $invoiceDate)
            ->bindValue(':due_date', $dueDate)
            ->bindValue(':extended_due_date', $extendedDueDate)
            ->bindValue(':amount', $contract['monthly_charges'])
            ->execute();
    }

    private function insertDefaultSystemPlan($db)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $adminId = 1;

            // Check if default contract exists
            $existing = $db->createCommand(
                "SELECT id FROM system_contracts WHERE contract_number = 'DEFAULT-001'"
            )->queryScalar();

            if (!$existing) {
                $contractDescription = "This is the default system contract for the inventory management system.\n\n";
                $contractDescription .= "Contract Type: Monthly Subscription\n";
                $contractDescription .= "Monthly Charges: Applicable as per the agreement.\n";
                $contractDescription .= "Payment Terms: Due on the specified date each month.\n";
                $contractDescription .= "Extension Policy: " . "15 days grace period allowed beyond due date.\n";

                $policyDescription = "PAYMENT POLICY\n\n";
                $policyDescription .= "1. Payment Terms: Monthly subscription charges are due on or before the specified due date.\n";
                $policyDescription .= "2. Payment Methods: Bank transfer, online payment, or check.\n";
                $policyDescription .= "3. Grace Period: A maximum of 15 days extension is provided beyond the due date.\n";
                $policyDescription .= "4. Service Suspension: If payment is not received within the extended period, system access will be restricted to Super Admin only.\n";
                $policyDescription .= "5. Proof of Payment: Upload proof of payment through the system for verification.\n";
                $policyDescription .= "6. Invoice Generation: Monthly invoices are automatically generated and can be downloaded from the system.\n";
                $policyDescription .= "7. Refund Policy: No refunds for partial months. Cancellation requires 30 days notice.\n";

                $contractorInfo = "System Administrator\n";
                $contractorInfo .= "Email: admin@example.com\n";
                $contractorInfo .= "Phone: +1-555-0000\n";
                $contractorInfo .= "Address: Your Company Address\n";

                $db->createCommand()->insert('system_contracts', [
                    'contract_number' => 'DEFAULT-001',
                    'contract_name' => 'Default System Contract',
                    'contract_type' => 'monthly',
                    'contractor_name' => 'Company Administrator',
                    'contractor_cnic' => '00000-0000000-0',
                    'contractor_phone' => '+1-555-0000',
                    'contractor_email' => 'admin@example.com',
                    'contractor_address' => 'Your Company Address',
                    'installation_date' => date('Y-m-d'),
                    'contract_start_date' => date('Y-m-d'),
                    'monthly_charges' => 0,
                    'yearly_charges' => 0,
                    'monthly_due_date' => 1,
                    'maximum_extension_days' => 15,
                    'system_status' => 'active',
                    'contract_description' => $contractDescription,
                    'policy_description' => $policyDescription,
                    'contractor_info' => $contractorInfo,
                    'full_description' => "This is the system contract governing the use of the inventory management system. All users must comply with the payment terms and conditions outlined herein.",
                    'created_at' => $now,
                    'updated_at' => $now,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                    'is_active' => 1,
                    'is_deleted' => 0
                ])->execute();
            }
        } catch (\Exception $e) {
            // Silently fail - contracts table may not exist yet
        }
    }
}

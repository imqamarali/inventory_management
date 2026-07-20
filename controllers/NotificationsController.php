<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class NotificationsController extends Controller
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

    public function actionNotifications()
    {
        $modules = [
            ['name' => 'Notification Center', 'controller' => 'notifications/notificationcenter', 'icon' => 'fa fa-bell'],
            ['name' => 'Low Stock Alerts', 'controller' => 'notifications/lowstockalerts', 'icon' => 'fa fa-warning'],
            ['name' => 'Out of Stock Alerts', 'controller' => 'notifications/outofstockalerts', 'icon' => 'fa fa-times-circle'],
            ['name' => 'Pending Purchase Alerts', 'controller' => 'notifications/pendingpurchasealerts', 'icon' => 'fa fa-shopping-cart'],
            ['name' => 'Pending Sales Alerts', 'controller' => 'notifications/pendingsalesalerts', 'icon' => 'fa fa-shopping-bag'],
            ['name' => 'Payment Due Alerts', 'controller' => 'notifications/paymentduealerts', 'icon' => 'fa fa-money'],
            ['name' => 'Supplier Notifications', 'controller' => 'notifications/suppliernotifications', 'icon' => 'fa fa-truck'],
            ['name' => 'Customer Notifications', 'controller' => 'notifications/customernotifications', 'icon' => 'fa fa-users'],
        ];
        return $this->render('notifications', compact('modules'));
    }

    /* -------------------------------------------------------------
     * Notification Center
     * ----------------------------------------------------------- */
    public function actionNotificationcenter()
    {
        if (Yii::$app->request->isGet) {
            $user_id = $this->currentUserId();
            $perPage = (int)Yii::$app->request->get('per_page', 20);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;

            $where = " WHERE is_deleted=0 AND (user_id=:user_id OR user_id IS NULL) ";
            $params = [':user_id' => $user_id];

            $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_notifications $where", $params)->queryScalar();
            $unread = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_notifications $where AND is_read=0", $params)->queryScalar();
            $notifications = Yii::$app->db->createCommand("
                SELECT * FROM inventory_notifications $where ORDER BY created_at DESC LIMIT $offset,$perPage
            ", $params)->queryAll();

            return $this->renderPartial('notificationcenter', [
                'notifications' => $notifications,
                'total' => $total,
                'unread' => $unread,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => ceil($total / $perPage)
            ]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            $user_id = $this->currentUserId();

            if (isset($post['flag']) && $post['flag'] == 'search') {
                $type = trim($post['notification_type'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;
                $where = " WHERE is_deleted=0 AND (user_id=:user_id OR user_id IS NULL) ";
                $params = [':user_id' => $user_id];
                if ($type != '') {
                    $where .= " AND notification_type=:type";
                    $params[':type'] = $type;
                }
                $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_notifications $where", $params)->queryScalar();
                $notifications = Yii::$app->db->createCommand("SELECT * FROM inventory_notifications $where ORDER BY created_at DESC LIMIT $offset,$perPage", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['notifications' => $notifications, 'total' => (int)$total, 'page' => $page, 'per_page' => $perPage, 'total_pages' => ceil($total / $perPage)]);
            }

            if (isset($post['flag']) && $post['flag'] == 'markread') {
                Yii::$app->db->createCommand()->update('inventory_notifications',
                    ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
                    ['id' => $post['id']])->execute();
                return $this->jsonResponse(true, 'Notification marked as read.');
            }

            if (isset($post['flag']) && $post['flag'] == 'markallread') {
                Yii::$app->db->createCommand()->update('inventory_notifications',
                    ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
                    ['and', ['is_deleted' => 0], ['or', ['user_id' => $user_id], ['user_id' => null]]])->execute();
                return $this->jsonResponse(true, 'All notifications marked as read.');
            }

            $id = $post['id'] ?? null;
            if ($id && isset($post['delete']) && $post['delete'] == 1) {
                Yii::$app->db->createCommand()->update('inventory_notifications', ['is_deleted' => 1], ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Notification deleted successfully.');
            }

            if (empty($post['title']) || empty($post['message'])) {
                return $this->jsonResponse(false, 'Title and message are required.');
            }

            Yii::$app->db->createCommand()->insert('inventory_notifications', [
                'user_id' => !empty($post['user_id']) ? $post['user_id'] : null,
                'title' => $post['title'],
                'message' => $post['message'],
                'notification_type' => $post['notification_type'] ?? 'Info',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'is_active' => 1,
                'is_deleted' => 0,
            ])->execute();

            return $this->jsonResponse(true, 'Notification created successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Low Stock Alerts
     * ----------------------------------------------------------- */
    public function actionLowstockalerts()
    {
        if (Yii::$app->request->isGet) {
            $warehouse_id = Yii::$app->request->get('warehouse_id', '');
            $items = $this->buildStockAlerts($warehouse_id, 'low');
            $warehouses = Yii::$app->db->createCommand("SELECT id,warehouse_name FROM inventory_warehouses WHERE is_deleted=0 AND is_active=1 ORDER BY warehouse_name")->queryAll();
            return $this->renderPartial('lowstockalerts', ['items' => $items, 'warehouses' => $warehouses, 'warehouse_id' => $warehouse_id]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $items = $this->buildStockAlerts($post['warehouse_id'] ?? '', 'low');
                return $this->jsonResponse(true, 'Data loaded successfully!', ['items' => $items]);
            }
            if (isset($post['flag']) && $post['flag'] == 'notify') {
                return $this->pushStockNotification($post, 'low stock');
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Out of Stock Alerts
     * ----------------------------------------------------------- */
    public function actionOutofstockalerts()
    {
        if (Yii::$app->request->isGet) {
            $warehouse_id = Yii::$app->request->get('warehouse_id', '');
            $items = $this->buildStockAlerts($warehouse_id, 'out');
            $warehouses = Yii::$app->db->createCommand("SELECT id,warehouse_name FROM inventory_warehouses WHERE is_deleted=0 AND is_active=1 ORDER BY warehouse_name")->queryAll();
            return $this->renderPartial('outofstockalerts', ['items' => $items, 'warehouses' => $warehouses, 'warehouse_id' => $warehouse_id]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $items = $this->buildStockAlerts($post['warehouse_id'] ?? '', 'out');
                return $this->jsonResponse(true, 'Data loaded successfully!', ['items' => $items]);
            }
            if (isset($post['flag']) && $post['flag'] == 'notify') {
                return $this->pushStockNotification($post, 'out of stock');
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function buildStockAlerts($warehouse_id, $mode)
    {
        $where = " WHERE s.is_deleted=0 ";
        $params = [];
        if (!empty($warehouse_id)) {
            $where .= " AND s.warehouse_id=:warehouse_id";
            $params[':warehouse_id'] = $warehouse_id;
        }
        $where .= $mode == 'out' ? " AND s.quantity<=0" : " AND s.quantity<=p.reorder_level AND s.quantity>0";

        return Yii::$app->db->createCommand("
            SELECT
                s.id, s.quantity, s.available_quantity,
                p.product_name, p.sku, p.reorder_level, p.minimum_stock,
                w.warehouse_name
            FROM inventory_stock s
            INNER JOIN inventory_products p ON p.id=s.product_id
            INNER JOIN inventory_warehouses w ON w.id=s.warehouse_id
            $where
            ORDER BY s.quantity ASC
        ", $params)->queryAll();
    }

    private function pushStockNotification($post, $label)
    {
        $product = $post['product_name'] ?? 'Item';
        Yii::$app->db->createCommand()->insert('inventory_notifications', [
            'user_id' => null,
            'title' => ucfirst($label) . ' Alert',
            'message' => "$product is running $label.",
            'notification_type' => 'Warning',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'is_active' => 1,
            'is_deleted' => 0,
        ])->execute();
        return $this->jsonResponse(true, 'Notification sent successfully.');
    }

    /* -------------------------------------------------------------
     * Pending Purchase Alerts
     * ----------------------------------------------------------- */
    public function actionPendingpurchasealerts()
    {
        if (Yii::$app->request->isGet) {
            $orders = Yii::$app->db->createCommand("
                SELECT po.*, s.company_name,
                    DATEDIFF(CURDATE(),po.expected_date) days_overdue
                FROM inventory_purchase_orders po
                LEFT JOIN inventory_suppliers s ON s.id=po.supplier_id
                WHERE po.is_deleted=0 AND po.status IN ('Draft','Approved','Partially Received')
                ORDER BY po.expected_date ASC
            ")->queryAll();
            return $this->renderPartial('pendingpurchasealerts', ['orders' => $orders]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $orders = Yii::$app->db->createCommand("
                    SELECT po.*, s.company_name,
                        DATEDIFF(CURDATE(),po.expected_date) days_overdue
                    FROM inventory_purchase_orders po
                    LEFT JOIN inventory_suppliers s ON s.id=po.supplier_id
                    WHERE po.is_deleted=0 AND po.status IN ('Draft','Approved','Partially Received')
                    ORDER BY po.expected_date ASC
                ")->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['orders' => $orders]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Pending Sales Alerts
     * ----------------------------------------------------------- */
    public function actionPendingsalesalerts()
    {
        if (Yii::$app->request->isGet) {
            $orders = Yii::$app->db->createCommand("
                SELECT so.*, c.company_name, c.first_name, c.last_name,
                    DATEDIFF(CURDATE(),so.delivery_date) days_overdue
                FROM inventory_sales_orders so
                LEFT JOIN inventory_customers c ON c.id=so.customer_id
                WHERE so.is_deleted=0 AND so.order_status IN ('Draft','Confirmed','Packed','Dispatched')
                ORDER BY so.delivery_date ASC
            ")->queryAll();
            return $this->renderPartial('pendingsalesalerts', ['orders' => $orders]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $orders = Yii::$app->db->createCommand("
                    SELECT so.*, c.company_name, c.first_name, c.last_name,
                        DATEDIFF(CURDATE(),so.delivery_date) days_overdue
                    FROM inventory_sales_orders so
                    LEFT JOIN inventory_customers c ON c.id=so.customer_id
                    WHERE so.is_deleted=0 AND so.order_status IN ('Draft','Confirmed','Packed','Dispatched')
                    ORDER BY so.delivery_date ASC
                ")->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['orders' => $orders]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Payment Due Alerts
     * ----------------------------------------------------------- */
    public function actionPaymentduealerts()
    {
        if (Yii::$app->request->isGet) {
            $customers = Yii::$app->db->createCommand("
                SELECT id, customer_code, COALESCE(company_name,CONCAT(first_name,' ',last_name)) name, credit_limit, current_balance
                FROM inventory_customers
                WHERE is_deleted=0 AND current_balance>0
                ORDER BY current_balance DESC
            ")->queryAll();
            $suppliers = Yii::$app->db->createCommand("
                SELECT id, supplier_code, company_name name, credit_limit, current_balance
                FROM inventory_suppliers
                WHERE is_deleted=0 AND current_balance>0
                ORDER BY current_balance DESC
            ")->queryAll();
            return $this->renderPartial('paymentduealerts', ['customers' => $customers, 'suppliers' => $suppliers]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $customers = Yii::$app->db->createCommand("
                    SELECT id, customer_code, COALESCE(company_name,CONCAT(first_name,' ',last_name)) name, credit_limit, current_balance
                    FROM inventory_customers WHERE is_deleted=0 AND current_balance>0 ORDER BY current_balance DESC
                ")->queryAll();
                $suppliers = Yii::$app->db->createCommand("
                    SELECT id, supplier_code, company_name name, credit_limit, current_balance
                    FROM inventory_suppliers WHERE is_deleted=0 AND current_balance>0 ORDER BY current_balance DESC
                ")->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['customers' => $customers, 'suppliers' => $suppliers]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Supplier Notifications
     * ----------------------------------------------------------- */
    public function actionSuppliernotifications()
    {
        if (Yii::$app->request->isGet) {
            $documents = Yii::$app->db->createCommand("
                SELECT d.*, s.company_name
                FROM inventory_supplier_documents d
                INNER JOIN inventory_suppliers s ON s.id=d.supplier_id
                WHERE d.is_deleted=0 AND d.expiry_date IS NOT NULL
                AND d.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                ORDER BY d.expiry_date ASC
            ")->queryAll();
            return $this->renderPartial('suppliernotifications', ['documents' => $documents]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $documents = Yii::$app->db->createCommand("
                    SELECT d.*, s.company_name
                    FROM inventory_supplier_documents d
                    INNER JOIN inventory_suppliers s ON s.id=d.supplier_id
                    WHERE d.is_deleted=0 AND d.expiry_date IS NOT NULL
                    AND d.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                    ORDER BY d.expiry_date ASC
                ")->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['documents' => $documents]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Customer Notifications
     * ----------------------------------------------------------- */
    public function actionCustomernotifications()
    {
        if (Yii::$app->request->isGet) {
            $customers = Yii::$app->db->createCommand("
                SELECT id, customer_code, COALESCE(company_name,CONCAT(first_name,' ',last_name)) name, credit_limit, current_balance
                FROM inventory_customers
                WHERE is_deleted=0 AND current_balance > credit_limit AND credit_limit > 0
                ORDER BY current_balance DESC
            ")->queryAll();
            return $this->renderPartial('customernotifications', ['customers' => $customers]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $customers = Yii::$app->db->createCommand("
                    SELECT id, customer_code, COALESCE(company_name,CONCAT(first_name,' ',last_name)) name, credit_limit, current_balance
                    FROM inventory_customers
                    WHERE is_deleted=0 AND current_balance > credit_limit AND credit_limit > 0
                    ORDER BY current_balance DESC
                ")->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['customers' => $customers]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function actionInjectdb()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
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

            $transaction->commit();

            echo "Notifications tables created successfully.";
            exit;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo "Error: " . $e->getMessage();
            exit;
        }
    }
}
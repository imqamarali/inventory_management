<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class InventoryController extends Controller
{  

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

    
    private function checkPermission($actionType = 'view')
    {
        $user_array = Yii::$app->session->get('user_array');
        $role_id = $user_array['role_id'] ?? null;

        if (!$role_id) {
            return false;
        }

        $moduleId = Yii::$app->db->createCommand(
            "SELECT id FROM modules WHERE LOWER(name) = 'inventory' LIMIT 1"
        )->queryScalar();

        if (!$moduleId) {
            return false;
        }

        // Get user's permissions for Inventory module
        $permissions = Yii::$app->db->createCommand(
            "SELECT * FROM permissions
             WHERE module_id = :module_id
             AND role_id = :role_id
             LIMIT 1"
        )
            ->bindValue(':module_id', $moduleId)
            ->bindValue(':role_id', $role_id)
            ->queryOne();

        if (!$permissions) {
            return false;
        }

        // Check specific permission
        $permissionMap = [
            'view' => 'can_view',
            'add' => 'can_add',
            'create' => 'can_add',
            'edit' => 'can_edit',
            'update' => 'can_edit',
            'delete' => 'can_delete'
        ];

        $permissionField = $permissionMap[$actionType] ?? 'can_view';

        return (bool)$permissions[$permissionField];
    }

    private function requirePermission($actionType = 'view')
    {
        if (!$this->checkPermission($actionType)) {
            Yii::$app->response->statusCode = 403;
            echo json_encode(['error' => 'You do not have permission to ' . $actionType . ' this resource']);
            Yii::$app->end();
            return false;
        }
        return true;
    }

    private function checkModulePermission($moduleLink = 'inventory/dashboard')
    {
        $user_array = Yii::$app->session->get('user_array');
        $role_id = $user_array['role_id'] ?? null;

        if (!$role_id) {
            return false;
        }

        // Get module ID by link
        $moduleId = Yii::$app->db->createCommand(
            "SELECT id FROM modules WHERE link = :link LIMIT 1"
        )->bindValue(':link', $moduleLink)->queryScalar();

        if (!$moduleId) {
            return false;
        }
        
        $permissions = Yii::$app->db->createCommand(
            "SELECT can_view FROM permissions
             WHERE module_id = :module_id
             AND role_id = :role_id
             LIMIT 1"
        )
            ->bindValue(':module_id', $moduleId)
            ->bindValue(':role_id', $role_id)
            ->queryOne();

        return $permissions && (bool)$permissions['can_view'];
    }

    private function requireModulePermission($moduleLink = 'inventory/dashboard')
    {
        $status = $this->checkModulePermission($moduleLink);
        if (!$status) {
            Yii::$app->session->setFlash('warning', 'You do not have permission to access this module.');
            Yii::$app->response->statusCode = 403;
            $this->redirect(['inventory/dashboard']);
            Yii::$app->end();
        }
    }


    public function actionDashboard()
    {

        $this->requireModulePermission('inventory/dashboard');

        Yii::$app->Component->Activitylog('Viewed inventory dashboard', 'view', null, 'Inventory', ['module' => 'dashboard']);
        $user_array = Yii::$app->session->get('user_array');
        $user_id = $user_array['id'] ?? null;
        $student_data = null;
        $stats = $this->getDashboardStats();
        $unpaidInvoices = [];

        // Check for unpaid invoices for non-Super Admin users
        $isSuperAdmin = false;
        if ($user_array && isset($user_array['role_id'])) {
            $roleName = Yii::$app->db->createCommand(
                "SELECT name FROM roles WHERE id = :role_id LIMIT 1"
            )->bindValue(':role_id', $user_array['role_id'])->queryScalar();

            $isSuperAdmin = ($roleName === 'Super Admin');
        }

        if (!$isSuperAdmin) {
            try {
                $unpaidInvoices = Yii::$app->db->createCommand(
                    "SELECT si.id, si.invoice_number, si.amount, si.due_date, si.invoice_date
                     FROM system_invoices si
                     WHERE si.payment_status = 'unpaid'
                     AND si.is_deleted = 0
                     ORDER BY si.due_date ASC"
                )->queryAll();
            } catch (\Exception $e) {
                \Yii::error("Error fetching unpaid invoices: " . $e->getMessage());
            }
        }

        if ($user_id) {
            try {
                $student_data = Yii::$app->db->createCommand(
                    "SELECT s.*
                     FROM students s
                     LEFT JOIN school sch ON s.school_id = sch.school_id
                     WHERE s.student_id = (
                         SELECT referance FROM system_users WHERE id = :user_id
                     ) AND s.school_id = :school_id"
                )->bindValues([
                    ':user_id' => $user_id,
                    ':school_id' => $this->school_id
                ])->queryOne();
            } catch (\Exception $e) {
            }
        }
        return $this->render('dashboard', ['student_data' => $student_data, 'stats' => $stats, 'unpaidInvoices' => $unpaidInvoices]);
    }

    private function getDashboardStats()
    {
        $stats = [
            'warehouses' => 0,
            'total_products' => 0,
            'current_stock' => 0,
            'low_stock_items' => 0,
            'pending_purchase_orders' => 0,
            'pending_sales_orders' => 0,
            'today_sales' => 0,
            'today_purchases' => 0,
            'customers' => 0,
            'suppliers' => 0,
            'total_revenue' => 0,
            'total_purchases_value' => 0,
            'pending_returns' => 0,
            'total_assets' => 0,
            'total_liabilities' => 0,
            'total_equity' => 0,
        ];

        try {
            $db = Yii::$app->db;

            // Warehouses
            try {
                $stats['warehouses'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_warehouses WHERE is_deleted = 0 AND is_active = 1")->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Warehouse query failed: " . $e->getMessage());
            }

            // Total Products
            try {
                $stats['total_products'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_products WHERE is_deleted = 0")->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Total products query failed: " . $e->getMessage());
            }

            // Current Stock
            try {
                $result = $db->createCommand("SELECT IFNULL(SUM(quantity), 0) FROM inventory_stock WHERE is_deleted = 0")->queryScalar();
                $stats['current_stock'] = (int)$result;
            } catch (\Exception $e) {
                \Yii::warning("Current stock query failed: " . $e->getMessage());
            }

            // Low Stock Items
            try {
                $stats['low_stock_items'] = (int)$db->createCommand(
                    "SELECT COUNT(*) FROM inventory_stock s
                     WHERE s.is_deleted = 0
                     AND s.quantity <= (SELECT reorder_level FROM inventory_products p WHERE p.id = s.product_id)"
                )->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Low stock query failed: " . $e->getMessage());
            }

            // Pending Purchase Orders
            try {
                $stats['pending_purchase_orders'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_purchase_orders WHERE is_deleted = 0 AND status NOT IN ('completed', 'cancelled')")->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Pending purchase orders query failed: " . $e->getMessage());
            }

            // Pending Sales Orders
            try {
                $stats['pending_sales_orders'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_sales_orders WHERE is_deleted = 0 AND status NOT IN ('completed', 'cancelled')")->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Pending sales orders query failed: " . $e->getMessage());
            }

            // Today's Sales (from Sales Invoices)
            try {
                $result = $db->createCommand(
                    "SELECT IFNULL(SUM(grand_total), 0) FROM inventory_sales_invoices
                     WHERE DATE(created_at) = CURDATE() AND is_deleted = 0 AND status IN ('Paid', 'Partially Paid', 'Issued', 'Draft')"
                )->queryScalar();
                $stats['today_sales'] = (float)$result;
            } catch (\Exception $e) {
                \Yii::warning("Today's sales query failed: " . $e->getMessage());
                $stats['today_sales'] = 0;
            }

            // Today's Purchases (from Purchase Invoices)
            try {
                $result = $db->createCommand(
                    "SELECT IFNULL(SUM(grand_total), 0) FROM inventory_purchase_invoices
                     WHERE DATE(created_at) = CURDATE() AND is_deleted = 0 AND status IN ('Paid', 'Unpaid', 'Partially Paid')"
                )->queryScalar();
                $stats['today_purchases'] = (float)$result;
            } catch (\Exception $e) {
                \Yii::warning("Today's purchases query failed: " . $e->getMessage());
                $stats['today_purchases'] = 0;
            }

            // Customers
            try {
                $stats['customers'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_customers WHERE is_deleted = 0 AND is_active = 1")->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Customers query failed: " . $e->getMessage());
            }

            // Suppliers
            try {
                $stats['suppliers'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_suppliers WHERE is_deleted = 0 AND is_active = 1")->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Suppliers query failed: " . $e->getMessage());
            }

            // Total Revenue (from Sales Invoices)
            try {
                $result = $db->createCommand(
                    "SELECT IFNULL(SUM(grand_total), 0) FROM inventory_sales_invoices
                     WHERE is_deleted = 0 AND status IN ('Paid', 'Partially Paid', 'Issued', 'Draft')"
                )->queryScalar();
                $stats['total_revenue'] = (float)$result;
            } catch (\Exception $e) {
                \Yii::warning("Total revenue query failed: " . $e->getMessage());
                $stats['total_revenue'] = 0;
            }

            // Total Purchases Value (from Purchase Invoices)
            try {
                $result = $db->createCommand(
                    "SELECT IFNULL(SUM(grand_total), 0) FROM inventory_purchase_invoices
                     WHERE is_deleted = 0 AND status IN ('Paid', 'Unpaid', 'Partially Paid')"
                )->queryScalar();
                $stats['total_purchases_value'] = (float)$result;
            } catch (\Exception $e) {
                \Yii::warning("Total purchases value query failed: " . $e->getMessage());
                $stats['total_purchases_value'] = 0;
            }

            // Pending Returns
            try {
                $stats['pending_returns'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_returns WHERE is_deleted = 0 AND status NOT IN ('completed', 'rejected')")->queryScalar();
            } catch (\Exception $e) {
                // Returns table might not exist, so don't log error
                $stats['pending_returns'] = 0;
            }

            // BALANCE SHEET CALCULATIONS
            // Assets = Inventory Value + Accounts Receivable (unpaid sales invoices)
            try {
                // Inventory Value: Sum of (quantity * average_cost)
                $inventoryValue = $db->createCommand(
                    "SELECT IFNULL(SUM(quantity * average_cost), 0) FROM inventory_stock WHERE is_deleted = 0"
                )->queryScalar();

                // Accounts Receivable: Remaining balance in unpaid/partial sales invoices
                $accountsReceivable = $db->createCommand(
                    "SELECT IFNULL(SUM(remaining_balance), 0) FROM inventory_sales_invoices
                     WHERE is_deleted = 0 AND status IN ('Issued', 'Partially Paid')"
                )->queryScalar();

                $stats['total_assets'] = (float)$inventoryValue + (float)$accountsReceivable;
            } catch (\Exception $e) {
                \Yii::warning("Total assets calculation failed: " . $e->getMessage());
                $stats['total_assets'] = 0;
            }

            // Liabilities = Accounts Payable (unpaid purchase invoices)
            try {
                $stats['total_liabilities'] = (float)$db->createCommand(
                    "SELECT IFNULL(SUM(balance_amount), 0) FROM inventory_purchase_invoices
                     WHERE is_deleted = 0 AND status IN ('Unpaid', 'Partially Paid')"
                )->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Total liabilities calculation failed: " . $e->getMessage());
                $stats['total_liabilities'] = 0;
            }

            // Equity = Assets - Liabilities
            $stats['total_equity'] = $stats['total_assets'] - $stats['total_liabilities'];

            // Balance Sheet Profit/Loss = Total Revenue - Total Purchases Value
            $stats['balance_sheet_profit_loss'] = $stats['total_revenue'] - $stats['total_purchases_value'];

        } catch (\Exception $e) {
            \Yii::error("Dashboard stats error: " . $e->getMessage());
        }

        return $stats;
    }

    public function actionDashboardData()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $stats = $this->getDashboardStats();

            // Ensure all keys exist with default values
            $stats = array_merge([
                'warehouses' => 0,
                'total_products' => 0,
                'current_stock' => 0,
                'low_stock_items' => 0,
                'pending_purchase_orders' => 0,
                'pending_sales_orders' => 0,
                'today_sales' => 0,
                'today_purchases' => 0,
                'customers' => 0,
                'suppliers' => 0,
                'total_revenue' => 0,
                'total_purchases_value' => 0,
                'pending_returns' => 0,
            ], $stats);

            // Purchase Performance Chart Data (Last 12 months)
            $purchasePerformance = $this->getPurchasePerformanceData();

            // Sales Performance Chart Data (Last 12 months)
            $salesPerformance = $this->getSalesPerformanceData();

            return [
                'success' => true,
                'stats' => $stats,
                'purchasePerformance' => $purchasePerformance,
                'salesPerformance' => $salesPerformance,
            ];
        } catch (\Exception $e) {
            \Yii::error("Dashboard data error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to load dashboard data: ' . $e->getMessage(),
            ];
        }
    }

    public function actionTruncateSales()
    {
        // Check delete permission
        $this->requirePermission('delete');

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $password = Yii::$app->request->post('password', '');
        $user_id = $this->currentUserId();

        // Verify password against admin user
        $admin = Yii::$app->db->createCommand(
            "SELECT password FROM system_users WHERE id = :id AND is_active = 1",
            [':id' => $user_id]
        )->queryOne();

        if (!$admin) {
            return ['success' => false, 'message' => 'User not found'];
        }

        // Verify password using bcrypt
        if (!password_verify($password, $admin['password'])) {
            return ['success' => false, 'message' => 'Invalid password'];
        }

        try {
            $db = Yii::$app->db;
            $db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
            $tables = [
                'inventory_sale_invoice_payments',      // Payment history (must be first)
                'inventory_pos_payment_history',        // POS payment history (must be first)
                'inventory_sales_returns',              // Sales returns
                'inventory_sales_invoice_items',         // Invoice line items
                'inventory_sales_invoices',              // Sales invoices
                'inventory_pos_items',                  // POS items
                'inventory_pos_transactions',           // POS transactions
                'inventory_pos_sales',                  // POS sales
                'inventory_sales_order_items',          // Sales order line items
                'inventory_sales_orders'                // Sales orders
            ];

            foreach ($tables as $table) {
                try {
                    $db->createCommand("TRUNCATE TABLE $table")->execute();
                } catch (\Exception $e) {
                    // Table might not exist
                }
            }

            $db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();

            // Log the action
            try {
                Yii::$app->db->createCommand()->insert(
                    'activitylogs',
                    [
                        'activity' => 'Truncate Sales Records - Deleted all sale records including sales orders, invoices, payment history, POS sales, returns, and transactions',
                        'activitytype' => 'Truncate',
                        'module' => 'Sales',
                        'uid' => $user_id,
                        'ip_address' => Yii::$app->request->userIP,
                        'date' => date('Y-m-d'),
                        'datetime' => date('Y-m-d H:i:s')
                    ]
                )->execute();
            } catch (\Exception $e) {
                // Log table might not exist, continue anyway
            }

            return [
                'success' => true,
                'message' => 'All sale records have been successfully deleted including orders, invoices, payments, POS sales, returns, and transactions!'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function currentUserId()
    {
        $userArray = Yii::$app->session->get('user_array', []);
        return $userArray['id'] ?? null;
    }

    public function actionTruncatePurchases()
    {
        // Check delete permission
        $this->requirePermission('delete');

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $password = Yii::$app->request->post('password', '');
        $user_id = $this->currentUserId();

        // Verify password against admin user
        $admin = Yii::$app->db->createCommand(
            "SELECT password FROM system_users WHERE id = :id AND is_active = 1",
            [':id' => $user_id]
        )->queryOne();

        if (!$admin) {
            return ['success' => false, 'message' => 'User not found'];
        }

        // Verify password using bcrypt
        if (!password_verify($password, $admin['password'])) {
            return ['success' => false, 'message' => 'Invalid password'];
        }

        try {
            $db = Yii::$app->db;
            $db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();

            // All tables used by Purchase Controller
            $tables = [
                'inventory_purchase_invoice_payments',   // Payment history (must be first)
                'inventory_purchase_return_items',       // Purchase return line items
                'inventory_purchase_returns',            // Purchase returns
                'inventory_goods_receiving_items',       // Goods receiving line items
                'inventory_goods_receiving',             // Goods receiving records
                'inventory_purchase_invoice_items',      // Invoice line items
                'inventory_purchase_invoices',           // Purchase invoices
                'inventory_purchase_order_items',        // Purchase order line items
                'inventory_purchase_orders'              // Purchase orders
            ];

            foreach ($tables as $table) {
                try {
                    $db->createCommand("TRUNCATE TABLE $table")->execute();
                } catch (\Exception $e) {
                    // Table might not exist
                }
            }

            $db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();

            // Log the action
            try {
                Yii::$app->db->createCommand()->insert(
                    'activitylogs',
                    [
                        'activity' => 'Truncate Purchase Records - Deleted all purchase records including purchase orders, invoices, payment history, goods receiving, returns, and related items',
                        'activitytype' => 'Truncate',
                        'module' => 'Purchase',
                        'uid' => $user_id,
                        'ip_address' => Yii::$app->request->userIP,
                        'date' => date('Y-m-d'),
                        'datetime' => date('Y-m-d H:i:s')
                    ]
                )->execute();
            } catch (\Exception $e) {
                // Log table might not exist, continue anyway
            }

            return [
                'success' => true,
                'message' => 'All purchase records have been successfully deleted including orders, invoices, payments, goods receiving, returns, and line items!'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function getMonthSalesTotal()
    {
        try {
            $result = Yii::$app->db->createCommand(
                "SELECT IFNULL(SUM(total_amount), 0) FROM inventory_sales_orders
                 WHERE YEAR(created_at) = YEAR(CURDATE())
                 AND MONTH(created_at) = MONTH(CURDATE())
                 AND is_deleted = 0"
            )->queryScalar();
            return (float)$result;
        } catch (\Exception $e) {
            \Yii::warning("Month sales total query failed: " . $e->getMessage());
            return 0;
        }
    }

    private function getMonthPurchasesTotal()
    {
        try {
            $result = Yii::$app->db->createCommand(
                "SELECT IFNULL(SUM(total_amount), 0) FROM inventory_purchase_orders
                 WHERE YEAR(created_at) = YEAR(CURDATE())
                 AND MONTH(created_at) = MONTH(CURDATE())
                 AND is_deleted = 0"
            )->queryScalar();
            return (float)$result;
        } catch (\Exception $e) {
            \Yii::warning("Month purchases total query failed: " . $e->getMessage());
            return 0;
        }
    }

    private function getMonthlyChartData()
    {
        $data = [];
        try {
            for ($i = 5; $i >= 0; $i--) {
                $date = date('Y-m', strtotime("-$i months"));
                try {
                    $sales = Yii::$app->db->createCommand(
                        "SELECT IFNULL(SUM(total_amount), 0) FROM inventory_sales_orders
                         WHERE DATE_FORMAT(created_at, '%Y-%m') = :date
                         AND is_deleted = 0"
                    )->bindValue(':date', $date)->queryScalar();

                    $purchases = Yii::$app->db->createCommand(
                        "SELECT IFNULL(SUM(total_amount), 0) FROM inventory_purchase_orders
                         WHERE DATE_FORMAT(created_at, '%Y-%m') = :date
                         AND is_deleted = 0"
                    )->bindValue(':date', $date)->queryScalar();

                    $data[] = [
                        'month' => date('M Y', strtotime($date)),
                        'sales' => (int)$sales,
                        'purchases' => (int)$purchases,
                    ];
                } catch (\Exception $e) {
                    \Yii::warning("Monthly chart data for $date failed: " . $e->getMessage());
                    $data[] = [
                        'month' => date('M Y', strtotime($date)),
                        'sales' => 0,
                        'purchases' => 0,
                    ];
                }
            }
        } catch (\Exception $e) {
            \Yii::error("Monthly chart data query failed: " . $e->getMessage());
        }
        return $data;
    }

    private function getPurchasePerformanceData()
    {
        $data = [];
        try {
            for ($i = 11; $i >= 0; $i--) {
                $date = date('Y-m', strtotime("-$i months"));
                try {
                    $amount = Yii::$app->db->createCommand(
                        "SELECT IFNULL(SUM(grand_total), 0) FROM inventory_purchase_invoices
                         WHERE DATE_FORMAT(created_at, '%Y-%m') = :date
                         AND is_deleted = 0 AND status IN ('Paid', 'Partially Paid', 'Issued')"
                    )->bindValue(':date', $date)->queryScalar();

                    $data[] = [
                        'label' => date('M', strtotime($date)),
                        'amount' => (float)$amount,
                    ];
                } catch (\Exception $e) {
                    \Yii::warning("Purchase performance data for $date failed: " . $e->getMessage());
                    $data[] = [
                        'label' => date('M', strtotime($date)),
                        'amount' => 0,
                    ];
                }
            }
        } catch (\Exception $e) {
            \Yii::error("Purchase performance query failed: " . $e->getMessage());
        }
        return $data;
    }

    private function getSalesPerformanceData()
    {
        $data = [];
        try {
            for ($i = 11; $i >= 0; $i--) {
                $date = date('Y-m', strtotime("-$i months"));
                try {
                    $amount = Yii::$app->db->createCommand(
                        "SELECT IFNULL(SUM(grand_total), 0) FROM inventory_sales_invoices
                         WHERE DATE_FORMAT(created_at, '%Y-%m') = :date
                         AND is_deleted = 0 AND status IN ('Paid', 'Partially Paid', 'Issued')"
                    )->bindValue(':date', $date)->queryScalar();

                    $data[] = [
                        'label' => date('M', strtotime($date)),
                        'amount' => (float)$amount,
                    ];
                } catch (\Exception $e) {
                    \Yii::warning("Sales performance data for $date failed: " . $e->getMessage());
                    $data[] = [
                        'label' => date('M', strtotime($date)),
                        'amount' => 0,
                    ];
                }
            }
        } catch (\Exception $e) {
            \Yii::error("Sales performance query failed: " . $e->getMessage());
        }
        return $data;
    }

    public function actionWarehouses()
    {
        if (Yii::$app->request->isGet) {
            // Check view permission
            $this->requirePermission('view');

            $warehouses = Yii::$app->db->createCommand("SELECT *  FROM inventory_warehouses  WHERE is_deleted = 0  and is_active=1  ORDER BY id ASC")->queryAll();
            return $this->render('warehouses', ['warehouses' => $warehouses, 'zeromargin'=>true]);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $warehouse = Yii::$app->request->post();
            $warehouse_id = Yii::$app->request->post('id');
            if ($warehouse_id && isset($warehouse['delete']) && $warehouse['delete'] == 1) {
                // Check delete permission
                $this->requirePermission('delete');
                $result = Yii::$app->db->createCommand()
                    ->update(
                        'inventory_warehouses',
                        ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => Yii::$app->user->id ?? null,],
                        'id = :id',
                        [':id' => $warehouse_id]
                    )->execute();
                if ($result) {
                    Yii::$app->Component->Activitylog(
                        'Deleted warehouse ID: ' . $warehouse_id,
                        'delete',
                        $warehouse_id,
                        'inventory',
                        ['is_deleted' => 1]
                    );
                    return [
                        'success' => true,
                        'message' => 'Warehouse deleted successfully.'
                    ];
                }
                return [
                    'success' => false,
                    'message' => 'Failed to delete warehouse.'
                ];
            }
            if (empty($warehouse['warehouse_name'])) {
                return [
                    'success' => false,
                    'message' => 'Warehouse name is required.'
                ];
            }
            if (empty($warehouse['warehouse_code'])) {
                return [
                    'success' => false,
                    'message' => 'Warehouse code is required.'
                ];
            }

            if ($warehouse_id) {
                // Check edit permission for updates
                $this->requirePermission('edit');
            } else {
                // Check add permission for creates
                $this->requirePermission('add');
            }

            $warehouseData = [
                'warehouse_name' => $warehouse['warehouse_name'],
                'warehouse_code' => $warehouse['warehouse_code'],
                'address' => $warehouse['address'] ?? null,
                'city' => $warehouse['city'] ?? 'Islamabad',
                'province' => $warehouse['province'] ?? 'Islamabad Capital Territory',
                'country' => $warehouse['country'] ?? 'Pakistan',
                'contact_person' => $warehouse['contact_person'] ?? null,
                'phone' => $warehouse['phone'] ?? null,
                'email' => $warehouse['email'] ?? null,
                'remarks' => $warehouse['remarks'] ?? null,
                'is_active' => isset($warehouse['is_active']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Yii::$app->user->id ?? null,
            ];

            if ($warehouse_id) {
                $result = Yii::$app->db->createCommand()
                    ->update('inventory_warehouses', $warehouseData, 'id = :id', [':id' => $warehouse_id])
                    ->execute();
                if ($result) {
                    Yii::$app->Component->Activitylog(
                        'Updated warehouse: ' . $warehouse['warehouse_name'],
                        'update',
                        $warehouse_id,
                        'inventory',
                        $warehouseData
                    );
                    return [
                        'success' => true,
                        'message' => 'Warehouse updated successfully.'
                    ];
                }
                return [
                    'success' => false,
                    'message' => 'Failed to update warehouse.'
                ];
            }
            $warehouseData['created_at'] = date('Y-m-d H:i:s');
            $warehouseData['created_by'] = Yii::$app->user->id ?? null;
            $warehouseData['is_deleted'] = 0;
            $result = Yii::$app->db->createCommand()
                ->insert('inventory_warehouses', $warehouseData)
                ->execute();
            if ($result) {
                $newWarehouseId = Yii::$app->db->getLastInsertID();
                Yii::$app->Component->Activitylog(
                    'Created warehouse: ' . $warehouse['warehouse_name'],
                    'create',
                    $newWarehouseId,
                    'inventory',
                    $warehouseData
                );
                return [
                    'success' => true,
                    'message' => 'Warehouse created successfully.'
                ];
            }
            return [
                'success' => false,
                'message' => 'Failed to create warehouse.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    } 
    
    public function actionProducts()
    {
        // Check module permission
        $this->requireModulePermission('inventory/products');

        $modules = [
            ['name' => 'Product Dashboard', 'controller' => 'products/productdashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Categories', 'controller' => 'products/categories', 'icon' => 'fa fa-tags'],
            ['name' => 'Brands', 'controller' => 'products/brands', 'icon' => 'fa fa-certificate'],
            ['name' => 'Units', 'controller' => 'products/units', 'icon' => 'fa fa-balance-scale'],
            ['name' => 'Vehicle Makes', 'controller' => 'products/vehiclemakes', 'icon' => 'fa fa-car'],
            ['name' => 'Vehicle Models', 'controller' => 'products/vehiclemodels', 'icon' => 'fa fa-car'],
            ['name' => 'Product List', 'controller' => 'products/productlist', 'icon' => 'fa fa-cubes'],
        ];

        return $this->render('index', compact('modules'));
    }

    public function actionInventory()
    {
        // Check module permission
        $this->requireModulePermission('inventory/inventory');

        $modules = [
            ['name' => 'Dashboard', 'controller' => 'stock/inventorydashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Current Stock', 'controller' => 'stock/inventorycurrentstock', 'icon' => 'fa fa-cubes'],
            // ['name' => 'Reserved Stock', 'controller' => 'stock/inventoryreservedstock', 'icon' => 'fa fa-lock'],
            ['name' => 'Stock Adjustment', 'controller' => 'stock/inventorystockadjustment', 'icon' => 'fa fa-sliders'],
            // ['name' => 'Stock Movement', 'controller' => 'stock/inventorystockmovement', 'icon' => 'fa fa-exchange'],
            // ['name' => 'Opening Stock', 'controller' => 'stock/inventoryopeningstock', 'icon' => 'fa fa-plus-square'],
            ['name' => 'Damaged Stock', 'controller' => 'stock/inventorydamagedstock', 'icon' => 'fa fa-times-circle'],
            ['name' => 'Stock Valuation', 'controller' => 'stock/inventorystockvaluation', 'icon' => 'fa fa-line-chart'],
            ['name' => 'Low Stock Items', 'controller' => 'stock/inventorylowstock', 'icon' => 'fa fa-warning'],
            // ['name' => 'Stock Ledger', 'controller' => 'stock/inventorystockledger', 'icon' => 'fa fa-book'],
            // ['name' => 'Reorder Report', 'controller' => 'stock/inventoryreorderreport', 'icon' => 'fa fa-refresh'],
        ];
        return $this->render('index', compact('modules'));
    }
    
    public function actionSuppliers()
    {
        // Check module permission
        $this->requireModulePermission('inventory/suppliers');

        $modules = [
            ['name' => 'Dashboard', 'controller' => 'supplier/supplierdashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Supplier List', 'controller' => 'supplier/supplierlist', 'icon' => 'fa fa-truck'],
            ['name' => 'Supplier Ledger', 'controller' => 'supplier/supplierledger', 'icon' => 'fa fa-book'],
            ['name' => 'Supplier Payments', 'controller' => 'supplier/supplierpayments', 'icon' => 'fa fa-money'],
            ['name' => 'Purchase History', 'controller' => 'supplier/supplierpurchasehistory', 'icon' => 'fa fa-history'],
            ['name' => 'Supplier Performance', 'controller' => 'supplier/supplierperformance', 'icon' => 'fa fa-bar-chart'],
            ['name' => 'Supplier Documents', 'controller' => 'supplier/supplierdocuments', 'icon' => 'fa fa-folder-open'],
        ];

        return $this->render('index', compact('modules'));
    }

    public function actionPurchases()
    {
        // Check module permission
        $this->requireModulePermission('inventory/purchases');

        $modules = [
            ['name' => 'Dashboard', 'controller' => 'purchase/purchasedashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Purchase Orders', 'controller' => 'purchase/purchaseorders', 'icon' => 'fa fa-shopping-cart'],
            ['name' => 'Goods Receiving', 'controller' => 'purchase/goodsreceiving', 'icon' => 'fa fa-truck'],
            ['name' => 'Purchase Invoices', 'controller' => 'purchase/purchaseinvoices', 'icon' => 'fa fa-file-text'],
            ['name' => 'Pending Purchases', 'controller' => 'purchase/pendingpurchases', 'icon' => 'fa fa-clock-o'],
            ['name' => 'Approved Purchases', 'controller' => 'purchase/approvedpurchases', 'icon' => 'fa fa-check-circle'],
            ['name' => 'Purchase Reports', 'controller' => 'purchase/purchasereports', 'icon' => 'fa fa-bar-chart'],
            ['name' => 'Purchase Analytics', 'controller' => 'purchase/purchaseanalytics', 'icon' => 'fa fa-line-chart'],
        ];
        return $this->render('index', compact('modules'));
    }

    public function actionSales()
    {
        // Check module permission
        $this->requireModulePermission('inventory/sales');

        $modules = [
            ['name' => 'Sales Dashboard', 'controller' => 'sale/salesdashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Sale Order', 'controller' => 'sale/salesorders', 'icon' => 'fa fa-shopping-bag'],
            ['name' => 'Sales Invoices', 'controller' => 'sale/salesinvoices', 'icon' => 'fa fa-file-text'],
            ['name' => 'Pending Orders', 'controller' => 'sale/pendingorders', 'icon' => 'fa fa-clock-o'],
            ['name' => 'Delivered Orders', 'controller' => 'sale/deliveredorders', 'icon' => 'fa fa-check-circle'],
            ['name' => 'Cancelled Orders', 'controller' => 'sale/cancelledorders', 'icon' => 'fa fa-ban'],
            ['name' => 'Customer Payments', 'controller' => 'customers/customerpayments', 'icon' => 'fa fa-money'],
            ['name' => 'Sales Reports', 'controller' => 'sale/salesreports', 'icon' => 'fa fa-line-chart'],
        ];

        return $this->render('index', compact('modules'));
    }

    public function actionCustomers()
    {
        // Check module permission
        $this->requireModulePermission('inventory/customers');

        $modules = [
            ['name' => 'Customer Dashboard', 'controller' => 'customers/customerdashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Customer List', 'controller' => 'customers/customerlist', 'icon' => 'fa fa-users'],
            ['name' => 'Customer Payments', 'controller' => 'customers/customerpayments', 'icon' => 'fa fa-money'],
        ];

        return $this->render('index', compact('modules'));
    }

    public function actionStockAudit()
    {
        // Check module permission
        $this->requireModulePermission('inventory/stockaudit');

        $modules = [
            ['name' => 'Audit Dashboard', 'controller' => 'stockaudit/auditdashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Audit Schedule', 'controller' => 'stockaudit/auditschedule', 'icon' => 'fa fa-calendar'],
            ['name' => 'Physical Stock Count', 'controller' => 'stockaudit/physicalstockcount', 'icon' => 'fa fa-list-ol'],
            ['name' => 'Stock Verification', 'controller' => 'stockaudit/stockverification', 'icon' => 'fa fa-check-square-o'],
            ['name' => 'Stock Variance', 'controller' => 'stockaudit/stockvariance', 'icon' => 'fa fa-balance-scale'],
            ['name' => 'Stock Reconciliation', 'controller' => 'stockaudit/stockreconciliation', 'icon' => 'fa fa-refresh'],
            ['name' => 'Adjustment Approval', 'controller' => 'stockaudit/adjustmentapproval', 'icon' => 'fa fa-check-circle'],
            ['name' => 'Audit History', 'controller' => 'stockaudit/audithistory', 'icon' => 'fa fa-history'],
            ['name' => 'Audit Reports', 'controller' => 'stockaudit/auditreports', 'icon' => 'fa fa-file-text'],
        ];
        return $this->render('index', compact('modules'));
    }

    public function actionFinance()
    {
        // Check module permission
        $this->requireModulePermission('inventory/finance');

        $modules = [
            ['name' => 'Finance Dashboard', 'controller' => 'finance/financedashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Chart of Accounts', 'controller' => 'finance/chartofaccounts', 'icon' => 'fa fa-sitemap'],
            ['name' => 'Profit & Loss', 'controller' => 'finance/profitloss', 'icon' => 'fa fa-line-chart'],
            ['name' => 'Balance Sheet', 'controller' => 'finance/balancesheet', 'icon' => 'fa fa-file-text-o'],
        ];

        return $this->render('index', compact('modules'));
    }

    public function actionReports()
    {
        // Check module permission
        $this->requireModulePermission('inventory/reports');
        $modules = [
            ['name' => 'Reports Dashboard', 'controller' => 'reports/reportsdashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Inventory Reports', 'controller' => 'reports/inventoryreports', 'icon' => 'fa fa-cubes'],
            ['name' => 'Purchase Reports', 'controller' => 'reports/purchasereports', 'icon' => 'fa fa-shopping-cart'],
            ['name' => 'Sales Reports', 'controller' => 'reports/salesreports', 'icon' => 'fa fa-shopping-bag'],
            ['name' => 'Stock Valuation', 'controller' => 'reports/stockvaluationreport', 'icon' => 'fa fa-line-chart'],
            ['name' => 'Low Stock Report', 'controller' => 'reports/lowstockreport', 'icon' => 'fa fa-warning'],
            ['name' => 'Customer Ledger', 'controller' => 'reports/customerledgerreport', 'icon' => 'fa fa-users'],
            ['name' => 'Supplier Ledger', 'controller' => 'reports/supplierledgerreport', 'icon' => 'fa fa-truck'],
            ['name' => 'Warehouse Reports', 'controller' => 'reports/warehousereports', 'icon' => 'fa fa-building'],
            ['name' => 'Financial Reports', 'controller' => 'reports/financialreports', 'icon' => 'fa fa-money'],
            ['name' => 'Tax Reports', 'controller' => 'reports/taxreports', 'icon' => 'fa fa-percent'],
            ['name' => 'Product Performance', 'controller' => 'reports/productperformance', 'icon' => 'fa fa-trophy'],
        ];
        $twolines = true;
        return $this->render('index', array_merge(
            compact('modules'),
            compact('twolines')
        ));
    } 

    public function actionNotifications()
    {
        // Check module permission
        $this->requireModulePermission('inventory/notifications');

        $modules = [
            ['name' => 'Notification Center', 'controller' => 'notifications/notificationcenter', 'icon' => 'fa fa-bell'],
            ['name' => 'Low Stock Alerts', 'controller' => 'notifications/lowstockalerts', 'icon' => 'fa fa-warning'],
            ['name' => 'Out of Stock Alerts', 'controller' => 'notifications/outofstockalerts', 'icon' => 'fa fa-times-circle'],
            ['name' => 'Pending Purchase Alerts', 'controller' => 'notifications/pendingpurchasealerts', 'icon' => 'fa fa-shopping-cart'],
            ['name' => 'Pending Sales Alerts', 'controller' => 'notifications/pendingsalesalerts', 'icon' => 'fa fa-shopping-bag'],
            ['name' => 'Payment Due Alerts', 'controller' => 'notifications/paymentduealerts', 'icon' => 'fa fa-money'],
            // ['name' => 'Supplier Notifications', 'controller' => 'notifications/suppliernotifications', 'icon' => 'fa fa-truck'],
            // ['name' => 'Customer Notifications', 'controller' => 'notifications/customernotifications', 'icon' => 'fa fa-users'],
        ];
        return $this->render('index', compact('modules'));
    }

    public function actionSystemlogs()
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

        return $this->renderPartial('systemlogs');
    }

    public function actionActivitylogs()
    {
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $flag = Yii::$app->request->post('flag');
            if (isset($flag) && $flag === 'load') {
                return $this->getActivityLogs();
            }
            return $this->getActivityLogs();
        }

        // Get filter options
        $modules = Yii::$app->db->createCommand(
            "SELECT DISTINCT module FROM activitylogs WHERE module IS NOT NULL AND module != '' ORDER BY module"
        )->queryColumn();

        $activities = Yii::$app->db->createCommand(
            "SELECT DISTINCT activity FROM activitylogs WHERE activity IS NOT NULL AND activity != '' ORDER BY activity"
        )->queryColumn();

        return $this->render('activitylogs', [
            'modules' => $modules,
            'activities' => $activities
        ]);
    }

    private function getActivityLogs()
    {
        $page = max(1, (int)(Yii::$app->request->post('page') ?? 1));
        $perPage = max(10, (int)(Yii::$app->request->post('per_page') ?? 20));
        $offset = ($page - 1) * $perPage;

        $dateFrom = Yii::$app->request->post('date_from');
        $dateTo = Yii::$app->request->post('date_to');
        $module = Yii::$app->request->post('module');
        $activity = Yii::$app->request->post('activity');
        $search = Yii::$app->request->post('search');

        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($dateFrom)) {
            $where .= " AND DATE(datetime) >= :dateFrom ";
            $params[':dateFrom'] = $dateFrom;
        }

        if (!empty($dateTo)) {
            $where .= " AND DATE(datetime) <= :dateTo ";
            $params[':dateTo'] = $dateTo;
        }

        if (!empty($module)) {
            $where .= " AND module = :module ";
            $params[':module'] = $module;
        }

        if (!empty($activity)) {
            $where .= " AND activity = :activity ";
            $params[':activity'] = $activity;
        }

        if (!empty($search)) {
            $where .= " AND (activity LIKE :search OR user_agent LIKE :search OR ip_address LIKE :search) ";
            $params[':search'] = '%' . $search . '%';
        }

        $total = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM activitylogs {$where}",
            $params
        )->queryScalar();

        $logs = Yii::$app->db->createCommand(
            "SELECT * FROM activitylogs {$where} ORDER BY datetime DESC LIMIT {$offset}, {$perPage}",
            $params
        )->queryAll();

        // Format logs with user info
        foreach ($logs as &$log) {
            $log['formatted_date'] = date('M d, Y', strtotime($log['datetime']));
            $log['formatted_time'] = date('h:i A', strtotime($log['datetime']));

            // Get user info
            if (!empty($log['uid'])) {
                $user = Yii::$app->db->createCommand(
                    "SELECT username, first_name, last_name FROM system_users WHERE id = :id LIMIT 1",
                    [':id' => $log['uid']]
                )->queryOne();

                if ($user) {
                    $log['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $user['username'];
                } else {
                    $log['user_name'] = 'Unknown User';
                }
            } else {
                $log['user_name'] = 'System';
            }

            // Parse additional data
            if (!empty($log['additional_data'])) {
                $log['additional_data_decoded'] = json_decode($log['additional_data'], true);
            }
        }

        // Get summary statistics
        $summary = [
            'total_logs' => (int)$total,
            'total_modules' => (int)Yii::$app->db->createCommand(
                "SELECT COUNT(DISTINCT module) FROM activitylogs {$where}",
                $params
            )->queryScalar(),
            'total_users' => (int)Yii::$app->db->createCommand(
                "SELECT COUNT(DISTINCT uid) FROM activitylogs {$where}",
                $params
            )->queryScalar()
        ];

        return [
            'success' => true,
            'logs' => $logs,
            'summary' => $summary,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    public function actionProfile()
    {
        $user_array = Yii::$app->session->get('user_array');
        $user_id = $user_array['id'] ?? null;
        $session_id = Yii::$app->Component->ActiveSession();
        $student = null;
        $attendance_percentage = 0;
        $attendance_summary = [];
        $weekly_attendance = [];
        $weekly_timetable = [];
        $fee_summary = [];
        $recent_payments = [];

        // Get student data
        if ($user_id) {
            try {
                // Get student_id from system_users.referance
                $studentData = Yii::$app->db->createCommand(
                    "SELECT referance as student_id FROM system_users 
                     WHERE id = :user_id 
                     AND role_id = 4
                     LIMIT 1"
                )->bindValue(':user_id', $user_id)->queryOne();

                if ($studentData && !empty($studentData['student_id'])) {
                    $id = $studentData['student_id'];

                    // Fetch complete student profile
                    $student = Yii::$app->db->createCommand("
                        SELECT 
                            std.student_id,
                            std.admission_no,
                            std.roll_no,
                            CONCAT(std.first_name, ' ', std.last_name) as student_name,
                            std.first_name,
                            std.last_name,
                            std.email,
                            std.gender,
                            std.dob,
                            std.admission_date as date_of_admission,
                            std.mobile_number,
                            std.disable_id,
                            std.class_id,
                            std.section_id,
                            sess.session_name,
                            c.class_name,
                            s.section_name,
                            sc.name as category_name,
                            par.father_name,
                            par.mother_name,
                            par.father_phone,
                            par.mother_phone,
                            par.father_occupation,
                            par.mother_occupation,
                            std.photo_path
                        FROM students std
                        LEFT JOIN session sess ON std.session_id = sess.session_id
                        LEFT JOIN parents par ON std.student_id = par.student_id
                        LEFT JOIN classes c ON std.class_id = c.id
                        LEFT JOIN sections s ON std.section_id = s.id
                        LEFT JOIN student_categories sc ON std.category_id = sc.id
                        WHERE std.student_id = :student_id AND std.school_id = :school_id
                    ")->bindValues([
                        ':student_id' => $id,
                        ':school_id' => $this->school_id
                    ])->queryOne();

                    if ($student) {
                        // Check if student is disabled
                        $disabled_student = Yii::$app->db->createCommand(
                            "SELECT * FROM disabled_students 
                             WHERE student_id = :student_id 
                             AND session_id = :session_id 
                             AND school_id = :school_id
                             AND active_time IS NULL"
                        )->bindValues([
                            ':student_id' => $id,
                            ':session_id' => $session_id,
                            ':school_id' => $this->school_id
                        ])->queryOne();

                        $student['is_disabled'] = !empty($disabled_student);
                        $student['disabled_info'] = $disabled_student;

                        // Get attendance summary (last 30 days)
                        $attendance_summary = Yii::$app->db->createCommand("
                            SELECT 
                                COUNT(*) as total_days,
                                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
                                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent,
                                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late
                            FROM attendance
                            WHERE student_id = :student_id 
                            AND session_id = :session_id
                            AND school_id = :school_id
                            AND attendance_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                        ")->bindValues([
                            ':student_id' => $id,
                            ':session_id' => $session_id,
                            ':school_id' => $this->school_id
                        ])->queryOne();

                        // Calculate attendance percentage
                        if ($attendance_summary && $attendance_summary['total_days'] > 0) {
                            $attendance_percentage = round(($attendance_summary['present'] / $attendance_summary['total_days']) * 100, 1);
                        }

                        // Get detailed weekly attendance
                        $weekStart = date('Y-m-d', strtotime('monday this week'));
                        $weekEnd = date('Y-m-d', strtotime('sunday this week'));

                        try {
                            $weekly_attendance = Yii::$app->db->createCommand("
                                SELECT 
                                    a.attendance_date,
                                    a.status,
                                    a.remarks,
                                    a.period_id,
                                    tp.period_name,
                                    tp.start_time,
                                    tp.end_time,
                                    CONCAT(su.first_name, ' ', su.last_name) as marked_by_name
                                FROM attendance a
                                LEFT JOIN time_periods tp ON a.period_id = tp.id
                                LEFT JOIN system_users su ON a.marked_by = su.id
                                WHERE a.student_id = :student_id
                                AND a.school_id = :school_id
                                AND a.session_id = :session_id
                                AND a.attendance_date BETWEEN :week_start AND :week_end
                                ORDER BY a.attendance_date ASC, tp.period_order ASC
                            ")->bindValues([
                                ':student_id' => $id,
                                ':school_id' => $this->school_id,
                                ':session_id' => $session_id,
                                ':week_start' => $weekStart,
                                ':week_end' => $weekEnd
                            ])->queryAll();
                        } catch (\Exception $e) {
                            $weekly_attendance = [];
                        }

                        // Get student's weekly timetable
                        try {
                            $weekly_timetable = Yii::$app->db->createCommand("
                                SELECT 
                                    tt.*,
                                    tp.period_name,
                                    tp.start_time,
                                    tp.end_time,
                                    tp.period_order,
                                    tp.period_type,
                                    sub.subject_name,
                                    sub.subject_code,
                                    CONCAT(t.first_name, ' ', t.last_name) as teacher_name,
                                    c.class_name,
                                    s.section_name
                                FROM timetables tt
                                INNER JOIN time_periods tp ON tt.time_period_id = tp.id
                                INNER JOIN subjects sub ON tt.subject_id = sub.id
                                INNER JOIN system_users t ON tt.teacher_id = t.id
                                INNER JOIN classes c ON tt.class_id = c.id
                                LEFT JOIN sections s ON tt.section_id = s.id
                                WHERE tt.class_id = :class_id
                                AND (tt.section_id = :section_id OR tt.section_id IS NULL)
                                AND tt.school_id = :school_id
                                AND tt.session_id = :session_id
                                ORDER BY tt.day_of_week ASC, tp.period_order ASC
                            ")->bindValues([
                                ':class_id' => $student['class_id'] ?? 0,
                                ':section_id' => $student['section_id'] ?? 0,
                                ':school_id' => $this->school_id,
                                ':session_id' => $session_id
                            ])->queryAll();
                        } catch (\Exception $e) {
                            $weekly_timetable = [];
                        }

                        // Get detailed fee summary
                        try {
                            $fee_summary = Yii::$app->db->createCommand("
                                SELECT 
                                    COALESCE(SUM(fs.gross_amount), 0) as total_fee,
                                    COALESCE(SUM(fs.paid_amount), 0) as total_paid,
                                    COALESCE(SUM(fs.balance_amount), 0) as total_balance,
                                    COALESCE(SUM(fs.discount_amount), 0) as total_discount,
                                    COALESCE(SUM(fs.fine_amount), 0) as total_fine,
                                    COUNT(DISTINCT fs.fee_structure_id) as total_structures,
                                    SUM(CASE WHEN fs.payment_status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                                    SUM(CASE WHEN fs.payment_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                                    SUM(CASE WHEN fs.payment_status = 'partial' THEN 1 ELSE 0 END) as partial_count,
                                    SUM(CASE WHEN fs.payment_status = 'overdue' THEN 1 ELSE 0 END) as overdue_count
                                FROM fee_structure fs
                                WHERE fs.student_id = :student_id 
                                AND fs.session_id = :session_id
                                AND (fs.is_deleted = 0 OR fs.is_deleted IS NULL)
                            ")->bindValues([
                                ':student_id' => $id,
                                ':session_id' => $session_id
                            ])->queryOne();
                        } catch (\Exception $e) {
                            $fee_summary = ['total_fee' => 0, 'total_paid' => 0, 'total_balance' => 0, 'total_discount' => 0, 'total_fine' => 0];
                        }

                        // Get recent fee payments (last 5)
                        try {
                            $recent_payments = Yii::$app->db->createCommand("
                                SELECT 
                                    fp.payment_id,
                                    fp.receipt_number,
                                    fp.amount_paid,
                                    fp.payment_date,
                                    fp.payment_mode,
                                    fp.fine_applied,
                                    fp.discount_applied,
                                    fp.status
                                FROM fee_payments fp
                                WHERE fp.student_id = :student_id 
                                AND fp.session_id = :session_id
                                AND fp.status = 'success'
                                ORDER BY fp.payment_date DESC, fp.created_at DESC
                                LIMIT 5
                            ")->bindValues([
                                ':student_id' => $id,
                                ':session_id' => $session_id
                            ])->queryAll();
                        } catch (\Exception $e) {
                            $recent_payments = [];
                        }
                    }
                }
            } catch (\Exception $e) {
                Yii::error('Error fetching student profile: ' . $e->getMessage());
            }
        }

        // Log activity
        Yii::$app->Component->Activitylog(
            'Viewed student profile',
            'view',
            $student['student_id'] ?? null,
            'student',
            ['module' => 'profile']
        );

        return $this->render('profile', [
            'student' => $student,
            'attendance_summary' => $attendance_summary,
            'attendance_percentage' => $attendance_percentage,
            'weekly_attendance' => $weekly_attendance,
            'weekly_timetable' => $weekly_timetable,
            'fee_summary' => $fee_summary,
            'recent_payments' => $recent_payments,
            'isStudentRole' => true,
            'can_disable' => false,
            'selected' => 'home',
        ]);
    }

    public function actionEditProfile()
    {

        $user_array = Yii::$app->session->get('user_array');
        $user_id = $user_array['id'] ?? null;
        $student = null;

        // Get student data
        if ($user_id) {
            try {
                // Get student_id from system_users.referance
                $studentData = Yii::$app->db->createCommand(
                    "SELECT referance as student_id FROM system_users 
                     WHERE id = :user_id 
                     AND role_id = 4
                     LIMIT 1"
                )->bindValue(':user_id', $user_id)->queryOne();

                if ($studentData && !empty($studentData['student_id'])) {
                    $student_id = $studentData['student_id'];

                    // Fetch complete student profile
                    $student = Yii::$app->db->createCommand("
                        SELECT 
                            std.student_id,
                            std.admission_no,
                            std.roll_no,
                            std.first_name,
                            std.last_name,
                            std.email,
                            std.gender,
                            std.dob,
                            std.admission_date as date_of_admission,
                            std.mobile_number,
                            std.class_id,
                            std.section_id,
                            std.category_id,
                            std.religion,
                            std.caste,
                            std.height,
                            std.weight,
                            std.blood_group_id,
                            std.photo_path,
                            par.father_name,
                            par.mother_name,
                            par.father_phone,
                            par.mother_phone,
                            par.father_occupation,
                            par.mother_occupation
                        FROM students std
                        LEFT JOIN parents par ON std.student_id = par.student_id
                        WHERE std.student_id = :student_id AND std.school_id = :school_id
                    ")->bindValues([
                        ':student_id' => $student_id,
                        ':school_id' => $this->school_id
                    ])->queryOne();
                }
            } catch (\Exception $e) {
                Yii::error('Error fetching student data for edit: ' . $e->getMessage());
            }
        }

        if (!$student) {
            Yii::$app->session->setFlash('error', 'User record not found.');
            return $this->redirect(['inventory/profile']);
        }

        // Get dropdown data
        $blood_groups = Yii::$app->db->createCommand('SELECT * FROM blood_groups')->queryAll();
        $student_categories = Yii::$app->db->createCommand('SELECT * FROM student_categories')->queryAll();

        // Log activity
        Yii::$app->Component->Activitylog(
            'Viewed edit profile form',
            'view',
            $student['student_id'],
            'student',
            ['module' => 'profile', 'action' => 'edit_form']
        );

        return $this->render('edit_profile', [
            'student' => $student,
            'blood_groups' => $blood_groups,
            'student_categories' => $student_categories
        ]);
    }

    public function actionUpdateProfile()
    {
        // Check if this is a profile picture upload (has profile_picture file and no other form data)
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $postData = Yii::$app->request->post();
            // If only profile_picture is being uploaded (no other form fields), handle as picture upload
            if (empty($postData['first_name']) && empty($postData['email']) && empty($postData['mobile_number'])) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->updateProfilePicture();
            }
        }

        // Handle full profile update (form submission)
        // Check if this is an AJAX request
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }

        $user_array = Yii::$app->session->get('user_array');
        $user_id = $user_array['id'] ?? null;

        if (!$user_id) {
            if (Yii::$app->request->isAjax) {
                return [
                    'success' => false,
                    'message' => 'User not authenticated'
                ];
            }
            Yii::$app->session->setFlash('error', 'User not authenticated');
            return $this->redirect(['inventory/profile']);
        }

        try {
            // Get student_id from system_users.referance
            $studentData = Yii::$app->db->createCommand(
                "SELECT referance as student_id FROM system_users 
                 WHERE id = :user_id 
                 AND role_id = 4
                 LIMIT 1"
            )->bindValue(':user_id', $user_id)->queryOne();

            if (!$studentData || empty($studentData['student_id'])) {
                if (Yii::$app->request->isAjax) {
                    return [
                        'success' => false,
                        'message' => 'Student record not found'
                    ];
                }
                Yii::$app->session->setFlash('error', 'Student record not found');
                return $this->redirect(['inventory/profile']);
            }

            $student_id = $studentData['student_id'];
            $data = Yii::$app->request->post();

            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Update students table
                $studentUpdate = [
                    'first_name' => $data['first_name'] ?? null,
                    'last_name' => $data['last_name'] ?? null,
                    'email' => $data['email'] ?? null,
                    'mobile_number' => $data['mobile_number'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'dob' => $data['dob'] ?? null,
                    'religion' => $data['religion'] ?? null,
                    'caste' => $data['caste'] ?? null,
                    'height' => $data['height'] ?? null,
                    'weight' => $data['weight'] ?? null,
                    'blood_group_id' => !empty($data['blood_group_id']) ? $data['blood_group_id'] : null,
                    'category_id' => !empty($data['category_id']) ? $data['category_id'] : null,
                ];

                // Remove null values
                $studentUpdate = array_filter($studentUpdate, function ($value) {
                    return $value !== null && $value !== '';
                });

                if (!empty($studentUpdate)) {
                    Yii::$app->db->createCommand()
                        ->update('students', $studentUpdate, [
                            'student_id' => $student_id,
                            'school_id' => $this->school_id
                        ])
                        ->execute();
                }

                // Update parents table
                $parentUpdate = [
                    'father_name' => $data['father_name'] ?? null,
                    'mother_name' => $data['mother_name'] ?? null,
                    'father_phone' => $data['father_phone'] ?? null,
                    'mother_phone' => $data['mother_phone'] ?? null,
                    'father_occupation' => $data['father_occupation'] ?? null,
                    'mother_occupation' => $data['mother_occupation'] ?? null
                ];

                // Remove null values
                $parentUpdate = array_filter($parentUpdate, function ($value) {
                    return $value !== null && $value !== '';
                });

                if (!empty($parentUpdate)) {
                    // Check if parent record exists
                    $parentExists = Yii::$app->db->createCommand(
                        "SELECT parent_id FROM parents WHERE student_id = :student_id LIMIT 1"
                    )->bindValue(':student_id', $student_id)->queryOne();

                    if ($parentExists) {
                        Yii::$app->db->createCommand()
                            ->update('parents', $parentUpdate, ['student_id' => $student_id])
                            ->execute();
                    } else {
                        $parentUpdate['student_id'] = $student_id;
                        Yii::$app->db->createCommand()
                            ->insert('parents', $parentUpdate)
                            ->execute();
                    }
                }

                $transaction->commit();

                // Log activity
                Yii::$app->Component->Activitylog(
                    'Updated student profile',
                    'update',
                    $student_id,
                    'student',
                    ['module' => 'profile', 'fields_updated' => array_keys(array_merge($studentUpdate, $parentUpdate))]
                );

                if (Yii::$app->request->isAjax) {
                    return [
                        'success' => true,
                        'message' => 'Profile updated successfully!'
                    ];
                }

                Yii::$app->session->setFlash('success', 'Profile updated successfully!');
                return $this->redirect(['inventory/profile']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Yii::error('Error updating student profile: ' . $e->getMessage());
            $errorMessage = 'An error occurred while updating profile: ' . $e->getMessage();

            if (Yii::$app->request->isAjax) {
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }

            Yii::$app->session->setFlash('error', $errorMessage);
            return $this->redirect(['inventory/edit-profile']);
        }
    }

    public function actionUpdateCredentials()
    {
        // Ensure POST request
        if (!Yii::$app->request->isPost) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Invalid request method.'
                ];
            }
            Yii::$app->session->setFlash('error', 'Invalid request method.');
            return $this->redirect(['inventory/profile']);
        }

        // For AJAX requests, return JSON
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $user_array = Yii::$app->session->get('user_array');
        $user_id = $user_array['id'] ?? null;

        if (!$user_id) {
            $message = 'User not authenticated.';
            if (Yii::$app->request->isAjax) {
                return [
                    'success' => false,
                    'message' => $message
                ];
            }
            Yii::$app->session->setFlash('error', $message);
            return $this->redirect(['inventory/profile']);
        }

        // Collect input
        $username = trim(Yii::$app->request->post('username', ''));
        $current_password = Yii::$app->request->post('current_password', '');
        $new_password = Yii::$app->request->post('new_password', '');
        $confirm_password = Yii::$app->request->post('confirm_password', '');

        // Basic validations
        if ($username === '' || $current_password === '' || $new_password === '' || $confirm_password === '') {
            $message = 'All fields are required.';
            if (Yii::$app->request->isAjax) {
                return [
                    'success' => false,
                    'message' => $message
                ];
            }
            Yii::$app->session->setFlash('error', $message);
            return $this->redirect(['inventory/profile']);
        }

        if ($new_password !== $confirm_password) {
            $message = 'New password and confirmation do not match.';
            if (Yii::$app->request->isAjax) {
                return [
                    'success' => false,
                    'message' => $message
                ];
            }
            Yii::$app->session->setFlash('error', $message);
            return $this->redirect(['inventory/profile']);
        }

        try {
            // Fetch current user from system_users
            $userRow = Yii::$app->db->createCommand(
                "SELECT id, username, password, referance, role_id 
                 FROM system_users 
                 WHERE id = :id AND role_id = 4 
                 LIMIT 1"
            )->bindValue(':id', $user_id)->queryOne();

            if (!$userRow) {
                $message = 'User account not found.';
                if (Yii::$app->request->isAjax) {
                    return [
                        'success' => false,
                        'message' => $message
                    ];
                }
                Yii::$app->session->setFlash('error', $message);
                return $this->redirect(['inventory/profile']);
            }

            // Verify current password (plain-text comparison as used in User::validatePassword)
            if ($userRow['password'] !== $current_password) {
                $message = 'Current password is incorrect.';
                if (Yii::$app->request->isAjax) {
                    return [
                        'success' => false,
                        'message' => $message
                    ];
                }
                Yii::$app->session->setFlash('error', $message);
                return $this->redirect(['inventory/profile']);
            }

            // Ensure username is unique (excluding current user)
            $existing = Yii::$app->db->createCommand(
                "SELECT COUNT(*) AS total FROM system_users 
                 WHERE username = :username AND id != :id"
            )->bindValues([
                ':username' => $username,
                ':id' => $user_id
            ])->queryOne();

            if (!empty($existing['total']) && (int)$existing['total'] > 0) {
                $message = 'This username is already taken. Please choose another one.';
                if (Yii::$app->request->isAjax) {
                    return [
                        'success' => false,
                        'message' => $message
                    ];
                }
                Yii::$app->session->setFlash('error', $message);
                return $this->redirect(['inventory/profile']);
            }

            // Update credentials
            Yii::$app->db->createCommand()
                ->update('system_users', [
                    'username' => $username,
                    'password' => $new_password,
                ], [
                    'id' => $user_id
                ])->execute();

            // Update session data
            $user_array['username'] = $username;
            $user_array['password'] = $new_password;
            Yii::$app->session->set('user_array', $user_array);

            // Log activity
            Yii::$app->Component->Activitylog(
                'Updated own login credentials',
                'update',
                $userRow['referance'] ?? $user_id,
                'authentication',
                [
                    'username' => $username,
                    'role_id' => $userRow['role_id'] ?? 4
                ]
            );

            $successMessage = 'Login credentials updated successfully.';

            if (Yii::$app->request->isAjax) {
                return [
                    'success' => true,
                    'message' => $successMessage
                ];
            }

            Yii::$app->session->setFlash('success', $successMessage);
            return $this->redirect(['inventory/profile']);
        } catch (\Exception $e) {
            Yii::error('Error updating login credentials: ' . $e->getMessage());
            $message = 'An error occurred while updating login credentials: ' . $e->getMessage();

            if (Yii::$app->request->isAjax) {
                return [
                    'success' => false,
                    'message' => $message
                ];
            }

            Yii::$app->session->setFlash('error', $message);
            return $this->redirect(['inventory/profile']);
        }
    }

    private function updateProfilePicture()
    {
        $user_array = Yii::$app->session->get('user_array');
        $user_id = $user_array['id'] ?? null;

        if (!$user_id) {
            return [
                'success' => false,
                'message' => 'User not authenticated'
            ];
        }

        try {
            // Get student_id from system_users.referance
            $studentData = Yii::$app->db->createCommand(
                "SELECT referance as student_id FROM system_users 
                 WHERE id = :user_id 
                 AND role_id = 4
                 LIMIT 1"
            )->bindValue(':user_id', $user_id)->queryOne();

            if (!$studentData || empty($studentData['student_id'])) {
                return [
                    'success' => false,
                    'message' => 'Student record not found'
                ];
            }

            $student_id = $studentData['student_id'];

            // Check if profile picture is uploaded
            if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
                return [
                    'success' => false,
                    'message' => 'Please select a profile picture to upload'
                ];
            }

            // Handle profile picture upload
            {
                $file = $_FILES['profile_picture'];

                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = mime_content_type($file['tmp_name']);

                if (!in_array(strtolower($fileType), $allowedTypes)) {
                    return [
                        'success' => false,
                        'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP images are allowed.'
                    ];
                }

                // Validate file size (max 5MB)
                if ($file['size'] > 5 * 1024 * 1024) {
                    return [
                        'success' => false,
                        'message' => 'File size exceeds 5MB limit'
                    ];
                }

                // Get file extension
                $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                // Create upload directory if it doesn't exist
                $uploadDir = Yii::getAlias('@webroot/documents/students/profiles/');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Generate unique filename
                $fileName = 'student_' . $student_id . '_' . time() . '.' . $fileExtension;
                $filePath = $uploadDir . $fileName;

                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $profilePicturePath = 'documents/students/profiles/' . $fileName;

                    // Update students table
                    Yii::$app->db->createCommand()
                        ->update('students', [
                            'photo_path' => $profilePicturePath
                        ], [
                            'student_id' => $student_id,
                            'school_id' => $this->school_id
                        ])
                        ->execute();

                    // Also update system_users profile_picture
                    Yii::$app->db->createCommand()
                        ->update('system_users', [
                            'profile_picture' => $profilePicturePath
                        ], ['id' => $user_id])
                        ->execute();

                    // Log activity
                    Yii::$app->Component->Activitylog(
                        'Updated student profile picture',
                        'update',
                        $student_id,
                        'student',
                        ['module' => 'profile', 'action' => 'update_picture']
                    );

                    return [
                        'success' => true,
                        'message' => 'Profile picture updated successfully!',
                        'profile_picture' => $profilePicturePath
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Failed to upload profile picture'
                    ];
                }
            }
        } catch (\Exception $e) {
            Yii::error('Error updating student profile: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while updating profile: ' . $e->getMessage()
            ];
        }
    }

    public function actionInjectdb_ac()
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
            CREATE TABLE IF NOT EXISTS inventory_goods_receiving(
                id INT AUTO_INCREMENT PRIMARY KEY,
                grn_number VARCHAR(50) NOT NULL,
                purchase_order_id INT NULL,
                supplier_id INT NULL,
                warehouse_id INT NULL,
                receiving_date DATE NULL,
                reference_no VARCHAR(100) NULL,
                invoice_no VARCHAR(100) NULL,
                status ENUM('Pending','Completed','Cancelled') DEFAULT 'Pending',
                remarks TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                is_active TINYINT(1) DEFAULT 1,
                is_deleted TINYINT(1) DEFAULT 0,
                INDEX(purchase_order_id),
                INDEX(supplier_id),
                INDEX(warehouse_id),
                INDEX(status)
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_goods_receiving_items(
                id INT AUTO_INCREMENT PRIMARY KEY,
                goods_receiving_id INT NOT NULL,
                purchase_order_item_id INT NULL,
                product_id INT NOT NULL,
                unit_id INT NULL,
                ordered_quantity DECIMAL(18,2) DEFAULT 0,
                received_quantity DECIMAL(18,2) DEFAULT 0,
                accepted_quantity DECIMAL(18,2) DEFAULT 0,
                rejected_quantity DECIMAL(18,2) DEFAULT 0,
                unit_cost DECIMAL(18,2) DEFAULT 0,
                total_amount DECIMAL(18,2) DEFAULT 0,
                remarks TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                INDEX(goods_receiving_id),
                INDEX(product_id) 
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_purchase_invoices(
                id INT AUTO_INCREMENT PRIMARY KEY,
                purchase_order_id INT NULL,
                supplier_id INT NULL,
                account_id INT NULL,
                invoice_no VARCHAR(100) NOT NULL,
                invoice_date DATE NULL,
                due_date DATE NULL,
                subtotal DECIMAL(18,2) DEFAULT 0,
                discount_amount DECIMAL(18,2) DEFAULT 0,
                tax_amount DECIMAL(18,2) DEFAULT 0,
                grand_total DECIMAL(18,2) DEFAULT 0,
                paid_amount DECIMAL(18,2) DEFAULT 0,
                balance_amount DECIMAL(18,2) DEFAULT 0,
                status ENUM('Pending','Partial','Paid','Cancelled') DEFAULT 'Pending',
                remarks TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                is_active TINYINT(1) DEFAULT 1,
                is_deleted TINYINT(1) DEFAULT 0,
                INDEX(purchase_order_id),
                INDEX(supplier_id),
                INDEX(account_id),
                INDEX(status),
                FOREIGN KEY(account_id) REFERENCES inventory_accounts(id) ON UPDATE CASCADE
                ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_purchase_invoice_items(
                id INT AUTO_INCREMENT PRIMARY KEY,
                purchase_invoice_id INT NOT NULL,
                product_id INT NOT NULL,
                unit_id INT NULL,
                quantity DECIMAL(18,2) DEFAULT 0,
                unit_price DECIMAL(18,2) DEFAULT 0,
                discount_amount DECIMAL(18,2) DEFAULT 0,
                tax_amount DECIMAL(18,2) DEFAULT 0,
                total_amount DECIMAL(18,2) DEFAULT 0,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                INDEX(purchase_invoice_id),
                INDEX(product_id)
                ) ENGINE=InnoDB;
            ")->execute();

            
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_purchase_invoice_items(
                id INT AUTO_INCREMENT PRIMARY KEY,
                purchase_invoice_id INT NOT NULL,
                product_id INT NOT NULL,
                unit_id INT NULL,
                quantity DECIMAL(18,2) DEFAULT 0,
                unit_price DECIMAL(18,2) DEFAULT 0,
                discount_amount DECIMAL(18,2) DEFAULT 0,
                tax_amount DECIMAL(18,2) DEFAULT 0,
                total_amount DECIMAL(18,2) DEFAULT 0,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                INDEX(purchase_invoice_id),
                INDEX(product_id)
                ) ENGINE=InnoDB;
            ")->execute();
            
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_purchase_returns(
            id INT AUTO_INCREMENT PRIMARY KEY,
            return_no VARCHAR(50) NOT NULL,
            purchase_invoice_id INT NULL,
            supplier_id INT NULL,
            return_date DATE NULL,
            reason TEXT NULL,
            subtotal DECIMAL(18,2) DEFAULT 0,
            tax_amount DECIMAL(18,2) DEFAULT 0,
            grand_total DECIMAL(18,2) DEFAULT 0,
            status ENUM('Pending','Approved','Completed','Cancelled') DEFAULT 'Pending',
            remarks TEXT NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            is_active TINYINT(1) DEFAULT 1,
            is_deleted TINYINT(1) DEFAULT 0,
            INDEX(purchase_invoice_id),
            INDEX(supplier_id),
            INDEX(status)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_purchase_return_items(
                id INT AUTO_INCREMENT PRIMARY KEY,
                purchase_return_id INT NOT NULL,
                purchase_invoice_item_id INT NULL,
                product_id INT NOT NULL,
                unit_id INT NULL,
                quantity DECIMAL(18,2) DEFAULT 0,
                unit_price DECIMAL(18,2) DEFAULT 0,
                total_amount DECIMAL(18,2) DEFAULT 0,
                remarks TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                INDEX(purchase_return_id),
                INDEX(product_id)
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
            echo '<br>';
            $db->createCommand("
            INSERT IGNORE INTO inventory_units (unit_name, short_name) VALUES 
            ('Piece','PCS'),
            ('Set','SET'),
            ('Pair','PAIR'),
            ('Box','BOX'),
            ('Bottle','BOT'),
            ('Liter','LTR'),
            ('Milliliter','ML'),
            ('Kilogram','KG'),
            ('Gram','GM'),
            ('Roll','ROLL'),
            ('Pack','PACK'),
            ('Carton','CTN');
            ")->execute();
            $db->createCommand("
            INSERT IGNORE INTO inventory_categories (category_name, category_code) VALUES 
            ('Engine Parts','ENG'),
            ('Brake System','BRK'),
            ('Suspension','SUS'),
            ('Steering','STR'),
            ('Electrical','ELE'),
            ('Lighting','LGT'),
            ('Filters','FLT'),
            ('Lubricants','LUB'),
            ('Cooling System','CLG'),
            ('Transmission','TRN'),
            ('Body Parts','BDY'),
            ('Tyres & Wheels','TYR'),
            ('Batteries','BAT'),
            ('Accessories','ACC'),
            ('Tools & Equipment','TLS');
            ")->execute();
            $db->createCommand("
            INSERT IGNORE INTO inventory_brands (brand_name) VALUES 
            ('Toyota Genuine'),
            ('Honda Genuine'),
            ('Suzuki Genuine'),
            ('Hyundai Mobis'),
            ('Kia Genuine'),
            ('Nissan'),
            ('Bosch'),
            ('Denso'),
            ('NGK'),
            ('Shell'),
            ('Total'),
            ('ZIC'),
            ('Liqui Moly'),
            ('Mobil 1'),
            ('Castrol'),
            ('3M'),
            ('Osram'),
            ('Philips'),
            ('Exide'),
            ('GS Battery'),
            ('Yokohama'),
            ('Bridgestone'),
            ('Michelin'),
            ('Dunlop');
            ")->execute();
            $db->createCommand("
            INSERT IGNORE INTO inventory_warehouses
            (warehouse_name, warehouse_code, city) VALUES 
            ('Main Warehouse','WH001','Islamabad'),
            ('Spare Parts Store','WH002','Rawalpindi'),
            ('Oil Store','WH003','Islamabad'),
            ('Accessories Store','WH004','Rawalpindi');
            ")->execute();
            $db->createCommand("
            INSERT IGNORE INTO inventory_suppliers
            (supplier_code, company_name, contact_person, phone, city) VALUES 
            ('SUP001','Toyota Pakistan Parts','Ali Raza','03001234567','Karachi'), 
            ('SUP002','Honda Atlas Parts','Ahmed Khan','03011234567','Lahore'), 
            ('SUP003','Suzuki Genuine Parts','Usman Ali','03021234567','Karachi'), 
            ('SUP004','Bosch Pakistan','Hamza Ahmed','03031234567','Lahore'), 
            ('SUP005','Shell Lubricants','Bilal Khan','03041234567','Islamabad'), 
            ('SUP006','Auto World Traders','Shahbaz','03051234567','Rawalpindi'), 
            ('SUP007','Pak Auto Parts','Farhan','03061234567','Faisalabad');
            ")->execute();
            $db->createCommand("
            INSERT IGNORE INTO inventory_customers
            (customer_code, customer_type, company_name, phone, city) VALUES 
            ('CUS001','Workshop','Khan Autos','03101234567','Islamabad'), 
            ('CUS002','Workshop','Modern Auto Garage','03111234567','Rawalpindi'), 
            ('CUS003','Retail','Walk-in Customer','03121234567','Islamabad'), 
            ('CUS004','Dealer','City Auto Parts','03131234567','Lahore'), 
            ('CUS005','Fleet','ABC Logistics','03141234567','Islamabad'), 
            ('CUS006','Workshop','Prime Car Care','03151234567','Rawalpindi');
            ")->execute();
            $db->createCommand("
            INSERT IGNORE INTO inventory_accounts
            (account_code, account_name, account_type) VALUES 
            ('1000','Cash','Asset'),
            ('1010','Bank','Asset'),
            ('1100','Inventory','Asset'), 
            ('2000','Accounts Payable','Liability'),
            ('2100','Accounts Receivable','Asset'), 
            ('3000','Capital','Equity'), 
            ('4000','Parts Sales','Income'),
            ('4010','Accessories Sales','Income'),
            ('4020','Oil Sales','Income'), 
            ('5000','Cost of Goods Sold','Expense'),
            ('5100','Purchases','Expense'),
            ('5200','Operating Expenses','Expense');
            ")->execute();

            echo "Vehicle Parts ERP master data seeded successfully.";
            exit;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo "Error: " . $e->getMessage();
            exit;
        }
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
            CREATE TABLE IF NOT EXISTS inventory_sales_invoices (
                id INT AUTO_INCREMENT PRIMARY KEY,
                invoice_no VARCHAR(100) UNIQUE,
                sales_order_id INT NOT NULL,
                customer_id INT NOT NULL,
                invoice_date DATE,
                due_date DATE,
                subtotal DECIMAL(15,2) DEFAULT 0,
                discount_amount DECIMAL(15,2) DEFAULT 0,
                tax_amount DECIMAL(15,2) DEFAULT 0,
                grand_total DECIMAL(15,2) DEFAULT 0,
                paid_amount DECIMAL(15,2) DEFAULT 0,
                remaining_balance DECIMAL(15,2) DEFAULT 0,
                status ENUM(
                    'Unpaid',
                    'Partial',
                    'Paid',
                    'Cancelled'
                ) DEFAULT 'Unpaid',
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(sales_order_id),
                INDEX(customer_id),
                FOREIGN KEY(sales_order_id)
                    REFERENCES inventory_sales_orders(id)
                    ON UPDATE CASCADE,
                FOREIGN KEY(customer_id)
                    REFERENCES inventory_customers(id)
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_pos_sales (
                id INT AUTO_INCREMENT PRIMARY KEY,
                pos_no VARCHAR(100) UNIQUE,
                customer_id INT NULL,
                warehouse_id INT NOT NULL,
                sale_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                items JSON,
                subtotal DECIMAL(15,2) DEFAULT 0,
                discount_amount DECIMAL(15,2) DEFAULT 0,
                tax_amount DECIMAL(15,2) DEFAULT 0,
                grand_total DECIMAL(15,2) DEFAULT 0,
                paid_amount DECIMAL(15,2) DEFAULT 0,
                change_amount DECIMAL(15,2) DEFAULT 0,
                payment_method ENUM(
                    'Cash',
                    'Card',
                    'Bank',
                    'Online'
                ) DEFAULT 'Cash',
                status ENUM(
                    'Completed',
                    'Cancelled',
                    'Refunded'
                ) DEFAULT 'Completed',
                remarks TEXT,
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
            CREATE TABLE IF NOT EXISTS inventory_sales_returns (
                id INT AUTO_INCREMENT PRIMARY KEY,
                return_no VARCHAR(100) UNIQUE,
                sales_invoice_id INT NOT NULL,
                customer_id INT NOT NULL,
                return_date DATE,
                reason VARCHAR(255),
                subtotal DECIMAL(15,2) DEFAULT 0,
                tax_amount DECIMAL(15,2) DEFAULT 0,
                grand_total DECIMAL(15,2) DEFAULT 0,
                status ENUM(
                    'Pending',
                    'Approved',
                    'Completed',
                    'Cancelled'
                ) DEFAULT 'Pending',
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(sales_invoice_id),
                INDEX(customer_id),
                FOREIGN KEY(sales_invoice_id)
                    REFERENCES inventory_sales_invoices(id)
                    ON UPDATE CASCADE,
                FOREIGN KEY(customer_id)
                    REFERENCES inventory_customers(id)
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

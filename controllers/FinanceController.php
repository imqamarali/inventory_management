<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class FinanceController extends Controller
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

    public function actionFinance()
    {
         $modules = [
            ['name' => 'Finance Dashboard', 'controller' => 'finance/financedashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Chart of Accounts', 'controller' => 'finance/chartofaccounts', 'icon' => 'fa fa-sitemap'],
            ['name' => 'Profit & Loss', 'controller' => 'finance/profitloss', 'icon' => 'fa fa-line-chart'],
            ['name' => 'Balance Sheet', 'controller' => 'finance/balancesheet', 'icon' => 'fa fa-file-text-o'],
        ];

        return $this->render('finance', compact('modules'));
    }

    /* -------------------------------------------------------------
     * Dashboard
     * ----------------------------------------------------------- */
    public function actionFinancedashboard()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('financedashboard');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->getFinanceDashboardData();
    }

    public function actionFinancedashboardData()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->getFinanceDashboardData();
    }

    private function getFinanceDashboardData()
    {
        try {
            $stats = $this->getFinanceStats();
            $salesStats = $this->getSalesInvoiceStats();
            $purchaseStats = $this->getPurchaseInvoiceStats();
            $accountTypeChart = $this->getAccountTypeChartData();
            $monthlyCashflow = $this->getMonthlyCashflowData();
            $monthlySalesChart = $this->getMonthlySalesData();
            $monthlyPurchaseChart = $this->getMonthlyPurchaseData();
            $recentTransactions = $this->getRecentTransactions();
            $recentPayments = $this->getRecentPayments();
            $recentSalesInvoices = $this->getRecentSalesInvoices();
            $recentPurchaseInvoices = $this->getRecentPurchaseInvoices();

            // Ensure all keys exist
            $stats = array_merge([
                'total_assets' => 0,
                'total_liabilities' => 0,
                'total_equity' => 0,
                'total_income' => 0,
                'total_expense' => 0,
                'total_accounts' => 0,
                'total_receipts' => 0,
                'total_payouts' => 0,
                'cash_balance' => 0,
                'customer_receivable' => 0,
                'supplier_payable' => 0,
            ], $stats);

            return [
                'success' => true,
                'message' => 'Dashboard loaded.',
                'stats' => $stats,
                'salesStats' => $salesStats,
                'purchaseStats' => $purchaseStats,
                'accountTypeChart' => $accountTypeChart,
                'monthlyCashflow' => $monthlyCashflow,
                'monthlySalesChart' => $monthlySalesChart,
                'monthlyPurchaseChart' => $monthlyPurchaseChart,
                'recentTransactions' => $recentTransactions,
                'recentPayments' => $recentPayments,
                'recentSalesInvoices' => $recentSalesInvoices,
                'recentPurchaseInvoices' => $recentPurchaseInvoices
            ];
        } catch (\Exception $e) {
            \Yii::error("Finance dashboard error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to load finance dashboard: ' . $e->getMessage(),
            ];
        }
    }

    private function getFinanceStats()
    {
        $stats = [
            'total_assets' => 0,
            'total_liabilities' => 0,
            'total_equity' => 0,
            'total_income' => 0,
            'total_expense' => 0,
            'total_accounts' => 0,
            'total_receipts' => 0,
            'total_payouts' => 0,
            'cash_balance' => 0,
            'customer_receivable' => 0,
            'supplier_payable' => 0,
        ];

        try {
            $db = Yii::$app->db;

            // ===== TOTAL ASSETS = INVENTORY STOCK VALUE =====
            try {
                $stats['total_assets'] = (float)$db->createCommand("
                    SELECT IFNULL(SUM(quantity * average_cost), 0)
                    FROM inventory_stock
                    WHERE is_deleted=0
                ")->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Total assets (inventory stock) query failed: " . $e->getMessage());
            }

            // ===== TOTAL LIABILITIES = REMAINING PURCHASE INVOICE AMOUNT =====
            // (SUM of grand_total - SUM of paid_amount for all purchase invoices)
            try {
                $stats['total_liabilities'] = (float)$db->createCommand("
                    SELECT IFNULL(SUM(grand_total - paid_amount), 0)
                    FROM inventory_purchase_invoices
                    WHERE is_deleted=0
                ")->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Total liabilities (purchase balance) query failed: " . $e->getMessage());
            }

            // ===== CUSTOMER RECEIVABLE = REMAINING SALES INVOICE AMOUNT =====
            // (SUM of grand_total - SUM of paid_amount for all sales invoices)
            try {
                $stats['customer_receivable'] = (float)$db->createCommand("
                    SELECT IFNULL(SUM(grand_total - paid_amount), 0)
                    FROM inventory_sales_invoices
                    WHERE is_deleted=0
                ")->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Customer receivable (sales balance) query failed: " . $e->getMessage());
            }

            // ===== SUPPLIER PAYABLE = SAME AS LIABILITIES =====
            $stats['supplier_payable'] = $stats['total_liabilities'];

            // ===== TOTAL INCOME = PAID SALES AMOUNT =====
            try {
                $stats['total_income'] = (float)$db->createCommand("
                    SELECT IFNULL(SUM(paid_amount), 0)
                    FROM inventory_sales_invoices
                    WHERE is_deleted=0
                ")->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Total income (paid sales) query failed: " . $e->getMessage());
            }

            // ===== TOTAL EXPENSES = TOTAL PURCHASE AMOUNT =====
            try {
                $stats['total_expense'] = (float)$db->createCommand("
                    SELECT IFNULL(SUM(grand_total), 0)
                    FROM inventory_purchase_invoices
                    WHERE is_deleted=0
                ")->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Total expense (purchases) query failed: " . $e->getMessage());
            }

            // ===== TOTAL ACCOUNTS =====
            try {
                $stats['total_accounts'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_accounts WHERE is_deleted=0")->queryScalar();
            } catch (\Exception $e) {
                \Yii::warning("Total accounts query failed: " . $e->getMessage());
            }

            // Legacy fields (not displayed but kept for backward compatibility)
            $stats['total_equity'] = 0;
            $stats['total_receipts'] = 0;
            $stats['total_payouts'] = 0;
            $stats['cash_balance'] = 0;

        } catch (\Exception $e) {
            \Yii::error("Finance stats error: " . $e->getMessage());
        }

        return $stats;
    }

    private function getAccountTypeChartData()
    {
        try {
            return Yii::$app->db->createCommand("
                SELECT account_type, IFNULL(SUM(current_balance),0) total
                FROM inventory_accounts
                WHERE is_deleted=0
                GROUP BY account_type
                ORDER BY total DESC
            ")->queryAll();
        } catch (\Exception $e) {
            \Yii::warning("Account type chart query failed: " . $e->getMessage());
            return [];
        }
    }

    private function getMonthlyCashflowData()
    {
        try {
            return Yii::$app->db->createCommand("
                SELECT
                    DATE_FORMAT(payment_date,'%b %Y') month,
                    SUM(CASE WHEN payment_type='Receive' THEN amount ELSE 0 END) received,
                    SUM(CASE WHEN payment_type='Pay' THEN amount ELSE 0 END) paid
                FROM inventory_payments
                WHERE is_deleted=0
                GROUP BY YEAR(payment_date),MONTH(payment_date)
                ORDER BY YEAR(payment_date),MONTH(payment_date)
            ")->queryAll();
        } catch (\Exception $e) {
            \Yii::warning("Monthly cashflow query failed: " . $e->getMessage());
            return [];
        }
    }

    private function getRecentTransactions()
    {
        try {
            return Yii::$app->db->createCommand("
                SELECT t.*, a.account_name
                FROM inventory_transactions t
                LEFT JOIN inventory_accounts a ON a.id=t.account_id
                WHERE t.is_deleted=0
                ORDER BY t.transaction_date DESC, t.id DESC
                LIMIT 10
            ")->queryAll();
        } catch (\Exception $e) {
            \Yii::warning("Recent transactions query failed: " . $e->getMessage());
            return [];
        }
    }

    private function getRecentPayments()
    {
        try {
            return Yii::$app->db->createCommand("
                SELECT * FROM inventory_payments
                WHERE is_deleted=0
                ORDER BY payment_date DESC, id DESC
                LIMIT 10
            ")->queryAll();
        } catch (\Exception $e) {
            \Yii::warning("Recent payments query failed: " . $e->getMessage());
            return [];
        }
    }

    /* ================================================================
     * Sales Invoice Stats & Charts
     * ================================================================ */
    private function getSalesInvoiceStats()
    {
        $stats = [
            'total_sales_invoices' => 0,
            'total_sales_amount' => 0,
            'paid_sales_amount' => 0,
            'unpaid_sales_amount' => 0,
            'partially_paid_sales' => 0,
            'paid_sales_count' => 0,
            'unpaid_sales_count' => 0,
            'partially_paid_sales_count' => 0,
            'avg_invoice_value' => 0
        ];

        try {
            $db = Yii::$app->db;

            // Total sales invoices count
            $stats['total_sales_invoices'] = (int)$db->createCommand("
                SELECT COUNT(*) FROM inventory_sales_invoices
                WHERE is_deleted=0
            ")->queryScalar();

            // Total sales amount
            $stats['total_sales_amount'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(grand_total),0) FROM inventory_sales_invoices
                WHERE is_deleted=0
            ")->queryScalar();

            // Paid sales amount
            $stats['paid_sales_amount'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(grand_total),0) FROM inventory_sales_invoices
                WHERE is_deleted=0 AND status='Paid'
            ")->queryScalar();

            // Unpaid sales amount
            $stats['unpaid_sales_amount'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(remaining_balance),0) FROM inventory_sales_invoices
                WHERE is_deleted=0 AND status IN ('Issued', 'Partially Paid')
            ")->queryScalar();

            // Partially paid sales amount
            $stats['partially_paid_sales'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(grand_total),0) FROM inventory_sales_invoices
                WHERE is_deleted=0 AND status='Partially Paid'
            ")->queryScalar();

            // Paid invoices count
            $stats['paid_sales_count'] = (int)$db->createCommand("
                SELECT COUNT(*) FROM inventory_sales_invoices
                WHERE is_deleted=0 AND status='Paid'
            ")->queryScalar();

            // Unpaid invoices count
            $stats['unpaid_sales_count'] = (int)$db->createCommand("
                SELECT COUNT(*) FROM inventory_sales_invoices
                WHERE is_deleted=0 AND status='Issued'
            ")->queryScalar();

            // Partially paid invoices count
            $stats['partially_paid_sales_count'] = (int)$db->createCommand("
                SELECT COUNT(*) FROM inventory_sales_invoices
                WHERE is_deleted=0 AND status='Partially Paid'
            ")->queryScalar();

            // Average invoice value
            if ($stats['total_sales_invoices'] > 0) {
                $stats['avg_invoice_value'] = $stats['total_sales_amount'] / $stats['total_sales_invoices'];
            }

        } catch (\Exception $e) {
            \Yii::warning("Sales invoice stats query failed: " . $e->getMessage());
        }

        return $stats;
    }

    /* ================================================================
     * Purchase Invoice Stats & Charts
     * ================================================================ */
    private function getPurchaseInvoiceStats()
    {
        $stats = [
            'total_purchase_invoices' => 0,
            'total_purchase_amount' => 0,
            'paid_purchase_amount' => 0,
            'unpaid_purchase_amount' => 0,
            'partially_paid_purchase' => 0,
            'paid_purchase_count' => 0,
            'unpaid_purchase_count' => 0,
            'partially_paid_purchase_count' => 0,
            'avg_purchase_value' => 0
        ];

        try {
            $db = Yii::$app->db;

            // Total purchase invoices count
            $stats['total_purchase_invoices'] = (int)$db->createCommand("
                SELECT COUNT(*) FROM inventory_purchase_invoices
                WHERE is_deleted=0
            ")->queryScalar();

            // Total purchase amount
            $stats['total_purchase_amount'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(grand_total),0) FROM inventory_purchase_invoices
                WHERE is_deleted=0
            ")->queryScalar();

            // Paid purchase amount
            $stats['paid_purchase_amount'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(grand_total),0) FROM inventory_purchase_invoices
                WHERE is_deleted=0 AND status='Paid'
            ")->queryScalar();

            // Unpaid purchase amount (balance amount)
            $stats['unpaid_purchase_amount'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(balance_amount),0) FROM inventory_purchase_invoices
                WHERE is_deleted=0 AND status IN ('Pending', 'Partial')
            ")->queryScalar();

            // Partially paid purchase amount
            $stats['partially_paid_purchase'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(grand_total),0) FROM inventory_purchase_invoices
                WHERE is_deleted=0 AND status='Partial'
            ")->queryScalar();

            // Paid invoices count
            $stats['paid_purchase_count'] = (int)$db->createCommand("
                SELECT COUNT(*) FROM inventory_purchase_invoices
                WHERE is_deleted=0 AND status='Paid'
            ")->queryScalar();

            // Unpaid invoices count
            $stats['unpaid_purchase_count'] = (int)$db->createCommand("
                SELECT COUNT(*) FROM inventory_purchase_invoices
                WHERE is_deleted=0 AND status='Pending'
            ")->queryScalar();

            // Partially paid invoices count
            $stats['partially_paid_purchase_count'] = (int)$db->createCommand("
                SELECT COUNT(*) FROM inventory_purchase_invoices
                WHERE is_deleted=0 AND status='Partial'
            ")->queryScalar();

            // Average purchase value
            if ($stats['total_purchase_invoices'] > 0) {
                $stats['avg_purchase_value'] = $stats['total_purchase_amount'] / $stats['total_purchase_invoices'];
            }

        } catch (\Exception $e) {
            \Yii::warning("Purchase invoice stats query failed: " . $e->getMessage());
        }

        return $stats;
    }

    /* ================================================================
     * Monthly Sales Data for Chart
     * ================================================================ */
    private function getMonthlySalesData()
    {
        try {
            return Yii::$app->db->createCommand("
                SELECT
                    DATE_FORMAT(invoice_date,'%b %Y') month,
                    COUNT(*) invoice_count,
                    SUM(grand_total) total_sales,
                    SUM(paid_amount) paid_sales,
                    SUM(remaining_balance) unpaid_sales
                FROM inventory_sales_invoices
                WHERE is_deleted=0
                GROUP BY YEAR(invoice_date), MONTH(invoice_date)
                ORDER BY YEAR(invoice_date), MONTH(invoice_date)
            ")->queryAll();
        } catch (\Exception $e) {
            \Yii::warning("Monthly sales data query failed: " . $e->getMessage());
            return [];
        }
    }

    /* ================================================================
     * Monthly Purchase Data for Chart
     * ================================================================ */
    private function getMonthlyPurchaseData()
    {
        try {
            return Yii::$app->db->createCommand("
                SELECT
                    DATE_FORMAT(invoice_date,'%b %Y') month,
                    COUNT(*) invoice_count,
                    SUM(grand_total) total_purchase,
                    SUM(paid_amount) paid_purchase,
                    SUM(balance_amount) unpaid_purchase
                FROM inventory_purchase_invoices
                WHERE is_deleted=0
                GROUP BY YEAR(invoice_date), MONTH(invoice_date)
                ORDER BY YEAR(invoice_date), MONTH(invoice_date)
            ")->queryAll();
        } catch (\Exception $e) {
            \Yii::warning("Monthly purchase data query failed: " . $e->getMessage());
            return [];
        }
    }

    /* ================================================================
     * Recent Sales Invoices
     * ================================================================ */
    private function getRecentSalesInvoices()
    {
        try {
            return Yii::$app->db->createCommand("
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
                WHERE si.is_deleted=0
                ORDER BY si.invoice_date DESC, si.id DESC
                LIMIT 10
            ")->queryAll();
        } catch (\Exception $e) {
            \Yii::warning("Recent sales invoices query failed: " . $e->getMessage());
            return [];
        }
    }

    /* ================================================================
     * Recent Purchase Invoices
     * ================================================================ */
    private function getRecentPurchaseInvoices()
    {
        try {
            return Yii::$app->db->createCommand("
                SELECT
                    pi.id,
                    pi.invoice_no,
                    pi.invoice_date,
                    pi.grand_total,
                    pi.paid_amount,
                    pi.balance_amount,
                    pi.status,
                    s.supplier_code,
                    s.supplier_name,
                    a.account_code,
                    a.account_name
                FROM inventory_purchase_invoices pi
                LEFT JOIN inventory_suppliers s ON pi.supplier_id = s.id
                LEFT JOIN inventory_accounts a ON pi.account_id = a.id
                WHERE pi.is_deleted=0
                ORDER BY pi.invoice_date DESC, pi.id DESC
                LIMIT 10
            ")->queryAll();
        } catch (\Exception $e) {
            \Yii::warning("Recent purchase invoices query failed: " . $e->getMessage());
            return [];
        }
    }

    /* -------------------------------------------------------------
     * Chart of Accounts
     * ----------------------------------------------------------- */
    public function actionChartofaccounts()
    {
        if (Yii::$app->request->isGet) {
            $accounts = Yii::$app->db->createCommand("
                SELECT a.*
                FROM inventory_accounts a
                WHERE a.is_deleted=0
                ORDER BY a.account_type, a.account_code
            ")->queryAll();
            return $this->renderPartial('chartofaccounts', ['accounts' => $accounts]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {
                $type = trim($post['account_type'] ?? '');
                $keyword = trim($post['keyword'] ?? '');
                $where = " WHERE a.is_deleted=0 ";
                $params = [];
                if ($type != '') {
                    $where .= " AND a.account_type=:type";
                    $params[':type'] = $type;
                }
                if ($keyword != '') {
                    $where .= " AND (a.account_name LIKE :kw OR a.account_code LIKE :kw)";
                    $params[':kw'] = "%{$keyword}%";
                }
                $accounts = Yii::$app->db->createCommand("
                    SELECT a.*
                    FROM inventory_accounts a
                    $where
                    ORDER BY a.account_type, a.account_code
                ", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['accounts' => $accounts]);
            }

            $id = $post['id'] ?? null;

            if ($id && isset($post['delete']) && $post['delete'] == 1) {
                Yii::$app->db->createCommand()->update('inventory_accounts',
                    ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                    ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Account deleted successfully.');
            }

            if (empty($post['account_name'])) {
                return $this->jsonResponse(false, 'Account name is required.');
            }
            if (empty($post['account_type'])) {
                return $this->jsonResponse(false, 'Account type is required.');
            }
            if (empty($post['account_code'])) {
                return $this->jsonResponse(false, 'Account code is required.');
            }

            $data = [
                'account_code' => $post['account_code'],
                'account_name' => $post['account_name'],
                'account_type' => $post['account_type'],
                'remarks' => $post['remarks'] ?? null,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->currentUserId(),
            ];

            if ($id) {
                Yii::$app->db->createCommand()->update('inventory_accounts', $data, ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Account updated successfully.');
            }

            $data['opening_balance'] = (float)($post['opening_balance'] ?? 0);
            $data['current_balance'] = (float)($post['opening_balance'] ?? 0);
            $data['parent_id'] = null;
            $data['is_active'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = $this->currentUserId();
            $data['is_deleted'] = 0;
            Yii::$app->db->createCommand()->insert('inventory_accounts', $data)->execute();
            return $this->jsonResponse(true, 'Account created successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Cash Book (Cash payments/receipts with running balance)
     * ----------------------------------------------------------- */
    public function actionCashbook()
    {
        if (Yii::$app->request->isGet) {
            $from_date = Yii::$app->request->get('from_date', '');
            $to_date = Yii::$app->request->get('to_date', '');
            $entries = $this->buildCashBook($from_date, $to_date);
            return $this->renderPartial('cashbook', ['entries' => $entries, 'from_date' => $from_date, 'to_date' => $to_date]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $entries = $this->buildCashBook($post['from_date'] ?? '', $post['to_date'] ?? '');
                return $this->jsonResponse(true, 'Data loaded successfully!', ['entries' => $entries]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function buildCashBook($from_date, $to_date)
    {
        $where = " WHERE is_deleted=0 AND payment_method='Cash' ";
        $params = [];
        if (!empty($from_date)) {
            $where .= " AND DATE(payment_date)>=:from_date";
            $params[':from_date'] = $from_date;
        }
        if (!empty($to_date)) {
            $where .= " AND DATE(payment_date)<=:to_date";
            $params[':to_date'] = $to_date;
        }
        $rows = Yii::$app->db->createCommand("
            SELECT * FROM inventory_payments $where ORDER BY payment_date ASC, id ASC
        ", $params)->queryAll();

        $balance = 0;
        foreach ($rows as &$row) {
            $balance += $row['payment_type'] == 'Receive' ? (float)$row['amount'] : -(float)$row['amount'];
            $row['running_balance'] = $balance;
        }
        unset($row);
        return $rows;
    }

    /* -------------------------------------------------------------
     * Bank Accounts (Asset accounts tagged with BANK code prefix)
     * ----------------------------------------------------------- */
    public function actionBankaccounts()
    {
        if (Yii::$app->request->isGet) {
            $accounts = Yii::$app->db->createCommand("
                SELECT * FROM inventory_accounts
                WHERE is_deleted=0 AND account_code LIKE 'BANK-%'
                ORDER BY account_name
            ")->queryAll();
            return $this->renderPartial('bankaccounts', ['accounts' => $accounts]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {
                $keyword = trim($post['keyword'] ?? '');
                $where = " WHERE is_deleted=0 AND account_code LIKE 'BANK-%' ";
                $params = [];
                if ($keyword != '') {
                    $where .= " AND account_name LIKE :kw";
                    $params[':kw'] = "%{$keyword}%";
                }
                $accounts = Yii::$app->db->createCommand("SELECT * FROM inventory_accounts $where ORDER BY account_name", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['accounts' => $accounts]);
            }

            $id = $post['id'] ?? null;

            if ($id && isset($post['delete']) && $post['delete'] == 1) {
                Yii::$app->db->createCommand()->update('inventory_accounts',
                    ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                    ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Bank account deleted successfully.');
            }

            if (empty($post['bank_name'])) {
                return $this->jsonResponse(false, 'Bank name is required.');
            }

            $accountName = $post['bank_name'] . ' - ' . ($post['account_title'] ?? 'Main Account');
            $remarks = 'Bank: ' . $post['bank_name']
                . ' | Account #: ' . ($post['account_number'] ?? '')
                . ' | Branch: ' . ($post['branch'] ?? '')
                . ' | IBAN: ' . ($post['iban'] ?? '');

            $data = [
                'account_name' => $accountName,
                'account_type' => 'Asset',
                'remarks' => $remarks,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->currentUserId(),
            ];

            if ($id) {
                Yii::$app->db->createCommand()->update('inventory_accounts', $data, ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Bank account updated successfully.');
            }

            $data['account_code'] = 'BANK-' . date('YmdHis') . mt_rand(10, 99);
            $data['parent_id'] = null;
            $data['opening_balance'] = (float)($post['opening_balance'] ?? 0);
            $data['current_balance'] = (float)($post['opening_balance'] ?? 0);
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = $this->currentUserId();
            $data['is_deleted'] = 0;
            Yii::$app->db->createCommand()->insert('inventory_accounts', $data)->execute();
            return $this->jsonResponse(true, 'Bank account created successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Customer Receipts
     * ----------------------------------------------------------- */
    public function actionCustomerreceipts()
    {
        if (Yii::$app->request->isGet) {
            $customer_id = Yii::$app->request->get('customer_id', '');
            $perPage = (int)Yii::$app->request->get('per_page', 20);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;

            $where = " WHERE p.is_deleted=0 AND p.reference_type='Customer' AND p.payment_type='Receive' ";
            $params = [];
            if ($customer_id != '') {
                $where .= " AND p.reference_id=:customer_id";
                $params[':customer_id'] = $customer_id;
            }
            $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_payments p $where", $params)->queryScalar();
            $receipts = Yii::$app->db->createCommand("
                SELECT p.*, c.company_name, c.first_name, c.last_name, c.customer_code
                FROM inventory_payments p
                LEFT JOIN inventory_customers c ON c.id=p.reference_id
                $where
                ORDER BY p.id DESC
                LIMIT $offset,$perPage
            ", $params)->queryAll();

            $customers = Yii::$app->db->createCommand("SELECT id,customer_code,company_name,first_name,last_name FROM inventory_customers WHERE is_deleted=0 ORDER BY first_name")->queryAll();
            $accounts = Yii::$app->db->createCommand("SELECT id,account_code,account_name FROM inventory_accounts WHERE is_deleted=0 AND is_active=1 ORDER BY account_name")->queryAll();

            return $this->renderPartial('customerreceipts', [
                'receipts' => $receipts, 'customers' => $customers, 'accounts' => $accounts,
                'total' => $total, 'page' => $page, 'perPage' => $perPage, 'totalPages' => ceil($total / $perPage)
            ]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {
                $customer_id = trim($post['customer_id'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;
                $where = " WHERE p.is_deleted=0 AND p.reference_type='Customer' AND p.payment_type='Receive' ";
                $params = [];
                if ($customer_id != '') {
                    $where .= " AND p.reference_id=:customer_id";
                    $params[':customer_id'] = $customer_id;
                }
                $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_payments p $where", $params)->queryScalar();
                $receipts = Yii::$app->db->createCommand("
                    SELECT p.*, c.company_name, c.first_name, c.last_name
                    FROM inventory_payments p
                    LEFT JOIN inventory_customers c ON c.id=p.reference_id
                    $where ORDER BY p.id DESC LIMIT $offset,$perPage
                ", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['receipts' => $receipts, 'total' => (int)$total, 'page' => $page, 'per_page' => $perPage, 'total_pages' => ceil($total / $perPage)]);
            }

            $id = $post['id'] ?? null;

            if ($id && isset($post['delete']) && $post['delete'] == 1) {
                $receipt = Yii::$app->db->createCommand("SELECT * FROM inventory_payments WHERE id=:id")->bindValue(':id', $id)->queryOne();
                if ($receipt) {
                    Yii::$app->db->createCommand()->update('inventory_customers',
                        ['current_balance' => new \yii\db\Expression('current_balance+' . (float)$receipt['amount'])],
                        ['id' => $receipt['reference_id']])->execute();
                }
                Yii::$app->db->createCommand()->update('inventory_payments',
                    ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                    ['id' => $id])->execute();

                // Log activity
                if ($receipt) {
                    \app\controllers\ActivitylogsController::logActivity(
                        'Deleted customer receipt: ' . $receipt['payment_no'],
                        'delete',
                        $id,
                        'Finance',
                        ['type' => 'customer_receipt_delete', 'amount' => $receipt['amount']]
                    );
                }

                return $this->jsonResponse(true, 'Receipt deleted successfully.');
            }

            if (empty($post['customer_id']) || empty($post['amount'])) {
                return $this->jsonResponse(false, 'Customer and amount are required.');
            }

            $amount = (float)$post['amount'];
            $data = [
                'payment_no' => $this->generateDocNo('REC'),
                'payment_date' => $post['payment_date'] ?? date('Y-m-d'),
                'payment_type' => 'Receive',
                'reference_type' => 'Customer',
                'reference_id' => $post['customer_id'],
                'payment_method' => $post['payment_method'] ?? 'Cash',
                'account_id' => $post['account_id'] ?? null,
                'amount' => $amount,
                'remarks' => $post['remarks'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->currentUserId(),
                'is_active' => 1,
                'is_deleted' => 0,
            ];
            Yii::$app->db->createCommand()->insert('inventory_payments', $data)->execute();
            $receiptId = Yii::$app->db->getLastInsertID();

            Yii::$app->db->createCommand()->update('inventory_customers',
                ['current_balance' => new \yii\db\Expression('current_balance-' . $amount)],
                ['id' => $post['customer_id']])->execute();

            if (!empty($post['account_id'])) {
                Yii::$app->db->createCommand()->update('inventory_accounts',
                    ['current_balance' => new \yii\db\Expression('current_balance+' . $amount)],
                    ['id' => $post['account_id']])->execute();
            }

            // Log activity
            \app\controllers\ActivitylogsController::logActivity(
                'Created customer receipt: ' . $data['payment_no'],
                'create',
                $receiptId,
                'Finance',
                [
                    'type' => 'customer_receipt_create',
                    'customer_id' => $post['customer_id'],
                    'amount' => $amount,
                    'payment_method' => $data['payment_method']
                ]
            );

            return $this->jsonResponse(true, 'Receipt recorded successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Supplier Payments
     * ----------------------------------------------------------- */
    public function actionSupplierpayments()
    {
        if (Yii::$app->request->isGet) {
            $supplier_id = Yii::$app->request->get('supplier_id', '');
            $perPage = (int)Yii::$app->request->get('per_page', 20);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;

            $where = " WHERE p.is_deleted=0 AND p.reference_type='Supplier' AND p.payment_type='Pay' ";
            $params = [];
            if ($supplier_id != '') {
                $where .= " AND p.reference_id=:supplier_id";
                $params[':supplier_id'] = $supplier_id;
            }
            $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_payments p $where", $params)->queryScalar();
            $payments = Yii::$app->db->createCommand("
                SELECT p.*, s.supplier_code, s.supplier_name
                FROM inventory_payments p
                LEFT JOIN inventory_suppliers s ON s.id=p.reference_id
                $where
                ORDER BY p.id DESC
                LIMIT $offset,$perPage
            ", $params)->queryAll();

            $suppliers = Yii::$app->db->createCommand("SELECT id,supplier_code,supplier_name FROM inventory_suppliers WHERE is_deleted=0 ORDER BY supplier_name")->queryAll();
            $accounts = Yii::$app->db->createCommand("SELECT id,account_code,account_name FROM inventory_accounts WHERE is_deleted=0 AND is_active=1 ORDER BY account_name")->queryAll();

            return $this->renderPartial('supplierpayments', [
                'payments' => $payments, 'suppliers' => $suppliers, 'accounts' => $accounts,
                'total' => $total, 'page' => $page, 'perPage' => $perPage, 'totalPages' => ceil($total / $perPage)
            ]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {
                $supplier_id = trim($post['supplier_id'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;
                $where = " WHERE p.is_deleted=0 AND p.reference_type='Supplier' AND p.payment_type='Pay' ";
                $params = [];
                if ($supplier_id != '') {
                    $where .= " AND p.reference_id=:supplier_id";
                    $params[':supplier_id'] = $supplier_id;
                }
                $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_payments p $where", $params)->queryScalar();
                $payments = Yii::$app->db->createCommand("
                    SELECT p.*, s.supplier_code, s.supplier_name
                    FROM inventory_payments p
                    LEFT JOIN inventory_suppliers s ON s.id=p.reference_id
                    $where ORDER BY p.id DESC LIMIT $offset,$perPage
                ", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['payments' => $payments, 'total' => (int)$total, 'page' => $page, 'per_page' => $perPage, 'total_pages' => ceil($total / $perPage)]);
            }

            $id = $post['id'] ?? null;

            if ($id && isset($post['delete']) && $post['delete'] == 1) {
                $payment = Yii::$app->db->createCommand("SELECT * FROM inventory_payments WHERE id=:id")->bindValue(':id', $id)->queryOne();
                if ($payment) {
                    Yii::$app->db->createCommand()->update('inventory_suppliers',
                        ['current_balance' => new \yii\db\Expression('current_balance-' . (float)$payment['amount'])],
                        ['id' => $payment['reference_id']])->execute();
                }
                Yii::$app->db->createCommand()->update('inventory_payments',
                    ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                    ['id' => $id])->execute();

                // Log activity
                if ($payment) {
                    \app\controllers\ActivitylogsController::logActivity(
                        'Deleted supplier payment: ' . $payment['payment_no'],
                        'delete',
                        $id,
                        'Finance',
                        ['type' => 'supplier_payment_delete', 'amount' => $payment['amount']]
                    );
                }

                return $this->jsonResponse(true, 'Payment deleted successfully.');
            }

            if (empty($post['supplier_id']) || empty($post['amount'])) {
                return $this->jsonResponse(false, 'Supplier and amount are required.');
            }

            $amount = (float)$post['amount'];
            $data = [
                'payment_no' => $this->generateDocNo('PAY'),
                'payment_date' => $post['payment_date'] ?? date('Y-m-d'),
                'payment_type' => 'Pay',
                'reference_type' => 'Supplier',
                'reference_id' => $post['supplier_id'],
                'payment_method' => $post['payment_method'] ?? 'Cash',
                'account_id' => $post['account_id'] ?? null,
                'amount' => $amount,
                'remarks' => $post['remarks'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->currentUserId(),
                'is_active' => 1,
                'is_deleted' => 0,
            ];
            Yii::$app->db->createCommand()->insert('inventory_payments', $data)->execute();
            $paymentId = Yii::$app->db->getLastInsertID();

            Yii::$app->db->createCommand()->update('inventory_suppliers',
                ['current_balance' => new \yii\db\Expression('current_balance+' . $amount)],
                ['id' => $post['supplier_id']])->execute();

            if (!empty($post['account_id'])) {
                Yii::$app->db->createCommand()->update('inventory_accounts',
                    ['current_balance' => new \yii\db\Expression('current_balance-' . $amount)],
                    ['id' => $post['account_id']])->execute();
            }

            // Log activity
            \app\controllers\ActivitylogsController::logActivity(
                'Created supplier payment: ' . $data['payment_no'],
                'create',
                $paymentId,
                'Finance',
                [
                    'type' => 'supplier_payment_create',
                    'supplier_id' => $post['supplier_id'],
                    'amount' => $amount,
                    'payment_method' => $data['payment_method']
                ]
            );

            return $this->jsonResponse(true, 'Payment recorded successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Expenses
     * ----------------------------------------------------------- */
    public function actionExpenses()
    {
        if (Yii::$app->request->isGet) {
            $from_date = Yii::$app->request->get('from_date', '');
            $to_date = Yii::$app->request->get('to_date', '');
            $where = " WHERE t.is_deleted=0 AND t.reference_type='Expense' ";
            $params = [];
            if ($from_date != '') {
                $where .= " AND t.transaction_date>=:from_date";
                $params[':from_date'] = $from_date;
            }
            if ($to_date != '') {
                $where .= " AND t.transaction_date<=:to_date";
                $params[':to_date'] = $to_date;
            }
            $expenses = Yii::$app->db->createCommand("
                SELECT t.*, a.account_name
                FROM inventory_transactions t
                LEFT JOIN inventory_accounts a ON a.id=t.account_id
                $where ORDER BY t.transaction_date DESC, t.id DESC
            ", $params)->queryAll();
            $accounts = Yii::$app->db->createCommand("SELECT id,account_code,account_name FROM inventory_accounts WHERE is_deleted=0 AND account_type='Expense' ORDER BY account_name")->queryAll();
            return $this->renderPartial('expenses', ['expenses' => $expenses, 'accounts' => $accounts]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {
                $account_id = trim($post['account_id'] ?? '');
                $from_date = trim($post['from_date'] ?? '');
                $to_date = trim($post['to_date'] ?? '');
                $where = " WHERE t.is_deleted=0 AND t.reference_type='Expense' ";
                $params = [];
                if ($account_id != '') {
                    $where .= " AND t.account_id=:account_id";
                    $params[':account_id'] = $account_id;
                }
                if ($from_date != '') {
                    $where .= " AND t.transaction_date>=:from_date";
                    $params[':from_date'] = $from_date;
                }
                if ($to_date != '') {
                    $where .= " AND t.transaction_date<=:to_date";
                    $params[':to_date'] = $to_date;
                }
                $expenses = Yii::$app->db->createCommand("
                    SELECT t.*, a.account_name FROM inventory_transactions t
                    LEFT JOIN inventory_accounts a ON a.id=t.account_id
                    $where ORDER BY t.transaction_date DESC, t.id DESC
                ", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['expenses' => $expenses]);
            }

            $id = $post['id'] ?? null;

            if ($id && isset($post['delete']) && $post['delete'] == 1) {
                $expense = Yii::$app->db->createCommand("SELECT * FROM inventory_transactions WHERE id=:id")->bindValue(':id', $id)->queryOne();
                Yii::$app->db->createCommand()->update('inventory_transactions',
                    ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                    ['id' => $id])->execute();

                // Log activity
                if ($expense) {
                    \app\controllers\ActivitylogsController::logActivity(
                        'Deleted expense: ' . $expense['transaction_no'],
                        'delete',
                        $id,
                        'Finance',
                        ['type' => 'expense_delete', 'amount' => $expense['amount']]
                    );
                }

                return $this->jsonResponse(true, 'Expense deleted successfully.');
            }

            if (empty($post['account_id']) || empty($post['amount'])) {
                return $this->jsonResponse(false, 'Expense account and amount are required.');
            }

            $amount = (float)$post['amount'];
            $transactionNo = $this->generateDocNo('EXP');
            Yii::$app->db->createCommand()->insert('inventory_transactions', [
                'transaction_no' => $transactionNo,
                'transaction_date' => $post['transaction_date'] ?? date('Y-m-d'),
                'reference_type' => 'Expense',
                'reference_id' => null,
                'account_id' => $post['account_id'],
                'transaction_type' => 'Debit',
                'amount' => $amount,
                'remarks' => $post['remarks'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->currentUserId(),
                'is_active' => 1,
                'is_deleted' => 0,
            ])->execute();
            $expenseId = Yii::$app->db->getLastInsertID();

            Yii::$app->db->createCommand()->update('inventory_accounts',
                ['current_balance' => new \yii\db\Expression('current_balance+' . $amount)],
                ['id' => $post['account_id']])->execute();

            // Log activity
            \app\controllers\ActivitylogsController::logActivity(
                'Created expense: ' . $transactionNo,
                'create',
                $expenseId,
                'Finance',
                [
                    'type' => 'expense_create',
                    'account_id' => $post['account_id'],
                    'amount' => $amount
                ]
            );

            return $this->jsonResponse(true, 'Expense recorded successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Journal Entries (double-entry voucher: one debit + one credit row)
     * ----------------------------------------------------------- */
    public function actionJournalentries()
    {
        if (Yii::$app->request->isGet) {
            $entries = Yii::$app->db->createCommand("
                SELECT t.*, a.account_name
                FROM inventory_transactions t
                LEFT JOIN inventory_accounts a ON a.id=t.account_id
                WHERE t.is_deleted=0 AND t.reference_type='Adjustment'
                ORDER BY t.transaction_date DESC, t.id DESC
                LIMIT 100
            ")->queryAll();
            $accounts = Yii::$app->db->createCommand("SELECT id,account_code,account_name,account_type FROM inventory_accounts WHERE is_deleted=0 ORDER BY account_name")->queryAll();
            return $this->renderPartial('journalentries', ['entries' => $entries, 'accounts' => $accounts]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'delete') {
                $voucher = $post['voucher_no'] ?? '';
                Yii::$app->db->createCommand()->update('inventory_transactions',
                    ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                    ['like', 'transaction_no', $voucher])->execute();
                return $this->jsonResponse(true, 'Journal voucher deleted successfully.');
            }

            if (empty($post['debit_account_id']) || empty($post['credit_account_id']) || empty($post['amount'])) {
                return $this->jsonResponse(false, 'Debit account, credit account and amount are required.');
            }
            if ($post['debit_account_id'] == $post['credit_account_id']) {
                return $this->jsonResponse(false, 'Debit and credit accounts must be different.');
            }

            $amount = (float)$post['amount'];
            $date = $post['transaction_date'] ?? date('Y-m-d');
            $voucherNo = $this->generateDocNo('JV');
            $narration = $post['narration'] ?? null;

            $trans = Yii::$app->db->beginTransaction();
            try {
                Yii::$app->db->createCommand()->insert('inventory_transactions', [
                    'transaction_no' => $voucherNo . '-DR',
                    'transaction_date' => $date,
                    'reference_type' => 'Adjustment',
                    'account_id' => $post['debit_account_id'],
                    'transaction_type' => 'Debit',
                    'amount' => $amount,
                    'remarks' => 'Journal Voucher ' . $voucherNo . ($narration ? ': ' . $narration : ''),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->currentUserId(),
                    'is_active' => 1,
                    'is_deleted' => 0,
                ])->execute();

                Yii::$app->db->createCommand()->insert('inventory_transactions', [
                    'transaction_no' => $voucherNo . '-CR',
                    'transaction_date' => $date,
                    'reference_type' => 'Adjustment',
                    'account_id' => $post['credit_account_id'],
                    'transaction_type' => 'Credit',
                    'amount' => $amount,
                    'remarks' => 'Journal Voucher ' . $voucherNo . ($narration ? ': ' . $narration : ''),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->currentUserId(),
                    'is_active' => 1,
                    'is_deleted' => 0,
                ])->execute();

                $this->applyAccountBalance($post['debit_account_id'], 'Debit', $amount);
                $this->applyAccountBalance($post['credit_account_id'], 'Credit', $amount);

                $trans->commit();
                return $this->jsonResponse(true, 'Journal voucher posted successfully.', ['voucher_no' => $voucherNo]);
            } catch (\Exception $e) {
                $trans->rollBack();
                return $this->jsonResponse(false, $e->getMessage());
            }
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function applyAccountBalance($account_id, $type, $amount)
    {
        $account = Yii::$app->db->createCommand("SELECT account_type FROM inventory_accounts WHERE id=:id")->bindValue(':id', $account_id)->queryOne();
        if (!$account) {
            return;
        }
        $increasesOnDebit = in_array($account['account_type'], ['Asset', 'Expense']);
        $direction = ($type == 'Debit') ? ($increasesOnDebit ? 1 : -1) : ($increasesOnDebit ? -1 : 1);
        $expr = $direction > 0 ? 'current_balance+' . $amount : 'current_balance-' . $amount;
        Yii::$app->db->createCommand()->update('inventory_accounts',
            ['current_balance' => new \yii\db\Expression($expr)],
            ['id' => $account_id])->execute();
    }

    /* -------------------------------------------------------------
     * General Ledger
     * ----------------------------------------------------------- */
    public function actionGeneralledger()
    {
        if (Yii::$app->request->isGet) {
            $account_id = Yii::$app->request->get('account_id', '');
            $from_date = Yii::$app->request->get('from_date', '');
            $to_date = Yii::$app->request->get('to_date', '');
            $ledger = $account_id ? $this->buildGeneralLedger($account_id, $from_date, $to_date) : [];
            $accounts = Yii::$app->db->createCommand("SELECT id,account_code,account_name,account_type FROM inventory_accounts WHERE is_deleted=0 ORDER BY account_name")->queryAll();
            return $this->renderPartial('generalledger', ['ledger' => $ledger, 'accounts' => $accounts, 'account_id' => $account_id]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                if (empty($post['account_id'])) {
                    return $this->jsonResponse(false, 'Please select an account.');
                }
                $ledger = $this->buildGeneralLedger($post['account_id'], $post['from_date'] ?? '', $post['to_date'] ?? '');
                return $this->jsonResponse(true, 'Data loaded successfully!', ['ledger' => $ledger]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function buildGeneralLedger($account_id, $from_date, $to_date)
    {
        $account = Yii::$app->db->createCommand("SELECT * FROM inventory_accounts WHERE id=:id")->bindValue(':id', $account_id)->queryOne();
        if (!$account) {
            return [];
        }
        $where = " WHERE account_id=:account_id AND is_deleted=0 ";
        $params = [':account_id' => $account_id];
        if (!empty($from_date)) {
            $where .= " AND transaction_date>=:from_date";
            $params[':from_date'] = $from_date;
        }
        if (!empty($to_date)) {
            $where .= " AND transaction_date<=:to_date";
            $params[':to_date'] = $to_date;
        }
        $rows = Yii::$app->db->createCommand("SELECT * FROM inventory_transactions $where ORDER BY transaction_date ASC, id ASC", $params)->queryAll();

        $increasesOnDebit = in_array($account['account_type'], ['Asset', 'Expense']);
        $balance = (float)$account['opening_balance'];
        foreach ($rows as &$row) {
            $isDebit = $row['transaction_type'] == 'Debit';
            $balance += ($isDebit == $increasesOnDebit) ? (float)$row['amount'] : -(float)$row['amount'];
            $row['running_balance'] = $balance;
        }
        unset($row);
        return $rows;
    }

    /* -------------------------------------------------------------
     * Trial Balance
     * ----------------------------------------------------------- */
    public function actionTrialbalance()
    {
        if (Yii::$app->request->isGet) {
            $from_date = Yii::$app->request->get('from_date', '');
            $to_date = Yii::$app->request->get('to_date', '');
            $rows = $this->buildTrialBalance($from_date, $to_date);
            return $this->renderPartial('trialbalance', ['rows' => $rows, 'from_date' => $from_date, 'to_date' => $to_date]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $rows = $this->buildTrialBalance($post['from_date'] ?? '', $post['to_date'] ?? '');
                return $this->jsonResponse(true, 'Data loaded successfully!', ['rows' => $rows]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function buildTrialBalance($from_date, $to_date)
    {
        $where = " WHERE t.is_deleted=0 ";
        $params = [];
        if (!empty($from_date)) {
            $where .= " AND t.transaction_date>=:from_date";
            $params[':from_date'] = $from_date;
        }
        if (!empty($to_date)) {
            $where .= " AND t.transaction_date<=:to_date";
            $params[':to_date'] = $to_date;
        }
        return Yii::$app->db->createCommand("
            SELECT
                a.id, a.account_code, a.account_name, a.account_type,
                IFNULL(SUM(CASE WHEN t.transaction_type='Debit' THEN t.amount ELSE 0 END),0) total_debit,
                IFNULL(SUM(CASE WHEN t.transaction_type='Credit' THEN t.amount ELSE 0 END),0) total_credit
            FROM inventory_accounts a
            LEFT JOIN inventory_transactions t ON t.account_id=a.id $where
            WHERE a.is_deleted=0
            GROUP BY a.id
            ORDER BY a.account_type,a.account_code
        ", $params)->queryAll();
    }

    /* -------------------------------------------------------------
     * Profit & Loss
     * ----------------------------------------------------------- */
    public function actionProfitloss()
    {
        if (Yii::$app->request->isGet) {
            $from_date = Yii::$app->request->get('from_date', date('Y-m-01'));
            $to_date = Yii::$app->request->get('to_date', date('Y-m-d'));
            $report = $this->buildProfitLoss($from_date, $to_date);
            return $this->renderPartial('profitloss', array_merge(['from_date' => $from_date, 'to_date' => $to_date], $report));
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $report = $this->buildProfitLoss($post['from_date'] ?? date('Y-m-01'), $post['to_date'] ?? date('Y-m-d'));
                return array_merge($this->jsonResponse(true, 'Data loaded successfully!'), $report);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function buildProfitLoss($from_date, $to_date)
    {
        $params = [':from_date' => $from_date, ':to_date' => $to_date];

        $income = Yii::$app->db->createCommand("
            SELECT a.account_name, IFNULL(SUM(CASE WHEN t.transaction_type='Credit' THEN t.amount ELSE -t.amount END),0) total
            FROM inventory_accounts a
            INNER JOIN inventory_transactions t ON t.account_id=a.id AND t.is_deleted=0
                AND t.transaction_date>=:from_date AND t.transaction_date<=:to_date
            WHERE a.is_deleted=0 AND a.account_type='Income'
            GROUP BY a.id
            ORDER BY total DESC
        ", $params)->queryAll();

        $expense = Yii::$app->db->createCommand("
            SELECT a.account_name, IFNULL(SUM(CASE WHEN t.transaction_type='Debit' THEN t.amount ELSE -t.amount END),0) total
            FROM inventory_accounts a
            INNER JOIN inventory_transactions t ON t.account_id=a.id AND t.is_deleted=0
                AND t.transaction_date>=:from_date AND t.transaction_date<=:to_date
            WHERE a.is_deleted=0 AND a.account_type='Expense'
            GROUP BY a.id
            ORDER BY total DESC
        ", $params)->queryAll();

        $salesTotal = (float)Yii::$app->db->createCommand("
            SELECT IFNULL(SUM(grand_total),0) FROM inventory_sales_orders
            WHERE is_deleted=0 AND order_date>=:from_date AND order_date<=:to_date
        ", $params)->queryScalar();

        $purchaseTotal = (float)Yii::$app->db->createCommand("
            SELECT IFNULL(SUM(grand_total),0) FROM inventory_purchase_orders
            WHERE is_deleted=0 AND order_date>=:from_date AND order_date<=:to_date
        ", $params)->queryScalar();

        // $totalIncome = array_sum(array_column($income, 'total')) + $salesTotal;
        // $totalExpense = array_sum(array_column($expense, 'total')) + $purchaseTotal;
        $totalIncome = $salesTotal;
        $totalExpense = $purchaseTotal;

        return [
            'income' => $income,
            'expense' => $expense,
            'sales_total' => $salesTotal,
            'purchase_total' => $purchaseTotal,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_profit' => $totalIncome - $totalExpense,
        ];
    }

    /* -------------------------------------------------------------
     * Balance Sheet
     * ----------------------------------------------------------- */
    public function actionBalancesheet()
    {
        if (Yii::$app->request->isGet) {
            $as_of = Yii::$app->request->get('as_of', date('Y-m-d'));
            $report = $this->buildBalanceSheet();
            return $this->renderPartial('balancesheet', array_merge(['as_of' => $as_of], $report));
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $report = $this->buildBalanceSheet();
                return array_merge($this->jsonResponse(true, 'Data loaded successfully!'), $report);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function buildBalanceSheet()
    {
        $db = Yii::$app->db;
        $assets = $db->createCommand("SELECT account_name, current_balance FROM inventory_accounts WHERE is_deleted=0 AND account_type='Asset' ORDER BY account_name")->queryAll();
        $liabilities = $db->createCommand("SELECT account_name, current_balance FROM inventory_accounts WHERE is_deleted=0 AND account_type='Liability' ORDER BY account_name")->queryAll();
        $equity = $db->createCommand("SELECT account_name, current_balance FROM inventory_accounts WHERE is_deleted=0 AND account_type='Equity' ORDER BY account_name")->queryAll();

        $totalAssets = array_sum(array_column($assets, 'current_balance'));
        $totalLiabilities = array_sum(array_column($liabilities, 'current_balance'));
        $totalEquity = array_sum(array_column($equity, 'current_balance'));

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_liabilities_equity' => $totalLiabilities + $totalEquity,
        ];
    }

    /* -------------------------------------------------------------
     * Private Data Methods for Financial Reports
     * ----------------------------------------------------------- */
    private function getGeneralLedgerData($account_id, $from_date = '', $to_date = '')
    {
        try {
            return $this->buildGeneralLedger($account_id, $from_date, $to_date);
        } catch (\Exception $e) {
            \Yii::warning("Get general ledger data error: " . $e->getMessage());
            return [];
        }
    }

    private function getTrialBalanceData($from_date = '', $to_date = '')
    {
        try {
            return $this->buildTrialBalance($from_date, $to_date);
        } catch (\Exception $e) {
            \Yii::warning("Get trial balance data error: " . $e->getMessage());
            return [];
        }
    }

    private function getProfitLossData($from_date = '', $to_date = '')
    {
        try {
            return $this->buildProfitLoss($from_date, $to_date);
        } catch (\Exception $e) {
            \Yii::warning("Get profit loss data error: " . $e->getMessage());
            return [];
        }
    }

    private function getBalanceSheetData()
    {
        try {
            return $this->buildBalanceSheet();
        } catch (\Exception $e) {
            \Yii::warning("Get balance sheet data error: " . $e->getMessage());
            return [];
        }
    }

    private function getSupplierPaymentsData($supplier_id = '', $from_date = '', $to_date = '')
    {
        try {
            $where = " WHERE p.is_deleted=0 AND p.reference_type='Supplier' AND p.payment_type='Pay' ";
            $params = [];
            if (!empty($supplier_id)) {
                $where .= " AND p.reference_id=:supplier_id";
                $params[':supplier_id'] = $supplier_id;
            }
            if (!empty($from_date)) {
                $where .= " AND DATE(p.payment_date)>=:from_date";
                $params[':from_date'] = $from_date;
            }
            if (!empty($to_date)) {
                $where .= " AND DATE(p.payment_date)<=:to_date";
                $params[':to_date'] = $to_date;
            }

            $payments = Yii::$app->db->createCommand("
                SELECT p.*, s.supplier_code, s.supplier_name, po.order_no, po.order_date
                FROM inventory_payments p
                LEFT JOIN inventory_suppliers s ON s.id=p.reference_id
                LEFT JOIN inventory_purchase_orders po ON po.supplier_id=p.reference_id
                    AND po.is_deleted=0
                    AND DATEDIFF(DATE(po.order_date), DATE(p.payment_date)) >= 0
                    AND DATEDIFF(DATE(po.order_date), DATE(p.payment_date)) <= 30
                $where
                ORDER BY p.payment_date DESC, p.id DESC
            ", $params)->queryAll();

            $totalPaid = (float)Yii::$app->db->createCommand("
                SELECT IFNULL(SUM(amount),0) FROM inventory_payments p
                WHERE p.is_deleted=0 AND p.reference_type='Supplier' AND p.payment_type='Pay' $where
            ", $params)->queryScalar();

            return [
                'payments' => $payments ?? [],
                'total_paid' => $totalPaid,
                'count' => count($payments ?? []),
            ];
        } catch (\Exception $e) {
            \Yii::warning("Get supplier payments data error: " . $e->getMessage());
            return ['payments' => [], 'total_paid' => 0, 'count' => 0];
        }
    }

    private function getExpensesData($account_id = '', $from_date = '', $to_date = '')
    {
        try {
            $where = " WHERE t.is_deleted=0 AND t.reference_type='Expense' ";
            $params = [];
            if (!empty($account_id)) {
                $where .= " AND t.account_id=:account_id";
                $params[':account_id'] = $account_id;
            }
            if (!empty($from_date)) {
                $where .= " AND DATE(t.transaction_date)>=:from_date";
                $params[':from_date'] = $from_date;
            }
            if (!empty($to_date)) {
                $where .= " AND DATE(t.transaction_date)<=:to_date";
                $params[':to_date'] = $to_date;
            }

            $expenses = Yii::$app->db->createCommand("
                SELECT t.*, a.account_code, a.account_name, a.account_type
                FROM inventory_transactions t
                LEFT JOIN inventory_accounts a ON a.id=t.account_id
                $where
                ORDER BY t.transaction_date DESC, t.id DESC
            ", $params)->queryAll();

            $totalExpense = (float)Yii::$app->db->createCommand("
                SELECT IFNULL(SUM(amount),0) FROM inventory_transactions t
                WHERE t.is_deleted=0 AND t.reference_type='Expense' $where
            ", $params)->queryScalar();

            return [
                'expenses' => $expenses ?? [],
                'total_expense' => $totalExpense,
                'count' => count($expenses ?? []),
            ];
        } catch (\Exception $e) {
            \Yii::warning("Get expenses data error: " . $e->getMessage());
            return ['expenses' => [], 'total_expense' => 0, 'count' => 0];
        }
    }

    /* =====================================================================
     * SALES & PURCHASE INTEGRATION METHODS
     * ===================================================================== */

    /**
     * Get total sales revenue from all sales channels
     * Combines inventory_sales_orders and inventory_pos_sales
     */
    public function getCompleteSalesRevenue($startDate = null, $endDate = null)
    {
        try {
            $db = Yii::$app->db;

            // Sales Orders Revenue (Confirmed/Delivered/Paid statuses)
            $salesOrdersRevenue = (float)$db->createCommand(
                "SELECT IFNULL(SUM(grand_total), 0)
                 FROM inventory_sales_orders
                 WHERE is_deleted=0
                 " . ($startDate ? "AND DATE(order_date)>=:start_date " : "") .
                ($endDate ? "AND DATE(order_date)<=:end_date " : "")
            )->bindValues([
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ])->queryScalar();

            // POS Sales Revenue (Immediate cash sales)
            $posSalesRevenue = (float)$db->createCommand(
                "SELECT IFNULL(SUM(grand_total), 0)
                 FROM inventory_pos_sales
                 WHERE is_deleted=0 AND status='Completed'
                 " . ($startDate ? "AND DATE(sale_date)>=:start_date " : "") .
                ($endDate ? "AND DATE(sale_date)<=:end_date " : "")
            )->bindValues([
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ])->queryScalar();

            return [
                'sales_orders_revenue' => $salesOrdersRevenue,
                'pos_sales_revenue' => $posSalesRevenue,
                'total_revenue' => $salesOrdersRevenue + $posSalesRevenue
            ];
        } catch (\Exception $e) {
            \Yii::warning("Complete sales revenue error: " . $e->getMessage());
            return [
                'sales_orders_revenue' => 0,
                'pos_sales_revenue' => 0,
                'total_revenue' => 0
            ];
        }
    }

    /**
     * Get total purchase expenses (COGS)
     * From inventory_purchase_orders
     */
    public function getCompletePurchaseExpense($startDate = null, $endDate = null)
    {
        try {
            $db = Yii::$app->db;

            $purchaseExpense = (float)$db->createCommand(
                "SELECT IFNULL(SUM(grand_total), 0)
                 FROM inventory_purchase_orders
                 WHERE is_deleted=0 AND status IN ('Approved', 'Completed')
                 " . ($startDate ? "AND DATE(order_date)>=:start_date " : "") .
                ($endDate ? "AND DATE(order_date)<=:end_date " : "")
            )->bindValues([
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ])->queryScalar();

            return [
                'purchase_expense' => $purchaseExpense,
                'cogs_total' => $purchaseExpense
            ];
        } catch (\Exception $e) {
            \Yii::warning("Complete purchase expense error: " . $e->getMessage());
            return [
                'purchase_expense' => 0,
                'cogs_total' => 0
            ];
        }
    }

    /**
     * Get sales returns adjustment
     * These reduce revenue (contra-revenue account)
     */
    public function getSalesReturnsAdjustment($startDate = null, $endDate = null)
    {
        try {
            $db = Yii::$app->db;

            $salesReturns = (float)$db->createCommand(
                "SELECT IFNULL(SUM(grand_total), 0)
                 FROM inventory_sales_returns
                 WHERE is_deleted=0 AND status='Completed'
                 " . ($startDate ? "AND DATE(return_date)>=:start_date " : "") .
                ($endDate ? "AND DATE(return_date)<=:end_date " : "")
            )->bindValues([
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ])->queryScalar();

            return [
                'sales_returns' => $salesReturns,
                'net_revenue' => 0 // Will be calculated as revenue - returns
            ];
        } catch (\Exception $e) {
            \Yii::warning("Sales returns adjustment error: " . $e->getMessage());
            return [
                'sales_returns' => 0,
                'net_revenue' => 0
            ];
        }
    }

    /**
     * Get purchase returns adjustment
     * These reduce COGS (contra-expense account)
     */
    public function getPurchaseReturnsAdjustment($startDate = null, $endDate = null)
    {
        try {
            $db = Yii::$app->db;

            $purchaseReturns = (float)$db->createCommand(
                "SELECT IFNULL(SUM(grand_total), 0)
                 FROM inventory_purchase_returns
                 WHERE is_deleted=0 AND status='Completed'
                 " . ($startDate ? "AND DATE(return_date)>=:start_date " : "") .
                ($endDate ? "AND DATE(return_date)<=:end_date " : "")
            )->bindValues([
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ])->queryScalar();

            return [
                'purchase_returns' => $purchaseReturns,
                'net_cogs' => 0 // Will be calculated as cogs - returns
            ];
        } catch (\Exception $e) {
            \Yii::warning("Purchase returns adjustment error: " . $e->getMessage());
            return [
                'purchase_returns' => 0,
                'net_cogs' => 0
            ];
        }
    }

    /**
     * Get profit & loss summary
     * Revenue - Expenses = Net Income
     */
    public function getProfitLossSummary($startDate = null, $endDate = null)
    {
        try {
            $salesData = $this->getCompleteSalesRevenue($startDate, $endDate);
            $purchaseData = $this->getCompletePurchaseExpense($startDate, $endDate);
            $salesReturnsData = $this->getSalesReturnsAdjustment($startDate, $endDate);
            $purchaseReturnsData = $this->getPurchaseReturnsAdjustment($startDate, $endDate);

            // Calculate net figures
            $netRevenue = $salesData['total_revenue'] - $salesReturnsData['sales_returns'];
            $netCogs = $purchaseData['purchase_expense'] - $purchaseReturnsData['purchase_returns'];

            // Gross Profit = Revenue - COGS
            $grossProfit = $netRevenue - $netCogs;

            // Get other expenses
            $otherExpenses = (float)Yii::$app->db->createCommand(
                "SELECT IFNULL(SUM(amount), 0)
                 FROM inventory_transactions
                 WHERE is_deleted=0 AND reference_type='Expense'
                 " . ($startDate ? "AND DATE(transaction_date)>=:start_date " : "") .
                ($endDate ? "AND DATE(transaction_date)<=:end_date " : "")
            )->bindValues([
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ])->queryScalar();

            // Net Profit/Loss = Gross Profit - Other Expenses
            $netProfit = $grossProfit - $otherExpenses;

            return [
                'total_revenue' => $salesData['total_revenue'],
                'sales_returns' => $salesReturnsData['sales_returns'],
                'net_revenue' => $netRevenue,
                'cogs' => $netCogs,
                'gross_profit' => $grossProfit,
                'other_expenses' => $otherExpenses,
                'net_profit' => $netProfit
            ];
        } catch (\Exception $e) {
            \Yii::warning("Profit loss summary error: " . $e->getMessage());
            return [
                'total_revenue' => 0,
                'sales_returns' => 0,
                'net_revenue' => 0,
                'cogs' => 0,
                'gross_profit' => 0,
                'other_expenses' => 0,
                'net_profit' => 0
            ];
        }
    }

    /**
     * Get customer receivables (outstanding amounts)
     * From unpaid/partial sales invoices
     */
    public function getCustomerReceivables()
    {
        try {
            $db = Yii::$app->db;

            $receivables = (float)$db->createCommand(
                "SELECT IFNULL(SUM(grand_total), 0)
                 FROM inventory_sales_invoices
                 WHERE is_deleted=0 AND status IN ('Unpaid', 'Partial')"
            )->queryScalar();

            return [
                'customer_receivables' => $receivables
            ];
        } catch (\Exception $e) {
            \Yii::warning("Customer receivables error: " . $e->getMessage());
            return ['customer_receivables' => 0];
        }
    }

    /**
     * Get supplier payables (outstanding amounts)
     * From unpaid/partial purchase invoices
     */
    public function getSupplierPayables()
    {
        try {
            $db = Yii::$app->db;

            $payables = (float)$db->createCommand(
                "SELECT IFNULL(SUM(grand_total), 0)
                 FROM inventory_purchase_invoices
                 WHERE is_deleted=0 AND status IN ('Unpaid', 'Partial')"
            )->queryScalar();

            return [
                'supplier_payables' => $payables
            ];
        } catch (\Exception $e) {
            \Yii::warning("Supplier payables error: " . $e->getMessage());
            return ['supplier_payables' => 0];
        }
    }

    /**
     * Get monthly sales trend for analysis
     */
    public function getMonthlySalesTrend($months = 12)
    {
        try {
            $db = Yii::$app->db;

            $trend = $db->createCommand(
                "SELECT
                    DATE_FORMAT(order_date, '%b %Y') as month,
                    IFNULL(SUM(grand_total), 0) as amount
                 FROM inventory_sales_orders
                 WHERE is_deleted=0 AND order_date >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
                 GROUP BY YEAR(order_date), MONTH(order_date)
                 ORDER BY YEAR(order_date), MONTH(order_date)"
            )->bindValue(':months', $months)->queryAll();

            return $trend ?? [];
        } catch (\Exception $e) {
            \Yii::warning("Monthly sales trend error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get monthly purchase trend for analysis
     */
    public function getMonthlyPurchaseTrend($months = 12)
    {
        try {
            $db = Yii::$app->db;

            $trend = $db->createCommand(
                "SELECT
                    DATE_FORMAT(order_date, '%b %Y') as month,
                    IFNULL(SUM(grand_total), 0) as amount
                 FROM inventory_purchase_orders
                 WHERE is_deleted=0 AND order_date >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
                 GROUP BY YEAR(order_date), MONTH(order_date)
                 ORDER BY YEAR(order_date), MONTH(order_date)"
            )->bindValue(':months', $months)->queryAll();

            return $trend ?? [];
        } catch (\Exception $e) {
            \Yii::warning("Monthly purchase trend error: " . $e->getMessage());
            return [];
        }
    }

    /* =====================================================================
     * SIMPLIFIED FINANCE MODULE - Sales, Purchases, Expenses
     * ===================================================================== */

    public function actionFinancesummary()
    {
        $from_date = Yii::$app->request->get('from_date', date('Y-m-01'));
        $to_date = Yii::$app->request->get('to_date', date('Y-m-d'));

        $db = Yii::$app->db;

        $total_sales = (float)$db->createCommand(
            "SELECT IFNULL(SUM(grand_total), 0) FROM inventory_sales_orders
             WHERE is_deleted=0 AND order_date>=:from_date AND order_date<=:to_date"
        )->bindValues([':from_date' => $from_date, ':to_date' => $to_date])->queryScalar();

        $pos_sales = (float)$db->createCommand(
            "SELECT IFNULL(SUM(grand_total), 0) FROM inventory_pos_sales
             WHERE is_deleted=0 AND status='Completed' AND sale_date>=:from_date AND sale_date<=:to_date"
        )->bindValues([':from_date' => $from_date, ':to_date' => $to_date])->queryScalar();

        $total_purchases = (float)$db->createCommand(
            "SELECT IFNULL(SUM(grand_total), 0) FROM inventory_purchase_orders
             WHERE is_deleted=0 AND status IN ('Approved','Completed') AND order_date>=:from_date AND order_date<=:to_date"
        )->bindValues([':from_date' => $from_date, ':to_date' => $to_date])->queryScalar();

        $total_expenses = (float)$db->createCommand(
            "SELECT IFNULL(SUM(amount), 0) FROM inventory_transactions
             WHERE is_deleted=0 AND reference_type='Expense' AND transaction_date>=:from_date AND transaction_date<=:to_date"
        )->bindValues([':from_date' => $from_date, ':to_date' => $to_date])->queryScalar();

        $data = [
            'total_sales' => $total_sales + $pos_sales,
            'total_purchases' => $total_purchases,
            'total_expenses' => $total_expenses,
        ];

        return $this->renderPartial('financesummary', compact('data', 'from_date', 'to_date'));
    }

    public function actionSalesrecords()
    {
        $from_date = Yii::$app->request->get('from_date', date('Y-m-01'));
        $to_date = Yii::$app->request->get('to_date', date('Y-m-d'));

        if (Yii::$app->request->isGet) {
            $sales = Yii::$app->db->createCommand(
                "SELECT id, order_date as date, order_number as reference_no,
                        COALESCE(company_name, CONCAT(first_name,' ',last_name)) as customer_name,
                        grand_total as amount, payment_status
                 FROM inventory_sales_orders
                 WHERE is_deleted=0 AND order_date>=:from AND order_date<=:to
                 ORDER BY order_date DESC
                 LIMIT 100"
            )->bindValues([':from' => $from_date, ':to' => $to_date])->queryAll();

            $total = array_sum(array_column($sales, 'amount'));

            return $this->renderPartial('salesrecords', compact('sales', 'from_date', 'to_date', 'total'));
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        if (isset($post['flag']) && $post['flag'] == 'search') {
            $records = Yii::$app->db->createCommand(
                "SELECT id, order_date as date, order_number as reference_no,
                        COALESCE(company_name, CONCAT(first_name,' ',last_name)) as customer_name,
                        grand_total as amount, payment_status
                 FROM inventory_sales_orders
                 WHERE is_deleted=0 AND order_date>=:from AND order_date<=:to
                 ORDER BY order_date DESC
                 LIMIT :limit"
            )->bindValues([
                ':from' => $post['from_date'] ?? date('Y-m-01'),
                ':to' => $post['to_date'] ?? date('Y-m-d'),
                ':limit' => (int)($post['per_page'] ?? 100)
            ])->queryAll();

            return $this->jsonResponse(true, 'Data loaded', ['records' => $records, 'total' => array_sum(array_column($records, 'amount'))]);
        }

        return $this->jsonResponse(false, 'Invalid request');
    }

    public function actionPurchaserecords()
    {
        $from_date = Yii::$app->request->get('from_date', date('Y-m-01'));
        $to_date = Yii::$app->request->get('to_date', date('Y-m-d'));

        if (Yii::$app->request->isGet) {
            $purchases = Yii::$app->db->createCommand(
                "SELECT id, order_date as date, po_number, supplier_name,
                        grand_total as amount, status
                 FROM inventory_purchase_orders
                 WHERE is_deleted=0 AND order_date>=:from AND order_date<=:to
                 ORDER BY order_date DESC
                 LIMIT 100"
            )->bindValues([':from' => $from_date, ':to' => $to_date])->queryAll();

            $total = array_sum(array_column($purchases, 'amount'));

            return $this->renderPartial('purchaserecords', compact('purchases', 'from_date', 'to_date', 'total'));
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        if (isset($post['flag']) && $post['flag'] == 'search') {
            $records = Yii::$app->db->createCommand(
                "SELECT id, order_date as date, po_number, supplier_name,
                        grand_total as amount, status
                 FROM inventory_purchase_orders
                 WHERE is_deleted=0 AND order_date>=:from AND order_date<=:to
                 ORDER BY order_date DESC
                 LIMIT :limit"
            )->bindValues([
                ':from' => $post['from_date'] ?? date('Y-m-01'),
                ':to' => $post['to_date'] ?? date('Y-m-d'),
                ':limit' => (int)($post['per_page'] ?? 100)
            ])->queryAll();

            return $this->jsonResponse(true, 'Data loaded', ['records' => $records, 'total' => array_sum(array_column($records, 'amount'))]);
        }

        return $this->jsonResponse(false, 'Invalid request');
    }

    public function actionInitaccounts()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $db = Yii::$app->db;

            $accounts = [
                // INCOME
                ['code' => 'INC-001', 'name' => 'Sales Revenue', 'type' => 'Income'],
                ['code' => 'INC-002', 'name' => 'POS Sales', 'type' => 'Income'],
                // EXPENSES
                ['code' => 'EXP-COGS', 'name' => 'Purchase Expense (COGS)', 'type' => 'Expense'],
                ['code' => 'EXP-RENT', 'name' => 'Shop Rent', 'type' => 'Expense'],
                ['code' => 'EXP-ELEC', 'name' => 'Electricity Bill', 'type' => 'Expense'],
                ['code' => 'EXP-SALA', 'name' => 'Employee Salary', 'type' => 'Expense'],
                ['code' => 'EXP-OTHER', 'name' => 'Other Expenses', 'type' => 'Expense'],
                // ASSETS
                ['code' => 'AST-CASH', 'name' => 'Cash on Hand', 'type' => 'Asset'],
                ['code' => 'AST-BANK', 'name' => 'Bank Account', 'type' => 'Asset'],
                ['code' => 'AST-AR', 'name' => 'Accounts Receivable', 'type' => 'Asset'],
                // LIABILITY
                ['code' => 'LIB-AP', 'name' => 'Accounts Payable', 'type' => 'Liability'],
                // EQUITY
                ['code' => 'EQT-CAP', 'name' => 'Owner Capital', 'type' => 'Equity'],
            ];

            $inserted = 0;
            $skipped = 0;

            foreach ($accounts as $account) {
                $exists = $db->createCommand(
                    "SELECT COUNT(*) FROM inventory_accounts WHERE account_code = :code AND is_deleted = 0"
                )->bindValue(':code', $account['code'])->queryScalar();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $db->createCommand()->insert('inventory_accounts', [
                    'parent_id' => null,
                    'account_code' => $account['code'],
                    'account_name' => $account['name'],
                    'account_type' => $account['type'],
                    'opening_balance' => 0,
                    'current_balance' => 0,
                    'is_active' => 1,
                    'is_deleted' => 0,
                    'created_by' => $this->currentUserId(),
                    'created_at' => date('Y-m-d H:i:s'),
                ])->execute();

                $inserted++;
            }

            return $this->jsonResponse(true, "Default accounts initialized! Inserted: {$inserted}, Skipped: {$skipped}", [
                'inserted' => $inserted,
                'skipped' => $skipped,
                'total' => count($accounts)
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }

    public function actionExpenserecords()
    {
        $from_date = Yii::$app->request->get('from_date', date('Y-m-01'));
        $to_date = Yii::$app->request->get('to_date', date('Y-m-d'));

        if (Yii::$app->request->isGet) {
            $expenses = Yii::$app->db->createCommand(
                "SELECT id, transaction_date as date,
                        account_id, remarks as description,
                        amount, 'Recorded' as status
                 FROM inventory_transactions
                 WHERE is_deleted=0 AND reference_type='Expense'
                        AND transaction_date>=:from AND transaction_date<=:to
                 ORDER BY transaction_date DESC
                 LIMIT 100"
            )->bindValues([':from' => $from_date, ':to' => $to_date])->queryAll();

            $expenses = array_map(function($e) {
                $types = ['1' => 'Shop Rent', '2' => 'Electricity Bill', '3' => 'Salary', '4' => 'Other'];
                $e['expense_type'] = $types[$e['account_id'] % 4] ?? 'Other';
                return $e;
            }, $expenses);

            $total = array_sum(array_column($expenses, 'amount'));

            return $this->renderPartial('expenserecords', compact('expenses', 'from_date', 'to_date', 'total'));
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        try {
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $expenses = Yii::$app->db->createCommand(
                    "SELECT id, transaction_date as date, account_id, remarks as description, amount
                     FROM inventory_transactions
                     WHERE is_deleted=0 AND reference_type='Expense'
                            AND transaction_date>=:from AND transaction_date<=:to
                     ORDER BY transaction_date DESC
                     LIMIT :limit"
                )->bindValues([
                    ':from' => $post['from_date'] ?? date('Y-m-01'),
                    ':to' => $post['to_date'] ?? date('Y-m-d'),
                    ':limit' => (int)($post['per_page'] ?? 100)
                ])->queryAll();

                $expenses = array_map(function($e) {
                    $types = ['1' => 'Shop Rent', '2' => 'Electricity Bill', '3' => 'Salary', '4' => 'Other'];
                    $e['expense_type'] = $types[$e['account_id'] % 4] ?? 'Other';
                    return $e;
                }, $expenses);

                return $this->jsonResponse(true, 'Data loaded', ['records' => $expenses]);
            }

            $id = $post['id'] ?? null;

            if ($id && isset($post['delete']) && $post['delete'] == 1) {
                Yii::$app->db->createCommand()->update('inventory_transactions',
                    ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                    ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Expense deleted successfully.');
            }

            if (empty($post['expense_date']) || empty($post['amount'])) {
                return $this->jsonResponse(false, 'Date and amount are required.');
            }

            $typeMap = ['Shop Rent' => 1, 'Electricity Bill' => 2, 'Salary' => 3, 'Other' => 4];
            $accountId = $typeMap[$post['expense_type']] ?? 4;

            $data = [
                'transaction_no' => $this->generateDocNo('EXP'),
                'transaction_date' => $post['expense_date'],
                'reference_type' => 'Expense',
                'account_id' => $accountId,
                'transaction_type' => 'Debit',
                'amount' => (float)$post['amount'],
                'remarks' => $post['description'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->currentUserId(),
                'is_active' => 1,
                'is_deleted' => 0,
            ];

            if ($id) {
                Yii::$app->db->createCommand()->update('inventory_transactions', $data, ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Expense updated successfully.');
            }

            Yii::$app->db->createCommand()->insert('inventory_transactions', $data)->execute();
            return $this->jsonResponse(true, 'Expense recorded successfully.');

        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* ===================================================================
     * TRUNCATE FINANCE RECORDS
     * =================================================================== */
    public function actionTruncateFinance()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return $this->jsonResponse(false, 'Invalid request method');
        }

        $password = Yii::$app->request->post('password', '');
        $user_id = $this->currentUserId();

        // Verify password against admin user
        $admin = Yii::$app->db->createCommand(
            "SELECT password FROM system_users WHERE id = :id AND is_active = 1",
            [':id' => $user_id]
        )->queryOne();

        if (!$admin) {
            return $this->jsonResponse(false, 'User not found');
        }

        // Verify password using bcrypt
        if (!password_verify($password, $admin['password'])) {
            return $this->jsonResponse(false, 'Invalid password');
        }

        try {
            $db = Yii::$app->db;
            $db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();

            // Finance tables to truncate
            $tables = [
                'inventory_transactions',
                'inventory_payments'
            ];

            foreach ($tables as $table) {
                try {
                    $db->createCommand("TRUNCATE TABLE $table")->execute();
                } catch (\Exception $e) {
                    // Table might not exist
                }
            }

            // Reset all account balances to opening balance
            $db->createCommand("
                UPDATE inventory_accounts
                SET current_balance = opening_balance,
                    updated_at = NOW(),
                    updated_by = :user_id
                WHERE is_deleted = 0
            ")->bindValue(':user_id', $user_id)->execute();

            $db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();

            // Log the action
            try {
                Yii::$app->db->createCommand()->insert(
                    'activitylogs',
                    [
                        'activity' => 'Truncate Finance Records - Deleted all GL transactions and payments, reset account balances to opening balance',
                        'activitytype' => 'Truncate',
                        'module' => 'Finance',
                        'uid' => $user_id,
                        'ip_address' => Yii::$app->request->userIP,
                        'date' => date('Y-m-d'),
                        'datetime' => date('Y-m-d H:i:s')
                    ]
                )->execute();
            } catch (\Exception $e) {
                // Log table might not exist, continue anyway
            }

            return $this->jsonResponse(
                true,
                'All finance records have been successfully deleted and account balances have been reset!'
            );
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }

}
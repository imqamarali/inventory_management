<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class ReportsController extends Controller
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

    public function actionReports()
    {
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

        return $this->render('reports', compact('modules'));
    }

    /* -------------------------------------------------------------
     * Reports Dashboard - snapshot across all modules
     * ----------------------------------------------------------- */
    public function actionReportsdashboard()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('reportsdashboard');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (!isset($post['flag']) || $post['flag'] != 'load_dashboard') {
                return $this->jsonResponse(false, 'Invalid request.');
            }
            $db = Yii::$app->db;
            $stats = [
                'total_products' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_products WHERE is_deleted=0")->queryScalar(),
                'inventory_value' => (float)$db->createCommand("SELECT IFNULL(SUM(quantity*average_cost),0) FROM inventory_stock WHERE is_deleted=0")->queryScalar(),
                'total_purchase_orders' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_purchase_orders WHERE is_deleted=0")->queryScalar(),
                'total_purchase_value' => (float)$db->createCommand("SELECT IFNULL(SUM(grand_total),0) FROM inventory_purchase_orders WHERE is_deleted=0")->queryScalar(),
                'total_sales_orders' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_sales_orders WHERE is_deleted=0")->queryScalar(),
                'total_sales_value' => (float)$db->createCommand("SELECT IFNULL(SUM(grand_total),0) FROM inventory_sales_orders WHERE is_deleted=0")->queryScalar(),
                'total_customers' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_customers WHERE is_deleted=0")->queryScalar(),
                'total_suppliers' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_suppliers WHERE is_deleted=0")->queryScalar(),
                'low_stock_items' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock s INNER JOIN inventory_products p ON p.id=s.product_id WHERE s.is_deleted=0 AND s.quantity<=p.reorder_level AND s.quantity>0")->queryScalar(),
                'out_of_stock_items' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock WHERE is_deleted=0 AND quantity<=0")->queryScalar(),
            ];

            $salesVsPurchase = $db->createCommand("
                SELECT month, IFNULL(SUM(sales),0) sales, IFNULL(SUM(purchases),0) purchases FROM (
                    SELECT DATE_FORMAT(order_date,'%b %Y') month, grand_total sales, 0 purchases FROM inventory_sales_orders WHERE is_deleted=0
                    UNION ALL
                    SELECT DATE_FORMAT(order_date,'%b %Y') month, 0 sales, grand_total purchases FROM inventory_purchase_orders WHERE is_deleted=0
                ) t
                GROUP BY month
                ORDER BY STR_TO_DATE(month,'%b %Y')
            ")->queryAll();

            return array_merge($this->jsonResponse(true, 'Dashboard loaded.'), ['stats' => $stats, 'salesVsPurchase' => $salesVsPurchase]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function actionReportsDashboardData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $db = Yii::$app->db;
            $reports = [
                ['name' => 'Inventory Reports', 'action' => 'inventoryreports', 'icon' => 'fa-cubes', 'count' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_products WHERE is_deleted=0")->queryScalar()],
                ['name' => 'Purchase Orders', 'action' => 'purchasereports', 'icon' => 'fa-shopping-cart', 'count' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_purchase_orders WHERE is_deleted=0")->queryScalar()],
                ['name' => 'Sales Orders', 'action' => 'salesreports', 'icon' => 'fa-shopping-bag', 'count' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_sales_orders WHERE is_deleted=0")->queryScalar()],
                ['name' => 'Stock Valuation', 'action' => 'stockvaluationreport', 'icon' => 'fa-line-chart', 'count' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock WHERE is_deleted=0")->queryScalar()],
                ['name' => 'Low Stock Items', 'action' => 'lowstockreport', 'icon' => 'fa-warning', 'count' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock s INNER JOIN inventory_products p ON p.id=s.product_id WHERE s.is_deleted=0 AND s.quantity<=p.reorder_level")->queryScalar()],
                ['name' => 'Customer Ledger', 'action' => 'customerledgerreport', 'icon' => 'fa-users', 'count' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_customers WHERE is_deleted=0")->queryScalar()],
                ['name' => 'Supplier Ledger', 'action' => 'supplierledgerreport', 'icon' => 'fa-truck', 'count' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_suppliers WHERE is_deleted=0")->queryScalar()],
                ['name' => 'Warehouse Reports', 'action' => 'warehousereports', 'icon' => 'fa-building', 'count' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_warehouses WHERE is_deleted=0")->queryScalar()],
                ['name' => 'Financial Reports', 'action' => 'financialreports', 'icon' => 'fa-money', 'count' => 0],
                ['name' => 'Tax Reports', 'action' => 'taxreports', 'icon' => 'fa-percent', 'count' => 0],
                ['name' => 'Product Performance', 'action' => 'productperformance', 'icon' => 'fa-trophy', 'count' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_products WHERE is_deleted=0")->queryScalar()],
            ];
            return $this->jsonResponse(true, 'Reports loaded.', ['reports' => $reports]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Inventory Reports
     * ----------------------------------------------------------- */
    public function actionInventoryreports()
    {
        if (Yii::$app->request->isGet) {
            $categories = Yii::$app->db->createCommand("SELECT id,category_name FROM inventory_categories WHERE is_deleted=0 ORDER BY category_name")->queryAll();
            $warehouses = Yii::$app->db->createCommand("SELECT id,warehouse_name FROM inventory_warehouses WHERE is_deleted=0 ORDER BY warehouse_name")->queryAll();
            return $this->renderPartial('inventoryreports', ['categories' => $categories, 'warehouses' => $warehouses]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'load') {
                $category_id = trim($post['category_id'] ?? '');
                $warehouse_id = trim($post['warehouse_id'] ?? '');
                $where = " WHERE s.is_deleted=0 ";
                $params = [];
                if ($category_id != '') {
                    $where .= " AND p.category_id=:category_id";
                    $params[':category_id'] = $category_id;
                }
                if ($warehouse_id != '') {
                    $where .= " AND s.warehouse_id=:warehouse_id";
                    $params[':warehouse_id'] = $warehouse_id;
                }
                $rows = Yii::$app->db->createCommand("
                    SELECT
                        p.product_name, p.sku, c.category_name, w.warehouse_name,
                        s.quantity, s.reserved_quantity, s.available_quantity, s.average_cost,
                        (s.quantity*s.average_cost) stock_value
                    FROM inventory_stock s
                    INNER JOIN inventory_products p ON p.id=s.product_id
                    LEFT JOIN inventory_categories c ON c.id=p.category_id
                    INNER JOIN inventory_warehouses w ON w.id=s.warehouse_id
                    $where
                    ORDER BY p.product_name
                ", $params)->queryAll();

                $summary = [
                    'total_items' => count($rows),
                    'total_quantity' => array_sum(array_column($rows, 'quantity')),
                    'total_value' => array_sum(array_column($rows, 'stock_value')),
                ];
                return $this->jsonResponse(true, 'Data loaded successfully!', ['rows' => $rows, 'summary' => $summary]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Purchase Reports
     * ----------------------------------------------------------- */
    public function actionPurchasereports()
    {
        if (Yii::$app->request->isGet) {
            $suppliers = Yii::$app->db->createCommand("SELECT id,company_name FROM inventory_suppliers WHERE is_deleted=0 AND is_active=1 ORDER BY company_name")->queryAll();
            $warehouses = Yii::$app->db->createCommand("SELECT id,warehouse_name FROM inventory_warehouses WHERE is_deleted=0 AND is_active=1 ORDER BY warehouse_name")->queryAll();
            return $this->renderPartial('purchasereports', ['suppliers' => $suppliers, 'warehouses' => $warehouses]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'load') {
                $from_date = !empty(trim($post['from_date'] ?? '')) ? trim($post['from_date']) : null;
                $to_date = !empty(trim($post['to_date'] ?? '')) ? trim($post['to_date']) : null;
                $supplier_id = trim($post['supplier_id'] ?? '');
                $where = " WHERE po.is_deleted=0 ";
                $params = [];
                if ($from_date) {
                    $where .= " AND po.order_date>=:from_date";
                    $params[':from_date'] = $from_date;
                }
                if ($to_date) {
                    $where .= " AND po.order_date<=:to_date";
                    $params[':to_date'] = $to_date;
                }
                if ($supplier_id != '') {
                    $where .= " AND po.supplier_id=:supplier_id";
                    $params[':supplier_id'] = $supplier_id;
                }
                $summary = Yii::$app->db->createCommand("
                    SELECT COUNT(*) total_orders, IFNULL(SUM(po.grand_total),0) total_amount, IFNULL(AVG(po.grand_total),0) average_amount
                    FROM inventory_purchase_orders po $where
                ", $params)->queryOne();
                $rows = Yii::$app->db->createCommand("
                    SELECT po.*, s.company_name, w.warehouse_name
                    FROM inventory_purchase_orders po
                    LEFT JOIN inventory_suppliers s ON s.id=po.supplier_id
                    LEFT JOIN inventory_warehouses w ON w.id=po.warehouse_id
                    $where ORDER BY po.order_date DESC
                ", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['summary' => $summary, 'rows' => $rows]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* Purchase Report PDF */
    public function actionPurchasereportpdf()
    {
        try {
            $get = Yii::$app->request->get();
            $from_date = !empty(trim($get['from_date'] ?? '')) ? trim($get['from_date']) : null;
            $to_date = !empty(trim($get['to_date'] ?? '')) ? trim($get['to_date']) : null;
            $supplier_id = trim($get['supplier_id'] ?? '');

            $where = " WHERE po.is_deleted=0 ";
            $params = [];
            if ($from_date) {
                $where .= " AND po.order_date>=:from_date";
                $params[':from_date'] = $from_date;
            }
            if ($to_date) {
                $where .= " AND po.order_date<=:to_date";
                $params[':to_date'] = $to_date;
            }
            if ($supplier_id != '') {
                $where .= " AND po.supplier_id=:supplier_id";
                $params[':supplier_id'] = $supplier_id;
            }

            $summary = Yii::$app->db->createCommand("
                SELECT COUNT(*) total_orders, IFNULL(SUM(po.grand_total),0) total_amount, IFNULL(AVG(po.grand_total),0) average_amount
                FROM inventory_purchase_orders po $where
            ", $params)->queryOne();

            $rows = Yii::$app->db->createCommand("
                SELECT po.*, s.company_name, w.warehouse_name
                FROM inventory_purchase_orders po
                LEFT JOIN inventory_suppliers s ON s.id=po.supplier_id
                LEFT JOIN inventory_warehouses w ON w.id=po.warehouse_id
                $where ORDER BY po.order_date DESC
            ", $params)->queryAll();

            $this->generatePurchaseReportPDF($rows, $summary, $from_date, $to_date, $supplier_id);
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    private function generatePurchaseReportPDF($rows, $summary, $from_date, $to_date, $supplier_id)
    {
        require_once(Yii::getAlias('@app') . '/vendor/autoload.php');
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        // Set default line width
        $pdf->SetLineWidth(0.3);

        // ========== STAT CARDS SECTION ==========
        $cardWidth = 60;
        $cardHeight = 22;
        $cardSpacing = 2;

        // Card 1: Total Orders (Blue)
        $x1 = 10;
        $y1 = $pdf->GetY();

        $pdf->SetDrawColor(30, 100, 180);
        $pdf->SetFillColor(230, 242, 255);
        $pdf->SetLineWidth(0.5);

        $pdf->SetXY($x1, $y1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->Cell($cardWidth, 5, 'TOTAL ORDERS', 1, 1, 'L', true);

        $pdf->SetXY($x1, $y1 + 5);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell($cardWidth, 12, (int)$summary['total_orders'], 1, 1, 'C', true);

        // Card 2: Total Amount (Green)
        $x2 = $x1 + $cardWidth + $cardSpacing;
        $pdf->SetDrawColor(0, 150, 100);
        $pdf->SetFillColor(230, 250, 240);
        $pdf->SetLineWidth(0.5);

        $pdf->SetXY($x2, $y1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->Cell($cardWidth, 5, 'TOTAL AMOUNT', 1, 1, 'L', true);

        $pdf->SetXY($x2, $y1 + 5);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell($cardWidth, 12, 'PKR ' . number_format($summary['total_amount'], 0), 1, 1, 'C', true);

        // Card 3: Average Amount (Orange)
        $x3 = $x2 + $cardWidth + $cardSpacing;
        $pdf->SetDrawColor(250, 150, 0);
        $pdf->SetFillColor(255, 248, 230);
        $pdf->SetLineWidth(0.5);

        $pdf->SetXY($x3, $y1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->Cell(0, 5, 'AVERAGE AMOUNT', 1, 1, 'L', true);

        $pdf->SetXY($x3, $y1 + 5);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 12, 'PKR ' . number_format($summary['average_amount'], 0), 1, 1, 'C', true);

        $pdf->Ln(6);

        // ========== SUMMARY TABLES SECTION ==========
        $pdf->SetLineWidth(0.3);

        // Summarize data
        $statusSummary = [];
        $supplierSummary = [];
        $warehouseSummary = [];

        foreach ($rows as $row) {
            $status = $row['status'] ?? 'Unknown';
            $supplier = $row['company_name'] ?? 'N/A';
            $warehouse = $row['warehouse_name'] ?? 'N/A';
            $amount = (float)($row['grand_total'] ?? 0);

            if (!isset($statusSummary[$status])) {
                $statusSummary[$status] = ['count' => 0, 'total' => 0];
            }
            $statusSummary[$status]['count']++;
            $statusSummary[$status]['total'] += $amount;

            if (!isset($supplierSummary[$supplier])) {
                $supplierSummary[$supplier] = ['count' => 0, 'total' => 0];
            }
            $supplierSummary[$supplier]['count']++;
            $supplierSummary[$supplier]['total'] += $amount;

            if (!isset($warehouseSummary[$warehouse])) {
                $warehouseSummary[$warehouse] = ['count' => 0, 'total' => 0];
            }
            $warehouseSummary[$warehouse]['count']++;
            $warehouseSummary[$warehouse]['total'] += $amount;
        }

        // Summary section headers
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(40, 40, 40);
        $pdf->SetDrawColor(100, 100, 100);
        $pdf->SetFillColor(240, 240, 240);

        $sumColWidth = 63;
        $pdf->Cell($sumColWidth, 6, 'Status Summary', 1, 0, 'L', true);
        $pdf->Cell($sumColWidth, 6, 'Supplier Summary', 1, 0, 'L', true);
        $pdf->Cell(0, 6, 'Warehouse Summary', 1, 1, 'L', true);

        // Column headers
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetTextColor(50, 50, 50);
        $pdf->SetFillColor(250, 250, 250);

        $pdf->Cell(32, 5, 'Status', 1, 0, 'L', true);
        $pdf->Cell(15, 5, 'Orders', 1, 0, 'C', true);
        $pdf->Cell(16, 5, 'Amount', 1, 0, 'R', true);

        $pdf->Cell(32, 5, 'Supplier', 1, 0, 'L', true);
        $pdf->Cell(15, 5, 'Orders', 1, 0, 'C', true);
        $pdf->Cell(16, 5, 'Amount', 1, 0, 'R', true);

        $pdf->Cell(32, 5, 'Warehouse', 1, 0, 'L', true);
        $pdf->Cell(15, 5, 'Orders', 1, 0, 'C', true);
        $pdf->Cell(0, 5, 'Amount', 1, 1, 'R', true);

        // Summary rows with alternating colors
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $maxRows = max(count($statusSummary), count($supplierSummary), count($warehouseSummary));

        $statusItems = array_keys($statusSummary);
        $supplierItems = array_keys($supplierSummary);
        $warehouseItems = array_keys($warehouseSummary);

        for ($i = 0; $i < $maxRows; $i++) {
            $bgColor = ($i % 2 == 0) ? [255, 255, 255] : [245, 248, 252];
            $pdf->SetFillColor($bgColor[0], $bgColor[1], $bgColor[2]);

            // Status column
            if (isset($statusItems[$i])) {
                $status = $statusItems[$i];
                $pdf->Cell(32, 5, substr($status, 0, 22), 1, 0, 'L', true);
                $pdf->Cell(15, 5, $statusSummary[$status]['count'], 1, 0, 'C', true);
                $pdf->Cell(16, 5, number_format($statusSummary[$status]['total'], 0), 1, 0, 'R', true);
            } else {
                $pdf->Cell(32, 5, '', 1, 0, 'L', true);
                $pdf->Cell(15, 5, '', 1, 0, 'C', true);
                $pdf->Cell(16, 5, '', 1, 0, 'R', true);
            }

            // Supplier column
            if (isset($supplierItems[$i])) {
                $supplier = $supplierItems[$i];
                $pdf->Cell(32, 5, substr($supplier, 0, 22), 1, 0, 'L', true);
                $pdf->Cell(15, 5, $supplierSummary[$supplier]['count'], 1, 0, 'C', true);
                $pdf->Cell(16, 5, number_format($supplierSummary[$supplier]['total'], 0), 1, 0, 'R', true);
            } else {
                $pdf->Cell(32, 5, '', 1, 0, 'L', true);
                $pdf->Cell(15, 5, '', 1, 0, 'C', true);
                $pdf->Cell(16, 5, '', 1, 0, 'R', true);
            }

            // Warehouse column
            if (isset($warehouseItems[$i])) {
                $warehouse = $warehouseItems[$i];
                $pdf->Cell(32, 5, substr($warehouse, 0, 22), 1, 0, 'L', true);
                $pdf->Cell(15, 5, $warehouseSummary[$warehouse]['count'], 1, 0, 'C', true);
                $pdf->Cell(0, 5, number_format($warehouseSummary[$warehouse]['total'], 0), 1, 1, 'R', true);
            } else {
                $pdf->Cell(32, 5, '', 1, 0, 'L', true);
                $pdf->Cell(15, 5, '', 1, 0, 'C', true);
                $pdf->Cell(0, 5, '', 1, 1, 'R', true);
            }
        }

        $pdf->Ln(6);

        // ========== DETAILED REPORT SECTION ==========
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(40, 40, 40);
        $pdf->SetDrawColor(100, 100, 100);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(0, 6, 'Detailed Purchase Report', 1, 1, 'L', true);

        // Table Header
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetTextColor(50, 50, 50);
        $pdf->SetFillColor(250, 250, 250);

        $pdf->Cell(6, 5, '#', 1, 0, 'C', true);
        $pdf->Cell(26, 5, 'PO Number', 1, 0, 'L', true);
        $pdf->Cell(26, 5, 'Supplier', 1, 0, 'L', true);
        $pdf->Cell(24, 5, 'Warehouse', 1, 0, 'L', true);
        $pdf->Cell(18, 5, 'Order Date', 1, 0, 'C', true);
        $pdf->Cell(16, 5, 'Status', 1, 0, 'L', true);
        $pdf->Cell(0, 5, 'Grand Total', 1, 1, 'R', true);

        // Table Data with alternating colors
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(0, 0, 0);

        foreach ($rows as $idx => $row) {
            $bgColor = ($idx % 2 == 0) ? [255, 255, 255] : [245, 248, 252];
            $pdf->SetFillColor($bgColor[0], $bgColor[1], $bgColor[2]);

            $pdf->Cell(6, 5, $idx + 1, 1, 0, 'C', true);
            $pdf->Cell(26, 5, substr($row['po_number'] ?? 'N/A', 0, 16), 1, 0, 'L', true);
            $pdf->Cell(26, 5, substr($row['company_name'] ?? 'N/A', 0, 16), 1, 0, 'L', true);
            $pdf->Cell(24, 5, substr($row['warehouse_name'] ?? 'N/A', 0, 14), 1, 0, 'L', true);
            $pdf->Cell(18, 5, $row['order_date'] ?? 'N/A', 1, 0, 'C', true);
            $pdf->Cell(16, 5, substr($row['status'] ?? 'N/A', 0, 9), 1, 0, 'L', true);
            $pdf->Cell(0, 5, number_format($row['grand_total'] ?? 0, 2), 1, 1, 'R', true);
        }

        $pdf->Output('Purchase_Report_' . date('Ymd_His') . '.pdf', 'I');
    }

    /* -------------------------------------------------------------
     * Sales Reports
     * ----------------------------------------------------------- */
    public function actionSalesreports()
    {
        if (Yii::$app->request->isGet) {
            $customers = Yii::$app->db->createCommand("SELECT id,company_name,first_name,last_name FROM inventory_customers WHERE is_deleted=0 AND is_active=1 ORDER BY first_name")->queryAll();
            $warehouses = Yii::$app->db->createCommand("SELECT id,warehouse_name FROM inventory_warehouses WHERE is_deleted=0 AND is_active=1 ORDER BY warehouse_name")->queryAll();
            return $this->renderPartial('salesreports', ['customers' => $customers, 'warehouses' => $warehouses]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'load') {
                $from_date = !empty(trim($post['from_date'] ?? '')) ? trim($post['from_date']) : null;
                $to_date = !empty(trim($post['to_date'] ?? '')) ? trim($post['to_date']) : null;
                $customer_id = trim($post['customer_id'] ?? '');
                $where = " WHERE so.is_deleted=0 ";
                $params = [];
                if ($from_date) {
                    $where .= " AND so.order_date>=:from_date";
                    $params[':from_date'] = $from_date;
                }
                if ($to_date) {
                    $where .= " AND so.order_date<=:to_date";
                    $params[':to_date'] = $to_date;
                }
                if ($customer_id != '') {
                    $where .= " AND so.customer_id=:customer_id";
                    $params[':customer_id'] = $customer_id;
                }
                $summary = Yii::$app->db->createCommand("
                    SELECT COUNT(*) total_orders, IFNULL(SUM(so.grand_total),0) total_amount, IFNULL(AVG(so.grand_total),0) average_amount
                    FROM inventory_sales_orders so $where
                ", $params)->queryOne();
                $rows = Yii::$app->db->createCommand("
                    SELECT so.*, c.company_name, c.first_name, c.last_name, w.warehouse_name
                    FROM inventory_sales_orders so
                    LEFT JOIN inventory_customers c ON c.id=so.customer_id
                    LEFT JOIN inventory_warehouses w ON w.id=so.warehouse_id
                    $where ORDER BY so.order_date DESC
                ", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['summary' => $summary, 'rows' => $rows]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Stock Valuation Report
     * ----------------------------------------------------------- */
    public function actionStockvaluationreport()
    {
        if (Yii::$app->request->isGet) {
            $warehouses = Yii::$app->db->createCommand("SELECT id,warehouse_name FROM inventory_warehouses WHERE is_deleted=0 AND is_active=1 ORDER BY warehouse_name")->queryAll();
            return $this->renderPartial('stockvaluationreport', ['warehouses' => $warehouses]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'load') {
                $warehouse_id = trim($post['warehouse_id'] ?? '');
                $where = " WHERE s.is_deleted=0 ";
                $params = [];
                if ($warehouse_id != '') {
                    $where .= " AND s.warehouse_id=:warehouse_id";
                    $params[':warehouse_id'] = $warehouse_id;
                }
                $rows = Yii::$app->db->createCommand("
                    SELECT
                        p.product_name, p.sku, w.warehouse_name,
                        s.quantity, s.average_cost, s.last_purchase_price,
                        (s.quantity*s.average_cost) cost_value,
                        (s.quantity*p.selling_price) retail_value
                    FROM inventory_stock s
                    INNER JOIN inventory_products p ON p.id=s.product_id
                    INNER JOIN inventory_warehouses w ON w.id=s.warehouse_id
                    $where
                    ORDER BY cost_value DESC
                ", $params)->queryAll();
                $summary = [
                    'total_cost_value' => array_sum(array_column($rows, 'cost_value')),
                    'total_retail_value' => array_sum(array_column($rows, 'retail_value')),
                    'total_items' => count($rows),
                ];
                return $this->jsonResponse(true, 'Data loaded successfully!', ['rows' => $rows, 'summary' => $summary]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Low Stock Report
     * ----------------------------------------------------------- */
    public function actionLowstockreport()
    {
        if (Yii::$app->request->isGet) {
            $warehouses = Yii::$app->db->createCommand("SELECT id,warehouse_name FROM inventory_warehouses WHERE is_deleted=0 AND is_active=1 ORDER BY warehouse_name")->queryAll();
            return $this->renderPartial('lowstockreport', ['warehouses' => $warehouses]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'load') {
                $warehouse_id = trim($post['warehouse_id'] ?? '');
                $where = " WHERE s.is_deleted=0 AND s.quantity<=p.reorder_level ";
                $params = [];
                if ($warehouse_id != '') {
                    $where .= " AND s.warehouse_id=:warehouse_id";
                    $params[':warehouse_id'] = $warehouse_id;
                }
                $rows = Yii::$app->db->createCommand("
                    SELECT p.product_name, p.sku, w.warehouse_name, s.quantity, p.reorder_level, p.minimum_stock
                    FROM inventory_stock s
                    INNER JOIN inventory_products p ON p.id=s.product_id
                    INNER JOIN inventory_warehouses w ON w.id=s.warehouse_id
                    $where
                    ORDER BY s.quantity ASC
                ", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['rows' => $rows]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Customer Ledger Report
     * ----------------------------------------------------------- */
    public function actionCustomerledgerreport()
    {
        if (Yii::$app->request->isGet) {
            $customers = Yii::$app->db->createCommand("SELECT id,COALESCE(company_name,CONCAT(first_name,' ',last_name)) name FROM inventory_customers WHERE is_deleted=0 ORDER BY name")->queryAll();
            return $this->renderPartial('customerledgerreport', ['customers' => $customers]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'load') {
                $customer_id = trim($post['customer_id'] ?? '');
                $where = " WHERE is_deleted=0 ";
                $params = [];
                if ($customer_id != '') {
                    $where .= " AND id=:customer_id";
                    $params[':customer_id'] = $customer_id;
                }
                $rows = Yii::$app->db->createCommand("
                    SELECT id,customer_code,COALESCE(company_name,CONCAT(first_name,' ',last_name)) name,
                        credit_limit,opening_balance,current_balance
                    FROM inventory_customers $where ORDER BY current_balance DESC
                ", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['rows' => $rows]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Supplier Ledger Report
     * ----------------------------------------------------------- */
    public function actionSupplierledgerreport()
    {
        if (Yii::$app->request->isGet) {
            $suppliers = Yii::$app->db->createCommand("SELECT id,company_name FROM inventory_suppliers WHERE is_deleted=0 ORDER BY company_name")->queryAll();
            return $this->renderPartial('supplierledgerreport', ['suppliers' => $suppliers]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'load') {
                $supplier_id = trim($post['supplier_id'] ?? '');
                $where = " WHERE is_deleted=0 ";
                $params = [];
                if ($supplier_id != '') {
                    $where .= " AND id=:supplier_id";
                    $params[':supplier_id'] = $supplier_id;
                }
                $rows = Yii::$app->db->createCommand("
                    SELECT id,supplier_code,company_name,credit_limit,opening_balance,current_balance
                    FROM inventory_suppliers $where ORDER BY current_balance DESC
                ", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['rows' => $rows]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Warehouse Reports
     * ----------------------------------------------------------- */
    public function actionWarehousereports()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('warehousereports');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'load') {
                $rows = Yii::$app->db->createCommand("
                    SELECT
                        w.id, w.warehouse_name, w.warehouse_code,
                        COUNT(DISTINCT s.product_id) total_products,
                        IFNULL(SUM(s.quantity),0) total_quantity,
                        IFNULL(SUM(s.quantity*s.average_cost),0) total_value
                    FROM inventory_warehouses w
                    LEFT JOIN inventory_stock s ON s.warehouse_id=w.id AND s.is_deleted=0
                    WHERE w.is_deleted=0
                    GROUP BY w.id
                    ORDER BY total_value DESC
                ")->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['rows' => $rows]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Financial Reports
     * ----------------------------------------------------------- */
    public function actionFinancialreports()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('financialreports');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'load') {
                $from_date = !empty(trim($post['from_date'] ?? '')) ? trim($post['from_date']) : date('Y-m-01');
                $to_date = !empty(trim($post['to_date'] ?? '')) ? trim($post['to_date']) : date('Y-m-d');
                $db = Yii::$app->db;

                $accountTypeSummary = $db->createCommand("
                    SELECT account_type, IFNULL(SUM(current_balance),0) total
                    FROM inventory_accounts
                    WHERE is_deleted=0
                    GROUP BY account_type
                ")->queryAll();

                $income = (float)$db->createCommand("
                    SELECT IFNULL(SUM(grand_total),0) FROM inventory_sales_orders
                    WHERE is_deleted=0 AND order_date>=:f AND order_date<=:t
                ", [':f' => $from_date, ':t' => $to_date])->queryScalar();

                $expense = (float)$db->createCommand("
                    SELECT IFNULL(SUM(grand_total),0) FROM inventory_purchase_orders
                    WHERE is_deleted=0 AND order_date>=:f AND order_date<=:t
                ", [':f' => $from_date, ':t' => $to_date])->queryScalar();

                return $this->jsonResponse(true, 'Data loaded successfully!', [
                    'accountTypeSummary' => $accountTypeSummary,
                    'income' => $income,
                    'expense' => $expense,
                    'net' => $income - $expense
                ]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Tax Reports
     * ----------------------------------------------------------- */
    public function actionTaxreports()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('taxreports');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'load') {
                $from_date = !empty(trim($post['from_date'] ?? '')) ? trim($post['from_date']) : date('Y-m-01');
                $to_date = !empty(trim($post['to_date'] ?? '')) ? trim($post['to_date']) : date('Y-m-d');

                $salesTax = (float)Yii::$app->db->createCommand("
                    SELECT IFNULL(SUM(tax),0) FROM inventory_sales_orders
                    WHERE is_deleted=0 AND order_date>=:f AND order_date<=:t
                ", [':f' => $from_date, ':t' => $to_date])->queryScalar();

                $purchaseTax = (float)Yii::$app->db->createCommand("
                    SELECT IFNULL(SUM(tax),0) FROM inventory_purchase_orders
                    WHERE is_deleted=0 AND order_date>=:f AND order_date<=:t
                ", [':f' => $from_date, ':t' => $to_date])->queryScalar();

                $monthly = Yii::$app->db->createCommand("
                    SELECT month, IFNULL(SUM(sales_tax),0) sales_tax, IFNULL(SUM(purchase_tax),0) purchase_tax FROM (
                        SELECT DATE_FORMAT(order_date,'%b %Y') month, tax sales_tax, 0 purchase_tax FROM inventory_sales_orders WHERE is_deleted=0
                        UNION ALL
                        SELECT DATE_FORMAT(order_date,'%b %Y') month, 0 sales_tax, tax purchase_tax FROM inventory_purchase_orders WHERE is_deleted=0
                    ) t
                    GROUP BY month
                    ORDER BY STR_TO_DATE(month,'%b %Y')
                ")->queryAll();

                return $this->jsonResponse(true, 'Data loaded successfully!', [
                    'sales_tax' => $salesTax,
                    'purchase_tax' => $purchaseTax,
                    'net_tax' => $salesTax - $purchaseTax,
                    'monthly' => $monthly
                ]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Product Performance
     * ----------------------------------------------------------- */
    public function actionProductperformance()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('productperformance');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'load') {
                $from_date = !empty(trim($post['from_date'] ?? '')) ? trim($post['from_date']) : null;
                $to_date = !empty(trim($post['to_date'] ?? '')) ? trim($post['to_date']) : null;
                $where = " WHERE so.is_deleted=0 ";
                $params = [];
                if ($from_date) {
                    $where .= " AND so.order_date>=:from_date";
                    $params[':from_date'] = $from_date;
                }
                if ($to_date) {
                    $where .= " AND so.order_date<=:to_date";
                    $params[':to_date'] = $to_date;
                }
                $rows = Yii::$app->db->createCommand("
                    SELECT
                        p.product_name, p.sku,
                        SUM(i.quantity) total_quantity_sold,
                        SUM(i.total) total_revenue,
                        COUNT(DISTINCT so.id) total_orders
                    FROM inventory_sales_order_items i
                    INNER JOIN inventory_products p ON p.id=i.product_id
                    INNER JOIN inventory_sales_orders so ON so.id=i.sales_order_id
                    $where AND i.is_deleted=0
                    GROUP BY i.product_id
                    ORDER BY total_revenue DESC
                    LIMIT 50
                ", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['rows' => $rows]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* Export Report to Excel CSV */
    public function actionExportreport()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $post = Yii::$app->request->post();

        if (!isset($post['report_type']) || !isset($post['data'])) {
            Yii::$app->response->statusCode = 400;
            return 'Invalid request';
        }

        $reportType = $post['report_type'];
        $data = is_array($post['data']) ? $post['data'] : json_decode($post['data'], true);
        $filename = $reportType . '_' . date('YmdHis') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $output = fopen('php://output', 'w');

        if (!empty($data)) {
            $firstRow = $data[0];
            if (is_array($firstRow)) {
                fputcsv($output, array_keys($firstRow));
                foreach ($data as $row) {
                    fputcsv($output, array_values($row));
                }
            }
        }

        fclose($output);
        exit;
    }

    /* Export Inventory Report */
    public function actionExportinventoryreport()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $get = Yii::$app->request->get();

        $category_id = trim($get['category_id'] ?? '');
        $warehouse_id = trim($get['warehouse_id'] ?? '');
        $where = " WHERE s.is_deleted=0 ";
        $params = [];

        if ($category_id != '') {
            $where .= " AND p.category_id=:category_id";
            $params[':category_id'] = $category_id;
        }
        if ($warehouse_id != '') {
            $where .= " AND s.warehouse_id=:warehouse_id";
            $params[':warehouse_id'] = $warehouse_id;
        }

        $rows = Yii::$app->db->createCommand("
            SELECT
                p.product_name, p.sku, c.category_name, w.warehouse_name,
                s.quantity, s.reserved_quantity, s.available_quantity, s.average_cost,
                (s.quantity*s.average_cost) stock_value
            FROM inventory_stock s
            INNER JOIN inventory_products p ON p.id=s.product_id
            LEFT JOIN inventory_categories c ON c.id=p.category_id
            INNER JOIN inventory_warehouses w ON w.id=s.warehouse_id
            $where
            ORDER BY p.product_name
        ", $params)->queryAll();

        $filename = 'inventory_report_' . date('YmdHis') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Product Name', 'SKU', 'Category', 'Warehouse', 'Quantity', 'Reserved', 'Available', 'Avg Cost', 'Stock Value']);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['product_name'],
                $row['sku'],
                $row['category_name'],
                $row['warehouse_name'],
                number_format($row['quantity'], 2),
                number_format($row['reserved_quantity'], 2),
                number_format($row['available_quantity'], 2),
                number_format($row['average_cost'], 2),
                number_format($row['stock_value'], 2)
            ]);
        }

        fclose($output);
        exit;
    }

    /* Export Purchase Report */
    public function actionExportpurchasereport()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $get = Yii::$app->request->get();

        $from_date = trim($get['from_date'] ?? '');
        $to_date = trim($get['to_date'] ?? '');
        $supplier_id = trim($get['supplier_id'] ?? '');
        $where = " WHERE po.is_deleted=0 ";
        $params = [];

        if ($from_date != '') {
            $where .= " AND po.order_date>=:from_date";
            $params[':from_date'] = $from_date;
        }
        if ($to_date != '') {
            $where .= " AND po.order_date<=:to_date";
            $params[':to_date'] = $to_date;
        }
        if ($supplier_id != '') {
            $where .= " AND po.supplier_id=:supplier_id";
            $params[':supplier_id'] = $supplier_id;
        }

        $rows = Yii::$app->db->createCommand("
            SELECT po.*, s.company_name, w.warehouse_name
            FROM inventory_purchase_orders po
            LEFT JOIN inventory_suppliers s ON s.id=po.supplier_id
            LEFT JOIN inventory_warehouses w ON w.id=po.warehouse_id
            $where ORDER BY po.order_date DESC
        ", $params)->queryAll();

        $filename = 'purchase_report_' . date('YmdHis') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $output = fopen('php://output', 'w');
        fputcsv($output, ['PO Number', 'Supplier', 'Warehouse', 'Date', 'Status', 'Subtotal', 'Discount', 'Tax', 'Freight', 'Grand Total']);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['po_number'],
                $row['company_name'] ?? 'N/A',
                $row['warehouse_name'] ?? 'N/A',
                $row['order_date'],
                $row['status'],
                number_format($row['subtotal'], 2),
                number_format($row['discount'], 2),
                number_format($row['tax'], 2),
                number_format($row['freight'], 2),
                number_format($row['grand_total'], 2)
            ]);
        }

        fclose($output);
        exit;
    }

    /* Export Sales Report */
    public function actionExportsalesreport()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $get = Yii::$app->request->get();

        $from_date = trim($get['from_date'] ?? '');
        $to_date = trim($get['to_date'] ?? '');
        $customer_id = trim($get['customer_id'] ?? '');
        $where = " WHERE so.is_deleted=0 ";
        $params = [];

        if ($from_date != '') {
            $where .= " AND so.order_date>=:from_date";
            $params[':from_date'] = $from_date;
        }
        if ($to_date != '') {
            $where .= " AND so.order_date<=:to_date";
            $params[':to_date'] = $to_date;
        }
        if ($customer_id != '') {
            $where .= " AND so.customer_id=:customer_id";
            $params[':customer_id'] = $customer_id;
        }

        $rows = Yii::$app->db->createCommand("
            SELECT so.*, c.company_name, c.first_name, c.last_name, w.warehouse_name
            FROM inventory_sales_orders so
            LEFT JOIN inventory_customers c ON c.id=so.customer_id
            LEFT JOIN inventory_warehouses w ON w.id=so.warehouse_id
            $where ORDER BY so.order_date DESC
        ", $params)->queryAll();

        $filename = 'sales_report_' . date('YmdHis') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Order Number', 'Customer', 'Warehouse', 'Date', 'Status', 'Payment', 'Subtotal', 'Discount', 'Tax', 'Shipping', 'Grand Total']);

        foreach ($rows as $row) {
            $customer = $row['company_name'] ?? trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
            fputcsv($output, [
                $row['order_number'],
                $customer ?: 'N/A',
                $row['warehouse_name'] ?? 'N/A',
                $row['order_date'],
                $row['order_status'],
                $row['payment_status'],
                number_format($row['subtotal'], 2),
                number_format($row['discount'], 2),
                number_format($row['tax'], 2),
                number_format($row['shipping'], 2),
                number_format($row['grand_total'], 2)
            ]);
        }

        fclose($output);
        exit;
    }

    /* Export Stock Valuation Report */
    public function actionExportstockvaluation()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $get = Yii::$app->request->get();

        $warehouse_id = trim($get['warehouse_id'] ?? '');
        $where = " WHERE s.is_deleted=0 ";
        $params = [];

        if ($warehouse_id != '') {
            $where .= " AND s.warehouse_id=:warehouse_id";
            $params[':warehouse_id'] = $warehouse_id;
        }

        $rows = Yii::$app->db->createCommand("
            SELECT
                p.product_name, p.sku, w.warehouse_name,
                s.quantity, s.average_cost, s.last_purchase_price,
                (s.quantity*s.average_cost) cost_value,
                (s.quantity*p.selling_price) retail_value
            FROM inventory_stock s
            INNER JOIN inventory_products p ON p.id=s.product_id
            INNER JOIN inventory_warehouses w ON w.id=s.warehouse_id
            $where
            ORDER BY cost_value DESC
        ", $params)->queryAll();

        $filename = 'stock_valuation_' . date('YmdHis') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Product', 'SKU', 'Warehouse', 'Quantity', 'Avg Cost', 'Last Price', 'Cost Value', 'Retail Value']);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['product_name'],
                $row['sku'],
                $row['warehouse_name'],
                number_format($row['quantity'], 2),
                number_format($row['average_cost'], 2),
                number_format($row['last_purchase_price'], 2),
                number_format($row['cost_value'], 2),
                number_format($row['retail_value'], 2)
            ]);
        }

        fclose($output);
        exit;
    }

    /* Export Low Stock Report */
    public function actionExportlowstock()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $get = Yii::$app->request->get();

        $warehouse_id = trim($get['warehouse_id'] ?? '');
        $where = " WHERE s.is_deleted=0 AND s.quantity<=p.reorder_level ";
        $params = [];

        if ($warehouse_id != '') {
            $where .= " AND s.warehouse_id=:warehouse_id";
            $params[':warehouse_id'] = $warehouse_id;
        }

        $rows = Yii::$app->db->createCommand("
            SELECT p.product_name, p.sku, w.warehouse_name, s.quantity, p.reorder_level, p.minimum_stock
            FROM inventory_stock s
            INNER JOIN inventory_products p ON p.id=s.product_id
            INNER JOIN inventory_warehouses w ON w.id=s.warehouse_id
            $where
            ORDER BY s.quantity ASC
        ", $params)->queryAll();

        $filename = 'low_stock_report_' . date('YmdHis') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Product', 'SKU', 'Warehouse', 'Current Qty', 'Reorder Level', 'Minimum Stock', 'Status']);

        foreach ($rows as $row) {
            $status = $row['quantity'] <= 0 ? 'Out of Stock' : 'Low Stock';
            fputcsv($output, [
                $row['product_name'],
                $row['sku'],
                $row['warehouse_name'],
                number_format($row['quantity'], 2),
                number_format($row['reorder_level'], 2),
                number_format($row['minimum_stock'], 2),
                $status
            ]);
        }

        fclose($output);
        exit;
    }

    /* Export Customer Ledger */
    public function actionExportcustomerledger()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $get = Yii::$app->request->get();

        $customer_id = trim($get['customer_id'] ?? '');
        $where = " WHERE is_deleted=0 ";
        $params = [];

        if ($customer_id != '') {
            $where .= " AND id=:customer_id";
            $params[':customer_id'] = $customer_id;
        }

        $rows = Yii::$app->db->createCommand("
            SELECT id, customer_code, COALESCE(company_name, CONCAT(first_name,' ',last_name)) name,
                credit_limit, opening_balance, current_balance
            FROM inventory_customers $where ORDER BY current_balance DESC
        ", $params)->queryAll();

        $filename = 'customer_ledger_' . date('YmdHis') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Customer Code', 'Name', 'Credit Limit', 'Opening Balance', 'Current Balance']);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['customer_code'],
                $row['name'],
                number_format($row['credit_limit'], 2),
                number_format($row['opening_balance'], 2),
                number_format($row['current_balance'], 2)
            ]);
        }

        fclose($output);
        exit;
    }

    /* Export Supplier Ledger */
    public function actionExportsupplierledger()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $get = Yii::$app->request->get();

        $supplier_id = trim($get['supplier_id'] ?? '');
        $where = " WHERE is_deleted=0 ";
        $params = [];

        if ($supplier_id != '') {
            $where .= " AND id=:supplier_id";
            $params[':supplier_id'] = $supplier_id;
        }

        $rows = Yii::$app->db->createCommand("
            SELECT id, supplier_code, company_name, credit_limit, opening_balance, current_balance
            FROM inventory_suppliers $where ORDER BY current_balance DESC
        ", $params)->queryAll();

        $filename = 'supplier_ledger_' . date('YmdHis') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Supplier Code', 'Company Name', 'Credit Limit', 'Opening Balance', 'Current Balance']);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['supplier_code'],
                $row['company_name'],
                number_format($row['credit_limit'], 2),
                number_format($row['opening_balance'], 2),
                number_format($row['current_balance'], 2)
            ]);
        }

        fclose($output);
        exit;
    }

    /* Export Warehouse Report */
    public function actionExportwarehousereport()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        $rows = Yii::$app->db->createCommand("
            SELECT
                w.id, w.warehouse_name, w.warehouse_code,
                COUNT(DISTINCT s.product_id) total_products,
                IFNULL(SUM(s.quantity),0) total_quantity,
                IFNULL(SUM(s.quantity*s.average_cost),0) total_value
            FROM inventory_warehouses w
            LEFT JOIN inventory_stock s ON s.warehouse_id=w.id AND s.is_deleted=0
            WHERE w.is_deleted=0
            GROUP BY w.id
            ORDER BY total_value DESC
        ")->queryAll();

        $filename = 'warehouse_report_' . date('YmdHis') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Warehouse Code', 'Warehouse Name', 'Total Products', 'Total Quantity', 'Total Value']);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['warehouse_code'],
                $row['warehouse_name'],
                $row['total_products'],
                number_format($row['total_quantity'], 2),
                number_format($row['total_value'], 2)
            ]);
        }

        fclose($output);
        exit;
    }

    /* Export Product Performance */
    public function actionExportproductperformance()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $get = Yii::$app->request->get();

        $from_date = trim($get['from_date'] ?? '');
        $to_date = trim($get['to_date'] ?? '');
        $where = " WHERE so.is_deleted=0 ";
        $params = [];

        if ($from_date != '') {
            $where .= " AND so.order_date>=:from_date";
            $params[':from_date'] = $from_date;
        }
        if ($to_date != '') {
            $where .= " AND so.order_date<=:to_date";
            $params[':to_date'] = $to_date;
        }

        $rows = Yii::$app->db->createCommand("
            SELECT
                p.product_name, p.sku,
                SUM(i.quantity) total_quantity_sold,
                SUM(i.total) total_revenue,
                COUNT(DISTINCT so.id) total_orders
            FROM inventory_sales_order_items i
            INNER JOIN inventory_products p ON p.id=i.product_id
            INNER JOIN inventory_sales_orders so ON so.id=i.sales_order_id
            $where AND i.is_deleted=0
            GROUP BY i.product_id
            ORDER BY total_revenue DESC
            LIMIT 500
        ", $params)->queryAll();

        $filename = 'product_performance_' . date('YmdHis') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Product Name', 'SKU', 'Total Qty Sold', 'Total Revenue', 'Total Orders']);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['product_name'],
                $row['sku'],
                number_format($row['total_quantity_sold'], 2),
                number_format($row['total_revenue'], 2),
                $row['total_orders']
            ]);
        }

        fclose($output);
        exit;
    }

    public function actionInjectdb()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
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

            $transaction->commit();

            echo "Reports tables created successfully.";
            exit;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo "Error: " . $e->getMessage();
            exit;
        }
    }
}
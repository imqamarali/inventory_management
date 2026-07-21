<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class StockController extends Controller
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
        $user_role_id = Yii::$app->session->get('user_array')['role_id'] ?? null;
        // if ($user_role_id != 4) {
        //     Yii::$app->session->setFlash('error', 'Access denied. Inventory Manager access only.');
        //     $this->redirect(['site/index']);
        //     return false;
        // }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    public function actionInventory()
    {
        $modules = [
            ['name' => 'Dashboard', 'controller' => 'inventory/inventorydashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Current Stock', 'controller' => 'inventory/inventorycurrentstock', 'icon' => 'fa fa-cubes'],
            ['name' => 'Reserved Stock', 'controller' => 'inventory/inventoryreservedstock', 'icon' => 'fa fa-lock'],
            ['name' => 'Stock Adjustment', 'controller' => 'inventory/inventorystockadjustment', 'icon' => 'fa fa-sliders'],
            ['name' => 'Stock Valuation', 'controller' => 'inventory/inventorystockvaluation', 'icon' => 'fa fa-line-chart'],
            ['name' => 'Stock Movement', 'controller' => 'inventory/inventorystockmovement', 'icon' => 'fa fa-exchange'],
            ['name' => 'Opening Stock', 'controller' => 'inventory/inventoryopeningstock', 'icon' => 'fa fa-plus-square'],
            ['name' => 'Stock Transfer', 'controller' => 'inventory/inventorystocktransfer', 'icon' => 'fa fa-random'],
            ['name' => 'Low Stock Items', 'controller' => 'inventory/inventorylowstock', 'icon' => 'fa fa-warning'],
            ['name' => 'Damaged Stock', 'controller' => 'inventory/inventorydamagedstock', 'icon' => 'fa fa-times-circle'],
            ['name' => 'Stock Ledger', 'controller' => 'inventory/inventorystockledger', 'icon' => 'fa fa-book'],
            ['name' => 'Reorder Report', 'controller' => 'inventory/inventoryreorderreport', 'icon' => 'fa fa-refresh'],
        ];

        // SELECT `id`, `warehouse_id`, `product_id`, `quantity`, `reserved_quantity`, `available_quantity`, `average_cost`, `last_purchase_price`, `created_at`, `updated_at`, `created_by`, `updated_by`, `is_active`, `is_deleted` FROM `inventory_stock` WHERE 1;;
        // SELECT `id`, `adjustment_no`, `warehouse_id`, `adjustment_date`, `adjustment_type`, `reason`, `remarks`, `created_at`, `updated_at`, `created_by`, `updated_by`, `is_active`, `is_deleted` FROM `inventory_stock_adjustments` WHERE 1;
        // SELECT `id`, `adjustment_id`, `product_id`, `quantity`, `unit_cost`, `total_cost`, `remarks`, `created_at`, `updated_at`, `created_by`, `updated_by`, `is_active`, `is_deleted` FROM `inventory_stock_adjustment_items` WHERE 1;
        // SELECT `id`, `audit_no`, `warehouse_id`, `audit_date`, `status`, `remarks`, `created_at`, `updated_at`, `created_by`, `updated_by`, `is_active`, `is_deleted` FROM `inventory_stock_audits` WHERE 1;
        // SELECT `id`, `audit_id`, `product_id`, `system_quantity`, `physical_quantity`, `variance`, `remarks`, `created_at`, `updated_at`, `created_by`, `updated_by`, `is_active`, `is_deleted` FROM `inventory_stock_audit_items` WHERE 1;
        // SELECT `id`, `movement_no`, `warehouse_id`, `product_id`, `reference_type`, `reference_id`, `movement_type`, `quantity`, `unit_cost`, `total_cost`, `remarks`, `movement_date`, `created_at`, `updated_at`, `created_by`, `updated_by`, `is_active`, `is_deleted` FROM `inventory_stock_movements` WHERE 1;
        // SELECT `id`, `transfer_no`, `from_warehouse`, `to_warehouse`, `transfer_date`, `status`, `remarks`, `created_at`, `updated_at`, `created_by`, `updated_by`, `is_active`, `is_deleted` FROM `inventory_stock_transfers` WHERE 1;
        // SELECT `id`, `transfer_id`, `product_id`, `quantity`, `remarks`, `created_at`, `updated_at`, `created_by`, `updated_by`, `is_active`, `is_deleted` FROM `inventory_stock_transfer_items` WHERE 1
        return $this->render('inventory', compact('modules'));
    }
    public function actionInventorydashboard()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('inventorydashboard');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (!isset($post['flag']) || $post['flag'] != 'load_dashboard') {
                return ['success' => false, 'message' => 'Invalid request.'];
            }
            $db = Yii::$app->db;
            $stats = [];
            $stats['total_stock_items'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock WHERE is_deleted=0")->queryScalar();
            $stats['active_stock_items'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock WHERE is_deleted=0 AND is_active=1")->queryScalar();
            $stats['total_quantity'] = (float)$db->createCommand("SELECT IFNULL(SUM(quantity),0) FROM inventory_stock WHERE is_deleted=0")->queryScalar();
            $stats['available_quantity'] = (float)$db->createCommand("SELECT IFNULL(SUM(available_quantity),0) FROM inventory_stock WHERE is_deleted=0")->queryScalar();
            $stats['reserved_quantity'] = (float)$db->createCommand("SELECT IFNULL(SUM(reserved_quantity),0) FROM inventory_stock WHERE is_deleted=0")->queryScalar();
            $stats['inventory_value'] = (float)$db->createCommand("SELECT IFNULL(SUM(quantity*average_cost),0) FROM inventory_stock WHERE is_deleted=0")->queryScalar();
            $stats['stock_adjustments'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock_adjustments WHERE is_deleted=0")->queryScalar();
            $stats['stock_movements'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock_movements WHERE is_deleted=0")->queryScalar();
            $stats['stock_transfers'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock_transfers WHERE is_deleted=0")->queryScalar();
            $stats['stock_audits'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock_audits WHERE is_deleted=0")->queryScalar();
            $stats['pending_transfers'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock_transfers WHERE is_deleted=0 AND status='Pending'")->queryScalar();
            $stats['pending_audits'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock_audits WHERE is_deleted=0 AND status='Pending'")->queryScalar();

            $warehouseChart = Yii::$app->db->createCommand("
                SELECT
                    w.warehouse_name,
                    IFNULL(SUM(s.quantity),0) AS quantity,
                    IFNULL(SUM(s.available_quantity),0) AS available_quantity,
                    IFNULL(SUM(s.reserved_quantity),0) AS reserved_quantity
                FROM inventory_warehouses w
                LEFT JOIN inventory_stock s
                    ON s.warehouse_id = w.id
                    AND s.is_deleted = 0
                WHERE w.is_deleted = 0  AND w.is_active=1
                GROUP BY w.id,w.warehouse_name
                ORDER BY w.warehouse_name ASC
            ")->queryAll();
            $movementChart = $db->createCommand("
                SELECT
                    movement_type,
                    COUNT(*) total
                FROM inventory_stock_movements
                WHERE is_deleted=0
                GROUP BY movement_type
                ORDER BY total DESC
            ")->queryAll();

            $monthlyMovements = $db->createCommand("
                SELECT
                    DATE_FORMAT(movement_date,'%b %Y') month,
                    COUNT(*) total
                FROM inventory_stock_movements
                WHERE is_deleted=0
                GROUP BY YEAR(movement_date),MONTH(movement_date)
                ORDER BY YEAR(movement_date),MONTH(movement_date)
            ")->queryAll();

            $latestMovements = $db->createCommand("
                SELECT
                    sm.id,
                    sm.movement_no,
                    p.product_name,
                    sm.movement_type,
                    sm.quantity,
                    sm.movement_date
                FROM inventory_stock_movements sm
                LEFT JOIN inventory_products p
                    ON p.id=sm.product_id
                WHERE sm.is_deleted=0
                ORDER BY sm.movement_date DESC
                LIMIT 10
            ")->queryAll();

            $recentTransfers = $db->createCommand("
                SELECT
                    transfer_no,
                    transfer_date,
                    status
                FROM inventory_stock_transfers
                WHERE is_deleted=0
                ORDER BY transfer_date DESC
                LIMIT 10
            ")->queryAll();

            return [
                'success' => true,
                'stats' => $stats,
                'warehouseChart' => $warehouseChart,
                'movementChart' => $movementChart,
                'monthlyMovements' => $monthlyMovements,
                'latestMovements' => $latestMovements,
                'recentTransfers' => $recentTransfers
            ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    public function actionInventorycurrentstock()
    {
        if (Yii::$app->request->isGet) {

            $keyword = trim(Yii::$app->request->get('keyword', ''));
            $warehouse = Yii::$app->request->get('warehouse_id', '');
            $category = Yii::$app->request->get('category_id', '');
            $brand = Yii::$app->request->get('brand_id', '');
            $stock_status = Yii::$app->request->get('stock_status', '');

            $perPage = (int)Yii::$app->request->get('per_page', 10);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;

            $where = " WHERE s.is_deleted=0 ";
            $params = [];

            if ($keyword != '') {
                $where .= " AND (
                    p.product_name LIKE :keyword
                    OR p.sku LIKE :keyword
                    OR p.barcode LIKE :keyword
                )";
                $params[':keyword'] = "%{$keyword}%";
            }

            if ($warehouse != '') {
                $where .= " AND s.warehouse_id=:warehouse";
                $params[':warehouse'] = $warehouse;
            }

            if ($category != '') {
                $where .= " AND p.category_id=:category";
                $params[':category'] = $category;
            }

            if ($brand != '') {
                $where .= " AND p.brand_id=:brand";
                $params[':brand'] = $brand;
            }

            if ($stock_status == 'low') {
                $where .= " AND s.quantity<=p.reorder_level AND s.quantity>0";
            }

            if ($stock_status == 'out') {
                $where .= " AND s.quantity<=0";
            }

            if ($stock_status == 'available') {
                $where .= " AND s.quantity>p.reorder_level";
            }

            $total = Yii::$app->db->createCommand("
                SELECT COUNT(*)
                FROM inventory_stock s
                INNER JOIN inventory_products p
                    ON p.id=s.product_id
                $where
            ", $params)->queryScalar();

            $stocks = Yii::$app->db->createCommand("
                SELECT
                    s.*,
                    p.product_name,
                    p.sku,
                    p.barcode,
                    p.minimum_stock,
                    p.maximum_stock,
                    p.reorder_level,
                    p.selling_price,
                    c.category_name,
                    b.brand_name,
                    u.short_name unit_name,
                    w.warehouse_name,
                    w.warehouse_code
                FROM inventory_stock s
                INNER JOIN inventory_products p
                    ON p.id=s.product_id
                LEFT JOIN inventory_categories c
                    ON c.id=p.category_id
                LEFT JOIN inventory_brands b
                    ON b.id=p.brand_id
                LEFT JOIN inventory_units u
                    ON u.id=p.unit_id
                INNER JOIN inventory_warehouses w
                    ON w.id=s.warehouse_id
                $where
                ORDER BY p.product_name ASC
                LIMIT $offset,$perPage
            ", $params)->queryAll();

            $totalPages = ceil($total / $perPage);

            $warehouses = Yii::$app->db->createCommand("
                SELECT id,warehouse_name
                FROM inventory_warehouses
                WHERE is_deleted=0  and is_active=1
                ORDER BY warehouse_name
            ")->queryAll();

            $categories = Yii::$app->db->createCommand("
                SELECT id,category_name
                FROM inventory_categories
                WHERE is_deleted=0
                ORDER BY category_name
            ")->queryAll();

            $brands = Yii::$app->db->createCommand("
                SELECT id,brand_name
                FROM inventory_brands
                WHERE is_deleted=0
                ORDER BY brand_name
            ")->queryAll();

            $products = Yii::$app->db->createCommand("
                SELECT
                    ip.id,
                    ip.product_name,
                    ip.sku,
                    iu.unit_name,
                    ip.selling_price,
                    ip.purchase_price
                FROM inventory_products ip
                LEFT JOIN inventory_units iu ON ip.unit_id = iu.id
                WHERE ip.is_deleted=0
                ORDER BY ip.product_name
            ")->queryAll();

            return $this->renderPartial(
                'inventorycurrentstock',
                [
                    'stocks' => $stocks,
                    'warehouses' => $warehouses,
                    'categories' => $categories,
                    'brands' => $brands,
                    'products' => $products,
                    'total' => $total,
                    'totalPages' => $totalPages,
                    'page' => $page,
                    'perPage' => $perPage
                ]
            );
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {

            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {

                $keyword = trim($post['keyword'] ?? '');
                $warehouse = $post['warehouse_id'] ?? '';
                $categories = $post['category_id'] ?? [];
                $brands = $post['brand_id'] ?? [];
                $stock_status = $post['stock_status'] ?? '';

                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;

                $where = " WHERE s.is_deleted=0 ";
                $params = [];

                if ($keyword != '') {
                    $where .= " AND (
                        p.product_name LIKE :keyword
                        OR p.sku LIKE :keyword
                        OR p.barcode LIKE :keyword
                    )";
                    $params[':keyword'] = "%{$keyword}%";
                }

                if ($warehouse != '') {
                    $where .= " AND s.warehouse_id=:warehouse";
                    $params[':warehouse'] = $warehouse;
                }

                if (!empty($categories)) {

                    $placeholders = [];

                    foreach ($categories as $k => $value) {
                        $key = ":category{$k}";
                        $placeholders[] = $key;
                        $params[$key] = $value;
                    }

                    $where .= " AND p.category_id IN (" . implode(',', $placeholders) . ")";
                }

                if (!empty($brands)) {

                    $placeholders = [];

                    foreach ($brands as $k => $value) {
                        $key = ":brand{$k}";
                        $placeholders[] = $key;
                        $params[$key] = $value;
                    }

                    $where .= " AND p.brand_id IN (" . implode(',', $placeholders) . ")";
                }

                if ($stock_status == 'low') {
                    $where .= " AND s.quantity<=p.reorder_level AND s.quantity>0";
                }

                if ($stock_status == 'out') {
                    $where .= " AND s.quantity<=0";
                }

                if ($stock_status == 'available') {
                    $where .= " AND s.quantity>p.reorder_level";
                }

                $total = Yii::$app->db->createCommand("
                    SELECT COUNT(*)
                    FROM inventory_stock s
                    INNER JOIN inventory_products p
                        ON p.id=s.product_id
                    $where
                ", $params)->queryScalar();

                $stocks = Yii::$app->db->createCommand("
                    SELECT
                        s.*,
                        p.product_name,
                        p.sku,
                        p.barcode,
                        p.minimum_stock,
                        p.maximum_stock,
                        p.reorder_level,
                        p.selling_price,
                        p.is_active,
                        c.category_name,
                        b.brand_name,
                        u.short_name unit_name,
                        w.warehouse_name,
                        w.warehouse_code,
                        COALESCE(SUM(sii.quantity), 0) as sold_quantity,
                        COALESCE(SUM(sii.quantity * sii.unit_price), 0) as sold_amount,
                        COALESCE(SUM(COALESCE(pi.grand_total, 0) - COALESCE(pi.paid_amount, 0)), 0) as remaining_amount
                    FROM inventory_stock s
                    INNER JOIN inventory_products p
                        ON p.id=s.product_id
                    LEFT JOIN inventory_categories c
                        ON c.id=p.category_id
                    LEFT JOIN inventory_brands b
                        ON b.id=p.brand_id
                    LEFT JOIN inventory_units u
                        ON u.id=p.unit_id
                    INNER JOIN inventory_warehouses w
                        ON w.id=s.warehouse_id
                    LEFT JOIN inventory_sales_invoice_items sii
                        ON sii.product_id=p.id
                    LEFT JOIN inventory_sales_invoices si
                        ON si.id=sii.sales_invoice_id AND si.status IN ('Paid', 'Partially Paid', 'Issued')
                    LEFT JOIN inventory_purchase_invoice_items pii
                        ON pii.product_id=p.id
                    LEFT JOIN inventory_purchase_invoices pi
                        ON pi.id=pii.purchase_invoice_id AND pi.status IN ('Paid', 'Partially Paid', 'Issued') AND pi.is_deleted = 0
                    $where
                    GROUP BY s.id, p.id
                    ORDER BY p.product_name ASC
                    LIMIT $offset,$perPage
                ", $params)->queryAll();

                return [
                    'success' => true,
                    'stocks' => $stocks,
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ];
            }

            // Get product statistics (purchases, sales)
            if (isset($post['flag']) && $post['flag'] == 'get_stats') {
                $productId = (int)($post['product_id'] ?? 0);

                if ($productId > 0) {
                    // Get total purchase quantity and amount
                    $purchaseStats = Yii::$app->db->createCommand("
                        SELECT
                            COALESCE(SUM(pii.quantity), 0) as total_purchase_qty,
                            COALESCE(SUM(pii.quantity * pii.unit_price), 0) as total_purchase_amount
                        FROM inventory_purchase_invoice_items pii
                        LEFT JOIN inventory_purchase_invoices pi ON pi.id = pii.purchase_invoice_id
                        WHERE pii.product_id = :product_id
                            AND pi.status IN ('Paid', 'Partially Paid', 'Issued')
                            AND pi.is_deleted = 0
                    ")->bindValue(':product_id', $productId)->queryOne();

                    // Get total sales quantity and amount
                    $salesStats = Yii::$app->db->createCommand("
                        SELECT
                            COALESCE(SUM(sii.quantity), 0) as total_sold_qty,
                            COALESCE(SUM(sii.quantity * sii.unit_price), 0) as total_sold_amount
                        FROM inventory_sales_invoice_items sii
                        LEFT JOIN inventory_sales_invoices si ON si.id = sii.sales_invoice_id
                        WHERE sii.product_id = :product_id
                            AND si.status IN ('Paid', 'Partially Paid', 'Issued')
                            AND si.is_deleted = 0
                    ")->bindValue(':product_id', $productId)->queryOne();

                    $stats = array_merge(
                        $purchaseStats ?? ['total_purchase_qty' => 0, 'total_purchase_amount' => 0],
                        $salesStats ?? ['total_sold_qty' => 0, 'total_sold_amount' => 0]
                    );

                    return [
                        'success' => true,
                        'stats' => $stats,
                        'message' => 'Product statistics loaded successfully'
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Invalid product ID'
                ];
            }

            // Update product active/inactive status
            if (isset($post['flag']) && $post['flag'] == 'update_status') {
                $productId = (int)($post['product_id'] ?? 0);
                $isActive = (int)($post['is_active'] ?? 0);

                if ($productId > 0) {
                    Yii::$app->db->createCommand()
                        ->update('inventory_products', ['is_active' => $isActive], ['id' => $productId])
                        ->execute();

                    return [
                        'success' => true,
                        'message' => $isActive ? 'Product marked as active' : 'Product marked as inactive'
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Invalid product ID'
                ];
            }

            $stock = Yii::$app->request->post();

            $stock_id = Yii::$app->request->post('id');

            if ($stock_id && isset($stock['delete']) && $stock['delete'] == 1) {

                $result = Yii::$app->db->createCommand()
                    ->update(
                        'inventory_stock',
                        [
                            'is_deleted' => 1,
                            'is_active' => 0,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => $this->currentUserId()
                        ],
                        'id=:id',
                        [':id' => $stock_id]
                    )
                    ->execute();

                return $result
                    ? [
                        'success' => true,
                        'message' => 'Stock deleted successfully.'
                    ]
                    : [
                        'success' => false,
                        'message' => 'Failed to delete stock.'
                    ];
            }

            if (empty($stock['warehouse_id'])) {

                return [
                    'success' => false,
                    'message' => 'Warehouse is required.'
                ];
            }

            if (empty($stock['product_id'])) {

                return [
                    'success' => false,
                    'message' => 'Product is required.'
                ];
            }

            $quantity = (float)($stock['quantity'] ?? 0);

            $reserved = (float)($stock['reserved_quantity'] ?? 0);
            if ($quantity < $reserved) {
                return [
                    'success' => false,
                    'message' => 'Stock Quantity must be less than Reserved.'
                ];
            }
            $stockData = [
                'warehouse_id' => $stock['warehouse_id'],
                'product_id' => $stock['product_id'],
                'quantity' => $quantity,
                'reserved_quantity' => $reserved,
                'available_quantity' => $quantity - $reserved,
                'average_cost' => $stock['average_cost'] ?? 0,
                'last_purchase_price' => $stock['last_purchase_price'] ?? 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->currentUserId()
            ];
            if ($stock_id) {
                $result = Yii::$app->db->createCommand()
                    ->update('inventory_stock', $stockData, 'id=:id', [':id' => $stock_id])
                    ->execute();
                return $result
                    ? [
                        'success' => true,
                        'message' => 'Stock updated successfully.'
                    ]
                    : [
                        'success' => false,
                        'message' => 'Failed to update stock.'
                    ];
            }

            $exists = Yii::$app->db->createCommand("
                SELECT id
                FROM inventory_stock
                WHERE warehouse_id=:warehouse_id
                AND product_id=:product_id
                AND is_deleted=0
            ", [
                ':warehouse_id' => $stock['warehouse_id'],
                ':product_id' => $stock['product_id']
            ])->queryScalar();

            if ($exists) {

                return [
                    'success' => false,
                    'message' => 'Stock record already exists for this warehouse and product.'
                ];
            }

            $stockData['created_at'] = date('Y-m-d H:i:s');

            $stockData['created_by'] = Yii::$app->user->id ?? null;

            $stockData['is_deleted'] = 0;

            $result = Yii::$app->db->createCommand()
                ->insert('inventory_stock', $stockData)
                ->execute();

            return $result
                ? [
                    'success' => true,
                    'message' => 'Stock created successfully.'
                ]
                : [
                    'success' => false,
                    'message' => 'Failed to create stock.'
                ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    public function actionInventoryreservedstock()
    {
        if (Yii::$app->request->isGet) {

            $keyword = trim(Yii::$app->request->get('keyword', ''));
            $warehouse = Yii::$app->request->get('warehouse_id', '');
            $category = Yii::$app->request->get('category_id', '');
            $brand = Yii::$app->request->get('brand_id', '');

            $perPage = (int)Yii::$app->request->get('per_page', 10);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;

            $where = " WHERE s.is_deleted=0 AND s.reserved_quantity>0 ";
            $params = [];

            if ($keyword != '') {
                $where .= " AND (
                    p.product_name LIKE :keyword
                    OR p.sku LIKE :keyword
                    OR p.barcode LIKE :keyword
                )";
                $params[':keyword'] = "%{$keyword}%";
            }

            if ($warehouse != '') {
                $where .= " AND s.warehouse_id=:warehouse";
                $params[':warehouse'] = $warehouse;
            }

            if ($category != '') {
                $where .= " AND p.category_id=:category";
                $params[':category'] = $category;
            }

            if ($brand != '') {
                $where .= " AND p.brand_id=:brand";
                $params[':brand'] = $brand;
            }

            $total = Yii::$app->db->createCommand("
                SELECT COUNT(*)
                FROM inventory_stock s
                INNER JOIN inventory_products p
                    ON p.id=s.product_id
                $where
            ", $params)->queryScalar();
            $stocks = Yii::$app->db->createCommand("
                SELECT
                    s.*,
                    p.product_name,
                    p.sku,
                    p.barcode,
                    c.category_name,
                    b.brand_name,
                    u.short_name unit_name,
                    w.warehouse_name,
                    w.warehouse_code
                FROM inventory_stock s
                INNER JOIN inventory_products p
                    ON p.id=s.product_id
                LEFT JOIN inventory_categories c
                    ON c.id=p.category_id
                LEFT JOIN inventory_brands b
                    ON b.id=p.brand_id
                LEFT JOIN inventory_units u
                    ON u.id=p.unit_id
                INNER JOIN inventory_warehouses w
                    ON w.id=s.warehouse_id
                $where
                ORDER BY s.reserved_quantity DESC
                LIMIT $offset,$perPage
            ", $params)->queryAll();

            $totalPages = ceil($total / $perPage);

            $warehouses = Yii::$app->db->createCommand("
                SELECT id,warehouse_name
                FROM inventory_warehouses
                WHERE is_deleted=0 AND is_active=1
                ORDER BY warehouse_name
            ")->queryAll();

            $categories = Yii::$app->db->createCommand("
                SELECT id,category_name
                FROM inventory_categories
                WHERE is_deleted=0
                ORDER BY category_name
            ")->queryAll();

            $brands = Yii::$app->db->createCommand("
                SELECT id,brand_name
                FROM inventory_brands
                WHERE is_deleted=0
                ORDER BY brand_name
            ")->queryAll();

            return $this->renderPartial(
                'inventoryreservedstock',
                [
                    'stocks' => $stocks,
                    'warehouses' => $warehouses,
                    'categories' => $categories,
                    'brands' => $brands,
                    'total' => $total,
                    'totalPages' => $totalPages,
                    'page' => $page,
                    'perPage' => $perPage
                ]
            );
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {

            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {

                $keyword = trim($post['keyword'] ?? '');
                $warehouse = $post['warehouse_id'] ?? '';
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;

                $where = " WHERE s.is_deleted=0 AND s.reserved_quantity>0 ";
                $params = [];

                if ($keyword != '') {
                    $where .= " AND (
                        p.product_name LIKE :keyword
                        OR p.sku LIKE :keyword
                        OR p.barcode LIKE :keyword
                    )";
                    $params[':keyword'] = "%{$keyword}%";
                }

                if ($warehouse != '') {
                    $where .= " AND s.warehouse_id=:warehouse";
                    $params[':warehouse'] = $warehouse;
                }

                $total = Yii::$app->db->createCommand("
                    SELECT COUNT(*)
                    FROM inventory_stock s
                    INNER JOIN inventory_products p
                        ON p.id=s.product_id
                    $where
                ", $params)->queryScalar();

                $stocks = Yii::$app->db->createCommand("
                    SELECT
                        s.*,
                        p.product_name,
                        p.sku,
                        p.barcode,
                        w.warehouse_name,
                        w.warehouse_code
                    FROM inventory_stock s
                    INNER JOIN inventory_products p
                        ON p.id=s.product_id
                    INNER JOIN inventory_warehouses w
                        ON w.id=s.warehouse_id
                    $where
                    ORDER BY s.reserved_quantity DESC
                    LIMIT $offset,$perPage
                ", $params)->queryAll();

                return [
                    'success' => true,
                    'stocks' => $stocks,
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ];
            }
            $stock = Yii::$app->request->post();

            $stock_id = Yii::$app->request->post('id');

            if (empty($stock_id)) {
                return [
                    'success' => false,
                    'message' => 'Stock id is required.'
                ];
            }

            if (!isset($stock['reserved_quantity'])) {
                return [
                    'success' => false,
                    'message' => 'Reserved quantity is required.'
                ];
            }

            $row = Yii::$app->db->createCommand("
                SELECT *
                FROM inventory_stock
                WHERE id=:id
                AND is_deleted=0
            ", [
                ':id' => $stock_id
            ])->queryOne();

            if (!$row) {
                return [
                    'success' => false,
                    'message' => 'Stock record not found.'
                ];
            }

            $reserved = (float)$stock['reserved_quantity'];

            if ($reserved > $row['quantity']) {
                return [
                    'success' => false,
                    'message' => 'Reserved quantity cannot exceed stock quantity.'
                ];
            }
            $result = Yii::$app->db->createCommand()
                ->update(
                    'inventory_stock',
                    [
                        'reserved_quantity' => $reserved,
                        'available_quantity' => $row['quantity'] - $reserved,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Yii::$app->user->id ?? null
                    ],
                    'id=:id',
                    [
                        ':id' => $stock_id
                    ]
                )
                ->execute();

            return $result
                ? [
                    'success' => true,
                    'message' => 'Reserved stock updated successfully.'
                ]
                : [
                    'success' => false,
                    'message' => 'Failed to update reserved stock.'
                ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    public function actionInventorystockadjustment()
    {
        if(Yii::$app->request->isPost){

            $post=Yii::$app->request->post();
            $flag=$post['flag']??'';
            $user_id=$this->currentUserId();

            try{

                if($flag=='search'){
                    
                    $limit = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                    $page = !empty($post['page']) ? (int)$post['page'] : 1;
                    $offset = ($page - 1) * $limit;
                    
                    $where="a.is_deleted=0";
                    $params=[];

                    if(!empty($post['warehouse_id'])){
                        $where.=" AND a.warehouse_id=:warehouse_id";
                        $params[':warehouse_id']=$post['warehouse_id'];
                    }

                    if(!empty($post['adjustment_type'])){
                        $where.=" AND a.adjustment_type=:type";
                        $params[':type']=$post['adjustment_type'];
                    }

                    if(!empty($post['keyword'])){
                        $where.=" AND a.adjustment_no LIKE :keyword";
                        $params[':keyword']='%'.$post['keyword'].'%';
                    }

                    $total=Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_stock_adjustments a
                        WHERE $where
                    ")->bindValues($params)->queryScalar();

                    $rows=Yii::$app->db->createCommand("
                        SELECT
                            a.*,
                            w.warehouse_name,
                            COUNT(i.id) item_count,
                            COALESCE(SUM(i.total_cost),0) total_cost
                        FROM inventory_stock_adjustments a
                        INNER JOIN inventory_warehouses w
                        ON w.id=a.warehouse_id
                        LEFT JOIN inventory_stock_adjustment_items i
                        ON i.adjustment_id=a.id
                        AND i.is_deleted=0
                        WHERE $where
                        GROUP BY a.id
                        ORDER BY a.id DESC
                        LIMIT $limit OFFSET $offset
                    ")->bindValues($params)->queryAll();

                    if(isset($post['id']) && $post['id']!=''){

                        $items=Yii::$app->db->createCommand("
                            SELECT
                                i.product_id,
                                i.quantity,
                                i.unit_cost,
                                p.product_name,
                                p.sku
                            FROM inventory_stock_adjustment_items i
                            INNER JOIN inventory_products p
                            ON p.id=i.product_id
                            WHERE i.adjustment_id=:id
                            AND i.is_deleted=0
                        ")->bindValue(':id',$post['id'])->queryAll();

                        return $this->jsonResponse(true,'Data loaded successfully!',[
                            'data'=>$rows,
                            'items'=>$items,
                            'total'=>(int)$total
                        ]);
                    }

                    return $this->jsonResponse(true,'Data loaded successfully!',[
                        'data'=>$rows,
                        'total'=>(int)$total,
                        'page'=>$page,
                        'limit'=>$limit
                    ]);
                }

                if($flag=='create'){

                    return $this->saveStockAdjustment(
                        $post,
                        $user_id
                    );

                }

                if($flag=='update'){

                    if(empty($post['id'])){
                        return $this->jsonResponse(false,'Record id is required.');
                    }

                    return $this->saveStockAdjustment(
                        $post,
                        $user_id,
                        $post['id']
                    );

                }

                if($flag=='delete'){

                    if(empty($post['id'])){
                        return $this->jsonResponse(false,'Record id is required.');
                    }

                    return $this->deleteStockAdjustment(
                        $post['id'],
                        $user_id
                    );

                }

                return $this->jsonResponse(false,'Invalid request flag.');

            }catch(\Exception $e){

                return $this->jsonResponse(false,$e->getMessage());

            }
        }


        $warehouses=Yii::$app->db->createCommand("
            SELECT id,warehouse_name
            FROM inventory_warehouses
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY warehouse_name
        ")->queryAll();


        $products=Yii::$app->db->createCommand("
            SELECT id,product_name,sku,purchase_price
            FROM inventory_products
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY product_name
        ")->queryAll();


        return $this->renderPartial(
            'inventorystockadjustment',
            [
                'warehouses'=>$warehouses,
                'products'=>$products
            ]
        );
    }
    private function saveStockAdjustment($post,$user_id,$adjustment_id=null,$reason=null)
    {
        if(empty($post['warehouse_id'])||empty($post['adjustment_type'])){
            return $this->jsonResponse(false,'Warehouse and adjustment type are required.');
        }

        // When a caller (e.g. Damaged Stock) passes an explicit $reason, it always wins over
        // whatever was submitted in $post['reason'] so the record is reliably tagged/filterable.
        if($reason !== null){
            $post['reason'] = $reason;
        }

        $items=$post['items']??[];
        if(!is_array($items)){
            $items=json_decode($items,true);
        }
        if(empty($items)){
            return $this->jsonResponse(false,'At least one item is required.');
        }
        $trans=Yii::$app->db->beginTransaction();
        try{
            $warehouse_id=$post['warehouse_id'];
            $type=$post['adjustment_type'];
            if($adjustment_id){
                $this->reverseAdjustmentStockEffect($adjustment_id,$user_id);
                Yii::$app->db->createCommand()->update(
                    'inventory_stock_adjustments',
                    [
                        'warehouse_id'=>$warehouse_id,
                        'adjustment_date'=>$post['adjustment_date']??date('Y-m-d'),
                        'adjustment_type'=>$type,
                        'reason'=>$post['reason']??null,
                        'remarks'=>$post['remarks']??null,
                        'updated_by'=>$user_id
                    ],
                    ['id'=>$adjustment_id]
                )->execute();

                Yii::$app->db->createCommand()->update(
                    'inventory_stock_adjustment_items',
                    [
                        'is_deleted'=>1
                    ],
                    [
                        'adjustment_id'=>$adjustment_id
                    ]
                )->execute();

            }else{

                Yii::$app->db->createCommand()->insert(
                    'inventory_stock_adjustments',
                    [
                        'adjustment_no'=>$this->generateDocNo('ADJ'),
                        'warehouse_id'=>$warehouse_id,
                        'adjustment_date'=>$post['adjustment_date']??date('Y-m-d'),
                        'adjustment_type'=>$type,
                        'reason'=>$post['reason']??null,
                        'remarks'=>$post['remarks']??null,
                        'created_by'=>$user_id,
                        'updated_by'=>$user_id
                    ]
                )->execute();

                $adjustment_id=Yii::$app->db->getLastInsertID();

            }


            foreach($items as $item){

                if(empty($item['product_id'])||empty($item['quantity'])){
                    continue;
                }

                $product_id=$item['product_id'];
                $quantity=(float)$item['quantity'];
                $cost=(float)($item['unit_cost']??0);


                Yii::$app->db->createCommand()->insert(
                    'inventory_stock_adjustment_items',
                    [
                        'adjustment_id'=>$adjustment_id,
                        'product_id'=>$product_id,
                        'quantity'=>$quantity,
                        'unit_cost'=>$cost,
                        'total_cost'=>$quantity*$cost,
                        'remarks'=>$item['remarks']??null,
                        'created_by'=>$user_id,
                        'updated_by'=>$user_id
                    ]
                )->execute();


                $stock=Yii::$app->db->createCommand("
                    SELECT *
                    FROM inventory_stock
                    WHERE warehouse_id=:warehouse
                    AND product_id=:product
                    AND is_deleted=0
                ")->bindValues([
                    ':warehouse'=>$warehouse_id,
                    ':product'=>$product_id
                ])->queryOne();


                if($type=='Increase'){

                    $newQty=$quantity;

                    if($stock){

                        $newQty=$stock['quantity']+$quantity;

                        Yii::$app->db->createCommand()->update(
                            'inventory_stock',
                            [
                                'quantity'=>$newQty,
                                'available_quantity'=>$newQty-$stock['reserved_quantity'],
                                'updated_by'=>$user_id
                            ],
                            [
                                'id'=>$stock['id']
                            ]
                        )->execute();

                    }else{

                        Yii::$app->db->createCommand()->insert(
                            'inventory_stock',
                            [
                                'warehouse_id'=>$warehouse_id,
                                'product_id'=>$product_id,
                                'quantity'=>$quantity,
                                'reserved_quantity'=>0,
                                'available_quantity'=>$quantity,
                                'average_cost'=>$cost,
                                'created_by'=>$user_id,
                                'updated_by'=>$user_id
                            ]
                        )->execute();

                    }

                }else{

                    if(!$stock){

                        throw new \Exception('Stock not found.');

                    }


                    if($quantity>$stock['available_quantity']){

                        throw new \Exception(
                            'Insufficient available stock for product.'
                        );

                    }


                    $newQty=$stock['quantity']-$quantity;


                    Yii::$app->db->createCommand()->update(
                        'inventory_stock',
                        [
                            'quantity'=>$newQty,
                            'available_quantity'=>$newQty-$stock['reserved_quantity'],
                            'updated_by'=>$user_id
                        ],
                        [
                            'id'=>$stock['id']
                        ]
                    )->execute();

                }

                            Yii::$app->db->createCommand()->insert(
                    'inventory_stock_movements',
                    [
                        'movement_no'=>$this->generateDocNo('ADJM'),
                        'warehouse_id'=>$warehouse_id,
                        'product_id'=>$product_id,
                        'reference_type'=>'Adjustment',
                        'reference_id'=>$adjustment_id,
                        'movement_type'=>$type=='Increase'?'IN':'OUT',
                        'quantity'=>$quantity,
                        'unit_cost'=>$cost,
                        'total_cost'=>$quantity*$cost,
                        'remarks'=>$item['remarks']??$post['reason']??null,
                        'created_by'=>$user_id,
                        'updated_by'=>$user_id
                    ]
                )->execute();

            }

            $trans->commit();

            return $this->jsonResponse(
                true,
                'Data Saved successfully!',
                [
                    'id'=>$adjustment_id
                ]
            );

        }catch(\Exception $e){

            $trans->rollBack();

            return $this->jsonResponse(
                false,
                $e->getMessage()
            );

        }
    }
    private function reverseAdjustmentStockEffect($adjustment_id,$user_id)
    {
        $adjustment=Yii::$app->db->createCommand("
            SELECT *
            FROM inventory_stock_adjustments
            WHERE id=:id
        ")->bindValue(':id',$adjustment_id)->queryOne();

        if(!$adjustment){
            return;
        }


        $items=Yii::$app->db->createCommand("
            SELECT *
            FROM inventory_stock_adjustment_items
            WHERE adjustment_id=:id
            AND is_deleted=0
        ")->bindValue(':id',$adjustment_id)->queryAll();


        foreach($items as $item){

            $stock=Yii::$app->db->createCommand("
                SELECT *
                FROM inventory_stock
                WHERE warehouse_id=:warehouse
                AND product_id=:product
                AND is_deleted=0
            ")->bindValues([
                ':warehouse'=>$adjustment['warehouse_id'],
                ':product'=>$item['product_id']
            ])->queryOne();


            if(!$stock){
                continue;
            }
            if($adjustment['adjustment_type']=='Increase'){
                $newQty=$stock['quantity']-$item['quantity'];
            }else{
                $newQty=$stock['quantity']+$item['quantity'];
            }
            Yii::$app->db->createCommand()->update(
                'inventory_stock',
                [
                    'quantity'=>$newQty,
                    'available_quantity'=>$newQty-$stock['reserved_quantity'],
                    'updated_by'=>$user_id
                ],
                [
                    'id'=>$stock['id']
                ]
            )->execute();

        }
    }
    private function deleteStockAdjustment($id,$user_id)
    {
        $trans=Yii::$app->db->beginTransaction();

        try{

            $this->reverseAdjustmentStockEffect($id,$user_id);

            Yii::$app->db->createCommand()->update(
                'inventory_stock_adjustment_items',
                [
                    'is_deleted'=>1,
                    'updated_by'=>$user_id
                ],
                [
                    'adjustment_id'=>$id
                ]
            )->execute();


            Yii::$app->db->createCommand()->update(
                'inventory_stock_movements',
                [
                    'is_deleted'=>1,
                    'updated_by'=>$user_id
                ],
                [
                    'reference_id'=>$id,
                    'reference_type'=>'Adjustment'
                ]
            )->execute();


            $result=Yii::$app->db->createCommand()->update(
                'inventory_stock_adjustments',
                [
                    'is_deleted'=>1,
                    'is_active'=>0,
                    'updated_by'=>$user_id,
                    'updated_at'=>date('Y-m-d H:i:s')
                ],
                [
                    'id'=>$id
                ]
            )->execute();


            $trans->commit();


            return $result
                ? $this->jsonResponse(true,'Data Deleted successfully!')
                : $this->jsonResponse(false,'Failed to delete adjustment.');

        }catch(\Exception $e){

            $trans->rollBack();

            return $this->jsonResponse(
                false,
                $e->getMessage()
            );

        }
    }
    public function actionInventorystockledger()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $flag = $post['flag'] ?? '';
            $user_id = $this->currentUserId();

            try {
                if ($flag == 'search') {
                    
                    $limit = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                    $page = !empty($post['page']) ? (int)$post['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    $where = "m.is_deleted = 0";
                    $params = [];

                    if (!empty($post['warehouse_id'])) {
                        $where .= " AND m.warehouse_id = :warehouse_id";
                        $params[':warehouse_id'] = $post['warehouse_id'];
                    }
                    if (!empty($post['product_id'])) {
                        $where .= " AND m.product_id = :product_id";
                        $params[':product_id'] = $post['product_id'];
                    }
                    if (!empty($post['reference_type'])) {
                        $where .= " AND m.reference_type = :reference_type";
                        $params[':reference_type'] = $post['reference_type'];
                    }
                    if (!empty($post['movement_type'])) {
                        $where .= " AND m.movement_type = :movement_type";
                        $params[':movement_type'] = $post['movement_type'];
                    }
                    if (!empty($post['date_from'])) {
                        $where .= " AND DATE(m.movement_date) >= :date_from";
                        $params[':date_from'] = $post['date_from'];
                    }
                    if (!empty($post['date_to'])) {
                        $where .= " AND DATE(m.movement_date) <= :date_to";
                        $params[':date_to'] = $post['date_to'];
                    }
                    if (!empty($post['keyword'])) {
                        $where .= " AND (p.product_name LIKE :kw OR p.sku LIKE :kw OR m.movement_no LIKE :kw)";
                        $params[':kw'] = '%' . $post['keyword'] . '%';
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*) FROM inventory_stock_movements m
                        INNER JOIN inventory_products p ON p.id = m.product_id
                        WHERE $where
                    ")->bindValues($params)->queryScalar();

                    $rows = Yii::$app->db->createCommand("
                        SELECT m.*, p.product_name, p.sku, w.warehouse_name
                        FROM inventory_stock_movements m
                        INNER JOIN inventory_products p ON p.id = m.product_id
                        INNER JOIN inventory_warehouses w ON w.id = m.warehouse_id
                        WHERE $where
                        ORDER BY m.movement_date DESC, m.id DESC
                        LIMIT $limit OFFSET $offset
                    ")->bindValues($params)->queryAll();

                    return $this->jsonResponse(true, 'Data loaded successfully!', [
                        'data' => $rows,
                        'total' => (int)$total,
                        'page' => $page,
                        'limit' => $limit,
                    ]);
                } elseif ($flag == 'update') {
                    // Ledger entries are system generated; only remarks are editable here.
                    // Quantity corrections should go through Stock Adjustment for full traceability.
                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }

                    Yii::$app->db->createCommand()->update('inventory_stock_movements', [
                        'remarks' => $post['remarks'] ?? null,
                        'updated_by' => $user_id,
                    ], ['id' => $post['id'], 'is_deleted' => 0])->execute();

                    return $this->jsonResponse(true, 'Data Updated successfully!');
                } elseif ($flag == 'delete') {
                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }

                    $trans = Yii::$app->db->beginTransaction();
                    try {
                        $movement = Yii::$app->db->createCommand(
                            "SELECT * FROM inventory_stock_movements WHERE id = :id AND is_deleted = 0"
                        )->bindValues([':id' => $post['id']])->queryOne();

                        if (!$movement) {
                            throw new \Exception('Ledger entry not found.');
                        }

                        // Reverse the stock effect of this movement before marking it deleted, so
                        // inventory_stock stays consistent with the ledger (mirrors Stock Movement delete).
                        $stock = Yii::$app->db->createCommand("
                            SELECT * FROM inventory_stock WHERE warehouse_id = :w AND product_id = :p AND is_deleted = 0
                        ")->bindValues([':w' => $movement['warehouse_id'], ':p' => $movement['product_id']])->queryOne();

                        if ($stock) {
                            $reverseDelta = $movement['movement_type'] == 'IN' ? -$movement['quantity'] : $movement['quantity'];
                            $newQty = $stock['quantity'] + $reverseDelta;
                            Yii::$app->db->createCommand()->update('inventory_stock', [
                                'quantity' => $newQty,
                                'available_quantity' => $newQty - $stock['reserved_quantity'],
                                'updated_by' => $user_id,
                            ], ['id' => $stock['id']])->execute();
                        }

                        Yii::$app->db->createCommand()->update('inventory_stock_movements', [
                            'is_deleted' => 1,
                            'is_active' => 0,
                            'updated_by' => $user_id,
                        ], ['id' => $post['id']])->execute();

                        $trans->commit();
                        return $this->jsonResponse(true, 'Data Deleted successfully!');
                    } catch (\Exception $inner) {
                        $trans->rollBack();
                        return $this->jsonResponse(false, $inner->getMessage() ?: 'Failed to delete ledger entry. Please try again.');
                    }
                }

                return $this->jsonResponse(false, 'Invalid request flag.');
            } catch (\Exception $e) {
                return $this->jsonResponse(false, 'Failed to update data. Please try again.');
            }
        }

        $warehouses = Yii::$app->db->createCommand("SELECT id, warehouse_name FROM inventory_warehouses WHERE is_deleted = 0 and is_active=1 ORDER BY warehouse_name")->queryAll();
        $products = Yii::$app->db->createCommand("SELECT id, product_name, sku FROM inventory_products WHERE is_deleted = 0 ORDER BY product_name")->queryAll();

        return $this->renderPartial('inventorystockledger', [
            'warehouses' => $warehouses,
            'products' => $products,
        ]);
    }
 
    public function actionInventorystockmovement()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $flag = $post['flag'] ?? '';
            $user_id = $this->currentUserId();

            try {
                if ($flag == 'search') {
                    
                    $limit = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                    $page = !empty($post['page']) ? (int)$post['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    $where = "m.is_deleted = 0";
                    $params = [];
                    if (!empty($post['warehouse_id'])) {
                        $where .= " AND m.warehouse_id = :warehouse_id";
                        $params[':warehouse_id'] = $post['warehouse_id'];
                    }
                    if (!empty($post['product_id'])) {
                        $where .= " AND m.product_id = :product_id";
                        $params[':product_id'] = $post['product_id'];
                    }
                    if (!empty($post['movement_type'])) {
                        $where .= " AND m.movement_type = :movement_type";
                        $params[':movement_type'] = $post['movement_type'];
                    }
                    if (!empty($post['keyword'])) {
                        $where .= " AND (p.product_name LIKE :kw OR m.movement_no LIKE :kw)";
                        $params[':kw'] = '%' . $post['keyword'] . '%';
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*) FROM inventory_stock_movements m
                        INNER JOIN inventory_products p ON p.id = m.product_id
                        WHERE $where
                    ")->bindValues($params)->queryScalar();

                    $rows = Yii::$app->db->createCommand("
                        SELECT m.*, p.product_name, p.sku, w.warehouse_name
                        FROM inventory_stock_movements m
                        INNER JOIN inventory_products p ON p.id = m.product_id
                        INNER JOIN inventory_warehouses w ON w.id = m.warehouse_id
                        WHERE $where
                        ORDER BY m.movement_date DESC, m.id DESC
                        LIMIT $limit OFFSET $offset
                    ")->bindValues($params)->queryAll();

                    return $this->jsonResponse(true, 'Data loaded successfully!', [
                        'data' => $rows,
                        'total' => (int)$total,
                        'page' => $page,
                        'limit' => $limit,
                    ]);
                } elseif ($flag == 'create') {
                    if (empty($post['warehouse_id']) || empty($post['product_id']) || empty($post['movement_type']) || !isset($post['quantity'])) {
                        return $this->jsonResponse(false, 'Warehouse, Product, Movement Type and Quantity are required.');
                    }

                    $warehouse_id = $post['warehouse_id'];
                    $product_id = $post['product_id'];
                    $movement_type = $post['movement_type']; // IN | OUT
                    $quantity = (float)$post['quantity'];
                    $unit_cost = (float)($post['unit_cost'] ?? 0);

                    if ($quantity <= 0) {
                        return $this->jsonResponse(false, 'Quantity must be greater than zero.');
                    }

                    $trans = Yii::$app->db->beginTransaction();
                    try {
                        Yii::$app->db->createCommand()->insert('inventory_stock_movements', [
                            'movement_no' => $this->generateDocNo('MOV'),
                            'warehouse_id' => $warehouse_id,
                            'product_id' => $product_id,
                            'reference_type' => $post['reference_type'] ?? 'Adjustment',
                            'reference_id' => $post['reference_id'] ?? null,
                            'movement_type' => $movement_type,
                            'quantity' => $quantity,
                            'unit_cost' => $unit_cost,
                            'total_cost' => $unit_cost * $quantity,
                            'remarks' => $post['remarks'] ?? null,
                            'movement_date' => $post['movement_date'] ?? date('Y-m-d H:i:s'),
                            'created_by' => $user_id,
                            'updated_by' => $user_id,
                        ])->execute();
                        $movement_id = Yii::$app->db->getLastInsertID();

                        $stock = Yii::$app->db->createCommand("
                            SELECT * FROM inventory_stock WHERE warehouse_id = :w AND product_id = :p AND is_deleted = 0
                        ")->bindValues([':w' => $warehouse_id, ':p' => $product_id])->queryOne();

                        $delta = $movement_type == 'IN' ? $quantity : -$quantity;

                        if ($stock) {
                            if ($movement_type == 'OUT' && ($stock['quantity'] + $delta) < 0) {
                                throw new \Exception('Insufficient stock for this movement.');
                            }
                            $newQty = $stock['quantity'] + $delta;
                            Yii::$app->db->createCommand()->update('inventory_stock', [
                                'quantity' => $newQty,
                                'available_quantity' => $newQty - $stock['reserved_quantity'],
                                'average_cost' => $movement_type == 'IN' && $unit_cost > 0 ? $unit_cost : $stock['average_cost'],
                                'last_purchase_price' => $movement_type == 'IN' && $unit_cost > 0 ? $unit_cost : $stock['last_purchase_price'],
                                'updated_by' => $user_id,
                            ], ['id' => $stock['id']])->execute();
                        } else {
                            if ($movement_type == 'OUT') {
                                throw new \Exception('Insufficient stock for this movement.');
                            }
                            Yii::$app->db->createCommand()->insert('inventory_stock', [
                                'warehouse_id' => $warehouse_id,
                                'product_id' => $product_id,
                                'quantity' => $quantity,
                                'reserved_quantity' => 0,
                                'available_quantity' => $quantity,
                                'average_cost' => $unit_cost,
                                'last_purchase_price' => $unit_cost,
                                'created_by' => $user_id,
                                'updated_by' => $user_id,
                            ])->execute();
                        }

                        $trans->commit();
                        return $this->jsonResponse(true, 'Data Saved successfully!', ['id' => $movement_id]);
                    } catch (\Exception $inner) {
                        $trans->rollBack();
                        return $this->jsonResponse(false, $inner->getMessage() ?: 'Failed to save movement. Please try again.');
                    }
                } elseif ($flag == 'update') {
                    // Only descriptive fields are editable post-creation; quantity changes must go through a new movement
                    // to keep the stock ledger accurate.
                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }
                    Yii::$app->db->createCommand()->update('inventory_stock_movements', [
                        'remarks' => $post['remarks'] ?? null,
                        'updated_by' => $user_id,
                    ], ['id' => $post['id'], 'is_deleted' => 0])->execute();

                    return $this->jsonResponse(true, 'Data Updated successfully!');
                } elseif ($flag == 'delete') {
                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }

                    $trans = Yii::$app->db->beginTransaction();
                    try {
                        $movement = Yii::$app->db->createCommand(
                            "SELECT * FROM inventory_stock_movements WHERE id = :id AND is_deleted = 0"
                        )->bindValues([':id' => $post['id']])->queryOne();

                        if (!$movement) {
                            throw new \Exception('Movement not found.');
                        }

                        // Reverse the stock effect of this movement before marking it deleted.
                        $stock = Yii::$app->db->createCommand("
                            SELECT * FROM inventory_stock WHERE warehouse_id = :w AND product_id = :p AND is_deleted = 0
                        ")->bindValues([':w' => $movement['warehouse_id'], ':p' => $movement['product_id']])->queryOne();

                        if ($stock) {
                            $reverseDelta = $movement['movement_type'] == 'IN' ? -$movement['quantity'] : $movement['quantity'];
                            $newQty = $stock['quantity'] + $reverseDelta;
                            Yii::$app->db->createCommand()->update('inventory_stock', [
                                'quantity' => $newQty,
                                'available_quantity' => $newQty - $stock['reserved_quantity'],
                                'updated_by' => $user_id,
                            ], ['id' => $stock['id']])->execute();
                        }

                        Yii::$app->db->createCommand()->update('inventory_stock_movements', [
                            'is_deleted' => 1,
                            'is_active' => 0,
                            'updated_by' => $user_id,
                        ], ['id' => $post['id']])->execute();

                        $trans->commit();
                        return $this->jsonResponse(true, 'Data Deleted successfully!');
                    } catch (\Exception $inner) {
                        $trans->rollBack();
                        return $this->jsonResponse(false, $inner->getMessage() ?: 'Failed to delete movement. Please try again.');
                    }
                }

                return $this->jsonResponse(false, 'Invalid request flag.');
            } catch (\Exception $e) {
                return $this->jsonResponse(false, 'Failed to update data. Please try again.');
            }
        }

        $warehouses = Yii::$app->db->createCommand("SELECT id, warehouse_name FROM inventory_warehouses WHERE is_deleted = 0  and is_active=1 ORDER BY warehouse_name")->queryAll();
        $products = Yii::$app->db->createCommand("SELECT id, product_name, sku FROM inventory_products WHERE is_deleted = 0 ORDER BY product_name")->queryAll();

        return $this->renderPartial('inventorystockmovement', [
            'warehouses' => $warehouses,
            'products' => $products,
        ]);
    }
    public function actionInventoryopeningstock()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $flag = $post['flag'] ?? '';
            $user_id = $this->currentUserId();

            try {
                if ($flag == 'search') {
                    
                    $limit = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                    $page = !empty($post['page']) ? (int)$post['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    $where = "m.is_deleted = 0 AND m.reference_type = 'Opening Stock'";
                    $params = [];
                    if (!empty($post['warehouse_id'])) {
                        $where .= " AND m.warehouse_id = :warehouse_id";
                        $params[':warehouse_id'] = $post['warehouse_id'];
                    }
                    if (!empty($post['keyword'])) {
                        $where .= " AND (p.product_name LIKE :kw OR p.sku LIKE :kw)";
                        $params[':kw'] = '%' . $post['keyword'] . '%';
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*) FROM inventory_stock_movements m
                        INNER JOIN inventory_products p ON p.id = m.product_id
                        WHERE $where
                    ")->bindValues($params)->queryScalar();

                    $rows = Yii::$app->db->createCommand("
                        SELECT m.*, p.product_name, p.sku, w.warehouse_name
                        FROM inventory_stock_movements m
                        INNER JOIN inventory_products p ON p.id = m.product_id
                        INNER JOIN inventory_warehouses w ON w.id = m.warehouse_id
                        WHERE $where
                        ORDER BY m.movement_date DESC
                        LIMIT $limit OFFSET $offset
                    ")->bindValues($params)->queryAll();

                    return $this->jsonResponse(true, 'Data loaded successfully!', [
                        'data' => $rows,
                        'total' => (int)$total,
                        'page' => $page,
                        'limit' => $limit,
                    ]);
                } elseif ($flag == 'create') {
                    if (empty($post['warehouse_id']) || empty($post['product_id']) || !isset($post['quantity'])) {
                        return $this->jsonResponse(false, 'Warehouse, Product and Quantity are required.');
                    }

                    $warehouse_id = $post['warehouse_id'];
                    $product_id = $post['product_id'];
                    $quantity = (float)$post['quantity'];
                    $unit_cost = (float)($post['unit_cost'] ?? 0);

                    if ($quantity < 0) {
                        return $this->jsonResponse(false, 'Quantity cannot be negative.');
                    }

                    $existing = Yii::$app->db->createCommand("
                        SELECT id FROM inventory_stock
                        WHERE warehouse_id = :w AND product_id = :p AND is_deleted = 0
                    ")->bindValues([':w' => $warehouse_id, ':p' => $product_id])->queryOne();

                    if ($existing) {
                        return $this->jsonResponse(false, 'Opening stock already exists for this product in this warehouse.');
                    }

                    $trans = Yii::$app->db->beginTransaction();
                    try {
                        Yii::$app->db->createCommand()->insert('inventory_stock', [
                            'warehouse_id' => $warehouse_id,
                            'product_id' => $product_id,
                            'quantity' => $quantity,
                            'reserved_quantity' => 0,
                            'available_quantity' => $quantity,
                            'average_cost' => $unit_cost,
                            'last_purchase_price' => $unit_cost,
                            'created_by' => $user_id,
                            'updated_by' => $user_id,
                        ])->execute();

                        Yii::$app->db->createCommand()->insert('inventory_stock_movements', [
                            'movement_no' => $this->generateDocNo('OPN'),
                            'warehouse_id' => $warehouse_id,
                            'product_id' => $product_id,
                            'reference_type' => 'Opening Stock',
                            'reference_id' => null,
                            'movement_type' => 'IN',
                            'quantity' => $quantity,
                            'unit_cost' => $unit_cost,
                            'total_cost' => $unit_cost * $quantity,
                            'remarks' => $post['remarks'] ?? 'Opening stock entry',
                            'movement_date' => $post['movement_date'] ?? date('Y-m-d H:i:s'),
                            'created_by' => $user_id,
                            'updated_by' => $user_id,
                        ])->execute();

                        $trans->commit();
                        return $this->jsonResponse(true, 'Data Saved successfully!');
                    } catch (\Exception $inner) {
                        $trans->rollBack();
                        return $this->jsonResponse(false, 'Failed to save opening stock. Please try again.');
                    }
                } elseif ($flag == 'update') {
                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }

                    $movement = Yii::$app->db->createCommand(
                        "SELECT * FROM inventory_stock_movements WHERE id = :id AND is_deleted = 0 AND reference_type = 'Opening Stock'"
                    )->bindValues([':id' => $post['id']])->queryOne();

                    if (!$movement) {
                        return $this->jsonResponse(false, 'Opening stock record not found.');
                    }

                    $newQuantity = (float)($post['quantity'] ?? $movement['quantity']);
                    $unit_cost = (float)($post['unit_cost'] ?? $movement['unit_cost']);
                    $diff = $newQuantity - $movement['quantity'];

                    $trans = Yii::$app->db->beginTransaction();
                    try {
                        Yii::$app->db->createCommand()->update('inventory_stock_movements', [
                            'quantity' => $newQuantity,
                            'unit_cost' => $unit_cost,
                            'total_cost' => $unit_cost * $newQuantity,
                            'remarks' => $post['remarks'] ?? $movement['remarks'],
                            'updated_by' => $user_id,
                        ], ['id' => $post['id']])->execute();

                        $stock = Yii::$app->db->createCommand("
                            SELECT * FROM inventory_stock WHERE warehouse_id = :w AND product_id = :p AND is_deleted = 0
                        ")->bindValues([':w' => $movement['warehouse_id'], ':p' => $movement['product_id']])->queryOne();

                        if ($stock) {
                            $newQty = $stock['quantity'] + $diff;
                            Yii::$app->db->createCommand()->update('inventory_stock', [
                                'quantity' => $newQty,
                                'available_quantity' => $newQty - $stock['reserved_quantity'],
                                'average_cost' => $unit_cost > 0 ? $unit_cost : $stock['average_cost'],
                                'updated_by' => $user_id,
                            ], ['id' => $stock['id']])->execute();
                        }

                        $trans->commit();
                        return $this->jsonResponse(true, 'Data Updated successfully!');
                    } catch (\Exception $inner) {
                        $trans->rollBack();
                        return $this->jsonResponse(false, 'Failed to update opening stock. Please try again.');
                    }
                } elseif ($flag == 'delete') {
                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }

                    $trans = Yii::$app->db->beginTransaction();
                    try {
                        $movement = Yii::$app->db->createCommand(
                            "SELECT * FROM inventory_stock_movements WHERE id = :id AND is_deleted = 0 AND reference_type = 'Opening Stock'"
                        )->bindValues([':id' => $post['id']])->queryOne();

                        if (!$movement) {
                            throw new \Exception('Opening stock record not found.');
                        }

                        $stock = Yii::$app->db->createCommand("
                            SELECT * FROM inventory_stock WHERE warehouse_id = :w AND product_id = :p AND is_deleted = 0
                        ")->bindValues([':w' => $movement['warehouse_id'], ':p' => $movement['product_id']])->queryOne();

                        if ($stock) {
                            $newQty = $stock['quantity'] - $movement['quantity'];
                            Yii::$app->db->createCommand()->update('inventory_stock', [
                                'quantity' => $newQty,
                                'available_quantity' => $newQty - $stock['reserved_quantity'],
                                'updated_by' => $user_id,
                            ], ['id' => $stock['id']])->execute();
                        }

                        Yii::$app->db->createCommand()->update('inventory_stock_movements', [
                            'is_deleted' => 1,
                            'is_active' => 0,
                            'updated_by' => $user_id,
                        ], ['id' => $post['id']])->execute();

                        $trans->commit();
                        return $this->jsonResponse(true, 'Data Deleted successfully!');
                    } catch (\Exception $inner) {
                        $trans->rollBack();
                        return $this->jsonResponse(false, $inner->getMessage() ?: 'Failed to delete opening stock. Please try again.');
                    }
                }

                return $this->jsonResponse(false, 'Invalid request flag.');
            } catch (\Exception $e) {
                return $this->jsonResponse(false, 'Failed to update data. Please try again.');
            }
        }

        $warehouses = Yii::$app->db->createCommand("SELECT id, warehouse_name FROM inventory_warehouses WHERE is_deleted = 0  and is_active=1 ORDER BY warehouse_name")->queryAll();
        $products = Yii::$app->db->createCommand("SELECT id, product_name, sku FROM inventory_products WHERE is_deleted = 0 ORDER BY product_name")->queryAll();

        return $this->renderPartial('inventoryopeningstock', [
            'warehouses' => $warehouses,
            'products' => $products,
        ]);
    }

    public function actionInventorystocktransfer()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $flag = $post['flag'] ?? '';
            $user_id = $this->currentUserId();

            try {
                if ($flag == 'search') {
                    
                    $limit = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                    $page = !empty($post['page']) ? (int)$post['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    $where = "t.is_deleted = 0";
                    $params = [];
                    if (!empty($post['from_warehouse'])) {
                        $where .= " AND t.from_warehouse = :from_warehouse";
                        $params[':from_warehouse'] = $post['from_warehouse'];
                    }
                    if (!empty($post['to_warehouse'])) {
                        $where .= " AND t.to_warehouse = :to_warehouse";
                        $params[':to_warehouse'] = $post['to_warehouse'];
                    }
                    if (!empty($post['status'])) {
                        $where .= " AND t.status = :status";
                        $params[':status'] = $post['status'];
                    }
                    if (!empty($post['keyword'])) {
                        $where .= " AND t.transfer_no LIKE :kw";
                        $params[':kw'] = '%' . $post['keyword'] . '%';
                    }

                    $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_stock_transfers t WHERE $where")->bindValues($params)->queryScalar();

                    $rows = Yii::$app->db->createCommand("
                        SELECT t.*, fw.warehouse_name AS from_warehouse_name, tw.warehouse_name AS to_warehouse_name,
                            (SELECT COUNT(*) FROM inventory_stock_transfer_items i WHERE i.transfer_id = t.id AND i.is_deleted = 0) AS item_count
                        FROM inventory_stock_transfers t
                        INNER JOIN inventory_warehouses fw ON fw.id = t.from_warehouse
                        INNER JOIN inventory_warehouses tw ON tw.id = t.to_warehouse
                        WHERE $where
                        ORDER BY t.transfer_date DESC, t.id DESC
                        LIMIT $limit OFFSET $offset
                    ")->bindValues($params)->queryAll();

                    if (!empty($post['id'])) {
                        $items = Yii::$app->db->createCommand("
                            SELECT i.*, p.product_name, p.sku
                            FROM inventory_stock_transfer_items i
                            INNER JOIN inventory_products p ON p.id = i.product_id
                            WHERE i.transfer_id = :id AND i.is_deleted = 0
                        ")->bindValues([':id' => $post['id']])->queryAll();

                        return $this->jsonResponse(true, 'Data loaded successfully!', ['data' => $rows, 'items' => $items, 'total' => (int)$total]);
                    }

                    return $this->jsonResponse(true, 'Data loaded successfully!', [
                        'data' => $rows,
                        'total' => (int)$total,
                        'page' => $page,
                        'limit' => $limit,
                    ]);
                } elseif ($flag == 'create') {
                    if (empty($post['from_warehouse']) || empty($post['to_warehouse'])) {
                        return $this->jsonResponse(false, 'From and To warehouse are required.');
                    }
                    if ($post['from_warehouse'] == $post['to_warehouse']) {
                        return $this->jsonResponse(false, 'From and To warehouse cannot be the same.');
                    }

                    $items = $post['items'] ?? '[]';
                    $items = is_array($items) ? $items : json_decode($items, true);
                    if (empty($items)) {
                        return $this->jsonResponse(false, 'At least one item is required.');
                    }

                    $status = $post['status'] ?? 'Pending';

                    $trans = Yii::$app->db->beginTransaction();
                    try {
                        Yii::$app->db->createCommand()->insert('inventory_stock_transfers', [
                            'transfer_no' => $this->generateDocNo('TRF'),
                            'from_warehouse' => $post['from_warehouse'],
                            'to_warehouse' => $post['to_warehouse'],
                            'transfer_date' => $post['transfer_date'] ?? date('Y-m-d'),
                            'status' => $status,
                            'remarks' => $post['remarks'] ?? null,
                            'created_by' => $user_id,
                            'updated_by' => $user_id,
                        ])->execute();
                        $transfer_id = Yii::$app->db->getLastInsertID();

                        foreach ($items as $item) {
                            if (empty($item['product_id']) || !isset($item['quantity'])) {
                                continue;
                            }
                            Yii::$app->db->createCommand()->insert('inventory_stock_transfer_items', [
                                'transfer_id' => $transfer_id,
                                'product_id' => $item['product_id'],
                                'quantity' => (float)$item['quantity'],
                                'remarks' => $item['remarks'] ?? null,
                                'created_by' => $user_id,
                                'updated_by' => $user_id,
                            ])->execute();
                        }

                        if ($status == 'Completed') {
                            $this->applyStockTransferEffect($transfer_id, $user_id);
                        }

                        $trans->commit();
                        return $this->jsonResponse(true, 'Data Saved successfully!', ['id' => $transfer_id]);
                    } catch (\Exception $inner) {
                        $trans->rollBack();
                        return $this->jsonResponse(false, $inner->getMessage() ?: 'Failed to save transfer. Please try again.');
                    }
                } elseif ($flag == 'update') {
                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }

                    $transfer = Yii::$app->db->createCommand(
                        "SELECT * FROM inventory_stock_transfers WHERE id = :id AND is_deleted = 0"
                    )->bindValues([':id' => $post['id']])->queryOne();

                    if (!$transfer) {
                        return $this->jsonResponse(false, 'Transfer not found.');
                    }

                    $newStatus = $post['status'] ?? $transfer['status'];

                    $trans = Yii::$app->db->beginTransaction();
                    try {
                        Yii::$app->db->createCommand()->update('inventory_stock_transfers', [
                            'transfer_date' => $post['transfer_date'] ?? $transfer['transfer_date'],
                            'status' => $newStatus,
                            'remarks' => $post['remarks'] ?? $transfer['remarks'],
                            'updated_by' => $user_id,
                        ], ['id' => $post['id']])->execute();

                        // Only trigger the actual stock movement the moment status transitions into Completed.
                        if ($newStatus == 'Completed' && $transfer['status'] != 'Completed') {
                            $this->applyStockTransferEffect($post['id'], $user_id);
                        }

                        $trans->commit();
                        return $this->jsonResponse(true, 'Data Updated successfully!');
                    } catch (\Exception $inner) {
                        $trans->rollBack();
                        return $this->jsonResponse(false, $inner->getMessage() ?: 'Failed to update transfer. Please try again.');
                    }
                } elseif ($flag == 'delete') {
                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }

                    $trans = Yii::$app->db->beginTransaction();
                    try {
                        $transfer = Yii::$app->db->createCommand(
                            "SELECT * FROM inventory_stock_transfers WHERE id = :id AND is_deleted = 0"
                        )->bindValues([':id' => $post['id']])->queryOne();

                        if ($transfer && $transfer['status'] == 'Completed') {
                            $this->reverseStockTransferEffect($post['id'], $user_id);
                        }

                        Yii::$app->db->createCommand()->update('inventory_stock_transfer_items', [
                            'is_deleted' => 1,
                        ], ['transfer_id' => $post['id']])->execute();

                        Yii::$app->db->createCommand()->update('inventory_stock_transfers', [
                            'is_deleted' => 1,
                            'is_active' => 0,
                            'updated_by' => $user_id,
                        ], ['id' => $post['id']])->execute();

                        $trans->commit();
                        return $this->jsonResponse(true, 'Data Deleted successfully!');
                    } catch (\Exception $inner) {
                        $trans->rollBack();
                        return $this->jsonResponse(false, 'Failed to delete transfer. Please try again.');
                    }
                }

                return $this->jsonResponse(false, 'Invalid request flag.');
            } catch (\Exception $e) {
                return $this->jsonResponse(false, 'Failed to update data. Please try again.');
            }
        }

        $warehouses = Yii::$app->db->createCommand("SELECT id, warehouse_name FROM inventory_warehouses WHERE is_deleted = 0  and is_active=1 ORDER BY warehouse_name")->queryAll();
        $products = Yii::$app->db->createCommand("SELECT id, product_name, sku FROM inventory_products WHERE is_deleted = 0 ORDER BY product_name")->queryAll();

        return $this->renderPartial('inventorystocktransfer', [
            'warehouses' => $warehouses,
            'products' => $products,
        ]);
    }
    private function applyStockTransferEffect($transfer_id, $user_id)
    {
        $transfer = Yii::$app->db->createCommand(
            "SELECT * FROM inventory_stock_transfers WHERE id = :id"
        )->bindValues([':id' => $transfer_id])->queryOne();

        $items = Yii::$app->db->createCommand(
            "SELECT * FROM inventory_stock_transfer_items WHERE transfer_id = :id AND is_deleted = 0"
        )->bindValues([':id' => $transfer_id])->queryAll();

        foreach ($items as $item) {
            $quantity = (float)$item['quantity'];
            $product_id = $item['product_id'];

            $fromStock = Yii::$app->db->createCommand("
                SELECT * FROM inventory_stock WHERE warehouse_id = :w AND product_id = :p AND is_deleted = 0
            ")->bindValues([':w' => $transfer['from_warehouse'], ':p' => $product_id])->queryOne();

            if (!$fromStock || $fromStock['quantity'] < $quantity) {
                throw new \Exception('Insufficient stock to transfer for product id ' . $product_id . '.');
            }

            $newFromQty = $fromStock['quantity'] - $quantity;
            Yii::$app->db->createCommand()->update('inventory_stock', [
                'quantity' => $newFromQty,
                'available_quantity' => $newFromQty - $fromStock['reserved_quantity'],
                'updated_by' => $user_id,
            ], ['id' => $fromStock['id']])->execute();

            $toStock = Yii::$app->db->createCommand("
                SELECT * FROM inventory_stock WHERE warehouse_id = :w AND product_id = :p AND is_deleted = 0
            ")->bindValues([':w' => $transfer['to_warehouse'], ':p' => $product_id])->queryOne();

            if ($toStock) {
                $newToQty = $toStock['quantity'] + $quantity;
                Yii::$app->db->createCommand()->update('inventory_stock', [
                    'quantity' => $newToQty,
                    'available_quantity' => $newToQty - $toStock['reserved_quantity'],
                    'updated_by' => $user_id,
                ], ['id' => $toStock['id']])->execute();
            } else {
                Yii::$app->db->createCommand()->insert('inventory_stock', [
                    'warehouse_id' => $transfer['to_warehouse'],
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'reserved_quantity' => 0,
                    'available_quantity' => $quantity,
                    'average_cost' => $fromStock['average_cost'],
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                ])->execute();
            }

            Yii::$app->db->createCommand()->insert('inventory_stock_movements', [
                'movement_no' => $this->generateDocNo('TRFO'),
                'warehouse_id' => $transfer['from_warehouse'],
                'product_id' => $product_id,
                'reference_type' => 'Transfer Out',
                'reference_id' => $transfer_id,
                'movement_type' => 'OUT',
                'quantity' => $quantity,
                'unit_cost' => $fromStock['average_cost'],
                'total_cost' => $fromStock['average_cost'] * $quantity,
                'remarks' => 'Transfer to warehouse #' . $transfer['to_warehouse'],
                'created_by' => $user_id,
                'updated_by' => $user_id,
            ])->execute();

            Yii::$app->db->createCommand()->insert('inventory_stock_movements', [
                'movement_no' => $this->generateDocNo('TRFI'),
                'warehouse_id' => $transfer['to_warehouse'],
                'product_id' => $product_id,
                'reference_type' => 'Transfer In',
                'reference_id' => $transfer_id,
                'movement_type' => 'IN',
                'quantity' => $quantity,
                'unit_cost' => $fromStock['average_cost'],
                'total_cost' => $fromStock['average_cost'] * $quantity,
                'remarks' => 'Transfer from warehouse #' . $transfer['from_warehouse'],
                'created_by' => $user_id,
                'updated_by' => $user_id,
            ])->execute();
        }
    }
    private function reverseStockTransferEffect($transfer_id, $user_id)
    {
        $transfer = Yii::$app->db->createCommand(
            "SELECT * FROM inventory_stock_transfers WHERE id = :id"
        )->bindValues([':id' => $transfer_id])->queryOne();

        $items = Yii::$app->db->createCommand(
            "SELECT * FROM inventory_stock_transfer_items WHERE transfer_id = :id AND is_deleted = 0"
        )->bindValues([':id' => $transfer_id])->queryAll();

        foreach ($items as $item) {
            $quantity = (float)$item['quantity'];
            $product_id = $item['product_id'];

            $fromStock = Yii::$app->db->createCommand("
                SELECT * FROM inventory_stock WHERE warehouse_id = :w AND product_id = :p AND is_deleted = 0
            ")->bindValues([':w' => $transfer['from_warehouse'], ':p' => $product_id])->queryOne();
            if ($fromStock) {
                $newQty = $fromStock['quantity'] + $quantity;
                Yii::$app->db->createCommand()->update('inventory_stock', [
                    'quantity' => $newQty,
                    'available_quantity' => $newQty - $fromStock['reserved_quantity'],
                    'updated_by' => $user_id,
                ], ['id' => $fromStock['id']])->execute();
            }

            $toStock = Yii::$app->db->createCommand("
                SELECT * FROM inventory_stock WHERE warehouse_id = :w AND product_id = :p AND is_deleted = 0
            ")->bindValues([':w' => $transfer['to_warehouse'], ':p' => $product_id])->queryOne();
            if ($toStock) {
                $newQty = $toStock['quantity'] - $quantity;
                Yii::$app->db->createCommand()->update('inventory_stock', [
                    'quantity' => $newQty,
                    'available_quantity' => $newQty - $toStock['reserved_quantity'],
                    'updated_by' => $user_id,
                ], ['id' => $toStock['id']])->execute();
            }
        }
    }
    public function actionInventorylowstock()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $flag = $post['flag'] ?? '';

            try {
                // This screen is a derived report (computed from inventory_stock vs inventory_products.reorder_level),
                // so it only supports 'search'. Create/update/delete of stock should be done via
                // Current Stock, Stock Adjustment or Stock Movement instead.
                if ($flag == 'search') {
                    
                    $limit = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                    $page = !empty($post['page']) ? (int)$post['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    $where = "s.is_deleted = 0 AND s.quantity <= p.reorder_level";
                    $params = [];
                    if (!empty($post['warehouse_id'])) {
                        $where .= " AND s.warehouse_id = :warehouse_id";
                        $params[':warehouse_id'] = $post['warehouse_id'];
                    }
                    if (!empty($post['category_id'])) {
                        $where .= " AND p.category_id = :category_id";
                        $params[':category_id'] = $post['category_id'];
                    }
                    if (!empty($post['keyword'])) {
                        $where .= " AND (p.product_name LIKE :kw OR p.sku LIKE :kw)";
                        $params[':kw'] = '%' . $post['keyword'] . '%';
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*) FROM inventory_stock s
                        INNER JOIN inventory_products p ON p.id = s.product_id AND p.is_deleted = 0
                        WHERE $where
                    ")->bindValues($params)->queryScalar();

                    $rows = Yii::$app->db->createCommand("
                        SELECT s.id, s.warehouse_id, s.product_id, s.quantity,
                            p.product_name, p.sku, p.minimum_stock, p.reorder_level, p.maximum_stock,
                            c.category_name, w.warehouse_name,
                            (p.maximum_stock - s.quantity) AS suggested_reorder_qty
                        FROM inventory_stock s
                        INNER JOIN inventory_products p ON p.id = s.product_id AND p.is_deleted = 0
                        LEFT JOIN inventory_categories c ON c.id = p.category_id
                        INNER JOIN inventory_warehouses w ON w.id = s.warehouse_id
                        WHERE $where
                        ORDER BY s.quantity ASC
                        LIMIT $limit OFFSET $offset
                    ")->bindValues($params)->queryAll();

                    return $this->jsonResponse(true, 'Data loaded successfully!', [
                        'data' => $rows,
                        'total' => (int)$total,
                        'page' => $page,
                        'limit' => $limit,
                    ]);
                }

                return $this->jsonResponse(false, 'This is a read-only report; only the search flag is supported.');
            } catch (\Exception $e) {
                return $this->jsonResponse(false, 'Failed to load low stock report. Please try again.');
            }
        }

        $warehouses = Yii::$app->db->createCommand("SELECT id, warehouse_name FROM inventory_warehouses WHERE is_deleted = 0  and is_active=1 ORDER BY warehouse_name")->queryAll();
        $categories = Yii::$app->db->createCommand("SELECT id, category_name FROM inventory_categories WHERE is_deleted = 0 ORDER BY category_name")->queryAll();

        return $this->renderPartial('inventorylowstock', [
            'warehouses' => $warehouses,
            'categories' => $categories,
        ]);
    }
    public function actionInventorydamagedstock()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $flag = $post['flag'] ?? '';
            $user_id = $this->currentUserId();

            try {
                // Damaged stock is modelled as a Stock Adjustment with reason = 'Damage' and type = 'Decrease',
                // so it shares the same save/reverse/delete helpers as Stock Adjustment for consistency.
                if ($flag == 'search') {
                    
                    $limit = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                    $page = !empty($post['page']) ? (int)$post['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    $where = "a.is_deleted = 0 AND a.reason = 'Damage'";
                    $params = [];
                    if (!empty($post['warehouse_id'])) {
                        $where .= " AND a.warehouse_id = :warehouse_id";
                        $params[':warehouse_id'] = $post['warehouse_id'];
                    }
                    if (!empty($post['keyword'])) {
                        $where .= " AND a.adjustment_no LIKE :kw";
                        $params[':kw'] = '%' . $post['keyword'] . '%';
                    }

                    $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_stock_adjustments a WHERE $where")->bindValues($params)->queryScalar();

                    $rows = Yii::$app->db->createCommand("
                        SELECT a.*, w.warehouse_name,
                            (SELECT COALESCE(SUM(i.quantity),0) FROM inventory_stock_adjustment_items i WHERE i.adjustment_id = a.id AND i.is_deleted = 0) AS total_quantity,
                            (SELECT COALESCE(SUM(i.total_cost),0) FROM inventory_stock_adjustment_items i WHERE i.adjustment_id = a.id AND i.is_deleted = 0) AS total_cost
                        FROM inventory_stock_adjustments a
                        INNER JOIN inventory_warehouses w ON w.id = a.warehouse_id
                        WHERE $where
                        ORDER BY a.adjustment_date DESC
                        LIMIT $limit OFFSET $offset
                    ")->bindValues($params)->queryAll();

                    if (!empty($post['id'])) {
                        $items = Yii::$app->db->createCommand("
                            SELECT i.*, p.product_name, p.sku
                            FROM inventory_stock_adjustment_items i
                            INNER JOIN inventory_products p ON p.id = i.product_id
                            WHERE i.adjustment_id = :id AND i.is_deleted = 0
                        ")->bindValues([':id' => $post['id']])->queryAll();

                        return $this->jsonResponse(true, 'Data loaded successfully!', ['data' => $rows, 'items' => $items, 'total' => (int)$total]);
                    }

                    return $this->jsonResponse(true, 'Data loaded successfully!', [
                        'data' => $rows,
                        'total' => (int)$total,
                        'page' => $page,
                        'limit' => $limit,
                    ]);
                } elseif ($flag == 'create') {
                    $post['adjustment_type'] = 'Decrease';
                    return $this->saveStockAdjustment($post, $user_id, null, 'Damage');
                } elseif ($flag == 'update') {
                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }
                    $post['adjustment_type'] = 'Decrease';
                    return $this->saveStockAdjustment($post, $user_id, $post['id'], 'Damage');
                } elseif ($flag == 'delete') {
                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }
                    return $this->deleteStockAdjustment($post['id'], $user_id);
                }

                return $this->jsonResponse(false, 'Invalid request flag.');
            } catch (\Exception $e) {
                return $this->jsonResponse(false, 'Failed to update data. Please try again.');
            }
        }

        $warehouses = Yii::$app->db->createCommand("SELECT id, warehouse_name FROM inventory_warehouses WHERE is_deleted = 0  and is_active=1 ORDER BY warehouse_name")->queryAll();
        $products = Yii::$app->db->createCommand("SELECT id, product_name, sku FROM inventory_products WHERE is_deleted = 0 ORDER BY product_name")->queryAll();

        return $this->renderPartial('inventorydamagedstock', [
            'warehouses' => $warehouses,
            'products' => $products,
        ]);
    }
    public function actionInventorystockvaluation()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $flag = $post['flag'] ?? '';

            try {
                if ($flag == 'search') {
                    
                    $limit = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                    $page = !empty($post['page']) ? (int)$post['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    $where = "s.is_deleted = 0 AND p.is_deleted = 0";
                    $params = [];
                    if (!empty($post['warehouse_id'])) {
                        $where .= " AND s.warehouse_id = :warehouse_id";
                        $params[':warehouse_id'] = $post['warehouse_id'];
                    }
                    if (!empty($post['category_id'])) {
                        $where .= " AND p.category_id = :category_id";
                        $params[':category_id'] = $post['category_id'];
                    }
                    if (!empty($post['keyword'])) {
                        $where .= " AND (p.product_name LIKE :kw OR p.sku LIKE :kw)";
                        $params[':kw'] = '%' . $post['keyword'] . '%';
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*) FROM inventory_stock s INNER JOIN inventory_products p ON p.id = s.product_id WHERE $where
                    ")->bindValues($params)->queryScalar();

                    $rows = Yii::$app->db->createCommand("
                        SELECT s.id, s.warehouse_id, s.product_id, s.quantity, s.average_cost,
                            (s.quantity * s.average_cost) AS stock_value,
                            p.product_name, p.sku, p.selling_price, (s.quantity * p.selling_price) AS potential_sales_value,
                            c.category_name, b.brand_name, w.warehouse_name
                        FROM inventory_stock s
                        INNER JOIN inventory_products p ON p.id = s.product_id
                        LEFT JOIN inventory_categories c ON c.id = p.category_id
                        LEFT JOIN inventory_brands b ON b.id = p.brand_id
                        INNER JOIN inventory_warehouses w ON w.id = s.warehouse_id
                        WHERE $where
                        ORDER BY stock_value DESC
                        LIMIT $limit OFFSET $offset
                    ")->bindValues($params)->queryAll();

                    $summary = Yii::$app->db->createCommand("
                        SELECT
                            COALESCE(SUM(s.quantity * s.average_cost),0) AS total_stock_value,
                            COALESCE(SUM(s.quantity * p.selling_price),0) AS total_potential_sales_value,
                            COUNT(DISTINCT s.product_id) AS total_products
                        FROM inventory_stock s
                        INNER JOIN inventory_products p ON p.id = s.product_id
                        WHERE $where
                    ")->bindValues($params)->queryOne();

                    return $this->jsonResponse(true, 'Data loaded successfully!', [
                        'data' => $rows,
                        'summary' => $summary,
                        'total' => (int)$total,
                        'page' => $page,
                        'limit' => $limit,
                    ]);
                }

                return $this->jsonResponse(false, 'This is a read-only report; only the search flag is supported.');
            } catch (\Exception $e) {
                return $this->jsonResponse(false, 'Failed to load stock valuation report. Please try again.');
            }
        }

        $warehouses = Yii::$app->db->createCommand("SELECT id, warehouse_name FROM inventory_warehouses WHERE is_deleted = 0  and is_active=1 ORDER BY warehouse_name")->queryAll();
        $categories = Yii::$app->db->createCommand("SELECT id, category_name FROM inventory_categories WHERE is_deleted = 0 ORDER BY category_name")->queryAll();

        return $this->renderPartial('inventorystockvaluation', [
            'warehouses' => $warehouses,
            'categories' => $categories,
        ]);
    }
    public function actionInventoryreorderreport()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $flag = $post['flag'] ?? '';

            try {
                if ($flag == 'search') {
                    
                    $limit = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                    $page = !empty($post['page']) ? (int)$post['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    $where = "p.is_deleted = 0 AND p.is_active = 1 AND COALESCE(s.total_quantity,0) <= p.reorder_level";
                    $params = [];
                    if (!empty($post['category_id'])) {
                        $where .= " AND p.category_id = :category_id";
                        $params[':category_id'] = $post['category_id'];
                    }
                    if (!empty($post['brand_id'])) {
                        $where .= " AND p.brand_id = :brand_id";
                        $params[':brand_id'] = $post['brand_id'];
                    }
                    if (!empty($post['keyword'])) {
                        $where .= " AND (p.product_name LIKE :kw OR p.sku LIKE :kw)";
                        $params[':kw'] = '%' . $post['keyword'] . '%';
                    }

                    // Aggregate stock across all warehouses per product for a company-wide reorder view.
                    $stockSubquery = "
                        SELECT product_id, SUM(quantity) AS total_quantity
                        FROM inventory_stock
                        WHERE is_deleted = 0
                        GROUP BY product_id
                    ";

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_products p
                        LEFT JOIN ($stockSubquery) s ON s.product_id = p.id
                        WHERE $where
                    ")->bindValues($params)->queryScalar();

                    $rows = Yii::$app->db->createCommand("
                        SELECT p.id AS product_id, p.product_name, p.sku, p.minimum_stock, p.maximum_stock, p.reorder_level,
                            p.purchase_price, COALESCE(s.total_quantity,0) AS current_quantity,
                            (p.maximum_stock - COALESCE(s.total_quantity,0)) AS suggested_reorder_qty,
                            ((p.maximum_stock - COALESCE(s.total_quantity,0)) * p.purchase_price) AS estimated_reorder_cost,
                            c.category_name, b.brand_name
                        FROM inventory_products p
                        LEFT JOIN ($stockSubquery) s ON s.product_id = p.id
                        LEFT JOIN inventory_categories c ON c.id = p.category_id
                        LEFT JOIN inventory_brands b ON b.id = p.brand_id
                        WHERE $where
                        ORDER BY current_quantity ASC
                        LIMIT $limit OFFSET $offset
                    ")->bindValues($params)->queryAll();

                    return $this->jsonResponse(true, 'Data loaded successfully!', [
                        'data' => $rows,
                        'total' => (int)$total,
                        'page' => $page,
                        'limit' => $limit,
                    ]);
                }

                return $this->jsonResponse(false, 'This is a read-only report; only the search flag is supported.');
            } catch (\Exception $e) {
                return $this->jsonResponse(false, 'Failed to load reorder report. Please try again.');
            }
        }

        $categories = Yii::$app->db->createCommand("SELECT id, category_name FROM inventory_categories WHERE is_deleted = 0 ORDER BY category_name")->queryAll();
        $brands = Yii::$app->db->createCommand("SELECT id, brand_name FROM inventory_brands WHERE is_deleted = 0 ORDER BY brand_name")->queryAll();

        return $this->renderPartial('inventoryreorderreport', [
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}

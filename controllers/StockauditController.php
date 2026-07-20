<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class StockauditController extends Controller
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

    public function actionStockAudit()
    {
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

        return $this->render('stockaudit', compact('modules'));
    }

    /* -------------------------------------------------------------
     * Audit Dashboard
     * ----------------------------------------------------------- */
    public function actionAuditdashboard()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('auditdashboard');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (!isset($post['flag']) || $post['flag'] != 'load_dashboard') {
                return $this->jsonResponse(false, 'Invalid request.');
            }
            $db = Yii::$app->db;
            $stats = [];
            $stats['total_audits'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock_audits WHERE is_deleted=0")->queryScalar();
            $stats['open_audits'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock_audits WHERE is_deleted=0 AND status='Open'")->queryScalar();
            $stats['completed_audits'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock_audits WHERE is_deleted=0 AND status='Completed'")->queryScalar();
            $stats['total_items_counted'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock_audit_items WHERE is_deleted=0")->queryScalar();
            $stats['items_with_variance'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_stock_audit_items WHERE is_deleted=0 AND variance<>0")->queryScalar();
            $stats['total_variance_qty'] = (float)$db->createCommand("SELECT IFNULL(SUM(variance),0) FROM inventory_stock_audit_items WHERE is_deleted=0")->queryScalar();

            $warehouseChart = $db->createCommand("
                SELECT w.warehouse_name, COUNT(a.id) total_audits
                FROM inventory_warehouses w
                LEFT JOIN inventory_stock_audits a ON a.warehouse_id=w.id AND a.is_deleted=0
                WHERE w.is_deleted=0
                GROUP BY w.id
                ORDER BY total_audits DESC
            ")->queryAll();

            $recentAudits = $db->createCommand("
                SELECT a.*, w.warehouse_name,
                    (SELECT COUNT(*) FROM inventory_stock_audit_items i WHERE i.audit_id=a.id AND i.is_deleted=0) item_count,
                    (SELECT COUNT(*) FROM inventory_stock_audit_items i WHERE i.audit_id=a.id AND i.is_deleted=0 AND i.variance<>0) variance_count
                FROM inventory_stock_audits a
                LEFT JOIN inventory_warehouses w ON w.id=a.warehouse_id
                WHERE a.is_deleted=0
                ORDER BY a.audit_date DESC, a.id DESC
                LIMIT 10
            ")->queryAll();

            return array_merge($this->jsonResponse(true, 'Dashboard loaded.'), [
                'stats' => $stats,
                'warehouseChart' => $warehouseChart,
                'recentAudits' => $recentAudits
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Audit Schedule - plan future audits
     * ----------------------------------------------------------- */
    public function actionAuditschedule()
    {
        if (Yii::$app->request->isGet) {
            $audits = Yii::$app->db->createCommand("
                SELECT a.*, w.warehouse_name
                FROM inventory_stock_audits a
                LEFT JOIN inventory_warehouses w ON w.id=a.warehouse_id
                WHERE a.is_deleted=0 AND a.audit_date>=CURDATE()
                ORDER BY a.audit_date ASC
            ")->queryAll();
            $warehouses = Yii::$app->db->createCommand("SELECT id,warehouse_name FROM inventory_warehouses WHERE is_deleted=0 AND is_active=1 ORDER BY warehouse_name")->queryAll();
            return $this->renderPartial('auditschedule', ['audits' => $audits, 'warehouses' => $warehouses]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {
                $warehouse_id = trim($post['warehouse_id'] ?? '');
                $where = " WHERE a.is_deleted=0 ";
                $params = [];
                if ($warehouse_id != '') {
                    $where .= " AND a.warehouse_id=:warehouse_id";
                    $params[':warehouse_id'] = $warehouse_id;
                }
                $audits = Yii::$app->db->createCommand("
                    SELECT a.*, w.warehouse_name FROM inventory_stock_audits a
                    LEFT JOIN inventory_warehouses w ON w.id=a.warehouse_id
                    $where ORDER BY a.audit_date ASC
                ", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['audits' => $audits]);
            }

            $id = $post['id'] ?? null;

            if ($id && isset($post['delete']) && $post['delete'] == 1) {
                Yii::$app->db->createCommand()->update('inventory_stock_audits',
                    ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                    ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Scheduled audit deleted successfully.');
            }

            if (empty($post['warehouse_id']) || empty($post['audit_date'])) {
                return $this->jsonResponse(false, 'Warehouse and audit date are required.');
            }

            $data = [
                'warehouse_id' => $post['warehouse_id'],
                'audit_date' => $post['audit_date'],
                'remarks' => $post['remarks'] ?? null,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->currentUserId(),
            ];

            if ($id) {
                Yii::$app->db->createCommand()->update('inventory_stock_audits', $data, ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Scheduled audit updated successfully.');
            }

            $data['audit_no'] = $this->generateDocNo('AUD');
            $data['status'] = 'Open';
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = $this->currentUserId();
            $data['is_deleted'] = 0;
            Yii::$app->db->createCommand()->insert('inventory_stock_audits', $data)->execute();
            return $this->jsonResponse(true, 'Audit scheduled successfully.', ['id' => Yii::$app->db->getLastInsertID()]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Physical Stock Count
     * ----------------------------------------------------------- */
    public function actionPhysicalstockcount()
    {
        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();
            $flag = $post['flag'] ?? '';
            $user_id = $this->currentUserId();
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {
                if ($flag == 'search') {
                    $limit = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                    $page = !empty($post['page']) ? (int)$post['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    $where = "a.is_deleted=0";
                    $params = [];
                    if (!empty($post['warehouse_id'])) {
                        $where .= " AND a.warehouse_id=:warehouse_id";
                        $params[':warehouse_id'] = $post['warehouse_id'];
                    }
                    if (!empty($post['status'])) {
                        $where .= " AND a.status=:status";
                        $params[':status'] = $post['status'];
                    }
                    if (!empty($post['keyword'])) {
                        $where .= " AND a.audit_no LIKE :keyword";
                        $params[':keyword'] = '%' . $post['keyword'] . '%';
                    }

                    $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_stock_audits a WHERE $where")->bindValues($params)->queryScalar();

                    $rows = Yii::$app->db->createCommand("
                        SELECT a.*, w.warehouse_name,
                            COUNT(i.id) item_count
                        FROM inventory_stock_audits a
                        INNER JOIN inventory_warehouses w ON w.id=a.warehouse_id
                        LEFT JOIN inventory_stock_audit_items i ON i.audit_id=a.id AND i.is_deleted=0
                        WHERE $where
                        GROUP BY a.id
                        ORDER BY a.id DESC
                        LIMIT $limit OFFSET $offset
                    ")->bindValues($params)->queryAll();

                    if (isset($post['id']) && $post['id'] != '') {
                        $items = Yii::$app->db->createCommand("
                            SELECT i.*, p.product_name, p.sku
                            FROM inventory_stock_audit_items i
                            INNER JOIN inventory_products p ON p.id=i.product_id
                            WHERE i.audit_id=:id AND i.is_deleted=0
                        ")->bindValue(':id', $post['id'])->queryAll();

                        return $this->jsonResponse(true, 'Data loaded successfully!', ['data' => $rows, 'items' => $items, 'total' => (int)$total]);
                    }

                    return $this->jsonResponse(true, 'Data loaded successfully!', ['data' => $rows, 'total' => (int)$total, 'page' => $page, 'limit' => $limit]);
                }

                if ($flag == 'loadstock') {
                    if (empty($post['warehouse_id'])) {
                        return $this->jsonResponse(false, 'Warehouse is required.');
                    }
                    $stock = Yii::$app->db->createCommand("
                        SELECT s.product_id, p.product_name, p.sku, s.quantity system_quantity
                        FROM inventory_stock s
                        INNER JOIN inventory_products p ON p.id=s.product_id
                        WHERE s.warehouse_id=:wid AND s.is_deleted=0
                        ORDER BY p.product_name
                    ")->bindValue(':wid', $post['warehouse_id'])->queryAll();
                    return $this->jsonResponse(true, 'Data loaded successfully!', ['stock' => $stock]);
                }

                if ($flag == 'create') {
                    return $this->saveStockCount($post, $user_id);
                }

                if ($flag == 'delete') {
                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }
                    Yii::$app->db->createCommand()->update('inventory_stock_audits',
                        ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $user_id],
                        ['id' => $post['id']])->execute();
                    Yii::$app->db->createCommand()->update('inventory_stock_audit_items',
                        ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $user_id],
                        ['audit_id' => $post['id']])->execute();
                    return $this->jsonResponse(true, 'Data Deleted successfully!');
                }

                return $this->jsonResponse(false, 'Invalid request flag.');
            } catch (\Exception $e) {
                return $this->jsonResponse(false, $e->getMessage());
            }
        }

        $warehouses = Yii::$app->db->createCommand("SELECT id,warehouse_name FROM inventory_warehouses WHERE is_deleted=0 AND is_active=1 ORDER BY warehouse_name")->queryAll();

        return $this->renderPartial('physicalstockcount', ['warehouses' => $warehouses]);
    }

    private function saveStockCount($post, $user_id)
    {
        if (empty($post['warehouse_id'])) {
            return $this->jsonResponse(false, 'Warehouse is required.');
        }

        $items = $post['items'] ?? [];
        if (!is_array($items)) {
            $items = json_decode($items, true);
        }
        if (empty($items)) {
            return $this->jsonResponse(false, 'At least one counted item is required.');
        }

        $trans = Yii::$app->db->beginTransaction();
        try {
            $warehouse_id = $post['warehouse_id'];

            Yii::$app->db->createCommand()->insert('inventory_stock_audits', [
                'audit_no' => $this->generateDocNo('AUD'),
                'warehouse_id' => $warehouse_id,
                'audit_date' => $post['audit_date'] ?? date('Y-m-d'),
                'status' => 'Open',
                'remarks' => $post['remarks'] ?? null,
                'created_by' => $user_id,
                'updated_by' => $user_id
            ])->execute();

            $audit_id = Yii::$app->db->getLastInsertID();

            foreach ($items as $item) {
                if (empty($item['product_id']) || !isset($item['physical_quantity'])) {
                    continue;
                }

                $product_id = $item['product_id'];
                $physical = (float)$item['physical_quantity'];

                $stock = Yii::$app->db->createCommand("
                    SELECT quantity FROM inventory_stock
                    WHERE warehouse_id=:warehouse AND product_id=:product AND is_deleted=0
                ")->bindValues([':warehouse' => $warehouse_id, ':product' => $product_id])->queryOne();

                $system = $stock ? (float)$stock['quantity'] : 0;
                $variance = $physical - $system;

                Yii::$app->db->createCommand()->insert('inventory_stock_audit_items', [
                    'audit_id' => $audit_id,
                    'product_id' => $product_id,
                    'system_quantity' => $system,
                    'physical_quantity' => $physical,
                    'variance' => $variance,
                    'remarks' => $item['remarks'] ?? null,
                    'created_by' => $user_id,
                    'updated_by' => $user_id
                ])->execute();
            }

            $trans->commit();

            return $this->jsonResponse(true, 'Physical stock count saved successfully!', ['id' => $audit_id]);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Stock Verification - review counted audits before reconciling
     * ----------------------------------------------------------- */
    public function actionStockverification()
    {
        if (Yii::$app->request->isGet) {
            $audits = Yii::$app->db->createCommand("
                SELECT a.*, w.warehouse_name,
                    COUNT(i.id) item_count,
                    SUM(CASE WHEN i.variance<>0 THEN 1 ELSE 0 END) variance_count
                FROM inventory_stock_audits a
                INNER JOIN inventory_warehouses w ON w.id=a.warehouse_id
                LEFT JOIN inventory_stock_audit_items i ON i.audit_id=a.id AND i.is_deleted=0
                WHERE a.is_deleted=0 AND a.status='Open'
                GROUP BY a.id
                ORDER BY a.audit_date DESC
            ")->queryAll();
            return $this->renderPartial('stockverification', ['audits' => $audits]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'getitems') {
                $items = Yii::$app->db->createCommand("
                    SELECT i.*, p.product_name, p.sku
                    FROM inventory_stock_audit_items i
                    INNER JOIN inventory_products p ON p.id=i.product_id
                    WHERE i.audit_id=:id AND i.is_deleted=0
                    ORDER BY p.product_name
                ")->bindValue(':id', $post['id'])->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['items' => $items]);
            }

            if (isset($post['flag']) && $post['flag'] == 'updateitem') {
                if (empty($post['id']) || !isset($post['physical_quantity'])) {
                    return $this->jsonResponse(false, 'Item id and physical quantity are required.');
                }
                $physical = (float)$post['physical_quantity'];
                $item = Yii::$app->db->createCommand("SELECT system_quantity FROM inventory_stock_audit_items WHERE id=:id")->bindValue(':id', $post['id'])->queryOne();
                if (!$item) {
                    return $this->jsonResponse(false, 'Item not found.');
                }
                Yii::$app->db->createCommand()->update('inventory_stock_audit_items', [
                    'physical_quantity' => $physical,
                    'variance' => $physical - $item['system_quantity'],
                    'remarks' => $post['remarks'] ?? null,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->currentUserId()
                ], ['id' => $post['id']])->execute();
                return $this->jsonResponse(true, 'Count verified and updated successfully.');
            }

            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Stock Variance report
     * ----------------------------------------------------------- */
    public function actionStockvariance()
    {
        if (Yii::$app->request->isGet) {
            $warehouses = Yii::$app->db->createCommand("SELECT id,warehouse_name FROM inventory_warehouses WHERE is_deleted=0 AND is_active=1 ORDER BY warehouse_name")->queryAll();
            return $this->renderPartial('stockvariance', ['warehouses' => $warehouses]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $warehouse_id = trim($post['warehouse_id'] ?? '');
                $audit_id = trim($post['audit_id'] ?? '');
                $where = " WHERE i.is_deleted=0 AND i.variance<>0 ";
                $params = [];
                if ($warehouse_id != '') {
                    $where .= " AND a.warehouse_id=:warehouse_id";
                    $params[':warehouse_id'] = $warehouse_id;
                }
                if ($audit_id != '') {
                    $where .= " AND a.id=:audit_id";
                    $params[':audit_id'] = $audit_id;
                }
                $rows = Yii::$app->db->createCommand("
                    SELECT
                        a.audit_no, a.audit_date, a.status, w.warehouse_name,
                        p.product_name, p.sku,
                        i.system_quantity, i.physical_quantity, i.variance,
                        (i.variance*p.purchase_price) variance_value
                    FROM inventory_stock_audit_items i
                    INNER JOIN inventory_stock_audits a ON a.id=i.audit_id
                    INNER JOIN inventory_products p ON p.id=i.product_id
                    INNER JOIN inventory_warehouses w ON w.id=a.warehouse_id
                    $where
                    ORDER BY a.audit_date DESC
                ", $params)->queryAll();

                $summary = [
                    'total_variance_items' => count($rows),
                    'total_shortage' => array_sum(array_map(function ($r) {
                        return $r['variance'] < 0 ? abs($r['variance']) : 0;
                    }, $rows)),
                    'total_excess' => array_sum(array_map(function ($r) {
                        return $r['variance'] > 0 ? $r['variance'] : 0;
                    }, $rows)),
                    'total_variance_value' => array_sum(array_column($rows, 'variance_value')),
                ];

                return $this->jsonResponse(true, 'Data loaded successfully!', ['rows' => $rows, 'summary' => $summary]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Stock Reconciliation & Adjustment Approval
     * (post the counted variance into inventory_stock + movements)
     * ----------------------------------------------------------- */
    public function actionStockreconciliation()
    {
        if (Yii::$app->request->isGet) {
            $audits = Yii::$app->db->createCommand("
                SELECT a.*, w.warehouse_name,
                    SUM(CASE WHEN i.variance<>0 THEN 1 ELSE 0 END) variance_count
                FROM inventory_stock_audits a
                INNER JOIN inventory_warehouses w ON w.id=a.warehouse_id
                LEFT JOIN inventory_stock_audit_items i ON i.audit_id=a.id AND i.is_deleted=0
                WHERE a.is_deleted=0 AND a.status='Open'
                GROUP BY a.id
                HAVING variance_count>0
                ORDER BY a.audit_date DESC
            ")->queryAll();
            return $this->renderPartial('stockreconciliation', ['audits' => $audits]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {
                $audits = Yii::$app->db->createCommand("
                    SELECT a.*, w.warehouse_name,
                        SUM(CASE WHEN i.variance<>0 THEN 1 ELSE 0 END) variance_count
                    FROM inventory_stock_audits a
                    INNER JOIN inventory_warehouses w ON w.id=a.warehouse_id
                    LEFT JOIN inventory_stock_audit_items i ON i.audit_id=a.id AND i.is_deleted=0
                    WHERE a.is_deleted=0 AND a.status='Open'
                    GROUP BY a.id
                    HAVING variance_count>0
                    ORDER BY a.audit_date DESC
                ")->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['audits' => $audits]);
            }

            if (isset($post['flag']) && $post['flag'] == 'reconcile') {
                if (empty($post['id'])) {
                    return $this->jsonResponse(false, 'Audit id is required.');
                }
                return $this->applyAuditVariance($post['id'], $this->currentUserId());
            }

            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function actionAdjustmentapproval()
    {
        if (Yii::$app->request->isGet) {
            $audits = Yii::$app->db->createCommand("
                SELECT a.*, w.warehouse_name,
                    SUM(CASE WHEN i.variance<>0 THEN 1 ELSE 0 END) variance_count,
                    IFNULL(SUM(i.variance*p.purchase_price),0) variance_value
                FROM inventory_stock_audits a
                INNER JOIN inventory_warehouses w ON w.id=a.warehouse_id
                LEFT JOIN inventory_stock_audit_items i ON i.audit_id=a.id AND i.is_deleted=0
                LEFT JOIN inventory_products p ON p.id=i.product_id
                WHERE a.is_deleted=0 AND a.status='Open'
                GROUP BY a.id
                ORDER BY a.audit_date DESC
            ")->queryAll();
            return $this->renderPartial('adjustmentapproval', ['audits' => $audits]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'approve') {
                if (empty($post['id'])) {
                    return $this->jsonResponse(false, 'Audit id is required.');
                }
                return $this->applyAuditVariance($post['id'], $this->currentUserId());
            }

            if (isset($post['flag']) && $post['flag'] == 'reject') {
                if (empty($post['id'])) {
                    return $this->jsonResponse(false, 'Audit id is required.');
                }
                Yii::$app->db->createCommand()->update('inventory_stock_audits',
                    ['status' => 'Completed', 'remarks' => 'Rejected: ' . ($post['reason'] ?? ''), 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                    ['id' => $post['id']])->execute();
                return $this->jsonResponse(true, 'Audit adjustment rejected and closed without stock changes.');
            }

            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function applyAuditVariance($audit_id, $user_id)
    {
        $audit = Yii::$app->db->createCommand("SELECT * FROM inventory_stock_audits WHERE id=:id AND is_deleted=0")->bindValue(':id', $audit_id)->queryOne();
        if (!$audit) {
            return $this->jsonResponse(false, 'Audit not found.');
        }
        if ($audit['status'] == 'Completed') {
            return $this->jsonResponse(false, 'This audit has already been reconciled.');
        }

        $items = Yii::$app->db->createCommand("
            SELECT * FROM inventory_stock_audit_items WHERE audit_id=:id AND is_deleted=0 AND variance<>0
        ")->bindValue(':id', $audit_id)->queryAll();

        $trans = Yii::$app->db->beginTransaction();
        try {
            foreach ($items as $item) {
                $stock = Yii::$app->db->createCommand("
                    SELECT * FROM inventory_stock WHERE warehouse_id=:w AND product_id=:p AND is_deleted=0
                ")->bindValues([':w' => $audit['warehouse_id'], ':p' => $item['product_id']])->queryOne();

                $newQty = $item['physical_quantity'];

                if ($stock) {
                    Yii::$app->db->createCommand()->update('inventory_stock', [
                        'quantity' => $newQty,
                        'available_quantity' => $newQty - $stock['reserved_quantity'],
                        'updated_by' => $user_id
                    ], ['id' => $stock['id']])->execute();
                } else {
                    Yii::$app->db->createCommand()->insert('inventory_stock', [
                        'warehouse_id' => $audit['warehouse_id'],
                        'product_id' => $item['product_id'],
                        'quantity' => $newQty,
                        'reserved_quantity' => 0,
                        'available_quantity' => $newQty,
                        'created_by' => $user_id,
                        'updated_by' => $user_id
                    ])->execute();
                }

                Yii::$app->db->createCommand()->insert('inventory_stock_movements', [
                    'movement_no' => $this->generateDocNo('AUDM'),
                    'warehouse_id' => $audit['warehouse_id'],
                    'product_id' => $item['product_id'],
                    'reference_type' => 'Stock Audit',
                    'reference_id' => $audit_id,
                    'movement_type' => $item['variance'] > 0 ? 'IN' : 'OUT',
                    'quantity' => abs($item['variance']),
                    'remarks' => 'Reconciliation for audit ' . $audit['audit_no'],
                    'created_by' => $user_id,
                    'updated_by' => $user_id
                ])->execute();
            }

            Yii::$app->db->createCommand()->update('inventory_stock_audits',
                ['status' => 'Completed', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $user_id],
                ['id' => $audit_id])->execute();

            $trans->commit();

            return $this->jsonResponse(true, 'Stock reconciled successfully. ' . count($items) . ' item(s) adjusted.');
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Audit History
     * ----------------------------------------------------------- */
    public function actionAudithistory()
    {
        if (Yii::$app->request->isGet) {
            $perPage = (int)Yii::$app->request->get('per_page', 20);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;

            $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_stock_audits WHERE is_deleted=0")->queryScalar();
            $audits = Yii::$app->db->createCommand("
                SELECT a.*, w.warehouse_name,
                    COUNT(i.id) item_count,
                    SUM(CASE WHEN i.variance<>0 THEN 1 ELSE 0 END) variance_count
                FROM inventory_stock_audits a
                LEFT JOIN inventory_warehouses w ON w.id=a.warehouse_id
                LEFT JOIN inventory_stock_audit_items i ON i.audit_id=a.id AND i.is_deleted=0
                WHERE a.is_deleted=0
                GROUP BY a.id
                ORDER BY a.audit_date DESC
                LIMIT $offset,$perPage
            ")->queryAll();

            return $this->renderPartial('audithistory', [
                'audits' => $audits, 'total' => $total, 'page' => $page, 'perPage' => $perPage, 'totalPages' => ceil($total / $perPage)
            ]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $warehouse_id = trim($post['warehouse_id'] ?? '');
                $status = trim($post['status'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;

                $where = " WHERE a.is_deleted=0 ";
                $params = [];
                if ($warehouse_id != '') {
                    $where .= " AND a.warehouse_id=:warehouse_id";
                    $params[':warehouse_id'] = $warehouse_id;
                }
                if ($status != '') {
                    $where .= " AND a.status=:status";
                    $params[':status'] = $status;
                }

                $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_stock_audits a $where", $params)->queryScalar();
                $audits = Yii::$app->db->createCommand("
                    SELECT a.*, w.warehouse_name,
                        COUNT(i.id) item_count,
                        SUM(CASE WHEN i.variance<>0 THEN 1 ELSE 0 END) variance_count
                    FROM inventory_stock_audits a
                    LEFT JOIN inventory_warehouses w ON w.id=a.warehouse_id
                    LEFT JOIN inventory_stock_audit_items i ON i.audit_id=a.id AND i.is_deleted=0
                    $where
                    GROUP BY a.id
                    ORDER BY a.audit_date DESC
                    LIMIT $offset,$perPage
                ", $params)->queryAll();

                return $this->jsonResponse(true, 'Data loaded successfully!', ['audits' => $audits, 'total' => (int)$total, 'page' => $page, 'per_page' => $perPage, 'total_pages' => ceil($total / $perPage)]);
            }
            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Audit Reports
     * ----------------------------------------------------------- */
    public function actionAuditreports()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('auditreports');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'load') {
                $from_date = trim($post['from_date'] ?? '');
                $to_date = trim($post['to_date'] ?? '');
                $where = " WHERE a.is_deleted=0 ";
                $params = [];
                if ($from_date != '') {
                    $where .= " AND a.audit_date>=:from_date";
                    $params[':from_date'] = $from_date;
                }
                if ($to_date != '') {
                    $where .= " AND a.audit_date<=:to_date";
                    $params[':to_date'] = $to_date;
                }

                $summary = Yii::$app->db->createCommand("
                    SELECT
                        COUNT(DISTINCT a.id) total_audits,
                        COUNT(i.id) total_items_counted,
                        SUM(CASE WHEN i.variance<>0 THEN 1 ELSE 0 END) total_variance_items,
                        IFNULL(SUM(i.variance*p.purchase_price),0) total_variance_value
                    FROM inventory_stock_audits a
                    LEFT JOIN inventory_stock_audit_items i ON i.audit_id=a.id AND i.is_deleted=0
                    LEFT JOIN inventory_products p ON p.id=i.product_id
                    $where
                ", $params)->queryOne();

                $warehouseBreakdown = Yii::$app->db->createCommand("
                    SELECT w.warehouse_name,
                        COUNT(DISTINCT a.id) total_audits,
                        SUM(CASE WHEN i.variance<>0 THEN 1 ELSE 0 END) variance_items,
                        IFNULL(SUM(i.variance*p.purchase_price),0) variance_value
                    FROM inventory_stock_audits a
                    INNER JOIN inventory_warehouses w ON w.id=a.warehouse_id
                    LEFT JOIN inventory_stock_audit_items i ON i.audit_id=a.id AND i.is_deleted=0
                    LEFT JOIN inventory_products p ON p.id=i.product_id
                    $where
                    GROUP BY a.warehouse_id
                    ORDER BY variance_value DESC
                ", $params)->queryAll();

                $auditList = Yii::$app->db->createCommand("
                    SELECT a.*, w.warehouse_name
                    FROM inventory_stock_audits a
                    INNER JOIN inventory_warehouses w ON w.id=a.warehouse_id
                    $where
                    ORDER BY a.audit_date DESC
                ", $params)->queryAll();

                return $this->jsonResponse(true, 'Data loaded successfully!', [
                    'summary' => $summary,
                    'warehouseBreakdown' => $warehouseBreakdown,
                    'auditList' => $auditList
                ]);
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
                FOREIGN KEY(warehouse_id) REFERENCES inventory_warehouses(id)
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
                FOREIGN KEY(audit_id) REFERENCES inventory_stock_audits(id) ON DELETE CASCADE,
                FOREIGN KEY(product_id) REFERENCES inventory_products(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $transaction->commit();

            echo "Stock audit tables created successfully.";
            exit;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo "Error: " . $e->getMessage();
            exit;
        }
    }
}
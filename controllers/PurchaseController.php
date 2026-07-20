<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class PurchaseController extends Controller
{
    private function currentUserId() {
        $user_array = Yii::$app->session->get('user_array');
        return $user_array['id'] ?? null;
    }
    private function jsonResponse($success, $message, $data = []) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return array_merge(['success' => $success, 'message' => $message], $data);
    }
    private function generateDocNo($prefix) {
        return $prefix . '-' . date('Ymd') . '-' . date('His') . '-' . mt_rand(100, 999);
    }

    private function generateGRNNumber() {
        $db = Yii::$app->db;
        $count = $db->createCommand("SELECT COUNT(*) + 1 FROM inventory_goods_receiving")->queryScalar();
        return 'GRN-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    private function generateInvoiceNumber($type = 'GR') {
        $db = Yii::$app->db;
        if ($type == 'PI') {
            $count = $db->createCommand("SELECT COUNT(*) + 1 FROM inventory_purchase_invoices")->queryScalar();
            return 'PINV-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
        } else {
            $count = $db->createCommand("SELECT COUNT(*) + 1 FROM inventory_goods_receiving")->queryScalar();
            return 'INV-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
        }
    }

    private function postPurchaseToGL($purchase_order_id, $invoice_no, $grand_total, $user_id)
    {
        $db = Yii::$app->db;

        try {
            // Get default purchase account from settings
            $purchaseAccountId = $db->createCommand(
                "SELECT setting_value FROM inventory_settings WHERE setting_key='default_purchase_account' AND is_deleted=0"
            )->queryScalar();

            if (!$purchaseAccountId) {
                return false; // Purchase account not configured
            }

            $transactionNo = 'PURCH-' . $invoice_no;

            // Debit: Cost of Goods Sold / Purchases Expense Account
            $db->createCommand()->insert('inventory_transactions', [
                'transaction_no' => $transactionNo . '-DR',
                'transaction_date' => date('Y-m-d'),
                'reference_type' => 'Purchase',
                'reference_id' => $purchase_order_id,
                'account_id' => $purchaseAccountId,
                'transaction_type' => 'Debit',
                'amount' => $grand_total,
                'remarks' => 'Purchase recorded - Invoice: ' . $invoice_no,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $user_id,
                'is_active' => 1,
                'is_deleted' => 0
            ])->execute();

            // Credit: Accounts Payable Account (Supplier payable tracking)
            $apAccountId = $db->createCommand(
                "SELECT id FROM inventory_accounts WHERE account_code='2000' AND is_deleted=0 LIMIT 1"
            )->queryScalar();

            if ($apAccountId) {
                $db->createCommand()->insert('inventory_transactions', [
                    'transaction_no' => $transactionNo . '-CR',
                    'transaction_date' => date('Y-m-d'),
                    'reference_type' => 'Purchase',
                    'reference_id' => $purchase_order_id,
                    'account_id' => $apAccountId,
                    'transaction_type' => 'Credit',
                    'amount' => $grand_total,
                    'remarks' => 'Accounts Payable - Invoice: ' . $invoice_no,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $user_id,
                    'is_active' => 1,
                    'is_deleted' => 0
                ])->execute();

                // Update account balance for Purchase Expense
                $db->createCommand()->update('inventory_accounts', [
                    'current_balance' => new \yii\db\Expression('current_balance + ' . $grand_total)
                ], ['id' => $purchaseAccountId])->execute();

                // Update account balance for Accounts Payable
                $db->createCommand()->update('inventory_accounts', [
                    'current_balance' => new \yii\db\Expression('current_balance + ' . $grand_total)
                ], ['id' => $apAccountId])->execute();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function createGoodsReceiving($db, $purchaseOrderId, $status) {
        $po = $db->createCommand("
            SELECT * FROM inventory_purchase_orders WHERE id=:id
        ",[':id' => $purchaseOrderId])->queryOne();

        if (!$po) return false;

        $grn_number = $this->generateGRNNumber();
        $invoice_no = $this->generateInvoiceNumber('GR');

        $goodsReceivingData = [
            'grn_number' => $grn_number,
            'purchase_order_id' => $purchaseOrderId,
            'supplier_id' => $po['supplier_id'],
            'warehouse_id' => $po['warehouse_id'],
            'receiving_date' => $po['expected_date'],
            'invoice_no' => $invoice_no,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'is_active' => 1,
            'is_deleted' => 0
        ];

        $db->createCommand()->insert('inventory_goods_receiving', $goodsReceivingData)->execute();
        return $db->getLastInsertID();
    }

    private function createPurchaseInvoice($db, $purchaseOrderId) {
        $po = $db->createCommand("
            SELECT * FROM inventory_purchase_orders WHERE id=:id
        ",[':id' => $purchaseOrderId])->queryOne();

        if (!$po) return false;

        $invoice_no = $this->generateInvoiceNumber('PI');

        $invoiceData = [
            'purchase_order_id' => $purchaseOrderId,
            'supplier_id' => $po['supplier_id'],
            'invoice_no' => $invoice_no,
            'invoice_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime($po['expected_date'] . ' + 30 days')),
            'subtotal' => $po['subtotal'],
            'discount_amount' => $po['discount'],
            'tax_amount' => $po['tax'],
            'grand_total' => $po['grand_total'],
            'paid_amount' => 0,
            'balance_amount' => $po['grand_total'],
            'status' => 'Unpaid',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'is_active' => 1,
            'is_deleted' => 0
        ];

        $db->createCommand()->insert('inventory_purchase_invoices', $invoiceData)->execute();
        $invoiceId = $db->getLastInsertID();

        // Copy PO items to invoice items
        $poItems = $db->createCommand("
            SELECT * FROM inventory_purchase_order_items
            WHERE purchase_order_id=:id AND is_deleted=0
        ",[':id' => $purchaseOrderId])->queryAll();

        foreach ($poItems as $item) {
            $invoiceItemData = [
                'purchase_invoice_id' => $invoiceId,
                'product_id' => $item['product_id'],
                'unit_id' => null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_amount' => $item['discount_amount'],
                'tax_amount' => $item['tax_amount'],
                'total_amount' => $item['line_total'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $db->createCommand()->insert('inventory_purchase_invoice_items', $invoiceItemData)->execute();
        }

        // Post to GL when invoice is created
        $this->postPurchaseToGL($purchaseOrderId, $invoice_no, $po['grand_total'], $this->currentUserId());

        return $invoiceId;
    }

    private function updateStockForCompletedPO($db, $purchaseOrderId) {
        $poItems = $db->createCommand("
            SELECT poi.*, p.id as product_id
            FROM inventory_purchase_order_items poi
            LEFT JOIN inventory_products p ON p.id=poi.product_id
            WHERE poi.purchase_order_id=:id AND poi.is_deleted=0
        ",[':id' => $purchaseOrderId])->queryAll();

        $po = $db->createCommand("
            SELECT warehouse_id FROM inventory_purchase_orders WHERE id=:id
        ",[':id' => $purchaseOrderId])->queryOne();

        if (!$po) return false;

        foreach ($poItems as $item) {
            $quantity = (float)$item['quantity'];
            $unitPrice = (float)$item['unit_price'];
            $totalCost = $quantity * $unitPrice;

            // Get current stock
            $currentStock = $db->createCommand("
                SELECT * FROM inventory_stock
                WHERE product_id=:product_id AND warehouse_id=:warehouse_id
            ",[':product_id' => $item['product_id'], ':warehouse_id' => $po['warehouse_id']])->queryOne();

            if ($currentStock) {
                $newQuantity = $currentStock['quantity'] + $quantity;
                // Calculate weighted average cost
                $currentCost = (float)$currentStock['average_cost'];
                $currentTotal = $currentStock['quantity'] * $currentCost;
                $newAverageCost = ($currentTotal + $totalCost) / $newQuantity;

                $db->createCommand()->update(
                    'inventory_stock',
                    [
                        'quantity' => $newQuantity,
                        'average_cost' => $newAverageCost,
                        'last_purchase_price' => $unitPrice,
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                    ['product_id' => $item['product_id'], 'warehouse_id' => $po['warehouse_id']]
                )->execute();
            } else {
                $db->createCommand()->insert(
                    'inventory_stock',
                    [
                        'product_id' => $item['product_id'],
                        'warehouse_id' => $po['warehouse_id'],
                        'quantity' => $quantity,
                        'average_cost' => $unitPrice,
                        'last_purchase_price' => $unitPrice,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'is_active' => 1,
                        'is_deleted' => 0
                    ]
                )->execute();
            }
        }

        return true;
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


    public function actionPurchases()
    {
        $modules = [
            ['name' => 'Purchase Dashboard', 'controller' => 'purchase/purchasedashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Purchase Orders', 'controller' => 'purchase/purchaseorders', 'icon' => 'fa fa-shopping-cart'],
            ['name' => 'Create Purchase', 'controller' => 'purchase/createpurchase', 'icon' => 'fa fa-plus'],
            ['name' => 'Goods Receiving', 'controller' => 'purchase/goodsreceiving', 'icon' => 'fa fa-truck'],
            ['name' => 'Purchase Invoices', 'controller' => 'purchase/purchaseinvoices', 'icon' => 'fa fa-file-text'],
            ['name' => 'Pending Purchases', 'controller' => 'purchase/pendingpurchases', 'icon' => 'fa fa-clock-o'],
            ['name' => 'Approved Purchases', 'controller' => 'purchase/approvedpurchases', 'icon' => 'fa fa-check-circle'],
            ['name' => 'Purchase Returns', 'controller' => 'purchase/purchasereturns', 'icon' => 'fa fa-undo'],
            ['name' => 'Supplier Payments', 'controller' => 'purchase/supplierpayments', 'icon' => 'fa fa-money'],
            ['name' => 'Purchase Reports', 'controller' => 'purchase/purchasereports', 'icon' => 'fa fa-bar-chart'],
            ['name' => 'Purchase Analytics', 'controller' => 'purchase/purchaseanalytics', 'icon' => 'fa fa-line-chart'],
        ];

        return $this->render('purchases', compact('modules'));
    }

    public function actionPurchasedashboard()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('purchasedashboard');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {

            $post = Yii::$app->request->post();

            if (!isset($post['flag']) || $post['flag'] != 'load_dashboard') {
                return [
                    'success' => false,
                    'message' => 'Invalid request.'
                ];
            }

            $db = Yii::$app->db;

            $stats = [];

            $stats['total_purchase_orders'] = (int)$db->createCommand("
            SELECT COUNT(*)
            FROM inventory_purchase_orders
            WHERE is_deleted=0
        ")->queryScalar();

            $stats['pending_purchase_orders'] = (int)$db->createCommand("
            SELECT COUNT(*)
            FROM inventory_purchase_orders
            WHERE is_deleted=0
            AND status='Pending'
        ")->queryScalar();

            $stats['approved_purchase_orders'] = (int)$db->createCommand("
            SELECT COUNT(*)
            FROM inventory_purchase_orders
            WHERE is_deleted=0
            AND status='Approved'
        ")->queryScalar();

            $stats['completed_purchase_orders'] = (int)$db->createCommand("
            SELECT COUNT(*)
            FROM inventory_purchase_orders
            WHERE is_deleted=0
            AND status='Completed'
        ")->queryScalar();

            $stats['cancelled_purchase_orders'] = (int)$db->createCommand("
            SELECT COUNT(*)
            FROM inventory_purchase_orders
            WHERE is_deleted=0
            AND status='Cancelled'
        ")->queryScalar();

            $stats['total_purchase_value'] = (float)$db->createCommand("
            SELECT IFNULL(SUM(grand_total),0)
            FROM inventory_purchase_orders
            WHERE is_deleted=0
        ")->queryScalar();

            $stats['total_goods_received'] = (int)$db->createCommand("
            SELECT COUNT(*)
            FROM inventory_goods_receiving
            WHERE is_deleted=0
        ")->queryScalar();

            $stats['pending_goods_receiving'] = (int)$db->createCommand("
            SELECT COUNT(*)
            FROM inventory_goods_receiving
            WHERE is_deleted=0
            AND status='Pending'
        ")->queryScalar();

            $stats['completed_goods_receiving'] = (int)$db->createCommand("
            SELECT COUNT(*)
            FROM inventory_goods_receiving
            WHERE is_deleted=0
            AND status='Completed'
        ")->queryScalar();

            $stats['total_suppliers'] = (int)$db->createCommand("
            SELECT COUNT(*)
            FROM inventory_suppliers
            WHERE is_deleted=0
            AND is_active=1
        ")->queryScalar();

            $stats['total_payment_amount'] = (float)$db->createCommand("
            SELECT IFNULL(SUM(amount),0)
            FROM inventory_payments
            WHERE is_deleted=0
            AND reference_type='Purchase'
        ")->queryScalar();

            $statusChart = $db->createCommand("
            SELECT
                status,
                COUNT(*) total
            FROM inventory_purchase_orders
            WHERE is_deleted=0
            GROUP BY status
            ORDER BY total DESC
        ")->queryAll();

            $supplierChart = $db->createCommand("
            SELECT
                s.company_name,
                IFNULL(SUM(po.grand_total),0) total
            FROM inventory_purchase_orders po
            LEFT JOIN inventory_suppliers s
                ON s.id=po.supplier_id
            WHERE po.is_deleted=0
            GROUP BY po.supplier_id,s.company_name
            ORDER BY total DESC
            LIMIT 10
        ")->queryAll();

            $monthlyPurchases = $db->createCommand("
            SELECT
                DATE_FORMAT(order_date,'%b %Y') month,
                IFNULL(SUM(grand_total),0) total
            FROM inventory_purchase_orders
            WHERE is_deleted=0
            GROUP BY YEAR(order_date),MONTH(order_date)
            ORDER BY YEAR(order_date),MONTH(order_date)
        ")->queryAll();

            $latestPurchaseOrders = $db->createCommand("
            SELECT
                po.po_number,
                s.company_name,
                po.order_date,
                po.status,
                po.grand_total
            FROM inventory_purchase_orders po
            LEFT JOIN inventory_suppliers s
                ON s.id=po.supplier_id
            WHERE po.is_deleted=0
            ORDER BY po.order_date DESC
            LIMIT 10
        ")->queryAll();

            $latestGoodsReceiving = $db->createCommand("
            SELECT
                gr.grn_number,
                po.po_number,
                s.company_name,
                gr.receiving_date,
                gr.status
            FROM inventory_goods_receiving gr
            LEFT JOIN inventory_purchase_orders po
                ON po.id=gr.purchase_order_id
            LEFT JOIN inventory_suppliers s
                ON s.id=gr.supplier_id
            WHERE gr.is_deleted=0
            ORDER BY gr.receiving_date DESC
            LIMIT 10
        ")->queryAll();

            return [
                'success' => true,
                'stats' => $stats,
                'statusChart' => $statusChart,
                'supplierChart' => $supplierChart,
                'monthlyPurchases' => $monthlyPurchases,
                'latestPurchaseOrders' => $latestPurchaseOrders,
                'latestGoodsReceiving' => $latestGoodsReceiving
            ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    

    public function actionPurchaseorders()
    {
        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {

                if (isset($post['flag']) && $post['flag'] == 'load') {

                    $page = max(1, (int)($post['page'] ?? 1));
                    $perPage = max(10, (int)($post['per_page'] ?? 20));
                    $offset = ($page - 1) * $perPage;

                    $supplier_id = trim($post['supplier_id'] ?? '');
                    $warehouse_id = trim($post['warehouse_id'] ?? '');
                    $status = trim($post['status'] ?? '');
                    $po_number = trim($post['po_number'] ?? '');
                    $from_date = trim($post['from_date'] ?? '');
                    $to_date = trim($post['to_date'] ?? '');

                    $where = " WHERE po.is_deleted=0 ";
                    $params = [];

                    if ($supplier_id != '') {
                        $where .= " AND po.supplier_id=:supplier_id ";
                        $params[':supplier_id'] = $supplier_id;
                    }

                    if ($warehouse_id != '') {
                        $where .= " AND po.warehouse_id=:warehouse_id ";
                        $params[':warehouse_id'] = $warehouse_id;
                    }

                    if ($status != '') {
                        $where .= " AND po.status=:status ";
                        $params[':status'] = $status;
                    }

                    if ($po_number != '') {
                        $where .= " AND po.po_number LIKE :po_number ";
                        $params[':po_number'] = '%' . $po_number . '%';
                    }

                    if ($from_date != '') {
                        $where .= " AND po.order_date>=:from_date ";
                        $params[':from_date'] = $from_date;
                    }

                    if ($to_date != '') {
                        $where .= " AND po.order_date<=:to_date ";
                        $params[':to_date'] = $to_date;
                    }

                    $total = $db = Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_purchase_orders po
                        {$where}
                    ", $params)->queryScalar();

                    $purchaseOrders = Yii::$app->db->createCommand("
                        SELECT
                            po.*,
                            s.company_name,
                            w.warehouse_name
                        FROM inventory_purchase_orders po
                        LEFT JOIN inventory_suppliers s
                            ON s.id=po.supplier_id
                        LEFT JOIN inventory_warehouses w
                            ON w.id=po.warehouse_id
                        {$where}
                        ORDER BY po.id DESC
                        LIMIT {$offset},{$perPage}
                    ", $params)->queryAll();

                    return [
                        'success' => true,
                        'purchaseOrders' => $purchaseOrders,
                        'page' => $page,
                        'perPage' => $perPage,
                        'total' => $total,
                        'totalPages' => ceil($total / $perPage)
                    ];
                }
                if (isset($post['flag']) && $post['flag'] == 'loadProducts') {

                    $products = Yii::$app->db->createCommand("
                        SELECT
                            p.id,
                            p.product_name,
                            p.sku,
                            p.selling_price as sale_price,
                            p.purchase_price,
                            u.unit_name
                        FROM inventory_products p
                        LEFT JOIN inventory_units u
                            ON u.id=p.unit_id
                        WHERE p.is_deleted=0
                        AND p.is_active=1
                        ORDER BY p.product_name
                    ")->queryAll();

                    return [
                        'success' => true,
                        'products' => $products
                    ];
                }

                if (isset($post['flag']) && $post['flag'] == 'getOrder') {

                    $id = (int)$post['id'];

                    $order = Yii::$app->db->createCommand("
                        SELECT *
                        FROM inventory_purchase_orders
                        WHERE id=:id
                        LIMIT 1
                    ",[
                        ':id'=>$id
                    ])->queryOne();

                    $items = Yii::$app->db->createCommand("
                        SELECT
                            i.*,
                            p.product_name,
                            p.sku
                        FROM inventory_purchase_order_items i
                        LEFT JOIN inventory_products p
                            ON p.id=i.product_id
                        WHERE i.purchase_order_id=:id
                        AND i.is_deleted=0
                        ORDER BY i.id
                    ",[
                        ':id'=>$id
                    ])->queryAll();

                    return [
                        'success'=>true,
                        'order'=>$order,
                        'items'=>$items
                    ];
                }

                
                if (isset($post['flag']) && $post['flag'] == 'save') {

                    $db=Yii::$app->db;
                    $transaction=$db->beginTransaction();

                    try{

                        $id=(int)($post['id']??0);

                        $header=[

                            'supplier_id'=>$post['supplier_id'],
                            'warehouse_id'=>$post['warehouse_id'],
                            'order_date'=>$post['order_date'],
                            'expected_date'=>$post['expected_date'],
                            'payment_terms'=>$post['payment_terms'],
                            'status'=>$post['status'],
                            'subtotal'=>$post['subtotal'],
                            'discount'=>$post['discount_amount'],
                            'tax'=>$post['tax_amount'],
                            'freight'=>$post['shipping_amount'],
                            'grand_total'=>$post['grand_total'],
                            'notes'=>$post['remarks'],
                            'updated_at'=>date('Y-m-d H:i:s')

                        ];

                        if($id>0){

                            $db->createCommand()->update(
                                'inventory_purchase_orders',
                                $header,
                                ['id'=>$id]
                            )->execute();

                            $purchaseOrderId=$id;

                        }else{

                            $header['po_number']='PO-'.date('YmdHis').rand(100,999);
                            $header['created_at']=date('Y-m-d H:i:s');
                            $header['is_active']=1;
                            $header['is_deleted']=0;

                            $db->createCommand()->insert(
                                'inventory_purchase_orders',
                                $header
                            )->execute();

                            $purchaseOrderId=$db->getLastInsertID();

                        }
                        if($id>0){
                            $db->createCommand()->update(
                                'inventory_purchase_order_items',
                                ['is_deleted'=>1,'updated_at'=>date('Y-m-d H:i:s')],
                                ['purchase_order_id'=>$purchaseOrderId]
                            )->execute();
                        }
                        $items=json_decode($post['items'],true);
                        if(is_array($items)){
                            foreach($items as $row){
                                if(empty($row['product_id'])){continue;}
                                $qty=(float)$row['quantity'];
                                $db->createCommand()->insert(
                                    'inventory_purchase_order_items',
                                    [
                                        'purchase_order_id'=>$purchaseOrderId,
                                        'product_id'=>$row['product_id'],
                                        'quantity'=>$qty,
                                        'received_quantity'=>0,
                                        'remaining_quantity'=>$qty,
                                        'unit_price'=>$row['unit_price'],
                                        'discount_amount'=>$row['discount_amount'],
                                        'tax_amount'=>$row['tax_amount'],
                                        'line_total'=>$row['line_total'],
                                        'remarks'=>$row['remarks'],
                                        'created_at'=>date('Y-m-d H:i:s'),
                                        'is_active'=>1,
                                        'is_deleted'=>0
                                    ]
                                )->execute();
                            }
                        }

                        // AUTO-CREATE GOODS RECEIVING AND PURCHASE INVOICE
                        if($id==0){
                            // New PO - auto create related documents
                            $poStatus = $post['status'];
                            $goodsReceivingId = $this->createGoodsReceiving($db, $purchaseOrderId, $poStatus);
                            $invoiceId = $this->createPurchaseInvoice($db, $purchaseOrderId);
                        }

                        // If status is Completed, update stock
                        if($post['status'] == 'Completed'){
                            $this->updateStockForCompletedPO($db, $purchaseOrderId);
                            // Also update GR status to Completed
                            $db->createCommand()->update(
                                'inventory_goods_receiving',
                                ['status' => 'Completed', 'updated_at' => date('Y-m-d H:i:s')],
                                ['purchase_order_id' => $purchaseOrderId]
                            )->execute();
                        }

                        $transaction->commit();
                        return[
                            'success'=>true,
                            'message'=>'Purchase Order saved successfully. Goods Receiving and Invoice auto-created!'
                        ];
                    }catch(\Exception $e){
                        $transaction->rollBack();
                        return[
                            'success'=>false,
                            'message'=>$e->getMessage()
                        ];
                    }
                }

                if (isset($post['flag']) && $post['flag'] == 'delete') {

                    Yii::$app->db->createCommand()->update(
                        'inventory_purchase_orders',
                        [
                            'is_deleted' => 1,
                            'updated_at' => date('Y-m-d H:i:s')
                        ],
                        ['id' => $post['id']]
                    )->execute();

                    return [
                        'success' => true,
                        'message' => 'Purchase Order deleted successfully.'
                    ];
                }

                if (isset($post['flag']) && $post['flag'] == 'updateStatus') {

                    $db = Yii::$app->db;
                    $transaction = $db->beginTransaction();

                    try {
                        $poId = (int)($post['id'] ?? 0);
                        $newStatus = trim($post['status'] ?? '');

                        if ($poId <= 0 || empty($newStatus)) {
                            return ['success' => false, 'message' => 'Invalid PO ID or status.'];
                        }

                        // Update PO status
                        $db->createCommand()->update(
                            'inventory_purchase_orders',
                            ['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')],
                            ['id' => $poId]
                        )->execute();

                        // Update GR status to match PO status
                        $db->createCommand()->update(
                            'inventory_goods_receiving',
                            ['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')],
                            ['purchase_order_id' => $poId]
                        )->execute();

                        // If status is Completed, update stock
                        if ($newStatus == 'Completed') {
                            $this->updateStockForCompletedPO($db, $poId);
                        }

                        $transaction->commit();
                        return [
                            'success' => true,
                            'message' => 'Purchase Order status updated successfully.'
                        ];
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => $e->getMessage()
                        ];
                    }
                }

                return [
                    'success' => false,
                    'message' => 'Invalid request.'
                ];
            } catch (\Exception $e) {

                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        $suppliers = Yii::$app->db->createCommand("
            SELECT id,company_name
            FROM inventory_suppliers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY company_name
        ")->queryAll();

        $warehouses = Yii::$app->db->createCommand("
            SELECT id,warehouse_name
            FROM inventory_warehouses
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY warehouse_name
        ")->queryAll();

        $purchaseOrders = Yii::$app->db->createCommand("
            SELECT
                po.*,
                s.company_name,
                w.warehouse_name
            FROM inventory_purchase_orders po
            LEFT JOIN inventory_suppliers s
                ON s.id=po.supplier_id
            LEFT JOIN inventory_warehouses w
                ON w.id=po.warehouse_id
            WHERE po.is_deleted=0
            ORDER BY po.id DESC
            LIMIT 20
        ")->queryAll();

        return $this->renderPartial('purchaseorders', [
            'purchaseOrders' => $purchaseOrders,
            'suppliers' => $suppliers,
            'warehouses' => $warehouses
        ]);
    }

    public function actionGoodsreceiving()
    {
        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {

                if (isset($post['flag']) && $post['flag'] == 'load') {

                    $page = max(1, (int)($post['page'] ?? 1));
                    $perPage = max(10, (int)($post['per_page'] ?? 20));
                    $offset = ($page - 1) * $perPage;

                    $supplier_id = trim($post['supplier_id'] ?? '');
                    $warehouse_id = trim($post['warehouse_id'] ?? '');
                    $purchase_order_id = trim($post['purchase_order_id'] ?? '');
                    $status = trim($post['status'] ?? '');
                    $grn_number = trim($post['grn_number'] ?? '');
                    $from_date = trim($post['from_date'] ?? '');
                    $to_date = trim($post['to_date'] ?? '');

                    $where = " WHERE gr.is_deleted=0 ";
                    $params = [];

                    if ($supplier_id != '') {
                        $where .= " AND gr.supplier_id=:supplier_id ";
                        $params[':supplier_id'] = $supplier_id;
                    }

                    if ($warehouse_id != '') {
                        $where .= " AND gr.warehouse_id=:warehouse_id ";
                        $params[':warehouse_id'] = $warehouse_id;
                    }

                    if ($purchase_order_id != '') {
                        $where .= " AND gr.purchase_order_id=:purchase_order_id ";
                        $params[':purchase_order_id'] = $purchase_order_id;
                    }

                    if ($status != '') {
                        $where .= " AND gr.status=:status ";
                        $params[':status'] = $status;
                    }

                    if ($grn_number != '') {
                        $where .= " AND gr.grn_number LIKE :grn_number ";
                        $params[':grn_number'] = '%' . $grn_number . '%';
                    }

                    if ($from_date != '') {
                        $where .= " AND gr.receiving_date>=:from_date ";
                        $params[':from_date'] = $from_date;
                    }

                    if ($to_date != '') {
                        $where .= " AND gr.receiving_date<=:to_date ";
                        $params[':to_date'] = $to_date;
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_goods_receiving gr
                        {$where}
                    ", $params)->queryScalar();

                    $goodsReceiving = Yii::$app->db->createCommand("
                        SELECT
                            gr.*,
                            po.po_number,
                            s.company_name,
                            w.warehouse_name
                        FROM inventory_goods_receiving gr
                        LEFT JOIN inventory_purchase_orders po
                            ON po.id=gr.purchase_order_id
                        LEFT JOIN inventory_suppliers s
                            ON s.id=gr.supplier_id
                        LEFT JOIN inventory_warehouses w
                            ON w.id=gr.warehouse_id
                        {$where}
                        ORDER BY gr.id DESC
                        LIMIT {$offset},{$perPage}
                    ", $params)->queryAll();

                    return [
                        'success' => true,
                        'goodsReceiving' => $goodsReceiving,
                        'page' => $page,
                        'perPage' => $perPage,
                        'total' => $total,
                        'totalPages' => ceil($total / $perPage)
                    ];
                }

                if (isset($post['flag']) && $post['flag'] == 'save') {

                    $db = Yii::$app->db;
                    $transaction = $db->beginTransaction();

                    try {

                        $id = (int)($post['id'] ?? 0);

                        $data = [
                            'purchase_order_id' => $post['purchase_order_id'],
                            'supplier_id' => $post['supplier_id'],
                            'warehouse_id' => $post['warehouse_id'],
                            'receiving_date' => $post['receiving_date'],
                            'reference_no' => $post['reference_no'],
                            'invoice_no' => $post['invoice_no'],
                            'status' => $post['status'],
                            'remarks' => $post['remarks'],
                            'updated_at' => date('Y-m-d H:i:s')
                        ];

                        if ($id > 0) {

                            $db->createCommand()->update(
                                'inventory_goods_receiving',
                                $data,
                                ['id' => $id]
                            )->execute();

                            $goodsReceivingId = $id;
                        } else {

                            $data['grn_number'] = 'GRN-' . date('YmdHis') . rand(100, 999);
                            $data['created_at'] = date('Y-m-d H:i:s');
                            $data['is_active'] = 1;
                            $data['is_deleted'] = 0;

                            $db->createCommand()->insert(
                                'inventory_goods_receiving',
                                $data
                            )->execute();

                            $goodsReceivingId = $db->getLastInsertID();
                        }

                        $transaction->commit();

                        return [
                            'success' => true,
                            'message' => 'Goods Receiving saved successfully.',
                            'id' => $goodsReceivingId
                        ];
                    } catch (\Exception $e) {

                        $transaction->rollBack();

                        return [
                            'success' => false,
                            'message' => $e->getMessage()
                        ];
                    }
                }

                if (isset($post['flag']) && $post['flag'] == 'delete') {

                    Yii::$app->db->createCommand()->update(
                        'inventory_goods_receiving',
                        [
                            'is_deleted' => 1,
                            'updated_at' => date('Y-m-d H:i:s')
                        ],
                        ['id' => $post['id']]
                    )->execute();

                    return [
                        'success' => true,
                        'message' => 'Goods Receiving deleted successfully.'
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Invalid request.'
                ];
            } catch (\Exception $e) {

                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        $purchaseOrders = Yii::$app->db->createCommand("
            SELECT id,po_number
            FROM inventory_purchase_orders
            WHERE is_deleted=0
            ORDER BY po_number
        ")->queryAll();

        $suppliers = Yii::$app->db->createCommand("
            SELECT id,company_name
            FROM inventory_suppliers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY company_name
        ")->queryAll();

        $warehouses = Yii::$app->db->createCommand("
            SELECT id,warehouse_name
            FROM inventory_warehouses
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY warehouse_name
        ")->queryAll();

        $goodsReceiving = Yii::$app->db->createCommand("
            SELECT
                gr.*,
                po.po_number,
                s.company_name,
                w.warehouse_name
            FROM inventory_goods_receiving gr
            LEFT JOIN inventory_purchase_orders po
                ON po.id=gr.purchase_order_id
            LEFT JOIN inventory_suppliers s
                ON s.id=gr.supplier_id
            LEFT JOIN inventory_warehouses w
                ON w.id=gr.warehouse_id
            WHERE gr.is_deleted=0
            ORDER BY gr.id DESC
            LIMIT 20
        ")->queryAll();

        return $this->renderPartial('goodsreceiving', [
            'goodsReceiving' => $goodsReceiving,
            'purchaseOrders' => $purchaseOrders,
            'suppliers' => $suppliers,
            'warehouses' => $warehouses
        ]);
    }

    public function actionPurchaseinvoices()
    {
        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {

                if (isset($post['flag']) && $post['flag'] == 'load') {

                    $page = max(1, (int)($post['page'] ?? 1));
                    $perPage = max(10, (int)($post['per_page'] ?? 20));
                    $offset = ($page - 1) * $perPage;

                    $supplier_id = trim($post['supplier_id'] ?? '');
                    $purchase_order_id = trim($post['purchase_order_id'] ?? '');
                    $status = trim($post['status'] ?? '');
                    $invoice_no = trim($post['invoice_no'] ?? '');
                    $from_date = trim($post['from_date'] ?? '');
                    $to_date = trim($post['to_date'] ?? '');

                    $where = " WHERE pi.is_deleted=0 ";
                    $params = [];

                    if ($supplier_id != '') {
                        $where .= " AND pi.supplier_id=:supplier_id ";
                        $params[':supplier_id'] = $supplier_id;
                    }

                    if ($purchase_order_id != '') {
                        $where .= " AND pi.purchase_order_id=:purchase_order_id ";
                        $params[':purchase_order_id'] = $purchase_order_id;
                    }

                    if ($status != '') {
                        $where .= " AND pi.status=:status ";
                        $params[':status'] = $status;
                    }

                    if ($invoice_no != '') {
                        $where .= " AND pi.invoice_no LIKE :invoice_no ";
                        $params[':invoice_no'] = '%' . $invoice_no . '%';
                    }

                    if ($from_date != '') {
                        $where .= " AND pi.invoice_date>=:from_date ";
                        $params[':from_date'] = $from_date;
                    }

                    if ($to_date != '') {
                        $where .= " AND pi.invoice_date<=:to_date ";
                        $params[':to_date'] = $to_date;
                    }

                    $total = Yii::$app->db->createCommand("
                    SELECT COUNT(*)
                    FROM inventory_purchase_invoices pi
                    {$where}
                ", $params)->queryScalar();

                    $purchaseInvoices = Yii::$app->db->createCommand("
                    SELECT
                        pi.*,
                        po.po_number,
                        po.status AS po_status,
                        s.company_name
                    FROM inventory_purchase_invoices pi
                    LEFT JOIN inventory_purchase_orders po
                        ON po.id=pi.purchase_order_id
                    LEFT JOIN inventory_suppliers s
                        ON s.id=pi.supplier_id
                    {$where}
                    ORDER BY pi.id DESC
                    LIMIT {$offset},{$perPage}
                ", $params)->queryAll();

                    return [
                        'success' => true,
                        'purchaseInvoices' => $purchaseInvoices,
                        'page' => $page,
                        'perPage' => $perPage,
                        'total' => $total,
                        'totalPages' => ceil($total / $perPage)
                    ];
                }

                if (isset($post['flag']) && $post['flag'] == 'save') {

                    $db = Yii::$app->db;
                    $transaction = $db->beginTransaction();

                    try {

                        $id = (int)($post['id'] ?? 0);

                        if ($id > 0) {
                            // On edit - allow Status and Remarks, and payment info if PO is Completed
                            $invoice = $db->createCommand("
                                SELECT pi.*, po.status AS po_status
                                FROM inventory_purchase_invoices pi
                                LEFT JOIN inventory_purchase_orders po ON po.id = pi.purchase_order_id
                                WHERE pi.id = :id
                            ", [':id' => $id])->queryOne();

                            if (!$invoice) {
                                throw new \Exception('Invoice not found');
                            }

                            $data = [
                                'status' => $post['status'],
                                'remarks' => $post['remarks'],
                                'updated_at' => date('Y-m-d H:i:s')
                            ];

                            // Allow payment amount updates always (not just when PO is Completed)
                            if (isset($post['paid_amount'])) {
                                $paidAmount = floatval($post['paid_amount'] ?? 0);
                                $previousPaidAmount = floatval($invoice['paid_amount'] ?? 0);

                                // Process if paid amount has changed
                                if ($paidAmount != $previousPaidAmount) {
                                    $paymentAmount = $paidAmount - $previousPaidAmount;
                                    $remainingBalance = floatval($invoice['grand_total']) - $paidAmount;

                                    // Ensure remaining balance doesn't go negative
                                    if ($remainingBalance < 0) {
                                        $remainingBalance = 0;
                                        $paidAmount = floatval($invoice['grand_total']);
                                    }

                                    $data['paid_amount'] = $paidAmount;
                                    $data['balance_amount'] = $remainingBalance;

                                    // Create payment history record if payment amount increased
                                    if ($paymentAmount > 0) {
                                        $db->createCommand()->insert(
                                            'inventory_purchase_invoice_payments',
                                            [
                                                'purchase_invoice_id' => $id,
                                                'paid_amount' => $paymentAmount,
                                                'payment_date' => date('Y-m-d'),
                                                'remarks' => $post['remarks'] ?? '',
                                                'created_by' => $this->currentUserId()
                                            ]
                                        )->execute();
                                    }

                                    // Update status to Paid if remaining balance is 0
                                    if ($remainingBalance <= 0) {
                                        $data['status'] = 'Paid';

                                        // Create supplier payment entry when invoice is fully paid
                                        try {
                                            $db->createCommand()->insert(
                                                'inventory_payments',
                                                [
                                                    'supplier_id' => $invoice['supplier_id'],
                                                    'reference_type' => 'Purchase Invoice',
                                                    'reference_id' => $id,
                                                    'payment_amount' => $paidAmount,
                                                    'payment_date' => date('Y-m-d'),
                                                    'payment_method' => 'Invoice Payment',
                                                    'remarks' => 'Full payment for invoice ' . $invoice['invoice_no'],
                                                    'created_by' => $this->currentUserId(),
                                                    'created_at' => date('Y-m-d H:i:s')
                                                ]
                                            )->execute();
                                        } catch (\Exception $e) {
                                            // Log table might not exist or other error, continue anyway
                                        }
                                    }
                                }
                            }

                            $db->createCommand()->update(
                                'inventory_purchase_invoices',
                                $data,
                                ['id' => $id]
                            )->execute();

                            $invoiceId = $id;
                        } else {
                            // On create - allow all fields
                            $data = [
                                'purchase_order_id' => $post['purchase_order_id'],
                                'supplier_id' => $post['supplier_id'],
                                'invoice_no' => $post['invoice_no'],
                                'invoice_date' => $post['invoice_date'],
                                'due_date' => $post['due_date'],
                                'subtotal' => $post['subtotal'],
                                'discount_amount' => $post['discount_amount'],
                                'tax_amount' => $post['tax_amount'],
                                'grand_total' => $post['grand_total'],
                                'status' => $post['status'],
                                'remarks' => $post['remarks'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'is_active' => 1,
                                'is_deleted' => 0,
                                'updated_at' => date('Y-m-d H:i:s')
                            ];

                            $db->createCommand()->insert(
                                'inventory_purchase_invoices',
                                $data
                            )->execute();

                            $invoiceId = $db->getLastInsertID();
                        }

                        $transaction->commit();

                        return [
                            'success' => true,
                            'message' => 'Purchase Invoice saved successfully.',
                            'id' => $invoiceId
                        ];
                    } catch (\Exception $e) {

                        $transaction->rollBack();

                        return [
                            'success' => false,
                            'message' => $e->getMessage()
                        ];
                    }
                }

                if (isset($post['flag']) && $post['flag'] == 'delete') {

                    Yii::$app->db->createCommand()->update(
                        'inventory_purchase_invoices',
                        [
                            'is_deleted' => 1,
                            'updated_at' => date('Y-m-d H:i:s')
                        ],
                        ['id' => $post['id']]
                    )->execute();

                    return [
                        'success' => true,
                        'message' => 'Purchase Invoice deleted successfully.'
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Invalid request.'
                ];
            } catch (\Exception $e) {

                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        $purchaseOrders = Yii::$app->db->createCommand("
            SELECT id,po_number
            FROM inventory_purchase_orders
            WHERE is_deleted=0
            ORDER BY po_number
        ")->queryAll();

        $suppliers = Yii::$app->db->createCommand("
            SELECT id,company_name
            FROM inventory_suppliers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY company_name
        ")->queryAll();

        $purchaseInvoices = Yii::$app->db->createCommand("
            SELECT
                pi.*,
                po.po_number,
                po.status AS po_status,
                s.company_name
            FROM inventory_purchase_invoices pi
            LEFT JOIN inventory_purchase_orders po
                ON po.id=pi.purchase_order_id
            LEFT JOIN inventory_suppliers s
                ON s.id=pi.supplier_id
            WHERE pi.is_deleted=0
            ORDER BY pi.id DESC
            LIMIT 20
        ")->queryAll();

        return $this->renderPartial('purchaseinvoices', [
            'purchaseInvoices' => $purchaseInvoices,
            'purchaseOrders' => $purchaseOrders,
            'suppliers' => $suppliers
        ]);
    }

    
    public function actionPendingpurchases()
    {
        if (Yii::$app->request->isPost) {

            $post=Yii::$app->request->post();
            Yii::$app->response->format=Response::FORMAT_JSON;

            try{

                if(isset($post['flag']) && $post['flag']=='load'){

                    $page=max(1,(int)($post['page']??1));
                    $perPage=max(10,(int)($post['per_page']??20));
                    $offset=($page-1)*$perPage;

                    $supplier_id=trim($post['supplier_id']??'');
                    $warehouse_id=trim($post['warehouse_id']??'');
                    $po_number=trim($post['po_number']??'');
                    $from_date=trim($post['from_date']??'');
                    $to_date=trim($post['to_date']??'');

                    $where=" WHERE po.is_deleted=0 AND po.status='Draft' ";
                    $params=[];

                    if($supplier_id!=''){
                        $where.=" AND po.supplier_id=:supplier_id ";
                        $params[':supplier_id']=$supplier_id;
                    }

                    if($warehouse_id!=''){
                        $where.=" AND po.warehouse_id=:warehouse_id ";
                        $params[':warehouse_id']=$warehouse_id;
                    }

                    if($po_number!=''){
                        $where.=" AND po.po_number LIKE :po_number ";
                        $params[':po_number']='%'.$po_number.'%';
                    }

                    if($from_date!=''){
                        $where.=" AND po.order_date>=:from_date ";
                        $params[':from_date']=$from_date;
                    }

                    if($to_date!=''){
                        $where.=" AND po.order_date<=:to_date ";
                        $params[':to_date']=$to_date;
                    }

                    $total=Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_purchase_orders po
                        {$where}
                    ",$params)->queryScalar();

                    $pendingPurchases=Yii::$app->db->createCommand("
                        SELECT
                            po.*,
                            s.company_name,
                            w.warehouse_name
                        FROM inventory_purchase_orders po
                        LEFT JOIN inventory_suppliers s
                            ON s.id=po.supplier_id
                        LEFT JOIN inventory_warehouses w
                            ON w.id=po.warehouse_id
                        {$where}
                        ORDER BY po.order_date DESC,po.id DESC
                        LIMIT {$offset},{$perPage}
                    ",$params)->queryAll();

                    return[
                        'success'=>true,
                        'pendingPurchases'=>$pendingPurchases,
                        'page'=>$page,
                        'perPage'=>$perPage,
                        'total'=>$total,
                        'totalPages'=>ceil($total/$perPage)
                    ];
                }

                if(isset($post['flag']) && $post['flag']=='approve'){

                    Yii::$app->db->createCommand()->update(
                        'inventory_purchase_orders',
                        [
                            'status'=>'Approved',
                            'updated_at'=>date('Y-m-d H:i:s')
                        ],
                        ['id'=>$post['id']]
                    )->execute();

                    return[
                        'success'=>true,
                        'message'=>'Purchase Order approved successfully.'
                    ];

                }

                if(isset($post['flag']) && $post['flag']=='cancel'){

                    Yii::$app->db->createCommand()->update(
                        'inventory_purchase_orders',
                        [
                            'status'=>'Cancelled',
                            'updated_at'=>date('Y-m-d H:i:s')
                        ],
                        ['id'=>$post['id']]
                    )->execute();

                    return[
                        'success'=>true,
                        'message'=>'Purchase Order cancelled successfully.'
                    ];

                }

                return[
                    'success'=>false,
                    'message'=>'Invalid request.'
                ];

            }catch(\Exception $e){

                return[
                    'success'=>false,
                    'message'=>$e->getMessage()
                ];

            }

        }

        $suppliers=Yii::$app->db->createCommand("
            SELECT id,company_name
            FROM inventory_suppliers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY company_name
        ")->queryAll();

        $warehouses=Yii::$app->db->createCommand("
            SELECT id,warehouse_name
            FROM inventory_warehouses
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY warehouse_name
        ")->queryAll();

        $pendingPurchases=Yii::$app->db->createCommand("
            SELECT
                po.*,
                s.company_name,
                w.warehouse_name
            FROM inventory_purchase_orders po
            LEFT JOIN inventory_suppliers s
                ON s.id=po.supplier_id
            LEFT JOIN inventory_warehouses w
                ON w.id=po.warehouse_id
            WHERE po.is_deleted=0
            AND po.status='Pending'
            ORDER BY po.order_date DESC,po.id DESC
            LIMIT 20
        ")->queryAll();

        return $this->renderPartial('pendingpurchases',[
            'pendingPurchases'=>$pendingPurchases,
            'suppliers'=>$suppliers,
            'warehouses'=>$warehouses
        ]);
    }

    public function actionApprovedpurchases()
    {
        if (Yii::$app->request->isPost) {

            $post=Yii::$app->request->post();
            Yii::$app->response->format=Response::FORMAT_JSON;

            try{

                if(isset($post['flag']) && $post['flag']=='load'){

                    $page=max(1,(int)($post['page']??1));
                    $perPage=max(10,(int)($post['per_page']??20));
                    $offset=($page-1)*$perPage;

                    $supplier_id=trim($post['supplier_id']??'');
                    $warehouse_id=trim($post['warehouse_id']??'');
                    $po_number=trim($post['po_number']??'');
                    $from_date=trim($post['from_date']??'');
                    $to_date=trim($post['to_date']??'');

                    $where=" WHERE po.is_deleted=0 AND po.status='Approved' ";
                    $params=[];

                    if($supplier_id!=''){
                        $where.=" AND po.supplier_id=:supplier_id ";
                        $params[':supplier_id']=$supplier_id;
                    }

                    if($warehouse_id!=''){
                        $where.=" AND po.warehouse_id=:warehouse_id ";
                        $params[':warehouse_id']=$warehouse_id;
                    }

                    if($po_number!=''){
                        $where.=" AND po.po_number LIKE :po_number ";
                        $params[':po_number']='%'.$po_number.'%';
                    }

                    if($from_date!=''){
                        $where.=" AND po.order_date>=:from_date ";
                        $params[':from_date']=$from_date;
                    }

                    if($to_date!=''){
                        $where.=" AND po.order_date<=:to_date ";
                        $params[':to_date']=$to_date;
                    }

                    $total=Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_purchase_orders po
                        {$where}
                    ",$params)->queryScalar();

                    $approvedPurchases=Yii::$app->db->createCommand("
                        SELECT
                            po.*,
                            s.company_name,
                            w.warehouse_name
                        FROM inventory_purchase_orders po
                        LEFT JOIN inventory_suppliers s
                            ON s.id=po.supplier_id
                        LEFT JOIN inventory_warehouses w
                            ON w.id=po.warehouse_id
                        {$where}
                        ORDER BY po.order_date DESC,po.id DESC
                        LIMIT {$offset},{$perPage}
                    ",$params)->queryAll();

                    return[
                        'success'=>true,
                        'approvedPurchases'=>$approvedPurchases,
                        'page'=>$page,
                        'perPage'=>$perPage,
                        'total'=>$total,
                        'totalPages'=>ceil($total/$perPage)
                    ];
                }

                if(isset($post['flag']) && $post['flag']=='complete'){

                    Yii::$app->db->createCommand()->update(
                        'inventory_purchase_orders',
                        [
                            'status'=>'Completed',
                            'updated_at'=>date('Y-m-d H:i:s')
                        ],
                        ['id'=>$post['id']]
                    )->execute();

                    return[
                        'success'=>true,
                        'message'=>'Purchase Order completed successfully.'
                    ];

                }

                if(isset($post['flag']) && $post['flag']=='cancel'){

                    Yii::$app->db->createCommand()->update(
                        'inventory_purchase_orders',
                        [
                            'status'=>'Cancelled',
                            'updated_at'=>date('Y-m-d H:i:s')
                        ],
                        ['id'=>$post['id']]
                    )->execute();

                    return[
                        'success'=>true,
                        'message'=>'Purchase Order cancelled successfully.'
                    ];

                }

                return[
                    'success'=>false,
                    'message'=>'Invalid request.'
                ];

            }catch(\Exception $e){

                return[
                    'success'=>false,
                    'message'=>$e->getMessage()
                ];

            }

        }

        $suppliers=Yii::$app->db->createCommand("
            SELECT id,company_name
            FROM inventory_suppliers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY company_name
        ")->queryAll();

        $warehouses=Yii::$app->db->createCommand("
            SELECT id,warehouse_name
            FROM inventory_warehouses
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY warehouse_name
        ")->queryAll();

        $approvedPurchases=Yii::$app->db->createCommand("
            SELECT
                po.*,
                s.company_name,
                w.warehouse_name
            FROM inventory_purchase_orders po
            LEFT JOIN inventory_suppliers s
                ON s.id=po.supplier_id
            LEFT JOIN inventory_warehouses w
                ON w.id=po.warehouse_id
            WHERE po.is_deleted=0
            AND po.status='Approved'
            ORDER BY po.order_date DESC,po.id DESC
            LIMIT 20
        ")->queryAll();

        return $this->renderPartial('approvedpurchases',[
            'approvedPurchases'=>$approvedPurchases,
            'suppliers'=>$suppliers,
            'warehouses'=>$warehouses
        ]);
    } 

    public function actionPurchasereturns()
    {
        if (Yii::$app->request->isPost) {

            $post=Yii::$app->request->post();
            Yii::$app->response->format=Response::FORMAT_JSON;

            try{

                if(isset($post['flag']) && $post['flag']=='load'){

                    $page=max(1,(int)($post['page']??1));
                    $perPage=max(10,(int)($post['per_page']??20));
                    $offset=($page-1)*$perPage;

                    $supplier_id=trim($post['supplier_id']??'');
                    $purchase_invoice_id=trim($post['purchase_invoice_id']??'');
                    $status=trim($post['status']??'');
                    $return_no=trim($post['return_no']??'');
                    $from_date=trim($post['from_date']??'');
                    $to_date=trim($post['to_date']??'');

                    $where=" WHERE pr.is_deleted=0 ";
                    $params=[];

                    if($supplier_id!=''){
                        $where.=" AND pr.supplier_id=:supplier_id ";
                        $params[':supplier_id']=$supplier_id;
                    }

                    if($purchase_invoice_id!=''){
                        $where.=" AND pr.purchase_invoice_id=:purchase_invoice_id ";
                        $params[':purchase_invoice_id']=$purchase_invoice_id;
                    }

                    if($status!=''){
                        $where.=" AND pr.status=:status ";
                        $params[':status']=$status;
                    }

                    if($return_no!=''){
                        $where.=" AND pr.return_no LIKE :return_no ";
                        $params[':return_no']='%'.$return_no.'%';
                    }

                    if($from_date!=''){
                        $where.=" AND pr.return_date>=:from_date ";
                        $params[':from_date']=$from_date;
                    }

                    if($to_date!=''){
                        $where.=" AND pr.return_date<=:to_date ";
                        $params[':to_date']=$to_date;
                    }

                    $total=Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_purchase_returns pr
                        {$where}
                    ",$params)->queryScalar();

                    $purchaseReturns=Yii::$app->db->createCommand("
                        SELECT
                            pr.*,
                            pi.invoice_no,
                            s.company_name
                        FROM inventory_purchase_returns pr
                        LEFT JOIN inventory_purchase_invoices pi
                            ON pi.id=pr.purchase_invoice_id
                        LEFT JOIN inventory_suppliers s
                            ON s.id=pr.supplier_id
                        {$where}
                        ORDER BY pr.id DESC
                        LIMIT {$offset},{$perPage}
                    ",$params)->queryAll();

                    return[
                        'success'=>true,
                        'purchaseReturns'=>$purchaseReturns,
                        'page'=>$page,
                        'perPage'=>$perPage,
                        'total'=>$total,
                        'totalPages'=>ceil($total/$perPage)
                    ];
                }

                if(isset($post['flag']) && $post['flag']=='save'){

                    $db=Yii::$app->db;
                    $transaction=$db->beginTransaction();

                    try{

                        $id=(int)($post['id']??0);

                        $data=[
                            'purchase_invoice_id'=>$post['purchase_invoice_id'],
                            'supplier_id'=>$post['supplier_id'],
                            'return_date'=>$post['return_date'],
                            'reason'=>$post['reason'],
                            'subtotal'=>$post['subtotal'],
                            'tax_amount'=>$post['tax_amount'],
                            'grand_total'=>$post['grand_total'],
                            'status'=>$post['status'],
                            'remarks'=>$post['remarks'],
                            'updated_at'=>date('Y-m-d H:i:s')
                        ];

                        if($id>0){

                            $db->createCommand()->update(
                                'inventory_purchase_returns',
                                $data,
                                ['id'=>$id]
                            )->execute();

                            $returnId=$id;

                        }else{

                            $data['return_no']='PRN-'.date('YmdHis').rand(100,999);
                            $data['created_at']=date('Y-m-d H:i:s');
                            $data['is_active']=1;
                            $data['is_deleted']=0;

                            $db->createCommand()->insert(
                                'inventory_purchase_returns',
                                $data
                            )->execute();

                            $returnId=$db->getLastInsertID();

                        }

                        $transaction->commit();

                        return[
                            'success'=>true,
                            'message'=>'Purchase Return saved successfully.',
                            'id'=>$returnId
                        ];

                    }catch(\Exception $e){

                        $transaction->rollBack();

                        return[
                            'success'=>false,
                            'message'=>$e->getMessage()
                        ];

                    }

                }

                if(isset($post['flag']) && $post['flag']=='delete'){

                    Yii::$app->db->createCommand()->update(
                        'inventory_purchase_returns',
                        [
                            'is_deleted'=>1,
                            'updated_at'=>date('Y-m-d H:i:s')
                        ],
                        ['id'=>$post['id']]
                    )->execute();

                    return[
                        'success'=>true,
                        'message'=>'Purchase Return deleted successfully.'
                    ];

                }

                return[
                    'success'=>false,
                    'message'=>'Invalid request.'
                ];

            }catch(\Exception $e){

                return[
                    'success'=>false,
                    'message'=>$e->getMessage()
                ];

            }

        }

        $suppliers=Yii::$app->db->createCommand("
            SELECT id,company_name
            FROM inventory_suppliers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY company_name
        ")->queryAll();

        $purchaseInvoices=Yii::$app->db->createCommand("
            SELECT id,invoice_no
            FROM inventory_purchase_invoices
            WHERE is_deleted=0
            ORDER BY invoice_no
        ")->queryAll();

        $purchaseReturns=Yii::$app->db->createCommand("
            SELECT
                pr.*,
                pi.invoice_no,
                s.company_name
            FROM inventory_purchase_returns pr
            LEFT JOIN inventory_purchase_invoices pi
                ON pi.id=pr.purchase_invoice_id
            LEFT JOIN inventory_suppliers s
                ON s.id=pr.supplier_id
            WHERE pr.is_deleted=0
            ORDER BY pr.id DESC
            LIMIT 20
        ")->queryAll();

        return $this->renderPartial('purchasereturns',[
            'purchaseReturns'=>$purchaseReturns,
            'purchaseInvoices'=>$purchaseInvoices,
            'suppliers'=>$suppliers
        ]);
    }

    
    public function actionPurchasereports()
    {
        if (Yii::$app->request->isPost) {

            $post=Yii::$app->request->post();
            Yii::$app->response->format=Response::FORMAT_JSON;

            try{

                if(isset($post['flag']) && $post['flag']=='load'){

                    $from_date=trim($post['from_date']??'');
                    $to_date=trim($post['to_date']??'');
                    $supplier_id=trim($post['supplier_id']??'');
                    $warehouse_id=trim($post['warehouse_id']??'');
                    $status=trim($post['status']??'');

                    $where=" WHERE po.is_deleted=0 ";
                    $params=[];

                    if($from_date!=''){
                        $where.=" AND po.order_date>=:from_date ";
                        $params[':from_date']=$from_date;
                    }

                    if($to_date!=''){
                        $where.=" AND po.order_date<=:to_date ";
                        $params[':to_date']=$to_date;
                    }

                    if($supplier_id!=''){
                        $where.=" AND po.supplier_id=:supplier_id ";
                        $params[':supplier_id']=$supplier_id;
                    }

                    if($warehouse_id!=''){
                        $where.=" AND po.warehouse_id=:warehouse_id ";
                        $params[':warehouse_id']=$warehouse_id;
                    }

                    if($status!=''){
                        $where.=" AND po.status=:status ";
                        $params[':status']=$status;
                    }

                    $summary=Yii::$app->db->createCommand("
                        SELECT
                            COUNT(*) total_orders,
                            IFNULL(SUM(po.grand_total),0) total_amount,
                            IFNULL(AVG(po.grand_total),0) average_amount
                        FROM inventory_purchase_orders po
                        {$where}
                    ",$params)->queryOne();

                    $statusSummary=Yii::$app->db->createCommand("
                        SELECT
                            po.status,
                            COUNT(*) total_orders,
                            IFNULL(SUM(po.grand_total),0) total_amount
                        FROM inventory_purchase_orders po
                        {$where}
                        GROUP BY po.status
                        ORDER BY total_orders DESC
                    ",$params)->queryAll();

                    $supplierSummary=Yii::$app->db->createCommand("
                        SELECT
                            s.company_name,
                            COUNT(po.id) total_orders,
                            IFNULL(SUM(po.grand_total),0) total_amount
                        FROM inventory_purchase_orders po
                        LEFT JOIN inventory_suppliers s
                            ON s.id=po.supplier_id
                        {$where}
                        GROUP BY po.supplier_id,s.company_name
                        ORDER BY total_amount DESC
                    ",$params)->queryAll();

                    $warehouseSummary=Yii::$app->db->createCommand("
                        SELECT
                            w.warehouse_name,
                            COUNT(po.id) total_orders,
                            IFNULL(SUM(po.grand_total),0) total_amount
                        FROM inventory_purchase_orders po
                        LEFT JOIN inventory_warehouses w
                            ON w.id=po.warehouse_id
                        {$where}
                        GROUP BY po.warehouse_id,w.warehouse_name
                        ORDER BY total_amount DESC
                    ",$params)->queryAll();

                    $purchaseReport=Yii::$app->db->createCommand("
                        SELECT
                            po.*,
                            s.company_name,
                            w.warehouse_name
                        FROM inventory_purchase_orders po
                        LEFT JOIN inventory_suppliers s
                            ON s.id=po.supplier_id
                        LEFT JOIN inventory_warehouses w
                            ON w.id=po.warehouse_id
                        {$where}
                        ORDER BY po.order_date DESC,po.id DESC
                    ",$params)->queryAll();

                    return[
                        'success'=>true,
                        'summary'=>$summary,
                        'statusSummary'=>$statusSummary,
                        'supplierSummary'=>$supplierSummary,
                        'warehouseSummary'=>$warehouseSummary,
                        'purchaseReport'=>$purchaseReport
                    ];
                }

                return[
                    'success'=>false,
                    'message'=>'Invalid request.'
                ];

            }catch(\Exception $e){

                return[
                    'success'=>false,
                    'message'=>$e->getMessage()
                ];

            }

        }

        $suppliers=Yii::$app->db->createCommand("
            SELECT id,company_name
            FROM inventory_suppliers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY company_name
        ")->queryAll();

        $warehouses=Yii::$app->db->createCommand("
            SELECT id,warehouse_name
            FROM inventory_warehouses
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY warehouse_name
        ")->queryAll();

        return $this->renderPartial('purchasereports',[
            'suppliers'=>$suppliers,
            'warehouses'=>$warehouses
        ]);
    }

    
    public function actionPurchaseanalytics()
    {
        if (Yii::$app->request->isPost) {

            $post=Yii::$app->request->post();
            Yii::$app->response->format=Response::FORMAT_JSON;

            try{

                if(isset($post['flag']) && $post['flag']=='load'){

                    $from_date=trim($post['from_date']??'');
                    $to_date=trim($post['to_date']??'');

                    $where=" WHERE po.is_deleted=0 ";
                    $params=[];

                    if($from_date!=''){
                        $where.=" AND po.order_date>=:from_date ";
                        $params[':from_date']=$from_date;
                    }

                    if($to_date!=''){
                        $where.=" AND po.order_date<=:to_date ";
                        $params[':to_date']=$to_date;
                    }

                    $stats=[];

                    $stats['total_purchase_orders']=(int)Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_purchase_orders po
                        {$where}
                    ",$params)->queryScalar();

                    $stats['total_purchase_amount']=(float)Yii::$app->db->createCommand("
                        SELECT IFNULL(SUM(po.grand_total),0)
                        FROM inventory_purchase_orders po
                        {$where}
                    ",$params)->queryScalar();

                    $stats['average_purchase']=(float)Yii::$app->db->createCommand("
                        SELECT IFNULL(AVG(po.grand_total),0)
                        FROM inventory_purchase_orders po
                        {$where}
                    ",$params)->queryScalar();

                    $stats['total_suppliers']=(int)Yii::$app->db->createCommand("
                        SELECT COUNT(DISTINCT po.supplier_id)
                        FROM inventory_purchase_orders po
                        {$where}
                    ",$params)->queryScalar();

                    $statusChart=Yii::$app->db->createCommand("
                        SELECT
                            po.status,
                            COUNT(*) total
                        FROM inventory_purchase_orders po
                        {$where}
                        GROUP BY po.status
                        ORDER BY total DESC",$params)->queryAll();

                    $monthlyChart=Yii::$app->db->createCommand("
                        SELECT
                            DATE_FORMAT(po.order_date,'%b %Y') month,
                            COUNT(*) total_orders,
                            IFNULL(SUM(po.grand_total),0) total_amount
                        FROM inventory_purchase_orders po
                        {$where}
                        GROUP BY YEAR(po.order_date),MONTH(po.order_date)
                        ORDER BY YEAR(po.order_date),MONTH(po.order_date)
                        ",$params)->queryAll();

                    $supplierChart=Yii::$app->db->createCommand("
                        SELECT
                            s.company_name,
                            COUNT(po.id) total_orders,
                            IFNULL(SUM(po.grand_total),0) total_amount
                        FROM inventory_purchase_orders po
                        LEFT JOIN inventory_suppliers s
                            ON s.id=po.supplier_id
                        {$where}
                        GROUP BY po.supplier_id,s.company_name
                        ORDER BY total_amount DESC
                        LIMIT 10
                        ",$params)->queryAll();

                    $warehouseChart=Yii::$app->db->createCommand("
                        SELECT
                            w.warehouse_name,
                            COUNT(po.id) total_orders,
                            IFNULL(SUM(po.grand_total),0) total_amount
                        FROM inventory_purchase_orders po
                        LEFT JOIN inventory_warehouses w
                            ON w.id=po.warehouse_id
                        {$where}
                        GROUP BY po.warehouse_id,w.warehouse_name
                        ORDER BY total_amount DESC
                        ",$params)->queryAll();

                    $topProducts=Yii::$app->db->createCommand("
                        SELECT
                            p.product_name,
                            SUM(pi.quantity) quantity,
                            SUM(pi.line_total) total_amount
                        FROM inventory_purchase_order_items pi
                        LEFT JOIN inventory_products p
                            ON p.id=pi.product_id
                        LEFT JOIN inventory_purchase_orders po
                            ON po.id=pi.purchase_order_id
                        {$where}
                        GROUP BY pi.product_id,p.product_name
                        ORDER BY quantity DESC
                        LIMIT 10
                        ",$params)->queryAll();

                    $recentPurchases=Yii::$app->db->createCommand("
                        SELECT
                            po.po_number,
                            po.order_date,
                            s.company_name,
                            po.status,
                            po.grand_total
                        FROM inventory_purchase_orders po
                        LEFT JOIN inventory_suppliers s
                            ON s.id=po.supplier_id
                        {$where}
                        ORDER BY po.order_date DESC
                        LIMIT 10
                        ",$params)->queryAll();

                    return[
                        'success'=>true,
                        'stats'=>$stats,
                        'statusChart'=>$statusChart,
                        'monthlyChart'=>$monthlyChart,
                        'supplierChart'=>$supplierChart,
                        'warehouseChart'=>$warehouseChart,
                        'topProducts'=>$topProducts,
                        'recentPurchases'=>$recentPurchases
                    ];

                }

                return[
                    'success'=>false,
                    'message'=>'Invalid request.'
                ];

            }catch(\Exception $e){

                return[
                    'success'=>false,
                    'message'=>$e->getMessage()
                ];

            }

        }

        return $this->renderPartial('purchaseanalytics');
    }
 
}

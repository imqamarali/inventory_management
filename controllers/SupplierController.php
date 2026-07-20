<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class SupplierController extends Controller
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

    public function actionSuppliers()
    {
        $modules = [
            ['name' => 'Dashboard', 'controller' => 'supplier/supplierdashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Supplier List', 'controller' => 'supplier/supplierlist', 'icon' => 'fa fa-truck'],
            ['name' => 'Supplier Ledger', 'controller' => 'supplier/supplierledger', 'icon' => 'fa fa-book'],
            ['name' => 'Supplier Payments', 'controller' => 'supplier/supplierpayments', 'icon' => 'fa fa-money'],
            ['name' => 'Purchase History', 'controller' => 'supplier/supplierpurchasehistory', 'icon' => 'fa fa-history'],
            ['name' => 'Supplier Performance', 'controller' => 'supplier/supplierperformance', 'icon' => 'fa fa-bar-chart'],
            ['name' => 'Supplier Documents', 'controller' => 'supplier/supplierdocuments', 'icon' => 'fa fa-folder-open'],
        ];

        return $this->render('suppliers', compact('modules'));
    }

    public function actionSupplierdashboard()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('supplierdashboard');
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

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

            $stats['total_suppliers'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_suppliers WHERE is_deleted=0")->queryScalar();
            $stats['active_suppliers'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_suppliers WHERE is_deleted=0 AND is_active=1")->queryScalar();
            $stats['inactive_suppliers'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_suppliers WHERE is_deleted=0 AND is_active=0")->queryScalar();

            $stats['total_contacts'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_supplier_contacts WHERE is_deleted=0")->queryScalar();

            $stats['total_payments'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_payments WHERE is_deleted=0 AND reference_type='Supplier'")->queryScalar();

            $stats['total_payment_amount'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(amount),0)
                FROM inventory_payments
                WHERE is_deleted=0
                AND reference_type='Supplier'
            ")->queryScalar();

            $stats['total_purchase_orders'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_purchase_orders
                WHERE is_deleted=0
            ")->queryScalar();

            $stats['completed_purchase_orders'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_purchase_orders
                WHERE is_deleted=0
                AND status='Completed'
            ")->queryScalar();

            $stats['pending_purchase_orders'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_purchase_orders
                WHERE is_deleted=0
                AND status='Pending'
            ")->queryScalar();

            $stats['total_purchase_amount'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(grand_total),0)
                FROM inventory_purchase_orders
                WHERE is_deleted=0
            ")->queryScalar();

            $stats['documents_uploaded'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_supplier_documents
                WHERE is_deleted=0
            ")->queryScalar();

            $supplierChart = $db->createCommand("
                SELECT
                    company_name,
                    current_balance
                FROM inventory_suppliers
                WHERE is_deleted=0
                ORDER BY current_balance DESC
                LIMIT 10
            ")->queryAll();

            $paymentMethodChart = $db->createCommand("
                SELECT
                    payment_method,
                    COUNT(*) total
                FROM inventory_payments
                WHERE is_deleted=0
                AND reference_type='Supplier'
                GROUP BY payment_method
                ORDER BY total DESC
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

            $latestPayments = $db->createCommand("
                SELECT
                    p.payment_no,
                    s.company_name,
                    p.payment_method,
                    p.amount,
                    p.payment_date
                FROM inventory_payments p
                LEFT JOIN inventory_suppliers s
                    ON s.id=p.reference_id
                WHERE p.is_deleted=0
                AND p.reference_type='Supplier'
                ORDER BY p.payment_date DESC
                LIMIT 10
            ")->queryAll();

            $recentPurchases = $db->createCommand("
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

            return [
                'success' => true,
                'stats' => $stats,
                'supplierChart' => $supplierChart,
                'paymentMethodChart' => $paymentMethodChart,
                'monthlyPurchases' => $monthlyPurchases,
                'latestPayments' => $latestPayments,
                'recentPurchases' => $recentPurchases
            ];

        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];

        }
    }

    public function actionSupplierlist()
    {
        if (Yii::$app->request->isGet) {
            $company = trim(Yii::$app->request->get('company_name', ''));
            $supplierCode = trim(Yii::$app->request->get('supplier_code', ''));
            $contact = trim(Yii::$app->request->get('contact_person', ''));
            $phone = trim(Yii::$app->request->get('phone', ''));
            $city = trim(Yii::$app->request->get('city', ''));
            $perPage = (int)Yii::$app->request->get('per_page', 50);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;
            $where = " WHERE is_deleted=0 ";
            $params = [];
            if ($company != '') {
                $where .= " AND company_name LIKE :company";
                $params[':company'] = "%{$company}%";
            }
            if ($supplierCode != '') {
                $where .= " AND supplier_code LIKE :supplier_code";
                $params[':supplier_code'] = "%{$supplierCode}%";
            }
            if ($contact != '') {
                $where .= " AND contact_person LIKE :contact";
                $params[':contact'] = "%{$contact}%";
            }
            if ($phone != '') {
                $where .= " AND (phone LIKE :phone OR mobile LIKE :phone)";
                $params[':phone'] = "%{$phone}%";
            }
            if ($city != '') {
                $where .= " AND city LIKE :city";
                $params[':city'] = "%{$city}%";
            }
            $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_suppliers $where", $params)->queryScalar();
            $suppliers = Yii::$app->db->createCommand("SELECT * FROM inventory_suppliers $where ORDER BY id DESC LIMIT $offset,$perPage", $params)->queryAll();
            return $this->renderPartial('supplierlist', [
                'suppliers'  => $suppliers,
                'total'      => $total,
                'page'       => $page,
                'perPage'    => $perPage,
                'totalPages' => ceil($total / $perPage)
            ]);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $company = trim($post['company_name'] ?? '');
                $supplierCode = trim($post['supplier_code'] ?? '');
                $contact = trim($post['contact_person'] ?? '');
                $phone = trim($post['phone'] ?? '');
                $city = trim($post['city'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;
                $where = " WHERE is_deleted=0 ";
                $params = [];
                if ($company != '') {
                    $where .= " AND company_name LIKE :company";
                    $params[':company'] = "%{$company}%";
                }
                if ($supplierCode != '') {
                    $where .= " AND supplier_code LIKE :supplier_code";
                    $params[':supplier_code'] = "%{$supplierCode}%";
                }
                if ($contact != '') {
                    $where .= " AND contact_person LIKE :contact";
                    $params[':contact'] = "%{$contact}%";
                }
                if ($phone != '') {
                    $where .= " AND (phone LIKE :phone OR mobile LIKE :phone)";
                    $params[':phone'] = "%{$phone}%";
                }
                if ($city != '') {
                    $where .= " AND city LIKE :city";
                    $params[':city'] = "%{$city}%";
                }
                $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_suppliers $where", $params)->queryScalar();
                $suppliers = Yii::$app->db->createCommand("SELECT * FROM inventory_suppliers $where ORDER BY id DESC LIMIT $offset,$perPage", $params)->queryAll();
                return [
                    'success' => true,
                    'suppliers' => $suppliers,
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ];
            }
            $supplierId = $post['id'] ?? null;
            if ($supplierId && isset($post['delete']) && $post['delete'] == 1) {
                $result = Yii::$app->db->createCommand()
                    ->update(
                        'inventory_suppliers',
                        ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => Yii::$app->user->id ?? null],
                        'id=:id',
                        [':id' => $supplierId]
                    )->execute();
                return $result
                    ? ['success' => true, 'message' => 'Supplier deleted successfully.']
                    : ['success' => false, 'message' => 'Failed to delete supplier.'];
            }

            if (empty($post['company_name'])) {
                return [
                    'success' => false,
                    'message' => 'Company name is required.'
                ];
            }
            $supplierData = [
                'supplier_code' => $post['supplier_code'] ?? null,
                'company_name' => $post['company_name'],
                'contact_person' => $post['contact_person'] ?? null,
                'email' => $post['email'] ?? null,
                'phone' => $post['phone'] ?? null,
                'mobile' => $post['mobile'] ?? null,
                'website' => $post['website'] ?? null,
                'tax_number' => $post['tax_number'] ?? null,
                'payment_terms' => $post['payment_terms'] ?? null,
                'credit_limit' => $post['credit_limit'] ?? 0,
                'opening_balance' => $post['opening_balance'] ?? 0,
                'current_balance' => $post['current_balance'] ?? 0,
                'address' => $post['address'] ?? null,
                'city' => $post['city'] ?? null,
                'province' => $post['province'] ?? null,
                'country' => $post['country'] ?? null,
                'postal_code' => $post['postal_code'] ?? null,
                'remarks' => $post['remarks'] ?? null,
                'is_active' => isset($post['is_active']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Yii::$app->user->id ?? null

            ];
            if ($supplierId) {
                $result = Yii::$app->db->createCommand()
                    ->update(
                        'inventory_suppliers',
                        $supplierData,
                        'id=:id',
                        [':id' => $supplierId]
                    )->execute();
                return $result
                    ? ['success' => true, 'message' => 'Supplier updated successfully.']
                    : ['success' => false, 'message' => 'Failed to update supplier.'];
            }
            $supplierData['created_at'] = date('Y-m-d H:i:s');
            $supplierData['created_by'] = Yii::$app->user->id ?? null;
            $supplierData['is_deleted'] = 0;
            $result = Yii::$app->db->createCommand()
                ->insert('inventory_suppliers', $supplierData)
                ->execute();
            return $result
                ? ['success' => true, 'message' => 'Supplier created successfully.']
                : ['success' => false, 'message' => 'Failed to create supplier.'];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error : ' . $e->getMessage()
            ];
        }
    }

    public function actionSupplierledger()
    {
        if (Yii::$app->request->isGet) {
            $supplierId = Yii::$app->request->get('supplier_id', '');
            $fromDate = Yii::$app->request->get('from_date', '');
            $toDate = Yii::$app->request->get('to_date', '');
            $perPage = (int)Yii::$app->request->get('per_page', 50);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;

            $where = " WHERE s.is_deleted=0 ";
            $params = [];

            if ($supplierId != '') {
                $where .= " AND s.id=:supplier_id";
                $params[':supplier_id'] = $supplierId;
            }

            if ($fromDate != '') {
                $where .= " AND DATE(p.payment_date)>=:from_date";
                $params[':from_date'] = $fromDate;
            }

            if ($toDate != '') {
                $where .= " AND DATE(p.payment_date)<=:to_date";
                $params[':to_date'] = $toDate;
            }

            $total = Yii::$app->db->createCommand("
                SELECT COUNT(*)
                FROM inventory_suppliers s
                LEFT JOIN inventory_payments p
                    ON p.reference_id=s.id
                    AND p.reference_type='Supplier'
                    AND p.is_deleted=0
                $where
            ", $params)->queryScalar();

            $ledger = Yii::$app->db->createCommand("
                SELECT
                    s.id,
                    s.supplier_code,
                    s.company_name,
                    s.contact_person,
                    s.phone,
                    s.current_balance,
                    p.payment_no,
                    p.payment_date,
                    p.payment_method,
                    p.payment_type,
                    p.amount,
                    p.remarks
                FROM inventory_suppliers s
                LEFT JOIN inventory_payments p
                    ON p.reference_id=s.id
                    AND p.reference_type='Supplier'
                    AND p.is_deleted=0
                $where
                ORDER BY p.payment_date DESC,s.company_name ASC
                LIMIT $offset,$perPage
            ", $params)->queryAll();

            $suppliers = Yii::$app->db->createCommand("
                SELECT id,company_name
                FROM inventory_suppliers
                WHERE is_deleted=0
                ORDER BY company_name
            ")->queryAll();

            return $this->renderPartial('supplierledger', [
                'ledger' => $ledger,
                'suppliers' => $suppliers,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => ceil($total / $perPage)
            ]);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {

            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {

                $supplierId = trim($post['supplier_id'] ?? '');
                $fromDate = trim($post['from_date'] ?? '');
                $toDate = trim($post['to_date'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;

                $where = " WHERE s.is_deleted=0 ";
                $params = [];

                if ($supplierId != '') {
                    $where .= " AND s.id=:supplier_id";
                    $params[':supplier_id'] = $supplierId;
                }

                if ($fromDate != '') {
                    $where .= " AND DATE(p.payment_date)>=:from_date";
                    $params[':from_date'] = $fromDate;
                }

                if ($toDate != '') {
                    $where .= " AND DATE(p.payment_date)<=:to_date";
                    $params[':to_date'] = $toDate;
                }

                $total = Yii::$app->db->createCommand("
                    SELECT COUNT(*)
                    FROM inventory_suppliers s
                    LEFT JOIN inventory_payments p
                        ON p.reference_id=s.id
                        AND p.reference_type='Supplier'
                        AND p.is_deleted=0
                    $where
                ", $params)->queryScalar();

                $ledger = Yii::$app->db->createCommand("
                    SELECT
                        s.id,
                        s.supplier_code,
                        s.company_name,
                        s.contact_person,
                        s.phone,
                        s.current_balance,
                        p.payment_no,
                        p.payment_date,
                        p.payment_method,
                        p.payment_type,
                        p.amount,
                        p.remarks
                    FROM inventory_suppliers s
                    LEFT JOIN inventory_payments p
                        ON p.reference_id=s.id
                        AND p.reference_type='Supplier'
                        AND p.is_deleted=0
                    $where
                    ORDER BY p.payment_date DESC,s.company_name ASC
                    LIMIT $offset,$perPage
                ", $params)->queryAll();

                return [
                    'success' => true,
                    'ledger' => $ledger,
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ];
            }

            return [
                'success' => true,
                'message' => 'Data loaded successfully.'
            ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => 'Error : ' . $e->getMessage()
            ];
        }
    }

    public function actionSupplierpayments()
    {
        if (Yii::$app->request->isGet) {
            $supplierId = Yii::$app->request->get('supplier_id', '');
            $paymentNo = trim(Yii::$app->request->get('payment_no', ''));
            $paymentMethod = trim(Yii::$app->request->get('payment_method', ''));
            $fromDate = Yii::$app->request->get('from_date', '');
            $toDate = Yii::$app->request->get('to_date', '');
            $perPage = (int)Yii::$app->request->get('per_page', 50);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;

            $where = " WHERE p.is_deleted=0 AND p.reference_type='Supplier' ";
            $params = [];
            if ($supplierId != '') {
                $where .= " AND p.reference_id=:supplier_id";
                $params[':supplier_id'] = $supplierId;
            }
            if ($paymentNo != '') {
                $where .= " AND p.payment_no LIKE :payment_no";
                $params[':payment_no'] = "%{$paymentNo}%";
            }
            if ($paymentMethod != '') {
                $where .= " AND p.payment_method=:payment_method";
                $params[':payment_method'] = $paymentMethod;
            }

            if ($fromDate != '') {
                $where .= " AND DATE(p.payment_date)>=:from_date";
                $params[':from_date'] = $fromDate;
            }

            if ($toDate != '') {
                $where .= " AND DATE(p.payment_date)<=:to_date";
                $params[':to_date'] = $toDate;
            }

            $total = Yii::$app->db->createCommand("
                SELECT COUNT(*)
                FROM inventory_payments p
                LEFT JOIN inventory_suppliers s
                    ON s.id=p.reference_id
                $where", $params)->queryScalar();

            $payments = Yii::$app->db->createCommand("
                SELECT
                    p.*,
                    s.company_name,
                    s.supplier_code
                FROM inventory_payments p
                LEFT JOIN inventory_suppliers s
                ON s.id=p.reference_id
                $where
                ORDER BY p.id DESC
                LIMIT $offset,$perPage
            ", $params)->queryAll();

            $suppliers = Yii::$app->db->createCommand("
                    SELECT id,company_name
                    FROM inventory_suppliers
                    WHERE is_deleted=0
                    ORDER BY company_name
                ")->queryAll();
            $accounts = Yii::$app->db->createCommand("
                    SELECT
                        id,
                        account_code,
                        account_name,
                        account_type
                    FROM inventory_accounts
                    WHERE is_deleted=0
                    AND is_active=1
                    ORDER BY account_name
                ")->queryAll();

            return $this->renderPartial('supplierpayments', [
                'payments'=>$payments,
                'suppliers'=>$suppliers,
                'accounts'=>$accounts,
                'total'=>$total,
                'page'=>$page,
                'perPage'=>$perPage,
                'totalPages'=>ceil($total/$perPage)
            ]);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {

            $post = Yii::$app->request->post();
            if (isset($post['flag']) && $post['flag'] == 'search') {
                $supplierId = trim($post['supplier_id'] ?? '');
                $paymentNo = trim($post['payment_no'] ?? '');
                $paymentMethod = trim($post['payment_method'] ?? '');
                $fromDate = trim($post['from_date'] ?? '');
                $toDate = trim($post['to_date'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;
                $where = " WHERE p.is_deleted=0 AND p.reference_type='Supplier' ";
                $params = [];
                if ($supplierId != '') {
                    $where .= " AND p.reference_id=:supplier_id";
                    $params[':supplier_id'] = $supplierId;
                }
                if ($paymentNo != '') {
                    $where .= " AND p.payment_no LIKE :payment_no";
                    $params[':payment_no'] = "%{$paymentNo}%";
                }
                if ($paymentMethod != '') {
                    $where .= " AND p.payment_method=:payment_method";
                    $params[':payment_method'] = $paymentMethod;
                }
                if ($fromDate != '') {
                    $where .= " AND DATE(p.payment_date)>=:from_date";
                    $params[':from_date'] = $fromDate;
                }
                if ($toDate != '') {
                    $where .= " AND DATE(p.payment_date)<=:to_date";
                    $params[':to_date'] = $toDate;
                }
                $total = Yii::$app->db->createCommand("
                    SELECT COUNT(*)
                    FROM inventory_payments p
                    LEFT JOIN inventory_suppliers s
                        ON s.id=p.reference_id
                    $where
                ", $params)->queryScalar();

                $payments = Yii::$app->db->createCommand("
                    SELECT
                        p.*,
                        s.company_name,
                        s.supplier_code
                    FROM inventory_payments p
                    LEFT JOIN inventory_suppliers s
                        ON s.id=p.reference_id
                    $where
                    ORDER BY p.id DESC
                    LIMIT $offset,$perPage
                ", $params)->queryAll();

                return [
                    'success' => true,
                    'payments' => $payments,
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ];
            }

            $paymentId = $post['id'] ?? null;

            if ($paymentId && isset($post['delete']) && $post['delete'] == 1) {

                $result = Yii::$app->db->createCommand()->update(
                    'inventory_payments',
                    [
                        'is_deleted' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Yii::$app->user->id ?? null
                    ],
                    'id=:id',
                    [':id' => $paymentId]
                )->execute();

                return $result
                    ? ['success' => true, 'message' => 'Payment deleted successfully.']
                    : ['success' => false, 'message' => 'Failed to delete payment.'];
            }

            if (empty($post['reference_id'])) {
                return [
                    'success' => false,
                    'message' => 'Supplier is required.'
                ];
            }

            if (empty($post['amount'])) {
                return [
                    'success' => false,
                    'message' => 'Amount is required.'
                ];
            }

            $paymentData = [
                'payment_no' => $post['payment_no'] ?? $this->generateDocNo('PAY'),
                'payment_date' => !empty($post['payment_date']) ? date('Y-m-d', strtotime($post['payment_date'])) : date('Y-m-d'),
                'payment_type' => $post['payment_type'] ?? 'Pay',
                'reference_type' => 'Supplier',
                'reference_id' => $post['reference_id'],
                'payment_method' => $post['payment_method'] ?? 'Cash',
                'account_id' => $post['account_id'] ?? null,
                'amount' => $post['amount'],
                'remarks' => $post['remarks'] ?? null,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Yii::$app->user->id ?? null
            ];

            if ($paymentId) {

                $result = Yii::$app->db->createCommand()->update(
                    'inventory_payments',
                    $paymentData,
                    'id=:id',
                    [':id' => $paymentId]
                )->execute();

                return $result
                    ? ['success' => true, 'message' => 'Payment updated successfully.']
                    : ['success' => false, 'message' => 'Failed to update payment.'];
            }

            $paymentData['created_at'] = date('Y-m-d H:i:s');
            $paymentData['created_by'] = Yii::$app->user->id ?? null;
            $paymentData['is_active'] = 1;
            $paymentData['is_deleted'] = 0;

            $result = Yii::$app->db->createCommand()
                ->insert('inventory_payments', $paymentData)
                ->execute();

            return $result
                ? ['success' => true, 'message' => 'Payment created successfully.']
                : ['success' => false, 'message' => 'Failed to create payment.'];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => 'Error : ' . $e->getMessage()
            ];
        }
    }

    public function actionSupplierpurchasehistory()
    {
        if (Yii::$app->request->isGet) {
            $supplierId = Yii::$app->request->get('supplier_id', '');
            $poNumber = trim(Yii::$app->request->get('po_number', ''));
            $status = trim(Yii::$app->request->get('status', ''));
            $fromDate = Yii::$app->request->get('from_date', '');
            $toDate = Yii::$app->request->get('to_date', '');
            $perPage = (int)Yii::$app->request->get('per_page', 50);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;

            $where = " WHERE po.is_deleted=0 ";
            $params = [];

            if ($supplierId != '') {
                $where .= " AND po.supplier_id=:supplier_id";
                $params[':supplier_id'] = $supplierId;
            }

            if ($poNumber != '') {
                $where .= " AND po.po_number LIKE :po_number";
                $params[':po_number'] = "%{$poNumber}%";
            }

            if ($status != '') {
                $where .= " AND po.status=:status";
                $params[':status'] = $status;
            }

            if ($fromDate != '') {
                $where .= " AND po.order_date>=:from_date";
                $params[':from_date'] = $fromDate;
            }

            if ($toDate != '') {
                $where .= " AND po.order_date<=:to_date";
                $params[':to_date'] = $toDate;
            }

            $total = Yii::$app->db->createCommand("
                SELECT COUNT(*)
                FROM inventory_purchase_orders po
                INNER JOIN inventory_suppliers s
                    ON s.id=po.supplier_id
                LEFT JOIN inventory_warehouses w
                    ON w.id=po.warehouse_id
                $where
            ", $params)->queryScalar();

            $purchases = Yii::$app->db->createCommand("
                SELECT
                    po.*,
                    s.company_name,
                    s.supplier_code,
                    w.warehouse_name
                FROM inventory_purchase_orders po
                INNER JOIN inventory_suppliers s
                    ON s.id=po.supplier_id
                LEFT JOIN inventory_warehouses w
                    ON w.id=po.warehouse_id
                $where
                ORDER BY po.id DESC
                LIMIT $offset,$perPage
            ", $params)->queryAll();

            $suppliers = Yii::$app->db->createCommand("
                SELECT id,company_name
                FROM inventory_suppliers
                WHERE is_deleted=0
                ORDER BY company_name
            ")->queryAll();

            return $this->renderPartial('purchasehistory', [
                'purchases' => $purchases,
                'suppliers' => $suppliers,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => ceil($total / $perPage)
            ]);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {

            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {

                $supplierId = trim($post['supplier_id'] ?? '');
                $poNumber = trim($post['po_number'] ?? '');
                $status = trim($post['status'] ?? '');
                $fromDate = trim($post['from_date'] ?? '');
                $toDate = trim($post['to_date'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;

                $where = " WHERE po.is_deleted=0 ";
                $params = [];

                if ($supplierId != '') {
                    $where .= " AND po.supplier_id=:supplier_id";
                    $params[':supplier_id'] = $supplierId;
                }

                if ($poNumber != '') {
                    $where .= " AND po.po_number LIKE :po_number";
                    $params[':po_number'] = "%{$poNumber}%";
                }

                if ($status != '') {
                    $where .= " AND po.status=:status";
                    $params[':status'] = $status;
                }

                if ($fromDate != '') {
                    $where .= " AND po.order_date>=:from_date";
                    $params[':from_date'] = $fromDate;
                }

                if ($toDate != '') {
                    $where .= " AND po.order_date<=:to_date";
                    $params[':to_date'] = $toDate;
                }

                $total = Yii::$app->db->createCommand("
                    SELECT COUNT(*)
                    FROM inventory_purchase_orders po
                    INNER JOIN inventory_suppliers s
                        ON s.id=po.supplier_id
                    LEFT JOIN inventory_warehouses w
                        ON w.id=po.warehouse_id
                    $where
                ", $params)->queryScalar();

                $purchases = Yii::$app->db->createCommand("
                    SELECT
                        po.*,
                        s.company_name,
                        s.supplier_code,
                        w.warehouse_name
                    FROM inventory_purchase_orders po
                    INNER JOIN inventory_suppliers s
                        ON s.id=po.supplier_id
                    LEFT JOIN inventory_warehouses w
                        ON w.id=po.warehouse_id
                    $where
                    ORDER BY po.id DESC
                    LIMIT $offset,$perPage
                ", $params)->queryAll();

                return [
                    'success' => true,
                    'purchases' => $purchases,
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ];
            }

            return [
                'success' => true,
                'message' => 'Purchase history loaded successfully.'
            ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => 'Error : ' . $e->getMessage()
            ];
        }
    }

    public function actionSupplierperformance()
    {
        if (Yii::$app->request->isGet) {
            $supplierId = Yii::$app->request->get('supplier_id', '');
            $fromDate = Yii::$app->request->get('from_date', '');
            $toDate = Yii::$app->request->get('to_date', '');
            $perPage = (int)Yii::$app->request->get('per_page', 50);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;

            $where = " WHERE s.is_deleted=0 ";
            $params = [];

            if ($supplierId != '') {
                $where .= " AND s.id=:supplier_id";
                $params[':supplier_id'] = $supplierId;
            }

            if ($fromDate != '') {
                $where .= " AND po.order_date>=:from_date";
                $params[':from_date'] = $fromDate;
            }

            if ($toDate != '') {
                $where .= " AND po.order_date<=:to_date";
                $params[':to_date'] = $toDate;
            }

            $total = Yii::$app->db->createCommand("
                SELECT COUNT(DISTINCT s.id)
                FROM inventory_suppliers s
                LEFT JOIN inventory_purchase_orders po
                    ON po.supplier_id=s.id
                    AND po.is_deleted=0
                $where
            ", $params)->queryScalar();

            $performance = Yii::$app->db->createCommand("
                SELECT
                    s.id,
                    s.supplier_code,
                    s.company_name,
                    s.contact_person,
                    s.phone,
                    COUNT(DISTINCT po.id) AS total_orders,
                    IFNULL(SUM(po.grand_total),0) AS total_purchase_amount,
                    IFNULL(AVG(po.grand_total),0) AS average_order_value,
                    MAX(po.order_date) AS last_purchase_date,
                    SUM(CASE WHEN po.status='Completed' THEN 1 ELSE 0 END) AS completed_orders,
                    SUM(CASE WHEN po.status='Cancelled' THEN 1 ELSE 0 END) AS cancelled_orders,
                    SUM(CASE WHEN po.status='Pending' THEN 1 ELSE 0 END) AS pending_orders
                FROM inventory_suppliers s
                LEFT JOIN inventory_purchase_orders po
                    ON po.supplier_id=s.id
                    AND po.is_deleted=0
                $where
                GROUP BY s.id
                ORDER BY total_purchase_amount DESC
                LIMIT $offset,$perPage
            ", $params)->queryAll();

            $suppliers = Yii::$app->db->createCommand("
                SELECT id,company_name
                FROM inventory_suppliers
                WHERE is_deleted=0
                ORDER BY company_name
            ")->queryAll();

            return $this->renderPartial('supplierperformance', [
                'performance' => $performance,
                'suppliers' => $suppliers,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => ceil($total / $perPage)
            ]);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {

            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {

                $supplierId = trim($post['supplier_id'] ?? '');
                $fromDate = trim($post['from_date'] ?? '');
                $toDate = trim($post['to_date'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;

                $where = " WHERE s.is_deleted=0 ";
                $params = [];

                if ($supplierId != '') {
                    $where .= " AND s.id=:supplier_id";
                    $params[':supplier_id'] = $supplierId;
                }

                if ($fromDate != '') {
                    $where .= " AND po.order_date>=:from_date";
                    $params[':from_date'] = $fromDate;
                }

                if ($toDate != '') {
                    $where .= " AND po.order_date<=:to_date";
                    $params[':to_date'] = $toDate;
                }

                $total = Yii::$app->db->createCommand("
                    SELECT COUNT(DISTINCT s.id)
                    FROM inventory_suppliers s
                    LEFT JOIN inventory_purchase_orders po
                        ON po.supplier_id=s.id
                        AND po.is_deleted=0
                    $where
                ", $params)->queryScalar();

                $performance = Yii::$app->db->createCommand("
                    SELECT
                        s.id,
                        s.supplier_code,
                        s.company_name,
                        s.contact_person,
                        s.phone,
                        COUNT(DISTINCT po.id) AS total_orders,
                        IFNULL(SUM(po.grand_total),0) AS total_purchase_amount,
                        IFNULL(AVG(po.grand_total),0) AS average_order_value,
                        MAX(po.order_date) AS last_purchase_date,
                        SUM(CASE WHEN po.status='Completed' THEN 1 ELSE 0 END) AS completed_orders,
                        SUM(CASE WHEN po.status='Cancelled' THEN 1 ELSE 0 END) AS cancelled_orders,
                        SUM(CASE WHEN po.status='Pending' THEN 1 ELSE 0 END) AS pending_orders
                    FROM inventory_suppliers s
                    LEFT JOIN inventory_purchase_orders po
                        ON po.supplier_id=s.id
                        AND po.is_deleted=0
                    $where
                    GROUP BY s.id
                    ORDER BY total_purchase_amount DESC
                    LIMIT $offset,$perPage
                ", $params)->queryAll();

                return [
                    'success' => true,
                    'performance' => $performance,
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ];
            }

            return [
                'success' => true,
                'message' => 'Supplier performance loaded successfully.'
            ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => 'Error : ' . $e->getMessage()
            ];
        }
    }

    public function actionSupplierdocuments()
    {
        if (Yii::$app->request->isGet) {
            $supplierId = Yii::$app->request->get('supplier_id', '');
            $documentType = trim(Yii::$app->request->get('document_type', ''));
            $documentName = trim(Yii::$app->request->get('document_name', ''));
            $perPage = (int)Yii::$app->request->get('per_page', 50);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;

            $where = " WHERE d.is_deleted=0 ";
            $params = [];

            if ($supplierId != '') {
                $where .= " AND d.supplier_id=:supplier_id";
                $params[':supplier_id'] = $supplierId;
            }

            if ($documentType != '') {
                $where .= " AND d.document_type LIKE :document_type";
                $params[':document_type'] = "%{$documentType}%";
            }

            if ($documentName != '') {
                $where .= " AND d.document_name LIKE :document_name";
                $params[':document_name'] = "%{$documentName}%";
            }

            $total = Yii::$app->db->createCommand("
            SELECT COUNT(*)
            FROM inventory_supplier_documents d
            INNER JOIN inventory_suppliers s
                ON s.id=d.supplier_id
            $where
        ", $params)->queryScalar();

            $documents = Yii::$app->db->createCommand("
            SELECT
                d.*,
                s.company_name,
                s.supplier_code
            FROM inventory_supplier_documents d
            INNER JOIN inventory_suppliers s
                ON s.id=d.supplier_id
            $where
            ORDER BY d.id DESC
            LIMIT $offset,$perPage
        ", $params)->queryAll();

            $suppliers = Yii::$app->db->createCommand("
            SELECT id,company_name
            FROM inventory_suppliers
            WHERE is_deleted=0
            ORDER BY company_name
        ")->queryAll();

            return $this->renderPartial('supplierdocuments', [
                'documents' => $documents,
                'suppliers' => $suppliers,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => ceil($total / $perPage)
            ]);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {

            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {

                $supplierId = trim($post['supplier_id'] ?? '');
                $documentType = trim($post['document_type'] ?? '');
                $documentName = trim($post['document_name'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;

                $where = " WHERE d.is_deleted=0 ";
                $params = [];

                if ($supplierId != '') {
                    $where .= " AND d.supplier_id=:supplier_id";
                    $params[':supplier_id'] = $supplierId;
                }

                if ($documentType != '') {
                    $where .= " AND d.document_type LIKE :document_type";
                    $params[':document_type'] = "%{$documentType}%";
                }

                if ($documentName != '') {
                    $where .= " AND d.document_name LIKE :document_name";
                    $params[':document_name'] = "%{$documentName}%";
                }

                $total = Yii::$app->db->createCommand("
                SELECT COUNT(*)
                FROM inventory_supplier_documents d
                INNER JOIN inventory_suppliers s
                    ON s.id=d.supplier_id
                $where
            ", $params)->queryScalar();

                $documents = Yii::$app->db->createCommand("
                SELECT
                    d.*,
                    s.company_name,
                    s.supplier_code
                FROM inventory_supplier_documents d
                INNER JOIN inventory_suppliers s
                    ON s.id=d.supplier_id
                $where
                ORDER BY d.id DESC
                LIMIT $offset,$perPage
            ", $params)->queryAll();

                return [
                    'success' => true,
                    'documents' => $documents,
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ];
            }

            $documentId = $post['id'] ?? null;

            if ($documentId && isset($post['delete']) && $post['delete'] == 1) {

                $result = Yii::$app->db->createCommand()->update(
                    'inventory_supplier_documents',
                    [
                        'is_deleted' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Yii::$app->user->id ?? null
                    ],
                    'id=:id',
                    [':id' => $documentId]
                )->execute();

                return $result
                    ? ['success' => true, 'message' => 'Document deleted successfully.']
                    : ['success' => false, 'message' => 'Failed to delete document.'];
            }

            if (empty($post['supplier_id'])) {
                return [
                    'success' => false,
                    'message' => 'Supplier is required.'
                ];
            }

            if (empty($post['document_name'])) {
                return [
                    'success' => false,
                    'message' => 'Document name is required.'
                ];
            }

            $documentData = [
                'supplier_id' => $post['supplier_id'],
                'document_type' => $post['document_type'] ?? null,
                'document_name' => $post['document_name'],
                'document_file' => $post['document_file'] ?? null,
                'remarks' => $post['remarks'] ?? null,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Yii::$app->user->id ?? null
            ];

            if ($documentId) {

                $result = Yii::$app->db->createCommand()->update(
                    'inventory_supplier_documents',
                    $documentData,
                    'id=:id',
                    [':id' => $documentId]
                )->execute();

                return $result
                    ? ['success' => true, 'message' => 'Document updated successfully.']
                    : ['success' => false, 'message' => 'Failed to update document.'];
            }

            $documentData['created_at'] = date('Y-m-d H:i:s');
            $documentData['created_by'] = Yii::$app->user->id ?? null;
            $documentData['is_active'] = 1;
            $documentData['is_deleted'] = 0;

            $result = Yii::$app->db->createCommand()
                ->insert('inventory_supplier_documents', $documentData)
                ->execute();

            return $result
                ? ['success' => true, 'message' => 'Document uploaded successfully.']
                : ['success' => false, 'message' => 'Failed to upload document.'];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => 'Error : ' . $e->getMessage()
            ];
        }
    }
}
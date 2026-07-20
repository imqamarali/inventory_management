<?php
namespace app\controllers;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class CustomersController extends Controller
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

    public function behaviors() {
        return [
            'access' => ['class' => AccessControl::class, 'rules' => [['allow' => true, 'roles' => ['@']]]],
            'verbs' => ['class' => VerbFilter::class, 'actions' => ['logout' => ['post']]],
        ];
    }

    public function actions() {
        return [
            'error' => ['class' => 'yii\web\ErrorAction'],
            'captcha' => ['class' => 'yii\captcha\CaptchaAction', 'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null],
        ];
    }

    public function beforeAction($action) {
        if (Yii::$app->session->has('user_array') == NULL) {
            $this->redirect(['site/index']);
            return false;
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionCustomers() {
        $modules = [
            ['name' => 'Dashboard', 'controller' => 'customers/customerdashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Customer List', 'controller' => 'customers/customerlist', 'icon' => 'fa fa-users'],
            ['name' => 'Add Customer', 'controller' => 'customers/addcustomer', 'icon' => 'fa fa-user-plus'],
            ['name' => 'Retail Customers', 'controller' => 'customers/retailcustomers', 'icon' => 'fa fa-shopping-cart'],
            ['name' => 'Workshop Customers', 'controller' => 'customers/workshopcustomers', 'icon' => 'fa fa-wrench'],
            ['name' => 'Fleet Customers', 'controller' => 'customers/fleetcustomers', 'icon' => 'fa fa-bus'],
            ['name' => 'Dealer Customers', 'controller' => 'customers/dealercustomers', 'icon' => 'fa fa-building'],
            ['name' => 'Customer Ledger', 'controller' => 'customers/customerledger', 'icon' => 'fa fa-book'],
            ['name' => 'Customer Payments', 'controller' => 'customers/customerpayments', 'icon' => 'fa fa-money'],
            ['name' => 'Sales History', 'controller' => 'customers/saleshistory', 'icon' => 'fa fa-history'],
            ['name' => 'Customer Returns', 'controller' => 'customers/customerreturns', 'icon' => 'fa fa-reply'],
            ['name' => 'Outstanding Balance', 'controller' => 'customers/customerbalance', 'icon' => 'fa fa-credit-card'],
        ];
        return $this->render('customers', compact('modules'));
    }

    public function actionCustomerdashboard() {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('customerdashboard');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            if (!isset($post['flag']) || $post['flag'] != 'load_dashboard') {
                return $this->jsonResponse(false, 'Invalid request.');
            }
            $db = Yii::$app->db;
            $stats = [
                'total_customers' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_customers WHERE is_deleted=0")->queryScalar(),
                'active_customers' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_customers WHERE is_deleted=0 AND is_active=1")->queryScalar(),
                'retail_customers' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_customers WHERE is_deleted=0 AND customer_type='Retail'")->queryScalar(),
                'company_customers' => (int)$db->createCommand("SELECT COUNT(*) FROM inventory_customers WHERE is_deleted=0 AND customer_type='Company'")->queryScalar(),
                'total_receivable' => (float)$db->createCommand("SELECT IFNULL(SUM(current_balance),0) FROM inventory_customers WHERE is_deleted=0 AND current_balance>0")->queryScalar(),
                'total_credit_limit' => (float)$db->createCommand("SELECT IFNULL(SUM(credit_limit),0) FROM inventory_customers WHERE is_deleted=0")->queryScalar(),
            ];
            $recentCustomers = $db->createCommand("SELECT id, customer_code, company_name, first_name, last_name, customer_type, email, created_at FROM inventory_customers WHERE is_deleted=0 ORDER BY created_at DESC LIMIT 10")->queryAll();
            return array_merge($this->jsonResponse(true, 'Dashboard loaded.'), ['stats' => $stats, 'recentCustomers' => $recentCustomers]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function actionCustomerlist() {
        if (Yii::$app->request->isGet) {
            $customers = [];
            return $this->renderPartial('customerlist', ['customers' => $customers]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'load') {
                $company_name = trim($post['company_name'] ?? '');
                $email = trim($post['email'] ?? '');
                $phone = trim($post['phone'] ?? '');
                $customer_type = trim($post['customer_type'] ?? '');
                $is_active = $post['is_active'] !== '' ? (int)$post['is_active'] : '';
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 20;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;

                $where = " WHERE c.is_deleted=0 ";
                $params = [];
                if ($company_name != '') {
                    $where .= " AND (c.company_name LIKE :company_name OR c.first_name LIKE :company_name OR c.last_name LIKE :company_name)";
                    $params[':company_name'] = "%$company_name%";
                }
                if ($email != '') {
                    $where .= " AND c.email LIKE :email";
                    $params[':email'] = "%$email%";
                }
                if ($phone != '') {
                    $where .= " AND (c.phone LIKE :phone OR c.mobile LIKE :phone)";
                    $params[':phone'] = "%$phone%";
                }
                if ($customer_type != '') {
                    $where .= " AND c.customer_type=:customer_type";
                    $params[':customer_type'] = $customer_type;
                }
                if ($is_active !== '') {
                    $where .= " AND c.is_active=:is_active";
                    $params[':is_active'] = $is_active;
                }

                $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_customers c $where", $params)->queryScalar();
                $customers = Yii::$app->db->createCommand("
                    SELECT c.* FROM inventory_customers c
                    $where
                    ORDER BY c.company_name
                    LIMIT $offset,$perPage
                ", $params)->queryAll();

                return array_merge($this->jsonResponse(true, 'Customers loaded.'), [
                    'customers' => $customers,
                    'page' => $page,
                    'totalPages' => ceil($total / $perPage)
                ]);
            }

            $data = $post;
            $id = $data['id'] ?? null;

            if ($id && isset($data['delete'])) {
                Yii::$app->db->createCommand()->update('inventory_customers',
                    ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                    ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Customer deleted successfully.');
            }

            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function actionAddcustomer() {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('addcustomer');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $data = Yii::$app->request->post();
            $id = $data['id'] ?? null;

            if (empty($data['company_name']) && empty($data['first_name'])) {
                return $this->jsonResponse(false, 'Customer name is required.');
            }

            $trans = Yii::$app->db->beginTransaction();
            try {
                $custData = [
                    'customer_type' => $data['customer_type'] ?? 'Individual',
                    'company_name' => $data['company_name'] ?? null,
                    'first_name' => $data['first_name'] ?? null,
                    'last_name' => $data['last_name'] ?? null,
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'mobile' => $data['mobile'] ?? null,
                    'tax_number' => $data['tax_number'] ?? null,
                    'credit_limit' => (float)($data['credit_limit'] ?? 0),
                    'opening_balance' => (float)($data['opening_balance'] ?? 0),
                    'current_balance' => (float)($data['opening_balance'] ?? 0),
                    'payment_terms' => (int)($data['payment_terms'] ?? 0),
                    'address' => $data['address'] ?? null,
                    'city' => $data['city'] ?? null,
                    'province' => $data['province'] ?? null,
                    'country' => $data['country'] ?? null,
                    'postal_code' => $data['postal_code'] ?? null,
                    'remarks' => $data['remarks'] ?? null,
                ];

                if ($id) {
                    $custData['updated_at'] = date('Y-m-d H:i:s');
                    $custData['updated_by'] = $this->currentUserId();
                    Yii::$app->db->createCommand()->update('inventory_customers', $custData, ['id' => $id])->execute();
                } else {
                    $custData['customer_code'] = $this->generateDocNo('CUST');
                    $custData['created_by'] = $this->currentUserId();
                    $custData['is_deleted'] = 0;
                    Yii::$app->db->createCommand()->insert('inventory_customers', $custData)->execute();
                    $id = Yii::$app->db->getLastInsertID();
                }

                if (!empty($data['contacts']) && is_array($data['contacts'])) {
                    foreach ($data['contacts'] as $contact) {
                        if (!empty($contact['name'])) {
                            Yii::$app->db->createCommand()->insert('inventory_customer_contacts', [
                                'customer_id' => $id,
                                'contact_name' => $contact['name'],
                                'designation' => $contact['designation'] ?? null,
                                'email' => $contact['email'] ?? null,
                                'phone' => $contact['phone'] ?? null,
                                'mobile' => $contact['mobile'] ?? null,
                                'notes' => $contact['notes'] ?? null,
                                'created_by' => $this->currentUserId(),
                                'is_deleted' => 0,
                            ])->execute();
                        }
                    }
                }

                $trans->commit();
                return $this->jsonResponse(true, $id ? 'Customer updated successfully.' : 'Customer created successfully.', ['id' => $id]);
            } catch (\Exception $inner) {
                $trans->rollBack();
                return $this->jsonResponse(false, $inner->getMessage());
            }
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function actionRetailcustomers() {
        return $this->actionCustomersByType('Individual');
    }

    public function actionWorkshopcustomers() {
        return $this->actionCustomersByType('Workshop');
    }

    public function actionFleetcustomers() {
        return $this->actionCustomersByType('Fleet');
    }

    public function actionDealercustomers() {
        return $this->actionCustomersByType('Dealer');
    }

    private function actionCustomersByType($type) {
        if (Yii::$app->request->isGet) {
            $customers = [];
            return $this->renderPartial('typecustomers', ['customers' => $customers, 'type' => $type]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'load') {
                $customer_type = trim($post['type'] ?? $type);
                $company_name = trim($post['company_name'] ?? '');
                $email = trim($post['email'] ?? '');
                $phone = trim($post['phone'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 20;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;

                $where = " WHERE c.is_deleted=0 AND c.customer_type=:type ";
                $params = [':type' => $customer_type];
                if ($company_name != '') {
                    $where .= " AND (c.company_name LIKE :company_name OR c.first_name LIKE :company_name OR c.last_name LIKE :company_name)";
                    $params[':company_name'] = "%$company_name%";
                }
                if ($email != '') {
                    $where .= " AND c.email LIKE :email";
                    $params[':email'] = "%$email%";
                }
                if ($phone != '') {
                    $where .= " AND (c.phone LIKE :phone OR c.mobile LIKE :phone)";
                    $params[':phone'] = "%$phone%";
                }

                $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_customers c $where", $params)->queryScalar();
                $customers = Yii::$app->db->createCommand("
                    SELECT c.* FROM inventory_customers c
                    $where
                    ORDER BY c.company_name
                    LIMIT $offset,$perPage
                ", $params)->queryAll();

                return array_merge($this->jsonResponse(true, 'Customers loaded.'), [
                    'customers' => $customers,
                    'page' => $page,
                    'totalPages' => ceil($total / $perPage)
                ]);
            }

            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function actionCustomerledger() {
        if (Yii::$app->request->isGet) {
            $customers = Yii::$app->db->createCommand("SELECT id, COALESCE(company_name, CONCAT(first_name,' ',last_name)) name FROM inventory_customers WHERE is_deleted=0 ORDER BY name")->queryAll();
            $ledger = [];
            return $this->renderPartial('customerledger', compact('customers', 'ledger'));
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'load') {
                $customer_id = trim($post['customer_id'] ?? '');
                $from_date = trim($post['from_date'] ?? '');
                $to_date = trim($post['to_date'] ?? '');

                if (empty($customer_id)) {
                    return $this->jsonResponse(false, 'Customer is required.');
                }

                $where = " WHERE s.customer_id=:customer_id AND s.is_deleted=0 ";
                $params = [':customer_id' => $customer_id];
                if ($from_date != '') {
                    $where .= " AND s.order_date >= :from_date";
                    $params[':from_date'] = $from_date;
                }
                if ($to_date != '') {
                    $where .= " AND s.order_date <= :to_date";
                    $params[':to_date'] = $to_date;
                }

                $ledger = Yii::$app->db->createCommand("
                    SELECT
                        DATE(s.order_date) date,
                        s.order_number reference,
                        'Sale' type,
                        COALESCE(SUM(i.quantity*i.unit_price),0) amount
                    FROM inventory_sales_orders s
                    LEFT JOIN inventory_sales_order_items i ON i.sales_order_id=s.id
                    $where
                    GROUP BY s.id
                    ORDER BY s.order_date DESC
                ", $params)->queryAll();

                return array_merge($this->jsonResponse(true, 'Ledger loaded.'), ['ledger' => $ledger]);
            }

            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function actionCustomerpayments() {
        if (Yii::$app->request->isGet) {
            $customers = Yii::$app->db->createCommand("SELECT id, COALESCE(company_name, CONCAT(first_name,' ',last_name)) name FROM inventory_customers WHERE is_deleted=0 ORDER BY name")->queryAll();
            $payments = [];
            return $this->renderPartial('customerpayments', compact('customers', 'payments'));
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'load') {
                $customer_id = trim($post['customer_id'] ?? '');
                $from_date = trim($post['from_date'] ?? '');
                $to_date = trim($post['to_date'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 20;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;

                $where = " WHERE p.is_deleted=0 AND p.reference_type='Customer' ";
                $params = [];
                if ($customer_id != '') {
                    $where .= " AND p.reference_id=:customer_id";
                    $params[':customer_id'] = $customer_id;
                }
                if ($from_date != '') {
                    $where .= " AND p.payment_date >= :from_date";
                    $params[':from_date'] = $from_date;
                }
                if ($to_date != '') {
                    $where .= " AND p.payment_date <= :to_date";
                    $params[':to_date'] = $to_date;
                }

                $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_payments p $where", $params)->queryScalar();
                $payments = Yii::$app->db->createCommand("
                    SELECT p.*, COALESCE(c.company_name, CONCAT(c.first_name,' ',c.last_name)) customer_name
                    FROM inventory_payments p
                    LEFT JOIN inventory_customers c ON c.id=p.reference_id
                    $where
                    ORDER BY p.payment_date DESC
                    LIMIT $offset,$perPage
                ", $params)->queryAll();

                return array_merge($this->jsonResponse(true, 'Payments loaded.'), [
                    'payments' => $payments,
                    'page' => $page,
                    'totalPages' => ceil($total / $perPage)
                ]);
            }

            if (isset($post['flag']) && $post['flag'] == 'delete') {
                $id = $post['id'] ?? null;
                if ($id) {
                    Yii::$app->db->createCommand()->update('inventory_payments',
                        ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                        ['id' => $id])->execute();
                    return $this->jsonResponse(true, 'Payment deleted successfully.');
                }
                return $this->jsonResponse(false, 'Payment ID is required.');
            }

            if (empty($post['customer_id']) || empty($post['amount'])) {
                return $this->jsonResponse(false, 'Customer and amount are required.');
            }
            Yii::$app->db->createCommand()->insert('inventory_payments', [
                'payment_no' => $this->generateDocNo('PAY'),
                'payment_date' => $post['payment_date'] ?? date('Y-m-d'),
                'payment_type' => 'Receive',
                'reference_type' => 'Customer',
                'reference_id' => (int)$post['customer_id'],
                'payment_method' => $post['payment_method'] ?? 'Cash',
                'amount' => (float)$post['amount'],
                'remarks' => $post['remarks'] ?? null,
                'created_by' => $this->currentUserId(),
                'is_active' => 1,
                'is_deleted' => 0,
            ])->execute();
            Yii::$app->db->createCommand()->update('inventory_customers',
                ['current_balance' => new \yii\db\Expression('current_balance-'.(float)$post['amount'])],
                ['id' => (int)$post['customer_id']])->execute();
            return $this->jsonResponse(true, 'Payment recorded successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function actionSaleshistory() {
        if (Yii::$app->request->isGet) {
            $customers = Yii::$app->db->createCommand("SELECT id, COALESCE(company_name, CONCAT(first_name,' ',last_name)) name FROM inventory_customers WHERE is_deleted=0 ORDER BY name")->queryAll();
            $sales = [];
            return $this->renderPartial('saleshistory', compact('customers', 'sales'));
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'load') {
                $customer_id = trim($post['customer_id'] ?? '');
                $from_date = trim($post['from_date'] ?? '');
                $to_date = trim($post['to_date'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 20;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;

                if (empty($customer_id)) {
                    return $this->jsonResponse(false, 'Customer is required.');
                }

                $where = " WHERE s.customer_id=:customer_id AND s.is_deleted=0 ";
                $params = [':customer_id' => $customer_id];
                if ($from_date != '') {
                    $where .= " AND s.order_date >= :from_date";
                    $params[':from_date'] = $from_date;
                }
                if ($to_date != '') {
                    $where .= " AND s.order_date <= :to_date";
                    $params[':to_date'] = $to_date;
                }

                $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_sales_orders s $where", $params)->queryScalar();
                $sales = Yii::$app->db->createCommand("
                    SELECT s.*,
                        COALESCE(c.company_name, CONCAT(c.first_name,' ',c.last_name)) customer_name,
                        COALESCE(SUM(i.quantity),0) items_count
                    FROM inventory_sales_orders s
                    LEFT JOIN inventory_customers c ON c.id=s.customer_id
                    LEFT JOIN inventory_sales_order_items i ON i.sales_order_id=s.id
                    $where
                    GROUP BY s.id
                    ORDER BY s.order_date DESC
                    LIMIT $offset,$perPage
                ", $params)->queryAll();

                return array_merge($this->jsonResponse(true, 'Sales loaded.'), [
                    'sales' => $sales,
                    'page' => $page,
                    'totalPages' => ceil($total / $perPage)
                ]);
            }

            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Customer Returns
     * ----------------------------------------------------------- */
    public function actionCustomerreturns() {
        if (Yii::$app->request->isGet) {
            $customer_id = Yii::$app->request->get('customer_id', '');
            $status = Yii::$app->request->get('status', '');
            $perPage = (int)Yii::$app->request->get('per_page', 20);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;

            $where = " WHERE r.is_deleted=0 ";
            $params = [];
            if ($customer_id != '') {
                $where .= " AND r.customer_id=:customer_id";
                $params[':customer_id'] = $customer_id;
            }
            if ($status != '') {
                $where .= " AND r.status=:status";
                $params[':status'] = $status;
            }

            $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_sales_returns r $where", $params)->queryScalar();
            $returns = Yii::$app->db->createCommand("
                SELECT r.*, so.order_number, COALESCE(c.company_name, CONCAT(c.first_name,' ',c.last_name)) customer_name
                FROM inventory_sales_returns r
                LEFT JOIN inventory_sales_orders so ON so.id=r.sales_order_id
                LEFT JOIN inventory_customers c ON c.id=r.customer_id
                $where
                ORDER BY r.id DESC
                LIMIT $offset,$perPage
            ", $params)->queryAll();

            $customers = Yii::$app->db->createCommand("SELECT id, COALESCE(company_name, CONCAT(first_name,' ',last_name)) name FROM inventory_customers WHERE is_deleted=0 ORDER BY name")->queryAll();
            $salesOrders = Yii::$app->db->createCommand("SELECT id, order_number, customer_id FROM inventory_sales_orders WHERE is_deleted=0 ORDER BY order_number DESC LIMIT 200")->queryAll();

            return $this->renderPartial('customerreturns', [
                'returns' => $returns, 'customers' => $customers, 'salesOrders' => $salesOrders,
                'total' => $total, 'page' => $page, 'perPage' => $perPage, 'totalPages' => ceil($total / $perPage)
            ]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {
                $customer_id = trim($post['customer_id'] ?? '');
                $status = trim($post['status'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset = ($page - 1) * $perPage;

                $where = " WHERE r.is_deleted=0 ";
                $params = [];
                if ($customer_id != '') {
                    $where .= " AND r.customer_id=:customer_id";
                    $params[':customer_id'] = $customer_id;
                }
                if ($status != '') {
                    $where .= " AND r.status=:status";
                    $params[':status'] = $status;
                }

                $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_sales_returns r $where", $params)->queryScalar();
                $returns = Yii::$app->db->createCommand("
                    SELECT r.*, so.order_number, COALESCE(c.company_name, CONCAT(c.first_name,' ',c.last_name)) customer_name
                    FROM inventory_sales_returns r
                    LEFT JOIN inventory_sales_orders so ON so.id=r.sales_order_id
                    LEFT JOIN inventory_customers c ON c.id=r.customer_id
                    $where ORDER BY r.id DESC LIMIT $offset,$perPage
                ", $params)->queryAll();

                return $this->jsonResponse(true, 'Data loaded successfully!', ['returns' => $returns, 'total' => (int)$total, 'page' => $page, 'per_page' => $perPage, 'total_pages' => ceil($total / $perPage)]);
            }

            $id = $post['id'] ?? null;

            if ($id && isset($post['delete']) && $post['delete'] == 1) {
                Yii::$app->db->createCommand()->update('inventory_sales_returns',
                    ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                    ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Return deleted successfully.');
            }

            if ($id && isset($post['flag']) && $post['flag'] == 'approve') {
                Yii::$app->db->createCommand()->update('inventory_sales_returns',
                    ['status' => 'Completed', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                    ['id' => $id])->execute();
                Yii::$app->db->createCommand()->update('inventory_customers',
                    ['current_balance' => new \yii\db\Expression('current_balance-' . (float)$post['grand_total'])],
                    ['id' => $post['customer_id']])->execute();
                return $this->jsonResponse(true, 'Return approved successfully.');
            }

            if (empty($post['customer_id'])) {
                return $this->jsonResponse(false, 'Customer is required.');
            }

            $subtotal = (float)($post['subtotal'] ?? 0);
            $tax = (float)($post['tax_amount'] ?? 0);
            $grandTotal = $subtotal + $tax;

            $data = [
                'sales_order_id' => $post['sales_order_id'] ?? null,
                'customer_id' => $post['customer_id'],
                'return_date' => $post['return_date'] ?? date('Y-m-d'),
                'reason' => $post['reason'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'grand_total' => $grandTotal,
                'status' => $post['status'] ?? 'Pending',
                'remarks' => $post['remarks'] ?? null,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->currentUserId(),
            ];

            if ($id) {
                Yii::$app->db->createCommand()->update('inventory_sales_returns', $data, ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Return updated successfully.');
            }

            $data['return_no'] = $this->generateDocNo('SRN');
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = $this->currentUserId();
            $data['is_active'] = 1;
            $data['is_deleted'] = 0;
            Yii::$app->db->createCommand()->insert('inventory_sales_returns', $data)->execute();
            return $this->jsonResponse(true, 'Return created successfully.', ['id' => Yii::$app->db->getLastInsertID()]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function actionCustomerbalance() {
        if (Yii::$app->request->isGet) {
            $customers = [];
            return $this->renderPartial('customerbalance', compact('customers'));
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'load') {
                $customer_name = trim($post['customer_name'] ?? '');
                $status_filter = trim($post['status_filter'] ?? '');
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 50;

                $where = " WHERE c.is_deleted=0 ";
                $params = [];

                if ($customer_name != '') {
                    $where .= " AND (c.customer_code LIKE :customer_name OR c.company_name LIKE :customer_name OR c.first_name LIKE :customer_name OR c.last_name LIKE :customer_name)";
                    $params[':customer_name'] = "%$customer_name%";
                }

                if ($status_filter == 'overdue') {
                    $where .= " AND c.current_balance > c.credit_limit";
                } elseif ($status_filter == 'due') {
                    $where .= " AND c.current_balance > 0 AND c.current_balance <= c.credit_limit";
                } else {
                    $where .= " AND c.current_balance > 0";
                }

                $customers = Yii::$app->db->createCommand("
                    SELECT
                        c.id,
                        c.customer_code,
                        COALESCE(c.company_name, CONCAT(c.first_name,' ',c.last_name)) name,
                        c.email,
                        c.credit_limit,
                        c.current_balance,
                        CASE WHEN c.current_balance > c.credit_limit THEN 'Over Limit' WHEN c.current_balance > 0 THEN 'Due' ELSE 'Paid' END status
                    FROM inventory_customers c
                    $where
                    ORDER BY c.current_balance DESC
                    LIMIT $perPage
                ", $params)->queryAll();

                return array_merge($this->jsonResponse(true, 'Balances loaded.'), ['customers' => $customers]);
            }

            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

}
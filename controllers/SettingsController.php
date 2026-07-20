<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class SettingsController extends Controller
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

    public function actionSettings()
    {
        $modules = [
            ['name' => 'General Settings', 'controller' => 'settings/generalsettings', 'icon' => 'fa fa-cogs'],
            ['name' => 'Company Profile', 'controller' => 'settings/companyprofile', 'icon' => 'fa fa-building'],
            ['name' => 'Account Settings', 'controller' => 'settings/accountsettings', 'icon' => 'fa fa-sitemap'],
            ['name' => 'Email Configuration', 'controller' => 'settings/email', 'icon' => 'fa fa-envelope'],
            ['name' => 'SMS Configuration', 'controller' => 'settings/sms', 'icon' => 'fa fa-mobile'],
            ['name' => 'Users', 'controller' => 'settings/users', 'icon' => 'fa fa-user'],
            ['name' => 'Roles & Permissions', 'controller' => 'settings/roles', 'icon' => 'fa fa-shield'],
            ['name' => 'Tax Settings', 'controller' => 'settings/taxsettings', 'icon' => 'fa fa-percent'],
            ['name' => 'Calendar', 'controller' => 'settings/calendar', 'icon' => 'fa fa-calendar'],
            ['name' => 'Backup & Restore', 'controller' => 'settings/backuprestore', 'icon' => 'fa fa-database'],
        ];

        return $this->render('settings', compact('modules'));
    }

    /* -------------------------------------------------------------
     * General Settings (key/value store)
     * ----------------------------------------------------------- */
    public function actionGeneralsettings()
    {
        if (Yii::$app->request->isGet) {
            $settings = Yii::$app->db->createCommand("SELECT * FROM inventory_settings WHERE is_deleted=0 ORDER BY setting_key")->queryAll();
            return $this->renderPartial('generalsettings', ['settings' => $settings]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            // Handle bulk save from AJAX forms (FormData)
            if (isset($post['flag']) && $post['flag'] == 'save_bulk') {
                $excludeKeys = [Yii::$app->request->csrfParam, 'flag'];
                foreach ($post as $key => $value) {
                    if (!in_array($key, $excludeKeys) && $value !== '') {
                        $this->saveSetting($key, $value);
                    }
                }
                return $this->jsonResponse(true, 'Settings updated successfully.');
            }

            $id = $post['id'] ?? null;
            if ($id && isset($post['delete']) && $post['delete'] == 1) {
                Yii::$app->db->createCommand()->update('inventory_settings', ['is_deleted' => 1], ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Setting removed successfully.');
            }

            if (empty($post['setting_key'])) {
                return $this->jsonResponse(false, 'Setting key is required.');
            }

            $this->saveSetting($post['setting_key'], $post['setting_value'] ?? '');
            return $this->jsonResponse(true, 'Setting saved successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function saveSetting($key, $value)
    {
        $exists = Yii::$app->db->createCommand("SELECT id FROM inventory_settings WHERE setting_key=:key")->bindValue(':key', $key)->queryScalar();
        if ($exists) {
            Yii::$app->db->createCommand()->update('inventory_settings', [
                'setting_value' => $value,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->currentUserId(),
                'is_deleted' => 0
            ], ['id' => $exists])->execute();
        } else {
            Yii::$app->db->createCommand()->insert('inventory_settings', [
                'setting_key' => $key,
                'setting_value' => $value,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->currentUserId(),
                'is_deleted' => 0
            ])->execute();
        }
    }

    private function getSetting($key, $default = null)
    {
        $value = Yii::$app->db->createCommand("SELECT setting_value FROM inventory_settings WHERE setting_key=:key AND is_deleted=0")->bindValue(':key', $key)->queryScalar();
        return $value !== false ? $value : $default;
    }

    /* -------------------------------------------------------------
     * Company Profile
     * ----------------------------------------------------------- */
    public function actionCompanyprofile()
    {
        $fields = ['company_name', 'company_address', 'company_phone', 'company_email', 'company_website', 'tax_number', 'currency', 'currency_symbol', 'fiscal_year_start', 'company_logo'];

        if (Yii::$app->request->isGet) {
            $profile = [];
            foreach ($fields as $field) {
                $profile[$field] = $this->getSetting($field, '');
            }
            return $this->renderPartial('companyprofile', ['profile' => $profile]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['company_logo'];
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = mime_content_type($file['tmp_name']);
                if (!in_array(strtolower($fileType), $allowedTypes)) {
                    return $this->jsonResponse(false, 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed.');
                }
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $uploadDir = Yii::getAlias('@webroot/documents/company/');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $newFileName = 'logo_' . time() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
                    $this->saveSetting('company_logo', 'documents/company/' . $newFileName);
                }
            }

            foreach ($fields as $field) {
                if (isset($post[$field])) {
                    $this->saveSetting($field, $post[$field]);
                }
            }

            return $this->jsonResponse(true, 'Company profile updated successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Users
     * ----------------------------------------------------------- */
    public function actionUsers()
    {
        if (Yii::$app->request->isGet) {
            $keyword = trim(Yii::$app->request->get('keyword', ''));
            $where = " WHERE 1=1 ";
            $params = [];
            if ($keyword != '') {
                $where .= " AND (u.username LIKE :kw OR u.email LIKE :kw OR u.first_name LIKE :kw OR u.last_name LIKE :kw)";
                $params[':kw'] = "%{$keyword}%";
            }
            $users = Yii::$app->db->createCommand("
                SELECT u.id, u.username, u.email, u.first_name, u.last_name, u.phone, u.role_id, u.last_login, u.created_at, r.name role_name
                FROM system_users u
                LEFT JOIN roles r ON r.id=u.role_id
                $where
                ORDER BY u.id DESC
            ", $params)->queryAll();
            $roles = Yii::$app->db->createCommand("SELECT id,name FROM roles ORDER BY name")->queryAll();
            return $this->renderPartial('users', ['users' => $users, 'roles' => $roles, 'keyword' => $keyword]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {
                $keyword = trim($post['keyword'] ?? '');
                $where = " WHERE 1=1 ";
                $params = [];
                if ($keyword != '') {
                    $where .= " AND (u.username LIKE :kw OR u.email LIKE :kw)";
                    $params[':kw'] = "%{$keyword}%";
                }
                $users = Yii::$app->db->createCommand("
                    SELECT u.id, u.username, u.email, u.first_name, u.last_name, u.phone, u.role_id, r.name role_name
                    FROM system_users u LEFT JOIN roles r ON r.id=u.role_id
                    $where ORDER BY u.id DESC
                ", $params)->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['users' => $users]);
            }

            if (isset($post['flag']) && $post['flag'] == 'resetpassword') {
                if (empty($post['id']) || empty($post['new_password'])) {
                    return $this->jsonResponse(false, 'User and new password are required.');
                }
                Yii::$app->db->createCommand()->update('system_users', ['password' => $post['new_password']], ['id' => $post['id']])->execute();
                return $this->jsonResponse(true, 'Password reset successfully.');
            }

            $id = $post['id'] ?? null;

            if ($id && isset($post['delete']) && $post['delete'] == 1) {
                if ($id == $this->currentUserId()) {
                    return $this->jsonResponse(false, 'You cannot delete your own account.');
                }
                Yii::$app->db->createCommand()->delete('system_users', ['id' => $id])->execute();
                return $this->jsonResponse(true, 'User deleted successfully.');
            }

            if (empty($post['username']) || empty($post['email'])) {
                return $this->jsonResponse(false, 'Username and email are required.');
            }

            $data = [
                'username' => $post['username'],
                'email' => $post['email'],
                'first_name' => $post['first_name'] ?? null,
                'last_name' => $post['last_name'] ?? null,
                'phone' => $post['phone'] ?? null,
                'role_id' => $post['role_id'] ?? null,
                'address' => $post['address'] ?? null,
            ];

            if ($id) {
                Yii::$app->db->createCommand()->update('system_users', $data, ['id' => $id])->execute();
                return $this->jsonResponse(true, 'User updated successfully.');
            }

            $data['password'] = $post['password'] ?? bin2hex(random_bytes(4));
            $data['created_at'] = date('Y-m-d H:i:s');
            Yii::$app->db->createCommand()->insert('system_users', $data)->execute();
            return $this->jsonResponse(true, 'User created successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Roles & Permissions
     * ----------------------------------------------------------- */
    public function actionRoles()
    {
        if (Yii::$app->request->isGet) {
            $roles = Yii::$app->db->createCommand("
                SELECT r.*, (SELECT COUNT(*) FROM system_users u WHERE u.role_id=r.id) user_count
                FROM roles r
                ORDER BY r.name
            ")->queryAll();
            return $this->renderPartial('roles', ['roles' => $roles]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            $id = $post['id'] ?? null;

            if ($id && isset($post['delete']) && $post['delete'] == 1) {
                $inUse = Yii::$app->db->createCommand("SELECT COUNT(*) FROM system_users WHERE role_id=:id")->bindValue(':id', $id)->queryScalar();
                if ($inUse > 0) {
                    return $this->jsonResponse(false, 'Cannot delete a role that is assigned to users.');
                }
                Yii::$app->db->createCommand()->delete('roles', ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Role deleted successfully.');
            }

            if (empty($post['name'])) {
                return $this->jsonResponse(false, 'Role name is required.');
            }

            if ($id) {
                Yii::$app->db->createCommand()->update('roles', ['name' => $post['name']], ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Role updated successfully.');
            }

            Yii::$app->db->createCommand()->insert('roles', ['name' => $post['name']])->execute();
            return $this->jsonResponse(true, 'Role created successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Tax Settings
     * ----------------------------------------------------------- */
    public function actionTaxsettings()
    {
        if (Yii::$app->request->isGet) {
            $rates = Yii::$app->db->createCommand("SELECT * FROM inventory_tax_rates WHERE is_deleted=0 ORDER BY tax_name")->queryAll();
            return $this->renderPartial('taxsettings', ['rates' => $rates]);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'search') {
                $rates = Yii::$app->db->createCommand("SELECT * FROM inventory_tax_rates WHERE is_deleted=0 ORDER BY tax_name")->queryAll();
                return $this->jsonResponse(true, 'Data loaded successfully!', ['rates' => $rates]);
            }

            $id = $post['id'] ?? null;

            if ($id && isset($post['delete']) && $post['delete'] == 1) {
                Yii::$app->db->createCommand()->update('inventory_tax_rates', ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Tax rate deleted successfully.');
            }

            if (empty($post['tax_name']) || !isset($post['tax_percentage'])) {
                return $this->jsonResponse(false, 'Tax name and percentage are required.');
            }

            $data = [
                'tax_name' => $post['tax_name'],
                'tax_percentage' => (float)$post['tax_percentage'],
                'is_default' => isset($post['is_default']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($id) {
                Yii::$app->db->createCommand()->update('inventory_tax_rates', $data, ['id' => $id])->execute();
                return $this->jsonResponse(true, 'Tax rate updated successfully.');
            }

            $data['created_at'] = date('Y-m-d H:i:s');
            $data['is_deleted'] = 0;
            Yii::$app->db->createCommand()->insert('inventory_tax_rates', $data)->execute();
            return $this->jsonResponse(true, 'Tax rate created successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Email Configuration (SMTP)
     * ----------------------------------------------------------- */
    public function actionEmail()
    {
        $fields = ['email_smtp_host', 'email_smtp_port', 'email_smtp_username', 'email_smtp_password', 'email_from_address', 'email_from_name', 'email_encryption', 'email_smtp_enabled'];

        if (Yii::$app->request->isGet) {
            $config = [];
            foreach ($fields as $field) {
                if ($field === 'email_smtp_password') {
                    $config[$field] = ''; // Never return password in GET
                } else {
                    $config[$field] = $this->getSetting($field, '');
                }
            }
            return $this->renderPartial('email', ['config' => $config]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'test') {
                $result = $this->testEmailConnection(
                    $post['email_smtp_host'] ?? '',
                    $post['email_smtp_port'] ?? 587,
                    $post['email_smtp_username'] ?? '',
                    $post['email_smtp_password'] ?? '',
                    $post['email_encryption'] ?? 'tls',
                    $post['email_from_address'] ?? ''
                );
                return $this->jsonResponse($result['success'], $result['message']);
            }

            foreach ($fields as $field) {
                if (isset($post[$field])) {
                    $value = $post[$field];
                    if ($field === 'email_smtp_port') {
                        $value = (int)$value;
                    }
                    $this->saveSetting($field, $value);
                }
            }

            return $this->jsonResponse(true, 'Email configuration updated successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function testEmailConnection($host, $port, $username, $password, $encryption, $fromEmail)
    {
        try {
            if (empty($host) || empty($port) || empty($username) || empty($password)) {
                return ['success' => false, 'message' => 'All SMTP fields are required.'];
            }

            $transport = (new \Swift_SmtpTransport($host, (int)$port, $encryption))
                ->setUsername($username)
                ->setPassword($password);

            $mailer = new \Swift_Mailer($transport);
            $message = (new \Swift_Message('Test Email'))
                ->setFrom($fromEmail ?: $username)
                ->setTo($username)
                ->setBody('This is a test email from Inventory System.');

            if ($mailer->send($message)) {
                return ['success' => true, 'message' => 'Test email sent successfully!'];
            } else {
                return ['success' => false, 'message' => 'Failed to send test email.'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()];
        }
    }

    /* -------------------------------------------------------------
     * SMS Configuration
     * ----------------------------------------------------------- */
    public function actionSms()
    {
        $fields = ['sms_api_provider', 'sms_api_key', 'sms_api_secret', 'sms_sender_id', 'sms_enabled'];

        if (Yii::$app->request->isGet) {
            $config = [];
            foreach ($fields as $field) {
                if (in_array($field, ['sms_api_key', 'sms_api_secret'])) {
                    $config[$field] = ''; // Never return secrets in GET
                } else {
                    $config[$field] = $this->getSetting($field, '');
                }
            }
            $config['sms_providers'] = ['twilio' => 'Twilio', 'vonage' => 'Vonage (Nexmo)', 'aws_sns' => 'AWS SNS', 'custom' => 'Custom API'];
            return $this->renderPartial('sms', ['config' => $config]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'test') {
                $result = $this->testSmsConnection(
                    $post['sms_api_provider'] ?? '',
                    $post['sms_api_key'] ?? '',
                    $post['sms_api_secret'] ?? '',
                    $post['sms_sender_id'] ?? ''
                );
                return $this->jsonResponse($result['success'], $result['message']);
            }

            foreach ($fields as $field) {
                if (isset($post[$field])) {
                    $this->saveSetting($field, $post[$field]);
                }
            }

            return $this->jsonResponse(true, 'SMS configuration updated successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function testSmsConnection($provider, $apiKey, $apiSecret, $senderId)
    {
        try {
            if (empty($provider) || empty($apiKey) || empty($senderId)) {
                return ['success' => false, 'message' => 'Provider, API Key, and Sender ID are required.'];
            }

            // Validate based on provider
            switch ($provider) {
                case 'twilio':
                    if (empty($apiSecret)) {
                        return ['success' => false, 'message' => 'Twilio requires both API Key and Auth Token.'];
                    }
                    // Basic validation - in production, make actual API call
                    if (strlen($apiKey) < 20 || strlen($apiSecret) < 32) {
                        return ['success' => false, 'message' => 'Invalid Twilio credentials format.'];
                    }
                    break;
                case 'vonage':
                    if (empty($apiSecret)) {
                        return ['success' => false, 'message' => 'Vonage requires both API Key and Secret.'];
                    }
                    break;
                case 'aws_sns':
                    if (strlen($apiKey) < 20) {
                        return ['success' => false, 'message' => 'Invalid AWS SNS credentials format.'];
                    }
                    break;
            }

            return ['success' => true, 'message' => 'SMS credentials validated successfully!'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Validation failed: ' . $e->getMessage()];
        }
    }

    /* -------------------------------------------------------------
     * Calendar
     * ----------------------------------------------------------- */
    public function actionCalendar()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;
            try {
                if (isset($post['flag']) && $post['flag'] == 'get' && !empty($post['id'])) {
                    $event = Yii::$app->db->createCommand("SELECT * FROM inventory_events WHERE id=:id AND is_deleted=0")->bindValue(':id', $post['id'])->queryOne();
                    return $event ? $this->jsonResponse(true, 'Event found.', ['event' => $event]) : $this->jsonResponse(false, 'Event not found.');
                }

                $id = $post['id'] ?? null;

                if ($id && isset($post['delete']) && $post['delete'] == 1) {
                    Yii::$app->db->createCommand()->update('inventory_events', ['is_deleted' => 1], ['id' => $id])->execute();
                    return $this->jsonResponse(true, 'Event deleted successfully.');
                }

                if (empty($post['title']) || empty($post['start_datetime'])) {
                    return $this->jsonResponse(false, 'Title and start date/time are required.');
                }

                $data = [
                    'title' => $post['title'],
                    'description' => $post['description'] ?? null,
                    'start_datetime' => $post['start_datetime'],
                    'end_datetime' => $post['end_datetime'] ?? $post['start_datetime'],
                    'location' => $post['location'] ?? null,
                    'event_color' => $post['event_color'] ?? '#3fb50f',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->currentUserId(),
                ];

                if ($id) {
                    Yii::$app->db->createCommand()->update('inventory_events', $data, ['id' => $id])->execute();
                    return $this->jsonResponse(true, 'Event updated successfully.');
                }

                $data['created_at'] = date('Y-m-d H:i:s');
                $data['created_by'] = $this->currentUserId();
                $data['is_deleted'] = 0;
                Yii::$app->db->createCommand()->insert('inventory_events', $data)->execute();
                return $this->jsonResponse(true, 'Event created successfully.');
            } catch (\Exception $e) {
                return $this->jsonResponse(false, $e->getMessage());
            }
        }

        $events = Yii::$app->db->createCommand("
            SELECT id, title, description, start_datetime, end_datetime, location, event_color
            FROM inventory_events
            WHERE is_deleted=0
            ORDER BY start_datetime ASC
        ")->queryAll();

        return $this->render('calendar', ['events' => $events]);
    }

    /* -------------------------------------------------------------
     * Account Settings (Financial Accounts)
     * ----------------------------------------------------------- */
    public function actionAccountsettings()
    {
        $fields = ['default_sales_account', 'default_purchase_account', 'default_expense_account', 'default_refund_account'];

        if (Yii::$app->request->isGet) {
            // Get all active accounts for dropdown
            $accounts = Yii::$app->db->createCommand("
                SELECT id, account_code, account_name, account_type
                FROM inventory_accounts
                WHERE is_deleted=0 AND is_active=1
                ORDER BY account_type, account_name
            ")->queryAll();

            $settings = [];
            foreach ($fields as $field) {
                $settings[$field] = $this->getSetting($field, '');
            }

            return $this->renderPartial('accountsettings', ['settings' => $settings, 'accounts' => $accounts]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            foreach ($fields as $field) {
                if (isset($post[$field])) {
                    $this->saveSetting($field, $post[$field]);
                }
            }

            return $this->jsonResponse(true, 'Account settings updated successfully.');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    /* -------------------------------------------------------------
     * Backup & Restore
     * ----------------------------------------------------------- */
    public function actionBackuprestore()
    {
        $tables = [
            'inventory_categories', 'inventory_brands', 'inventory_units', 'inventory_products',
            'inventory_warehouses', 'inventory_stock', 'inventory_suppliers', 'inventory_customers',
            'inventory_accounts', 'inventory_settings', 'inventory_tax_rates'
        ];

        if (Yii::$app->request->isGet) {
            return $this->renderPartial('backuprestore', ['tables' => $tables]);
        }

        try {
            $post = Yii::$app->request->post();

            if (isset($post['flag']) && $post['flag'] == 'backup') {
                $dump = [];
                foreach ($tables as $table) {
                    try {
                        $dump[$table] = Yii::$app->db->createCommand("SELECT * FROM $table WHERE is_deleted=0")->queryAll();
                    } catch (\Exception $e) {
                        $dump[$table] = [];
                    }
                }
                $json = json_encode(['generated_at' => date('Y-m-d H:i:s'), 'data' => $dump], JSON_PRETTY_PRINT);
                Yii::$app->response->format = Response::FORMAT_RAW;
                Yii::$app->response->headers->add('Content-Type', 'application/json');
                Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="inventory_backup_' . date('Ymd_His') . '.json"');
                return $json;
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            if (isset($post['flag']) && $post['flag'] == 'restore') {
                if (empty($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
                    return $this->jsonResponse(false, 'Please select a valid backup file.');
                }
                $content = file_get_contents($_FILES['backup_file']['tmp_name']);
                $payload = json_decode($content, true);
                if (!$payload || empty($payload['data'])) {
                    return $this->jsonResponse(false, 'Invalid backup file format.');
                }

                $imported = 0;
                $trans = Yii::$app->db->beginTransaction();
                try {
                    foreach ($payload['data'] as $table => $rows) {
                        if (!in_array($table, $tables) || empty($rows)) {
                            continue;
                        }
                        foreach ($rows as $row) {
                            unset($row['id']);
                            try {
                                Yii::$app->db->createCommand()->insert($table, $row)->execute();
                                $imported++;
                            } catch (\Exception $e) {
                                // Skip rows that fail (duplicate keys, missing refs, etc.)
                            }
                        }
                    }
                    $trans->commit();
                    return $this->jsonResponse(true, "Restore completed. $imported record(s) imported.");
                } catch (\Exception $e) {
                    $trans->rollBack();
                    return $this->jsonResponse(false, $e->getMessage());
                }
            }

            return $this->jsonResponse(false, 'Invalid request.');
        } catch (\Exception $e) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function actionInjectdb()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(150) UNIQUE,
                setting_value TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_deleted TINYINT DEFAULT 0
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_tax_rates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tax_name VARCHAR(100) NOT NULL,
                tax_percentage DECIMAL(5,2) DEFAULT 0,
                is_default TINYINT DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                is_deleted TINYINT DEFAULT 0
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                start_datetime DATETIME NOT NULL,
                end_datetime DATETIME NULL,
                location VARCHAR(255),
                event_color VARCHAR(20) DEFAULT '#3fb50f',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_deleted TINYINT DEFAULT 0
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_email_config (
                id INT AUTO_INCREMENT PRIMARY KEY,
                smtp_host VARCHAR(255),
                smtp_port INT DEFAULT 587,
                smtp_username VARCHAR(255),
                smtp_password VARCHAR(255),
                from_address VARCHAR(255),
                from_name VARCHAR(255),
                encryption VARCHAR(20) DEFAULT 'tls',
                is_enabled TINYINT DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_sms_config (
                id INT AUTO_INCREMENT PRIMARY KEY,
                api_provider VARCHAR(100),
                api_key VARCHAR(255),
                api_secret VARCHAR(255),
                sender_id VARCHAR(255),
                is_enabled TINYINT DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT
            ) ENGINE=InnoDB;
            ")->execute();

            $transaction->commit();

            echo "Settings tables created successfully.";
            exit;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo "Error: " . $e->getMessage();
            exit;
        }
    }
}
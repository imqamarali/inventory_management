<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class Components extends Component
{
    /**
     * Log user activity in the system
     * @param string $activity The activity description (e.g., "Created new student", "Updated fee structure")
     * @param string $activityType The type of activity (e.g., "create", "update", "delete", "view", "login", "logout")
     * @param mixed $refId Reference ID (can be student_id, fee_id, etc.)
     * @param string|null $module The module name (e.g., "students", "fee", "settings")
     * @param array $additionalData Any additional data to log (will be JSON encoded)
     * @return bool Success status
     */
    public function Activitylog($activity, $activityType, $refId = null, $module = null, $additionalData = [])
    {
        try {
            // Set timezone to Asia/Karachi
            date_default_timezone_set('Asia/Karachi');

            // Get user ID from session
            $userId = Yii::$app->session->get('user_array')['id'] ?? null;

            // If no user ID (system action), use 0
            if (!$userId) {
                $userId = 0;
            }

            // Get IP address
            $ipAddress = Yii::$app->request->userIP ?? 'Unknown';

            // Get user agent
            $userAgent = Yii::$app->request->userAgent ?? 'Unknown';

            // Convert additional data to JSON
            $additionalDataJson = !empty($additionalData) ? json_encode($additionalData) : null;

            // Insert activity log with parameter binding (prevents SQL injection)
            Yii::$app->db->createCommand()
                ->insert('activitylogs', [
                    'activity' => $activity,
                    'activitytype' => $activityType,
                    'refid' => $refId,
                    'module' => $module,
                    'uid' => $userId,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'additional_data' => $additionalDataJson,
                    'date' => date('Y-m-d'),
                    'datetime' => date('Y-m-d H:i:s'),
                ])
                ->execute();

            return true;
        } catch (\Exception $e) {
            // Log error but don't break the application
            Yii::error("Failed to log activity: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get formatted activity logs with user information
     * @param array $filters Filters for the query (user_id, module, activitytype, date_from, date_to)
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array Activity logs with user details
     */
    public function GetActivityLogs($filters = [], $limit = 50, $offset = 0)
    {
        try {
            $query = "SELECT al.*, 
                      CONCAT(su.first_name, ' ', su.last_name) as user_name,
                      su.username,
                      su.email,
                      r.name as role_name
                      FROM activitylogs al
                      LEFT JOIN system_users su ON al.uid = su.id
                      LEFT JOIN roles r ON su.role_id = r.id
                      WHERE 1=1";

            $params = [];

            // Apply filters
            if (!empty($filters['user_id'])) {
                $query .= " AND al.uid = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }

            if (!empty($filters['module'])) {
                $query .= " AND al.module = :module";
                $params[':module'] = $filters['module'];
            }

            if (!empty($filters['activitytype'])) {
                $query .= " AND al.activitytype = :activitytype";
                $params[':activitytype'] = $filters['activitytype'];
            }

            if (!empty($filters['date_from'])) {
                $query .= " AND al.date >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }

            if (!empty($filters['date_to'])) {
                $query .= " AND al.date <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }

            if (!empty($filters['search'])) {
                $query .= " AND (al.activity LIKE :search OR CONCAT(su.first_name, ' ', su.last_name) LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }

            // Get school_id for filtering
            $school_id = $this->School_id();
            if ($school_id) {
                $query .= " AND (su.school_id = :school_id OR su.school_id IS NULL)";
                $params[':school_id'] = $school_id;
            }

            $query .= " ORDER BY al.datetime DESC LIMIT :limit OFFSET :offset";

            $command = Yii::$app->db->createCommand($query);

            // Bind all parameters
            foreach ($params as $key => $value) {
                $command->bindValue($key, $value);
            }

            $command->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $command->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);

            return $command->queryAll();
        } catch (\Exception $e) {
            Yii::error("Failed to get activity logs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total count of activity logs for pagination
     * @param array $filters Same filters as GetActivityLogs
     * @return int Total count
     */
    public function GetActivityLogsCount($filters = [])
    {
        try {
            $query = "SELECT COUNT(*) as total
                      FROM activitylogs al
                      LEFT JOIN system_users su ON al.uid = su.id
                      WHERE 1=1";

            $params = [];

            // Apply same filters
            if (!empty($filters['user_id'])) {
                $query .= " AND al.uid = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }

            if (!empty($filters['module'])) {
                $query .= " AND al.module = :module";
                $params[':module'] = $filters['module'];
            }

            if (!empty($filters['activitytype'])) {
                $query .= " AND al.activitytype = :activitytype";
                $params[':activitytype'] = $filters['activitytype'];
            }

            if (!empty($filters['date_from'])) {
                $query .= " AND al.date >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }

            if (!empty($filters['date_to'])) {
                $query .= " AND al.date <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }

            if (!empty($filters['search'])) {
                $query .= " AND (al.activity LIKE :search OR CONCAT(su.first_name, ' ', su.last_name) LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }

            // Get school_id for filtering
            $school_id = $this->School_id();
            if ($school_id) {
                $query .= " AND (su.school_id = :school_id OR su.school_id IS NULL)";
                $params[':school_id'] = $school_id;
            }

            $command = Yii::$app->db->createCommand($query);

            // Bind all parameters
            foreach ($params as $key => $value) {
                $command->bindValue($key, $value);
            }

            $result = $command->queryOne();
            return (int)($result['total'] ?? 0);
        } catch (\Exception $e) {
            Yii::error("Failed to get activity logs count: " . $e->getMessage());
            return 0;
        }
    } 

    public function School()
    {
        $user_id = Yii::$app->session->get('user_array')['id'];
        if($user_id){
            $item = Yii::$app->db->createCommand(
                "SELECT sc.* FROM school sc
                        LEFT JOIN system_users su ON (sc.school_id = su.school_id)
                        WHERE su.id= $user_id"
            )->queryOne();
            return $item;
        }
        return null;
    }
    public function School_id()
    {
        try {
            $user_id = Yii::$app->session->get('user_array')['id'];
            if (!$user_id) {
                throw new \Exception('User not found in session');
            }

            $item = Yii::$app->db->createCommand(
                "SELECT sc.* FROM school sc
                    LEFT JOIN system_users su ON (sc.school_id = su.school_id)
                    WHERE su.id = :user_id"
            )->bindValue(':user_id', $user_id, \PDO::PARAM_INT)
                ->queryOne();

            if ($item === false) {
                throw new \Exception('School not found for the given user');
            }

            return $item['school_id'];
        } catch (\Exception $e) {
            return Yii::$app->response->redirect(['site/logout']);
        }
    }  
    public function Languages()
    {
        $items = Yii::$app->db->createCommand('SELECT * FROM `languages`')->queryAll();
        return $items;
    }
    public function Notifications()
    {
        $notifications = Yii::$app->db->createCommand(
            "SELECT ne.* FROM `notification_event` ne
                LEFT JOIN session on session.session_id=ne.session_id
                WHERE session.is_active=1"
        )->queryAll();

        $menuMap = [];
        // SELECT `id`, `name`, `code`, `sample_message`, `email`, `sms`,
        //  `push`, `student`, `guardian`,
        //  `admin`, `session_id` FROM `notification_event` WHERE 1
        foreach ($notifications as $item) {
            $menuMap[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'code' => $item['code'],
                'sample_message' => $item['sample_message'],
                'email' => (int) $item['email'],
                'sms' => (int) $item['sms'],
                'push' => (int)$item['push'],
                'student' => (int) $item['student'],
                'guardian' => (int) $item['guardian'],
                'admin' => (int) $item['admin'],
            ];
        }

        return $menuMap;
    }
    public function Email()
    {
        $item = Yii::$app->db->createCommand('SELECT * FROM `smtp_settings`')->queryOne();
        return $item;
    }
    public function CMS()
    {
        $item = Yii::$app->db->createCommand(
            'SELECT fcs.*,l.name as lang_name,l.code as lang_code FROM `front_cms_settings` fcs
                LEFT JOIN languages l on l.id=fcs.language_id'
        )->queryOne();
        return $item;
    }
    public function Roles()
    {
        $items = Yii::$app->db->createCommand('SELECT * FROM `roles`')->queryAll();
        return $items;
    }
    public function CheckPermissions($module_id, $submodule = null)
    {

        $user = Yii::$app->session->get('user_array');

        // If no session exists, redirect to index.php
        if ($user === null) {
            return Yii::$app->response->redirect(['site/index'])->send();
        }

        // If user exists, get role_id safely
        $role_id = $user['role_id'] ?? null;
        $query = "SELECT permissions.*,modules_features.name FROM `permissions` 
                    LEFT JOIN modules_features on permissions.feature_id=modules_features.id
                    WHERE permissions.module_id=$module_id and permissions.role_id=$role_id;";
        if ($submodule) {

            $query = "SELECT permissions.*,modules_features.name FROM `permissions` 
                    LEFT JOIN modules_features on permissions.feature_id=modules_features.id
                    WHERE permissions.module_id=$module_id and permissions.feature_id=$submodule and permissions.role_id=$role_id;";
        }
        // echo $query;exi
        $info = Yii::$app->db->createCommand($query)->queryAll();
        return $info;
    }
    public function CheckSettingPermissions($module_id)
    {
        $role_id = Yii::$app->session->get('user_array')['role_id'];
        $query = "SELECT * FROM `setting_permissions` WHERE
                    setting_id =$module_id and role_id=$role_id;";
        $info = Yii::$app->db->createCommand($query)->queryOne();
        return $info;
    }
    public function CheckStudentPermissions($module_id, $submodule = null)
    {
        $role_id = Yii::$app->session->get('user_array')['role_id'];
        $query = "SELECT permissions.* FROM `permissions` 
                    WHERE permissions.module_id=$module_id AND permissions.feature_id IS NULL AND permissions.role_id=$role_id;";
        $info = Yii::$app->db->createCommand($query)->queryOne();
        return $info;
    }
    public function SessionId()
    {
        return Yii::$app->session->get('user_array')['role_id'];
    }
    public function CheckRole()
    {
        if (Yii::$app->session->get('user_array') != null) {
            $session_id = Yii::$app->session->get('user_array')['role_id'];

            $items = Yii::$app->db->createCommand("SELECT * FROM `roles` where id= $session_id")->queryOne();
            if ($items)
                return $items['id'];
            return -1;
        } else {
            return -1;
        }
    }
    public function CurrentSession()
    {
        $items = Yii::$app->db->createCommand("SELECT session_name, icon, date, end_date  FROM `session` WHERE is_active=1")->queryOne();
        return $items;
    }
    public function ActiveSession()
    {
        $items = Yii::$app->db->createCommand("SELECT *, session_id as id  FROM `session` WHERE is_active=1 ORDER by date DESC LIMIT 1")->queryOne();
        if ($items)
            return $items['id'];
        return -1;
    }
    public function SaveingPath($user, $type)
    {
        $students_profile = '/documents/students/profiles/';
        $students_documents = 'documents/students/documents/';

        if ($user == 'student') {
            if ($type == 'profile')
                return $students_profile;
            else if ($type == 'document')
                return $students_documents;
        }


        return null;
    }
}

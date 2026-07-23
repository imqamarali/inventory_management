<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SuperController extends Controller
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
        ];
    }

    /**
     * Ensure only authenticated users can access SuperController.
     */
    public function beforeAction($action)
    {
        // Keep behaviour consistent with other controllers (CSRF often disabled)
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    /**
     * Simple system overview page using Ace Admin layout.
     */
    public function actionIndex()
    {
        $schoolId = Yii::$app->Component->School_id();

        // Current school (if configured)
        $school = null;
        if ($schoolId) {
            try {
                $school = Yii::$app->db->createCommand(
                    'SELECT * FROM school WHERE school_id = :id'
                )
                    ->bindValue(':id', $schoolId)
                    ->queryOne();
            } catch (\Exception $e) {
                $school = null;
            }
        }

        // Basic numeric stats (safe fallbacks if tables missing)
        $stats = [
            'students' => 0,
            'staff' => 0,
            'classes' => 0,
            'sections' => 0,
            'documents' => 0,
        ];

        try {
            $stats['students'] = (int)Yii::$app->db->createCommand(
                'SELECT COUNT(*) FROM students WHERE school_id = :id'
            )
                ->bindValue(':id', $schoolId)
                ->queryScalar();
        } catch (\Exception $e) {
        }

        try {
            $stats['staff'] = (int)Yii::$app->db->createCommand(
                'SELECT COUNT(*) FROM staff WHERE school_id = :id'
            )
                ->bindValue(':id', $schoolId)
                ->queryScalar();
        } catch (\Exception $e) {
        }

        try {
            $stats['classes'] = (int)Yii::$app->db->createCommand(
                'SELECT COUNT(*) FROM classes WHERE school_id = :id'
            )
                ->bindValue(':id', $schoolId)
                ->queryScalar();
        } catch (\Exception $e) {
        }

        try {
            $stats['sections'] = (int)Yii::$app->db->createCommand(
                'SELECT COUNT(*) FROM sections WHERE school_id = :id'
            )
                ->bindValue(':id', $schoolId)
                ->queryScalar();
        } catch (\Exception $e) {
        }

        try {
            $stats['documents'] = (int)Yii::$app->db->createCommand(
                'SELECT COUNT(*) FROM documentations WHERE school_id = :id'
            )
                ->bindValue(':id', $schoolId)
                ->queryScalar();
        } catch (\Exception $e) {
        }

        // Current logged-in user & role
        $user = Yii::$app->session->get('user_array') ?? null;
        $roleName = null;

        if ($user && isset($user['role_id'])) {
            try {
                $roleName = Yii::$app->db->createCommand(
                    'SELECT name FROM roles WHERE id = :id'
                )
                    ->bindValue(':id', $user['role_id'])
                    ->queryScalar() ?: null;
            } catch (\Exception $e) {
                $roleName = null;
            }
        }

        // System-level information
        $db = Yii::$app->db;
        $dsn = $db->dsn;
        $dbName = '';
        $dbHost = 'localhost';

        if (is_string($dsn)) {
            if (preg_match('/dbname=([^;]+)/', $dsn, $dbNameMatch)) {
                $dbName = $dbNameMatch[1];
            }
            if (preg_match('/host=([^;]+)/', $dsn, $hostMatch)) {
                $dbHost = $hostMatch[1];
            }
        }

        $systemInfo = [
            'appName' => Yii::$app->name,
            'environment' => defined('YII_ENV') ? YII_ENV : 'prod',
            'phpVersion' => PHP_VERSION,
            'yiiVersion' => \Yii::getVersion(),
            'dbName' => $dbName,
            'dbHost' => $dbHost,
            'serverSoftware' => $_SERVER['SERVER_SOFTWARE'] ?? php_sapi_name(),
        ];

        $controllersInfo = $this->getControllersInfo();

        // Default selected controller for initial load (used by JS)
        $selectedControllerId = null;
        $selectedController = null;

        if (!empty($controllersInfo)) {
            $selectedController = $controllersInfo[0];
            $selectedControllerId = $selectedController['id'];
        }

        // No precomputed table stats; they are loaded via AJAX
        $selectedTableStats = [];

        return $this->render('index', [
            'school' => $school,
            'stats' => $stats,
            'systemInfo' => $systemInfo,
            'user' => $user,
            'roleName' => $roleName,
            'controllersInfo' => $controllersInfo,
            'selectedController' => $selectedController,
            'selectedControllerId' => $selectedControllerId,
            'selectedTableStats' => $selectedTableStats,
        ]);
    }

    /**
     * View recent records for a specific table used by a controller.
     *
     * @param string $controller Controller class id (e.g. AcademicController)
     * @param string $table      Database table name
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionTable(string $controller, string $table)
    {
        [$controllerInfo, $tableName] = $this->resolveControllerAndTable($controller, $table);

        $db = Yii::$app->db;

        // Get columns
        try {
            $columns = $db->createCommand("SHOW COLUMNS FROM `{$tableName}`")->queryAll();
        } catch (\Exception $e) {
            throw new NotFoundHttpException('Failed to load table structure: ' . $e->getMessage());
        }

        $columnNames = array_map(static function ($col) {
            return $col['Field'] ?? '';
        }, $columns);

        // Fetch latest 50 records ordered by first column (generic)
        $rows = [];
        if (!empty($columnNames)) {
            try {
                $rows = $db->createCommand("SELECT * FROM `{$tableName}` ORDER BY 1 DESC LIMIT 50")->queryAll();
            } catch (\Exception $e) {
                $rows = [];
            }
        }

        return $this->render('table', [
            'controllerInfo' => $controllerInfo,
            'tableName' => $tableName,
            'columns' => $columnNames,
            'rows' => $rows,
        ]);
    }

    /**
     * Return table data as JSON for use in SweetAlert modal.
     */
    public function actionGetTableData(string $controller, string $table): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            [$controllerInfo, $tableName, $pkField] = $this->resolveControllerAndTable($controller, $table, true);

            $db = Yii::$app->db;

            // Get columns
            $columnsMeta = $db->createCommand("SHOW COLUMNS FROM `{$tableName}`")->queryAll();
            $columns = array_map(static function ($col) {
                return $col['Field'] ?? '';
            }, $columnsMeta);

            if (empty($columns)) {
                return [
                    'success' => false,
                    'message' => 'No columns found for table.',
                ];
            }

            // Fetch latest 100 records ordered by primary key (if available) or first column
            $orderBy = $pkField ? "`{$pkField}` DESC" : '1 DESC';
            $rows = $db->createCommand("SELECT * FROM `{$tableName}` ORDER BY {$orderBy} LIMIT 100")->queryAll();

            return [
                'success' => true,
                'controller' => $controllerInfo['id'],
                'controllerTitle' => $controllerInfo['title'] ?? $controllerInfo['id'],
                'table' => $tableName,
                'pk' => $pkField,
                'columns' => $columns,
                'rows' => $rows,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get tables + stats for a specific controller as JSON (for AJAX).
     */
    public function actionGetControllerTables(string $controller): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $controllersInfo = $this->getControllersInfo();

            $controllerInfo = null;
            foreach ($controllersInfo as $ctrl) {
                if ($ctrl['id'] === $controller) {
                    $controllerInfo = $ctrl;
                    break;
                }
            }

            if ($controllerInfo === null) {
                return ['success' => false, 'message' => 'Controller not found.'];
            }

            $stats = [];
            if (!empty($controllerInfo['tables'])) {
                $stats = $this->getTablesStats($controllerInfo['tables']);
            }

            $tables = [];
            foreach ($stats as $name => $s) {
                $tables[] = [
                    'name' => $name,
                    'rows' => $s['rows'],
                    'lastInsertedAt' => $s['lastInsertedAt'],
                ];
            }

            return [
                'success' => true,
                'controllerId' => $controllerInfo['id'],
                'controllerTitle' => $controllerInfo['title'] ?? $controllerInfo['id'],
                'tables' => $tables,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Update a single cell value in a table row.
     */
    public function actionUpdateCell(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;

        if (!$request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method.'];
        }

        $controller = $request->post('controller');
        $table = $request->post('table');
        $column = $request->post('column');
        $pkField = $request->post('pk_field');
        $pkValue = $request->post('pk_value');
        $value = $request->post('value');

        if (!$controller || !$table || !$column || !$pkField || $pkValue === null) {
            return ['success' => false, 'message' => 'Missing required parameters.'];
        }

        try {
            [$controllerInfo, $tableName] = $this->resolveControllerAndTable($controller, $table);

            $db = Yii::$app->db;

            // Ensure column exists
            $columnsMeta = $db->createCommand("SHOW COLUMNS FROM `{$tableName}`")->queryAll();
            $columns = array_map(static function ($col) {
                return $col['Field'] ?? '';
            }, $columnsMeta);

            if (!in_array($column, $columns, true)) {
                return ['success' => false, 'message' => 'Invalid column.'];
            }

            // Prevent editing primary key directly
            $pkDetected = $this->detectPrimaryKey($db, $tableName);
            if ($pkDetected !== null && $column === $pkDetected) {
                return ['success' => false, 'message' => 'Editing primary key is not allowed.'];
            }

            $affected = $db->createCommand()
                ->update($tableName, [$column => $value], [$pkField => $pkValue])
                ->execute();

            if ($affected === 0) {
                return ['success' => false, 'message' => 'No rows updated.'];
            }

            return ['success' => true, 'message' => 'Cell updated successfully.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Delete a single row from a table.
     */
    public function actionDeleteRow(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;

        if (!$request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method.'];
        }

        $controller = $request->post('controller');
        $table = $request->post('table');
        $pkField = $request->post('pk_field');
        $pkValue = $request->post('pk_value');

        if (!$controller || !$table || !$pkField || $pkValue === null) {
            return ['success' => false, 'message' => 'Missing required parameters.'];
        }

        try {
            [$controllerInfo, $tableName] = $this->resolveControllerAndTable($controller, $table);

            $db = Yii::$app->db;

            $affected = $db->createCommand()
                ->delete($tableName, [$pkField => $pkValue])
                ->execute();

            if ($affected === 0) {
                return ['success' => false, 'message' => 'No rows deleted.'];
            }

            return ['success' => true, 'message' => 'Row deleted successfully.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Truncate a table.
     */
    public function actionTruncateTable(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;

        if (!$request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method.'];
        }

        $controller = $request->post('controller');
        $table = $request->post('table');

        if (!$controller || !$table) {
            return ['success' => false, 'message' => 'Missing required parameters.'];
        }

        // Only allow Super Admin to truncate tables
        $roleId = Yii::$app->session->get('user_array')['role_id'] ?? null;
        if ($roleId !== 1) {
            return ['success' => false, 'message' => 'Unauthorized. Only Super Admin can truncate tables.'];
        }

        try {
            [$controllerInfo, $tableName] = $this->resolveControllerAndTable($controller, $table);

            $db = Yii::$app->db;

            // First try a native TRUNCATE (fast + resets AUTO_INCREMENT in MySQL)
            try {
                $db->createCommand("TRUNCATE TABLE `{$tableName}`")->execute();

                return [
                    'success' => true,
                    'message' => 'Table truncated successfully.',
                ];
            } catch (\Throwable $truncateException) {
                // If TRUNCATE is not supported or fails (permissions, engine, etc),
                // fall back to deleting all rows and manually resetting AUTO_INCREMENT.
                try {
                    // Delete all rows
                    $db->createCommand("DELETE FROM `{$tableName}`")->execute();

                    // Best-effort reset of AUTO_INCREMENT (MySQL/MariaDB)
                    try {
                        $db->createCommand("ALTER TABLE `{$tableName}` AUTO_INCREMENT = 1")->execute();
                    } catch (\Throwable $alterException) {
                        // If resetting AUTO_INCREMENT fails, still consider the truncate
                        // operation successful because rows have been cleared.
                        return [
                            'success' => true,
                            'message' => 'Table rows deleted, but AUTO_INCREMENT could not be reset: ' . $alterException->getMessage(),
                        ];
                    }

                    return [
                        'success' => true,
                        'message' => 'Table rows deleted and AUTO_INCREMENT reset (TRUNCATE fallback).',
                    ];
                } catch (\Throwable $fallbackException) {
                    // If both TRUNCATE and the fallback fail, report a detailed error.
                    return [
                        'success' => false,
                        'message' => 'Failed to truncate table: ' . $truncateException->getMessage()
                            . '; fallback delete/reset failed: ' . $fallbackException->getMessage(),
                    ];
                }
            }
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Resolve controller & table, and optionally primary key field.
     *
     * @param string $controllerId
     * @param string $tableName
     * @param bool $withPk
     * @return array
     * @throws NotFoundHttpException
     */
    protected function resolveControllerAndTable(string $controllerId, string $tableName, bool $withPk = false): array
    {
        $controllersInfo = $this->getControllersInfo();

        $controllerInfo = null;
        foreach ($controllersInfo as $ctrl) {
            if ($ctrl['id'] === $controllerId) {
                $controllerInfo = $ctrl;
                break;
            }
        }

        if ($controllerInfo === null) {
            throw new NotFoundHttpException('Controller not found.');
        }

        // Validate table name
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
            throw new NotFoundHttpException('Invalid table name.');
        }

        if (empty($controllerInfo['tables']) || !in_array(strtolower($tableName), array_map('strtolower', $controllerInfo['tables']), true)) {
            throw new NotFoundHttpException('Table not associated with this controller.');
        }

        if (!$withPk) {
            return [$controllerInfo, $tableName];
        }

        $db = Yii::$app->db;
        $pkField = $this->detectPrimaryKey($db, $tableName);

        return [$controllerInfo, $tableName, $pkField];
    }

    /**
     * Get all actions for a controller (for Test Run).
     */
    public function actionGetControllerActions(string $controller): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $controllersInfo = $this->getControllersInfo();

            $controllerInfo = null;
            foreach ($controllersInfo as $ctrl) {
                if ($ctrl['id'] === $controller) {
                    $controllerInfo = $ctrl;
                    break;
                }
            }

            if ($controllerInfo === null) {
                return ['success' => false, 'message' => 'Controller not found.'];
            }

            $routeId = $controllerInfo['routeId'] ?? ($controllerInfo['route_id'] ?? null);
            if ($routeId === null) {
                return ['success' => false, 'message' => 'Controller route not available.'];
            }

            $filePath = Yii::getAlias('@app/controllers/' . $controller . '.php');
            if (!is_file($filePath) || !is_readable($filePath)) {
                return ['success' => false, 'message' => 'Controller file not readable.'];
            }

            $content = file_get_contents($filePath);
            if ($content === false) {
                return ['success' => false, 'message' => 'Failed to read controller file.'];
            }

            $pattern = '/public\s+function\s+action([A-Z][A-Za-z0-9_]*)\s*\(/';
            $actions = [];

            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $suffix) {
                    $methodName = 'action' . $suffix;

                    // Convert CamelCase to id (e.g. GetStats -> get-stats)
                    $id = lcfirst($suffix);
                    $id = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $id));

                    // Skip some obviously internal/test-unfriendly actions if needed
                    $actions[] = [
                        'id' => $id,
                        'method' => $methodName,
                        'route' => $routeId . '/' . $id,
                    ];
                }
            }

            return [
                'success' => true,
                'controllerId' => $controllerInfo['id'],
                'controllerTitle' => $controllerInfo['title'] ?? $controllerInfo['id'],
                'actions' => $actions,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Run a controller action as a "test" and report result.
     */
    public function actionRunActionTest(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;

        if (!$request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method.'];
        }

        $controller = $request->post('controller');
        $actionId = $request->post('action');

        if (!$controller || !$actionId) {
            return ['success' => false, 'message' => 'Missing controller or action.'];
        }

        try {
            $controllersInfo = $this->getControllersInfo();

            $controllerInfo = null;
            foreach ($controllersInfo as $ctrl) {
                if ($ctrl['id'] === $controller) {
                    $controllerInfo = $ctrl;
                    break;
                }
            }

            if ($controllerInfo === null) {
                return ['success' => false, 'message' => 'Controller not found.'];
            }

            $routeId = $controllerInfo['routeId'] ?? ($controllerInfo['route_id'] ?? null);
            if ($routeId === null) {
                return ['success' => false, 'message' => 'Controller route not available.'];
            }

            $route = $routeId . '/' . $actionId;

            $start = microtime(true);
            try {
                $result = Yii::$app->runAction($route, ['__superTest' => 1]);
                $status = 'ok';
                $message = 'OK';
                $resultType = is_object($result) ? get_class($result) : gettype($result);
            } catch (\Throwable $e) {
                $status = 'error';
                $message = $e->getMessage();
                $resultType = get_class($e);
            }
            $durationMs = (microtime(true) - $start) * 1000;

            return [
                'success' => true,
                'status' => $status,
                'message' => $message,
                'resultType' => $resultType,
                'durationMs' => round($durationMs, 1),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build a list of controllers and the database tables they touch
     * by scanning SQL statements in controller PHP files.
     *
     * @return array
     */
    protected function getControllersInfo(): array
    {
        $controllersDir = Yii::getAlias('@app/controllers');
        $files = glob($controllersDir . DIRECTORY_SEPARATOR . '*Controller.php') ?: [];

        $info = [];

        foreach ($files as $file) {
            $className = basename($file, '.php');

            // Skip this controller itself
            if ($className === 'SuperController') {
                continue;
            }

            $routeId = strtolower(preg_replace('/Controller$/', '', $className));
            $title = $this->humanizeControllerName($className);
            $tables = $this->extractTablesFromFile($file);

            $info[] = [
                'id' => $className,
                'title' => $title,
                'route' => $routeId ? $routeId . '/index' : null,
                'routeId' => $routeId,
                'tables' => $tables,
            ];
        }

        // Sort alphabetically by title
        usort($info, static function ($a, $b) {
            return strcmp($a['title'], $b['title']);
        });

        return $info;
    }

    /**
     * Convert controller class name to a readable title.
     *
     * Example: AcademicController -> Academic, OnlinepaymentsController -> Onlinepayments
     */
    protected function humanizeControllerName(string $className): string
    {
        $name = preg_replace('/Controller$/', '', $className);
        // Split CamelCase into words
        $name = preg_replace('/(?<!^)([A-Z])/', ' $1', $name);
        return trim($name);
    }

    /**
     * Extract table names from SQL statements in a PHP file.
     *
     * This scans for FROM / JOIN / INSERT INTO / UPDATE / DELETE FROM patterns.
     */
    protected function extractTablesFromFile(string $filePath): array
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            return [];
        }

        $patterns = [
            '/\bFROM\s+`?([a-zA-Z0-9_]+)`?/i',
            '/\bJOIN\s+`?([a-zA-Z0-9_]+)`?/i',
            '/\bINSERT\s+INTO\s+`?([a-zA-Z0-9_]+)`?/i',
            '/\bUPDATE\s+`?([a-zA-Z0-9_]+)`?/i',
            '/\bDELETE\s+FROM\s+`?([a-zA-Z0-9_]+)`?/i',
        ];

        $tables = [];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $table) {
                    $table = trim($table);
                    if ($table === '') {
                        continue;
                    }
                    $tables[] = strtolower($table);
                }
            }
        }

        if (empty($tables)) {
            return [];
        }

        $tables = array_values(array_unique($tables));
        sort($tables);

        return $tables;
    }

    /**
     * Compute basic stats for a list of tables:
     * - total rows
     * - last inserted record datetime (best-effort, based on common columns)
     *
     * @param array $tables
     * @return array
     */
    protected function getTablesStats(array $tables): array
    {
        $db = Yii::$app->db;
        $stats = [];

        foreach ($tables as $table) {
            $tableName = trim($table);
            $rowCount = null;
            $lastInsertedAt = null;

            try {
                // Total rows
                $rowCount = (int)$db->createCommand("SELECT COUNT(*) FROM `{$tableName}`")->queryScalar();

                // Last inserted datetime (best-effort)
                $lastInsertedAt = $this->detectLastInsertedAt($db, $tableName);
            } catch (\Exception $e) {
                // Skip invalid/missing tables
                continue;
            }

            $stats[$tableName] = [
                'rows' => $rowCount,
                'lastInsertedAt' => $lastInsertedAt,
            ];
        }

        return $stats;
    }

    /**
     * Try to determine last inserted datetime for a table by:
     * 1. Looking for common datetime columns (created_at, created_on, date, datetime, created)
     * 2. Using MAX() on the best matching column
     *
     * @param \yii\db\Connection $db
     * @param string $tableName
     * @return string|null
     */
    protected function detectLastInsertedAt($db, string $tableName): ?string
    {
        try {
            $columns = $db->createCommand("SHOW COLUMNS FROM `{$tableName}`")->queryAll();
        } catch (\Exception $e) {
            return null;
        }

        if (empty($columns)) {
            return null;
        }

        $fieldNames = array_map(static function ($col) {
            return $col['Field'] ?? '';
        }, $columns);

        $commonNames = [
            'created_at',
            'created_on',
            'created',
            'date',
            'datetime',
            'timestamp',
        ];

        $chosenField = null;

        foreach ($commonNames as $candidate) {
            foreach ($fieldNames as $field) {
                if (strcasecmp($field, $candidate) === 0) {
                    $chosenField = $field;
                    break 2;
                }
            }
        }

        if ($chosenField === null) {
            return null;
        }

        try {
            $value = $db->createCommand(
                "SELECT MAX(`{$chosenField}`) AS dt FROM `{$tableName}`"
            )->queryScalar();

            return $value !== false ? (string)$value : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Try to detect the primary key field for a table.
     *
     * @param \yii\db\Connection $db
     * @param string $tableName
     * @return string|null
     */
    protected function detectPrimaryKey($db, string $tableName): ?string
    {
        try {
            // Try to get PRIMARY key
            $keys = $db->createCommand("SHOW KEYS FROM `{$tableName}` WHERE Key_name = 'PRIMARY'")->queryAll();
            if (!empty($keys)) {
                return $keys[0]['Column_name'] ?? null;
            }

            // Fallback to first column
            $columns = $db->createCommand("SHOW COLUMNS FROM `{$tableName}`")->queryAll();
            if (!empty($columns) && isset($columns[0]['Field'])) {
                return $columns[0]['Field'];
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}

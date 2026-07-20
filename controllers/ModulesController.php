<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\db\Exception;


class ModulesController extends Controller
{
    public $school_id;
    public function init()
    {
        parent::init();
        $school_id = Yii::$app->Component->School_id();
        $this->school_id = $school_id;
    }
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
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
    public function beforeAction($action)
    {
        if (Yii::$app->session->has('user_array') == NULL)
            $this->redirect(['site/index']);
        else {
            $this->enableCsrfValidation = false;
            return parent::beforeAction($action);
        }
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
    public function actionIndex()
    {
        $role_id = Yii::$app->session->get('user_array')['id'];
        $role = Yii::$app->db->createCommand("SELECT * FROM `roles` WHERE id='" . $role_id . "'")->queryOne();
        if (empty($role)) {
            Yii::$app->session->setFlash('success', 'No Role found.');
            return $this->redirect(['config/']);
        }

        $modules = Yii::$app->db->createCommand("SELECT * FROM modules WHERE active=1  ORDER BY order_by")->queryAll();

        $moduleList = [];
        foreach ($modules as $module) {
            $submenus = [];
            $moduleFeatures = Yii::$app->db->createCommand("SELECT * FROM modules_features WHERE module_id = :module_id")
                ->bindValue(':module_id', $module['id'])->queryAll();

            foreach ($moduleFeatures as $feature) {

                $permissions = Yii::$app->db->createCommand("
                        SELECT * FROM permissions
                        WHERE module_id = :module_id
                        AND feature_id = :feature_id
                        AND role_id = :role_id")
                    ->bindValue(':module_id', $module['id'])
                    ->bindValue(':feature_id', $feature['id'])
                    ->bindValue(':role_id', $role_id)
                    ->queryOne();

                if ($permissions && $permissions['can_view']) {
                    $submenus[] = [
                        'feature_id' => $feature['id'],
                        'link' => $feature['link'] ?? '#',
                        'icon' => $module['icon'],
                        'active' => false,
                        'title' => $feature['name'],
                        'permission_id' => $permissions['id'],
                        'can_view' => (int) $permissions['can_view'],
                        'can_add' => (int) $permissions['can_add'],
                        'can_edit' => (int)$permissions['can_edit'],
                        'can_delete' => (int)$permissions['can_delete'],
                    ];
                }
            }

            if (!empty($submenus)) {
                $moduleList[] = [
                    'module_id' => $module['id'],
                    'title' => $module['name'],
                    'is_active' => $module['active'],
                    'link' => '#',
                    'icon' => $module['icon'],
                    'active' => false,
                    'submenus' => $submenus,
                ];
            }
        }
        $returnList = [];
        if (empty($moduleList)) {
            Yii::$app->session->setFlash('success', 'You don\'t have any permissions');
            return $this->render('index', [
                'permissions' => $returnList,
            ]);
        }

        // $returnList['role_id'] = $id;
        // $returnList['role_name'] = $role['name'];
        $returnList["modules"] = $moduleList;


        return $this->render('index', [
            'permissions' => $returnList,
        ]);
    }
    public function actionPermissions($id)
    {
        $role_id = Yii::$app->Component->CheckRole();

        if ($role_id != 1) {
            Yii::$app->session->setFlash('toast', 'You do not have permissions');
            return $this->redirect(['site/index']);
        }
        if ($_REQUEST['type'] == 'modules') {

            $role = Yii::$app->db->createCommand("SELECT * FROM `roles` WHERE id='" . $id . "'")->queryOne();
            if (empty($role)) {
                Yii::$app->session->setFlash('success', 'No Role found.');
                return $this->redirect(['config/']);
            }

            // If student role (id=4), show type=2 modules (navbar/student modules), otherwise type=1 (sidebar modules)
            $module_type = ($id == 4) ? 2 : 1;
            $modules = Yii::$app->db->createCommand('SELECT * FROM modules WHERE active=1 AND type=:type ORDER BY order_by')
                ->bindValue(':type', $module_type)
                ->queryAll();

            $moduleList = [];
            foreach ($modules as $module) {
                $id_not_in = "";
                $submenus = [];
                $moduleFeatures = Yii::$app->db->createCommand(
                    "SELECT * FROM modules_features WHERE `is_active` = '1' AND module_id = :module_id"
                )
                    ->bindValue(':module_id', $module['id'])->queryAll();

                // If module features exist, process them
                if (!empty($moduleFeatures)) {
                    foreach ($moduleFeatures as $feature) {

                        $permissions = Yii::$app->db->createCommand("
                            SELECT * FROM permissions
                            WHERE module_id = :module_id
                            AND feature_id = :feature_id
                            AND role_id = :role_id")
                            ->bindValue(':module_id', $module['id'])
                            ->bindValue(':feature_id', $feature['id'])
                            ->bindValue(':role_id', $id)
                            ->queryOne();
                        if (!$permissions) {
                            Yii::$app->db->createCommand()->insert('permissions', [
                                'role_id' => $id,
                                'module_id' => $module['id'],
                                'feature_id' => $feature['id'],
                                'can_add' => 0,
                                'can_view' => 0,
                                'can_edit' => 0,
                                'can_delete' => 0
                            ])->execute();
                            $permissions = Yii::$app->db->createCommand("
                                    SELECT * FROM permissions
                                    WHERE module_id = :module_id
                                    AND feature_id = :feature_id
                                    AND role_id = :role_id")
                                ->bindValue(':module_id', $module['id'])
                                ->bindValue(':feature_id', $feature['id'])
                                ->bindValue(':role_id', $id)
                                ->queryOne();
                        }
                        if ($permissions) {
                            $submenus[] = [
                                'feature_id' => $feature['id'],
                                'link' => '#',
                                'icon' => $module['icon'],
                                'active' => false,
                                'title' => $feature['name'],
                                'permission_id' => $permissions['id'],
                                'can_view' => (int) $permissions['can_view'],
                                'can_add' => (int) $permissions['can_add'],
                                'can_edit' => (int) $permissions['can_edit'],
                                'can_delete' => (int) $permissions['can_delete'],
                            ];
                        }
                    }

                    if (!empty($submenus)) {
                        $moduleList[] = [
                            'module_id' => $module['id'],
                            'title' => $module['name'],
                            'link' => '#',
                            'icon' => $module['icon'],
                            'is_active' => (int) $module['active'],
                            'active' => false,
                            'submenus' => $submenus,
                        ];
                    }
                } else {
                    // If no module features found, get permissions directly
                    $permissions = Yii::$app->db->createCommand("
                            SELECT * FROM permissions
                            WHERE module_id = :module_id
                            AND feature_id IS NULL
                            AND role_id = :role_id")
                        ->bindValue(':module_id', $module['id'])
                        ->bindValue(':role_id', $id)
                        ->queryOne();
                    if (!$permissions) {
                        Yii::$app->db->createCommand()->insert('permissions', [
                            'role_id' => $id,
                            'module_id' => $module['id'],
                            'feature_id' => NULL,
                            'can_add' => 0,
                            'can_view' => 0,
                            'can_edit' => 0,
                            'can_delete' => 0
                        ])->execute();
                        $permissions = Yii::$app->db->createCommand("
                                SELECT * FROM permissions
                                WHERE module_id = :module_id
                                AND feature_id  IS NULL
                                AND role_id = :role_id")
                            ->bindValue(':module_id', $module['id'])
                            ->bindValue(':role_id', $id)
                            ->queryOne();
                    }
                    if ($permissions) {
                        $moduleList[] = [
                            'module_id' => $module['id'],
                            'title' => $module['name'],
                            'link' => '#',
                            'icon' => $module['icon'],
                            'is_active' => (int) $module['active'],
                            'active' => false,
                            'submenus' => [],
                            'permission_id' => $permissions['id'],
                            'can_view' => (int) $permissions['can_view'],
                            'can_add' => (int) $permissions['can_add'],
                            'can_edit' => (int) $permissions['can_edit'],
                            'can_delete' => (int) $permissions['can_delete'],
                        ];
                    }
                }
            }

            // echo json_encode($moduleList);exit;

            $returnList = [];
            if (empty($moduleList)) {
                Yii::$app->session->setFlash('toast', 'No defaults Permissions set for this Role.');
                return $this->redirect(['config/']);
            }

            $returnList['role_id'] = $id;
            $returnList['role_name'] = $role['name'];
            $returnList["modules"] = $moduleList;


            return $this->render('permissions', [
                'permissions' => $returnList,
            ]);
        } else if ($_REQUEST['type'] == 'settings') {
            $role = Yii::$app->db->createCommand("SELECT * FROM `roles` WHERE id='" . $id . "'")->queryOne();
            if (empty($role)) {
                Yii::$app->session->setFlash('toast', 'Error! No Role found.');
                return $this->redirect(['config/index', 'flag' => 'roles']);
            }
            $settings = Yii::$app->db->createCommand('SELECT * FROM system_settings')->queryAll();

            $settingsList = [];
            foreach ($settings as $setting) {
                $permissions = Yii::$app->db->createCommand("
                        SELECT * FROM setting_permissions
                        WHERE setting_id = :setting_id
                        AND role_id = :role_id")
                    ->bindValue(':setting_id', $setting['id'])
                    ->bindValue(':role_id', $id)
                    ->queryOne();
                if (!$permissions) {
                    Yii::$app->db->createCommand()->insert('setting_permissions', [
                        'role_id' => $id,
                        'setting_id' => $setting['id'],
                        'can_add' => 0,
                        'can_view' => 0,
                        'can_edit' => 0,
                        'can_delete' => 0
                    ])->execute();
                    $permissions = Yii::$app->db->createCommand("
                            SELECT * FROM setting_permissions
                            WHERE setting_id = :setting_id
                            AND role_id = :role_id")
                        ->bindValue(':setting_id', $setting['id'])
                        ->bindValue(':role_id', $id)
                        ->queryOne();
                }
                $settingsList[] = [
                    'setting_id' => $permissions['id'] ?? '',
                    'title' => $setting['name'],
                    'active' => (int) $setting['active'],
                    'can_add' => $permissions ? (int) $permissions['can_add'] : 0,
                    'can_view' => $permissions ? (int) $permissions['can_view'] : 0,
                    'can_edit' => $permissions ? (int) $permissions['can_edit'] : 0,
                    'can_delete' => $permissions ? (int) $permissions['can_delete'] : 0,
                ];
            }
            if (empty($settingsList)) {
                Yii::$app->session->setFlash('toast', 'Error! No default settings set for this Role.');
                return $this->redirect(['config/index', 'flag' => 'roles']);
            }
            $returnList = [
                'role_id' => $id,
                'role_name' => $role['name'],
                'settings' => $settingsList,
            ];

            return $this->render('system_permissions', [
                'setting_permissions' => $returnList,
            ]);
        } else if ($_REQUEST['type'] == 'schools') {
            $role = Yii::$app->db->createCommand("SELECT * FROM `roles` WHERE id='" . $id . "'")->queryOne();
            if (empty($role)) {
                Yii::$app->session->setFlash('toast', 'Error! No Role found.');
                return $this->redirect(['config/index', 'flag' => 'roles']);
            }
            $schools = Yii::$app->db->createCommand('SELECT * FROM school')->queryAll();

            $school_permissions = [];
            foreach ($schools as $school) {
                $permissions = Yii::$app->db->createCommand("
                        SELECT * FROM school_permissions
                        WHERE school_id = :school_id
                        AND role_id = :role_id")
                    ->bindValue(':school_id', $school['school_id'])
                    ->bindValue(':role_id', $id)
                    ->queryOne();
                if (!$permissions) {
                    Yii::$app->db->createCommand()->insert('school_permissions', [
                        'role_id' => $id,
                        'school_id' => $school['school_id'],
                        'can_add' => 0,
                        'can_view' => 0,
                        'can_edit' => 0,
                        'can_delete' => 0
                    ])->execute();
                    $permissions = Yii::$app->db->createCommand("
                            SELECT * FROM school_permissions
                            WHERE school_id = :school_id
                            AND role_id = :role_id")
                        ->bindValue(':school_id', $school['school_id'])
                        ->bindValue(':role_id', $id)
                        ->queryOne();
                }
                $school_permissions[] = [
                    'school_id' => $permissions['id'] ?? '',
                    'title' => $school['school_name'],
                    'active' => (int) $school['active'],
                    'can_add' => $permissions ? (int) $permissions['can_add'] : 0,
                    'can_view' => $permissions ? (int) $permissions['can_view'] : 0,
                    'can_edit' => $permissions ? (int) $permissions['can_edit'] : 0,
                    'can_delete' => $permissions ? (int) $permissions['can_delete'] : 0,
                ];
            }
            if (empty($school_permissions)) {
                Yii::$app->session->setFlash('toast', 'Error! No default settings set for this Role.');
                return $this->redirect(['config/index', 'flag' => 'roles']);
            }
            $returnList = [
                'role_id' => $id,
                'role_name' => $role['name'],
                'school_permissions' => $school_permissions,
            ];

            return $this->render('school_permissions', [
                'school_permissions' => $returnList,
            ]);
        } else if ($_REQUEST['type'] == 'reports') {
            $role = Yii::$app->db->createCommand("SELECT * FROM `roles` WHERE id='" . $id . "'")->queryOne();
            if (empty($role)) {
                Yii::$app->session->setFlash('toast', 'Error! No Role found.');
                return $this->redirect(['config/index', 'flag' => 'roles']);
            }
            $features = Yii::$app->db->createCommand('SELECT * FROM `modules_features` WHERE module_id=116 AND is_active=1')->queryAll();
            if (empty($features)) {
                Yii::$app->session->setFlash('toast', 'Error! Features not found.');
                return $this->redirect(['config/index', 'flag' => 'roles']);
            }

            $reports_permissions = [];

            foreach ($features as $feature) {
                $can_view = Yii::$app->db->createCommand("
                    SELECT * FROM permissions
                    WHERE module_id = 116
                    AND feature_id = :feature_id
                    AND role_id = :role_id
                    AND can_view = 1")
                    ->bindValue(':feature_id', $feature['id'])
                    ->bindValue(':role_id', $id)
                    ->queryOne();

                $feature_reports = [];
                if ($can_view) {
                    $reports_1 = Yii::$app->db->createCommand("
                        SELECT `id`, `name`, `icon`, `description`, `link`, `active`, `order_by`, `created_at`, `feature_id`
                        FROM `reports` WHERE feature_id = :feature_id AND active=1 ")
                        ->bindValue(':feature_id', $feature['id'])
                        ->queryAll();
                    foreach ($reports_1 as $report_item) {
                        $permissions = Yii::$app->db->createCommand("
                            SELECT * FROM `reports_permissions`
                            WHERE report_id = :report_id
                            AND feature_id = :feature_id
                            AND role_id = :role_id")
                            ->bindValue(':report_id', $report_item['id'])
                            ->bindValue(':feature_id', $report_item['feature_id'])
                            ->bindValue(':role_id', $id)
                            ->queryOne();

                        if (!$permissions) {
                            Yii::$app->db->createCommand()->insert('reports_permissions', [
                                'report_id' => $report_item['id'],
                                'feature_id' => $report_item['feature_id'],
                                'role_id' => $id,
                                'is_active' => 1,
                                'can_add' => 0,
                                'can_view' => 0,
                                'can_edit' => 0,
                                'can_delete' => 0
                            ])->execute();

                            $permissions = Yii::$app->db->createCommand("
                                SELECT * FROM `reports_permissions`
                                WHERE report_id = :report_id
                                AND feature_id = :feature_id
                                AND role_id = :role_id")
                                ->bindValue(':report_id', $report_item['id'])
                                ->bindValue(':feature_id', $report_item['feature_id'])
                                ->bindValue(':role_id', $id)
                                ->queryOne();
                        }

                        $feature_reports[] = [
                            'feature_id' => $report_item['id'],
                            'title' => $report_item['name'],
                            'is_active' => (int) $report_item['active'],
                            'report_permission_id' => $permissions['id'],
                            'can_add' => (int) ($permissions['can_add'] ?? 0),
                            'can_view' => (int) ($permissions['can_view'] ?? 0),
                            'can_edit' => (int) ($permissions['can_edit'] ?? 0),
                            'can_delete' => (int) ($permissions['can_delete'] ?? 0),
                        ];
                    }
                }

                if (!empty($feature_reports)) {
                    $reports_permissions[] = [
                        'module_id' => $feature['id'],
                        'title' => $feature['name'],
                        'icon' => $feature['icon'],
                        'reports' => $feature_reports
                    ];
                }
            }

            if (empty($reports_permissions)) {
                Yii::$app->session->setFlash('toast', 'Error! No Permissions set for this Role.');
                return $this->redirect(['config/index', 'flag' => 'roles']);
            }

            // Prepare the final $reports array structure
            $final_reports = [
                'role_id' => $id,
                'role_name' => $role['name'],
                'type' => "System Reports",
                'system_reports' => $reports_permissions,
            ];


            // $reports = [
            //     'role_id' => $id,
            //     'role_name' => $role['name'],
            //     'type' => "System Reports",
            //     'system_reports' => [
            //         'feature_id' => $feature['id'],
            //         'report_type' => $feature['name'],
            //         'reports' => [
            //             'report_id' => $report_item['id'],
            //             'report_name' => $report_item['name'],
            //             'is_active' => (int) $report_item['active'],
            //             'report_permission_id' => $permissions['id'],
            //             'can_add' => (int) ($permissions['can_add'] ?? 0),
            //             'can_view' => (int) ($permissions['can_view'] ?? 0),
            //             'can_edit' => (int) ($permissions['can_edit'] ?? 0),
            //             'can_delete' => (int) ($permissions['can_delete'] ?? 0),
            //         ]
            //     ],
            // ];


            // echo json_encode($final_reports);
            // exit;


            return $this->render('reports_permissions', [
                'reports' => $final_reports,
            ]);
        } else if ($_REQUEST['type'] == 'dashboard') {
            $role = Yii::$app->db->createCommand("SELECT * FROM `roles` WHERE id='" . $id . "'")->queryOne();
            if (empty($role)) {
                Yii::$app->session->setFlash('toast', 'Error! No Role found.');
                return $this->redirect(['config/index', 'flag' => 'roles']);
            }

            // Define dashboard stat types - only from stats-grid
            $statTypes = [
                ['type' => 'students', 'label' => 'Students'],
                ['type' => 'teachers', 'label' => 'Teachers'],
                ['type' => 'classes', 'label' => 'Classes'],
                ['type' => 'exams', 'label' => 'Exams'],
                ['type' => 'fees_today', 'label' => 'Today\'s Collection'],
                ['type' => 'fees_month', 'label' => 'Monthly Collection'],
                ['type' => 'outstanding', 'label' => 'Outstanding Fees'],
                ['type' => 'meetings', 'label' => 'Meetings'],
                ['type' => 'tickets', 'label' => 'Support Tickets'],
                ['type' => 'subjects', 'label' => 'Subjects'],
                ['type' => 'documents', 'label' => 'Documents'],
                ['type' => 'attendance', 'label' => 'Attendance'],
            ];

            $dashboard_permissions = [];
            foreach ($statTypes as $stat) {
                $permissions = Yii::$app->db->createCommand("
                    SELECT * FROM dashboard_permissions
                    WHERE role_id = :role_id
                    AND stat_type = :stat_type")
                    ->bindValue(':role_id', $id)
                    ->bindValue(':stat_type', $stat['type'])
                    ->queryOne();

                if (!$permissions) {
                    Yii::$app->db->createCommand()->insert('dashboard_permissions', [
                        'role_id' => $id,
                        'stat_type' => $stat['type'],
                        'is_visible' => 1,
                        'created_by' => Yii::$app->session->get('user_array')['id'] ?? 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ])->execute();
                    $permissions = Yii::$app->db->createCommand("
                        SELECT * FROM dashboard_permissions
                        WHERE role_id = :role_id
                        AND stat_type = :stat_type")
                        ->bindValue(':role_id', $id)
                        ->bindValue(':stat_type', $stat['type'])
                        ->queryOne();
                }

                $dashboard_permissions[] = [
                    'stat_type' => $stat['type'],
                    'label' => $stat['label'],
                    'permission_id' => $permissions['permission_id'] ?? null,
                    'is_visible' => (int) ($permissions['is_visible'] ?? 1),
                ];
            }

            $returnList = [
                'role_id' => $id,
                'role_name' => $role['name'],
                'dashboard_permissions' => $dashboard_permissions,
            ];

            return $this->render('dashboard_permissions', [
                'permissions' => $returnList,
            ]);
        } else {
            Yii::$app->session->setFlash('toast', 'Error! You do not have permissions');
            return $this->redirect(['config/index', 'flag' => 'roles']);
        }
    }
    public function actionPermissions_copy($id)
    {

        $permissions = Yii::$app->db->createCommand(
            "SELECT p.id as permission_id,mf.id as feature_id, r.name as role_name, m.name as module_name, m.icon as module_icon, mf.name as feature_name,
        p.is_active, p.can_view, p.can_add, p.can_edit, p.can_delete, m.active
        FROM permissions p
        LEFT JOIN roles r on p.role_id = r.id
        LEFT JOIN modules_features mf on mf.id = p.feature_id
        LEFT JOIN modules m on m.id = p.module_id
        WHERE p.role_id = $id
        ORDER BY p.id"
        )->queryAll();

        $structuredPermissions = [];

        foreach ($permissions as $permission) {
            $roleName = $permission['role_name'];
            $moduleName = $permission['module_name'];
            $active =  $permission['active'];
            $moduleId = $permission['permission_id'];
            $feature_id = $permission['feature_id'];
            $moduleIcon = $permission['module_icon'];
            $featureName = $permission['feature_name'];
            if (!isset($structuredPermissions[$roleName])) {
                $structuredPermissions[$roleName] = [
                    'role_name' => $roleName,
                    'modules' => []
                ];
            }
            if (!isset($structuredPermissions[$roleName]['modules'][$moduleName])) {
                $structuredPermissions[$roleName]['modules'][$moduleName] = [
                    'module_name' => $moduleName,
                    'module_icon' => $moduleIcon,
                    'module_id' => $moduleId,
                    'active' => (int) $active,
                    'features' => []
                ];
            }
            $structuredPermissions[$roleName]['modules'][$moduleName]['features'][] = [
                'feature_name' => $featureName,
                'feature_id' => $feature_id,
                'is_active' => (int)$permission['is_active'],
                'view' => (int) $permission['can_view'],
                'add' => (int)$permission['can_add'],
                'update' => (int) $permission['can_edit'],
                'delete' => (int)$permission['can_delete'],
            ];
        }
        $structuredPermissions = array_values($structuredPermissions);

        if (empty($structuredPermissions)) {

            Yii::$app->session->setFlash('toast', 'Error! No defaults Permissions set for this Role.');
            return $this->redirect(['config/']);
        }

        return $this->render('permissions', [
            'permissions' => $structuredPermissions,
        ]);
    }
    public function actionUpdate()
    {
        $name = $_POST["name"]; // active, add, view, update, delete
        $status = (int)$_POST["status"]; // 0 or 1
        $id = (int)$_POST["id"]; // module ID or modules_features ID
        $type = (int)$_POST["type"]; // 1 for module, 2 for modules_features

        if ($type == 1) {
            // Get module name before update
            $moduleName = Yii::$app->db->createCommand("SELECT name FROM modules WHERE id = :id")
                ->bindValue(':id', $id)
                ->queryScalar();

            // Direct SQL query for updating module status
            $sql = "UPDATE modules SET active = $status WHERE id = $id";
            Yii::$app->db->createCommand($sql)->execute();

            // Log activity
            Yii::$app->Component->Activitylog(
                'Updated module status: ' . $moduleName . ' - ' . ($status ? 'Activated' : 'Deactivated'),
                'update',
                $id,
                'modules',
                ['module' => $moduleName, 'status' => $status ? 'active' : 'inactive']
            );

            Yii::$app->session->setFlash('success', 'Module status updated.');
        } elseif ($type == 2) {
            // Direct SQL query for updating permission in permissions table
            $sql = "UPDATE permissions SET $name = $status WHERE id = $id";
            Yii::$app->db->createCommand($sql)->execute();

            // Log activity
            Yii::$app->Component->Activitylog(
                'Updated permission: ' . $name . ' set to ' . ($status ? 'enabled' : 'disabled'),
                'update',
                $id,
                'modules',
                ['permission_type' => $name, 'status' => $status]
            );

            Yii::$app->session->setFlash('success', 'Permission updated.');
        } else {
            Yii::$app->session->setFlash('error', 'Invalid update requested.');
        }

        return "TRUE";
    }
    public function actionUpdate_reports()
    {
        $name = $_POST["name"]; // active, add, view, update, delete
        $status = (int)$_POST["status"]; // 0 or 1
        $id = (int)$_POST["permission_id"]; // permission ID 
        $type = (int)$_POST["type"]; // 1 for module, 2 for modules_features

        if ($type == 1) {
            // echo    $sql = "UPDATE reports SET active = $status WHERE id = $mod_id";
            // Yii::$app->db->createCommand($sql)->execute();
            // Yii::$app->session->setFlash('toas', 'Module status updated.');
        } elseif ($type == 2) {
            // Get report name before update
            $reportInfo = Yii::$app->db->createCommand("
                SELECT r.name 
                FROM reports_permissions rp
                LEFT JOIN reports r ON rp.report_id = r.id
                WHERE rp.id = :id
            ")->bindValue(':id', $id)->queryOne();

            // Direct SQL query for updating permission in permissions table
            $sql = "UPDATE reports_permissions SET $name = $status WHERE id = $id  ";
            Yii::$app->db->createCommand($sql)->execute();

            // Log activity
            Yii::$app->Component->Activitylog(
                'Updated report permission: ' . ($reportInfo['name'] ?? 'Unknown') . ' - ' . $name . ' set to ' . ($status ? 'enabled' : 'disabled'),
                'update',
                $id,
                'modules',
                ['report' => $reportInfo['name'] ?? 'Unknown', 'permission_type' => $name, 'status' => $status]
            );

            // Yii::$app->session->setFlash('success', 'Permission updated.');
        } else {
            Yii::$app->session->setFlash('error', 'Invalid update requested.');
            return "FALSE";
        }

        return "TRUE";
    }
    public function actionUpdate_dashboard()
    {
        $permission_id = $_POST["permission_id"] ?? null;
        $stat_type = $_POST["stat_type"] ?? '';
        $is_visible = (int)$_POST["is_visible"]; // 0 or 1
        $role_id = (int)$_POST["role"]; // role ID

        if (empty($stat_type)) {
            return "FALSE";
        }

        // Get stat label for logging
        $statLabel = $stat_type;
        $statTypes = [
            'students' => 'Students',
            'teachers' => 'Teachers',
            'classes' => 'Classes',
            'exams' => 'Exams',
            'fees_today' => 'Today\'s Collection',
            'fees_month' => 'Monthly Collection',
            'outstanding' => 'Outstanding Fees',
            'meetings' => 'Meetings',
            'tickets' => 'Support Tickets',
            'subjects' => 'Subjects',
            'documents' => 'Documents',
            'attendance' => 'Attendance',
        ];
        if (isset($statTypes[$stat_type])) {
            $statLabel = $statTypes[$stat_type];
        }

        if ($permission_id) {
            // Update existing permission
            $sql = "UPDATE dashboard_permissions SET is_visible = $is_visible, updated_by = " . (Yii::$app->session->get('user_array')['id'] ?? 1) . ", updated_at = NOW() WHERE permission_id = $permission_id";
            Yii::$app->db->createCommand($sql)->execute();
        } else {
            // Insert new permission
            Yii::$app->db->createCommand()->insert('dashboard_permissions', [
                'role_id' => $role_id,
                'stat_type' => $stat_type,
                'is_visible' => $is_visible,
                'created_by' => Yii::$app->session->get('user_array')['id'] ?? 1,
                'created_at' => date('Y-m-d H:i:s')
            ])->execute();
        }

        // Log activity
        Yii::$app->Component->Activitylog(
            'Updated dashboard permission: ' . $statLabel . ' - ' . ($is_visible ? 'enabled' : 'disabled'),
            'update',
            $permission_id ?? null,
            'dashboard',
            ['stat_type' => $stat_type, 'stat_label' => $statLabel, 'is_visible' => $is_visible, 'role_id' => $role_id]
        );

        return "TRUE";
    }
    public function actionUpdatepermission()
    {
        $role_id = Yii::$app->Component->CheckRole();

        if ($role_id != 1) {
            Yii::$app->session->setFlash('success', 'You do not have permissions for this action');
            return $this->redirect(['site/index']);
        }
        $name = $_POST["type"]; // active, add, view, update, delete
        $status = (int)$_POST["status"]; // 0 or 1
        $id = (int)$_POST["setting_id"]; // setting_id ID
        $role_id = (int)$_POST["role_id"]; // setting_id ID

        // Get setting and role name before update
        $info = Yii::$app->db->createCommand("
            SELECT ss.name as setting_name, r.name as role_name
            FROM setting_permissions sp
            LEFT JOIN system_settings ss ON sp.setting_id = ss.id
            LEFT JOIN roles r ON sp.role_id = r.id
            WHERE sp.id = :id
        ")->bindValue(':id', $id)->queryOne();

        echo    $sql = "UPDATE `setting_permissions` SET  $name = $status WHERE `id` = $id";
        Yii::$app->db->createCommand($sql)->execute();

        // Log activity
        Yii::$app->Component->Activitylog(
            'Updated setting permission for ' . ($info['role_name'] ?? 'Unknown Role') . ': ' . ($info['setting_name'] ?? 'Unknown Setting') . ' - ' . $name . ' set to ' . ($status ? 'enabled' : 'disabled'),
            'update',
            $id,
            'modules',
            ['setting' => $info['setting_name'] ?? 'Unknown', 'role' => $info['role_name'] ?? 'Unknown', 'permission_type' => $name, 'status' => $status]
        );

        return;
        // Yii::$app->session->setFlash('success', 'Permission updated.');
        // return $this->redirect(['modules/permissions', 'type' => 'settings', 'id' => $role_id]);
    }
    public function actionUpdateschoolpermission()
    {
        $role_id = Yii::$app->Component->CheckRole();

        if ($role_id != 1) {
            Yii::$app->session->setFlash('success', 'You do not have permissions for this action');
            return $this->redirect(['site/index']);
        }
        $name = $_POST["type"];
        $status = (int)$_POST["status"];
        $id = (int)$_POST["school_id"];
        $role_id = (int)$_POST["role_id"];

        // Get school and role name before update
        $info = Yii::$app->db->createCommand("
            SELECT s.school_name, r.name as role_name
            FROM school_permissions sp
            LEFT JOIN school s ON sp.school_id = s.school_id
            LEFT JOIN roles r ON sp.role_id = r.id
            WHERE sp.id = :id
        ")->bindValue(':id', $id)->queryOne();

        echo    $sql = "UPDATE `school_permissions` SET  $name = $status WHERE `id` = $id";
        Yii::$app->db->createCommand($sql)->execute();

        // Log activity
        Yii::$app->Component->Activitylog(
            'Updated school permission for ' . ($info['role_name'] ?? 'Unknown Role') . ': ' . ($info['school_name'] ?? 'Unknown School') . ' - ' . $name . ' set to ' . ($status ? 'enabled' : 'disabled'),
            'update',
            $id,
            'modules',
            ['school' => $info['school_name'] ?? 'Unknown', 'role' => $info['role_name'] ?? 'Unknown', 'permission_type' => $name, 'status' => $status]
        );

        return;
    }
}

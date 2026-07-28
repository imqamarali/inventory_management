<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class ProductsController extends Controller
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
                        'actions' => ['fetchexternaldata', 'injectbulkdata'],
                        'roles' => ['?', '@'],
                    ],
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
        // Set JSON format for AJAX endpoints
        $ajaxActions = ['fetchexternaldata', 'injectbulkdata'];
        if (in_array($action->id, $ajaxActions)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        // Check user session for other actions
        if (!in_array($action->id, $ajaxActions) && Yii::$app->session->has('user_array') == NULL) {
            $this->redirect(['site/index']);
            return false;
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


  
    public function actionProducts()
    {
        $modules = [
            ['name' => 'Product Dashboard', 'controller' => 'products/productdashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Load Products Data', 'controller' => 'products/loadproductsdata', 'icon' => 'fa fa-upload'],
            ['name' => 'Categories', 'controller' => 'products/categories', 'icon' => 'fa fa-tags'],
            ['name' => 'Brands', 'controller' => 'products/brands', 'icon' => 'fa fa-certificate'],
            ['name' => 'Units', 'controller' => 'products/units', 'icon' => 'fa fa-balance-scale'],
            ['name' => 'Vehicle Makes', 'controller' => 'products/vehiclemakes', 'icon' => 'fa fa-car'],
            ['name' => 'Vehicle Models', 'controller' => 'products/vehiclemodels', 'icon' => 'fa fa-car'],
            ['name' => 'Product List', 'controller' => 'products/productlist', 'icon' => 'fa fa-cubes'],
        ];

        return $this->render('index', compact('modules'));
    }

    /**
     * Fetch External Data - Get data from internet sources (Makes, Models, Brands, Categories, Units)
     */
    public function actionFetchexternaldata()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $type = $post['type'] ?? null;

        if (!$type) {
            return $this->jsonResponse(false, 'Data type is required');
        }

        $dataService = new \app\models\ExternalDataService();

        try {
            $data = [];
            switch ($type) {
                case 'makes':
                    $data = $dataService->getVehicleMakes();
                    break;
                case 'brands':
                    $data = $dataService->getAutomotiveBrands();
                    break;
                case 'categories':
                    $data = $dataService->getAutoPartsCategories();
                    break;
                case 'units':
                    $data = $dataService->getStandardUnits();
                    break;
                case 'models':
                    $makeId = $post['make_id'] ?? null;
                    if (!$makeId) {
                        return $this->jsonResponse(false, 'Make ID is required for models');
                    }
                    // Get make name first
                    $make = Yii::$app->db->createCommand(
                        "SELECT make_name FROM inventory_vehicle_makes WHERE id = :id"
                    )->bindValue(':id', $makeId)->queryOne();

                    if (!$make) {
                        return $this->jsonResponse(false, 'Selected make not found');
                    }

                    $data = $dataService->getModelsForMake($make['make_name']);
                    break;
                default:
                    return $this->jsonResponse(false, 'Invalid data type');
            }

            return $this->jsonResponse(true, 'Data fetched successfully', [
                'data' => $data,
                'count' => count($data),
                'type' => $type
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error fetching data: ' . $e->getMessage());
        }
    }

    /**
     * Inject Bulk Data - Save multiple records to database with progress tracking
     */
    public function actionInjectbulkdata()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $type = $post['type'] ?? null;
        $data = json_decode($post['data'] ?? '[]', true);
        $selectedIndices = json_decode($post['selected'] ?? '[]', true);

        if (!$type || empty($data) || empty($selectedIndices)) {
            return $this->jsonResponse(false, 'Type, data, and selected items are required');
        }

        $userId = $this->currentUserId();
        $results = [
            'total' => count($selectedIndices),
            'inserted' => 0,
            'skipped' => 0,
            'errors' => 0,
            'messages' => [],
            'progress' => 0
        ];

        try {
            foreach ($selectedIndices as $index) {
                if (!isset($data[$index])) {
                    continue;
                }

                $item = $data[$index];
                $error = '';

                try {
                    switch ($type) {
                        case 'makes':
                            $error = $this->insertVehicleMake($item, $userId);
                            break;
                        case 'brands':
                            $error = $this->insertBrand($item, $userId);
                            break;
                        case 'categories':
                            $error = $this->insertCategory($item, $userId);
                            break;
                        case 'units':
                            $error = $this->insertUnit($item, $userId);
                            break;
                        case 'models':
                            $error = $this->insertVehicleModel($item, $userId, $post['make_id'] ?? null);
                            break;
                    }

                    if ($error) {
                        $results['skipped']++;
                        $results['messages'][] = "Skipped: " . $item['make_name'] ?? $item['brand_name'] ?? $item['category_name'] ?? $item['unit_name'] ?? 'Unknown' . " - $error";
                    } else {
                        $results['inserted']++;
                    }
                } catch (\Exception $e) {
                    $results['errors']++;
                    $results['messages'][] = "Error: " . $e->getMessage();
                }

                $results['progress'] = intval(($results['inserted'] + $results['skipped'] + $results['errors']) / $results['total'] * 100);
            }

            return $this->jsonResponse(true, "Injection complete: {$results['inserted']} inserted, {$results['skipped']} skipped, {$results['errors']} errors", $results);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error during bulk injection: ' . $e->getMessage());
        }
    }

    private function insertVehicleMake($item, $userId)
    {
        $makeName = trim($item['make_name'] ?? '');
        if (empty($makeName)) {
            return 'Missing make name';
        }

        // Check if already exists
        $exists = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM inventory_vehicle_makes WHERE make_name = :name AND is_deleted = 0"
        )->bindValue(':name', $makeName)->queryScalar();

        if ($exists) {
            return 'Already exists';
        }

        try {
            Yii::$app->db->createCommand()->insert('inventory_vehicle_makes', [
                'make_name' => $makeName,
                'make_code' => trim($item['make_code'] ?? ''),
                'country' => trim($item['country'] ?? ''),
                'website' => trim($item['website'] ?? ''),
                'created_by' => $userId,
                'is_active' => 1,
                'is_deleted' => 0,
            ])->execute();
            return '';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function insertBrand($item, $userId)
    {
        $brandName = trim($item['brand_name'] ?? '');
        if (empty($brandName)) {
            return 'Missing brand name';
        }

        $exists = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM inventory_brands WHERE brand_name = :name AND is_deleted = 0"
        )->bindValue(':name', $brandName)->queryScalar();

        if ($exists) {
            return 'Already exists';
        }

        try {
            Yii::$app->db->createCommand()->insert('inventory_brands', [
                'brand_name' => $brandName,
                'brand_code' => trim($item['brand_code'] ?? ''),
                'website' => trim($item['website'] ?? ''),
                'email' => trim($item['email'] ?? ''),
                'phone' => trim($item['phone'] ?? ''),
                'created_by' => $userId,
                'is_active' => 1,
                'is_deleted' => 0,
            ])->execute();
            return '';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function insertCategory($item, $userId)
    {
        $categoryName = trim($item['category_name'] ?? '');
        if (empty($categoryName)) {
            return 'Missing category name';
        }

        $exists = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM inventory_categories WHERE category_name = :name AND is_deleted = 0"
        )->bindValue(':name', $categoryName)->queryScalar();

        if ($exists) {
            return 'Already exists';
        }

        try {
            Yii::$app->db->createCommand()->insert('inventory_categories', [
                'category_name' => $categoryName,
                'category_code' => trim($item['category_code'] ?? ''),
                'parent_id' => $item['parent_id'] ?? null,
                'description' => trim($item['description'] ?? ''),
                'created_by' => $userId,
                'is_active' => 1,
                'is_deleted' => 0,
            ])->execute();
            return '';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function insertUnit($item, $userId)
    {
        $unitName = trim($item['unit_name'] ?? '');
        $shortName = trim($item['short_name'] ?? '');

        if (empty($unitName) || empty($shortName)) {
            return 'Missing unit or short name';
        }

        $exists = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM inventory_units WHERE unit_name = :name AND is_deleted = 0"
        )->bindValue(':name', $unitName)->queryScalar();

        if ($exists) {
            return 'Already exists';
        }

        try {
            Yii::$app->db->createCommand()->insert('inventory_units', [
                'unit_name' => $unitName,
                'short_name' => $shortName,
                'description' => trim($item['description'] ?? ''),
                'created_by' => $userId,
                'is_active' => 1,
                'is_deleted' => 0,
            ])->execute();
            return '';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function insertVehicleModel($item, $userId, $makeId)
    {
        $modelName = trim($item['model_name'] ?? '');
        if (empty($modelName) || !$makeId) {
            return 'Missing model name or make ID';
        }

        // Verify make exists
        $makeExists = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM inventory_vehicle_makes WHERE id = :id AND is_deleted = 0"
        )->bindValue(':id', $makeId)->queryScalar();

        if (!$makeExists) {
            return 'Make not found';
        }

        // Check if model already exists for this make
        $exists = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM inventory_vehicle_models
             WHERE make_id = :make_id AND model_name = :model_name AND is_deleted = 0"
        )->bindValues([':make_id' => $makeId, ':model_name' => $modelName])->queryScalar();

        if ($exists) {
            return 'Already exists';
        }

        try {
            Yii::$app->db->createCommand()->insert('inventory_vehicle_models', [
                'make_id' => $makeId,
                'model_name' => $modelName,
                'model_code' => trim($item['model_code'] ?? ''),
                'model_year' => trim($item['model_year'] ?? ''),
                'engine_type' => trim($item['engine_type'] ?? ''),
                'engine_capacity' => trim($item['engine_capacity'] ?? ''),
                'fuel_type' => $item['fuel_type'] ?? 'Petrol',
                'transmission' => $item['transmission'] ?? 'Manual',
                'created_by' => $userId,
                'is_active' => 1,
                'is_deleted' => 0,
            ])->execute();
            return '';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Load Products Data - Real-time insertion for Categories, Brands, Units, Makes & Models
     */
    public function actionLoadproductsdata()
    {
        if (Yii::$app->request->isGet) {
            // Load initial data for displaying in view
            $categories = Yii::$app->db->createCommand(
                "SELECT id, parent_id, category_name, category_code, is_active
                 FROM inventory_categories
                 WHERE is_deleted = 0
                 ORDER BY parent_id, category_name"
            )->queryAll();

            $brands = Yii::$app->db->createCommand(
                "SELECT id, brand_name, brand_code, is_active
                 FROM inventory_brands
                 WHERE is_deleted = 0
                 ORDER BY brand_name"
            )->queryAll();

            $units = Yii::$app->db->createCommand(
                "SELECT id, unit_name, short_name, is_active
                 FROM inventory_units
                 WHERE is_deleted = 0
                 ORDER BY unit_name"
            )->queryAll();

            $makes = Yii::$app->db->createCommand(
                "SELECT id, make_name, make_code, country, is_active
                 FROM inventory_vehicle_makes
                 WHERE is_deleted = 0
                 ORDER BY make_name"
            )->queryAll();

            $models = Yii::$app->db->createCommand(
                "SELECT vm.id, vm.make_id, vm.model_name, vm.model_code, vm.model_year,
                        vm.engine_type, vm.fuel_type, vm.transmission,
                        m.make_name, vm.is_active
                 FROM inventory_vehicle_models vm
                 LEFT JOIN inventory_vehicle_makes m ON m.id = vm.make_id
                 WHERE vm.is_deleted = 0
                 ORDER BY m.make_name, vm.model_name"
            )->queryAll();

            return $this->renderPartial('loadproductsdata_partial');
        }

        // AJAX POST requests for real-time data insertion
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $action = $post['action'] ?? null;
        $userId = $this->currentUserId();

        try {
            switch ($action) {
                case 'add_category':
                    return $this->addCategory($post, $userId);
                case 'add_brand':
                    return $this->addBrand($post, $userId);
                case 'add_unit':
                    return $this->addUnit($post, $userId);
                case 'add_make':
                    return $this->addVehicleMake($post, $userId);
                case 'add_model':
                    return $this->addVehicleModel($post, $userId);
                case 'update_category':
                    return $this->updateCategory($post, $userId);
                case 'update_brand':
                    return $this->updateBrand($post, $userId);
                case 'update_unit':
                    return $this->updateUnit($post, $userId);
                case 'update_make':
                    return $this->updateVehicleMake($post, $userId);
                case 'update_model':
                    return $this->updateVehicleModel($post, $userId);
                case 'delete_category':
                    return $this->deleteCategory($post);
                case 'delete_brand':
                    return $this->deleteBrand($post);
                case 'delete_unit':
                    return $this->deleteUnit($post);
                case 'delete_make':
                    return $this->deleteVehicleMake($post);
                case 'delete_model':
                    return $this->deleteVehicleModel($post);
                case 'get_data':
                    return $this->getTableData($post);
                default:
                    return $this->jsonResponse(false, 'Invalid action');
            }
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }

    // ========== CATEGORY FUNCTIONS ==========
    private function addCategory($post, $userId)
    {
        $categoryName = trim($post['category_name'] ?? '');
        $categoryCode = trim($post['category_code'] ?? '');
        $parentId = $post['parent_id'] ?? null;
        $description = $post['description'] ?? '';

        if (empty($categoryName)) {
            return $this->jsonResponse(false, 'Category name is required');
        }

        // Check for duplicate code
        if (!empty($categoryCode)) {
            $exists = Yii::$app->db->createCommand(
                "SELECT COUNT(*) FROM inventory_categories WHERE category_code = :code AND is_deleted = 0"
            )->bindValue(':code', $categoryCode)->queryScalar();

            if ($exists) {
                return $this->jsonResponse(false, 'Category code already exists');
            }
        }

        Yii::$app->db->createCommand()->insert('inventory_categories', [
            'category_name' => $categoryName,
            'category_code' => $categoryCode ?: null,
            'parent_id' => $parentId ?: null,
            'description' => $description,
            'created_by' => $userId,
            'is_active' => 1,
            'is_deleted' => 0,
        ])->execute();

        return $this->jsonResponse(true, 'Category added successfully', [
            'id' => Yii::$app->db->lastInsertID
        ]);
    }

    private function updateCategory($post, $userId)
    {
        $id = $post['id'] ?? null;
        $categoryName = trim($post['category_name'] ?? '');
        $categoryCode = trim($post['category_code'] ?? '');
        $parentId = $post['parent_id'] ?? null;
        $description = $post['description'] ?? '';
        $isActive = $post['is_active'] ?? 1;

        if (!$id || empty($categoryName)) {
            return $this->jsonResponse(false, 'Category ID and name are required');
        }

        Yii::$app->db->createCommand()->update('inventory_categories', [
            'category_name' => $categoryName,
            'category_code' => $categoryCode ?: null,
            'parent_id' => $parentId ?: null,
            'description' => $description,
            'updated_by' => $userId,
            'is_active' => $isActive,
        ], 'id = :id', [':id' => $id])->execute();

        return $this->jsonResponse(true, 'Category updated successfully');
    }

    private function deleteCategory($post)
    {
        $id = $post['id'] ?? null;
        if (!$id) {
            return $this->jsonResponse(false, 'Category ID is required');
        }

        Yii::$app->db->createCommand()->update('inventory_categories', [
            'is_deleted' => 1,
        ], 'id = :id', [':id' => $id])->execute();

        return $this->jsonResponse(true, 'Category deleted successfully');
    }

    // ========== BRAND FUNCTIONS ==========
    private function addBrand($post, $userId)
    {
        $brandName = trim($post['brand_name'] ?? '');
        $brandCode = trim($post['brand_code'] ?? '');
        $website = trim($post['website'] ?? '');
        $email = trim($post['email'] ?? '');
        $phone = trim($post['phone'] ?? '');

        if (empty($brandName)) {
            return $this->jsonResponse(false, 'Brand name is required');
        }

        if (!empty($brandCode)) {
            $exists = Yii::$app->db->createCommand(
                "SELECT COUNT(*) FROM inventory_brands WHERE brand_code = :code AND is_deleted = 0"
            )->bindValue(':code', $brandCode)->queryScalar();

            if ($exists) {
                return $this->jsonResponse(false, 'Brand code already exists');
            }
        }

        Yii::$app->db->createCommand()->insert('inventory_brands', [
            'brand_name' => $brandName,
            'brand_code' => $brandCode ?: null,
            'website' => !empty($website) ? $website : null,
            'email' => !empty($email) ? $email : null,
            'phone' => !empty($phone) ? $phone : null,
            'created_by' => $userId,
            'is_active' => 1,
            'is_deleted' => 0,
        ])->execute();

        return $this->jsonResponse(true, 'Brand added successfully', [
            'id' => Yii::$app->db->lastInsertID
        ]);
    }

    private function updateBrand($post, $userId)
    {
        $id = $post['id'] ?? null;
        $brandName = trim($post['brand_name'] ?? '');
        $brandCode = trim($post['brand_code'] ?? '');
        $website = trim($post['website'] ?? '');
        $email = trim($post['email'] ?? '');
        $phone = trim($post['phone'] ?? '');
        $isActive = $post['is_active'] ?? 1;

        if (!$id || empty($brandName)) {
            return $this->jsonResponse(false, 'Brand ID and name are required');
        }

        Yii::$app->db->createCommand()->update('inventory_brands', [
            'brand_name' => $brandName,
            'brand_code' => $brandCode ?: null,
            'website' => !empty($website) ? $website : null,
            'email' => !empty($email) ? $email : null,
            'phone' => !empty($phone) ? $phone : null,
            'updated_by' => $userId,
            'is_active' => $isActive,
        ], 'id = :id', [':id' => $id])->execute();

        return $this->jsonResponse(true, 'Brand updated successfully');
    }

    private function deleteBrand($post)
    {
        $id = $post['id'] ?? null;
        if (!$id) {
            return $this->jsonResponse(false, 'Brand ID is required');
        }

        Yii::$app->db->createCommand()->update('inventory_brands', [
            'is_deleted' => 1,
        ], 'id = :id', [':id' => $id])->execute();

        return $this->jsonResponse(true, 'Brand deleted successfully');
    }

    // ========== UNIT FUNCTIONS ==========
    private function addUnit($post, $userId)
    {
        $unitName = trim($post['unit_name'] ?? '');
        $shortName = trim($post['short_name'] ?? '');
        $description = $post['description'] ?? '';

        if (empty($unitName) || empty($shortName)) {
            return $this->jsonResponse(false, 'Unit name and short name are required');
        }

        Yii::$app->db->createCommand()->insert('inventory_units', [
            'unit_name' => $unitName,
            'short_name' => $shortName,
            'description' => $description,
            'created_by' => $userId,
            'is_active' => 1,
            'is_deleted' => 0,
        ])->execute();

        return $this->jsonResponse(true, 'Unit added successfully', [
            'id' => Yii::$app->db->lastInsertID
        ]);
    }

    private function updateUnit($post, $userId)
    {
        $id = $post['id'] ?? null;
        $unitName = trim($post['unit_name'] ?? '');
        $shortName = trim($post['short_name'] ?? '');
        $description = $post['description'] ?? '';
        $isActive = $post['is_active'] ?? 1;

        if (!$id || empty($unitName) || empty($shortName)) {
            return $this->jsonResponse(false, 'Unit ID, name and short name are required');
        }

        Yii::$app->db->createCommand()->update('inventory_units', [
            'unit_name' => $unitName,
            'short_name' => $shortName,
            'description' => $description,
            'updated_by' => $userId,
            'is_active' => $isActive,
        ], 'id = :id', [':id' => $id])->execute();

        return $this->jsonResponse(true, 'Unit updated successfully');
    }

    private function deleteUnit($post)
    {
        $id = $post['id'] ?? null;
        if (!$id) {
            return $this->jsonResponse(false, 'Unit ID is required');
        }

        Yii::$app->db->createCommand()->update('inventory_units', [
            'is_deleted' => 1,
        ], 'id = :id', [':id' => $id])->execute();

        return $this->jsonResponse(true, 'Unit deleted successfully');
    }

    // ========== VEHICLE MAKE FUNCTIONS ==========
    private function addVehicleMake($post, $userId)
    {
        $makeName = trim($post['make_name'] ?? '');
        $makeCode = trim($post['make_code'] ?? '');
        $country = trim($post['country'] ?? '');
        $website = trim($post['website'] ?? '');

        if (empty($makeName)) {
            return $this->jsonResponse(false, 'Make name is required');
        }

        if (!empty($makeCode)) {
            $exists = Yii::$app->db->createCommand(
                "SELECT COUNT(*) FROM inventory_vehicle_makes WHERE make_code = :code AND is_deleted = 0"
            )->bindValue(':code', $makeCode)->queryScalar();

            if ($exists) {
                return $this->jsonResponse(false, 'Make code already exists');
            }
        }

        Yii::$app->db->createCommand()->insert('inventory_vehicle_makes', [
            'make_name' => $makeName,
            'make_code' => $makeCode ?: null,
            'country' => !empty($country) ? $country : null,
            'website' => !empty($website) ? $website : null,
            'created_by' => $userId,
            'is_active' => 1,
            'is_deleted' => 0,
        ])->execute();

        return $this->jsonResponse(true, 'Vehicle Make added successfully', [
            'id' => Yii::$app->db->lastInsertID
        ]);
    }

    private function updateVehicleMake($post, $userId)
    {
        $id = $post['id'] ?? null;
        $makeName = trim($post['make_name'] ?? '');
        $makeCode = trim($post['make_code'] ?? '');
        $country = trim($post['country'] ?? '');
        $website = trim($post['website'] ?? '');
        $isActive = $post['is_active'] ?? 1;

        if (!$id || empty($makeName)) {
            return $this->jsonResponse(false, 'Make ID and name are required');
        }

        Yii::$app->db->createCommand()->update('inventory_vehicle_makes', [
            'make_name' => $makeName,
            'make_code' => $makeCode ?: null,
            'country' => !empty($country) ? $country : null,
            'website' => !empty($website) ? $website : null,
            'updated_by' => $userId,
            'is_active' => $isActive,
        ], 'id = :id', [':id' => $id])->execute();

        return $this->jsonResponse(true, 'Vehicle Make updated successfully');
    }

    private function deleteVehicleMake($post)
    {
        $id = $post['id'] ?? null;
        if (!$id) {
            return $this->jsonResponse(false, 'Make ID is required');
        }

        Yii::$app->db->createCommand()->update('inventory_vehicle_makes', [
            'is_deleted' => 1,
        ], 'id = :id', [':id' => $id])->execute();

        return $this->jsonResponse(true, 'Vehicle Make deleted successfully');
    }

    // ========== VEHICLE MODEL FUNCTIONS ==========
    private function addVehicleModel($post, $userId)
    {
        $makeId = $post['make_id'] ?? null;
        $modelName = trim($post['model_name'] ?? '');
        $modelCode = trim($post['model_code'] ?? '');
        $modelYear = trim($post['model_year'] ?? '');
        $engineType = trim($post['engine_type'] ?? '');
        $engineCapacity = trim($post['engine_capacity'] ?? '');
        $fuelType = $post['fuel_type'] ?? 'Petrol';
        $transmission = $post['transmission'] ?? 'Manual';

        if (!$makeId || empty($modelName)) {
            return $this->jsonResponse(false, 'Make ID and model name are required');
        }

        // Verify make exists
        $makeExists = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM inventory_vehicle_makes WHERE id = :id AND is_deleted = 0"
        )->bindValue(':id', $makeId)->queryScalar();

        if (!$makeExists) {
            return $this->jsonResponse(false, 'Selected make does not exist');
        }

        Yii::$app->db->createCommand()->insert('inventory_vehicle_models', [
            'make_id' => $makeId,
            'model_name' => $modelName,
            'model_code' => $modelCode ?: null,
            'model_year' => $modelYear ?: null,
            'engine_type' => !empty($engineType) ? $engineType : null,
            'engine_capacity' => !empty($engineCapacity) ? $engineCapacity : null,
            'fuel_type' => $fuelType,
            'transmission' => $transmission,
            'created_by' => $userId,
            'is_active' => 1,
            'is_deleted' => 0,
        ])->execute();

        return $this->jsonResponse(true, 'Vehicle Model added successfully', [
            'id' => Yii::$app->db->lastInsertID
        ]);
    }

    private function updateVehicleModel($post, $userId)
    {
        $id = $post['id'] ?? null;
        $makeId = $post['make_id'] ?? null;
        $modelName = trim($post['model_name'] ?? '');
        $modelCode = trim($post['model_code'] ?? '');
        $modelYear = trim($post['model_year'] ?? '');
        $engineType = trim($post['engine_type'] ?? '');
        $engineCapacity = trim($post['engine_capacity'] ?? '');
        $fuelType = $post['fuel_type'] ?? 'Petrol';
        $transmission = $post['transmission'] ?? 'Manual';
        $isActive = $post['is_active'] ?? 1;

        if (!$id || !$makeId || empty($modelName)) {
            return $this->jsonResponse(false, 'Model ID, Make ID and name are required');
        }

        // Verify make exists
        $makeExists = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM inventory_vehicle_makes WHERE id = :id AND is_deleted = 0"
        )->bindValue(':id', $makeId)->queryScalar();

        if (!$makeExists) {
            return $this->jsonResponse(false, 'Selected make does not exist');
        }

        Yii::$app->db->createCommand()->update('inventory_vehicle_models', [
            'make_id' => $makeId,
            'model_name' => $modelName,
            'model_code' => $modelCode ?: null,
            'model_year' => $modelYear ?: null,
            'engine_type' => !empty($engineType) ? $engineType : null,
            'engine_capacity' => !empty($engineCapacity) ? $engineCapacity : null,
            'fuel_type' => $fuelType,
            'transmission' => $transmission,
            'updated_by' => $userId,
            'is_active' => $isActive,
        ], 'id = :id', [':id' => $id])->execute();

        return $this->jsonResponse(true, 'Vehicle Model updated successfully');
    }

    private function deleteVehicleModel($post)
    {
        $id = $post['id'] ?? null;
        if (!$id) {
            return $this->jsonResponse(false, 'Model ID is required');
        }

        Yii::$app->db->createCommand()->update('inventory_vehicle_models', [
            'is_deleted' => 1,
        ], 'id = :id', [':id' => $id])->execute();

        return $this->jsonResponse(true, 'Vehicle Model deleted successfully');
    }

    // ========== GET DATA FUNCTION ==========
    private function getTableData($post)
    {
        $table = $post['table'] ?? null;
        $page = (int)($post['page'] ?? 1);
        $limit = (int)($post['limit'] ?? 15);
        $offset = ($page - 1) * $limit;

        if (!$table) {
            return $this->jsonResponse(false, 'Table name is required');
        }

        $data = [];
        $total = 0;

        switch ($table) {
            case 'categories':
                $total = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_categories WHERE is_deleted = 0"
                )->queryScalar();
                $data = Yii::$app->db->createCommand(
                    "SELECT * FROM inventory_categories WHERE is_deleted = 0
                     ORDER BY parent_id, category_name LIMIT $offset, $limit"
                )->queryAll();
                break;
            case 'brands':
                $total = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_brands WHERE is_deleted = 0"
                )->queryScalar();
                $data = Yii::$app->db->createCommand(
                    "SELECT * FROM inventory_brands WHERE is_deleted = 0
                     ORDER BY brand_name LIMIT $offset, $limit"
                )->queryAll();
                break;
            case 'units':
                $total = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_units WHERE is_deleted = 0"
                )->queryScalar();
                $data = Yii::$app->db->createCommand(
                    "SELECT * FROM inventory_units WHERE is_deleted = 0
                     ORDER BY unit_name LIMIT $offset, $limit"
                )->queryAll();
                break;
            case 'makes':
                $total = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_vehicle_makes WHERE is_deleted = 0"
                )->queryScalar();
                $data = Yii::$app->db->createCommand(
                    "SELECT * FROM inventory_vehicle_makes WHERE is_deleted = 0
                     ORDER BY make_name LIMIT $offset, $limit"
                )->queryAll();
                break;
            case 'models':
                $total = Yii::$app->db->createCommand(
                    "SELECT * FROM inventory_vehicle_models WHERE is_deleted = 0"
                )->queryScalar();
                $data = Yii::$app->db->createCommand(
                    "SELECT vm.*, m.make_name FROM inventory_vehicle_models vm
                     LEFT JOIN inventory_vehicle_makes m ON m.id = vm.make_id
                     WHERE vm.is_deleted = 0
                     ORDER BY m.make_name, vm.model_name LIMIT $offset, $limit"
                )->queryAll();
                break;
        }

        return $this->jsonResponse(true, 'Data retrieved successfully', [
            'data' => $data,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ]);
    }

    public function actionProductdashboard()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('productdashboard');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();

            // Handle truncate all (products + master data)
            if (isset($post['flag']) && $post['flag'] == 'truncate_all') {
                // Only allow Super Admin to truncate
                $roleId = Yii::$app->session->get('user_array')['role_id'] ?? null;
                if ($roleId !== 1) {
                    return ['success' => false, 'message' => 'Unauthorized. Only Super Admin can truncate records.'];
                }

                $password = Yii::$app->request->post('password', '');
                $user_id = Yii::$app->user->id ?? 1;

                // Verify password against admin user
                $admin = Yii::$app->db->createCommand(
                    "SELECT password FROM system_users WHERE id = :id AND is_active = 1",
                    [':id' => $user_id]
                )->queryOne();

                if (!$admin) {
                    return ['success' => false, 'message' => 'User not found'];
                }

                // Verify password using bcrypt
                if (!password_verify($password, $admin['password'])) {
                    return ['success' => false, 'message' => 'Invalid password'];
                }

                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();
                $deletedRecords = [];

                try {
                    // Disable foreign key checks temporarily
                    $db->createCommand("SET FOREIGN_KEY_CHECKS=0")->execute();

                    // Delete in correct order (respecting all foreign key relationships)

                    // TIER 1: POS Related (no dependencies)
                    try { $db->createCommand("DELETE FROM inventory_pos_payment_history")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_pos_items")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_pos_transactions")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_pos_sales")->execute(); } catch (\Exception $e) {}

                    // TIER 2: Purchase Returns (child of purchases)
                    try { $db->createCommand("DELETE FROM inventory_purchase_return_items")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_purchase_returns")->execute(); } catch (\Exception $e) {}

                    // TIER 3: Purchase Invoices & Details (references purchases)
                    try { $db->createCommand("DELETE FROM inventory_purchase_invoice_payments")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_purchase_invoice_items")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_purchase_invoices")->execute(); } catch (\Exception $e) {}

                    // TIER 4: Purchase Orders & Items (parent of invoices)
                    try { $db->createCommand("DELETE FROM inventory_purchase_order_items")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_purchase_orders")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM purchase_order_items")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM purchase_orders")->execute(); } catch (\Exception $e) {}

                    // TIER 5: Sales Returns (child of sales)
                    try { $db->createCommand("DELETE FROM inventory_sales_returns")->execute(); } catch (\Exception $e) {}

                    // TIER 6: Sales Invoices & Details (references sales)
                    try { $db->createCommand("DELETE FROM inventory_sale_invoice_payments")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_sale_invoice_items")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_sales_invoice_payments")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_sales_invoice_items")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_sales_invoices")->execute(); } catch (\Exception $e) {}

                    // TIER 7: Sales Orders & Items (parent of invoices)
                    try { $db->createCommand("DELETE FROM inventory_sales_order_items")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_sales_orders")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM sales_order_items")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM sales_orders")->execute(); } catch (\Exception $e) {}

                    // TIER 8: Goods Receiving (references products)
                    try { $db->createCommand("DELETE FROM inventory_goods_receiving_items")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_goods_receiving")->execute(); } catch (\Exception $e) {}

                    // TIER 9: Stock Audits & Movements (references stock & products)
                    try { $db->createCommand("DELETE FROM inventory_stock_audit_items")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_stock_audits")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_stock_transfer_items")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_stock_transfers")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_stock_adjustment_items")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_stock_adjustments")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_stock_movements")->execute(); } catch (\Exception $e) {}

                    // TIER 10: Stock Records (references products)
                    try { $db->createCommand("DELETE FROM inventory_stock")->execute(); } catch (\Exception $e) {}

                    // TIER 11: Transactions & Payments (references products)
                    try { $db->createCommand("DELETE FROM inventory_transactions")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_payments")->execute(); } catch (\Exception $e) {}

                    // TIER 12: Products (all references cleaned up)
                    try { $db->createCommand("DELETE FROM inventory_products")->execute(); } catch (\Exception $e) {}

                    // TIER 13: Master Data (Vehicle Models & Makes)
                    try { $db->createCommand("DELETE FROM inventory_vehicle_models")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_vehicle_makes")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_brands")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_units")->execute(); } catch (\Exception $e) {}
                    try { $db->createCommand("DELETE FROM inventory_categories")->execute(); } catch (\Exception $e) {}

                    // Reset auto-increment for ALL truncated tables
                    $autoIncrementTables = [
                        'inventory_categories', 'inventory_brands', 'inventory_units',
                        'inventory_vehicle_makes', 'inventory_vehicle_models',
                        'inventory_products', 'inventory_stock',
                        'inventory_stock_adjustments', 'inventory_stock_adjustment_items',
                        'inventory_stock_movements', 'inventory_stock_transfers', 'inventory_stock_transfer_items',
                        'inventory_stock_audits', 'inventory_stock_audit_items',
                        'inventory_purchase_orders', 'inventory_purchase_order_items',
                        'inventory_purchase_invoices', 'inventory_purchase_invoice_items', 'inventory_purchase_invoice_payments',
                        'inventory_purchase_returns', 'inventory_purchase_return_items',
                        'inventory_sales_orders', 'inventory_sales_order_items',
                        'inventory_sales_invoices', 'inventory_sales_invoice_items', 'inventory_sales_invoice_payments',
                        'inventory_sales_returns', 'inventory_sale_invoice_items', 'inventory_sale_invoice_payments',
                        'inventory_goods_receiving', 'inventory_goods_receiving_items',
                        'inventory_transactions', 'inventory_payments',
                        'inventory_pos_sales', 'inventory_pos_items', 'inventory_pos_transactions', 'inventory_pos_payment_history',
                        // Alternative table names
                        'purchase_orders', 'purchase_order_items', 'sales_orders', 'sales_order_items', 'sales_order_details'
                    ];

                    foreach ($autoIncrementTables as $table) {
                        try {
                            $db->createCommand("ALTER TABLE " . $table . " AUTO_INCREMENT = 1")->execute();
                        } catch (\Exception $e) {
                            // Table might not exist, continue
                        }
                    }

                    // Re-enable foreign key checks
                    $db->createCommand("SET FOREIGN_KEY_CHECKS=1")->execute();

                    $transaction->commit();

                    // Log the action
                    try {
                        $db->createCommand()->insert(
                            'activitylogs',
                            [
                                'activity' => 'Truncate All Data - Deleted all products, master data (categories, brands, units, makes, models), stock, purchase orders, and sales orders',
                                'activitytype' => 'Truncate',
                                'module' => 'Products',
                                'uid' => $user_id,
                                'ip_address' => Yii::$app->request->userIP,
                                'date' => date('Y-m-d'),
                                'datetime' => date('Y-m-d H:i:s')
                            ]
                        )->execute();
                    } catch (\Exception $e) {}

                    return [
                        'success' => true,
                        'message' => 'All products, master data (categories, brands, units, makes, models), stock, purchase orders, and sales orders have been truncated successfully.',
                        'details' => $deletedRecords
                    ];
                } catch (\Exception $e) {
                    try {
                        // Re-enable foreign key checks on error
                        $db->createCommand("SET FOREIGN_KEY_CHECKS=1")->execute();
                    } catch (\Exception $fkE) {
                        // Ignore FK re-enable errors
                    }
                    $transaction->rollBack();
                    return ['success' => false, 'message' => 'Error truncating data: ' . $e->getMessage()];
                }
            }

            if (!isset($post['flag']) || $post['flag'] != 'load_dashboard') {
                return [
                    'success' => false,
                    'message' => 'Invalid request.'
                ];
            }
            $db = Yii::$app->db;
            $stats = [];
            $stats['total_products'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_products WHERE is_deleted=0")->queryScalar();
            $stats['active_products'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_products WHERE is_deleted=0 AND is_active=1")->queryScalar();
            $stats['inactive_products'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_products WHERE is_deleted=0 AND is_active=0")->queryScalar();
            $stats['categories'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_categories WHERE is_deleted=0")->queryScalar();
            $stats['brands'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_brands WHERE is_deleted=0")->queryScalar();
            $stats['units'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_units WHERE is_deleted=0")->queryScalar();
            $stats['vehicle_makes'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_vehicle_makes WHERE is_deleted=0")->queryScalar();
            $stats['vehicle_models'] = (int)$db->createCommand("SELECT COUNT(*) FROM inventory_vehicle_models  WHERE is_deleted=0")->queryScalar();
            $stats['inventory_value'] = (float)$db->createCommand("SELECT IFNULL(SUM(purchase_price),0) FROM inventory_products WHERE is_deleted=0")->queryScalar();
            $stats['average_price'] = (float)$db->createCommand("SELECT IFNULL(SUM(selling_price),0) FROM inventory_products WHERE is_deleted=0")->queryScalar();

            $categoryChart = $db->createCommand("
                SELECT c.category_name, COUNT(p.id) total
                FROM inventory_categories c
                LEFT JOIN inventory_products p
                    ON p.category_id=c.id
                    AND p.is_deleted=0
                WHERE c.is_deleted=0
                GROUP BY c.id
                ORDER BY total DESC
            ")->queryAll();
            $brandChart = $db->createCommand("
                SELECT
                    b.brand_name,
                    COUNT(p.id) total
                FROM inventory_brands b
                LEFT JOIN inventory_products p
                    ON p.brand_id=b.id
                    AND p.is_deleted=0
                WHERE b.is_deleted=0
                GROUP BY b.id
                ORDER BY total DESC
            ")->queryAll();
            $monthlyProducts = $db->createCommand("
                SELECT
                    DATE_FORMAT(created_at,'%b %Y') month,
                    COUNT(*) total
                FROM inventory_products
                WHERE is_deleted=0
                GROUP BY YEAR(created_at),MONTH(created_at)
                ORDER BY YEAR(created_at),MONTH(created_at)
            ")->queryAll();
            $latestProducts = $db->createCommand("
                SELECT
                    p.id,
                    p.product_name,
                    p.sku,
                    p.selling_price,
                    c.category_name,
                    b.brand_name
                FROM inventory_products p
                LEFT JOIN inventory_categories c ON c.id=p.category_id
                LEFT JOIN inventory_brands b ON b.id=p.brand_id
                WHERE p.is_deleted=0
                ORDER BY p.created_at DESC
                LIMIT 10
            ")->queryAll();

            $recentUpdates = $db->createCommand("
                SELECT
                    id,
                    product_name,
                    updated_at
                FROM inventory_products
                WHERE is_deleted=0
                ORDER BY updated_at DESC
                LIMIT 10
            ")->queryAll();

            return [
                'success' => true,
                'stats' => $stats,
                'categoryChart' => $categoryChart,
                'brandChart' => $brandChart,
                'monthlyProducts' => $monthlyProducts,
                'latestProducts' => $latestProducts,
                'recentUpdates' => $recentUpdates
            ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    public function actionProductlist()
    {
        if (Yii::$app->request->isGet) {
            $name = trim(Yii::$app->request->get('product_name', ''));
            $sku = trim(Yii::$app->request->get('sku', ''));
            $category = Yii::$app->request->get('category_id', '');
            $brand = Yii::$app->request->get('brand_id', '');
            $model_id = Yii::$app->request->get('model_id', '');

            $perPage = (int)Yii::$app->request->get('per_page', 10);
            $page = max(1, (int)Yii::$app->request->get('page', 1));
            $offset = ($page - 1) * $perPage;

            $where = " WHERE p.is_deleted=0 ";
            $params = [];
            if ($name != '') {
                $where .= " AND p.product_name LIKE :name";
                $params[':name'] = "%{$name}%";
            }
            if ($sku != '') {
                $where .= " AND p.sku LIKE :sku";
                $params[':sku'] = "%{$sku}%";
            }
            if ($category != '') {
                $where .= " AND p.category_id=:category";
                $params[':category'] = $category;
            }
            if ($brand != '') {
                $where .= " AND p.brand_id=:brand";
                $params[':brand'] = $brand;
            }
            if ($model_id != '') {
                $where .= " AND p.model_id=:model_id";
                $params[':model_id'] = $model_id;
            }
            $total = Yii::$app->db->createCommand("
            SELECT COUNT(*)
            FROM inventory_products p
            $where
            ", $params)->queryScalar();
            $products = Yii::$app->db->createCommand("
                SELECT p.*,c.category_name,b.brand_name,
                CONCAT(m.model_name,',',m.model_year) as model_name,
                unn.unit_name
                FROM inventory_products p
                LEFT JOIN inventory_categories c ON c.id=p.category_id
                LEFT JOIN inventory_brands b ON b.id=p.brand_id
                LEFT JOIN inventory_vehicle_models m ON m.id=p.model_id
                LEFT JOIN inventory_units unn ON unn.id = p.unit_id
                $where
                ORDER BY p.id ASC
                LIMIT $offset,$perPage
            ")->queryAll();
            $totalPages = ceil($total / $perPage);
            $categories = Yii::$app->db->createCommand("SELECT id,category_name FROM inventory_categories WHERE is_deleted=0 ORDER BY category_name ASC")->queryAll();
            $brands = Yii::$app->db->createCommand("SELECT id,brand_name FROM inventory_brands WHERE is_deleted=0 ORDER BY brand_name ASC")->queryAll();
            $models = Yii::$app->db->createCommand("SELECT m.id,CONCAT(m.model_name,',',m.model_code,',',m.model_year,' | ', mk.make_name,',',mk.make_code) as model_name FROM inventory_vehicle_models m LEFT JOIN inventory_vehicle_makes mk ON m.make_id = mk.id WHERE m.is_deleted=0 ORDER BY m.model_name ASC;")->queryAll();
            $units = Yii::$app->db->createCommand("SELECT id,unit_name FROM inventory_units WHERE is_deleted=0 ORDER BY unit_name ASC")->queryAll();

            return $this->renderPartial(
                'productlist',
                [
                    'products' => $products,
                    'categories' => $categories,
                    'brands' => $brands,
                    'models' => $models,
                    'units' => $units,
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
                $name = trim($post['product_name'] ?? '');
                $sku = trim($post['sku'] ?? '');
                $categories = $post['category_id'] ?? [];
                $brands     = $post['brand_id'] ?? [];
                $model_id      = $post['model_id'] ?? [];
                $perPage = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                $page    = !empty($post['page']) ? (int)$post['page'] : 1;
                $offset  = ($page - 1) * $perPage;
                $where = " WHERE p.is_deleted=0 ";
                $params = [];
                if ($name != '') {
                    $where .= " AND p.product_name LIKE :name";
                    $params[':name'] = "%{$name}%";
                }
                if ($sku != '') {
                    $where .= " AND p.sku LIKE :sku";
                    $params[':sku'] = "%{$sku}%";
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
                if (!empty($model_id)) {
                    $placeholders = [];
                    foreach ($model_id as $k => $value) {
                        $key = ":model_id{$k}";
                        $placeholders[] = $key;
                        $params[$key] = $value;
                    }
                    $where .= " AND p.model_id IN (" . implode(',', $placeholders) . ")";
                }
                $total = Yii::$app->db->createCommand("SELECT COUNT(*) FROM inventory_products p $where", $params)->queryScalar();
                $products = Yii::$app->db->createCommand("
                    SELECT p.*,c.category_name,b.brand_name,
                    CONCAT(m.model_name,',',m.model_year) as model_name,
                    unn.unit_name
                    FROM inventory_products p
                    LEFT JOIN inventory_categories c ON c.id=p.category_id
                    LEFT JOIN inventory_brands b ON b.id=p.brand_id
                    LEFT JOIN inventory_vehicle_models m ON m.id=p.model_id
                    LEFT JOIN inventory_units unn ON unn.id = p.unit_id
                    $where
                    ORDER BY p.id ASC
                    LIMIT $offset,$perPage
                ", $params)->queryAll();
                return [
                    'success' => true,
                    'products' => $products,
                    'total' => (int)$total,
                    'page' => $page,
                    'per_page' => $perPage,
                    'total_pages' => ceil($total / $perPage)
                ];
            }

            $product = Yii::$app->request->post();
            $product_id = Yii::$app->request->post('id');

            if ($product_id && isset($product['delete']) && $product['delete'] == 1) {
                $result = Yii::$app->db->createCommand()
                    ->update('inventory_products', ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => Yii::$app->user->id ?? null], 'id=:id', [':id' => $product_id])
                    ->execute();
                return $result ? ['success' => true, 'message' => 'Product deleted successfully.'] : ['success' => false, 'message' => 'Failed to delete product.'];
            }

            if (empty($product['product_name'])) {
                return ['success' => false, 'message' => 'Product name is required.'];
            }

            $productData = [
                'category_id' => $product['category_id'] ?? null,
                'brand_id' => $product['brand_id'] ?? null,
                'model_id' => $product['model_id'] ?? null,
                'unit_id' => $product['unit_id'] ?? null,
                'product_name' => $product['product_name'],
                'sku' => $product['sku'] ?? null,
                'barcode' => $product['barcode'] ?? null,
                'description' => $product['description'] ?? null,
                'purchase_price' => $product['purchase_price'] ?? 0,
                'selling_price' => $product['selling_price'] ?? 0,
                'minimum_stock' => $product['minimum_stock'] ?? 0,
                'maximum_stock' => $product['maximum_stock'] ?? 0,
                'reorder_level' => $product['reorder_level'] ?? 0,
                'weight' => $product['weight'] ?? null,
                'length' => $product['length'] ?? null,
                'width' => $product['width'] ?? null,
                'height' => $product['height'] ?? null,
                'is_active' => isset($product['is_active']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Yii::$app->user->id ?? null
            ];

            if ($product_id) {
                $result = Yii::$app->db->createCommand()
                    ->update('inventory_products', $productData, 'id=:id', [':id' => $product_id])
                    ->execute();
                return $result ? ['success' => true, 'message' => 'Product updated successfully.'] : ['success' => false, 'message' => 'Failed to update product.'];
            }

            $productData['created_at'] = date('Y-m-d H:i:s');
            $productData['created_by'] = Yii::$app->user->id ?? null;
            $productData['is_deleted'] = 0;

            $result = Yii::$app->db->createCommand()
                ->insert('inventory_products', $productData)
                ->execute();

            return $result ? ['success' => true, 'message' => 'Product created successfully.'] : ['success' => false, 'message' => 'Failed to create product.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    public function actionCategories()
    {
        if (Yii::$app->request->isGet) {
            $categories = Yii::$app->db->createCommand("SELECT * FROM inventory_categories WHERE is_deleted = 0 ORDER BY id ASC")->queryAll();

            foreach ($categories as &$category) {
                $category['total_products'] = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_products WHERE category_id = :category_id AND is_deleted = 0",
                    [':category_id' => $category['id']]
                )->queryScalar();

                $category['active_products'] = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_products WHERE category_id = :category_id AND is_active = 1 AND is_deleted = 0",
                    [':category_id' => $category['id']]
                )->queryScalar();

                $category['inactive_products'] = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_products WHERE category_id = :category_id AND is_active = 0 AND is_deleted = 0",
                    [':category_id' => $category['id']]
                )->queryScalar();
            }

            return $this->renderPartial('categories', ['categories' => $categories]);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $category = Yii::$app->request->post();
            $category_id = Yii::$app->request->post('id');
            if ($category_id && isset($category['delete']) && $category['delete'] == 1) {
                $activeProductCount = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_products WHERE category_id = :category_id AND is_active = 1 AND is_deleted = 0",
                    [':category_id' => $category_id]
                )->queryScalar();

                if ($activeProductCount > 0) {
                    return [
                        'success' => false,
                        'message' => 'Cannot delete category with active products. Please deactivate or delete all active products first.'
                    ];
                }

                $result = Yii::$app->db->createCommand()
                    ->update(
                        'inventory_categories',
                        ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => Yii::$app->user->id ?? null],
                        'id = :id',
                        [':id' => $category_id]
                    )->execute();
                if ($result) {
                    \app\controllers\ActivitylogsController::logActivity(
                        'Deleted category: ' . $category['category_name'],
                        'delete',
                        $category_id,
                        'Products',
                        ['type' => 'category_delete']
                    );
                    return [
                        'success' => true,
                        'message' => 'Category deleted successfully.'
                    ];
                }
                return [
                    'success' => false,
                    'message' => 'Failed to delete category.'
                ];
            }
            if (empty($category['category_name'])) {
                return [
                    'success' => false,
                    'message' => 'Category name is required.'
                ];
            }
            if (empty($category['category_code'])) {
                return [
                    'success' => false,
                    'message' => 'Category code is required.'
                ];
            }
            $categoryData = [
                'parent_id' => $category['parent_id'] ?? null,
                'category_name' => $category['category_name'],
                'category_code' => $category['category_code'],
                'description' => $category['description'] ?? null,
                'is_active' => isset($category['is_active']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Yii::$app->user->id ?? null,
            ];
            if ($category_id) {
                $result = Yii::$app->db->createCommand()
                    ->update('inventory_categories', $categoryData, 'id = :id', [':id' => $category_id])
                    ->execute();
                if ($result) {
                    \app\controllers\ActivitylogsController::logActivity(
                        'Updated category: ' . $category['category_name'],
                        'update',
                        $category_id,
                        'Products',
                        [
                            'type' => 'category_update',
                            'category_code' => $category['category_code']
                        ]
                    );
                    return [
                        'success' => true,
                        'message' => 'Category updated successfully.'
                    ];
                }
                return [
                    'success' => false,
                    'message' => 'Failed to update category.'
                ];
            }
            $categoryData['created_at'] = date('Y-m-d H:i:s');
            $categoryData['created_by'] = Yii::$app->user->id ?? null;
            $categoryData['is_deleted'] = 0;
            $result = Yii::$app->db->createCommand()
                ->insert('inventory_categories', $categoryData)
                ->execute();
            if ($result) {
                $newCategoryId = Yii::$app->db->getLastInsertID();
                \app\controllers\ActivitylogsController::logActivity(
                    'Created category: ' . $category['category_name'],
                    'create',
                    $newCategoryId,
                    'Products',
                    [
                        'type' => 'category_create',
                        'category_code' => $category['category_code']
                    ]
                );
                return [
                    'success' => true,
                    'message' => 'Category created successfully.'
                ];
            }
            return [
                'success' => false,
                'message' => 'Failed to create category.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    public function actionBrands()
    {
        if (Yii::$app->request->isGet) {
            $brands = Yii::$app->db->createCommand("SELECT * FROM inventory_brands WHERE is_deleted = 0 ORDER BY id ASC")->queryAll();
            return $this->renderPartial('brands', ['brands' => $brands]);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $brand = Yii::$app->request->post();
            $brand_id = Yii::$app->request->post('id');
            if ($brand_id && isset($brand['delete']) && $brand['delete'] == 1) {
                $result = Yii::$app->db->createCommand()
                    ->update('inventory_brands', ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => Yii::$app->user->id ?? null], 'id = :id', [':id' => $brand_id])
                    ->execute();
                if ($result) {
                    \app\controllers\ActivitylogsController::logActivity(
                        'Deleted brand: ' . $brand['brand_name'],
                        'delete',
                        $brand_id,
                        'Products',
                        ['type' => 'brand_delete']
                    );
                    return ['success' => true, 'message' => 'Brand deleted successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to delete brand.'];
            }
            if (empty($brand['brand_name'])) {
                return ['success' => false, 'message' => 'Brand name is required.'];
            }
            $brandData = [
                'brand_name' => $brand['brand_name'],
                'brand_code' => $brand['brand_code'] ?? null,
                'website' => $brand['website'] ?? null,
                'email' => $brand['email'] ?? null,
                'phone' => $brand['phone'] ?? null,
                'notes' => $brand['notes'] ?? null,
                'is_active' => isset($brand['is_active']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Yii::$app->user->id ?? null
            ];
            if ($brand_id) {
                $result = Yii::$app->db->createCommand()
                    ->update('inventory_brands', $brandData, 'id = :id', [':id' => $brand_id])
                    ->execute();
                if ($result) {
                    \app\controllers\ActivitylogsController::logActivity(
                        'Updated brand: ' . $brand['brand_name'],
                        'update',
                        $brand_id,
                        'Products',
                        [
                            'type' => 'brand_update',
                            'brand_code' => $brand['brand_code'] ?? null
                        ]
                    );
                    return ['success' => true, 'message' => 'Brand updated successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to update brand.'];
            }
            $brandData['created_at'] = date('Y-m-d H:i:s');
            $brandData['created_by'] = Yii::$app->user->id ?? null;
            $brandData['is_deleted'] = 0;
            $result = Yii::$app->db->createCommand()->insert('inventory_brands', $brandData)->execute();
            if ($result) {
                $newId = Yii::$app->db->getLastInsertID();
                \app\controllers\ActivitylogsController::logActivity(
                    'Created brand: ' . $brand['brand_name'],
                    'create',
                    $newId,
                    'Products',
                    [
                        'type' => 'brand_create',
                        'brand_code' => $brand['brand_code'] ?? null
                    ]
                );
                return ['success' => true, 'message' => 'Brand created successfully.'];
            }
            return ['success' => false, 'message' => 'Failed to create brand.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    public function actionUnits()
    {
        if (Yii::$app->request->isGet) {
            $units = Yii::$app->db->createCommand("SELECT * FROM inventory_units WHERE is_deleted = 0 ORDER BY id ASC")->queryAll();
            return $this->renderPartial('units', ['units' => $units]);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $unit = Yii::$app->request->post();
            $unit_id = Yii::$app->request->post('id');
            if ($unit_id && isset($unit['delete']) && $unit['delete'] == 1) {
                $result = Yii::$app->db->createCommand()
                    ->update('inventory_units', ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => Yii::$app->user->id ?? null], 'id = :id', [':id' => $unit_id])
                    ->execute();
                if ($result) {
                    \app\controllers\ActivitylogsController::logActivity(
                        'Deleted unit: ' . $unit['unit_name'],
                        'delete',
                        $unit_id,
                        'Products',
                        ['type' => 'unit_delete']
                    );
                    return ['success' => true, 'message' => 'Unit deleted successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to delete unit.'];
            }
            if (empty($unit['unit_name'])) {
                return ['success' => false, 'message' => 'Unit name is required.'];
            }
            $unitData = [
                'unit_name' => $unit['unit_name'],
                'short_name' => $unit['short_name'] ?? null,
                'description' => $unit['description'] ?? null,
                'is_active' => isset($unit['is_active']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Yii::$app->user->id ?? null
            ];
            if ($unit_id) {
                $result = Yii::$app->db->createCommand()
                    ->update('inventory_units', $unitData, 'id = :id', [':id' => $unit_id])
                    ->execute();
                if ($result) {
                    \app\controllers\ActivitylogsController::logActivity(
                        'Updated unit: ' . $unit['unit_name'],
                        'update',
                        $unit_id,
                        'Products',
                        [
                            'type' => 'unit_update',
                            'short_name' => $unit['short_name'] ?? null
                        ]
                    );
                    return ['success' => true, 'message' => 'Unit updated successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to update unit.'];
            }
            $unitData['created_at'] = date('Y-m-d H:i:s');
            $unitData['created_by'] = Yii::$app->user->id ?? null;
            $unitData['is_deleted'] = 0;
            $result = Yii::$app->db->createCommand()->insert('inventory_units', $unitData)->execute();
            if ($result) {
                $newId = Yii::$app->db->getLastInsertID();
                \app\controllers\ActivitylogsController::logActivity(
                    'Created unit: ' . $unit['unit_name'],
                    'create',
                    $newId,
                    'Products',
                    [
                        'type' => 'unit_create',
                        'short_name' => $unit['short_name'] ?? null
                    ]
                );
                return ['success' => true, 'message' => 'Unit created successfully.'];
            }
            return ['success' => false, 'message' => 'Failed to create unit.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function actionVehiclemakes()
    {
        if (Yii::$app->request->isGet) {
            $vehiclemakes = Yii::$app->db->createCommand("SELECT * FROM inventory_vehicle_makes WHERE is_deleted = 0 ORDER BY id ASC")->queryAll();

            foreach ($vehiclemakes as &$vehiclemake) {
                $vehiclemake['total_products'] = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_products p
                     LEFT JOIN inventory_vehicle_models vm ON vm.id = p.model_id
                     WHERE vm.make_id = :make_id AND p.is_deleted = 0",
                    [':make_id' => $vehiclemake['id']]
                )->queryScalar();

                $vehiclemake['active_products'] = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_products p
                     LEFT JOIN inventory_vehicle_models vm ON vm.id = p.model_id
                     WHERE vm.make_id = :make_id AND p.is_active = 1 AND p.is_deleted = 0",
                    [':make_id' => $vehiclemake['id']]
                )->queryScalar();

                $vehiclemake['inactive_products'] = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_products p
                     LEFT JOIN inventory_vehicle_models vm ON vm.id = p.model_id
                     WHERE vm.make_id = :make_id AND p.is_active = 0 AND p.is_deleted = 0",
                    [':make_id' => $vehiclemake['id']]
                )->queryScalar();
            }

            return $this->renderPartial('vehiclemakes', ['vehiclemakes' => $vehiclemakes]);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $vehiclemake = Yii::$app->request->post();
            $vehiclemake_id = Yii::$app->request->post('id');
            if ($vehiclemake_id && isset($vehiclemake['delete']) && $vehiclemake['delete'] == 1) {
                $activeProductCount = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_products p
                     LEFT JOIN inventory_vehicle_models vm ON vm.id = p.model_id
                     WHERE vm.make_id = :make_id AND p.is_active = 1 AND p.is_deleted = 0",
                    [':make_id' => $vehiclemake_id]
                )->queryScalar();

                if ($activeProductCount > 0) {
                    return [
                        'success' => false,
                        'message' => 'Cannot delete vehicle make with active products. Please deactivate or delete all active products first.'
                    ];
                }

                $result = Yii::$app->db->createCommand()
                    ->update('inventory_vehicle_makes', ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => Yii::$app->user->id ?? null], 'id = :id', [':id' => $vehiclemake_id])
                    ->execute();
                if ($result) {
                    \app\controllers\ActivitylogsController::logActivity(
                        'Deleted vehicle make: ' . $vehiclemake['make_name'],
                        'delete',
                        $vehiclemake_id,
                        'Products',
                        ['type' => 'vehicle_make_delete']
                    );
                    return ['success' => true, 'message' => 'Vehicle make deleted successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to delete vehicle make.'];
            }
            if (empty($vehiclemake['make_name'])) {
                return ['success' => false, 'message' => 'Vehicle make name is required.'];
            }
            $vehiclemakeData = [
                'make_name' => $vehiclemake['make_name'],
                'make_code' => $vehiclemake['make_code'] ?? null,
                'country' => $vehiclemake['country'] ?? null,
                'website' => $vehiclemake['website'] ?? null,
                'notes' => $vehiclemake['notes'] ?? null,
                'is_active' => isset($vehiclemake['is_active']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Yii::$app->user->id ?? null
            ];
            if ($vehiclemake_id) {
                $result = Yii::$app->db->createCommand()
                    ->update('inventory_vehicle_makes', $vehiclemakeData, 'id = :id', [':id' => $vehiclemake_id])
                    ->execute();
                if ($result) {
                    \app\controllers\ActivitylogsController::logActivity(
                        'Updated vehicle make: ' . $vehiclemake['make_name'],
                        'update',
                        $vehiclemake_id,
                        'Products',
                        [
                            'type' => 'vehicle_make_update',
                            'make_code' => $vehiclemake['make_code'] ?? null
                        ]
                    );
                    return ['success' => true, 'message' => 'Vehicle make updated successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to update vehicle make.'];
            }
            $vehiclemakeData['created_at'] = date('Y-m-d H:i:s');
            $vehiclemakeData['created_by'] = Yii::$app->user->id ?? null;
            $vehiclemakeData['is_deleted'] = 0;
            $result = Yii::$app->db->createCommand()->insert('inventory_vehicle_makes', $vehiclemakeData)->execute();
            if ($result) {
                $newId = Yii::$app->db->getLastInsertID();
                \app\controllers\ActivitylogsController::logActivity(
                    'Created vehicle make: ' . $vehiclemake['make_name'],
                    'create',
                    $newId,
                    'Products',
                    [
                        'type' => 'vehicle_make_create',
                        'make_code' => $vehiclemake['make_code'] ?? null
                    ]
                );
                return ['success' => true, 'message' => 'Vehicle make created successfully.'];
            }
            return ['success' => false, 'message' => 'Failed to create vehicle make.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    public function actionVehiclemodels()
    {
        if (Yii::$app->request->isGet) {
            $vehiclemodels = Yii::$app->db->createCommand("
                SELECT vm.*, v.make_name
                FROM inventory_vehicle_models vm
                LEFT JOIN inventory_vehicle_makes v ON v.id = vm.make_id
                WHERE vm.is_deleted = 0
                ORDER BY vm.id ASC
            ")->queryAll();

            foreach ($vehiclemodels as &$vehiclemodel) {
                $vehiclemodel['total_products'] = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_products WHERE model_id = :model_id AND is_deleted = 0",
                    [':model_id' => $vehiclemodel['id']]
                )->queryScalar();

                $vehiclemodel['active_products'] = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_products WHERE model_id = :model_id AND is_active = 1 AND is_deleted = 0",
                    [':model_id' => $vehiclemodel['id']]
                )->queryScalar();

                $vehiclemodel['inactive_products'] = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_products WHERE model_id = :model_id AND is_active = 0 AND is_deleted = 0",
                    [':model_id' => $vehiclemodel['id']]
                )->queryScalar();
            }

            $vehiclemakes = Yii::$app->db->createCommand("SELECT * FROM inventory_vehicle_makes WHERE is_deleted = 0 ORDER BY make_name ASC")->queryAll();
            return $this->renderPartial('vehiclemodels', ['vehiclemodels' => $vehiclemodels, 'vehiclemakes' => $vehiclemakes]);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $vehiclemodel = Yii::$app->request->post();
            $vehiclemodel_id = Yii::$app->request->post('id');
            if ($vehiclemodel_id && isset($vehiclemodel['delete']) && $vehiclemodel['delete'] == 1) {
                $activeProductCount = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM inventory_products WHERE model_id = :model_id AND is_active = 1 AND is_deleted = 0",
                    [':model_id' => $vehiclemodel_id]
                )->queryScalar();

                if ($activeProductCount > 0) {
                    return [
                        'success' => false,
                        'message' => 'Cannot delete vehicle model with active products. Please deactivate or delete all active products first.'
                    ];
                }

                $result = Yii::$app->db->createCommand()
                    ->update('inventory_vehicle_models', ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => Yii::$app->user->id ?? null], 'id = :id', [':id' => $vehiclemodel_id])
                    ->execute();
                if ($result) {
                    \app\controllers\ActivitylogsController::logActivity(
                        'Deleted vehicle model: ' . $vehiclemodel['model_name'],
                        'delete',
                        $vehiclemodel_id,
                        'Products',
                        ['type' => 'vehicle_model_delete']
                    );
                    return ['success' => true, 'message' => 'Vehicle model deleted successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to delete vehicle model.'];
            }
            if (empty($vehiclemodel['model_name'])) {
                return ['success' => false, 'message' => 'Vehicle model name is required.'];
            }
            if (empty($vehiclemodel['make_id'])) {
                return ['success' => false, 'message' => 'Vehicle make is required.'];
            }
            $vehiclemodelData = [
                'make_id' => $vehiclemodel['make_id'],
                'model_name' => $vehiclemodel['model_name'],
                'model_code' => $vehiclemodel['model_code'] ?? null,
                'model_year' => $vehiclemodel['model_year'] ?? null,
                'engine_type' => $vehiclemodel['engine_type'] ?? null,
                'engine_capacity' => $vehiclemodel['engine_capacity'] ?? null,
                'fuel_type' => $vehiclemodel['fuel_type'] ?? null,
                'transmission' => $vehiclemodel['transmission'] ?? null,
                'notes' => $vehiclemodel['notes'] ?? null,
                'is_active' => isset($vehiclemodel['is_active']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Yii::$app->user->id ?? null
            ];
            if ($vehiclemodel_id) {
                $result = Yii::$app->db->createCommand()
                    ->update('inventory_vehicle_models', $vehiclemodelData, 'id = :id', [':id' => $vehiclemodel_id])
                    ->execute();
                if ($result) {
                    \app\controllers\ActivitylogsController::logActivity(
                        'Updated vehicle model: ' . $vehiclemodel['model_name'],
                        'update',
                        $vehiclemodel_id,
                        'Products',
                        [
                            'type' => 'vehicle_model_update',
                            'model_year' => $vehiclemodel['model_year'] ?? null
                        ]
                    );
                    return ['success' => true, 'message' => 'Vehicle model updated successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to update vehicle model.'];
            }
            $vehiclemodelData['created_at'] = date('Y-m-d H:i:s');
            $vehiclemodelData['created_by'] = Yii::$app->user->id ?? null;
            $vehiclemodelData['is_deleted'] = 0;
            $result = Yii::$app->db->createCommand()->insert('inventory_vehicle_models', $vehiclemodelData)->execute();
            if ($result) {
                $newId = Yii::$app->db->getLastInsertID();
                \app\controllers\ActivitylogsController::logActivity(
                    'Created vehicle model: ' . $vehiclemodel['model_name'],
                    'create',
                    $newId,
                    'Products',
                    [
                        'type' => 'vehicle_model_create',
                        'model_year' => $vehiclemodel['model_year'] ?? null
                    ]
                );
                return ['success' => true, 'message' => 'Vehicle model created successfully.'];
            }
            return ['success' => false, 'message' => 'Failed to create vehicle model.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

 
}

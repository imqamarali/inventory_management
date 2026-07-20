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


  
    public function actionProducts()
    {
        $modules = [
            ['name' => 'Product Dashboard', 'controller' => 'products/productdashboard', 'icon' => 'fa fa-dashboard'],
            ['name' => 'Categories', 'controller' => 'products/categories', 'icon' => 'fa fa-tags'],
            ['name' => 'Brands', 'controller' => 'products/brands', 'icon' => 'fa fa-certificate'],
            ['name' => 'Units', 'controller' => 'products/units', 'icon' => 'fa fa-balance-scale'],
            ['name' => 'Vehicle Makes', 'controller' => 'products/vehiclemakes', 'icon' => 'fa fa-car'],
            ['name' => 'Vehicle Models', 'controller' => 'products/vehiclemodels', 'icon' => 'fa fa-car'],
            ['name' => 'Product List', 'controller' => 'products/productlist', 'icon' => 'fa fa-cubes'],
        ];

        return $this->render('index', compact('modules'));
    }

    public function actionProductdashboard()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('productdashboard');
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
            return $this->renderPartial('categories', ['categories' => $categories]);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $category = Yii::$app->request->post();
            $category_id = Yii::$app->request->post('id');
            if ($category_id && isset($category['delete']) && $category['delete'] == 1) {
                $result = Yii::$app->db->createCommand()
                    ->update(
                        'inventory_categories',
                        ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => Yii::$app->user->id ?? null],
                        'id = :id',
                        [':id' => $category_id]
                    )->execute();
                if ($result) {
                    Yii::$app->Component->Activitylog(
                        'Deleted category ID: ' . $category_id,
                        'delete',
                        $category_id,
                        'inventory',
                        ['is_deleted' => 1]
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
                    Yii::$app->Component->Activitylog(
                        'Updated category: ' . $category['category_name'],
                        'update',
                        $category_id,
                        'inventory',
                        $categoryData
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
                Yii::$app->Component->Activitylog(
                    'Created category: ' . $category['category_name'],
                    'create',
                    $newCategoryId,
                    'inventory',
                    $categoryData
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
                    Yii::$app->Component->Activitylog('Deleted brand ID: ' . $brand_id, 'delete', $brand_id, 'inventory', ['is_deleted' => 1]);
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
                    Yii::$app->Component->Activitylog('Updated brand: ' . $brand['brand_name'], 'update', $brand_id, 'inventory', $brandData);
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
                Yii::$app->Component->Activitylog('Created brand: ' . $brand['brand_name'], 'create', $newId, 'inventory', $brandData);
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
                return $result ? ['success' => true, 'message' => 'Unit deleted successfully.'] : ['success' => false, 'message' => 'Failed to delete unit.'];
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
                return $result ? ['success' => true, 'message' => 'Unit updated successfully.'] : ['success' => false, 'message' => 'Failed to update unit.'];
            }
            $unitData['created_at'] = date('Y-m-d H:i:s');
            $unitData['created_by'] = Yii::$app->user->id ?? null;
            $unitData['is_deleted'] = 0;
            $result = Yii::$app->db->createCommand()->insert('inventory_units', $unitData)->execute();
            return $result ? ['success' => true, 'message' => 'Unit created successfully.'] : ['success' => false, 'message' => 'Failed to create unit.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function actionVehiclemakes()
    {
        if (Yii::$app->request->isGet) {
            $vehiclemakes = Yii::$app->db->createCommand("SELECT * FROM inventory_vehicle_makes WHERE is_deleted = 0 ORDER BY id ASC")->queryAll();
            return $this->renderPartial('vehiclemakes', ['vehiclemakes' => $vehiclemakes]);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $vehiclemake = Yii::$app->request->post();
            $vehiclemake_id = Yii::$app->request->post('id');
            if ($vehiclemake_id && isset($vehiclemake['delete']) && $vehiclemake['delete'] == 1) {
                $result = Yii::$app->db->createCommand()
                    ->update('inventory_vehicle_makes', ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => Yii::$app->user->id ?? null], 'id = :id', [':id' => $vehiclemake_id])
                    ->execute();
                return $result ? ['success' => true, 'message' => 'Vehicle make deleted successfully.'] : ['success' => false, 'message' => 'Failed to delete vehicle make.'];
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
                return $result ? ['success' => true, 'message' => 'Vehicle make updated successfully.'] : ['success' => false, 'message' => 'Failed to update vehicle make.'];
            }
            $vehiclemakeData['created_at'] = date('Y-m-d H:i:s');
            $vehiclemakeData['created_by'] = Yii::$app->user->id ?? null;
            $vehiclemakeData['is_deleted'] = 0;
            $result = Yii::$app->db->createCommand()->insert('inventory_vehicle_makes', $vehiclemakeData)->execute();
            return $result ? ['success' => true, 'message' => 'Vehicle make created successfully.'] : ['success' => false, 'message' => 'Failed to create vehicle make.'];
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
            $vehiclemakes = Yii::$app->db->createCommand("SELECT * FROM inventory_vehicle_makes WHERE is_deleted = 0 ORDER BY make_name ASC")->queryAll();
            return $this->renderPartial('vehiclemodels', ['vehiclemodels' => $vehiclemodels, 'vehiclemakes' => $vehiclemakes]);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $vehiclemodel = Yii::$app->request->post();
            $vehiclemodel_id = Yii::$app->request->post('id');
            if ($vehiclemodel_id && isset($vehiclemodel['delete']) && $vehiclemodel['delete'] == 1) {
                $result = Yii::$app->db->createCommand()
                    ->update('inventory_vehicle_models', ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => Yii::$app->user->id ?? null], 'id = :id', [':id' => $vehiclemodel_id])
                    ->execute();
                return $result ? ['success' => true, 'message' => 'Vehicle model deleted successfully.'] : ['success' => false, 'message' => 'Failed to delete vehicle model.'];
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
                return $result ? ['success' => true, 'message' => 'Vehicle model updated successfully.'] : ['success' => false, 'message' => 'Failed to update vehicle model.'];
            }
            $vehiclemodelData['created_at'] = date('Y-m-d H:i:s');
            $vehiclemodelData['created_by'] = Yii::$app->user->id ?? null;
            $vehiclemodelData['is_deleted'] = 0;
            $result = Yii::$app->db->createCommand()->insert('inventory_vehicle_models', $vehiclemodelData)->execute();
            return $result ? ['success' => true, 'message' => 'Vehicle model created successfully.'] : ['success' => false, 'message' => 'Failed to create vehicle model.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }


    public function actionInjectdb()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_categories ( 
                id INT AUTO_INCREMENT PRIMARY KEY,
                parent_id INT NULL,
                category_name VARCHAR(150) NOT NULL,
                category_code VARCHAR(50) UNIQUE,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT NULL,
                updated_by INT NULL,
                is_active TINYINT(1) DEFAULT 1,
                is_deleted TINYINT(1) DEFAULT 0,
                INDEX(parent_id)

            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_brands ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                brand_name VARCHAR(150) NOT NULL,
                brand_code VARCHAR(50), 
                website VARCHAR(255),
                email VARCHAR(150),
                phone VARCHAR(30), 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_units ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                unit_name VARCHAR(100),
                short_name VARCHAR(20), 
                description TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_products ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                category_id INT,
                brand_id INT,
                unit_id INT, 
                product_name VARCHAR(200) NOT NULL,
                sku VARCHAR(100) UNIQUE,
                barcode VARCHAR(150), 
                description TEXT, 
                purchase_price DECIMAL(15,2) DEFAULT 0,
                selling_price DECIMAL(15,2) DEFAULT 0, 
                minimum_stock DECIMAL(15,2) DEFAULT 0,
                maximum_stock DECIMAL(15,2) DEFAULT 0,
                reorder_level DECIMAL(15,2) DEFAULT 0, 
                product_image VARCHAR(255), 
                weight DECIMAL(10,2),
                length DECIMAL(10,2),
                width DECIMAL(10,2),
                height DECIMAL(10,2), 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0, 
                INDEX(category_id),
                INDEX(brand_id),
                INDEX(unit_id), 
                FOREIGN KEY(category_id)
                    REFERENCES inventory_categories(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(brand_id)
                    REFERENCES inventory_brands(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(unit_id)
                    REFERENCES inventory_units(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_warehouses ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                warehouse_name VARCHAR(200), 
                warehouse_code VARCHAR(50), 
                address TEXT, 
                city VARCHAR(100),
                province VARCHAR(100),
                country VARCHAR(100), 
                contact_person VARCHAR(150),
                phone VARCHAR(50),
                email VARCHAR(150), 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                warehouse_id INT NOT NULL,
                product_id INT NOT NULL, 
                quantity DECIMAL(15,2) DEFAULT 0, 
                reserved_quantity DECIMAL(15,2) DEFAULT 0, 
                available_quantity DECIMAL(15,2) DEFAULT 0, 
                average_cost DECIMAL(15,2) DEFAULT 0, 
                last_purchase_price DECIMAL(15,2) DEFAULT 0, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0, 
                UNIQUE(product_id,warehouse_id), 
                INDEX(product_id),
                INDEX(warehouse_id), 
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            echo "Part 1 database created successfully.";

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_suppliers ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                supplier_code VARCHAR(50) UNIQUE, 
                company_name VARCHAR(200) NOT NULL, 
                contact_person VARCHAR(150), 
                email VARCHAR(150), 
                phone VARCHAR(50), 
                mobile VARCHAR(50), 
                website VARCHAR(255), 
                tax_number VARCHAR(100), 
                payment_terms INT DEFAULT 30, 
                credit_limit DECIMAL(15,2) DEFAULT 0, 
                opening_balance DECIMAL(15,2) DEFAULT 0, 
                current_balance DECIMAL(15,2) DEFAULT 0, 
                address TEXT, 
                city VARCHAR(100), 
                province VARCHAR(100), 
                country VARCHAR(100), 
                postal_code VARCHAR(20), 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP,
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_supplier_contacts ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                supplier_id INT NOT NULL, 
                contact_name VARCHAR(150), 
                designation VARCHAR(100), 
                phone VARCHAR(50), 
                mobile VARCHAR(50), 
                email VARCHAR(150), 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(supplier_id), 
                FOREIGN KEY(supplier_id)
                    REFERENCES inventory_suppliers(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
                CREATE TABLE IF NOT EXISTS inventory_supplier_documents (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    supplier_id INT NOT NULL,
                    document_type VARCHAR(100),
                    document_name VARCHAR(255) NOT NULL,
                    document_file VARCHAR(500),
                    expiry_date DATE NULL,
                    remarks TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                        ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT NULL,
                    updated_by INT NULL,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(supplier_id),
                    FOREIGN KEY(supplier_id)
                        REFERENCES inventory_suppliers(id)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
                ) ENGINE=InnoDB;
                ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_purchase_orders ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                po_number VARCHAR(100) UNIQUE, 
                supplier_id INT NOT NULL, 
                warehouse_id INT NOT NULL, 
                order_date DATE, 
                expected_date DATE, 
                status ENUM( 
                    'Draft', 
                    'Approved', 
                    'Partially Received', 
                    'Completed', 
                    'Cancelled' 
                ) DEFAULT 'Draft', 
                subtotal DECIMAL(15,2) DEFAULT 0, 
                discount DECIMAL(15,2) DEFAULT 0, 
                tax DECIMAL(15,2) DEFAULT 0, 
                freight DECIMAL(15,2) DEFAULT 0, 
                grand_total DECIMAL(15,2) DEFAULT 0, 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(supplier_id), 
                INDEX(warehouse_id), 
                FOREIGN KEY(supplier_id)
                    REFERENCES inventory_suppliers(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_purchase_order_items ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                purchase_order_id INT NOT NULL, 
                product_id INT NOT NULL, 
                quantity DECIMAL(15,2) DEFAULT 0, 
                received_quantity DECIMAL(15,2) DEFAULT 0, 
                remaining_quantity DECIMAL(15,2) DEFAULT 0, 
                unit_price DECIMAL(15,2) DEFAULT 0, 
                discount DECIMAL(15,2) DEFAULT 0, 
                tax DECIMAL(15,2) DEFAULT 0, 
                total DECIMAL(15,2) DEFAULT 0, 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(purchase_order_id), 
                INDEX(product_id), 
                FOREIGN KEY(purchase_order_id)
                    REFERENCES inventory_purchase_orders(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE, 
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_customers ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                customer_code VARCHAR(50) UNIQUE, 
                customer_type ENUM(
                    'Individual',
                    'Company'
                ) DEFAULT 'Individual', 
                company_name VARCHAR(200), 
                first_name VARCHAR(100), 
                last_name VARCHAR(100), 
                email VARCHAR(150), 
                phone VARCHAR(50), 
                mobile VARCHAR(50), 
                tax_number VARCHAR(100), 
                credit_limit DECIMAL(15,2) DEFAULT 0, 
                opening_balance DECIMAL(15,2) DEFAULT 0, 
                current_balance DECIMAL(15,2) DEFAULT 0, 
                payment_terms INT DEFAULT 0, 
                address TEXT, 
                city VARCHAR(100), 
                province VARCHAR(100), 
                country VARCHAR(100), 
                postal_code VARCHAR(20), 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_customer_contacts ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                customer_id INT NOT NULL, 
                contact_name VARCHAR(150), 
                designation VARCHAR(100), 
                email VARCHAR(150), 
                phone VARCHAR(50), 
                mobile VARCHAR(50), 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(customer_id), 
                FOREIGN KEY(customer_id)
                    REFERENCES inventory_customers(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_sales_orders ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                order_number VARCHAR(100) UNIQUE, 
                customer_id INT NOT NULL, 
                warehouse_id INT NOT NULL, 
                order_date DATE, 
                delivery_date DATE, 
                order_status ENUM( 
                    'Draft', 
                    'Confirmed', 
                    'Packed', 
                    'Dispatched',  
                    'Delivered', 
                    'Cancelled' 
                ) DEFAULT 'Draft', 
                payment_status ENUM( 
                    'Pending', 
                    'Partial', 
                    'Paid' 
                ) DEFAULT 'Pending', 
                subtotal DECIMAL(15,2) DEFAULT 0, 
                discount DECIMAL(15,2) DEFAULT 0, 
                tax DECIMAL(15,2) DEFAULT 0, 
                shipping DECIMAL(15,2) DEFAULT 0, 
                grand_total DECIMAL(15,2) DEFAULT 0, 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(customer_id), 
                INDEX(warehouse_id), 
                FOREIGN KEY(customer_id)
                    REFERENCES inventory_customers(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_sales_order_items ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                sales_order_id INT NOT NULL, 
                product_id INT NOT NULL, 
                quantity DECIMAL(15,2) DEFAULT 0, 
                delivered_quantity DECIMAL(15,2) DEFAULT 0, 
                remaining_quantity DECIMAL(15,2) DEFAULT 0, 
                unit_price DECIMAL(15,2) DEFAULT 0, 
                discount DECIMAL(15,2) DEFAULT 0, 
                tax DECIMAL(15,2) DEFAULT 0, 
                total DECIMAL(15,2) DEFAULT 0, 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(sales_order_id), 
                INDEX(product_id), 
                FOREIGN KEY(sales_order_id)
                    REFERENCES inventory_sales_orders(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE, 
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_movements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                movement_no VARCHAR(50) UNIQUE,
                warehouse_id INT NOT NULL,
                product_id INT NOT NULL,
                reference_type ENUM(
                    'Purchase',
                    'Sale',
                    'Transfer In',
                    'Transfer Out',
                    'Adjustment',
                    'Return Purchase',
                    'Return Sale',
                    'Opening Stock',
                    'Stock Audit'
                ) NOT NULL,
                reference_id INT NULL,
                movement_type ENUM('IN','OUT') NOT NULL,
                quantity DECIMAL(15,2) NOT NULL,
                unit_cost DECIMAL(15,2) DEFAULT 0,
                total_cost DECIMAL(15,2) DEFAULT 0,
                remarks TEXT,
                movement_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(product_id),
                INDEX(warehouse_id),
                INDEX(reference_id),
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
                    ON UPDATE CASCADE,
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_adjustments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                adjustment_no VARCHAR(50) UNIQUE,
                warehouse_id INT NOT NULL,
                adjustment_date DATE,
                adjustment_type ENUM('Increase','Decrease') NOT NULL,
                reason ENUM('Damage','Expired','Lost','Correction','Other'),
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
            CREATE TABLE IF NOT EXISTS inventory_stock_adjustment_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                adjustment_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity DECIMAL(15,2),
                unit_cost DECIMAL(15,2),
                total_cost DECIMAL(15,2),
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(adjustment_id)
                    REFERENCES inventory_stock_adjustments(id)
                    ON DELETE CASCADE,
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_transfers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transfer_no VARCHAR(50) UNIQUE,
                from_warehouse INT NOT NULL,
                to_warehouse INT NOT NULL,
                transfer_date DATE,
                status ENUM('Pending','In Transit','Completed','Cancelled') DEFAULT 'Pending',
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(from_warehouse) REFERENCES inventory_warehouses(id),
                FOREIGN KEY(to_warehouse) REFERENCES inventory_warehouses(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_transfer_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transfer_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity DECIMAL(15,2),
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(transfer_id)
                    REFERENCES inventory_stock_transfers(id)
                    ON DELETE CASCADE,
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
            ) ENGINE=InnoDB;
            ")->execute();

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
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
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
                FOREIGN KEY(audit_id)
                    REFERENCES inventory_stock_audits(id)
                    ON DELETE CASCADE,
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_accounts ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                parent_id INT NULL, 
                account_code VARCHAR(50) UNIQUE, 
                account_name VARCHAR(200) NOT NULL, 
                account_type ENUM('Asset','Liability','Equity','Income','Expense') NOT NULL,
                opening_balance DECIMAL(15,2) DEFAULT 0,
                current_balance DECIMAL(15,2) DEFAULT 0,
                remarks TEXT,

                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,

                created_by INT,
                updated_by INT,

                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,

                INDEX(parent_id),

                FOREIGN KEY(parent_id)
                    REFERENCES inventory_accounts(id)
                    ON UPDATE CASCADE
                    ON DELETE SET NULL

            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transaction_no VARCHAR(100) UNIQUE,
                transaction_date DATE,
                reference_type ENUM('Purchase','Sale','Payment','Receipt','Expense','Adjustment'),
                reference_id INT,
                account_id INT NOT NULL,
                transaction_type ENUM('Debit','Credit'
                ) NOT NULL,
                amount DECIMAL(15,2) NOT NULL,
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(account_id),
                FOREIGN KEY(account_id)
                    REFERENCES inventory_accounts(id)
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_payments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                payment_no VARCHAR(100) UNIQUE,
                payment_date DATE,
                payment_type ENUM('Receive','Pay'),
                reference_type ENUM('Customer','Supplier'),
                reference_id INT,
                payment_method ENUM('Cash','Bank','Cheque','Online'),
                account_id INT,
                amount DECIMAL(15,2),
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(account_id)
                    REFERENCES inventory_accounts(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                title VARCHAR(255),
                message TEXT,
                notification_type ENUM('Info','Success','Warning','Error') DEFAULT 'Info',
                is_read TINYINT DEFAULT 0,
                read_at DATETIME NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(user_id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_logs (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                module VARCHAR(100),
                action VARCHAR(100),
                table_name VARCHAR(100),
                record_id BIGINT,
                old_data LONGTEXT,
                new_data LONGTEXT,
                ip_address VARCHAR(50),
                user_agent TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX(user_id),
                INDEX(module),
                INDEX(table_name),
                INDEX(record_id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_reports (
                id INT AUTO_INCREMENT PRIMARY KEY,
                report_name VARCHAR(200),
                report_type VARCHAR(100),
                generated_by INT,
                filters JSON,
                file_path VARCHAR(500),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                is_deleted TINYINT DEFAULT 0
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_vehicle_makes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                make_name VARCHAR(150) NOT NULL,
                make_code VARCHAR(50) UNIQUE,
                country VARCHAR(100),
                website VARCHAR(255),
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_vehicle_models (
                id INT AUTO_INCREMENT PRIMARY KEY,
                make_id INT NOT NULL,
                model_name VARCHAR(150) NOT NULL,
                model_code VARCHAR(50),
                model_year VARCHAR(50),
                engine_type VARCHAR(100),
                engine_capacity VARCHAR(50),
                fuel_type ENUM(
                    'Petrol',
                    'Diesel',
                    'Hybrid',
                    'Electric',
                    'CNG'
                ) DEFAULT 'Petrol',
                transmission ENUM(
                    'Manual',
                    'Automatic',
                    'CVT'
                ) DEFAULT 'Manual',
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(make_id),
                FOREIGN KEY(make_id)
                    REFERENCES inventory_vehicle_makes(id)
                    ON UPDATE CASCADE
                    ON DELETE CASCADE
            ) ENGINE=InnoDB;
            ")->execute();

            $transaction->commit();

            echo "Database created successfully.";
            exit;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo "Error: " . $e->getMessage();
            exit;
        }
    }
}

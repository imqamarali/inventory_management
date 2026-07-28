<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Load Products Data - Real Time Insertion';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['products']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .data-form {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #0f4c29;
    }

    .table-wrapper {
        max-height: 600px;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-top: 15px;
    }

    .table-wrapper table {
        margin-bottom: 0;
        font-size: 12px;
    }

    .data-table th {
        background: #f5f5f5;
        position: sticky;
        top: 0;
        z-index: 10;
        font-weight: 600;
    }

    .action-btn {
        padding: 4px 8px;
        font-size: 11px;
        margin: 0 2px;
    }

    .tab-pane {
        display: none;
        animation: fadeIn 0.3s ease-in;
    }

    .tab-pane.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .nav-tabs {
        border-bottom: 2px solid #0f4c29;
    }

    .nav-tabs .nav-link {
        color: #333;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 10px 20px;
    }

    .nav-tabs .nav-link.active {
        color: #0f4c29;
        background: transparent;
        border-bottom: 3px solid #0f4c29;
    }

    .nav-tabs .nav-link:hover {
        border-bottom: 3px solid #d4d4d4;
    }

    .badge-status {
        font-size: 10px;
        padding: 3px 8px;
    }

    .form-row-custom {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .form-row-custom .form-group {
        margin-bottom: 0;
    }

    .sync-status {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: none;
        z-index: 9999;
        max-width: 400px;
    }

    .sync-status.show {
        display: block;
    }

    .sync-status.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .sync-status.error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .data-count {
        background: #e8f4f8;
        padding: 10px 15px;
        border-radius: 4px;
        font-size: 12px;
        margin-bottom: 15px;
    }

    .no-data {
        text-align: center;
        padding: 30px;
        color: #999;
    }

    .edit-row {
        background: #e8f4f8 !important;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-upload"></i> Real-Time Product Data Insertion
                        </h3>
                        <small class="text-muted">Add/Update Categories, Brands, Units, Makes & Models</small>
                    </div>

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="nav-item">
                            <a class="nav-link active" href="#categories-tab" role="tab" data-toggle="tab">
                                <i class="fa fa-tags"></i> Categories
                            </a>
                        </li>
                        <li role="presentation" class="nav-item">
                            <a class="nav-link" href="#brands-tab" role="tab" data-toggle="tab">
                                <i class="fa fa-certificate"></i> Brands
                            </a>
                        </li>
                        <li role="presentation" class="nav-item">
                            <a class="nav-link" href="#units-tab" role="tab" data-toggle="tab">
                                <i class="fa fa-balance-scale"></i> Units
                            </a>
                        </li>
                        <li role="presentation" class="nav-item">
                            <a class="nav-link" href="#makes-tab" role="tab" data-toggle="tab">
                                <i class="fa fa-car"></i> Vehicle Makes
                            </a>
                        </li>
                        <li role="presentation" class="nav-item">
                            <a class="nav-link" href="#models-tab" role="tab" data-toggle="tab">
                                <i class="fa fa-car"></i> Vehicle Models
                            </a>
                        </li>
                    </ul>

                    <div class="box-body">
                        <!-- CATEGORIES TAB -->
                        <div role="tabpanel" class="tab-pane active" id="categories-tab">
                            <div class="data-form">
                                <h4><i class="fa fa-tags"></i> Add New Category</h4>
                                <div class="form-row-custom">
                                    <div class="form-group">
                                        <label>Category Name *</label>
                                        <input type="text" class="form-control input-sm" id="cat_name" placeholder="e.g., Filters">
                                    </div>
                                    <div class="form-group">
                                        <label>Category Code</label>
                                        <input type="text" class="form-control input-sm" id="cat_code" placeholder="e.g., CAT-001">
                                    </div>
                                    <div class="form-group">
                                        <label>Parent Category</label>
                                        <select class="form-control input-sm" id="cat_parent">
                                            <option value="">-- None --</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <?php if (!$cat['parent_id']): ?>
                                                    <option value="<?= $cat['id'] ?>"><?= Html::encode($cat['category_name']) ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control input-sm" id="cat_description" placeholder="Category description" rows="2"></textarea>
                                </div>
                                <button class="btn btn-primary btn-sm" onclick="addData('category')">
                                    <i class="fa fa-plus"></i> Add Category
                                </button>
                            </div>

                            <div class="data-count">
                                Total Categories: <strong><?= count($categories) ?></strong>
                            </div>

                            <div class="table-wrapper">
                                <table class="table table-striped table-hover data-table" id="categories-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="25%">Name</th>
                                            <th width="15%">Code</th>
                                            <th width="20%">Parent</th>
                                            <th width="15%">Status</th>
                                            <th width="20%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($categories)): ?>
                                            <tr>
                                                <td colspan="6" class="no-data">No categories found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($categories as $cat): ?>
                                                <tr data-id="<?= $cat['id'] ?>" data-type="category">
                                                    <td><?= $cat['id'] ?></td>
                                                    <td><?= Html::encode($cat['category_name']) ?></td>
                                                    <td><code><?= $cat['category_code'] ?? '-' ?></code></td>
                                                    <td><?php
                                                        $parent = null;
                                                        foreach ($categories as $c) {
                                                            if ($c['id'] == $cat['parent_id']) {
                                                                $parent = $c['category_name'];
                                                                break;
                                                            }
                                                        }
                                                        echo $parent ?? '<span class="text-muted">-</span>';
                                                    ?></td>
                                                    <td>
                                                        <span class="badge badge-status <?= $cat['is_active'] ? 'badge-success' : 'badge-warning' ?>">
                                                            <?= $cat['is_active'] ? 'Active' : 'Inactive' ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-warning action-btn" onclick="editCategory(<?= $cat['id'] ?>)">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-danger action-btn" onclick="deleteData(<?= $cat['id'] ?>, 'category')">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- BRANDS TAB -->
                        <div role="tabpanel" class="tab-pane" id="brands-tab">
                            <div class="data-form">
                                <h4><i class="fa fa-certificate"></i> Add New Brand</h4>
                                <div class="form-row-custom">
                                    <div class="form-group">
                                        <label>Brand Name *</label>
                                        <input type="text" class="form-control input-sm" id="brand_name" placeholder="e.g., Bosch">
                                    </div>
                                    <div class="form-group">
                                        <label>Brand Code</label>
                                        <input type="text" class="form-control input-sm" id="brand_code" placeholder="e.g., BOSCH">
                                    </div>
                                    <div class="form-group">
                                        <label>Website</label>
                                        <input type="url" class="form-control input-sm" id="brand_website" placeholder="https://www.example.com">
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control input-sm" id="brand_email" placeholder="info@brand.com">
                                    </div>
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="tel" class="form-control input-sm" id="brand_phone" placeholder="+92 300 0000000">
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-sm" onclick="addData('brand')">
                                    <i class="fa fa-plus"></i> Add Brand
                                </button>
                            </div>

                            <div class="data-count">
                                Total Brands: <strong><?= count($brands) ?></strong>
                            </div>

                            <div class="table-wrapper">
                                <table class="table table-striped table-hover data-table" id="brands-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="20%">Name</th>
                                            <th width="15%">Code</th>
                                            <th width="20%">Website</th>
                                            <th width="15%">Email</th>
                                            <th width="15%">Status</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($brands)): ?>
                                            <tr>
                                                <td colspan="7" class="no-data">No brands found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($brands as $brand): ?>
                                                <tr data-id="<?= $brand['id'] ?>" data-type="brand">
                                                    <td><?= $brand['id'] ?></td>
                                                    <td><?= Html::encode($brand['brand_name']) ?></td>
                                                    <td><code><?= $brand['brand_code'] ?? '-' ?></code></td>
                                                    <td><small><?= $brand['website'] ? Html::encode($brand['website']) : '-' ?></small></td>
                                                    <td><small><?= $brand['email'] ?? '-' ?></small></td>
                                                    <td>
                                                        <span class="badge badge-status <?= $brand['is_active'] ? 'badge-success' : 'badge-warning' ?>">
                                                            <?= $brand['is_active'] ? 'Active' : 'Inactive' ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-warning action-btn" onclick="editBrand(<?= $brand['id'] ?>)">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-danger action-btn" onclick="deleteData(<?= $brand['id'] ?>, 'brand')">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- UNITS TAB -->
                        <div role="tabpanel" class="tab-pane" id="units-tab">
                            <div class="data-form">
                                <h4><i class="fa fa-balance-scale"></i> Add New Unit</h4>
                                <div class="form-row-custom">
                                    <div class="form-group">
                                        <label>Unit Name *</label>
                                        <input type="text" class="form-control input-sm" id="unit_name" placeholder="e.g., Piece">
                                    </div>
                                    <div class="form-group">
                                        <label>Short Name *</label>
                                        <input type="text" class="form-control input-sm" id="unit_short" placeholder="e.g., PCS" maxlength="20">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control input-sm" id="unit_description" placeholder="Unit description" rows="2"></textarea>
                                </div>
                                <button class="btn btn-primary btn-sm" onclick="addData('unit')">
                                    <i class="fa fa-plus"></i> Add Unit
                                </button>
                            </div>

                            <div class="data-count">
                                Total Units: <strong><?= count($units) ?></strong>
                            </div>

                            <div class="table-wrapper">
                                <table class="table table-striped table-hover data-table" id="units-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="30%">Name</th>
                                            <th width="20%">Short Name</th>
                                            <th width="30%">Description</th>
                                            <th width="15%">Status</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($units)): ?>
                                            <tr>
                                                <td colspan="6" class="no-data">No units found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($units as $unit): ?>
                                                <tr data-id="<?= $unit['id'] ?>" data-type="unit">
                                                    <td><?= $unit['id'] ?></td>
                                                    <td><?= Html::encode($unit['unit_name']) ?></td>
                                                    <td><code><?= Html::encode($unit['short_name']) ?></code></td>
                                                    <td><small><?= $unit['description'] ?? '-' ?></small></td>
                                                    <td>
                                                        <span class="badge badge-status <?= $unit['is_active'] ? 'badge-success' : 'badge-warning' ?>">
                                                            <?= $unit['is_active'] ? 'Active' : 'Inactive' ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-warning action-btn" onclick="editUnit(<?= $unit['id'] ?>)">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-danger action-btn" onclick="deleteData(<?= $unit['id'] ?>, 'unit')">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- VEHICLE MAKES TAB -->
                        <div role="tabpanel" class="tab-pane" id="makes-tab">
                            <div class="data-form">
                                <h4><i class="fa fa-car"></i> Add New Vehicle Make</h4>
                                <div class="form-row-custom">
                                    <div class="form-group">
                                        <label>Make Name *</label>
                                        <input type="text" class="form-control input-sm" id="make_name" placeholder="e.g., Toyota">
                                    </div>
                                    <div class="form-group">
                                        <label>Make Code</label>
                                        <input type="text" class="form-control input-sm" id="make_code" placeholder="e.g., TYT">
                                    </div>
                                    <div class="form-group">
                                        <label>Country</label>
                                        <input type="text" class="form-control input-sm" id="make_country" placeholder="e.g., Japan">
                                    </div>
                                    <div class="form-group">
                                        <label>Website</label>
                                        <input type="url" class="form-control input-sm" id="make_website" placeholder="https://www.example.com">
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-sm" onclick="addData('make')">
                                    <i class="fa fa-plus"></i> Add Make
                                </button>
                            </div>

                            <div class="data-count">
                                Total Makes: <strong><?= count($makes) ?></strong>
                            </div>

                            <div class="table-wrapper">
                                <table class="table table-striped table-hover data-table" id="makes-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="25%">Name</th>
                                            <th width="15%">Code</th>
                                            <th width="20%">Country</th>
                                            <th width="20%">Website</th>
                                            <th width="15%">Status</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($makes)): ?>
                                            <tr>
                                                <td colspan="7" class="no-data">No makes found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($makes as $make): ?>
                                                <tr data-id="<?= $make['id'] ?>" data-type="make">
                                                    <td><?= $make['id'] ?></td>
                                                    <td><strong><?= Html::encode($make['make_name']) ?></strong></td>
                                                    <td><code><?= $make['make_code'] ?? '-' ?></code></td>
                                                    <td><?= $make['country'] ?? '-' ?></td>
                                                    <td><small><?= $make['website'] ? Html::encode($make['website']) : '-' ?></small></td>
                                                    <td>
                                                        <span class="badge badge-status <?= $make['is_active'] ? 'badge-success' : 'badge-warning' ?>">
                                                            <?= $make['is_active'] ? 'Active' : 'Inactive' ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-warning action-btn" onclick="editMake(<?= $make['id'] ?>)">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-danger action-btn" onclick="deleteData(<?= $make['id'] ?>, 'make')">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- VEHICLE MODELS TAB -->
                        <div role="tabpanel" class="tab-pane" id="models-tab">
                            <div class="data-form">
                                <h4><i class="fa fa-car"></i> Add New Vehicle Model</h4>
                                <div class="form-row-custom">
                                    <div class="form-group">
                                        <label>Vehicle Make *</label>
                                        <select class="form-control input-sm" id="model_make">
                                            <option value="">-- Select Make --</option>
                                            <?php foreach ($makes as $make): ?>
                                                <option value="<?= $make['id'] ?>"><?= Html::encode($make['make_name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Model Name *</label>
                                        <input type="text" class="form-control input-sm" id="model_name" placeholder="e.g., Corolla">
                                    </div>
                                    <div class="form-group">
                                        <label>Model Code</label>
                                        <input type="text" class="form-control input-sm" id="model_code" placeholder="e.g., CR-2023">
                                    </div>
                                    <div class="form-group">
                                        <label>Model Year</label>
                                        <input type="text" class="form-control input-sm" id="model_year" placeholder="e.g., 2023">
                                    </div>
                                </div>
                                <div class="form-row-custom">
                                    <div class="form-group">
                                        <label>Engine Type</label>
                                        <input type="text" class="form-control input-sm" id="model_engine" placeholder="e.g., 1.6L 4-cylinder">
                                    </div>
                                    <div class="form-group">
                                        <label>Engine Capacity</label>
                                        <input type="text" class="form-control input-sm" id="model_capacity" placeholder="e.g., 1600cc">
                                    </div>
                                    <div class="form-group">
                                        <label>Fuel Type</label>
                                        <select class="form-control input-sm" id="model_fuel">
                                            <option value="Petrol">Petrol</option>
                                            <option value="Diesel">Diesel</option>
                                            <option value="Hybrid">Hybrid</option>
                                            <option value="Electric">Electric</option>
                                            <option value="CNG">CNG</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Transmission</label>
                                        <select class="form-control input-sm" id="model_trans">
                                            <option value="Manual">Manual</option>
                                            <option value="Automatic">Automatic</option>
                                            <option value="CVT">CVT</option>
                                        </select>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-sm" onclick="addData('model')">
                                    <i class="fa fa-plus"></i> Add Model
                                </button>
                            </div>

                            <div class="data-count">
                                Total Models: <strong><?= count($models) ?></strong>
                            </div>

                            <div class="table-wrapper">
                                <table class="table table-striped table-hover data-table" id="models-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="15%">Make</th>
                                            <th width="15%">Model</th>
                                            <th width="10%">Year</th>
                                            <th width="15%">Engine</th>
                                            <th width="12%">Fuel</th>
                                            <th width="12%">Trans</th>
                                            <th width="15%">Status</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($models)): ?>
                                            <tr>
                                                <td colspan="9" class="no-data">No models found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($models as $model): ?>
                                                <tr data-id="<?= $model['id'] ?>" data-type="model">
                                                    <td><?= $model['id'] ?></td>
                                                    <td><strong><?= Html::encode($model['make_name']) ?></strong></td>
                                                    <td><?= Html::encode($model['model_name']) ?></td>
                                                    <td><?= $model['model_year'] ?? '-' ?></td>
                                                    <td><small><?= $model['engine_type'] ?? '-' ?></small></td>
                                                    <td><span class="badge badge-info"><?= $model['fuel_type'] ?></span></td>
                                                    <td><span class="badge badge-secondary"><?= $model['transmission'] ?></span></td>
                                                    <td>
                                                        <span class="badge badge-status <?= $model['is_active'] ? 'badge-success' : 'badge-warning' ?>">
                                                            <?= $model['is_active'] ? 'Active' : 'Inactive' ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-warning action-btn" onclick="editModel(<?= $model['id'] ?>)">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-danger action-btn" onclick="deleteData(<?= $model['id'] ?>, 'model')">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification -->
<div class="sync-status" id="syncStatus"></div>

<script>
const baseUrl = '<?= Url::to(['products/loadproductsdata']) ?>';

// Show notification
function showNotification(message, type) {
    const status = document.getElementById('syncStatus');
    status.textContent = message;
    status.className = 'sync-status show ' + type;
    setTimeout(() => {
        status.classList.remove('show');
    }, 3500);
}

// Add Data
function addData(type) {
    let data = {action: 'add_' + type};

    if (type === 'category') {
        data.category_name = document.getElementById('cat_name').value;
        data.category_code = document.getElementById('cat_code').value;
        data.parent_id = document.getElementById('cat_parent').value || null;
        data.description = document.getElementById('cat_description').value;
    } else if (type === 'brand') {
        data.brand_name = document.getElementById('brand_name').value;
        data.brand_code = document.getElementById('brand_code').value;
        data.website = document.getElementById('brand_website').value;
        data.email = document.getElementById('brand_email').value;
        data.phone = document.getElementById('brand_phone').value;
    } else if (type === 'unit') {
        data.unit_name = document.getElementById('unit_name').value;
        data.short_name = document.getElementById('unit_short').value;
        data.description = document.getElementById('unit_description').value;
    } else if (type === 'make') {
        data.make_name = document.getElementById('make_name').value;
        data.make_code = document.getElementById('make_code').value;
        data.country = document.getElementById('make_country').value;
        data.website = document.getElementById('make_website').value;
    } else if (type === 'model') {
        data.make_id = document.getElementById('model_make').value;
        data.model_name = document.getElementById('model_name').value;
        data.model_code = document.getElementById('model_code').value;
        data.model_year = document.getElementById('model_year').value;
        data.engine_type = document.getElementById('model_engine').value;
        data.engine_capacity = document.getElementById('model_capacity').value;
        data.fuel_type = document.getElementById('model_fuel').value;
        data.transmission = document.getElementById('model_trans').value;
    }

    $.ajax({
        url: baseUrl,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function(response) {
            if (response.success) {
                showNotification('✓ ' + response.message, 'success');
                clearForm(type);
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('✗ ' + response.message, 'error');
            }
        },
        error: function() {
            showNotification('✗ Error adding ' + type, 'error');
        }
    });
}

// Delete Data
function deleteData(id, type) {
    if (confirm('Are you sure you want to delete this ' + type + '?')) {
        $.ajax({
            url: baseUrl,
            type: 'POST',
            dataType: 'json',
            data: {action: 'delete_' + type, id: id},
            success: function(response) {
                if (response.success) {
                    showNotification('✓ ' + response.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('✗ ' + response.message, 'error');
                }
            },
            error: function() {
                showNotification('✗ Error deleting ' + type, 'error');
            }
        });
    }
}

// Clear Form
function clearForm(type) {
    if (type === 'category') {
        document.getElementById('cat_name').value = '';
        document.getElementById('cat_code').value = '';
        document.getElementById('cat_parent').value = '';
        document.getElementById('cat_description').value = '';
    } else if (type === 'brand') {
        document.getElementById('brand_name').value = '';
        document.getElementById('brand_code').value = '';
        document.getElementById('brand_website').value = '';
        document.getElementById('brand_email').value = '';
        document.getElementById('brand_phone').value = '';
    } else if (type === 'unit') {
        document.getElementById('unit_name').value = '';
        document.getElementById('unit_short').value = '';
        document.getElementById('unit_description').value = '';
    } else if (type === 'make') {
        document.getElementById('make_name').value = '';
        document.getElementById('make_code').value = '';
        document.getElementById('make_country').value = '';
        document.getElementById('make_website').value = '';
    } else if (type === 'model') {
        document.getElementById('model_make').value = '';
        document.getElementById('model_name').value = '';
        document.getElementById('model_code').value = '';
        document.getElementById('model_year').value = '';
        document.getElementById('model_engine').value = '';
        document.getElementById('model_capacity').value = '';
        document.getElementById('model_fuel').value = 'Petrol';
        document.getElementById('model_trans').value = 'Manual';
    }
}

// Tab switcher
$(document).on('click', '.nav-link', function(e) {
    e.preventDefault();
    const target = $(this).attr('href');
    $('.tab-pane').removeClass('active');
    $(target).addClass('active');
    $(this).addClass('active').siblings().removeClass('active');
});

// Edit functions (stubs - can be expanded)
function editCategory(id) { alert('Edit functionality for categories coming soon!'); }
function editBrand(id) { alert('Edit functionality for brands coming soon!'); }
function editUnit(id) { alert('Edit functionality for units coming soon!'); }
function editMake(id) { alert('Edit functionality for makes coming soon!'); }
function editModel(id) { alert('Edit functionality for models coming soon!'); }
</script>

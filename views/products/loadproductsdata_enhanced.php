<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Load Products Data - Smart Import System';
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

    .import-section {
        background: #fff3cd;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #ffc107;
        margin-bottom: 20px;
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

    /* Import Modal Styles */
    .import-modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .import-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .import-modal-content {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        width: 95%;
    }

    .import-modal-close {
        float: right;
        font-size: 24px;
        cursor: pointer;
        color: #999;
    }

    .import-modal-close:hover {
        color: #000;
    }

    .import-data-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .import-item {
        border: 1px solid #ddd;
        padding: 12px;
        border-radius: 4px;
        background: #f9f9f9;
    }

    .import-item input[type="checkbox"] {
        margin-right: 8px;
    }

    .import-item.selected {
        background: #d4edda;
        border-color: #28a745;
    }

    .progress-bar-wrapper {
        margin: 20px 0;
    }

    .progress-container {
        display: none;
        margin: 20px 0;
    }

    .progress-container.show {
        display: block;
    }

    .progress-text {
        font-size: 12px;
        margin-bottom: 5px;
    }

    .progress {
        height: 25px;
        border-radius: 4px;
        background: #e9ecef;
    }

    .progress-bar {
        background: linear-gradient(90deg, #0f4c29, #16a34a);
        text-align: center;
        color: white;
        font-size: 12px;
        font-weight: bold;
        line-height: 25px;
        transition: width 0.3s ease;
    }

    .import-results {
        display: none;
        margin-top: 20px;
        padding: 15px;
        border-radius: 4px;
        background: #f0f0f0;
    }

    .import-results.show {
        display: block;
    }

    .result-item {
        font-size: 12px;
        padding: 5px;
        margin: 3px 0;
    }

    .result-item.success {
        color: #155724;
    }

    .result-item.warning {
        color: #856404;
    }

    .result-item.error {
        color: #721c24;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-upload"></i> Smart Product Data Insertion
                        </h3>
                        <small class="text-muted">Import data from internet or add manually</small>
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
                            <div class="import-section">
                                <h5><i class="fa fa-download"></i> Import Categories from Internet</h5>
                                <p class="text-muted">Fetch popular automotive categories and select which ones to add</p>
                                <button class="btn btn-success btn-sm" onclick="fetchExternalData('categories')">
                                    <i class="fa fa-cloud-download"></i> Fetch Categories
                                </button>
                            </div>

                            <div class="data-form">
                                <h4><i class="fa fa-tags"></i> Add New Category Manually</h4>
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
                            <div class="import-section">
                                <h5><i class="fa fa-download"></i> Import Brands from Internet</h5>
                                <p class="text-muted">Fetch popular automotive part brands and select which ones to add</p>
                                <button class="btn btn-success btn-sm" onclick="fetchExternalData('brands')">
                                    <i class="fa fa-cloud-download"></i> Fetch Brands
                                </button>
                            </div>

                            <div class="data-form">
                                <h4><i class="fa fa-certificate"></i> Add New Brand Manually</h4>
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
                                        <input type="tel" class="form-control input-sm" id="brand_phone" placeholder="+1 234 567 890">
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
                                            <th width="10%">Code</th>
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
                                                    <td><?php echo !empty($brand['website']) ? '<a href="' . Html::encode($brand['website']) . '" target="_blank">' . Html::encode($brand['website']) . '</a>' : '-'; ?></td>
                                                    <td><?= Html::encode($brand['email'] ?? '-') ?></td>
                                                    <td>
                                                        <span class="badge badge-status <?= $brand['is_active'] ? 'badge-success' : 'badge-warning' ?>">
                                                            <?= $brand['is_active'] ? 'Active' : 'Inactive' ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-danger action-btn" onclick="deleteData(<?= $brand['id'] ?>, 'brand')">
                                                            <i class="fa fa-trash"></i>
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
                            <div class="import-section">
                                <h5><i class="fa fa-download"></i> Import Units from Internet</h5>
                                <p class="text-muted">Fetch standard measurement units and select which ones to add</p>
                                <button class="btn btn-success btn-sm" onclick="fetchExternalData('units')">
                                    <i class="fa fa-cloud-download"></i> Fetch Units
                                </button>
                            </div>

                            <div class="data-form">
                                <h4><i class="fa fa-balance-scale"></i> Add New Unit Manually</h4>
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
                                            <th width="25%">Unit Name</th>
                                            <th width="15%">Short Name</th>
                                            <th width="30%">Description</th>
                                            <th width="15%">Status</th>
                                            <th width="10%">Actions</th>
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
                                                    <td><code><?= $unit['short_name'] ?></code></td>
                                                    <td><?= Html::encode($unit['description'] ?? '') ?></td>
                                                    <td>
                                                        <span class="badge badge-status <?= $unit['is_active'] ? 'badge-success' : 'badge-warning' ?>">
                                                            <?= $unit['is_active'] ? 'Active' : 'Inactive' ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-danger action-btn" onclick="deleteData(<?= $unit['id'] ?>, 'unit')">
                                                            <i class="fa fa-trash"></i>
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
                            <div class="import-section">
                                <h5><i class="fa fa-download"></i> Import Vehicle Makes from Internet</h5>
                                <p class="text-muted">Fetch popular car manufacturers from worldwide and select which ones to add</p>
                                <button class="btn btn-success btn-sm" onclick="fetchExternalData('makes')">
                                    <i class="fa fa-cloud-download"></i> Fetch Vehicle Makes
                                </button>
                            </div>

                            <div class="data-form">
                                <h4><i class="fa fa-car"></i> Add New Vehicle Make Manually</h4>
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
                                            <th width="20%">Make Name</th>
                                            <th width="10%">Code</th>
                                            <th width="20%">Country</th>
                                            <th width="25%">Website</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Actions</th>
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
                                                    <td><?= Html::encode($make['make_name']) ?></td>
                                                    <td><code><?= $make['make_code'] ?? '-' ?></code></td>
                                                    <td><?= Html::encode($make['country'] ?? '-') ?></td>
                                                    <td><?php echo !empty($make['website']) ? '<a href="' . Html::encode($make['website']) . '" target="_blank">' . Html::encode($make['website']) . '</a>' : '-'; ?></td>
                                                    <td>
                                                        <span class="badge badge-status <?= $make['is_active'] ? 'badge-success' : 'badge-warning' ?>">
                                                            <?= $make['is_active'] ? 'Active' : 'Inactive' ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-danger action-btn" onclick="deleteData(<?= $make['id'] ?>, 'make')">
                                                            <i class="fa fa-trash"></i>
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
                            <div class="import-section">
                                <h5><i class="fa fa-download"></i> Import Vehicle Models from Internet</h5>
                                <p class="text-muted">Fetch specific car models for selected makes and add them</p>
                                <div class="form-row-custom" style="margin-top: 10px;">
                                    <div class="form-group">
                                        <label>Select Make:</label>
                                        <select class="form-control input-sm" id="model_make_select">
                                            <option value="">-- Select a Make --</option>
                                            <?php foreach ($makes as $make): ?>
                                                <option value="<?= $make['id'] ?>"><?= Html::encode($make['make_name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-success btn-sm" onclick="fetchModelsForMake()">
                                            <i class="fa fa-cloud-download"></i> Fetch Models
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="data-form">
                                <h4><i class="fa fa-car"></i> Add New Vehicle Model Manually</h4>
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
                                        <label>Model Year</label>
                                        <input type="text" class="form-control input-sm" id="model_year" placeholder="e.g., 2023">
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
                                        <select class="form-control input-sm" id="model_transmission">
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
                                            <th width="12%">Fuel</th>
                                            <th width="12%">Transmission</th>
                                            <th width="10%">Status</th>
                                            <th width="11%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($models)): ?>
                                            <tr>
                                                <td colspan="8" class="no-data">No models found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($models as $model): ?>
                                                <tr data-id="<?= $model['id'] ?>" data-type="model">
                                                    <td><?= $model['id'] ?></td>
                                                    <td><?= Html::encode($model['make_name']) ?></td>
                                                    <td><?= Html::encode($model['model_name']) ?></td>
                                                    <td><?= Html::encode($model['model_year'] ?? '-') ?></td>
                                                    <td><span class="badge badge-primary"><?= $model['fuel_type'] ?></span></td>
                                                    <td><?= Html::encode($model['transmission'] ?? '-') ?></td>
                                                    <td>
                                                        <span class="badge badge-status <?= $model['is_active'] ? 'badge-success' : 'badge-warning' ?>">
                                                            <?= $model['is_active'] ? 'Active' : 'Inactive' ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-danger action-btn" onclick="deleteData(<?= $model['id'] ?>, 'model')">
                                                            <i class="fa fa-trash"></i>
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

<!-- Import Modal -->
<div id="importModal" class="import-modal">
    <div class="import-modal-content">
        <span class="import-modal-close" onclick="closeImportModal()">&times;</span>
        <h3>
            <i class="fa fa-plus-circle"></i>
            <span id="importModalTitle">Import Data</span>
        </h3>
        <p id="importModalDesc" class="text-muted"></p>

        <div id="importDataGrid" class="import-data-grid"></div>

        <div class="progress-container" id="progressContainer">
            <div class="progress-text">
                <span id="progressText">Processing: 0/0</span>
            </div>
            <div class="progress">
                <div class="progress-bar" id="progressBar" style="width: 0%;">0%</div>
            </div>
        </div>

        <div class="import-results" id="importResults">
            <h5>Results:</h5>
            <div id="resultsList"></div>
        </div>

        <div style="margin-top: 20px; text-align: right;">
            <button class="btn btn-secondary" onclick="closeImportModal()">
                <i class="fa fa-times"></i> Close
            </button>
            <button class="btn btn-primary" id="injectButton" onclick="injectBulkData()">
                <i class="fa fa-download"></i> <span id="injectButtonText">Inject Selected</span>
            </button>
        </div>
    </div>
</div>

<script>
let currentImportData = [];
let currentImportType = '';
let currentMakeId = '';

function fetchExternalData(type) {
    showToast('Fetching ' + type + ' from internet...', 'info');

    $.ajax({
        url: '<?= Url::to(['products/fetchexternaldata']) ?>',
        type: 'POST',
        data: { type: type },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                currentImportData = response.data;
                currentImportType = type;
                showImportModal(type, response.data);
                showToast('Fetched ' + response.count + ' ' + type + ' successfully!', 'success');
            } else {
                showToast('Error: ' + response.message, 'error');
            }
        },
        error: function() {
            showToast('Failed to fetch data from internet', 'error');
        }
    });
}

function fetchModelsForMake() {
    const makeId = document.getElementById('model_make_select').value;
    if (!makeId) {
        showToast('Please select a vehicle make first', 'warning');
        return;
    }

    currentMakeId = makeId;
    fetchExternalData('models');
}

function showImportModal(type, data) {
    const modal = document.getElementById('importModal');
    const grid = document.getElementById('importDataGrid');
    const title = document.getElementById('importModalTitle');
    const desc = document.getElementById('importModalDesc');

    // Set title and description
    const titles = {
        'makes': 'Vehicle Makes',
        'brands': 'Automotive Brands',
        'categories': 'Auto Parts Categories',
        'units': 'Measurement Units',
        'models': 'Vehicle Models'
    };

    title.textContent = 'Import ' + titles[type];
    desc.textContent = 'Select which items you want to add to your database (click to select/deselect)';

    // Create grid items
    grid.innerHTML = '';
    data.forEach((item, index) => {
        const name = item.make_name || item.brand_name || item.category_name || item.unit_name || item.model_name || 'Unknown';
        const div = document.createElement('div');
        div.className = 'import-item';
        div.innerHTML = `
            <label style="cursor: pointer; display: flex; align-items: center;">
                <input type="checkbox" class="import-checkbox" data-index="${index}" value="${index}">
                <span>${name}</span>
            </label>
        `;
        div.onclick = function(e) {
            if (e.target.tagName !== 'INPUT') {
                const checkbox = div.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
            }
            updateItemSelection();
        };
        grid.appendChild(div);
    });

    modal.classList.add('show');
}

function updateItemSelection() {
    const items = document.querySelectorAll('.import-item');
    items.forEach(item => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (checkbox.checked) {
            item.classList.add('selected');
        } else {
            item.classList.remove('selected');
        }
    });
}

function closeImportModal() {
    const modal = document.getElementById('importModal');
    modal.classList.remove('show');
    currentImportData = [];
    currentImportType = '';
}

function injectBulkData() {
    const selected = [];
    document.querySelectorAll('.import-checkbox:checked').forEach(checkbox => {
        selected.push(parseInt(checkbox.value));
    });

    if (selected.length === 0) {
        showToast('Please select at least one item to inject', 'warning');
        return;
    }

    const progressContainer = document.getElementById('progressContainer');
    const resultsList = document.getElementById('resultsList');

    progressContainer.classList.add('show');
    resultsList.innerHTML = '';

    const injectButton = document.getElementById('injectButton');
    injectButton.disabled = true;

    const postData = {
        type: currentImportType,
        data: JSON.stringify(currentImportData),
        selected: JSON.stringify(selected)
    };

    // Add make_id for models
    if (currentImportType === 'models' && currentMakeId) {
        postData.make_id = currentMakeId;
    }

    $.ajax({
        url: '<?= Url::to(['products/injectbulkdata']) ?>',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update progress bar
                const progress = response.progress || 100;
                document.getElementById('progressBar').style.width = progress + '%';
                document.getElementById('progressBar').textContent = progress + '%';
                document.getElementById('progressText').textContent = 'Completed: ' + response.inserted + ' inserted, ' + response.skipped + ' skipped';

                // Display results
                const results = document.getElementById('importResults');
                results.classList.add('show');

                if (response.messages && response.messages.length > 0) {
                    let html = '';
                    response.messages.forEach(msg => {
                        let cls = 'success';
                        if (msg.includes('Skipped')) cls = 'warning';
                        if (msg.includes('Error')) cls = 'error';
                        html += '<div class="result-item ' + cls + '">' + msg + '</div>';
                    });
                    resultsList.innerHTML = html;
                }

                showToast(response.inserted + ' records injected successfully!', 'success');

                // Refresh the table after a delay
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showToast('Error: ' + response.message, 'error');
            }

            injectButton.disabled = false;
        },
        error: function(xhr, status, error) {
            showToast('Error during injection: ' + error, 'error');
            injectButton.disabled = false;
        }
    });
}

function showToast(message, type) {
    const toast = $('<div class="alert alert-' + (type === 'error' ? 'danger' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info') + ' alert-dismissible fade show" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
        '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
        '<i class="fa fa-' + (type === 'success' ? 'check' : type === 'error' ? 'times' : 'info') + '-circle"></i> ' + message +
        '</div>');
    $('body').append(toast);
    setTimeout(() => toast.remove(), 4000);
}

function addData(type) {
    // Existing addData function (same as before)
    let data = {};

    if (type === 'category') {
        data = {
            action: 'add_category',
            category_name: $('#cat_name').val(),
            category_code: $('#cat_code').val(),
            parent_id: $('#cat_parent').val() || null,
            description: $('#cat_description').val()
        };
    } else if (type === 'brand') {
        data = {
            action: 'add_brand',
            brand_name: $('#brand_name').val(),
            brand_code: $('#brand_code').val(),
            website: $('#brand_website').val(),
            email: $('#brand_email').val(),
            phone: $('#brand_phone').val()
        };
    } else if (type === 'unit') {
        data = {
            action: 'add_unit',
            unit_name: $('#unit_name').val(),
            short_name: $('#unit_short').val(),
            description: $('#unit_description').val()
        };
    } else if (type === 'make') {
        data = {
            action: 'add_make',
            make_name: $('#make_name').val(),
            make_code: $('#make_code').val(),
            country: $('#make_country').val(),
            website: $('#make_website').val()
        };
    } else if (type === 'model') {
        data = {
            action: 'add_model',
            make_id: $('#model_make').val(),
            model_name: $('#model_name').val(),
            model_code: '',
            model_year: $('#model_year').val(),
            engine_type: '',
            fuel_type: $('#model_fuel').val(),
            transmission: $('#model_transmission').val()
        };
    }

    $.ajax({
        url: '<?= Url::to(['products/loadproductsdata']) ?>',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(response.message, 'error');
            }
        }
    });
}

function deleteData(id, type) {
    if (!confirm('Are you sure you want to delete this ' + type + '?')) return;

    let data = { id: id };
    if (type === 'category') data.action = 'delete_category';
    else if (type === 'brand') data.action = 'delete_brand';
    else if (type === 'unit') data.action = 'delete_unit';
    else if (type === 'make') data.action = 'delete_make';
    else if (type === 'model') data.action = 'delete_model';

    $.ajax({
        url: '<?= Url::to(['products/loadproductsdata']) ?>',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(response.message, 'error');
            }
        }
    });
}
</script>

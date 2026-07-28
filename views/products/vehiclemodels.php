<?php
use yii\helpers\Html;
if (!isset($vehiclemodels)) {
    $vehiclemodels = [];
}
if (!isset($vehiclemakes)) {
    $vehiclemakes = [];
}
?>

<style>
    .vehiclemodels-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .search-filter-group {
        display: flex;
        gap: 10px;
        flex: 1;
        min-width: 300px;
        align-items: center;
    }

    .search-filter-group input, .search-filter-group select {
        height: 36px;
        font-size: 13px;
        padding: 8px 12px;
    }

    .model-table-container {
        overflow-x: auto;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .model-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    .model-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .model-table thead th {
        padding: 15px;
        font-weight: 600;
        text-align: left;
        font-size: 13px;
        border-bottom: 2px solid #667eea;
    }

    .model-table tbody tr {
        border-bottom: 1px solid #E8EDF2;
        transition: background-color 0.2s ease;
    }

    .model-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .model-table tbody tr.filtered-out {
        display: none;
    }

    .model-table td {
        padding: 12px 15px;
        font-size: 13px;
        color: #333;
    }

    .model-table .make-badge {
        display: inline-block;
        background: #e3f2fd;
        color: #1976d2;
        padding: 4px 8px;
        border-radius: 3px;
        font-weight: 500;
    }

    .model-table .fuel-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 500;
    }

    .model-table .fuel-badge.petrol {
        background: #c8e6c9;
        color: #2e7d32;
    }

    .model-table .fuel-badge.diesel {
        background: #ffe0b2;
        color: #e65100;
    }

    .model-table .trans-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 500;
        background: #f3e5f5;
        color: #6a1b9a;
    }

    .product-count {
        font-weight: 600;
        color: #667eea;
        font-size: 14px;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .action-buttons button {
        padding: 6px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
    }

    .edit-btn {
        background: #4CAF50;
        color: white;
    }

    .edit-btn:hover {
        background: #45a049;
    }

    .delete-btn {
        background: #f44336;
        color: white;
    }

    .delete-btn:hover {
        background: #da190b;
    }

    .products-row {
        display: none;
    }

    .products-row.show {
        display: table-row;
    }

    .products-cell {
        padding: 0 !important;
    }

    .products-list {
        background: #f5f5f5;
        padding: 15px;
        border-radius: 4px;
    }

    .products-list .product-item {
        background: white;
        padding: 8px 12px;
        margin: 5px 0;
        border-radius: 3px;
        border-left: 3px solid #667eea;
        font-size: 12px;
        display: inline-block;
        margin-right: 10px;
    }

    .no-data-alert {
        text-align: center;
        padding: 40px;
    }

    .no-data-alert i {
        color: #6FB3E0;
        margin-bottom: 15px;
    }

    .stats-row {
        display: flex;
        gap: 15px;
        margin-top: 20px;
        justify-content: flex-start;
    }

    .stat-card {
        background: white;
        padding: 15px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        flex: 1;
        max-width: 200px;
        text-align: center;
    }

    .stat-card .stat-value {
        font-size: 24px;
        font-weight: 600;
        color: #667eea;
    }

    .stat-card .stat-label {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li class="active"><i class="fa fa-car"></i> Vehicle Models</li>
            </ul>
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <!-- Statistics -->
                    <div class="stats-row">
                        <div class="stat-card">
                            <div class="stat-value"><?= count($vehiclemodels) ?></div>
                            <div class="stat-label">Total Models</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?= count($vehiclemakes) ?></div>
                            <div class="stat-label">Vehicle Makes</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="totalProducts">0</div>
                            <div class="stat-label">Products Linked</div>
                        </div>
                    </div>

                    <!-- Search & Filter Section -->
                    <div class="vehiclemodels-header" style="margin-top: 30px;">
                        <div class="search-filter-group">
                            <i class="fa fa-search" style="color: #999;"></i>
                            <input type="text" id="modelSearch" class="form-control"
                                placeholder="Search by model name, make, code...">

                            <select id="makeFilter" class="form-control" style="max-width: 200px;">
                                <option value="">All Makes</option>
                                <?php foreach ($vehiclemakes as $make): ?>
                                    <option value="<?= htmlspecialchars($make['make_name']) ?>">
                                        <?= htmlspecialchars($make['make_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select id="fuelFilter" class="form-control" style="max-width: 150px;">
                                <option value="">All Fuel Types</option>
                                <option value="Petrol">Petrol</option>
                                <option value="Diesel">Diesel</option>
                            </select>

                            <button id="clearFilters" class="btn btn-sm btn-default" title="Clear filters">
                                <i class="fa fa-times"></i> Clear
                            </button>
                        </div>

                        <div>
                            <button class="btn btn-sm btn-primary" onclick="openVehicleModelModal()">
                                <i class="fa fa-plus"></i> Add Model
                            </button>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="model-table-container" style="margin-top: 20px;">
                        <?php if (count($vehiclemodels) == 0) { ?>
                            <div class="alert alert-info no-data-alert">
                                <i class="fa fa-info-circle fa-3x"></i>
                                <h4 style="margin-top:15px;">No Vehicle Models Found</h4>
                                <p>Start by adding your first vehicle model using the button above</p>
                            </div>
                        <?php } else { ?>
                            <table class="model-table">
                                <thead>
                                    <tr>
                                        <th style="width: 120px;">Make</th>
                                        <th style="width: 140px;">Model Name</th>
                                        <th style="width: 100px;">Code</th>
                                        <th style="width: 80px;">Year</th>
                                        <th style="width: 90px;">Fuel Type</th>
                                        <th style="width: 110px;">Transmission</th>
                                        <th style="width: 120px;">Engine Type</th>
                                        <th style="width: 100px;">Products</th>
                                        <th style="width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="modelsTable">
                                    <?php foreach ($vehiclemodels as $item): ?>
                                        <tr class="model-row" data-make="<?= htmlspecialchars($item['make_name'] ?? '') ?>"
                                            data-fuel="<?= htmlspecialchars($item['fuel_type'] ?? '') ?>"
                                            data-searchtext="<?= htmlspecialchars(strtolower($item['model_name'] . ' ' . $item['make_name'] . ' ' . ($item['model_code'] ?? ''))) ?>">
                                            <td>
                                                <span class="make-badge">
                                                    <i class="fa fa-car"></i> <?= htmlspecialchars($item['make_name'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($item['model_name'] ?? 'N/A') ?></strong>
                                            </td>
                                            <td>
                                                <code><?= htmlspecialchars($item['model_code'] ?? '-') ?></code>
                                            </td>
                                            <td>
                                                <i class="fa fa-calendar"></i> <?= htmlspecialchars($item['model_year'] ?? '-') ?>
                                            </td>
                                            <td>
                                                <span class="fuel-badge <?= strtolower($item['fuel_type'] ?? '') ?>">
                                                    <i class="fa fa-<?= $item['fuel_type'] === 'Diesel' ? 'tint' : 'flash' ?>"></i>
                                                    <?= htmlspecialchars($item['fuel_type'] ?? '-') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="trans-badge">
                                                    <i class="fa fa-gears"></i> <?= htmlspecialchars($item['transmission'] ?? '-') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?= htmlspecialchars($item['engine_type'] ?? '-') ?></small>
                                                <?php if (!empty($item['engine_capacity'])): ?>
                                                    <br><small style="color: #999;"><?= htmlspecialchars($item['engine_capacity']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="product-count" data-model-id="<?= $item['id'] ?>">
                                                    <?= $item['total_products'] ?? 0 ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="edit-btn" onclick="openVehicleModelModal(<?= htmlspecialchars(json_encode($item)) ?>)" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="delete-btn" onclick="deleteVehicleModel(<?= $item['id'] ?>)" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>

                    <!-- Product Filter Section -->
                    <div style="margin-top: 30px; background: white; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <h4><i class="fa fa-cubes"></i> Filter Products by Model</h4>
                        <div style="margin-top: 15px;">
                            <div class="search-filter-group">
                                <i class="fa fa-search" style="color: #999;"></i>
                                <input type="text" id="productSearch" class="form-control"
                                    placeholder="Search products by name, SKU, category...">

                                <select id="modelSelect" class="form-control" style="max-width: 250px;">
                                    <option value="">All Models</option>
                                    <?php foreach ($vehiclemodels as $model): ?>
                                        <option value="<?= $model['id'] ?>">
                                            <?= htmlspecialchars($model['make_name'] . ' ' . $model['model_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <button id="loadProducts" class="btn btn-sm btn-info">
                                    <i class="fa fa-search"></i> Load Products
                                </button>
                            </div>
                        </div>
                        <div id="productsContainer" style="margin-top: 15px;">
                            <p class="text-muted">Select a model and click "Load Products" to see associated products</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Real-time search and filter
    function applyFilters() {
        const searchText = document.getElementById('modelSearch').value.toLowerCase();
        const makeFilter = document.getElementById('makeFilter').value;
        const fuelFilter = document.getElementById('fuelFilter').value;

        let visibleCount = 0;
        document.querySelectorAll('.model-row').forEach(row => {
            let show = true;

            // Search text filter
            if (searchText && !row.dataset.searchtext.includes(searchText)) {
                show = false;
            }

            // Make filter
            if (makeFilter && row.dataset.make !== makeFilter) {
                show = false;
            }

            // Fuel filter
            if (fuelFilter && row.dataset.fuel !== fuelFilter) {
                show = false;
            }

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        // Show "No results" message
        const table = document.getElementById('modelsTable');
        if (visibleCount === 0) {
            table.innerHTML += '<tr><td colspan="9" style="text-align: center; padding: 20px;">No models match your search criteria</td></tr>';
        }
    }

    // Event listeners for filters
    document.getElementById('modelSearch').addEventListener('keyup', applyFilters);
    document.getElementById('makeFilter').addEventListener('change', applyFilters);
    document.getElementById('fuelFilter').addEventListener('change', applyFilters);

    document.getElementById('clearFilters').addEventListener('click', function() {
        document.getElementById('modelSearch').value = '';
        document.getElementById('makeFilter').value = '';
        document.getElementById('fuelFilter').value = '';
        applyFilters();
    });

    // Load products by model
    document.getElementById('loadProducts').addEventListener('click', function() {
        const modelId = document.getElementById('modelSelect').value;
        const container = document.getElementById('productsContainer');

        if (!modelId) {
            container.innerHTML = '<div class="alert alert-warning"><i class="fa fa-warning"></i> Please select a model first</div>';
            return;
        }

        container.innerHTML = '<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading products...</div>';

        fetch('index.php?r=products/getmodelproducts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
            },
            body: 'model_id=' + modelId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.products && data.products.length > 0) {
                let html = '<div class="products-list">';
                html += '<p><strong>Total Products: ' + data.products.length + '</strong></p>';
                data.products.forEach(product => {
                    html += '<div class="product-item">';
                    html += '<strong>' + product.product_name + '</strong><br>';
                    html += '<small>SKU: ' + product.sku + ' | Category: ' + product.category_name + '</small>';
                    html += '</div>';
                });
                html += '</div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="alert alert-info"><i class="fa fa-info-circle"></i> No products found for this model</div>';
            }
        })
        .catch(error => {
            container.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation"></i> Error loading products</div>';
        });
    });

    function openVehicleModelModal(vehicleModelData=null) {
        const isEdit = vehicleModelData !== null;
        const title = isEdit ? 'Update Vehicle Model' : 'New Vehicle Model';
        const id = isEdit ? (vehicleModelData.id || '') : '';
        const makeId = isEdit ? (vehicleModelData.make_id || '') : '';
        const modelName = isEdit ? (vehicleModelData.model_name || '') : '';
        const modelCode = isEdit ? (vehicleModelData.model_code || '') : '';
        const modelYear = isEdit ? (vehicleModelData.model_year || '') : '';
        const engineType = isEdit ? (vehicleModelData.engine_type || '') : '';
        const engineCapacity = isEdit ? (vehicleModelData.engine_capacity || '') : '';
        const fuelType = isEdit ? (vehicleModelData.fuel_type || '') : '';
        const transmission = isEdit ? (vehicleModelData.transmission || '') : '';
        const notes = isEdit ? (vehicleModelData.notes || '') : '';
        const isActive = isEdit && (vehicleModelData.is_active == 1 || vehicleModelData.is_active == '1');

        Swal.fire({
            title: title,
            html: `
            <form style="text-align:left;">
            <input type="hidden" id="swal_vehicle_model_id" value="${id}">
            <div class="row">
            <div class="col-md-6">
            <label>Vehicle Make <span class="text-danger">*</span></label>
            <select id="swal_make_id" class="form-control">
            <?php foreach($vehiclemakes as $make): ?>
            <option value="<?= $make['id'] ?>" ${makeId=='<?= $make['id'] ?>'?'selected':''}>
            <?= htmlspecialchars($make['make_name']) ?>
            </option>
            <?php endforeach; ?>
            </select>
            </div>
            <div class="col-md-6">
            <label>Model Name <span class="text-danger">*</span></label>
            <input type="text" id="swal_model_name" class="form-control" value="${modelName}">
            </div>
            </div>
            <div class="row">
            <div class="col-md-6">
            <label>Model Code</label>
            <input type="text" id="swal_model_code" class="form-control" value="${modelCode}">
            </div>
            <div class="col-md-6">
            <label>Model Year</label>
            <input type="text" id="swal_model_year" class="form-control" value="${modelYear}">
            </div>
            </div>
            <div class="row">
            <div class="col-md-6">
            <label>Engine Type</label>
            <input type="text" id="swal_engine_type" class="form-control" value="${engineType}">
            </div>
            <div class="col-md-6">
            <label>Engine Capacity</label>
            <input type="text" id="swal_engine_capacity" class="form-control" value="${engineCapacity}">
            </div>
            </div>
            <div class="row">
            <div class="col-md-6">
            <label>Fuel Type</label>
            <input type="text" id="swal_fuel_type" class="form-control" value="${fuelType}">
            </div>
            <div class="col-md-6">
            <label>Transmission</label>
            <input type="text" id="swal_transmission" class="form-control" value="${transmission}">
            </div>
            </div>
            <div class="row">
            <div class="col-md-12">
            <label>Notes</label>
            <input type="text" id="swal_notes" class="form-control" value="${notes}">
            </div>
            </div>
            <div class="form-group" style="margin-top:10px;">
            <label>
            <input type="checkbox" id="swal_active" ${isActive?'checked':''}>
            Active
            </label>
            </div>
            </form>
            `,
            width: '700px',
            showCancelButton: true,
            confirmButtonText: isEdit ? '<i class="ace-icon fa fa-save"></i> Update' : '<i class="ace-icon fa fa-save"></i> Create',
            cancelButtonText: '<i class="ace-icon fa fa-times"></i> Cancel',
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#6c757d',
            focusConfirm: false,
            preConfirm: () => {
                const name = document.getElementById('swal_model_name').value.trim();
                if (!name) {
                    Swal.showValidationMessage('Model name is required');
                    return false;
                }
                return {
                    id: document.getElementById('swal_vehicle_model_id').value,
                    make_id: document.getElementById('swal_make_id').value,
                    model_name: name,
                    model_code: document.getElementById('swal_model_code').value,
                    model_year: document.getElementById('swal_model_year').value,
                    engine_type: document.getElementById('swal_engine_type').value,
                    engine_capacity: document.getElementById('swal_engine_capacity').value,
                    fuel_type: document.getElementById('swal_fuel_type').value,
                    transmission: document.getElementById('swal_transmission').value,
                    notes: document.getElementById('swal_notes').value,
                    active: document.getElementById('swal_active').checked
                };
            }
        }).then(result => {
            if (result.isConfirmed && result.value) {
                saveVehicleModel(result.value);
            }
        });
    }

    function saveVehicleModel(formData) {
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('id', formData.id);
        data.append('make_id', formData.make_id);
        data.append('model_name', formData.model_name);
        data.append('model_code', formData.model_code);
        data.append('model_year', formData.model_year);
        data.append('engine_type', formData.engine_type);
        data.append('engine_capacity', formData.engine_capacity);
        data.append('fuel_type', formData.fuel_type);
        data.append('transmission', formData.transmission);
        data.append('notes', formData.notes);

        if (formData.active) {
            data.append('is_active', '1');
        }

        fetch('index.php?r=products/vehiclemodels', {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    $('.ajax-module.active').trigger('click');
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'An error occurred. Please try again.', 'error');
        });
    }

    function deleteVehicleModel(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Vehicle model will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if (result.isConfirmed) {
                const data = new FormData();
                data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
                data.append('id', id);
                data.append('delete', '1');

                fetch('index.php?r=products/vehiclemodels', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('.ajax-module.active').trigger('click');
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
            }
        });
    }
</script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    
document.getElementById('vehicleModelSearch').addEventListener('keyup',function(){
    let value=this.value.toLowerCase();
    document.querySelectorAll('.vehiclemodel-item').forEach(function(item){
        item.style.display=item.innerText.toLowerCase().includes(value)?'':'none';
    });
});
function openVehicleModelModal(vehicleModelData=null){
    const isEdit=vehicleModelData!==null;
    const title=isEdit?'Update Vehicle Model':'New Vehicle Model';
    const id=isEdit?(vehicleModelData.id||''):'';
    const makeId=isEdit?(vehicleModelData.make_id||''):'';
    const modelName=isEdit?(vehicleModelData.model_name||''):'';
    const modelCode=isEdit?(vehicleModelData.model_code||''):'';
    const modelYear=isEdit?(vehicleModelData.model_year||''):'';
    const engineType=isEdit?(vehicleModelData.engine_type||''):'';
    const engineCapacity=isEdit?(vehicleModelData.engine_capacity||''):'';
    const fuelType=isEdit?(vehicleModelData.fuel_type||''):'';
    const transmission=isEdit?(vehicleModelData.transmission||''):'';
    const notes=isEdit?(vehicleModelData.notes||''):'';
    const isActive=isEdit&&(vehicleModelData.is_active==1||vehicleModelData.is_active=='1');

    Swal.fire({
        title:title,
        html:`
        <form style="text-align:left;">
        <input type="hidden" id="swal_vehicle_model_id" value="${id}">
        <div class="row">
        <div class="col-md-6">
        <label>Vehicle Make <span class="text-danger">*</span></label>
        <select id="swal_make_id" class="form-control">
        <?php foreach($vehiclemakes as $make): ?>
        <option value="<?= $make['id'] ?>" ${makeId=='<?= $make['id'] ?>'?'selected':''}>
        <?= htmlspecialchars($make['make_name']) ?>
        </option>
        <?php endforeach; ?>
        </select>
        </div>
        <div class="col-md-6">
        <label>Model Name <span class="text-danger">*</span></label>
        <input type="text" id="swal_model_name" class="form-control" value="${modelName}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
        <label>Model Code</label>
        <input type="text" id="swal_model_code" class="form-control" value="${modelCode}">
        </div>
        <div class="col-md-6">
        <label>Model Year</label>
        <input type="tezt" id="swal_model_year" class="form-control" value="${modelYear}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
        <label>Engine Type</label>
        <input type="text" id="swal_engine_type" class="form-control" value="${engineType}">
        </div>
        <div class="col-md-6">
        <label>Engine Capacity</label>
        <input type="text" id="swal_engine_capacity" class="form-control" value="${engineCapacity}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
        <label>Fuel Type</label>
        <input type="text" id="swal_fuel_type" class="form-control" value="${fuelType}">
        </div>
        <div class="col-md-6">
        <label>Transmission</label>
        <input type="text" id="swal_transmission" class="form-control" value="${transmission}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-12">
        <label>Notes</label>
        <input type="text" id="swal_notes" class="form-control" value="${notes}">
        </div>
        </div>
        <div class="form-group" style="margin-top:10px;">
        <label>
        <input type="checkbox" id="swal_active" ${isActive?'checked':''}>
        Active
        </label>
        </div>
        </form>
        `,
        width:'700px',
        showCancelButton:true,
        confirmButtonText:isEdit?'<i class="ace-icon fa fa-save"></i> Update Vehicle Model':'<i class="ace-icon fa fa-save"></i> Create Vehicle Model',
        cancelButtonText:'<i class="ace-icon fa fa-times"></i> Cancel',
        confirmButtonColor:'#87B87F',
        cancelButtonColor:'#6c757d',
        focusConfirm:false,
        preConfirm:()=>{
            const name=document.getElementById('swal_model_name').value.trim();
            if(!name){
                Swal.showValidationMessage('Model name is required');
                return false;
            }
            return {
                id:document.getElementById('swal_vehicle_model_id').value,
                make_id:document.getElementById('swal_make_id').value,
                model_name:name,
                model_code:document.getElementById('swal_model_code').value,
                model_year:document.getElementById('swal_model_year').value,
                engine_type:document.getElementById('swal_engine_type').value,
                engine_capacity:document.getElementById('swal_engine_capacity').value,
                fuel_type:document.getElementById('swal_fuel_type').value,
                transmission:document.getElementById('swal_transmission').value,
                notes:document.getElementById('swal_notes').value,
                active:document.getElementById('swal_active').checked
            };
        }
    }).then(result=>{
        if(result.isConfirmed&&result.value){
            saveVehicleModel(result.value);
        }
    });
}

function saveVehicleModel(formData){
    Swal.fire({
        title:'Processing...',
        text:'Please wait',
        allowOutsideClick:false,
        showConfirmButton:false,
        didOpen:()=>{Swal.showLoading();}
    });

    const data=new FormData();
    data.append('_csrf','<?= Yii::$app->request->getCsrfToken() ?>');
    data.append('id',formData.id);
    data.append('make_id',formData.make_id);
    data.append('model_name',formData.model_name);
    data.append('model_code',formData.model_code);
    data.append('model_year',formData.model_year);
    data.append('engine_type',formData.engine_type);
    data.append('engine_capacity',formData.engine_capacity);
    data.append('fuel_type',formData.fuel_type);
    data.append('transmission',formData.transmission);
    data.append('notes',formData.notes);

    if(formData.active){
        data.append('is_active','1');
    }

    fetch('index.php?r=products/vehiclemodels',{
        method:'POST',
        body:data
    })
    .then(response=>response.json())
    .then(data=>{
        if(data.success){
            Swal.fire({
                icon:'success',
                title:'Success!',
                text:data.message,
                timer:1500,
                showConfirmButton:false
            }).then(()=>{
                $('.ajax-module.active').trigger('click');
            });
        }else{
            Swal.fire('Error',data.message,'error');
        }
    })
    .catch(()=>{
        Swal.fire('Error','An error occurred. Please try again.','error');
    });
}

function deleteVehicleModel(id){
    Swal.fire({
        title:'Are you sure?',
        text:'Vehicle model will be deleted.',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#d33',
        cancelButtonColor:'#3085d6',
        confirmButtonText:'Yes, delete it!'
    }).then(result=>{
        if(result.isConfirmed){
            const data=new FormData();
            data.append('_csrf','<?= Yii::$app->request->getCsrfToken() ?>');
            data.append('id',id);
            data.append('delete','1');

            fetch('index.php?r=products/vehiclemodels',{
                method:'POST',
                body:data
            })
            .then(response=>response.json())
            .then(data=>{
                if(data.success){
                    Swal.fire({
                        icon:'success',
                        title:'Success!',
                        text:data.message,
                        timer:1500,
                        showConfirmButton:false
                    }).then(()=>{
                        $('.ajax-module.active').trigger('click');
                    });
                }else{
                    Swal.fire('Error',data.message,'error');
                }
            });
        }
    });
}
</script>
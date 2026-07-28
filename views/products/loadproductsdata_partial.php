<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Smart Product Data Insertion';
?>

<style>
    .import-section {
        background: #fff3cd;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #ffc107;
        margin-bottom: 20px;
    }

    .import-section h5 {
        margin-top: 0;
        color: #333;
        font-weight: 600;
    }

    .import-section p {
        margin-bottom: 15px;
        font-size: 13px;
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
        margin-bottom: 20px;
    }

    .nav-tabs .nav-link {
        color: #333;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 10px 20px;
        font-size: 14px;
    }

    .nav-tabs .nav-link.active {
        color: #0f4c29;
        background: transparent;
        border-bottom: 3px solid #0f4c29;
    }

    .nav-tabs .nav-link:hover {
        border-bottom: 3px solid #d4d4d4;
    }

    /* SweetAlert Custom Styles */
    .swal-modal-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
        margin-top: 20px;
        max-height: 400px;
        overflow-y: auto;
    }

    .swal-item-checkbox {
        display: flex;
        align-items: center;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: #f9f9f9;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .swal-item-checkbox:hover {
        background: #f0f0f0;
        border-color: #999;
    }

    .swal-item-checkbox input[type="checkbox"] {
        margin-right: 8px;
        cursor: pointer;
    }

    .swal-item-checkbox.selected {
        background: #d4edda;
        border-color: #28a745;
    }

    .swal-item-checkbox label {
        margin: 0;
        cursor: pointer;
        flex: 1;
        font-size: 13px;
    }

    .swal-progress-container {
        margin-top: 20px;
        display: none;
    }

    .swal-progress-container.show {
        display: block;
    }

    .progress {
        height: 30px;
        border-radius: 4px;
        background: #e9ecef;
        margin-bottom: 10px;
    }

    .progress-bar {
        background: linear-gradient(90deg, #0f4c29, #16a34a);
        text-align: center;
        color: white;
        font-weight: bold;
        line-height: 30px;
        transition: width 0.3s ease;
    }

    .progress-text {
        font-size: 12px;
        color: #666;
    }

    .swal-results {
        margin-top: 15px;
        max-height: 200px;
        overflow-y: auto;
        font-size: 12px;
    }

    .result-item {
        padding: 5px 0;
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
                            <i class="fa fa-cloud-download"></i> Smart Product Data Insertion
                        </h3>
                        <small class="text-muted">Import data from internet - One click to fetch, select, and save</small>
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
                                <p class="text-muted">Fetch popular automotive categories and select which ones to add to your database</p>
                                <button class="btn btn-success btn-sm" onclick="startImport('categories')">
                                    <i class="fa fa-cloud-download"></i> Fetch Categories
                                </button>
                            </div>
                        </div>

                        <!-- BRANDS TAB -->
                        <div role="tabpanel" class="tab-pane" id="brands-tab">
                            <div class="import-section">
                                <h5><i class="fa fa-download"></i> Import Brands from Internet</h5>
                                <p class="text-muted">Fetch popular automotive part brands and select which ones to add to your database</p>
                                <button class="btn btn-success btn-sm" onclick="startImport('brands')">
                                    <i class="fa fa-cloud-download"></i> Fetch Brands
                                </button>
                            </div>
                        </div>

                        <!-- UNITS TAB -->
                        <div role="tabpanel" class="tab-pane" id="units-tab">
                            <div class="import-section">
                                <h5><i class="fa fa-download"></i> Import Units from Internet</h5>
                                <p class="text-muted">Fetch standard measurement units and select which ones to add to your database</p>
                                <button class="btn btn-success btn-sm" onclick="startImport('units')">
                                    <i class="fa fa-cloud-download"></i> Fetch Units
                                </button>
                            </div>
                        </div>

                        <!-- VEHICLE MAKES TAB -->
                        <div role="tabpanel" class="tab-pane" id="makes-tab">
                            <div class="import-section">
                                <h5><i class="fa fa-download"></i> Import Vehicle Makes from Internet</h5>
                                <p class="text-muted">Fetch popular car manufacturers from worldwide and select which ones to add to your database</p>
                                <button class="btn btn-success btn-sm" onclick="startImport('makes')">
                                    <i class="fa fa-cloud-download"></i> Fetch Vehicle Makes
                                </button>
                            </div>
                        </div>

                        <!-- VEHICLE MODELS TAB -->
                        <div role="tabpanel" class="tab-pane" id="models-tab">
                            <div class="import-section">
                                <h5><i class="fa fa-download"></i> Import Vehicle Models from Internet</h5>
                                <p class="text-muted">Fetch specific car models for selected makes and add them to your database</p>
                                <div style="margin-top: 15px;">
                                    <label style="font-size: 13px; margin-bottom: 8px; display: block;">Select Make:</label>
                                    <select class="form-control input-sm" id="model_make_select" style="max-width: 300px;">
                                        <option value="">-- Select a Make --</option>
                                        <?php
                                        $makes = Yii::$app->db->createCommand(
                                            "SELECT id, make_name FROM inventory_vehicle_makes WHERE is_deleted = 0 ORDER BY make_name"
                                        )->queryAll();
                                        foreach ($makes as $make):
                                        ?>
                                            <option value="<?= $make['id'] ?>"><?= Html::encode($make['make_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-success btn-sm" onclick="startImportModels()" style="margin-top: 10px;">
                                        <i class="fa fa-cloud-download"></i> Fetch Models
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentImportData = [];
let currentImportType = '';
let currentMakeId = '';

function startImport(type) {
    currentImportType = type;
    fetchExternalData(type);
}

function startImportModels() {
    const makeId = document.getElementById('model_make_select').value;
    if (!makeId) {
        Swal.fire({
            icon: 'warning',
            title: 'Please Select',
            text: 'Please select a vehicle make first',
            confirmButtonColor: '#0f4c29'
        });
        return;
    }
    currentMakeId = makeId;
    currentImportType = 'models';
    fetchExternalData('models');
}

function fetchExternalData(type) {
    Swal.fire({
        title: 'Fetching Data...',
        html: '<p>Retrieving ' + type + ' from internet...</p>',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const postData = { type: type };
    if (type === 'models' && currentMakeId) {
        postData.make_id = currentMakeId;
    }

    $.ajax({
        url: '<?= Url::to(['products/fetchexternaldata']) ?>',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                currentImportData = response.data;
                showImportModal(type, response.data);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonColor: '#0f4c29'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to fetch data from internet',
                confirmButtonColor: '#0f4c29'
            });
        }
    });
}

function showImportModal(type, data) {
    const titles = {
        'makes': 'Vehicle Makes',
        'brands': 'Automotive Brands',
        'categories': 'Auto Parts Categories',
        'units': 'Measurement Units',
        'models': 'Vehicle Models'
    };

    let htmlContent = '<div style="text-align: left;">';
    htmlContent += '<p style="margin-bottom: 15px; font-size: 13px; color: #666;">Select which items you want to add (click to toggle):</p>';
    htmlContent += '<div class="swal-modal-grid">';

    data.forEach((item, index) => {
        const name = item.make_name || item.brand_name || item.category_name || item.unit_name || item.model_name || 'Unknown';
        htmlContent += '<label class="swal-item-checkbox">';
        htmlContent += '<input type="checkbox" class="import-checkbox" data-index="' + index + '" value="' + index + '">';
        htmlContent += '<label style="cursor: pointer;">' + name + '</label>';
        htmlContent += '</label>';
    });

    htmlContent += '</div></div>';

    Swal.fire({
        title: 'Import ' + titles[type],
        html: htmlContent,
        width: '600px',
        icon: undefined,
        showCancelButton: true,
        confirmButtonText: 'Inject Selected',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        didOpen: () => {
            // Add event listeners to checkboxes
            document.querySelectorAll('.import-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateItemSelection();
                });
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            injectBulkData();
        }
    });
}

function updateItemSelection() {
    document.querySelectorAll('.swal-item-checkbox').forEach(item => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (checkbox.checked) {
            item.classList.add('selected');
        } else {
            item.classList.remove('selected');
        }
    });
}

function injectBulkData() {
    const selected = [];
    document.querySelectorAll('.import-checkbox:checked').forEach(checkbox => {
        selected.push(parseInt(checkbox.value));
    });

    if (selected.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Selection',
            text: 'Please select at least one item to inject',
            confirmButtonColor: '#0f4c29'
        });
        return;
    }

    const postData = {
        type: currentImportType,
        data: JSON.stringify(currentImportData),
        selected: JSON.stringify(selected)
    };

    if (currentImportType === 'models' && currentMakeId) {
        postData.make_id = currentMakeId;
    }

    Swal.fire({
        title: 'Injecting Data...',
        html: '<div class="swal-progress-container show">' +
              '<div class="progress">' +
              '<div class="progress-bar" id="swalProgressBar" style="width: 0%;">0%</div>' +
              '</div>' +
              '<div class="progress-text">Processing: <span id="swalProgressText">0/0</span></div>' +
              '</div>' +
              '<div class="swal-results" id="swalResults"></div>',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '<?= Url::to(['products/injectbulkdata']) ?>',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const progress = response.progress || 100;
                document.getElementById('swalProgressBar').style.width = progress + '%';
                document.getElementById('swalProgressBar').textContent = progress + '%';
                document.getElementById('swalProgressText').textContent = response.inserted + '/' + selected.length;

                let resultHtml = '';
                if (response.messages && response.messages.length > 0) {
                    response.messages.forEach(msg => {
                        let cls = 'success';
                        if (msg.includes('Skipped')) cls = 'warning';
                        if (msg.includes('Error')) cls = 'error';
                        resultHtml += '<div class="result-item ' + cls + '">' + msg + '</div>';
                    });
                }
                document.getElementById('swalResults').innerHTML = resultHtml;

                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        html: '<p><strong>' + response.inserted + ' records</strong> injected successfully!</p>' +
                              '<p style="font-size: 12px; color: #666; margin-top: 10px;">Page will refresh automatically...</p>',
                        confirmButtonColor: '#0f4c29',
                        didOpen: () => {
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        }
                    });
                }, 1000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonColor: '#0f4c29'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error during injection: ' + error,
                confirmButtonColor: '#0f4c29'
            });
        }
    });
}
</script>

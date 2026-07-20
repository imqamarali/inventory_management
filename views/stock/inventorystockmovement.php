<?php

use yii\helpers\Html;

if (!isset($warehouses)) $warehouses = [];
if (!isset($products)) $products = [];
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=stock/dashboard">Home</a>
                </li>
                <li class="active">Stock Movement</li>
                <li style="float:right;">
                    <div class="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;"
                                onclick="openMovementModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Stock Movement
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <input type="text" id="keyword" class="new-input" style="width:18%" placeholder="Product / Movement No">

            <select id="warehouse_id" class="new-input" style="width:16%">
                <option value="">All Warehouses</option>
                <?php foreach ($warehouses as $w) { ?>
                    <option value="<?= $w['id'] ?>"><?= Html::encode($w['warehouse_name']) ?></option>
                <?php } ?>
            </select>

            <select id="product_id" class="chzn-select" style="width:16%">
                <option value="">All Products</option>
                <?php foreach ($products as $p) { ?>
                    <option value="<?= $p['id'] ?>"><?= Html::encode($p['product_name']) ?></option>
                <?php } ?>
            </select>

            <select id="movement_type" class="new-input" style="width:12%">
                <option value="">IN / OUT</option>
                <option value="IN">IN</option>
                <option value="OUT">OUT</option>
            </select>

            <input type="number" id="per_page" class="new-input" style="width:10%" value="10" placeholder="Records">

            <input type="button" class="btn btn-primary" onclick="searchMovement()" value="Search"
                style="height:30px;padding:0;margin-top:-3px;">
        </div>

        <div class="widget-main">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="movement_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Movement No</th>
                            <th>Date</th>
                            <th>Warehouse</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Unit Cost</th>
                            <th>Total Cost</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="11" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
                <div id="paginationArea" class="text-center"></div>
            </div>
        </div>

    </div>
</div>

<script>
    if (typeof warehouses === 'undefined' || !warehouses) {
        var warehouses = <?= json_encode($warehouses) ?>;
    }

    if (typeof products === 'undefined' || !products) {
        var products = <?= json_encode($products) ?>;
    }
</script>

<script>
      
    setTimeout(function() {
        $('.chzn-select').chosen({
            search_contains: true,
            no_results_text: "No record found"
        });
    }, 500);

    searchMovement();

    function searchMovement(page = 1) {

        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'search');
        data.append('page', page);
        data.append('per_page', $('#per_page').val());
        data.append('keyword', $('#keyword').val());
        data.append('warehouse_id', $('#warehouse_id').val());
        data.append('product_id', $('#product_id').val());
        data.append('movement_type', $('#movement_type').val());

        fetch('index.php?r=stock/inventorystockmovement', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderMovements(res.data);
                    renderPagination(res.page, res.total, res.limit);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.close();
                Swal.fire('Error', 'Unable to load stock movements.', 'error');
            });

    }

    function renderMovements(rows) {
        let html = '';
        if (rows.length == 0) {
            html = '<tr><td colspan="11" class="text-center">No Movements Found</td></tr>';
        } else {
            rows.forEach(function(item, i) {
                let typeBadge = item.movement_type == 'IN'
                    ? '<span class="label label-success">IN</span>'
                    : '<span class="label label-danger">OUT</span>';
                html += `
                    <tr>
                    <td>${i+1}</td>
                    <td>${item.movement_no}</td>
                    <td>${item.movement_date??''}</td>
                    <td>${item.warehouse_name}</td>
                    <td>${item.product_name}<br><small>${item.sku??''}</small></td>
                    <td>${typeBadge}</td>
                    <td>${parseFloat(item.quantity).toFixed(2)}</td>
                    <td>${parseFloat(item.unit_cost??0).toFixed(2)}</td>
                    <td>${parseFloat(item.total_cost??0).toFixed(2)}</td>
                    <td>${item.remarks??''}</td>
                    <td>
                    <button onclick='editMovementRemarks(${JSON.stringify(item)})'>
                    <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button onclick="deleteMovement(${item.id})">
                    <i class="fa fa-trash"></i>
                    </button>
                    </td>
                    </tr>`;
            });
        }
        $('#movement_table tbody').html(html);
    }

    function renderPagination(page, total, limit) {
        let pages = Math.ceil(total / limit);
        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `
                <button class=" ${i==page?'btn-primary':'btn-default'}"
                onclick="searchMovement(${i})">
                ${i}
                </button>`;
        }
        $('#paginationArea').html(html);
    }

   
    function openMovementModal() {

        let warehouseOptions = '';
        <?php foreach ($warehouses as $w) { ?>
            warehouseOptions += '<option value="<?= $w['id'] ?>"><?= Html::encode($w['warehouse_name']) ?></option>';
        <?php } ?>

        let productOptions = '<option value="">Select Product</option>';
        products.forEach(function (p) {
            productOptions += `
                <option value="${p.id}">
                    ${p.product_name} (${p.sku ?? ''})
                </option>
            `;
        });

        Swal.fire({
            title: 'New Stock Movement',
            width: '750px',
            html: `
                <form id="movementForm">

                    <div class="row">

                        <div class="col-md-6">
                            <label class="mb-1">Warehouse</label>
                            <select id="swal_warehouse" class="form-control">
                                ${warehouseOptions}
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="mb-1">Product</label>
                            <select id="swal_product" class="form-control">
                                ${productOptions}
                            </select>
                        </div>

                    </div>

                    <div class="row mt-3">

                        <div class="col-md-6">
                            <label class="mb-1">Reference Type</label>
                            <select id="swal_reference_type" class="form-control">
                                <option value="Purchase">Purchase</option>
                                <option value="Sale">Sale</option>
                                <option value="Transfer In">Transfer In</option>
                                <option value="Transfer Out">Transfer Out</option>
                                <option value="Adjustment">Adjustment</option>
                                <option value="Return Purchase">Return Purchase</option>
                                <option value="Return Sale">Return Sale</option>
                                <option value="Opening Stock">Opening Stock</option>
                                <option value="Stock Audit">Stock Audit</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="mb-1">Movement Type</label>
                            <select id="swal_type" class="form-control">
                                <option value="IN">IN</option>
                                <option value="OUT">OUT</option>
                            </select>
                        </div>

                    </div>

                    <div class="row mt-3">

                        <div class="col-md-6">
                            <label class="mb-1">Quantity</label>
                            <input
                                type="number"
                                id="swal_quantity"
                                class="form-control"
                                value="0"
                                min="0"
                                step="0.01">
                        </div>

                        <div class="col-md-6">
                            <label class="mb-1">Unit Cost</label>
                            <input
                                type="number"
                                id="swal_unit_cost"
                                class="form-control"
                                value="0"
                                min="0"
                                step="0.01">
                        </div>

                    </div>

                    <div class="row mt-3">

                        <div class="col-md-6">
                            <label class="mb-1">Movement Date</label>
                            <input
                                type="datetime-local"
                                id="swal_movement_date"
                                class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="mb-1">Remarks</label>
                            <input
                                type="text"
                                id="swal_remarks"
                                class="form-control">
                        </div>

                    </div>

                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Save Movement',

            preConfirm: () => {

                if ($('#swal_warehouse').val() === '' || $('#swal_product').val() === '') {
                    Swal.showValidationMessage('Warehouse and Product are required');
                    return false;
                }

                if (
                    !parseFloat($('#swal_quantity').val()) ||
                    parseFloat($('#swal_quantity').val()) <= 0
                ) {
                    Swal.showValidationMessage('Quantity must be greater than zero');
                    return false;
                }

                return {
                    warehouse_id: $('#swal_warehouse').val(),
                    product_id: $('#swal_product').val(),
                    reference_type: $('#swal_reference_type').val(),
                    movement_type: $('#swal_type').val(),
                    quantity: $('#swal_quantity').val(),
                    unit_cost: $('#swal_unit_cost').val(),
                    movement_date: $('#swal_movement_date').val(),
                    remarks: $('#swal_remarks').val()
                };
            }

        }).then(function (result) {
            if (result.isConfirmed) {
                saveMovement(result.value);
            }
        });

    }


    function saveMovement(formData) {

        Swal.fire({
            title: 'Processing...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'create');
        Object.keys(formData).forEach(function(key) {
            data.append(key, formData[key]);
        });

        fetch('index.php?r=stock/inventorystockmovement', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        searchMovement();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Unable to save movement.', 'error');
            });
    }

    function editMovementRemarks(item) {
        Swal.fire({
            title: 'Update Remarks',
            html: `
                <div class="form-group" style="text-align:left;">
                <label>Movement No: ${item.movement_no}</label>
                <textarea id="swal_remarks_edit" class="form-control" rows="3">${item.remarks??''}</textarea>
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'Save',
            preConfirm: () => {
                return {
                    id: item.id,
                    remarks: document.getElementById('swal_remarks_edit').value
                };
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                updateMovementRemarks(result.value);
            }
        });
    }

    function updateMovementRemarks(formData) {

        Swal.fire({
            title: 'Processing...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'update');
        data.append('id', formData.id);
        data.append('remarks', formData.remarks);

        fetch('index.php?r=stock/inventorystockmovement', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        searchMovement();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Unable to update remarks.', 'error');
            });
    }

    function deleteMovement(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This movement will be removed and its stock effect reversed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete'
        }).then(function(result) {
            if (!result.isConfirmed) return;

            const data = new FormData();
            data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
            data.append('flag', 'delete');
            data.append('id', id);

            fetch('index.php?r=stock/inventorystockmovement', {
                    method: 'POST',
                    body: data
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            searchMovement();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Unable to delete movement.', 'error');
                });
        });
    }
</script>

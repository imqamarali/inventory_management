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
                <li class="active">Opening Stock</li>
                <!-- <li style="float:right;">
                    <div class="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;"
                                onclick="openOpeningStockModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Opening Stock
                            </a>
                        </div>
                    </div>
                </li> -->
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <input type="text" id="keyword" class="new-input" style="width:20%" placeholder="Product / SKU">

            <select id="warehouse_id" class="new-input" style="width:20%">
                <option value="">All Warehouses</option>
                <?php foreach ($warehouses as $w) { ?>
                    <option value="<?= $w['id'] ?>"><?= Html::encode($w['warehouse_name']) ?></option>
                <?php } ?>
            </select>

            <input type="number" id="per_page" class="new-input" style="width:10%" value="10" placeholder="Records">

            <input type="button" class="btn btn-primary" onclick="searchOpeningStock()" value="Search"
                style="height:30px;padding:0;margin-top:-3px;">
        </div>

        <div class="widget-main">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="opening_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Warehouse</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Cost</th>
                            <th>Total Cost</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="9" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
                <div id="paginationArea" class="text-center"></div>
            </div>
        </div>

    </div>
</div>
<script>
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

    searchOpeningStock();

    function searchOpeningStock(page = 1) {

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

        fetch('index.php?r=stock/inventoryopeningstock', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderOpeningStock(res.data);
                    renderPagination(res.page, res.total, res.limit);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.close();
                Swal.fire('Error', 'Unable to load opening stock.', 'error');
            });

    }

    function renderOpeningStock(rows) {
        let html = '';
        if (rows.length == 0) {
            html = '<tr><td colspan="9" class="text-center">No Opening Stock Records Found</td></tr>';
        } else {
            rows.forEach(function(item, i) {
                html += `
                    <tr>
                    <td>${i+1}</td>
                    <td>${item.movement_date??''}</td>
                    <td>${item.warehouse_name}</td>
                    <td>${item.product_name}<br><small>${item.sku??''}</small></td>
                    <td>${parseFloat(item.quantity).toFixed(2)}</td>
                    <td>${parseFloat(item.unit_cost??0).toFixed(2)}</td>
                    <td>${parseFloat(item.total_cost??0).toFixed(2)}</td>
                    <td>${item.remarks??''}</td>
                    <td>
                    <button onclick='editOpeningStock(${JSON.stringify(item)})'>
                    <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button onclick="deleteOpeningStock(${item.id})">
                    <i class="fa fa-trash"></i>
                    </button>
                    </td>
                    </tr>`;
            });
        }
        $('#opening_table tbody').html(html);
    }

    function renderPagination(page, total, limit) {
        let pages = Math.ceil(total / limit);
        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `
                <button class=" ${i==page?'btn-primary':'btn-default'}"
                onclick="searchOpeningStock(${i})">
                ${i}
                </button>`;
        }
        $('#paginationArea').html(html);
    }

    function openOpeningStockModal(row = null) {

        let edit = row != null;
        let id = edit ? row.id : '';
        let warehouseId = edit ? row.warehouse_id : '';
        let productId = edit ? row.product_id : '';
        let quantity = edit ? row.quantity : 0;
        let unitCost = edit ? row.unit_cost : 0;
        let remarks = edit ? (row.remarks ?? '') : '';

        let warehouseOptions = '<option value="">Select Warehouse</option>';
        <?php foreach ($warehouses as $w) { ?>
            warehouseOptions += `<option value="<?= $w['id'] ?>" ${warehouseId=='<?= $w['id'] ?>'?'selected':''}><?= Html::encode($w['warehouse_name']) ?></option>`;
        <?php } ?>

        let productOptions = '<option value="">Select Product</option>';
        products.forEach(function(p) {
            productOptions += `<option value="${p.id}" ${p.id==productId?'selected':''}>${p.product_name} (${p.sku??''})</option>`;
        });

        Swal.fire({
            title: edit ? 'Update Opening Stock' : 'Add Opening Stock',
            width: '700px',
            html: `
                <form id="openingForm">
                <input type="hidden" id="swal_id" value="${id}">
                <div class="row">
                <div class="col-md-6">
                <label>Warehouse</label>
                <select id="swal_warehouse" class="form-control" ${edit ? 'disabled' : ''}>${warehouseOptions}</select>
                </div>
                <div class="col-md-6">
                <label>Product</label>
                <select id="swal_product" class="form-control" ${edit ? 'disabled' : ''}>${productOptions}</select>
                </div>
                </div>
                <div class="row" style="margin-top:10px;">
                <div class="col-md-6">
                <label>Quantity</label>
                <input type="number" id="swal_quantity" class="form-control" value="${quantity}">
                </div>
                <div class="col-md-6">
                <label>Unit Cost</label>
                <input type="number" id="swal_unit_cost" class="form-control" value="${unitCost}">
                </div>
                </div>
                <div class="row" style="margin-top:10px;">
                <div class="col-md-12">
                <label>Remarks</label>
                <input id="swal_remarks" class="form-control" value="${remarks}">
                </div>
                </div>
                </form>
                `,
            showCancelButton: true,
            confirmButtonText: edit ? 'Update' : 'Save',
            preConfirm: () => {
                if (!edit && ($('#swal_warehouse').val() == '' || $('#swal_product').val() == '')) {
                    Swal.showValidationMessage('Warehouse and Product are required');
                    return false;
                }
                return {
                    id: id,
                    warehouse_id: warehouseId || $('#swal_warehouse').val(),
                    product_id: productId || $('#swal_product').val(),
                    quantity: $('#swal_quantity').val(),
                    unit_cost: $('#swal_unit_cost').val(),
                    remarks: $('#swal_remarks').val()
                };
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                saveOpeningStock(result.value, edit);
            }
        });
    }

    function editOpeningStock(row) {
        openOpeningStockModal(row);
    }

    function saveOpeningStock(formData, edit) {

        Swal.fire({
            title: 'Processing...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', edit ? 'update' : 'create');
        Object.keys(formData).forEach(function(key) {
            data.append(key, formData[key]);
        });

        fetch('index.php?r=stock/inventoryopeningstock', {
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
                        searchOpeningStock();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Unable to save opening stock.', 'error');
            });
    }

    function deleteOpeningStock(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This opening stock entry will be removed and its stock effect reversed.',
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

            fetch('index.php?r=stock/inventoryopeningstock', {
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
                            searchOpeningStock();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Unable to delete record.', 'error');
                });
        });
    }
</script>

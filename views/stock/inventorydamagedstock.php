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
                <li class="active">Damaged Stock</li>
                <li style="float:right;">
                    <div class="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;"
                                onclick="openDamagedModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Report Damaged Stock
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <input type="text" id="keyword" class="new-input" style="width:20%" placeholder="Adjustment No">

            <select id="warehouse_id" class="new-input" style="width:20%">
                <option value="">All Warehouses</option>
                <?php foreach ($warehouses as $w) { ?>
                    <option value="<?= $w['id'] ?>"><?= Html::encode($w['warehouse_name']) ?></option>
                <?php } ?>
            </select>

            <input type="number" id="per_page" class="new-input" style="width:10%" value="10" placeholder="Records">

            <input type="button" class="btn btn-primary" onclick="searchDamaged()" value="Search"
                style="height:30px;padding:0;margin-top:-3px;">
        </div>

        <div class="widget-main">
            <div class="alert alert-info">
                <i class="ace-icon fa fa-info-circle"></i>
                Damaged stock is recorded as a stock decrease with reason "Damage", so it stays fully traceable in the
                Stock Ledger and Stock Adjustment history.
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="damaged_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Adjustment No</th>
                            <th>Warehouse</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total Cost</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">Loading...</td>
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

    searchDamaged();

    function searchDamaged(page = 1) {

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

        fetch('index.php?r=stock/inventorydamagedstock', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderDamaged(res.data);
                    renderPagination(res.page, res.total, res.limit);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.close();
                Swal.fire('Error', 'Unable to load damaged stock.', 'error');
            });

    }

    function renderDamaged(rows) {
        let html = '';
        if (rows.length == 0) {
            html = '<tr><td colspan="8" class="text-center">No Damaged Stock Records Found</td></tr>';
        } else {
            rows.forEach(function(item, i) {
                html += `
                    <tr>
                    <td>${i+1}</td>
                    <td>${item.adjustment_no}</td>
                    <td>${item.warehouse_name}</td>
                    <td>${item.adjustment_date??''}</td>
                    <td>${parseFloat(item.total_quantity??0).toFixed(2)}</td>
                    <td>${parseFloat(item.total_cost??0).toFixed(2)}</td>
                    <td>${item.remarks??''}</td>
                    <td>
                    <button onclick="editDamaged(${item.id})">
                    <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button onclick="deleteDamaged(${item.id})">
                    <i class="fa fa-trash"></i>
                    </button>
                    </td>
                    </tr>`;
            });
        }
        $('#damaged_table tbody').html(html);
    }

    function renderPagination(page, total, limit) {
        let pages = Math.ceil(total / limit);
        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `
                <button class="${i==page?'btn-primary':'btn-default'}"
                onclick="searchDamaged(${i})">
                ${i}
                </button>`;
        }
        $('#paginationArea').html(html);
    }

    function editDamaged(id) {

        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'search');
        data.append('id', id);

        fetch('index.php?r=stock/inventorydamagedstock', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    const row = res.data.find(r => r.id == id) || {
                        id: id
                    };
                    row.items = res.items;
                    openDamagedModal(row);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
    }

    function openDamagedModal(row = null) {

        let edit = row != null;
        let id = edit ? row.id : '';
        let warehouseId = edit ? row.warehouse_id : '';
        let remarks = edit ? (row.remarks ?? '') : '';

        let warehouseOptions = '<option value="">Select Warehouse</option>';
        warehouses.forEach(function(w) {
            warehouseOptions += `<option value="${w.id}" ${w.id==warehouseId?'selected':''}>${w.warehouse_name}</option>`;
        });

        Swal.fire({
            title: edit ? 'Update Damaged Stock' : 'Report Damaged Stock',
            width: '800px',
            html: `
                <form id="damagedForm">
                <input type="hidden" id="swal_id" value="${id}">
                <div class="row">
                <div class="col-md-6">
                <label>Warehouse</label>
                <select id="swal_warehouse" class="form-control">${warehouseOptions}</select>
                </div>
                <div class="col-md-6">
                <label>Remarks</label>
                <input id="swal_remarks" class="form-control" value="${remarks}">
                </div>
                </div>

                <hr>

                <div id="itemsArea"></div>

                <div class="text-right" style="margin-top:15px;">
                    <button type="button" onclick="addDamagedItem()">
                        <i class="fa fa-plus"></i>
                        Add Item
                    </button>
                </div>
                </form>
                `,
            showCancelButton: true,
            confirmButtonText: 'Save',
            didOpen: () => {
                if (edit && row.items) {
                    row.items.forEach(item => addDamagedItem(item));
                } else {
                    addDamagedItem();
                }
            },
            preConfirm: () => {
                if ($('#swal_warehouse').val() == '') {
                    Swal.showValidationMessage('Warehouse is required');
                    return false;
                }
                let items = [];
                $('.damaged-item').each(function() {
                    items.push({
                        product_id: $(this).find('.product_id').val(),
                        quantity: $(this).find('.quantity').val(),
                        unit_cost: $(this).find('.unit_cost').val()
                    });
                });
                return {
                    id: id,
                    warehouse_id: $('#swal_warehouse').val(),
                    remarks: $('#swal_remarks').val(),
                    items: items
                };
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                saveDamaged(result.value, edit);
            }
        });
    }

    function addDamagedItem(item = {}) {

        let options = '<option value="">Select Product</option>';
        products.forEach(function(p) {
            options += `<option value="${p.id}" ${p.id==item.product_id?'selected':''}>${p.product_name} (${p.sku??''})</option>`;
        });

        let html = `
            <div class="row damaged-item" style="margin-bottom:5px;">
            <div class="col-md-6">
            <select class="form-control product_id">${options}</select>
            </div>
            <div class="col-md-3">
            <input type="number" class="form-control quantity" placeholder="Quantity" value="${item.quantity??''}">
            </div>
            <div class="col-md-2">
            <input type="number" class="form-control unit_cost" placeholder="Cost" value="${item.unit_cost??''}">
            </div>
            <div class="col-md-1">
            <button type="button" class="btn-danger" onclick="$(this).closest('.damaged-item').remove()">
            <i class="fa fa-trash"></i>
            </button>
            </div>
            </div>`;

        $('#itemsArea').append(html);
    }

    function saveDamaged(formData, edit) {

        Swal.fire({
            title: 'Processing...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', edit ? 'update' : 'create');
        data.append('id', formData.id);
        data.append('warehouse_id', formData.warehouse_id);
        data.append('remarks', formData.remarks);
        data.append('items', JSON.stringify(formData.items));

        fetch('index.php?r=stock/inventorydamagedstock', {
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
                        searchDamaged();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Unable to save damaged stock.', 'error');
            });
    }

    function deleteDamaged(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This damaged stock record will be deleted and its stock effect reversed.',
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

            fetch('index.php?r=stock/inventorydamagedstock', {
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
                            searchDamaged();
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

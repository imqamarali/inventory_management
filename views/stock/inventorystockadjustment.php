<?php

use yii\helpers\Html;

if (!isset($warehouses)) {
    $warehouses = [];
}
if (!isset($products)) {
    $products = [];
}
?>
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=stock/dashboard">Home</a>
                </li>
                <li class="active">Stock Adjustment</li>
                <li style="float:right;">
                    <div class="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary"
                                style="font-size:12px;cursor:pointer;"
                                onclick="openAdjustmentModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Stock Adjustment
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div style="padding-top:10px;padding-left:13px;">
            <div class="float-left">
                <input type="text"
                    id="keyword"
                    class="new-input"
                    style="width:20%"
                    placeholder="Adjustment No">

                <select id="warehouse_id" class="new-input" style="width:20%">
                    <option value="">All Warehouses</option>
                    <?php foreach ($warehouses as $w) { ?>
                        <option value="<?= $w['id'] ?>">
                            <?= Html::encode($w['warehouse_name']) ?>
                        </option>
                    <?php } ?>
                </select>

                <select id="adjustment_type" class="new-input" style="width:15%">
                    <option value="">All Types</option>
                    <option value="Increase">Increase</option>
                    <option value="Decrease">Decrease</option>
                </select>

                <input type="number"
                    id="per_page"
                    class="new-input"
                    style="width:10%"
                    value="10"
                    placeholder="Records">

                <input type="button"
                    class="btn btn-primary"
                    onclick="searchAdjustment()"
                    value="Search"
                    style="height:30px;padding:0px;margin-top:-3px;">
            </div>
        </div>
        <div class="widget-main">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="adjustment_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Adjustment No</th>
                            <th>Warehouse</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Items</th>
                            <th>Total Cost</th>
                            <th>Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($adjustments)) { ?>
                            <tr>
                                <td colspan="9" class="text-center">
                                    No Stock Adjustment Found
                                </td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($adjustments as $key => $row) { ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><?= Html::encode($row['adjustment_no']) ?></td>
                                    <td><?= Html::encode($row['warehouse_name']) ?></td>
                                    <td><?= Html::encode($row['adjustment_date']) ?></td>
                                    <td>
                                        <?php if ($row['adjustment_type'] == 'Increase') { ?>
                                            <span class="label label-success">Increase</span>
                                        <?php } else { ?>
                                            <span class="label label-danger">Decrease</span>
                                        <?php } ?>
                                    </td>
                                    <td><?= $row['item_count'] ?></td>
                                    <td><?= $row['total_cost'] ?></td>
                                    <td><?= Html::encode($row['reason']) ?></td>
                                    <td>
                                        <button onclick="editAdjustment(<?= json_encode($row) ?>)">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        |
                                        <button onclick="deleteAdjustment(<?= $row['id'] ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
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
     
    searchAdjustment(); 
    setTimeout(function() {
        $('.chzn-select').chosen({
            search_contains: true,
            no_results_text: "No record found"
        });
    }, 500);
    function searchAdjustment(page = 1) {
        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        let data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'search');
        data.append('page', page);
        data.append('per_page', $('#per_page').val());
        data.append('keyword', $('#keyword').val());
        data.append('warehouse_id', $('#warehouse_id').val());
        data.append('adjustment_type', $('#adjustment_type').val());

        fetch('index.php?r=stock/inventorystockadjustment', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderAdjustments(res.data);
                    renderPagination(res.page, res.total, res.limit);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
    }

    function renderAdjustments(data) {
        let html = '';
        if (data.length == 0) {
            html = '<tr><td colspan="9" class="text-center">No Record Found</td></tr>';
        } else {
            data.forEach((row, i) => {
                html += `
                    <tr>
                    <td>${i+1}</td>
                    <td>${row.adjustment_no}</td>
                    <td>${row.warehouse_name}</td>
                    <td>${row.adjustment_date}</td>
                    <td>${row.adjustment_type}</td>
                    <td>${row.item_count}</td>
                    <td>${row.total_cost}</td>
                    <td>${row.reason??''}</td>
                    <td>
                    <button onclick='editAdjustment(${JSON.stringify(row)})'>
                    <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button onclick="deleteAdjustment(${row.id})">
                    <i class="fa fa-trash"></i>
                    </button>
                    </td>
                    </tr>`;
            });
        }
        $('#adjustment_table tbody').html(html);
    }

    function renderPagination(page, total, limit) {
        let pages = Math.ceil(total / limit);
        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `
                <button class=" ${i==page?'btn-primary':'btn-default'}"
                onclick="searchAdjustment(${i})">
                ${i}
                </button>`;
        }
        $('#paginationArea').html(html);
    }

    function editAdjustment(row){

        Swal.fire({
            title:'Loading...',
            allowOutsideClick:false,
            showConfirmButton:false,
            didOpen:()=>{
                Swal.showLoading();
            }
        });

        let data=new FormData();

        data.append('_csrf','<?=Yii::$app->request->getCsrfToken()?>');
        data.append('flag','search');
        data.append('id',row.id);

        fetch('index.php?r=stock/inventorystockadjustment',{
            method:'POST',
            body:data
        })
        .then(r=>r.json())
        .then(res=>{

            Swal.close();

            if(res.success){

                row.items=res.items;

                openAdjustmentModal(row);

            }else{

                Swal.fire(
                    'Error',
                    res.message,
                    'error'
                );

            }

        });

    }

    function openAdjustmentModal(row = null) {
        console.log(row);
        let edit = row != null;
        let id = edit ? row.id : '';
        let warehouse = edit ? row.warehouse_id : '';
        let type = edit ? row.adjustment_type : '';
        let reason = edit ? row.reason : '';
        let remarks = edit ? row.remarks : '';

        let warehouseOptions = '';
        warehouses.forEach(w => {
            warehouseOptions += `
                <option value="${w.id}" ${w.id==warehouse?'selected':''}>
                ${w.warehouse_name}
                </option>`;
        });

        Swal.fire({
            title: edit ? 'Update Adjustment' : 'New Stock Adjustment',
            html: `
                <form id="adjustmentForm">

                <input type="hidden" id="adjustment_id" value="${id}">

                <div class="row">

                    <div class="col-md-6">
                        <label>Warehouse</label>
                        <select id="swal_warehouse" class="form-control">
                            ${warehouseOptions}
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Adjustment Type</label>
                        <select id="swal_type" class="form-control">
                            <option value="Increase" ${type=='Increase'?'selected':''}>Increase</option>
                            <option value="Decrease" ${type=='Decrease'?'selected':''}>Decrease</option>
                        </select>
                    </div>

                </div>

                <div class="row" style="margin-top:15px;">

                    <div class="col-md-6">
                        <label>Reason</label>
                        <input
                            id="swal_reason"
                            class="form-control"
                            value="${reason}">
                    </div>

                    <div class="col-md-6">
                        <label>Remarks</label>
                        <input
                            id="swal_remarks"
                            class="form-control"
                            value="${remarks}">
                    </div>

                </div>

                <hr>

                <div id="itemsArea"></div>

                <div class="text-right" style="margin-top:15px;">
                    <button
                        type="button"
                        onclick="addAdjustmentItem()">
                        <i class="fa fa-plus"></i>
                        Add Item
                    </button>
                </div>

                </form>
                `,
            width: '800px',
            showCancelButton: true,
            confirmButtonText: 'Save',
            didOpen: () => {
                if (edit && row.items) {
                    row.items.forEach(item => {
                        addAdjustmentItem(item);
                    });
                }
            },
            preConfirm: () => {
                let items = [];
                $('.adjustment-item').each(function() {
                    items.push({
                        product_id: $(this).find('.product_id').val(),
                        quantity: $(this).find('.quantity').val(),
                        unit_cost: $(this).find('.unit_cost').val()
                    });
                });
                return {
                    id: $('#adjustment_id').val(),
                    warehouse_id: $('#swal_warehouse').val(),
                    adjustment_type: $('#swal_type').val(),
                    reason: $('#swal_reason').val(),
                    remarks: $('#swal_remarks').val(),
                    items: items
                };

            }
        }).then(result => {
            if (result.isConfirmed) {
                saveStockAdjustment(result.value);
            }
        });
    }


    function addAdjustmentItem(item = {}) {

        let options = '<option value="">Select Product</option>';

        products.forEach(p => {
            options += `
                <option value="${p.id}" ${p.id==item.product_id?'selected':''}>
                ${p.product_name} (${p.sku??''})
                </option>`;
        });

        let html = `
            <div class="row adjustment-item" style="margin-bottom:5px;">
            <div class="col-md-6">
            <select class="form-control product_id">
            ${options}
            </select>
            </div>

            <div class="col-md-3">
            <input type="number"
            class="form-control quantity"
            placeholder="Quantity"
            value="${item.quantity??''}">
            </div>

            <div class="col-md-2">
            <input type="number"
            class="form-control unit_cost"
            placeholder="Cost"
            value="${item.unit_cost??''}">
            </div>

            <div class="col-md-1">
            <button type="button"
            class="btn-danger"
            onclick="$(this).closest('.adjustment-item').remove()">
            <i class="fa fa-trash"></i>
            </button>
            </div>
            </div>`;

        $('#itemsArea').append(html);
    }

    function saveStockAdjustment(formData) {
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', formData.id ? 'update' : 'create');
        data.append('id', formData.id);
        data.append('warehouse_id', formData.warehouse_id);
        data.append('adjustment_type', formData.adjustment_type);
        data.append('reason', formData.reason);
        data.append('remarks', formData.remarks);
        data.append('items', JSON.stringify(formData.items));

        fetch('index.php?r=stock/inventorystockadjustment', {
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
                        // $('.ajax-module.active').trigger('click');
                    });

                } else {

                    Swal.fire(
                        'Error',
                        res.message,
                        'error'
                    );

                }

            })
            .catch(() => {

                Swal.fire(
                    'Error',
                    'Unable to save stock adjustment.',
                    'error'
                );

            });

    }

    function deleteAdjustment(id) {

        Swal.fire({
            title: 'Are you sure?',
            text: 'Adjustment will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {

            if (result.isConfirmed) {

                let data = new FormData();

                data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
                data.append('flag', 'delete');
                data.append('id', id);

                fetch('index.php?r=stock/inventorystockadjustment', {
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
                                $('.ajax-module.active').trigger('click');
                            });

                        } else {

                            Swal.fire(
                                'Error',
                                res.message,
                                'error'
                            );

                        }

                    });

            }

        });

    }
</script>
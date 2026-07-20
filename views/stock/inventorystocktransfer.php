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
                <li class="active">Stock Transfer</li>
                <li style="float:right;">
                    <div class="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;"
                                onclick="openTransferModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Stock Transfer
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <input type="text" id="keyword" class="new-input" style="width:16%" placeholder="Transfer No">

            <select id="from_warehouse" class="chzn-select" style="width:16%">
                <option value="">From Warehouse</option>
                <?php foreach ($warehouses as $w) { ?>
                    <option value="<?= $w['id'] ?>"><?= Html::encode($w['warehouse_name']) ?></option>
                <?php } ?>
            </select>

            <select id="to_warehouse" class="chzn-select" style="width:16%">
                <option value="">To Warehouse</option>
                <?php foreach ($warehouses as $w) { ?>
                    <option value="<?= $w['id'] ?>"><?= Html::encode($w['warehouse_name']) ?></option>
                <?php } ?>
            </select>

            <select id="status" class="new-input" style="width:14%">
                <option value="">All Status</option>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>

            <input type="number" id="per_page" class="new-input" style="width:10%" value="10" placeholder="Records">

            <input type="button" class="btn btn-primary" onclick="searchTransfer()" value="Search"
                style="height:30px;padding:0;margin-top:-3px;">
        </div>

        <div class="widget-main">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="transfer_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Transfer No</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Status</th>
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
    if (typeof warehouses === 'undefined' || !warehouses) {
        var warehouses = <?= json_encode($warehouses) ?>;
    }

    if (typeof products === 'undefined' || !products) {
        var products = <?= json_encode($products) ?>;
    }
    setTimeout(function() {
        $('.chzn-select').chosen({
            search_contains: true,
            no_results_text: "No record found"
        });
    }, 500);

    searchTransfer();

    function searchTransfer(page = 1) {

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
        data.append('from_warehouse', $('#from_warehouse').val());
        data.append('to_warehouse', $('#to_warehouse').val());
        data.append('status', $('#status').val());

        fetch('index.php?r=stock/inventorystocktransfer', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderTransfers(res.data);
                    renderPagination(res.page, res.total, res.limit);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.close();
                Swal.fire('Error', 'Unable to load stock transfers.', 'error');
            });

    }

    function statusBadge(status) {
        if (status == 'Completed') return '<span class="label label-success">Completed</span>';
        if (status == 'Cancelled') return '<span class="label label-danger">Cancelled</span>';
        return '<span class="label label-warning">Pending</span>';
    }

    function renderTransfers(rows) {
        let html = '';
        if (rows.length == 0) {
            html = '<tr><td colspan="9" class="text-center">No Stock Transfers Found</td></tr>';
        } else {
            rows.forEach(function(item, i) {
                html += `
                    <tr>
                    <td>${i+1}</td>
                    <td>${item.transfer_no}</td>
                    <td>${item.from_warehouse_name}</td>
                    <td>${item.to_warehouse_name}</td>
                    <td>${item.transfer_date??''}</td>
                    <td>${item.item_count}</td>
                    <td>${statusBadge(item.status)}</td>
                    <td>${item.remarks??''}</td>
                    <td>
                    <button onclick="editTransfer(${item.id})">
                    <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button onclick="deleteTransfer(${item.id})">
                    <i class="fa fa-trash"></i>
                    </button>
                    </td>
                    </tr>`;
            });
        }
        $('#transfer_table tbody').html(html);
    }

    function renderPagination(page, total, limit) {
        let pages = Math.ceil(total / limit);
        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `
                <button class=" ${i==page?'btn-primary':'btn-default'}"
                onclick="searchTransfer(${i})">
                ${i}
                </button>`;
        }
        $('#paginationArea').html(html);
    }

    function editTransfer(id) {

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

        fetch('index.php?r=stock/inventorystocktransfer', {
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
                    openTransferModal(row);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
    }

    function openTransferModal(row = null) {

        let edit = row != null;
        let id = edit ? row.id : '';
        let fromWarehouse = edit ? row.from_warehouse : '';
        let toWarehouse = edit ? row.to_warehouse : '';
        let status = edit ? row.status : 'Pending';
        let remarks = edit ? (row.remarks ?? '') : '';

        let fromOptions = '<option value="">Select Warehouse</option>';
        let toOptions = '<option value="">Select Warehouse</option>';
        warehouses.forEach(function(w) {
            fromOptions += `<option value="${w.id}" ${w.id==fromWarehouse?'selected':''}>${w.warehouse_name}</option>`;
            toOptions += `<option value="${w.id}" ${w.id==toWarehouse?'selected':''}>${w.warehouse_name}</option>`;
        });

        Swal.fire({
            title: edit ? 'Update Stock Transfer' : 'New Stock Transfer',
            width: '850px',
            html: `
                <form id="transferForm">
                <input type="hidden" id="swal_id" value="${id}">
                <div class="row">
                <div class="col-md-4">
                <label>From Warehouse</label>
                <select id="swal_from" class="form-control" ${edit ? 'disabled' : ''}>${fromOptions}</select>
                </div>
                <div class="col-md-4">
                <label>To Warehouse</label>
                <select id="swal_to" class="form-control" ${edit ? 'disabled' : ''}>${toOptions}</select>
                </div>
                <div class="col-md-4">
                <label>Status</label>
                <select id="swal_status" class="form-control">
                <option value="Pending" ${status=='Pending'?'selected':''}>Pending</option>
                <option value="Completed" ${status=='Completed'?'selected':''}>Completed</option>
                <option value="Cancelled" ${status=='Cancelled'?'selected':''}>Cancelled</option>
                </select>
                </div>
                </div>
                <div class="row" style="margin-top:10px;">
                <div class="col-md-12">
                <label>Remarks</label>
                <input id="swal_remarks" class="form-control" value="${remarks}">
                </div>
                </div>

                <hr>

                <div id="itemsArea"></div>

                <div class="text-right" style="margin-top:15px;">
                    <button type="button" ${edit ? 'style="display:none;"' : ''} onclick="addTransferItem()">
                        <i class="fa fa-plus"></i>
                        Add Item
                    </button>
                </div>
                </form>
                `,
            showCancelButton: true,
            confirmButtonText: edit ? 'Update' : 'Save',
            didOpen: () => {
                if (edit && row.items) {
                    row.items.forEach(item => addTransferItem(item, true));
                } else {
                    addTransferItem();
                }
            },
            preConfirm: () => {
                if (!edit && ($('#swal_from').val() == '' || $('#swal_to').val() == '')) {
                    Swal.showValidationMessage('From and To warehouse are required');
                    return false;
                }
                if (!edit && $('#swal_from').val() == $('#swal_to').val()) {
                    Swal.showValidationMessage('From and To warehouse cannot be the same');
                    return false;
                }
                let items = [];
                $('.transfer-item').each(function() {
                    items.push({
                        product_id: $(this).find('.product_id').val(),
                        quantity: $(this).find('.quantity').val(),
                        remarks: $(this).find('.item_remarks').val()
                    });
                });
                return {
                    id: id,
                    from_warehouse: fromWarehouse || $('#swal_from').val(),
                    to_warehouse: toWarehouse || $('#swal_to').val(),
                    status: $('#swal_status').val(),
                    remarks: $('#swal_remarks').val(),
                    items: items
                };
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                saveTransfer(result.value, edit);
            }
        });
    }

    function addTransferItem(item = {}, readOnly = false) {

        let options = '<option value="">Select Product</option>';
        products.forEach(function(p) {
            options += `<option value="${p.id}" ${p.id==item.product_id?'selected':''}>${p.product_name} (${p.sku??''})</option>`;
        });

        let html = `
            <div class="row transfer-item" style="margin-bottom:5px;">
            <div class="col-md-5">
            <select class="form-control product_id" ${readOnly ? 'disabled' : ''}>${options}</select>
            </div>
            <div class="col-md-3">
            <input type="number" class="form-control quantity" placeholder="Quantity" value="${item.quantity??''}" ${readOnly ? 'disabled' : ''}>
            </div>
            <div class="col-md-3">
            <input class="form-control item_remarks" placeholder="Remarks" value="${item.remarks??''}" ${readOnly ? 'disabled' : ''}>
            </div>
            <div class="col-md-1">
            ${readOnly ? '' : `<button type="button" class="btn-danger" onclick="$(this).closest('.transfer-item').remove()">
            <i class="fa fa-trash"></i>
            </button>`}
            </div>
            </div>`;

        $('#itemsArea').append(html);
    }

    function saveTransfer(formData, edit) {

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
        data.append('from_warehouse', formData.from_warehouse);
        data.append('to_warehouse', formData.to_warehouse);
        data.append('status', formData.status);
        data.append('remarks', formData.remarks);
        data.append('items', JSON.stringify(formData.items));

        fetch('index.php?r=stock/inventorystocktransfer', {
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
                        searchTransfer();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Unable to save stock transfer.', 'error');
            });
    }

    function deleteTransfer(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This transfer will be deleted and, if completed, its stock effect reversed.',
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

            fetch('index.php?r=stock/inventorystocktransfer', {
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
                            searchTransfer();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Unable to delete transfer.', 'error');
                });
        });
    }
</script>

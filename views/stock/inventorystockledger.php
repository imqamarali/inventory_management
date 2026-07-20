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
                <li class="active">Stock Ledger</li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <div class="row">
                <div class="col-md-12">
                    <input type="text" id="keyword" class="new-input" style="width:10%" placeholder="Product / SKU / Movement No">
 
                    <select id="product_id" class="chzn-select" style="width:15%">
                        <option value="">All Products</option>
                        <?php foreach ($products as $p) { ?>
                            <option value="<?= $p['id'] ?>"><?= Html::encode($p['product_name']) ?></option>
                        <?php } ?>
                    </select>

                    <select id="movement_type" class="new-input" style="width:10%">
                        <option value="">IN / OUT</option>
                        <option value="IN">IN</option>
                        <option value="OUT">OUT</option>
                    </select>

                    <select id="reference_type" class="new-input" style="width:14%">
                        <option value="">All References</option>
                        <option value="Adjustment">Adjustment</option>
                        <option value="Opening Stock">Opening Stock</option>
                        <option value="Transfer In">Transfer In</option>
                        <option value="Transfer Out">Transfer Out</option>
                    </select>

                    <input type="date" id="date_from" class="new-input" style="width:12%">
                    <input type="date" id="date_to" class="new-input" style="width:12%">
                 
                    <input type="number" id="per_page" class="new-input" style="width:10%" value="10" placeholder="Records">
                    <input type="button" class="btn btn-primary" onclick="searchLedger()" value="Search"
                        style="height:30px;padding:0;">
                </div>
            </div>
        </div>

        <div class="widget-main">
            <div class="alert alert-info">
                <i class="ace-icon fa fa-info-circle"></i>
                The stock ledger is system generated from every movement (adjustments, opening stock, transfers). Only
                remarks can be edited here; quantity corrections should go through Stock Adjustment for full traceability.
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="ledger_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Movement No</th>
                            <th>Date</th>
                            <th>Warehouse</th>
                            <th>Product</th>
                            <th>Reference</th>
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
                            <td colspan="12" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
                <div id="paginationArea" class="text-center"></div>
            </div>
        </div>

    </div>
</div>

<script>
    setTimeout(function() {
        $('.chzn-select').chosen({
            search_contains: true,
            no_results_text: "No record found"
        });
    }, 500);

    searchLedger();

    function searchLedger(page = 1) {

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
        data.append('product_id', $('#product_id').val());
        data.append('movement_type', $('#movement_type').val());
        data.append('reference_type', $('#reference_type').val());
        data.append('date_from', $('#date_from').val());
        data.append('date_to', $('#date_to').val());

        fetch('index.php?r=stock/inventorystockledger', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderLedger(res.data);
                    renderPagination(res.page, res.total, res.limit);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.close();
                Swal.fire('Error', 'Unable to load stock ledger.', 'error');
            });

    }

    function renderLedger(rows) {
        let html = '';
        if (rows.length == 0) {
            html = '<tr><td colspan="12" class="text-center">No Ledger Entries Found</td></tr>';
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
                    <td>${item.reference_type??''}</td>
                    <td>${typeBadge}</td>
                    <td>${parseFloat(item.quantity).toFixed(2)}</td>
                    <td>${parseFloat(item.unit_cost??0).toFixed(2)}</td>
                    <td>${parseFloat(item.total_cost??0).toFixed(2)}</td>
                    <td>${item.remarks??''}</td>
                    <td>
                    <button onclick='editRemarks(${JSON.stringify(item)})'>
                    <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button onclick="deleteLedgerEntry(${item.id})">
                    <i class="fa fa-trash"></i>
                    </button>
                    </td>
                    </tr>`;
            });
        }
        $('#ledger_table tbody').html(html);
    }

    function renderPagination(page, total, limit) {
        let pages = Math.ceil(total / limit);
        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `
                <button class="${i==page?'btn-primary':'btn-default'}"
                onclick="searchLedger(${i})">
                ${i}
                </button>`;
        }
        $('#paginationArea').html(html);
    }

    function editRemarks(item) {
        Swal.fire({
            title: 'Update Remarks',
            html: `
                <div class="form-group" style="text-align:left;">
                <label>Movement No: ${item.movement_no}</label>
                <textarea id="swal_remarks" class="form-control" rows="3">${item.remarks??''}</textarea>
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'Save',
            preConfirm: () => {
                return {
                    id: item.id,
                    remarks: document.getElementById('swal_remarks').value
                };
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                saveRemarks(result.value);
            }
        });
    }

    function saveRemarks(formData) {

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

        fetch('index.php?r=stock/inventorystockledger', {
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
                        searchLedger();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Unable to update remarks.', 'error');
            });
    }

    function deleteLedgerEntry(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This ledger entry will be removed and its stock effect reversed.',
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

            fetch('index.php?r=stock/inventorystockledger', {
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
                            searchLedger();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Unable to delete entry.', 'error');
                });
        });
    }
</script>

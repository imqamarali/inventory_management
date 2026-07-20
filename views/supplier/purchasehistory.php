<?php

use yii\helpers\Html;

if (!isset($purchases)) {
    $purchases = [];
}
if (!isset($suppliers)) {
    $suppliers = [];
}
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li class="active">Purchase History</li>
            </ul>
        </div>
        <div style="padding-top:10px;padding-left:13px;">
            <form id="purchase_history_search">
                <select id="supplier_id" class="new-input" style="width:20%;">
                    <option value="">All Suppliers</option>
                    <?php foreach ($suppliers as $supplier) { ?>
                        <option value="<?= $supplier['id'] ?>">
                            <?= Html::encode($supplier['company_name']) ?>
                        </option>
                    <?php } ?>
                </select>
                <input type="text" id="po_number" class="new-input" placeholder="PO Number" style="width:14%;">
                <select id="status" class="new-input" style="width:13%;">
                    <option value="">Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <input type="date" id="from_date" class="new-input" style="width:13%;">
                <input type="date" id="to_date" class="new-input" style="width:13%;">
                <input type="text" id="per_page" class="new-input" value="<?= $perPage ?? 20 ?>" placeholder="Rows" style="width:8%;">
                <input type="button" class="btn btn-primary" value="Search" style="height:30px;padding:0 15px;margin-top:-3px;" onclick="searchPurchaseHistory()">
            </form>
        </div>
        <div class="widget-main">

            <?php if (count($purchases) == 0) { ?>

                <div class="alert alert-info text-center">
                    <i class="ace-icon fa fa-shopping-cart fa-3x" style="color:#6FB3E0;"></i>
                    <h4 style="margin-top:15px;">No Purchase History Found</h4>
                    <p>No purchase records available.</p>
                </div>

            <?php } else { ?>

                <div class="table-responsive">

                    <table class="table table-striped table-bordered table-hover" id="purchase_history_table">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>PO No</th>
                                <th>Supplier</th>
                                <th>Warehouse</th>
                                <th>Order Date</th>
                                <th>Status</th>
                                <th>Total Amount</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($purchases as $key => $item) { ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><?= Html::encode($item['po_number']) ?></td>
                                    <td><?= Html::encode($item['company_name']) ?></td>
                                    <td><?= Html::encode($item['warehouse_name']) ?></td>
                                    <td><?= Html::encode($item['order_date']) ?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'label-default';
                                        if ($item['status'] == 'Completed') {
                                            $statusClass = 'label-success';
                                        } elseif ($item['status'] == 'Pending') {
                                            $statusClass = 'label-warning';
                                        } elseif ($item['status'] == 'Cancelled') {
                                            $statusClass = 'label-danger';
                                        } elseif ($item['status'] == 'Approved') {
                                            $statusClass = 'label-info';
                                        }
                                        ?>
                                        <span class="label <?= $statusClass ?>">
                                            <?= Html::encode($item['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($item['grand_total'] ?? 0, 2) ?></td>
                                    <td><?= Html::encode($item['remarks']) ?></td>
                                    <td>
                                        <button class="btn btn-xs btn-info" onclick='viewPurchase(<?= json_encode($item) ?>)'>
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div id="paginationArea" class="text-center"></div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function searchPurchaseHistory(page = 1) {
        Swal.fire({
            title: 'Loading Purchase History...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'search');
        data.append('supplier_id', $('#supplier_id').val());
        data.append('po_number', $('#po_number').val());
        data.append('status', $('#status').val());
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=supplier/supplierpurchasehistory', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderPurchaseHistory(res.purchases);
                    renderPagination(res.page, res.total_pages);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.close();
                Swal.fire('Error', 'Unable to fetch purchase history.', 'error');
            });
    }

    function renderPurchaseHistory(purchases) {
        let html = '';
        if (purchases.length == 0) {
            html = `<tr><td colspan="9" class="text-center">No Purchase History Found</td></tr>`;
        } else {
            purchases.forEach(function(item, index) {
                let status = 'label-default';
                if (item.status == 'Completed') status = 'label-success';
                else if (item.status == 'Pending') status = 'label-warning';
                else if (item.status == 'Approved') status = 'label-info';
                else if (item.status == 'Cancelled') status = 'label-danger';
                html += `
                    <tr>
                    <td>${index+1}</td>
                    <td>${item.po_number??''}</td>
                    <td>${item.company_name??''}</td>
                    <td>${item.warehouse_name??''}</td>
                    <td>${item.order_date??''}</td>
                    <td><span class="label ${status}">${item.status??''}</span></td>
                    <td>${Number(item.grand_total??0).toLocaleString()}</td>
                    <td>${item.remarks??''}</td>
                    <td>
                    <button class="btn btn-xs btn-info" onclick='viewPurchase(${JSON.stringify(item)})'>
                    <i class="fa fa-eye"></i>
                    </button>
                    </td>
                    </tr>`;
            });
        }
        $('#purchase_history_table tbody').html(html);
    }

    function viewPurchase(data) {

        Swal.fire({
            title: 'Purchase Details',
            width: '1000px',
            confirmButtonColor: '#87B87F',
            confirmButtonText: 'Close',
            html: `
                <div class="row">
                <div class="col-md-3">
                <label>PO Number</label>
                <input class="form-control" readonly value="${data.po_number??''}">
                </div>
                <div class="col-md-3">
                <label>Supplier</label>
                <input class="form-control" readonly value="${data.company_name??''}">
                </div>
                <div class="col-md-3">
                <label>Warehouse</label>
                <input class="form-control" readonly value="${data.warehouse_name??''}">
                </div>
                <div class="col-md-3">
                <label>Order Date</label>
                <input class="form-control" readonly value="${data.order_date??''}">
                </div>
                </div>

                <div class="row" style="margin-top:10px;">
                <div class="col-md-3">
                <label>Status</label>
                <input class="form-control" readonly value="${data.status??''}">
                </div>
                <div class="col-md-3">
                <label>Total Amount</label>
                <input class="form-control" readonly value="${Number(data.grand_total??0).toLocaleString()}">
                </div>
                <div class="col-md-3">
                <label>Discount</label>
                <input class="form-control" readonly value="${Number(data.discount_amount??0).toLocaleString()}">
                </div>
                <div class="col-md-3">
                <label>Tax</label>
                <input class="form-control" readonly value="${Number(data.tax_amount??0).toLocaleString()}">
                </div>
                </div>

                <div class="row" style="margin-top:10px;">
                <div class="col-md-12">
                <label>Remarks</label>
                <textarea class="form-control" rows="3" readonly>${data.remarks??''}</textarea>
                </div>
                </div>
                `
        });
    } 
    function renderPagination(page, totalPages) {
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `<button class="${i==page?'btn-primary':'btn-default'}" onclick="searchPurchaseHistory(${i})">${i}</button> `;
        }
        $('#paginationArea').html(html);
    }

    $(function() {

        $('#supplier_id,#status,#from_date,#to_date').change(function() {
            searchPurchaseHistory();
        });

        $('#po_number,#per_page').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                searchPurchaseHistory();
            }
        });

    });
</script>
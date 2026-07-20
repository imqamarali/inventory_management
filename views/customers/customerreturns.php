<?php

use yii\helpers\Html;

if (!isset($returns)) $returns = [];
if (!isset($customers)) $customers = [];
if (!isset($salesOrders)) $salesOrders = [];
if (!isset($total)) $total = 0;
if (!isset($page)) $page = 1;
if (!isset($perPage)) $perPage = 20;
if (!isset($totalPages)) $totalPages = 1;
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=customers/customerdashboard">Home</a>
                </li>
                <li class="active">Customer Returns</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openReturnModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Record Return
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="return_search" onsubmit="return false;">

                <select name="customer_id" id="customer_id" class="new-input" style="width:20%;">
                    <option value="">All Customers</option>
                    <?php foreach ($customers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['name']) ?></option>
                    <?php } ?>
                </select>

                <select name="status" id="status" class="new-input" style="width:15%;">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>

                <input type="date" name="from_date" id="from_date" class="new-input" style="width:12%;">
                <input type="date" name="to_date" id="to_date" class="new-input" style="width:12%;">

                <input type="text" name="per_page" id="per_page" value="20" class="new-input" style="width:6%;" placeholder="Records?">

                <input type="button" class="btn btn-primary"
                    onclick="searchform()"
                    value="Search"
                    style="height:30px;padding:0;margin-top:-3px;" />

            </form>
        </div>

        <div class="widget-main">

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="return_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Return No</th>
                            <th>Customer</th>
                            <th>Order Ref</th>
                            <th>Return Date</th>
                            <th>Reason</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($returns as $key => $item) { ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= Html::encode($item['return_no']) ?></td>
                                <td><?= Html::encode($item['customer_name'] ?? '') ?></td>
                                <td><?= Html::encode($item['order_number'] ?? '') ?></td>
                                <td><?= Html::encode($item['return_date']) ?></td>
                                <td><?= Html::encode($item['reason'] ?? '') ?></td>
                                <td><?= number_format($item['grand_total'] ?? 0, 2) ?></td>
                                <td>
                                    <?php
                                    $statusMap = ['Pending' => 'warning', 'Approved' => 'info', 'Completed' => 'success', 'Cancelled' => 'danger'];
                                    $statusColor = $statusMap[$item['status']] ?? 'default';
                                    ?>
                                    <span class="label label-<?= $statusColor ?>"><?= Html::encode($item['status']) ?></span>
                                </td>
                                <td>
                                    <button onclick='openReturnModal(<?= json_encode($item) ?>)'>
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    |
                                    <button onclick="deleteReturn(<?= $item['id'] ?>)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div id="paginationArea" class="text-center"></div>

            </div>

        </div>

    </div>
</div>

<style>
    .swal2-popup.swal-wide-popup {
        width: 900px !important;
        max-width: 95vw !important;
    }

    .swal2-popup.swal-wide-popup .swal2-html-container {
        max-height: none !important;
        overflow: visible !important;
    }
</style>

<script>
    let customers = <?= json_encode($customers) ?>;
    let salesOrders = <?= json_encode($salesOrders) ?>;

    searchform();

    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading Returns...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'search');
        data.append('customer_id', $('#customer_id').val());
        data.append('status', $('#status').val());
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=customers/customerreturns', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderReturns(res.returns);
                    renderPagination(res.page, res.totalPages);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load returns.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });
    }

    function renderReturns(rows) {

        let html = '';

        if (rows.length == 0) {

            html = `
        <tr>
            <td colspan="9" class="text-center">
                No Returns Found
            </td>
        </tr>`;

        } else {

            rows.forEach(function(item, index) {

                const statusMap = {
                    'Pending': 'warning',
                    'Approved': 'info',
                    'Completed': 'success',
                    'Cancelled': 'danger'
                };
                const statusClass = statusMap[item.status] || 'default';

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.return_no}</td>
                <td>${item.customer_name??''}</td>
                <td>${item.order_number??''}</td>
                <td>${item.return_date??''}</td>
                <td>${item.reason??''}</td>
                <td>${parseFloat(item.grand_total||0).toFixed(2)}</td>
                <td><span class="label label-${statusClass}">${item.status}</span></td>
                <td>
                    <button onclick='openReturnModal(${JSON.stringify(item)})'>
                        <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button onclick="deleteReturn(${item.id})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;

            });

        }

        $('#return_table tbody').html(html);

    }

    function renderPagination(page, totalPages) {
        if (totalPages <= 1) {
            $('#paginationArea').html('');
            return;
        }
        let html = '<nav><ul class="pagination">';
        if (page > 1) {
            html += `<li class="page-item"><a class="page-link" onclick="searchform(1)">First</a></li>`;
            html += `<li class="page-item"><a class="page-link" onclick="searchform(${page - 1})">Previous</a></li>`;
        }
        for (let i = Math.max(1, page - 2); i <= Math.min(totalPages, page + 2); i++) {
            if (i === page) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link" onclick="searchform(${i})">${i}</a></li>`;
            }
        }
        if (page < totalPages) {
            html += `<li class="page-item"><a class="page-link" onclick="searchform(${page + 1})">Next</a></li>`;
            html += `<li class="page-item"><a class="page-link" onclick="searchform(${totalPages})">Last</a></li>`;
        }
        html += '</ul></nav>';
        $('#paginationArea').html(html);
    }

    function openReturnModal(item = null) {
        let html = `
        <form id="returnForm" style="padding:15px;">
            <input type="hidden" name="id" id="return_id" value="${item?.id || ''}">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div>
                    <label class="form-label">Customer</label>
                    <select class="form-control" name="customer_id" id="ret_customer_id" required>
                        <option value="">Select Customer</option>
                        ${customers.map(c => `<option value="${c.id}" ${item?.customer_id == c.id ? 'selected' : ''}>${c.name}</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label class="form-label">Sales Order</label>
                    <select class="form-control" name="sales_order_id">
                        <option value="">Select Order</option>
                        ${salesOrders.map(o => `<option value="${o.id}" ${item?.sales_order_id == o.id ? 'selected' : ''}>${o.order_number}</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label class="form-label">Return Date</label>
                    <input type="date" class="form-control" name="return_date" value="${item?.return_date || new Date().toISOString().split('T')[0]}" required>
                </div>
                <div>
                    <label class="form-label">Reason</label>
                    <input type="text" class="form-control" name="reason" value="${item?.reason || ''}" placeholder="Return reason">
                </div>
                <div>
                    <label class="form-label">Refund Amount</label>
                    <input type="number" class="form-control" name="grand_total" value="${item?.grand_total || 0}" step="0.01" required>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status">
                        <option value="Pending" ${item?.status === 'Pending' ? 'selected' : ''}>Pending</option>
                        <option value="Approved" ${item?.status === 'Approved' ? 'selected' : ''}>Approved</option>
                        <option value="Completed" ${item?.status === 'Completed' ? 'selected' : ''}>Completed</option>
                        <option value="Cancelled" ${item?.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                    </select>
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label">Remarks</label>
                    <textarea class="form-control" name="remarks" rows="2" placeholder="Remarks">${item?.remarks || ''}</textarea>
                </div>
            </div>
        </form>`;

        Swal.fire({
            title: item ? 'Edit Return' : 'Record Return',
            html: html,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Save',
            didOpen: () => {
                $('#ret_customer_id').focus();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                saveReturn();
            }
        });
    }

    function saveReturn() {
        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('id', $('#return_id').val());
        data.append('customer_id', $('select[name="customer_id"]').val());
        data.append('sales_order_id', $('select[name="sales_order_id"]').val());
        data.append('return_date', $('input[name="return_date"]').val());
        data.append('reason', $('input[name="reason"]').val());
        data.append('grand_total', $('input[name="grand_total"]').val());
        data.append('status', $('select[name="status"]').val());
        data.append('remarks', $('textarea[name="remarks"]').val());

        fetch('index.php?r=customers/customerreturns', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Success', res.message, 'success').then(() => {
                        searchform();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'Unable to save!', 'error');
            });
    }

    function deleteReturn(id) {
        Swal.fire({
            title: 'Confirm Delete',
            text: 'Are you sure you want to delete this return?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                const data = new FormData();
                data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
                data.append('id', id);
                data.append('flag', 'delete');

                fetch('index.php?r=customers/customerreturns', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire('Deleted', res.message, 'success').then(() => {
                                searchform();
                            });
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire('Error', 'Unable to delete!', 'error');
                    });
            }
        });
    }
</script>

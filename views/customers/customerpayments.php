<?php

use yii\helpers\Html;

if (!isset($customers)) $customers = [];
if (!isset($payments)) $payments = [];
$customer_id = $customer_id ?? 0;
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=customers/customerdashboard">Home</a>
                </li>
                <li class="active">Customer Payments</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openPaymentModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Record Payment
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="payment_search" onsubmit="return false;">

                <select name="customer_id" id="customer_id" class="new-input" style="width:25%;">
                    <option value="">All Customers</option>
                    <?php foreach ($customers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['name']) ?></option>
                    <?php } ?>
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
                <table class="table table-striped table-bordered table-hover" id="payment_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Payment No</th>
                            <th>Customer</th>
                            <th>Payment Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $key => $item) { ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= Html::encode($item['payment_no']) ?></td>
                                <td><?= Html::encode($item['customer_name'] ?? '') ?></td>
                                <td><?= Html::encode($item['payment_date']) ?></td>
                                <td><?= number_format($item['amount'], 2) ?></td>
                                <td><?= Html::encode($item['payment_method'] ?? '') ?></td>
                                <td><?= Html::encode($item['remarks'] ?? '') ?></td>
                                <td>
                                    <button onclick="deletePayment(<?= $item['id'] ?>)">
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

    searchform();

    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading Payments...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'load');
        data.append('customer_id', $('#customer_id').val());
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=customers/customerpayments', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderPayments(res.payments);
                    renderPagination(res.page, res.totalPages);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load payments.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });
    }

    function renderPayments(rows) {

        let html = '';

        if (rows.length == 0) {

            html = `
        <tr>
            <td colspan="8" class="text-center">
                No Payments Found
            </td>
        </tr>`;

        } else {

            rows.forEach(function(item, index) {

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.payment_no}</td>
                <td>${item.customer_name??''}</td>
                <td>${item.payment_date??''}</td>
                <td>${parseFloat(item.amount).toFixed(2)}</td>
                <td>${item.payment_method??''}</td>
                <td>${item.remarks??''}</td>
                <td>
                    <button onclick="deletePayment(${item.id})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;

            });

        }

        $('#payment_table tbody').html(html);

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

    function openPaymentModal() {
        let html = `
        <form id="paymentForm" style="padding:15px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                <div>
                    <label class="form-label">Customer</label>
                    <select class="form-control" name="customer_id" id="pay_customer_id" required>
                        <option value="">Select Customer</option>
                        ${customers.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label class="form-label">Payment Date</label>
                    <input type="date" class="form-control" name="payment_date" value="${new Date().toISOString().split('T')[0]}" required>
                </div>
                <div>
                    <label class="form-label">Amount</label>
                    <input type="number" class="form-control" name="amount" step="0.01" required>
                </div>
                <div>
                    <label class="form-label">Payment Method</label>
                    <select class="form-control" name="payment_method">
                        <option value="Cash">Cash</option>
                        <option value="Bank">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Online">Online</option>
                    </select>
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label">Remarks</label>
                    <textarea class="form-control" name="remarks" rows="2" placeholder="Remarks"></textarea>
                </div>
            </div>
        </form>`;

        Swal.fire({
            title: 'Record Payment',
            html: html,
            width: '1000px',
            showCancelButton: true,
            confirmButtonText: 'Record',
            didOpen: () => {
                $('#pay_customer_id').focus();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                savePayment();
            }
        });
    }

    function savePayment() {
        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('customer_id', $('select[name="customer_id"]').val());
        data.append('payment_date', $('input[name="payment_date"]').val());
        data.append('amount', $('input[name="amount"]').val());
        data.append('payment_method', $('select[name="payment_method"]').val());
        data.append('remarks', $('textarea[name="remarks"]').val());

        fetch('index.php?r=customers/customerpayments', {
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

    function deletePayment(id) {
        Swal.fire({
            title: 'Confirm Delete',
            text: 'Are you sure you want to delete this payment?',
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

                fetch('index.php?r=customers/customerpayments', {
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

<?php

use yii\helpers\Html;

if (!isset($payments)) {
    $payments = [];
}
if (!isset($suppliers)) {
    $suppliers = [];
}
if (!isset($accounts)) {
    $accounts = [];
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
                <li class="active">Supplier Payments</li>
                <li style="float:right;">
                    <div class="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openPaymentModal()">
                                <i class="ace-icon fa fa-plus"></i> Add Payment
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">

            <form id="supplier_payment_search">

                <input type="hidden" name="r" value="supplier/supplierpayments">

                <select id="supplier_id" class="new-input" style="width:22%;">
                    <option value="">All Suppliers</option>
                    <?php foreach ($suppliers as $supplier) { ?>
                        <option value="<?= $supplier['id'] ?>">
                            <?= Html::encode($supplier['company_name']) ?>
                        </option>
                    <?php } ?>
                </select>

                <input
                    type="text"
                    id="payment_no"
                    class="new-input"
                    style="width:14%;"
                    placeholder="Payment No">

                <select
                    id="payment_method"
                    class="new-input"
                    style="width:13%;">

                    <option value="">Method</option>
                    <option value="Cash">Cash</option>
                    <option value="Bank">Bank</option>
                    <option value="Cheque">Cheque</option>
                    <option value="Online">Online</option>

                </select>

                <input
                    type="date"
                    id="from_date"
                    class="new-input"
                    style="width:13%;">

                <input
                    type="date"
                    id="to_date"
                    class="new-input"
                    style="width:13%;">

                <input
                    type="text"
                    id="per_page"
                    value="<?= $perPage ?? 20 ?>"
                    class="new-input"
                    style="width:7%;"
                    placeholder="Rows">

                <input
                    type="button"
                    class="btn btn-primary"
                    value="Search"
                    style="height:30px;padding:0 15px;margin-top:-3px;"
                    onclick="searchSupplierPayments()">

            </form>

        </div>
        <div class="widget-main">
            <?php if (count($payments) == 0) { ?>
                <div class="alert alert-info text-center">
                    <i class="ace-icon fa fa-money fa-3x" style="color:#6FB3E0;"></i>
                    <h4 style="margin-top:15px;">No Payments Found</h4>
                    <p>No supplier payments available.</p>
                </div>
            <?php } else { ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="supplier_payment_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Payment No</th>
                                <th>Supplier</th>
                                <th>Method</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $key => $item) { ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><?= Html::encode($item['payment_no']) ?></td>
                                    <td><?= Html::encode($item['company_name']) ?></td>
                                    <td><?= Html::encode($item['payment_method']) ?></td>
                                    <td><?= Html::encode($item['payment_type']) ?></td>
                                    <td><?= Html::encode($item['payment_date']) ?></td>
                                    <td><?= number_format($item['amount'] ?? 0, 2) ?></td>
                                    <td><?= Html::encode($item['remarks']) ?></td>
                                    <td>
                                        <button type="button" onclick='openPaymentModal(<?= json_encode($item) ?>)'><i class="fa fa-pencil"></i></button>
                                        &nbsp;|&nbsp;
                                        <button type="button" onclick="deletePayment(<?= $item['id'] ?>)"><i class="fa fa-trash"></i></button>
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
    function searchSupplierPayments(page = 1) {
        Swal.fire({
            title: 'Loading...',
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
        data.append('payment_no', $('#payment_no').val());
        data.append('payment_method', $('#payment_method').val());
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);
        fetch('index.php?r=supplier/supplierpayments', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderSupplierPayments(res.payments);
                    renderPagination(res.page, res.total_pages);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.close();
                Swal.fire('Error', 'Unable to load supplier payments.', 'error');
            });
    }

    function renderSupplierPayments(payments) {
        let html = '';
        if (payments.length == 0) {
            html = `<tr><td colspan="9" class="text-center">No Payments Found</td></tr>`;
        } else {
            payments.forEach(function(item, index) {
                html += `
                        <tr>
                        <td>${index+1}</td>
                        <td>${item.payment_no??''}</td>
                        <td>${item.company_name??''}</td>
                        <td>${item.payment_method??''}</td>
                        <td>${item.payment_type??''}</td>
                        <td>${item.payment_date??''}</td>
                        <td>${Number(item.amount??0).toLocaleString()}</td>
                        <td>${item.remarks??''}</td>
                        <td>
                        <button type="button" onclick='openPaymentModal(${JSON.stringify(item)})'><i class="fa fa-pencil"></i></button>
                        &nbsp;|&nbsp;
                        <button type="button" onclick="deletePayment(${item.id})"><i class="fa fa-trash"></i></button>
                        </td>
                        </tr>`;
            });
        }
        $('#supplier_payment_table tbody').html(html);
    }

    function openPaymentModal(data = null) {
        const edit = data !== null;
        const id = edit ? (data.id || '') : '';
        const supplier_id = edit ? (data.reference_id || '') : '';
        const payment_date = edit ? (data.payment_date || '<?= date("Y-m-d") ?>') : '<?= date("Y-m-d") ?>';
        const payment_method = edit ? (data.payment_method || 'Cash') : 'Cash';
        const payment_type = edit ? (data.payment_type || 'Pay') : 'Pay';
        const account_id = edit ? (data.account_id || '') : '';
        const amount = edit ? (data.amount || '0') : '0';
        const remarks = edit ? (data.remarks || '') : '';

        Swal.fire({
            title: edit ? 'Update Supplier Payment' : 'New Supplier Payment',
            width: '900px', 
            heightAuto:false,
            scrollbarPadding:false,
            showCancelButton: true,
            customClass:{
                popup:'supplier-modal'
            },
            confirmButtonColor: '#87B87F',
            cancelButtonColor: '#6c757d',
            confirmButtonText: edit ? '<i class="fa fa-save"></i> Update' : '<i class="fa fa-save"></i> Save',
            html:`
                <input type="hidden" id="swal_payment_id" value="${id}">

                <div class="row">
                    <div class="col-md-6">
                        <label>Supplier *</label>
                        <select id="swal_supplier_id" class="form-control">
                            <option value="">Select Supplier</option>
                            <?php foreach($suppliers as $supplier){ ?>
                            <option value="<?= $supplier['id'] ?>">
                                <?= Html::encode($supplier['company_name']) ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Payment Date *</label>
                        <input type="date" id="swal_payment_date" class="form-control" value="${payment_date}">
                    </div>
                </div>

                <div class="row" style="margin-top:10px;">
                    <div class="col-md-6">
                        <label>Payment Type</label>
                        <select id="swal_payment_type" class="form-control">
                            <option value="Pay">Pay</option>
                            <option value="Receive">Receive</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Payment Method</label>
                        <select id="swal_payment_method" class="form-control">
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Online">Online</option>
                        </select>
                    </div>
                </div>

                <div class="row" style="margin-top:10px;">
                    <div class="col-md-6">
                        <label>Account *</label>
                        <select id="swal_account_id" class="form-control">
                            <option value="">Select Account</option>
                            <?php foreach($accounts as $account){ ?>
                            <option value="<?= $account['id'] ?>">
                                <?= Html::encode($account['account_name']) ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Amount *</label>
                        <input type="number" id="swal_amount" class="form-control" value="${amount}">
                    </div>
                </div>

                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <label>Remarks</label>
                        <textarea id="swal_remarks" class="form-control" rows="3">${remarks}</textarea>
                    </div>
                </div>
                `,
            didOpen: () => {
                $('#swal_supplier_id').val(supplier_id);
                $('#swal_payment_date').val(payment_date);
                $('#swal_payment_method').val(payment_method);
                $('#swal_payment_type').val(payment_type);
                $('#swal_account_id').val(account_id);
                $('#swal_amount').val(amount);
                $('#swal_remarks').val(remarks);
            },
            preConfirm: () => {
                if ($('#swal_supplier_id').val() == '' || $('#swal_amount').val() == '') {
                    Swal.showValidationMessage('Supplier and Amount are required.');
                    return false;
                }
                return {
                    id: $('#swal_payment_id').val(),
                    reference_id: $('#swal_supplier_id').val(),
                    payment_date: $('#swal_payment_date').val(),
                    payment_method: $('#swal_payment_method').val(),
                    payment_type: $('#swal_payment_type').val(), 
                    account_id: $('#swal_account_id').val(),
                    amount: $('#swal_amount').val(),
                    remarks: $('#swal_remarks').val()
                };
            }
        }).then(result => {
            if (result.isConfirmed) {
                savePayment(result.value);
            }
        });
    }

    function savePayment(formData) {
        const data = new FormData();  
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        Object.keys(formData).forEach(function(key) {
            if (key != 'active') {
                data.append(key, formData[key]);
            }
        });
 
        fetch('index.php?r=supplier/supplierpayments', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Success', res.message, 'success').then(() => {
                        $('.ajax-module.active').trigger('click');
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Unable to save payment.', 'error');
            });
    }

    function deletePayment(id) {
        Swal.fire({
            title: 'Delete Payment?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const data = new FormData();
                data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
                data.append('id', id);
                data.append('delete', 1);
                fetch('index.php?r=supplier/supplierpayments', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire('Deleted', res.message, 'success').then(() => {
                                $('.ajax-module.active').trigger('click');
                            });
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Unable to delete payment.', 'error');
                    });
            }
        });
    }

    function renderPagination(page, totalPages) {
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `<button class="${i==page?'btn btn-xs btn-primary':'btn btn-xs btn-default'}" onclick="searchSupplierPayments(${i})">${i}</button> `;
        }
        $('#paginationArea').html(html);
    }

    $(function() {
        $('#supplier_id,#payment_method,#from_date,#to_date').change(function() {
            searchSupplierPayments();
        });
        $('#payment_no,#per_page').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                searchSupplierPayments();
            }
        });
    });
</script>
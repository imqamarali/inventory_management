<?php
/**
 * PURCHASE RETURNS VIEW
 * ================================================================================
 * PURPOSE: Track returns of defective/incorrect goods back to suppliers
 *
 * FUNCTIONALITY:
 * - Record purchase returns to suppliers
 * - Link returns to original purchase invoices
 * - Specify return reason (Defective, Damaged, Wrong Item, Quality Issues)
 * - Generate debit notes for return amounts
 * - Track return status (Pending, Approved, Completed)
 * - Filter returns by supplier, status, date range
 * - Search by return number
 * - Support partial returns
 *
 * DATA MANAGEMENT:
 * - Stores in: inventory_purchase_returns table
 * - Foreign key: references inventory_purchase_invoices
 * - Records: return_no, purchase_invoice_id, supplier_id, return_date,
 *            reason, subtotal, tax_amount, grand_total, status, remarks
 * - Status: Pending, Approved, Completed
 * - Reverses inventory_stock movements
 *
 * FINANCE INTEGRATION:
 * - Purchase returns are EXPENSE REVERSALS (reduce COGS)
 * - Grand totals are subtracted from:
 *   • Cost of Goods Sold (reducing expense)
 *   • Accounts Payable (when invoice not yet paid)
 *   • Cash paid out (when we receive refund)
 * - Return tracking identifies supplier quality issues
 * - Enables accurate net purchase cost (Purchases - Returns)
 * - Approval status determines when adjustment is recorded
 * ================================================================================
 */

use yii\helpers\Html;

if (!isset($purchaseReturns)) $purchaseReturns = [];
if (!isset($purchaseInvoices)) $purchaseInvoices = [];
if (!isset($suppliers)) $suppliers = [];
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=purchase/purchasedashboard">Home</a>
                </li>
                <li class="active">Purchase Returns</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openReturnModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Purchase Return
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="return_search" onsubmit="return false;">

                <input type="text" name="return_no" id="return_no" class="new-input" style="width:15%;" placeholder="Return No">

                <select name="supplier_id" id="supplier_id" class="new-input" style="width:16%;">
                    <option value="">All Suppliers</option>
                    <?php foreach ($suppliers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['company_name']) ?></option>
                    <?php } ?>
                </select>

                <select name="purchase_invoice_id" id="purchase_invoice_id" class="new-input" style="width:16%;">
                    <option value="">All Invoices</option>
                    <?php foreach ($purchaseInvoices as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['invoice_no']) ?></option>
                    <?php } ?>
                </select>

                <select name="status" id="status" class="new-input" style="width:12%;">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>

                <input type="date" name="from_date" id="from_date" class="new-input" style="width:11%;">
                <input type="date" name="to_date" id="to_date" class="new-input" style="width:11%;">

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
                            <th>Invoice No</th>
                            <th>Supplier</th>
                            <th>Return Date</th>
                            <th>Reason</th>
                            <th>Grand Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchaseReturns as $key => $item) { ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= Html::encode($item['return_no']) ?></td>
                                <td><?= Html::encode($item['invoice_no']) ?></td>
                                <td><?= Html::encode($item['company_name']) ?></td>
                                <td><?= Html::encode($item['return_date']) ?></td>
                                <td><?= Html::encode($item['reason']) ?></td>
                                <td><?= number_format($item['grand_total'], 2) ?></td>
                                <td><?= returnStatusBadgeServer($item['status']) ?></td>
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

<?php
function returnStatusBadgeServer($status)
{
    $map = ['Pending' => 'warning', 'Approved' => 'primary', 'Completed' => 'success', 'Cancelled' => 'danger'];
    $cls = $map[$status] ?? 'default';
    return '<span class="label label-' . $cls . '">' . Html::encode($status) . '</span>';
}
?>

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
    if (typeof purchaseInvoices === 'undefined' || !purchaseInvoices) {
        var purchaseInvoices = <?= json_encode($purchaseInvoices) ?>;
    }
    if (typeof suppliers === 'undefined' || !suppliers) {
        var suppliers = <?= json_encode($suppliers) ?>;
    }
</script>

<script>
    searchform();

    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading Purchase Returns...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'load');
        data.append('return_no', $('#return_no').val());
        data.append('supplier_id', $('#supplier_id').val());
        data.append('purchase_invoice_id', $('#purchase_invoice_id').val());
        data.append('status', $('#status').val());
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=purchase/purchasereturns', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderReturns(res.purchaseReturns);
                    renderPagination(res.page, res.totalPages);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load purchase returns.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });

    }

    function returnStatusBadge(status) {
        const map = {
            'Pending': 'warning',
            'Approved': 'primary',
            'Completed': 'success',
            'Cancelled': 'danger'
        };
        const cls = map[status] || 'default';
        return '<span class="label label-' + cls + '">' + status + '</span>';
    }

    function renderReturns(rows) {

        let html = '';

        if (rows.length == 0) {

            html = `
        <tr>
            <td colspan="9" class="text-center">
                No Purchase Returns Found
            </td>
        </tr>`;

        } else {

            rows.forEach(function(item, index) {

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.return_no}</td>
                <td>${item.invoice_no??''}</td>
                <td>${item.company_name??''}</td>
                <td>${item.return_date??''}</td>
                <td>${item.reason??''}</td>
                <td>${parseFloat(item.grand_total).toFixed(2)}</td>
                <td>${returnStatusBadge(item.status)}</td>
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
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `
        <button
            class="${i==page?'btn-primary':'btn-default'}"
            onclick="searchform(${i})">
            ${i}
        </button>`;
        }
        $('#paginationArea').html(html);
    }

    function calcReturnGrandTotal() {
        let subtotal = parseFloat($('#swal_subtotal').val()) || 0;
        let tax = parseFloat($('#swal_tax').val()) || 0;
        let grand = subtotal + tax;
        $('#swal_grand_total').val(grand.toFixed(2));
    }

    function openReturnModal(returnData = null) {
        const isEdit = returnData !== null;
        const id = isEdit ? returnData.id : '';
        const invoiceId = isEdit ? returnData.purchase_invoice_id : '';
        const supplierId = isEdit ? returnData.supplier_id : '';
        const returnDate = isEdit ? returnData.return_date : '';
        const reason = isEdit ? (returnData.reason ?? '') : '';
        const subtotal = isEdit ? returnData.subtotal : 0;
        const tax = isEdit ? returnData.tax_amount : 0;
        const grandTotal = isEdit ? returnData.grand_total : 0;
        const status = isEdit ? returnData.status : 'Pending';
        const remarks = isEdit ? (returnData.remarks ?? '') : '';

        let invoiceOptions = '<option value="">Select Purchase Invoice</option>';
        purchaseInvoices.forEach(function(item) {
            invoiceOptions += `<option value="${item.id}" ${item.id==invoiceId?'selected':''}>${item.invoice_no}</option>`;
        });

        let supplierOptions = '<option value="">Select Supplier</option>';
        suppliers.forEach(function(item) {
            supplierOptions += `<option value="${item.id}" ${item.id==supplierId?'selected':''}>${item.company_name}</option>`;
        });

        const statusList = ['Pending', 'Approved', 'Completed', 'Cancelled'];
        let statusOptions = '';
        statusList.forEach(function(s) {
            statusOptions += `<option value="${s}" ${s==status?'selected':''}>${s}</option>`;
        });

        Swal.fire({
            title: isEdit ? 'Update Purchase Return' : 'Add Purchase Return',
            width: '900px',
            customClass: {
                popup: 'swal-wide-popup'
            },
            didOpen: () => {
                $('#swal_invoice').chosen({
                    width: '100%',
                    search_contains: true
                });
                $('#swal_supplier').chosen({
                    width: '100%',
                    search_contains: true
                });
                $('#swal_subtotal,#swal_tax').on('input', calcReturnGrandTotal);
            },
            html: `
                <form id="returnForm">

                <input type="hidden" id="swal_id" value="${id}">

                <div class="row">
                <div class="col-md-6">
                <label>Purchase Invoice</label>
                <select id="swal_invoice" class="form-control chzn-select-modal">
                ${invoiceOptions}
                </select>
                </div>

                <div class="col-md-6">
                <label>Supplier</label>
                <select id="swal_supplier" class="form-control chzn-select-modal">
                ${supplierOptions}
                </select>
                </div>
                </div>

                <div class="row">
                <div class="col-md-6">
                <label>Return Date</label>
                <input type="date" id="swal_return_date" class="form-control" value="${returnDate}">
                </div>

                <div class="col-md-6">
                <label>Reason</label>
                <input type="text" id="swal_reason" class="form-control" value="${reason}">
                </div>
                </div>

                <div class="row">
                <div class="col-md-3">
                <label>Subtotal</label>
                <input type="number" step="0.01" id="swal_subtotal" class="form-control" value="${subtotal}">
                </div>

                <div class="col-md-3">
                <label>Tax Amount</label>
                <input type="number" step="0.01" id="swal_tax" class="form-control" value="${tax}">
                </div>

                <div class="col-md-3">
                <label>Grand Total</label>
                <input type="number" step="0.01" readonly id="swal_grand_total" class="form-control" value="${grandTotal}">
                </div>

                <div class="col-md-3">
                <label>Status</label>
                <select id="swal_status" class="form-control">
                ${statusOptions}
                </select>
                </div>
                </div>

                <div class="row">
                <div class="col-md-12">
                <label>Remarks</label>
                <input type="text" id="swal_remarks" class="form-control" value="${remarks}">
                </div>
                </div>

                </form>
                `,
            showCancelButton: true,
            confirmButtonText: isEdit ? 'Update Return' : 'Save Return',
            confirmButtonColor: '#87B87F',
            cancelButtonText: 'Cancel',

            preConfirm: () => {

                calcReturnGrandTotal();

                if ($('#swal_invoice').val() == '' || $('#swal_supplier').val() == '' || $('#swal_return_date').val() == '') {
                    Swal.showValidationMessage('Purchase Invoice, Supplier and Return Date are required');
                    return false;
                }

                return {
                    id: $('#swal_id').val(),
                    purchase_invoice_id: $('#swal_invoice').val(),
                    supplier_id: $('#swal_supplier').val(),
                    return_date: $('#swal_return_date').val(),
                    reason: $('#swal_reason').val(),
                    subtotal: $('#swal_subtotal').val(),
                    tax_amount: $('#swal_tax').val(),
                    grand_total: $('#swal_grand_total').val(),
                    status: $('#swal_status').val(),
                    remarks: $('#swal_remarks').val(),
                    flag: 'save'
                };

            }

        }).then(function(result) {

            if (result.isConfirmed) {
                saveReturn(result.value);
            }

        });

    }

    function saveReturn(formData) {

        Swal.fire({
            title: 'Processing...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');

        Object.keys(formData).forEach(function(key) {
            data.append(key, formData[key]);
        });

        fetch('index.php?r=purchase/purchasereturns', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {

                if (res.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('.ajax-module.active').trigger('click');
                    });

                } else {

                    Swal.fire('Error', res.message, 'error');

                }

            })
            .catch(() => {
                Swal.fire('Error', 'Unable to save data.', 'error');
            });

    }

    function deleteReturn(id) {

        Swal.fire({
            title: 'Are you sure?',
            text: 'Purchase Return will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete'
        }).then(function(result) {

            if (!result.isConfirmed) {
                return;
            }

            const data = new FormData();

            data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
            data.append('flag', 'delete');
            data.append('id', id);

            fetch('index.php?r=purchase/purchasereturns', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(res => {

                    if (res.success) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('.ajax-module.active').trigger('click');
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

<?php
/**
 * PURCHASE INVOICES VIEW
 * ================================================================================
 * PURPOSE: Track supplier invoices received against purchase orders
 *
 * FUNCTIONALITY:
 * - Create and link invoices to purchase orders
 * - Record invoice numbers from suppliers
 * - Set invoice and due dates for payment tracking
 * - Track payment status (Unpaid, Partial, Paid)
 * - Filter invoices by supplier, status, date range
 * - Search by invoice number
 * - Update invoice payment status
 * - Delete invoices
 * - Automatic due date calculation from payment terms
 *
 * DATA MANAGEMENT:
 * - Stores invoices in: inventory_purchase_invoices table
 * - Foreign key: references inventory_purchase_orders
 * - Records: invoice_no, purchase_order_id, supplier_id, invoice_date, due_date,
 *            subtotal, discount_amount, tax_amount, grand_total, status, remarks
 * - Status values: Unpaid, Partial, Paid
 *
 * FINANCE INTEGRATION:
 * - Purchase invoices are formal bills received from suppliers
 * - Represents Accounts Payable liability until paid
 * - Grand totals contribute to:
 *   • Accounts Payable balance (unpaid invoices)
 *   • Expense recognition in P&L
 *   • Cash flow forecasting (by due date)
 *   • Supplier aging reports
 * - Payment status tracks payment performance vs terms
 * ================================================================================
 */

use yii\helpers\Html;

$this->title = 'Purchase Invoices';

if (!isset($purchaseInvoices)) $purchaseInvoices = [];
if (!isset($purchaseOrders)) $purchaseOrders = [];
$purchaseOrdersList = $purchaseOrders ?? [];
if (!isset($suppliers)) $suppliers = [];
$suppliersList = $suppliers ?? [];

?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=purchase/purchasedashboard">Home</a>
                </li>
                <li class="active">Purchase Invoices</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openInvoiceModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Purchase Invoice
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="invoice_search" onsubmit="return false;">

                <input type="text" name="invoice_no" id="invoice_no" class="new-input" style="width:15%;" placeholder="Invoice No">

                <select name="supplier_id" id="supplier_id" class="new-input" style="width:16%;">
                    <option value="">All Suppliers</option>
                    <?php foreach ($suppliers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['company_name']) ?></option>
                    <?php } ?>
                </select>

                <select name="purchase_order_id" id="purchase_order_id" class="new-input" style="width:15%;">
                    <option value="">All PO</option>
                    <?php foreach ($purchaseOrders as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['po_number']) ?></option>
                    <?php } ?>
                </select>

                <select name="status" id="status" class="new-input" style="width:12%;">
                    <option value="">All Status</option>
                    <option value="Unpaid">Unpaid</option>
                    <option value="Partial">Partial</option>
                    <option value="Paid">Paid</option>
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
                <table class="table table-striped table-bordered table-hover" id="invoice_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Invoice Date</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Tax</th>
                            <th>Grand Total</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchaseInvoices as $key => $item) {
                            $subtotal = (float)($item['subtotal'] ?? 0);
                            $discount = (float)($item['discount_amount'] ?? 0);
                            $tax = (float)($item['tax_amount'] ?? 0);
                            $grandTotal = (float)($item['grand_total'] ?? 0);
                            $paidAmount = (float)($item['paid_amount'] ?? 0);
                            $remainingAmount = $grandTotal - $paidAmount;
                        ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= Html::encode($item['invoice_no']) ?></td>
                                <td><?= Html::encode($item['po_number']) ?></td>
                                <td><?= Html::encode($item['company_name']) ?></td>
                                <td><?= Html::encode($item['invoice_date']) ?></td>
                                <td><?= number_format($subtotal, 2) ?></td>
                                <td><?= number_format($discount, 2) ?></td>
                                <td><?= number_format($tax, 2) ?></td>
                                <td><strong><?= number_format($grandTotal, 2) ?></strong></td>
                                <td><?= number_format($paidAmount, 2) ?></td>
                                <td><?= number_format($remainingAmount, 2) ?></td>
                                <td><?= invoiceStatusBadgeServer($item['status']) ?></td>
                                <td>
                                    <button onclick='openInvoiceModal(<?= json_encode($item) ?>)' title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    |
                                    <button onclick="printPurchaseInvoice(<?= $item['id'] ?>)" title="Print PDF">
                                        <i class="fa fa-print"></i>
                                    </button>
                                    |
                                    <button onclick="deleteInvoice(<?= $item['id'] ?>)" title="Delete">
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
function invoiceStatusBadgeServer($status)
{
    $map = ['Unpaid' => 'danger', 'Partial' => 'warning', 'Paid' => 'success', 'Cancelled' => 'default'];
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

<link rel="stylesheet" href="/inventory_system/web/css/sweetalert2.min.css">
<script src="/inventory_system/web/js/sweetalert2.all.min.js"></script>

<script>
    const purchaseOrdersList = <?= json_encode($purchaseOrdersList ?? []) ?>;
    const suppliersList = <?= json_encode($suppliersList ?? []) ?>;
</script>

<script>
    searchform();

    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading Purchase Invoices...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'load');
        data.append('invoice_no', $('#invoice_no').val());
        data.append('supplier_id', $('#supplier_id').val());
        data.append('purchase_order_id', $('#purchase_order_id').val());
        data.append('status', $('#status').val());
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=purchase/purchaseinvoices', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderInvoices(res.purchaseInvoices);
                    renderPagination(res.page, res.totalPages);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load purchase invoices.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });

    }

    function invoiceStatusBadge(status) {
        const map = {
            'Unpaid': 'danger',
            'Partial': 'warning',
            'Paid': 'success',
            'Cancelled': 'default'
        };
        const cls = map[status] || 'default';
        return '<span class="label label-' + cls + '">' + status + '</span>';
    }

    function renderInvoices(rows) {

        let html = '';

        if (rows.length == 0) {

            html = `
        <tr>
            <td colspan="13" class="text-center">
                No Purchase Invoices Found
            </td>
        </tr>`;

        } else {

            rows.forEach(function(item, index) {
                const subtotal = parseFloat(item.subtotal || 0);
                const discount = parseFloat(item.discount_amount || 0);
                const tax = parseFloat(item.tax_amount || 0);
                const grandTotal = parseFloat(item.grand_total || 0);
                const paidAmount = parseFloat(item.paid_amount || 0);
                const remainingAmount = grandTotal - paidAmount;

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.invoice_no}</td>
                <td>${item.po_number??''}</td>
                <td>${item.company_name??''}</td>
                <td>${item.invoice_date??''}</td>
                <td>${subtotal.toFixed(2)}</td>
                <td>${discount.toFixed(2)}</td>
                <td>${tax.toFixed(2)}</td>
                <td><strong>${grandTotal.toFixed(2)}</strong></td>
                <td>${paidAmount.toFixed(2)}</td>
                <td>${remainingAmount.toFixed(2)}</td>
                <td>${invoiceStatusBadge(item.status)}</td>
                <td>
                    <button onclick='openInvoiceModal(${JSON.stringify(item)})' title="Edit">
                        <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button onclick="printPurchaseInvoice(${item.id})" title="Print PDF">
                        <i class="fa fa-print" style="color: #27ae60;"></i>
                    </button>
                    |
                    <button onclick="deleteInvoice(${item.id})" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;
            });

        }

        $('#invoice_table tbody').html(html);

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

    function calcInvoiceGrandTotal() {
        let subtotal = parseFloat($('#swal_subtotal').val()) || 0;
        let discount = parseFloat($('#swal_discount').val()) || 0;
        let tax = parseFloat($('#swal_tax').val()) || 0;
        let grand = subtotal - discount + tax;
        $('#swal_grand_total').val(grand.toFixed(2));
    }

    function openInvoiceModal(invoiceData = null) {
        const isEdit = invoiceData !== null;
        const id = isEdit ? invoiceData.id : '';
        const poId = isEdit ? invoiceData.purchase_order_id : '';
        const supplierId = isEdit ? invoiceData.supplier_id : '';
        const invoiceNo = isEdit ? invoiceData.invoice_no : '';
        const invoiceDate = isEdit ? invoiceData.invoice_date : '';
        const dueDate = isEdit ? invoiceData.due_date : '';
        const subtotal = isEdit ? invoiceData.subtotal : 0;
        const discount = isEdit ? invoiceData.discount_amount : 0;
        const tax = isEdit ? invoiceData.tax_amount : 0;
        const grandTotal = isEdit ? invoiceData.grand_total : 0;
        const status = isEdit ? invoiceData.status : 'Unpaid';
        const remarks = isEdit ? (invoiceData.remarks ?? '') : '';

        let poOptions = '<option value="">Select Purchase Order</option>';
        (purchaseOrdersList ?? []).forEach(function(item) {
            poOptions += `<option value="${item?.id??''}" ${(item?.id??'')==poId?'selected':''}>${item?.po_number??''}</option>`;
        });

        let supplierOptions = '<option value="">Select Supplier</option>';
        (suppliersList ?? []).forEach(function(item) {
            supplierOptions += `<option value="${item?.id??''}" ${(item?.id??'')==supplierId?'selected':''}>${item?.company_name??''}</option>`;
        });

        const statusList = ['Unpaid', 'Partial', 'Paid', 'Cancelled'];
        let statusOptions = '';
        statusList.forEach(function(s) {
            statusOptions += `<option value="${s}" ${s==status?'selected':''}>${s}</option>`;
        });

        Swal.fire({
            title: isEdit ? 'Update Purchase Invoice' : 'Add Purchase Invoice',
            width: '900px',
            customClass: {
                popup: 'swal-wide-popup'
            },
            didOpen: () => {
                $('#swal_po').chosen({
                    width: '100%',
                    search_contains: true
                });
                $('#swal_supplier').chosen({
                    width: '100%',
                    search_contains: true
                });
                $('#swal_subtotal,#swal_discount,#swal_tax').on('input', calcInvoiceGrandTotal);

                // Add real-time balance calculation for payment amount
                $('#swal_paid_amount').on('input', function() {
                    const grandTotal = parseFloat($('#swal_grand_total').val()) || 0;
                    const paidAmount = parseFloat($(this).val()) || 0;
                    const remainingBalance = Math.max(0, grandTotal - paidAmount);
                    $('#swal_remaining_balance').val(remainingBalance.toFixed(2));
                });
            },
            html: `
                <form id="invoiceForm">

                <input type="hidden" id="swal_id" value="${id}">

                <div class="row">
                <div class="col-md-6">
                <label>Purchase Order</label>
                <select id="swal_po" class="form-control chzn-select-modal" ${isEdit ? 'disabled' : ''}>
                ${poOptions}
                </select>
                </div>

                <div class="col-md-6">
                <label>Supplier</label>
                <select id="swal_supplier" class="form-control chzn-select-modal" ${isEdit ? 'disabled' : ''}>
                ${supplierOptions}
                </select>
                </div>
                </div>

                <div class="row">
                <div class="col-md-4">
                <label>Invoice No</label>
                <input type="text" id="swal_invoice_no" class="form-control" value="${invoiceNo}" ${isEdit ? 'readonly' : ''}>
                </div>

                <div class="col-md-4">
                <label>Invoice Date</label>
                <input type="date" id="swal_invoice_date" class="form-control" value="${invoiceDate}" ${isEdit ? 'readonly' : ''}>
                </div>

                <div class="col-md-4">
                <label>Due Date</label>
                <input type="date" id="swal_due_date" class="form-control" value="${dueDate}" ${isEdit ? 'readonly' : ''}>
                </div>
                </div>

                <div class="row">
                <div class="col-md-3">
                <label>Subtotal</label>
                <input type="number" step="0.01" id="swal_subtotal" class="form-control" value="${subtotal}" ${isEdit ? 'readonly' : ''}>
                </div>

                <div class="col-md-3">
                <label>Discount Amount</label>
                <input type="number" step="0.01" id="swal_discount" class="form-control" value="${discount}" ${isEdit ? 'readonly' : ''}>
                </div>

                <div class="col-md-3">
                <label>Tax Amount</label>
                <input type="number" step="0.01" id="swal_tax" class="form-control" value="${tax}" ${isEdit ? 'readonly' : ''}>
                </div>

                <div class="col-md-3">
                <label>Grand Total</label>
                <input type="number" step="0.01" readonly id="swal_grand_total" class="form-control" value="${grandTotal}">
                </div>
                </div>

                ${isEdit ? `
                <div class="row">
                <div class="col-md-6">
                <label>Paid Amount</label>
                <input type="number" step="0.01" id="swal_paid_amount" class="form-control" value="${isEdit ? (invoiceData.paid_amount || 0) : 0}" style="background: #fffacd;">
                </div>

                <div class="col-md-6">
                <label>Remaining Balance</label>
                <input type="number" step="0.01" readonly id="swal_remaining_balance" class="form-control" value="${isEdit ? (invoiceData.grand_total - (invoiceData.paid_amount || 0)) : grandTotal}" style="background: #ffe6e6;">
                </div>
                </div>
                ` : ''}

                <div class="row">
                <div class="col-md-4">
                <label>Status</label>
                <select id="swal_status" class="form-control">
                ${statusOptions}
                </select>
                </div>

                <div class="col-md-8">
                <label>Remarks</label>
                <input type="text" id="swal_remarks" class="form-control" value="${remarks}">
                </div>
                </div>

                </form>
                `,
            showCancelButton: true,
            confirmButtonText: isEdit ? 'Update Invoice' : 'Save Invoice',
            confirmButtonColor: '#87B87F',
            cancelButtonText: 'Cancel',

            preConfirm: () => {

                const id = $('#swal_id').val();

                // On edit - return Status, Remarks, and payment info if available
                if (id) {
                    const formData = {
                        id: id,
                        status: $('#swal_status').val(),
                        remarks: $('#swal_remarks').val(),
                        flag: 'save'
                    };

                    // Add payment info if payment fields exist
                    const paidAmountField = $('#swal_paid_amount');
                    if (paidAmountField.length) {
                        formData.paid_amount = parseFloat(paidAmountField.val()) || 0;
                        formData.payment_date = $('#swal_payment_date').val();
                    }

                    return formData;
                }

                // On create - validate all required fields
                calcInvoiceGrandTotal();

                if ($('#swal_po').val() == '' || $('#swal_supplier').val() == '' || $('#swal_invoice_no').val().trim() == '' || $('#swal_invoice_date').val() == '') {
                    Swal.showValidationMessage('Purchase Order, Supplier, Invoice No and Invoice Date are required');
                    return false;
                }

                return {
                    id: id,
                    purchase_order_id: $('#swal_po').val(),
                    supplier_id: $('#swal_supplier').val(),
                    invoice_no: $('#swal_invoice_no').val(),
                    invoice_date: $('#swal_invoice_date').val(),
                    due_date: $('#swal_due_date').val(),
                    subtotal: $('#swal_subtotal').val(),
                    discount_amount: $('#swal_discount').val(),
                    tax_amount: $('#swal_tax').val(),
                    grand_total: $('#swal_grand_total').val(),
                    status: $('#swal_status').val(),
                    remarks: $('#swal_remarks').val(),
                    flag: 'save'
                };

            }

        }).then(function(result) {

            if (result.isConfirmed) {
                saveInvoice(result.value);
            }

        });

    }

    function saveInvoice(formData) {

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

        fetch('index.php?r=purchase/purchaseinvoices', {
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

    function printPurchaseInvoice(id) {
        const url = 'index.php?r=documents/purchaseinvoice&id=' + id;
        window.open(url, '_blank');
    }

    function deleteInvoice(id) {

        Swal.fire({
            title: 'Are you sure?',
            text: 'Purchase Invoice will be deleted.',
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

            fetch('index.php?r=purchase/purchaseinvoices', {
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
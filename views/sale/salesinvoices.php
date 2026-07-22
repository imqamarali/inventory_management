<?php
/**
 * SALES INVOICES VIEW
 * ================================================================================
 * PURPOSE: Manage sales invoices created from sales orders with payment tracking
 *
 * FUNCTIONALITY:
 * - Create invoices from confirmed sales orders
 * - Link invoices to sales orders
 * - Track invoice status (Unpaid, Partial, Paid)
 * - Set invoice and due dates
 * - Filter by customer, status, date range
 * - Search by invoice number
 * - Update invoice payment status
 *
 * DATA MANAGEMENT:
 * - Stores invoices in: inventory_sales_invoices table
 * - Foreign key: references inventory_sales_orders (sales_order_id)
 * - Records: invoice_no, sales_order_id, customer_id, invoice_date, due_date,
 *            subtotal, discount, tax, grand_total, status
 * - Status values: Unpaid, Partial, Paid
 *
 * FINANCE INTEGRATION:
 * - Invoices represent formal billing to customers
 * - Grand totals feed into Finance module for:
 *   • Accounts Receivable (unpaid invoices)
 *   • Revenue recognition (invoice date vs order date)
 *   • Customer aging analysis
 *   • Cash flow forecasting
 * - Payment status determines when revenue is recorded (accrual basis)
 * ================================================================================
 */

use yii\helpers\Html;

if (!isset($salesInvoices)) $salesInvoices = [];
if (!isset($salesOrders)) $salesOrders = [];
if (!isset($customers)) $customers = [];

?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=sale/salesdashboard">Home</a>
                </li>
                <li class="active">Sales Invoices</li>
                <!-- Add Sales Invoice button hidden - invoices auto-created from Sales Orders only -->
                <!-- <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openInvoiceModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Sales Invoice
                            </a>
                        </div>
                    </div>
                </li> -->
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="invoice_search" onsubmit="return false;">

                <input type="text" name="invoice_no" id="invoice_no" class="new-input" style="width:15%;" placeholder="Invoice No">

                <select name="customer_id" id="customer_id" class="new-input" style="width:16%;">
                    <option value="">All Customers</option>
                    <?php foreach ($customers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['company_name'] ?: trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))) ?></option>
                    <?php } ?>
                </select>

                <select name="sales_order_id" id="sales_order_id" class="new-input" style="width:15%;">
                    <option value="">All Orders</option>
                    <?php foreach ($salesOrders as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['order_number']) ?></option>
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
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Invoice Date</th> 
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Tax</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Invoice Status</th> 
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($salesInvoices as $key => $item) { ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= Html::encode($item['invoice_no']) ?></td>
                                <td><?= Html::encode($item['order_number']) ?></td>
                                <td><?= Html::encode($item['company_name'] ?: trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''))) ?></td>
                                <td><?= Html::encode($item['invoice_date']) ?></td> 
                                <td><?= number_format($item['subtotal'] ?? 0, 2) ?></td>
                                <td><?= number_format($item['discount'] ?? 0, 2) ?></td>
                                <td><?= number_format($item['tax'] ?? 0, 2) ?></td>
                                <td><strong><?= number_format($item['grand_total'] ?? 0, 2) ?></strong></td>
                                <td><?= number_format($item['paid_amount'] ?? 0, 2) ?></td>
                                <td><?= number_format(($item['grand_total'] ?? 0) - ($item['paid_amount'] ?? 0), 2) ?></td>
                                <td><?= invoiceStatusBadgeServer($item['status']) ?></td> 
                                <td>
                                    <button onclick='openInvoiceModal(<?= json_encode($item) ?>)'>
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    |
                                    <button>
                                        <a href="index.php?r=documents/salesinvoice&id=<?= $item['id'] ?>" target="_blank">
                                            <i class="fa fa-print" style="color: #27ae60;"></i>
                                        </a>
                                    </button>
                                    |
                                    <button onclick="deleteInvoice(<?= $item['id'] ?>)">
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
    $map = ['Draft' => 'warning', 'Issued' => 'info', 'Paid' => 'success', 'Partially Paid' => 'warning', 'Cancelled' => 'default'];
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
    const salesOrdersList = <?= json_encode($salesOrders ?? []) ?>;
    const customersList = <?= json_encode($customers ?? []) ?>;

    function customerName(item) {
        return item.company_name || ((item.first_name || '') + ' ' + (item.last_name || ''));
    }
</script>

<script>
    searchform();

    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading Sales Invoices...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'load');
        data.append('invoice_no', $('#invoice_no').val());
        data.append('customer_id', $('#customer_id').val());
        data.append('sales_order_id', $('#sales_order_id').val());
        data.append('status', $('#status').val());
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=sale/salesinvoices', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderInvoices(res.salesInvoices);
                    renderPagination(res.page, res.totalPages);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load sales invoices.', 'error');
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
            'Draft': 'warning',
            'Issued': 'info',
            'Paid': 'success',
            'Partially Paid': 'warning',
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
            <td colspan="14" class="text-center">
                No Sales Invoices Found
            </td>
        </tr>`;

        } else {

            rows.forEach(function(item, index) {
                const subtotal = parseFloat(item.subtotal) || 0;
                const discount = parseFloat(item.discount) || 0;
                const tax = parseFloat(item.tax) || 0;
                const grandTotal = parseFloat(item.grand_total) || 0;
                const paidAmount = parseFloat(item.paid_amount) || 0;
                const remainingAmount = grandTotal - paidAmount;

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.invoice_no}</td>
                <td>${item.order_number??''}</td>
                <td>${customerName(item)}</td>
                <td>${item.invoice_date??''}</td> 
                <td>${subtotal.toFixed(2)}</td>
                <td>${discount.toFixed(2)}</td>
                <td>${tax.toFixed(2)}</td>
                <td><strong>${grandTotal.toFixed(2)}</strong></td>
                <td>${paidAmount.toFixed(2)}</td>
                <td>${remainingAmount.toFixed(2)}</td>
                <td>${invoiceStatusBadge(item.status)}</td> 
                <td>
                    <button onclick='openInvoiceModal(${JSON.stringify(item)})'>
                        <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button>
                        <a href="index.php?r=documents/salesinvoice&id=${item.id}" target="_blank" >
                             <i class="fa fa-print" style="color: #27ae60;"></i>
                        </a>
                    </button>
                    |
                    <button onclick="deleteInvoice(${item.id})">
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
        let subtotal = parseFloat($('#si_subtotal').val()) || 0;
        let discount = parseFloat($('#si_discount').val()) || 0;
        let tax = parseFloat($('#si_tax').val()) || 0;
        let grand = subtotal - discount + tax;
        $('#si_grand_total').val(grand.toFixed(2));
    }

    function openInvoiceModal(invoiceData = null) {
        const isEdit = invoiceData !== null && invoiceData.id;
        const id = isEdit ? invoiceData.id : '';
        const orderId = isEdit ? invoiceData.sales_order_id : '';
        const customerId = isEdit ? invoiceData.customer_id : '';
        const invoiceDate = isEdit ? (invoiceData.invoice_date || '') : '';
        const dueDate = isEdit ? (invoiceData.due_date || '') : '';
        const subtotal = parseFloat(isEdit ? (invoiceData.subtotal || 0) : 0);
        const discount = parseFloat(isEdit ? (invoiceData.discount || 0) : 0);
        const tax = parseFloat(isEdit ? (invoiceData.tax || 0) : 0);
        const grandTotal = parseFloat(isEdit ? (invoiceData.grand_total || 0) : 0);
        const paidAmount = parseFloat(isEdit ? (invoiceData.paid_amount || 0) : 0);
        const remainingBalance = parseFloat(isEdit ? (invoiceData.remaining_balance || 0) : (grandTotal - paidAmount));
        const invoiceStatus = isEdit ? (invoiceData.status || 'Draft') : 'Draft';
        const orderStatus = isEdit ? (invoiceData.order_status || 'N/A') : 'N/A';
        const paymentStatus = isEdit ? (invoiceData.payment_status || 'N/A') : 'N/A';
        const notes = isEdit ? (invoiceData.notes || '') : '';

        // Prevent editing if invoice is paid
        if (isEdit && invoiceStatus === 'Paid') {
            Swal.fire({
                icon: 'warning',
                title: 'Cannot Edit',
                text: 'This Sales Invoice is Paid and cannot be edited.',
                confirmButtonColor: '#87B87F'
            });
            return;
        }

        let orderOptions = '<option value="">Select Sales Order</option>';
        (salesOrdersList ?? []).forEach(function(item) {
            orderOptions += `<option value="${item?.id??''}" ${(item?.id??'')==orderId?'selected':''}>${item?.order_number??''}</option>`;
        });

        let customerOptions = '<option value="">Select Customer</option>';
        (customersList ?? []).forEach(function(item) {
            customerOptions += `<option value="${item?.id??''}" ${(item?.id??'')==customerId?'selected':''}>${customerName(item)}</option>`;
        });

        const statusList = ['Draft', 'Issued', 'Paid', 'Partially Paid', 'Cancelled'];
        let statusOptions = '';
        statusList.forEach(function(s) {
            statusOptions += `<option value="${s}" ${s==invoiceStatus?'selected':''}>${s}</option>`;
        });

        Swal.fire({
            title: isEdit ? 'Update Sales Invoice' : 'Add Sales Invoice',
            width: '900px',
            customClass: {
                popup: 'swal-wide-popup'
            },
            didOpen: () => {
                $('#si_order').chosen({
                    width: '100%',
                    search_contains: true
                });
                $('#si_customer').chosen({
                    width: '100%',
                    search_contains: true
                });
                // Calculate remaining balance when paid amount changes (UPDATE ONLY)
                $('#si_paid_amount').on('input', function() {
                    const grand = parseFloat($('#si_grand_total').val()) || 0;
                    const paid = parseFloat($(this).val()) || 0;

                    // Validate paid amount - cannot exceed grand total
                    if (paid > grand) {
                        $(this).val(grand.toFixed(2));
                        const remaining = 0;
                        $('#si_remaining_balance').val(remaining.toFixed(2));
                        return;
                    }

                    // Strict check: Only update remaining balance, no other fields
                    const remaining = Math.max(0, grand - paid);
                    $('#si_remaining_balance').val(remaining.toFixed(2));
                });
                // Load invoice items if editing
                if (isEdit  && invoiceData.id) {
                    loadInvoiceItems(invoiceData.id);
                }
            },
            html: `
                <form id="si_invoiceForm">

                <input type="hidden" id="si_id" value="${id}">

                <div class="row">
                <div class="col-md-6">
                <label>Sales Order</label>
                <select id="si_order" class="form-control chzn-select-modal" ${isEdit ? 'disabled' : ''} style="${isEdit ? 'background:#f5f5f5; cursor: not-allowed;' : ''}">
                ${orderOptions}
                </select>
                </div>

                <div class="col-md-6">
                <label>Customer</label>
                <select id="si_customer" class="form-control chzn-select-modal" ${isEdit ? 'disabled' : ''} style="${isEdit ? 'background:#f5f5f5; cursor: not-allowed;' : ''}">
                ${customerOptions}
                </select>
                </div>
                </div>

                <div class="row">
                <div class="col-md-6">
                <label>Invoice Date</label>
                <input type="date" id="si_invoice_date" class="form-control" value="${invoiceDate}" ${isEdit ? 'readonly' : ''} style="${isEdit ? 'background:#f5f5f5; cursor: not-allowed;' : ''}">
                </div>

                <div class="col-md-6">
                <label>Due Date</label>
                <input type="date" id="si_due_date" class="form-control" value="${dueDate}" ${isEdit ? 'readonly' : ''} style="${isEdit ? 'background:#f5f5f5; cursor: not-allowed;' : ''}">
                </div>
                </div>

                <div class="row">
                <div class="col-md-3">
                <label>Subtotal</label>
                <input type="number" step="0.01" id="si_subtotal" class="form-control" readonly value="${subtotal.toFixed(2)}" style="background:#f5f5f5;">
                </div>

                <div class="col-md-3">
                <label>Discount</label>
                <input type="number" step="0.01" id="si_discount" class="form-control" readonly value="${discount.toFixed(2)}" style="background:#f5f5f5;">
                </div>

                <div class="col-md-3">
                <label>Tax</label>
                <input type="number" step="0.01" id="si_tax" class="form-control" readonly value="${tax.toFixed(2)}" style="background:#f5f5f5;">
                </div>

                <div class="col-md-3">
                <label><strong>Grand Total</strong></label>
                <input type="number" step="0.01" readonly id="si_grand_total" class="form-control" value="${grandTotal.toFixed(2)}" style="background:#fff3cd; font-weight:bold;">
                </div>
                </div>

                <div class="row">
                <div class="col-md-3">
                <label><strong>Paid Amount (Editable on Update)</strong></label>
                <input type="number" step="0.01" id="si_paid_amount" class="form-control" value="${paidAmount.toFixed(2)}" style="${isEdit ? 'background:#fff3cd;' : ''}" ${isEdit ? '' : 'readonly'}>
                </div>

                <div class="col-md-3">
                <label><strong>Remaining Balance</strong></label>
                <input type="number" step="0.01" readonly id="si_remaining_balance" class="form-control" value="${remainingBalance.toFixed(2)}" style="background:#f5f5f5; font-weight:bold; color:#d9534f;">
                </div>

                <div class="col-md-3">
                <label>Order Status</label>
                <input type="text" readonly id="si_order_status" class="form-control" value="${orderStatus}" style="background:#f5f5f5;">
                </div>

                <div class="col-md-3">
                <label>Payment Status</label>
                <input type="text" readonly id="si_payment_status" class="form-control" value="${paymentStatus}" style="background:#f5f5f5;">
                </div>
                </div>

                <div class="row" style="margin-top:10px;">
                <div class="col-md-4">
                <label>Invoice Status</label>
                <select id="si_status" class="form-control">
                ${statusOptions}
                </select>
                </div>

                <div class="col-md-8">
                <label>Remarks/Notes</label>
                <input type="text" id="si_notes" class="form-control" value="${notes}" placeholder="Add remarks or notes">
                </div>
                </div>

                <div class="row" style="margin-top:15px;">
                <div class="col-md-12">
                <label><strong>Products in this Invoice</strong></label>
                <div id="si_invoice_items_container" class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th width="80px">Qty</th>
                                <th width="100px">Rate</th>
                                <th width="100px">Discount</th>
                                <th width="100px">Tax</th>
                                <th width="100px">Total</th>
                            </tr>
                        </thead>
                        <tbody id="si_invoice_items_body">
                            <tr><td colspan="6" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                </div>
                </div>

                </form>
                `,
            showCancelButton: true,
            confirmButtonText: isEdit ? 'Update Invoice' : 'Save Invoice',
            confirmButtonColor: '#87B87F',
            cancelButtonText: 'Cancel',

            preConfirm: () => {

                calcInvoiceGrandTotal();

                const recordId = $('#si_id').val();
                const isUpdate = recordId !== '' && recordId !== '0';

                if (!isUpdate && ($('#si_order').val() == '' || $('#si_customer').val() == '' || $('#si_invoice_date').val() == '')) {
                    Swal.showValidationMessage('Sales Order, Customer and Invoice Date are required');
                    return false;
                }

                // STRICT CHECK: For UPDATE: Only send payment-related fields (paid_amount and notes)
                // Do NOT send other amount-related fields to prevent accidental changes
                if (isUpdate) {
                    return {
                        id: recordId,
                        paid_amount: parseFloat($('#si_paid_amount').val()) || 0,
                        notes: $('#si_notes').val(),
                        flag: 'save'
                    };
                }

                // For CREATE: Send all fields
                return {
                    id: recordId,
                    sales_order_id: $('#si_order').val(),
                    customer_id: $('#si_customer').val(),
                    invoice_date: $('#si_invoice_date').val(),
                    due_date: $('#si_due_date').val(),
                    subtotal: $('#si_subtotal').val(),
                    discount: $('#si_discount').val(),
                    tax: $('#si_tax').val(),
                    grand_total: $('#si_grand_total').val(),
                    paid_amount: parseFloat($('#si_paid_amount').val()) || 0,
                    remaining_balance: $('#si_remaining_balance').val(),
                    status: $('#si_status').val(),
                    notes: $('#si_notes').val(),
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

        fetch('index.php?r=sale/salesinvoices', {
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
                        searchform();
                    });

                } else {

                    Swal.fire('Error', res.message, 'error');

                }

            })
            .catch(() => {
                Swal.fire('Error', 'Unable to save data.', 'error');
            });

    }

    function deleteInvoice(id) {

        Swal.fire({
            title: 'Are you sure?',
            text: 'Sales Invoice will be deleted.',
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

            fetch('index.php?r=sale/salesinvoices', {
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
                            searchform();
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

    function loadInvoiceItems(invoiceId) {
        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'get_items');
        data.append('id', invoiceId);

        fetch('index.php?r=sale/salesinvoices', {
            method: 'POST',
            body: data
        })
            .then(res => res.json())
            .then(res => {
                if (res.success && res.items) {
                    let html = '';
                    res.items.forEach(item => {
                        html += `
                            <tr>
                                <td>${item.product_name || 'N/A'}</td>
                                <td>${parseFloat(item.quantity || 0).toFixed(2)}</td>
                                <td>${parseFloat(item.unit_price || 0).toFixed(2)}</td>
                                <td>${parseFloat(item.discount || 0).toFixed(2)}</td>
                                <td>${parseFloat(item.tax || 0).toFixed(2)}</td>
                                <td>${parseFloat(item.total || 0).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                    $('#si_invoice_items_body').html(html);
                } else {
                    $('#si_invoice_items_body').html('<tr><td colspan="6" class="text-center">No items found</td></tr>');
                }
            })
            .catch(e => {
                console.error(e);
                $('#si_invoice_items_body').html('<tr><td colspan="6" class="text-center">Error loading items</td></tr>');
            });
    }
</script>
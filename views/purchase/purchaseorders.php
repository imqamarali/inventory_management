<?php
/**
 * PURCHASE ORDERS VIEW
 * ================================================================================
 * PURPOSE: View, manage, and track all purchase orders from suppliers
 *
 * FUNCTIONALITY:
 * - List all purchase orders with supplier and warehouse details
 * - Filter by supplier, warehouse, status, date range
 * - Search by purchase order number
 * - Create new purchase orders
 * - Update order details and items
 * - Change order status (Pending, Approved, Completed, Cancelled)
 * - View order items with quantities and pricing
 * - Delete orders (soft delete)
 * - Pagination for large datasets
 *
 * DATA MANAGEMENT:
 * - Source table: inventory_purchase_orders
 * - Displays: PO Number, Supplier, Warehouse, Order Date, Expected Date,
 *             Status, Grand Total, Payment Terms
 * - Status values: Pending, Approved, Completed, Cancelled
 * - Tracks: subtotal, discount, tax, freight (shipping), grand_total
 *
 * FINANCE INTEGRATION:
 * - Grand totals form the basis of:
 *   • Cost of Goods Sold (COGS) in Profit & Loss
 *   • Accounts Payable aging analysis
 *   • Supplier payables tracking in Balance Sheet
 *   • Monthly purchase expense trends
 * - Status progression helps determine when expense is recognized
 * - Payment terms enable supplier creditor analysis
 * ================================================================================
 */

use yii\helpers\Html;

$this->title = 'Purchase Orders';

if (!isset($purchaseOrders)) $purchaseOrders = [];
if (!isset($suppliers)) $suppliers = [];
if (!isset($warehouses)) $warehouses = [];
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=purchase/purchasedashboard">Home</a>
                </li>
                <li class="active">Purchase Orders</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="loadOrder()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Purchase Order
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="order_search" onsubmit="return false;">

                <input type="text" name="po_number" id="po_number" class="new-input" style="width:15%;" placeholder="PO Number">

                <select name="supplier_id" id="supplier_id" class="new-input" style="width:15%;">
                    <option value="">All Suppliers</option>
                    <?php foreach ($suppliers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['company_name']) ?></option>
                    <?php } ?>
                </select>

                <select name="warehouse_id" id="warehouse_id" class="new-input" style="width:15%;">
                    <option value="">All Warehouses</option>
                    <?php foreach ($warehouses as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['warehouse_name']) ?></option>
                    <?php } ?>
                </select>

                <select name="status" id="status" class="new-input" style="width:14%;">
                    <option value="">All Status</option>
                    <option value="Draft">Draft</option>
                    <option value="Approved">Approved</option>
                    <option value="Partially Received">Partially Received</option>
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
                <table class="table table-striped table-bordered table-hover" id="order_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Order Date</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Tax</th>
                            <th>Grand Total</th>
                            <th>GRN Number</th>
                            <th>Invoice No</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchaseOrders as $key => $item) {
                            $subtotal = (float)($item['subtotal'] ?? 0);
                            $discount = (float)($item['discount'] ?? 0);
                            $tax = (float)($item['tax'] ?? 0);
                            $grandTotal = (float)($item['grand_total'] ?? 0);
                        ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= Html::encode($item['po_number']) ?></td>
                                <td><?= Html::encode($item['company_name']) ?></td>
                                <td><?= Html::encode($item['order_date']) ?></td>
                                <td><?= number_format($subtotal, 2) ?></td>
                                <td><?= number_format($discount, 2) ?></td>
                                <td><?= number_format($tax, 2) ?></td>
                                <td><strong><?= number_format($grandTotal, 2) ?></strong></td>
                                <td><?= Html::encode($item['grn_number'] ?? '-') ?></td>
                                <td><?= Html::encode($item['invoice_no'] ?? '-') ?></td>
                                <td><?= statusBadgeServer($item['status']) ?></td>
                                <td>
                                    <button onclick="loadOrder(<?= $item['id'] ?>)">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    |
                                    <button onclick="deleteOrder(<?= $item['id'] ?>)">
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
function statusBadgeServer($status)
{
    $map = [
        'Draft' => 'default',
        'Approved' => 'primary',
        'Partially Received' => 'warning',
        'Completed' => 'success',
        'Cancelled' => 'danger'
    ];
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
    window.suppliers = window.suppliers || <?= json_encode($suppliers) ?>;
    window.warehouses = window.warehouses || <?= json_encode($warehouses) ?>;
    window.purchaseProducts = window.purchaseProducts || [];
</script>

<script>
    searchform();

    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading Purchase Orders...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'load');
        data.append('po_number', $('#po_number').val());
        data.append('supplier_id', $('#supplier_id').val());
        data.append('warehouse_id', $('#warehouse_id').val());
        data.append('status', $('#status').val());
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=purchase/purchaseorders', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderOrders(res.purchaseOrders);
                    renderPagination(res.page, res.totalPages);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load purchase orders.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });

    }

    function statusBadge(status) {
        const map = {
            'Draft': 'default',
            'Approved': 'primary',
            'Partially Received': 'warning',
            'Completed': 'success',
            'Cancelled': 'danger'
        };
        const cls = map[status] || 'default';
        return '<span class="label label-' + cls + '">' + status + '</span>';
    }

    function renderOrders(rows) {

        let html = '';

        if (rows.length == 0) {

            html = `
        <tr>
            <td colspan="12" class="text-center">
                No Purchase Orders Found
            </td>
        </tr>`;

        } else {

            rows.forEach(function(item, index) {
                const subtotal = parseFloat(item.subtotal || 0);
                const discount = parseFloat(item.discount || 0);
                const tax = parseFloat(item.tax || 0);
                const grandTotal = parseFloat(item.grand_total || 0);

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.po_number}</td>
                <td>${item.company_name??''}</td>
                <td>${item.order_date??''}</td>
                <td>${subtotal.toFixed(2)}</td>
                <td>${discount.toFixed(2)}</td>
                <td>${tax.toFixed(2)}</td>
                <td><strong>${grandTotal.toFixed(2)}</strong></td>
                <td>${item.grn_number??'-'}</td>
                <td>${item.invoice_no??'-'}</td>
                <td>${statusBadge(item.status)}</td>
                <td>
                    <button onclick="loadOrder(${item.id})" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button onclick="showStatusOptions(${item.id}, '${item.status}')" title="Update Status">
                        <i class="fa fa-exchange"></i>
                    </button>
                    |
                    <button onclick="printPurchaseOrder(${item.id})" title="Print PDF">
                        <i class="fa fa-print"></i>
                    </button>
                    |
                    <button onclick="deleteOrder(${item.id})" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;
            });

        }

        $('#order_table tbody').html(html);

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

    function showStatusOptions(id, currentStatus) {
        const statuses = ['Pending', 'Approved', 'Completed', 'Cancelled'];

        // .filter(s => s !== currentStatus)
        const options = statuses
            .map(s => `<option value="${s}">${s}</option>`)
            .join('');

        Swal.fire({
            title: 'Update Purchase Order Status',
            html: `
                <div style="text-align: left;">
                    <label>Current Status: <strong>${currentStatus}</strong></label><br><br>
                    <label style="color: #0066cc; font-size: 13px;"><i class="fa fa-info-circle"></i> (On setting Status to Completed, it will automatically update stock quantities as well)</label><br><br>
                    <label>New Status:</label>
                    <select id="newStatus" class="form-control" style="margin-top: 10px;">
                        <option value="">-- Select Status --</option>
                        ${options}
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Update Status',
            confirmButtonColor: '#87B87F',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const status = $('#newStatus').val();
                if (!status) {
                    Swal.showValidationMessage('Please select a status');
                    return false;
                }
                return status;
            }
        }).then(r => {
            if (r.isConfirmed) {
                updatePOStatus(id, r.value);
            }
        });
    }

    function updatePOStatus(id, status) {
        Swal.fire({
            title: 'Updating Status...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const fd = new FormData();
        fd.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        fd.append('flag', 'updateStatus');
        fd.append('id', id);
        fd.append('status', status);

        fetch('index.php?r=purchase/purchaseorders', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
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
                Swal.fire('Error', res.message || 'Failed to update status', 'error');
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Error', 'Unable to communicate with server', 'error');
        });
    }

    function printPurchaseOrder(id) {
        const url = 'index.php?r=documents/purchaseorder&id=' + id;
        window.open(url, '_blank');
    }

    function deleteOrder(id) {

        Swal.fire({
            title: 'Are you sure?',
            text: 'Purchase Order will be deleted.',
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

            fetch('index.php?r=purchase/purchaseorders', {
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

    loadPurchaseProducts();

    function loadPurchaseProducts(callback = null) {

        const fd = new FormData();

        fd.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        fd.append('flag', 'loadProducts');

        fetch('index.php?r=purchase/purchaseorders', {

                method: 'POST',
                body: fd

            })
            .then(r => r.json())
            .then(r => {

                if (!r.success) return;

                purchaseProducts = r.products || [];

                if (callback) callback();

            })
            .catch(console.error);

    }


    function getProductOptions(selected = '') {

        let html = '<option value="">Select Product</option>';

        purchaseProducts.forEach(p => {

            html += `<option value="${p.id}" data-price="${p.purchase_price}" ${selected==p.id?'selected':''}>${p.product_name}</option>`;

        });

        return html;

    }

    function supplierOptions(selected = '') {

        let html = '<option value="">Select Supplier</option>';

        suppliers.forEach(s => {

            html += `<option value="${s.id}" ${selected==s.id?'selected':''}>${s.company_name}</option>`;

        });

        return html;

    }

    function warehouseOptions(selected = '') {

        let html = '';

        warehouses.forEach(w => {

            html += `<option value="${w.id}" ${selected==w.id?'selected':''}>${w.warehouse_name}</option>`;

        });

        return html;

    }

    function statusOptions(selected = 'Draft') {

        let arr = [
            'Draft',
            'Approved',
            'Partially Received',
            'Completed',
            'Cancelled'
        ];

        let html = '';

        arr.forEach(s => {

            html += `<option value="${s}" ${selected==s?'selected':''}>${s}</option>`;

        });

        return html;

    }

    function openOrderModal(order = null) {

        if (order) {

            loadOrder(order.id);

            return;

        }

        showOrderModal();

    }

    function showOrderModal(data = {}) {

        const isEdit = !!data.id;

        Swal.fire({

            title: isEdit ? 'Update Purchase Order' : 'Add Purchase Order',

            width: '1100px',

            customClass: {
                popup: 'swal-wide-popup'
            },

            html: getOrderModalHtml(data),

            showCancelButton: true,

            confirmButtonText: isEdit ? 'Update Order' : 'Save Order',

            confirmButtonColor: '#87B87F',

            cancelButtonText: 'Cancel',

            didOpen: function() {

                $('#swal_supplier').chosen({
                    width: '100%'
                });
                $('#swal_warehouse').chosen({
                    width: '100%'
                });
                $('#swal_status').chosen({
                    width: '100%'
                });

                $('#swal_discount,#swal_tax,#swal_freight').on('input', updateGrandTotal);

                if (isEdit) {

                    (data.items || []).forEach(i => addPurchaseOrderRow(i));

                } else {

                    addPurchaseOrderRow();

                }

            },

            preConfirm: () => {

                let items = validatePurchaseOrder();

                if (items === false) return false;

                return {

                    id: $('#swal_id').val(),
                    supplier_id: $('#swal_supplier').val(),
                    warehouse_id: $('#swal_warehouse').val(),
                    order_date: $('#swal_order_date').val(),
                    expected_date: $('#swal_expected_date').val(),
                    payment_terms: $('#swal_payment_terms').val(),
                    status: $('#swal_status').val(),
                    subtotal: $('#swal_subtotal').val(),
                    discount_amount: $('#swal_discount').val(),
                    tax_amount: $('#swal_tax').val(),
                    shipping_amount: $('#swal_freight').val(),
                    grand_total: $('#swal_grand_total').val(),
                    remarks: $('#swal_remarks').val(),
                    items: JSON.stringify(items),
                    flag: 'save'

                };

            }

        }).then(r => {

            if (r.isConfirmed) saveOrder(r.value);

        });

    }

    function getOrderModalHtml(d = {}) {

        return `

        <form id="orderForm">

        <input type="hidden" id="swal_id" value="${d.id||''}">

        <div class="row">

        <div class="col-md-4">

        <label>Supplier</label>

        <select id="swal_supplier" class="form-control">

        ${supplierOptions(d.supplier_id)}

        </select>

        </div>

        <div class="col-md-4">

        <label>Warehouse</label>

        <select id="swal_warehouse" class="form-control">

        ${warehouseOptions(d.warehouse_id)}

        </select>

        </div>

        <div class="col-md-4">

        <label>Status</label>

        <select id="swal_status" class="form-control">

        ${statusOptions(d.status)}

        </select>

        </div>

        </div>

        <div class="row">

        <div class="col-md-4">

        <label>Order Date</label>

        <input type="date" id="swal_order_date" class="form-control" value="${d.order_date||''}">

        </div>

        <div class="col-md-4">

        <label>Expected Date</label>

        <input type="date" id="swal_expected_date" class="form-control" value="${d.expected_date||''}">

        </div>

        <div class="col-md-4">

        <label>Payment Terms</label>

        <input type="text" id="swal_payment_terms" class="form-control" value="${d.payment_terms||''}">

        </div>

        </div>

        <div class="row">

        <div class="col-md-3">

        <label>Subtotal</label>

        <input type="number" id="swal_subtotal" class="form-control" readonly value="${d.subtotal||0}">

        </div>

        <div class="col-md-3">

        <label>Discount</label>

        <input type="number" id="swal_discount" class="form-control" value="${d.discount||0}">

        </div>

        <div class="col-md-3">

        <label>Tax</label>

        <input type="number" id="swal_tax" class="form-control" value="${d.tax||0}">

        </div>

        <div class="col-md-3">

        <label>Freight</label>

        <input type="number" id="swal_freight" class="form-control" value="${d.freight||0}">

        </div>

        </div>

        <div class="row">

        <div class="col-md-4">

        <label>Grand Total</label>

        <input type="number" id="swal_grand_total" class="form-control" readonly value="${d.grand_total||0}">

        </div>

        <div class="col-md-6">

        <label>Remarks</label>

        <input type="text" id="swal_remarks" class="form-control" value="${d.notes||''}">

        </div>

        <div class="col-md-2" style="margin-top: 28px;">

        <label>&nbsp;</label>

        <button type="button" id="btnAddItem" style="padding:5px">

        <i class="fa fa-plus"></i>

        Add Item

        </button>

        </div>

        </div>

        <hr>

        <table class="table table-bordered table-striped" id="purchaseItemTable">

        <thead>

        <tr>

        <th width="28%">Product</th>

        <th width="8%">Qty</th>

        <th width="10%">Rate</th>

        <th width="10%">Discount</th>

        <th width="10%">Tax</th>

        <th width="12%">Total</th>

        <th>Remarks</th>

        <th width="5%"></th>

        </tr>

        </thead>

        <tbody></tbody>

        </table>

        </form>

        `;

    }

    function addPurchaseOrderRow(item = {}) {

        $('#purchaseItemTable tbody').append(`
            <tr>
            <td><select class="form-control item-product">${getProductOptions(item.product_id||'')}</select></td>
            <td><input type="number" class="form-control item-qty" value="${item.quantity||1}"></td>
            <td><input type="number" class="form-control item-rate" value="${item.unit_price||0}"></td>
            <td><input type="number" class="form-control item-discount" value="${item.discount_amount||0}"></td>
            <td><input type="number" class="form-control item-tax" value="${item.tax_amount||0}"></td>
            <td><input type="number" class="form-control item-total" readonly value="${item.line_total||0}"></td>
            <td><input type="text" class="form-control item-remarks" value="${item.remarks||''}"></td>
            <td><button type="button" class="remove-item"><i class="fa fa-trash"></i></button></td>
            </tr>`);

        let tr = $('#purchaseItemTable tbody tr:last');

        tr.find('.item-product').chosen({
            width: '100%',
            search_contains: true
        });

        calculateRow(tr);

    }
    $(document).on('click', '#btnAddItem', function() {

        addPurchaseOrderRow();

    });

    $(document).on('change', '.item-product', function() {

        let tr = $(this).closest('tr');

        let price = parseFloat($(this).find(':selected').data('price')) || 0;

        tr.find('.item-rate').val(price.toFixed(2));

        calculateRow(tr);

    });
    $(document).on('input', '.item-qty,.item-rate,.item-discount,.item-tax', function() {

        calculateRow($(this).closest('tr'));

    });
    $(document).on('click', '.remove-item', function() {

        $(this).closest('tr').remove();

        calculateTotals();

    });

    function calculateRow(tr) {

        let qty = parseFloat(tr.find('.item-qty').val()) || 0;

        let rate = parseFloat(tr.find('.item-rate').val()) || 0;

        let discount = parseFloat(tr.find('.item-discount').val()) || 0;

        let tax = parseFloat(tr.find('.item-tax').val()) || 0;

        let total = (qty * rate) - discount + tax;

        tr.find('.item-total').val(total.toFixed(2));

        calculateTotals();

    }

    function calculateTotals() {

        let subtotal = 0;
        let totalDiscount = 0;
        let totalTax = 0;

        $('#purchaseItemTable tbody tr').each(function() {

            subtotal += parseFloat($(this).find('.item-total').val()) || 0;
            totalDiscount += parseFloat($(this).find('.item-discount').val()) || 0;
            totalTax += parseFloat($(this).find('.item-tax').val()) || 0;

        });

        $('#swal_subtotal').val(subtotal.toFixed(2));
        $('#swal_discount').val(totalDiscount.toFixed(2));
        $('#swal_tax').val(totalTax.toFixed(2));

        updateGrandTotal();

    }

    function updateGrandTotal() {

        let subtotal = parseFloat($('#swal_subtotal').val()) || 0;
        let freight = parseFloat($('#swal_freight').val()) || 0;

        // Subtotal already includes line-level discounts and taxes, so just add freight
        $('#swal_grand_total').val((subtotal + freight).toFixed(2));

    }
    $(document).on('input', '#swal_discount,#swal_tax,#swal_freight', updateGrandTotal);

    function collectPurchaseItems() {

        let items = [];

        $('#purchaseItemTable tbody tr').each(function() {

            items.push({

                product_id: $(this).find('.item-product').val(),
                quantity: $(this).find('.item-qty').val(),
                unit_price: $(this).find('.item-rate').val(),
                discount_amount: $(this).find('.item-discount').val(),
                tax_amount: $(this).find('.item-tax').val(),
                line_total: $(this).find('.item-total').val(),
                remarks: $(this).find('.item-remarks').val()

            });

        });

        return items;

    }

    function validatePurchaseOrder() {

        if (!$('#swal_supplier').val()) {

            Swal.showValidationMessage('Please select supplier.');

            return false;

        }

        if (!$('#swal_warehouse').val()) {

            Swal.showValidationMessage('Please select warehouse.');

            return false;

        }

        if (!$('#swal_order_date').val()) {

            Swal.showValidationMessage('Please select order date.');

            return false;

        }

        let items = collectPurchaseItems();

        if (items.length == 0) {

            Swal.showValidationMessage('Please add at least one item.');

            return false;

        }

        let ok = true;

        items.forEach(function(r) {

            if (!r.product_id || parseFloat(r.quantity) <= 0) {

                ok = false;

            }

        });

        if (!ok) {

            Swal.showValidationMessage('Please complete all item rows.');

            return false;

        }

        return items;

    }


    function loadOrder(id) {

        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const fd = new FormData();

        fd.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        fd.append('flag', 'getOrder');
        fd.append('id', id);

        fetch('index.php?r=purchase/purchaseorders', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(r => {

                Swal.close();

                if (!r.success) {

                    Swal.fire('Error', r.message, 'error');

                    return;

                }

                r.order.items = r.items || [];

                showOrderModal(r.order);

            })
            .catch(() => {

                Swal.close();

                Swal.fire('Error', 'Unable to load purchase order.', 'error');

            });

    }



    function saveOrder(formData) {

        Swal.fire({
            title: 'Saving Purchase Order...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const fd = new FormData();

        fd.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');

        Object.keys(formData).forEach(function(key) {
            fd.append(key, formData[key]);
        });

        fetch('index.php?r=purchase/purchaseorders', {

                method: 'POST',
                body: fd

            })
            .then(r => r.json())
            .then(function(res) {

                if (!res.success) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message || 'Unable to save purchase order.'
                    });

                    return;

                }

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(function() {

                    searchform();

                });

            })
            .catch(function(error) {

                console.log(error);

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to communicate with server.'
                });

            });

    }
</script>
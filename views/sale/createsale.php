<?php
/**
 * CREATE SALE VIEW
 * ================================================================================
 * PURPOSE: Create and manage new sales orders with customer, products, and terms
 *
 * FUNCTIONALITY:
 * - Create new sales orders with line items
 * - Update existing sales orders
 * - Search and filter existing orders
 * - Manage order status (Draft, Confirmed, Dispatched, Delivered)
 * - Track payment status (Pending, Paid)
 * - Set delivery dates and customer details
 *
 * DATA MANAGEMENT:
 * - Stores orders in: inventory_sales_orders table
 * - Stores items in: inventory_sales_order_items table
 * - Manages stock: inventory_stock table (reserves/deducts stock)
 * - Records: order_number, customer_id, warehouse_id, order_date, delivery_date,
 *            order_status, payment_status, subtotal, discount, tax, shipping, grand_total
 *
 * FINANCE INTEGRATION:
 * - All sales orders' grand_total is used in:
 *   • Finance Dashboard for Total Revenue calculation
 *   • Profit & Loss statement (Revenue section)
 *   • Customer Receivables tracking
 *   • Monthly sales trends analysis
 * - Payment status tracks Accounts Receivable aging
 * - Order status progression: Draft → Confirmed → Dispatched → Delivered
 * ================================================================================
 */

use yii\helpers\Html;

if (!isset($customers)) $customers = [];
if (!isset($warehouses)) $warehouses = [];
if (!isset($products)) $products = [];
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=sale/salesdashboard">Home</a>
                </li>
                <li class="active">Create Sale</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="showSaleModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Create Sale
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="sale_search" onsubmit="return false;">

                <input type="text" name="keyword" id="keyword" class="new-input" style="width:16%;" placeholder="Order Number">

                <select name="customer_id" id="customer_id" class="new-input" style="width:18%;">
                    <option value="">All Customers</option>
                    <?php foreach ($customers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['company_name'] ?: trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))) ?></option>
                    <?php } ?>
                </select>

                <select name="warehouse_id" id="warehouse_id" class="new-input" style="width:18%;">
                    <option value="">All Warehouses</option>
                    <?php foreach ($warehouses as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['warehouse_name']) ?></option>
                    <?php } ?>
                </select>

                <input type="text" name="per_page" id="per_page" value="20" class="new-input" style="width:7%;" placeholder="Records?">

                <input type="button" class="btn btn-primary"
                    onclick="searchform()"
                    value="Search"
                    style="height:30px;padding:0;margin-top:-3px;" />

            </form>
        </div>

        <div class="widget-main">

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="sale_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Warehouse</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Tax</th>
                            <th>Grand Total</th>
                            <th>Paid Amount</th>
                            <th>Remaining Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                <div id="paginationArea" class="text-center"></div>

            </div>

        </div>

    </div>
</div>

<style>
    .swal2-popup.swal-wide-popup {
        width: 1100px !important;
        max-width: 95vw !important;
    }

    .swal2-popup.swal-wide-popup .swal2-html-container {
        max-height: none !important;
        overflow: visible !important;
    }
</style>

<script>
    // Initialize data from PHP variables
    const customers = <?= json_encode($customers) ?>;
    const warehouses = <?= json_encode($warehouses) ?>;
    const saleProducts = <?= json_encode($products) ?>;
</script>

<script>
    searchform();

    function customerName(item) {
        return item.company_name || ((item.first_name || '') + ' ' + (item.last_name || ''));
    }

    function statusBadge(status) {
        const map = {
            'Draft': 'default',
            'Confirmed': 'primary',
            'Packed': 'warning',
            'Dispatched': 'info',
            'Delivered': 'success',
            'Cancelled': 'danger'
        };
        const cls = map[status] || 'default';
        return '<span class="label label-' + cls + '">' + status + '</span>';
    }

    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading Sales Orders...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'search');
        data.append('keyword', $('#keyword').val());
        data.append('customer_id', $('#customer_id').val());
        data.append('warehouse_id', $('#warehouse_id').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=sale/createsale', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderOrders(res.data);
                    renderPagination(res.page || page, Math.ceil((res.total || 0) / (res.limit || 20)));
                } else {
                    Swal.fire('Error', res.message || 'Failed to load sales orders.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });

    }

    function renderOrders(rows) {

        let html = '';

        if (!rows || rows.length == 0) {

            html = `
        <tr>
            <td colspan="8" class="text-center">
                No Sales Orders Found
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
                <td>${item.order_number}</td>
                <td>${customerName(item)}</td>
                <td>${item.warehouse_name??''}</td>
                <td>${item.order_date??''}</td>
                <td>${statusBadge(item.order_status)}</td>
                <td>${subtotal.toFixed(2)}</td>
                <td>${discount.toFixed(2)}</td>
                <td>${tax.toFixed(2)}</td>
                <td><strong>${grandTotal.toFixed(2)}</strong></td>
                <td>${paidAmount.toFixed(2)}</td>
                <td>${remainingAmount.toFixed(2)}</td>
                <td>
                    <button onclick="loadOrder(${item.id})" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button title="Print">
                        <a href="index.php?r=documents/salesorder&id=${item.id}" target="_blank" title="View PDF">
                            <i class="fa fa-file-pdf-o"></i>
                        </a>
                    </button>
                    |
                    <button onclick="deleteOrder(${item.id})" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;
            });

        }

        $('#sale_table tbody').html(html);

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

    function getProductOptions(selected = '') {

        let html = '<option value="">Select Product</option>';

        saleProducts.forEach(p => {

            html += `<option value="${p.id}" data-price="${p.selling_price}" ${selected==p.id?'selected':''}>${p.product_name}</option>`;

        });

        return html;

    }

    function customerOptions(selected = '') {

        let html = '<option value="">Select Customer</option>';

        customers.forEach(c => {

            html += `<option value="${c.id}" ${selected==c.id?'selected':''}>${customerName(c)}</option>`;

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

        let arr = ['Draft', 'Confirmed', 'Packed', 'Dispatched', 'Delivered', 'Cancelled'];

        let html = '';

        arr.forEach(s => {

            html += `<option value="${s}" ${selected==s?'selected':''}>${s}</option>`;

        });

        return html;

    }

    function showSaleModal(data = {}) {

        const isEdit = !!data.id;

        Swal.fire({

            title: isEdit ? 'Update Sale' : 'New Sale',

            width: '1100px',

            customClass: {
                popup: 'swal-wide-popup'
            },

            html: getSaleModalHtml(data),

            showCancelButton: true,

            confirmButtonText: isEdit ? 'Update Sale' : 'Create Sale',

            confirmButtonColor: '#87B87F',

            cancelButtonText: 'Cancel',

            didOpen: function() {

                // Remove all previous event listeners to prevent duplicate handlers
                $('#btnAddItem').off('click');
                $(document).off('click', '.remove-item');
                $(document).off('change', '.item-product');
                $(document).off('input', '.item-qty,.item-rate,.item-discount,.item-tax');

                $('#swal_customer').chosen({
                    width: '100%'
                });
                $('#swal_warehouse').chosen({
                    width: '100%'
                });
                $('#swal_order_status').chosen({
                    width: '100%'
                });

                // Only shipping is editable
                $('#swal_shipping').on('input', updateGrandTotal);

                // Add Item button - with event delegation to prevent duplicates
                $('#btnAddItem').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).prop('disabled', true);
                    addSaleRow();
                    setTimeout(() => $(this).prop('disabled', false), 100);
                });

                if (isEdit) {

                    (data.items || []).forEach(i => addSaleRow(i));

                } else {

                    addSaleRow();

                }

            },

            preConfirm: () => {

                let items = validateSaleOrder();

                if (items === false) return false;

                return {

                    id: $('#swal_id').val(),
                    customer_id: $('#swal_customer').val(),
                    warehouse_id: $('#swal_warehouse').val(),
                    order_date: $('#swal_order_date').val(),
                    delivery_date: $('#swal_delivery_date').val(),
                    order_status: $('#swal_order_status').val(),
                    payment_status: $('#swal_payment_status').val(),
                    subtotal: $('#swal_subtotal').val(),
                    discount: $('#swal_discount').val(),
                    tax: $('#swal_tax').val(),
                    shipping: $('#swal_shipping').val(),
                    grand_total: $('#swal_grand_total').val(),
                    paid_amount: $('#swal_paid_amount').val(),
                    remaining_amount: $('#swal_remaining_amount').val(),
                    notes: $('#swal_notes').val(),
                    items: JSON.stringify(items),
                    flag: isEdit ? 'update' : 'create'

                };

            }

        }).then(r => {

            if (r.isConfirmed) saveSale(r.value);

        });

    }

    function getSaleModalHtml(d = {}) {

        return `

        <form id="saleForm">

        <input type="hidden" id="swal_id" value="${d.id||''}">

        <div class="row">

        <div class="col-md-4">
        <label>Customer</label>
        <select id="swal_customer" class="form-control">
        ${customerOptions(d.customer_id)}
        </select>
        </div>

        <div class="col-md-4">
        <label>Warehouse</label>
        <select id="swal_warehouse" class="form-control">
        ${warehouseOptions(d.warehouse_id)}
        </select>
        </div>

        <div class="col-md-4">
        <label>Order Status</label>
        <select id="swal_order_status" class="form-control">
        ${statusOptions(d.order_status)}
        </select>
        </div>

        </div>

        <div class="row">

        <div class="col-md-4">
        <label>Order Date</label>
        <input type="date" id="swal_order_date" class="form-control" value="${d.order_date||'<?= date('Y-m-d') ?>'}">
        </div>

        <div class="col-md-4">
        <label>Delivery Date</label>
        <input type="date" id="swal_delivery_date" class="form-control" value="${d.delivery_date||''}">
        </div>

        <div class="col-md-3">
        <label>Payment Status</label>
        <select id="swal_payment_status" class="form-control">
        <option value="Pending" ${(d.payment_status||'Pending')=='Pending'?'selected':''}>Pending</option>
        <option value="Partial" ${d.payment_status=='Partial'?'selected':''}>Partial</option>
        <option value="Paid" ${d.payment_status=='Paid'?'selected':''}>Paid</option>
        </select>
        </div>
        
        <div class="col-md-1">
        <button type="button" id="btnAddItem" style="padding: 5px;margin-top: 27px;">Add Item</button>
        </div>

        </div>
 

        <table class="table table-bordered table-striped" id="saleItemTable" style="margin-top:12px">
        <thead>
        <tr>
        <th width="25%">Product</th>
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

        <div class="row">

        <div class="col-md-2">
        <label>Subtotal</label>
        <input type="number" id="swal_subtotal" class="form-control" readonly value="${d.subtotal||0}" style="background:#f5f5f5;">
        </div>

        <div class="col-md-2">
        <label>Discount (Sum)</label>
        <input type="number" id="swal_discount" class="form-control" readonly value="${d.discount||0}" style="background:#f5f5f5;">
        </div>

        <div class="col-md-2">
        <label>Tax (Sum)</label>
        <input type="number" id="swal_tax" class="form-control" readonly value="${d.tax||0}" style="background:#f5f5f5;">
        </div>

        <div class="col-md-2">
        <label>Shipping</label>
        <input type="number" id="swal_shipping" class="form-control" value="${d.shipping||0}" step="0.01">
        </div>
        <div class="col-md-4">
        <label><strong>Grand Total</strong></label>
        <input type="number" id="swal_grand_total" class="form-control" readonly value="${d.grand_total||0}" style="background:#fff3cd; font-size: 16px; font-weight: bold;">
        </div>

        </div>

        <div class="row" style="margin-top: 15px;">

        <div class="col-md-2">
        <label>Paid Amount</label>
        <input type="number" id="swal_paid_amount" class="form-control" value="${d.paid_amount||0}" step="0.01">
        </div>

        <div class="col-md-2">
        <label><strong>Remaining Amount</strong></label>
        <input type="number" id="swal_remaining_amount" class="form-control" readonly value="${d.remaining_amount||0}" style="background:#f5f5f5; font-weight: bold;">
        </div>

        </div>

        <div class="row">

        <div class="col-md-12">
        <label>Notes</label>
        <input type="text" id="swal_notes" class="form-control" value="${d.notes||''}">
        </div>
        </div>

        </form>

        `;

    }

    function addSaleRow(item = {}) {

        // Generate unique ID for this row's product select
        const rowId = Date.now();
        const productId = item.product_id || '';
        const productPrice = item.unit_price || 0;

        // Create row with dropdown
        $('#saleItemTable tbody').append(`
            <tr data-row-id="${rowId}">
            <td>
                <select class="form-control item-product" data-row-id="${rowId}" style="width: 100%;">
                    <option value="">-- Select Product --</option>
                    ${saleProducts.map(p => `<option value="${p.id}" data-price="${p.selling_price}" ${p.id == productId ? 'selected' : ''}>${p.product_name} (${p.sku})</option>`).join('')}
                </select>
            </td>
            <td><input type="number" class="form-control item-qty" value="${item.quantity||1}" min="1"></td>
            <td><input type="number" class="form-control item-rate" value="${productPrice || item.unit_price || 0}" step="0.01"></td>
            <td><input type="number" class="form-control item-discount" value="${item.discount||0}" step="0.01"></td>
            <td><input type="number" class="form-control item-tax" value="${item.tax||0}" step="0.01"></td>
            <td><input type="number" class="form-control item-total" readonly value="${item.total||0}" step="0.01"></td>
            <td><input type="text" class="form-control item-remarks" value="${item.remarks||''}"></td>
            <td><button type="button" class="btn btn-danger btn-xs remove-item"><i class="fa fa-trash"></i></button></td>
            </tr>`);

        // Get the newly added row
        const tr = $('#saleItemTable tbody tr:last');
        const productSelect = tr.find('.item-product');

        // Initialize chosen for this dropdown
        productSelect.chosen({
            width: '100%',
            search_contains: true,
            no_results_text: 'Product not found'
        });

        // Update Chosen display if a product is already selected (for edit mode)
        if (productId) {
            productSelect.trigger('chosen:updated');
        }

        // Handle product selection in this row
        productSelect.off('change').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const price = parseFloat(selectedOption.data('price')) || 0;
            tr.find('.item-rate').val(price).trigger('input');
        });

        // Handle quantity, rate, discount, tax changes
        tr.find('.item-qty, .item-rate, .item-discount, .item-tax').off('input').on('input', function() {
            calculateRow(tr);
        });

        // Handle row deletion
        tr.find('.remove-item').off('click').on('click', function(e) {
            e.preventDefault();
            tr.remove();
            calculateTotals();
        });

        calculateRow(tr);
    }

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

        $('#saleItemTable tbody tr').each(function() {
            const qty = parseFloat($(this).find('.item-qty').val()) || 0;
            const rate = parseFloat($(this).find('.item-rate').val()) || 0;
            const discount = parseFloat($(this).find('.item-discount').val()) || 0;
            const tax = parseFloat($(this).find('.item-tax').val()) || 0;

            subtotal += (qty * rate);
            totalDiscount += discount;
            totalTax += tax;
        });

        $('#swal_subtotal').val(subtotal.toFixed(2));
        $('#swal_discount').val(totalDiscount.toFixed(2));
        $('#swal_tax').val(totalTax.toFixed(2));

        updateGrandTotal();

    }

    function updateGrandTotal() {

        let subtotal = parseFloat($('#swal_subtotal').val()) || 0;
        let discount = parseFloat($('#swal_discount').val()) || 0;
        let tax = parseFloat($('#swal_tax').val()) || 0;
        let shipping = parseFloat($('#swal_shipping').val()) || 0;

        // Grand Total = Subtotal - Discount + Tax + Shipping
        let grandTotal = subtotal - discount + tax + shipping;
        $('#swal_grand_total').val(grandTotal.toFixed(2));

        // Update remaining amount
        updateRemainingAmount();
    }

    function updateRemainingAmount() {
        let grandTotal = parseFloat($('#swal_grand_total').val()) || 0;
        let paidAmount = parseFloat($('#swal_paid_amount').val()) || 0;
        let remainingAmount = grandTotal - paidAmount;
        $('#swal_remaining_amount').val(Math.max(0, remainingAmount).toFixed(2));
    }

    $(document).on('input', '#swal_shipping', updateGrandTotal);
    $(document).on('input', '#swal_paid_amount', updateRemainingAmount);

    function collectSaleItems() {

        let items = [];

        $('#saleItemTable tbody tr').each(function() {

            const productId = $(this).find('.item-product').val();

            if (productId) {  // Only add rows with a product selected
                items.push({
                    product_id: productId,
                    quantity: $(this).find('.item-qty').val(),
                    unit_price: $(this).find('.item-rate').val(),
                    discount: $(this).find('.item-discount').val(),
                    tax: $(this).find('.item-tax').val(),
                    total: $(this).find('.item-total').val(),
                    remarks: $(this).find('.item-remarks').val()
                });
            }

        });

        return items;

    }

    function validateSaleOrder() {

        if (!$('#swal_customer').val()) {

            Swal.showValidationMessage('Please select customer.');

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

        let items = collectSaleItems();

        if (items.length == 0) {

            Swal.showValidationMessage('Please add at least one item.');

            return false;

        }

        let ok = true;

        items.forEach(function(r) {

            if (!r.product_id || parseFloat(r.quantity) <= 0 || parseFloat(r.unit_price) < 0) {

                ok = false;

            }

        });

        if (!ok) {

            Swal.showValidationMessage('Please complete all item rows with valid data.');

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
        fd.append('flag', 'search');
        fd.append('id', id);

        fetch('index.php?r=sale/createsale', {
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

                let order = (r.data && r.data[0]) || {};
                order.items = r.items || [];

                showSaleModal(order);

            })
            .catch(() => {

                Swal.close();

                Swal.fire('Error', 'Unable to load sales order.', 'error');

            });

    }

    function saveSale(formData) {

        Swal.fire({
            title: 'Saving Sale...',
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

        fetch('index.php?r=sale/createsale', {

                method: 'POST',
                body: fd

            })
            .then(r => r.json())
            .then(function(res) {

                if (!res.success) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message || 'Unable to save sale.'
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

    function deleteOrder(id) {

        Swal.fire({
            title: 'Are you sure?',
            text: 'Sales Order will be deleted.',
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

            fetch('index.php?r=sale/createsale', {
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
</script>
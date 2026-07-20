<?php
/**
 * POS SALES VIEW
 * ================================================================================
 * PURPOSE: Point of Sale (POS) transactions for immediate/cash sales
 *
 * FUNCTIONALITY:
 * - Create quick POS transactions for walk-in customers
 * - Add multiple products to cart with quantities
 * - Calculate totals with discounts and taxes
 * - Process payments (Cash, Card, Cheque)
 * - Generate change calculation
 * - List recent POS transactions
 * - Search and filter POS sales
 *
 * DATA MANAGEMENT:
 * - Stores POS sales in: inventory_pos_sales table
 * - Records: pos_no, customer_id, warehouse_id, sale_date, items (JSON),
 *            subtotal, discount_amount, tax_amount, grand_total, paid_amount,
 *            change_amount, payment_method, status, remarks
 * - Status: Completed or Cancelled
 * - Immediately deducts from inventory_stock (unlike orders which reserve)
 * - Creates inventory_stock_movements record for tracking
 *
 * FINANCE INTEGRATION:
 * - POS sales are immediate cash sales
 * - Grand totals contribute to:
 *   • Daily sales revenue in Finance Dashboard
 *   • Cash flow tracking (immediate payment)
 *   • Point of Sale revenue stream in P&L
 * - Payment method tracking enables bank reconciliation
 * - Treated as instant revenue (cash basis) not accrual
 * ================================================================================
 */

use yii\helpers\Html;

if (!isset($customers)) $customers = [];
if (!isset($warehouses)) $warehouses = [];
if (!isset($products)) $products = [];
?>

<!-- Load required libraries before inline scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=sale/salesdashboard">Home</a>
                </li>
                <li class="active">POS Sales</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="showPosModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                New POS Sale
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="pos_search" onsubmit="return false;">

                <input type="text" name="keyword" id="keyword" class="new-input" style="width:16%;" placeholder="POS Number">

                <select name="warehouse_id" id="warehouse_id" class="new-input" style="width:18%;">
                    <option value="">All Warehouses</option>
                    <?php foreach ($warehouses as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['warehouse_name']) ?></option>
                    <?php } ?>
                </select>

                <select name="customer_id" id="customer_id" class="new-input" style="width:18%;">
                    <option value="">All Customers</option>
                    <?php foreach ($customers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['company_name'] ?: trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))) ?></option>
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
                <table class="table table-striped table-bordered table-hover" id="pos_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>POS No</th>
                            <th>Customer</th>
                            <th>Warehouse</th>
                            <th>Sale Date</th>
                            <th>Payment Method</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Tax</th>
                            <th>Grand Total</th>
                            <th>Paid Amount</th>
                            <th>Remaining Amount</th>
                            <th>Status</th>
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
        width: 1000px !important;
        max-width: 95vw !important;
    }

    .swal2-popup.swal-wide-popup .swal2-html-container {
        max-height: none !important;
        overflow: visible !important;
    }
</style>

<script>
    // Initialize data from PHP variables - scope to window for partial view reloads
    if (typeof window.posViewData === 'undefined') {
        window.posViewData = {};
    }
    // Update data (allows reload of partial view without errors)
    window.posViewData.customers = <?= json_encode($customers) ?>;
    window.posViewData.warehouses = <?= json_encode($warehouses) ?>;
    window.posViewData.posProducts = <?= json_encode($products) ?>;
</script>

<script>
    // Initialize page once DOM and libraries are ready
    document.addEventListener('DOMContentLoaded', function() {
        // Wait for Swal and jQuery to be available
        function checkAndInitialize() {
            if (typeof Swal !== 'undefined' && typeof jQuery !== 'undefined') {
                // All libraries loaded, initialize
                searchform();
            } else {
                // Wait a bit and try again
                setTimeout(checkAndInitialize, 100);
            }
        }
        checkAndInitialize();
    });

    function customerName(item) {
        if (!item) return 'Walk-in';
        return item.company_name || ((item.first_name || '') + ' ' + (item.last_name || '')) || 'Walk-in';
    }

    function posStatusBadge(status) {
        const map = {
            'Completed': 'success',
            'Cancelled': 'danger'
        };
        const cls = map[status] || 'default';
        return '<span class="label label-' + cls + '">' + status + '</span>';
    }

    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading POS Sales...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'search');
        data.append('keyword', $('#keyword').val());
        data.append('warehouse_id', $('#warehouse_id').val());
        data.append('customer_id', $('#customer_id').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=sale/possales', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderPos(res.data);
                    renderPagination(res.page || page, Math.ceil((res.total || 0) / (res.limit || 20)));
                } else {
                    Swal.fire('Error', res.message || 'Failed to load POS sales.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });

    }

    // Store POS data globally for modal access
    window.posDataMap = {};

    function renderPos(rows) {

        let html = '';
        window.posDataMap = {}; // Clear previous data

        if (!rows || rows.length == 0) {

            html = `
        <tr>
            <td colspan="14" class="text-center">
                No POS Sales Found
            </td>
        </tr>`;

        } else {

            rows.forEach(function(item, index) {
                // Store data globally using ID as key
                window.posDataMap[item.id] = item;

                const subtotal = parseFloat(item.subtotal) || 0;
                const discount = parseFloat(item.discount_amount) || 0;
                const tax = parseFloat(item.tax_amount) || 0;
                const grandTotal = parseFloat(item.grand_total) || 0;
                const paidAmount = parseFloat(item.paid_amount) || 0;
                const remainingAmount = grandTotal - paidAmount;

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.pos_no}</td>
                <td>${customerName(item)}</td>
                <td>${item.warehouse_name??''}</td>
                <td>${item.sale_date??''}</td>
                <td>${item.payment_method??''}</td>
                <td>${subtotal.toFixed(2)}</td>
                <td>${discount.toFixed(2)}</td>
                <td>${tax.toFixed(2)}</td>
                <td><strong>${grandTotal.toFixed(2)}</strong></td>
                <td>${paidAmount.toFixed(2)}</td>
                <td>${remainingAmount.toFixed(2)}</td>
                <td>${posStatusBadge(item.status)}</td>
                <td>
                    <button onclick="editPos(${item.id})" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button title="POS Receipt">
                        <a href="index.php?r=documents/posreceipt&id=${item.id}" target="_blank" title="POS Receipt">
                            <i class="fa fa-file-pdf-o"></i>
                        </a>
                    </button>
                    |
                    <button onclick="deletePos(${item.id})" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;
            });

        }

        $('#pos_table tbody').html(html);

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

        window.posViewData.posProducts.forEach(p => {

            html += `<option value="${p.id}" data-price="${p.selling_price}" ${selected==p.id?'selected':''}>${p.product_name}</option>`;

        });

        return html;

    }

    function showPosModal() {

        let customerOptions = '<option value="">Walk-in Customer</option>';
        window.posViewData.customers.forEach(c => {
            customerOptions += `<option value="${c.id}">${customerName(c)}</option>`;
        });

        let warehouseOptions = '';
        window.posViewData.warehouses.forEach(w => {
            warehouseOptions += `<option value="${w.id}">${w.warehouse_name}</option>`;
        });

        Swal.fire({

            title: 'New POS Sale',

            width: '1200px',

            customClass: {
                popup: 'swal-wide-popup'
            },

            html: `
                <form id="posForm">

                <div class="row">

                <div class="col-md-4">
                <label>Warehouse<span style="color:red;">*</span></label>
                <select id="swal_warehouse" class="form-control">
                ${warehouseOptions}
                </select>
                </div>

                <div class="col-md-4">
                <label>Customer</label>
                <select id="swal_customer" class="form-control">
                ${customerOptions}
                </select>
                </div>

                <div class="col-md-2">
                    <label>Payment Method</label>
                    <select id="swal_payment_method" class="form-control">
                        <option value="Cash">Cash</option>
                        <option value="Bank">Bank</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Online">Online</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="button" id="btnAddPosItem" style="padding: 5px;margin-top: 27px;">Add Item</button>
                </div>

                </div>

                <!-- Walk-in Customer Fields -->
                <div id="walkinFields" style="display:none; margin-top:15px; padding:15px; background:#f9f9f9; border:1px solid #ddd; border-radius:4px;">
                <div class="row">
                <div class="col-md-3">
                <label>Customer Name<span style="color:red;">*</span></label>
                <input type="text" id="swal_customer_name" class="form-control" placeholder="Enter name">
                </div>
                <div class="col-md-3">
                <label>Email (Optional)</label>
                <input type="email" id="swal_customer_email" class="form-control" placeholder="email@example.com">
                </div>
                <div class="col-md-3">
                <label>Phone No<span style="color:red;">*</span></label>
                <input type="text" id="swal_customer_phone" class="form-control" placeholder="Phone number">
                </div>
                <div class="col-md-3">
                <label>Reference No (Optional)</label>
                <input type="text" id="swal_customer_reference" class="form-control" placeholder="Reference">
                </div>
                </div>
                </div>

                <table class="table table-bordered table-striped" style="margin-top: 12px;" id="posItemTable">

                <thead>

                <tr>

                <th width="25%">Product</th>
                <th width="8%">Qty</th>
                <th width="10%">Rate</th>
                <th width="10%">Discount</th>
                <th width="10%">Tax</th>
                <th width="12%">Total</th>
                <th width="15%">Remarks</th>
                <th width="5%"></th>

                </tr>

                </thead>

                <tbody></tbody>

                </table>

                <div class="row">

                <div class="col-md-2">
                <label>Subtotal</label>
                <input type="number" id="swal_subtotal" class="form-control" readonly value="0" style="background:#f5f5f5;">
                </div>

                <div class="col-md-2">
                <label>Discount (Sum)</label>
                <input type="number" id="swal_total_discount" class="form-control" readonly value="0" style="background:#f5f5f5;">
                </div>

                <div class="col-md-2">
                <label>Tax (Sum)</label>
                <input type="number" id="swal_total_tax" class="form-control" readonly value="0" style="background:#f5f5f5;">
                </div>

                <div class="col-md-4">
                <label><strong>Grand Total</strong></label>
                <input type="number" id="swal_grand_total" class="form-control" readonly value="0" style="background:#fff3cd; font-weight:bold;">
                </div>

                </div>

                <div class="row" style="margin-top: 15px;">

                <div class="col-md-2">
                <label>Paid Amount</label>
                <input type="number" id="swal_paid_amount" class="form-control" value="0" step="0.01">
                </div>

                <div class="col-md-2">
                <label><strong>Remaining Amount</strong></label>
                <input type="number" id="swal_remaining_amount" class="form-control" readonly value="0" style="background:#f5f5f5; font-weight:bold;">
                </div>

                <div class="col-md-8">
                <label>Remarks</label>
                <input type="text" id="swal_remarks" class="form-control" placeholder="Add notes or remarks">
                </div>

                </div>

                </form>
                `,

            showCancelButton: true,

            confirmButtonText: 'Complete Sale',

            confirmButtonColor: '#87B87F',

            cancelButtonText: 'Cancel',

            didOpen: function() {

                $('#swal_warehouse').chosen({ width: '100%' });
                $('#swal_customer').chosen({ width: '100%' });

                // Show/hide walk-in fields based on customer selection
                $('#swal_customer').on('change', function() {
                    const val = $(this).val();
                    if (val === '') {
                        $('#walkinFields').show();
                    } else {
                        $('#walkinFields').hide();
                    }
                });

                // Remove all previous event listeners
                $(document).off('change', '#posItemTable .item-product');
                $(document).off('input', '#posItemTable .item-qty, #posItemTable .item-rate, #posItemTable .item-discount, #posItemTable .item-tax');
                $(document).off('click', '#posItemTable .remove-item');
                $(document).off('input', '#swal_paid_amount');

                // Add Item button handler
                const addItemState = { lastClick: Date.now() - 500 };
                const addItemBtn = document.getElementById('btnAddPosItem');
                if (addItemBtn) {
                    addItemBtn.onclick = function(e) {
                        if (e) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                        const now = Date.now();
                        if (now - addItemState.lastClick < 350) return;
                        addItemState.lastClick = now;
                        addPosRow();
                        calculatePosTotals();
                    };
                }

                // Product change handler
                $(document).on('change', '#posItemTable .item-product', function() {
                    let tr = $(this).closest('tr');
                    let productId = $(this).val();
                    let product = window.posViewData.posProducts.find(p => p.id == productId);
                    let price = product ? parseFloat(product.selling_price) || 0 : 0;
                    tr.find('.item-rate').val(price.toFixed(2)).trigger('input');
                });

                // Item calculation handlers
                $(document).on('input', '#posItemTable .item-qty, #posItemTable .item-rate, #posItemTable .item-discount, #posItemTable .item-tax', function() {
                    calculatePosRow($(this).closest('tr'));
                });

                // Delete row handler
                $(document).on('click', '#posItemTable .remove-item', function() {
                    $(this).closest('tr').remove();
                    calculatePosTotals();
                });

                // Paid amount change handler
                $(document).on('input', '#swal_paid_amount', updatePosChange);

                // Add initial row
                addPosRow();

            },

            preConfirm: () => {

                let items = validatePosSale();

                if (items === false) return false;

                let customerId = $('#swal_customer').val();

                // Validate walk-in customer fields
                if (!customerId) {
                    let name = $('#swal_customer_name').val().trim();
                    let phone = $('#swal_customer_phone').val().trim();

                    if (!name) {
                        Swal.showValidationMessage('Please enter customer name.');
                        return false;
                    }
                    if (!phone) {
                        Swal.showValidationMessage('Please enter customer phone number.');
                        return false;
                    }

                    return {
                        warehouse_id: $('#swal_warehouse').val(),
                        customer_type: 'Walk-in',
                        customer_name: name,
                        customer_email: $('#swal_customer_email').val().trim(),
                        customer_phone: phone,
                        customer_reference: $('#swal_customer_reference').val().trim(),
                        payment_method: $('#swal_payment_method').val(),
                        paid_amount: $('#swal_paid_amount').val(),
                        remarks: $('#swal_remarks').val().trim(),
                        items: JSON.stringify(items),
                        flag: 'create'
                    };
                }

                return {
                    warehouse_id: $('#swal_warehouse').val(),
                    customer_id: customerId,
                    payment_method: $('#swal_payment_method').val(),
                    paid_amount: $('#swal_paid_amount').val(),
                    remarks: $('#swal_remarks').val().trim(),
                    items: JSON.stringify(items),
                    flag: 'create'
                };

            }

        }).then(r => {

            if (r.isConfirmed) savePos(r.value);

        });

    }

    function addPosRow(item = {}) {

        $('#posItemTable tbody').append(`
            <tr>
            <td><select class="form-control item-product" style="width: 100%;">
                <option value="">-- Select Product --</option>
                ${window.posViewData.posProducts.map(p => `<option value="${p.id}" data-price="${p.selling_price}" ${p.id == (item.product_id||'') ? 'selected' : ''}>${p.product_name} (${p.sku})</option>`).join('')}
            </select></td>
            <td><input type="number" class="form-control item-qty" value="${item.quantity||1}" min="1" step="0.01"></td>
            <td><input type="number" class="form-control item-rate" value="${item.unit_price||0}" step="0.01"></td>
            <td><input type="number" class="form-control item-discount" value="${item.discount||0}" step="0.01"></td>
            <td><input type="number" class="form-control item-tax" value="${item.tax||0}" step="0.01"></td>
            <td><input type="number" class="form-control item-total" readonly value="${item.total||0}" step="0.01"></td>
            <td><input type="text" class="form-control item-remarks" value="${item.remarks||''}"></td>
            <td><button type="button" class="btn btn-danger btn-xs remove-item"><i class="fa fa-trash"></i></button></td>
            </tr>`);

        let tr = $('#posItemTable tbody tr:last');

        tr.find('.item-product').chosen({
            width: '100%',
            search_contains: true
        });

        calculatePosRow(tr);

    }

    $(document).on('change', '#posItemTable .item-product', function() {

        let tr = $(this).closest('tr');
        let productId = $(this).val();

        // Find product in data array instead of relying on data attribute (works better with Chosen.js)
        let product = window.posViewData.posProducts.find(p => p.id == productId);
        let price = product ? parseFloat(product.selling_price) || 0 : 0;

        tr.find('.item-rate').val(price.toFixed(2));

        calculatePosRow(tr);

    });
    $(document).on('input', '#posItemTable .item-qty,#posItemTable .item-rate,#posItemTable .item-discount,#posItemTable .item-tax', function() {

        calculatePosRow($(this).closest('tr'));

    });
    $(document).on('click', '#posItemTable .remove-item', function() {

        $(this).closest('tr').remove();

        calculatePosTotals();

    });

    function calculatePosRow(tr) {

        let qty = parseFloat(tr.find('.item-qty').val()) || 0;

        let rate = parseFloat(tr.find('.item-rate').val()) || 0;

        let discount = parseFloat(tr.find('.item-discount').val()) || 0;

        let tax = parseFloat(tr.find('.item-tax').val()) || 0;

        let total = (qty * rate) - discount + tax;

        tr.find('.item-total').val(total.toFixed(2));

        calculatePosTotals();

    }

    function calculatePosTotals() {

        let subtotal = 0;
        let totalDiscount = 0;
        let totalTax = 0;

        $('#posItemTable tbody tr').each(function() {

            let qty = parseFloat($(this).find('.item-qty').val()) || 0;
            let rate = parseFloat($(this).find('.item-rate').val()) || 0;
            let discount = parseFloat($(this).find('.item-discount').val()) || 0;
            let tax = parseFloat($(this).find('.item-tax').val()) || 0;

            subtotal += (qty * rate);
            totalDiscount += discount;
            totalTax += tax;

        });

        let grandTotal = subtotal - totalDiscount + totalTax;

        $('#swal_subtotal').val(subtotal.toFixed(2));
        $('#swal_total_discount').val(totalDiscount.toFixed(2));
        $('#swal_total_tax').val(totalTax.toFixed(2));
        $('#swal_grand_total').val(grandTotal.toFixed(2));

        updatePosChange();

    }

    function updatePosChange() {

        let grandTotal = parseFloat($('#swal_grand_total').val()) || 0;
        let paidAmount = parseFloat($('#swal_paid_amount').val()) || 0;
        let remainingAmount = grandTotal - paidAmount;

        $('#swal_remaining_amount').val(Math.max(0, remainingAmount).toFixed(2));

        // Style remaining amount field based on value
        if (remainingAmount > 0) {
            $('#swal_remaining_amount').css('background-color', '#ffe6e6');
        } else {
            $('#swal_remaining_amount').css('background-color', '#e6ffe6');
        }

    }

    function collectPosItems() {

        let items = [];

        $('#posItemTable tbody tr').each(function() {

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

    function validatePosSale() {

        if (!$('#swal_warehouse').val()) {

            Swal.showValidationMessage('Please select warehouse.');

            return false;

        }

        let items = collectPosItems();

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

    function savePos(formData) {

        Swal.fire({
            title: 'Processing Sale...',
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

        fetch('index.php?r=sale/possales', {

                method: 'POST',
                body: fd

            })
            .then(r => r.json())
            .then(function(res) {

                if (!res.success) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message || 'Unable to complete sale.'
                    });

                    return;

                }

                Swal.fire({
                    icon: 'success',
                    title: 'Sale Completed',
                    text: res.message + (res.change_amount ? (' Change: ' + Number(res.change_amount).toLocaleString()) : ''),
                    timer: 2000,
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

    function editPos(posId) {

        // Get data from global map
        const posData = window.posDataMap[posId];

        if (!posData) {
            Swal.fire('Error', 'POS Sale data not found', 'error');
            return;
        }

        let customerOptions = '<option value="">Walk-in Customer</option>';
        window.posViewData.customers.forEach(c => {
            const selected = c.id == posData.customer_id ? 'selected' : '';
            customerOptions += `<option value="${c.id}" ${selected}>${customerName(c)}</option>`;
        });

        let warehouseOptions = '';
        window.posViewData.warehouses.forEach(w => {
            const selected = w.id == posData.warehouse_id ? 'selected' : '';
            warehouseOptions += `<option value="${w.id}" ${selected}>${w.warehouse_name}</option>`;
        });

        const posItems = posData.items && typeof posData.items === 'string' ? JSON.parse(posData.items) : (posData.items || []);
        const grandTotal = parseFloat(posData.grand_total) || 0;
        const paidAmount = parseFloat(posData.paid_amount) || 0;
        const remainingAmount = grandTotal - paidAmount;

        Swal.fire({
            title: 'Edit POS Sale',
            width: '1200px',
            customClass: {
                popup: 'swal-wide-popup'
            },
            html: `
                <form id="posEditForm">
                <input type="hidden" id="edit_pos_id" value="${posData.id}">

                <div class="row">
                <div class="col-md-3">
                <label>Warehouse<span style="color:red;">*</span></label>
                <select id="edit_swal_warehouse" class="form-control">
                ${warehouseOptions}
                </select>
                </div>

                <div class="col-md-3">
                <label>Customer</label>
                <select id="edit_swal_customer" class="form-control">
                ${customerOptions}
                </select>
                </div>

                <div class="col-md-3">
                <label>Payment Method</label>
                <select id="edit_swal_payment_method" class="form-control">
                <option value="Cash" ${posData.payment_method === 'Cash' ? 'selected' : ''}>Cash</option>
                <option value="Bank" ${posData.payment_method === 'Bank' ? 'selected' : ''}>Bank</option>
                <option value="Cheque" ${posData.payment_method === 'Cheque' ? 'selected' : ''}>Cheque</option>
                <option value="Online" ${posData.payment_method === 'Online' ? 'selected' : ''}>Online</option>
                </select>
                </div>
                </div>

                <div class="row" style="margin-top:15px;">
                <div class="col-md-12">
                <button type="button" class="btn btn-success btn-sm" id="btnEditAddPosItem">
                <i class="fa fa-plus"></i>
                Add Item
                </button>
                </div>
                </div>

                <hr>

                <table class="table table-bordered table-striped" id="editPosItemTable">
                <thead>
                <tr>
                <th width="30%">Product</th>
                <th width="10%">Qty</th>
                <th width="12%">Rate</th>
                <th width="12%">Discount</th>
                <th width="12%">Tax</th>
                <th width="12%">Total</th>
                <th width="2%"></th>
                </tr>
                </thead>
                <tbody></tbody>
                </table>

                <div class="row">

                <div class="col-md-2">
                <label>Subtotal</label>
                <input type="number" step="0.01" id="edit_swal_subtotal" class="form-control" readonly value="0" style="background:#f5f5f5;">
                </div>

                <div class="col-md-2">
                <label>Discount (Sum)</label>
                <input type="number" step="0.01" id="edit_swal_total_discount" class="form-control" readonly value="0" style="background:#f5f5f5;">
                </div>

                <div class="col-md-2">
                <label>Tax (Sum)</label>
                <input type="number" step="0.01" id="edit_swal_total_tax" class="form-control" readonly value="0" style="background:#f5f5f5;">
                </div>

                <div class="col-md-4">
                <label><strong>Grand Total</strong></label>
                <input type="number" step="0.01" id="edit_swal_grand_total" class="form-control" readonly value="${grandTotal.toFixed(2)}" style="background:#fff3cd; font-weight:bold;">
                </div>

                </div>

                <div class="row">

                <div class="col-md-3">
                <label>Paid Amount</label>
                <input type="number" step="0.01" id="edit_swal_paid_amount" class="form-control" value="${paidAmount.toFixed(2)}">
                </div>

                <div class="col-md-3">
                <label><strong>Remaining Amount</strong></label>
                <input type="number" step="0.01" id="edit_swal_remaining_amount" class="form-control" readonly value="${remainingAmount.toFixed(2)}" style="background:#ffe6e6; font-weight:bold;">
                </div>

                <div class="col-md-6">
                <label>Remarks</label>
                <input type="text" id="edit_swal_remarks" class="form-control" value="${posData.remarks || ''}" placeholder="Add notes or remarks">
                </div>

                </div>

                </form>
                `,
            showCancelButton: true,
            confirmButtonText: 'Update Sale',
            confirmButtonColor: '#87B87F',
            cancelButtonText: 'Cancel',
            didOpen: function() {
                $('#edit_swal_warehouse').chosen({width: '100%'});
                $('#edit_swal_customer').chosen({width: '100%'});

                // Load existing items
                posItems.forEach(item => {
                    addEditPosRow(item);
                });

                // Calculate totals
                updateEditPosTotals();

                // Handle paid amount changes
                $('#edit_swal_paid_amount').on('input', function() {
                    const paid = parseFloat($(this).val()) || 0;
                    const remaining = Math.max(0, grandTotal - paid);
                    $('#edit_swal_remaining_amount').val(remaining.toFixed(2));
                });

                // Add item button
                $('#btnEditAddPosItem').on('click', function(e) {
                    e.preventDefault();
                    addEditPosRow();
                    updateEditPosTotals();
                });

                // Item changes
                $('#editPosItemTable').on('change input', 'input, select', function() {
                    updateEditPosTotals();
                });

                // Delete row
                $(document).on('click', '.edit-delete-row', function() {
                    $(this).closest('tr').remove();
                    updateEditPosTotals();
                });
            },
            preConfirm: () => {
                let items = [];
                $('#editPosItemTable tbody tr').each(function() {
                    const qty = parseFloat($(this).find('.edit-item-qty').val()) || 0;
                    if (qty > 0) {
                        items.push({
                            product_id: $(this).find('.edit-item-product').val(),
                            quantity: qty,
                            unit_price: parseFloat($(this).find('.edit-item-rate').val()) || 0,
                            discount: parseFloat($(this).find('.edit-item-discount').val()) || 0,
                            tax: parseFloat($(this).find('.edit-item-tax').val()) || 0
                        });
                    }
                });

                if (items.length === 0) {
                    Swal.showValidationMessage('Please add at least one item');
                    return false;
                }

                return {
                    id: $('#edit_pos_id').val(),
                    warehouse_id: $('#edit_swal_warehouse').val(),
                    customer_id: $('#edit_swal_customer').val(),
                    payment_method: $('#edit_swal_payment_method').val(),
                    paid_amount: $('#edit_swal_paid_amount').val(),
                    remarks: $('#edit_swal_remarks').val().trim(),
                    items: JSON.stringify(items),
                    flag: 'update_full'
                };
            }
        }).then(r => {
            if (r.isConfirmed) {
                savePosEdit(r.value);
            }
        });

    }

    function addEditPosRow(item = {}) {
        const productId = item.product_id || '';
        const productPrice = item.unit_price || 0;

        $('#editPosItemTable tbody').append(`
            <tr>
            <td><select class="form-control edit-item-product">${getProductOptions(productId)}</select></td>
            <td><input type="number" class="form-control edit-item-qty" value="${item.quantity||1}"></td>
            <td><input type="number" step="0.01" class="form-control edit-item-rate" value="${productPrice || 0}"></td>
            <td><input type="number" step="0.01" class="form-control edit-item-discount" value="${item.discount||0}"></td>
            <td><input type="number" step="0.01" class="form-control edit-item-tax" value="${item.tax||0}"></td>
            <td><input type="number" step="0.01" readonly class="form-control edit-item-total" value="0" style="background:#f5f5f5;"></td>
            <td><button type="button" class="btn btn-danger btn-xs edit-delete-row"><i class="fa fa-trash"></i></button></td>
            </tr>
        `);

        const tr = $('#editPosItemTable tbody tr:last');
        const productSelect = tr.find('.edit-item-product');

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

        // Handle product selection
        productSelect.off('change').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const price = parseFloat(selectedOption.data('price')) || 0;
            tr.find('.edit-item-rate').val(price.toFixed(2)).trigger('input');
        });
    }

    function updateEditPosTotals() {
        let subtotal = 0, totalDiscount = 0, totalTax = 0;

        $('#editPosItemTable tbody tr').each(function() {
            const qty = parseFloat($(this).find('.edit-item-qty').val()) || 0;
            const rate = parseFloat($(this).find('.edit-item-rate').val()) || 0;
            const discount = parseFloat($(this).find('.edit-item-discount').val()) || 0;
            const tax = parseFloat($(this).find('.edit-item-tax').val()) || 0;

            const itemTotal = (qty * rate) - discount + tax;
            $(this).find('.edit-item-total').val(itemTotal.toFixed(2));

            subtotal += qty * rate;
            totalDiscount += discount;
            totalTax += tax;
        });

        const grandTotal = subtotal - totalDiscount + totalTax;

        $('#edit_swal_subtotal').val(subtotal.toFixed(2));
        $('#edit_swal_total_discount').val(totalDiscount.toFixed(2));
        $('#edit_swal_total_tax').val(totalTax.toFixed(2));
        $('#edit_swal_grand_total').val(grandTotal.toFixed(2));

        const paidAmount = parseFloat($('#edit_swal_paid_amount').val()) || 0;
        const remainingAmount = grandTotal - paidAmount;
        $('#edit_swal_remaining_amount').val(remainingAmount.toFixed(2));
    }

    function savePosEdit(formData) {

        Swal.fire({
            title: 'Processing...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'update_full');
        data.append('id', formData.id);
        data.append('warehouse_id', formData.warehouse_id);
        data.append('customer_id', formData.customer_id);
        data.append('payment_method', formData.payment_method);
        data.append('paid_amount', formData.paid_amount);
        data.append('items', formData.items);

        fetch('index.php?r=sale/possales', {
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
                Swal.fire('Error', 'Unable to update record.', 'error');
            });

    }

    function updatePosSale(formData) {

        Swal.fire({
            title: 'Processing...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'update');
        data.append('id', formData.id);
        data.append('paid_amount', formData.paid_amount);
        data.append('remarks', formData.remarks);

        fetch('index.php?r=sale/possales', {
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
                Swal.fire('Error', 'Unable to update record.', 'error');
            });

    }

    function deletePos(id) {

        Swal.fire({
            title: 'Are you sure?',
            text: 'POS Sale will be cancelled and stock reversed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Cancel Sale'
        }).then(function(result) {

            if (!result.isConfirmed) {
                return;
            }

            const data = new FormData();

            data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
            data.append('flag', 'delete');
            data.append('id', id);

            fetch('index.php?r=sale/possales', {
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
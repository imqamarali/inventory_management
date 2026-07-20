<?php
/**
 * SALES ORDERS VIEW
 * ================================================================================
 * PURPOSE: View and manage all sales orders with filtering, search, and status updates
 *
 * FUNCTIONALITY:
 * - List all sales orders with customer and warehouse details
 * - Filter by customer, warehouse, status, payment status, date range
 * - Search by order number
 * - Update order status and payment status
 * - Delete orders (soft delete)
 * - Pagination support for large datasets
 *
 * DATA MANAGEMENT:
 * - Source table: inventory_sales_orders
 * - Displays: Order number, Customer, Warehouse, Order Date, Delivery Date,
 *             Order Status, Payment Status, Grand Total
 * - Status values: Draft, Confirmed, Dispatched, Delivered, Cancelled
 * - Payment Status: Pending, Partial, Paid
 *
 * FINANCE INTEGRATION:
 * - Grand totals are summed in Finance Dashboard for Total Sales Value
 * - Payment status progression tracks cash collection
 * - This view provides the source data for:
 *   • Sales Revenue in Profit & Loss statement
 *   • Accounts Receivable aging analysis
 *   • Monthly sales trends
 *   • Customer credit limits tracking
 * ================================================================================
 */

use yii\helpers\Html;

$this->title = 'Sales Orders';

if (!isset($salesOrders)) $salesOrders = [];
if (!isset($customers)) $customers = [];
if (!isset($warehouses)) $warehouses = [];
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=sale/salesdashboard">Home</a>
                </li>
                <li class="active">Sales Orders</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="showSaleOrderModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                New Sale Order
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="order_search" onsubmit="return false;">

                <input type="text" name="order_number" id="order_number" class="new-input" style="width:14%;" placeholder="Order Number">

                <select name="customer_id" id="customer_id" class="new-input" style="width:15%;">
                    <option value="">All Customers</option>
                    <?php foreach ($customers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['company_name'] ?: trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))) ?></option>
                    <?php } ?>
                </select>

                <select name="warehouse_id" id="warehouse_id" class="new-input" style="width:13%;">
                    <option value="">All Warehouses</option>
                    <?php foreach ($warehouses as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['warehouse_name']) ?></option>
                    <?php } ?>
                </select>

                <select name="order_status" id="order_status" class="new-input" style="width:12%;">
                    <option value="">All Status</option>
                    <option value="Draft">Draft</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Packed">Packed</option>
                    <option value="Dispatched">Dispatched</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Cancelled">Cancelled</option>
                </select>

                <select name="payment_status" id="payment_status" class="new-input" style="width:12%;">
                    <option value="">All Payment</option>
                    <option value="Pending">Pending</option>
                    <option value="Partial">Partial</option>
                    <option value="Paid">Paid</option>
                </select>

                <input type="date" name="from_date" id="from_date" class="new-input" style="width:10%;">
                <input type="date" name="to_date" id="to_date" class="new-input" style="width:10%;">

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
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Warehouse</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Grand Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($salesOrders as $key => $item) { ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= Html::encode($item['order_number']) ?></td>
                                <td><?= Html::encode($item['company_name'] ?: trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''))) ?></td>
                                <td><?= Html::encode($item['warehouse_name']) ?></td>
                                <td><?= Html::encode($item['order_date']) ?></td>
                                <td><?= statusBadgeServer($item['order_status']) ?></td>
                                <td><?= paymentBadgeServer($item['payment_status']) ?></td>
                                <td><?= number_format($item['grand_total'], 2) ?></td>
                                <td>
                                    <button onclick='showOrderModal(<?= json_encode($item) ?>)' title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    |
                                    <button onclick="printSalesOrder(<?= $item['id'] ?>)" title="Print PDF">
                                        <i class="fa fa-print"></i>
                                    </button>
                                    |
                                    <button onclick="deleteOrder(<?= $item['id'] ?>)" title="Delete">
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
        'Confirmed' => 'primary',
        'Packed' => 'warning',
        'Dispatched' => 'info',
        'Delivered' => 'success',
        'Cancelled' => 'danger'
    ];
    $cls = $map[$status] ?? 'default';
    return '<span class="label label-' . $cls . '">' . Html::encode($status) . '</span>';
}
function paymentBadgeServer($status)
{
    $map = ['Pending' => 'danger', 'Partial' => 'warning', 'Paid' => 'success'];
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
    if (typeof customers === 'undefined' || !customers) {
        var customers = <?= json_encode($customers) ?>;
    }
    if (typeof warehouses === 'undefined' || !warehouses) {
        var warehouses = <?= json_encode($warehouses) ?>;
    }
    if (typeof saleOrderViewData === 'undefined') {
        window.saleOrderViewData = {
            customers: <?= json_encode($customers) ?>,
            warehouses: <?= json_encode($warehouses) ?>,
            products: []
        };
    }
</script>

<script>
    searchform();

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
        data.append('flag', 'load');
        data.append('order_number', $('#order_number').val());
        data.append('customer_id', $('#customer_id').val());
        data.append('warehouse_id', $('#warehouse_id').val());
        data.append('order_status', $('#order_status').val());
        data.append('payment_status', $('#payment_status').val());
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=sale/salesorders', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderOrders(res.salesOrders);
                    renderPagination(res.page, res.totalPages);
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

    function paymentBadge(status) {
        const map = {
            'Pending': 'danger',
            'Partial': 'warning',
            'Paid': 'success'
        };
        const cls = map[status] || 'default';
        return '<span class="label label-' + cls + '">' + status + '</span>';
    }

    function customerName(item) {
        return item.company_name || ((item.first_name || '') + ' ' + (item.last_name || ''));
    }

    function renderOrders(rows) {

        let html = '';

        if (rows.length == 0) {

            html = `
        <tr>
            <td colspan="9" class="text-center">
                No Sales Orders Found
            </td>
        </tr>`;

        } else {

            rows.forEach(function(item, index) {

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.order_number}</td>
                <td>${customerName(item)}</td>
                <td>${item.warehouse_name??''}</td>
                <td>${item.order_date??''}</td>
                <td>${statusBadge(item.order_status)}</td>
                <td>${paymentBadge(item.payment_status)}</td>
                <td>${parseFloat(item.grand_total).toFixed(2)}</td>
                <td>
                    <button onclick='showSaleOrderModal(${JSON.stringify(item)})' title="Edit">
                        <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button onclick="printSalesOrder(${item.id})" title="Print PDF">
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

    function printSalesOrder(id) {
        const url = 'index.php?r=documents/salesorder&id=' + id;
        window.open(url, '_blank');
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

            fetch('index.php?r=sale/salesorders', {
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

    function calcOrderGrandTotal() {
        let subtotal = parseFloat($('#swal_subtotal').val()) || 0;
        let discount = parseFloat($('#swal_discount').val()) || 0;
        let tax = parseFloat($('#swal_tax').val()) || 0;
        let shipping = parseFloat($('#swal_shipping').val()) || 0;
        let grand = subtotal - discount + tax + shipping;
        $('#swal_grand_total').val(grand.toFixed(2));
    }

    function showSaleOrderModal(orderData = null) {
        const isEdit = orderData !== null && orderData.id;
        const id = isEdit ? orderData.id : '';
        const customerId = isEdit ? orderData.customer_id : '';
        const warehouseId = isEdit ? orderData.warehouse_id : '';
        const orderDate = isEdit ? orderData.order_date : '<?= date('Y-m-d') ?>';
        const deliveryDate = isEdit ? (orderData.delivery_date || '') : '';
        const orderStatus = isEdit ? orderData.order_status : 'Draft';
        const paymentStatus = isEdit ? orderData.payment_status : 'Pending';
        const subtotal = isEdit ? orderData.subtotal : 0;
        const discount = isEdit ? orderData.discount : 0;
        const tax = isEdit ? orderData.tax : 0;
        const shipping = isEdit ? orderData.shipping : 0;
        const grandTotal = isEdit ? orderData.grand_total : 0;
        const notes = isEdit ? (orderData.notes || '') : '';

        let customerOptions = '<option value="">Walk-in Customer</option>';
        window.saleOrderViewData.customers.forEach(c => {
            customerOptions += `<option value="${c.id}" ${c.id==customerId?'selected':''}>${customerName(c)}</option>`;
        });

        let warehouseOptions = '';
        window.saleOrderViewData.warehouses.forEach(w => {
            warehouseOptions += `<option value="${w.id}" ${w.id==warehouseId?'selected':''}>${w.warehouse_name}</option>`;
        });

        const statusList = ['Draft', 'Confirmed', 'Packed', 'Dispatched', 'Delivered', 'Cancelled'];
        let statusOptions = '';
        statusList.forEach(s => {
            statusOptions += `<option value="${s}" ${s==orderStatus?'selected':''}>${s}</option>`;
        });

        const paymentStatusList = ['Pending', 'Partial', 'Paid'];
        let paymentStatusOptions = '';
        paymentStatusList.forEach(s => {
            paymentStatusOptions += `<option value="${s}" ${s==paymentStatus?'selected':''}>${s}</option>`;
        });

        Swal.fire({
            title: isEdit ? 'Edit Sale Order' : 'New Sale Order',
            width: '1300px',
            customClass: { popup: 'swal-wide-popup' },
            html: `
                <form id="saleOrderForm">
                <input type="hidden" id="swal_id" value="${id}">

                <div class="row">
                <div class="col-md-3">
                <label>Warehouse<span style="color:red;">*</span></label>
                <select id="swal_warehouse" class="form-control">
                ${warehouseOptions}
                </select>
                </div>

                <div class="col-md-4">
                <label>Customer<span style="color:red;">*</span></label>
                <select id="swal_customer" class="form-control">
                ${customerOptions}
                </select>
                </div>

                <div class="col-md-2">
                <label>Order Date</label>
                <input type="date" id="swal_order_date" class="form-control" value="${orderDate}">
                </div>

                <div class="col-md-3">
                <label>Delivery Date</label>
                <input type="date" id="swal_delivery_date" class="form-control" value="${deliveryDate}">
                </div>
                </div>

                <div class="row" style="margin-top:10px;">
                <div class="col-md-3">
                <label>Order Status</label>
                <select id="swal_order_status" class="form-control">
                ${statusOptions}
                </select>
                </div>
                <div class="col-md-3">
                <label>Payment Status</label>
                <select id="swal_payment_status" class="form-control">
                ${paymentStatusOptions}
                </select>
                </div>
                <div class="col-md-6">
                <label>Notes</label>
                <input type="text" id="swal_notes" class="form-control" placeholder="Add notes or remarks" value="${notes}">
                </div>
                </div>

                <div class="row" style="margin-top:10px;">
                <div class="col-md-11"></div>
                <div class="col-md-1">
                <button type="button" id="btnAddItem" class="btn btn-primary" style="width:100%;">Add Item</button>
                </div>
                </div>

                <!-- Walk-in Customer Details -->
                <div id="walkinFields" style="display:none; margin-top:15px; padding:15px; background:#f9f9f9; border:1px solid #ddd; border-radius:4px;">
                <div class="row">
                <div class="col-md-3">
                <label>Customer Name<span style="color:red;">*</span></label>
                <input type="text" id="swal_customer_name" class="form-control" placeholder="Enter name">
                </div>
                <div class="col-md-3">
                <label>Email</label>
                <input type="email" id="swal_customer_email" class="form-control" placeholder="email@example.com">
                </div>
                <div class="col-md-3">
                <label>Phone<span style="color:red;">*</span></label>
                <input type="text" id="swal_customer_phone" class="form-control" placeholder="Phone number">
                </div>
                <div class="col-md-3">
                <label>Reference</label>
                <input type="text" id="swal_customer_reference" class="form-control" placeholder="Reference no">
                </div>
                </div>
                </div>

                <!-- Customer Details Display (when selected) -->
                <div id="customerDetails" style="display:none; margin-top:10px; padding:10px; background:#e8f4f8; border:1px solid #b3d9e8; border-radius:4px;">
                <div class="row">
                <div class="col-md-3"><strong>Name:</strong> <span id="detailName"></span></div>
                <div class="col-md-3"><strong>Email:</strong> <span id="detailEmail"></span></div>
                <div class="col-md-3"><strong>Phone:</strong> <span id="detailPhone"></span></div>
                <div class="col-md-3"><strong>Ref:</strong> <span id="detailRef"></span></div>
                </div>
                </div>

                <!-- Products Table -->
                <table class="table table-bordered table-striped" style="margin-top: 15px;" id="saleItemTable">
                <thead>
                <tr>
                <th>Product</th>
                <th width="80px">Available Qty</th>
                <th width="80px">Qty</th>
                <th width="100px">Rate</th>
                <th width="100px">Discount</th>
                <th width="100px">Tax</th>
                <th width="100px">Total</th>
                <th width="5%"></th>
                </tr>
                </thead>
                <tbody></tbody>
                </table>

                <div class="row" style="margin-top:15px;">
                <div class="col-md-2">
                <label>Subtotal</label>
                <input type="number" id="swal_subtotal" class="form-control" readonly value="0" style="background:#f5f5f5;">
                </div>
                <div class="col-md-2">
                <label>Discount</label>
                <input type="number" id="swal_discount" class="form-control" value="${discount}" step="0.01">
                </div>
                <div class="col-md-2">
                <label>Tax</label>
                <input type="number" id="swal_tax" class="form-control" value="${tax}" step="0.01">
                </div>
                <div class="col-md-2">
                <label>Shipping</label>
                <input type="number" id="swal_shipping" class="form-control" value="${shipping}" step="0.01">
                </div>
                <div class="col-md-4">
                <label><strong>Grand Total</strong></label>
                <input type="number" id="swal_grand_total" class="form-control" readonly value="0" style="background:#fff3cd; font-weight:bold;">
                </div>
                </div>
                </form>
            `,
            didOpen: () => {
                setupSaleOrderModal(isEdit, id);
            },
            showCancelButton: true,
            confirmButtonText: isEdit ? 'Update Order' : 'Save Order',
            confirmButtonColor: '#87B87F',
            cancelButtonText: 'Cancel',
            preConfirm: () => validateAndSubmitOrder(isEdit)
        }).then(r => {
            if (r.isConfirmed) saveSaleOrder(r.value);
        });
    }

    function setupSaleOrderModal(isEdit, orderId) {
        // Load products on warehouse change
        $('#swal_warehouse').on('change', function() {
            loadProductsForWarehouse($(this).val());
        });

        // Handle customer selection
        $('#swal_customer').on('change', function() {
            const customerId = $(this).val();
            if (customerId === '') {
                $('#walkinFields').show();
                $('#customerDetails').hide();
            } else {
                $('#walkinFields').hide();
                const customer = window.saleOrderViewData.customers.find(c => c.id == customerId);
                if (customer) {
                    $('#detailName').text(customerName(customer));
                    $('#detailEmail').text(customer.email || 'N/A');
                    $('#detailPhone').text(customer.phone || customer.mobile || 'N/A');
                    $('#detailRef').text(customer.customer_code || 'N/A');
                    $('#customerDetails').show();
                }
            }
        });

        // Add Item button
        document.getElementById('btnAddItem').onclick = function(e) {
            e.preventDefault();
            addSaleOrderRow();
            calculateSaleOrderTotals();
        };

        // Event handlers for rows
        $(document).off('change', '#saleItemTable .item-product');
        $(document).off('input', '#saleItemTable .item-qty, #saleItemTable .item-rate, #saleItemTable .item-discount, #saleItemTable .item-tax');
        $(document).off('click', '#saleItemTable .remove-item');
        $(document).off('input', '#swal_discount, #swal_tax');

        // Product change
        $(document).on('change', '#saleItemTable .item-product', function() {
            let tr = $(this).closest('tr');
            let productId = $(this).val();
            let product = window.saleOrderViewData.products.find(p => p.id == productId);
            if (product) {
                tr.find('.item-rate').val(parseFloat(product.selling_price || 0).toFixed(2));
                tr.find('.available-qty').text(parseFloat(product.available_quantity || 0).toFixed(2));
                tr.find('.item-qty').attr('max', parseFloat(product.available_quantity || 0));
                tr.find('.item-qty').trigger('input');
            }
        });

        // Row calculations
        $(document).on('input', '#saleItemTable .item-qty, #saleItemTable .item-rate, #saleItemTable .item-discount, #saleItemTable .item-tax', function() {
            let tr = $(this).closest('tr');
            calculateSaleOrderRow(tr);
            calculateSaleOrderTotals();
        });

        $(document).on('input', '#swal_discount, #swal_tax, #swal_shipping', calculateSaleOrderTotals);

        // Delete row
        $(document).on('click', '#saleItemTable .remove-item', function() {
            $(this).closest('tr').remove();
            calculateSaleOrderTotals();
        });

        // Load existing items if editing
        if (isEdit && orderId) {
            loadSaleOrderItems(orderId);
        } else {
            addSaleOrderRow();
        }

        // Initial warehouse load
        if ($('#swal_warehouse').val()) {
            loadProductsForWarehouse($('#swal_warehouse').val());
        }
    }

    function loadProductsForWarehouse(warehouseId) {
        if (!warehouseId) return;

        fetch('index.php?r=sale/saleorder&flag=get_products&warehouse_id=' + warehouseId, { method: 'GET' })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    window.saleOrderViewData.products = res.products || [];
                    // Refresh product selects in table
                    updateProductOptions();
                }
            })
            .catch(e => console.error(e));
    }

    function updateProductOptions() {
        let productOptions = '<option value="">-- Select Product --</option>';
        window.saleOrderViewData.products.forEach(p => {
            const available = parseFloat(p.available_quantity || 0).toFixed(2);
            productOptions += `<option value="${p.id}" data-price="${p.selling_price}" data-qty="${available}">${p.product_name} (${p.sku}) - Avail: ${available}</option>`;
        });

        $('#saleItemTable tbody tr').each(function() {
            let select = $(this).find('.item-product');
            let currentVal = select.val();
            select.html(productOptions);
            if (currentVal) select.val(currentVal);
        });
    }

    function addSaleOrderRow(item = {}) {
        let productOptions = '<option value="">-- Select Product --</option>';
        window.saleOrderViewData.products.forEach(p => {
            const available = parseFloat(p.available_quantity || 0).toFixed(2);
            productOptions += `<option value="${p.id}" data-price="${p.selling_price}" data-qty="${available}" ${p.id == (item.product_id||'') ? 'selected' : ''}>${p.product_name} (${p.sku}) - Avail: ${available}</option>`;
        });

        $('#saleItemTable tbody').append(`
            <tr>
            <td><select class="form-control item-product" style="width:100%;">
                ${productOptions}
            </select></td>
            <td><span class="available-qty">${item.available_qty || 0}</span></td>
            <td><input type="number" class="form-control item-qty" value="${item.quantity||1}" min="1" step="0.01" max="999999"></td>
            <td><input type="number" class="form-control item-rate" value="${item.unit_price||0}" step="0.01"></td>
            <td><input type="number" class="form-control item-discount" value="${item.discount||0}" step="0.01"></td>
            <td><input type="number" class="form-control item-tax" value="${item.tax||0}" step="0.01"></td>
            <td><input type="number" class="form-control item-total" readonly value="${item.total||0}" step="0.01"></td>
            <td><button type="button" class="btn btn-danger btn-xs remove-item"><i class="fa fa-trash"></i></button></td>
            </tr>`);
    }

    function calculateSaleOrderRow(tr) {
        let qty = parseFloat(tr.find('.item-qty').val()) || 0;
        let rate = parseFloat(tr.find('.item-rate').val()) || 0;
        let discount = parseFloat(tr.find('.item-discount').val()) || 0;
        let tax = parseFloat(tr.find('.item-tax').val()) || 0;
        let availableQty = parseFloat(tr.find('.available-qty').text()) || 0;

        // Validate qty not exceeding available
        if (qty > availableQty) {
            tr.find('.item-qty').val(availableQty).css('border', '2px solid #ff6b6b');
            qty = availableQty;
            Swal.fire('Warning', `Cannot exceed available quantity of ${availableQty}`, 'warning');
        } else {
            tr.find('.item-qty').css('border', '');
        }

        let total = (qty * rate) - discount + tax;
        tr.find('.item-total').val(Math.max(0, total).toFixed(2));
    }

    function calculateSaleOrderTotals() {
        let subtotal = 0;
        $('#saleItemTable tbody tr').each(function() {
            subtotal += parseFloat($(this).find('.item-total').val()) || 0;
        });

        let discount = parseFloat($('#swal_discount').val()) || 0;
        let tax = parseFloat($('#swal_tax').val()) || 0;
        let shipping = parseFloat($('#swal_shipping').val()) || 0;
        let grand = subtotal + tax + shipping - discount;

        $('#swal_subtotal').val(Math.max(0, subtotal).toFixed(2));
        $('#swal_grand_total').val(Math.max(0, grand).toFixed(2));
    }

    function loadSaleOrderItems(orderId) {
        fetch('index.php?r=sale/saleorder&flag=get_items&id=' + orderId, { method: 'GET' })
            .then(res => res.json())
            .then(res => {
                if (res.success && res.items) {
                    $('#saleItemTable tbody').html('');
                    res.items.forEach(item => addSaleOrderRow(item));
                    calculateSaleOrderTotals();
                }
            })
            .catch(e => console.error(e));
    }

    function validateAndSubmitOrder(isEdit) {
        const customerId = $('#swal_customer').val();
        const warehouseId = $('#swal_warehouse').val();
        const orderDate = $('#swal_order_date').val();
        const items = [];

        if (!warehouseId || !orderDate) {
            Swal.showValidationMessage('Warehouse and Order Date are required');
            return false;
        }

        if (!customerId) {
            const name = $('#swal_customer_name').val().trim();
            const phone = $('#swal_customer_phone').val().trim();
            if (!name || !phone) {
                Swal.showValidationMessage('Walk-in customer requires Name and Phone');
                return false;
            }
        }

        let hasItems = false;
        $('#saleItemTable tbody tr').each(function() {
            let productId = $(this).find('.item-product').val();
            let qty = parseFloat($(this).find('.item-qty').val()) || 0;
            if (productId && qty > 0) {
                items.push({
                    product_id: productId,
                    quantity: qty,
                    unit_price: $(this).find('.item-rate').val(),
                    discount: $(this).find('.item-discount').val(),
                    tax: $(this).find('.item-tax').val(),
                    total: $(this).find('.item-total').val()
                });
                hasItems = true;
            }
        });

        if (!hasItems) {
            Swal.showValidationMessage('Add at least one product');
            return false;
        }

        return {
            id: $('#swal_id').val(),
            customer_id: customerId,
            customer_name: $('#swal_customer_name').val(),
            customer_email: $('#swal_customer_email').val(),
            customer_phone: $('#swal_customer_phone').val(),
            customer_reference: $('#swal_customer_reference').val(),
            warehouse_id: warehouseId,
            order_date: orderDate,
            delivery_date: $('#swal_delivery_date').val(),
            order_status: $('#swal_order_status').val(),
            payment_status: $('#swal_payment_status').val(),
            discount: $('#swal_discount').val(),
            tax: $('#swal_tax').val(),
            shipping: $('#swal_shipping').val(),
            grand_total: $('#swal_grand_total').val(),
            notes: $('#swal_notes').val(),
            items: JSON.stringify(items),
            flag: isEdit ? 'update' : 'create'
        };
    }

    function saveSaleOrder(data) {
        Swal.fire({
            title: 'Processing...',
            text: 'Saving sale order',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const formData = new FormData();
        Object.keys(data).forEach(key => formData.append(key, data[key]));
        formData.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');

        fetch('index.php?r=sale/saleorder', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            Swal.close();
            if (res.success) {
                Swal.fire('Success', res.message, 'success').then(() => searchform());
            } else {
                Swal.fire('Error', res.message || 'Failed to save order', 'error');
            }
        })
        .catch(e => {
            Swal.close();
            Swal.fire('Error', 'Unable to save order', 'error');
            console.error(e);
        });
    }

    function saveOrder(formData) {

        Swal.fire({
            title: 'Saving Sales Order...',
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

        fetch('index.php?r=sale/salesorders', {
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
</script>
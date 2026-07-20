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
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="showOrderModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Sales Order
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
                    <button onclick='showOrderModal(${JSON.stringify(item)})' title="Edit">
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

    function showOrderModal(orderData = null) {
        const isEdit = orderData !== null;
        const id = isEdit ? orderData.id : '';
        const customerId = isEdit ? orderData.customer_id : '';
        const warehouseId = isEdit ? orderData.warehouse_id : '';
        const orderDate = isEdit ? orderData.order_date : '<?= date('Y-m-d') ?>';
        const deliveryDate = isEdit ? (orderData.delivery_date ?? '') : '';
        const orderStatus = isEdit ? orderData.order_status : 'Draft';
        const paymentStatus = isEdit ? orderData.payment_status : 'Pending';
        const subtotal = isEdit ? orderData.subtotal : 0;
        const discount = isEdit ? orderData.discount : 0;
        const tax = isEdit ? orderData.tax : 0;
        const shipping = isEdit ? orderData.shipping : 0;
        const grandTotal = isEdit ? orderData.grand_total : 0;
        const notes = isEdit ? (orderData.notes ?? '') : '';

        let customerOptions = '<option value="">Select Customer</option>';
        customers.forEach(function(item) {
            customerOptions += `<option value="${item.id}" ${item.id==customerId?'selected':''}>${customerName(item)}</option>`;
        });

        let warehouseOptions = '';
        warehouses.forEach(function(item) {
            warehouseOptions += `<option value="${item.id}" ${item.id==warehouseId?'selected':''}>${item.warehouse_name}</option>`;
        });

        const orderStatusList = ['Draft', 'Confirmed', 'Packed', 'Dispatched', 'Delivered', 'Cancelled'];
        let orderStatusOptions = '';
        orderStatusList.forEach(function(s) {
            orderStatusOptions += `<option value="${s}" ${s==orderStatus?'selected':''}>${s}</option>`;
        });

        const paymentStatusList = ['Pending', 'Partial', 'Paid'];
        let paymentStatusOptions = '';
        paymentStatusList.forEach(function(s) {
            paymentStatusOptions += `<option value="${s}" ${s==paymentStatus?'selected':''}>${s}</option>`;
        });

        Swal.fire({
            title: isEdit ? 'Update Sales Order' : 'Add Sales Order',
            width: '900px',
            customClass: {
                popup: 'swal-wide-popup'
            },
            didOpen: () => {
                $('#swal_customer').chosen({
                    width: '100%',
                    search_contains: true
                });
                $('#swal_warehouse').chosen({
                    width: '100%',
                    search_contains: true
                });
                $('#swal_subtotal,#swal_discount,#swal_tax,#swal_shipping').on('input', calcOrderGrandTotal);
            },
            html: `
                <form id="orderForm">

                <input type="hidden" id="swal_id" value="${id}">

                <div class="row">
                <div class="col-md-6">
                <label>Customer</label>
                <select id="swal_customer" class="form-control chzn-select-modal">
                ${customerOptions}
                </select>
                </div>

                <div class="col-md-6">
                <label>Warehouse</label>
                <select id="swal_warehouse" class="form-control chzn-select-modal">
                ${warehouseOptions}
                </select>
                </div>
                </div>

                <div class="row">
                <div class="col-md-4">
                <label>Order Date</label>
                <input type="date" id="swal_order_date" class="form-control" value="${orderDate}">
                </div>

                <div class="col-md-4">
                <label>Delivery Date</label>
                <input type="date" id="swal_delivery_date" class="form-control" value="${deliveryDate}">
                </div>

                <div class="col-md-4">
                <label>Order Status</label>
                <select id="swal_order_status" class="form-control">
                ${orderStatusOptions}
                </select>
                </div>
                </div>

                <div class="row">
                <div class="col-md-3">
                <label>Subtotal</label>
                <input type="number" step="0.01" id="swal_subtotal" class="form-control" value="${subtotal||0}">
                </div>

                <div class="col-md-3">
                <label>Discount</label>
                <input type="number" step="0.01" id="swal_discount" class="form-control" value="${discount||0}">
                </div>

                <div class="col-md-3">
                <label>Tax</label>
                <input type="number" step="0.01" id="swal_tax" class="form-control" value="${tax||0}">
                </div>

                <div class="col-md-3">
                <label>Shipping</label>
                <input type="number" step="0.01" id="swal_shipping" class="form-control" value="${shipping||0}">
                </div>
                </div>

                <div class="row">
                <div class="col-md-4">
                <label>Grand Total</label>
                <input type="number" step="0.01" readonly id="swal_grand_total" class="form-control" value="${grandTotal||0}">
                </div>

                <div class="col-md-4">
                <label>Payment Status</label>
                <select id="swal_payment_status" class="form-control">
                ${paymentStatusOptions}
                </select>
                </div>

                <div class="col-md-4">
                <label>Notes</label>
                <input type="text" id="swal_notes" class="form-control" value="${notes}">
                </div>
                </div>

                </form>
                `,
            showCancelButton: true,
            confirmButtonText: isEdit ? 'Update Order' : 'Save Order',
            confirmButtonColor: '#87B87F',
            cancelButtonText: 'Cancel',

            preConfirm: () => {

                calcOrderGrandTotal();

                if ($('#swal_customer').val() == '' || $('#swal_warehouse').val() == '' || $('#swal_order_date').val() == '') {
                    Swal.showValidationMessage('Customer, Warehouse and Order Date are required');
                    return false;
                }

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
                    notes: $('#swal_notes').val(),
                    flag: 'save'
                };

            }

        }).then(function(result) {

            if (result.isConfirmed) {
                saveOrder(result.value);
            }

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
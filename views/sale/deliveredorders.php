<?php

use yii\helpers\Html;

if (!isset($deliveredOrders)) $deliveredOrders = [];
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
                <li class="active">Delivered Orders</li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="delivered_search" onsubmit="return false;">

                <input type="text" name="order_number" id="order_number" class="new-input" style="width:16%;" placeholder="Order Number">

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

                <input type="date" name="from_date" id="from_date" class="new-input" style="width:13%;">
                <input type="date" name="to_date" id="to_date" class="new-input" style="width:13%;">

                <input type="text" name="per_page" id="per_page" value="20" class="new-input" style="width:7%;" placeholder="Records?">

                <input type="button" class="btn btn-primary"
                    onclick="searchform()"
                    value="Search"
                    style="height:30px;padding:0;margin-top:-3px;" />

            </form>
        </div>

        <div class="widget-main">

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="delivered_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Warehouse</th>
                            <th>Delivery Date</th>
                            <th>Payment Status</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Tax</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deliveredOrders as $key => $item) {
                            $subtotal = (float)($item['subtotal'] ?? 0);
                            $discount = (float)($item['discount'] ?? 0);
                            $tax = (float)($item['tax'] ?? 0);
                            $grandTotal = (float)($item['grand_total'] ?? 0);
                            $paidAmount = (float)($item['paid_amount'] ?? 0);
                            $remainingAmount = $grandTotal - $paidAmount;
                        ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= Html::encode($item['order_number']) ?></td>
                                <td><?= Html::encode($item['company_name'] ?: trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''))) ?></td>
                                <td><?= Html::encode($item['warehouse_name']) ?></td>
                                <td><?= Html::encode($item['delivery_date']) ?></td>
                                <td><?= Html::encode($item['payment_status']) ?></td>
                                <td><?= number_format($subtotal, 2) ?></td>
                                <td><?= number_format($discount, 2) ?></td>
                                <td><?= number_format($tax, 2) ?></td>
                                <td><strong><?= number_format($grandTotal, 2) ?></strong></td>
                                <td><?= number_format($paidAmount, 2) ?></td>
                                <td><?= number_format($remainingAmount, 2) ?></td>
                                <td>
                                    <?php if ($item['payment_status'] != 'Paid') { ?>
                                        <button onclick="markPaid(<?= $item['id'] ?>)">
                                            <i class="fa fa-money"></i> Mark Paid
                                        </button>
                                    <?php } else { ?>
                                        <span class="label label-success">Paid</span>
                                    <?php } ?>
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

<script>
    searchform();

    function customerName(item) {
        return item.company_name || ((item.first_name || '') + ' ' + (item.last_name || ''));
    }

    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading Delivered Orders...',
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
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=sale/deliveredorders', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderDelivered(res.deliveredOrders);
                    renderPagination(res.page, res.totalPages);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load delivered orders.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });

    }

    function renderDelivered(rows) {

        let html = '';

        if (rows.length == 0) {

            html = `
        <tr>
            <td colspan="13" class="text-center">
                No Delivered Orders Found
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

                let actionHtml = item.payment_status != 'Paid' ?
                    `<button onclick="markPaid(${item.id})"><i class="fa fa-money"></i> Mark Paid</button>` :
                    `<span class="label label-success">Paid</span>`;

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.order_number}</td>
                <td>${customerName(item)}</td>
                <td>${item.warehouse_name??''}</td>
                <td>${item.delivery_date??''}</td>
                <td>${item.payment_status??''}</td>
                <td>${subtotal.toFixed(2)}</td>
                <td>${discount.toFixed(2)}</td>
                <td>${tax.toFixed(2)}</td>
                <td><strong>${grandTotal.toFixed(2)}</strong></td>
                <td>${paidAmount.toFixed(2)}</td>
                <td>${remainingAmount.toFixed(2)}</td>
                <td>${actionHtml}</td>
            </tr>`;
            });

        }

        $('#delivered_table tbody').html(html);

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

    function markPaid(id) {

        Swal.fire({
            title: 'Mark this order as Paid?',
            text: 'The payment status will be changed to Paid.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#87B87F',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Mark Paid'
        }).then(function(result) {

            if (!result.isConfirmed) {
                return;
            }

            const data = new FormData();

            data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
            data.append('flag', 'markpaid');
            data.append('id', id);

            fetch('index.php?r=sale/deliveredorders', {
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
                    Swal.fire('Error', 'Unable to update order.', 'error');
                });

        });

    }
</script>
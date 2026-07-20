<?php

use yii\helpers\Html;

if (!isset($customers)) $customers = [];
if (!isset($sales)) $sales = [];
$customer_id = $customer_id ?? 0;
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=customers/customerdashboard">Home</a>
                </li>
                <li class="active">Sales History</li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="sales_search" onsubmit="return false;">

                <select name="customer_id" id="customer_id" class="new-input" style="width:30%;" onchange="loadSalesHistory()">
                    <option value="">-- Select Customer --</option>
                    <?php foreach ($customers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['name']) ?></option>
                    <?php } ?>
                </select>

                <input type="date" name="from_date" id="from_date" class="new-input" style="width:12%;" onchange="loadSalesHistory()">
                <input type="date" name="to_date" id="to_date" class="new-input" style="width:12%;" onchange="loadSalesHistory()">

                <input type="text" name="per_page" id="per_page" value="20" class="new-input" style="width:6%;" placeholder="Records?">

                <input type="button" class="btn btn-primary"
                    onclick="searchform()"
                    value="Search"
                    style="height:30px;padding:0;margin-top:-3px;" />

            </form>
        </div>

        <div class="widget-main">

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="sales_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order Number</th>
                            <th>Order Date</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Order Status</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="9" class="text-center">Select a customer to view sales history</td>
                        </tr>
                    </tbody>
                </table>

                <div id="paginationArea" class="text-center"></div>

            </div>

        </div>

    </div>
</div>

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
    let customers = <?= json_encode($customers) ?>;

    function loadSalesHistory() {
        const customerId = $('#customer_id').val();
        if (!customerId) {
            $('#sales_table tbody').html('<tr><td colspan="9" class="text-center">Select a customer to view sales history</td></tr>');
            return;
        }
        searchform();
    }

    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading Sales History...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'load');
        data.append('customer_id', $('#customer_id').val());
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=customers/saleshistory', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderSales(res.sales);
                    renderPagination(res.page, res.totalPages);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load sales history.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });
    }

    function renderSales(rows) {

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

                const orderStatusMap = {
                    'Draft': 'default',
                    'Confirmed': 'primary',
                    'Packed': 'warning',
                    'Dispatched': 'info',
                    'Delivered': 'success',
                    'Cancelled': 'danger'
                };

                const paymentStatusMap = {
                    'Pending': 'danger',
                    'Partial': 'warning',
                    'Paid': 'success'
                };

                const orderClass = orderStatusMap[item.order_status] || 'default';
                const paymentClass = paymentStatusMap[item.payment_status] || 'default';

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.order_number}</td>
                <td>${item.order_date??''}</td>
                <td>${item.customer_name??''}</td>
                <td><span class="label label-default">${item.items_count || 0}</span></td>
                <td>${parseFloat(item.grand_total||0).toFixed(2)}</td>
                <td><span class="label label-${orderClass}">${item.order_status}</span></td>
                <td><span class="label label-${paymentClass}">${item.payment_status}</span></td>
                <td>
                    <button onclick='viewSaleDetails(${JSON.stringify(item)})'>
                        <i class="fa fa-eye"></i>
                    </button>
                </td>
            </tr>`;

            });

        }

        $('#sales_table tbody').html(html);

    }

    function renderPagination(page, totalPages) {
        if (totalPages <= 1) {
            $('#paginationArea').html('');
            return;
        }
        let html = '<nav><ul class="pagination">';
        if (page > 1) {
            html += `<li class="page-item"><a class="page-link" onclick="searchform(1)">First</a></li>`;
            html += `<li class="page-item"><a class="page-link" onclick="searchform(${page - 1})">Previous</a></li>`;
        }
        for (let i = Math.max(1, page - 2); i <= Math.min(totalPages, page + 2); i++) {
            if (i === page) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link" onclick="searchform(${i})">${i}</a></li>`;
            }
        }
        if (page < totalPages) {
            html += `<li class="page-item"><a class="page-link" onclick="searchform(${page + 1})">Next</a></li>`;
            html += `<li class="page-item"><a class="page-link" onclick="searchform(${totalPages})">Last</a></li>`;
        }
        html += '</ul></nav>';
        $('#paginationArea').html(html);
    }

    function viewSaleDetails(item) {
        Swal.fire({
            title: 'Sale Order Details',
            html: `
                <div style="text-align:left;padding:15px;">
                    <p><strong>Order Number:</strong> ${item.order_number}</p>
                    <p><strong>Order Date:</strong> ${item.order_date}</p>
                    <p><strong>Order Status:</strong> ${item.order_status}</p>
                    <p><strong>Payment Status:</strong> ${item.payment_status}</p>
                    <p><strong>Grand Total:</strong> $${parseFloat(item.grand_total).toFixed(2)}</p>
                    <p><strong>Remarks:</strong> ${item.notes || 'N/A'}</p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Close'
        });
    }
</script>

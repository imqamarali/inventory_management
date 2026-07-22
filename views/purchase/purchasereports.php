<?php

use yii\helpers\Html;

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
                <li class="active">Purchase Reports</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="printReport()">
                                <i class="ace-icon fa fa-print"></i>
                                Print
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="report_search" onsubmit="return false;">

                <input type="date" name="from_date" value="<?= date('Y-m-d') ?>" id="from_date" class="new-input" style="width:14%;">
                <input type="date" name="to_date" value="<?= date('Y-m-d') ?>" id="to_date" class="new-input" style="width:14%;">

                <select name="supplier_id" id="supplier_id" class="new-input" style="width:18%;">
                    <option value="">All Suppliers</option>
                    <?php foreach ($suppliers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['company_name']) ?></option>
                    <?php } ?>
                </select>

                <select name="warehouse_id" id="warehouse_id" class="new-input" style="width:18%;">
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

                <input type="button" class="btn btn-primary"
                    onclick="searchform()"
                    value="Search"
                    style="height:30px;padding:0;margin-top:-3px;" />

            </form>
        </div>

        <div class="widget-main">

            <div class="stats-grid" style="display:flex;gap:15px;margin-bottom:15px;">
                <div class="stat-card blue" style="flex:1;">
                    <div class="stat-header">
                        <span class="stat-title">Total Orders</span>
                        <div class="stat-icon"><i class="fa fa-shopping-cart"></i></div>
                    </div>
                    <div class="stat-value" id="total_orders">0</div>
                </div>
                <div class="stat-card green" style="flex:1;">
                    <div class="stat-header">
                        <span class="stat-title">Total Amount</span>
                        <div class="stat-icon"><i class="fa fa-money"></i></div>
                    </div>
                    <div class="stat-value" id="total_amount">PKR 0</div>
                </div>
                <div class="stat-card orange" style="flex:1;">
                    <div class="stat-header">
                        <span class="stat-title">Average Amount</span>
                        <div class="stat-icon"><i class="fa fa-line-chart"></i></div>
                    </div>
                    <div class="stat-value" id="average_amount">PKR 0</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="dashboard-box">
                        <h4><i class="fa fa-pie-chart"></i> Status Summary</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Orders</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="statusSummaryBody">
                                    <tr>
                                        <td colspan="3" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dashboard-box">
                        <h4><i class="fa fa-truck"></i> Supplier Summary</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Orders</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="supplierSummaryBody">
                                    <tr>
                                        <td colspan="3" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dashboard-box">
                        <h4><i class="fa fa-building"></i> Warehouse Summary</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Warehouse</th>
                                        <th>Orders</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="warehouseSummaryBody">
                                    <tr>
                                        <td colspan="3" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-box" style="margin-top:15px;">
                <h4><i class="fa fa-list"></i> Detailed Purchase Report</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="report_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>PO Number</th>
                                <th>Supplier</th>
                                <th>Warehouse</th>
                                <th>Order Date</th>
                                <th>Status</th>
                                <th class="text-right">Grand Total</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <tr>
                                <td colspan="7" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    searchform();

    function searchform() {

        Swal.fire({
            title: 'Loading Report...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'load');
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('supplier_id', $('#supplier_id').val());
        data.append('warehouse_id', $('#warehouse_id').val());
        data.append('status', $('#status').val());

        fetch('index.php?r=purchase/purchasereports', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    loadSummary(res.summary);
                    loadStatusSummary(res.statusSummary);
                    loadSupplierSummary(res.supplierSummary);
                    loadWarehouseSummary(res.warehouseSummary);
                    loadReportTable(res.purchaseReport);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load report.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });

    }

    function loadSummary(summary) {
        $('#total_orders').text(Number(summary.total_orders || 0).toLocaleString());
        $('#total_amount').text('PKR ' + Number(summary.total_amount || 0).toLocaleString());
        $('#average_amount').text('PKR ' + Number(summary.average_amount || 0).toLocaleString(undefined, {maximumFractionDigits: 2}));
    }

    function loadStatusSummary(rows) {
        let html = '';
        if (!rows || rows.length == 0) {
            html = '<tr><td colspan="3" class="text-center">No Data Found</td></tr>';
        } else {
            rows.forEach(function(row) {
                html += `<tr>
                    <td>${row.status}</td>
                    <td>${row.total_orders}</td>
                    <td class="text-right">${Number(row.total_amount).toLocaleString()}</td>
                </tr>`;
            });
        }
        $('#statusSummaryBody').html(html);
    }

    function loadSupplierSummary(rows) {
        let html = '';
        if (!rows || rows.length == 0) {
            html = '<tr><td colspan="3" class="text-center">No Data Found</td></tr>';
        } else {
            rows.forEach(function(row) {
                html += `<tr>
                    <td>${row.company_name??'N/A'}</td>
                    <td>${row.total_orders}</td>
                    <td class="text-right">${Number(row.total_amount).toLocaleString()}</td>
                </tr>`;
            });
        }
        $('#supplierSummaryBody').html(html);
    }

    function loadWarehouseSummary(rows) {
        let html = '';
        if (!rows || rows.length == 0) {
            html = '<tr><td colspan="3" class="text-center">No Data Found</td></tr>';
        } else {
            rows.forEach(function(row) {
                html += `<tr>
                    <td>${row.warehouse_name??'N/A'}</td>
                    <td>${row.total_orders}</td>
                    <td class="text-right">${Number(row.total_amount).toLocaleString()}</td>
                </tr>`;
            });
        }
        $('#warehouseSummaryBody').html(html);
    }

    function loadReportTable(rows) {
        let html = '';
        if (!rows || rows.length == 0) {
            html = '<tr><td colspan="7" class="text-center">No Purchase Orders Found</td></tr>';
        } else {
            rows.forEach(function(row, index) {
                html += `<tr>
                    <td>${index+1}</td>
                    <td>${row.po_number}</td>
                    <td>${row.company_name??''}</td>
                    <td>${row.warehouse_name??''}</td>
                    <td>${row.order_date??''}</td>
                    <td>${row.status}</td>
                    <td class="text-right">${parseFloat(row.grand_total).toFixed(2)}</td>
                </tr>`;
            });
        }
        $('#reportTableBody').html(html);
    }

    function printReport() {
        // Get filter values
        const fromDate = $('#from_date').val() || '';
        const toDate = $('#to_date').val() || '';
        const supplierId = $('#supplier_id').val() || '';

        // Build URL with filters
        const params = new URLSearchParams();
        if (fromDate) params.append('from_date', fromDate);
        if (toDate) params.append('to_date', toDate);
        if (supplierId) params.append('supplier_id', supplierId);

        // Open PDF in new tab
        const pdfUrl = 'index.php?r=reports/purchasereportpdf&' + params.toString();
        window.open(pdfUrl, '_blank');
    }
</script>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=purchase/purchasedashboard">Home</a>
                </li>
                <li class="active">Purchase Analytics</li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="analytics_search" onsubmit="return false;">

                <input type="date" name="from_date" value="<?= date('Y-m-d') ?>" id="from_date" class="new-input" style="width:14%;">
                <input type="date" name="to_date" value="<?= date('Y-m-d') ?>" id="to_date" class="new-input" style="width:14%;">

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
                    <div class="stat-value" id="total_purchase_orders">0</div>
                </div>
                <div class="stat-card green" style="flex:1;">
                    <div class="stat-header">
                        <span class="stat-title">Total Amount</span>
                        <div class="stat-icon"><i class="fa fa-money"></i></div>
                    </div>
                    <div class="stat-value" id="total_purchase_amount">PKR 0</div>
                </div>
                <div class="stat-card orange" style="flex:1;">
                    <div class="stat-header">
                        <span class="stat-title">Average Purchase</span>
                        <div class="stat-icon"><i class="fa fa-line-chart"></i></div>
                    </div>
                    <div class="stat-value" id="average_purchase">PKR 0</div>
                </div>
                <div class="stat-card purple" style="flex:1;">
                    <div class="stat-header">
                        <span class="stat-title">Suppliers</span>
                        <div class="stat-icon"><i class="fa fa-truck"></i></div>
                    </div>
                    <div class="stat-value" id="total_suppliers">0</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="dashboard-box">
                        <h4><i class="fa fa-pie-chart"></i> Orders By Status</h4>
                        <canvas id="statusChart" height="220"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="dashboard-box">
                        <h4><i class="fa fa-bar-chart"></i> Top Suppliers</h4>
                        <canvas id="supplierChart" height="220"></canvas>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top:15px;">
                <div class="col-md-6">
                    <div class="dashboard-box">
                        <h4><i class="fa fa-building"></i> Purchases By Warehouse</h4>
                        <canvas id="warehouseChart" height="220"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="dashboard-box">
                        <h4><i class="fa fa-line-chart"></i> Monthly Purchase Trend</h4>
                        <canvas id="monthlyChart" height="220"></canvas>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top:15px;">
                <div class="col-md-6">
                    <div class="dashboard-box">
                        <h4><i class="fa fa-cubes"></i> Top Products By Quantity</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="topProductsBody">
                                    <tr>
                                        <td colspan="3" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="dashboard-box">
                        <h4><i class="fa fa-clock-o"></i> Recent Purchases</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>PO Number</th>
                                        <th>Supplier</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="recentPurchasesBody">
                                    <tr>
                                        <td colspan="5" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    if (typeof Chart === 'undefined') {
        document.write('<scr' + 'ipt src="https://cdn.jsdelivr.net/npm/chart.js"><\/scr' + 'ipt>');
    }
</script>
<script>
    var statusChart = null;
    var supplierChart = null;
    var warehouseChart = null;
    var monthlyChart = null;

    searchform();

    function searchform() {

        Swal.fire({
            title: 'Loading Analytics...',
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

        fetch('index.php?r=purchase/purchaseanalytics', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    loadStats(res.stats);
                    loadStatusChart(res.statusChart);
                    loadSupplierChart(res.supplierChart);
                    loadWarehouseChart(res.warehouseChart);
                    loadMonthlyChart(res.monthlyChart);
                    loadTopProducts(res.topProducts);
                    loadRecentPurchases(res.recentPurchases);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load analytics.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });

    }

    function loadStats(stats) {
        $('#total_purchase_orders').text(Number(stats.total_purchase_orders || 0).toLocaleString());
        $('#total_purchase_amount').text('PKR ' + Number(stats.total_purchase_amount || 0).toLocaleString());
        $('#average_purchase').text('PKR ' + Number(stats.average_purchase || 0).toLocaleString(undefined, {maximumFractionDigits: 2}));
        $('#total_suppliers').text(Number(stats.total_suppliers || 0).toLocaleString());
    }

    function loadStatusChart(data) {
        if (statusChart) statusChart.destroy();

        let labels = [], values = [];
        (data || []).forEach(function(row) {
            labels.push(row.status);
            values.push(parseInt(row.total));
        });

        statusChart = new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#3498db', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#e74c3c']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    function loadSupplierChart(data) {
        if (supplierChart) supplierChart.destroy();

        let labels = [], values = [];
        (data || []).forEach(function(row) {
            labels.push(row.company_name);
            values.push(parseFloat(row.total_amount));
        });

        supplierChart = new Chart(document.getElementById('supplierChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Amount',
                    data: values,
                    backgroundColor: '#3498db'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function loadWarehouseChart(data) {
        if (warehouseChart) warehouseChart.destroy();

        let labels = [], values = [];
        (data || []).forEach(function(row) {
            labels.push(row.warehouse_name);
            values.push(parseFloat(row.total_amount));
        });

        warehouseChart = new Chart(document.getElementById('warehouseChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Amount',
                    data: values,
                    backgroundColor: '#9b59b6'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function loadMonthlyChart(data) {
        if (monthlyChart) monthlyChart.destroy();

        let labels = [], values = [];
        (data || []).forEach(function(row) {
            labels.push(row.month);
            values.push(parseFloat(row.total_amount));
        });

        monthlyChart = new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Purchase Amount',
                    data: values,
                    fill: true,
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39,174,96,.15)',
                    tension: .4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function loadTopProducts(rows) {
        let html = '';
        if (!rows || rows.length == 0) {
            html = '<tr><td colspan="3" class="text-center">No Data Found</td></tr>';
        } else {
            rows.forEach(function(row) {
                html += `<tr>
                    <td>${row.product_name??'N/A'}</td>
                    <td class="text-right">${Number(row.quantity || 0).toLocaleString()}</td>
                    <td class="text-right">${Number(row.total_amount || 0).toLocaleString()}</td>
                </tr>`;
            });
        }
        $('#topProductsBody').html(html);
    }

    function loadRecentPurchases(rows) {
        let html = '';
        if (!rows || rows.length == 0) {
            html = '<tr><td colspan="5" class="text-center">No Purchases Found</td></tr>';
        } else {
            rows.forEach(function(row) {
                html += `<tr>
                    <td>${row.po_number}</td>
                    <td>${row.company_name??''}</td>
                    <td>${row.order_date??''}</td>
                    <td>${row.status}</td>
                    <td class="text-right">${parseFloat(row.grand_total).toFixed(2)}</td>
                </tr>`;
            });
        }
        $('#recentPurchasesBody').html(html);
    }
</script>

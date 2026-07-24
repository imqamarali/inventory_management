<div class="page-content">

    <div class="dashboard-header">

        <div>
            <h3>
                <i class="fa fa-users"></i>
                Customer Dashboard
                <small>Customer Overview & Analytics</small>
            </h3>
        </div>

        <div>
            <button id="refreshDashboard">
                <i class="fa fa-refresh"></i>
                Refresh
            </button>
        </div>

    </div>

    <div class="stats-grid">

        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Customers</span>
                <div class="stat-icon"><i class="fa fa-users"></i></div>
            </div>
            <div class="stat-value" id="total_customers">...</div>
            <div class="stat-subtitle">Total Customers</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Active</span>
                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="stat-value" id="active_customers">...</div>
            <div class="stat-subtitle">Active Customers</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">System</span>
                <div class="stat-icon"><i class="fa fa-building"></i></div>
            </div>
            <div class="stat-value" id="system_customers">...</div>
            <div class="stat-subtitle">System Customers</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Walk-in</span>
                <div class="stat-icon"><i class="fa fa-user"></i></div>
            </div>
            <div class="stat-value" id="walkin_customers">...</div>
            <div class="stat-subtitle">Walk-in Customers</div>
        </div>

        <div class="stat-card teal">
            <div class="stat-header">
                <span class="stat-title">Orders</span>
                <div class="stat-icon"><i class="fa fa-shopping-cart"></i></div>
            </div>
            <div class="stat-value" id="total_orders">...</div>
            <div class="stat-subtitle">Total Orders</div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Paid</span>
                <div class="stat-icon"><i class="fa fa-money"></i></div>
            </div>
            <div class="stat-value" id="total_paid">...</div>
            <div class="stat-subtitle">Total Paid</div>
        </div>

        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Balance</span>
                <div class="stat-icon"><i class="fa fa-credit-card"></i></div>
            </div>
            <div class="stat-value" id="total_balance">...</div>
            <div class="stat-subtitle">Outstanding Balance</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Sales Value</span>
                <div class="stat-icon"><i class="fa fa-line-chart"></i></div>
            </div>
            <div class="stat-value" id="total_sales_amount">...</div>
            <div class="stat-subtitle">Total Sales Amount</div>
        </div>

    </div>
    <div class="row">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-bar-chart"></i>
                    Top Customer Balances
                </h4>

                <canvas id="customerChart" height="220"></canvas>

            </div>

        </div>

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-pie-chart"></i>
                    Payment Methods
                </h4>

                <canvas id="paymentMethodChart" height="220"></canvas>

            </div>

        </div>

    </div>

    <div class="row" style="margin-top:15px;">

        <div class="col-md-12">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-line-chart"></i>
                    Monthly Sales
                </h4>

                <canvas id="monthlySalesChart" height="100"></canvas>

            </div>

        </div>

    </div>

    <div class="row" style="margin-top:15px;">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-money"></i>
                    Latest Customer Payments
                </h4>

                <div class="table-responsive">

                    <table class="table table-striped table-bordered table-hover">

                        <thead>
                            <tr>
                                <th>Payment #</th>
                                <th>Customer</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody id="latestPaymentsBody">
                            <tr>
                                <td colspan="5" class="text-center">Loading...</td>
                            </tr>
                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-shopping-cart"></i>
                    Recent Sales Orders
                </h4>

                <div class="table-responsive">

                    <table class="table table-striped table-bordered table-hover">

                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody id="recentSalesBody">
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    var customerChart = null;
    var paymentMethodChart = null;
    var monthlySalesChart = null;
    $(function() {

        loadDashboard();

        $("#refreshDashboard").click(function() {

            loadDashboard();

        });

    });

    function loadDashboard() {

        showDashboardLoading();

        $.ajax({

            url: "<?= Yii::$app->urlManager->createUrl('customers/customerdashboard') ?>",

            type: "POST",

            dataType: "json",

            data: {
                flag: "load_dashboard"
            },

            success: function(response) {

                hideDashboardLoading();

                if (response.success) {

                    loadStatistics(response.stats);

                    loadCustomerChart(response.customerChart);

                    loadPaymentMethodChart(response.paymentMethodChart);

                    loadMonthlySalesChart(response.monthlySales);

                    loadLatestPayments(response.latestPayments);

                    loadRecentSales(response.recentSales);

                } else {

                    alert(response.message);

                }

            },

            error: function() {

                hideDashboardLoading();

                alert("Unable to load dashboard.");

            }

        });

    }

    function showDashboardLoading() {

        $(".stat-value").each(function() {

            $(this).addClass("loading").html("&nbsp;&nbsp;&nbsp;&nbsp;");

        });

    }

    function hideDashboardLoading() {

        $(".stat-value").removeClass("loading");

    }

    function loadStatistics(stats) {

        animateCounter("#total_customers", stats.total_customers);

        animateCounter("#active_customers", stats.active_customers);

        animateCounter("#system_customers", stats.system_customers);

        animateCounter("#walkin_customers", stats.walkin_customers);

        animateCounter("#total_orders", stats.total_orders);

        animateCurrency("#total_paid", stats.total_paid);

        animateCurrency("#total_balance", stats.total_balance);

        animateCurrency("#total_sales_amount", stats.total_sales_amount);

    }

    function animateCounter(id, value) {

        $({
            count: 0
        }).animate({
            count: value
        }, {
            duration: 700,
            easing: "swing",
            step: function() {
                $(id).text(Math.floor(this.count).toLocaleString());
            },
            complete: function() {
                $(id).text(Number(value).toLocaleString());
            }
        });

    }

    function animateCurrency(id, value) {

        $({
            count: 0
        }).animate({
            count: value
        }, {
            duration: 700,
            easing: "swing",
            step: function() {
                $(id).text("PKR " + Math.floor(this.count).toLocaleString());
            },
            complete: function() {
                $(id).text("PKR " + Number(value).toLocaleString());
            }
        });
    }

    function loadCustomerChart(data) {

        const labels = data.map(x => x.name);
        const values = data.map(x => parseFloat(x.current_balance));

        if (customerChart) {
            customerChart.destroy();
        }

        customerChart = new Chart(document.getElementById("customerChart"), {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: "Outstanding Balance",
                    data: values,
                    backgroundColor: "#4F81BD",
                    borderColor: "#3A6EA5",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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

    function loadPaymentMethodChart(data) {

        const labels = data.map(x => x.payment_method);
        const values = data.map(x => parseInt(x.total));

        if (paymentMethodChart) {
            paymentMethodChart.destroy();
        }

        paymentMethodChart = new Chart(document.getElementById("paymentMethodChart"), {
            type: "pie",
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: [
                        "#5B9BD5",
                        "#70AD47",
                        "#FFC000",
                        "#ED7D31",
                        "#A5A5A5",
                        "#4472C4"
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom"
                    }
                }
            }
        });

    }

    function loadMonthlySalesChart(data) {

        const labels = data.map(x => x.month);
        const values = data.map(x => parseFloat(x.total));

        if (monthlySalesChart) {
            monthlySalesChart.destroy();
        }

        monthlySalesChart = new Chart(document.getElementById("monthlySalesChart"), {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: "Sales Amount",
                    data: values,
                    fill: false,
                    borderColor: "#5B9BD5",
                    backgroundColor: "#5B9BD5",
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
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

    function loadLatestPayments(data) {

        let html = '';

        if (data.length === 0) {

            html = '<tr><td colspan="5" class="text-center">No payments found.</td></tr>';

        } else {

            $.each(data, function(i, row) {

                html += `
<tr>
<td>${row.payment_no}</td>
<td>${row.customer_name}</td>
<td>${row.payment_method}</td>
<td>PKR ${Number(row.amount).toLocaleString()}</td>
<td>${row.payment_date}</td>
</tr>
`;

            });

        }

        $("#latestPaymentsBody").html(html);

    }

    function loadRecentSales(data) {

        let html = '';

        if (data.length === 0) {

            html = '<tr><td colspan="5" class="text-center">No sales orders found.</td></tr>';

        } else {

            $.each(data, function(i, row) {

                let badge = 'label-default';

                if (row.order_status === 'Delivered') {
                    badge = 'label-success';
                } else if (row.order_status === 'Draft') {
                    badge = 'label-warning';
                } else if (row.order_status === 'Confirmed') {
                    badge = 'label-info';
                } else if (row.order_status === 'Cancelled') {
                    badge = 'label-danger';
                } else if (row.order_status === 'Dispatched') {
                    badge = 'label-primary';
                } else if (row.order_status === 'Packed') {
                    badge = 'label-secondary';
                }

                html += `
                    <tr>
                    <td>${row.order_number}</td>
                    <td>${row.customer_name}</td>
                    <td><span class="label ${badge}">${row.order_status}</span></td>
                    <td>PKR ${Number(row.grand_total).toLocaleString()}</td>
                    <td>${row.order_date}</td>
                    </tr>
                    `;

            });

        }

        $("#recentSalesBody").html(html);

    }
</script>
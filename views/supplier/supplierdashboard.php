<div class="page-content">

    <div class="dashboard-header">

        <div>
            <h3>
                <i class="fa fa-truck"></i>
                Supplier Dashboard
                <small>Supplier Overview & Analytics</small>
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
                <span class="stat-title">Suppliers</span>
                <div class="stat-icon"><i class="fa fa-truck"></i></div>
            </div>
            <div class="stat-value" id="total_suppliers">...</div>
            <div class="stat-subtitle">Total Suppliers</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Active</span>
                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="stat-value" id="active_suppliers">...</div>
            <div class="stat-subtitle">Active Suppliers</div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Inactive</span>
                <div class="stat-icon"><i class="fa fa-times-circle"></i></div>
            </div>
            <div class="stat-value" id="inactive_suppliers">...</div>
            <div class="stat-subtitle">Inactive Suppliers</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Contacts</span>
                <div class="stat-icon"><i class="fa fa-users"></i></div>
            </div>
            <div class="stat-value" id="total_contacts">...</div>
            <div class="stat-subtitle">Supplier Contacts</div>
        </div>

        <div class="stat-card teal">
            <div class="stat-header">
                <span class="stat-title">Payments</span>
                <div class="stat-icon"><i class="fa fa-money"></i></div>
            </div>
            <div class="stat-value" id="total_payments">...</div>
            <div class="stat-subtitle">Supplier Payments</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Paid Amount</span>
                <div class="stat-icon"><i class="fa fa-dollar"></i></div>
            </div>
            <div class="stat-value" id="total_payment_amount">...</div>
            <div class="stat-subtitle">Total Payments</div>
        </div>

        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Purchases</span>
                <div class="stat-icon"><i class="fa fa-shopping-cart"></i></div>
            </div>
            <div class="stat-value" id="total_purchase_orders">...</div>
            <div class="stat-subtitle">Purchase Orders</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Purchase Value</span>
                <div class="stat-icon"><i class="fa fa-line-chart"></i></div>
            </div>
            <div class="stat-value" id="total_purchase_amount">...</div>
            <div class="stat-subtitle">Purchase Amount</div>
        </div>

    </div>
    <div class="row">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-bar-chart"></i>
                    Top Supplier Balances
                </h4>

                <canvas id="supplierChart" height="220"></canvas>

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
                    Monthly Purchases
                </h4>

                <canvas id="monthlyPurchaseChart" height="100"></canvas>

            </div>

        </div>

    </div>

    <div class="row" style="margin-top:15px;">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-money"></i>
                    Latest Supplier Payments
                </h4>

                <div class="table-responsive">

                    <table class="table table-striped table-bordered table-hover">

                        <thead>
                            <tr>
                                <th>Payment #</th>
                                <th>Supplier</th>
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
                    Recent Purchase Orders
                </h4>

                <div class="table-responsive">

                    <table class="table table-striped table-bordered table-hover">

                        <thead>
                            <tr>
                                <th>PO #</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Date</th>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    var supplierChart = null;
    var paymentMethodChart = null;
    var monthlyPurchaseChart = null;
    $(function() {

        loadDashboard();

        $("#refreshDashboard").click(function() {

            loadDashboard();

        });

    });

    function loadDashboard() {

        showDashboardLoading();

        $.ajax({

            url: "<?= Yii::$app->urlManager->createUrl('supplier/supplierdashboard') ?>",

            type: "POST",

            dataType: "json",

            data: {
                flag: "load_dashboard"
            },

            success: function(response) {

                hideDashboardLoading();

                if (response.success) {

                    loadStatistics(response.stats);

                    loadSupplierChart(response.supplierChart);

                    loadPaymentMethodChart(response.paymentMethodChart);

                    loadMonthlyPurchaseChart(response.monthlyPurchases);

                    loadLatestPayments(response.latestPayments);

                    loadRecentPurchases(response.recentPurchases);

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

        animateCounter("#total_suppliers", stats.total_suppliers);

        animateCounter("#active_suppliers", stats.active_suppliers);

        animateCounter("#inactive_suppliers", stats.inactive_suppliers);

        animateCounter("#total_contacts", stats.total_contacts);

        animateCounter("#total_payments", stats.total_payments);

        animateCurrency("#total_payment_amount", stats.total_payment_amount);

        animateCounter("#total_purchase_orders", stats.total_purchase_orders);

        animateCurrency("#total_purchase_amount", stats.total_purchase_amount);

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

    function loadSupplierChart(data) {

        const labels = data.map(x => x.company_name);
        const values = data.map(x => parseFloat(x.current_balance));

        if (supplierChart) {
            supplierChart.destroy();
        }

        supplierChart = new Chart(document.getElementById("supplierChart"), {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: "Current Balance",
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

    function loadMonthlyPurchaseChart(data) {

        const labels = data.map(x => x.month);
        const values = data.map(x => parseFloat(x.total));

        if (monthlyPurchaseChart) {
            monthlyPurchaseChart.destroy();
        }

        monthlyPurchaseChart = new Chart(document.getElementById("monthlyPurchaseChart"), {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: "Purchase Amount",
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
<td>${row.company_name}</td>
<td>${row.payment_method}</td>
<td>PKR ${Number(row.amount).toLocaleString()}</td>
<td>${row.payment_date}</td>
</tr>
`;

            });

        }

        $("#latestPaymentsBody").html(html);

    }

    function loadRecentPurchases(data) {

        let html = '';

        if (data.length === 0) {

            html = '<tr><td colspan="5" class="text-center">No purchase orders found.</td></tr>';

        } else {

            $.each(data, function(i, row) {

                let badge = 'label-default';

                if (row.status === 'Completed') {
                    badge = 'label-success';
                } else if (row.status === 'Pending') {
                    badge = 'label-warning';
                } else if (row.status === 'Approved') {
                    badge = 'label-info';
                } else if (row.status === 'Cancelled') {
                    badge = 'label-danger';
                }

                html += `
                    <tr>
                    <td>${row.po_number}</td>
                    <td>${row.company_name}</td>
                    <td><span class="label ${badge}">${row.status}</span></td>
                    <td>PKR ${Number(row.grand_total).toLocaleString()}</td>
                    <td>${row.order_date}</td>
                    </tr>
                    `;

            });

        }

        $("#recentPurchasesBody").html(html);

    }
</script>
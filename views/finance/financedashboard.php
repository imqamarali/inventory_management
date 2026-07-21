<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-money"></i>
                Finance Dashboard
                <small>Financial Overview & Analytics</small>
            </h3>
        </div>

        <div style="display: flex; gap: 10px;">
            <button id="refreshDashboard" >
                <i class="fa fa-refresh"></i>
                Refresh
            </button>
            <button id="truncateFinance" >
                <i class="fa fa-trash"></i>
                Truncate Finance
            </button>
        </div>
    </div>

    <div class="stats-grid">

        <!-- Total Assets -->
        <div class="stat-card blue">

            <div class="stat-header">

                <span class="stat-title">
                    Total Assets
                </span>

                <div class="stat-icon">
                    <i class="fa fa-briefcase"></i>
                </div>

            </div>

            <div class="stat-value" id="total_assets">
                ...
            </div>

            <div class="stat-subtitle">
                Asset Value
            </div>

        </div>


        <!-- Total Liabilities -->
        <div class="stat-card orange">

            <div class="stat-header">

                <span class="stat-title">
                    Liabilities
                </span>

                <div class="stat-icon">
                    <i class="fa fa-credit-card"></i>
                </div>

            </div>

            <div class="stat-value" id="total_liabilities">
                ...
            </div>

            <div class="stat-subtitle">
                Current Liabilities
            </div>

        </div>




        <!-- Total Income -->
        <div class="stat-card purple">

            <div class="stat-header">

                <span class="stat-title">
                    Total Income
                </span>

                <div class="stat-icon">
                    <i class="fa fa-arrow-circle-up"></i>
                </div>

            </div>

            <div class="stat-value" id="total_income">
                ...
            </div>

            <div class="stat-subtitle">
                Income Generated
            </div>

        </div>


        <!-- Total Expenses -->
        <div class="stat-card red">

            <div class="stat-header">

                <span class="stat-title">
                    Total Expenses
                </span>

                <div class="stat-icon">
                    <i class="fa fa-arrow-circle-down"></i>
                </div>

            </div>

            <div class="stat-value" id="total_expense">
                ...
            </div>

            <div class="stat-subtitle">
                Total Expenses
            </div>

        </div>




        <!-- Total Accounts -->
        <div class="stat-card blue">

            <div class="stat-header">

                <span class="stat-title">
                    Accounts
                </span>

                <div class="stat-icon">
                    <i class="fa fa-list"></i>
                </div>

            </div>

            <div class="stat-value" id="total_accounts">
                ...
            </div>

            <div class="stat-subtitle">
                Chart Accounts
            </div>

        </div>


        <!-- Customer Receivable -->
        <div class="stat-card green">

            <div class="stat-header">

                <span class="stat-title">
                    Receivables
                </span>

                <div class="stat-icon">
                    <i class="fa fa-arrow-left"></i>
                </div>

            </div>

            <div class="stat-value" id="customer_receivable">
                ...
            </div>

            <div class="stat-subtitle">
                Customer Receivable
            </div>

        </div>


        <!-- Supplier Payable -->
        <div class="stat-card orange">

            <div class="stat-header">

                <span class="stat-title">
                    Payables
                </span>

                <div class="stat-icon">
                    <i class="fa fa-arrow-right"></i>
                </div>

            </div>

            <div class="stat-value" id="supplier_payable">
                ...
            </div>

            <div class="stat-subtitle">
                Supplier Payable
            </div>

        </div>

    </div>

    <!-- Sales Invoice Stats -->
    <div class="section-title">
        <h4><i class="fa fa-shopping-cart"></i> Sales Performance</h4>
    </div>

    <div class="stats-grid">

        <!-- Total Sales Invoices -->
        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Total Sales</span>
                <div class="stat-icon"><i class="fa fa-file-text"></i></div>
            </div>
            <div class="stat-value" id="total_sales_invoices">...</div>
            <div class="stat-subtitle">Invoices Count</div>
        </div>

        <!-- Total Sales Amount -->
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Sales Amount</span>
                <div class="stat-icon"><i class="fa fa-money"></i></div>
            </div>
            <div class="stat-value" id="total_sales_amount">...</div>
            <div class="stat-subtitle">Total Invoiced</div>
        </div>

        <!-- Paid Sales Amount -->
        <div class="stat-card teal">
            <div class="stat-header">
                <span class="stat-title">Paid Sales</span>
                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="stat-value" id="paid_sales_amount">...</div>
            <div class="stat-subtitle">Amount Received</div>
        </div>

        <!-- Unpaid Sales Amount -->
        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Outstanding Sales</span>
                <div class="stat-icon"><i class="fa fa-exclamation-circle"></i></div>
            </div>
            <div class="stat-value" id="unpaid_sales_amount">...</div>
            <div class="stat-subtitle">Remaining Balance</div>
        </div>

    </div>

    <!-- Purchase Invoice Stats -->
    <div class="section-title">
        <h4><i class="fa fa-shopping-bag"></i> Purchase Performance</h4>
    </div>

    <div class="stats-grid">

        <!-- Total Purchase Invoices -->
        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Total Purchases</span>
                <div class="stat-icon"><i class="fa fa-file-text"></i></div>
            </div>
            <div class="stat-value" id="total_purchase_invoices">...</div>
            <div class="stat-subtitle">Invoices Count</div>
        </div>

        <!-- Total Purchase Amount -->
        <div class="stat-card indigo">
            <div class="stat-header">
                <span class="stat-title">Purchase Amount</span>
                <div class="stat-icon"><i class="fa fa-money"></i></div>
            </div>
            <div class="stat-value" id="total_purchase_amount">...</div>
            <div class="stat-subtitle">Total Invoiced</div>
        </div>

        <!-- Paid Purchase Amount -->
        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Paid Purchases</span>
                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="stat-value" id="paid_purchase_amount">...</div>
            <div class="stat-subtitle">Amount Paid</div>
        </div>

        <!-- Unpaid Purchase Amount -->
        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Outstanding Purchases</span>
                <div class="stat-icon"><i class="fa fa-exclamation-circle"></i></div>
            </div>
            <div class="stat-value" id="unpaid_purchase_amount">...</div>
            <div class="stat-subtitle">Remaining Balance</div>
        </div>

    </div>

    <!-- Charts -->

    <div class="row">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-pie-chart"></i>
                    Account Types Distribution
                </h4>

                <canvas id="accountTypeChart" height="220"></canvas>

            </div>

        </div>


        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-bar-chart"></i>
                    Monthly Cashflow
                </h4>

                <canvas id="cashflowChart" height="220"></canvas>

            </div>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .section-title {
        margin-top: 30px;
        margin-bottom: 20px;
        border-bottom: 2px solid #e0e0e0;
        padding-bottom: 10px;
    }

    .section-title h4 {
        color: #333;
        font-weight: 600;
        margin: 0;
    }

    .section-title i {
        margin-right: 8px;
        color: #007bff;
    }
</style>

<script>
    var accountTypeChart = null;
    var cashflowChart = null;

    $(function() {

        loadDashboard();

        $("#refreshDashboard").click(function() {

            loadDashboard();

        });

        $("#truncateFinance").click(function() {

            truncateFinanceRecords();

        });

    });

    function loadDashboard() {
        showDashboardLoading();
        $.ajax({
            url: "<?= Yii::$app->urlManager->createUrl('finance/financedashboard-data') ?>",
            type: "POST",
            dataType: "json",
            data: { flag: "load_dashboard" },
            timeout: 5000,
            success: function(response) {
                hideDashboardLoading();
                if (response.success) {
                    loadStatistics(response.stats);
                    loadSalesStats(response.salesStats);
                    loadPurchaseStats(response.purchaseStats);
                    if (typeof Chart === 'function' || typeof Chart === 'object') {
                        loadAccountTypeChart(response.accountTypeChart);
                        loadCashflowChart(response.monthlyCashflow);
                    } else {
                        console.warn('Chart.js not loaded');
                    }
                } else {
                    showError(response.message || 'Failed to load dashboard');
                }
            },
            error: function(xhr, status, error) {
                hideDashboardLoading();
                if (status === 'timeout') {
                    showError('Request timed out. Please try again.');
                } else {
                    showError('Network error: ' + (xhr.status || 'Unknown error'));
                }
            }
        });
    }

    function showDashboardLoading() {

        $(".stat-value").each(function() {

            $(this)
                .addClass("loading")
                .html("&nbsp;&nbsp;&nbsp;&nbsp;");

        });

    }


    function hideDashboardLoading() {

        $(".stat-value").removeClass("loading");

    }


    function loadStatistics(stats) {

        animateCurrency("#total_assets", stats.total_assets);

        animateCurrency("#total_liabilities", stats.total_liabilities);

        animateCurrency("#total_income", stats.total_income);

        animateCurrency("#total_expense", stats.total_expense);

        animateCounter("#total_accounts", stats.total_accounts);

        animateCurrency("#customer_receivable", stats.customer_receivable);

        animateCurrency("#supplier_payable", stats.supplier_payable);

    }

    function loadSalesStats(salesStats) {
        animateCounter("#total_sales_invoices", salesStats.total_sales_invoices);
        animateCurrency("#total_sales_amount", salesStats.total_sales_amount);
        animateCurrency("#paid_sales_amount", salesStats.paid_sales_amount);
        animateCurrency("#unpaid_sales_amount", salesStats.unpaid_sales_amount);
    }

    function loadPurchaseStats(purchaseStats) {
        animateCounter("#total_purchase_invoices", purchaseStats.total_purchase_invoices);
        animateCurrency("#total_purchase_amount", purchaseStats.total_purchase_amount);
        animateCurrency("#paid_purchase_amount", purchaseStats.paid_purchase_amount);
        animateCurrency("#unpaid_purchase_amount", purchaseStats.unpaid_purchase_amount);
    }


    function animateCounter(id, value) {

        $({
            count: 0
        }).animate({

                count: value

            },

            {

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

            },

            {

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

    function loadAccountTypeChart(data) {

        if (accountTypeChart) {
            accountTypeChart.destroy();
        }

        let labels = [];
        let values = [];
        let colors = ['#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6'];

        $.each(data, function(i, row) {

            labels.push(row.account_type);
            values.push(parseFloat(row.total));

        });

        accountTypeChart = new Chart(
            document.getElementById("accountTypeChart"), {
                type: "doughnut",
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors.slice(0, values.length)
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
            }
        );

    }


    function loadCashflowChart(data) {

        if (cashflowChart) {
            cashflowChart.destroy();
        }

        let labels = [];
        let received = [];
        let paid = [];

        $.each(data, function(i, row) {

            labels.push(row.month);
            received.push(parseFloat(row.received));
            paid.push(parseFloat(row.paid));

        });

        cashflowChart = new Chart(
            document.getElementById("cashflowChart"), {

                type: "bar",

                data: {

                    labels: labels,

                    datasets: [{

                        label: "Received",

                        data: received,

                        backgroundColor: "#2ecc71"

                    }, {

                        label: "Paid",

                        data: paid,

                        backgroundColor: "#e74c3c"

                    }]

                },

                options: {

                    responsive: true,

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

            }

        );

    }

    function showError(message) {
        const alert = $(`<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`);
        $(document.body).prepend(alert);
        setTimeout(() => alert.fadeOut(), 5000);
    }

    function truncateFinanceRecords() {
        Swal.fire({
            title: 'Truncate Finance Records?',
            html: 'This will permanently delete ALL GL transactions, payments, and reset account balances to opening balance.<br><strong>This action CANNOT be undone!</strong>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete All',
            confirmButtonColor: '#c9302c',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Enter Your Password',
                    text: 'Please confirm by entering your password',
                    input: 'password',
                    inputPlaceholder: 'Enter your password...',
                    showCancelButton: true,
                    confirmButtonColor: '#c9302c',
                    confirmButtonText: 'Confirm',
                    inputAttributes: {
                        autocapitalize: 'off',
                        autocorrect: 'off'
                    }
                }).then((passwordResult) => {
                    if (passwordResult.isConfirmed) {
                        const password = passwordResult.value;

                        if (!password || password === '') {
                            Swal.fire('Error!', 'Password is required', 'error');
                            return;
                        }

                        $.ajax({
                            url: '<?= Yii::$app->urlManager->createUrl("finance/truncate-finance") ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: { password: password },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonColor: '#0f4c29'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Failed to truncate finance records', 'error');
                            }
                        });
                    }
                });
            }
        });
    }
</script>

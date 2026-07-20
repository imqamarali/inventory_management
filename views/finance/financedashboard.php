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

        <div>
            <button id="refreshDashboard">
                <i class="fa fa-refresh"></i>
                Refresh
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


        <!-- Total Equity -->
        <div class="stat-card green">

            <div class="stat-header">

                <span class="stat-title">
                    Equity
                </span>

                <div class="stat-icon">
                    <i class="fa fa-percent"></i>
                </div>

            </div>

            <div class="stat-value" id="total_equity">
                ...
            </div>

            <div class="stat-subtitle">
                Owner's Equity
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


        <!-- Total Receipts -->
        <div class="stat-card teal">

            <div class="stat-header">

                <span class="stat-title">
                    Total Receipts
                </span>

                <div class="stat-icon">
                    <i class="fa fa-inbox"></i>
                </div>

            </div>

            <div class="stat-value" id="total_receipts">
                ...
            </div>

            <div class="stat-subtitle">
                Cash Received
            </div>

        </div>


        <!-- Total Payouts -->
        <div class="stat-card pink">

            <div class="stat-header">

                <span class="stat-title">
                    Total Payouts
                </span>

                <div class="stat-icon">
                    <i class="fa fa-send"></i>
                </div>

            </div>

            <div class="stat-value" id="total_payouts">
                ...
            </div>

            <div class="stat-subtitle">
                Cash Paid Out
            </div>

        </div>


        <!-- Cash Balance -->
        <div class="stat-card indigo">

            <div class="stat-header">

                <span class="stat-title">
                    Cash Balance
                </span>

                <div class="stat-icon">
                    <i class="fa fa-wallet"></i>
                </div>

            </div>

            <div class="stat-value" id="cash_balance">
                ...
            </div>

            <div class="stat-subtitle">
                Current Cash Balance
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

<script>
    var accountTypeChart = null;
    var cashflowChart = null;

    $(function() {

        loadDashboard();

        $("#refreshDashboard").click(function() {

            loadDashboard();

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

        animateCurrency("#total_equity", stats.total_equity);

        animateCurrency("#total_income", stats.total_income);

        animateCurrency("#total_expense", stats.total_expense);

        animateCounter("#total_accounts", stats.total_accounts);

        animateCurrency("#total_receipts", stats.total_receipts);

        animateCurrency("#total_payouts", stats.total_payouts);

        animateCurrency("#cash_balance", stats.cash_balance);

        animateCurrency("#customer_receivable", stats.customer_receivable);

        animateCurrency("#supplier_payable", stats.supplier_payable);

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
</script>

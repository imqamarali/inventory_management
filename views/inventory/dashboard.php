<?php

use yii\helpers\Html;
use yii\helpers\Url;

// Get current school information
$school_id = Yii::$app->session->get('user_array')['school_id'] ?? null;
$current_school = null;
if ($school_id) {
    try {
        $current_school = Yii::$app->db->createCommand('SELECT * FROM school WHERE school_id = :school_id')
            ->bindValue(':school_id', $school_id)
            ->queryOne();
    } catch (\Exception $e) {
        $current_school = null;
    }
}
$navbar_color = $current_school['navbar_color'] ?? '#0f4c29';
$school_name = $current_school['school_name'] ?? 'School Management System';
$student_name = (Yii::$app->session->get('user_array')['first_name'] ?? '') . ' ' . (Yii::$app->session->get('user_array')['last_name'] ?? 'Student');
$this->title = 'Student Dashboard';
?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
    }

    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, <?= $navbar_color ?>f5 0%, <?= $navbar_color ?>dd 100%);
        color: white;
        padding: 12px 18px;
        border-radius: 8px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .welcome-profile-picture {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.3);
        object-fit: cover;
        flex-shrink: 0;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .welcome-profile-picture img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .welcome-content {
        flex: 1;
        min-width: 0;
    }

    .welcome-banner h2 {
        margin: 0 0 5px 0;
        font-size: 18px;
        font-weight: 700;
    }

    .welcome-banner p {
        margin: 0;
        font-size: 12px;
        opacity: 0.9;
    }

    .welcome-info {
        display: flex;
        gap: 20px;
        margin-top: 10px;
        flex-wrap: wrap;
    }

    .welcome-info-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .welcome-info-item i {
        font-size: 13px;
        opacity: 0.9;
    }

    .welcome-info-item span {
        font-size: 11px;
    }
</style>
<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-shopping-bag"></i>
                Inventory Dashboard
                <small>Inventory Overview & Analytics</small>
            </h3>
        </div>

        <div>
            <button id="refreshDashboard">
                <i class="fa fa-refresh"></i>
                Refresh
            </button>
        </div>
    </div>
    <div class="welcome-banner">
        <?php
        $photo_path = $student_data['photo_path'] ?? null;

        $profile_photo_url = !empty($photo_path) ? Url::to('@web/' . $photo_path, true) : null;

        $initials = strtoupper(substr(Yii::$app->session->get('user_array')['first_name'] ?? 'S', 0, 1) . substr(Yii::$app->session->get('user_array')['last_name'] ?? 'A', 0, 1));
        ?>
        <div class="welcome-profile-picture">
            <?php if ($profile_photo_url): ?>
                <img src="<?= $profile_photo_url ?>" alt="Profile Picture"
                    onerror="this.style.display='none'; this.parentElement.innerHTML='<?= $initials ?>';">
            <?php else: ?>
                <?= $initials ?>
            <?php endif; ?>
        </div>
        <div class="welcome-content">
            <h2>Welcome, <?= Html::encode(Yii::$app->session->get('user_array')['first_name'] ?? 'User') ?>! 👋</h2>
            <p style="margin: 0; opacity: 0.9; display: flex; align-items: center; gap: 20px;">
                <span id="current-date"><?= date('l, F j, Y') ?></span>
                <span style="font-weight: 600; font-size: 18px;" id="current-time"></span>
            </p>
        </div>
    </div>


    <div class="stats-grid">

        <!-- Warehouses -->
        <div class="stat-card teal">

            <div class="stat-header">

                <span class="stat-title">
                    Warehouses
                </span>

                <div class="stat-icon">
                    <i class="fa fa-building"></i>
                </div>

            </div>

            <div class="stat-value" id="warehouses">
                ...
            </div>

            <div class="stat-subtitle">
                Active Locations
            </div>

        </div>


        <!-- Total Products -->
        <div class="stat-card blue">

            <div class="stat-header">

                <span class="stat-title">
                    Products
                </span>

                <div class="stat-icon">
                    <i class="fa fa-cubes"></i>
                </div>

            </div>

            <div class="stat-value" id="total_products">
                ...
            </div>

            <div class="stat-subtitle">
                Registered Products
            </div>

        </div>


        <!-- Current Stock -->
        <div class="stat-card green">

            <div class="stat-header">

                <span class="stat-title">
                    Current Stock
                </span>

                <div class="stat-icon">
                    <i class="fa fa-archive"></i>
                </div>

            </div>

            <div class="stat-value" id="current_stock">
                ...
            </div>

            <div class="stat-subtitle">
                Available Units
            </div>

        </div>


        <!-- Low Stock Items -->
        <div class="stat-card orange">

            <div class="stat-header">

                <span class="stat-title">
                    Low Stock
                </span>

                <div class="stat-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>

            </div>

            <div class="stat-value" id="low_stock_items">
                ...
            </div>

            <div class="stat-subtitle">
                Require Restocking
            </div>

        </div>


        <!-- Pending Purchase Orders -->
        <div class="stat-card purple">

            <div class="stat-header">

                <span class="stat-title">
                    Purchase Orders
                </span>

                <div class="stat-icon">
                    <i class="fa fa-truck"></i>
                </div>

            </div>

            <div class="stat-value" id="pending_purchase_orders">
                ...
            </div>

            <div class="stat-subtitle">
                Pending Delivery
            </div>

        </div>


        <!-- Pending Sales Orders -->
        <div class="stat-card red">

            <div class="stat-header">

                <span class="stat-title">
                    Sales Orders
                </span>

                <div class="stat-icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>

            </div>

            <div class="stat-value" id="pending_sales_orders">
                ...
            </div>

            <div class="stat-subtitle">
                Orders to Fulfill
            </div>

        </div>


        <!-- Today's Sales -->
        <div class="stat-card green">

            <div class="stat-header">

                <span class="stat-title">
                    Today's Sales
                </span>

                <div class="stat-icon">
                    <i class="fa fa-line-chart"></i>
                </div>

            </div>

            <div class="stat-value" id="today_sales">
                ...
            </div>

            <div class="stat-subtitle">
                Sales Revenue Today
            </div>

        </div>


        <!-- Today's Purchases -->
        <div class="stat-card blue">

            <div class="stat-header">

                <span class="stat-title">
                    Today's Purchases
                </span>

                <div class="stat-icon">
                    <i class="fa fa-shopping-bag"></i>
                </div>

            </div>

            <div class="stat-value" id="today_purchases">
                ...
            </div>

            <div class="stat-subtitle">
                Purchases Made Today
            </div>

        </div>


        <!-- Customers -->
        <div class="stat-card purple">

            <div class="stat-header">

                <span class="stat-title">
                    Customers
                </span>

                <div class="stat-icon">
                    <i class="fa fa-users"></i>
                </div>

            </div>

            <div class="stat-value" id="customers">
                ...
            </div>

            <div class="stat-subtitle">
                Active Customers
            </div>

        </div>


        <!-- Suppliers -->
        <div class="stat-card teal">

            <div class="stat-header">

                <span class="stat-title">
                    Suppliers
                </span>

                <div class="stat-icon">
                    <i class="fa fa-industry"></i>
                </div>

            </div>

            <div class="stat-value" id="suppliers">
                ...
            </div>

            <div class="stat-subtitle">
                Active Suppliers
            </div>

        </div>


        <!-- Total Revenue -->
        <div class="stat-card red">

            <div class="stat-header">

                <span class="stat-title">
                    Total Revenue
                </span>

                <div class="stat-icon">
                    <i class="fa fa-money"></i>
                </div>

            </div>

            <div class="stat-value" id="total_revenue">
                ...
            </div>

            <div class="stat-subtitle">
                All Sales Combined
            </div>

        </div>


        <!-- Total Purchases Value -->
        <div class="stat-card blue">

            <div class="stat-header">

                <span class="stat-title">
                    Total Purchases
                </span>

                <div class="stat-icon">
                    <i class="fa fa-credit-card"></i>
                </div>

            </div>

            <div class="stat-value" id="total_purchases_value">
                ...
            </div>

            <div class="stat-subtitle">
                All Purchases Combined
            </div>

        </div>


        <!-- Pending Returns -->
        <div class="stat-card orange">

            <div class="stat-header">

                <span class="stat-title">
                    Pending Returns
                </span>

                <div class="stat-icon">
                    <i class="fa fa-undo"></i>
                </div>

            </div>

            <div class="stat-value" id="pending_returns">
                ...
            </div>

            <div class="stat-subtitle">
                Awaiting Processing
            </div>

        </div>

    </div>

    <!-- Charts -->

    <div class="row">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-pie-chart"></i>
                    Inventory Distribution
                </h4>

                <canvas id="inventoryChart" height="220"></canvas>

            </div>

        </div>


        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-bar-chart"></i>
                    Sales vs Purchases
                </h4>

                <canvas id="salesPurchaseChart" height="220"></canvas>

            </div>

        </div>

    </div>



    <div class="row" style="margin-top:15px;">

        <div class="col-md-12">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-line-chart"></i>
                    Monthly Overview
                </h4>

                <canvas id="monthlyChart" height="100"></canvas>

            </div>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    var inventoryChart = null;
    var salesPurchaseChart = null;
    var monthlyChart = null;

    // Live Clock Function
    function updateLiveTime() {
        var now = new Date();
        var hours = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        var seconds = String(now.getSeconds()).padStart(2, '0');
        var timeString = hours + ':' + minutes + ':' + seconds;

        document.getElementById('current-time').textContent = timeString;
    }

    // Update time every second
    setInterval(updateLiveTime, 1000);

    // Initial call to set time immediately
    updateLiveTime();

    $(function() {

        loadDashboard();

        $("#refreshDashboard").click(function() {

            loadDashboard();

        });

    });

    function loadDashboard() {
        showDashboardLoading();
        $.ajax({
            url: "<?= Yii::$app->urlManager->createUrl('inventory/dashboard-data') ?>",
            type: "POST",
            dataType: "json",
            data: {
                flag: "load_dashboard"
            },
            timeout: 5000,
            success: function(response) {
                hideDashboardLoading();
                if (response.success) {
                    loadStatistics(response.stats);
                    if (typeof Chart === 'function' || typeof Chart === 'object') {
                        loadInventoryChart(response.inventoryChart);
                        loadSalesPurchaseChart(response.salesPurchaseChart);
                        loadMonthlyChart(response.monthlyData);
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

        animateCounter("#warehouses", stats.warehouses);

        animateCounter("#total_products", stats.total_products);

        animateCounter("#current_stock", stats.current_stock);

        animateCounter("#low_stock_items", stats.low_stock_items);

        animateCounter("#pending_purchase_orders", stats.pending_purchase_orders);

        animateCounter("#pending_sales_orders", stats.pending_sales_orders);

        animateCurrency("#today_sales", stats.today_sales);

        animateCurrency("#today_purchases", stats.today_purchases);

        animateCounter("#customers", stats.customers);

        animateCounter("#suppliers", stats.suppliers);

        animateCurrency("#total_revenue", stats.total_revenue);

        animateCurrency("#total_purchases_value", stats.total_purchases_value);

        animateCounter("#pending_returns", stats.pending_returns);

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

    function loadInventoryChart(data) {

        if (inventoryChart) {
            inventoryChart.destroy();
        }

        let labels = [];
        let values = [];
        let colors = ['#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6', '#1abc9c'];

        $.each(data, function(i, row) {

            labels.push(row.name);
            values.push(parseFloat(row.value));

        });

        inventoryChart = new Chart(
            document.getElementById("inventoryChart"), {
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


    function loadSalesPurchaseChart(data) {

        if (salesPurchaseChart) {
            salesPurchaseChart.destroy();
        }

        let labels = [];
        let sales = [];
        let purchases = [];

        $.each(data, function(i, row) {

            labels.push(row.label);
            sales.push(parseFloat(row.sales));
            purchases.push(parseFloat(row.purchases));

        });

        salesPurchaseChart = new Chart(
            document.getElementById("salesPurchaseChart"), {

                type: "bar",

                data: {

                    labels: labels,

                    datasets: [{

                        label: "Sales",

                        data: sales,

                        backgroundColor: "#2ecc71"

                    }, {

                        label: "Purchases",

                        data: purchases,

                        backgroundColor: "#3498db"

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


    function loadMonthlyChart(data) {

        if (monthlyChart) {
            monthlyChart.destroy();
        }

        let labels = [];
        let sales = [];
        let purchases = [];

        $.each(data, function(i, row) {

            labels.push(row.month);
            sales.push(parseInt(row.sales));
            purchases.push(parseInt(row.purchases));

        });

        monthlyChart = new Chart(
            document.getElementById("monthlyChart"), {

                type: "line",

                data: {

                    labels: labels,

                    datasets: [{

                        label: "Sales",

                        data: sales,

                        fill: false,

                        borderColor: "#27ae60",

                        tension: .4

                    }, {

                        label: "Purchases",

                        data: purchases,

                        fill: false,

                        borderColor: "#3498db",

                        tension: .4

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
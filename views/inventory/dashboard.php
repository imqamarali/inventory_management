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


    <!-- Key Financial Metrics -->
    <div class="stats-grid">

        <!-- All Important Purchase -->
        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">All Purchase</span>
                <div class="stat-icon">
                    <i class="fa fa-shopping-bag"></i>
                </div>
            </div>
            <div class="stat-value" id="total_purchase">...</div>
            <div class="stat-subtitle">Total Purchase Value</div>
        </div>

        <!-- All Important Sale -->
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">All Sale</span>
                <div class="stat-icon">
                    <i class="fa fa-dollar"></i>
                </div>
            </div>
            <div class="stat-value" id="total_sale">...</div>
            <div class="stat-subtitle">Total Sale Value</div>
        </div>

        <!-- Today Purchase -->
        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Today Purchase</span>
                <div class="stat-icon">
                    <i class="fa fa-inbox"></i>
                </div>
            </div>
            <div class="stat-value" id="today_purchase">...</div>
            <div class="stat-subtitle">Today's Purchase Total</div>
        </div>

        <!-- Today Sale -->
        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Today Sale</span>
                <div class="stat-icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
            </div>
            <div class="stat-value" id="today_sale">...</div>
            <div class="stat-subtitle">Today's Sale Total</div>
        </div>

        <!-- Profit Loss -->
        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Profit/Loss</span>
                <div class="stat-icon">
                    <i class="fa fa-line-chart"></i>
                </div>
            </div>
            <div class="stat-value" id="profit_loss">...</div>
            <div class="stat-subtitle">Overall P/L</div>
        </div>

    </div>

    <!-- Performance Charts -->

    <div class="row">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-bar-chart"></i>
                    Purchase Performance
                </h4>

                <canvas id="purchasePerformanceChart" height="220"></canvas>

            </div>

        </div>


        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-line-chart"></i>
                    Sales Performance
                </h4>

                <canvas id="salesPerformanceChart" height="220"></canvas>

            </div>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    var purchasePerformanceChart = null;
    var salesPerformanceChart = null;

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
                        loadPurchasePerformanceChart(response.purchasePerformance || []);
                        loadSalesPerformanceChart(response.salesPerformance || []);
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

        animateCurrency("#total_purchase", stats.total_purchases_value);

        animateCurrency("#total_sale", stats.total_revenue);

        animateCurrency("#today_purchase", stats.today_purchases);

        animateCurrency("#today_sale", stats.today_sales);

        // Calculate and display profit/loss
        const profitLoss = stats.total_revenue - stats.total_purchases_value;
        const profitPercent = stats.total_purchases_value > 0
            ? ((profitLoss / stats.total_purchases_value) * 100).toFixed(2)
            : 0;

        const profitElement = $("#profit_loss");
        if (profitLoss >= 0) {
            profitElement.html(`<span style="color: #2ecc71; font-weight: bold;">+PKR ${Math.abs(profitLoss).toLocaleString()}</span><br><span style="font-size: 11px; color: #2ecc71;">(+${profitPercent}%)</span>`);
        } else {
            profitElement.html(`<span style="color: #e74c3c; font-weight: bold;">-PKR ${Math.abs(profitLoss).toLocaleString()}</span><br><span style="font-size: 11px; color: #e74c3c;">(${profitPercent}%)</span>`);
        }

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

    function loadPurchasePerformanceChart(data) {

        if (purchasePerformanceChart) {
            purchasePerformanceChart.destroy();
        }

        let labels = [];
        let amounts = [];

        $.each(data, function(i, row) {
            labels.push(row.label);
            amounts.push(parseFloat(row.amount));
        });

        purchasePerformanceChart = new Chart(
            document.getElementById("purchasePerformanceChart"), {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Purchase Amount",
                        data: amounts,
                        backgroundColor: "#3498db",
                        borderColor: "#2980b9",
                        borderWidth: 1
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
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'PKR ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            }
        );

    }

    function loadSalesPerformanceChart(data) {

        if (salesPerformanceChart) {
            salesPerformanceChart.destroy();
        }

        let labels = [];
        let amounts = [];

        $.each(data, function(i, row) {
            labels.push(row.label);
            amounts.push(parseFloat(row.amount));
        });

        salesPerformanceChart = new Chart(
            document.getElementById("salesPerformanceChart"), {
                type: "line",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Sales Amount",
                        data: amounts,
                        borderColor: "#2ecc71",
                        backgroundColor: "rgba(46, 204, 113, 0.1)",
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: "#2ecc71"
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
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'PKR ' + value.toLocaleString();
                                }
                            }
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
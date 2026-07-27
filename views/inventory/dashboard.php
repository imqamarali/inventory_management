<?php

use yii\helpers\Html;
use yii\helpers\Url;

// Get navbar color from settings
$navbar_color = Yii::$app->db->createCommand(
    "SELECT setting_value FROM inventory_settings WHERE setting_key='navbar_color' AND is_deleted=0 LIMIT 1"
)->queryScalar() ?: '#0f4c29';

$companyName = Yii::$app->db->createCommand(
    "SELECT setting_value FROM inventory_settings WHERE setting_key='company_name' AND is_deleted=0 LIMIT 1"
)->queryScalar() ?: 'Inventory Management System';

$user_array = Yii::$app->session->get('user_array') ?? [];
$userFirstName = $user_array['first_name'] ?? 'User';
$userLastName = $user_array['last_name'] ?? '';
$student_name = trim($userFirstName . ' ' . $userLastName);
$this->title = 'Dashboard';
?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
    }

    /* Dashboard Header */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px;
        background: linear-gradient(135deg, <?= $navbar_color ?>f5 0%, <?= $navbar_color ?>dd 100%);
        border-radius: 8px;
        color: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .dashboard-header h3 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        color: white;
    }

    .dashboard-header h3 small {
        display: block;
        font-size: 12px;
        font-weight: 400;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 3px;
    }

    .dashboard-header button {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.3s ease;
    }

    .dashboard-header button:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
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
    <?php if (!empty($unpaidInvoices)): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Show unpaid invoice notification modal
            document.addEventListener('DOMContentLoaded', function() {
                const unpaidInvoices = <?= json_encode($unpaidInvoices) ?>;

                if (unpaidInvoices && unpaidInvoices.length > 0) {
                    let invoiceList = '<div style="text-align: left; margin: 10px 0;">';

                    unpaidInvoices.forEach((invoice, index) => {
                        const dueDate = new Date(invoice.due_date);
                        const today = new Date();
                        const isOverdue = dueDate < today;
                        const dueDateStr = dueDate.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });

                        invoiceList += `
                            <div style="
                                padding: 12px;
                                margin: 8px 0;
                                border: 1px solid #e0e0e0;
                                border-radius: 4px;
                                background: ${isOverdue ? '#ffe6e6' : '#fff9e6'};
                                border-left: 4px solid ${isOverdue ? '#ff5252' : '#ffc107'};
                            ">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <strong style="font-size: 13px;">Invoice #${invoice.invoice_number}</strong>
                                    <span style="
                                        padding: 3px 8px;
                                        background: ${isOverdue ? '#ff5252' : '#ffc107'};
                                        color: white;
                                        border-radius: 3px;
                                        font-size: 11px;
                                        font-weight: bold;
                                    ">${isOverdue ? 'OVERDUE' : 'DUE SOON'}</span>
                                </div>
                                <div style="font-size: 12px; color: #666; margin-bottom: 4px;">
                                    Amount: <strong>PKR ${parseFloat(invoice.amount).toLocaleString()}</strong>
                                </div>
                                <div style="font-size: 11px; color: #999;">
                                    Due Date: <strong>${dueDateStr}</strong>
                                </div>
                            </div>
                        `;
                    });

                    invoiceList += '</div>';

                    Swal.fire({
                        title: '⚠️ Unpaid Invoices',
                        html: `
                            <div style="text-align: left;">
                                <p style="color: #666; margin-bottom: 15px;">
                                    You have <strong>${unpaidInvoices.length}</strong> unpaid invoice${unpaidInvoices.length > 1 ? 's' : ''} pending payment.
                                </p>
                                ${invoiceList}
                            </div>
                        `,
                        icon: 'warning',
                        confirmButtonText: 'Pay Now',
                        confirmButtonColor: '#4CAF50',
                        cancelButtonText: 'Close',
                        showCancelButton: true,
                        width: '600px',
                        didOpen: (modal) => {
                            // Add custom styling to the modal
                            const confirmBtn = modal.querySelector('.swal2-confirm');
                            if (confirmBtn) {
                                confirmBtn.style.marginRight = '10px';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'index.php?r=payment/payment-history';
                        }
                    });
                }
            });
        </script>
    <?php endif; ?>

    <!-- Quick Action Buttons -->
    <style>
        .dashboard-actions-wrapper {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .exam-quick-actions-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
    </style>

    <div class="dashboard-actions-wrapper">
        <div class="exam-quick-actions-group">
            <!-- Add New Product -->
            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openProductModal()" title="Add New Product">
                <i class="ace-icon fa fa-plus"></i>
                Add New Product
            </a>

            <!-- Add Current Stock -->
            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openStockModal()" title="Add Current Stock">
                <i class="ace-icon fa fa-plus"></i>
                Add Current Stock
            </a>

            <!-- Add New Supplier -->
            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openSupplierModal()" title="Add New Supplier">
                <i class="ace-icon fa fa-plus"></i>
                Add New Supplier
            </a>

            <!-- Add New Customer -->
            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openCustomerModal()" title="Add New Customer">
                <i class="ace-icon fa fa-plus"></i>
                Add New Customer
            </a>

            <!-- Add Purchase Order -->
            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="loadOrder()" title="Add Purchase Order">
                <i class="ace-icon fa fa-plus"></i>
                Add Purchase Order
            </a>

            <!-- Add Sales Order -->
            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openOrderModal()" title="Add Sales Order">
                <i class="ace-icon fa fa-plus"></i>
                Add Sales Order
            </a>
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
        <button id="refreshDashboard" style="background: transparent;border: none;">
            <i class="fa fa-refresh"></i>
            Refresh
        </button>
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

        <!-- Today Profit/Loss -->
        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Today P/L</span>
                <div class="stat-icon">
                    <i class="fa fa-line-chart"></i>
                </div>
            </div>
            <div class="stat-value" id="today_profit_loss">...</div>
            <div class="stat-subtitle">Today's Profit/Loss</div>
        </div>

        <!-- Balance Sheet Verification -->
        <div class="stat-card teal">
            <div class="stat-header">
                <span class="stat-title">Balance Sheet</span>
                <div class="stat-icon">
                    <i class="fa fa-balance-scale"></i>
                </div>
            </div>
            <div class="stat-value" id="balance_status" style="font-size: 14px;">...</div>
            <div class="stat-subtitle">Equation Verified</div>
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

        // Load dashboard data only (not modal data on page load)
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

        // Calculate and display overall profit/loss
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

        // Calculate and display today's profit/loss
        const todayProfitLoss = stats.today_sales - stats.today_purchases;
        const todayProfitPercent = stats.today_purchases > 0
            ? ((todayProfitLoss / stats.today_purchases) * 100).toFixed(2)
            : 0;

        const todayProfitElement = $("#today_profit_loss");
        if (todayProfitLoss >= 0) {
            todayProfitElement.html(`<span style="color: #2ecc71; font-weight: bold;">+PKR ${Math.abs(todayProfitLoss).toLocaleString()}</span><br><span style="font-size: 11px; color: #2ecc71;">(+${todayProfitPercent}%)</span>`);
        } else {
            todayProfitElement.html(`<span style="color: #e74c3c; font-weight: bold;">-PKR ${Math.abs(todayProfitLoss).toLocaleString()}</span><br><span style="font-size: 11px; color: #e74c3c;">(${todayProfitPercent}%)</span>`);
        }

        // Display Balance Sheet Profit/Loss
        const profitLossBS = parseFloat(stats.balance_sheet_profit_loss) || 0;
        const statusCard = $("#balance_status");

        if (profitLossBS > 0) {
            // Profit - Green
            statusCard.html(`<span style="color: #2ecc71; font-weight: bold; font-size: 16px;">PKR ${Math.abs(profitLossBS).toLocaleString()}</span><br><span style="font-size: 11px; color: #2ecc71; font-weight: 600;">Profit</span>`);
        } else if (profitLossBS < 0) {
            // Loss - Red
            statusCard.html(`<span style="color: #e74c3c; font-weight: bold; font-size: 16px;">PKR ${Math.abs(profitLossBS).toLocaleString()}</span><br><span style="font-size: 11px; color: #e74c3c; font-weight: 600;">Loss</span>`);
        } else {
            // Break even
            statusCard.html(`<span style="color: #7f8c8d; font-weight: bold; font-size: 16px;">PKR 0</span><br><span style="font-size: 11px; color: #7f8c8d; font-weight: 600;">Break Even</span>`);
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

    // Initialize dashboard modals data
    let dashboardModalData = {
        success: false,
        categories: [],
        brands: [],
        models: [],
        units: [],
        products: [],
        warehouses: [],
        suppliers: [],
        customers: []
    };

    // Load modal data on dashboard load
    $(function() {
        loadDashboardModalData();
    });

    function loadDashboardModalData() {
        return new Promise((resolve) => {
            // If data already loaded, resolve immediately
            if (dashboardModalData.success === true && dashboardModalData.suppliers && dashboardModalData.suppliers.length > 0) {
                resolve();
                return;
            }

            fetch('index.php?r=inventory/dashboard-modals', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: '_csrf=<?= Yii::$app->request->getCsrfToken() ?>'
            })
            .then(response => response.json())
            .then(data => {
                console.log('Dashboard modal data loaded:', data);
                if (data.success) {
                    dashboardModalData.success = true;
                    dashboardModalData.categories = data.categories || [];
                    dashboardModalData.brands = data.brands || [];
                    dashboardModalData.models = data.models || [];
                    dashboardModalData.units = data.units || [];
                    dashboardModalData.products = data.products || [];
                    dashboardModalData.warehouses = data.warehouses || [];
                    dashboardModalData.suppliers = data.suppliers || [];
                    dashboardModalData.customers = data.customers || [];
                    console.log('Updated dashboardModalData:', dashboardModalData);
                } else {
                    console.error('Failed to load modal data:', data.message);
                }
                resolve();
            })
            .catch(error => {
                console.error('Error loading modal data:', error);
                resolve();
            });
        });
    }

    // ===== PRODUCT MODAL =====
    function openProductModal(productData = null) {
        const isEdit = productData !== null;
        const title = isEdit ? 'Update Product' : 'New Product';
        const id = isEdit ? (productData.id || '') : '';
        const categoryId = isEdit ? (productData.category_id || '') : '';
        const brandId = isEdit ? (productData.brand_id || '') : '';
        const modelId = isEdit ? (productData.model_id || '') : '';
        const unitId = isEdit ? (productData.unit_id || '') : '';
        const name = isEdit ? (productData.product_name || '') : '';
        const sku = isEdit ? (productData.sku || '') : '';
        const barcode = isEdit ? (productData.barcode || '') : '';
        const description = isEdit ? (productData.description || '') : '';
        const purchase = isEdit ? (productData.purchase_price || '') : '';
        const selling = isEdit ? (productData.selling_price || '') : '';
        const min = isEdit ? (productData.minimum_stock || '') : '';
        const max = isEdit ? (productData.maximum_stock || '') : '';
        const reorder = isEdit ? (productData.reorder_level || '') : '';
        const weight = isEdit ? (productData.weight || '') : '';
        const length = isEdit ? (productData.length || '') : '';
        const width = isEdit ? (productData.width || '') : '';
        const height = isEdit ? (productData.height || '') : '';
        const active = isEdit && (productData.is_active == 1 || productData.is_active == '1');

        let categoryOptions = '<option value="">Select Category</option>';
        (dashboardModalData.categories || []).forEach(item => {
            categoryOptions += `<option value="${item.id}" ${item.id==categoryId?'selected':''}>${item.category_name}</option>`;
        });

        let brandOptions = '<option value="">Select Brand</option>';
        (dashboardModalData.brands || []).forEach(item => {
            brandOptions += `<option value="${item.id}" ${item.id==brandId?'selected':''}>${item.brand_name}</option>`;
        });

        let modelOptions = '<option value="">Select Model</option>';
        (dashboardModalData.models || []).forEach(item => {
            modelOptions += `<option value="${item.id}" ${item.id==modelId?'selected':''}>${item.model_name}</option>`;
        });

        let unitOptions = '<option value="">Select Unit</option>';
        (dashboardModalData.units || []).forEach(item => {
            unitOptions += `<option value="${item.id}" ${item.id==unitId?'selected':''}>${item.unit_name}</option>`;
        });

        Swal.fire({
            title: title,
            allowOutsideClick: false,
            allowEscapeKey: false,
            html: `<form id="productForm" style="text-align:left;">
                <input type="hidden" id="swal_product_id" value="${id}">
                <div class="row">
                    <div class="col-md-4">
                        <label>Product Name <span class="text-danger">*</span></label>
                        <input type="text" id="swal_product_name" class="form-control" value="${name}">
                    </div>
                    <div class="col-md-4">
                        <label>SKU</label>
                        <input type="text" id="swal_sku" class="form-control" value="${sku}">
                    </div>
                    <div class="col-md-4">
                        <label>Category</label>
                        <select id="swal_category_id" class="form-control">${categoryOptions}</select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Brand</label>
                        <select id="swal_brand_id" class="form-control">${brandOptions}</select>
                    </div>
                    <div class="col-md-4">
                        <label>Model</label>
                        <select id="swal_model_id" class="form-control">${modelOptions}</select>
                    </div>
                    <div class="col-md-4">
                        <label>Unit</label>
                        <select id="swal_unit_id" class="form-control">${unitOptions}</select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Purchase Price</label>
                        <input type="number" id="swal_purchase_price" class="form-control" value="${purchase}">
                    </div>
                    <div class="col-md-6">
                        <label>Selling Price</label>
                        <input type="number" id="swal_selling_price" class="form-control" value="${selling}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Minimum Stock</label>
                        <input type="number" id="swal_minimum_stock" class="form-control" value="${min}">
                    </div>
                    <div class="col-md-4">
                        <label>Maximum Stock</label>
                        <input type="number" id="swal_maximum_stock" class="form-control" value="${max}">
                    </div>
                    <div class="col-md-4">
                        <label>Reorder Level</label>
                        <input type="number" id="swal_reorder_level" class="form-control" value="${reorder}">
                    </div>
                </div>
                <div class="form-group" style="margin-top:10px;">
                    <label><input type="checkbox" id="swal_active" ${active?'checked':''}> Active</label>
                </div>
            </form>`,
            width: '700px',
            showCancelButton: true,
            confirmButtonText: isEdit ? '<i class="ace-icon fa fa-save"></i> Update' : '<i class="ace-icon fa fa-save"></i> Create',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const productName = document.getElementById('swal_product_name').value.trim();
                if (!productName) {
                    Swal.showValidationMessage('Product name is required');
                    return false;
                }
                return {
                    id: document.getElementById('swal_product_id').value,
                    category_id: document.getElementById('swal_category_id').value,
                    brand_id: document.getElementById('swal_brand_id').value,
                    model_id: document.getElementById('swal_model_id').value,
                    unit_id: document.getElementById('swal_unit_id').value,
                    product_name: productName,
                    sku: document.getElementById('swal_sku').value,
                    purchase_price: document.getElementById('swal_purchase_price').value,
                    selling_price: document.getElementById('swal_selling_price').value,
                    minimum_stock: document.getElementById('swal_minimum_stock').value,
                    maximum_stock: document.getElementById('swal_maximum_stock').value,
                    reorder_level: document.getElementById('swal_reorder_level').value,
                    is_active: document.getElementById('swal_active').checked ? 1 : 0
                };
            }
        }).then(result => {
            if (result.isConfirmed && result.value) {
                saveProductFromDashboard(result.value);
            }
        });
    }

    function saveProductFromDashboard(formData) {
        Swal.fire({ title: 'Processing...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        Object.keys(formData).forEach(key => data.append(key, formData[key]));

        fetch('index.php?r=products/productlist', {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire(data.success ? {icon: 'success', title: 'Success!', text: data.message, timer: 1500, showConfirmButton: false}
                      : {icon: 'error', title: 'Error', text: data.message});
        })
        .catch(() => Swal.fire('Error', 'An error occurred', 'error'));
    }

    // ===== STOCK MODAL =====
    function openStockModal(stockData = null) {
        const isEdit = stockData !== null;
        let productOptions = '<option value="">Select Product</option>';
        (dashboardModalData.products || []).forEach(item => {
            productOptions += `<option value="${item.id}">${item.product_name}</option>`;
        });

        let warehouseOptions = '<option value="">Select Warehouse</option>';
        (dashboardModalData.warehouses || []).forEach(item => {
            warehouseOptions += `<option value="${item.id}">${item.warehouse_name}</option>`;
        });

        Swal.fire({
            title: 'Add Current Stock',
            allowOutsideClick: false,
            allowEscapeKey: false,
            html: `<form style="text-align:left;">
                <div class="row">
                    <div class="col-md-6">
                        <label>Product <span class="text-danger">*</span></label>
                        <select id="swal_product_id" class="form-control">${productOptions}</select>
                    </div>
                    <div class="col-md-6">
                        <label>Warehouse</label>
                        <select id="swal_warehouse_id" class="form-control">${warehouseOptions}</select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" id="swal_stock_quantity" class="form-control" value="0">
                    </div>
                    <div class="col-md-6">
                        <label>Cost Price</label>
                        <input type="number" id="swal_cost_price" class="form-control" value="0">
                    </div>
                </div>
            </form>`,
            width: '600px',
            showCancelButton: true,
            confirmButtonText: '<i class="ace-icon fa fa-save"></i> Add Stock',
            preConfirm: () => {
                const product = document.getElementById('swal_product_id').value;
                const quantity = document.getElementById('swal_stock_quantity').value;
                if (!product || !quantity) {
                    Swal.showValidationMessage('Product and Quantity are required');
                    return false;
                }
                return { product_id: product, warehouse_id: document.getElementById('swal_warehouse_id').value, quantity: quantity, cost_price: document.getElementById('swal_cost_price').value };
            }
        }).then(result => {
            if (result.isConfirmed && result.value) saveStockFromDashboard(result.value);
        });
    }

    function saveStockFromDashboard(formData) {
        Swal.fire({ title: 'Processing...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        Object.keys(formData).forEach(key => data.append(key, formData[key]));
        fetch('index.php?r=stock/inventorycurrentstock', { method: 'POST', body: data })
        .then(response => response.json())
        .then(data => {
            Swal.fire(data.success ? {icon: 'success', title: 'Success!', text: data.message, timer: 1500, showConfirmButton: false}
                      : {icon: 'error', title: 'Error', text: data.message});
        })
        .catch(() => Swal.fire('Error', 'An error occurred', 'error'));
    }

    // ===== SUPPLIER MODAL =====
    function openSupplierModal(supplierData = null) {
        const isEdit = supplierData !== null;
        const id = isEdit ? (supplierData.id || '') : '';
        const code = isEdit ? (supplierData.supplier_code || '') : '';
        const company = isEdit ? (supplierData.company_name || '') : '';
        const contact = isEdit ? (supplierData.contact_person || '') : '';
        const email = isEdit ? (supplierData.email || '') : '';
        const phone = isEdit ? (supplierData.phone || '') : '';
        const address = isEdit ? (supplierData.address || '') : '';
        const city = isEdit ? (supplierData.city || '') : '';
        const province = isEdit ? (supplierData.province || '') : '';
        const postal = isEdit ? (supplierData.postal_code || '') : '';
        const remarks = isEdit ? (supplierData.remarks || '') : '';

        Swal.fire({
            title: isEdit ? 'Update Supplier' : 'Add New Supplier',
            html: `<form style="text-align:left; max-height: 500px; overflow-y: auto;">
                <input type="hidden" id="swal_supplier_id" value="${id}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Supplier Code <span class="text-danger">*</span></label>
                            <input type="text" id="swal_supplier_code" class="form-control" value="${code}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Company Name <span class="text-danger">*</span></label>
                            <input type="text" id="swal_supplier_company" class="form-control" value="${company}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Contact Person</label>
                            <input type="text" id="swal_supplier_contact" class="form-control" value="${contact}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="swal_supplier_email" class="form-control" value="${email}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" id="swal_supplier_phone" class="form-control" value="${phone}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea id="swal_supplier_address" class="form-control" style="height: 60px;">${address}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" id="swal_supplier_city" class="form-control" value="${city}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Province</label>
                            <input type="text" id="swal_supplier_province" class="form-control" value="${province}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Postal Code</label>
                            <input type="text" id="swal_supplier_postal" class="form-control" value="${postal}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea id="swal_supplier_remarks" class="form-control" style="height: 60px;">${remarks}</textarea>
                </div>
            </form>`,
            width: '750px',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showCancelButton: true,
            confirmButtonText: '<i class="ace-icon fa fa-save"></i> Save',
            preConfirm: () => {
                const code = document.getElementById('swal_supplier_code').value.trim();
                const company = document.getElementById('swal_supplier_company').value.trim();
                if (!code) {
                    Swal.showValidationMessage('Supplier Code is required');
                    return false;
                }
                if (!company) {
                    Swal.showValidationMessage('Company Name is required');
                    return false;
                }
                return {
                    id,
                    supplier_code: code,
                    company_name: company,
                    contact_person: document.getElementById('swal_supplier_contact').value,
                    email: document.getElementById('swal_supplier_email').value,
                    phone: document.getElementById('swal_supplier_phone').value,
                    address: document.getElementById('swal_supplier_address').value,
                    city: document.getElementById('swal_supplier_city').value,
                    province: document.getElementById('swal_supplier_province').value,
                    postal_code: document.getElementById('swal_supplier_postal').value,
                    remarks: document.getElementById('swal_supplier_remarks').value
                };
            }
        }).then(result => {
            if (result.isConfirmed && result.value) saveSupplierFromDashboard(result.value);
        });
    }

    function saveSupplierFromDashboard(formData) {
        Swal.fire({ title: 'Processing...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        Object.keys(formData).forEach(key => data.append(key, formData[key]));
        fetch('index.php?r=supplier/supplierlist', { method: 'POST', body: data })
        .then(response => response.json())
        .then(data => {
            Swal.fire(data.success ? {icon: 'success', title: 'Success!', text: data.message, timer: 1500, showConfirmButton: false}
                      : {icon: 'error', title: 'Error', text: data.message});
        })
        .catch(() => Swal.fire('Error', 'An error occurred', 'error'));
    }

    // ===== CUSTOMER MODAL =====
    function openCustomerModal(customerData = null) {
        const isEdit = customerData !== null;
        const id = isEdit ? (customerData.id || '') : '';
        const firstName = isEdit ? (customerData.first_name || '') : '';
        const lastName = isEdit ? (customerData.last_name || '') : '';
        const companyName = isEdit ? (customerData.company_name || '') : '';
        const email = isEdit ? (customerData.email || '') : '';
        const phone = isEdit ? (customerData.phone || '') : '';
        const address = isEdit ? (customerData.address || '') : '';
        const remarks = isEdit ? (customerData.remarks || '') : '';

        Swal.fire({
            title: isEdit ? 'Update Customer' : 'Add New Customer',
            html: `<form style="text-align:left;">
                <input type="hidden" id="swal_customer_id" value="${id}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>First Name <span class="text-danger">*</span></label>
                            <input type="text" id="swal_customer_first_name" class="form-control" value="${firstName}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Last Name <span class="text-danger">*</span></label>
                            <input type="text" id="swal_customer_last_name" class="form-control" value="${lastName}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" id="swal_customer_company_name" class="form-control" value="${companyName}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="swal_customer_email" class="form-control" value="${email}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" id="swal_customer_phone" class="form-control" value="${phone}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" id="swal_customer_address" class="form-control" value="${address}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea id="swal_customer_remarks" class="form-control" rows="3">${remarks}</textarea>
                </div>
            </form>`,
            width: '700px',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showCancelButton: true,
            confirmButtonText: '<i class="ace-icon fa fa-save"></i> Save',
            preConfirm: () => {
                const firstName = document.getElementById('swal_customer_first_name').value.trim();
                const lastName = document.getElementById('swal_customer_last_name').value.trim();
                if (!firstName || !lastName) {
                    Swal.showValidationMessage('First Name and Last Name are required');
                    return false;
                }
                return {
                    id,
                    first_name: firstName,
                    last_name: lastName,
                    company_name: document.getElementById('swal_customer_company_name').value.trim(),
                    email: document.getElementById('swal_customer_email').value.trim(),
                    phone: document.getElementById('swal_customer_phone').value.trim(),
                    address: document.getElementById('swal_customer_address').value.trim(),
                    remarks: document.getElementById('swal_customer_remarks').value.trim()
                };
            }
        }).then(result => {
            if (result.isConfirmed && result.value) saveCustomerFromDashboard(result.value);
        });
    }

    function saveCustomerFromDashboard(formData) {
        Swal.fire({ title: 'Processing...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        Object.keys(formData).forEach(key => data.append(key, formData[key]));
        fetch('index.php?r=customers/addcustomer', { method: 'POST', body: data })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                Swal.fire({icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false});
                setTimeout(() => loadDashboard(), 1500);
            } else {
                Swal.fire({icon: 'error', title: 'Error', text: response.message});
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'An error occurred while saving customer', 'error');
        });
    }

    // ===== PURCHASE ORDER MODAL - EXACT COPY FROM purchaseorders.php =====
    function getProductOptions(selected = '') {
        let html = '<option value="">Select Product</option>';
        let products = dashboardModalData.products || [];
        console.log('Loading products:', products);
        if (Array.isArray(products) && products.length > 0) {
            products.forEach(p => {
                if (p && p.id && p.product_name) {
                    let displayName = p.sku ? `${p.product_name} (${p.sku})` : p.product_name;
                    html += `<option value="${p.id}" data-price="${p.selling_price || 0}" ${selected==p.id?'selected':''}>${displayName}</option>`;
                }
            });
        } else {
            console.warn('No products available');
        }
        return html;
    }

    function supplierOptions(selected = '') {
        let html = '<option value="">Select Supplier</option>';
        let suppliers = dashboardModalData.suppliers || [];
        console.log('Loading suppliers:', suppliers);
        if (Array.isArray(suppliers) && suppliers.length > 0) {
            suppliers.forEach(s => {
                if (s && s.id) {
                    // Support both field names: supplier_name and company_name
                    let supplierName = s.supplier_name || s.company_name || 'Unknown';
                    html += `<option value="${s.id}" ${selected==s.id?'selected':''}>${supplierName}</option>`;
                }
            });
        } else {
            console.warn('No suppliers available');
        }
        return html;
    }

    function warehouseOptions(selected = '') {
        let html = '';
        let warehouses = dashboardModalData.warehouses || [];
        console.log('Loading warehouses:', warehouses);
        if (Array.isArray(warehouses) && warehouses.length > 0) {
            warehouses.forEach(w => {
                if (w && w.id && w.warehouse_name) {
                    html += `<option value="${w.id}" ${selected==w.id?'selected':''}>${w.warehouse_name}</option>`;
                }
            });
        } else {
            console.warn('No warehouses available');
        }
        return html;
    }

    function statusOptions(selected = 'Draft') {
        let arr = ['Draft', 'Approved', 'Partially Received', 'Completed', 'Cancelled'];
        let html = '';
        arr.forEach(s => {
            html += `<option value="${s}" ${selected==s?'selected':''}>${s}</option>`;
        });
        return html;
    }

    function getOrderModalHtml(d = {}) {
        let supplierOpts = supplierOptions(d.supplier_id);
        let warehouseOpts = warehouseOptions(d.warehouse_id);
 

        // Get first warehouse by default
        let defaultWarehouseId = d.warehouse_id || (dashboardModalData.warehouses && dashboardModalData.warehouses.length > 0 ? dashboardModalData.warehouses[0].id : '');

        return `<form id="orderForm">
        <input type="hidden" id="swal_id" value="${d.id||''}" style="display:none">
        <input type="hidden" id="swal_warehouse" value="${defaultWarehouseId}"  style="display:none">
        <!-- Supplier, Status, GRN Status, and Invoice Status in one row -->
        <div class="row">
        <div class="col-md-3">
        <label>Supplier <span class="text-danger">*</span></label>
        <select id="swal_supplier" class="form-control">
        ${supplierOpts}
        </select>
        </div>
        <div class="col-md-3">
        <label>Status</label>
        <select id="swal_status" class="form-control">
        ${statusOptions(d.status)}
        </select>
        </div>
        <div class="col-md-3">
        <label>Goods Receiving Status</label>
        <select id="swal_gr_status" class="form-control">
        <option value="Pending" ${d.grn_status=='Pending'?'selected':''}>Pending</option>
        <option value="Completed" ${d.grn_status=='Completed'?'selected':''}>Completed</option>
        <option value="Cancelled" ${d.grn_status=='Cancelled'?'selected':''}>Cancelled</option>
        </select>
        </div>
        <div class="col-md-3">
        <label>Invoice Status</label>
        <select id="swal_invoice_status" class="form-control">
        <option value="Pending" ${d.payment_status=='Pending'?'selected':''}>Pending</option>
        <option value="Partial" ${d.payment_status=='Partial'?'selected':''}>Partial</option>
        <option value="Paid" ${d.payment_status=='Paid'?'selected':''}>Paid</option>
        </select>
        </div>
        </div>
        <div class="row">
        <div class="col-md-4">
        <label>Order Date</label>
        <input type="date" id="swal_order_date" class="form-control" value="${d.order_date||''}">
        </div>
        <div class="col-md-4">
        <label>Expected Date</label>
        <input type="date" id="swal_expected_date" class="form-control" value="${d.expected_date||''}">
        </div>
        <div class="col-md-4">
        <label>Payment Terms</label>
        <input type="text" id="swal_payment_terms" class="form-control" value="${d.payment_terms||''}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-10">
        <label>Remarks</label>
        <input type="text" id="swal_remarks" class="form-control" value="${d.notes||''}">
        </div>
        <div class="col-md-2" style="margin-top: 28px;">
        <label>&nbsp;</label>
        <button type="button" id="btnAddItem" style="padding:5px">
        <i class="fa fa-plus"></i>
        Add Item
        </button>
        </div>
        </div>
        <hr>
        <table class="table table-bordered table-striped" id="purchaseItemTable">
        <thead>
        <tr>
        <th width="30%">Product</th>
        <th width="10%">Qty</th>
        <th width="12%">Rate</th>
        <th width="18%">Total</th>
        <th>Remarks</th>
        <th width="5%"></th>
        </tr>
        </thead>
        <tbody></tbody>
        </table>
        <hr>
        <div class="row">
        <div class="col-md-3">
        <label>Subtotal</label>
        <input type="number" id="swal_subtotal" class="form-control" readonly value="${d.subtotal||0}">
        </div>
        <div class="col-md-3">
        <label>Discount</label>
        <input type="number" id="swal_discount" class="form-control" value="${d.discount||0}">
        </div>
        <div class="col-md-3">
        <label>Tax</label>
        <input type="number" id="swal_tax" class="form-control" value="${d.tax||0}">
        </div>
        <div class="col-md-3">
        <label>Freight</label>
        <input type="number" id="swal_freight" class="form-control" value="${d.freight||0}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-3">
        <label><strong>Grand Total</strong></label>
        <input type="number" id="swal_grand_total" class="form-control" readonly value="${d.grand_total||0}" style="background:#fff3cd; font-weight:bold;">
        </div>
        <div class="col-md-3">
        <label><strong>Paid Amount</strong></label>
        <input type="number" id="swal_paid_amount" class="form-control" value="${d.paid_amount||0}" step="0.01">
        </div>
        <div class="col-md-3">
        <label><strong>Remaining Balance</strong></label>
        <input type="number" id="swal_remaining_amount" class="form-control" readonly value="${(d.grand_total||0) - (d.paid_amount||0)}" style="background:#e8f4f8; font-weight:bold;">
        </div>
        <div class="col-md-3"></div>
        </div>
        </form>`;
    }

    function addPurchaseOrderRow(item = {}) {
        $('#purchaseItemTable tbody').append(`
            <tr>
            <td><select class="form-control item-product">${getProductOptions(item.product_id||'')}</select></td>
            <td><input type="number" class="form-control item-qty" value="${item.quantity||1}"></td>
            <td><input type="number" class="form-control item-rate" value="${item.unit_price||0}"></td>
            <td><input type="number" class="form-control item-total" readonly value="${item.line_total||0}"></td>
            <td><input type="text" class="form-control item-remarks" value="${item.remarks||''}"></td>
            <td><button type="button" class="remove-item"><i class="fa fa-trash"></i></button></td>
            </tr>`);
        let tr = $('#purchaseItemTable tbody tr:last');
        let productSelect = tr.find('.item-product');

        // Attach change handler BEFORE initializing chosen
        productSelect.on('change', function() {
            let selectedValue = $(this).val();
            if (selectedValue) {
                let selectedOption = $(this).find('option[value="' + selectedValue + '"]');
                let price = parseFloat(selectedOption.data('price')) || 0;
                tr.find('.item-rate').val(price.toFixed(2));
                calculateRow(tr);
            }
        });

        // Initialize chosen
        productSelect.chosen({width: '100%', search_contains: true});
        calculateRow(tr);
    }

    $(document).on('click', '#btnAddItem', function() {
        addPurchaseOrderRow();
    });


    $(document).on('input', '.item-qty,.item-rate', function() {
        calculateRow($(this).closest('tr'));
    });

    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    function calculateRow(tr) {
        let qty = parseFloat(tr.find('.item-qty').val()) || 0;
        let rate = parseFloat(tr.find('.item-rate').val()) || 0;
        let total = qty * rate;
        tr.find('.item-total').val(total.toFixed(2));
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        $('#purchaseItemTable tbody tr').each(function() {
            subtotal += parseFloat($(this).find('.item-total').val()) || 0;
        });
        $('#swal_subtotal').val(subtotal.toFixed(2));
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let subtotal = parseFloat($('#swal_subtotal').val()) || 0;
        let freight = parseFloat($('#swal_freight').val()) || 0;
        $('#swal_grand_total').val((subtotal + freight).toFixed(2));
    }

    $(document).on('input', '#swal_discount,#swal_tax,#swal_freight', updateGrandTotal);

    function collectPurchaseItems() {
        let items = [];
        $('#purchaseItemTable tbody tr').each(function() {
            items.push({
                product_id: $(this).find('.item-product').val(),
                quantity: $(this).find('.item-qty').val(),
                unit_price: $(this).find('.item-rate').val(),
                discount_amount: 0,
                tax_amount: 0,
                line_total: $(this).find('.item-total').val(),
                remarks: $(this).find('.item-remarks').val()
            });
        });
        return items;
    }

    function validatePurchaseOrder() {
        if (!$('#swal_supplier').val()) {
            Swal.showValidationMessage('Please select supplier.');
            return false;
        }
        if (!$('#swal_warehouse').val()) {
            Swal.showValidationMessage('Please select warehouse.');
            return false;
        }
        if (!$('#swal_order_date').val()) {
            Swal.showValidationMessage('Please select order date.');
            return false;
        }
        let items = collectPurchaseItems();
        if (items.length == 0) {
            Swal.showValidationMessage('Please add at least one item.');
            return false;
        }
        let ok = true;
        items.forEach(function(r) {
            if (!r.product_id || parseFloat(r.quantity) <= 0) {
                ok = false;
            }
        });
        if (!ok) {
            Swal.showValidationMessage('Please complete all item rows.');
            return false;
        }
        return items;
    }

    function loadOrder(id) {
        console.log('loadOrder called with id:', id);

        Swal.fire({
            title: 'Preparing Purchase Order...',
            html: '<p>Loading suppliers, warehouses, and products...</p>',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        // Load data when modal is clicked (not on page load)
        loadDashboardModalData().then(() => {
            console.log('Data loaded successfully');

            if (id) {
                // Load existing order
                const fd = new FormData();
                fd.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
                fd.append('flag', 'getOrder');
                fd.append('id', id);

                fetch('index.php?r=purchase/purchaseorders', {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(r => {
                    Swal.close();
                    if (!r.success) {
                        Swal.fire('Error', r.message, 'error');
                        return;
                    }
                    r.order.items = r.items || [];
                    showOrderModal(r.order);
                })
                .catch((error) => {
                    Swal.close();
                    console.error('Error loading order:', error);
                    Swal.fire('Error', 'Unable to load purchase order.', 'error');
                });
            } else {
                // Create new order - show modal immediately
                setTimeout(() => {
                    Swal.close();
                    showOrderModal({});
                }, 100);
            }
        }).catch((error) => {
            Swal.close();
            console.error('Error loading data:', error);
            Swal.fire('Error', 'Failed to load data. Please try again.', 'error');
        });
    }

    function saveOrder(formData) {
        const isCompleted = formData.status === 'Completed';
        const title = isCompleted ? 'Processing Purchase Order & Updating Stock...' : 'Saving Purchase Order...';

        Swal.fire({
            title: title,
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const fd = new FormData();
        fd.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        Object.keys(formData).forEach(function(key) {
            fd.append(key, formData[key]);
        });

        // If status is Completed, also mark as needing stock update
        if (isCompleted) {
            fd.append('update_stock', '1');
        }

        fetch('index.php?r=purchase/purchaseorders', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(function(res) {
            if (!res.success) {
                Swal.fire({icon: 'error', title: 'Error', text: res.message || 'Unable to save purchase order.'});
                return;
            }

            // Show success modal with PO details
            let paidAmount = parseFloat(formData.paid_amount) || 0;
            let grandTotal = parseFloat(formData.grand_total) || 0;
            let remainingAmount = grandTotal - paidAmount;

            let successHtml = `
                <div style="text-align: left;">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>PO Number:</strong></td>
                            <td style="padding: 8px; border-bottom: 1px solid #ddd;">${res.po_number || '-'}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>GRN Number:</strong></td>
                            <td style="padding: 8px; border-bottom: 1px solid #ddd;">${res.grn_number || '-'}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Reference No:</strong></td>
                            <td style="padding: 8px; border-bottom: 1px solid #ddd;">${res.reference_no || '-'}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Invoice No:</strong></td>
                            <td style="padding: 8px; border-bottom: 1px solid #ddd;">${res.invoice_no || '-'}</td>
                        </tr>
                        <tr style="background: #f9f9f9;">
                            <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Grand Total:</strong></td>
                            <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">PKR ${grandTotal.toLocaleString()}</td>
                        </tr>
                        <tr style="background: #e8f4f8;">
                            <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Paid Amount:</strong></td>
                            <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold; color: #27ae60;">PKR ${paidAmount.toLocaleString()}</td>
                        </tr>
                        <tr style="background: #fff3cd;">
                            <td style="padding: 8px;"><strong>Remaining Amount:</strong></td>
                            <td style="padding: 8px; font-weight: bold; color: #e74c3c;">PKR ${remainingAmount.toLocaleString()}</td>
                        </tr>
                    </table>
                </div>
            `;

            Swal.fire({
                title: '✅ Purchase Order Saved Successfully!',
                html: successHtml,
                icon: 'success',
                width: '600px',
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'OK',
                confirmButtonColor: '#27ae60',
                didOpen: (modal) => {
                    // Add Print button
                    const printBtn = document.createElement('button');
                    printBtn.innerHTML = '<i class="fa fa-print"></i> Print PO';
                    printBtn.className = 'swal2-button swal2-styled';
                    printBtn.style.backgroundColor = '#3498db';
                    printBtn.style.marginRight = '10px';
                    printBtn.onclick = () => {
                        let poId = res.po_id || formData.id;
                        window.open('index.php?r=documents/purchaseorder&id=' + poId, '_blank');
                    };
                    modal.querySelector('.swal2-actions').insertBefore(printBtn, modal.querySelector('.swal2-confirm'));
                }
            }).then(() => {
                loadDashboard();
            });
        })
        .catch(function(error) {
            console.log(error);
            Swal.fire({icon: 'error', title: 'Error', text: 'Unable to communicate with server.'});
        });
    }

    function showOrderModal(data = {}) {
        const isEdit = !!data.id;
        Swal.fire({
            title: isEdit ? 'Update Purchase Order' : 'Add Purchase Order',
            width: '1100px',
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {popup: 'swal-wide-popup'},
            html: getOrderModalHtml(data),
            showCancelButton: true,
            confirmButtonText: isEdit ? 'Update Order' : 'Save Order',
            confirmButtonColor: '#87B87F',
            cancelButtonText: 'Cancel',
            didOpen: function() {
                // Ensure dropdowns are properly initialized
                setTimeout(() => {
                    // Destroy existing chosen instances if any
                    $('#swal_supplier').off().chosen('destroy').chosen({width: '100%', search_contains: true});
                    // $('#swal_warehouse').off().chosen('destroy').chosen({width: '100%', search_contains: true});
                    $('#swal_status').off().chosen('destroy').chosen({width: '100%', search_contains: true});
                    $('#swal_gr_status').chosen({width: '100%', search_contains: true});

                    // Auto-update GRN status when PO status changes to Completed
                    $('#swal_status').on('change', function() {
                        if ($(this).val() === 'Completed') {
                            $('#swal_gr_status').val('Completed').trigger('chosen:updated');
                        }
                    });

                    // Update remaining balance when paid amount changes
                    $('#swal_paid_amount').on('input', function() {
                        let grandTotal = parseFloat($('#swal_grand_total').val()) || 0;
                        let paidAmount = parseFloat($(this).val()) || 0;
                        let remaining = grandTotal - paidAmount;
                        $('#swal_remaining_amount').val(Math.max(0, remaining).toFixed(2));
                    });

                    $('#swal_discount,#swal_tax,#swal_freight').on('input', updateGrandTotal);

                    if (isEdit) {
                        (data.items || []).forEach(i => addPurchaseOrderRow(i));
                    } else {
                        addPurchaseOrderRow();
                    }

                    // Ensure focus is on the modal
                    $('.swal2-container').focus();
                }, 100);
            },
            preConfirm: () => {
                let items = validatePurchaseOrder();
                if (items === false) return false;
                return {
                    id: $('#swal_id').val(),
                    supplier_id: $('#swal_supplier').val(),
                    warehouse_id: $('#swal_warehouse').val(),
                    order_date: $('#swal_order_date').val(),
                    expected_date: $('#swal_expected_date').val(),
                    payment_terms: $('#swal_payment_terms').val(),
                    status: $('#swal_status').val(),
                    grn_status: $('#swal_gr_status').val(),
                    payment_status: $('#swal_invoice_status').val(),
                    subtotal: $('#swal_subtotal').val(),
                    discount_amount: $('#swal_discount').val(),
                    tax_amount: $('#swal_tax').val(),
                    shipping_amount: $('#swal_freight').val(),
                    grand_total: $('#swal_grand_total').val(),
                    paid_amount: $('#swal_paid_amount').val(),
                    remarks: $('#swal_remarks').val(),
                    items: JSON.stringify(items),
                    flag: 'save'
                };
            }
        }).then(r => {
            if (r.isConfirmed) saveOrder(r.value);
        });
    }

    // ===== SALES ORDER MODAL - EXACT COPY FROM salesorders.php =====
    function customerName(c) {
        return c.customer_name || c.name || '';
    }

    function openOrderModal(orderData = null) {
        // Show modal immediately, load data in background
        showSalesOrderModal(orderData);

        // Load data in background if needed
        if (!window.saleOrderViewData || !window.saleOrderViewData.customers || window.saleOrderViewData.customers.length === 0) {
            loadDashboardModalData().then(() => {
                console.log('Sales order data loaded successfully');
                // Update modal with fresh data if it's still open
                if (document.querySelector('.swal2-popup')) {
                    const currentWarehouse = $('#so_warehouse').val();
                    if (currentWarehouse) {
                        loadProductsForWarehouse(currentWarehouse);
                    }
                }
            }).catch((error) => {
                console.error('Error loading sales order data:', error);
            });
        }
    }

    function showSalesOrderModal(orderData = null) {
        const isEdit = orderData !== null && orderData.id;
        const id = isEdit ? orderData.id : '';
        const customerId = isEdit ? orderData.customer_id : '';
        const warehouseId = isEdit ? orderData.warehouse_id : '';
        const orderDate = isEdit ? orderData.order_date : '<?= date('Y-m-d') ?>';
        const deliveryDate = isEdit ? orderData.delivery_date : '<?= date('Y-m-d') ?>';
        const orderStatus = isEdit ? orderData.order_status : 'Draft';
        const paymentStatus = isEdit ? orderData.payment_status : 'Pending';
        const subtotal = isEdit ? orderData.subtotal : 0;
        const discount = isEdit ? orderData.discount : 0;
        const tax = isEdit ? orderData.tax : 0;
        const shipping = isEdit ? orderData.shipping : 0;
        const grandTotal = isEdit ? orderData.grand_total : 0;
        const paidAmount = isEdit ? (orderData.paid_amount || 0) : 0;
        const notes = isEdit ? (orderData.notes || '') : '';

        // Initialize saleOrderViewData for dashboard with loaded data
        if (!window.saleOrderViewData) {
            window.saleOrderViewData = {
                customers: dashboardModalData.customers || [],
                warehouses: dashboardModalData.warehouses || [],
                products: dashboardModalData.products || []
            };
        } else {
            // Update with latest data
            window.saleOrderViewData.customers = dashboardModalData.customers || window.saleOrderViewData.customers;
            window.saleOrderViewData.warehouses = dashboardModalData.warehouses || window.saleOrderViewData.warehouses;
            window.saleOrderViewData.products = dashboardModalData.products || window.saleOrderViewData.products;
        }

        let customerOptions = '<option value="">Walk-in Customer</option>';
        window.saleOrderViewData.customers.forEach(c => {
            customerOptions += `<option value="${c.id}" ${c.id==customerId?'selected':''}>${customerName(c)}</option>`;
        });

        // Set first warehouse as default
        let defaultWarehouseId = warehouseId || (window.saleOrderViewData.warehouses && window.saleOrderViewData.warehouses.length > 0 ? window.saleOrderViewData.warehouses[0].id : '');

        let warehouseOptions = '';
        window.saleOrderViewData.warehouses.forEach(w => {
            warehouseOptions += `<option value="${w.id}" ${w.id==defaultWarehouseId?'selected':''}>${w.warehouse_name}</option>`;
        });

        const statusList = ['Draft', 'Confirmed', 'Packed', 'Dispatched', 'Delivered', 'Cancelled'];
        let statusOptions = '';
        statusList.forEach(s => {
            statusOptions += `<option value="${s}" ${s==orderStatus?'selected':''}>${s}</option>`;
        });

        const paymentStatusList = ['Pending', 'Partial', 'Paid'];
        let paymentStatusOptions = '';
        paymentStatusList.forEach(s => {
            paymentStatusOptions += `<option value="${s}" ${s==paymentStatus?'selected':''}>${s}</option>`;
        });

        Swal.fire({
            title: isEdit ? 'Edit Sale Order' : 'New Sale Order',
            width: '1100px',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                // const popup = document.querySelector('.swal2-popup');
                // if (popup) {
                //     popup.style.width = '96vw';
                //     popup.style.maxWidth = '96vw';
                //     popup.style.maxHeight = '96vh';
                // }
                setupSaleOrderModal(isEdit, id);
            },
            html: `<form id="saleOrderForm">
                <input type="hidden" id="so_id" value="${id}">
                <input type="hidden" id="so_warehouse" value="${defaultWarehouseId}">
                <div class="row">
                <div class="col-md-3" style="display:none;">
                <label>Warehouse<span style="color:red;">*</span></label>
                <select id="so_warehouse_display" class="form-control" disabled>
                ${warehouseOptions}
                </select>
                </div>
                <div class="col-md-3">
                <label>Customer<span style="color:red;">*</span></label>
                <select id="so_customer" class="form-control">
                ${customerOptions}
                </select>
                </div>
                <div class="col-md-3">
                <label>Order Date</label>
                <input type="date" id="so_order_date" class="form-control" value="${orderDate}">
                </div>
                <div class="col-md-3" style="display:none">
                <label>Delivery Date</label>
                <input type="date" id="so_delivery_date" class="form-control" value="${deliveryDate}">
                </div>
                <div class="col-md-3">
                <label>Order Status</label>
                <select id="so_order_status" class="form-control">
                ${statusOptions}
                </select>
                </div>
                <div class="col-md-3">
                <label>Payment Status</label>
                <select id="so_payment_status" class="form-control" style="background:#f5f5f5;">
                ${paymentStatusOptions}
                </select>
                </div>
                </div>
                
                <div id="walkinFields" style="margin-top:15px; padding:15px; background:#f9f9f9; border:1px solid #ddd; border-radius:4px;">
                <div class="row">
                <div class="col-md-3">
                <label>Customer Name<span style="color:red;">*</span></label>
                <input type="text" id="so_customer_name" class="form-control" placeholder="Enter name">
                </div>
                <div class="col-md-3">
                <label>Email</label>
                <input type="email" id="so_customer_email" class="form-control" placeholder="email@example.com">
                </div>
                <div class="col-md-3">
                <label>Phone<span style="color:red;">*</span></label>
                <input type="text" id="so_customer_phone" class="form-control" placeholder="Phone number">
                </div>
                <div class="col-md-3">
                <label>Reference</label>
                <input type="text" id="so_customer_reference" class="form-control" placeholder="Reference no">
                </div>
                </div>
                </div>
                <div id="customerDetails" style="display:none; margin-top:10px; padding:10px; background:#e8f4f8; border:1px solid #b3d9e8; border-radius:4px;">
                <div class="row">
                <div class="col-md-3"><strong>Name:</strong> <span id="detailName"></span></div>
                <div class="col-md-3"><strong>Email:</strong> <span id="detailEmail"></span></div>
                <div class="col-md-3"><strong>Phone:</strong> <span id="detailPhone"></span></div>
                <div class="col-md-3"><strong>Ref:</strong> <span id="detailRef"></span></div>
                </div>
                </div>
                <div class="row" style="margin-top:10px;">
                <div class="col-md-6">
                <label>Notes</label>
                <input type="text" id="so_notes" class="form-control" placeholder="Add notes or remarks" value="${notes}">
                </div>
                <div class="col-md-2" style="margin-top: 25px;">
                <button type="button" id="btnAddItem" style="width:100%;padding: 6px;">Add Item</button>
                </div>
                </div>
                <table class="table table-bordered table-striped" style="margin-top: 15px;" id="saleItemTable">
                <thead>
                <tr>
                <th>Product</th>
                <th width="80px">Available</th>
                <th width="80px">Qty</th>
                <th width="100px">Rate</th>
                <th width="100px">Discount</th>
                <th width="100px">Tax</th>
                <th width="100px">Total</th>
                <th width="5%"></th>
                </tr>
                </thead>
                <tbody></tbody>
                </table>
                <div class="row" style="margin-top:15px;">
                <div class="col-md-2">
                <label>Subtotal</label>
                <input type="number" id="so_subtotal" class="form-control" readonly value="0" style="background:#f5f5f5;">
                </div>
                <div class="col-md-2">
                <label>Discount</label>
                <input type="number" id="so_discount" class="form-control" value="${discount}" step="0.01" placeholder="0.00">
                </div>
                <div class="col-md-2">
                <label>Tax</label>
                <input type="number" id="so_tax" class="form-control" value="${tax}" step="0.01" placeholder="0.00">
                </div>
                <div class="col-md-2">
                <label>Shipping</label>
                <input type="number" id="so_shipping" class="form-control" value="${shipping}" step="0.01" placeholder="0.00">
                </div>
                <div class="col-md-2">
                <label><strong>Grand Total</strong></label>
                <input type="number" id="so_grand_total"  class="form-control" readonly value="0" style="background:#fff3cd; font-weight:bold;">
                </div>
                </div>
                <div class="row" style="margin-top:10px;">
                <div class="col-md-3">
                <label>Paid Amount</label>
                <input type="number" id="so_paid_amount" class="form-control" value="${paidAmount}" step="0.01" placeholder="0.00">
                </div>
                <div class="col-md-3">
                <label><strong>Remaining Amount</strong></label>
                <input type="number" id="so_remaining_amount" class="form-control" readonly value="${(grandTotal - paidAmount).toFixed(2)}" style="background:#e8f4f8; font-weight:bold;">
                </div>
                </div>
                </form>`,
            showCancelButton: true,
            confirmButtonText: isEdit ? 'Update Order' : 'Save Order',
            confirmButtonColor: '#87B87F',
            cancelButtonText: 'Cancel',
            preConfirm: () => validateAndSubmitOrder(isEdit)
        }).then(r => {
            if (r.isConfirmed) saveSaleOrder(r.value);
        });
    }

    function setupSaleOrderModal(isEdit, orderId) {
        window.saleOrderIsEdit = isEdit;

        setTimeout(() => {
            // Initialize dropdowns with chosen (warehouse is now hidden, so skip it)
            try {
                $('#so_customer').off().chosen('destroy').chosen({ width: '100%', search_contains: true });
            } catch(e) {
                // If destroy fails, just initialize
                $('#so_customer').chosen({ width: '100%', search_contains: true });
            }

            // Customer change event
            $('#so_customer').off('change').on('change', function() {
                const customerId = $(this).val();
                if (customerId === '') {
                    $('#walkinFields').show();
                    $('#customerDetails').hide();
                    $('#customerExistsMessage').hide();
                } else {
                    $('#walkinFields').hide();
                    $('#customerExistsMessage').hide();
                    const customer = window.saleOrderViewData.customers.find(c => c.id == customerId);
                    if (customer) {
                        $('#detailName').text(customerName(customer));
                        $('#detailEmail').text(customer.email || 'N/A');
                        $('#detailPhone').text(customer.phone || customer.mobile || 'N/A');
                        $('#detailRef').text(customer.customer_code || 'N/A');
                        $('#customerDetails').show();
                    }
                }
            });

            // Customer name/email search for existing customers (on blur)
            $(document).off('blur', '#so_customer_name, #so_customer_email').on('blur', '#so_customer_name, #so_customer_email', function() {
                const name = $('#so_customer_name').val().trim();
                const email = $('#so_customer_email').val().trim();

                // Only search if at least one field has content and is reasonably long
                if ((name.length >= 2 || email.length >= 3)) {
                    fetch('index.php?r=inventory/search-customer', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: '_csrf=<?= Yii::$app->request->getCsrfToken() ?>&name=' + encodeURIComponent(name) + '&email=' + encodeURIComponent(email)
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success && data.customer) {
                            // Customer found - auto-select and show message
                            $('#so_customer').val(data.customer.id).trigger('chosen:updated');

                            // Remove old message if exists
                            $('#customerExistsMessage').remove();

                            // Show existing customer message
                            let messageHtml = '<div id="customerExistsMessage" style="padding:10px; background:#e8f5e9; border:1px solid #4caf50; border-radius:4px; color:#2e7d32; margin-top:10px; display:flex; align-items:center; gap:8px;"><i class="fa fa-check-circle" style="font-size:18px;"></i><span><strong>✓ Customer already exists!</strong> Selected from database.</span></div>';
                            $('#walkinFields').after(messageHtml);

                            // Update customer details
                            const customer = data.customer;
                            $('#detailName').text(data.customer_name);
                            $('#detailEmail').text(customer.email || 'N/A');
                            $('#detailPhone').text(customer.phone || 'N/A');
                            $('#detailRef').text(customer.id);
                            $('#customerDetails').show();

                            // Trigger change event to hide walk-in fields
                            $('#so_customer').trigger('change');
                        } else {
                            // Customer not found - hide message if it exists
                            $('#customerExistsMessage').remove();
                        }
                    })
                    .catch(error => {
                        console.error('Error searching customer:', error);
                    });
                } else {
                    // Clear message if input is too short
                    $('#customerExistsMessage').remove();
                }
            });

            // Add Item button
            document.getElementById('btnAddItem').onclick = function(e) {
                e.preventDefault();
                addSaleOrderRow();
                calculateSaleOrderTotals();
            };

            // Remove old event handlers
            $(document).off('change', '#saleItemTable .item-product');
            $(document).off('input', '#saleItemTable .item-qty, #saleItemTable .item-rate, #saleItemTable .item-discount, #saleItemTable .item-tax');
            $(document).off('click', '#saleItemTable .remove-item');

            // Product change event
            $(document).on('change', '#saleItemTable .item-product', function() {
                let tr = $(this).closest('tr');
                let productId = $(this).val();
                let product = window.saleOrderViewData.products.find(p => p.id == productId);
                if (!productId) {
                    clearWarning();
                    return;
                }
                let isDuplicate = false;
                $('#saleItemTable tbody tr').each(function() {
                    if ($(this)[0] !== tr[0]) {
                        let otherProductId = $(this).find('.item-product').val();
                        if (otherProductId == productId) {
                            isDuplicate = true;
                            return false;
                        }
                    }
                });
                if (isDuplicate) {
                    showWarning('This product is already added to the order. Please select a different product.');
                    $(this).val('').trigger('chosen:updated');
                    return;
                }
                clearWarning();
                if (product) {
                    tr.find('.item-rate').val(parseFloat(product.selling_price || 0).toFixed(2));
                    tr.find('.available-qty').text(parseFloat(product.available_quantity || 0).toFixed(2));
                    tr.find('.item-qty').attr('max', parseFloat(product.available_quantity || 0));
                    tr.find('.item-qty').val(1);
                    tr.find('.item-qty').trigger('input');
                }
            });

            // Row input event
            $(document).on('input', '#saleItemTable .item-qty, #saleItemTable .item-rate, #saleItemTable .item-discount, #saleItemTable .item-tax', function() {
                let tr = $(this).closest('tr');
                calculateSaleOrderRow(tr);
                calculateSaleOrderTotals();
            });

            // Paid amount change event to recalculate remaining amount
            $(document).off('input', '#so_paid_amount').on('input', '#so_paid_amount', function() {
                let grandTotal = parseFloat($('#so_grand_total').val()) || 0;
                let paidAmount = parseFloat($(this).val()) || 0;
                let remaining = Math.max(0, grandTotal - paidAmount);
                $('#so_remaining_amount').val(remaining.toFixed(2));
            });

            // Discount, Tax, Shipping change events to recalculate grand total
            $(document).off('input', '#so_discount, #so_tax, #so_shipping').on('input', '#so_discount, #so_tax, #so_shipping', function() {
                calculateSaleOrderTotals();
            });

            // Remove item event
            $(document).on('click', '#saleItemTable .remove-item', function() {
                $(this).closest('tr').remove();
                calculateSaleOrderTotals();
            });

            // Load products for selected warehouse (hidden, but still load them)
            loadProductsForWarehouse($('#so_warehouse').val());

        }, 150);
    }

    function loadProductsForWarehouse(warehouseId) {
        if (!warehouseId) {
            window.saleOrderViewData.products = [];
            return;
        }
        // Fetch products with available quantity > 0 from database
        fetch('index.php?r=inventory/get-available-products', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: '_csrf=<?= Yii::$app->request->getCsrfToken() ?>&warehouse_id=' + warehouseId
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && Array.isArray(data.products)) {
                window.saleOrderViewData.products = data.products;
                console.log('Loaded ' + data.products.length + ' available products');
            } else {
                console.warn('Failed to load products:', data.message);
                window.saleOrderViewData.products = dashboardModalData.products || [];
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            window.saleOrderViewData.products = dashboardModalData.products || [];
        });
    }

    function addSaleOrderRow(item = {}) {
        const quantity = item.quantity || item.qty || 1;
        const unitPrice = item.unit_price || item.price || 0;
        const discountAmount = item.discount_amount || item.discount || 0;
        const taxAmount = item.tax_amount || item.tax || 0;
        const lineTotal = item.line_total || item.total || 0;
        const productId = item.product_id || '';

        // Load fresh products from database with available quantity > 0
        fetch('index.php?r=inventory/get-available-products', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: '_csrf=<?= Yii::$app->request->getCsrfToken() ?>&warehouse_id=' + $('#so_warehouse').val()
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && Array.isArray(data.products)) {
                window.saleOrderViewData.products = data.products;

                let productOptions = '<option value="">-- Select Product --</option>';
                let currentProductFound = false;
                window.saleOrderViewData.products.forEach(p => {
                    const available = parseFloat(p.available_quantity || 0).toFixed(2);
                    const selected = item.product_id && p.id == item.product_id ? 'selected' : '';
                    productOptions += `<option value="${p.id}" data-price="${p.selling_price}" data-qty="${available}" ${selected}>${p.product_name} (${p.sku}) - Avail: ${available}</option>`;
                    if (item.product_id && p.id == item.product_id) {
                        currentProductFound = true;
                    }
                });
                if (item.product_id && !currentProductFound) {
                    const available = parseFloat(item.available_quantity || 0).toFixed(2);
                    productOptions += `<option value="${item.product_id}" data-price="${item.unit_price || 0}" data-qty="${available}" selected>${item.product_name} (${item.sku}) - Avail: ${available} (Out of Stock)</option>`;
                }

                // Add the row with fresh products
                addSaleOrderRowToTable(productOptions, quantity, unitPrice, discountAmount, taxAmount, lineTotal, productId);
            } else {
                Swal.fire('Warning', 'No products with available quantity found', 'warning');
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            Swal.fire('Error', 'Failed to load products', 'error');
        });
    }

    function addSaleOrderRowToTable(productOptions, quantity, unitPrice, discountAmount, taxAmount, lineTotal, productId) {
        // Initialize with 0, will be updated when product is selected
        let initialAvailableQty = '0';

        // If productId is provided, find its available quantity
        if (productId) {
            const product = window.saleOrderViewData.products.find(p => p.id == productId);
            if (product) {
                initialAvailableQty = parseFloat(product.available_quantity || 0).toFixed(2);
            }
        }

        $('#saleItemTable tbody').append(`<tr data-original-qty="${parseFloat(quantity).toFixed(2)}" data-product-id="${productId}">
            <td><select class="form-control item-product chzn-select" style="width:100%;" data-product-id="${productId}">${productOptions}</select></td>
            <td><span class="available-qty">${initialAvailableQty}</span></td>
            <td><input type="number" class="form-control item-qty" value="${parseFloat(quantity).toFixed(2)}" min="1" step="0.01" max="999999"></td>
            <td><input type="number" class="form-control item-rate" value="${parseFloat(unitPrice).toFixed(2)}" step="0.01"></td>
            <td><input type="number" class="form-control item-discount" value="${parseFloat(discountAmount).toFixed(2)}" step="0.01"></td>
            <td><input type="number" class="form-control item-tax" value="${parseFloat(taxAmount).toFixed(2)}" step="0.01"></td>
            <td><input type="number" class="form-control item-total" readonly value="${parseFloat(lineTotal).toFixed(2)}" step="0.01"></td>
            <td><button type="button" class="remove-item"><i class="fa fa-trash"></i></button></td>
            </tr>`);
        const newRow = $('#saleItemTable tbody tr:last');
        const newSelect = newRow.find('.item-product');
        try {
            newSelect.off().chosen('destroy').chosen({width: '100%', search_contains: true});
        } catch(e) {
            newSelect.chosen({width: '100%', search_contains: true});
        }
        if (productId) {
            newSelect.val(productId).trigger('chosen:updated').trigger('change');
        }
    }

    function calculateSaleOrderRow(tr) {
        let qty = parseFloat(tr.find('.item-qty').val()) || 0;
        let rate = parseFloat(tr.find('.item-rate').val()) || 0;
        let discount = parseFloat(tr.find('.item-discount').val()) || 0;
        let tax = parseFloat(tr.find('.item-tax').val()) || 0;
        let availableQty = parseFloat(tr.find('.available-qty').text()) || 0;
        let productName = tr.find('.item-product option:selected').text();
        let maxAllowed = availableQty;
        if (window.saleOrderIsEdit) {
            let originalQty = parseFloat(tr.attr('data-original-qty')) || 0;
            maxAllowed = availableQty + originalQty;
        }
        if (qty > maxAllowed) {
            tr.find('.item-qty').val(maxAllowed).css('border', '2px solid #ff6b6b');
            qty = maxAllowed;
            showWarning(`Invalid Qty for ${productName}: Cannot exceed available quantity of ${availableQty}. Qty reset to ${availableQty}.`);
        } else {
            tr.find('.item-qty').css('border', '');
        }
        let total = (qty * rate) - discount + tax;
        tr.find('.item-total').val(Math.max(0, total).toFixed(2));
    }

    function showWarning(message) {
        let warningDiv = $('#saleOrderWarning');
        if (warningDiv.length === 0) {
            warningDiv = $(`<div id="saleOrderWarning" style="color: #d9534f; font-size: 12px; margin-bottom: 10px;margin-top:5px; padding: 8px 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;"></div>`);
            $('#saleItemTable').before(warningDiv);
        }
        warningDiv.html('<i class="fa fa-exclamation-circle"></i> ' + message).show();
    }

    function clearWarning() {
        $('#saleOrderWarning').fadeOut(300, function() { $(this).hide(); });
    }

    function calculateSaleOrderTotals() {
        // Calculate subtotal from line items (qty * rate for each item)
        let subtotal = 0;
        $('#saleItemTable tbody tr').each(function() {
            let qty = parseFloat($(this).find('.item-qty').val()) || 0;
            let rate = parseFloat($(this).find('.item-rate').val()) || 0;
            subtotal += (qty * rate);
        });

        // Get discount, tax, and shipping from form fields (allow user to edit)
        let discount = parseFloat($('#so_discount').val()) || 0;
        let tax = parseFloat($('#so_tax').val()) || 0;
        let shipping = parseFloat($('#so_shipping').val()) || 0;

        // Calculate grand total: subtotal - discount + tax + shipping
        let grand = subtotal - discount + tax + shipping;

        // Update subtotal field
        $('#so_subtotal').val(Math.max(0, subtotal).toFixed(2));

        // Update grand total field
        $('#so_grand_total').val(Math.max(0, grand).toFixed(2));

        // Update remaining amount based on paid amount
        let paidAmount = parseFloat($('#so_paid_amount').val()) || 0;
        let remaining = grand - paidAmount;
        $('#so_remaining_amount').val(Math.max(0, remaining).toFixed(2));
    }

    function validateAndSubmitOrder(isEdit) {
        if (!$('#so_warehouse').val()) {
            Swal.showValidationMessage('Please select warehouse');
            return false;
        }
        if (!$('#so_customer').val() && !$('#so_customer_name').val()) {
            Swal.showValidationMessage('Please select or enter customer');
            return false;
        }
        let items = [];
        $('#saleItemTable tbody tr').each(function() {
            items.push({
                product_id: $(this).find('.item-product').val(),
                quantity: $(this).find('.item-qty').val(),
                unit_price: $(this).find('.item-rate').val(),
                discount_amount: $(this).find('.item-discount').val(),
                tax_amount: $(this).find('.item-tax').val(),
                line_total: $(this).find('.item-total').val()
            });
        });
        if (items.length == 0) {
            Swal.showValidationMessage('Please add at least one item');
            return false;
        }
        return {
            id: $('#so_id').val(),
            warehouse_id: $('#so_warehouse').val(),
            customer_id: $('#so_customer').val(),
            order_date: $('#so_order_date').val(),
            delivery_date: $('#so_delivery_date').val(),
            order_status: $('#so_order_status').val(),
            payment_status: $('#so_payment_status').val(),
            subtotal: $('#so_subtotal').val(),
            discount: $('#so_discount').val(),
            tax: $('#so_tax').val(),
            shipping: $('#so_shipping').val(),
            grand_total: $('#so_grand_total').val(),
            paid_amount: $('#so_paid_amount').val(),
            notes: $('#so_notes').val(),
            customer_name: $('#so_customer_name').val(),
            customer_email: $('#so_customer_email').val(),
            customer_phone: $('#so_customer_phone').val(),
            customer_reference: $('#so_customer_reference').val(),
            items: JSON.stringify(items),
            flag: 'save'
        };
    }

    function saveSaleOrder(formData) {
        // Show loading modal
        Swal.fire({
            title: 'Processing Sale Order',
            html: '<div style="text-align: center; padding: 20px;"><i class="fa fa-spinner fa-spin" style="font-size: 48px; color: #3498db; margin-bottom: 20px;"></i><p style="font-size: 16px; color: #666; margin-top: 20px;">Please wait while we save your order...</p></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const fd = new FormData();
        fd.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        Object.keys(formData).forEach(key => fd.append(key, formData[key]));
        fetch('index.php?r=sale/salesorders', {method: 'POST', body: fd})
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                // Display order details after successful save
                let detailsHtml = `
                    <div style="text-align:left; margin:20px 0;">
                        <h4 style="color:#2ecc71; margin-bottom:20px; font-size:18px;"><i class="fa fa-check-circle"></i> Sale Order Created Successfully!</h4>
                        <table style="width:100%; border-collapse:collapse;">
                            <tr style="border-bottom:1px solid #e0e0e0; background:#f9f9f9;">
                                <td style="padding:12px 15px; font-weight:bold; width:40%;">Order Number:</td>
                                <td style="padding:12px 15px; color:#0066cc; font-weight:600;">${res.order_number || 'N/A'}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #e0e0e0;">
                                <td style="padding:12px 15px; font-weight:bold;">Invoice Number:</td>
                                <td style="padding:12px 15px; color:#0066cc; font-weight:600;">${res.invoice_number || 'N/A'}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #e0e0e0; background:#f9f9f9;">
                                <td style="padding:12px 15px; font-weight:bold;">Order Date:</td>
                                <td style="padding:12px 15px;">${res.order_date || 'N/A'}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #e0e0e0;">
                                <td style="padding:12px 15px; font-weight:bold;">Customer:</td>
                                <td style="padding:12px 15px;">${res.customer_name || 'Walk-in Customer'}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #e0e0e0; background:#f9f9f9;">
                                <td style="padding:12px 15px; font-weight:bold;">Total Items:</td>
                                <td style="padding:12px 15px;">${res.items_count || 0}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #e0e0e0;">
                                <td style="padding:12px 15px; font-weight:bold;">Subtotal:</td>
                                <td style="padding:12px 15px;">PKR ${parseFloat(res.subtotal || 0).toLocaleString()}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #e0e0e0; background:#f9f9f9;">
                                <td style="padding:12px 15px; font-weight:bold;">Tax:</td>
                                <td style="padding:12px 15px;">PKR ${parseFloat(res.tax || 0).toLocaleString()}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #e0e0e0;">
                                <td style="padding:12px 15px; font-weight:bold;">Grand Total:</td>
                                <td style="padding:12px 15px; font-weight:bold; color:#ff6b6b; font-size:16px;">PKR ${parseFloat(res.grand_total || 0).toLocaleString()}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #e0e0e0; background:#f9f9f9;">
                                <td style="padding:12px 15px; font-weight:bold;">Paid Amount:</td>
                                <td style="padding:12px 15px; color:#2ecc71; font-weight:bold;">PKR ${parseFloat(res.paid_amount || 0).toLocaleString()}</td>
                            </tr>
                            <tr style="border-bottom:1px solid #e0e0e0;">
                                <td style="padding:12px 15px; font-weight:bold;">Remaining Balance:</td>
                                <td style="padding:12px 15px; color:#3498db; font-weight:bold;">PKR ${parseFloat(res.remaining_balance || 0).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td style="padding:12px 15px; font-weight:bold; background:#f9f9f9;">Status:</td>
                                <td style="padding:12px 15px; background:#f9f9f9;"><span style="background:#e8f5e9; color:#2e7d32; padding:4px 8px; border-radius:4px; font-weight:bold;">${res.order_status || 'Draft'}</span></td>
                            </tr>
                        </table>
                    </div>
                `;
                Swal.fire({
                    icon: 'success',
                    title: 'Sale Order Created',
                    html: detailsHtml,
                    width: '700px',
                    confirmButtonText: 'Done',
                    confirmButtonColor: '#87B87F',
                    showDenyButton: res.invoice_id ? true : false,
                    denyButtonText: res.invoice_id ? '<i class="fa fa-print"></i> Print Invoice' : null,
                    denyButtonColor: '#3498db',
                    didOpen: (modal) => {
                        // Style the modal for better appearance
                        const popup = modal.querySelector('.swal2-popup');
                        if (popup) {
                            popup.style.borderRadius = '8px';
                            popup.style.boxShadow = '0 4px 20px rgba(0,0,0,0.15)';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        loadDashboard();
                    } else if (result.isDenied && res.invoice_id) {
                        // Open print page in new tab
                        window.open('index.php?r=documents/salesinvoice&id=' + res.invoice_id, '_blank');
                        loadDashboard();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'Unable to save order',
                    width: '500px'
                });
            }
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Unable to communicate with server',
                width: '500px'
            });
        });
    }
</script>
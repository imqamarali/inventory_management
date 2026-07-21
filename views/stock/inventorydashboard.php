<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-cubes"></i>
                Inventory Dashboard
                <small>Inventory Overview & Analytics</small>
            </h3>
        </div>

        <div >
            <button id="refreshDashboard">
                <i class="fa fa-refresh"></i>
                Refresh
            </button>
            <button id="truncateStockDetails">
                <i class="fa fa-trash"></i>
                Truncate Stock Details
            </button>
        </div>
    </div>


    <div class="stats-grid">

        <div class="stat-card blue">

            <div class="stat-header">

                <span class="stat-title">
                    Stock Items
                </span>

                <div class="stat-icon">
                    <i class="fa fa-cubes"></i>
                </div>

            </div>

            <div class="stat-value" id="total_stock_items">
                ...
            </div>

            <div class="stat-subtitle">
                Total Stock Items
            </div>

        </div>



        <div class="stat-card red">

            <div class="stat-header">

                <span class="stat-title">
                    Inventory Value
                </span>

                <div class="stat-icon">
                    <i class="fa fa-money"></i>
                </div>

            </div>

            <div class="stat-value" id="inventory_value">
                ...
            </div>

            <div class="stat-subtitle">
                Current Stock Value
            </div>

        </div>



        <div class="stat-card green">

            <div class="stat-header">

                <span class="stat-title">
                    Adjustments
                </span>

                <div class="stat-icon">
                    <i class="fa fa-sliders"></i>
                </div>

            </div>

            <div class="stat-value" id="stock_adjustments">
                ...
            </div>

            <div class="stat-subtitle">
                Stock Adjustments
            </div>

        </div>



        <div class="stat-card orange">

            <div class="stat-header">

                <span class="stat-title">
                    Total Purchase Value
                </span>

                <div class="stat-icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>

            </div>

            <div class="stat-value" id="total_purchase_value">
                ...
            </div>

            <div class="stat-subtitle">
                All Purchase Invoices
            </div>

        </div>



        <div class="stat-card purple">

            <div class="stat-header">

                <span class="stat-title">
                    Total Sale Value
                </span>

                <div class="stat-icon">
                    <i class="fa fa-dollar"></i>
                </div>

            </div>

            <div class="stat-value" id="total_sale_value">
                ...
            </div>

            <div class="stat-subtitle">
                All Sales Invoices
            </div>

        </div>



        <div class="stat-card teal">

            <div class="stat-header">

                <span class="stat-title">
                    Overall Trend
                </span>

                <div class="stat-icon">
                    <i class="fa fa-line-chart"></i>
                </div>

            </div>

            <div class="stat-value" id="overall_trend">
                ...
            </div>

            <div class="stat-subtitle">
                Profit / Loss %
            </div>

        </div>



        <!-- <div class="stat-card orange">

            <div class="stat-header">

                <span class="stat-title">
                    Transfers
                </span>

                <div class="stat-icon">
                    <i class="fa fa-random"></i>
                </div>

            </div>

            <div class="stat-value" id="stock_transfers">
                ...
            </div>

            <div class="stat-subtitle">
                Stock Transfers
            </div>

        </div>



        <div class="stat-card purple">

            <div class="stat-header">

                <span class="stat-title">
                    Audits
                </span>

                <div class="stat-icon">
                    <i class="fa fa-search"></i>
                </div>

            </div>

            <div class="stat-value" id="stock_audits">
                ...
            </div>

            <div class="stat-subtitle">
                Stock Audits
            </div>

        </div> -->


<!-- 
        <div class="stat-card teal">

            <div class="stat-header">

                <span class="stat-title">
                    Pending Transfers
                </span>

                <div class="stat-icon">
                    <i class="fa fa-clock-o"></i>
                </div>

            </div>

            <div class="stat-value" id="pending_transfers">
                ...
            </div>

            <div class="stat-subtitle">
                Awaiting Processing
            </div>

        </div> -->



        <!-- <div class="stat-card red">

            <div class="stat-header">

                <span class="stat-title">
                    Pending Audits
                </span>

                <div class="stat-icon">
                    <i class="fa fa-warning"></i>
                </div>

            </div>

            <div class="stat-value" id="pending_audits">
                ...
            </div>

            <div class="stat-subtitle">
                Pending Verification
            </div>

        </div> -->

    </div>
    <!-- Charts -->

    <div class="row">

        <div class="col-md-12">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-line-chart"></i>
                    Daily Sales & Purchase Trend (Last 30 Days)
                </h4>

                <canvas id="trendChart" height="100"></canvas>

            </div>

        </div>

    </div>



    <div class="row" style="margin-top:15px;">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-pie-chart"></i>
                    Stock By Warehouse
                </h4>

                <canvas id="warehouseChart" height="220"></canvas>

            </div>

        </div>


        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-bar-chart"></i>
                    Stock Movement Types
                </h4>

                <canvas id="movementChart" height="220"></canvas>

            </div>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    var trendChart = null;
    var warehouseChart = null;
    var movementChart = null;

    $(function() {

        loadDashboard();

        $("#refreshDashboard").click(function() {

            loadDashboard();

        });

    });

    function loadDashboard() {
        showDashboardLoading();
        $.ajax({
            url: "<?= Yii::$app->urlManager->createUrl('stock/inventorydashboard') ?>",
            type: "POST",
            dataType: "json",
            data: { flag: "load_dashboard" },
            timeout: 5000,
            success: function(response) {
                hideDashboardLoading();
                if (response.success) {
                    loadStatistics(response.stats);
                    if (typeof Chart === 'function' || typeof Chart === 'object') {
                        loadTrendChart(response.dailySalesData, response.dailyPurchaseData);
                        loadWarehouseChart(response.warehouseChart);
                        loadMovementChart(response.movementChart);
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

        animateCounter("#total_stock_items", stats.total_stock_items);

        animateCurrency("#inventory_value", stats.inventory_value);

        animateCounter("#stock_adjustments", stats.stock_adjustments);

        animateCurrency("#total_purchase_value", stats.total_purchase_value);

        animateCurrency("#total_sale_value", stats.total_sale_value);

        // Display overall trend with profit/loss indicator
        const trendValue = stats.overall_trend;
        const trendPercent = stats.profit_loss_percentage;
        const trendElement = $("#overall_trend");

        if (trendValue >= 0) {
            trendElement.html(`<span style="color: #2ecc71;">+${trendPercent}%</span>`);
        } else {
            trendElement.html(`<span style="color: #e74c3c;">${trendPercent}%</span>`);
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

    function loadTrendChart(salesData, purchaseData) {

        if (trendChart) {
            trendChart.destroy();
        }

        let labels = [];
        let salesAmounts = [];
        let purchaseAmounts = [];

        $.each(salesData, function(i, row) {
            labels.push(row.date);
            salesAmounts.push(parseFloat(row.amount));
        });

        $.each(purchaseData, function(i, row) {
            purchaseAmounts.push(parseFloat(row.amount));
        });

        trendChart = new Chart(
            document.getElementById("trendChart"), {
                type: "line",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "Sales",
                            data: salesAmounts,
                            borderColor: "#2ecc71",
                            backgroundColor: "rgba(46, 204, 113, 0.1)",
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: "#2ecc71"
                        },
                        {
                            label: "Purchases",
                            data: purchaseAmounts,
                            borderColor: "#e74c3c",
                            backgroundColor: "rgba(231, 76, 60, 0.1)",
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: "#e74c3c"
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
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

    function loadWarehouseChart(data) {

        if (warehouseChart) {
            warehouseChart.destroy();
        }

        let labels = [];
        let quantity = [];
        let available = [];
        let reserved = [];

        $.each(data, function(i, row) {

            labels.push(row.warehouse_name);
            quantity.push(parseFloat(row.quantity));
            available.push(parseFloat(row.available_quantity));
            reserved.push(parseFloat(row.reserved_quantity));

        });

        warehouseChart = new Chart(
            document.getElementById("warehouseChart"), {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                            label: "Total Quantity",
                            data: quantity,
                            backgroundColor: "#3498db"
                        },
                        {
                            label: "Available",
                            data: available,
                            backgroundColor: "#2ecc71"
                        },
                        {
                            label: "Reserved",
                            data: reserved,
                            backgroundColor: "#f39c12"
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            }
        );

    }


    function loadMovementChart(data) {

        if (movementChart) {
            movementChart.destroy();
        }

        let labels = [];
        let values = [];

        $.each(data, function(i, row) {

            labels.push(row.movement_type);

            values.push(parseInt(row.total));

        });

        movementChart = new Chart(
            document.getElementById("movementChart"), {

                type: "bar",

                data: {

                    labels: labels,

                    datasets: [{

                        label: "Movements",

                        data: values,

                        backgroundColor: "#3498db"

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

            }

        );

    }


    function loadMonthlyChart(data) {

        if (monthlyChart) {
            monthlyChart.destroy();
        }

        let labels = [];
        let values = [];

        $.each(data, function(i, row) {

            labels.push(row.month);

            values.push(parseInt(row.total));

        });

        monthlyChart = new Chart(
            document.getElementById("monthlyChart"), {

                type: "line",

                data: {

                    labels: labels,

                    datasets: [{

                        label: "Stock Movements",

                        data: values,

                        fill: true,

                        borderColor: "#27ae60",

                        backgroundColor: "rgba(39,174,96,.15)",

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

    // Truncate Stock Details Handler
    document.getElementById('truncateStockDetails').addEventListener('click', function() {
        Swal.fire({
            title: 'Truncate Stock Details?',
            html: '<div style="text-align: left;"><p style="margin-bottom: 15px;"><strong>⚠️ WARNING: This action is IRREVERSIBLE!</strong></p><p style="margin-bottom: 10px;">This will:</p><ul style="margin: 10px 0; padding-left: 20px;"><li>Delete all records from Stock Adjustment table</li><li>Delete all records from Damaged Stock table</li><li>Delete all Inventory Current Stock records</li><li>Add all Active products to Inventory Current Stock</li><li>Set all quantities to 0 and reserved to 0</li></ul></div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete All',
            confirmButtonColor: '#dc3545',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Enter Your Password',
                    text: 'Please confirm by entering your Inventory Admin password',
                    input: 'password',
                    inputPlaceholder: 'Enter your password...',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    confirmButtonColor: '#dc3545',
                    cancelButtonText: 'Cancel',
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

                        truncateStockData(password);
                    }
                });
            }
        });
    });

    function truncateStockData(password) {
        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl("stock/inventorydashboard") ?>',
            type: 'POST',
            dataType: 'json',
            data: { flag: 'truncate_stock', password: password },
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
                    Swal.fire('Error!', response.message || 'Failed to truncate stock details', 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Failed to truncate stock details', 'error');
            }
        });
    }
</script>
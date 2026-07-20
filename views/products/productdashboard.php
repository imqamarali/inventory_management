 

<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3><i class="fa fa-cubes"></i> Product Dashboard <small>Inventory Overview & Analytics</small></h3>
        </div>
        <div>
            <button id="refreshDashboard">
                <i class="fa fa-refresh"></i> Refresh
            </button>
        </div>
    </div>
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Products</span>
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


        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Categories</span>

                <div class="stat-icon">
                    <i class="fa fa-tags"></i>
                </div>
            </div>

            <div class="stat-value" id="categories">
                ...
            </div>

            <div class="stat-subtitle">
                Total Categories
            </div>
        </div>


        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Brands</span>

                <div class="stat-icon">
                    <i class="fa fa-certificate"></i>
                </div>
            </div>

            <div class="stat-value" id="brands">
                ...
            </div>

            <div class="stat-subtitle">
                Registered Brands
            </div>
        </div>


        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Units</span>

                <div class="stat-icon">
                    <i class="fa fa-balance-scale"></i>
                </div>
            </div>

            <div class="stat-value" id="units">
                ...
            </div>

            <div class="stat-subtitle">
                Measurement Units
            </div>
        </div>


        <div class="stat-card teal">
            <div class="stat-header">
                <span class="stat-title">Vehicle Makes</span>

                <div class="stat-icon">
                    <i class="fa fa-car"></i>
                </div>
            </div>

            <div class="stat-value" id="vehicle_makes">
                ...
            </div>

            <div class="stat-subtitle">
                Supported Makes
            </div>
        </div>


        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Vehicle Models</span>

                <div class="stat-icon">
                    <i class="fa fa-car"></i>
                </div>
            </div>

            <div class="stat-value" id="vehicle_models">
                ...
            </div>

            <div class="stat-subtitle">
                Supported Models
            </div>
        </div>


        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Inventory Value</span>

                <div class="stat-icon">
                    <i class="fa fa-money"></i>
                </div>
            </div>

            <div class="stat-value" id="inventory_value">
                ...
            </div>

            <div class="stat-subtitle">
                Purchase Value
            </div>
        </div>


        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Average Price</span>

                <div class="stat-icon">
                    <i class="fa fa-line-chart"></i>
                </div>
            </div>

            <div class="stat-value" id="average_price">
                ...
            </div>

            <div class="stat-subtitle">
                Selling Price
            </div>
        </div>

    </div>



    <!-- Charts -->

    <div class="row">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-pie-chart"></i>
                    Products By Category
                </h4>

                <canvas id="categoryChart" height="220"></canvas>

            </div>

        </div>


        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-bar-chart"></i>
                    Products By Brand
                </h4>

                <canvas id="brandChart" height="220"></canvas>

            </div>

        </div>

    </div>



    <div class="row" style="margin-top:15px;">

        <div class="col-md-12">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-line-chart"></i>
                    Monthly Products
                </h4>

                <canvas id="monthlyChart" height="100"></canvas>

            </div>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var categoryChart = null;
    var brandChart = null;
    var monthlyChart = null;
    $(function() {

        loadDashboard();

        $("#refreshDashboard").click(function() {

            loadDashboard();

        });

    });

    function loadDashboard() {

        showDashboardLoading();

        $.ajax({

            url: "<?= Yii::$app->urlManager->createUrl('products/productdashboard') ?>",

            type: "POST",

            dataType: "json",

            data: {
                flag: "load_dashboard"
            },

            success: function(response) {

                hideDashboardLoading();

                if (response.success) {

                    loadStatistics(response.stats);

                    loadCategoryChart(response.categoryChart);

                    loadBrandChart(response.brandChart);

                    loadMonthlyChart(response.monthlyProducts);

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

            $(this)
                .addClass("loading")
                .html("&nbsp;&nbsp;&nbsp;&nbsp;");

        });

    }

    function hideDashboardLoading() {

        $(".stat-value").removeClass("loading");

    }

    function loadStatistics(stats) {

        animateCounter("#total_products", stats.total_products);

        animateCounter("#categories", stats.categories);

        animateCounter("#brands", stats.brands);

        animateCounter("#units", stats.units);

        animateCounter("#vehicle_makes", stats.vehicle_makes);

        animateCounter("#vehicle_models", stats.vehicle_models);

        animateCurrency("#inventory_value", stats.inventory_value);

        animateCurrency("#average_price", stats.average_price);

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

    function loadCategoryChart(data) {

        if (categoryChart) {
            categoryChart.destroy();
        }

        let labels = [];
        let values = [];

        $.each(data, function(i, row) {

            labels.push(row.category_name);

            values.push(parseInt(row.total));

        });

        categoryChart = new Chart(
            document.getElementById("categoryChart"), {
                type: "doughnut",

                data: {

                    labels: labels,

                    datasets: [{

                        data: values,

                        backgroundColor: [
                            "#3498db",
                            "#2ecc71",
                            "#f39c12",
                            "#9b59b6",
                            "#1abc9c",
                            "#e74c3c",
                            "#34495e",
                            "#f1c40f"
                        ]

                    }]
                },

                options: {

                    responsive: true,

                    plugins: {
                        legend: {
                            position: "bottom"
                        }
                    }

                }

            }
        );

    }

    function loadBrandChart(data) {

        if (brandChart) {
            brandChart.destroy();
        }

        let labels = [];
        let values = [];

        $.each(data, function(i, row) {

            labels.push(row.brand_name);

            values.push(parseInt(row.total));

        });

        brandChart = new Chart(
            document.getElementById("brandChart"), {

                type: "bar",

                data: {

                    labels: labels,

                    datasets: [{

                        label: "Products",

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

                        label: "Products",

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
</script>
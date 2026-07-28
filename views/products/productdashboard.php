 

<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3><i class="fa fa-cubes"></i> Product Dashboard <small>Inventory Overview & Analytics</small></h3>
        </div>
        <div>
            <button id="refreshDashboard">
                <i class="fa fa-refresh"></i> Refresh
            </button>
            <?php
                $isSuperAdmin = false;
                if (isset(Yii::$app->session['user_array']['role_id'])) {
                    $roleId = Yii::$app->session['user_array']['role_id'];
                    $isSuperAdmin = Yii::$app->db->createCommand(
                        "SELECT COUNT(*) FROM roles WHERE id = :role_id AND name = 'Super Admin'"
                    )->bindValue(':role_id', $roleId)->queryScalar() > 0;
                }
            ?>
            <?php if ($isSuperAdmin): ?>
            <button id="truncateProducts">
                <i class="fa fa-trash"></i> Truncate Products
            </button>
            <?php endif; ?>
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

    // Truncate All Data Handler
    $(document).ready(function() {
        $('#truncateProducts').on('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Truncate All Inventory Data?',
                html: '<div style="text-align: left; padding: 15px;"><p style="margin-bottom: 15px; color: #d32f2f;"><strong>⚠️ WARNING: This action is IRREVERSIBLE!</strong></p><p style="margin-bottom: 10px;"><strong>This will permanently delete:</strong></p><ul style="margin: 10px 0; padding-left: 20px;"><li><strong>Products:</strong> All inventory products</li><li><strong>Master Data:</strong> Categories, Brands, Units, Vehicle Makes, Vehicle Models</li><li><strong>Stock:</strong> All inventory stock records</li><li><strong>Purchases:</strong> All purchase orders and details</li><li><strong>Sales:</strong> All sales orders and details</li></ul><p style="margin-top: 15px; color: #d32f2f; font-weight: bold;">⚠️ There is NO way to recover this data after truncation!</p></div>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete Everything',
                confirmButtonColor: '#dc3545',
                cancelButtonText: 'Cancel',
                width: 600
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Confirm with Password',
                        text: 'Enter your admin password to confirm this permanent action:',
                        input: 'password',
                        inputPlaceholder: 'Enter your password...',
                        showCancelButton: true,
                        confirmButtonText: 'Confirm & Delete',
                        confirmButtonColor: '#dc3545',
                        cancelButtonText: 'Cancel',
                        inputAttributes: {
                            autocapitalize: 'off',
                            autocorrect: 'off',
                            spellcheck: 'false'
                        },
                        preConfirm: (password) => {
                            if (!password) {
                                Swal.showValidationMessage('Password is required');
                                return false;
                            }
                            return password;
                        }
                    }).then((passwordResult) => {
                        if (passwordResult.isConfirmed) {
                            truncateAllData(passwordResult.value);
                        }
                    });
                }
            });
        });
    });

    function truncateAllData(password) {
        Swal.fire({
            title: 'Processing...',
            html: '<p>Truncating all data...</p><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl("products/productdashboard") ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                flag: 'truncate_all',
                password: password
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: '✅ Success!',
                        html: '<div style="text-align: left;"><p><strong>' + response.message + '</strong></p><p style="margin-top: 10px; font-size: 12px; color: #666;">Redirecting to dashboard...</p></div>',
                        icon: 'success',
                        confirmButtonColor: '#0f4c29',
                        allowOutsideClick: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: '❌ Error',
                        text: response.message || 'Failed to truncate data',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    title: '❌ Error',
                    text: 'Failed to process truncation request: ' + error,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    }
</script>
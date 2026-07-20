<div class="page-content">
    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-check-square-o"></i>
                Audit Dashboard
                <small>Inventory Audit Overview & Analytics</small>
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
                <span class="stat-title">Audit Sessions</span>
                <div class="stat-icon">
                    <i class="fa fa-list-alt"></i>
                </div>
            </div>
            <div class="stat-value" id="total_audits">0</div>
            <div class="stat-subtitle">Total Audit Sessions</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Pending</span>
                <div class="stat-icon">
                    <i class="fa fa-clock-o"></i>
                </div>
            </div>
            <div class="stat-value" id="pending_audits">0</div>
            <div class="stat-subtitle">Pending Audits</div>
        </div>
        <div class="stat-card teal">
            <div class="stat-header">
                <span class="stat-title">In Progress</span>
                <div class="stat-icon">
                    <i class="fa fa-refresh"></i>

                </div>

            </div>

            <div class="stat-value" id="inprogress_audits">0</div>

            <div class="stat-subtitle">Currently Running</div>

        </div>

        <div class="stat-card green">

            <div class="stat-header">

                <span class="stat-title">Completed</span>

                <div class="stat-icon">

                    <i class="fa fa-check-circle"></i>

                </div>

            </div>

            <div class="stat-value" id="completed_audits">0</div>

            <div class="stat-subtitle">Completed Audits</div>

        </div>

        <div class="stat-card red">

            <div class="stat-header">

                <span class="stat-title">Variance</span>

                <div class="stat-icon">

                    <i class="fa fa-exclamation-triangle"></i>

                </div>

            </div>

            <div class="stat-value" id="variance_found">0</div>

            <div class="stat-subtitle">Items With Difference</div>

        </div>

        <div class="stat-card purple">

            <div class="stat-header">

                <span class="stat-title">Matched</span>

                <div class="stat-icon">

                    <i class="fa fa-check-square"></i>

                </div>

            </div>

            <div class="stat-value" id="matched_items">0</div>

            <div class="stat-subtitle">Stock Matched</div>

        </div>

        <div class="stat-card blue">

            <div class="stat-header">

                <span class="stat-title">Adjustments</span>

                <div class="stat-icon">

                    <i class="fa fa-exchange"></i>

                </div>

            </div>

            <div class="stat-value" id="total_adjustments">0</div>

            <div class="stat-subtitle">Stock Adjustments</div>

        </div>

        <div class="stat-card green">

            <div class="stat-header">

                <span class="stat-title">Warehouses</span>

                <div class="stat-icon">

                    <i class="fa fa-building"></i>

                </div>

            </div>

            <div class="stat-value" id="warehouses_audited">0</div>

            <div class="stat-subtitle">Audited Warehouses</div>

        </div>

    </div>

    <div class="row">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>

                    <i class="fa fa-pie-chart"></i>

                    Audit Status

                </h4>

                <canvas id="auditStatusChart" height="220"></canvas>

            </div>

        </div>

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>

                    <i class="fa fa-bar-chart"></i>

                    Warehouse Audit Summary

                </h4>

                <canvas id="warehouseAuditChart" height="220"></canvas>

            </div>

        </div>

    </div>

    <div class="row" style="margin-top:15px;">

        <div class="col-md-12">

            <div class="dashboard-box">

                <h4>

                    <i class="fa fa-line-chart"></i>

                    Monthly Audit Trend

                </h4>

                <canvas id="monthlyAuditChart" height="100"></canvas>

            </div>

        </div>

    </div>

    <div class="row" style="margin-top:15px;">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>

                    <i class="fa fa-list-alt"></i>

                    Latest Audit Sessions

                </h4>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover">

                        <thead>

                            <tr>

                                <th>Audit No</th>

                                <th>Warehouse</th>

                                <th>Date</th>

                                <th>Status</th>

                                <th class="text-right">Variance</th>

                            </tr>

                        </thead>

                        <tbody id="latestAudits">

                            <tr>

                                <td colspan="5" class="text-center">

                                    Loading...

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>

                    <i class="fa fa-exchange"></i>

                    Latest Stock Adjustments

                </h4>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover">

                        <thead>

                            <tr>

                                <th>Adjustment No</th>

                                <th>Warehouse</th>

                                <th>Date</th>

                                <th>Type</th>

                                <th class="text-right">Items</th>

                            </tr>

                        </thead>

                        <tbody id="latestAdjustments">

                            <tr>

                                <td colspan="5" class="text-center">

                                    Loading...

                                </td>

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
    var auditStatusChart = null;
    var warehouseAuditChart = null;
    var monthlyAuditChart = null;
</script>
<script>
    $(function() {

        loadDashboard();

        $("#refreshDashboard").click(function() {

            loadDashboard();

        });

    });

    function loadDashboard() {

        showDashboardLoading();

        $.ajax({

            url: "<?= Yii::$app->urlManager->createUrl('stockaudit/auditdashboard') ?>",

            type: "POST",

            dataType: "json",

            data: {

                flag: "load_dashboard"

            },

            success: function(response) {

                hideDashboardLoading();

                if (response.success) {

                    if (response.stats) {

                        loadStatistics(response.stats);

                    }

                    if (response.auditStatusChart) {

                        loadAuditStatusChart(response.auditStatusChart);

                    }

                    if (response.warehouseAuditChart) {

                        loadWarehouseAuditChart(response.warehouseAuditChart);

                    }

                    if (response.monthlyAuditChart) {

                        loadMonthlyAuditChart(response.monthlyAuditChart);

                    }

                    if (response.latestAudits) {

                        loadLatestAudits(response.latestAudits);

                    }

                    if (response.latestAdjustments) {

                        loadLatestAdjustments(response.latestAdjustments);

                    }

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

        animateCounter("#total_audits", stats.total_audits);

        animateCounter("#pending_audits", stats.pending_audits);

        animateCounter("#inprogress_audits", stats.inprogress_audits);

        animateCounter("#completed_audits", stats.completed_audits);

        animateCounter("#variance_found", stats.variance_found);

        animateCounter("#matched_items", stats.matched_items);

        animateCounter("#total_adjustments", stats.total_adjustments);

        animateCounter("#warehouses_audited", stats.warehouses_audited);

    }

    function animateCounter(id, value) {

        value = (value == null || isNaN(value)) ? 0 : Number(value);

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

        value = (value == null || isNaN(value)) ? 0 : Number(value);

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


    function loadAuditStatusChart(data) {

        if (auditStatusChart) {
            auditStatusChart.destroy();
        }

        let labels = [];

        let values = [];

        $.each(data, function(i, row) {

            labels.push(row.status);

            values.push(parseInt(row.total));

        });

        auditStatusChart = new Chart(
            document.getElementById("auditStatusChart"), {

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
                            "#e74c3c"
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

    function loadWarehouseAuditChart(data) {

        if (warehouseAuditChart) {
            warehouseAuditChart.destroy();
        }

        let labels = [];

        let values = [];

        $.each(data, function(i, row) {

            labels.push(row.warehouse_name);

            values.push(parseInt(row.total));

        });

        warehouseAuditChart = new Chart(
            document.getElementById("warehouseAuditChart"), {

                type: "bar",

                data: {

                    labels: labels,

                    datasets: [{

                        label: "Audit Sessions",

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

    function loadMonthlyAuditChart(data) {

        if (monthlyAuditChart) {
            monthlyAuditChart.destroy();
        }

        let labels = [];

        let values = [];

        $.each(data, function(i, row) {

            labels.push(row.month);

            values.push(parseInt(row.total));

        });

        monthlyAuditChart = new Chart(
            document.getElementById("monthlyAuditChart"), {

                type: "line",

                data: {

                    labels: labels,

                    datasets: [{

                        label: "Audit Sessions",

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

    function loadLatestAudits(data) {

        let html = "";

        if (data.length == 0) {

            html += "<tr>";
            html += "<td colspan='5' class='text-center'>No Audit Records Found.</td>";
            html += "</tr>";

        } else {

            $.each(data, function(i, row) {

                html += "<tr>";

                html += "<td>" + row.audit_no + "</td>";

                html += "<td>" + row.warehouse_name + "</td>";

                html += "<td>" + row.audit_date + "</td>";

                html += "<td>" + row.status + "</td>";

                html += "<td class='text-right'>" + Number(row.variance_count).toLocaleString() + "</td>";

                html += "</tr>";

            });

        }

        $("#latestAudits").html(html);

    }

    function loadLatestAdjustments(data) {

        let html = "";

        if (data.length == 0) {

            html += "<tr>";
            html += "<td colspan='5' class='text-center'>No Stock Adjustments Found.</td>";
            html += "</tr>";

        } else {

            $.each(data, function(i, row) {

                html += "<tr>";

                html += "<td>" + row.adjustment_no + "</td>";

                html += "<td>" + row.warehouse_name + "</td>";

                html += "<td>" + row.adjustment_date + "</td>";

                html += "<td>" + row.adjustment_type + "</td>";

                html += "<td class='text-right'>" + Number(row.total_items).toLocaleString() + "</td>";

                html += "</tr>";

            });

        }

        $("#latestAdjustments").html(html);

    }
</script>
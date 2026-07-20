<!--
================================================================================
PURCHASE DASHBOARD VIEW
================================================================================
PURPOSE: Comprehensive purchase overview with KPIs, trends, and supplier analysis

FUNCTIONALITY:
- Display key purchase metrics and statistics
- Show purchase order status distribution
- Track goods receiving progress
- Display top suppliers by spending
- Show monthly purchase trends
- List recent purchase orders and goods received
- Track supplier payables
- Provide quick navigation to purchase modules

DATA DISPLAYED:
- Total Purchase Orders and value
- Order status breakdown (Pending, Approved, Completed, Cancelled)
- Goods receiving statistics
- Total suppliers and active suppliers
- Total supplier payment amount
- Purchase returns count
- Status distribution chart
- Supplier spending chart (top 10)
- Monthly purchase trends
- Recent purchase orders and GRNs

FINANCE INTEGRATION:
- Total purchase value aggregates inventory_purchase_orders grand_total
- Goods receiving count tracks asset acquisition progress
- Purchase status indicates expense recognition timing
- Supplier spending identifies key vendors and concentration risk
- Data feeds into Finance Dashboard for:
  • Total Expense/COGS calculation
  • Cost trends analysis
  • Supplier payment schedule
  • Working capital management
  • Cash flow forecasting
================================================================================
-->

<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-shopping-cart"></i>
                Purchase Dashboard
                <small>Purchase Overview & Analytics</small>
            </h3>
        </div>

        <div>
            <button id="refreshDashboard">
                <i class="fa fa-refresh"></i>
                Refresh
            </button>
            <button id="truncatePurchaseBtn" style="margin-left: 10px; cursor: pointer;">
                <i class="fa fa-trash"></i>
                Truncate Purchase Records
            </button>
        </div>
    </div>

    <div class="stats-grid">

        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Purchase Orders</span>
                <div class="stat-icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
            </div>
            <div class="stat-value" id="total_purchase_orders">0</div>
            <div class="stat-subtitle">Total Purchase Orders</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Suppliers</span>
                <div class="stat-icon">
                    <i class="fa fa-truck"></i>
                </div>
            </div>
            <div class="stat-value" id="total_suppliers">0</div>
            <div class="stat-subtitle">Active Suppliers</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Pending Orders</span>
                <div class="stat-icon">
                    <i class="fa fa-clock-o"></i>
                </div>
            </div>
            <div class="stat-value" id="pending_orders">0</div>
            <div class="stat-subtitle">Awaiting Approval</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Approved Orders</span>
                <div class="stat-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value" id="approved_orders">0</div>
            <div class="stat-subtitle">Ready For Receiving</div>
        </div>

        <div class="stat-card teal">
            <div class="stat-header">
                <span class="stat-title">Partially Received</span>
                <div class="stat-icon">
                    <i class="fa fa-truck"></i>
                </div>
            </div>
            <div class="stat-value" id="partially_received">0</div>
            <div class="stat-subtitle">Receiving In Progress</div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Completed Orders</span>
                <div class="stat-icon">
                    <i class="fa fa-check-square"></i>
                </div>
            </div>
            <div class="stat-value" id="completed_orders">0</div>
            <div class="stat-subtitle">Fully Received</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Purchase Value</span>
                <div class="stat-icon">
                    <i class="fa fa-money"></i>
                </div>
            </div>
            <div class="stat-value" id="purchase_value">PKR 0</div>
            <div class="stat-subtitle">Total Purchase Amount</div>
        </div>

        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Average Order</span>
                <div class="stat-icon">
                    <i class="fa fa-line-chart"></i>
                </div>
            </div>
            <div class="stat-value" id="average_order_value">PKR 0</div>
            <div class="stat-subtitle">Average Purchase Value</div>
        </div>

    </div>
    <div class="row">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-pie-chart"></i>
                    Purchase Orders By Status
                </h4>

                <canvas id="purchaseStatusChart" height="220"></canvas>

            </div>

        </div>

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-bar-chart"></i>
                    Top Suppliers
                </h4>

                <canvas id="supplierChart" height="220"></canvas>

            </div>

        </div>

    </div>



    <div class="row" style="margin-top:15px;">

        <div class="col-md-12">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-line-chart"></i>
                    Monthly Purchase Trend
                </h4>

                <canvas id="monthlyPurchaseChart" height="100"></canvas>

            </div>

        </div>

    </div>




    <div class="row" style="margin-top:15px;">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-truck"></i>
                    Latest Purchase Orders
                </h4>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover">

                        <thead>

                            <tr>

                                <th>PO No</th>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-right">Amount</th>

                            </tr>

                        </thead>

                        <tbody id="latestPurchaseOrders">

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
                    <i class="fa fa-clock-o"></i>
                    Pending Goods Receiving
                </h4>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover">

                        <thead>

                            <tr>

                                <th>PO No</th>
                                <th>Supplier</th>
                                <th>Warehouse</th>
                                <th>Status</th>

                            </tr>

                        </thead>

                        <tbody id="pendingReceiving">

                            <tr>

                                <td colspan="4" class="text-center">
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
    var purchaseStatusChart = null;
    var supplierChart = null;
    var monthlyPurchaseChart = null;
</script>

<script>

    $(function() {

        loadDashboard();

        $("#refreshDashboard").click(function() {

            loadDashboard();

        });

        $("#truncatePurchaseBtn").click(function() {
            Swal.fire({
                title: 'Truncate Purchase Records',
                text: 'This action will delete ALL purchase records including:\n- Purchase Orders\n- Goods Received Notes\n- Payment History\n- Stock Movements\n\nThis cannot be undone!',
                icon: 'warning',
                input: 'password',
                inputPlaceholder: 'Enter admin password to confirm',
                showCancelButton: true,
                confirmButtonColor: '#d9534f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Delete All Records',
                cancelButtonText: 'Cancel',
                preConfirm: (password) => {
                    if (!password) {
                        Swal.showValidationMessage('Password is required');
                        return false;
                    }
                    return password;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    truncatePurchaseRecords(result.value);
                }
            });
        });

    });


    function loadDashboard() {

        showDashboardLoading();

        $.ajax({

            url: "<?= Yii::$app->urlManager->createUrl('purchase/purchasedashboard') ?>",
            type: "POST",
            dataType: "json",

            data: {
                flag: "load_dashboard"
            },

            success: function(response) {

                hideDashboardLoading();

                if (response.success) {

                    loadStatistics(response.stats);

                    loadPurchaseStatusChart(response.purchaseStatusChart);

                    loadSupplierChart(response.supplierChart);

                    loadMonthlyPurchaseChart(response.monthlyPurchaseChart);

                    loadLatestPurchaseOrders(response.latestPurchaseOrders);

                    loadPendingReceiving(response.pendingReceiving);

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

        animateCounter("#total_purchase_orders", stats.total_purchase_orders);

        animateCounter("#total_suppliers", stats.total_suppliers);

        animateCounter("#pending_orders", stats.pending_orders);

        animateCounter("#approved_orders", stats.approved_orders);

        animateCounter("#partially_received", stats.partially_received);

        animateCounter("#completed_orders", stats.completed_orders);

        animateCurrency("#purchase_value", stats.purchase_value);

        animateCurrency("#average_order_value", stats.average_order_value);

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

    function loadPurchaseStatusChart(data) {

        if (purchaseStatusChart) {
            purchaseStatusChart.destroy();
        }

        let labels = [];
        let values = [];

        $.each(data, function(i, row) {

            labels.push(row.status);
            values.push(parseInt(row.total));

        });

        purchaseStatusChart = new Chart(
            document.getElementById("purchaseStatusChart"), {

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



    function loadSupplierChart(data) {

        if (supplierChart) {
            supplierChart.destroy();
        }

        let labels = [];
        let values = [];

        $.each(data, function(i, row) {

            labels.push(row.company_name);
            values.push(parseInt(row.total));

        });

        supplierChart = new Chart(
            document.getElementById("supplierChart"), {

                type: "bar",

                data: {

                    labels: labels,

                    datasets: [{

                        label: "Orders",

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
 

    function loadMonthlyPurchaseChart(data) {

        if (monthlyPurchaseChart) {
            monthlyPurchaseChart.destroy();
        }

        let labels = [];
        let values = [];

        $.each(data, function(i, row) {

            labels.push(row.month);
            values.push(parseFloat(row.total));

        });

        monthlyPurchaseChart = new Chart(
            document.getElementById("monthlyPurchaseChart"), {

                type: "line",

                data: {

                    labels: labels,

                    datasets: [{

                        label: "Purchase Amount",

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
 
    function loadLatestPurchaseOrders(data) {

        let html = "";

        if (data.length == 0) {

            html += "<tr>";
            html += "<td colspan='5' class='text-center'>No Purchase Orders Found.</td>";
            html += "</tr>";

        } else {

            $.each(data, function(i, row) {

                html += "<tr>";

                html += "<td>" + row.po_number + "</td>";

                html += "<td>" + row.company_name + "</td>";

                html += "<td>" + row.order_date + "</td>";

                html += "<td>" + row.status + "</td>";

                html += "<td class='text-right'>PKR " + Number(row.total_amount).toLocaleString() + "</td>";

                html += "</tr>";

            });

        }

        $("#latestPurchaseOrders").html(html);

    } 

    function loadPendingReceiving(data) {

        let html = "";

        if (data.length == 0) {

            html += "<tr>";
            html += "<td colspan='4' class='text-center'>No Pending Receiving.</td>";
            html += "</tr>";

        } else {

            $.each(data, function(i, row) {

                html += "<tr>";

                html += "<td>" + row.po_number + "</td>";

                html += "<td>" + row.company_name + "</td>";

                html += "<td>" + row.warehouse_name + "</td>";

                html += "<td>" + row.status + "</td>";

                html += "</tr>";

            });

        }

        $("#pendingReceiving").html(html);

    }

    function truncatePurchaseRecords(password) {
        Swal.fire({
            title: 'Processing...',
            text: 'Deleting all purchase records',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl('inventory/truncate-purchases') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                _csrf: '<?= Yii::$app->request->getCsrfToken() ?>',
                password: password
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        loadDashboard();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Failed to truncate records',
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Unable to process request',
                    icon: 'error'
                });
            }
        });
    }

</script>
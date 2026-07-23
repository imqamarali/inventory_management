<!--
================================================================================
SALES DASHBOARD VIEW
================================================================================
PURPOSE: Comprehensive sales overview with KPIs, charts, and analytics

FUNCTIONALITY:
- Display key sales metrics and statistics
- Show order status distribution (Draft, Confirmed, Dispatched, Delivered, Cancelled)
- Track payment collection progress
- Display top customers by sales value
- Show monthly sales trends
- List recent sales orders and POS transactions
- Provide quick navigation to sales modules

DATA DISPLAYED:
- Total Sales Orders count and value
- Order status breakdown with counts
- Total POS sales and value
- Invoice statistics (total, unpaid amounts)
- Customer payment receipts
- Sales returns count
- Customer count (active)
- Status distribution chart
- Top customers chart
- Monthly sales trend chart
- Recent orders and POS transactions

FINANCE INTEGRATION:
- Total sales value aggregates all inventory_sales_orders grand_total
- POS sales aggregates inventory_pos_sales grand_total for daily revenue
- Order statuses indicate fulfillment and revenue recognition timing
- Payment status tracking feeds into cash flow analysis
- Data syncs with Finance Dashboard for:
  • Total Revenue calculation
  • Daily/Monthly sales trends
  • Customer concentration analysis
  • Cash collection performance
================================================================================
-->

<div class="page-content">
    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-shopping-bag"></i>
                Sales Dashboard
                <small>Sales Overview & Analytics</small>
            </h3>
        </div> 
        <div>
            <button id="refreshDashboard">
                <i class="fa fa-refresh"></i>
                Refresh
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
            <button id="truncateSalesBtn" style="margin-left: 10px;    cursor: pointer;">
                <i class="fa fa-trash"></i>
                Truncate Sale Records
            </button>
            <?php endif; ?>
        </div>
    </div> 
    <div class="stats-grid"> 
        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Sales Orders</span>
                <div class="stat-icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
            </div>
            <div class="stat-value" id="total_sales_orders">0</div>
            <div class="stat-subtitle">Total Sales Orders</div>
        </div> 
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Customers</span>
                <div class="stat-icon">
                    <i class="fa fa-users"></i>
                </div>
            </div>
            <div class="stat-value" id="total_customers">0</div>
            <div class="stat-subtitle">Active Customers</div>
        </div> 
        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Draft Orders</span>
                <div class="stat-icon">
                    <i class="fa fa-file-text-o"></i>
                </div>
            </div>
            <div class="stat-value" id="draft_sales_orders">0</div>
            <div class="stat-subtitle">Draft Sales Orders</div>
        </div> 
        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Confirmed</span>
                <div class="stat-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value" id="confirmed_sales_orders">0</div>
            <div class="stat-subtitle">Confirmed Orders</div>
        </div>
        <div class="stat-card teal">
            <div class="stat-header">
                <span class="stat-title">Dispatched</span>
                <div class="stat-icon">
                    <i class="fa fa-truck"></i>
                </div>
            </div>
            <div class="stat-value" id="dispatched_sales_orders">0</div>
            <div class="stat-subtitle">Dispatched Orders</div>
        </div>
        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Delivered</span>
                <div class="stat-icon">
                    <i class="fa fa-check-square"></i>
                </div>
            </div>
            <div class="stat-value" id="delivered_sales_orders">0</div>
            <div class="stat-subtitle">Delivered Orders</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Cancelled</span>
                <div class="stat-icon">
                    <i class="fa fa-times-circle"></i>
                </div>
            </div>
            <div class="stat-value" id="cancelled_sales_orders">0</div>
            <div class="stat-subtitle">Cancelled Orders</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Sales Value</span>
                <div class="stat-icon">
                    <i class="fa fa-money"></i>
                </div>
            </div>
            <div class="stat-value" id="total_sales_value">PKR 0</div>
            <div class="stat-subtitle">Total Sales Amount</div>
        </div>

        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">POS Sales</span>
                <div class="stat-icon">
                    <i class="fa fa-desktop"></i>
                </div>
            </div>
            <div class="stat-value" id="total_pos_sales">0</div>
            <div class="stat-subtitle">POS Transactions</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">POS Value</span>
                <div class="stat-icon">
                    <i class="fa fa-credit-card"></i>
                </div>
            </div>
            <div class="stat-value" id="total_pos_value">PKR 0</div>
            <div class="stat-subtitle">POS Sales Value</div>
        </div>

        <div class="stat-card teal">
            <div class="stat-header">
                <span class="stat-title">Invoices</span>
                <div class="stat-icon">
                    <i class="fa fa-file-text"></i>
                </div>
            </div>
            <div class="stat-value" id="total_invoices">0</div>
            <div class="stat-subtitle">Sales Invoices</div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Outstanding</span>
                <div class="stat-icon">
                    <i class="fa fa-exclamation-circle"></i>
                </div>
            </div>
            <div class="stat-value" id="unpaid_invoice_amount">PKR 0</div>
            <div class="stat-subtitle">Outstanding Amount</div>
        </div>

    </div>
    <div class="row">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-pie-chart"></i>
                    Sales Orders By Status
                </h4>

                <canvas id="salesStatusChart" height="220"></canvas>

            </div>

        </div>

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-bar-chart"></i>
                    Top Customers
                </h4>

                <canvas id="customerChart" height="220"></canvas>

            </div>

        </div>

    </div>

    <div class="row" style="margin-top:15px;">

        <div class="col-md-12">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-line-chart"></i>
                    Monthly Sales Trend
                </h4>

                <canvas id="monthlySalesChart" height="100"></canvas>

            </div>

        </div>

    </div>

    <div class="row" style="margin-top:15px;">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-shopping-cart"></i>
                    Latest Sales Orders
                </h4>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover">

                        <thead>

                            <tr>
                                <th>Order No</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-right">Amount</th>
                            </tr>

                        </thead>

                        <tbody id="latestSalesOrders">

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
                    <i class="fa fa-desktop"></i>
                    Latest POS Sales
                </h4>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover">

                        <thead>

                            <tr>
                                <th>POS No</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-right">Amount</th>
                            </tr>

                        </thead>

                        <tbody id="latestPosSales">

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
    var salesStatusChart = null;
    var customerChart = null;
    var monthlySalesChart = null;
</script>
<script>
    $(function() {

        loadDashboard();

        $("#refreshDashboard").click(function() {

            loadDashboard();

        });

        $("#truncateSalesBtn").click(function() {
            Swal.fire({
                title: 'Truncate Sale Records',
                text: 'This action will delete ALL sale records including:\n- POS Sales\n- Sales Invoices\n- Payment History\n- Stock Movements\n\nThis cannot be undone!',
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
                    truncateSalesRecords(result.value);
                }
            });
        });

    });

    function loadDashboard() {

        showDashboardLoading();

        $.ajax({

            url: "<?= Yii::$app->urlManager->createUrl('sale/salesdashboard') ?>",
            type: "POST",
            dataType: "json",

            data: {
                flag: "load_dashboard"
            },

            success: function(response) {

                hideDashboardLoading();

                if (response.success) {

                    loadStatistics(response.stats);

                    loadSalesStatusChart(response.statusChart);

                    loadCustomerChart(response.customerChart);

                    loadMonthlySalesChart(response.monthlySales);

                    loadLatestSalesOrders(response.latestSalesOrders);

                    loadLatestPosSales(response.latestPosSales);

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

    function loadStatistics(stats){

    animateCounter("#total_sales_orders",stats.total_sales_orders);

    animateCounter("#total_customers",stats.total_customers);

    animateCounter("#draft_sales_orders",stats.draft_sales_orders);

    animateCounter("#confirmed_sales_orders",stats.confirmed_sales_orders);

    animateCounter("#dispatched_sales_orders",stats.dispatched_sales_orders);

    animateCounter("#delivered_sales_orders",stats.delivered_sales_orders);

    animateCounter("#cancelled_sales_orders",stats.cancelled_sales_orders);

    animateCurrency("#total_sales_value",stats.total_sales_value);

    animateCounter("#total_pos_sales",stats.total_pos_sales);

    animateCurrency("#total_pos_value",stats.total_pos_value);

    animateCounter("#total_invoices",stats.total_invoices);

    animateCurrency("#unpaid_invoice_amount",stats.unpaid_invoice_amount);

}

function animateCounter(id,value){

    value=(value==null||isNaN(value))?0:Number(value);

    $({
        count:0
    }).animate({
        count:value
    },{
        duration:700,
        easing:"swing",
        step:function(){

            $(id).text(Math.floor(this.count).toLocaleString());

        },
        complete:function(){

            $(id).text(Number(value).toLocaleString());

        }
    });

}

function animateCurrency(id,value){

    value=(value==null||isNaN(value))?0:Number(value);

    $({
        count:0
    }).animate({
        count:value
    },{
        duration:700,
        easing:"swing",
        step:function(){

            $(id).text("PKR "+Math.floor(this.count).toLocaleString());

        },
        complete:function(){

            $(id).text("PKR "+Number(value).toLocaleString());

        }
    });

}

function loadSalesStatusChart(data){

    if(salesStatusChart){
        salesStatusChart.destroy();
    }

    let labels=[];
    let values=[];

    $.each(data,function(i,row){

        labels.push(row.order_status);

        values.push(parseInt(row.total));

    });

    salesStatusChart=new Chart(
        document.getElementById("salesStatusChart"),
        {

            type:"doughnut",

            data:{

                labels:labels,

                datasets:[{

                    data:values,

                    backgroundColor:[
                        "#3498db",
                        "#2ecc71",
                        "#f39c12",
                        "#9b59b6",
                        "#1abc9c",
                        "#e74c3c"
                    ]

                }]

            },

            options:{

                responsive:true,

                plugins:{

                    legend:{

                        position:"bottom"

                    }

                }

            }

        }

    );

}

function loadCustomerChart(data){

    if(customerChart){
        customerChart.destroy();
    }

    let labels=[];

    let values=[];

    $.each(data,function(i,row){

        var customer="";

        if(row.company_name && row.company_name!=""){

            customer=row.company_name;

        }else{

            customer=(row.first_name?row.first_name:"")+" "+(row.last_name?row.last_name:"");

        }

        labels.push($.trim(customer));

        values.push(parseFloat(row.total));

    });

    customerChart=new Chart(
        document.getElementById("customerChart"),
        {

            type:"bar",

            data:{

                labels:labels,

                datasets:[{

                    label:"Sales Amount",

                    data:values,

                    backgroundColor:"#3498db"

                }]

            },

            options:{

                responsive:true,

                plugins:{

                    legend:{

                        display:false

                    }

                },

                scales:{

                    y:{

                        beginAtZero:true

                    }

                }

            }

        }

    );

}

function loadMonthlySalesChart(data){

    if(monthlySalesChart){
        monthlySalesChart.destroy();
    }

    let labels=[];

    let values=[];

    $.each(data,function(i,row){

        labels.push(row.month);

        values.push(parseFloat(row.total));

    });

    monthlySalesChart=new Chart(
        document.getElementById("monthlySalesChart"),
        {

            type:"line",

            data:{

                labels:labels,

                datasets:[{

                    label:"Sales Amount",

                    data:values,

                    fill:true,

                    borderColor:"#27ae60",

                    backgroundColor:"rgba(39,174,96,.15)",

                    tension:.4

                }]

            },

            options:{

                responsive:true,

                plugins:{

                    legend:{

                        display:false

                    }

                },

                scales:{

                    y:{

                        beginAtZero:true

                    }

                }

            }

        }

    );

}

function loadLatestSalesOrders(data){

    let html="";

    if(data.length==0){

        html+="<tr>";
        html+="<td colspan='5' class='text-center'>No Sales Orders Found.</td>";
        html+="</tr>";

    }else{

        $.each(data,function(i,row){

            var customer="";

            if(row.company_name && row.company_name!=""){

                customer=row.company_name;

            }else{

                customer=(row.first_name?row.first_name:"")+" "+(row.last_name?row.last_name:"");

            }

            html+="<tr>";

            html+="<td>"+row.order_number+"</td>";

            html+="<td>"+$.trim(customer)+"</td>";

            html+="<td>"+row.order_date+"</td>";

            html+="<td>"+row.order_status+"</td>";

            html+="<td class='text-right'>PKR "+Number(row.grand_total).toLocaleString()+"</td>";

            html+="</tr>";

        });

    }

    $("#latestSalesOrders").html(html);

}

function loadLatestPosSales(data){

    let html="";

    if(data.length==0){

        html+="<tr>";
        html+="<td colspan='5' class='text-center'>No POS Sales Found.</td>";
        html+="</tr>";

    }else{

        $.each(data,function(i,row){

            var customer="";

            if(row.company_name && row.company_name!=""){

                customer=row.company_name;

            }else{

                customer=(row.first_name?row.first_name:"")+" "+(row.last_name?row.last_name:"");

            }

            html+="<tr>";

            html+="<td>"+row.pos_no+"</td>";

            html+="<td>"+$.trim(customer)+"</td>";

            html+="<td>"+row.sale_date+"</td>";

            html+="<td>"+row.status+"</td>";

            html+="<td class='text-right'>PKR "+Number(row.grand_total).toLocaleString()+"</td>";

            html+="</tr>";

        });

    }

    $("#latestPosSales").html(html);

}

function truncateSalesRecords(password) {
    Swal.fire({
        title: 'Processing...',
        text: 'Deleting all sale records',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
    });

    $.ajax({
        url: '<?= Yii::$app->urlManager->createUrl('inventory/truncate-sales') ?>',
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
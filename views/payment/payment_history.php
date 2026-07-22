<!--
================================================================================
PAYMENT HISTORY VIEW
================================================================================
PURPOSE: Comprehensive payment overview with KPIs, trends, and invoice tracking

FUNCTIONALITY:
- Display key payment metrics and statistics
- Show payment status distribution
- Track payment history and trends
- Display monthly payment information
- List recent invoices and payment status
- Track payment amounts and due dates
- Provide quick navigation to payment modules

DATA DISPLAYED:
- Total invoice records
- Paid amount and remaining amount
- Next payment due date
- Payment status breakdown
- Monthly payment trends
- Recent invoices and payment status

================================================================================
-->

<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-credit-card"></i>
                Payment History
                <small>Payment Overview & Analytics</small>
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
                <span class="stat-title">Total Months</span>
                <div class="stat-icon">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
            <div class="stat-value" id="total_months">0</div>
            <div class="stat-subtitle">Invoice Records</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Paid Amount</span>
                <div class="stat-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value" id="paid_amount">PKR 0</div>
            <div class="stat-subtitle">Completed Payments</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Remaining Amount</span>
                <div class="stat-icon">
                    <i class="fa fa-hourglass"></i>
                </div>
            </div>
            <div class="stat-value" id="remaining_amount">PKR 0</div>
            <div class="stat-subtitle">Pending Payment</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Next Due</span>
                <div class="stat-icon">
                    <i class="fa fa-calendar-check-o"></i>
                </div>
            </div>
            <div class="stat-value" id="next_due_date">-</div>
            <div class="stat-subtitle">Payment Due Date</div>
        </div>

        <div class="stat-card teal">
            <div class="stat-header">
                <span class="stat-title">Unpaid Invoices</span>
                <div class="stat-icon">
                    <i class="fa fa-file"></i>
                </div>
            </div>
            <div class="stat-value" id="unpaid_count">0</div>
            <div class="stat-subtitle">Awaiting Payment</div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Paid Invoices</span>
                <div class="stat-icon">
                    <i class="fa fa-check-square"></i>
                </div>
            </div>
            <div class="stat-value" id="paid_count">0</div>
            <div class="stat-subtitle">Completed</div>
        </div>

    </div>

    <div class="row" style="margin-top:15px;">

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-list"></i>
                    Latest Invoices
                </h4>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover">

                        <thead>

                            <tr>

                                <th>Invoice #</th>
                                <th>Contract</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-right">Amount</th>

                            </tr>

                        </thead>

                        <tbody id="latestInvoices">

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
                    Pending Payments
                </h4>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover">

                        <thead>

                            <tr>

                                <th>Invoice #</th>
                                <th>Contract</th>
                                <th>Due Date</th>
                                <th class="text-right">Amount</th>

                            </tr>

                        </thead>

                        <tbody id="pendingPayments">

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

            url: "<?= Yii::$app->urlManager->createUrl('payment/payment-history') ?>",
            type: "POST",
            dataType: "json",

            data: {
                flag: "load_dashboard",
                "<?= Yii::$app->request->csrfParam ?>": "<?= Yii::$app->request->getCsrfToken() ?>"
            },

            success: function(response) {

                hideDashboardLoading();

                if (response.success) {

                    loadStatistics(response.stats);
                    loadLatestInvoices(response.latestInvoices);
                    loadPendingPayments(response.pendingPayments);

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

        animateCounter("#total_months", stats.total_months);
        animateCurrency("#paid_amount", stats.paid_amount);
        animateCurrency("#remaining_amount", stats.remaining_amount);
        animateCounter("#unpaid_count", stats.unpaid_count);
        animateCounter("#paid_count", stats.paid_count);

        // Set next due date (no animation)
        if (stats.next_due_date) {
            var dueDate = new Date(stats.next_due_date);
            var formattedDate = dueDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            $("#next_due_date").text(formattedDate);
        }

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

    function loadLatestInvoices(data) {

        let html = "";

        if (data.length == 0) {

            html += "<tr>";
            html += "<td colspan='5' class='text-center'>No Invoices Found.</td>";
            html += "</tr>";

        } else {

            $.each(data, function(i, row) {

                html += "<tr>";

                html += "<td>" + row.invoice_number + "</td>";

                html += "<td>" + row.contract_name + "</td>";

                html += "<td>" + row.invoice_date + "</td>";

                html += "<td><span class='label label-" + (row.payment_status === 'paid' ? 'success' : (row.payment_status === 'partial' ? 'warning' : 'danger')) + "'>" + row.payment_status.toUpperCase() + "</span></td>";

                html += "<td class='text-right'>PKR " + Number(row.amount).toLocaleString() + "</td>";

                html += "</tr>";

            });

        }

        $("#latestInvoices").html(html);

    }

    function loadPendingPayments(data) {

        let html = "";

        if (data.length == 0) {

            html += "<tr>";
            html += "<td colspan='4' class='text-center'>No Pending Payments.</td>";
            html += "</tr>";

        } else {

            $.each(data, function(i, row) {

                html += "<tr>";

                html += "<td>" + row.invoice_number + "</td>";

                html += "<td>" + row.contract_name + "</td>";

                html += "<td>" + row.due_date + "</td>";

                html += "<td class='text-right'>PKR " + Number(row.remaining_amount).toLocaleString() + "</td>";

                html += "</tr>";

            });

        }

        $("#pendingPayments").html(html);

    }

</script>

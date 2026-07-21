<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-line-chart"></i>
                Profit & Loss Statement
                <small>Income and Expense Report</small>
            </h3>
        </div>
        <div style="display: flex; gap: 10px;">
            <button id="refreshProfitLoss">
                <i class="fa fa-refresh"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Top Stat Cards -->
    <div class="stats-grid">
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Total Income</span>
                <div class="stat-icon"><i class="fa fa-arrow-circle-up"></i></div>
            </div>
            <div class="stat-value" id="total_income_value">...</div>
            <div class="stat-subtitle">Revenue & Income</div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Total Expense</span>
                <div class="stat-icon"><i class="fa fa-arrow-circle-down"></i></div>
            </div>
            <div class="stat-value" id="total_expense_value">...</div>
            <div class="stat-subtitle">All Expenses</div>
        </div>

        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Gross Profit</span>
                <div class="stat-icon"><i class="fa fa-dollar"></i></div>
            </div>
            <div class="stat-value" id="gross_profit_value">...</div>
            <div class="stat-subtitle">Income - Expenses</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Profit Margin</span>
                <div class="stat-icon"><i class="fa fa-percent"></i></div>
            </div>
            <div class="stat-value" id="profit_margin_value">...</div>
            <div class="stat-subtitle">Profit Ratio</div>
        </div>
    </div>

    <!-- Income and Expense Tables -->
    <div class="row" style="margin-top: 20px;">
        <div class="col-md-6">
            <div class="dashboard-box">
                <h4>
                    <i class="fa fa-arrow-circle-up"></i>
                    Income & Revenue
                </h4>

                <div style="overflow-x: auto;">
                    <table class="table table-striped table-hover compact-table">
                        <thead>
                            <tr>
                                <th width="70%">Description</th>
                                <th width="30%">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="income_tbody">
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 20px;">
                                    <small style="color: #999;">Loading...</small>
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
                    <i class="fa fa-arrow-circle-down"></i>
                    Expenses & Costs
                </h4>

                <div style="overflow-x: auto;">
                    <table class="table table-striped table-hover compact-table">
                        <thead>
                            <tr>
                                <th width="70%">Description</th>
                                <th width="30%">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="expense_tbody">
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 20px;">
                                    <small style="color: #999;">Loading...</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- Net Profit Summary -->
    <div class="row" style="margin-top: 20px;">
        <div class="col-md-12">
            <div class="dashboard-box">
                <h4>
                    <i class="fa fa-bar-chart"></i>
                    Net Profit Summary
                </h4>

                <div style="padding: 30px;">
                    <table class="summary-table" style="width: 100%; margin: 0 auto; max-width: 600px;">
                        <tr>
                            <td style="padding: 10px 0; text-align: right; width: 70%;">
                                <strong>Total Income</strong>
                            </td>
                            <td style="padding: 10px 20px; text-align: right; width: 30%;">
                                <strong class="text-success" id="summary_income">
                                    PKR 0.00
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; text-align: right; border-top: 1px solid #e9ecef;">
                                <strong>Less: Total Expense</strong>
                            </td>
                            <td style="padding: 10px 20px; text-align: right; border-top: 1px solid #e9ecef;">
                                <strong class="text-danger" id="summary_expense">
                                    PKR 0.00
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; text-align: right; border-top: 2px solid #2d3748; border-bottom: 2px solid #2d3748; background: #f8f9fa;">
                                <strong>NET PROFIT / LOSS</strong>
                            </td>
                            <td style="padding: 10px 20px; text-align: right; border-top: 2px solid #2d3748; border-bottom: 2px solid #2d3748; background: #f8f9fa;">
                                <strong id="summary_netprofit" style="font-size: 18px;">PKR 0.00</strong>
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
    $(function() {
        loadProfitLoss();

        $("#refreshProfitLoss").click(function() {
            loadProfitLoss();
        });
    });

    function loadProfitLoss() {
        showLoadingState();
        $.ajax({
            url: "<?= Yii::$app->urlManager->createUrl('finance/profitloss') ?>",
            type: "POST",
            dataType: "json",
            data: {
                flag: "search",
                from_date: '<?= $from_date ?? date("Y-m-01") ?>',
                to_date: '<?= $to_date ?? date("Y-m-d") ?>'
            },
            timeout: 5000,
            success: function(response) {
                hideLoadingState();
                if (response.success) {
                    loadProfitLossData(response);
                } else {
                    showError(response.message || 'Failed to load profit & loss data');
                }
            },
            error: function(xhr, status, error) {
                hideLoadingState();
                if (status === 'timeout') {
                    showError('Request timed out. Please try again.');
                } else {
                    showError('Network error: ' + (xhr.status || 'Unknown error'));
                }
            }
        });
    }

    function showLoadingState() {
        $("#total_income_value, #total_expense_value, #gross_profit_value, #profit_margin_value").each(function() {
            $(this).addClass("loading").html("...");
        });
    }

    function hideLoadingState() {
        $("#total_income_value, #total_expense_value, #gross_profit_value, #profit_margin_value").removeClass("loading");
    }

    function loadProfitLossData(data) {
        // Animate stat card values
        animateCurrency("#total_income_value", data.total_income);
        animateCurrency("#total_expense_value", data.total_expense);

        // Calculate and display gross profit
        var grossProfit = data.total_income - data.total_expense;
        animateCurrency("#gross_profit_value", grossProfit);

        // Calculate and display profit margin
        var profitMargin = data.total_income > 0 ? ((grossProfit / data.total_income) * 100) : 0;
        animatePercentage("#profit_margin_value", profitMargin);

        // Load income table
        loadIncomeTable(data.income, data.sales_total, data.total_income);

        // Load expense table
        loadExpenseTable(data.expense, data.purchase_total, data.total_expense);

        // Update net profit summary
        updateNetProfitSummary(data.total_income, data.total_expense, grossProfit);
    }

    function loadIncomeTable(income, salesTotal, totalIncome) {
        var html = '';

        if (!income || income.length === 0) {
            html += '<tr><td colspan="2" style="text-align: center; padding: 20px;"><small style="color: #999;">No income accounts</small></td></tr>';
        } else {
            income.forEach(function(item) {
                html += '<tr>';
                html += '<td>' + (item.account_name || '-') + '</td>';
                html += '<td><strong class="text-success">PKR ' + Number(item.total || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></td>';
                html += '</tr>';
            });
        }

        if (salesTotal && salesTotal > 0) {
            html += '<tr style="border-top: 2px solid #e9ecef;">';
            html += '<td><strong>Sales Orders</strong></td>';
            html += '<td><strong class="text-success">PKR ' + Number(salesTotal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></td>';
            html += '</tr>';
        }

        html += '<tr style="background: #f8f9fa; font-weight: bold;">';
        html += '<td>TOTAL INCOME</td>';
        html += '<td><span class="text-success">PKR ' + Number(totalIncome).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></td>';
        html += '</tr>';

        $("#income_tbody").html(html);
    }

    function loadExpenseTable(expense, purchaseTotal, totalExpense) {
        var html = '';

        if (!expense || expense.length === 0) {
            html += '<tr><td colspan="2" style="text-align: center; padding: 20px;"><small style="color: #999;">No expense accounts</small></td></tr>';
        } else {
            expense.forEach(function(item) {
                html += '<tr>';
                html += '<td>' + (item.account_name || '-') + '</td>';
                html += '<td><strong class="text-danger">PKR ' + Number(item.total || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></td>';
                html += '</tr>';
            });
        }

        if (purchaseTotal && purchaseTotal > 0) {
            html += '<tr style="border-top: 2px solid #e9ecef;">';
            html += '<td><strong>Purchase Orders</strong></td>';
            html += '<td><strong class="text-danger">PKR ' + Number(purchaseTotal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></td>';
            html += '</tr>';
        }

        html += '<tr style="background: #f8f9fa; font-weight: bold;">';
        html += '<td>TOTAL EXPENSE</td>';
        html += '<td><span class="text-danger">PKR ' + Number(totalExpense).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></td>';
        html += '</tr>';

        $("#expense_tbody").html(html);
    }

    function updateNetProfitSummary(totalIncome, totalExpense, netProfit) {
        $("#summary_income").text("PKR " + Number(totalIncome).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $("#summary_expense").text("PKR " + Number(totalExpense).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        if (netProfit >= 0) {
            $("#summary_netprofit").html('<span class="text-success" style="font-size: 18px;">PKR ' + Number(netProfit).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>');
        } else {
            $("#summary_netprofit").html('<span class="text-danger" style="font-size: 18px;">PKR (' + Number(Math.abs(netProfit)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ')</span>');
        }
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

                    $(id).text("PKR " + Number(value).toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0}));

                }

            });

    }

    function animatePercentage(id, value) {
        $({
            count: 0
        }).animate({
                count: value

            },

            {

                duration: 700,

                easing: "swing",

                step: function() {

                    $(id).text(Math.floor(this.count) + '%');

                },

                complete: function() {

                    $(id).text(Number(value).toFixed(1) + '%');

                }

            });

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

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-left: 3px solid;
        margin-bottom: 15px;
    }

    .stat-card.green { border-left-color: #2ecc71; }
    .stat-card.red { border-left-color: #e74c3c; }
    .stat-card.blue { border-left-color: #3498db; }
    .stat-card.purple { border-left-color: #9b59b6; }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .stat-title {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 600;
        color: #7f8c8d;
    }

    .stat-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .stat-card.green .stat-icon { background: rgba(46, 204, 113, 0.1); color: #2ecc71; }
    .stat-card.red .stat-icon { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }
    .stat-card.blue .stat-icon { background: rgba(52, 152, 219, 0.1); color: #3498db; }
    .stat-card.purple .stat-icon { background: rgba(155, 89, 182, 0.1); color: #9b59b6; }

    .stat-value {
        font-size: 20px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 5px;
    }

    .stat-subtitle {
        font-size: 11px;
        color: #95a5a6;
    }

    .dashboard-box {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 0;
        overflow: hidden;
    }

    .dashboard-box h4 {
        background: #f8f9fa;
        padding: 15px;
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #2d3748;
        border-bottom: 1px solid #e9ecef;
    }

    .dashboard-box h4 i {
        margin-right: 10px;
        color: #f39c12;
    }

    .compact-table {
        width: 100%;
        font-size: 12px;
        border-collapse: collapse;
    }

    .compact-table thead th {
        background: #f8f9fa;
        padding: 12px;
        font-weight: 600;
        text-align: left;
        border-bottom: 2px solid #e9ecef;
        color: #495057;
    }

    .compact-table tbody td {
        padding: 12px;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }

    .compact-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .text-success { color: #2ecc71; }
    .text-danger { color: #e74c3c; }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e9ecef;
    }

    .dashboard-header h3 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        color: #2d3748;
    }

    .dashboard-header h3 small {
        display: block;
        font-size: 12px;
        font-weight: 400;
        color: #7f8c8d;
        margin-top: 3px;
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
        border: none;
        padding: 6px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }
</style>

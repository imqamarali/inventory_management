<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-file-text-o"></i>
                Balance Sheet
                <small>Financial Summary</small>
            </h3>
        </div>
        <div style="display: flex; gap: 10px;">
            <button id="refreshBalanceSheet">
                <i class="fa fa-refresh"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Top Stat Cards -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Total Assets</span>
                <div class="stat-icon"><i class="fa fa-briefcase"></i></div>
            </div>
            <div class="stat-value" id="total_assets_value">...</div>
            <div class="stat-subtitle">Asset Value</div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Total Expenses</span>
                <div class="stat-icon"><i class="fa fa-arrow-circle-down"></i></div>
            </div>
            <div class="stat-value" id="total_expenses_value">...</div>
            <div class="stat-subtitle">Total Expenses</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Total Income</span>
                <div class="stat-icon"><i class="fa fa-arrow-circle-up"></i></div>
            </div>
            <div class="stat-value" id="total_income_value">...</div>
            <div class="stat-subtitle">Income Generated</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Balance Status</span>
                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="stat-value" id="balance_status_value">...</div>
            <div class="stat-subtitle">Financial Health</div>
        </div>
    </div>

    <!-- Sales and Purchase Accounts Section -->
    <div class="row" style="margin-top: 20px;">
        <div class="col-md-6">
            <div class="dashboard-box">
                <h4>
                    <i class="fa fa-shopping-cart"></i>
                    Sales Accounts
                </h4>

                <div style="overflow-x: auto;">
                    <table class="table table-striped table-hover compact-table">
                        <thead>
                            <tr>
                                <th width="70%">Account Name</th>
                                <th width="30%">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="sales_accounts_tbody">
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
                    <i class="fa fa-shopping-bag"></i>
                    Purchase Accounts
                </h4>

                <div style="overflow-x: auto;">
                    <table class="table table-striped table-hover compact-table">
                        <thead>
                            <tr>
                                <th width="70%">Account Name</th>
                                <th width="30%">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="purchase_accounts_tbody">
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

    <!-- Balance Sheet Equation -->
    <div class="row" style="margin-top: 20px;">
        <div class="col-md-12">
            <div class="dashboard-box">
                <h4>
                    <i class="fa fa-bar-chart"></i>
                    Balance Sheet Equation
                </h4>

                <div style="padding: 30px;">
                    <table class="summary-table" style="width: 100%; margin: 0 auto; max-width: 800px;">
                        <tr>
                            <td style="padding: 10px 0; text-align: center; width: 30%;">
                                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">ASSETS</div>
                                <div style="font-size: 18px; font-weight: bold; color: #3498db;" id="equation_assets">
                                    PKR 0
                                </div>
                            </td>
                            <td style="padding: 10px 0; text-align: center; width: 10%;">
                                <div style="font-size: 16px; font-weight: bold;">
                                    =
                                </div>
                            </td>
                            <td style="padding: 10px 0; text-align: center; width: 30%;">
                                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">INCOME</div>
                                <div style="font-size: 14px; font-weight: bold; color: #2ecc71;" id="equation_income">
                                    PKR 0
                                </div>
                            </td>
                            <td style="padding: 10px 0; text-align: center; width: 10%;">
                                <div style="font-size: 16px; font-weight: bold;">
                                    -
                                </div>
                            </td>
                            <td style="padding: 10px 0; text-align: center; width: 20%;">
                                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">EXPENSES</div>
                                <div style="font-size: 14px; font-weight: bold; color: #e74c3c;" id="equation_expenses">
                                    PKR 0
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" style="padding-top: 20px; text-align: center; border-top: 2px solid #e9ecef;">
                                <span id="balance_equation_status" style="font-weight: bold;">Loading...</span>
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
        loadBalanceSheet();

        $("#refreshBalanceSheet").click(function() {
            loadBalanceSheet();
        });
    });

    function loadBalanceSheet() {
        showLoadingState();
        $.ajax({
            url: "<?= Yii::$app->urlManager->createUrl('finance/financedashboard-data') ?>",
            type: "POST",
            dataType: "json",
            data: { flag: "load_dashboard" },
            timeout: 5000,
            success: function(response) {
                hideLoadingState();
                if (response.success) {
                    loadBalanceSheetData(response);
                } else {
                    showError(response.message || 'Failed to load balance sheet');
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
        $("#total_assets_value, #total_expenses_value, #total_income_value, #balance_status_value").each(function() {
            $(this).addClass("loading").html("...");
        });
    }

    function hideLoadingState() {
        $("#total_assets_value, #total_expenses_value, #total_income_value, #balance_status_value").removeClass("loading");
    }

    function loadBalanceSheetData(data) {
        // Load stat card values with animation
        animateCurrency("#total_assets_value", data.stats.total_assets);
        animateCurrency("#total_expenses_value", data.stats.total_expense);
        animateCurrency("#total_income_value", data.stats.total_income);

        // Load balance status based on income vs expenses
        var profit = data.stats.total_income - data.stats.total_expense;
        if (profit >= 0) {
            $("#balance_status_value").text("✓ Profitable");
        } else {
            $("#balance_status_value").text("✗ Loss");
        }

        // Load sales accounts
        loadSalesAccountsTable(data.salesStats);

        // Load purchase accounts
        loadPurchaseAccountsTable(data.purchaseStats);

        // Load balance sheet equation
        animateCurrency("#equation_assets", data.stats.total_assets);
        animateCurrency("#equation_income", data.stats.total_income);
        animateCurrency("#equation_expenses", data.stats.total_expense);

        // Load balance equation status
        var profit = data.stats.total_income - data.stats.total_expense;
        if (profit >= 0) {
            $("#balance_equation_status").html('<span style="color: #2ecc71; font-weight: bold;">✓ Assets = ' + Number(profit).toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0}) + ' (Profit)</span>');
        } else {
            $("#balance_equation_status").html('<span style="color: #e74c3c; font-weight: bold;">✗ Assets = ' + Number(profit).toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0}) + ' (Loss)</span>');
        }
    }

    function loadSalesAccountsTable(salesStats) {
        var html = '';

        if (!salesStats) {
            html = '<tr><td colspan="2" style="text-align: center; padding: 20px;"><small style="color: #999;">No sales data</small></td></tr>';
        } else {
            html += '<tr>';
            html += '<td>Total Sales Invoices</td>';
            html += '<td><strong class="text-success">' + (salesStats.total_sales_invoices || 0) + '</strong></td>';
            html += '</tr>';

            html += '<tr>';
            html += '<td>Total Sales Amount</td>';
            html += '<td><strong class="text-success">PKR ' + Number(salesStats.total_sales_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></td>';
            html += '</tr>';

            html += '<tr>';
            html += '<td>Paid Sales</td>';
            html += '<td><strong class="text-success">PKR ' + Number(salesStats.paid_sales_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></td>';
            html += '</tr>';

            html += '<tr style="background: #f8f9fa; font-weight: bold;">';
            html += '<td>Outstanding Sales</td>';
            html += '<td><span class="text-info">PKR ' + Number(salesStats.unpaid_sales_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></td>';
            html += '</tr>';
        }

        $("#sales_accounts_tbody").html(html);
    }

    function loadPurchaseAccountsTable(purchaseStats) {
        var html = '';

        if (!purchaseStats) {
            html = '<tr><td colspan="2" style="text-align: center; padding: 20px;"><small style="color: #999;">No purchase data</small></td></tr>';
        } else {
            html += '<tr>';
            html += '<td>Total Purchase Invoices</td>';
            html += '<td><strong class="text-danger">' + (purchaseStats.total_purchase_invoices || 0) + '</strong></td>';
            html += '</tr>';

            html += '<tr>';
            html += '<td>Total Purchase Amount</td>';
            html += '<td><strong class="text-danger">PKR ' + Number(purchaseStats.total_purchase_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></td>';
            html += '</tr>';

            html += '<tr>';
            html += '<td>Paid Purchases</td>';
            html += '<td><strong class="text-danger">PKR ' + Number(purchaseStats.paid_purchase_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></td>';
            html += '</tr>';

            html += '<tr style="background: #f8f9fa; font-weight: bold;">';
            html += '<td>Outstanding Purchases</td>';
            html += '<td><span class="text-danger">PKR ' + Number(purchaseStats.unpaid_purchase_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></td>';
            html += '</tr>';
        }

        $("#purchase_accounts_tbody").html(html);
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

    .stat-card.blue { border-left-color: #3498db; }
    .stat-card.red { border-left-color: #e74c3c; }
    .stat-card.green { border-left-color: #2ecc71; }
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

    .stat-card.blue .stat-icon { background: rgba(52, 152, 219, 0.1); color: #3498db; }
    .stat-card.red .stat-icon { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }
    .stat-card.green .stat-icon { background: rgba(46, 204, 113, 0.1); color: #2ecc71; }
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

    .text-info { color: #3498db; }
    .text-danger { color: #e74c3c; }
    .text-success { color: #2ecc71; }

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

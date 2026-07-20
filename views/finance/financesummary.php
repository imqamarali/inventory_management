<?php
/**
 * FINANCE SUMMARY VIEW
 * ================================================================================
 * PURPOSE: Simplified financial dashboard showing Sales, Purchases, and Expenses
 *
 * DISPLAYS:
 * - Total Sales Revenue (Sales Orders + POS Sales)
 * - Total Purchase Expenses (Purchase Orders)
 * - Operating Expenses (Rent, Electricity, Other)
 * - Net Profit/Loss Summary
 * - Monthly comparison charts
 * ================================================================================
 */

use yii\helpers\Html;

$this->title = 'Finance Summary';

if (!isset($data)) $data = [];
if (!isset($from_date)) $from_date = date('Y-m-01');
if (!isset($to_date)) $to_date = date('Y-m-d');
?>

<div class="main-content">
    <div class="main-content-inner">

        <!-- Breadcrumbs -->
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=finance/finance">Finance</a>
                </li>
                <li class="active">Finance Summary</li>
            </ul>
        </div>

        <!-- Date Range Filter -->
        <div style="padding-top:10px;padding-left:13px;padding-bottom:15px;">
            <form id="date_filter" onsubmit="return false;">
                <input type="date" name="from_date" id="from_date" class="new-input" style="width:15%;" value="<?= $from_date ?>">
                <input type="date" name="to_date" id="to_date" class="new-input" style="width:15%;" value="<?= $to_date ?>">
                <button type="button" class="btn btn-primary" onclick="filterFinance()" style="height:30px;padding:5px 15px;">
                    <i class="fa fa-filter"></i> Filter
                </button>
                <button type="button" class="btn btn-default" onclick="downloadPDF()" style="height:30px;padding:5px 15px;">
                    <i class="fa fa-download"></i> Download PDF
                </button>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-sm-6">
                <div class="widget-box">
                    <div class="widget-header">
                        <h4 class="widget-title">
                            <i class="fa fa-shopping-cart"></i> Total Sales
                        </h4>
                    </div>
                    <div class="widget-body">
                        <div class="text-center">
                            <h2 class="text-success">
                                <?= isset($data['total_sales']) ? number_format($data['total_sales'], 2) : '0.00' ?>
                            </h2>
                            <small>Sales Orders + POS Sales</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="widget-box">
                    <div class="widget-header">
                        <h4 class="widget-title">
                            <i class="fa fa-shopping-bag"></i> Total Purchases
                        </h4>
                    </div>
                    <div class="widget-body">
                        <div class="text-center">
                            <h2 class="text-danger">
                                <?= isset($data['total_purchases']) ? number_format($data['total_purchases'], 2) : '0.00' ?>
                            </h2>
                            <small>Purchase Orders (COGS)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top:15px;">
            <div class="col-sm-6">
                <div class="widget-box">
                    <div class="widget-header">
                        <h4 class="widget-title">
                            <i class="fa fa-credit-card"></i> Total Expenses
                        </h4>
                    </div>
                    <div class="widget-body">
                        <div class="text-center">
                            <h2 class="text-warning">
                                <?= isset($data['total_expenses']) ? number_format($data['total_expenses'], 2) : '0.00' ?>
                            </h2>
                            <small>Rent + Electricity + Other</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="widget-box">
                    <div class="widget-header">
                        <h4 class="widget-title">
                            <i class="fa fa-line-chart"></i> Net Profit/Loss
                        </h4>
                    </div>
                    <div class="widget-body">
                        <div class="text-center">
                            <?php
                            $net = (isset($data['total_sales']) ? $data['total_sales'] : 0) -
                                   (isset($data['total_purchases']) ? $data['total_purchases'] : 0) -
                                   (isset($data['total_expenses']) ? $data['total_expenses'] : 0);
                            $color = $net >= 0 ? 'text-success' : 'text-danger';
                            ?>
                            <h2 class="<?= $color ?>">
                                <?= number_format($net, 2) ?>
                            </h2>
                            <small>Sales - Purchases - Expenses</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Links -->
        <div class="row" style="margin-top:20px;">
            <div class="col-sm-12">
                <div class="widget-box">
                    <div class="widget-header">
                        <h4 class="widget-title">Quick Access</h4>
                    </div>
                    <div class="widget-body">
                        <a href="index.php?r=finance/salesrecords" class="btn btn-sm btn-info" style="margin:5px;">
                            <i class="fa fa-eye"></i> View Sales Records
                        </a>
                        <a href="index.php?r=finance/purchaserecords" class="btn btn-sm btn-info" style="margin:5px;">
                            <i class="fa fa-eye"></i> View Purchase Records
                        </a>
                        <a href="index.php?r=finance/expenserecords" class="btn btn-sm btn-info" style="margin:5px;">
                            <i class="fa fa-eye"></i> View Expense Records
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- main-content-inner -->
</div><!-- main-content -->

<script>
function filterFinance() {
    const from_date = document.getElementById('from_date').value;
    const to_date = document.getElementById('to_date').value;
    window.location.href = `index.php?r=finance/financesummary&from_date=${from_date}&to_date=${to_date}`;
}

function downloadPDF() {
    const from_date = document.getElementById('from_date').value;
    const to_date = document.getElementById('to_date').value;
    window.open(`index.php?r=finance/financesummary&from_date=${from_date}&to_date=${to_date}&pdf=1`, '_blank');
}
</script>

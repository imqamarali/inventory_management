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

        <div>
            <a href="<?= Url::to(['finance/finance']) ?>" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Total Income</span>
                <div class="stat-icon"><i class="fa fa-arrow-circle-down"></i></div>
            </div>
            <div class="stat-value">
                PKR <?= number_format($total_income ?? 0, 0) ?>
            </div>
            <div class="stat-subtitle">Revenue & Income</div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Total Expense</span>
                <div class="stat-icon"><i class="fa fa-arrow-circle-up"></i></div>
            </div>
            <div class="stat-value">
                PKR <?= number_format($total_expense ?? 0, 0) ?>
            </div>
            <div class="stat-subtitle">All Expenses</div>
        </div>

        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Gross Profit</span>
                <div class="stat-icon"><i class="fa fa-dollar"></i></div>
            </div>
            <div class="stat-value">
                <?php $gp = ($total_income ?? 0) - ($total_expense ?? 0); ?>
                PKR <?= number_format($gp, 0) ?>
            </div>
            <div class="stat-subtitle">Income - Expenses</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Profit Margin</span>
                <div class="stat-icon"><i class="fa fa-percent"></i></div>
            </div>
            <div class="stat-value">
                <?php
                    $margin = ($total_income ?? 0) > 0 ? (($gp / ($total_income ?? 0)) * 100) : 0;
                    echo number_format($margin, 1) . '%';
                ?>
            </div>
            <div class="stat-subtitle">Profit Ratio</div>
        </div>
    </div>

    <div class="row" style="margin-top: 20px;">
        <div class="col-md-6">
            <div class="dashboard-box">
                <h4>
                    <i class="fa fa-arrow-circle-down"></i>
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
                        <tbody>
                            <?php
                            $income = $income ?? [];
                            $totalIncome = 0;
                            if (empty($income)): ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; padding: 20px;">
                                        <small style="color: #999;">No income accounts</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($income as $item): ?>
                                    <?php $totalIncome += (float)($item['total'] ?? 0); ?>
                                    <tr>
                                        <td>
                                            <?= Html::encode($item['account_name'] ?? '-') ?>
                                        </td>
                                        <td>
                                            <strong class="text-success">
                                                PKR <?= number_format((float)($item['total'] ?? 0), 2) ?>
                                            </strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php
                            $salesTotal = $sales_total ?? 0;
                            $totalIncome += $salesTotal;
                            if ($salesTotal > 0): ?>
                                <tr style="border-top: 2px solid #e9ecef;">
                                    <td><strong>Sales Orders</strong></td>
                                    <td>
                                        <strong class="text-success">
                                            PKR <?= number_format($salesTotal, 2) ?>
                                        </strong>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr style="background: #f8f9fa; font-weight: bold;">
                                <td>TOTAL INCOME</td>
                                <td>
                                    <span class="text-success">
                                        PKR <?= number_format($totalIncome, 2) ?>
                                    </span>
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
                    <i class="fa fa-arrow-circle-up"></i>
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
                        <tbody>
                            <?php
                            $expense = $expense ?? [];
                            $totalExpense = 0;
                            if (empty($expense)): ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; padding: 20px;">
                                        <small style="color: #999;">No expense accounts</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($expense as $item): ?>
                                    <?php $totalExpense += (float)($item['total'] ?? 0); ?>
                                    <tr>
                                        <td>
                                            <?= Html::encode($item['account_name'] ?? '-') ?>
                                        </td>
                                        <td>
                                            <strong class="text-danger">
                                                PKR <?= number_format((float)($item['total'] ?? 0), 2) ?>
                                            </strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php
                            $purchaseTotal = $purchase_total ?? 0;
                            $totalExpense += $purchaseTotal;
                            if ($purchaseTotal > 0): ?>
                                <tr style="border-top: 2px solid #e9ecef;">
                                    <td><strong>Purchase Orders</strong></td>
                                    <td>
                                        <strong class="text-danger">
                                            PKR <?= number_format($purchaseTotal, 2) ?>
                                        </strong>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr style="background: #f8f9fa; font-weight: bold;">
                                <td>TOTAL EXPENSE</td>
                                <td>
                                    <span class="text-danger">
                                        PKR <?= number_format($totalExpense, 2) ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

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
                                <strong class="text-success">
                                    PKR <?= number_format($total_income ?? 0, 2) ?>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; text-align: right; border-top: 1px solid #e9ecef;">
                                <strong>Less: Total Expense</strong>
                            </td>
                            <td style="padding: 10px 20px; text-align: right; border-top: 1px solid #e9ecef;">
                                <strong class="text-danger">
                                    PKR <?= number_format($total_expense ?? 0, 2) ?>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; text-align: right; border-top: 2px solid #2d3748; border-bottom: 2px solid #2d3748; background: #f8f9fa;">
                                <strong>NET PROFIT / LOSS</strong>
                            </td>
                            <td style="padding: 10px 20px; text-align: right; border-top: 2px solid #2d3748; border-bottom: 2px solid #2d3748; background: #f8f9fa;">
                                <?php
                                $netProfit = ($total_income ?? 0) - ($total_expense ?? 0);
                                if ($netProfit >= 0) {
                                    echo '<strong class="text-success" style="font-size: 18px;">PKR ' . number_format($netProfit, 2) . '</strong>';
                                } else {
                                    echo '<strong class="text-danger" style="font-size: 18px;">PKR (' . number_format(abs($netProfit), 2) . ')</strong>';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>

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

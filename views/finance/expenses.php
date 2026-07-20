<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-credit-card"></i>
                Expenses
                <small>Business Expense Tracking & Recording</small>
            </h3>
        </div>

        <div>
            <a href="<?= Url::to(['finance/finance']) ?>" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Total Expenses</span>
                <div class="stat-icon"><i class="fa fa-credit-card"></i></div>
            </div>
            <div class="stat-value">PKR 0</div>
            <div class="stat-subtitle">All Expenses</div>
        </div>

        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">This Month</span>
                <div class="stat-icon"><i class="fa fa-calendar"></i></div>
            </div>
            <div class="stat-value">PKR 0</div>
            <div class="stat-subtitle">Current Month</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Total Records</span>
                <div class="stat-icon"><i class="fa fa-list-alt"></i></div>
            </div>
            <div class="stat-value">0</div>
            <div class="stat-subtitle">Expense Entries</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Avg. Expense</span>
                <div class="stat-icon"><i class="fa fa-bar-chart"></i></div>
            </div>
            <div class="stat-value">PKR 0</div>
            <div class="stat-subtitle">Average Amount</div>
        </div>
    </div>

    <div class="dashboard-box" style="margin-top: 20px;">
                <h4>
                    <i class="fa fa-list"></i>
                    Expenses List
                </h4>

                <div style="overflow-x: auto;">
                    <table class="table table-striped table-hover compact-table">
                        <thead>
                            <tr>
                                <th width="12%">Date</th>
                                <th width="10%">Entry No.</th>
                                <th width="22%">Expense Account</th>
                                <th width="15%">Amount</th>
                                <th width="20%">Remarks</th>
                                <th width="13%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $expenses = $expenses ?? [];
                            if (empty($expenses)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px;">
                                        <i class="fa fa-inbox" style="font-size: 30px; color: #ccc;"></i>
                                        <p style="color: #999; margin-top: 10px;">No expenses found</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($expenses as $expense): ?>
                                    <tr>
                                        <td>
                                            <small><?= Html::encode(date('d-M-Y', strtotime($expense['transaction_date'] ?? ''))) ?></small>
                                        </td>
                                        <td>
                                            <strong><?= Html::encode($expense['transaction_no'] ?? '-') ?></strong>
                                        </td>
                                        <td>
                                            <?= Html::encode($expense['account_name'] ?? '-') ?>
                                        </td>
                                        <td>
                                            <strong class="text-danger">
                                                PKR <?= number_format((float)($expense['amount'] ?? 0), 2) ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <small><?= Html::encode($expense['remarks'] ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $is_active = $expense['is_active'] ?? 1;
                                            $badge_class = $is_active ? 'badge-success' : 'badge-danger';
                                            $status_text = $is_active ? 'Active' : 'Inactive';
                                            ?>
                                            <span class="badge badge-sm <?= $badge_class ?>">
                                                <?= $status_text ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
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

    .stat-card.red { border-left-color: #e74c3c; }
    .stat-card.blue { border-left-color: #3498db; }
    .stat-card.purple { border-left-color: #9b59b6; }
    .stat-card.orange { border-left-color: #e67e22; }

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

    .stat-card.red .stat-icon { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }
    .stat-card.blue .stat-icon { background: rgba(52, 152, 219, 0.1); color: #3498db; }
    .stat-card.purple .stat-icon { background: rgba(155, 89, 182, 0.1); color: #9b59b6; }
    .stat-card.orange .stat-icon { background: rgba(230, 126, 34, 0.1); color: #e67e22; }

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
        color: #e74c3c;
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

    .text-danger { color: #e74c3c; }

    .badge-sm {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        display: inline-block;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

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

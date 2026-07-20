<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-balance-scale"></i>
                Trial Balance
                <small>Account Debit & Credit Summary Report</small>
            </h3>
        </div>

        <div>
            <a href="<?= Url::to(['finance/finance']) ?>" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Total Accounts</span>
                <div class="stat-icon"><i class="fa fa-folder-open"></i></div>
            </div>
            <div class="stat-value" id="total-accounts">0</div>
            <div class="stat-subtitle">Active Accounts</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Total Debits</span>
                <div class="stat-icon"><i class="fa fa-arrow-right"></i></div>
            </div>
            <div class="stat-value" id="total-debits">PKR 0</div>
            <div class="stat-subtitle">Debit Side</div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Total Credits</span>
                <div class="stat-icon"><i class="fa fa-arrow-left"></i></div>
            </div>
            <div class="stat-value" id="total-credits">PKR 0</div>
            <div class="stat-subtitle">Credit Side</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Balance</span>
                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="stat-value" id="balance-status">✓ Balanced</div>
            <div class="stat-subtitle">Debit = Credit</div>
        </div>
    </div>

    <div class="dashboard-box" style="margin-top: 20px;">
                <h4>
                    <i class="fa fa-list"></i>
                    Trial Balance Report
                </h4>

                <div style="overflow-x: auto;">
                    <table class="table table-striped table-hover compact-table">
                        <thead>
                            <tr>
                                <th width="8%">Code</th>
                                <th width="22%">Account Name</th>
                                <th width="15%">Account Type</th>
                                <th width="18%">Total Debit</th>
                                <th width="18%">Total Credit</th>
                                <th width="19%">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rows = $rows ?? [];
                            $totalDebits = 0;
                            $totalCredits = 0;
                            if (empty($rows)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px;">
                                        <i class="fa fa-inbox" style="font-size: 30px; color: #ccc;"></i>
                                        <p style="color: #999; margin-top: 10px;">No trial balance data found</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($rows as $row): ?>
                                    <?php
                                    $totalDebits += (float)($row['total_debit'] ?? 0);
                                    $totalCredits += (float)($row['total_credit'] ?? 0);
                                    ?>
                                    <tr>
                                        <td>
                                            <small><?= Html::encode($row['account_code'] ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <strong><?= Html::encode($row['account_name'] ?? '-') ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm badge-info">
                                                <?= Html::encode($row['account_type'] ?? '-') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $debit = (float)($row['total_debit'] ?? 0);
                                            if ($debit > 0) {
                                                echo '<span class="text-danger">PKR ' . number_format($debit, 2) . '</span>';
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $credit = (float)($row['total_credit'] ?? 0);
                                            if ($credit > 0) {
                                                echo '<span class="text-success">PKR ' . number_format($credit, 2) . '</span>';
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <strong>
                                                <?php
                                                $balance = $debit - $credit;
                                                if ($balance > 0) {
                                                    echo '<span class="text-danger">Dr: PKR ' . number_format(abs($balance), 2) . '</span>';
                                                } elseif ($balance < 0) {
                                                    echo '<span class="text-success">Cr: PKR ' . number_format(abs($balance), 2) . '</span>';
                                                } else {
                                                    echo '<span class="text-muted">-</span>';
                                                }
                                                ?>
                                            </strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr style="background: #f8f9fa; font-weight: bold;">
                                    <td colspan="3">TOTAL</td>
                                    <td>
                                        <span class="text-danger">PKR <?= number_format($totalDebits, 2) ?></span>
                                    </td>
                                    <td>
                                        <span class="text-success">PKR <?= number_format($totalCredits, 2) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $diff = abs($totalDebits - $totalCredits);
                                        if ($diff < 0.01) {
                                            echo '<span class="text-success">✓ Balanced</span>';
                                        } else {
                                            echo '<span class="text-danger">✗ Imbalanced: ' . number_format($diff, 2) . '</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
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

    .stat-card.blue { border-left-color: #3498db; }
    .stat-card.green { border-left-color: #2ecc71; }
    .stat-card.red { border-left-color: #e74c3c; }
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
    .stat-card.green .stat-icon { background: rgba(46, 204, 113, 0.1); color: #2ecc71; }
    .stat-card.red .stat-icon { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }
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
        color: #9b59b6;
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
    .text-muted { color: #95a5a6; }

    .badge-sm {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        display: inline-block;
    }

    .badge-info {
        background: #cffafe;
        color: #0c4a6e;
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

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
                <small>Statement of Financial Position</small>
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
                <span class="stat-title">Total Assets</span>
                <div class="stat-icon"><i class="fa fa-folder-open"></i></div>
            </div>
            <div class="stat-value">
                PKR <?= number_format($total_assets ?? 0, 0) ?>
            </div>
            <div class="stat-subtitle">What We Own</div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Total Liabilities</span>
                <div class="stat-icon"><i class="fa fa-credit-card"></i></div>
            </div>
            <div class="stat-value">
                PKR <?= number_format($total_liabilities ?? 0, 0) ?>
            </div>
            <div class="stat-subtitle">What We Owe</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Total Equity</span>
                <div class="stat-icon"><i class="fa fa-dollar"></i></div>
            </div>
            <div class="stat-value">
                PKR <?= number_format($total_equity ?? 0, 0) ?>
            </div>
            <div class="stat-subtitle">Net Worth</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Balance Status</span>
                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="stat-value">
                <?php
                $diff = abs(($total_assets ?? 0) - (($total_liabilities ?? 0) + ($total_equity ?? 0)));
                echo $diff < 0.01 ? '✓ OK' : '✗ Diff';
                ?>
            </div>
            <div class="stat-subtitle">Assets = L + E</div>
        </div>
    </div>

    <div class="row" style="margin-top: 20px;">
        <div class="col-md-6">
            <div class="dashboard-box">
                <h4>
                    <i class="fa fa-folder-open"></i>
                    Assets
                </h4>

                <div style="overflow-x: auto;">
                    <table class="table table-striped table-hover compact-table">
                        <thead>
                            <tr>
                                <th width="70%">Asset Account</th>
                                <th width="30%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $assets = $assets ?? [];
                            if (empty($assets)): ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; padding: 20px;">
                                        <small style="color: #999;">No asset accounts</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($assets as $asset): ?>
                                    <tr>
                                        <td>
                                            <?= Html::encode($asset['account_name'] ?? '-') ?>
                                        </td>
                                        <td>
                                            <strong class="text-info">
                                                PKR <?= number_format((float)($asset['current_balance'] ?? 0), 2) ?>
                                            </strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <tr style="background: #f8f9fa; font-weight: bold; border-top: 2px solid #e9ecef;">
                                <td>TOTAL ASSETS</td>
                                <td>
                                    <span class="text-info">
                                        PKR <?= number_format($total_assets ?? 0, 2) ?>
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
                    <i class="fa fa-credit-card"></i>
                    Liabilities & Equity
                </h4>

                <div style="overflow-x: auto;">
                    <table class="table table-striped table-hover compact-table">
                        <thead>
                            <tr>
                                <th width="70%">Account</th>
                                <th width="30%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $liabilities = $liabilities ?? [];
                            if (empty($liabilities)): ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; padding: 10px;">
                                        <small style="color: #999;">No liability accounts</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($liabilities as $liability): ?>
                                    <tr>
                                        <td>
                                            <?= Html::encode($liability['account_name'] ?? '-') ?>
                                        </td>
                                        <td>
                                            <strong class="text-danger">
                                                PKR <?= number_format((float)($liability['current_balance'] ?? 0), 2) ?>
                                            </strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <tr style="background: #f8f9fa; font-weight: bold;">
                                <td>TOTAL LIABILITIES</td>
                                <td>
                                    <span class="text-danger">
                                        PKR <?= number_format($total_liabilities ?? 0, 2) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr style="border-top: 2px solid #e9ecef;">
                                <td colspan="2" style="padding: 5px;"></td>
                            </tr>
                            <?php
                            $equity = $equity ?? [];
                            if (!empty($equity)):
                                foreach ($equity as $eq):
                            ?>
                                    <tr>
                                        <td>
                                            <?= Html::encode($eq['account_name'] ?? '-') ?>
                                        </td>
                                        <td>
                                            <strong class="text-success">
                                                PKR <?= number_format((float)($eq['current_balance'] ?? 0), 2) ?>
                                            </strong>
                                        </td>
                                    </tr>
                            <?php
                                endforeach;
                            else:
                            ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; padding: 10px;">
                                        <small style="color: #999;">No equity accounts</small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr style="background: #f8f9fa; font-weight: bold;">
                                <td>TOTAL EQUITY</td>
                                <td>
                                    <span class="text-success">
                                        PKR <?= number_format($total_equity ?? 0, 2) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr style="background: #f8f9fa; font-weight: bold;">
                                <td>TOTAL LIABILITIES + EQUITY</td>
                                <td>
                                    <span class="text-success">
                                        PKR <?= number_format($total_liabilities_equity ?? 0, 2) ?>
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
                    Balance Sheet Equation
                </h4>

                <div style="padding: 30px;">
                    <table class="summary-table" style="width: 100%; margin: 0 auto; max-width: 600px;">
                        <tr>
                            <td style="padding: 10px 0; text-align: center; width: 33%;">
                                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">ASSETS</div>
                                <div style="font-size: 18px; font-weight: bold; color: #3498db;">
                                    PKR <?= number_format($total_assets ?? 0, 0) ?>
                                </div>
                            </td>
                            <td style="padding: 10px 0; text-align: center; width: 33%;">
                                <div style="font-size: 16px; font-weight: bold;">
                                    =
                                </div>
                            </td>
                            <td style="padding: 10px 0;">
                                <div style="text-align: center;">
                                    <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">LIABILITIES</div>
                                    <div style="font-size: 14px; font-weight: bold; color: #e74c3c;">
                                        PKR <?= number_format($total_liabilities ?? 0, 0) ?>
                                    </div>
                                </div>
                                <div style="text-align: center; margin-top: 10px;">
                                    <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">+ EQUITY</div>
                                    <div style="font-size: 14px; font-weight: bold; color: #2ecc71;">
                                        PKR <?= number_format($total_equity ?? 0, 0) ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding-top: 20px; text-align: center; border-top: 2px solid #e9ecef;">
                                <?php
                                $diff = abs(($total_assets ?? 0) - (($total_liabilities ?? 0) + ($total_equity ?? 0)));
                                if ($diff < 0.01) {
                                    echo '<span style="color: #2ecc71; font-weight: bold;">✓ Balance Sheet is Balanced</span>';
                                } else {
                                    echo '<span style="color: #e74c3c; font-weight: bold;">✗ Balance Sheet Imbalance: ' . number_format($diff, 2) . '</span>';
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

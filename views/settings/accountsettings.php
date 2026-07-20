<?php
use yii\helpers\Html;

if (!isset($settings)) $settings = [];
if (!isset($accounts)) $accounts = [];

$accountsByType = [];
foreach ($accounts as $account) {
    $type = $account['account_type'];
    if (!isset($accountsByType[$type])) {
        $accountsByType[$type] = [];
    }
    $accountsByType[$type][] = $account;
}
?>

<div class="widget-box">
    <div class="widget-header" style="background-color: #0f4c29; color: white; padding: 12px 15px;">
        <h4 class="widget-title" style="color: white; margin: 0;">
            <i class="ace-icon fa fa-sitemap"></i> Account Settings
        </h4>
    </div>

    <div class="widget-body" style="padding: 20px;">
        <form id="accountsettings_form" method="POST" onsubmit="return saveAccountSettings()">
            <div class="row">
                <!-- Sales Account -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="default_sales_account">
                            <strong>Default Sales Account</strong>
                            <span class="label label-warning" style="margin-left: 8px; font-size: 11px;">Income</span>
                        </label>
                        <select name="default_sales_account" id="default_sales_account" class="form-control" style="width: 100%;">
                            <option value="">-- Select Sales Account --</option>
                            <?php if (isset($accountsByType['Income'])): ?>
                                <?php foreach ($accountsByType['Income'] as $account): ?>
                                    <option value="<?= $account['id'] ?>" <?= ($settings['default_sales_account'] == $account['id']) ? 'selected' : '' ?>>
                                        [<?= Html::encode($account['account_code']) ?>] <?= Html::encode($account['account_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small style="color: #666; display: block; margin-top: 5px;">
                            <i class="fa fa-info-circle"></i> Account used to record sales revenue
                        </small>
                    </div>
                </div>

                <!-- Purchase Account -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="default_purchase_account">
                            <strong>Default Purchase Account</strong>
                            <span class="label label-info" style="margin-left: 8px; font-size: 11px;">Expense</span>
                        </label>
                        <select name="default_purchase_account" id="default_purchase_account" class="form-control" style="width: 100%;">
                            <option value="">-- Select Purchase Account --</option>
                            <?php if (isset($accountsByType['Expense'])): ?>
                                <?php foreach ($accountsByType['Expense'] as $account): ?>
                                    <option value="<?= $account['id'] ?>" <?= ($settings['default_purchase_account'] == $account['id']) ? 'selected' : '' ?>>
                                        [<?= Html::encode($account['account_code']) ?>] <?= Html::encode($account['account_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small style="color: #666; display: block; margin-top: 5px;">
                            <i class="fa fa-info-circle"></i> Account used to record purchase expenses
                        </small>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Expense Account -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="default_expense_account">
                            <strong>Default Expense Account</strong>
                            <span class="label label-danger" style="margin-left: 8px; font-size: 11px;">Expense</span>
                        </label>
                        <select name="default_expense_account" id="default_expense_account" class="form-control" style="width: 100%;">
                            <option value="">-- Select Expense Account --</option>
                            <?php if (isset($accountsByType['Expense'])): ?>
                                <?php foreach ($accountsByType['Expense'] as $account): ?>
                                    <option value="<?= $account['id'] ?>" <?= ($settings['default_expense_account'] == $account['id']) ? 'selected' : '' ?>>
                                        [<?= Html::encode($account['account_code']) ?>] <?= Html::encode($account['account_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small style="color: #666; display: block; margin-top: 5px;">
                            <i class="fa fa-info-circle"></i> Account used for miscellaneous expenses
                        </small>
                    </div>
                </div>

                <!-- Refund Account -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="default_refund_account">
                            <strong>Default Refund Account</strong>
                            <span class="label label-success" style="margin-left: 8px; font-size: 11px;">Liability</span>
                        </label>
                        <select name="default_refund_account" id="default_refund_account" class="form-control" style="width: 100%;">
                            <option value="">-- Select Refund Account --</option>
                            <?php if (isset($accountsByType['Liability'])): ?>
                                <?php foreach ($accountsByType['Liability'] as $account): ?>
                                    <option value="<?= $account['id'] ?>" <?= ($settings['default_refund_account'] == $account['id']) ? 'selected' : '' ?>>
                                        [<?= Html::encode($account['account_code']) ?>] <?= Html::encode($account['account_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small style="color: #666; display: block; margin-top: 5px;">
                            <i class="fa fa-info-circle"></i> Account used for customer refunds
                        </small>
                    </div>
                </div>
            </div>

            <div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #0f4c29; margin: 25px 0;">
                <i class="fa fa-info-circle" style="color: #0f4c29;"></i>
                <strong style="margin-left: 8px;">Note:</strong> These settings establish the default accounts used for Sales, Purchase, Expense, and Refund transactions. Individual transactions can still specify different accounts if needed.
            </div>

            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd;">
                <button type="submit" class="btn btn-primary" style="background-color: #0f4c29; border-color: #0f4c29;">
                    <i class="fa fa-save"></i> Save Settings
                </button>
                <button type="reset" class="btn btn-default" style="margin-left: 10px;">
                    <i class="fa fa-refresh"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function saveAccountSettings() {
    const formData = new FormData(document.getElementById('accountsettings_form'));
    formData.append('flag', 'save');

    fetch('index.php?r=settings/accountsettings', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal({
                title: 'Success!',
                text: data.message,
                type: 'success',
                confirmButtonColor: '#0f4c29'
            });
        } else {
            swal({
                title: 'Error!',
                text: data.message,
                type: 'error',
                confirmButtonColor: '#0f4c29'
            });
        }
    })
    .catch(error => {
        swal({
            title: 'Error!',
            text: 'Failed to save settings',
            type: 'error',
            confirmButtonColor: '#0f4c29'
        });
        console.error('Error:', error);
    });

    return false;
}
</script>

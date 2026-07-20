<?php
/**
 * ACCOUNT SETTINGS VIEW
 * ================================================================================
 * PURPOSE: Configure default financial accounts for Sales, Purchase, Expense & Refunds
 *
 * DISPLAYS:
 * - Default Sales Account selector
 * - Default Purchase Account selector
 * - Default Expense Account selector
 * - Default Refund Account selector
 * ================================================================================
 */

use yii\helpers\Html;

if (!isset($settings)) $settings = [];
if (!isset($accounts)) $accounts = [];

// Group accounts by type
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
    <div class="widget-header with-tool" style="background-color: #0f4c29; color: white;">
        <h4 class="widget-title" style="color: white;">
            <i class="ace-icon fa fa-sitemap"></i> Account Settings
        </h4>
    </div>

    <div class="widget-body">
        <form id="accountsettings_form" method="POST" onsubmit="return saveAccountSettings()">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="default_sales_account">
                    <strong>Default Sales Account</strong>
                    <span class="label label-warning" style="margin-left: 10px; font-size: 11px;">Income</span>
                </label>
                <select name="default_sales_account" id="default_sales_account" class="new-input" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                    <option value="">-- Select Sales Account --</option>
                    <?php if (isset($accountsByType['Income'])): ?>
                        <optgroup label="Income Accounts">
                            <?php foreach ($accountsByType['Income'] as $account): ?>
                                <option value="<?= $account['id'] ?>"
                                    <?= ($settings['default_sales_account'] == $account['id']) ? 'selected' : '' ?>>
                                    [<?= Html::encode($account['account_code']) ?>] <?= Html::encode($account['account_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endif; ?>
                </select>
                <small style="color: #666; display: block; margin-top: 5px;">
                    <i class="fa fa-info-circle"></i> Account used to record sales revenue
                </small>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="default_purchase_account">
                    <strong>Default Purchase Account</strong>
                    <span class="label label-info" style="margin-left: 10px; font-size: 11px;">Expense</span>
                </label>
                <select name="default_purchase_account" id="default_purchase_account" class="new-input" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                    <option value="">-- Select Purchase Account --</option>
                    <?php if (isset($accountsByType['Expense'])): ?>
                        <optgroup label="Expense Accounts">
                            <?php foreach ($accountsByType['Expense'] as $account): ?>
                                <option value="<?= $account['id'] ?>"
                                    <?= ($settings['default_purchase_account'] == $account['id']) ? 'selected' : '' ?>>
                                    [<?= Html::encode($account['account_code']) ?>] <?= Html::encode($account['account_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endif; ?>
                </select>
                <small style="color: #666; display: block; margin-top: 5px;">
                    <i class="fa fa-info-circle"></i> Account used to record purchase expenses
                </small>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="default_expense_account">
                    <strong>Default Expense Account</strong>
                    <span class="label label-danger" style="margin-left: 10px; font-size: 11px;">Expense</span>
                </label>
                <select name="default_expense_account" id="default_expense_account" class="new-input" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                    <option value="">-- Select Expense Account --</option>
                    <?php if (isset($accountsByType['Expense'])): ?>
                        <optgroup label="Expense Accounts">
                            <?php foreach ($accountsByType['Expense'] as $account): ?>
                                <option value="<?= $account['id'] ?>"
                                    <?= ($settings['default_expense_account'] == $account['id']) ? 'selected' : '' ?>>
                                    [<?= Html::encode($account['account_code']) ?>] <?= Html::encode($account['account_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endif; ?>
                </select>
                <small style="color: #666; display: block; margin-top: 5px;">
                    <i class="fa fa-info-circle"></i> Account used for miscellaneous expenses
                </small>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="default_refund_account">
                    <strong>Default Refund Account</strong>
                    <span class="label label-success" style="margin-left: 10px; font-size: 11px;">Liability</span>
                </label>
                <select name="default_refund_account" id="default_refund_account" class="new-input" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                    <option value="">-- Select Refund Account --</option>
                    <?php if (isset($accountsByType['Liability'])): ?>
                        <optgroup label="Liability Accounts">
                            <?php foreach ($accountsByType['Liability'] as $account): ?>
                                <option value="<?= $account['id'] ?>"
                                    <?= ($settings['default_refund_account'] == $account['id']) ? 'selected' : '' ?>>
                                    [<?= Html::encode($account['account_code']) ?>] <?= Html::encode($account['account_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endif; ?>
                </select>
                <small style="color: #666; display: block; margin-top: 5px;">
                    <i class="fa fa-info-circle"></i> Account used for customer refunds
                </small>
            </div>

            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 3px; margin-top: 30px;">
                <div class="alert alert-info" style="margin: 0;">
                    <i class="fa fa-info-circle"></i>
                    <strong>Note:</strong> These settings establish the default accounts used for Sales, Purchase, Expense, and Refund transactions.
                    Individual transactions can still specify different accounts if needed.
                </div>
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

<style>
.new-input {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 13px;
    transition: border-color 0.3s;
}

.new-input:focus {
    border-color: #0f4c29;
    outline: none;
    box-shadow: 0 0 5px rgba(15, 76, 41, 0.3);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}
</style>

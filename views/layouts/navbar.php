<?php

use yii\helpers\Html;
use yii\helpers\Url;

$session = Yii::$app->session;
$user = $session->get('user_array');

$loggedIn = !Yii::$app->user->isGuest && !empty($user);

$userFirstName = $user['first_name'] ?? 'User';
$userInitial = strtoupper(substr($userFirstName, 0, 1));

// Get company info from settings
$companyName = Yii::$app->db->createCommand(
    "SELECT setting_value FROM inventory_settings WHERE setting_key='app_name' AND is_deleted=0 LIMIT 1"
)->queryScalar() ?: 'Inventory Management System';

$companyTagline = Yii::$app->db->createCommand(
    "SELECT setting_value FROM inventory_settings WHERE setting_key='company_tagline' AND is_deleted=0 LIMIT 1"
)->queryScalar() ?: 'All Systems Operational';

$navbarColor = Yii::$app->db->createCommand(
    "SELECT setting_value FROM inventory_settings WHERE setting_key='navbar_color' AND is_deleted=0 LIMIT 1"
)->queryScalar() ?: '#0f4c29';

$profileImage = null;
$showInitials = true;

if (!empty($user['profile_picture'])) {
    $imagePath = Yii::getAlias('@webroot/' . $user['profile_picture']);
    if (file_exists($imagePath)) {
        $profileImage = Url::to('@web/' . $user['profile_picture']);
        $showInitials = false;
    }
}

$userLastName = $user['last_name'] ?? '';
$userInitials = strtoupper(substr($userFirstName, 0, 1) . substr($userLastName, 0, 1));
if (empty($userInitials) || $userInitials === '') {
    $userInitials = strtoupper(substr($userFirstName, 0, 2));
}

// Get user role name
$userRoleName = '';
if ($loggedIn && isset($user['role_id'])) {
    $userRoleName = Yii::$app->db->createCommand(
        "SELECT name FROM roles WHERE id = :role_id LIMIT 1"
    )->bindValue(':role_id', $user['role_id'])->queryScalar() ?: 'User';
}
?>

<div id="navbar"
     class="navbar navbar-default navbar-fixed-top ace-save-state"
     style="background:<?= Html::encode($navbarColor) ?>;border-color:<?= Html::encode($navbarColor) ?>;">

    <div class="navbar-container ace-save-state" id="navbar-container">

        <div class="navbar-header pull-left">

            <a href="<?= Url::to(['inventory/dashboard']) ?>" class="navbar-brand">
                <small style="font-size: medium;"><?= Html::encode($companyName) ?></small>
            </a>

        </div>
        <div class="navbar-header pull-left hidden-xs">

            <ul class="nav navbar-nav">
                <li>
                    <a href="<?= Url::to(['inventory/dashboard']) ?>">
                        <?= Html::encode($companyTagline) ?>
                    </a>
                </li>
            </ul>

        </div>

        <?php if ($loggedIn): ?>

            <!-- Current Month Invoice Info Chip (Centered) -->
            <div style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">
                <?php
                $currentInvoice = Yii::$app->db->createCommand(
                    "SELECT si.invoice_number, si.due_date, si.payment_status
                     FROM system_invoices si
                     WHERE si.is_deleted = 0
                     AND MONTH(si.invoice_date) = MONTH(NOW())
                     AND YEAR(si.invoice_date) = YEAR(NOW())
                     LIMIT 1"
                )->queryOne();
                ?>
                <?php if ($currentInvoice): ?>
                    <?php
                    $dueDate = new DateTime($currentInvoice['due_date']);
                    $today = new DateTime();
                    $daysLeft = $dueDate->diff($today)->days;
                    if ($dueDate < $today) {
                        $daysLeft = -$daysLeft;
                    }

                    $borderColor = $daysLeft > 3 ? '#4CAF50' : '#FF5252';
                    $textColor = $daysLeft > 3 ? '#4CAF50' : '#FF5252';
                    $statusIcon = $currentInvoice['payment_status'] === 'paid' ? '✓ PAID' : '⚠️ UNPAID';
                    $statusColor = $currentInvoice['payment_status'] === 'paid' ? '#4CAF50' : '#FF5252';
                    ?>
                    <div style="
                        display: inline-block;
                        padding: 8px 16px;
                        border-radius: 20px;
                        color: white;
                        font-size: 13px;
                        font-weight: 500;
                        white-space: nowrap;
                    ">
                        <span>
                            ⏱️ <?php if ($daysLeft > 0): ?><?= $daysLeft ?> days<?php else: ?><?= abs($daysLeft) ?> overdue<?php endif; ?>
                        </span>
                        &nbsp;|&nbsp;
                        <span style="color: <?= $statusColor ?>;">
                            <?= $statusIcon ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="navbar-buttons navbar-header pull-right">

                <ul class="nav ace-nav">

                    <!-- Payment History Icon -->
                    <li class="dropdown-modal">
                        <a href="index.php?r=payment/payment-history" title="Payment History" style="background: transparent !important; padding: 0 10px;">
                            <i class="ace-icon fa  fa-heart red" style="font-size: 18px; color: rgba(255,255,255,0.9);"></i>
                        </a>
                    </li>

                    <li class=" dropdown-modal">

                        <a data-toggle="dropdown"
                           href="#"
                           class="dropdown-toggle"
                           style="background: <?= Html::encode($navbarColor) ?>; display: flex; align-items: center; gap: 12px; padding: 8px 15px !important;">

                            <div style="width: 35px; height: 30px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <?php if ($showInitials): ?>
                                    <div class="nav-user-photo"
                                        style="width: 35px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: bold; font-size: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                                        <?= Html::encode($userInitials) ?>
                                    </div>
                                <?php else: ?>
                                    <img class="nav-user-photo"
                                        style="width: 35px; height: 30px; border-radius: 50%; object-fit: cover; object-position: center; box-shadow: 0 2px 8px rgba(0,0,0,0.2);"
                                         src="<?= Html::encode($profileImage) ?>"
                                         alt="Profile">
                                <?php endif; ?>
                            </div>

                            <div style="display: flex; flex-direction: column; justify-content: center;">
                                <div style="font-size: 14px; font-weight: 600; color: white; line-height: 1.2;">
                                    <?= Html::encode($userFirstName) ?>
                                </div>
                                <div style="font-size: 12px; color: rgba(255,255,255,0.8); line-height: 1.2;">
                                    <?= Html::encode($userRoleName) ?>
                                </div>
                            </div>

                            <i class="fa fa-chevron-down" style="color: rgba(255,255,255,0.7); font-size: 12px; margin-left: 8px;"></i>

                        </a>

                        <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
 
                            <li class="divider"></li>

                            <li>
                                <a href="index.php?r=user/profile">
                                    <i class="ace-icon fa fa-user"></i>
                                    My Profile
                                </a>
                            </li>

                            <li>
                                <?= Html::beginForm(['site/logout'], 'post') ?>
                                <button type="submit"
                                        class="btn btn-link"
                                        style="width:100%;text-align:left;padding:10px 20px;color:#333;text-decoration:none;">
                                    <i class="ace-icon fa fa-power-off"></i>
                                    Logout
                                </button>
                                <?= Html::endForm() ?>
                            </li>

                        </ul>

                    </li>

                </ul>

            </div>

        <?php endif; ?>

    </div>

</div>
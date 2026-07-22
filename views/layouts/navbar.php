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
                        <a href="index.php?r=payment-history/index" title="Payment History">
                            <i class="ace-icon fa fa-credit-card" style="font-size: 16px; color: rgba(255,255,255,0.8);"></i>
                            <span class="user-info" style="font-size: 11px; white-space: nowrap;">
                                Payments
                            </span>
                        </a>
                    </li>

                    <li class=" dropdown-modal">

                        <a data-toggle="dropdown"
                           href="#"
                           class="dropdown-toggle"
                           style="background: <?= Html::encode($navbarColor) ?>;">

                            <div style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; margin-right: 5px; margin-left: -5px;">
                                <?php if ($showInitials): ?>
                                    <div class="nav-user-photo"
                                        style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: rgba(255,255,255,0.2); color: white; font-weight: bold; font-size: 14px;">
                                        <?= Html::encode($userInitials) ?>
                                    </div>
                                <?php else: ?>
                                    <img class="nav-user-photo"
                                        style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; object-position: center;     margin-top: 10px;"
                                         src="<?= Html::encode($profileImage) ?>"
                                         alt="Profile">
                                <?php endif; ?>
                            </div>

                            <span class="user-info">
                                <small>Welcome</small>
                                <?= Html::encode($userFirstName) ?>
                            </span>


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
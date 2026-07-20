<?php

use yii\helpers\Html;
use yii\helpers\Url;

$session = Yii::$app->session;
$user = $session->get('user_array');

$loggedIn = !Yii::$app->user->isGuest && !empty($user);

$userFirstName = $user['first_name'] ?? 'User';
$userInitial = strtoupper(substr($userFirstName, 0, 1));

$school = null;

if (!empty($user['school_id'])) {
    $school = Yii::$app->db->createCommand("
        SELECT school_name,motto,logo,navbar_color
        FROM school
        WHERE school_id=:id
        LIMIT 1
    ")
        ->bindValue(':id', $user['school_id'])
        ->queryOne();
}

$schoolName = $school['school_name'] ?? 'Online Quran Academy';
$schoolMotto = $school['motto'] ?? 'Learning Management System';
$navbarColor = $school['navbar_color'] ?? '#0f4c29';

$profileImage = Url::to('@web/images/default-user.png');

if (!empty($user['profile_picture'])) {
    $profileImage = Url::to('@web/' . $user['profile_picture']);
}
?>

<div id="navbar"
     class="navbar navbar-default navbar-fixed-top ace-save-state"
     style="background:<?= Html::encode($navbarColor) ?>;border-color:<?= Html::encode($navbarColor) ?>;">

    <div class="navbar-container ace-save-state" id="navbar-container">

        <div class="navbar-header pull-left">

            <a href="<?= Url::to(['inventory/dashboard']) ?>" class="navbar-brand">
                <small style="font-size: medium;"><?= Html::encode($schoolName) ?></small>
            </a>

        </div>

        <div class="navbar-header pull-left hidden-xs">

            <ul class="nav navbar-nav">
                <li>
                    <a href="<?= Url::to(['inventory/dashboard']) ?>">
                        <?= Html::encode($schoolMotto) ?>
                    </a>
                </li>
            </ul>

        </div>

        <?php if ($loggedIn): ?>

            <div class="navbar-buttons navbar-header pull-right">

                <ul class="nav ace-nav">

                    <li class=" dropdown-modal">

                        <a data-toggle="dropdown"
                           href="#"
                           class="dropdown-toggle"
                           style="background: #438eb9;">

                            <img class="nav-user-photo"
                                style="max-width: 33px;"
                                 src="<?= Html::encode($profileImage) ?>"
                                 alt="Profile">

                            <span class="user-info">
                                <small>Welcome</small>
                                <?= Html::encode($userFirstName) ?>
                            </span>
 

                        </a>

                        <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
 
                            <li class="divider"></li>

                            <li>
                                <a href="index.php?r=settings/profile">
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
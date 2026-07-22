<?php

use yii\helpers\Html;

?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%; margin-bottom: 8px;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li class="active">User Profile & Settings</li>
            </ul>
        </div>

        <div class="page-content" style="padding: 8px;">
            <!-- Profile Card at Top Left -->
            <div class="row" style="margin: 0; margin-bottom: 8px;">
                <div class="col-md-2" style="padding: 4px;">
                    <div class="widget-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; margin: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
                        <div class="widget-body" style="padding: 12px;">
                            <div class="text-center">
                                <div style="width: 80px; height: 80px; background: white; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <?php if (!empty($user['profile_picture'])): ?>
                                        <img src="<?= Html::encode($user['profile_picture']) ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="Profile">
                                    <?php else: ?>
                                        <i class="ace-icon fa fa-user fa-3x" style="color: #667eea;"></i>
                                    <?php endif; ?>
                                </div>
                                <h4 style="margin: 6px 0; font-weight: bold; font-size: 14px;">
                                    <?= Html::encode(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'User') ?>
                                </h4>
                                <p style="margin: 3px 0; font-size: 11px; opacity: 0.85;">
                                    <i class="fa fa-user-circle"></i> <?= Html::encode($user['username'] ?? '-') ?>
                                </p>
                                <p style="margin: 3px 0; font-size: 10px; opacity: 0.8;">
                                    <i class="fa fa-envelope"></i> <?= Html::encode($user['email'] ?? '-') ?>
                                </p>
                            </div>
                            <hr style="margin: 8px 0; opacity: 0.3;">
                            <div style="text-align: center; font-size: 11px;">
                                <div style="margin: 4px 0;">
                                    <span class="label" style="background-color: <?= ($user['is_active'] ?? 0) ? '#4CAF50' : '#FF9800' ?>; color: white; padding: 3px 8px; font-size: 10px;">
                                        <?= ($user['is_active'] ?? 0) ? '✓ Active' : '○ Inactive' ?>
                                    </span>
                                </div>
                                <div style="margin: 4px 0; opacity: 0.9;">
                                    <small>Member since</small><br>
                                    <strong style="font-size: 11px;"><?= date('M Y', strtotime($user['created_at'] ?? 'now')) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 1: Personal Information (Full Width) -->
            <div class="row" style="margin: 0; margin-bottom: 8px;">
                <div class="col-md-12" style="padding: 4px;">
                    <div class="widget-box" style="margin: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div class="widget-header" style="padding: 8px 12px; background: #f5f5f5; border-bottom: 1px solid #e3e9f3;">
                            <h4 class="widget-title" style="margin: 0; font-size: 13px; font-weight: bold;">
                                <i class="fa fa-user"></i> Personal Information
                            </h4>
                        </div>
                        <div class="widget-body" style="padding: 10px;">
                            <div class="row" style="margin: 0;">
                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">First Name</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 12px; border-left: 2px solid #2196F3;">
                                            <?= Html::encode($user['first_name'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Last Name</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 12px; border-left: 2px solid #2196F3;">
                                            <?= Html::encode($user['last_name'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Username</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 12px; border-left: 2px solid #9C27B0;">
                                            <?= Html::encode($user['username'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Email</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 11px; border-left: 2px solid #2196F3; word-break: break-word;">
                                            <?= Html::encode($user['email'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Phone</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 12px; border-left: 2px solid #4CAF50;">
                                            <i class="fa fa-phone"></i> <?= Html::encode($user['phone'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">WhatsApp</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 12px; border-left: 2px solid #25D366;">
                                            <i class="fa fa-whatsapp"></i> <?= Html::encode($user['whatsapp'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin: 0;">
                                <div class="col-md-3" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Date of Birth</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 12px; border-left: 2px solid #FF6B6B;">
                                            <i class="fa fa-birthday-cake"></i> <?= $user['date_of_birth'] ? date('M d, Y', strtotime($user['date_of_birth'])) : '-' ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Gender</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 12px; border-left: 2px solid #FF1493;">
                                            <?php
                                            $genderIcon = match($user['gender'] ?? '') {
                                                'Male' => '♂ ',
                                                'Female' => '♀ ',
                                                default => '○ '
                                            };
                                            echo $genderIcon . Html::encode($user['gender'] ?? '-');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Account Status</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; border-left: 2px solid #4CAF50;">
                                            <span class="label" style="background-color: <?= ($user['status'] ?? 0) ? '#4CAF50' : '#FF9800' ?>; color: white; padding: 3px 8px; font-size: 10px; display: inline-block;">
                                                <?= ($user['status'] ?? 0) ? '✓ Enabled' : '○ Disabled' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Reference</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 12px; border-left: 2px solid #9C27B0;">
                                            <i class="fa fa-link"></i> <?= Html::encode($user['referance'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Location & Social (Full Width) -->
            <div class="row" style="margin: 0; margin-bottom: 8px;">
                <div class="col-md-12" style="padding: 4px;">
                    <div class="widget-box" style="margin: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div class="widget-header" style="padding: 8px 12px; background: #f5f5f5; border-bottom: 1px solid #e3e9f3;">
                            <h4 class="widget-title" style="margin: 0; font-size: 13px; font-weight: bold;">
                                <i class="fa fa-map-marker"></i> Location & Social Media
                            </h4>
                        </div>
                        <div class="widget-body" style="padding: 10px;">
                            <div class="row" style="margin: 0;">
                                <div class="col-md-3" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Address</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 12px; border-left: 2px solid #2196F3; min-height: 30px;">
                                            <i class="fa fa-home"></i> <?= Html::encode($user['address'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">City</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 12px; border-left: 2px solid #FF9800;">
                                            <?= Html::encode($user['city'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Country</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 12px; border-left: 2px solid #4CAF50;">
                                            <i class="fa fa-globe"></i> <?= Html::encode($user['country'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Facebook</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 11px; border-left: 2px solid #1877F2; word-break: break-word;">
                                            <i class="fa fa-facebook" style="color: #1877F2;"></i> <?= Html::encode($user['facebook'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Instagram</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 11px; border-left: 2px solid #E4405F; word-break: break-word;">
                                            <i class="fa fa-instagram" style="color: #E4405F;"></i> <?= Html::encode($user['instagram'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Pinterest</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 11px; border-left: 2px solid #E60B3F; word-break: break-word;">
                                            <i class="fa fa-pinterest" style="color: #E60B3F;"></i> <?= Html::encode($user['pinterest'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($user['about'])): ?>
                            <div class="row" style="margin: 0;">
                                <div class="col-md-12" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">About</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 11px; border-left: 2px solid #2196F3; max-height: 50px; overflow-y: auto;">
                                            <?= Html::encode($user['about']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 3: Account & Security (Full Width) -->
            <div class="row" style="margin: 0;">
                <div class="col-md-12" style="padding: 4px;">
                    <div class="widget-box" style="margin: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div class="widget-header" style="padding: 8px 12px; background: #f5f5f5; border-bottom: 1px solid #e3e9f3;">
                            <h4 class="widget-title" style="margin: 0; font-size: 13px; font-weight: bold;">
                                <i class="fa fa-lock"></i> Account & Security
                            </h4>
                        </div>
                        <div class="widget-body" style="padding: 10px;">
                            <div class="row" style="margin: 0;">
                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Account Created</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 11px; border-left: 2px solid #2196F3;">
                                            <i class="fa fa-calendar"></i> <?= date('M d, Y', strtotime($user['created_at'] ?? 'now')) ?><br>
                                            <small style="opacity: 0.7;"><?= date('h:i A', strtotime($user['created_at'] ?? 'now')) ?></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Last Updated</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 11px; border-left: 2px solid #9C27B0;">
                                            <i class="fa fa-refresh"></i> <?= $user['updated_at'] ? date('M d, Y', strtotime($user['updated_at'])) : 'N/A' ?><br>
                                            <?php if ($user['updated_at']): ?>
                                                <small style="opacity: 0.7;"><?= date('h:i A', strtotime($user['updated_at'])) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Last Login</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; font-size: 11px; border-left: 2px solid #FF9800;">
                                            <i class="fa fa-clock-o"></i> <?= $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never' ?><br>
                                            <?php if ($user['last_login']): ?>
                                                <small style="opacity: 0.7;"><?= date('h:i A', strtotime($user['last_login'])) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Failed Attempts</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; border-left: 2px solid <?= ($user['failed_login_attempts'] ?? 0) > 0 ? '#f44336' : '#4CAF50' ?>;">
                                            <span style="font-size: 16px; font-weight: bold; color: <?= ($user['failed_login_attempts'] ?? 0) > 0 ? '#f44336' : '#4CAF50' ?>;">
                                                <?= ($user['failed_login_attempts'] ?? 0) ?>
                                            </span>
                                            <span style="font-size: 10px; color: #999;"> attempt<?= ($user['failed_login_attempts'] ?? 0) != 1 ? 's' : '' ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Account Active</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; border-left: 2px solid #4CAF50;">
                                            <span class="label" style="background-color: <?= ($user['is_active'] ?? 0) ? '#4CAF50' : '#FF9800' ?>; color: white; padding: 3px 8px; font-size: 10px; display: inline-block;">
                                                <?= ($user['is_active'] ?? 0) ? '✓ Yes' : '○ No' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2" style="padding: 3px;">
                                    <div style="margin-bottom: 6px;">
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">&nbsp;</label>
                                        <div style="display: flex; gap: 4px; margin-bottom: 6px;">
                                            <button class="btn btn-primary" onclick="showChangePasswordModal()" style="flex: 1; padding: 5px; font-size: 11px;">
                                                <i class="fa fa-key"></i> Password
                                            </button>
                                            <button class="btn btn-warning" onclick="logoutConfirm()" style="flex: 1; padding: 5px; font-size: 11px;">
                                                <i class="fa fa-sign-out"></i> Logout
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showChangePasswordModal() {
        Swal.fire({
            title: 'Change Password',
            html: `
                <form id="changePasswordForm" style="text-align: left;">
                    <div style="margin-bottom: 12px;">
                        <label style="font-weight: bold; display: block; margin-bottom: 4px; font-size: 12px;">Current Password</label>
                        <input type="password" id="currentPassword" class="form-control" placeholder="Enter current password" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 3px; font-size: 12px;">
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="font-weight: bold; display: block; margin-bottom: 4px; font-size: 12px;">New Password</label>
                        <input type="password" id="newPassword" class="form-control" placeholder="Enter new password" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 3px; font-size: 12px;">
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="font-weight: bold; display: block; margin-bottom: 4px; font-size: 12px;">Confirm Password</label>
                        <input type="password" id="confirmPassword" class="form-control" placeholder="Confirm new password" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 3px; font-size: 12px;">
                    </div>
                </form>
            `,
            width: '450px',
            showCancelButton: true,
            confirmButtonText: '<i class="fa fa-save"></i> Update Password',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#87B87F',
            cancelButtonColor: '#6c757d',
            preConfirm: () => {
                const current = document.getElementById('currentPassword').value;
                const newPass = document.getElementById('newPassword').value;
                const confirm = document.getElementById('confirmPassword').value;

                if (!current) {
                    Swal.showValidationMessage('Current password is required');
                    return false;
                }

                if (!newPass) {
                    Swal.showValidationMessage('New password is required');
                    return false;
                }

                if (newPass.length < 6) {
                    Swal.showValidationMessage('Password must be at least 6 characters');
                    return false;
                }

                if (newPass !== confirm) {
                    Swal.showValidationMessage('Passwords do not match');
                    return false;
                }

                return { current, newPass };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Coming Soon', 'Password change feature will be available soon', 'info');
            }
        });
    }

    function logoutConfirm() {
        Swal.fire({
            title: 'Logout',
            text: 'Are you sure you want to logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php?r=site/logout';
            }
        });
    }
</script>

<style>
    .page-content {
        max-height: calc(100vh - 150px);
        overflow-y: auto;
    }

    .widget-box {
        border: 1px solid #e3e9f3;
        border-radius: 3px;
        background: white;
    }

    .widget-header {
        background: #f5f5f5;
        border-radius: 3px 3px 0 0;
    }

    .widget-title {
        margin: 0;
        color: #333;
        font-size: 13px;
        font-weight: bold;
    }

    .widget-body {
        padding: 0;
    }

    .btn {
        padding: 6px 12px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
        font-weight: bold;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background-color: #2196F3;
        color: white;
    }

    .btn-primary:hover {
        background-color: #1976D2;
    }

    .btn-warning {
        background-color: #FF9800;
        color: white;
    }

    .btn-warning:hover {
        background-color: #F57C00;
    }

    .label {
        display: inline-block;
        padding: 3px 8px;
        font-size: 10px;
        font-weight: bold;
        border-radius: 3px;
    }

    .text-center {
        text-align: center;
    }
</style>

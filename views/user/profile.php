<?php

use yii\helpers\Html;

?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li class="active">User Profile & Settings</li>
            </ul>
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-md-3">
                    <!-- Profile Card -->
                    <div class="widget-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                        <div class="widget-body">
                            <div class="text-center" style="padding: 20px;">
                                <div style="width: 120px; height: 120px; background: white; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <?php if (!empty($user['profile_picture'])): ?>
                                        <img src="<?= Html::encode($user['profile_picture']) ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="Profile">
                                    <?php else: ?>
                                        <i class="ace-icon fa fa-user fa-4x" style="color: #667eea;"></i>
                                    <?php endif; ?>
                                </div>
                                <h3 style="margin: 10px 0; font-weight: bold;">
                                    <?= Html::encode(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'User' ?>
                                </h3>
                                <p style="margin: 5px 0; opacity: 0.9;">
                                    <i class="fa fa-envelope"></i> <?= Html::encode($user['email'] ?? 'No email') ?>
                                </p>
                                <p style="margin: 5px 0; opacity: 0.9;">
                                    <i class="fa fa-phone"></i> <?= Html::encode($user['phone'] ?? 'No phone') ?>
                                </p>
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.2);">
                                    <span class="label label-info" style="background-color: rgba(255,255,255,0.3);">
                                        <?= $userArray['role_id'] ?? 'User' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Card -->
                    <div class="widget-box" style="margin-top: 15px;">
                        <div class="widget-header">
                            <h4 class="widget-title">
                                <i class="fa fa-info-circle"></i> Account Status
                            </h4>
                        </div>
                        <div class="widget-body">
                            <div style="padding: 15px;">
                                <div class="status-item" style="margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                                    <span><i class="fa fa-check-circle" style="color: #4CAF50; margin-right: 8px;"></i>Account Active</span>
                                    <span class="label label-success"><?= ($user['is_active'] ?? 0) ? 'Yes' : 'No' ?></span>
                                </div>
                                <div class="status-item" style="margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                                    <span><i class="fa fa-toggle-on" style="color: #2196F3; margin-right: 8px;"></i>Status</span>
                                    <span class="label" style="background-color: <?= ($user['status'] ?? 0) ? '#4CAF50' : '#FF9800' ?>; color: white;">
                                        <?= ($user['status'] ?? 0) ? 'Enabled' : 'Disabled' ?>
                                    </span>
                                </div>
                                <div class="status-item" style="display: flex; justify-content: space-between; align-items: center;">
                                    <span><i class="fa fa-calendar" style="color: #FF9800; margin-right: 8px;"></i>Joined</span>
                                    <span style="font-size: 12px; color: #666;">
                                        <?= date('M d, Y', strtotime($user['created_at'] ?? 'now')) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <!-- Personal Information -->
                    <div class="widget-box">
                        <div class="widget-header">
                            <h4 class="widget-title">
                                <i class="fa fa-user"></i> Personal Information
                            </h4>
                        </div>
                        <div class="widget-body">
                            <div style="padding: 20px;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">First Name</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <?= Html::encode($user['first_name'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Last Name</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <?= Html::encode($user['last_name'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Username</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-user-circle" style="margin-right: 8px; color: #2196F3;"></i>
                                                <?= Html::encode($user['username'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Email Address</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-envelope" style="margin-right: 8px; color: #2196F3;"></i>
                                                <?= Html::encode($user['email'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Phone Number</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-phone" style="margin-right: 8px; color: #4CAF50;"></i>
                                                <?= Html::encode($user['phone'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">WhatsApp</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-whatsapp" style="margin-right: 8px; color: #25D366;"></i>
                                                <?= Html::encode($user['whatsapp'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Date of Birth</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-birthday-cake" style="margin-right: 8px; color: #FF6B6B;"></i>
                                                <?= $user['date_of_birth'] ? date('M d, Y', strtotime($user['date_of_birth'])) : '-' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Gender</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <?php
                                                $genderIcon = match($user['gender'] ?? '') {
                                                    'Male' => '<i class="fa fa-mars" style="margin-right: 8px; color: #1E90FF;"></i>',
                                                    'Female' => '<i class="fa fa-venus" style="margin-right: 8px; color: #FF1493;"></i>',
                                                    default => '<i class="fa fa-question-circle" style="margin-right: 8px; color: #999;"></i>'
                                                };
                                                echo $genderIcon . Html::encode($user['gender'] ?? '-');
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address & Location -->
                    <div class="widget-box" style="margin-top: 15px;">
                        <div class="widget-header">
                            <h4 class="widget-title">
                                <i class="fa fa-map-marker"></i> Address & Location
                            </h4>
                        </div>
                        <div class="widget-body">
                            <div style="padding: 20px;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Address</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-home" style="margin-right: 8px; color: #2196F3;"></i>
                                                <?= Html::encode($user['address'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">City</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-building" style="margin-right: 8px; color: #FF9800;"></i>
                                                <?= Html::encode($user['city'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Country</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-globe" style="margin-right: 8px; color: #4CAF50;"></i>
                                                <?= Html::encode($user['country'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Reference</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-link" style="margin-right: 8px; color: #9C27B0;"></i>
                                                <?= Html::encode($user['referance'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="widget-box" style="margin-top: 15px;">
                        <div class="widget-header">
                            <h4 class="widget-title">
                                <i class="fa fa-share-alt"></i> Social Media
                            </h4>
                        </div>
                        <div class="widget-body">
                            <div style="padding: 20px;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Facebook</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-facebook" style="margin-right: 8px; color: #1877F2;"></i>
                                                <?= Html::encode($user['facebook'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Instagram</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-instagram" style="margin-right: 8px; color: #E4405F;"></i>
                                                <?= Html::encode($user['instagram'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Pinterest</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-pinterest" style="margin-right: 8px; color: #E60B3F;"></i>
                                                <?= Html::encode($user['pinterest'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">About</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px; max-height: 80px; overflow-y: auto;">
                                                <i class="fa fa-info-circle" style="margin-right: 8px; color: #2196F3;"></i>
                                                <?= Html::encode($user['about'] ?? '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Login Information -->
                    <div class="widget-box" style="margin-top: 15px;">
                        <div class="widget-header">
                            <h4 class="widget-title">
                                <i class="fa fa-lock"></i> Login Information
                            </h4>
                        </div>
                        <div class="widget-body">
                            <div style="padding: 20px;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Last Login</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-clock-o" style="margin-right: 8px; color: #FF9800;"></i>
                                                <?= $user['last_login'] ? date('M d, Y h:i A', strtotime($user['last_login'])) : 'Never Logged In' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Failed Login Attempts</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-exclamation-triangle" style="margin-right: 8px; color: <?= ($user['failed_login_attempts'] ?? 0) > 0 ? '#f44336' : '#4CAF50' ?>;"></i>
                                                <span style="color: <?= ($user['failed_login_attempts'] ?? 0) > 0 ? '#f44336' : '#4CAF50' ?>;">
                                                    <?= ($user['failed_login_attempts'] ?? 0) ?> attempt<?= ($user['failed_login_attempts'] ?? 0) != 1 ? 's' : '' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Account Created</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-calendar" style="margin-right: 8px; color: #2196F3;"></i>
                                                <?= date('M d, Y h:i A', strtotime($user['created_at'] ?? 'now')) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Last Updated</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <i class="fa fa-refresh" style="margin-right: 8px; color: #9C27B0;"></i>
                                                <?= $user['updated_at'] ? date('M d, Y h:i A', strtotime($user['updated_at'])) : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-item" style="margin-bottom: 15px;">
                                            <label style="color: #666; font-weight: bold; font-size: 12px;">Account Status</label>
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                                <?php if ($user['is_active'] ?? 0): ?>
                                                    <span class="label label-success"><i class="fa fa-check-circle"></i> Active</span>
                                                <?php else: ?>
                                                    <span class="label label-danger"><i class="fa fa-times-circle"></i> Inactive</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Security -->
                    <div class="widget-box" style="margin-top: 15px; margin-bottom: 20px;">
                        <div class="widget-header">
                            <h4 class="widget-title">
                                <i class="fa fa-shield"></i> Account Security
                            </h4>
                        </div>
                        <div class="widget-body">
                            <div style="padding: 20px;">
                                <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
                                    <i class="fa fa-info-circle" style="color: #ffc107;"></i>
                                    <strong style="color: #856404;">Security Tip:</strong>
                                    <p style="color: #856404; margin-top: 5px; margin-bottom: 0;">
                                        Keep your password secure. Never share your login credentials with anyone. We recommend changing your password regularly.
                                    </p>
                                </div>

                                <button class="btn btn-primary" onclick="showChangePasswordModal()" style="margin-right: 10px;">
                                    <i class="fa fa-key"></i> Change Password
                                </button>
                                <button class="btn btn-warning" onclick="logoutConfirm()">
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showChangePasswordModal() {
        Swal.fire({
            title: 'Change Password',
            html: `
                <form id="changePasswordForm" style="text-align: left;">
                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Current Password</label>
                        <input type="password" id="currentPassword" class="form-control" placeholder="Enter current password" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">New Password</label>
                        <input type="password" id="newPassword" class="form-control" placeholder="Enter new password" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Confirm Password</label>
                        <input type="password" id="confirmPassword" class="form-control" placeholder="Confirm new password" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </form>
            `,
            width: '500px',
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
            title: 'Logout Confirmation',
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
    .info-item {
        background: white;
    }

    .status-item {
        padding: 8px 0;
        font-size: 14px;
    }

    .widget-box {
        border: 1px solid #e3e9f3;
        border-radius: 4px;
        background: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .widget-header {
        background: #f8f9fa;
        padding: 12px 15px;
        border-bottom: 1px solid #e3e9f3;
        border-radius: 4px 4px 0 0;
    }

    .widget-title {
        margin: 0;
        color: #333;
        font-size: 14px;
        font-weight: bold;
    }

    .widget-body {
        padding: 0;
    }

    .label {
        display: inline-block;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: bold;
        border-radius: 3px;
        margin-right: 5px;
    }

    .label-success {
        background-color: #4CAF50;
        color: white;
    }

    .label-info {
        background-color: #2196F3;
        color: white;
    }

    .label-primary {
        background-color: #007bff;
        color: white;
    }

    .label-danger {
        background-color: #f44336;
        color: white;
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: bold;
        transition: all 0.3s ease;
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
</style>

<?php

use yii\helpers\Html;
if(!isset($user))
{
    $user = [];
}
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
                    <div class="widget-box">
                        <div class="widget-body" style="padding: 12px;">
                            <div class="text-center">
                                <div id="profilePictureContainer" style="width: 80px; height: 80px; background: white; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 2px solid #e3e9f3; position: relative; cursor: pointer;">
                                    <?php if (!empty($user['profile_picture'])): ?>
                                        <img id="profileImagePreview" src="<?= Html::encode($user['profile_picture']) ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="Profile">
                                    <?php else: ?>
                                        <i id="profileImageIcon" class="ace-icon fa fa-user fa-3x" style="color: #667eea;"></i>
                                    <?php endif; ?>
                                    <input type="file" id="profilePictureInput" accept="image/*" style="display: none;">
                                    <div style="position: absolute; width: 100%; height: 100%; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0) url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2220%22 height=%2220%22 viewBox=%220 0 24 24%22 fill=%22white%22><path d=%22M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z%22/></svg>') center no-repeat; background-size: 20px 20px; cursor: pointer; opacity: 0; transition: opacity 0.3s;" id="profilePictureOverlay" onmouseover="this.style.opacity='1'; this.style.backgroundColor='rgba(0,0,0,0.5)';" onmouseout="this.style.opacity='0'; this.style.backgroundColor='rgba(0,0,0,0)';"></div>
                                </div>
                                <h4 style="margin: 6px 0; font-weight: bold; font-size: 14px;">
                                    <?= Html::encode(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'User') ?>
                                    <?php if (($user['is_active'] ?? 0) == 1): ?>
                                        <i class="fa fa-check-circle" style="color:#4CAF50; font-size: 12px;"></i>
                                    <?php else: ?>
                                        <i class="fa fa-times-circle" style="color:#FF9800; font-size: 12px;"></i>
                                    <?php endif; ?>
                                </h4>
                                <p style="margin: 3px 0; font-size: 11px; opacity: 0.85;">
                                    <i class="fa fa-user-circle"></i> <?= Html::encode($user['username'] ?? '-') ?>
                                </p>
                                <p style="margin: 3px 0; font-size: 10px; opacity: 0.8;">
                                    <i class="fa fa-envelope"></i> <?= Html::encode($user['email'] ?? '-') ?>
                                </p>
                            </div>
                            <hr>
                            <hr style="margin: 8px 0; opacity: 0.2;">
                            <div style="text-align: center; font-size: 11px;">
                                <div style="margin: 4px 0; opacity: 0.9;">
                                    <small>Member since</small><br>
                                    <strong style="font-size: 11px;"><?= date('M Y', strtotime($user['created_at'] ?? 'now')) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile Card -->
                <div class="col-md-10" style="padding: 4px;">
                    <div class="widget-box">
                        
                        <div class="widget-body" style="padding: 12px;">
                            <form id="editProfileForm" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                                <input type="hidden" id="userId" value="<?= $user['id'] ?>">

                                <div style="grid-column: span 1;">
                                    <label style="color: #666; font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">Username *</label>
                                    <input type="text" id="username" class="form-control" value="<?= Html::encode($user['username'] ?? '') ?>" style="padding: 6px; font-size: 12px;">
                                    <small id="usernameError" style="color: #f44336; display: none;"></small>
                                </div>

                                <div style="grid-column: span 1;">
                                    <label style="color: #666; font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">Email</label>
                                    <input type="email" id="email" class="form-control" value="<?= Html::encode($user['email'] ?? '') ?>" style="padding: 6px; font-size: 12px;">
                                </div>

                                <div style="grid-column: span 1;">
                                    <label style="color: #666; font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">First Name</label>
                                    <input type="text" id="firstName" class="form-control" value="<?= Html::encode($user['first_name'] ?? '') ?>" style="padding: 6px; font-size: 12px;">
                                </div>

                                <div style="grid-column: span 1;">
                                    <label style="color: #666; font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">Last Name</label>
                                    <input type="text" id="lastName" class="form-control" value="<?= Html::encode($user['last_name'] ?? '') ?>" style="padding: 6px; font-size: 12px;">
                                </div>

                                <div style="grid-column: span 1;">
                                    <label style="color: #666; font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">Phone</label>
                                    <input type="text" id="phone" class="form-control" value="<?= Html::encode($user['phone'] ?? '') ?>" style="padding: 6px; font-size: 12px;">
                                </div>

                                <div style="grid-column: span 1;">
                                    <label style="color: #666; font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">WhatsApp</label>
                                    <input type="text" id="whatsapp" class="form-control" value="<?= Html::encode($user['whatsapp'] ?? '') ?>" style="padding: 6px; font-size: 12px;">
                                </div>

                                <div style="grid-column: span 1;">
                                    <label style="color: #666; font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">Date of Birth</label>
                                    <input type="date" id="dateOfBirth" class="form-control" value="<?= $user['date_of_birth'] ? date('Y-m-d', strtotime($user['date_of_birth'])) : '' ?>" style="padding: 6px; font-size: 12px;">
                                </div>

                                <div style="grid-column: span 1;">
                                    <label style="color: #666; font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">Gender</label>
                                    <select id="gender" class="form-control" style="padding: 6px; font-size: 12px;">
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?= ($user['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= ($user['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                        <option value="Other" <?= ($user['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>

                                <div style="grid-column: span 2;">
                                    <label style="color: #666; font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">Address</label>
                                    <input type="text" id="address" class="form-control" value="<?= Html::encode($user['address'] ?? '') ?>" style="padding: 6px; font-size: 12px;">
                                </div>

                                <div style="grid-column: span 1;">
                                    <label style="color: #666; font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">City</label>
                                    <input type="text" id="city" class="form-control" value="<?= Html::encode($user['city'] ?? '') ?>" style="padding: 6px; font-size: 12px;">
                                </div>

                                <div style="grid-column: span 1;">
                                    <label style="color: #666; font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">Country</label>
                                    <input type="text" id="country" class="form-control" value="<?= Html::encode($user['country'] ?? '') ?>" style="padding: 6px; font-size: 12px;">
                                </div>

                                <div style="grid-column: span 4; margin-top: 8px; display: flex; gap: 8px;">
                                    <button type="button" class="btn btn-success" onclick="saveProfile()" style="flex: 1; padding: 7px; font-size: 12px;">
                                        <i class="fa fa-save"></i> Save Changes
                                    </button>
                                    <button type="button" class="btn btn-warning" onclick="changePassword()" style="flex: 1; padding: 7px; font-size: 12px;">
                                        <i class="fa fa-key"></i> Change Password
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="logout()" style="flex: 1; padding: 7px; font-size: 12px;">
                                        <i class="fa fa-sign-out"></i> Logout
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 3: Account & Security (Full Width) -->
            <div class="row" style="margin: 0;">
                <div class="col-md-12" style="padding: 4px;">
                    <div class="widget-box" style="margin: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        
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
                                        <label style="color: #999; font-size: 10px; font-weight: bold;">Status</label>
                                        <div style="background: #f9f9f9; padding: 6px; border-radius: 3px; border-left: 2px solid #2196F3;">
                                            <span class="label" style="background-color: <?= ($user['status'] ?? 0) ? '#4CAF50' : '#FF9800' ?>; color: white; padding: 3px 8px; font-size: 10px; display: inline-block;">
                                                <?= ($user['status'] ?? 0) ? '✓ Enabled' : '○ Disabled' ?>
                                            </span>
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
    function saveProfile() {
        const username = document.getElementById('username').value.trim();

        if (!username) {
            Swal.fire('Error', 'Username is required', 'error');
            return;
        }

        // Check if username changed, verify it doesn't exist
        if (username !== '<?= Html::encode($user['username']) ?>') {
            checkUsernameAvailability(username, function(available) {
                if (!available) {
                    document.getElementById('usernameError').textContent = 'Username already exists';
                    document.getElementById('usernameError').style.display = 'block';
                    return;
                }
                submitProfileUpdate();
            });
        } else {
            submitProfileUpdate();
        }
    }

    function checkUsernameAvailability(username, callback) {
        const formData = new FormData();
        formData.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        formData.append('flag', 'checkUsername');
        formData.append('username', username);
        formData.append('userId', document.getElementById('userId').value);

        fetch('index.php?r=user/profile', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            callback(data.available);
        })
        .catch(e => {
            console.error(e);
            callback(false);
        });
    }

    function submitProfileUpdate() {
        const formData = new FormData();
        formData.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        formData.append('flag', 'updateProfile');
        formData.append('userId', document.getElementById('userId').value);
        formData.append('username', document.getElementById('username').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('first_name', document.getElementById('firstName').value);
        formData.append('last_name', document.getElementById('lastName').value);
        formData.append('phone', document.getElementById('phone').value);
        formData.append('whatsapp', document.getElementById('whatsapp').value);
        formData.append('date_of_birth', document.getElementById('dateOfBirth').value);
        formData.append('gender', document.getElementById('gender').value);
        formData.append('address', document.getElementById('address').value);
        formData.append('city', document.getElementById('city').value);
        formData.append('country', document.getElementById('country').value);

        Swal.fire({
            title: 'Updating Profile...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('index.php?r=user/profile', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                Swal.fire('Success', data.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(e => {
            Swal.close();
            Swal.fire('Error', 'Failed to update profile', 'error');
            console.error(e);
        });
    }

    function changePassword() {
        Swal.fire({
            title: 'Change Password',
            html: `
                <form style="text-align: left;">
                    <div style="margin-bottom: 12px;">
                        <label style="font-weight: bold; display: block; margin-bottom: 4px; font-size: 12px;">Current Password *</label>
                        <input type="password" id="currentPassword" class="form-control" placeholder="Enter current password" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 3px; font-size: 12px;">
                        <small id="currentPasswordError" style="color: #f44336; display: none;"></small>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="font-weight: bold; display: block; margin-bottom: 4px; font-size: 12px;">New Password *</label>
                        <input type="password" id="newPassword" class="form-control" placeholder="Enter new password (min 6 characters)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 3px; font-size: 12px;">
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="font-weight: bold; display: block; margin-bottom: 4px; font-size: 12px;">Confirm Password *</label>
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
                updatePassword(result.value.current, result.value.newPass);
            }
        });
    }

    function updatePassword(currentPassword, newPassword) {
        const formData = new FormData();
        formData.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        formData.append('flag', 'changePassword');
        formData.append('userId', document.getElementById('userId').value);
        formData.append('current_password', currentPassword);
        formData.append('new_password', newPassword);

        Swal.fire({
            title: 'Updating Password...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('index.php?r=user/profile', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                Swal.fire('Success', data.message, 'success');
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(e => {
            Swal.close();
            Swal.fire('Error', 'Failed to change password', 'error');
            console.error(e);
        });
    }

    function logout() {
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

    // Make container and overlay clickable
    const profileContainer = document.getElementById('profilePictureContainer');
    const profileOverlay = document.getElementById('profilePictureOverlay');

    profileContainer.addEventListener('click', function() {
        document.getElementById('profilePictureInput').click();
    });

    profileOverlay.addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('profilePictureInput').click();
    });

    // Profile picture upload handler
    document.getElementById('profilePictureInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            Swal.fire('Error', 'Please select an image file', 'error');
            return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire('Error', 'Image size should not exceed 5MB', 'error');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(event) {
            const preview = event.target.result;
            const profileImage = document.getElementById('profileImagePreview');
            const profileIcon = document.getElementById('profileImageIcon');

            if (profileIcon) {
                profileIcon.remove();
            }

            if (profileImage) {
                profileImage.src = preview;
            } else {
                const img = document.createElement('img');
                img.id = 'profileImagePreview';
                img.src = preview;
                img.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
                document.getElementById('profilePictureContainer').appendChild(img);
            }
        };
        reader.readAsDataURL(file);

        // Upload file
        uploadProfilePicture(file);
    });

    function uploadProfilePicture(file) {
        const formData = new FormData();
        formData.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        formData.append('flag', 'uploadProfilePicture');
        formData.append('userId', document.getElementById('userId').value);
        formData.append('profile_picture', file);

        Swal.fire({
            title: 'Uploading Profile Picture...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('index.php?r=user/profile', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                Swal.fire('Success', data.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message, 'error');
                location.reload();
            }
        })
        .catch(e => {
            Swal.close();
            Swal.fire('Error', 'Failed to upload profile picture', 'error');
            console.error(e);
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
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
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

    .btn-success {
        background-color: #4CAF50;
        color: white;
    }

    .btn-success:hover {
        background-color: #45a049;
    }

    .btn-warning {
        background-color: #FF9800;
        color: white;
    }

    .btn-warning:hover {
        background-color: #F57C00;
    }

    .btn-danger {
        background-color: #f44336;
        color: white;
    }

    .btn-danger:hover {
        background-color: #da190b;
    }

    .form-control {
        border: 1px solid #ddd;
        border-radius: 3px;
        background: white;
    }

    .form-control:focus {
        outline: none;
        border-color: #2196F3;
        box-shadow: 0 0 5px rgba(33, 150, 243, 0.3);
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

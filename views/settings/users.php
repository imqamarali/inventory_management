<?php

if (!isset($users)) {
    $users = [];
}
if (!isset($roles)) {
    $roles = [];
}

?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=settings/settings">Home</a>
                </li>
                <li class="active">Users</li>
                <div class="nav-search" id="nav-search">
                    <div class="exam-quick-actions-group">
                        <a class="btn btn-sm btn-white btn-primary"
                            style="font-size: 12px; cursor:pointer;"
                            onclick="openUserModal()">
                            <i class="ace-icon fa fa-plus"></i>
                            Add New User
                        </a>
                    </div>
                </div>
            </ul>
        </div>

        <div class="page-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="widget-body">
                            <div class="widget-main padding-12">
                                <?php if (count($users) == 0) { ?>
                                    <div class="alert alert-info text-center">
                                        <i class="ace-icon fa fa-info-circle fa-3x" style="color: #6FB3E0;"></i>
                                        <h4 style="margin-top: 15px;">No Users Found</h4>
                                        <p>Start by adding your first user using the button above</p>
                                    </div>
                                <?php } else { ?>
                                    <div class="row" id="users_container">
                                        <?php foreach ($users as $key => $item):
                                            $isActive = $item['is_active'] ?? 1;
                                            $statusClass = $isActive ? 'label-success' : 'label-danger';
                                        ?>
                                            <div class="col-md-4 col-sm-6 user-item">
                                                <div class="class-card">
                                                    <div class="class-header">
                                                        <div style="flex: 1;">
                                                            <div class="class-name">
                                                                <i class="fa fa-user" style="margin-right: 8px;"></i>
                                                                <?php echo htmlspecialchars($item['username']); ?>
                                                            </div>
                                                        </div>

                                                        <div class="btn-group">
                                                            <button type="button"
                                                                onclick="openUserModal(<?php echo htmlspecialchars(json_encode($item)); ?>)"
                                                                title="Edit User">
                                                                <i class="ace-icon fa fa-pencil"></i>
                                                            </button>
                                                            &nbsp;&nbsp;|&nbsp;&nbsp;
                                                            <button type="button"
                                                                onclick="openResetPasswordModal(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['username']); ?>')"
                                                                title="Reset Password">
                                                                <i class="ace-icon fa fa-key"></i>
                                                            </button>
                                                            &nbsp;&nbsp;|&nbsp;&nbsp;
                                                            <button type="button"
                                                                onclick="deleteUser(<?php echo $item['id']; ?>)"
                                                                title="Delete User">
                                                                <i class="ace-icon fa fa-trash"></i>
                                                            </button>
                                                        </div>

                                                    </div>

                                                    <div class="class-stats">

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-envelope"></i>
                                                            <span>Email: <?php echo htmlspecialchars($item['email']); ?></span>
                                                        </div>

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-id-card"></i>
                                                            <span>Name: <?php echo htmlspecialchars(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? '')); ?></span>
                                                        </div>

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-shield"></i>
                                                            <span>Role: <?php echo htmlspecialchars($item['role_name'] ?? 'N/A'); ?></span>
                                                        </div>

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-phone"></i>
                                                            <span>Phone: <?php echo htmlspecialchars($item['phone'] ?? 'N/A'); ?></span>
                                                        </div>

                                                    </div>

                                                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #E3E9ED;">

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-calendar"></i>
                                                            <span>Created: <?php echo date('M d, Y', strtotime($item['created_at'] ?? now())); ?></span>
                                                        </div>

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-sign-in"></i>
                                                            <span>Last Login: <?php echo $item['last_login'] ? date('M d, Y H:i', strtotime($item['last_login'])) : 'Never'; ?></span>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>

                                        <?php endforeach; ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<script>
    // Make roles data globally available
    window.rolesData = <?= json_encode($roles) ?>;

    // Define functions in global scope
    window.openResetPasswordModal = function(userId, username) {
        if (typeof Swal === 'undefined') {
            alert('Please enter new password for ' + username);
            var newPass = prompt('Enter new password:');
            if (newPass) {
                window.resetUserPassword(userId, newPass);
            }
            return;
        }

        Swal.fire({
            title: 'Reset Password',
            html: `
                <div style="text-align: left;">
                    <p><strong>User: ${username}</strong></p>
                    <div class="form-group" style="margin-top: 15px;">
                        <label>New Password:</label>
                        <input type="password" id="newPassword" class="form-control" placeholder="Enter new password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password:</label>
                        <input type="password" id="confirmPassword" class="form-control" placeholder="Confirm password" required>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Reset Password',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
            preConfirm: function() {
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;

                if (!newPassword) {
                    Swal.showValidationError('Password is required');
                    return false;
                }

                if (newPassword.length < 6) {
                    Swal.showValidationError('Password must be at least 6 characters');
                    return false;
                }

                if (newPassword !== confirmPassword) {
                    Swal.showValidationError('Passwords do not match');
                    return false;
                }

                return newPassword;
            }
        }).then(result => {
            if (result.isConfirmed) {
                window.resetUserPassword(userId, result.value);
            }
        });
    };

    window.resetUserPassword = function(userId, newPassword) {
        const formData = new FormData();
        formData.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->getCsrfToken() ?>');
        formData.append('flag', 'resetpassword');
        formData.append('id', userId);
        formData.append('new_password', newPassword);

        fetch('index.php?r=settings/users', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000
                    });
                } else {
                    alert('Password reset successfully');
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error!', data.message, 'error');
                } else {
                    alert('Error: ' + data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error!', 'An error occurred while resetting password', 'error');
            } else {
                alert('Error occurred');
            }
        });
    };
</script>


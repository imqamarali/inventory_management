<?php
use yii\helpers\Html;

if (!isset($users)) {
    $users = [];
}
if (!isset($roles)) {
    $roles = [];
}
if (!isset($keyword)) {
    $keyword = '';
}
?>
<div class="row" style="margin-top: 20px;">
    <div class="col-sm-12">
        <div class="btn-group">
            <button class="btn btn-sm btn-primary" onclick="openUserModal()">
                <i class="ace-icon fa fa-plus"></i> Add New User
            </button>
            <div class="input-group" style="display: inline-block; margin-left: 10px; width: 250px;">
                <input type="text" class="form-control input-sm" id="userKeyword" placeholder="Search users..." value="<?= htmlspecialchars($keyword) ?>">
                <span class="input-group-btn">
                    <button class="btn btn-sm btn-info" id="userSearchBtn">
                        <i class="ace-icon fa fa-search"></i>
                    </button>
                </span>
            </div>
        </div>

        <div id="usersContainer" style="margin-top: 15px;">
            <?php if (empty($users)): ?>
            <div class="alert alert-info">
                <i class="ace-icon fa fa-info-circle"></i>
                No users found. Click "Add New User" to create one.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-condensed">
                    <thead>
                        <tr>
                            <th width="18%">Username</th>
                            <th width="18%">Email</th>
                            <th width="18%">Full Name</th>
                            <th width="15%">Role</th>
                            <th width="15%">Last Login</th>
                            <th width="16%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <?php foreach ($users as $user): ?>
                        <tr data-user-id="<?= $user['id'] ?>">
                            <td><strong><?= htmlspecialchars($user['username']) ?></strong></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></td>
                            <td><span class="label label-info"><?= htmlspecialchars($user['role_name'] ?? 'N/A') ?></span></td>
                            <td><small class="text-muted"><?= $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never' ?></small></td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-info" onclick="editUser(<?= htmlspecialchars(json_encode($user)) ?>)" title="Edit">
                                    <i class="ace-icon fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-xs btn-warning" onclick="resetUserPassword(<?= $user['id'] ?>)" title="Reset Password">
                                    <i class="ace-icon fa fa-key"></i>
                                </button>
                                <button class="btn btn-xs btn-danger" onclick="deleteUser(<?= $user['id'] ?>)" title="Delete">
                                    <i class="ace-icon fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const rolesData = <?= json_encode($roles) ?>;

document.getElementById('userSearchBtn').addEventListener('click', function() {
    searchUsers();
});

document.getElementById('userKeyword').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchUsers();
    }
});

function searchUsers() {
    const keyword = document.getElementById('userKeyword').value.trim();
    const data = new FormData();
    data.append('flag', 'search');
    data.append('keyword', keyword);
    data.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->getCsrfToken() ?>');

    fetch('index.php?r=settings/users', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            updateUsersTable(result.users);
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'An error occurred while searching.', 'error');
    });
}

function openUserModal(userData = null) {
    const isEdit = userData !== null;
    const title = isEdit ? 'Edit User' : 'Add New User';
    const userId = isEdit ? (userData.id || '') : '';
    const username = isEdit ? (userData.username || '') : '';
    const email = isEdit ? (userData.email || '') : '';
    const firstName = isEdit ? (userData.first_name || '') : '';
    const lastName = isEdit ? (userData.last_name || '') : '';
    const phone = isEdit ? (userData.phone || '') : '';
    const roleId = isEdit ? (userData.role_id || '') : '';

    const roleOptions = rolesData.map(role =>
        `<option value="${role.id}" ${roleId == role.id ? 'selected' : ''}>${escapeHtml(role.name)}</option>`
    ).join('');

    Swal.fire({
        title: title,
        html: `
            <form id="userForm" style="text-align:left;">
            <input type="hidden" id="swal_user_id" value="${userId}">

            <div class="row">
            <div class="col-md-6">
            <label>Username <span class="text-danger">*</span></label>
            <input type="text" id="swal_username" class="form-control" value="${escapeHtml(username)}" placeholder="Enter username">
            </div>

            <div class="col-md-6">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" id="swal_email" class="form-control" value="${escapeHtml(email)}" placeholder="Enter email">
            </div>
            </div>

            <div class="row">
            <div class="col-md-6">
            <label>First Name</label>
            <input type="text" id="swal_first_name" class="form-control" value="${escapeHtml(firstName)}" placeholder="First name">
            </div>

            <div class="col-md-6">
            <label>Last Name</label>
            <input type="text" id="swal_last_name" class="form-control" value="${escapeHtml(lastName)}" placeholder="Last name">
            </div>
            </div>

            <div class="row">
            <div class="col-md-6">
            <label>Phone</label>
            <input type="tel" id="swal_phone" class="form-control" value="${escapeHtml(phone)}" placeholder="Phone number">
            </div>

            <div class="col-md-6">
            <label>Role</label>
            <select id="swal_role_id" class="form-control">
                <option value="">-- Select Role --</option>
                ${roleOptions}
            </select>
            </div>
            </div>

            ${!isEdit ? `
            <div class="row">
            <div class="col-md-12">
            <label>Password <span class="text-danger">*</span></label>
            <input type="password" id="swal_password" class="form-control" placeholder="Enter password (min 8 chars)">
            </div>
            </div>
            ` : ''}

            </form>
        `,
        width: '600px',
        showCancelButton: true,
        confirmButtonText: isEdit ? '<i class="ace-icon fa fa-save"></i> Update User' : '<i class="ace-icon fa fa-save"></i> Create User',
        cancelButtonText: '<i class="ace-icon fa fa-times"></i> Cancel',
        confirmButtonColor: '#87B87F',
        cancelButtonColor: '#6c757d',
        focusConfirm: false,
        preConfirm: () => {
            const username = document.getElementById('swal_username').value.trim();
            const email = document.getElementById('swal_email').value.trim();

            if (!username) {
                Swal.showValidationMessage('Username is required');
                return false;
            }

            if (!email) {
                Swal.showValidationMessage('Email is required');
                return false;
            }

            if (!isEdit) {
                const password = document.getElementById('swal_password').value.trim();
                if (!password || password.length < 8) {
                    Swal.showValidationMessage('Password must be at least 8 characters');
                    return false;
                }
            }

            return {
                id: document.getElementById('swal_user_id').value,
                username: username,
                email: email,
                first_name: document.getElementById('swal_first_name').value.trim(),
                last_name: document.getElementById('swal_last_name').value.trim(),
                phone: document.getElementById('swal_phone').value.trim(),
                role_id: document.getElementById('swal_role_id').value,
                password: !isEdit ? document.getElementById('swal_password').value : undefined
            };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            saveUser(result.value);
        }
    });
}

function saveUser(formData) {
    Swal.fire({
        title: 'Processing...',
        text: 'Please wait',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const data = new FormData();
    data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
    data.append('id', formData.id);
    data.append('username', formData.username);
    data.append('email', formData.email);
    data.append('first_name', formData.first_name);
    data.append('last_name', formData.last_name);
    data.append('phone', formData.phone);
    data.append('role_id', formData.role_id);
    if (formData.password) {
        data.append('password', formData.password);
    }

    fetch('index.php?r=settings/users', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: result.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'An error occurred. Please try again.', 'error');
        console.error('Error:', error);
    });
}

function editUser(user) {
    openUserModal(user);
}

function resetUserPassword(userId) {
    Swal.fire({
        title: 'Reset Password',
        html: `
            <div style="text-align: left;">
            <label>New Password <span class="text-danger">*</span></label>
            <input type="password" id="newPassword" class="form-control" placeholder="Enter new password (min 8 chars)" style="margin-bottom: 10px;">
            <small class="text-muted">Password must be at least 8 characters long.</small>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="ace-icon fa fa-save"></i> Reset',
        cancelButtonText: '<i class="ace-icon fa fa-times"></i> Cancel',
        confirmButtonColor: '#f39c12',
        cancelButtonColor: '#6c757d',
        preConfirm: () => {
            const password = document.getElementById('newPassword').value.trim();
            if (!password || password.length < 8) {
                Swal.showValidationMessage('Password must be at least 8 characters');
                return false;
            }
            return password;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const data = new FormData();
            data.append('flag', 'resetpassword');
            data.append('id', userId);
            data.append('new_password', result.value);
            data.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->getCsrfToken() ?>');

            fetch('index.php?r=settings/users', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Success!', res.message, 'success');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'An error occurred.', 'error');
            });
        }
    });
}

function deleteUser(userId) {
    Swal.fire({
        title: 'Delete User?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="ace-icon fa fa-trash"></i> Yes, delete!',
        cancelButtonText: '<i class="ace-icon fa fa-times"></i> Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const data = new FormData();
            data.append('id', userId);
            data.append('delete', '1');
            data.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->getCsrfToken() ?>');

            fetch('index.php?r=settings/users', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Deleted!', res.message, 'success').then(() => {
                        document.querySelector(`[data-user-id="${userId}"]`).remove();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'An error occurred.', 'error');
            });
        }
    });
}

function updateUsersTable(users) {
    const tbody = document.getElementById('usersTableBody');
    const container = document.getElementById('usersContainer');

    if (!users || users.length === 0) {
        container.innerHTML = '<div class="alert alert-info"><i class="ace-icon fa fa-info-circle"></i> No users found.</div>';
        return;
    }

    tbody.innerHTML = '';
    users.forEach(user => {
        const row = `<tr data-user-id="${user.id}">
            <td><strong>${escapeHtml(user.username)}</strong></td>
            <td>${escapeHtml(user.email)}</td>
            <td>${escapeHtml((user.first_name || '') + ' ' + (user.last_name || ''))}</td>
            <td><span class="label label-info">${escapeHtml(user.role_name || 'N/A')}</span></td>
            <td><small class="text-muted">Never</small></td>
            <td class="text-center">
                <button class="btn btn-xs btn-info" onclick="editUser(${JSON.stringify(user).replace(/"/g, '&quot;')})" title="Edit">
                    <i class="ace-icon fa fa-pencil"></i>
                </button>
                <button class="btn btn-xs btn-warning" onclick="resetUserPassword(${user.id})" title="Reset Password">
                    <i class="ace-icon fa fa-key"></i>
                </button>
                <button class="btn btn-xs btn-danger" onclick="deleteUser(${user.id})" title="Delete">
                    <i class="ace-icon fa fa-trash"></i>
                </button>
            </td>
        </tr>`;
        tbody.innerHTML += row;
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

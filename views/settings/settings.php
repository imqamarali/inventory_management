<?php
use yii\helpers\Html;

if (!isset($modules)) {
    $modules = [];
}

// Dashboard color scheme
$navbarColor = '#0f4c29';
$accentColor = '#3498db';
?>
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li class="active">Settings</li>
                <li class="active">System Configurations</li>
            </ul>
        </div>
        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="widget-box" style="border-top: 2px solid; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        
                        <div class="widget-body">
                            <div class="widget-main padding-0">
                                <div class="row" style="margin: 0;">
                                    <!-- Left Menu -->
                                    <div class="col-sm-3" style="padding: 0; border-right: 1px solid #e8e8e8; min-height: 600px; width: 220px">
                                        <div class="settings-menu" style="border-radius: 0; margin: 0; padding: 0;">
                                            <ul class="nav nav-pills nav-stacked" id="settingsMenu" style="padding: 0; margin: 0;">
                                                <?php foreach ($modules as $index => $module):
                                                    $active = $index === 0 ? 'active' : '';
                                                ?>
                                                <li class="<?= $active ?>">
                                                    <a href="javascript:void(0)"
                                                       data-tab="<?= htmlspecialchars($module['controller']) ?>"
                                                       onclick="loadSettingsTab('<?= htmlspecialchars($module['controller']) ?>', this)"
                                                       style="border: none; border-left: 4px solid transparent; margin: 0; padding: 7px 6px; border-radius: 0; background: #fafafa;">
                                                        <i class="ace-icon <?= htmlspecialchars($module['icon']) ?>" style="width: 18px;"></i>
                                                        <span style="margin-left: 8px;"><?= htmlspecialchars($module['name']) ?></span>
                                                    </a>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Right Content -->
                                    <div class="col-sm-10">
                                        <div id="settingsContent" style="min-height: 500px;">
                                            <div class="text-center" style="padding: 60px 20px;">
                                                <i class="ace-icon fa fa-spinner fa-spin" style="font-size: 48px; color: <?= $navbarColor ?>;"></i>
                                                <p style="margin-top: 20px; color: #666; font-size: 14px;">Loading settings...</p>
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
</div>

<style>
.settings-menu {
    background: #fafafa;
    border-radius: 0;
    padding: 0;
    margin: 0;
}

.settings-menu-header {
    background-color: #0f4c29;
    color: white;
    padding: 15px;
    font-weight: 600;
}

.settings-menu .nav-pills > li > a {
    border-radius: 0;
    padding: 12px 15px;
    margin-bottom: 0;
    background: #fafafa;
    border: none;
    border-left: 4px solid transparent;
    color: #333;
    transition: all 0.3s ease;
    border-bottom: 1px solid #e8e8e8;
    font-weight: 500;
}

.settings-menu .nav-pills > li > a:hover {
    background: #f0f8ff;
    border-left-color: #0f4c29;
    color: #0f4c29;
}

.settings-menu .nav-pills > li.active > a {
    background: #e3f2fd;
    border-left-color: #0f4c29;
    color: #0f4c29;
    font-weight: 600;
}

.settings-menu .nav-pills > li > a i {
    margin-right: 8px;
    width: 18px;
    color: inherit;
}

.alert {
    border-radius: 4px;
    margin-bottom: 15px;
    border-left: 4px solid;
}

.alert-success {
    border-left-color: #2ecc71;
}

.alert-danger {
    border-left-color: #e74c3c;
}

.alert-warning {
    border-left-color: #f39c12;
}

.alert-info {
    border-left-color: #3498db;
}
</style>

<script>
function loadSettingsTab(controller, element) {
    // Update active menu item
    document.querySelectorAll('#settingsMenu li').forEach(li => {
        li.classList.remove('active');
    });
    element.closest('li').classList.add('active');

    // Load content
    const contentDiv = document.getElementById('settingsContent');
    contentDiv.innerHTML = '<div class="text-center" style="padding: 60px 20px;"><i class="ace-icon fa fa-spinner fa-spin" style="font-size: 48px; color: #0f4c29;"></i><p style="margin-top: 20px; color: #666;">Loading...</p></div>';

    fetch('index.php?r=' + controller, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(data => {
        // Extract scripts and HTML separately
        const temp = document.createElement('div');
        temp.innerHTML = data;

        // Get all script tags
        const scripts = temp.querySelectorAll('script');
        const scriptTexts = [];

        scripts.forEach(script => {
            scriptTexts.push(script.textContent);
            script.remove();
        });

        // Insert HTML without scripts
        contentDiv.innerHTML = temp.innerHTML;

        // Execute scripts in order
        scriptTexts.forEach(scriptText => {
            try {
                eval(scriptText);
            } catch (error) {
                console.error('Script execution error:', error);
            }
        });

        // Reinitialize Bootstrap tooltips
        if (typeof $ !== 'undefined' && $.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    })
    .catch(error => {
        contentDiv.innerHTML = '<div class="alert alert-danger" style="margin: 20px;"><i class="ace-icon fa fa-exclamation-triangle"></i> <strong>Error!</strong> Failed to load settings. Please try again.</div>';
        console.error('Error:', error);
    });
}

// Load first tab on page load
document.addEventListener('DOMContentLoaded', function() {
    const firstLink = document.querySelector('#settingsMenu li:first-child a');
    if (firstLink) {
        firstLink.click();
    }
});

// ===== USERS MANAGEMENT FUNCTIONS =====
function openUserModal(userData = null) {
    const isEdit = userData !== null;
    const title = isEdit ? 'Update User' : 'New User';
    const userId = isEdit ? (userData.id || '') : '';
    const username = isEdit ? (userData.username || '') : '';
    const email = isEdit ? (userData.email || '') : '';
    const firstName = isEdit ? (userData.first_name || '') : '';
    const lastName = isEdit ? (userData.last_name || '') : '';
    const phone = isEdit ? (userData.phone || '') : '';
    const roleId = isEdit ? (userData.role_id || '') : '';
    const address = isEdit ? (userData.address || '') : '';

    // Use roles data that's already loaded from the page
    const rolesData = window.rolesData || [];
    showUserModal(isEdit, title, userId, username, email, firstName, lastName, phone, roleId, address, rolesData);
}

function showUserModal(isEdit, title, userId, username, email, firstName, lastName, phone, roleId, address, rolesData) {
    let rolesHtml = '<option value="">Select a role</option>';
    rolesData.forEach(role => {
        const selected = roleId == role.id ? 'selected' : '';
        rolesHtml += `<option value="${role.id}" ${selected}>${role.name}</option>`;
    });

    Swal.fire({
        title: title,
        html:`
            <form id="userForm" style="text-align:left;">
            <input type="hidden" id="swal_user_id" value="${userId}">

            <div class="row">
            <div class="col-md-6">
            <label>Username <span class="text-danger">*</span></label>
            <input type="text" id="swal_username" class="form-control" value="${username}" ${isEdit ? 'readonly' : ''}>
            </div>

            <div class="col-md-6">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" id="swal_email" class="form-control" value="${email}">
            </div>
            </div>

            <div class="row">
            <div class="col-md-6">
            <label>First Name</label>
            <input type="text" id="swal_first_name" class="form-control" value="${firstName}">
            </div>

            <div class="col-md-6">
            <label>Last Name</label>
            <input type="text" id="swal_last_name" class="form-control" value="${lastName}">
            </div>
            </div>

            <div class="row">
            <div class="col-md-6">
            <label>Phone</label>
            <input type="text" id="swal_phone" class="form-control" value="${phone}">
            </div>

            <div class="col-md-6">
            <label>Role <span class="text-danger">*</span></label>
            <select id="swal_role_id" class="form-control">
                ${rolesHtml}
            </select>
            </div>
            </div>

            <div class="row">
            <div class="col-md-12">
            <label>Address</label>
            <input type="text" id="swal_address" class="form-control" value="${address}">
            </div>
            </div>

            ${!isEdit ? `
            <div class="row" style="margin-top: 10px;">
            <div class="col-md-12">
            <label>Password <span class="text-danger">*</span></label>
            <input type="password" id="swal_password" class="form-control" placeholder="Leave empty for auto-generated">
            </div>
            </div>
            ` : ''}

            </form>
        `,
        width: '700px',
        showCancelButton: true,
        confirmButtonText: isEdit ? '<i class="ace-icon fa fa-save"></i> Update User' : '<i class="ace-icon fa fa-save"></i> Create User',
        cancelButtonText: '<i class="ace-icon fa fa-times"></i> Cancel',
        confirmButtonColor: '#87B87F',
        cancelButtonColor: '#6c757d',
        focusConfirm: false,
        preConfirm: () => {
            const username = document.getElementById('swal_username').value.trim();
            const email = document.getElementById('swal_email').value.trim();
            const roleId = document.getElementById('swal_role_id').value;

            if (!username) {
                Swal.showValidationMessage('Username is required');
                return false;
            }

            if (!email) {
                Swal.showValidationMessage('Email is required');
                return false;
            }

            if (!roleId) {
                Swal.showValidationMessage('Role is required');
                return false;
            }

            return {
                id: document.getElementById('swal_user_id').value,
                username: username,
                email: email,
                first_name: document.getElementById('swal_first_name').value,
                last_name: document.getElementById('swal_last_name').value,
                phone: document.getElementById('swal_phone').value,
                role_id: roleId,
                address: document.getElementById('swal_address').value,
                password: document.getElementById('swal_password') ? document.getElementById('swal_password').value : ''
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
    data.append('address', formData.address);
    if (formData.password) {
        data.append('password', formData.password);
    }
    fetch('index.php?r=settings/users', {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred. Please try again.'
            });
        });
}

function deleteUser(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'User will be deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const data = new FormData();
            data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
            data.append('id', id);
            data.append('delete', '1');
            fetch('index.php?r=settings/users', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
        }
    });
}

// ===== ROLES MANAGEMENT FUNCTIONS =====
function openRoleModal(roleData = null) {
    const isEdit = roleData !== null;
    const title = isEdit ? 'Update Role' : 'New Role';
    const roleId = isEdit ? (roleData.id || '') : '';
    const roleName = isEdit ? (roleData.name || '') : '';

    // Fetch modules and permissions from server
    Promise.all([
        fetch('index.php?r=settings/modules').then(r => r.json()),
        isEdit ? fetch('index.php?r=settings/role-permissions&role_id=' + roleId).then(r => r.json()) : Promise.resolve({permissions: {}})
    ]).then(([modulesData, permissionsData]) => {
        const modules = modulesData.modules || [];
        const existingPermissions = permissionsData.permissions || {};
        showRoleModal(isEdit, title, roleId, roleName, modules, existingPermissions);
    }).catch(error => {
        console.error('Error loading role data:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to load role data'
        });
    });
}

function showRoleModal(isEdit, title, roleId, roleName, modules, existingPermissions) {
    let permissionsHtml = '<div style="border: 1px solid #ddd; padding: 12px; border-radius: 4px; max-height: 500px; overflow-y: auto; background: #f9f9f9;"><div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">';

    modules.forEach(module => {
        const isModuleAllChecked = existingPermissions[module.key + '.all'] ? 'checked' : '';

        permissionsHtml += `
            <div class="permission-group" style="border: 1px solid #e0e0e0; padding: 10px; border-radius: 3px; background: white;">
            
            <div style="margin-left: 15px;font-size: 12px;display: flex;gap: 20px;">
            <label style="font-weight: 600; margin: 0; cursor: pointer;">
            <input type="checkbox" class="permission-module-all" data-module="${module.key}" ${isModuleAllChecked} style="cursor: pointer;">
            <strong>${module.name}</strong>
            </label>
        `;

        module.permissions.forEach(perm => {
            const permLabel = perm.charAt(0).toUpperCase() + perm.slice(1);
            const isChecked = existingPermissions[module.key + '.' + perm] ? 'checked' : '';
            permissionsHtml += `<label style="display: block; margin-bottom: 5px; cursor: pointer;"><input type="checkbox" name="permissions" data-module="${module.key}" value="${module.key}.${perm}" ${isChecked} style="cursor: pointer;"> ${permLabel}</label>`;
        });

        permissionsHtml += '</div></div>';
    });

    permissionsHtml += '</div></div>';

    Swal.fire({
        title: title,
        width:'1400px',
        html: `
            <form id="roleForm" style="text-align:left;">
            <input type="hidden" id="swal_role_id" value="${roleId}">

            <div class="form-group">
            <label>Role Name <span class="text-danger">*</span></label>
            <input type="text" id="swal_role_name" class="form-control" value="${roleName}" placeholder="e.g., Administrator, Manager, Staff">
            </div>

            <div class="form-group">
            <label>Module Permissions (2 per row)</label>
            ${permissionsHtml}
            </div>

            </form>
        `,
        width: '900px',
        showCancelButton: true,
        confirmButtonText: isEdit ? '<i class="ace-icon fa fa-save"></i> Update Role' : '<i class="ace-icon fa fa-save"></i> Create Role',
        cancelButtonText: '<i class="ace-icon fa fa-times"></i> Cancel',
        confirmButtonColor: '#87B87F',
        cancelButtonColor: '#6c757d',
        focusConfirm: false,
        didOpen: () => {
            setTimeout(() => {
                // Handle module checkbox toggle (all permissions for that module)
                document.querySelectorAll('.permission-module-all').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const module = this.dataset.module;
                        const isChecked = this.checked;
                        // Toggle all permissions for this module
                        const modulePerms = document.querySelectorAll(`input[name="permissions"][data-module="${module}"]`);
                        modulePerms.forEach(perm => {
                            perm.checked = isChecked;
                        });
                    });
                });

                // Handle individual permission checkboxes
                document.querySelectorAll('input[name="permissions"]').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const module = this.dataset.module;
                        // Get all permissions for this module
                        const modulePerms = document.querySelectorAll(`input[name="permissions"][data-module="${module}"]`);
                        const allChecked = Array.from(modulePerms).every(p => p.checked);
                        const anyChecked = Array.from(modulePerms).some(p => p.checked);

                        // Update the module checkbox state
                        const moduleCheckbox = document.querySelector(`.permission-module-all[data-module="${module}"]`);
                        if (allChecked) {
                            moduleCheckbox.checked = true;
                            moduleCheckbox.indeterminate = false;
                        } else if (anyChecked) {
                            moduleCheckbox.indeterminate = true;
                        } else {
                            moduleCheckbox.checked = false;
                            moduleCheckbox.indeterminate = false;
                        }
                    });
                });

                // Initialize module checkbox states based on current permission selections
                document.querySelectorAll('.permission-module-all').forEach(moduleCheckbox => {
                    const module = moduleCheckbox.dataset.module;
                    const modulePerms = document.querySelectorAll(`input[name="permissions"][data-module="${module}"]`);
                    const allChecked = Array.from(modulePerms).every(p => p.checked);
                    const anyChecked = Array.from(modulePerms).some(p => p.checked);

                    if (allChecked) {
                        moduleCheckbox.checked = true;
                        moduleCheckbox.indeterminate = false;
                    } else if (anyChecked) {
                        moduleCheckbox.indeterminate = true;
                    } else {
                        moduleCheckbox.checked = false;
                        moduleCheckbox.indeterminate = false;
                    }
                });
            }, 100);
        },
        preConfirm: () => {
            const name = document.getElementById('swal_role_name').value.trim();

            if (!name) {
                Swal.showValidationMessage('Role name is required');
                return false;
            }

            const selectedPermissions = Array.from(document.querySelectorAll('input[name="permissions"]:checked')).map(cb => cb.value);

            return {
                id: document.getElementById('swal_role_id').value,
                name: name,
                permissions: selectedPermissions
            };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            saveRole(result.value);
        }
    });
}

function saveRole(formData) {
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
    data.append('name', formData.name);
    data.append('permissions', JSON.stringify(formData.permissions || []));

    fetch('index.php?r=settings/roles', {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred. Please try again.'
            });
        });
}

function deleteRole(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Role will be deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const data = new FormData();
            data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
            data.append('id', id);
            data.append('delete', '1');
            fetch('index.php?r=settings/roles', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
        }
    });
}
</script>
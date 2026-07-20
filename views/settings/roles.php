<?php
use yii\helpers\Html;

if (!isset($roles)) {
    $roles = [];
}
?>
<div class="row" style="margin-top: 20px;">
    <div class="col-sm-12">
        <div class="btn-group">
            <button class="btn btn-sm btn-primary" onclick="openRoleModal()">
                <i class="ace-icon fa fa-plus"></i> Add New Role
            </button>
        </div>

        <div id="rolesContainer" style="margin-top: 15px;">
            <?php if (empty($roles)): ?>
            <div class="alert alert-info">
                <i class="ace-icon fa fa-info-circle"></i>
                No roles defined. Click "Add New Role" to create one.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-condensed">
                    <thead>
                        <tr>
                            <th width="35%">Role Name</th>
                            <th width="20%">Users Assigned</th>
                            <th width="25%">Status</th>
                            <th width="20%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rolesTableBody">
                        <?php foreach ($roles as $role): ?>
                        <tr data-role-id="<?= $role['id'] ?>">
                            <td><strong><?= htmlspecialchars($role['name']) ?></strong></td>
                            <td>
                                <span class="badge badge-info"><?= $role['user_count'] ?? 0 ?></span>
                            </td>
                            <td>
                                <?php if (($role['user_count'] ?? 0) > 0): ?>
                                <span class="label label-warning"><i class="ace-icon fa fa-user"></i> In Use</span>
                                <?php else: ?>
                                <span class="label label-default"><i class="ace-icon fa fa-check"></i> Unused</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-info" onclick="editRole(<?= htmlspecialchars(json_encode($role)) ?>)" title="Edit">
                                    <i class="ace-icon fa fa-pencil"></i>
                                </button>
                                <?php if (($role['user_count'] ?? 0) === 0): ?>
                                <button class="btn btn-xs btn-danger" onclick="deleteRole(<?= $role['id'] ?>)" title="Delete">
                                    <i class="ace-icon fa fa-trash"></i>
                                </button>
                                <?php else: ?>
                                <button class="btn btn-xs btn-default" disabled title="Cannot delete: Users assigned">
                                    <i class="ace-icon fa fa-lock"></i>
                                </button>
                                <?php endif; ?>
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
function openRoleModal(roleData = null) {
    const isEdit = roleData !== null;
    const title = isEdit ? 'Edit Role' : 'Add New Role';
    const roleId = isEdit ? (roleData.id || '') : '';
    const roleName = isEdit ? (roleData.name || '') : '';

    Swal.fire({
        title: title,
        html: `
            <form id="roleForm" style="text-align:left;">
            <input type="hidden" id="swal_role_id" value="${roleId}">

            <div class="form-group">
            <label>Role Name <span class="text-danger">*</span></label>
            <input type="text" id="swal_role_name" class="form-control" value="${escapeHtml(roleName)}" placeholder="e.g., Administrator, Manager">
            </div>

            <div class="form-group">
            <label>Permissions</label>
            <div style="border: 1px solid #ddd; padding: 12px; border-radius: 4px; max-height: 300px; overflow-y: auto; background: #f9f9f9;">

            <div class="permission-group" style="margin-bottom: 12px;">
            <h6 style="margin: 0 0 8px 0;">
            <label style="font-weight: 600; margin: 0;">
            <input type="checkbox" class="permission-category" data-category="dashboard"> Dashboard
            </label>
            </h6>
            <div style="margin-left: 15px;">
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="dashboard.view"> View Dashboard</label>
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="dashboard.export"> Export Reports</label>
            </div>
            </div>

            <div class="permission-group" style="margin-bottom: 12px;">
            <h6 style="margin: 0 0 8px 0;">
            <label style="font-weight: 600; margin: 0;">
            <input type="checkbox" class="permission-category" data-category="inventory"> Inventory Management
            </label>
            </h6>
            <div style="margin-left: 15px;">
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="inventory.view"> View Inventory</label>
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="inventory.create"> Create Items</label>
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="inventory.edit"> Edit Items</label>
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="inventory.delete"> Delete Items</label>
            </div>
            </div>

            <div class="permission-group" style="margin-bottom: 12px;">
            <h6 style="margin: 0 0 8px 0;">
            <label style="font-weight: 600; margin: 0;">
            <input type="checkbox" class="permission-category" data-category="sales"> Sales & Orders
            </label>
            </h6>
            <div style="margin-left: 15px;">
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="sales.view"> View Sales</label>
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="sales.create"> Create Orders</label>
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="sales.approve"> Approve Orders</label>
            </div>
            </div>

            <div class="permission-group" style="margin-bottom: 12px;">
            <h6 style="margin: 0 0 8px 0;">
            <label style="font-weight: 600; margin: 0;">
            <input type="checkbox" class="permission-category" data-category="finance"> Finance & Reports
            </label>
            </h6>
            <div style="margin-left: 15px;">
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="finance.view"> View Reports</label>
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="finance.approve"> Approve Transactions</label>
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="finance.audit"> Audit Trail</label>
            </div>
            </div>

            <div class="permission-group">
            <h6 style="margin: 0 0 8px 0;">
            <label style="font-weight: 600; margin: 0;">
            <input type="checkbox" class="permission-category" data-category="settings"> Settings & Admin
            </label>
            </h6>
            <div style="margin-left: 15px;">
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="settings.users"> Manage Users</label>
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="settings.roles"> Manage Roles</label>
            <label style="display: block; margin: 4px 0;"><input type="checkbox" class="permission-item" name="permissions[]" value="settings.config"> System Configuration</label>
            </div>
            </div>

            </div>
            </div>

            </form>
        `,
        width: '600px',
        showCancelButton: true,
        confirmButtonText: isEdit ? '<i class="ace-icon fa fa-save"></i> Update Role' : '<i class="ace-icon fa fa-save"></i> Create Role',
        cancelButtonText: '<i class="ace-icon fa fa-times"></i> Cancel',
        confirmButtonColor: '#87B87F',
        cancelButtonColor: '#6c757d',
        focusConfirm: false,
        preConfirm: () => {
            const roleName = document.getElementById('swal_role_name').value.trim();

            if (!roleName) {
                Swal.showValidationMessage('Role name is required');
                return false;
            }

            const permissions = Array.from(document.querySelectorAll('.permission-item:checked')).map(p => p.value);

            return {
                id: document.getElementById('swal_role_id').value,
                name: roleName,
                permissions: permissions
            };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            saveRole(result.value);
        }
    });

    // Setup category checkbox handlers
    setTimeout(() => {
        document.querySelectorAll('.permission-category').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const category = this.dataset.category;
                const container = this.closest('.permission-group');
                const items = container.querySelectorAll('.permission-item');
                items.forEach(item => {
                    item.checked = this.checked;
                });
            });
        });
    }, 100);
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

    fetch('index.php?r=settings/roles', {
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

function editRole(role) {
    openRoleModal(role);
}

function deleteRole(roleId) {
    Swal.fire({
        title: 'Delete Role?',
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
            data.append('id', roleId);
            data.append('delete', '1');
            data.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->getCsrfToken() ?>');

            fetch('index.php?r=settings/roles', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    Swal.fire('Deleted!', result.message, 'success').then(() => {
                        document.querySelector(`[data-role-id="${roleId}"]`).remove();
                    });
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'An error occurred.', 'error');
            });
        }
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

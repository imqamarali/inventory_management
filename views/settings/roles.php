<?php

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
                <li class="active">Roles & Permissions</li>
                <div class="nav-search" id="nav-search">
                    <div class="exam-quick-actions-group">
                        <a class="btn btn-sm btn-white btn-primary"
                            style="font-size: 12px; cursor:pointer;"
                            onclick="openRoleModal()">
                            <i class="ace-icon fa fa-plus"></i>
                            Add New Role
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
                                <?php if (count($roles) == 0) { ?>
                                    <div class="alert alert-info text-center">
                                        <i class="ace-icon fa fa-info-circle fa-3x" style="color: #6FB3E0;"></i>
                                        <h4 style="margin-top: 15px;">No Roles Found</h4>
                                        <p>Start by adding your first role using the button above</p>
                                    </div>
                                <?php } else { ?>
                                    <div class="row" id="roles_container">
                                        <?php foreach ($roles as $key => $item):
                                            $userCount = $item['user_count'] ?? 0;
                                            $statusClass = $userCount > 0 ? 'label-warning' : 'label-success';
                                        ?>
                                            <div class="col-md-4 col-sm-6 role-item">
                                                <div class="class-card">
                                                    <div class="class-header">
                                                        <div style="flex: 1;">
                                                            <div class="class-name">
                                                                <i class="fa fa-shield" style="margin-right: 8px;"></i>
                                                                <?php echo htmlspecialchars($item['name']); ?>
                                                            </div>
                                                        </div>

                                                        <div class="btn-group">
                                                            <button type="button"
                                                                onclick="openRoleModal(<?php echo htmlspecialchars(json_encode($item)); ?>)"
                                                                title="Edit Role">
                                                                <i class="ace-icon fa fa-pencil"></i>
                                                            </button>
                                                            &nbsp;&nbsp;|&nbsp;&nbsp;
                                                            <button type="button"
                                                                onclick="<?php echo $userCount > 0 ? 'alert(\'Cannot delete: Users assigned to this role\')' : 'deleteRole(' . $item['id'] . ')'; ?>"
                                                                title="<?php echo $userCount > 0 ? 'Cannot delete: Users assigned' : 'Delete Role'; ?>"
                                                                <?php echo $userCount > 0 ? 'disabled' : ''; ?>>
                                                                <i class="ace-icon fa fa-trash"></i>
                                                            </button>
                                                        </div>

                                                    </div>

                                                    <div class="class-stats">

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-users"></i>
                                                            <span>Users Assigned: <?php echo $userCount; ?></span>
                                                        </div>

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-status"></i>
                                                            <span>Status:
                                                                <?php if ($userCount > 0): ?>
                                                                    <span class="label label-warning">In Use</span>
                                                                <?php else: ?>
                                                                    <span class="label label-success">Available</span>
                                                                <?php endif; ?>
                                                            </span>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function openRoleModal(roleData = null) {
        const isEdit = roleData !== null;
        const title = isEdit ? 'Update Role' : 'New Role';
        const roleId = isEdit ? (roleData.id || '') : '';
        const roleName = isEdit ? (roleData.name || '') : '';

        const modules = [
            { name: 'Dashboard', key: 'dashboard', permissions: ['view'] },
            { name: 'Sales', key: 'sales', permissions: ['view', 'create', 'update', 'delete'] },
            { name: 'Purchase', key: 'purchase', permissions: ['view', 'create', 'update', 'delete'] },
            { name: 'Inventory', key: 'inventory', permissions: ['view', 'edit', 'transfer', 'adjust'] },
            { name: 'Products', key: 'products', permissions: ['view', 'create', 'update', 'delete'] },
            { name: 'Customers', key: 'customers', permissions: ['view', 'create', 'update', 'delete'] },
            { name: 'Suppliers', key: 'suppliers', permissions: ['view', 'create', 'update', 'delete'] },
            { name: 'Finance', key: 'finance', permissions: ['view', 'create', 'update', 'delete'] },
            { name: 'Reports', key: 'reports', permissions: ['view', 'export'] },
            { name: 'Settings', key: 'settings', permissions: ['view', 'manage'] }
        ];

        let permissionsHtml = '<div style="border: 1px solid #ddd; padding: 12px; border-radius: 4px; max-height: 350px; overflow-y: auto; background: #f9f9f9;">';

        modules.forEach(module => {
            permissionsHtml += `
                <div class="permission-group" style="margin-bottom: 12px;">
                <h6 style="margin: 0 0 8px 0;">
                <label style="font-weight: 600; margin: 0;">
                <input type="checkbox" class="permission-category" data-category="${module.key}"> ${module.name}
                </label>
                </h6>
                <div style="margin-left: 20px;">
            `;

            module.permissions.forEach(perm => {
                const permLabel = perm.charAt(0).toUpperCase() + perm.slice(1);
                permissionsHtml += `<label><input type="checkbox" name="permissions" value="${module.key}.${perm}"> ${permLabel}</label><br>`;
            });

            permissionsHtml += '</div></div>';
        });

        permissionsHtml += '</div>';

        Swal.fire({
            title: title,
            html: `
                <form id="roleForm" style="text-align:left;">
                <input type="hidden" id="swal_role_id" value="${roleId}">

                <div class="form-group">
                <label>Role Name <span class="text-danger">*</span></label>
                <input type="text" id="swal_role_name" class="form-control" value="${roleName}" placeholder="e.g., Administrator, Manager, Staff">
                </div>

                <div class="form-group">
                <label>Module Permissions</label>
                ${permissionsHtml}
                </div>

                </form>
                `,
            width: '700px',

            showCancelButton: true,

            confirmButtonText: isEdit ?
                '<i class="ace-icon fa fa-save"></i> Update Role' :
                '<i class="ace-icon fa fa-save"></i> Create Role',

            cancelButtonText: '<i class="ace-icon fa fa-times"></i> Cancel',

            confirmButtonColor: '#87B87F',

            cancelButtonColor: '#6c757d',

            focusConfirm: false,

            didOpen: () => {
                // Set up category checkbox handlers
                setTimeout(() => {
                    document.querySelectorAll('.permission-category').forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const category = this.dataset.category;
                            const isChecked = this.checked;
                            const allCheckboxes = document.querySelectorAll(`input[name="permissions"][value^="${category}."]`);
                            allCheckboxes.forEach(perm => {
                                perm.checked = isChecked;
                            });
                        });
                    });
                }, 100);
            },

            preConfirm: () => {

                const name = document.getElementById('swal_role_name').value.trim();

                if (!name) {

                    Swal.showValidationMessage('Role name is required');

                    return false;

                }

                return {

                    id: document.getElementById('swal_role_id').value,

                    name: name

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


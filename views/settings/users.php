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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const rolesData = <?= json_encode($roles) ?>;

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

            confirmButtonText: isEdit ?
                '<i class="ace-icon fa fa-save"></i> Update User' :
                '<i class="ace-icon fa fa-save"></i> Create User',

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
</script>


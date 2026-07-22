<?php

if (!isset($warehouses)) {
    $warehouses = [];
}

?>
 
<div class="main-content"> 
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li class="active">Warehouses</li>
                <div class="nav-search" id="nav-search">
                    <div class="exam-quick-actions-group"> 
                        <a class="btn btn-sm btn-white btn-primary"
                            style="font-size: 12px; cursor:pointer;"
                            onclick="openWarehouseModal()"> 
                            <i class="ace-icon fa fa-plus"></i>
                            Add New Warehouse 
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
                                <?php if (count($warehouses) == 0) { ?>
                                    <div class="alert alert-info text-center">
                                        <i class="ace-icon fa fa-info-circle fa-3x" style="color: #6FB3E0;"></i>
                                        <h4 style="margin-top: 15px;">No Sessions Found</h4>
                                        <p>Start by adding your first session using the button above</p>
                                    </div>
                                <?php } else { ?>
                                    <div class="row" id="sessions_container">
                                        <?php foreach ($warehouses as $key => $item):
                                            $isActive = $item['is_active'] == 1;
                                            $statusClass = $isActive ? 'label-success' : 'label-danger';
                                        ?>
                                            <div class="col-md-4 col-sm-6 session-item">
                                                <div class="class-card">
                                                    <div class="class-header">
                                                        <div style="flex: 1;">
                                                            <div class="class-name">
                                                                <i class="fa fa-building" style="margin-right: 8px;"></i>
                                                                <?php echo htmlspecialchars($item['warehouse_name']); ?>

                                                            </div>
                                                        </div>

                                                        <div class="btn-group">
                                                            <button type="button"
                                                                onclick="openWarehouseModal(<?php echo htmlspecialchars(json_encode($item)); ?>)"
                                                                title="Edit Warehouse">
                                                                <i class="ace-icon fa fa-pencil"></i>
                                                            </button>
                                                            &nbsp;&nbsp;|&nbsp;&nbsp;
                                                            <button type="button"
                                                                onclick="deleteWarehouse(<?php echo $item['id']; ?>)"
                                                                title="Delete Warehouse">
                                                                <i class="ace-icon fa fa-trash"></i>
                                                            </button>
                                                        </div>

                                                    </div>

                                                    <div class="class-stats">

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-barcode"></i>
                                                            <span>Code: <?php echo htmlspecialchars($item['warehouse_code']); ?></span>
                                                        </div>

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-map-marker"></i>
                                                            <span>City: <?php echo htmlspecialchars($item['city']); ?></span>
                                                        </div>

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-map"></i>
                                                            <span>Province: <?php echo htmlspecialchars($item['province']); ?></span>
                                                        </div>

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-globe"></i>
                                                            <span>Country: <?php echo htmlspecialchars($item['country']); ?></span>
                                                        </div>

                                                    </div>


                                                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #E3E9ED;">

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-home"></i>
                                                            <span>Address: <?php echo htmlspecialchars($item['address']); ?></span>
                                                        </div>

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-user"></i>
                                                            <span>Contact: <?php echo htmlspecialchars($item['contact_person']); ?></span>
                                                        </div>

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-phone"></i>
                                                            <span>Phone: <?php echo htmlspecialchars($item['phone']); ?></span>
                                                        </div>

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-envelope"></i>
                                                            <span>Email: <?php echo htmlspecialchars($item['email']); ?></span>
                                                        </div>

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-comment"></i>
                                                            <span>Remarks: <?php echo htmlspecialchars($item['remarks']); ?></span>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    function openWarehouseModal(warehouseData = null) { 
        const isEdit = warehouseData !== null;
        const title = isEdit ? 'Update Warehouse' : 'New Warehouse'; 
        const warehouseId = isEdit ? (warehouseData.id || '') : '';
        const warehouseName = isEdit ? (warehouseData.warehouse_name || '') : '';
        const warehouseCode = isEdit ? (warehouseData.warehouse_code || '') : '';
        const address = isEdit ? (warehouseData.address || '') : '';
        const city = isEdit ? (warehouseData.city || '') : 'Islamabad';
        const province = isEdit ? (warehouseData.province || '') : 'Islamabad Capital Territory';
        const country = isEdit ? (warehouseData.country || '') : 'Pakistan';
        const contactPerson = isEdit ? (warehouseData.contact_person || '') : '';
        const phone = isEdit ? (warehouseData.phone || '') : '';
        const email = isEdit ? (warehouseData.email || '') : '';
        const remarks = isEdit ? (warehouseData.remarks || '') : '';

        const isActive = isEdit && (warehouseData.is_active == 1 || warehouseData.is_active == '1');


        Swal.fire({

            title: title,
            html:`
                    <form id="warehouseForm" style="text-align:left;">
                    <input type="hidden" id="swal_warehouse_id" value="${warehouseId}">

                    <div class="row">
                    <div class="col-md-6">
                    <label>Warehouse Name <span class="text-danger">*</span></label>
                    <input type="text" id="swal_warehouse_name" class="form-control" value="${warehouseName}">
                    </div>

                    <div class="col-md-6">
                    <label>Warehouse Code <span class="text-danger">*</span></label>
                    <input type="text" id="swal_warehouse_code" class="form-control" value="${warehouseCode}">
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-md-6">
                    <label>Address</label>
                    <input type="text" id="swal_address" class="form-control" value="${address}">
                    </div>

                    <div class="col-md-6">
                    <label>City</label>
                    <input type="text" id="swal_city" class="form-control" value="${city}">
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-md-6">
                    <label>Province</label>
                    <input type="text" id="swal_province" class="form-control" value="${province}">
                    </div>

                    <div class="col-md-6">
                    <label>Country</label>
                    <input type="text" id="swal_country" class="form-control" value="${country}">
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-md-6">
                    <label>Contact Person</label>
                    <input type="text" id="swal_contact_person" class="form-control" value="${contactPerson}">
                    </div>

                    <div class="col-md-6">
                    <label>Phone</label>
                    <input type="text" id="swal_phone" class="form-control" value="${phone}">
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-md-6">
                    <label>Email</label>
                    <input type="email" id="swal_email" class="form-control" value="${email}">
                    </div>

                    <div class="col-md-6">
                    <label>Remarks</label>
                    <input type="text" id="swal_remarks" class="form-control" value="${remarks}">
                    </div>
                    </div>

                    <div class="form-group" style="margin-top:10px;">
                    <label>
                    <input type="checkbox" id="swal_active" ${isActive ? 'checked' : ''}>
                    Active
                    </label>
                    </div>

                    </form>
                    `,
            width: '700px',

            showCancelButton: true,

            confirmButtonText: isEdit ?
                '<i class="ace-icon fa fa-save"></i> Update Warehouse' :
                '<i class="ace-icon fa fa-save"></i> Create Warehouse',

            cancelButtonText: '<i class="ace-icon fa fa-times"></i> Cancel',

            confirmButtonColor: '#87B87F',

            cancelButtonColor: '#6c757d',

            focusConfirm: false,


            preConfirm: () => {


                const name = document.getElementById('swal_warehouse_name').value.trim();

                const code = document.getElementById('swal_warehouse_code').value.trim();


                if (!name) {

                    Swal.showValidationMessage('Warehouse name is required');

                    return false;

                }


                if (!code) {

                    Swal.showValidationMessage('Warehouse code is required');

                    return false;

                }


                return {

                    id: document.getElementById('swal_warehouse_id').value,

                    warehouse_name: name,

                    warehouse_code: code,

                    address: document.getElementById('swal_address').value,

                    city: document.getElementById('swal_city').value,

                    province: document.getElementById('swal_province').value,

                    country: document.getElementById('swal_country').value,

                    contact_person: document.getElementById('swal_contact_person').value,

                    phone: document.getElementById('swal_phone').value,

                    email: document.getElementById('swal_email').value,

                    remarks: document.getElementById('swal_remarks').value,

                    active: document.getElementById('swal_active').checked

                };

            }


        }).then((result) => {

            if (result.isConfirmed && result.value) {

                saveWarehouse(result.value);

            }

        });


    }



    function saveWarehouse(formData) {  
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
        data.append('warehouse_name', formData.warehouse_name);
        data.append('warehouse_code', formData.warehouse_code);
        data.append('address', formData.address);
        data.append('city', formData.city);
        data.append('province', formData.province);
        data.append('country', formData.country);
        data.append('contact_person', formData.contact_person);
        data.append('phone', formData.phone);
        data.append('email', formData.email);
        data.append('remarks', formData.remarks);
        if (formData.active) {
            data.append('is_active', '1');
        }
        fetch('index.php?r=inventory/warehouses', {
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

    function deleteWarehouse(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Warehouse will be deleted.',
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
                fetch('index.php?r=inventory/warehouses', {
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



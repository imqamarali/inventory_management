<?php

use yii\helpers\Html;

if (!isset($suppliers)) {
    $suppliers = [];
}
?>
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li><i class="ace-icon fa fa-home home-icon"></i><a href="index.php?r=inventory/dashboard">Home</a></li>
                <li class="active">Suppliers</li>
                <li style="float:right;">
                    <div class="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openSupplierModal()"><i class="ace-icon fa fa-plus"></i> Add New Supplier</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="supplier_search">
                <input type="hidden" name="r" value="supplier/supplierlist">
                <input type="text" id="supplier_code" class="new-input" style="width:14%" placeholder="Supplier Code">
                <input type="text" id="company_name" class="new-input" style="width:20%" placeholder="Company Name">
                <input type="text" id="contact_person" class="new-input" style="width:18%" placeholder="Contact Person">
                <input type="text" id="city" class="new-input" style="width:15%" placeholder="City">
                <input type="text" id="per_page" value="<?= $perPage ?? 20 ?>" class="new-input" style="width:8%" placeholder="Rows">
                <input type="button" class="btn btn-primary" value="Search" style="height:30px;padding:0 15px;margin-top:-3px;" onclick="searchSuppliers()">
            </form>
        </div>
        <div class="widget-main">
            <?php if (count($suppliers) == 0) { ?>
                <div class="alert alert-info text-center">
                    <i class="ace-icon fa fa-info-circle fa-3x" style="color:#6FB3E0;"></i>
                    <h4 style="margin-top:15px;">No Suppliers Found</h4>
                    <p>Start by adding your first supplier using the button above.</p>
                </div>
            <?php } else { ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="suppliers_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Supplier Code</th>
                                <th>Company</th>
                                <th>Contact Person</th>
                                <th>Phone</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>City</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suppliers as $key => $item) { ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><?= Html::encode($item['supplier_code']) ?></td>
                                    <td><?= Html::encode($item['company_name']) ?></td>
                                    <td><?= Html::encode($item['contact_person']) ?></td>
                                    <td><?= Html::encode($item['phone']) ?></td>
                                    <td><?= Html::encode($item['mobile']) ?></td>
                                    <td><?= Html::encode($item['email']) ?></td>
                                    <td><?= Html::encode($item['city']) ?></td>
                                    <td><?= number_format($item['current_balance'], 2) ?></td>
                                    <td>
                                        <?php if ($item['is_active']) { ?>
                                            <span class="label label-success">Active</span>
                                        <?php } else { ?>
                                            <span class="label label-danger">Inactive</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <button type="button" onclick='openSupplierModal(<?= json_encode($item) ?>)'><i class="fa fa-pencil"></i></button>
                                        &nbsp;|&nbsp;
                                        <button type="button" onclick="deleteSupplier(<?= $item['id'] ?>)"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div id="paginationArea" class="text-center"></div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function searchSuppliers(page = 1) {
        Swal.fire({
            title: 'Loading Suppliers...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'search');
        data.append('supplier_code', $('#supplier_code').val());
        data.append('company_name', $('#company_name').val());
        data.append('contact_person', $('#contact_person').val());
        data.append('city', $('#city').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);
        fetch('index.php?r=supplier/supplierlist', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderSuppliers(res.suppliers);
                    renderPagination(res.page, res.total_pages);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.close();
                Swal.fire('Error', 'Unable to fetch suppliers.', 'error');
            });
    }

    function renderSuppliers(suppliers) {
        let html = '';
        if (suppliers.length == 0) {
            html = '<tr><td colspan="11" class="text-center">No Suppliers Found</td></tr>';
        } else {
            suppliers.forEach(function(item, index) {
                html += `
                    <tr>
                    <td>${index+1}</td>
                    <td>${item.supplier_code??''}</td>
                    <td>${item.company_name??''}</td>
                    <td>${item.contact_person??''}</td>
                    <td>${item.phone??''}</td>
                    <td>${item.mobile??''}</td>
                    <td>${item.email??''}</td>
                    <td>${item.city??''}</td>
                    <td>${Number(item.current_balance).toLocaleString()}</td>
                    <td>${item.is_active==1?'<span class="label label-success">Active</span>':'<span class="label label-danger">Inactive</span>'}</td>
                    <td>
                    <button onclick='openSupplierModal(${JSON.stringify(item)})'><i class="fa fa-pencil"></i></button>
                    |
                    <button onclick="deleteSupplier(${item.id})"><i class="fa fa-trash"></i></button>
                    </td>
                    </tr>`;
            });
        }
        $('#suppliers_table tbody').html(html);
    }

    function renderPagination(page, totalPages) {
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `<button class="${i==page?'btn-primary':' btn-default'}" onclick="searchSuppliers(${i})">${i}</button> `;
        }
        $('#paginationArea').html(html);
    }

    function openSupplierModal(data = null) {
        const edit = data !== null;
        const id = edit ? (data.id || '') : '';
        const supplier_code = edit ? (data.supplier_code || '') : '';
        const company_name = edit ? (data.company_name || '') : '';
        const contact_person = edit ? (data.contact_person || '') : '';
        const email = edit ? (data.email || '') : '';
        const phone = edit ? (data.phone || '') : '';
        const mobile = edit ? (data.mobile || '') : '';
        const website = edit ? (data.website || '') : '';
        const tax_number = edit ? (data.tax_number || '') : '';
        const payment_terms = edit ? (data.payment_terms || '') : '';
        const credit_limit = edit ? (data.credit_limit || '0') : '0';
        const opening_balance = edit ? (data.opening_balance || '0') : '0';
        const current_balance = edit ? (data.current_balance || '0') : '0';
        const address = edit ? (data.address || '') : '';
        const city = edit ? (data.city || '') : '';
        const province = edit ? (data.province || '') : '';
        const country = edit ? (data.country || '') : '';
        const postal_code = edit ? (data.postal_code || '') : '';
        const remarks = edit ? (data.remarks || '') : '';
        const active = edit && (data.is_active == 1 || data.is_active == '1');

        Swal.fire({
            title: edit ? 'Update Supplier' : 'New Supplier',
            width: '900px',
            showCancelButton: true,
            confirmButtonColor: '#87B87F',
            cancelButtonColor: '#6c757d',
            confirmButtonText: edit ? '<i class="fa fa-save"></i> Update' : '<i class="fa fa-save"></i> Save',
            html: `
                <input type="hidden" id="swal_supplier_id" value="${id}">
                <div class="row">
                <div class="col-md-4"><label>Supplier Code *</label><input id="swal_supplier_code" class="form-control" value="${supplier_code}"></div>
                <div class="col-md-8"><label>Company Name *</label><input id="swal_company_name" class="form-control" value="${company_name}"></div>
                </div>
                <div class="row">
                <div class="col-md-4"><label>Contact Person</label><input id="swal_contact_person" class="form-control" value="${contact_person}"></div>
                <div class="col-md-4"><label>Email</label><input id="swal_email" class="form-control" value="${email}"></div>
                <div class="col-md-4"><label>Phone</label><input id="swal_phone" class="form-control" value="${phone}"></div>
                </div>
                <div class="row">
                <div class="col-md-4"><label>Mobile</label><input id="swal_mobile" class="form-control" value="${mobile}"></div>
                <div class="col-md-4"><label>Website</label><input id="swal_website" class="form-control" value="${website}"></div>
                <div class="col-md-4"><label>Tax Number</label><input id="swal_tax_number" class="form-control" value="${tax_number}"></div>
                </div>
                <div class="row">
                <div class="col-md-4"><label>Payment Terms</label><input id="swal_payment_terms" class="form-control" value="${payment_terms}"></div>
                <div class="col-md-4"><label>Credit Limit</label><input id="swal_credit_limit" type="number" class="form-control" value="${credit_limit}"></div>
                <div class="col-md-4"><label>Opening Balance</label><input id="swal_opening_balance" type="number" class="form-control" value="${opening_balance}"></div>
                </div>
                <div class="row">
                <div class="col-md-12"><label>Address</label><textarea id="swal_address" class="form-control">${address}</textarea></div>
                </div>
                <div class="row">
                <div class="col-md-3"><label>City</label><input id="swal_city" class="form-control" value="${city}"></div>
                <div class="col-md-3"><label>Province</label><input id="swal_province" class="form-control" value="${province}"></div>
                <div class="col-md-3"><label>Country</label><input id="swal_country" class="form-control" value="${country}"></div>
                <div class="col-md-3"><label>Postal Code</label><input id="swal_postal_code" class="form-control" value="${postal_code}"></div>
                </div>
                <div class="row">
                <div class="col-md-12"><label>Remarks</label><textarea id="swal_remarks" class="form-control">${remarks}</textarea></div>
                </div>
                <div style="margin-top:10px;">
                <label><input type="checkbox" id="swal_active" ${active?'checked':''}> Active</label>
                </div>
                `,
            preConfirm: () => {
                if ($('#swal_supplier_code').val().trim() == '' || $('#swal_company_name').val().trim() == '') {
                    Swal.showValidationMessage('Supplier Code and Company Name are required.');
                    return false;
                }
                return {
                    id: $('#swal_supplier_id').val(),
                    supplier_code: $('#swal_supplier_code').val(),
                    company_name: $('#swal_company_name').val(),
                    contact_person: $('#swal_contact_person').val(),
                    email: $('#swal_email').val(),
                    phone: $('#swal_phone').val(),
                    mobile: $('#swal_mobile').val(),
                    website: $('#swal_website').val(),
                    tax_number: $('#swal_tax_number').val(),
                    payment_terms: $('#swal_payment_terms').val(),
                    credit_limit: $('#swal_credit_limit').val(),
                    opening_balance: $('#swal_opening_balance').val(),
                    address: $('#swal_address').val(),
                    city: $('#swal_city').val(),
                    province: $('#swal_province').val(),
                    country: $('#swal_country').val(),
                    postal_code: $('#swal_postal_code').val(),
                    remarks: $('#swal_remarks').val(),
                    active: $('#swal_active').is(':checked')
                };
            }
        }).then(result => {
            if (result.isConfirmed) {
                saveSupplier(result.value);
            }
        });
    }

    function saveSupplier(formData) {
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
        Object.keys(formData).forEach(function(key) {
            if (key != 'active') {
                data.append(key, formData[key]);
            }
        });

        if (formData.active) {
            data.append('is_active', '1');
        }

        fetch('index.php?r=supplier/supplierlist', {
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
                        $('.ajax-module.active').trigger('click');
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'An error occurred. Please try again.', 'error');
            });
    }


    function deleteSupplier(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Supplier will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if (result.isConfirmed) {
                const data = new FormData();
                data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
                data.append('id', id);
                data.append('delete', '1');

                fetch('index.php?r=supplier/supplierlist', {
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
                                $('.ajax-module.active').trigger('click');
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Unable to delete supplier.', 'error');
                    });
            }
        });
    }
</script>
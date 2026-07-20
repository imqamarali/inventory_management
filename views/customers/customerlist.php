<?php

use yii\helpers\Html;

if (!isset($customers)) $customers = [];
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=customers/customerdashboard">Home</a>
                </li>
                <li class="active">Customer List</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openCustomerModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Customer
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="customer_search" onsubmit="return false;">

                <input type="text" name="company_name" id="company_name" class="new-input" style="width:18%;" placeholder="Company / Name">

                <input type="text" name="email" id="email" class="new-input" style="width:15%;" placeholder="Email">

                <input type="text" name="phone" id="phone" class="new-input" style="width:12%;" placeholder="Phone">

                <select name="customer_type" id="customer_type" class="new-input" style="width:13%;">
                    <option value="">All Types</option>
                    <option value="Individual">Individual</option>
                    <option value="Company">Company</option>
                </select>

                <select name="is_active" id="is_active" class="new-input" style="width:12%;">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>

                <input type="text" name="per_page" id="per_page" value="20" class="new-input" style="width:6%;" placeholder="Records?">

                <input type="button" class="btn btn-primary"
                    onclick="searchform()"
                    value="Search"
                    style="height:30px;padding:0;margin-top:-3px;" />

            </form>
        </div>

        <div class="widget-main">

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="customer_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $key => $item) { ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= Html::encode($item['customer_code']) ?></td>
                                <td><?= Html::encode($item['company_name'] ?: trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''))) ?></td>
                                <td><?= Html::encode($item['customer_type']) ?></td>
                                <td><?= Html::encode($item['email'] ?? '') ?></td>
                                <td><?= Html::encode($item['phone'] ?? '') ?></td>
                                <td><?= number_format($item['current_balance'] ?? 0, 2) ?></td>
                                <td><?= $item['is_active'] ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>' ?></td>
                                <td>
                                    <button onclick='openCustomerModal(<?= json_encode($item) ?>)'>
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    |
                                    <button onclick="deleteCustomer(<?= $item['id'] ?>)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div id="paginationArea" class="text-center"></div>

            </div>

        </div>

    </div>
</div>

<style>
    .swal2-popup.swal-wide-popup {
        width: 900px !important;
        max-width: 95vw !important;
    }

    .swal2-popup.swal-wide-popup .swal2-html-container {
        max-height: none !important;
        overflow: visible !important;
    }
</style>

<script>
    searchform();

    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading Customers...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'load');
        data.append('company_name', $('#company_name').val());
        data.append('email', $('#email').val());
        data.append('phone', $('#phone').val());
        data.append('customer_type', $('#customer_type').val());
        data.append('is_active', $('#is_active').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=customers/customerlist', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderCustomers(res.customers);
                    renderPagination(res.page, res.totalPages);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load customers.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });
    }

    function renderCustomers(rows) {

        let html = '';

        if (rows.length == 0) {

            html = `
        <tr>
            <td colspan="9" class="text-center">
                No Customers Found
            </td>
        </tr>`;

        } else {

            rows.forEach(function(item, index) {

                const statusLabel = item.is_active ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.customer_code}</td>
                <td>${(item.company_name || (item.first_name + ' ' + item.last_name)).trim()}</td>
                <td>${item.customer_type??''}</td>
                <td>${item.email??''}</td>
                <td>${item.phone??''}</td>
                <td>${parseFloat(item.current_balance).toFixed(2)}</td>
                <td>${statusLabel}</td>
                <td>
                    <button onclick='openCustomerModal(${JSON.stringify(item)})'>
                        <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button onclick="deleteCustomer(${item.id})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;

            });

        }

        $('#customer_table tbody').html(html);

    }

    function renderPagination(page, totalPages) {
        if (totalPages <= 1) {
            $('#paginationArea').html('');
            return;
        }
        let html = '<nav><ul class="pagination">';
        if (page > 1) {
            html += `<li class="page-item"><a class="page-link" onclick="searchform(1)">First</a></li>`;
            html += `<li class="page-item"><a class="page-link" onclick="searchform(${page - 1})">Previous</a></li>`;
        }
        for (let i = Math.max(1, page - 2); i <= Math.min(totalPages, page + 2); i++) {
            if (i === page) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link" onclick="searchform(${i})">${i}</a></li>`;
            }
        }
        if (page < totalPages) {
            html += `<li class="page-item"><a class="page-link" onclick="searchform(${page + 1})">Next</a></li>`;
            html += `<li class="page-item"><a class="page-link" onclick="searchform(${totalPages})">Last</a></li>`;
        }
        html += '</ul></nav>';
        $('#paginationArea').html(html);
    }

    function openCustomerModal(item = null) {
       
        let html = `
            <form id="customerForm" style="padding:15px;">
                <input type="hidden" name="id" id="cust_id" value="${item?.id || ''}">

                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:15px;">

                    <div>
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" id="first_name" value="${item?.first_name || ''}" placeholder="First name">
                    </div>

                    <div>
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="last_name" value="${item?.last_name || ''}" placeholder="Last name">
                    </div>

                    <div>
                        <label class="form-label">Company Name</label>
                        <input type="text" class="form-control" name="company_name" value="${item?.company_name || ''}" placeholder="Company name">
                    </div>

                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="${item?.email || ''}" placeholder="Email">
                    </div>

                    <div>
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" value="${item?.phone || ''}" placeholder="Phone">
                    </div>

                    <div>
                        <label class="form-label">Mobile</label>
                        <input type="text" class="form-control" name="mobile" value="${item?.mobile || ''}" placeholder="Mobile">
                    </div>

                    <div>
                        <label class="form-label">Customer Type</label>
                        <select class="form-control" name="customer_type">
                            <option value="Individual" ${item?.customer_type === 'Individual' ? 'selected' : ''}>Individual</option>
                            <option value="Company" ${item?.customer_type === 'Company' ? 'selected' : ''}>Company</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Tax Number</label>
                        <input type="text" class="form-control" name="tax_number" value="${item?.tax_number || ''}" placeholder="Tax number">
                    </div>

                    <div>
                        <label class="form-label">Credit Limit</label>
                        <input type="number" class="form-control" name="credit_limit" value="${item?.credit_limit || 0}" step="0.01">
                    </div>

                    <div>
                        <label class="form-label">Payment Terms (Days)</label>
                        <input type="number" class="form-control" name="payment_terms" value="${item?.payment_terms || 0}">
                    </div>

                    <div>
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" name="address" value="${item?.address || ''}" placeholder="Address">
                    </div>

                    <div>
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="city" value="${item?.city || ''}" placeholder="City">
                    </div>

                    <div>
                        <label class="form-label">Province</label>
                        <input type="text" class="form-control" name="province" value="${item?.province || ''}" placeholder="Province">
                    </div>

                    <div>
                        <label class="form-label">Country</label>
                        <input type="text" class="form-control" name="country" value="${item?.country || ''}" placeholder="Country">
                    </div>

                    <div>
                        <label class="form-label">Postal Code</label>
                        <input type="text" class="form-control" name="postal_code" value="${item?.postal_code || ''}" placeholder="Postal code">
                    </div>

                    <div style="grid-column:1/-1;">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" rows="2" placeholder="Remarks">${item?.remarks || ''}</textarea>
                    </div>

                </div>
            </form>
            `;

        Swal.fire({
            title: item ? 'Edit Customer' : 'Add Customer',
            
            width: '1000px',
            html: html,
            
            customClass: {
                popup: 'swal-wide-popup'
            },
            showCancelButton: true,
            confirmButtonText: 'Save',
            didOpen: () => {
                $('#cust_id').focus();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                saveCustomer();
            }
        });
    }

    function saveCustomer() {
        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('id', $('#cust_id').val());
        data.append('first_name', $('input[name="first_name"]').val());
        data.append('last_name', $('input[name="last_name"]').val());
        data.append('company_name', $('input[name="company_name"]').val());
        data.append('email', $('input[name="email"]').val());
        data.append('phone', $('input[name="phone"]').val());
        data.append('mobile', $('input[name="mobile"]').val());
        data.append('customer_type', $('select[name="customer_type"]').val());
        data.append('tax_number', $('input[name="tax_number"]').val());
        data.append('credit_limit', $('input[name="credit_limit"]').val());
        data.append('payment_terms', $('input[name="payment_terms"]').val());
        data.append('address', $('input[name="address"]').val());
        data.append('city', $('input[name="city"]').val());
        data.append('province', $('input[name="province"]').val());
        data.append('country', $('input[name="country"]').val());
        data.append('postal_code', $('input[name="postal_code"]').val());
        data.append('remarks', $('textarea[name="remarks"]').val());

        fetch('index.php?r=customers/addcustomer', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Success', res.message, 'success').then(() => {
                        searchform();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'Unable to save!', 'error');
            });
    }

    function deleteCustomer(id) {
        Swal.fire({
            title: 'Confirm Delete',
            text: 'Are you sure you want to delete this customer?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                const data = new FormData();
                data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
                data.append('id', id);
                data.append('delete', 1);

                fetch('index.php?r=customers/customerlist', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire('Deleted', res.message, 'success').then(() => {
                                searchform();
                            });
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire('Error', 'Unable to delete!', 'error');
                    });
            }
        });
    }
</script>

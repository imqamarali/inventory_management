<?php

use yii\helpers\Html;

if (!isset($customers)) $customers = [];
$type = $type ?? 'All';
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=customers/customerdashboard">Home</a>
                </li>
                <li class="active"><?= Html::encode(ucfirst($type)) ?> Customers</li>
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

                <input type="hidden" name="type" id="customer_type" value="<?= Html::encode($type) ?>">

                <input type="text" name="company_name" id="company_name" class="new-input" style="width:20%;" placeholder="Company / Name">

                <input type="text" name="email" id="email" class="new-input" style="width:15%;" placeholder="Email">

                <input type="text" name="phone" id="phone" class="new-input" style="width:12%;" placeholder="Phone">

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
                            <th>Email</th>
                            <th>Phone</th>
                            <th>City</th>
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
                                <td><?= Html::encode($item['email'] ?? '') ?></td>
                                <td><?= Html::encode($item['phone'] ?? '') ?></td>
                                <td><?= Html::encode($item['city'] ?? '') ?></td>
                                <td><?= number_format($item['current_balance'] ?? 0, 2) ?></td>
                                <td><?= $item['is_active'] ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>' ?></td>
                                <td>
                                    <button onclick='viewLedger(<?= $item['id'] ?>)' title="View Ledger">
                                        <i class="fa fa-book"></i>
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
        data.append('type', $('#customer_type').val());
        data.append('company_name', $('#company_name').val());
        data.append('email', $('#email').val());
        data.append('phone', $('#phone').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=customers/retailcustomers', {
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
                <td>${item.email??''}</td>
                <td>${item.phone??''}</td>
                <td>${item.city??''}</td>
                <td>${parseFloat(item.current_balance||0).toFixed(2)}</td>
                <td>${statusLabel}</td>
                <td>
                    <button onclick='viewLedger(${item.id})' title="View Ledger">
                        <i class="fa fa-book"></i>
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

    function openCustomerModal() {
        window.location.href = 'index.php?r=customers/addcustomer';
    }

    function viewLedger(customerId) {
        window.location.href = 'index.php?r=customers/customerledger&customer_id=' + customerId;
    }
</script>

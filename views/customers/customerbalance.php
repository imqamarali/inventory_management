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
                <li class="active">Outstanding Balance</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="loadBalances()">
                                <i class="ace-icon fa fa-refresh"></i>
                                Refresh
                            </a>
                            <a class="btn btn-sm btn-white btn-info" style="font-size:12px;cursor:pointer;" onclick="window.print()">
                                <i class="ace-icon fa fa-print"></i>
                                Print
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="balance_search" onsubmit="return false;">

                <input type="text" name="customer_name" id="customer_name" class="new-input" style="width:25%;" placeholder="Customer Name / Code">

                <select name="status_filter" id="status_filter" class="new-input" style="width:15%;" onchange="loadBalances()">
                    <option value="">All Status</option>
                    <option value="overdue">Over Limit</option>
                    <option value="due">Due</option>
                </select>

                <input type="text" name="per_page" id="per_page" value="50" class="new-input" style="width:6%;" placeholder="Records?">

                <input type="button" class="btn btn-primary"
                    onclick="loadBalances()"
                    value="Search"
                    style="height:30px;padding:0;margin-top:-3px;" />

            </form>
        </div>

        <div class="widget-main">

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="balance_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Credit Limit</th>
                            <th>Outstanding Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>

            </div>

        </div>

        <div style="margin-top:20px;">
            <div class="row">
                <div class="col-lg-6">
                    <div class="widget-box">
                        <div class="widget-header">
                            <h4 class="widget-title">Summary</h4>
                        </div>
                        <div class="widget-body">
                            <div style="padding:15px;">
                                <p><strong>Total Outstanding:</strong> <span class="text-danger" id="total_outstanding">$0.00</span></p>
                                <p><strong>Customers Over Limit:</strong> <span class="text-danger" id="over_limit_count">0</span></p>
                                <p><strong>Due Customers:</strong> <span class="text-warning" id="due_count">0</span></p>
                            </div>
                        </div>
                    </div>
                </div>
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

    @media print {
        .breadcrumbs,
        #balance_search,
        .btn,
        .nav-search {
            display: none !important;
        }
    }
</style>

<script>
    $(document).ready(function() {
        loadBalances();
    });

    function loadBalances() {

        Swal.fire({
            title: 'Loading Balances...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'load');
        data.append('customer_name', $('#customer_name').val());
        data.append('status_filter', $('#status_filter').val());
        data.append('per_page', $('#per_page').val());

        fetch('index.php?r=customers/customerbalance', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderBalances(res.customers);
                    updateSummary(res.customers);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load balances.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });
    }

    function renderBalances(rows) {

        let html = '';

        if (rows.length == 0) {

            html = `
        <tr>
            <td colspan="8" class="text-center text-success">
                All customers have zero balance - Great!
            </td>
        </tr>`;

        } else {

            rows.forEach(function(item, index) {

                let statusBadge = '';
                if (item.current_balance > item.credit_limit) {
                    statusBadge = '<span class="label label-danger">Over Limit</span>';
                } else if (item.current_balance > 0) {
                    statusBadge = '<span class="label label-warning">Due</span>';
                } else {
                    statusBadge = '<span class="label label-success">Paid</span>';
                }

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.customer_code}</td>
                <td>${item.name??''}</td>
                <td>${item.email??''}</td>
                <td>${parseFloat(item.credit_limit).toFixed(2)}</td>
                <td class="text-danger fw-bold">${parseFloat(item.current_balance).toFixed(2)}</td>
                <td>${statusBadge}</td>
                <td>
                    <button onclick="recordPayment(${item.id})" title="Record Payment">
                        <i class="fa fa-credit-card"></i>
                    </button>
                </td>
            </tr>`;

            });

        }

        $('#balance_table tbody').html(html);

    }

    function updateSummary(rows) {
        let totalOutstanding = 0;
        let overLimitCount = 0;
        let dueCount = 0;

        rows.forEach(function(item) {
            totalOutstanding += parseFloat(item.current_balance);
            if (item.current_balance > item.credit_limit) {
                overLimitCount++;
            } else if (item.current_balance > 0) {
                dueCount++;
            }
        });

        $('#total_outstanding').text('$' + totalOutstanding.toFixed(2));
        $('#over_limit_count').text(overLimitCount);
        $('#due_count').text(dueCount);
    }

    function recordPayment(customerId) {
        Swal.fire({
            title: 'Redirect to Payment',
            text: 'You will be redirected to record a payment for this customer.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Go to Payment',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php?r=customers/customerpayments';
            }
        });
    }
</script>

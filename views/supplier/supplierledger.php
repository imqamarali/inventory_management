<?php

use yii\helpers\Html;

if (!isset($ledger)) {
    $ledger = [];
}
if (!isset($suppliers)) {
    $suppliers = [];
}
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li class="active">Supplier Ledger</li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">

            <form id="supplier_ledger_search">

                <input type="hidden" name="r" value="supplier/supplierledger">

                <select id="supplier_id" class="new-input" style="width:22%;">
                    <option value="">All Suppliers</option>
                    <?php foreach ($suppliers as $supplier) { ?>
                        <option value="<?= $supplier['id'] ?>">
                            <?= Html::encode($supplier['company_name']) ?>
                        </option>
                    <?php } ?>
                </select>

                <input
                    type="date"
                    id="from_date"
                    class="new-input"
                    style="width:14%;">

                <input
                    type="date"
                    id="to_date"
                    class="new-input"
                    style="width:14%;">

                <input
                    type="text"
                    id="per_page"
                    value="<?= $perPage ?? 20 ?>"
                    class="new-input"
                    style="width:8%;"
                    placeholder="Rows">

                <input
                    type="button"
                    class="btn btn-primary"
                    value="Search"
                    style="height:30px;padding:0 15px;margin-top:-3px;"
                    onclick="searchSupplierLedger()">

            </form>

        </div>
        <div class="widget-main">
            <?php if (count($ledger) == 0) { ?>

                <div class="alert alert-info text-center">
                    <i class="ace-icon fa fa-book fa-3x" style="color:#6FB3E0;"></i>
                    <h4 style="margin-top:15px;">No Ledger Found</h4>
                    <p>No supplier ledger records available.</p>
                </div>

            <?php } else { ?>

                <div class="table-responsive">

                    <table class="table table-striped table-bordered table-hover" id="supplier_ledger_table">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Supplier Code</th>
                                <th>Company</th>
                                <th>Contact Person</th>
                                <th>Phone</th>
                                <th>Payment No</th>
                                <th>Payment Date</th>
                                <th>Payment Method</th>
                                <th>Payment Type</th>
                                <th>Amount</th>
                                <th>Balance</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ledger as $key => $item) { ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><?= Html::encode($item['supplier_code'] ?? '') ?></td>
                                    <td><?= Html::encode($item['company_name'] ?? '') ?></td>
                                    <td><?= Html::encode($item['contact_person'] ?? '') ?></td>
                                    <td><?= Html::encode($item['phone'] ?? '') ?></td>
                                    <td><?= Html::encode($item['payment_no'] ?? '') ?></td>
                                    <td><?= Html::encode($item['payment_date'] ?? '') ?></td>
                                    <td><?= Html::encode($item['payment_method'] ?? '') ?></td>
                                    <td><?= Html::encode($item['payment_type'] ?? '') ?></td>
                                    <td><?= number_format($item['amount'] ?? '0', 2) ?></td>
                                    <td><?= number_format($item['current_balance'] ?? '0', 2) ?></td>
                                    <td><?= Html::encode($item['remarks'] ?? '') ?></td>
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

<script>
    function searchSupplierLedger(page = 1) {
        Swal.fire({
            title: 'Loading Supplier Ledger...',
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
        data.append('supplier_id', $('#supplier_id').val());
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=supplier/supplierledger', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(res => {

                Swal.close();

                if (res.success) {

                    renderSupplierLedger(res.ledger);
                    renderPagination(res.page, res.total_pages);

                } else {

                    Swal.fire('Error', res.message, 'error');

                }

            })
            .catch(() => {

                Swal.close();

                Swal.fire(
                    'Error',
                    'Unable to fetch supplier ledger.',
                    'error'
                );

            });
    }

    function renderSupplierLedger(ledger) {
        let html = '';
        if (ledger.length == 0) {
            html = `
            <tr>
                <td colspan="12" class="text-center">
                    No Supplier Ledger Found
                </td>
            </tr>
        `;
        } else {
            ledger.forEach(function(item, index) {
                html += `
                <tr> 
                    <td>${index+1}</td> 
                    <td>${item.supplier_code??''}</td> 
                    <td>${item.company_name??''}</td>  
                    <td>${item.contact_person??''}</td> 
                    <td>${item.phone??''}</td> 
                    <td>${item.payment_no??''}</td> 
                    <td>${item.payment_date??''}</td> 
                    <td>${item.payment_method??''}</td> 
                    <td>${item.payment_type??''}</td> 
                    <td>${Number(item.amount??0).toLocaleString()}</td> 
                    <td>${Number(item.current_balance??0).toLocaleString()}</td> 
                    <td>${item.remarks??''}</td> 
                </tr>
            `;
            });
        }
        $('#supplier_ledger_table tbody').html(html);
    }
    function renderPagination(page, totalPages) {
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `
            <button
                class="${i==page?'btn-primary':'btn-default'}"
                onclick="searchSupplierLedger(${i})">
                ${i}
            </button>
        `;
            html += ' ';
        }
        $('#paginationArea').html(html);
    }
    $(document).ready(function() {

        if ($('#supplier_ledger_table tbody tr').length == 0) {
            searchSupplierLedger();
        }

        $('#supplier_id').change(function() {
            searchSupplierLedger();
        });

        $('#from_date').change(function() {
            searchSupplierLedger();
        });

        $('#to_date').change(function() {
            searchSupplierLedger();
        });

        $('#per_page').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                searchSupplierLedger();
            }
        });

    });
</script>
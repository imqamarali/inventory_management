<?php

use yii\helpers\Html;

if (!isset($customers)) $customers = [];
if (!isset($ledger)) $ledger = [];
$customer_id = $customer_id ?? 0;
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=customers/customerdashboard">Home</a>
                </li>
                <li class="active">Customer Ledger</li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="ledger_search" onsubmit="return false;">

                <select name="customer_id" id="customer_id" class="new-input" style="width:35%;" onchange="loadLedgerData()">
                    <option value="">-- Select Customer --</option>
                    <?php foreach ($customers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['name']) ?></option>
                    <?php } ?>
                </select>

                <input type="date" name="from_date" id="from_date" class="new-input" style="width:12%;" onchange="loadLedgerData()">
                <input type="date" name="to_date" id="to_date" class="new-input" style="width:12%;" onchange="loadLedgerData()">

                <input type="button" class="btn btn-primary"
                    onclick="loadLedgerData()"
                    value="Load"
                    style="height:30px;padding:0;margin-top:-3px;" />

            </form>
        </div>

        <div class="widget-main">

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="ledger_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Debit (Sales)</th>
                            <th>Credit (Payment)</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center">Select a customer to view ledger</td>
                        </tr>
                    </tbody>
                </table>

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
    let customers = <?= json_encode($customers) ?>;

    function loadLedgerData() {
        const customerId = $('#customer_id').val();
        if (!customerId) {
            $('#ledger_table tbody').html('<tr><td colspan="7" class="text-center">Select a customer to view ledger</td></tr>');
            return;
        }
        loadLedger();
    }

    function loadLedger() {

        Swal.fire({
            title: 'Loading Ledger...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'load');
        data.append('customer_id', $('#customer_id').val());
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());

        fetch('index.php?r=customers/customerledger', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderLedger(res.ledger);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load ledger.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });
    }

    function renderLedger(rows) {

        let html = '';
        let runningBalance = 0;

        if (rows.length == 0) {

            html = `
        <tr>
            <td colspan="7" class="text-center">
                No Transactions Found
            </td>
        </tr>`;

        } else {

            rows.forEach(function(item, index) {

                let debit = '';
                let credit = '';

                if (item.type === 'Sale') {
                    debit = parseFloat(item.amount).toFixed(2);
                    runningBalance += parseFloat(item.amount);
                } else if (item.type === 'Payment') {
                    credit = parseFloat(item.amount).toFixed(2);
                    runningBalance -= parseFloat(item.amount);
                }

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.date}</td>
                <td>${item.reference}</td>
                <td><span class="label label-info">${item.type}</span></td>
                <td class="text-danger">${debit ? '$' + debit : '-'}</td>
                <td class="text-success">${credit ? '$' + credit : '-'}</td>
                <td class="fw-bold">$${runningBalance.toFixed(2)}</td>
            </tr>`;

            });

        }

        $('#ledger_table tbody').html(html);

    }
</script>

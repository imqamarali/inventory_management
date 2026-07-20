<?php
/**
 * PURCHASE RECORDS VIEW
 * ================================================================================
 * PURPOSE: Display all purchase expenses (Cost of Goods Sold)
 *
 * SHOWS:
 * - Order Date
 * - Purchase Order Number
 * - Supplier Name
 * - Amount
 * - Payment Status
 * - Running Total
 * ================================================================================
 */

use yii\helpers\Html;

$this->title = 'Purchase Records';

if (!isset($purchases)) $purchases = [];
if (!isset($from_date)) $from_date = date('Y-m-01');
if (!isset($to_date)) $to_date = date('Y-m-d');
if (!isset($total)) $total = 0;
?>

<div class="main-content">
    <div class="main-content-inner">

        <!-- Breadcrumbs -->
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=finance/finance">Finance</a>
                </li>
                <li class="active">Purchase Records</li>
            </ul>
        </div>

        <!-- Filter Form -->
        <div style="padding-top:10px;padding-left:13px;padding-bottom:15px;">
            <form id="search_form" onsubmit="return false;">
                <select name="status" id="status" class="new-input" style="width:15%;">
                    <option value="">All Status</option>
                    <option value="Approved">Approved</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>

                <input type="date" name="from_date" id="from_date" class="new-input" style="width:12%;" value="<?= $from_date ?>">
                <input type="date" name="to_date" id="to_date" class="new-input" style="width:12%;" value="<?= $to_date ?>">

                <input type="text" name="per_page" id="per_page" value="20" class="new-input" style="width:8%;" placeholder="Records">

                <button type="button" class="btn btn-primary" onclick="searchPurchaseRecords()" style="height:30px;padding:5px 15px;">
                    <i class="fa fa-search"></i> Search
                </button>
            </form>
        </div>

        <!-- Records Table -->
        <div class="widget-main">
            <div style="padding:10px;font-weight:bold;">
                Total Purchases: <span class="text-danger"><?= number_format($total, 2) ?></span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:12%;">Date</th>
                            <th style="width:15%;">PO Number</th>
                            <th style="width:25%;">Supplier</th>
                            <th style="width:13%;">Amount</th>
                            <th style="width:12%;">Status</th>
                            <th style="width:18%;">Running Total</th>
                        </tr>
                    </thead>
                    <tbody id="records_body">
                        <?php
                        $running_total = 0;
                        foreach ($purchases as $key => $item) {
                            $running_total += $item['amount'];
                            $status_class = $item['status'] === 'Completed' ? 'success' : ($item['status'] === 'Approved' ? 'info' : 'danger');
                        ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= Html::encode($item['date']) ?></td>
                                <td><?= Html::encode($item['po_number']) ?></td>
                                <td><?= Html::encode($item['supplier_name']) ?></td>
                                <td class="text-right"><?= number_format($item['amount'], 2) ?></td>
                                <td>
                                    <span class="label label-<?= $status_class ?>" style="font-size:11px;">
                                        <?= $item['status'] ?>
                                    </span>
                                </td>
                                <td class="text-right"><?= number_format($running_total, 2) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($purchases)) { ?>
                <div class="text-center" style="padding:20px;">
                    <p class="text-muted">No purchase records found for the selected period.</p>
                </div>
            <?php } ?>
        </div>

    </div><!-- main-content-inner -->
</div><!-- main-content -->

<script>
function searchPurchaseRecords() {
    const from_date = document.getElementById('from_date').value;
    const to_date = document.getElementById('to_date').value;
    const status = document.getElementById('status').value;
    const per_page = document.getElementById('per_page').value;

    $.post('index.php?r=finance/purchaserecords', {
        flag: 'search',
        from_date: from_date,
        to_date: to_date,
        status: status,
        per_page: per_page
    }, function(response) {
        if (response.success) {
            let html = '';
            let running = 0;
            response.records.forEach((item, idx) => {
                running += parseFloat(item.amount);
                const sClass = item.status === 'Completed' ? 'success' : (item.status === 'Approved' ? 'info' : 'danger');
                html += `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${item.date}</td>
                        <td>${item.po_number}</td>
                        <td>${item.supplier_name}</td>
                        <td class="text-right">${parseFloat(item.amount).toFixed(2)}</td>
                        <td><span class="label label-${sClass}" style="font-size:11px;">${item.status}</span></td>
                        <td class="text-right">${running.toFixed(2)}</td>
                    </tr>
                `;
            });
            $('#records_body').html(html);
        } else {
            alert('Error: ' + response.message);
        }
    }, 'json');
}
</script>

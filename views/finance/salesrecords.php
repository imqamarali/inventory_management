<?php
/**
 * SALES RECORDS VIEW
 * ================================================================================
 * PURPOSE: Display all sales revenue (Sales Orders + POS Sales)
 *
 * SHOWS:
 * - Order/Sale Date
 * - Reference Number
 * - Customer/Type
 * - Amount
 * - Payment Status
 * - Running Total
 * ================================================================================
 */

use yii\helpers\Html;

$this->title = 'Sales Records';

if (!isset($sales)) $sales = [];
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
                <li class="active">Sales Records</li>
            </ul>
        </div>

        <!-- Filter Form -->
        <div style="padding-top:10px;padding-left:13px;padding-bottom:15px;">
            <form id="search_form" onsubmit="return false;">
                <select name="sale_type" id="sale_type" class="new-input" style="width:15%;">
                    <option value="">All Sales</option>
                    <option value="Order">Sales Orders</option>
                    <option value="POS">POS Sales</option>
                </select>

                <input type="date" name="from_date" id="from_date" class="new-input" style="width:12%;" value="<?= $from_date ?>">
                <input type="date" name="to_date" id="to_date" class="new-input" style="width:12%;" value="<?= $to_date ?>">

                <input type="text" name="per_page" id="per_page" value="20" class="new-input" style="width:8%;" placeholder="Records">

                <button type="button" class="btn btn-primary" onclick="searchSalesRecords()" style="height:30px;padding:5px 15px;">
                    <i class="fa fa-search"></i> Search
                </button>
            </form>
        </div>

        <!-- Records Table -->
        <div class="widget-main">
            <div style="padding:10px;font-weight:bold;">
                Total Sales: <span class="text-success"><?= number_format($total, 2) ?></span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:12%;">Date</th>
                            <th style="width:15%;">Reference</th>
                            <th style="width:25%;">Customer/Type</th>
                            <th style="width:13%;">Amount</th>
                            <th style="width:12%;">Payment Status</th>
                            <th style="width:18%;">Running Total</th>
                        </tr>
                    </thead>
                    <tbody id="records_body">
                        <?php
                        $running_total = 0;
                        foreach ($sales as $key => $item) {
                            $running_total += $item['amount'];
                            $payment_class = $item['payment_status'] === 'Paid' ? 'success' : ($item['payment_status'] === 'Partial' ? 'warning' : 'danger');
                        ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= Html::encode($item['date']) ?></td>
                                <td><?= Html::encode($item['reference_no']) ?></td>
                                <td><?= Html::encode($item['customer_name']) ?></td>
                                <td class="text-right"><?= number_format($item['amount'], 2) ?></td>
                                <td>
                                    <span class="label label-<?= $payment_class ?>" style="font-size:11px;">
                                        <?= $item['payment_status'] ?>
                                    </span>
                                </td>
                                <td class="text-right"><?= number_format($running_total, 2) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($sales)) { ?>
                <div class="text-center" style="padding:20px;">
                    <p class="text-muted">No sales records found for the selected period.</p>
                </div>
            <?php } ?>
        </div>

    </div><!-- main-content-inner -->
</div><!-- main-content -->

<script>
function searchSalesRecords() {
    const from_date = document.getElementById('from_date').value;
    const to_date = document.getElementById('to_date').value;
    const sale_type = document.getElementById('sale_type').value;
    const per_page = document.getElementById('per_page').value;

    $.post('index.php?r=finance/salesrecords', {
        flag: 'search',
        from_date: from_date,
        to_date: to_date,
        sale_type: sale_type,
        per_page: per_page
    }, function(response) {
        if (response.success) {
            let html = '';
            let running = 0;
            response.records.forEach((item, idx) => {
                running += parseFloat(item.amount);
                const pClass = item.payment_status === 'Paid' ? 'success' : (item.payment_status === 'Partial' ? 'warning' : 'danger');
                html += `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${item.date}</td>
                        <td>${item.reference_no}</td>
                        <td>${item.customer_name}</td>
                        <td class="text-right">${parseFloat(item.amount).toFixed(2)}</td>
                        <td><span class="label label-${pClass}" style="font-size:11px;">${item.payment_status}</span></td>
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

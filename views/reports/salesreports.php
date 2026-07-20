<?php
use yii\helpers\Html;

if (!isset($customers)) $customers = [];
if (!isset($warehouses)) $warehouses = [];
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li><i class="ace-icon fa fa-home home-icon"></i><a href="index.php?r=reports/reports">Reports</a></li>
                <li class="active">Sales Reports</li>
                <li style="float:right;">
                    <button class="btn btn-sm btn-primary" onclick="exportReport()" style="font-size:12px;">
                        <i class="ace-icon fa fa-download"></i> Export Excel
                    </button>
                </li>
            </ul>
        </div>

        <div style="padding:15px; background:#f5f5f5; border-radius:4px; margin-bottom:15px;">
            <form id="filter_form">
                <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                    <input type="date" name="from_date" class="new-input" style="flex:1; min-width:120px; height:32px;">
                    <input type="date" name="to_date" class="new-input" style="flex:1; min-width:120px; height:32px;">
                    <select name="customer_id" class="new-input" style="flex:1; min-width:150px; height:32px;">
                        <option value="">All Customers</option>
                        <?php foreach ($customers as $row) {
                            $name = $row['company_name'] ?? trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
                        ?>
                            <option value="<?= $row['id'] ?>"><?= Html::encode($name) ?></option>
                        <?php } ?>
                    </select>
                    <button type="button" class="btn btn-primary" onclick="loadReport()" style="height:32px; padding:0 20px;">
                        <i class="ace-icon fa fa-search"></i> Generate
                    </button>
                </div>
            </form>
        </div>

        <div id="report_container" class="widget-main">
            <div class="alert alert-info text-center">
                <i class="ace-icon fa fa-shopping-bag fa-3x" style="color:#6FB3E0;"></i>
                <h4 style="margin-top:15px;">No data to display</h4>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        let currentReportData = [];

        window.loadReport = function loadReport() {
        const formData = new FormData(document.getElementById('filter_form'));
        const data = new URLSearchParams(formData);
        data.append('flag', 'load');

        Swal.fire({title: 'Loading Report...', text: 'Processing data', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
        fetch('index.php?r=reports/salesreports', {method: 'POST', body: data})
            .then(r => r.json())
            .then(d => {
                Swal.close();
                if (d.success) {
                    currentReportData = d.rows || [];
                    renderTable(d.rows, d.summary);
                } else Swal.fire('Error', d.message, 'error');
            })
            .catch(e => {Swal.close(); Swal.fire('Error', e.message, 'error');});
    }
    function renderTable(rows, summary) {
        let html = '';
        if (summary) {
            html += `<div class="stats-grid">

                <div class="stat-card blue">

                    <div class="stat-header">

                        <span class="stat-title">
                            Total Orders
                        </span>

                        <div class="stat-icon">
                            <i class="fa fa-shopping-bag"></i>
                        </div>

                    </div>

                    <div class="stat-value">
                        ${summary.total_orders || 0}
                    </div>

                    <div class="stat-subtitle">
                        Sales Orders
                    </div>

                </div>


                <div class="stat-card orange">

                    <div class="stat-header">

                        <span class="stat-title">
                            Total Amount
                        </span>

                        <div class="stat-icon">
                            <i class="fa fa-money"></i>
                        </div>

                    </div>

                    <div class="stat-value">
                        ₨ ${(summary.total_amount || 0).toLocaleString('en-PK', {minimumFractionDigits: 0})}
                    </div>

                    <div class="stat-subtitle">
                        Grand Total Value
                    </div>

                </div>


                <div class="stat-card teal">

                    <div class="stat-header">

                        <span class="stat-title">
                            Average Amount
                        </span>

                        <div class="stat-icon">
                            <i class="fa fa-calculator"></i>
                        </div>

                    </div>

                    <div class="stat-value">
                        ₨ ${(summary.average_amount || 0).toLocaleString('en-PK', {minimumFractionDigits: 0})}
                    </div>

                    <div class="stat-subtitle">
                        Avg Per Order
                    </div>

                </div>

            </div>`;
        }
        html += '<div class="table-responsive"><table class="table table-striped table-bordered table-hover"><thead><tr>';
        html += '<th style="width:5%;">#</th><th>Order Number</th><th>Customer</th><th>Warehouse</th><th>Date</th><th>Status</th><th>Payment</th>';
        html += '<th class="text-right">Subtotal</th><th class="text-right">Discount</th><th class="text-right">Tax</th><th class="text-right">Shipping</th><th class="text-right">Grand Total</th>';
        html += '</tr></thead><tbody>';
        if (rows && rows.length > 0) {
            rows.forEach((row, idx) => {
                const customer = row.company_name || (row.first_name || '') + ' ' + (row.last_name || '');
                html += `<tr><td>${idx + 1}</td><td><strong>${htmlEscape(row.order_number)}</strong></td><td>${htmlEscape(customer)}</td><td>${htmlEscape(row.warehouse_name || 'N/A')}</td><td>${row.order_date}</td>`;
                html += `<td><span class="label label-${row.order_status === 'Delivered' ? 'success' : 'info'}">${row.order_status}</span></td>`;
                html += `<td><span class="label label-${row.payment_status === 'Paid' ? 'success' : 'warning'}">${row.payment_status}</span></td>`;
                html += `<td class="text-right">₨ ${parseFloat(row.subtotal).toFixed(2)}</td>`;
                html += `<td class="text-right">₨ ${parseFloat(row.discount).toFixed(2)}</td>`;
                html += `<td class="text-right">₨ ${parseFloat(row.tax).toFixed(2)}</td>`;
                html += `<td class="text-right">₨ ${parseFloat(row.shipping).toFixed(2)}</td>`;
                html += `<td class="text-right"><strong>₨ ${parseFloat(row.grand_total).toFixed(2)}</strong></td></tr>`;
            });
        } else {
            html += '<tr><td colspan="12" class="text-center">No data</td></tr>';
        }
        html += '</tbody></table></div>';
        document.getElementById('report_container').innerHTML = html;
    }

    window.exportReport = function exportReport() {
        if (currentReportData.length === 0) {Swal.fire('Info', 'Generate report first', 'info'); return;}
        const filters = new URLSearchParams(new FormData(document.getElementById('filter_form')));
        window.location.href = `index.php?r=reports/exportsalesreport&${filters.toString()}`;
    };

    function htmlEscape(text) {
        if (!text) return '';
        const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
    })();
</script>

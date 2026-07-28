<?php
use yii\helpers\Html;

if (!isset($suppliers)) $suppliers = [];
if (!isset($warehouses)) $warehouses = [];
?>

<div class="main-content">
    <div class="main-content-inner">
        <form id="filter_form" style="display:none;">
            <input type="hidden" name="from_date">
            <input type="hidden" name="to_date">
            <input type="hidden" name="supplier_id">
        </form>

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <li style="display:flex; align-items:center; gap:10px; min-width:300px;">
                    <a href="index.php?r=inventory/reports" style="color: #0f4c29;">
                        <i class="fa fa-file-text"></i> Reports</a>
                    <span style="margin:0 5px;">›</span>
                    <span class="active">Purchase Reports</span>
                </li>
                <li style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                    <input type="text" id="searchInput" placeholder="Search..."
                        style="height:32px; font-size:12px; min-width:120px; padding:6px 10px; border:1px solid #ddd; border-radius:3px;">

                    <input type="date" id="fromDateFilter" form="filter_form" name="from_date"
                        style="height:32px; font-size:12px; min-width:120px; padding:6px 10px; border:1px solid #ddd; border-radius:3px;">

                    <input type="date" id="toDateFilter" form="filter_form" name="to_date"
                        style="height:32px; font-size:12px; min-width:120px; padding:6px 10px; border:1px solid #ddd; border-radius:3px;">

                    <select id="supplierFilter" form="filter_form" name="supplier_id"
                        style="height:32px; font-size:12px; min-width:130px; padding:6px 10px; border:1px solid #ddd; border-radius:3px;">
                        <option value="">All Suppliers</option>
                        <?php foreach ($suppliers as $row) { ?>
                            <option value="<?= $row['id'] ?>"><?= Html::encode($row['company_name']) ?></option>
                        <?php } ?>
                    </select>

                    <button type="button" onclick="loadReport()">
                        <i class="ace-icon fa fa-search"></i> Generate
                    </button>

                    <button onclick="exportReport()">
                        <i class="ace-icon fa fa-download"></i> Export Excel
                    </button>
                </li>
            </ul>
        </div>

        <div id="report_container" class="widget-main">
            <div id="print_header" class="print-header" style="display:none;">
                <h2 style="margin:0; padding:0; font-size:18px;">Purchase Report</h2>
                <div id="print_filters" class="print-filters" style="margin-top:10px;"></div>
            </div>
            <div class="alert alert-info text-center">
                <i class="ace-icon fa fa-shopping-cart fa-3x" style="color:#6FB3E0;"></i>
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

        Swal.fire({title: 'Loading...', text: 'Please wait', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
        fetch('index.php?r=reports/purchasereports', {method: 'POST', body: data})
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
                            <i class="fa fa-shopping-cart"></i>
                        </div>

                    </div>

                    <div class="stat-value">
                        ${summary.total_orders || 0}
                    </div>

                    <div class="stat-subtitle">
                        Purchase Orders
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
        html += '<th style="width:5%;">#</th><th>PO Number</th><th>Supplier</th><th>Warehouse</th><th>Date</th><th>Status</th>';
        html += '<th class="text-right">Subtotal</th><th class="text-right">Discount</th><th class="text-right">Tax</th><th class="text-right">Freight</th><th class="text-right">Grand Total</th>';
        html += '</tr></thead><tbody>';
        if (rows && rows.length > 0) {
            rows.forEach((row, idx) => {
                const statusColor = {'Draft': 'default', 'Approved': 'info', 'Partially Received': 'warning', 'Completed': 'success', 'Cancelled': 'danger'}[row.status] || 'default';
                html += `<tr><td>${idx + 1}</td><td><strong>${htmlEscape(row.po_number)}</strong></td><td>${htmlEscape(row.company_name || 'N/A')}</td><td>${htmlEscape(row.warehouse_name || 'N/A')}</td><td>${row.order_date}</td><td><span class="label label-${statusColor}">${row.status}</span></td>`;
                html += `<td class="text-right">₨ ${parseFloat(row.subtotal).toFixed(2)}</td>`;
                html += `<td class="text-right">₨ ${parseFloat(row.discount).toFixed(2)}</td>`;
                html += `<td class="text-right">₨ ${parseFloat(row.tax).toFixed(2)}</td>`;
                html += `<td class="text-right">₨ ${parseFloat(row.freight).toFixed(2)}</td>`;
                html += `<td class="text-right"><strong>₨ ${parseFloat(row.grand_total).toFixed(2)}</strong></td></tr>`;
            });
        } else {
            html += '<tr><td colspan="11" class="text-center">No data</td></tr>';
        }
        html += '</tbody></table></div>';
        document.getElementById('report_container').innerHTML = html;
    }

    window.exportReport = function exportReport() {
        if (currentReportData.length === 0) {Swal.fire('Info', 'Generate report first', 'info'); return;}
        const filters = new URLSearchParams(new FormData(document.getElementById('filter_form')));
        window.location.href = `index.php?r=reports/exportpurchasereport&${filters.toString()}`;
    };

    function htmlEscape(text) {
        if (!text) return '';
        const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
    })();
</script>

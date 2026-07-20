<?php
use yii\helpers\Html;
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li><i class="ace-icon fa fa-home home-icon"></i><a href="index.php?r=reports/reports">Reports</a></li>
                <li class="active">Product Performance</li>
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
                    <button type="button" class="btn btn-primary" onclick="loadReport()" style="height:32px; padding:0 20px;">
                        <i class="ace-icon fa fa-search"></i> Generate
                    </button>
                </div>
            </form>
        </div>

        <div id="report_container" class="widget-main">
            <div class="alert alert-info text-center">
                <i class="ace-icon fa fa-trophy fa-3x" style="color:#6FB3E0;"></i>
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

            Swal.fire({title: 'Analyzing Products...', text: 'Loading performance data', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
            fetch('index.php?r=reports/productperformance', {method: 'POST', body: new URLSearchParams(data)})
                .then(r => r.json())
                .then(d => {
                    Swal.close();
                    if (d.success) {
                        currentReportData = d.rows || [];
                        renderTable(d.rows);
                    } else Swal.fire('Error', d.message, 'error');
                })
                .catch(e => {Swal.close(); Swal.fire('Error', e.message, 'error');});
        };

        function renderTable(rows) {
            let html = '<div class="table-responsive"><table class="table table-striped table-bordered table-hover"><thead><tr>';
            html += '<th style="width:5%;">#</th><th>Product Name</th><th>SKU</th><th class="text-right">Qty Sold</th>';
            html += '<th class="text-right">Total Revenue</th><th class="text-right">Total Orders</th>';
            html += '</tr></thead><tbody>';
            if (rows && rows.length > 0) {
                rows.forEach((row, idx) => {
                    html += `<tr><td>${idx + 1}</td><td><strong>${htmlEscape(row.product_name)}</strong><br><small>${htmlEscape(row.sku)}</small></td><td>${htmlEscape(row.sku)}</td>`;
                    html += `<td class="text-right">${parseFloat(row.total_quantity_sold).toFixed(2)}</td>`;
                    html += `<td class="text-right">₨ ${parseFloat(row.total_revenue).toLocaleString('en-PK', {minimumFractionDigits: 2})}</td>`;
                    html += `<td class="text-right">${row.total_orders}</td></tr>`;
                });
            } else {
                html += '<tr><td colspan="6" class="text-center">No data</td></tr>';
            }
            html += '</tbody></table></div>';
            document.getElementById('report_container').innerHTML = html;
        }

        window.exportReport = function exportReport() {
            if (currentReportData.length === 0) {Swal.fire('Info', 'Generate report first', 'info'); return;}
            const filters = new URLSearchParams(new FormData(document.getElementById('filter_form')));
            window.location.href = `index.php?r=reports/exportproductperformance&${filters.toString()}`;
        };

        function htmlEscape(text) {
            if (!text) return '';
            const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }
    })();
</script>

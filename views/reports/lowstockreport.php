<?php
use yii\helpers\Html;

if (!isset($warehouses)) $warehouses = [];
?>

<div class="main-content">
    <div class="main-content-inner">
        <form id="filter_form" style="display:none;">
            <input type="hidden" name="warehouse_id">
        </form>

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <li style="display:flex; align-items:center; gap:10px; min-width:300px;">
                    <a href="index.php?r=inventory/reports" style="color: #0f4c29;">
                        <i class="fa fa-file-text"></i> Reports</a>
                    <span style="margin:0 5px;">›</span>
                    <span class="active">Low Stock Report</span>
                </li>
                <li style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                    <input type="text" id="searchInput" placeholder="Search..."
                        style="height:32px; font-size:12px; min-width:120px; padding:6px 10px; border:1px solid #ddd; border-radius:3px;">

                    <select id="warehouseFilter" form="filter_form" name="warehouse_id"
                        style="height:32px; font-size:12px; min-width:130px; padding:6px 10px; border:1px solid #ddd; border-radius:3px;">
                        <option value="">All Warehouses</option>
                        <?php foreach ($warehouses as $row) { ?>
                            <option value="<?= $row['id'] ?>"><?= Html::encode($row['warehouse_name']) ?></option>
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
            <div class="alert alert-warning text-center">
                <i class="ace-icon fa fa-warning fa-3x" style="color:#FFC107;"></i>
                <h4 style="margin-top:15px;">No data to display</h4>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        let currentReportData = [];

        window.loadReport = function loadReport() {
            const data = new URLSearchParams(new FormData(document.getElementById('filter_form')));
            data.append('flag', 'load');
            Swal.fire({title: 'Loading...', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
            fetch('index.php?r=reports/lowstockreport', {method: 'POST', body: new URLSearchParams(data)})
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
            html += '<th style="width:5%;">#</th><th>Product</th><th>SKU</th><th>Warehouse</th><th class="text-right">Current Qty</th>';
            html += '<th class="text-right">Reorder Level</th><th class="text-right">Min Stock</th><th>Status</th>';
            html += '</tr></thead><tbody>';
            if (rows && rows.length > 0) {
                rows.forEach((row, idx) => {
                    const status = row.quantity <= 0 ? '<span class="label label-danger">Out of Stock</span>' : '<span class="label label-warning">Low Stock</span>';
                    html += `<tr><td>${idx + 1}</td><td><strong>${htmlEscape(row.product_name)}</strong><br><small>${htmlEscape(row.sku)}</small></td><td>${htmlEscape(row.sku)}</td><td>${htmlEscape(row.warehouse_name)}</td>`;
                    html += `<td class="text-right">${parseFloat(row.quantity).toFixed(2)}</td>`;
                    html += `<td class="text-right">${parseFloat(row.reorder_level).toFixed(2)}</td>`;
                    html += `<td class="text-right">${parseFloat(row.minimum_stock).toFixed(2)}</td>`;
                    html += `<td>${status}</td></tr>`;
                });
            } else {
                html += '<tr><td colspan="8" class="text-center">No low stock items</td></tr>';
            }
            html += '</tbody></table></div>';
            document.getElementById('report_container').innerHTML = html;
        }

        window.exportReport = function exportReport() {
            if (currentReportData.length === 0) {Swal.fire('Info', 'Generate report first', 'info'); return;}
            const filters = new URLSearchParams(new FormData(document.getElementById('filter_form')));
            window.location.href = `index.php?r=reports/exportlowstock&${filters.toString()}`;
        };

        function htmlEscape(text) {
            if (!text) return '';
            const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }
    })();
</script>

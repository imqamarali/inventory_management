<?php
use yii\helpers\Html;
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li><i class="ace-icon fa fa-home home-icon"></i><a href="index.php?r=reports/reports">Reports</a></li>
                <li class="active">Warehouse Reports</li>
                <li style="float:right;">
                    <button class="btn btn-sm btn-primary" onclick="exportReport()" style="font-size:12px;">
                        <i class="ace-icon fa fa-download"></i> Export Excel
                    </button>
                </li>
            </ul>
        </div>

        <div style="padding:15px; background:#f5f5f5; border-radius:4px; margin-bottom:15px;">
            <button type="button" class="btn btn-primary" onclick="loadReport()" style="height:32px; padding:0 20px;">
                <i class="ace-icon fa fa-refresh"></i> Refresh Report
            </button>
        </div>

        <div id="report_container" class="widget-main">
            <div class="alert alert-info text-center">
                <i class="ace-icon fa fa-building fa-3x" style="color:#6FB3E0;"></i>
                <h4 style="margin-top:15px;">Click refresh to load warehouse data</h4>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        let currentReportData = [];

        window.loadReport = function loadReport() {
            Swal.fire({title: 'Loading...', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
            fetch('index.php?r=reports/warehousereports', {
                method: 'POST',
                body: new URLSearchParams({flag: 'load'})
            })
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
            let totalProducts = 0, totalQty = 0, totalVal = 0;
            if (rows && rows.length > 0) {
                rows.forEach(row => {
                    totalProducts += row.total_products || 0;
                    totalQty += parseFloat(row.total_quantity || 0);
                    totalVal += parseFloat(row.total_value || 0);
                });
            }

            let html = `<div class="stats-grid">

                <div class="stat-card blue">

                    <div class="stat-header">

                        <span class="stat-title">
                            Total Products
                        </span>

                        <div class="stat-icon">
                            <i class="fa fa-cubes"></i>
                        </div>

                    </div>

                    <div class="stat-value">
                        ${totalProducts}
                    </div>

                    <div class="stat-subtitle">
                        Product Types
                    </div>

                </div>


                <div class="stat-card orange">

                    <div class="stat-header">

                        <span class="stat-title">
                            Total Quantity
                        </span>

                        <div class="stat-icon">
                            <i class="fa fa-bars"></i>
                        </div>

                    </div>

                    <div class="stat-value">
                        ${totalQty.toFixed(2)}
                    </div>

                    <div class="stat-subtitle">
                        Units in Stock
                    </div>

                </div>


                <div class="stat-card teal">

                    <div class="stat-header">

                        <span class="stat-title">
                            Total Value
                        </span>

                        <div class="stat-icon">
                            <i class="fa fa-money"></i>
                        </div>

                    </div>

                    <div class="stat-value">
                        ₨ ${totalVal.toLocaleString('en-PK', {minimumFractionDigits: 0})}
                    </div>

                    <div class="stat-subtitle">
                        Warehouse Value
                    </div>

                </div>

            </div>`;

            html += '<div class="table-responsive"><table class="table table-striped table-bordered table-hover"><thead><tr>';
            html += '<th style="width:5%;">#</th><th>Warehouse Code</th><th>Warehouse Name</th><th class="text-right">Total Products</th>';
            html += '<th class="text-right">Total Quantity</th><th class="text-right">Total Value</th>';
            html += '</tr></thead><tbody>';
            if (rows && rows.length > 0) {
                rows.forEach((row, idx) => {
                    html += `<tr><td>${idx + 1}</td><td>${htmlEscape(row.warehouse_code)}</td><td><strong>${htmlEscape(row.warehouse_name)}</strong></td>`;
                    html += `<td class="text-right">${row.total_products || 0}</td>`;
                    html += `<td class="text-right">${parseFloat(row.total_quantity).toFixed(2)}</td>`;
                    html += `<td class="text-right">₨ ${parseFloat(row.total_value).toLocaleString('en-PK', {minimumFractionDigits: 2})}</td></tr>`;
                });
            } else {
                html += '<tr><td colspan="6" class="text-center">No data</td></tr>';
            }
            html += '</tbody></table></div>';
            document.getElementById('report_container').innerHTML = html;
        }

        window.exportReport = function exportReport() {
            if (currentReportData.length === 0) {Swal.fire('Info', 'Generate report first', 'info'); return;}
            window.location.href = 'index.php?r=reports/exportwarehousereport';
        };

        function htmlEscape(text) {
            if (!text) return '';
            const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }
    })();
</script>

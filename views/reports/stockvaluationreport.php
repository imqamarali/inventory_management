<?php
use yii\helpers\Html;

if (!isset($warehouses)) $warehouses = [];
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li><i class="ace-icon fa fa-home home-icon"></i><a href="index.php?r=reports/reports">Reports</a></li>
                <li class="active">Stock Valuation Report</li>
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
                    <select name="warehouse_id" class="new-input" style="flex:1; min-width:150px; height:32px;">
                        <option value="">All Warehouses</option>
                        <?php foreach ($warehouses as $row) { ?>
                            <option value="<?= $row['id'] ?>"><?= Html::encode($row['warehouse_name']) ?></option>
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
                <i class="ace-icon fa fa-line-chart fa-3x" style="color:#6FB3E0;"></i>
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
            fetch('index.php?r=reports/stockvaluationreport', {method: 'POST', body: new URLSearchParams(data)})
                .then(r => r.json())
                .then(d => {
                    Swal.close();
                    if (d.success) {
                        currentReportData = d.rows || [];
                        renderTable(d.rows, d.summary);
                    } else Swal.fire('Error', d.message, 'error');
                })
                .catch(e => {Swal.close(); Swal.fire('Error', e.message, 'error');});
        };

        function renderTable(rows, summary) {
            let html = '';
            if (summary) {
                html += `<div class="stats-grid">

                    <div class="stat-card blue">

                        <div class="stat-header">

                            <span class="stat-title">
                                Total Items
                            </span>

                            <div class="stat-icon">
                                <i class="fa fa-cubes"></i>
                            </div>

                        </div>

                        <div class="stat-value">
                            ${summary.total_items || 0}
                        </div>

                        <div class="stat-subtitle">
                            Stock Items
                        </div>

                    </div>


                    <div class="stat-card orange">

                        <div class="stat-header">

                            <span class="stat-title">
                                Cost Value
                            </span>

                            <div class="stat-icon">
                                <i class="fa fa-money"></i>
                            </div>

                        </div>

                        <div class="stat-value">
                            ₨ ${(summary.total_cost_value || 0).toLocaleString('en-PK', {minimumFractionDigits: 0})}
                        </div>

                        <div class="stat-subtitle">
                            Total Cost Value
                        </div>

                    </div>


                    <div class="stat-card teal">

                        <div class="stat-header">

                            <span class="stat-title">
                                Retail Value
                            </span>

                            <div class="stat-icon">
                                <i class="fa fa-tags"></i>
                            </div>

                        </div>

                        <div class="stat-value">
                            ₨ ${(summary.total_retail_value || 0).toLocaleString('en-PK', {minimumFractionDigits: 0})}
                        </div>

                        <div class="stat-subtitle">
                            Retail Value
                        </div>

                    </div>

                </div>`;
            }
            html += '<div class="table-responsive"><table class="table table-striped table-bordered table-hover"><thead><tr>';
            html += '<th style="width:5%;">#</th><th>Product</th><th>SKU</th><th>Warehouse</th><th class="text-right">Quantity</th>';
            html += '<th class="text-right">Avg Cost</th><th class="text-right">Last Price</th><th class="text-right">Cost Value</th><th class="text-right">Retail Value</th>';
            html += '</tr></thead><tbody>';
            if (rows && rows.length > 0) {
                rows.forEach((row, idx) => {
                    html += `<tr><td>${idx + 1}</td><td><strong>${htmlEscape(row.product_name)}</strong></td><td>${htmlEscape(row.sku)}</td><td>${htmlEscape(row.warehouse_name)}</td>`;
                    html += `<td class="text-right">${parseFloat(row.quantity).toFixed(2)}</td>`;
                    html += `<td class="text-right">₨ ${parseFloat(row.average_cost).toFixed(2)}</td>`;
                    html += `<td class="text-right">₨ ${parseFloat(row.last_purchase_price).toFixed(2)}</td>`;
                    html += `<td class="text-right">₨ ${parseFloat(row.cost_value).toFixed(2)}</td>`;
                    html += `<td class="text-right">₨ ${parseFloat(row.retail_value).toFixed(2)}</td></tr>`;
                });
            } else {
                html += '<tr><td colspan="9" class="text-center">No data</td></tr>';
            }
            html += '</tbody></table></div>';
            document.getElementById('report_container').innerHTML = html;
        }

        window.exportReport = function exportReport() {
            if (currentReportData.length === 0) {Swal.fire('Info', 'Generate report first', 'info'); return;}
            const filters = new URLSearchParams(new FormData(document.getElementById('filter_form')));
            window.location.href = `index.php?r=reports/exportstockvaluation&${filters.toString()}`;
        };

        function htmlEscape(text) {
            if (!text) return '';
            const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }
    })();
</script>

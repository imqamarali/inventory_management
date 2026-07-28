<?php
use yii\helpers\Html;

if (!isset($categories)) $categories = [];
if (!isset($warehouses)) $warehouses = [];
?>

<div class="main-content">
    <div class="main-content-inner">
        <form id="filter_form" style="display:none;">
            <input type="hidden" name="category_id">
            <input type="hidden" name="warehouse_id">
        </form>

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <li style="display:flex; align-items:center; gap:10px; min-width:300px;">
                    <a href="index.php?r=inventory/reports" style="color: #0f4c29;">
                        <i class="fa fa-file-text"></i> Reports</a>
                    <span style="margin:0 5px;">›</span>
                    <span class="active">Inventory Reports</span>
                </li>
                <li style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                    <input type="text" id="searchInput" placeholder="Search..."
                        style="height:32px; font-size:12px; min-width:120px; padding:6px 10px; border:1px solid #ddd; border-radius:3px;">

                    <select id="categoryFilter" form="filter_form" name="category_id"
                        style="height:32px; font-size:12px; min-width:130px; padding:6px 10px; border:1px solid #ddd; border-radius:3px;">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $row) { ?>
                            <option value="<?= $row['id'] ?>"><?= Html::encode($row['category_name']) ?></option>
                        <?php } ?>
                    </select>

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
            <div class="alert alert-info text-center">
                <i class="ace-icon fa fa-database fa-3x" style="color:#6FB3E0;"></i>
                <h4 style="margin-top:15px;">No data to display</h4>
                <p>Click "Generate" to load inventory data</p>
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

            Swal.fire({
                title: 'Loading Report...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('index.php?r=reports/inventoryreports', {
                method: 'POST',
                body: new URLSearchParams(data)
            })
                .then(r => r.json())
                .then(d => {
                    Swal.close();
                    if (d.success) {
                        currentReportData = d.rows || [];
                        renderTable(d.rows, d.summary);
                    } else {
                        Swal.fire('Error', d.message, 'error');
                    }
                })
                .catch(e => {
                    Swal.close();
                    Swal.fire('Error', e.message, 'error');
                });
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
                                <i class="fa fa-database"></i>
                            </div>

                        </div>

                        <div class="stat-value">
                            ${summary.total_items || 0}
                        </div>

                        <div class="stat-subtitle">
                            Inventory Items
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
                            ${(summary.total_quantity || 0).toFixed(2)}
                        </div>

                        <div class="stat-subtitle">
                            Units Available
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
                            ₨ ${(summary.total_value || 0).toLocaleString('en-PK', {minimumFractionDigits: 0})}
                        </div>

                        <div class="stat-subtitle">
                            Inventory Value
                        </div>

                    </div>

                </div>`;
            }

            html += '<div class="table-responsive"><table class="table table-striped table-bordered table-hover">';
            html += '<thead><tr>';
            html += '<th style="width:5%;">#</th><th>Product</th><th>Category</th><th>Warehouse</th>';
            html += '<th class="text-right">Quantity</th><th class="text-right">Reserved</th><th class="text-right">Available</th>';
            html += '<th class="text-right">Avg Cost</th><th class="text-right">Stock Value</th>';
            html += '</tr></thead><tbody>';

            if (rows && rows.length > 0) {
                rows.forEach((row, idx) => {
                    html += `
                        <tr>
                            <td>${idx + 1}</td>
                            <td><strong>${htmlEscape(row.product_name)}</strong><br><small>${htmlEscape(row.sku)}</small></td>
                            <td>${htmlEscape(row.category_name || 'N/A')}</td>
                            <td>${htmlEscape(row.warehouse_name || 'N/A')}</td>
                            <td class="text-right">${parseFloat(row.quantity).toFixed(2)}</td>
                            <td class="text-right">${parseFloat(row.reserved_quantity).toFixed(2)}</td>
                            <td class="text-right">${parseFloat(row.available_quantity).toFixed(2)}</td>
                            <td class="text-right">₨ ${parseFloat(row.average_cost).toFixed(2)}</td>
                            <td class="text-right">₨ ${parseFloat(row.stock_value).toFixed(2)}</td>
                        </tr>
                    `;
                });
            } else {
                html += '<tr><td colspan="9" class="text-center">No data available</td></tr>';
            }

            html += '</tbody></table></div>';
            document.getElementById('report_container').innerHTML = html;
        }

        window.exportReport = function exportReport() {
            if (currentReportData.length === 0) {
                Swal.fire('Info', 'Please generate report first', 'info');
                return;
            }

            const filters = new URLSearchParams(new FormData(document.getElementById('filter_form')));
            const queryString = filters.toString();
            window.location.href = `index.php?r=reports/exportinventoryreport&${queryString}`;
        };

        function htmlEscape(text) {
            if (!text) return '';
            const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }
    })();
</script>

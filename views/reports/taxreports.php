<?php
use yii\helpers\Html;
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li><i class="ace-icon fa fa-home home-icon"></i><a href="index.php?r=reports/reports">Reports</a></li>
                <li class="active">Tax Reports</li>
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
                <i class="ace-icon fa fa-percent fa-3x" style="color:#6FB3E0;"></i>
                <h4 style="margin-top:15px;">No data to display</h4>
            </div>
        </div>
    </div>
</div>


<script>
    (function() {
        window.loadReport = function loadReport() {
            const formData = new FormData(document.getElementById('filter_form'));
            const data = new URLSearchParams(formData);
            data.append('flag', 'load');

            Swal.fire({title: 'Generating Tax Report...', text: 'Processing tax data', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
            fetch('index.php?r=reports/taxreports', {method: 'POST', body: data})
                .then(r => r.json())
                .then(d => {
                    Swal.close();
                    if (d.success) renderReport(d);
                    else Swal.fire('Error', d.message, 'error');
                })
                .catch(e => {Swal.close(); Swal.fire('Error', e.message, 'error');});
        };

        function renderReport(data) {
            let html = `<div class="stats-grid">

                <div class="stat-card blue">

                    <div class="stat-header">

                        <span class="stat-title">
                            Sales Tax
                        </span>

                        <div class="stat-icon">
                            <i class="fa fa-shopping-bag"></i>
                        </div>

                    </div>

                    <div class="stat-value">
                        ₨ ${(data.sales_tax || 0).toLocaleString('en-PK', {minimumFractionDigits: 0})}
                    </div>

                    <div class="stat-subtitle">
                        Sales Tax
                    </div>

                </div>


                <div class="stat-card orange">

                    <div class="stat-header">

                        <span class="stat-title">
                            Purchase Tax
                        </span>

                        <div class="stat-icon">
                            <i class="fa fa-shopping-cart"></i>
                        </div>

                    </div>

                    <div class="stat-value">
                        ₨ ${(data.purchase_tax || 0).toLocaleString('en-PK', {minimumFractionDigits: 0})}
                    </div>

                    <div class="stat-subtitle">
                        Purchase Tax
                    </div>

                </div>


                <div class="stat-card teal">

                    <div class="stat-header">

                        <span class="stat-title">
                            Net Tax
                        </span>

                        <div class="stat-icon">
                            <i class="fa fa-percent"></i>
                        </div>

                    </div>

                    <div class="stat-value">
                        ₨ ${(data.net_tax || 0).toLocaleString('en-PK', {minimumFractionDigits: 0})}
                    </div>

                    <div class="stat-subtitle">
                        Net Tax
                    </div>

                </div>

            </div>`;

            if (data.monthly && data.monthly.length > 0) {
                html += '<div style="margin-top:20px;"><h4>Monthly Tax Breakdown</h4>';
                html += '<div class="table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>Month</th><th class="text-right">Sales Tax</th><th class="text-right">Purchase Tax</th><th class="text-right">Net Tax</th></tr></thead><tbody>';
                data.monthly.forEach(row => {
                    const net = (parseFloat(row.sales_tax || 0) - parseFloat(row.purchase_tax || 0));
                    html += `<tr><td><strong>${row.month}</strong></td>`;
                    html += `<td class="text-right">₨ ${parseFloat(row.sales_tax || 0).toLocaleString('en-PK', {minimumFractionDigits: 2})}</td>`;
                    html += `<td class="text-right">₨ ${parseFloat(row.purchase_tax || 0).toLocaleString('en-PK', {minimumFractionDigits: 2})}</td>`;
                    html += `<td class="text-right"><strong>₨ ${net.toLocaleString('en-PK', {minimumFractionDigits: 2})}</strong></td></tr>`;
                });
                html += '</tbody></table></div></div>';
            }

            document.getElementById('report_container').innerHTML = html;
        }
    })();
</script>

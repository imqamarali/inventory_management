<?php
use yii\helpers\Html;
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li><i class="ace-icon fa fa-home home-icon"></i><a href="index.php?r=reports/reports">Reports</a></li>
                <li class="active">Financial Reports</li>
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
                <i class="ace-icon fa fa-money fa-3x" style="color:#6FB3E0;"></i>
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

            Swal.fire({title: 'Generating Financial Report...', text: 'Loading data', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
            fetch('index.php?r=reports/financialreports', {method: 'POST', body: data})
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
                            Total Income
                        </span>

                        <div class="stat-icon">
                            <i class="fa fa-arrow-up"></i>
                        </div>

                    </div>

                    <div class="stat-value">
                        ₨ ${(data.income || 0).toLocaleString('en-PK', {minimumFractionDigits: 0})}
                    </div>

                    <div class="stat-subtitle">
                        Income Generated
                    </div>

                </div>


                <div class="stat-card orange">

                    <div class="stat-header">

                        <span class="stat-title">
                            Total Expense
                        </span>

                        <div class="stat-icon">
                            <i class="fa fa-arrow-down"></i>
                        </div>

                    </div>

                    <div class="stat-value">
                        ₨ ${(data.expense || 0).toLocaleString('en-PK', {minimumFractionDigits: 0})}
                    </div>

                    <div class="stat-subtitle">
                        Expenses Incurred
                    </div>

                </div>


                <div class="stat-card teal">

                    <div class="stat-header">

                        <span class="stat-title">
                            Net Profit/Loss
                        </span>

                        <div class="stat-icon">
                            <i class="fa fa-balance-scale"></i>
                        </div>

                    </div>

                    <div class="stat-value">
                        ₨ ${(data.net || 0).toLocaleString('en-PK', {minimumFractionDigits: 0})}
                    </div>

                    <div class="stat-subtitle">
                        Net Position
                    </div>

                </div>

            </div>`;

            if (data.accountTypeSummary && data.accountTypeSummary.length > 0) {
                html += '<div style="margin-top:20px;"><h4>Account Summary</h4>';
                html += '<div class="table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>Account Type</th><th class="text-right">Balance</th></tr></thead><tbody>';
                data.accountTypeSummary.forEach(row => {
                    html += `<tr><td><strong>${row.account_type}</strong></td><td class="text-right">₨ ${parseFloat(row.total || 0).toLocaleString('en-PK', {minimumFractionDigits: 2})}</td></tr>`;
                });
                html += '</tbody></table></div></div>';
            }

            document.getElementById('report_container').innerHTML = html;
        }
    })();
</script>

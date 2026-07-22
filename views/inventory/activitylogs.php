<?php
use yii\helpers\Html;
if (!isset($modules)) $modules = [];
if (!isset($activities)) $activities = [];
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li><i class="ace-icon fa fa-home home-icon"></i><a href="index.php?r=inventory/dashboard">Home</a></li>
                <li class="active">Activity Logs</li>
            </ul>
        </div>

        <div style="padding:15px; background:#f5f5f5; border-radius:4px; margin-bottom:15px;">
            <form id="filter_form">
                <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                    <input type="date" name="date_from" class="new-input" style="flex:1; min-width:120px; height:32px;">
                    <input type="date" name="date_to" class="new-input" style="flex:1; min-width:120px; height:32px;">
                    <select name="module" class="new-input" style="flex:1; min-width:150px; height:32px;">
                        <option value="">All Modules</option>
                        <?php foreach ($modules as $mod): ?>
                            <option value="<?= Html::encode($mod) ?>"><?= Html::encode($mod) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="activity" class="new-input" style="flex:1; min-width:150px; height:32px;">
                        <option value="">All Activities</option>
                        <?php foreach ($activities as $act): ?>
                            <option value="<?= Html::encode($act) ?>"><?= Html::encode($act) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="search" class="new-input" placeholder="Search IP/User" style="flex:1; min-width:120px; height:32px;">
                    <button type="button" class="btn btn-primary" onclick="loadReport()" style="height:32px; padding:0 20px;">
                        <i class="ace-icon fa fa-search"></i> Generate
                    </button>
                </div>
            </form>
            <div id="total_records_info" style="margin-top: 10px; font-size: 12px; color: #7f8c8d; display: none;">
                <i class="fa fa-info-circle"></i> <span id="total_records_count">0</span> total activity records in system
            </div>
        </div>

        <div id="report_container" class="widget-main">
            <div class="alert alert-info text-center">
                <i class="ace-icon fa fa-history fa-3x" style="color:#6FB3E0;"></i>
                <h4 style="margin-top:15px;">No data to display</h4>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    let currentReportData = [];
    const activityTypeColors = {
        'create': 'success',
        'update': 'warning',
        'delete': 'danger',
        'view': 'primary',
        'login': 'success',
        'logout': 'info'
    };

    window.loadReport = function loadReport() {
        const formData = new FormData(document.getElementById('filter_form'));
        const data = new URLSearchParams(formData);
        data.append('flag', 'load');
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');

        Swal.fire({title: 'Loading Activity Logs...', text: 'Processing data', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
        fetch('index.php?r=inventory/activitylogs', {method: 'POST', body: data})
            .then(r => r.json())
            .then(d => {
                Swal.close();
                if (d.success) {
                    currentReportData = d.logs || [];
                    renderTable(d.logs, d.summary, d.page, d.perPage, d.total);
                } else Swal.fire('Error', d.message || 'Failed to load logs', 'error');
            })
            .catch(e => {Swal.close(); Swal.fire('Error', e.message, 'error');});
    };

    function renderTable(rows, summary, page = 1, perPage = 20, total = 0) {
        let html = '';
        if (summary) {
            html += `<div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-header">
                        <span class="stat-title">Total Logs</span>
                        <div class="stat-icon"><i class="fa fa-history"></i></div>
                    </div>
                    <div class="stat-value">${summary.total_logs || 0}</div>
                    <div class="stat-subtitle">Activity Records</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-header">
                        <span class="stat-title">Modules</span>
                        <div class="stat-icon"><i class="fa fa-cubes"></i></div>
                    </div>
                    <div class="stat-value">${summary.total_modules || 0}</div>
                    <div class="stat-subtitle">Active Modules</div>
                </div>
                <div class="stat-card teal">
                    <div class="stat-header">
                        <span class="stat-title">Users</span>
                        <div class="stat-icon"><i class="fa fa-users"></i></div>
                    </div>
                    <div class="stat-value">${summary.total_users || 0}</div>
                    <div class="stat-subtitle">Active Users</div>
                </div>
            </div>`;
        }
        html += '<div class="table-responsive"><table class="table table-striped table-bordered table-hover"><thead><tr>';
        html += '<th style="width:3%;">#</th><th>Date & Time</th><th>User</th><th>Activity</th><th>Module</th><th>Type</th><th>Reference</th><th>Details</th>';
        html += '</tr></thead><tbody>';
        if (rows && rows.length > 0) {
            rows.forEach((row, idx) => {
                const typeColor = activityTypeColors[row.activitytype?.toLowerCase()] || 'default';
                const refLink = row.refid ? `<a href="#" onclick="return false;" title="Record ID: ${htmlEscape(row.refid)}" style="color:#0066cc;">#${htmlEscape(row.refid)}</a>` : '-';
                html += `<tr><td>${idx + 1}</td><td><strong>${htmlEscape(row.formatted_date)}</strong><br><small>${htmlEscape(row.formatted_time)}</small></td>`;
                html += `<td><i class="fa fa-user"></i> ${htmlEscape(row.user_name)}</td>`;
                html += `<td>${htmlEscape(row.activity || '-')}</td>`;
                html += `<td><span class="label label-info" style="font-size:10px;">${htmlEscape(row.module || '-')}</span></td>`;
                html += `<td><span class="label label-${typeColor}" style="font-size:10px;">${htmlEscape(row.activitytype || '-')}</span></td>`;
                html += `<td>${refLink}</td>`;
                html += `<td><button class="btn btn-xs" onclick="showDetails('${escapeJson(JSON.stringify(row))}');" title="View Details"><i class="fa fa-info-circle"></i></button></td></tr>`;
            });
        } else {
            html += '<tr><td colspan="8" class="text-center">No activity logs found</td></tr>';
        }
        html += '</tbody></table></div>';

        // Add pagination info
        if (rows && rows.length > 0) {
            const from = ((page - 1) * perPage) + 1;
            const to = Math.min(from + rows.length - 1, total);
            html += `<div class="pagination-info">Showing ${from} to ${to} of ${total} records</div>`;
        }

        document.getElementById('report_container').innerHTML = html;

        // Update total records info
        if (total > 0) {
            document.getElementById('total_records_count').textContent = total.toLocaleString('en-US');
            document.getElementById('total_records_info').style.display = 'block';
        } else {
            document.getElementById('total_records_info').style.display = 'none';
        }
    }

    function htmlEscape(text) {
        if (!text) return '';
        const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    function escapeJson(str) {
        return str.replace(/'/g, '&#39;');
    }

    window.showDetails = function(jsonStr) {
        try {
            const log = JSON.parse(jsonStr);
            let html = `<table style="width:100%;text-align:left">
                <tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>Date & Time:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3">${htmlEscape(log.formatted_date)} ${htmlEscape(log.formatted_time)}</td></tr>
                <tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>User:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3">${htmlEscape(log.user_name)}</td></tr>
                <tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>Activity:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3">${htmlEscape(log.activity || '-')}</td></tr>
                <tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>Module:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3">${htmlEscape(log.module || '-')}</td></tr>
                <tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>Type:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3"><span class="label label-${activityTypeColors[log.activitytype?.toLowerCase()] || 'default'}">${htmlEscape(log.activitytype || '-')}</span></td></tr>
                <tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>Reference ID:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3">${log.refid ? '<code>' + htmlEscape(log.refid) + '</code>' : '-'}</td></tr>
                <tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>IP Address:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3"><code>${htmlEscape(log.ip_address || '-')}</code></td></tr>
                <tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>User Agent:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3"><small>${htmlEscape((log.user_agent || '-').substring(0, 100))}</small></td></tr>`;

            if (log.additional_data_decoded && Object.keys(log.additional_data_decoded).length > 0) {
                html += `<tr><td style="padding:8px;"><strong>Changes/Details:</strong></td><td style="padding:8px;"><pre style="background:#f9f9f9;padding:8px;border-radius:4px;max-height:300px;overflow:auto;font-size:11px;margin:0;">${htmlEscape(JSON.stringify(log.additional_data_decoded, null, 2))}</pre></td></tr>`;
            }
            html += '</table>';
            Swal.fire({title:'Activity Details',html:html,width:'700px',confirmButtonText:'Close'});
        } catch(e) {
            Swal.fire('Error','Failed to parse activity details','error');
        }
    };

})();
</script>

<style>
.new-input {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 13px;
    font-family: inherit;
    transition: border-color 0.2s;
}

.new-input:focus {
    outline: none;
    border-color: #2196F3;
    box-shadow: 0 0 5px rgba(33,150,243,0.3);
}

.widget-main {
    padding: 15px;
    background: #fff;
    border: 1px solid #e3e9f3;
    border-radius: 4px;
}

.stats-grid {
    margin-bottom: 15px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.stat-card {
    flex: 1;
    min-width: 180px;
    background: white;
    border: 1px solid #e9ecef;
    border-left: 4px solid;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 0;
}

.stat-card.blue { border-left-color: #3498db; }
.stat-card.orange { border-left-color: #e67e22; }
.stat-card.teal { border-left-color: #16a085; }
.stat-card.green { border-left-color: #2ecc71; }
.stat-card.red { border-left-color: #e74c3c; }
.stat-card.purple { border-left-color: #9b59b6; }

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.stat-title {
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 600;
    color: #7f8c8d;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.stat-card.blue .stat-icon { background: rgba(52, 152, 219, 0.1); color: #3498db; }
.stat-card.orange .stat-icon { background: rgba(230, 126, 34, 0.1); color: #e67e22; }
.stat-card.teal .stat-icon { background: rgba(22, 160, 133, 0.1); color: #16a085; }
.stat-card.green .stat-icon { background: rgba(46, 204, 113, 0.1); color: #2ecc71; }
.stat-card.red .stat-icon { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }
.stat-card.purple .stat-icon { background: rgba(155, 89, 182, 0.1); color: #9b59b6; }

.stat-value {
    font-size: 18px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 3px;
    line-height: 1.2;
}

.stat-subtitle {
    font-size: 10px;
    color: #95a5a6;
}

.pagination-info {
    text-align: right;
    padding: 10px 15px;
    font-size: 12px;
    color: #7f8c8d;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
}

.table thead tr {
    background: #f9f9f9;
}

.table th {
    padding: 10px;
    text-align: left;
    font-weight: bold;
    border-bottom: 1px solid #e3e9f3;
    font-size: 12px;
}

.table td {
    padding: 10px;
    border-bottom: 1px solid #e3e9f3;
    font-size: 12px;
}

.table tbody tr:hover {
    background: #f5f5f5;
}

.label {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    font-weight: bold;
    font-size: 10px;
}

.label-success { background: #4CAF50; color: white; }
.label-warning { background: #FF9800; color: white; }
.label-danger { background: #f44336; color: white; }
.label-primary { background: #007bff; color: white; }
.label-info { background: #2196F3; color: white; }
.label-default { background: #e3e9f3; color: #333; }

.btn {
    padding: 6px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    background: #fff;
    transition: all 0.2s;
}

.btn-primary {
    background: #2196F3;
    color: white;
    border-color: #2196F3;
}

.btn:hover {
    opacity: 0.8;
}

.btn-xs {
    padding: 3px 6px;
    font-size: 11px;
}

.text-center {
    text-align: center;
}

.alert {
    padding: 20px;
    border-radius: 4px;
    text-align: center;
}

.alert-info {
    background: #e3f2fd;
    color: #1976d2;
    border: 1px solid #90caf9;
}

code {
    background: #f5f5f5;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
    font-size: 11px;
}
</style>

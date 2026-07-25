<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'System Backup Management';
?>

<div class="page-content">

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-database"></i>
                System Backup Management
                <small>Database Backup & Recovery</small>
            </h3>
        </div>

        <div style="display: flex; gap: 10px;">
            <button id="createBackupBtn" class="btn btn-success">
                <i class="fa fa-plus"></i>
                Create Backup
            </button>
            <button id="refreshBtn" class="btn btn-default">
                <i class="fa fa-refresh"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Grid (6 Cards) -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Total Backups</span>
                <div class="stat-icon">
                    <i class="fa fa-folder"></i>
                </div>
            </div>
            <div class="stat-value" id="total_backups">0</div>
            <div class="stat-subtitle">Backup Files</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Total Size</span>
                <div class="stat-icon">
                    <i class="fa fa-database"></i>
                </div>
            </div>
            <div class="stat-value" id="total_backup_size">0 B</div>
            <div class="stat-subtitle">Total Storage</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Project Size</span>
                <div class="stat-icon">
                    <i class="fa fa-hdd-o"></i>
                </div>
            </div>
            <div class="stat-value" id="project_size">0 B</div>
            <div class="stat-subtitle">Disk Usage</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">DB Response</span>
                <div class="stat-icon">
                    <i class="fa fa-bolt"></i>
                </div>
            </div>
            <div class="stat-value" id="db_response_time">0 ms</div>
            <div class="stat-subtitle">Performance</div>
        </div>

        <div class="stat-card teal">
            <div class="stat-header">
                <span class="stat-title">Last Backup</span>
                <div class="stat-icon">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
            <div class="stat-value" id="last_backup_time">Never</div>
            <div class="stat-subtitle">Most Recent</div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Largest File</span>
                <div class="stat-icon">
                    <i class="fa fa-file"></i>
                </div>
            </div>
            <div class="stat-value" id="largest_backup_size">0 B</div>
            <div class="stat-subtitle">Max Backup Size</div>
        </div>
    </div>

    <!-- Backup List Table -->
    <div class="row" style="margin-top: 15px;">
        <div class="col-md-12">
            <div class="dashboard-box">
                <h4>
                    <i class="fa fa-list"></i>
                    Backup Files
                </h4>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="30%">Backup File</th>
                                <th width="15%">Size</th>
                                <th width="25%">Created Date</th>
                                <th width="25%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="backups-list">
                            <tr>
                                <td colspan="5" class="text-center" style="padding: 40px;">
                                    <i class="fa fa-spinner fa-spin" style="font-size: 24px; margin-right: 10px;"></i>
                                    <span>Loading backups...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Restore Password Modal -->
<div class="modal fade" id="restorePasswordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-lock"></i> Confirm Restore Operation
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> Restoring a backup will overwrite your current database.
                    A backup of current data will be created automatically before restore.
                </div>
                <div class="form-group">
                    <label>Super Admin Password <span style="color: red;">*</span></label>
                    <input type="password" id="restorePassword" class="form-control" placeholder="Enter super admin password">
                </div>
                <p style="font-size: 12px; color: #666;">Please enter your super admin password to confirm this operation.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRestoreBtn">
                    <i class="fa fa-refresh"></i> Restore Now
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .page-content {
        padding: 20px;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px;
        background: white;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .dashboard-header h3 {
        margin: 0;
        font-size: 24px;
        color: #333;
    }

    .dashboard-header h3 small {
        display: block;
        font-size: 12px;
        color: #999;
        margin-top: 5px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 4px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
    }

    .stat-card.blue { border-left-color: #2196F3; }
    .stat-card.green { border-left-color: #4CAF50; }
    .stat-card.orange { border-left-color: #FF9800; }
    .stat-card.purple { border-left-color: #9C27B0; }
    .stat-card.teal { border-left-color: #009688; }
    .stat-card.red { border-left-color: #F44336; }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .stat-title {
        font-size: 12px;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
    }

    .stat-icon {
        font-size: 24px;
        opacity: 0.3;
    }

    .stat-value {
        font-size: 28px;
        font-weight: bold;
        color: #333;
        margin-bottom: 8px;
    }

    .stat-value.loading {
        font-size: 14px;
        opacity: 0.5;
    }

    .stat-subtitle {
        font-size: 12px;
        color: #999;
    }

    .dashboard-box {
        background: white;
        border-radius: 4px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .dashboard-box h4 {
        margin: 0 0 15px 0;
        font-size: 16px;
        color: #2c3e50;
        font-weight: 600;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background: #f9f9f9;
        border-color: #ddd;
        font-weight: 600;
        color: #2c3e50;
    }

    .table tbody tr:hover {
        background: #f9f9f9;
    }

    .btn {
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-success {
        background: #27ae60;
        color: white;
        border-color: #27ae60;
    }

    .btn-success:hover {
        background: #229954;
        border-color: #229954;
    }

    .btn-default {
        background: #ecf0f1;
        color: #2c3e50;
        border-color: #bdc3c7;
    }

    .btn-default:hover {
        background: #d5dbdb;
        border-color: #a6acaf;
    }

    .btn-info {
        background: #3498db;
        color: white;
        border-color: #3498db;
        padding: 6px 12px;
        font-size: 12px;
    }

    .btn-info:hover {
        background: #2980b9;
        border-color: #2980b9;
    }

    .btn-primary {
        background: #2196F3;
        color: white;
        border-color: #2196F3;
        padding: 6px 12px;
        font-size: 12px;
    }

    .btn-primary:hover {
        background: #1976D2;
        border-color: #1976D2;
    }

    .btn-danger {
        background: #e74c3c;
        color: white;
        border-color: #e74c3c;
        padding: 6px 12px;
        font-size: 12px;
    }

    .btn-danger:hover {
        background: #c0392b;
        border-color: #c0392b;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
    }

    .alert-warning {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        color: #856404;
        padding: 12px;
        border-radius: 4px;
    }
</style>

<script>
    var currentRestoreFile = null;

    function loadBackupStats() {
        fetch('index.php?r=system/backup&action=stats', {
            method: 'GET'
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                document.getElementById('total_backups').textContent = data.data.total_backups;
                document.getElementById('total_backup_size').textContent = data.data.total_backup_size_formatted;
                document.getElementById('project_size').textContent = data.data.project_size;
                document.getElementById('db_response_time').textContent = data.data.db_response_time + ' ms';
                document.getElementById('largest_backup_size').textContent = data.data.largest_backup;

                if (data.data.backups && data.data.backups.length > 0) {
                    document.getElementById('last_backup_time').textContent = data.data.backups[0].date_formatted;
                }
            }
        })
        .catch(err => console.error('Error loading stats:', err));
    }

    function loadBackups() {
        fetch('index.php?r=system/backup&action=list', {
            method: 'GET'
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                let html = '';
                if (data.data.length === 0) {
                    html = '<tr><td colspan="5" class="text-center" style="padding: 40px;"><i class="fa fa-inbox" style="font-size: 24px; margin-right: 10px; opacity: 0.5;"></i><span style="color: #999;">No backups found</span></td></tr>';
                } else {
                    data.data.forEach((backup, index) => {
                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td><code style="background: #f5f5f5; padding: 4px 8px; border-radius: 3px;">${backup.filename}</code></td>
                                <td>${backup.size_formatted}</td>
                                <td>${backup.date_formatted}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-info" onclick="showRestorePassword('${backup.filename}')" title="Restore from this backup">
                                            <i class="fa fa-refresh"></i> Restore
                                        </button>
                                        <button class="btn btn-primary" onclick="downloadBackup('${backup.filename}')" title="Download backup file">
                                            <i class="fa fa-download"></i> Download
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteBackup('${backup.filename}')" title="Delete this backup">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                document.getElementById('backups-list').innerHTML = html;
            }
        })
        .catch(err => {
            console.error('Error loading backups:', err);
            document.getElementById('backups-list').innerHTML = '<tr><td colspan="5" class="text-center" style="padding: 40px; color: red;"><i class="fa fa-exclamation-triangle" style="font-size: 24px; margin-right: 10px;"></i><span>Failed to load backups</span></td></tr>';
        });
    }

    function createBackup() {
        document.getElementById('createBackupBtn').disabled = true;
        document.getElementById('createBackupBtn').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Creating...';

        const formData = new FormData();
        formData.append('action', 'create');

        fetch('index.php?r=system/backup', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Backup created successfully!', 'success');
                loadBackupStats();
                loadBackups();
            } else {
                showToast(data.message || 'Failed to create backup', 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            showToast('Error creating backup', 'error');
        })
        .finally(() => {
            document.getElementById('createBackupBtn').disabled = false;
            document.getElementById('createBackupBtn').innerHTML = '<i class="fa fa-plus"></i> Create Backup';
        });
    }

    function showRestorePassword(filename) {
        currentRestoreFile = filename;
        $('#restorePasswordModal').modal('show');
    }

    function restoreBackup() {
        const password = document.getElementById('restorePassword').value;

        if (!password) {
            showToast('Please enter super admin password', 'error');
            return;
        }

        document.getElementById('confirmRestoreBtn').disabled = true;

        const formData = new FormData();
        formData.append('action', 'restore');
        formData.append('backup_file', currentRestoreFile);
        formData.append('password', password);

        fetch('index.php?r=system/backup', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            $('#restorePasswordModal').modal('hide');
            document.getElementById('restorePassword').value = '';

            if (data.success) {
                showToast('Database restored successfully!', 'success');
                loadBackupStats();
                loadBackups();
            } else {
                showToast(data.message || 'Failed to restore backup', 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            showToast('Error restoring backup', 'error');
        })
        .finally(() => {
            document.getElementById('confirmRestoreBtn').disabled = false;
        });
    }

    function downloadBackup(filename) {
        window.location.href = 'index.php?r=system/backup&action=download&file=' + encodeURIComponent(filename);
    }

    function deleteBackup(filename) {
        if (!confirm('Are you sure you want to delete this backup?\n' + filename + '\n\nThis action cannot be undone!')) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('backup_file', filename);

        fetch('index.php?r=system/backup', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Backup deleted successfully!', 'success');
                loadBackupStats();
                loadBackups();
            } else {
                showToast(data.message || 'Failed to delete backup', 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            showToast('Error deleting backup', 'error');
        });
    }

    function showToast(message, type) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Success' : 'Error',
                text: message,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            alert(message);
        }
    }

    // Event Listeners
    document.getElementById('createBackupBtn').addEventListener('click', createBackup);
    document.getElementById('confirmRestoreBtn').addEventListener('click', restoreBackup);
    document.getElementById('refreshBtn').addEventListener('click', () => {
        loadBackupStats();
        loadBackups();
    });

    // Load on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadBackupStats();
        loadBackups();
    });
</script>

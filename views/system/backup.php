<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'System Backup Management';
?>

<div class="page-content">

    <!-- Header -->
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
            <button id="refreshBackupBtn" class="btn btn-default">
                <i class="fa fa-refresh"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Grid -->
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
                <span class="stat-title">Backup Size</span>
                <div class="stat-icon">
                    <i class="fa fa-database"></i>
                </div>
            </div>
            <div class="stat-value" id="total_backup_size">0 B</div>
            <div class="stat-subtitle">Total Backup Storage</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Project Size</span>
                <div class="stat-icon">
                    <i class="fa fa-hdd-o"></i>
                </div>
            </div>
            <div class="stat-value" id="project_size">0 B</div>
            <div class="stat-subtitle">Total Disk Usage</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">DB Response Time</span>
                <div class="stat-icon">
                    <i class="fa fa-bolt"></i>
                </div>
            </div>
            <div class="stat-value" id="db_response_time">0 ms</div>
            <div class="stat-subtitle">Database Performance</div>
        </div>
    </div>

    <!-- Backup List -->
    <div class="widget-main" style="margin-top: 30px; padding: 20px;">
        <h4>Backup Files</h4>
        <hr />

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">Backup File</th>
                        <th width="15%">Size</th>
                        <th width="20%">Created</th>
                        <th width="35%">Actions</th>
                    </tr>
                </thead>
                <tbody id="backups-list">
                    <tr>
                        <td colspan="5" class="text-center">Loading backups...</td>
                    </tr>
                </tbody>
            </table>
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
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

    .stat-subtitle {
        font-size: 12px;
        color: #999;
    }

    .backup-action-btn {
        padding: 5px 10px;
        margin: 2px;
        font-size: 12px;
        vertical-align: middle;
    }

    .widget-main {
        background: white;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
</style>

<script>
var currentRestoreFile = null;

function loadBackupStats() {
    fetch('index.php?r=system/backup&action=stats')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                document.getElementById('total_backups').textContent = data.data.total_backups;
                document.getElementById('total_backup_size').textContent = data.data.total_backup_size_formatted;
                document.getElementById('project_size').textContent = data.data.project_size;
                document.getElementById('db_response_time').textContent = data.data.db_response_time + ' ms';
            }
        })
        .catch(err => console.error('Error loading stats:', err));
}

function loadBackups() {
    fetch('index.php?r=system/backup&action=list')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                let html = '';
                if (data.data.length === 0) {
                    html = '<tr><td colspan="5" class="text-center">No backups found</td></tr>';
                } else {
                    data.data.forEach((backup, index) => {
                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td><code>${backup.filename}</code></td>
                                <td>${backup.size_formatted}</td>
                                <td>${backup.date_formatted}</td>
                                <td>
                                    <button class="btn btn-sm btn-info backup-action-btn" onclick="showRestorePassword('${backup.filename}')">
                                        <i class="fa fa-refresh"></i> Restore
                                    </button>
                                    <button class="btn btn-sm btn-primary backup-action-btn" onclick="downloadBackup('${backup.filename}')">
                                        <i class="fa fa-download"></i> Download
                                    </button>
                                    <button class="btn btn-sm btn-danger backup-action-btn" onclick="deleteBackup('${backup.filename}')">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                document.getElementById('backups-list').innerHTML = html;
            }
        })
        .catch(err => console.error('Error loading backups:', err));
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
    if (!confirm('Are you sure you want to delete this backup?\n' + filename)) {
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
    // Using existing toast system if available
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type,
            title: type === 'success' ? 'Success' : 'Error',
            text: message,
            timer: 3000
        });
    } else {
        alert(message);
    }
}

// Event Listeners
document.getElementById('createBackupBtn').addEventListener('click', createBackup);
document.getElementById('confirmRestoreBtn').addEventListener('click', restoreBackup);
document.getElementById('refreshBackupBtn').addEventListener('click', () => {
    loadBackupStats();
    loadBackups();
});

// Load on page load
document.addEventListener('DOMContentLoaded', () => {
    loadBackupStats();
    loadBackups();
});
</script>

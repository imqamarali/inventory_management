<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Database Backup & Restore';
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li><i class="ace-icon fa fa-home home-icon"></i> <a href="index.php?r=inventory/dashboard">Home</a></li>
                <li class="active">Database Backup</li>
            </ul>
        </div>

        <div class="widget-main" style="padding: 20px;">
            <h3>Database Backup & Restore Management</h3>
            <hr />

            <!-- Backup Controls -->
            <div style="margin-bottom: 30px;">
                <button class="btn btn-success" onclick="createBackup()" style="margin-bottom: 10px;">
                    <i class="fa fa-plus"></i> Create New Backup
                </button>
                <span id="backup-status" style="margin-left: 20px;"></span>
            </div>

            <!-- Backups Table -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Backup File</th>
                            <th>Size</th>
                            <th>Created</th>
                            <th>Actions</th>
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

        <div id="toastBox"></div>
    </div>
</div>

<style>
    .backup-action-btn {
        padding: 5px 10px;
        margin: 2px;
        font-size: 12px;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 3px;
        color: white;
        font-weight: bold;
    }
    .status-success { background-color: #66bb6a; }
    .status-error { background-color: #ef5350; }
    .status-info { background-color: #29b6f6; }
</style>

<script>
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
                                <td>${backup.filename}</td>
                                <td>${backup.size_formatted}</td>
                                <td>${backup.date_formatted}</td>
                                <td>
                                    <button class="btn btn-sm btn-info backup-action-btn" onclick="restoreBackup('${backup.filename}')">
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
    document.getElementById('backup-status').innerHTML = '<span class="status-badge status-info">Creating backup...</span>';

    const formData = new FormData();
    formData.append('action', 'create');

    fetch('index.php?r=system/backup', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            document.getElementById('backup-status').innerHTML = `<span class="status-badge status-success">${data.message}</span>`;
            setTimeout(loadBackups, 1000);
        } else {
            showToast(data.message, 'error');
            document.getElementById('backup-status').innerHTML = `<span class="status-badge status-error">${data.message}</span>`;
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('Error creating backup', 'error');
    });
}

function restoreBackup(filename) {
    if (!confirm('This will restore the database from ' + filename + '.\n\nA backup of current data will be created first.\n\nAre you sure?')) {
        return;
    }

    document.getElementById('backup-status').innerHTML = '<span class="status-badge status-info">Restoring backup...</span>';

    const formData = new FormData();
    formData.append('action', 'restore');
    formData.append('backup_file', filename);

    fetch('index.php?r=system/backup', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Backup restored successfully. Pre-restore backup saved as: ' + data.pre_backup, 'success');
            document.getElementById('backup-status').innerHTML = `<span class="status-badge status-success">Restored successfully</span>`;
            setTimeout(loadBackups, 1000);
        } else {
            showToast(data.message, 'error');
            document.getElementById('backup-status').innerHTML = `<span class="status-badge status-error">${data.message}</span>`;
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('Error restoring backup', 'error');
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
            showToast(data.message, 'success');
            loadBackups();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('Error deleting backup', 'error');
    });
}

function showToast(message, type) {
    const toastBox = document.getElementById('toastBox');
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.innerHTML = `
        <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span style="margin-left: 10px;">${message}</span>
    `;
    toastBox.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}

// Load backups on page load
document.addEventListener('DOMContentLoaded', loadBackups);
</script>

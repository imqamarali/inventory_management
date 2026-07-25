<?php
use yii\helpers\Html;

if (!isset($config)) $config = [];
?>

<div class="widget-box">
    <div class="widget-header" style="background-color: #1e3c72; color: white; padding: 12px 15px;">
        <h4 class="widget-title" style="color: white; margin: 0;">
            <i class="fa fa-database"></i> System Backup Configuration
        </h4>
    </div>

    <div class="widget-body" style="padding: 20px;">
        <form id="systembackup_form" method="POST" onsubmit="return saveBackupConfig()">
            <div id="form-message" style="margin-bottom: 20px; display: none;"></div>

            <div class="row">
                <!-- Auto Backup Enabled -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="auto_backup_enabled" name="auto_backup_enabled"
                                   value="1" <?= (!empty($config['auto_backup_enabled']) ? 'checked' : '') ?>>
                            <strong>Enable Automatic Backup</strong>
                        </label>
                        <small style="color: #666; display: block; margin-top: 5px;">
                            <i class="fa fa-info-circle"></i> Enable automatic database backups
                        </small>
                    </div>
                </div>

                <!-- Backup Frequency -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="backup_frequency">
                            <strong>Backup Frequency</strong>
                        </label>
                        <select name="backup_frequency" id="backup_frequency" class="form-control">
                            <option value="daily" <?= (($config['backup_frequency'] ?? '') === 'daily' ? 'selected' : '') ?>>
                                Daily (Every 24 hours)
                            </option>
                            <option value="weekly" <?= (($config['backup_frequency'] ?? '') === 'weekly' ? 'selected' : '') ?>>
                                Weekly (Every 7 days)
                            </option>
                            <option value="monthly" <?= (($config['backup_frequency'] ?? '') === 'monthly' ? 'selected' : '') ?>>
                                Monthly (Every 30 days)
                            </option>
                        </select>
                        <small style="color: #666; display: block; margin-top: 5px;">
                            <i class="fa fa-info-circle"></i> How often to create automatic backups
                        </small>
                    </div>
                </div>
            </div>

            <hr style="margin: 20px 0;" />

            <div class="row">
                <!-- Auto Delete Old Backups -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="auto_delete_enabled" name="auto_delete_enabled"
                                   value="1" <?= (!empty($config['auto_delete_enabled']) ? 'checked' : '') ?>>
                            <strong>Auto-Delete Old Backups</strong>
                        </label>
                        <small style="color: #666; display: block; margin-top: 5px;">
                            <i class="fa fa-info-circle"></i> Automatically delete backups older than specified days
                        </small>
                    </div>
                </div>

                <!-- Delete Backups Older Than -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="backup_retention_days">
                            <strong>Retention Period (Days)</strong>
                        </label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="number" name="backup_retention_days" id="backup_retention_days"
                                   class="form-control" style="flex: 1;"
                                   value="<?= $config['backup_retention_days'] ?? 30 ?>" min="1" max="365">
                            <span style="white-space: nowrap; color: #666;">days</span>
                        </div>
                        <small style="color: #666; display: block; margin-top: 5px;">
                            <i class="fa fa-info-circle"></i> Backups older than this will be deleted automatically
                        </small>
                    </div>
                </div>
            </div>

            <hr style="margin: 20px 0;" />

            <!-- Info Box -->
            <div style="background: #f0f7ff; border-left: 4px solid #2196F3; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <h5 style="margin-top: 0; color: #1976D2;">
                    <i class="fa fa-lightbulb-o"></i> Backup Information
                </h5>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li><strong>Daily backups</strong>: One backup every 24 hours</li>
                    <li><strong>Weekly backups</strong>: One backup every 7 days</li>
                    <li><strong>Monthly backups</strong>: One backup every 30 days</li>
                    <li><strong>Retention</strong>: Enter number of days to keep backups. Older backups will be automatically deleted.</li>
                    <li><strong>Storage</strong>: Each backup is approximately equal to your database size</li>
                </ul>
            </div>

            <!-- Current Status -->
            <div style="background: #f5f5f5; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <h5 style="margin-top: 0;">Current Status</h5>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div>
                        <strong>Last Backup:</strong>
                        <p id="last_backup" style="margin: 5px 0 0 0; color: #666;">Checking...</p>
                    </div>
                    <div>
                        <strong>Next Scheduled:</strong>
                        <p id="next_backup" style="margin: 5px 0 0 0; color: #666;">Checking...</p>
                    </div>
                    <div>
                        <strong>Auto Backup Status:</strong>
                        <p id="backup_status" style="margin: 5px 0 0 0; color: #666;">Checking...</p>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div style="text-align: right;">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Save Configuration
                </button>
                <button type="reset" class="btn btn-default" style="margin-left: 10px;">
                    <i class="fa fa-undo"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .widget-box {
        background: white;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .widget-header {
        border-bottom: 1px solid #ddd;
        border-radius: 4px 4px 0 0;
    }

    .widget-body {
        border-radius: 0 0 4px 4px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #333;
    }

    .form-group input[type="checkbox"] {
        margin-right: 8px;
    }

    .form-group small {
        display: block;
        margin-top: 5px;
        color: #666;
    }

    .alert-info {
        background: #e3f2fd;
        border-left: 4px solid #2196F3;
        padding: 12px 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
</style>

<script>
function saveBackupConfig() {
    const formData = new FormData(document.getElementById('systembackup_form'));
    formData.append('action', 'save_backup_config');

    fetch('index.php?r=settings/backup', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        const msgDiv = document.getElementById('form-message');
        msgDiv.style.display = 'block';

        if (data.success) {
            msgDiv.innerHTML = '<div class="alert alert-success" style="margin: 0;"><i class="fa fa-check-circle"></i> Configuration saved successfully!</div>';
            setTimeout(() => { msgDiv.style.display = 'none'; }, 5000);
            loadBackupStatus();
        } else {
            msgDiv.innerHTML = '<div class="alert alert-danger" style="margin: 0;"><i class="fa fa-times-circle"></i> ' + (data.message || 'Failed to save configuration') + '</div>';
        }
    })
    .catch(err => {
        console.error('Error:', err);
        const msgDiv = document.getElementById('form-message');
        msgDiv.style.display = 'block';
        msgDiv.innerHTML = '<div class="alert alert-danger" style="margin: 0;"><i class="fa fa-times-circle"></i> Error saving configuration</div>';
    });

    return false;
}

function loadBackupStatus() {
    fetch('index.php?r=settings/backup&action=get_status')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('last_backup').textContent = data.data.last_backup || 'Never';
                document.getElementById('next_backup').textContent = data.data.next_backup || 'Not scheduled';
                document.getElementById('backup_status').innerHTML = data.data.status_badge || 'Disabled';
            }
        })
        .catch(err => console.error('Error loading status:', err));
}

document.addEventListener('DOMContentLoaded', loadBackupStatus);

// Disable/enable retention days field based on checkbox
document.getElementById('auto_delete_enabled').addEventListener('change', function() {
    document.getElementById('backup_retention_days').disabled = !this.checked;
});

// Initial state
document.getElementById('backup_retention_days').disabled = !document.getElementById('auto_delete_enabled').checked;
document.getElementById('backup_frequency').disabled = !document.getElementById('auto_backup_enabled').checked;

// Enable/disable frequency select based on auto_backup checkbox
document.getElementById('auto_backup_enabled').addEventListener('change', function() {
    document.getElementById('backup_frequency').disabled = !this.checked;
});
</script>

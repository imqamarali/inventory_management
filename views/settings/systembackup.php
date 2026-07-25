<?php
use yii\helpers\Html;

if (!isset($config)) $config = [];
?>

<div class="page-content">

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-database"></i>
                System Backup
                <small>Backup Configuration & Management</small>
            </h3>
        </div>
    </div>

    <!-- Configuration Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Auto Backup</span>
                <div class="stat-icon">
                    <i class="fa fa-refresh"></i>
                </div>
            </div>
            <div class="stat-value" id="auto_status">Disabled</div>
            <div class="stat-subtitle">Enable Automatic Backups</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Backup Frequency</span>
                <div class="stat-icon">
                    <i class="fa fa-clock-o"></i>
                </div>
            </div>
            <div class="stat-value" id="frequency_status">Daily</div>
            <div class="stat-subtitle">Backup Schedule</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Auto-Delete</span>
                <div class="stat-icon">
                    <i class="fa fa-trash"></i>
                </div>
            </div>
            <div class="stat-value" id="delete_status">Disabled</div>
            <div class="stat-subtitle">Old Backup Cleanup</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Retention Period</span>
                <div class="stat-icon">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
            <div class="stat-value" id="retention_days">30</div>
            <div class="stat-subtitle">Days to Keep Backups</div>
        </div>
    </div>

    <!-- Configuration Section -->
    <div class="row" style="margin-top: 15px;">
        <div class="col-md-12">
            <div class="dashboard-box">
                <h4>
                    <i class="fa fa-cogs"></i>
                    Configuration Settings
                </h4>

                <div id="form-message" style="margin-bottom: 20px; display: none;"></div>

                <form id="systembackup_form" onsubmit="return saveBackupConfig()">

                    <!-- Auto Backup Section -->
                    <div style="margin-bottom: 25px; padding: 15px; background: #f8f9fa; border-radius: 4px; border-left: 4px solid #3498db;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div>
                                <h5 style="margin: 0 0 5px; color: #2c3e50; font-weight: 600;">Automatic Backup</h5>
                                <small style="color: #7f8c8d;">Enable automatic database backups on a schedule</small>
                            </div>
                            <label style="display: flex; align-items: center; margin: 0;">
                                <input type="checkbox" id="auto_backup_enabled" name="auto_backup_enabled" value="1"
                                       <?= (!empty($config['auto_backup_enabled']) ? 'checked' : '') ?>
                                       style="margin: 0 8px 0 0; width: 18px; height: 18px; cursor: pointer;">
                                <span style="cursor: pointer; color: #2c3e50; font-weight: 500;">Enable</span>
                            </label>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <label for="backup_frequency" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">Backup Frequency</label>
                                <select name="backup_frequency" id="backup_frequency" class="form-control" style="width: 100%;">
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
                                <small style="color: #7f8c8d; display: block; margin-top: 5px;">How often to create automatic backups</small>
                            </div>
                            <div style="display: flex; flex-direction: column; justify-content: flex-end;">
                                <div style="background: #e8f4f8; padding: 10px; border-radius: 4px; border-left: 3px solid #3498db;">
                                    <div style="font-size: 11px; color: #7f8c8d; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Next Scheduled</div>
                                    <div id="next_backup" style="font-size: 14px; font-weight: 600; color: #2c3e50;">Checking...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Auto-Delete Section -->
                    <div style="margin-bottom: 25px; padding: 15px; background: #f8f9fa; border-radius: 4px; border-left: 4px solid #e74c3c;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div>
                                <h5 style="margin: 0 0 5px; color: #2c3e50; font-weight: 600;">Auto-Delete Old Backups</h5>
                                <small style="color: #7f8c8d;">Automatically delete backups older than specified days</small>
                            </div>
                            <label style="display: flex; align-items: center; margin: 0;">
                                <input type="checkbox" id="auto_delete_enabled" name="auto_delete_enabled" value="1"
                                       <?= (!empty($config['auto_delete_enabled']) ? 'checked' : '') ?>
                                       style="margin: 0 8px 0 0; width: 18px; height: 18px; cursor: pointer;">
                                <span style="cursor: pointer; color: #2c3e50; font-weight: 500;">Enable</span>
                            </label>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <label for="backup_retention_days" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">
                                    Retention Period (Days)
                                </label>
                                <input type="number" name="backup_retention_days" id="backup_retention_days" class="form-control"
                                       value="<?= $config['backup_retention_days'] ?? 30 ?>" min="1" max="365" style="width: 100%;">
                                <small style="color: #7f8c8d; display: block; margin-top: 5px;">Backups older than this will be deleted (1-365 days)</small>
                            </div>
                            <div style="display: flex; flex-direction: column; justify-content: flex-end;">
                                <div style="background: #fdf5e6; padding: 10px; border-radius: 4px; border-left: 3px solid #f39c12;">
                                    <div style="font-size: 11px; color: #7f8c8d; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Last Backup</div>
                                    <div id="last_backup" style="font-size: 14px; font-weight: 600; color: #2c3e50;">Checking...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Information Box -->
                    <div style="background: #e8f4f8; border-left: 4px solid #3498db; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <h5 style="margin: 0 0 10px; color: #1976D2;">
                            <i class="fa fa-lightbulb-o"></i> Backup Information
                        </h5>
                        <ul style="margin: 10px 0; padding-left: 20px; color: #2c3e50; font-size: 13px; line-height: 1.8;">
                            <li><strong>Daily backups:</strong> One backup every 24 hours</li>
                            <li><strong>Weekly backups:</strong> One backup every 7 days</li>
                            <li><strong>Monthly backups:</strong> One backup every 30 days</li>
                            <li><strong>Retention:</strong> Enter number of days to keep backups. Older backups will be automatically deleted.</li>
                            <li><strong>Storage:</strong> Each backup file is approximately equal to your database size</li>
                            <li><strong>Safety:</strong> Pre-restore backups are created automatically before any restore operation</li>
                        </ul>
                    </div>

                    <!-- Save Button -->
                    <div style="text-align: right; padding-top: 15px; border-top: 1px solid #e8e8e8;">
                        <button type="submit" class="btn btn-success" style="margin-right: 10px;">
                            <i class="fa fa-save"></i> Save Configuration
                        </button>
                        <button type="reset" class="btn btn-default">
                            <i class="fa fa-undo"></i> Reset
                        </button>
                    </div>

                </form>

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
    }

    .form-control {
        padding: 8px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 13px;
    }

    .form-control:focus {
        border-color: #3498db;
        outline: none;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
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
                msgDiv.innerHTML = '<div class="alert alert-success" style="margin: 0; padding: 12px; border-radius: 4px; background: #d4edda; color: #155724; border-left: 4px solid #28a745;"><i class="fa fa-check-circle"></i> ' + data.message + '</div>';
                setTimeout(() => { msgDiv.style.display = 'none'; }, 5000);
                loadBackupStatus();
            } else {
                msgDiv.innerHTML = '<div class="alert alert-danger" style="margin: 0; padding: 12px; border-radius: 4px; background: #f8d7da; color: #721c24; border-left: 4px solid #f44336;"><i class="fa fa-times-circle"></i> ' + (data.message || 'Failed to save configuration') + '</div>';
            }
        })
        .catch(err => {
            console.error('Error:', err);
            const msgDiv = document.getElementById('form-message');
            msgDiv.style.display = 'block';
            msgDiv.innerHTML = '<div class="alert alert-danger" style="margin: 0; padding: 12px; border-radius: 4px; background: #f8d7da; color: #721c24; border-left: 4px solid #f44336;"><i class="fa fa-times-circle"></i> Error saving configuration</div>';
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

                    const autoEnabled = data.data.status_badge.includes('Enabled');
                    document.getElementById('auto_status').textContent = autoEnabled ? 'Enabled' : 'Disabled';
                    document.getElementById('delete_status').textContent = document.getElementById('auto_delete_enabled').checked ? 'Enabled' : 'Disabled';
                    document.getElementById('frequency_status').textContent = (document.getElementById('backup_frequency').value || 'daily').charAt(0).toUpperCase() + (document.getElementById('backup_frequency').value || 'daily').slice(1);
                    document.getElementById('retention_days').textContent = document.getElementById('backup_retention_days').value || '30';
                }
            })
            .catch(err => console.error('Error loading status:', err));
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadBackupStatus();

        // Update stats when form changes
        document.getElementById('auto_backup_enabled').addEventListener('change', function() {
            document.getElementById('auto_status').textContent = this.checked ? 'Enabled' : 'Disabled';
            document.getElementById('backup_frequency').disabled = !this.checked;
        });

        document.getElementById('auto_delete_enabled').addEventListener('change', function() {
            document.getElementById('delete_status').textContent = this.checked ? 'Enabled' : 'Disabled';
            document.getElementById('backup_retention_days').disabled = !this.checked;
        });

        document.getElementById('backup_frequency').addEventListener('change', function() {
            document.getElementById('frequency_status').textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
        });

        document.getElementById('backup_retention_days').addEventListener('change', function() {
            document.getElementById('retention_days').textContent = this.value || '30';
        });

        // Initial state
        document.getElementById('backup_retention_days').disabled = !document.getElementById('auto_delete_enabled').checked;
        document.getElementById('backup_frequency').disabled = !document.getElementById('auto_backup_enabled').checked;
    });
</script>

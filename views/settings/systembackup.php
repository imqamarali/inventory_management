<?php
use yii\helpers\Html;

if (!isset($config)) $config = [];
if (!isset($backupStats)) $backupStats = [];
?>

<div class="widget-box"> 
    <div class="widget-body" style="padding: 20px;">

        <!-- Status Message -->
        <div id="status-message" style="display: none; margin-bottom: 20px;"></div>

        <!-- Backup Statistics Grid (Smaller) -->
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 25px;">
            <div class="stat-card blue" style="padding: 15px;">
                <div class="stat-header" style="margin-bottom: 10px;">
                    <span class="stat-title" style="font-size: 11px;">Total Backups</span>
                    <div class="stat-icon" style="font-size: 18px;">
                        <i class="fa fa-folder"></i>
                    </div>
                </div>
                <div class="stat-value" id="total_backups" style="font-size: 24px;"><?= $backupStats['total_backups'] ?? 0 ?></div>
                <div class="stat-subtitle" style="font-size: 11px;">Backup Files</div>
            </div>

            <div class="stat-card green" style="padding: 15px;">
                <div class="stat-header" style="margin-bottom: 10px;">
                    <span class="stat-title" style="font-size: 11px;">Total Size</span>
                    <div class="stat-icon" style="font-size: 18px;">
                        <i class="fa fa-database"></i>
                    </div>
                </div>
                <div class="stat-value" id="total_backup_size" style="font-size: 20px;"><?= $backupStats['total_size'] ?? '0 KB' ?></div>
                <div class="stat-subtitle" style="font-size: 11px;">Total Storage</div>
            </div>

            <div class="stat-card orange" style="padding: 15px;">
                <div class="stat-header" style="margin-bottom: 10px;">
                    <span class="stat-title" style="font-size: 11px;">Project Size</span>
                    <div class="stat-icon" style="font-size: 18px;">
                        <i class="fa fa-hdd-o"></i>
                    </div>
                </div>
                <div class="stat-value" id="project_size" style="font-size: 20px;"><?= $backupStats['project_size'] ?? '0 MB' ?></div>
                <div class="stat-subtitle" style="font-size: 11px;">Disk Usage</div>
            </div>

            <div class="stat-card purple" style="padding: 15px;">
                <div class="stat-header" style="margin-bottom: 10px;">
                    <span class="stat-title" style="font-size: 11px;">DB Response</span>
                    <div class="stat-icon" style="font-size: 18px;">
                        <i class="fa fa-bolt"></i>
                    </div>
                </div>
                <div class="stat-value" id="db_response_time" style="font-size: 24px;"><?= $backupStats['db_response_time'] ?? '0 ms' ?></div>
                <div class="stat-subtitle" style="font-size: 11px;">Performance</div>
            </div>

            <div class="stat-card teal" style="padding: 15px;">
                <div class="stat-header" style="margin-bottom: 10px;">
                    <span class="stat-title" style="font-size: 11px;">Last Backup</span>
                    <div class="stat-icon" style="font-size: 18px;">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
                <div class="stat-value" id="last_backup_time" style="font-size: 16px;"><?= $backupStats['last_backup_time'] ?? '-' ?></div>
                <div class="stat-subtitle" style="font-size: 11px;">Most Recent</div>
            </div>

            <div class="stat-card red" style="padding: 15px;">
                <div class="stat-header" style="margin-bottom: 10px;">
                    <span class="stat-title" style="font-size: 11px;">Largest File</span>
                    <div class="stat-icon" style="font-size: 18px;">
                        <i class="fa fa-file"></i>
                    </div>
                </div>
                <div class="stat-value" id="largest_backup_size" style="font-size: 20px;"><?= $backupStats['largest_backup_size'] ?? '0 KB' ?></div>
                <div class="stat-subtitle" style="font-size: 11px;">Max Backup Size</div>
            </div>
        </div>

        <!-- Configuration Form -->
        <form id="systembackup_form" method="POST">
            <div class="row">
                <!-- Auto Backup Section -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="auto_backup_enabled">
                            <strong>Enable Automatic Backup</strong>
                            <span class="label label-info" style="margin-left: 8px; font-size: 11px;">Schedule</span>
                        </label>
                        <div style="display: flex; align-items: center; margin-top: 8px;">
                            <input type="checkbox" id="auto_backup_enabled" name="auto_backup_enabled" value="1"
                                   <?= (!empty($config['auto_backup_enabled']) ? 'checked' : '') ?>
                                   style="width: 18px; height: 18px; cursor: pointer; margin-right: 10px;">
                            <span style="color: #666;">Enable automatic database backups</span>
                        </div>
                        <small style="color: #666; display: block; margin-top: 8px;">
                            <i class="fa fa-info-circle"></i> System will create scheduled backups automatically
                        </small>
                    </div>
                </div>

                <!-- Backup Frequency -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="backup_frequency">
                            <strong>Backup Frequency</strong>
                            <span class="label label-warning" style="margin-left: 8px; font-size: 11px;">Schedule</span>
                        </label>
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
                        <small style="color: #666; display: block; margin-top: 5px;">
                            <i class="fa fa-info-circle"></i> How often to create automatic backups
                        </small>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Auto Delete Old Backups -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="auto_delete_enabled">
                            <strong>Auto-Delete Old Backups</strong>
                            <span class="label label-danger" style="margin-left: 8px; font-size: 11px;">Cleanup</span>
                        </label>
                        <div style="display: flex; align-items: center; margin-top: 8px;">
                            <input type="checkbox" id="auto_delete_enabled" name="auto_delete_enabled" value="1"
                                   <?= (!empty($config['auto_delete_enabled']) ? 'checked' : '') ?>
                                   style="width: 18px; height: 18px; cursor: pointer; margin-right: 10px;">
                            <span style="color: #666;">Enable automatic cleanup of old backups</span>
                        </div>
                        <small style="color: #666; display: block; margin-top: 8px;">
                            <i class="fa fa-info-circle"></i> Automatically delete backups older than retention period
                        </small>
                    </div>
                </div>

                <!-- Retention Period -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="backup_retention_days">
                            <strong>Retention Period (Days)</strong>
                            <span class="label label-success" style="margin-left: 8px; font-size: 11px;">Storage</span>
                        </label>
                        <input type="number" name="backup_retention_days" id="backup_retention_days" class="form-control"
                               value="<?= $config['backup_retention_days'] ?? 30 ?>" min="1" max="365" style="width: 100%;">
                        <small style="color: #666; display: block; margin-top: 5px;">
                            <i class="fa fa-info-circle"></i> Backups older than this will be deleted (1-365 days)
                        </small>
                    </div>
                </div>
            </div>

            <!-- Information Box -->
            <div style="background: #f0f4f8; border-left: 4px solid #34495e; padding: 15px; margin: 25px 0; border-radius: 4px;">
                <i class="fa fa-lightbulb-o" style="color: #34495e;"></i>
                <strong style="margin-left: 8px;">Backup Information</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px; color: #666; font-size: 12px; line-height: 1.8;">
                    <li><strong>Daily backups:</strong> One backup every 24 hours</li>
                    <li><strong>Weekly backups:</strong> One backup every 7 days</li>
                    <li><strong>Monthly backups:</strong> One backup every 30 days</li>
                    <li><strong>Retention:</strong> Backups older than the specified days will be deleted automatically</li>
                    <li><strong>Storage:</strong> Each backup file is approximately equal to your database size</li>
                    <li><strong>Safety:</strong> Pre-restore backups are created automatically before restore operations</li>
                </ul>
            </div>

            <!-- Save Buttons -->
            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd;">
                <button type="button" class="btn btn-primary" style="background-color: #34495e; border-color: #34495e;" onclick="saveBackupConfig();">
                    <i class="fa fa-save"></i> Save Settings
                </button>
                <button type="reset" class="btn btn-default" style="margin-left: 10px;">
                    <i class="fa fa-refresh"></i> Reset
                </button>
            </div>
        </form>

    </div>
</div>

<style>
    .widget-box {
        background: white;
        border-radius: 4px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .widget-header {
        border-bottom: 1px solid #e5e5e5;
        border-radius: 4px 4px 0 0;
    }

    .widget-body {
        border-radius: 0 0 4px 4px;
    }

    .stats-grid {
        display: grid;
        gap: 12px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: white;
        border-radius: 4px;
        border-left: 4px solid;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
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
    }

    .stat-title {
        font-size: 11px;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
    }

    .stat-icon {
        font-size: 18px;
        opacity: 0.3;
    }

    .stat-value {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
    }

    .stat-subtitle {
        font-size: 11px;
        color: #999;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #2c3e50;
    }

    .form-control {
        padding: 8px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 13px;
        width: 100%;
    }

    .form-control:focus {
        border-color: #34495e;
        outline: none;
        box-shadow: 0 0 0 3px rgba(52, 73, 94, 0.1);
    }

    .btn {
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-block;
    }

    .btn-primary {
        background: #34495e;
        color: white;
        border-color: #34495e;
    }

    .btn-primary:hover {
        background: #2c3e50;
        border-color: #2c3e50;
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

    .label {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        color: white;
    }

    .label-info { background-color: #3498db; }
    .label-warning { background-color: #f39c12; }
    .label-danger { background-color: #e74c3c; }
    .label-success { background-color: #27ae60; }
</style>

<script>
// Ensure functions are available globally
(function() {
    // Get base URL for AJAX calls
    var baseUrl = window.location.pathname.split('/web/')[0] + '/web/';

    window.showMessage = function(type, message) {
        const msgDiv = document.getElementById('status-message');
        if (!msgDiv) {
            console.error('Status message div not found!');
            return;
        }

        msgDiv.style.display = 'block';

        if (type === 'success') {
            msgDiv.innerHTML = '<div style="padding: 15px; background: #d4edda; color: #155724; border-left: 4px solid #28a745; border-radius: 4px;"><i class="fa fa-check-circle"></i> ' + message + '</div>';
            // Auto-hide after 5 seconds for success messages
            setTimeout(() => {
                msgDiv.style.display = 'none';
            }, 5000);
        } else {
            msgDiv.innerHTML = '<div style="padding: 15px; background: #f8d7da; color: #721c24; border-left: 4px solid #f44336; border-radius: 4px;"><i class="fa fa-times-circle"></i> ' + message + '</div>';
        }
    };

    window.saveBackupConfig = function() {
        console.log('Saving backup config...');

        const formData = new FormData(document.getElementById('systembackup_form'));
        formData.append('flag', 'save');

        const url = baseUrl + 'index.php?r=settings/backup';
        console.log('Posting to:', url);

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('HTTP error, status = ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                window.showMessage('success', data.message);
                // Reload stats after save
                setTimeout(() => {
                    window.loadBackupStats();
                }, 500);
            } else {
                window.showMessage('error', data.message || 'Failed to save configuration');
            }
        })
        .catch(error => {
            console.error('Error saving config:', error);
            window.showMessage('error', 'Failed to save settings: ' + error.message);
        });
    };

    window.loadBackupStats = function() {
        const url = baseUrl + 'index.php?r=settings/backup&action=stats';
        console.log('Loading stats from:', url);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP error, status = ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Stats data:', data);
                if (data.success && data.data) {
                    const stats = data.data;
                    const totalBackupsEl = document.getElementById('total_backups');
                    const totalSizeEl = document.getElementById('total_backup_size');
                    const projectSizeEl = document.getElementById('project_size');
                    const dbResponseEl = document.getElementById('db_response_time');
                    const lastBackupEl = document.getElementById('last_backup_time');
                    const largestEl = document.getElementById('largest_backup_size');

                    if (totalBackupsEl) totalBackupsEl.textContent = stats.total_backups || 0;
                    if (totalSizeEl) totalSizeEl.textContent = stats.total_size || '0 KB';
                    if (projectSizeEl) projectSizeEl.textContent = stats.project_size || '0 MB';
                    if (dbResponseEl) dbResponseEl.textContent = stats.db_response_time || '0 ms';
                    if (lastBackupEl) lastBackupEl.textContent = stats.last_backup_time || '-';
                    if (largestEl) largestEl.textContent = stats.largest_backup_size || '0 KB';
                }
            })
            .catch(err => console.error('Error loading backup stats:', err));
    };

    // Load stats on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded, loading backup stats...');
        window.loadBackupStats();
    });
})();
</script>

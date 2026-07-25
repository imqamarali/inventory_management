<?php

$this->title = 'System Backup Management';

?>

<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-database"></i>
                System Backup Management
                <small>Database Backup & Recovery</small>
            </h3>
        </div>

        <div style="display: flex; gap: 10px;">
            <button id="createBackupBtn">
                <i class="fa fa-plus"></i>
                Create Backup
            </button>
            <button id="refreshDashboard">
                <i class="fa fa-refresh"></i>
                Refresh
            </button>
        </div>
    </div>

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

    <div class="row" style="margin-top:15px;">

        <div class="col-md-12">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-list"></i>
                    Latest Backups
                </h4>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover">

                        <thead>

                            <tr>

                                <th>#</th>
                                <th>Backup File</th>
                                <th>File Size</th>
                                <th>Created Date</th>
                                <th>Actions</th>

                            </tr>

                        </thead>

                        <tbody id="latestBackups">

                            <tr>

                                <td colspan="5" class="text-center">
                                    Loading backups...
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

<script>

    var currentRestoreFile = null;
    var restorePassword = null;
    var baseUrl = window.location.pathname.split('/web/')[0] + '/web/';

    $(function() {
        loadDashboard();

        $("#createBackupBtn").click(function() {
            createBackup();
        });

        $("#refreshDashboard").click(function() {
            loadDashboard();
        });
    });

    function loadDashboard() {
        showDashboardLoading();

        $.ajax({
            url: baseUrl + 'index.php?r=system/backup&action=stats',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                hideDashboardLoading();

                if (response.success && response.data) {
                    loadStatistics(response.data);
                    loadLatestBackups(response.data.backups || []);
                } else {
                    alert('Failed to load backup data');
                }
            },
            error: function() {
                hideDashboardLoading();
                alert('Error loading backup statistics');
            }
        });
    }

    function showDashboardLoading() {
        $(".stat-value").each(function() {
            $(this)
                .addClass("loading")
                .html("&nbsp;&nbsp;&nbsp;&nbsp;");
        });
    }

    function hideDashboardLoading() {
        $(".stat-value").removeClass("loading");
    }

    function loadStatistics(stats) {
        $("#total_backups").text(stats.total_backups || 0);
        $("#total_backup_size").text(stats.total_backup_size_formatted || '0 B');
        $("#project_size").text(stats.project_size || '0 B');
        $("#db_response_time").text((stats.db_response_time || 0) + ' ms');
        $("#largest_backup_size").text(stats.largest_backup || '0 B');

        if (stats.backups && stats.backups.length > 0) {
            $("#last_backup_time").text(stats.backups[0].date_formatted);
        }
    }

    function loadLatestBackups(backups) {
        let html = "";

        if (backups.length == 0) {
            html += "<tr>";
            html += "<td colspan='5' class='text-center'>No Backups Found.</td>";
            html += "</tr>";
        } else {
            $.each(backups, function(i, row) {
                html += "<tr>";
                html += "<td>" + (i + 1) + "</td>";
                html += "<td><code>" + row.filename + "</code></td>";
                html += "<td>" + row.size_formatted + "</td>";
                html += "<td>" + row.date_formatted + "</td>";
                html += "<td style='text-align: center;'>";
                html += "<button  onclick=\"showRestorePassword('" + row.filename + "')\" title='Restore from this backup' style='margin: 2px;'><i class='fa fa-refresh'></i> Restore</button>";
                html += "<button  onclick=\"downloadBackup('" + row.filename + "')\" title='Download backup file' style='margin: 2px;'><i class='fa fa-download'></i> Download</button>";
                html += "<button  onclick=\"deleteBackup('" + row.filename + "')\" title='Delete this backup' style='margin: 2px;'><i class='fa fa-trash'></i> Delete</button>";
                html += "</td>";
                html += "</tr>";
            });
        }

        $("#latestBackups").html(html);
    }

    function createBackup() {
        Swal.fire({
            title: 'Create Database Backup',
            html: '<i class="fa fa-database" style="font-size: 48px; color: #3498db; margin-bottom: 20px;"></i><p style="margin: 20px 0; color: #555;">This will create a new database backup.<br>This may take a few minutes.</p>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#27ae60',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: '<i class="fa fa-plus"></i> Create Backup',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                startBackupProgress();
            }
        });
    }

    function startBackupProgress() {
        Swal.fire({
            title: 'Creating Backup',
            html: '<div style="text-align: left;"><div id="progressMessage" style="margin: 20px 0; color: #2c3e50;"><i class="fa fa-spinner fa-spin"></i> Initializing backup...</div><div style="width: 100%; height: 6px; background: #ecf0f1; border-radius: 3px; overflow: hidden;"><div id="progressBar" style="width: 0%; height: 100%; background: #27ae60; transition: width 0.3s ease;"></div></div></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Simulate progress updates
        updateProgressMessage('Connecting to database...', 10);

        setTimeout(() => updateProgressMessage('Exporting database...', 40), 500);
        setTimeout(() => updateProgressMessage('Compressing backup file...', 70), 2000);
        setTimeout(() => updateProgressMessage('Finalizing backup...', 90), 4000);

        $.ajax({
            url: baseUrl + 'index.php?r=system/backup',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'create'
            },
            success: function(response) {
                updateProgressMessage('Backup completed!', 100);

                setTimeout(() => {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Backup Created Successfully',
                            html: '<p style="margin: 10px 0;">File: <strong>' + (response.file || 'backup.sql') + '</strong></p><p style="color: #666;">Size: <strong>' + (response.size ? formatBytes(response.size) : 'N/A') + '</strong></p>',
                            confirmButtonColor: '#27ae60'
                        }).then(() => {
                            loadDashboard();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Backup Failed',
                            text: response.message || 'Failed to create backup',
                            confirmButtonColor: '#e74c3c'
                        });
                    }
                }, 500);
            },
            error: function() {
                updateProgressMessage('Error creating backup!', 100);

                setTimeout(() => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while creating the backup',
                        confirmButtonColor: '#e74c3c'
                    });
                }, 500);
            }
        });
    }

    function updateProgressMessage(message, percentage) {
        const msgEl = document.getElementById('progressMessage');
        const barEl = document.getElementById('progressBar');
        if (msgEl) {
            msgEl.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ' + message;
        }
        if (barEl) {
            barEl.style.width = percentage + '%';
        }
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    function showRestorePassword(filename) {
        currentRestoreFile = filename;

        // First, load comparison data
        loadRestoreComparison(filename);
    }

    function loadRestoreComparison(filename) {
        Swal.fire({
            title: 'Analyzing Backup...',
            html: '<div style="text-align: center;"><i class="fa fa-spinner fa-spin" style="font-size: 40px; color: #3498db;"></i><p style="margin-top: 15px; color: #666;">Comparing backup with current database...</p></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: baseUrl + 'index.php?r=system/backup',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'compare',
                backup_file: filename
            },
            success: function(response) {
                Swal.close();

                if (response.success && response.data) {
                    showRestoreComparisonModal(response.data, filename);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Comparison Failed',
                        text: response.message || 'Failed to compare backup',
                        confirmButtonColor: '#e74c3c'
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while analyzing the backup',
                    confirmButtonColor: '#e74c3c'
                });
            }
        });
    }

    function showRestoreComparisonModal(comparison, filename) {
        const summary = comparison.summary;
        const tables = comparison.table_details.slice(0, 10); // Show first 10 affected tables

        let statsHtml = '<div style="text-align: left; margin-bottom: 20px;">';
        statsHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';

        // Left column stats
        statsHtml += '<div style="padding: 12px; background: #ecf0f1; border-radius: 5px;">';
        statsHtml += '<p style="margin: 5px 0; font-size: 12px; color: #666;"><strong>Total Tables</strong></p>';
        statsHtml += '<p style="margin: 5px 0; font-size: 18px; font-weight: bold; color: #2c3e50;">' + summary.total_tables_in_backup + ' backup / ' + summary.total_tables_in_current + ' current</p>';
        statsHtml += '</div>';

        statsHtml += '<div style="padding: 12px; background: #ecf0f1; border-radius: 5px;">';
        statsHtml += '<p style="margin: 5px 0; font-size: 12px; color: #666;"><strong>Total Records</strong></p>';
        statsHtml += '<p style="margin: 5px 0; font-size: 18px; font-weight: bold; color: #2c3e50;">' + summary.total_records_backup + ' backup / ' + summary.total_records_current + ' current</p>';
        statsHtml += '</div>';

        // Right column impact
        statsHtml += '<div style="padding: 12px; background: ' + (summary.tables_to_drop > 0 ? '#ffe6e6' : '#e8f8f5') + '; border-radius: 5px;">';
        statsHtml += '<p style="margin: 5px 0; font-size: 12px; color: #666;"><i class="fa fa-trash"></i> <strong>Tables to Drop</strong></p>';
        statsHtml += '<p style="margin: 5px 0; font-size: 18px; font-weight: bold; color: #e74c3c;">' + summary.tables_to_drop + '</p>';
        statsHtml += '</div>';

        statsHtml += '<div style="padding: 12px; background: ' + (summary.tables_to_create > 0 ? '#e6f3ff' : '#e8f8f5') + '; border-radius: 5px;">';
        statsHtml += '<p style="margin: 5px 0; font-size: 12px; color: #666;"><i class="fa fa-plus"></i> <strong>Tables to Create</strong></p>';
        statsHtml += '<p style="margin: 5px 0; font-size: 18px; font-weight: bold; color: #3498db;">' + summary.tables_to_create + '</p>';
        statsHtml += '</div>';

        statsHtml += '<div style="padding: 12px; background: ' + (summary.tables_to_update > 0 ? '#fff9e6' : '#e8f8f5') + '; border-radius: 5px;">';
        statsHtml += '<p style="margin: 5px 0; font-size: 12px; color: #666;"><i class="fa fa-pencil"></i> <strong>Tables to Update</strong></p>';
        statsHtml += '<p style="margin: 5px 0; font-size: 18px; font-weight: bold; color: #f39c12;">' + summary.tables_to_update + '</p>';
        statsHtml += '</div>';

        statsHtml += '<div style="padding: 12px; background: #ecf0f1; border-radius: 5px;">';
        statsHtml += '<p style="margin: 5px 0; font-size: 12px; color: #666;"><strong>Record Difference</strong></p>';
        statsHtml += '<p style="margin: 5px 0; font-size: 18px; font-weight: bold; color: #2c3e50;">' + summary.total_records_difference + '</p>';
        statsHtml += '</div>';

        statsHtml += '</div>';
        statsHtml += '</div>';

        // Build affected tables stats cards
        let affectedTablesHtml = '<div style="border-top: 2px solid #ecf0f1; padding-top: 15px; margin-top: 15px;">';
        affectedTablesHtml += '<p style="font-size: 13px; color: #2c3e50; font-weight: bold; margin-bottom: 15px;"><i class="fa fa-database"></i> Affected Tables Details</p>';
        affectedTablesHtml += '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px;">';

        tables.forEach(function(tbl) {
            let statusColor = '';
            let statusIcon = '';
            let statusLabel = '';

            if (tbl.status === 'drop') {
                statusColor = '#ffe6e6';
                statusIcon = '<i class="fa fa-trash" style="color: #e74c3c; font-size: 20px;"></i>';
                statusLabel = 'Drop';
            } else if (tbl.status === 'create') {
                statusColor = '#e6f3ff';
                statusIcon = '<i class="fa fa-plus-circle" style="color: #3498db; font-size: 20px;"></i>';
                statusLabel = 'Create';
            } else if (tbl.status === 'update') {
                statusColor = '#fff9e6';
                statusIcon = '<i class="fa fa-pencil" style="color: #f39c12; font-size: 20px;"></i>';
                statusLabel = 'Update';
            } else {
                statusColor = '#e8f8f5';
                statusIcon = '<i class="fa fa-check-circle" style="color: #27ae60; font-size: 20px;"></i>';
                statusLabel = 'OK';
            }

            const changeValue = tbl.record_difference > 0 ?
                (tbl.backup_records > tbl.current_records ? '+' + tbl.record_difference : '-' + tbl.record_difference) :
                '0';

            affectedTablesHtml += '<div style="padding: 12px; background: ' + statusColor + '; border-radius: 6px; text-align: center; border: 1px solid rgba(0,0,0,0.05);">';
            affectedTablesHtml += '<div style="margin-bottom: 8px;">' + statusIcon + '</div>';
            affectedTablesHtml += '<p style="font-size: 11px; color: #666; margin: 0 0 5px 0; font-weight: 600;">' + tbl.table + '</p>';
            affectedTablesHtml += '<p style="font-size: 12px; color: #2c3e50; margin: 0 0 3px 0; font-weight: bold;">' + tbl.backup_records + ' records</p>';
            affectedTablesHtml += '<p style="font-size: 10px; color: #999; margin: 0;">' + statusLabel + ' (' + changeValue + ')</p>';
            affectedTablesHtml += '</div>';
        });

        affectedTablesHtml += '</div>';
        affectedTablesHtml += '</div>';

        const allHtml = statsHtml + affectedTablesHtml;

        Swal.fire({
            title: 'Database Comparison',
            html: allHtml,
            width: 900,
            confirmButtonColor: '#3498db',
            confirmButtonText: '<i class="fa fa-arrow-right"></i> Continue to Password',
            showCancelButton: true,
            cancelButtonColor: '#95a5a6',
            didOpen: () => {
                // Make the modal scrollable
                const swalContent = document.querySelector('.swal2-html-container');
                if (swalContent) {
                    swalContent.style.maxHeight = '500px';
                    swalContent.style.overflowY = 'auto';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                showRestorePasswordModal();
            }
        });
    }

    function showRestorePasswordModal() {
        Swal.fire({
            title: '<i class="fa fa-lock"></i> Confirm Restore Operation',
            html: '<div style="text-align: left;"><div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; border-radius: 4px; margin-bottom: 15px;"><i class="fa fa-exclamation-triangle"></i> <strong>Warning:</strong> This will overwrite your database.<br>A backup of current data will be created automatically.</div><div class="form-group" style="margin-bottom: 15px;"><label style="font-weight: bold; margin-bottom: 8px; display: block;">Super Admin Password <span style="color: red;">*</span></label><input type="password" id="restorePasswordInput" class="form-control" placeholder="Enter super admin password" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; width: 100%;"></div><p style="font-size: 12px; color: #666;">Please enter your super admin password to confirm.</p></div>',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: '<i class="fa fa-refresh"></i> Restore Now',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const inputPassword = document.getElementById('restorePasswordInput')?.value || '';

                if (!inputPassword) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Password Required',
                        text: 'Please enter your super admin password to proceed',
                        confirmButtonColor: '#f39c12'
                    }).then(() => {
                        showRestorePasswordModal();
                    });
                    return;
                }

                restorePassword = inputPassword;
                startRestoreProgress();
            }
        });

        // Focus password field
        setTimeout(() => {
            document.getElementById('restorePasswordInput')?.focus();
        }, 200);
    }


    function startRestoreProgress() {
        Swal.fire({
            title: 'Restoring Database',
            html: '<div style="text-align: left;"><div id="restoreMessage" style="margin: 20px 0; color: #2c3e50;"><i class="fa fa-spinner fa-spin"></i> Creating pre-restore backup...</div><div style="width: 100%; height: 6px; background: #ecf0f1; border-radius: 3px; overflow: hidden;"><div id="restoreBar" style="width: 0%; height: 100%; background: #e74c3c; transition: width 0.3s ease;"></div></div></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Simulate progress updates
        updateRestoreMessage('Creating pre-restore backup...', 20);
        setTimeout(() => updateRestoreMessage('Dropping current tables...', 40), 1000);
        setTimeout(() => updateRestoreMessage('Restoring database tables...', 70), 2500);
        setTimeout(() => updateRestoreMessage('Verifying data integrity...', 90), 4000);

        $.ajax({
            url: baseUrl + 'index.php?r=system/backup',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'restore',
                backup_file: currentRestoreFile,
                password: restorePassword
            },
            success: function(response) {
                updateRestoreMessage('Restore completed!', 100);

                setTimeout(() => {
                    Swal.close();
                    restorePassword = null;

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Restore Successful',
                            html: '<div style="text-align: left;"><p style="margin: 15px 0;"><i class="fa fa-check-circle" style="color: #27ae60; margin-right: 10px;"></i>Your database has been restored successfully!</p><p style="font-size: 13px; color: #666; margin-top: 15px;">Pre-restore backup: <strong>' + (response.pre_backup || 'N/A') + '</strong></p><p style="font-size: 13px; color: #666;">Restored from: <strong>' + (response.restored_from || 'N/A') + '</strong></p></div>',
                            confirmButtonColor: '#27ae60'
                        }).then(() => {
                            loadDashboard();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Restore Failed',
                            text: response.message || 'Failed to restore backup',
                            confirmButtonColor: '#e74c3c'
                        });
                    }
                }, 500);
            },
            error: function() {
                updateRestoreMessage('Restore failed!', 100);

                setTimeout(() => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while restoring the backup',
                        confirmButtonColor: '#e74c3c'
                    });
                }, 500);
            }
        });
    }

    function updateRestoreMessage(message, percentage) {
        const msgEl = document.getElementById('restoreMessage');
        const barEl = document.getElementById('restoreBar');
        if (msgEl) {
            msgEl.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ' + message;
        }
        if (barEl) {
            barEl.style.width = percentage + '%';
        }
    }

    function downloadBackup(filename) {
        window.location.href = baseUrl + 'index.php?r=system/backup&action=download&file=' + encodeURIComponent(filename);
    }

    function deleteBackup(filename) {
        Swal.fire({
            title: 'Delete Backup File',
            html: '<p style="margin: 15px 0; color: #555;"><strong>File:</strong> ' + filename + '</p><p style="color: #e74c3c; margin-top: 15px;"><i class="fa fa-exclamation-triangle"></i> <strong>This action cannot be undone!</strong></p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: '<i class="fa fa-trash"></i> Delete Backup',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                startDeleteProgress(filename);
            }
        });
    }

    function startDeleteProgress(filename) {
        Swal.fire({
            title: 'Deleting Backup',
            html: '<div style="text-align: left;"><div id="deleteMessage" style="margin: 20px 0; color: #2c3e50;"><i class="fa fa-spinner fa-spin"></i> Removing backup file...</div><div style="width: 100%; height: 6px; background: #ecf0f1; border-radius: 3px; overflow: hidden;"><div id="deleteBar" style="width: 0%; height: 100%; background: #e74c3c; transition: width 0.3s ease;"></div></div></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        updateDeleteMessage('Verifying backup file...', 20);
        setTimeout(() => updateDeleteMessage('Removing from storage...', 60), 500);
        setTimeout(() => updateDeleteMessage('Updating records...', 90), 1500);

        $.ajax({
            url: baseUrl + 'index.php?r=system/backup',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'delete',
                backup_file: filename
            },
            success: function(response) {
                updateDeleteMessage('Backup deleted!', 100);

                setTimeout(() => {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Backup Deleted',
                            text: 'Backup file deleted successfully',
                            confirmButtonColor: '#27ae60'
                        }).then(() => {
                            loadDashboard();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Delete Failed',
                            text: response.message || 'Failed to delete backup',
                            confirmButtonColor: '#e74c3c'
                        });
                    }
                }, 500);
            },
            error: function() {
                updateDeleteMessage('Error deleting backup!', 100);

                setTimeout(() => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while deleting the backup',
                        confirmButtonColor: '#e74c3c'
                    });
                }, 500);
            }
        });
    }

    function updateDeleteMessage(message, percentage) {
        const msgEl = document.getElementById('deleteMessage');
        const barEl = document.getElementById('deleteBar');
        if (msgEl) {
            msgEl.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ' + message;
        }
        if (barEl) {
            barEl.style.width = percentage + '%';
        }
    }

</script>

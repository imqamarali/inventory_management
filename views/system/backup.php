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
            <button id="createBackupBtn" class="btn btn-success">
                <i class="fa fa-plus"></i>
                Create Backup
            </button>
            <button id="refreshDashboard" class="btn btn-default">
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
    var baseUrl = window.location.pathname.split('/web/')[0] + '/web/';

    $(function() {
        loadDashboard();

        $("#createBackupBtn").click(function() {
            createBackup();
        });

        $("#refreshDashboard").click(function() {
            loadDashboard();
        });

        $("#confirmRestoreBtn").click(function() {
            restoreBackup();
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
                html += "<button class='btn btn-sm btn-info' onclick=\"showRestorePassword('" + row.filename + "')\" title='Restore from this backup' style='margin: 2px;'><i class='fa fa-refresh'></i> Restore</button>";
                html += "<button class='btn btn-sm btn-primary' onclick=\"downloadBackup('" + row.filename + "')\" title='Download backup file' style='margin: 2px;'><i class='fa fa-download'></i> Download</button>";
                html += "<button class='btn btn-sm btn-danger' onclick=\"deleteBackup('" + row.filename + "')\" title='Delete this backup' style='margin: 2px;'><i class='fa fa-trash'></i> Delete</button>";
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
        $('#restorePasswordModal').modal('show');
    }

    function restoreBackup() {
        const password = $('#restorePassword').val();

        if (!password) {
            Swal.fire({
                icon: 'warning',
                title: 'Password Required',
                text: 'Please enter your super admin password to proceed',
                confirmButtonColor: '#f39c12'
            });
            return;
        }

        // Show final confirmation with file details
        Swal.fire({
            title: 'Confirm Restore Operation',
            html: '<div style="text-align: left; color: #555;"><p><strong>File:</strong> ' + currentRestoreFile + '</p><p style="margin-top: 10px; color: #e74c3c;"><i class="fa fa-exclamation-triangle"></i> <strong>This will overwrite your current database!</strong></p><p style="margin-top: 10px; font-size: 13px;">A backup of your current data will be created automatically.</p></div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: '<i class="fa fa-refresh"></i> Restore Now',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                startRestoreProgress();
            }
        });
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

        const password = $('#restorePassword').val();

        $.ajax({
            url: baseUrl + 'index.php?r=system/backup',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'restore',
                backup_file: currentRestoreFile,
                password: password
            },
            success: function(response) {
                updateRestoreMessage('Restore completed!', 100);

                setTimeout(() => {
                    Swal.close();
                    $('#restorePasswordModal').modal('hide');
                    $('#restorePassword').val('');

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Restore Successful',
                            html: '<p style="margin: 15px 0;">Your database has been restored successfully!</p><p style="font-size: 13px; color: #666;">Pre-restore backup: <strong>' + (response.pre_backup || 'N/A') + '</strong></p>',
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

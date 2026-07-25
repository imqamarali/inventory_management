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
        if (!confirm('Create a new database backup? This may take a few minutes.')) {
            return;
        }

        Swal.fire({
            title: 'Creating Backup',
            html: '<i class="fa fa-spinner fa-spin" style="font-size: 48px; color: #3498db;"></i><p style="margin-top: 20px;">Please wait, creating backup...</p>',
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
                action: 'create'
            },
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Backup Created',
                        text: 'Database backup created successfully!',
                        confirmButtonColor: '#27ae60'
                    }).then(() => {
                        loadDashboard();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to create backup'
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error creating backup'
                });
            }
        });
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
                text: 'Please enter super admin password'
            });
            return;
        }

        Swal.fire({
            title: 'Restoring Backup',
            html: '<i class="fa fa-spinner fa-spin" style="font-size: 48px; color: #3498db;"></i><p style="margin-top: 20px;">Please wait, restoring database...</p>',
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
                action: 'restore',
                backup_file: currentRestoreFile,
                password: password
            },
            success: function(response) {
                Swal.close();
                $('#restorePasswordModal').modal('hide');
                $('#restorePassword').val('');

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Restore Complete',
                        text: 'Database restored successfully!',
                        confirmButtonColor: '#27ae60'
                    }).then(() => {
                        loadDashboard();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Restore Failed',
                        text: response.message || 'Failed to restore backup'
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error restoring backup'
                });
            }
        });
    }

    function downloadBackup(filename) {
        window.location.href = baseUrl + 'index.php?r=system/backup&action=download&file=' + encodeURIComponent(filename);
    }

    function deleteBackup(filename) {
        if (!confirm('Delete this backup?\n' + filename + '\n\nThis action cannot be undone!')) {
            return;
        }

        Swal.fire({
            title: 'Deleting Backup',
            html: '<i class="fa fa-spinner fa-spin" style="font-size: 48px; color: #e74c3c;"></i><p style="margin-top: 20px;">Deleting backup file...</p>',
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
                action: 'delete',
                backup_file: filename
            },
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: 'Backup deleted successfully',
                        confirmButtonColor: '#27ae60'
                    }).then(() => {
                        loadDashboard();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to delete backup'
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error deleting backup'
                });
            }
        });
    }

</script>

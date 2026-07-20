<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php">Home</a>
                </li>
                <li class="active">Truncate System</li>
            </ul>
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <!-- Danger Alert -->
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert">
                            <i class="ace-icon fa fa-times"></i>
                        </button>
                        <strong>
                            <i class="ace-icon fa fa-exclamation-triangle"></i>
                            DANGER ZONE - IRREVERSIBLE ACTION!
                        </strong>
                        <br>
                        This action will permanently delete ALL data from the database including:
                        <ul style="margin-top: 10px;">
                            <li>All Students and their records</li>
                            <li>All Staff members (except Super Admin)</li>
                            <li>All Classes, Sections, and Subjects</li>
                            <li>All Attendance records</li>
                            <li>All Fee records and payments</li>
                            <li>All Exam records and results</li>
                            <li>All Documents and files references</li>
                            <li>All Meeting records</li>
                            <li>All Tickets and support data</li>
                        </ul>
                        <strong style="color: #d15b47;">Only the Super Admin account will be preserved.</strong>
                    </div>

                    <!-- Truncate Form -->
                    <div class="widget-box">
                        <div class="widget-header widget-header-red widget-header-flat">
                            <h4 class="widget-title lighter">
                                <i class="ace-icon fa fa-database"></i>
                                System Truncation
                            </h4>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <form id="truncate-form">
                                    <input type="hidden" name="_csrf"
                                        value="<?= Yii::$app->request->getCsrfToken() ?>" />

                                    <div class="form-group">
                                        <label style="font-size: 16px; font-weight: bold; color: #d15b47;">
                                            To confirm truncation, type exactly: <code>YES_TRUNCATE_ALL</code>
                                        </label>
                                        <input type="text" class="form-control" id="confirm_truncate"
                                            name="confirm_truncate" placeholder="Type confirmation code here"
                                            style="font-size: 18px; font-weight: bold; border: 2px solid #d15b47;"
                                            autocomplete="off" />
                                    </div>

                                    <div class="space-8"></div>

                                    <div class="alert alert-info">
                                        <i class="ace-icon fa fa-shield"></i>
                                        <strong>Safety Feature - Automatic Backup:</strong>
                                        <p style="margin-top: 10px; margin-bottom: 0;">
                                            Before truncation, the system will automatically create a complete database
                                            backup and save it to:
                                            <code>web/database_backups/backup_before_truncate_[timestamp].sql</code>
                                        </p>
                                    </div>

                                    <div class="alert alert-warning">
                                        <i class="ace-icon fa fa-info-circle"></i>
                                        <strong>What will happen:</strong>
                                        <ol style="margin-top: 10px;">
                                            <li><strong>Step 1:</strong> Complete database backup will be created
                                                automatically</li>
                                            <li><strong>Step 2:</strong> System will disable foreign key constraints
                                            </li>
                                            <li><strong>Step 3:</strong> All child tables will be truncated first</li>
                                            <li><strong>Step 4:</strong> All parent tables will be truncated next</li>
                                            <li><strong>Step 5:</strong> Classes, Sections, and Subjects will be removed
                                            </li>
                                            <li><strong>Step 6:</strong> All user accounts except Super Admin will be
                                                deleted</li>
                                            <li><strong>Step 7:</strong> Auto-increment IDs will reset to 1</li>
                                            <li><strong>Step 8:</strong> Foreign key constraints will be re-enabled</li>
                                            <li><strong>Step 9:</strong> You will remain logged in as Super Admin</li>
                                        </ol>
                                    </div>

                                    <div class="clearfix form-actions" style="margin-top: 20px;">
                                        <button type="submit" class="btn btn-danger" id="truncateBtn" disabled>
                                            <i class="ace-icon fa fa-trash bigger-110"></i>
                                            Truncate Entire System
                                        </button>
                                        <a href="index.php" class="btn btn-default">
                                            <i class="ace-icon fa fa-undo"></i>
                                            Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade in" id="lmyModal" tabindex="-1" role="dialog" style="display:none;">
    <div class="modal-dialog" style="width:50%;" role="document">
        <div class="modal-content" style="background: rgba(255,255,255,0.98); border-radius: 8px;">
            <div class="modal-body" style="padding: 40px;">
                <div id="loader" style="text-align: center;">
                    <i class="ace-icon fa fa-spinner fa-spin orange" style="font-size: 80px"></i>
                    <h3 style="color: #f59942; margin-top: 20px;">Processing...</h3>
                    <div style="margin-top: 20px;">
                        <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; text-align: left;">
                            <p style="margin: 5px 0; color: #666;">
                                <i class="fa fa-check-circle" style="color: #5cb85c;"></i>
                                Creating database backup...
                            </p>
                            <p style="margin: 5px 0; color: #666;">
                                <i class="fa fa-database"></i>
                                Truncating tables...
                            </p>
                            <p style="margin: 5px 0; color: #666;">
                                <i class="fa fa-cog"></i>
                                Resetting auto-increment values...
                            </p>
                        </div>
                        <p style="color: #999; margin-top: 15px; font-size: 14px;">
                            Please wait, this may take a few moments...
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Enable button only when correct confirmation code is entered
        $('#confirm_truncate').on('input', function() {
            const confirmCode = $(this).val();
            if (confirmCode === 'YES_TRUNCATE_ALL') {
                $('#truncateBtn').prop('disabled', false);
                $('#truncateBtn').removeClass('btn-danger').addClass('btn-danger');
            } else {
                $('#truncateBtn').prop('disabled', true);
            }
        });

        // Form submission with AJAX
        $('#truncate-form').on('submit', function(e) {
            e.preventDefault();

            const confirmCode = $('#confirm_truncate').val();

            if (confirmCode !== 'YES_TRUNCATE_ALL') {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Confirmation',
                    text: 'Please type the exact confirmation code: YES_TRUNCATE_ALL',
                });
                return;
            }

            // Double confirmation with SweetAlert
            Swal.fire({
                title: 'Final Confirmation',
                html: `
                <p style="font-size: 16px; color: #d15b47; font-weight: bold;">
                    ⚠️ This will DELETE ALL DATA from the system!
                </p>
                <p>Are you absolutely sure you want to proceed?</p>
                <p style="font-size: 14px; color: #999;">
                    This action cannot be undone!
                </p>
            `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Truncate Everything!',
                cancelButtonText: 'No, Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    performTruncate();
                }
            });
        });

        function performTruncate() {
            var formData = new FormData(document.getElementById('truncate-form'));

            $.ajax({
                url: 'index.php?r=site/truncatesystem',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#lmyModal').show();
                },
                complete: function() {
                    $('#lmyModal').hide();
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'System Truncated Successfully!',
                        html: `
                        <p style="font-size: 16px; margin-bottom: 15px;">${response}</p>
                        <div style="background: #f0f9ff; padding: 15px; border-radius: 8px; border-left: 4px solid #5cb85c; margin: 15px 0;">
                            <p style="margin: 0; color: #5cb85c;">
                                <i class="fa fa-check-circle"></i> 
                                <strong>The system has been reset to initial state.</strong>
                            </p>
                            <p style="margin: 10px 0 0 0; color: #666; font-size: 14px;">
                                <i class="fa fa-shield"></i> 
                                A backup has been saved in the database_backups folder.
                            </p>
                        </div>
                        <p style="font-size: 14px; color: #999;">
                            You can restore this backup later if needed.
                        </p>
                    `,
                        confirmButtonText: 'Go to Dashboard',
                        allowOutsideClick: false,
                        width: '600px'
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                },
                error: function(xhr, status, error) {
                    var errorMessage = 'Failed to truncate system';
                    if (xhr.responseText) {
                        errorMessage = xhr.responseText;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Truncation Failed!',
                        html: `
                        <p style="color: #d15b47;">${errorMessage}</p>
                        <p style="margin-top: 10px; font-size: 14px; color: #999;">
                            The system remains unchanged.
                        </p>
                    `,
                    });
                }
            });
        }
    });
</script>

<style>
    .widget-header-red {
        background: linear-gradient(135deg, #d15b47 0%, #c14439 100%);
        color: white;
        border: none;
    }

    code {
        background: #f5f5f5;
        padding: 3px 8px;
        border-radius: 3px;
        color: #d15b47;
        font-weight: bold;
        font-size: 14px;
    }

    .alert-danger {
        border-left: 4px solid #d15b47;
    }

    .alert-warning {
        border-left: 4px solid #f59942;
    }

    #confirm_truncate:focus {
        border-color: #d15b47;
        box-shadow: 0 0 8px rgba(209, 91, 71, 0.3);
    }
</style>
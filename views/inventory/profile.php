<?php

use yii\helpers\Html;

$this->title = 'My Profile';
?>
<style>
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .page-content {
            padding: 8px 10px !important;
        }

        .widget-box {
            margin-bottom: 15px;
        }

        .widget-main {
            padding: 10px !important;
        }

        /* Profile Picture Section */
        .col-sm-2 {
            width: 100%;
            margin-bottom: 15px;
        }

        #profilePicture {
            width: 100px !important;
            height: 100px !important;
        }

        .widget-toolbar {
            margin-top: 10px;
        }

        .widget-toolbar .btn {
            width: 100%;
            margin-bottom: 5px;
        }

        /* Information Columns */
        .col-sm-3,
        .col-sm-4 {
            width: 100%;
            margin-bottom: 10px;
            padding: 0 5px;
        }

        .widget-main {
            padding: 10px !important;
        }

        /* Two items per row on mobile - Grid layout */
        .profile-user-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border: 1px solid #e8e8e8;
            border-radius: 4px;
            overflow: hidden;
        }

        .profile-info-row {
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 8px 6px;
            border-right: 1px solid #e8e8e8;
            border-bottom: 1px solid #e8e8e8;
            min-height: auto;
        }

        /* Remove right border for even items (right column) */
        .profile-info-row:nth-child(even) {
            border-right: none;
        }

        /* Remove bottom border for last two items */
        .profile-info-row:nth-last-child(-n+2) {
            border-bottom: none;
        }

        /* If odd number of items, make last item span full width */
        .profile-info-row:last-child:nth-child(odd) {
            grid-column: 1 / -1;
            border-right: none;
        }

        .profile-info-name {
            width: 42% !important;
            font-weight: 600;
            margin: 0;
            margin-right: 6px;
            font-size: 10px;
            color: #666;
            flex-shrink: 0;
            line-height: 1.3;
        }

        .profile-info-value {
            width: 58% !important;
            font-size: 11px;
            text-align: left !important;
            flex: 1;
            margin: 0;
            line-height: 1.3;
            word-break: break-word;
        }

        .profile-info-value span,
        .profile-info-value a {
            font-size: 11px;
            line-height: 1.3;
        }

        /* Chart Sections */
        .col-sm-6 {
            width: 100%;
            margin-bottom: 15px;
        }

        #attendanceDonutChart,
        #feeDonutChart {
            max-width: 150px !important;
            max-height: 150px !important;
        }

        .widget-header {
            padding: 8px 10px !important;
        }

        .widget-title {
            font-size: 13px !important;
        }

        .widget-toolbar .label {
            font-size: 9px !important;
            padding: 3px 6px !important;
            margin-right: 3px !important;
            margin-bottom: 3px;
            display: inline-block;
        }

        .widget-header .widget-toolbar {
            flex-wrap: wrap;
            margin-top: 5px;
        }

        .chart-percentage {
            font-size: 24px !important;
        }

        .chart-label {
            font-size: 10px !important;
        }
    }

    @media (max-width: 480px) {
        .page-content {
            padding: 5px 8px !important;
        }

        .widget-main {
            padding: 8px !important;
        }

        #profilePicture {
            width: 80px !important;
            height: 80px !important;
        }

        .col-sm-3,
        .col-sm-4 {
            margin-bottom: 8px;
            padding: 0 3px;
        }

        .widget-main {
            padding: 8px !important;
        }

        .profile-info-row {
            flex-direction: row;
            padding: 6px 4px;
            min-height: auto;
        }

        .profile-info-name {
            width: 42% !important;
            font-size: 9px;
            margin-right: 4px;
            line-height: 1.2;
        }

        .profile-info-value {
            width: 58% !important;
            font-size: 10px;
            text-align: left !important;
            line-height: 1.2;
        }

        .profile-info-value span,
        .profile-info-value a {
            font-size: 10px;
            line-height: 1.2;
        }

        #attendanceDonutChart,
        #feeDonutChart {
            max-width: 120px !important;
            max-height: 120px !important;
        }

        .widget-title {
            font-size: 12px !important;
        }

        .widget-toolbar .label {
            font-size: 8px !important;
            padding: 2px 5px !important;
        }

        .widget-header {
            padding: 6px 8px !important;
        }

        .chart-percentage {
            font-size: 20px !important;
        }

        .chart-label {
            font-size: 9px !important;
        }
    }
</style>
<?php

$student = $student ?? [];
$fullName = trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));
$className = $student['class_name'] ?? 'N/A';
$sectionName = $student['section_name'] ?? 'N/A';
$admissionNo = $student['admission_no'] ?? 'N/A';
$rollNo = $student['roll_no'] ?? 'N/A';
$email = $student['email'] ?? 'N/A';
$mobile = $student['mobile_number'] ?? 'N/A';
$bloodGroup = $student['blood_group_name'] ?? 'N/A';
$category = $student['category_name'] ?? 'N/A';
$sessionName = $student['session_name'] ?? 'N/A';
$schoolName = $student['school_name'] ?? 'School';

$attPct = isset($attendance_percentage) ? (float)$attendance_percentage : 0.0;
$fee = $fee_summary ?? ['total_due' => 0, 'total_paid' => 0, 'total_balance' => 0];
$stats = $stats ?? ['assignments' => 0];
$permissions = $permissions ?? ['can_view' => 0, 'can_add' => 0, 'can_edit' => 0, 'can_delete' => 0];
$can_edit = isset($permissions['can_edit']) && $permissions['can_edit'] == 1;
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<div class="main-content">
    <div class="main-content-inner">

        <div>
            <!-- Personal Information -->
            <div class="">

                <div class="row">
                    <div class="col-sm-2">
                        <div class="widget-body">
                            <div class="widget-main"
                                style="padding: 15px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100%;">
                                <!-- Profile Picture -->
                                <div
                                    style="margin-bottom: 15px; display: flex; justify-content: center; position: relative;">
                                    <a href="#" onclick="openUpdateProfilePictureModal(); return false;"
                                        style="position: relative; display: inline-block; cursor: pointer; text-decoration: none;">
                                        <img id="profilePicture"
                                            src="<?= !empty($student['photo_path']) ? $student['photo_path'] : 'https://cdn.pixabay.com/photo/2015/03/04/22/35/avatar-659652_640.png' ?>"
                                            alt="Student Photo"
                                            style="width: 120px; height: 120px; border-radius: 50%; border: 3px solid #ddd; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); object-fit: cover;">
                                        <div
                                            style="position: absolute; bottom: 0; right: 0; background: #4dabf7; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                            <i class="ace-icon fa fa-camera" style="color: white; font-size: 16px;"></i>
                                        </div>
                                    </a>
                                </div>

                                <!-- Status -->
                                <div style="margin: 10px 0; text-align: center;">
                                    <?php if ($student['is_disabled'] ?? false): ?>
                                        <span class="label label-danger arrowed-in"
                                            style="font-size: 12px; padding: 5px 12px; display: inline-block;">
                                            <i class="ace-icon fa fa-ban"></i> Disabled
                                        </span>
                                    <?php else: ?>
                                        <span class="label label-success arrowed-in"
                                            style="font-size: 12px; padding: 5px 12px; display: inline-block;">
                                            <i class="ace-icon fa fa-check"></i> Active
                                        </span>
                                    <?php endif; ?>

                                </div>

                                <div class="widget-toolbar">
                                    <a href="<?= \yii\helpers\Url::to(['inventory/edit-profile']) ?>"
                                        class="" title="Edit Profile">
                                        <i class="ace-icon fa fa-pencil"></i> Edit Profile
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Personal Information -->
                    <div class="col-sm-5">
                        <div class="widget-body">
                            <div class="widget-main" style="padding: 15px;">
                                <div class="profile-user-info profile-user-info-striped">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name" style="width: 40%;">System User ID</div>
                                        <div class="profile-info-value" style="width: 60%;">
                                            <span
                                                class="label label-primary"><?= htmlspecialchars($student['student_id'] ?? '') ?></span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Full Name</div>
                                        <div class="profile-info-value">
                                            <span><strong><?= htmlspecialchars($student['student_name'] ?? '') ?></strong></span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Gender</div>
                                        <div class="profile-info-value">
                                            <span>
                                                <i
                                                    class="fa fa-<?= ($student['gender'] ?? '') == 'Male' ? 'male' : 'female' ?>"></i>
                                                <?= htmlspecialchars($student['gender'] ?? 'N/A') ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Date of Birth</div>
                                        <div class="profile-info-value">
                                            <span>
                                                <?php
                                                if (!empty($student['dob'])) {
                                                    $dob = new DateTime($student['dob']);
                                                    $age = $dob->diff(new DateTime())->y;
                                                    echo htmlspecialchars($dob->format('M d, Y')) . " <small>($age years)</small>";
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Email</div>
                                        <div class="profile-info-value">
                                            <a href="mailto:<?= htmlspecialchars($student['email'] ?? '') ?>">
                                                <i class="fa fa-envelope"></i>
                                                <?= htmlspecialchars($student['email'] ?? 'N/A') ?>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Mobile Number</div>
                                        <div class="profile-info-value">
                                            <a href="tel:<?= htmlspecialchars($student['mobile_number'] ?? '') ?>">
                                                <i class="fa fa-phone"></i>
                                                <?= htmlspecialchars($student['mobile_number'] ?? 'N/A') ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <div class="widget-body">
                            <div class="widget-main" style="padding: 15px;">
                                <div class="profile-user-info profile-user-info-striped">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Father's Name</div>
                                        <div class="profile-info-value">
                                            <span><?= htmlspecialchars($student['father_name'] ?? 'N/A') ?></span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Father's Phone</div>
                                        <div class="profile-info-value">
                                            <a href="tel:<?= htmlspecialchars($student['father_phone'] ?? '') ?>">
                                                <?= htmlspecialchars($student['father_phone'] ?? 'N/A') ?>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name" style="width: 150px;">Father's Occupation
                                        </div>
                                        <div class="profile-info-value">
                                            <span><?= htmlspecialchars($student['father_occupation'] ?? 'N/A') ?></span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Mother's Name</div>
                                        <div class="profile-info-value">
                                            <span><?= htmlspecialchars($student['mother_name'] ?? 'N/A') ?></span>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Mother's Phone</div>
                                        <div class="profile-info-value">
                                            <a href="tel:<?= htmlspecialchars($student['mother_phone'] ?? '') ?>">
                                                <?= htmlspecialchars($student['mother_phone'] ?? 'N/A') ?>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Mother's Occupation</div>
                                        <div class="profile-info-value">
                                            <span><?= htmlspecialchars($student['mother_occupation'] ?? 'N/A') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="page-content">

                <div class="row" style="margin-top: 20px;"> 
                        <div class="widget-box">
                            <div class="widget-header widget-header-flat widget-header-small">
                                <h5 class="widget-title">
                                    <i class="ace-icon fa fa-lock"></i>
                                    Login Credentials
                                </h5>
                            </div>
                            <div class="widget-body">
                                <div class="widget-main">
                                    <form id="update-credentials-form" method="post">
                                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                                            value="<?= Yii::$app->request->csrfToken ?>">

                                        <div class="row" style="margin-top:20px;">

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Username</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        name="username"
                                                        value="<?= htmlspecialchars(Yii::$app->session->get('user_array')['username'] ?? '') ?>"
                                                        placeholder="Enter username"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Current Password</label>
                                                    <input type="password"
                                                        class="form-control"
                                                        name="current_password"
                                                        placeholder="Enter current password"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>New Password</label>
                                                    <input type="password"
                                                        class="form-control"
                                                        name="new_password"
                                                        placeholder="Enter new password"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Confirm Password</label>
                                                    <input type="password"
                                                        class="form-control"
                                                        name="confirm_password"
                                                        placeholder="Confirm new password"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="col-md-2" style="margin-top: 32px;">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="">
                                                    <i class="ace-icon fa fa-save bigger-110"></i>
                                                    Update Login
                                                </button>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div> 
                </div>
            </div>
        </div>

    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Initialize Attendance Donut Chart
        document
            .addEventListener(
                'DOMContentLoaded',
                function() {
                    const
                        ctx =
                        document
                        .getElementById(
                            'attendanceDonutChart'
                        );
                    if (
                        ctx
                    ) {
                        const
                            present =
                            <?= $attendance_summary['present'] ?? 0 ?>;
                        const
                            absent =
                            <?= $attendance_summary['absent'] ?? 0 ?>;
                        const
                            late =
                            <?= $attendance_summary['late'] ?? 0 ?>;
                        const
                            total =
                            <?= $attendance_summary['total_days'] ?? 0 ?>;

                        new Chart
                            (ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: [
                                        'Present',
                                        'Absent',
                                        'Late'
                                    ],
                                    datasets: [{
                                        data: [present,
                                            absent,
                                            late
                                        ],
                                        backgroundColor: [
                                            '#87B87F',
                                            '#D15B47',
                                            '#ffa726'
                                        ],
                                        borderWidth: 0,
                                        cutout: '70%'
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: true,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(
                                                    context
                                                ) {
                                                    let label =
                                                        context
                                                        .label ||
                                                        '';
                                                    if (
                                                        label
                                                    ) {
                                                        label
                                                            +=
                                                            ': ';
                                                    }
                                                    label
                                                        +=
                                                        context
                                                        .parsed +
                                                        ' days';
                                                    return label;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                    }
                }
            );

        // Handle login credentials update
        $(document).ready(function() {
            $('#update-credentials-form').on('submit', function(e) {
                e.preventDefault();

                const form = this;
                const formData = $(form).serialize();

                Swal.fire({
                    title: 'Updating Login...',
                    text: 'Please wait while we update your login credentials',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['inventory/update-credentials']) ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response && response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message ||
                                    'Login credentials updated successfully!',
                                timer: 2500,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload page to reflect updated username in UI/session if needed
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: (response && response.message) ? response
                                    .message : 'Failed to update login credentials'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        let errorMsg = 'An error occurred while updating login credentials';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                });
            });
        });

        // Initialize Fee Payment Donut Chart
        document.addEventListener('DOMContentLoaded', function() {
            const feeCtx = document.getElementById('feeDonutChart');
            if (feeCtx) {
                const totalFee = <?= $fee_summary['total_fee'] ?? 0 ?>;
                const totalPaid = <?= $fee_summary['total_paid'] ?? 0 ?>;
                const totalBalance = <?= $fee_summary['total_balance'] ?? 0 ?>;

                new Chart(feeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Paid', 'Balance'],
                        datasets: [{
                            data: [totalPaid, totalBalance],
                            backgroundColor: [
                                '#87B87F',
                                '#D15B47'
                            ],
                            borderWidth: 0,
                            cutout: '70%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += 'Rs. ' + context.parsed
                                            .toLocaleString();
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });

        // Check payment integration before redirecting
        function checkPaymentIntegrationAndRedirect(
            studentParam
        ) {
            // Show loading
            Swal.fire({
                title: 'Checking...',
                text: 'Please wait while we check payment availability',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal
                        .showLoading();
                }
            });

            // Check payment gateways via AJAX
            $.ajax({
                url: 'index.php?r=onlinepayments/get-gateways',
                type: 'GET',
                dataType: 'json',
                success: function(
                    response
                ) {
                    if (response
                        .success &&
                        response
                        .count >
                        0
                    ) {
                        // Payment is integrated, make POST request to dashboard
                        Swal
                            .close();

                        // Extract student_id from studentParam
                        var studentId =
                            '';
                        if (studentParam &&
                            studentParam
                            .includes(
                                'student_id='
                            )
                        ) {
                            studentId
                                =
                                studentParam
                                .split(
                                    'student_id='
                                )[
                                    1
                                ]
                                .split(
                                    '&'
                                )[
                                    0
                                ];
                        } else {
                            // For student role, get from current student data
                            studentId
                                =
                                <?= json_encode($student['student_id'] ?? '') ?>;
                        }

                        // Create a form and submit it as POST
                        var form =
                            $('<form>', {
                                'method': 'POST',
                                'action': 'index.php?r=onlinepayments/dashboard'
                            });

                        // Add student_id as hidden input (if not student role)
                        if (
                            studentId
                        ) {
                            form.append(
                                $('<input>', {
                                    'type': 'hidden',
                                    'name': 'student_id',
                                    'value': studentId
                                })
                            );
                        }

                        // Add CSRF token
                        form.append(
                            $('<input>', {
                                'type': 'hidden',
                                'name': '<?= Yii::$app->request->csrfParam ?>',
                                'value': '<?= Yii::$app->request->csrfToken ?>'
                            })
                        );

                        // Append form to body and submit
                        $('body')
                            .append(
                                form
                            );
                        form
                            .submit();
                    } else {
                        // Payment is not integrated
                        Swal.fire({
                            icon: 'error',
                            title: 'Online Payment Not Integrated',
                            html: `
                            <div style="padding: 20px; text-align: center;">
                            <div style="font-size: 64px; color: #d9534f; margin-bottom: 20px;">
                                <i class="fa fa-exclamation-circle"></i>
                            </div>
                            <p style="font-size: 16px; color: #333; margin-bottom: 15px;">
                                Online payment gateway is not configured in the system.
                            </p>
                            <p style="font-size: 14px; color: #666; margin-bottom: 20px;">
                                To enable online payments, please ask your administrator to configure at least one payment gateway (Stripe, EasyPaisa, JazzCash, or Meezan Bank).
                            </p>
                            <div style="background: #fff3cd; padding: 12px; border-radius: 6px; border-left: 4px solid #ffc107; text-align: left;">
                                <i class="fa fa-info-circle" style="color: #856404;"></i>
                                <span style="font-size: 13px; color: #856404; margin-left: 5px;">
                                    For now, please use offline payment methods or visit the fee counter.
                                </span>
                            </div>
                        </div>
                    `,
                            width: '600px',
                            confirmButtonText: '<i class="fa fa-times"></i> Close',
                            confirmButtonColor: '#6c757d'
                        });
                    }
                },
                error: function(
                    xhr,
                    status,
                    error
                ) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: 'Unable to check payment gateway status. Please try again later.',
                        confirmButtonColor: '#d9534f'
                    });
                }
            });
        }

        $(document)
            .ready(
                function() {
                    // Check for flash messages
                    <?php if (Yii::$app->session->hasFlash('toast')): ?>
                        Swal.fire({
                            icon: 'info',
                            title: 'Notification',
                            text: '<?= Yii::$app->session->getFlash('toast') ?>',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    <?php endif; ?>

                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '<?= Yii::$app->session->getFlash('error') ?>',
                            confirmButtonColor: '#d9534f'
                        });
                    <?php endif; ?>
                }
            );
    </script>

    <script>
        // Open SweetAlert modal for profile picture update
        function openUpdateProfilePictureModal() {
            const currentPicture = document.getElementById('profilePicture').src;

            Swal.fire({
                title: '<i class="ace-icon fa fa-camera" style="color: #4dabf7;"></i> Update Profile Picture',
                html: `
            <div style="text-align: center; padding: 20px 0;">
                <div style="margin-bottom: 20px;">
                    <img id="swalProfilePreview" 
                         src="${currentPicture}"
                         alt="Profile Preview"
                         style="width: 180px; height: 180px; border-radius: 50%; border: 4px solid #ddd; object-fit: cover; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                </div>
                <div>
                    <label class="btn btn-primary btn-lg" style="margin: 0; padding: 10px 30px; cursor: pointer;">
                        <i class="ace-icon fa fa-upload"></i> Choose Photo
                        <input type="file" id="swalProfilePictureInput" accept="image/*" 
                               style="display: none;" onchange="previewSwalProfilePicture(this)">
                    </label>
                </div>
                <div style="margin-top: 15px; font-size: 12px; color: #666;">
                    <i class="ace-icon fa fa-info-circle"></i> Supported: JPG, PNG, GIF, WEBP (Max 5MB)
                </div>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: '<i class="ace-icon fa fa-save"></i> Update Picture',
                cancelButtonText: '<i class="ace-icon fa fa-times"></i> Cancel',
                confirmButtonColor: '#4dabf7',
                cancelButtonColor: '#6c757d',
                width: '500px',
                didOpen: () => {
                    // Preview function for SweetAlert
                    window.previewSwalProfilePicture = function(input) {
                        if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                document.getElementById('swalProfilePreview').src = e.target.result;
                            };
                            reader.readAsDataURL(input.files[0]);
                        }
                    };
                },
                preConfirm: () => {
                    const fileInput = document.getElementById('swalProfilePictureInput');
                    if (!fileInput || !fileInput.files || !fileInput.files[0]) {
                        Swal.showValidationMessage('Please select a profile picture');
                        return false;
                    }
                    return fileInput.files[0];
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    uploadProfilePicture(result.value);
                }
            });
        }

        // Upload profile picture
        function uploadProfilePicture(file) {
            const formData = new FormData();
            formData.append('profile_picture', file);
            formData.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->csrfToken ?>');

            Swal.fire({
                title: 'Updating...',
                text: 'Please wait while we update your profile picture',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= \yii\helpers\Url::to(['inventory/update-profile']) ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Profile picture updated successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Update the profile picture on the page
                            if (response.profile_picture) {
                                document.getElementById('profilePicture').src = response
                                    .profile_picture;
                            }
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update profile picture'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let errorMsg = 'An error occurred while updating profile picture';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                }
            });
        }
    </script>
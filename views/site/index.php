<?php
// Get current school information
$school_id = Yii::$app->session->get('user_array')['school_id'] ?? null;
$current_school = null;
if ($school_id) {
    try {
        $current_school = Yii::$app->db->createCommand('SELECT * FROM school WHERE school_id = :school_id')
            ->bindValue(':school_id', $school_id)
            ->queryOne();
    } catch (\Exception $e) {
        $current_school = null;
    }
}

$navbar_color = $current_school['navbar_color'] ?? '#0f4c29';
$school_name = $current_school['school_name'] ?? 'School Management System';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
    }

    .breadcrumbs {
        padding: 8px 0 !important;
        margin-bottom: 10px !important;
    }

    .page-content {
        padding: 10px 15px !important;
        background: transparent;
    }

    .modern-dashboard {
        background: transparent;
    }

    .main-content-inner {
        background: transparent !important;
    }

    /* Compact Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, <?= $navbar_color ?>f5 0%, <?= $navbar_color ?>dd 100%);
        backdrop-filter: blur(10px);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .welcome-banner h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .welcome-banner p {
        margin: 3px 0 0 0;
        font-size: 11px;
        opacity: 0.9;
    }

    .welcome-stats {
        display: flex;
        gap: 25px;
    }

    .welcome-stat {
        text-align: center;
    }

    .welcome-stat-num {
        font-size: 22px;
        font-weight: 700;
        display: block;
    }

    .welcome-stat-label {
        font-size: 9px;
        opacity: 0.9;
        text-transform: uppercase;
    }

    /* Compact Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 8px;
        padding: 12px 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.2s;
        border-left: 3px solid;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-card.s-blue {
        border-left-color: #4dabf7;
    }

    .stat-card.s-green {
        border-left-color: #51cf66;
    }

    .stat-card.s-orange {
        border-left-color: #ffa726;
    }

    .stat-card.s-red {
        border-left-color: #ff6b6b;
    }

    .stat-card.s-purple {
        border-left-color: #b197fc;
    }

    .stat-card.s-teal {
        border-left-color: #20c997;
    }

    .stat-info {
        flex: 1;
    }

    .stat-title {
        font-size: 10px;
        color: #6c757d;
        text-transform: uppercase;
        margin-bottom: 3px;
        font-weight: 500;
    }

    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 2px;
    }

    .stat-subtitle {
        font-size: 8px;
        /* color: #6c757d; */
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .s-blue .stat-icon {
        background: rgba(77, 171, 247, 0.1);
        color: #4dabf7;
    }

    .s-green .stat-icon {
        background: rgba(81, 207, 102, 0.1);
        color: #51cf66;
    }

    .s-orange .stat-icon {
        background: rgba(255, 167, 38, 0.1);
        color: #ffa726;
    }

    .s-red .stat-icon {
        background: rgba(255, 107, 107, 0.1);
        color: #ff6b6b;
    }

    .s-purple .stat-icon {
        background: rgba(177, 151, 252, 0.1);
        color: #b197fc;
    }

    .s-teal .stat-icon {
        background: rgba(32, 201, 151, 0.1);
        color: #20c997;
    }

    /* Compact Widgets */
    .widget-compact {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 12px;
    }

    .widget-header-compact {
        padding: 10px 15px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .widget-title-compact {
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    .widget-body-compact {
        padding: 12px 15px;
    }

    /* Compact Table */
    .compact-table {
        width: 100%;
        font-size: 11px;
    }

    .compact-table thead th {
        background: #f8f9fa;
        padding: 8px 10px;
        font-weight: 600;
        font-size: 10px;
        text-transform: uppercase;
        color: #6c757d;
        border-bottom: 1px solid #e9ecef;
    }

    .compact-table tbody td {
        padding: 8px 10px;
        border-bottom: 1px solid #f8f9fa;
    }

    .compact-table tbody tr:hover {
        background: #f8f9fa;
    }

    /* Badges */
    .badge-sm {
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 9px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-success {
        background: #d3f9d8;
        color: #2b8a3e;
    }

    .badge-warning {
        background: #fff3bf;
        color: #f08c00;
    }

    .badge-danger {
        background: #ffe3e3;
        color: #c92a2a;
    }

    .badge-info {
        background: #d0ebff;
        color: #1864ab;
    }

    .badge-primary {
        background: #e7f5ff;
        color: #1971c2;
    }

    /* Quick Actions - Compact */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 8px;
    }

    .quick-btn {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 12px 8px;
        border-radius: 6px;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 11px;
    }

    .quick-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        color: white;
        text-decoration: none;
    }

    .quick-btn i {
        font-size: 18px;
        display: block;
        margin-bottom: 5px;
    }

    .qb-blue {
        background: linear-gradient(135deg, #4dabf7, #339af0);
    }

    .qb-green {
        background: linear-gradient(135deg, #51cf66, #37b24d);
    }

    .qb-orange {
        background: linear-gradient(135deg, #ffa726, #fb8c00);
    }

    .qb-red {
        background: linear-gradient(135deg, #ff6b6b, #c92a2a);
    }

    .qb-purple {
        background: linear-gradient(135deg, #b197fc, #9775fa);
    }

    .qb-teal {
        background: linear-gradient(135deg, #20c997, #12b886);
    }

    /* Grid Layout */
    .dash-row {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
    }

    .dash-col-8 {
        flex: 0 0 66%;
    }

    .dash-col-4 {
        flex: 0 0 34%;
    }

    @media (max-width: 1200px) {
        .dash-row {
            flex-direction: column;
        }

        .dash-col-8,
        .dash-col-4 {
            flex: 1;
        }
    }

    /* Scrollable Lists */
    .scroll-list {
        max-height: 250px;
        overflow-y: auto;
    }

    .scroll-list::-webkit-scrollbar {
        width: 4px;
    }

    .scroll-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .scroll-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .two-pane {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        align-items: start;
    }

    .chart-container {
        position: relative;
        width: 100%;
        min-height: 220px;
    }

    .chart-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }

    .chart-card {
        background: #f8fafc;
        border-radius: 8px;
        padding: 12px;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.05);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .chart-card h5 {
        margin: 0;
        font-size: 12px;
        font-weight: 600;
        color: #2d3748;
    }

    .permissions-table-wrapper {
        max-height: 60vh;
        overflow: auto;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #ffffff;
        padding: 4px;
    }

    .permissions-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .permissions-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8fafc;
        padding: 8px 10px;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: #1f2937;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }

    .permissions-table tbody td {
        padding: 6px 8px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        text-align: center;
    }

    .permissions-table tbody tr:hover {
        background: #f8fafc;
    }

    .permissions-stats-cell {
        text-align: left;
        font-weight: 600;
        color: #1f2937;
        white-space: nowrap;
        position: sticky;
        left: 0;
        background: #ffffff;
        z-index: 1;
        font-size: 12px;
    }

    .permissions-table .switch-label {
        display: inline-block;
        font-size: 9px;
        margin-top: 3px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .permissions-table .lbl::before {
        line-height: 18px !important;
    }

    .permissions-table .ace-switch-4+.lbl {
        min-width: 58px;
    }


    /* List Items */
    .list-item {
        padding: 8px 10px;
        border-bottom: 1px solid #f1f3f5;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
    }

    .list-item:hover {
        background: #f8f9fa;
        cursor: pointer;
    }

    .list-item:last-child {
        border-bottom: none;
    }

    /* Widget Box Styles for Charts */
    .widget-box {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 15px;
    }

    .widget-header {
        padding: 12px 15px;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
        border-radius: 8px 8px 0 0;
    }

    .widget-header-small .widget-title {
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    .widget-body {
        padding: 0;
    }

    .widget-main {
        padding: 15px;
    }

    .padding-8 {
        padding: 15px;
    }
</style>

<?php
// Initialize stats array if not set
if (!isset($stats)) {
    $stats = [];
}

$currentMonthLabel = date('F');


?>

<div class="modern-dashboard">
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="welcome-banner">
                    <div>
                        <h3>Welcome,
                            <?= htmlspecialchars(Yii::$app->session->get('user_array')['first_name'] ?? 'User') ?>! 👋
                        </h3>
                        <p><?= htmlspecialchars($school_name) ?> • <?= date('l, F j, Y') ?></p>
                    </div>
                    <div class="welcome-stats">
                        <?php if (call_user_func($checkPermission, 'students')): ?>
                            <div class="welcome-stat" data-stat="students">
                                <span class="welcome-stat-num">0</span>
                                <span class="welcome-stat-label">Students</span>
                            </div>
                        <?php endif; ?>
                        <?php if (call_user_func($checkPermission, 'teachers')): ?>
                            <div class="welcome-stat" data-stat="teachers">
                                <span class="welcome-stat-num">0</span>
                                <span class="welcome-stat-label">Teachers</span>
                            </div>
                        <?php endif; ?>
                        <?php if (call_user_func($checkPermission, 'classes')): ?>
                            <div class="welcome-stat" data-stat="classes">
                                <span class="welcome-stat-num">0</span>
                                <span class="welcome-stat-label">Classes</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div class="stats-grid">
                    <?php if (call_user_func($checkPermission, 'students')): ?>
                        <div class="stat-card s-blue" data-stat="students">
                            <div class="stat-info">
                                <div class="stat-title">Students</div>
                                <div class="stat-value">0</div>
                                <div class="stat-subtitle loading-text">Loading...</div>
                            </div>
                            <div class="stat-icon"><i class="fa fa-users"></i></div>
                        </div>
                    <?php endif; ?>

                    <?php if (call_user_func($checkPermission, 'teachers')): ?>
                        <div class="stat-card s-green" data-stat="teachers">
                            <div class="stat-info">
                                <div class="stat-title">Teachers</div>
                                <div class="stat-value">0</div>
                                <div class="stat-subtitle loading-text">Loading...</div>
                            </div>
                            <div class="stat-icon"><i class="fa fa-chalkboard-teacher"></i></div>
                        </div>
                    <?php endif; ?>

                    <?php if (call_user_func($checkPermission, 'students')): ?>
                        <div class="stat-card s-teal" data-stat="new_admissions">
                            <div class="stat-info">
                                <div class="stat-title">New Admissions</div>
                                <div class="stat-value">0</div>
                                <div class="stat-subtitle loading-text">Loading...</div>
                            </div>
                            <div class="stat-icon"><i class="fa fa-user-plus"></i></div>
                        </div>
                    <?php endif; ?>


                    <?php if (call_user_func($checkPermission, 'subjects')): ?>
                        <div class="stat-card s-blue" data-stat="subjects">
                            <div class="stat-info">
                                <div class="stat-title">Subjects</div>
                                <div class="stat-value">0</div>
                                <div class="stat-subtitle">Available</div>
                            </div>
                            <div class="stat-icon"><i class="fa fa-book"></i></div>
                        </div>
                    <?php endif; ?>

                    <?php if (call_user_func($checkPermission, 'classes')): ?>
                        <div class="stat-card s-green" data-stat="classes">
                            <div class="stat-info">
                                <div class="stat-title">Classes</div>
                                <div class="stat-value">0</div>
                                <div class="stat-subtitle loading-text">Loading...</div>
                            </div>
                            <div class="stat-icon"><i class="fa fa-building"></i></div>
                        </div>
                    <?php endif; ?>

                    <?php if (call_user_func($checkPermission, 'fees_today')): ?>
                        <div class="stat-card s-orange" data-stat="fees_today">
                            <div class="stat-info">
                                <div class="stat-title">Today's Collection</div>
                                <div class="stat-value">0</div>
                                <div class="stat-subtitle">Fee Collected</div>
                            </div>
                            <div class="stat-icon"><i class="fa fa-money"></i></div>
                        </div>
                    <?php endif; ?>

                    <?php if (call_user_func($checkPermission, 'fees_month')): ?>
                        <div class="stat-card s-blue" data-stat="fees_month">
                            <div class="stat-info">
                                <div class="stat-title">Monthly Total</div>
                                <div class="stat-value">0</div>
                                <div class="stat-subtitle">This Month</div>
                            </div>
                            <div class="stat-icon"><i class="fa fa-bar-chart"></i></div>
                        </div>
                    <?php endif; ?>

                    <?php if (call_user_func($checkPermission, 'outstanding')): ?>
                        <div class="stat-card s-red" data-stat="outstanding">
                            <div class="stat-info">
                                <div class="stat-title">Outstanding</div>
                                <div class="stat-value">0</div>
                                <div class="stat-subtitle loading-text">Loading...</div>
                            </div>
                            <div class="stat-icon"><i class="fa fa-warning"></i></div>
                        </div>
                    <?php endif; ?>

                    <?php if (call_user_func($checkPermission, 'exams')): ?>
                        <div class="stat-card s-purple" data-stat="exams">
                            <div class="stat-info">
                                <div class="stat-title">Exams</div>
                                <div class="stat-value">0</div>
                                <div class="stat-subtitle loading-text">Loading...</div>
                            </div>
                            <div class="stat-icon"><i class="fa fa-pencil-square-o"></i></div>
                        </div>
                    <?php endif; ?>

                    <?php if (call_user_func($checkPermission, 'meetings')): ?>
                        <div class="stat-card s-teal" data-stat="meetings">
                            <div class="stat-info">
                                <div class="stat-title">Meetings</div>
                                <div class="stat-value">0</div>
                                <div class="stat-subtitle loading-text">Loading...</div>
                            </div>
                            <div class="stat-icon"><i class="fa fa-video-camera"></i></div>
                        </div>
                    <?php endif; ?>

                    <?php if (call_user_func($checkPermission, 'documents')): ?>
                        <div class="stat-card s-purple" data-stat="documents">
                            <div class="stat-info">
                                <div class="stat-title">Documents</div>
                                <div class="stat-value">0</div>
                                <div class="stat-subtitle">Uploaded</div>
                            </div>
                            <div class="stat-icon"><i class="fa fa-folder"></i></div>
                        </div>
                    <?php endif; ?>

                    <?php if (call_user_func($checkPermission, 'attendance')): ?>
                        <div class="stat-card s-green" data-stat="attendance">
                            <div class="stat-info">
                                <div class="stat-title">Attendance</div>
                                <div class="stat-value">0</div>
                                <div class="stat-subtitle">Present Today</div>
                            </div>
                            <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
                        </div>
                    <?php endif; ?>

                    <div class="stat-card s-blue" data-stat="timetables">
                        <div class="stat-info">
                            <div class="stat-title">Timetables</div>
                            <div class="stat-value">0</div>
                            <div class="stat-subtitle loading-text">Loading...</div>
                        </div>
                        <div class="stat-icon"><i class="fa fa-calendar"></i></div>
                    </div>

                    <div class="stat-card s-orange" data-stat="leaves">
                        <div class="stat-info">
                            <div class="stat-title">Leaves</div>
                            <div class="stat-value">0</div>
                            <div class="stat-subtitle loading-text">Loading...</div>
                        </div>
                        <div class="stat-icon"><i class="fa fa-calendar-times-o"></i></div>
                    </div>

                    <div class="stat-card s-purple" data-stat="lesson_plans">
                        <div class="stat-info">
                            <div class="stat-title">Lesson Plans</div>
                            <div class="stat-value">0</div>
                            <div class="stat-subtitle loading-text">Loading...</div>
                        </div>
                        <div class="stat-icon"><i class="fa fa-book"></i></div>
                    </div>

                    <div class="stat-card s-teal" data-stat="attendance_percentage">
                        <div class="stat-info">
                            <div class="stat-title">Daily Attendance</div>
                            <div class="stat-value">0%</div>
                            <div class="stat-subtitle loading-text">Loading...</div>
                        </div>
                        <div class="stat-icon"><i class="fa fa-percent"></i></div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col-sm-4">
                        <div class="widget-box">
                            <div class="widget-header widget-header-small">
                                <h4 class="widget-title">
                                    <i class="ace-icon fa fa-pie-chart"></i>
                                    Class-wise Student Distribution
                                </h4>
                            </div>
                            <div class="widget-body">
                                <div class="widget-main padding-8">
                                    <canvas id="classDistChart" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="widget-box">
                            <div class="widget-header widget-header-small">
                                <h4 class="widget-title">
                                    <i class="ace-icon fa fa-bar-chart"></i>
                                    Teacher Workload
                                </h4>
                            </div>
                            <div class="widget-body">
                                <div class="widget-main padding-8">
                                    <canvas id="teacherWorkloadChart" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="widget-box">
                            <div class="widget-header widget-header-small">
                                <h4 class="widget-title">
                                    <i class="ace-icon fa fa-bar-chart"></i> Lesson Plans Progress by Class
                                </h4>
                            </div>
                            <div class="widget-body">
                                <div class="widget-main padding-8">
                                    <div class="widget-main">
                                        <!-- Classes with Plans -->
                                        <div style="margin-bottom: 15px;">
                                            <div
                                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 12px; border-radius: 5px; color: white;">
                                                <div
                                                    style="display: flex; align-items: center; justify-content: space-between;">
                                                    <div>
                                                        <div style="font-size: 11px; opacity: 0.9;">Classes with Plans
                                                        </div>
                                                        <div style="font-size: 20px; font-weight: bold;"
                                                            id="classes-with-plans">0</div>
                                                    </div>
                                                    <i class="ace-icon fa fa-building"
                                                        style="font-size: 24px; opacity: 0.7;"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pending Approvals -->
                                        <div style="margin-bottom: 15px;">
                                            <div
                                                style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); padding: 12px; border-radius: 5px; color: white;">
                                                <div
                                                    style="display: flex; align-items: center; justify-content: space-between;">
                                                    <div>
                                                        <div style="font-size: 11px; opacity: 0.9;">Pending Approvals
                                                        </div>
                                                        <div style="font-size: 20px; font-weight: bold;"
                                                            id="pending-approvals">0</div>
                                                    </div>
                                                    <i class="ace-icon fa fa-hourglass-half"
                                                        style="font-size: 24px; opacity: 0.7;"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Overall Progress -->
                                        <div style="margin-bottom: 15px;">
                                            <div
                                                style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); padding: 12px; border-radius: 5px; color: white;">
                                                <div
                                                    style="display: flex; align-items: center; justify-content: space-between;">
                                                    <div>
                                                        <div style="font-size: 11px; opacity: 0.9;">Overall Progress
                                                        </div>
                                                        <div style="font-size: 20px; font-weight: bold;"
                                                            id="overall-progress">0%</div>
                                                    </div>
                                                    <i class="ace-icon fa fa-line-chart"
                                                        style="font-size: 24px; opacity: 0.7;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <canvas id="lessonProgressByClassChart" style="max-height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Helper function for number formatting
    function number_format(number, decimals) {
        decimals = decimals || 0;
        number = parseFloat(number);
        if (isNaN(number)) return '0';
        return number.toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }

    // Load dashboard stats via AJAX
    $(document).ready(function() {
        // Fetch dashboard stats
        $.ajax({
            url: 'index.php?r=site/get-dashboard-stats',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.stats) {
                    var stats = response.stats;

                    // Update welcome banner stats
                    $('.welcome-stat[data-stat="students"] .welcome-stat-num').text(stats
                        .total_students || 0);
                    $('.welcome-stat[data-stat="teachers"] .welcome-stat-num').text(stats.teachers ||
                        0);
                    $('.welcome-stat[data-stat="classes"] .welcome-stat-num').text(stats
                        .total_classes || 0);

                    // Update stats grid
                    $('.stat-card[data-stat="students"] .stat-value').html((stats.total_students || 0) +
                        '<div class="stat-subtitle">Active: ' + number_format(stats
                            .active_students || 0) + ' • Male: ' + number_format(stats
                            .male_students || 0) + ' • Female: ' + number_format(stats
                            .female_students || 0) + '</div>');
                    $('.stat-card[data-stat="students"] .loading-text').hide();

                    $('.stat-card[data-stat="teachers"] .stat-value').html((stats.teachers || 0) +
                        '<div class="stat-subtitle">Faculty Members</div>');
                    $('.stat-card[data-stat="teachers"] .loading-text').hide();

                    $('.stat-card[data-stat="new_admissions"] .stat-value').html(number_format(stats
                            .new_admissions_month || 0) +
                        '<div class="stat-subtitle">' + new Date().toLocaleString('en-US', {
                            month: 'long'
                        }) + ' Enrollments</div>');
                    $('.stat-card[data-stat="new_admissions"] .loading-text').hide();

                    $('.stat-card[data-stat="subjects"] .stat-value').text(stats.total_subjects || 0);

                    $('.stat-card[data-stat="classes"] .stat-value').html((stats.total_classes || 0) +
                        '<div class="stat-subtitle">' + (stats.total_sections || 0) +
                        ' Sections</div>');
                    $('.stat-card[data-stat="classes"] .loading-text').hide();

                    $('.stat-card[data-stat="fees_today"] .stat-value').html(number_format(stats
                            .total_fee_collected_today || 0) +
                        '<div class="stat-subtitle">Collected Today</div>');

                    $('.stat-card[data-stat="fees_month"] .stat-value').html(number_format(stats
                            .total_fee_collected_month || 0) +
                        '<div class="stat-subtitle">This Month</div>');

                    $('.stat-card[data-stat="outstanding"] .stat-value').html(number_format(stats
                            .total_outstanding || 0) + '<div class="stat-subtitle">Pending: ' +
                        number_format((stats.pending_fee_amount || 0), 2) + ' • Overdue: ' +
                        number_format((stats.overdue_fee_amount || 0), 2) + ' • Defaulters: ' +
                        number_format((stats.defaulters_count || 0)) + '</div>');
                    $('.stat-card[data-stat="outstanding"] .loading-text').hide();

                    $('.stat-card[data-stat="exams"] .stat-value').html((stats.total_exams || 0) +
                        '<div class="stat-subtitle">' + (stats.upcoming_exams || 0) +
                        ' Upcoming</div>');
                    $('.stat-card[data-stat="exams"] .loading-text').hide();

                    $('.stat-card[data-stat="meetings"] .stat-value').html((stats.total_meetings || 0) +
                        '<div class="stat-subtitle">' + (stats.upcoming_meetings || 0) +
                        ' Upcoming</div>');
                    $('.stat-card[data-stat="meetings"] .loading-text').hide();

                    $('.stat-card[data-stat="documents"] .stat-value').text(stats.total_documents || 0);
                    $('.stat-card[data-stat="attendance"] .stat-value').text(stats.attendance_today ||
                        0);

                    // Update new stats
                    $('.stat-card[data-stat="timetables"] .stat-value').text(stats.total_timetables ||
                        0);
                    $('.stat-card[data-stat="timetables"] .stat-subtitle').text('Total Schedules');
                    $('.stat-card[data-stat="timetables"] .loading-text').hide();

                    $('.stat-card[data-stat="leaves"] .stat-value').text(stats.total_leaves || 0);
                    $('.stat-card[data-stat="leaves"] .stat-subtitle').text('Total Requests');
                    $('.stat-card[data-stat="leaves"] .loading-text').hide();

                    $('.stat-card[data-stat="lesson_plans"] .stat-value').text(stats
                        .total_lesson_plans || 0);
                    $('.stat-card[data-stat="lesson_plans"] .stat-subtitle').text('Total Plans');
                    $('.stat-card[data-stat="lesson_plans"] .loading-text').hide();

                    $('.stat-card[data-stat="attendance_percentage"] .stat-value').text((stats
                        .daily_attendance_percentage || 0) + '%');
                    $('.stat-card[data-stat="attendance_percentage"] .stat-subtitle').text(
                        'Today\'s Rate');
                    $('.stat-card[data-stat="attendance_percentage"] .loading-text').hide();

                    // Show stat cards with animation
                    $('.stat-card').each(function(i) {
                        $(this).css({
                            'opacity': '0',
                            'transform': 'scale(0.9)'
                        });
                        setTimeout(() => {
                            $(this).css({
                                'opacity': '1',
                                'transform': 'scale(1)',
                                'transition': 'all 0.3s ease'
                            });
                        }, i * 30);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load dashboard stats:', error);
            }
        });

        // Load dashboard charts via AJAX
        $.ajax({
            url: 'index.php?r=site/get-dashboard-charts',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update Lesson Plans Stats
                    if (response.lessonPlansStats) {
                        $('#classes-with-plans').text(response.lessonPlansStats
                            .classes_with_lesson_plans || 0);
                        $('#pending-approvals').text(response.lessonPlansStats.pending_approvals || 0);
                        $('#overall-progress').text((response.lessonPlansStats.overall_progress || 0) +
                            '%');
                    }

                    // Class Distribution Chart
                    if (response.classDistribution && response.classDistribution.length > 0) {
                        const classDistCtx = document.getElementById('classDistChart');
                        if (classDistCtx) {
                            const classChart = new Chart(classDistCtx.getContext('2d'), {
                                type: 'doughnut',
                                data: {
                                    labels: response.classDistribution.map(item => item
                                        .class_name),
                                    datasets: [{
                                        data: response.classDistribution.map(item =>
                                            parseInt(item.student_count) || 0),
                                        backgroundColor: ['#4dabf7', '#51cf66',
                                            '#ffa726', '#e74c3c', '#9b59b6',
                                            '#3498db', '#1abc9c', '#f39c12'
                                        ]
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        }
                                    }
                                }
                            });
                        }
                    }

                    // Teacher Workload Chart
                    if (response.teacherWorkload && response.teacherWorkload.length > 0) {
                        const teacherWorkloadCtx = document.getElementById('teacherWorkloadChart');
                        if (teacherWorkloadCtx) {
                            const teacherChart = new Chart(teacherWorkloadCtx.getContext('2d'), {
                                type: 'bar',
                                data: {
                                    labels: response.teacherWorkload.map(item => item
                                        .teacher_name),
                                    datasets: [{
                                        label: 'Periods/Week',
                                        data: response.teacherWorkload.map(item =>
                                            parseInt(item.timetable_periods) || 0),
                                        backgroundColor: '#4dabf7'
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                stepSize: 1
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    }
                                }
                            });
                        }
                    }

                    // Lesson Progress by Class Chart
                    if (response.progressByClass && response.progressByClass.length > 0) {
                        const progressCtx = document.getElementById('lessonProgressByClassChart');
                        if (progressCtx) {
                            const progressChart = new Chart(progressCtx.getContext('2d'), {
                                type: 'bar',
                                data: {
                                    labels: response.progressByClass.map(item => item
                                        .class_name || 'Class ' + item.class_id),
                                    datasets: [{
                                        label: 'Progress %',
                                        data: response.progressByClass.map(item => {
                                            const total = parseInt(item
                                                .total_lessons) || 0;
                                            const completed = parseInt(item
                                                .completed_lessons) || 0;
                                            return total > 0 ? Math.round((
                                                    completed / total) *
                                                100) : 0;
                                        }),
                                        backgroundColor: response.progressByClass.map((
                                            item) => {
                                            const total = parseInt(item
                                                .total_lessons) || 0;
                                            const completed = parseInt(item
                                                .completed_lessons) || 0;
                                            const progress = total > 0 ? (
                                                completed / total) * 100 : 0;
                                            if (progress >= 80)
                                                return '#2ecc71';
                                            if (progress >= 50)
                                                return '#f39c12';
                                            if (progress >= 25)
                                                return '#e67e22';
                                            return '#e74c3c';
                                        }),
                                        borderColor: response.progressByClass.map((
                                            item) => {
                                            const total = parseInt(item
                                                .total_lessons) || 0;
                                            const completed = parseInt(item
                                                .completed_lessons) || 0;
                                            const progress = total > 0 ? (
                                                completed / total) * 100 : 0;
                                            if (progress >= 80)
                                                return '#27ae60';
                                            if (progress >= 50)
                                                return '#d68910';
                                            if (progress >= 25)
                                                return '#d35400';
                                            return '#c0392b';
                                        }),
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            max: 100,
                                            ticks: {
                                                callback: function(value) {
                                                    return value + '%';
                                                }
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    }
                                }
                            });
                        }
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load dashboard charts:', error);
            }
        });
    });
</script>
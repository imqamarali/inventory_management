<?php
$can_students = true;
$can_subjects = true;
$can_classes = true;
$can_sections = true;
$can_subject_groups = true;
$can_schools = true;
$can_exams = true;
$can_exam_results = true;
$can_syllabus = true;
$can_attendance = true;
$can_documentations = true;
$can_reporting = true;
$can_front_office_setup = true;
$can_settings = true;
$display_div = true;
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&display=swap" rel="stylesheet">
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"> </script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="page-content" style="padding: 1px; width: 100%; margin: 7px; overflow: hidden; max-height: 100vh;">
    <div class="row" style="overflow: hidden;">
        <div class="col-xs-12" style="margin-top: 1%; overflow: hidden;">

            <!-- Left Panel -->
            <div class="col-xs-5 dashicons" style="overflow-y: auto; max-height: calc(100vh - 100px);">
                <div class="dashboard col-xs-12">
                    <!-- Dashboard Header -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="dashboard-header">
                                <div class="dashboard-title-wrapper">
                                    <img class="dashboard-logo" src="images/logos/home.png" alt="Logo">
                                    <div>
                                        <h1 class="dashboard-title">Online Quran Academy</h1>
                                        <span class="dashboard-subtitle">Learning Management System</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Boxes -->
                        <div class="col-xs-10 dashicons">
                            <!-- System Users -->
                            <div class="infobox infobox-green">
                                <div class="infobox-icon">
                                    <img src="system_images/settings.png" style="height: 47px;" draggable="false"
                                        alt="System Users">
                                </div>
                                <div class="infobox-data" style="text-align: end;">
                                    <span class="infobox-data-number">
                                        <?= isset($stats['total_system_users']) ? $stats['total_system_users'] : 0 ?>
                                    </span>
                                    <div class="infobox-content">System Users</div>
                                </div>
                            </div>

                            <!-- Admins -->
                            <div class="infobox infobox-red">
                                <div class="infobox-icon">
                                    <img src="system_images/schools.png" style="height: 47px;" draggable="false"
                                        alt="Admins">
                                </div>
                                <div class="infobox-data" style="text-align: end;">
                                    <span class="infobox-data-number">
                                        <?= isset($stats['admins']) ? $stats['admins'] : 0 ?>
                                    </span>
                                    <div class="infobox-content">Admins</div>
                                </div>
                            </div>

                            <!-- Teachers -->
                            <div class="infobox infobox-orange">
                                <div class="infobox-icon">
                                    <img src="system_images/attendance.png" style="height: 47px;" draggable="false"
                                        alt="Teachers">
                                </div>
                                <div class="infobox-data" style="text-align: end;">
                                    <span class="infobox-data-number">
                                        <?= isset($stats['teachers']) ? $stats['teachers'] : 0 ?>
                                    </span>
                                    <div class="infobox-content">Teachers</div>
                                </div>
                            </div>

                            <!-- Students -->
                            <div class="infobox infobox-blue">
                                <div class="infobox-icon">
                                    <img src="system_images/student.png" style="height: 47px;" draggable="false"
                                        alt="Students">
                                </div>
                                <div class="infobox-data" style="text-align: end;">
                                    <span class="infobox-data-number">
                                        <?= isset($stats['total_students']) ? $stats['total_students'] : 0 ?>
                                    </span>
                                    <div class="infobox-content">Students</div>
                                </div>
                            </div>

                            <!-- Classes -->
                            <div class="infobox infobox-pink">
                                <div class="infobox-icon">
                                    <img src="system_images/class.png" style="height: 47px;" draggable="false"
                                        alt="Classes">
                                </div>
                                <div class="infobox-data" style="text-align: end;">
                                    <span class="infobox-data-number">
                                        <?= isset($stats['total_classes']) ? $stats['total_classes'] : 0 ?>
                                    </span>
                                    <div class="infobox-content">Classes</div>
                                </div>
                            </div>

                            <!-- Meetings -->
                            <div class="infobox infobox-grey">
                                <div class="infobox-icon">
                                    <img src="system_images/exams.png" style="height: 47px;" draggable="false"
                                        alt="Meetings">
                                </div>
                                <div class="infobox-data" style="text-align: end;">
                                    <span class="infobox-data-number">
                                        <?= isset($stats['total_meetings']) ? $stats['total_meetings'] : 0 ?>
                                    </span>
                                    <div class="infobox-content">Meetings</div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Tickets by Priority -->
                    <?php if (isset($tickets) && !empty($tickets)): ?>
                        <div class="row no-padding" style="margin-top : 12px; width: 106%;">
                            <div class="col-xs-12">
                                <table class="table table-striped table-bordered table-hover no_items">
                                    <thead>
                                        <tr>
                                            <th colspan="4">Support Tickets <i class="fa fa-ticket" style="color:grey"></i>
                                            </th>
                                        </tr>
                                        <tr style="background: #f8f9fa;">
                                            <th style="width: 45%; font-size: 11px;">Title</th>
                                            <th style="width: 20%; font-size: 11px; text-align: center;">Priority</th>
                                            <th style="width: 20%; font-size: 11px; text-align: center;">Status</th>
                                            <th style="width: 15%; font-size: 11px; text-align: center;">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets as $ticket):
                                            // Priority colors and icons
                                            $priorityColors = [
                                                'High' => ['bg' => '#ffebee', 'color' => '#c62828', 'icon' => 'fa-exclamation-circle'],
                                                'Medium' => ['bg' => '#fff3e0', 'color' => '#ef6c00', 'icon' => 'fa-exclamation-triangle'],
                                                'Low' => ['bg' => '#e8f5e9', 'color' => '#2e7d32', 'icon' => 'fa-info-circle']
                                            ];
                                            $priority = $ticket['priority'] ?? 'Medium';
                                            $pStyle = $priorityColors[$priority];

                                            // Status colors
                                            $statusColors = [
                                                'Open' => ['bg' => '#e3f2fd', 'color' => '#1565c0'],
                                                'Pending' => ['bg' => '#fff3e0', 'color' => '#ef6c00'],
                                                'On hold' => ['bg' => '#fce4ec', 'color' => '#c2185b'],
                                                'Solved' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                                                'Closed' => ['bg' => '#eceff1', 'color' => '#546e7a']
                                            ];
                                            $status = $ticket['status'] ?? 'Open';
                                            $sStyle = $statusColors[$status];
                                        ?>
                                            <tr style="cursor: pointer;"
                                                onclick="window.open('index.php?r=support/index', '_blank')">
                                                <td style="font-size: 11px;">
                                                    <strong><?= htmlspecialchars($ticket['title']) ?></strong><br>
                                                    <small
                                                        style="color: #777;"><?= htmlspecialchars($ticket['category']) ?></small>
                                                </td>
                                                <td style="text-align: center;">
                                                    <span
                                                        style="display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; background: <?= $pStyle['bg'] ?>; color: <?= $pStyle['color'] ?>;">
                                                        <i class="fa <?= $pStyle['icon'] ?>"></i> <?= $priority ?>
                                                    </span>
                                                </td>
                                                <td style="text-align: center;">
                                                    <span
                                                        style="display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; background: <?= $sStyle['bg'] ?>; color: <?= $sStyle['color'] ?>;">
                                                        <?= $status ?>
                                                    </span>
                                                </td>
                                                <td style="text-align: center; font-size: 10px; color: #999;">
                                                    <?= date('M d, Y', strtotime($ticket['created_at'])) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Recent Students -->
                    <?php if (isset($recent_students) && !empty($recent_students)): ?>
                        <div class="row no-padding" style="margin-top : 12px; width: 106%;">
                            <div class="col-xs-12">
                                <table class="table table-striped table-bordered table-hover no_items">
                                    <thead>
                                        <tr>
                                            <th colspan="3">Recent Students <i class="fa fa-user-graduate"
                                                    style="color:grey"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_students as $student): ?>
                                            <tr>
                                                <td style="width: 50%;"><?= htmlspecialchars($student['name']) ?></td>
                                                <td style="width: 30%;"><?= htmlspecialchars($student['admission_no']) ?></td>
                                                <td style="width: 20%; font-size: 11px; color: #999;">
                                                    <?= date('M d, Y', strtotime($student['created_at'])) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Upcoming Exams -->
                    <?php if (isset($upcoming_exams) && !empty($upcoming_exams)): ?>
                        <div class="row no-padding" style="margin-top : 12px; width: 106%;">
                            <div class="col-xs-12">
                                <table class="table table-striped table-bordered table-hover no_items">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Upcoming Exams <i class="fa fa-calendar-alt"
                                                    style="color:grey"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($upcoming_exams as $exam): ?>
                                            <tr>
                                                <td style="width: 70%;"><?= htmlspecialchars($exam['name']) ?></td>
                                                <td style="width: 30%; font-size: 11px; color: #999;">
                                                    <?= date('M d', strtotime($exam['start_date'])) ?> -
                                                    <?= date('M d', strtotime($exam['end_date'])) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Panel -->
            <div class="col-xs-7" style="overflow-y: auto; max-height: calc(100vh - 100px);">
                <div class="col-xs-12">
                    <div>
                        <div class="widget-toolbar" style="width: 25%; padding: 8px 10px;" id="studentWrapper">
                            <select class="form-control" id="studentDropdown" data-placeholder="Select Student"
                                onchange="updateNext(3)">
                                <option value="">Select Student</option>
                            </select>
                        </div>

                        <div class="widget-toolbar" style="width: 25%; padding: 8px 10px;" id="sectionWrapper">
                            <select class="form-control" id="sectionDropdown" onchange="updateNext(2)">
                                <option value="">Select Section</option>
                            </select>
                        </div>

                        <div class="widget-toolbar" style="width: 25%; padding: 8px 10px;">
                            <select class="form-control" id="classDropdown" onchange="updateNext(1)">
                                <option value="">Select Class</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= htmlspecialchars($class['id']) ?>">
                                        <?= htmlspecialchars($class['class_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="widget-toolbar"
                            style="width: 25%; padding: 8px 10px; border: none; font-size: medium; color: #285785;">
                            <i class="ace-icon fa fa-users"></i> Search Student
                        </div>
                    </div>
                </div>
                <?php if ($display_div): ?>
                    <div class="col-xs-12" style="margin-top: 2%;">
                        <?php if ($can_students): ?>
                            <div class="col-xs-2 menu-icon_main main-icons">
                                <a href="index.php?r=students/index" target="_blank">
                                    <img src="system_images/student.png" style="height: 47px;" draggable="false" alt="Students">
                                    <h6>Students</h6>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($can_attendance): ?>
                            <div class="col-xs-2 menu-icon_main main-icons">
                                <a href="index.php?r=humanresource/index" target="_blank">
                                    <img src="system_images/attendance.png" style="height: 47px;" draggable="false"
                                        alt="Teachers">
                                    <h6>Teachers</h6>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($can_front_office_setup): ?>
                            <div class="col-xs-2 main-icons">
                                <a href="index.php?r=onlineclass/index" target="_blank">
                                    <img src="system_images/frontoffice.png" style="height: 47px;" draggable="false"
                                        alt="Meetings">
                                    <h6>Meetings</h6>
                                </a>
                            </div>
                        <?php endif; ?>


                        <?php if ($can_documentations): ?>
                            <div class="col-xs-2 main-icons">
                                <a href="index.php?r=documentation/index" target="_blank">
                                    <img src="system_images/documentations.png" style="height: 47px;" draggable="false"
                                        alt="Documentations">
                                    <h6>Documentations</h6>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($can_reporting): ?>
                            <div class="col-xs-2 main-icons">
                                <a href="index.php?r=reporting/insdex" target="_blank">
                                    <img src="system_images/reporting.png" style="height: 47px;" draggable="false"
                                        alt="Reporting">
                                    <h6>Reporting</h6>
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

                <!-- Meetings Section -->
                <?php if (isset($meetings) && !empty($meetings)): ?>
                    <div class="col-xs-12" style="margin-top: 15px;">
                        <div class="meetings-grid">
                            <?php
                            // Limit meetings display to 4 for better layout
                            $displayMeetings = array_slice($meetings, 0, 4);
                            foreach ($displayMeetings as $meeting):
                                $statusClass = '';
                                $statusIcon = '';
                                switch ($meeting['status']) {
                                    case 'scheduled':
                                        $statusClass = 'status-scheduled';
                                        $statusIcon = 'fa-clock';
                                        break;
                                    case 'ongoing':
                                        $statusClass = 'status-ongoing';
                                        $statusIcon = 'fa-video';
                                        break;
                                    case 'completed':
                                        $statusClass = 'status-completed';
                                        $statusIcon = 'fa-check-circle';
                                        break;
                                    case 'cancelled':
                                        $statusClass = 'status-cancelled';
                                        $statusIcon = 'fa-times-circle';
                                        break;
                                    default:
                                        $statusClass = 'status-scheduled';
                                        $statusIcon = 'fa-calendar';
                                }

                                $meetingTypeClass = $meeting['meeting_type'] == 'online_class' ? 'type-online' : 'type-general';
                            ?>
                                <div class="meeting-card <?= $statusClass ?>">
                                    <div class="meeting-card-header">
                                        <div class="meeting-title">
                                            <i class="fa <?= $statusIcon ?>"></i>
                                            <?= htmlspecialchars($meeting['title']) ?>
                                        </div>
                                        <span class="meeting-type-badge <?= $meetingTypeClass ?>">
                                            <?= $meeting['meeting_type'] == 'online_class' ? 'Online Class' : 'Meeting' ?>
                                        </span>
                                    </div>

                                    <div class="meeting-card-body">
                                        <div class="meeting-info-row">
                                            <i class="fa fa-calendar-alt"></i>
                                            <span><?= date('M d, Y', strtotime($meeting['meeting_date'])) ?></span>
                                        </div>

                                        <div class="meeting-info-row">
                                            <i class="fa fa-clock"></i>
                                            <span>
                                                <?= date('g:i A', strtotime($meeting['start_datetime'])) ?> -
                                                <?= date('g:i A', strtotime($meeting['end_datetime'])) ?>
                                            </span>
                                        </div>

                                        <?php if (!empty($meeting['host_name'])): ?>
                                            <div class="meeting-info-row">
                                                <i class="fa fa-user"></i>
                                                <span><?= htmlspecialchars($meeting['host_name']) ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <div class="meeting-info-row">
                                            <i class="fa fa-users"></i>
                                            <span><?= $meeting['participant_count'] ?> Participant(s)</span>
                                        </div>
                                    </div>

                                    <div class="meeting-card-footer">
                                        <span class="meeting-status-badge">
                                            <i class="fa <?= $statusIcon ?>"></i>
                                            <?= ucfirst($meeting['status']) ?>
                                        </span>
                                        <a href="index.php?r=onlineclass/index" class="meeting-view-btn" target="_blank">
                                            View Details <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="lmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    data-backdrop="static" data-keyboard="false" style="display: none;">
    <div class="modal-dialog" style="width:45% !important;" role="document">
        <div class="modal-content" style="background: rgba(0,0,0,0); border: 0;">
            <div class="modal-body" style="height:400px;">
                <div id="loader" style="text-align: center;">
                    <i class="ace-icon fa fa-spinner fa-spin blue" style="font-size: 100px"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>

<script>
    $(document).ready(function() {
        if ($.fn.chosen) {
            $('#studentDropdown').chosen({
                width: '100%'
            });
        } else {
            console.error("Chosen plugin not loaded properly.");
        }
    });

    function updateNext(type) {
        $('#lmyModal').show();

        const class_id = $("#classDropdown").val();
        const section_id = $("#sectionDropdown").val();
        const student = $("#studentDropdown").val();

        if (type === 1) {
            // Load Sections
            $.ajax({
                url: 'index.php?r=site/getsections',
                type: 'GET',
                data: {
                    class_id
                },
                dataType: 'json',
                success: function(response) {
                    $('#lmyModal').hide();
                    const $sectionDropdown = $('#sectionDropdown');
                    $sectionDropdown.empty().append('<option value="">Select Section</option>');

                    if (response && response.length > 0) {
                        response.forEach(section => {
                            $sectionDropdown.append(
                                `<option value="${section.id}">${section.section_name}</option>`);
                        });
                        $('#sectionWrapper').show();
                    } else {
                        $('#sectionWrapper').hide();
                    }

                    // Reset Students dropdown
                    const $studentDropdown = $('#studentDropdown');
                    $studentDropdown.empty().append('<option value="">Select Student</option>').trigger(
                        "chosen:updated");
                },
                error: function() {
                    $('#lmyModal').hide();
                    alert('Failed to load sections.');
                }
            });
        } else if (type === 2) {
            // Load Students
            $.ajax({
                url: 'index.php?r=site/getstudents',
                type: 'GET',
                data: {
                    class_id,
                    section_id
                },
                dataType: 'json',
                success: function(response) {
                    $('#lmyModal').hide();
                    const $studentDropdown = $('#studentDropdown');
                    $studentDropdown.empty().append('<option value="">Select Student</option>');

                    if (response && response.length > 0) {
                        response.forEach(student => {
                            $studentDropdown.append(
                                `<option value="${student.id}">${student.name}</option>`);
                        });
                    }

                    $studentDropdown.trigger("chosen:updated");
                },
                error: function() {
                    $('#lmyModal').hide();
                    alert('Failed to load students.');
                }
            });
        } else if (type === 3) {
            // View student profile
            if (student) {
                viewProfile(student);
            } else {
                alert("Please select a student.");
            }
            $('#lmyModal').hide();
        }
    }


    function viewProfile(id) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?r=students/students_profile';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_csrf';
        csrfInput.value = '<?= Yii::$app->request->getCsrfToken() ?>';
        form.appendChild(csrfInput);
        const deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = 'view_profile';
        deleteInput.value = 'true';
        form.appendChild(deleteInput);
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'student_id';
        idInput.value = id;
        form.appendChild(idInput);

        form.target = '_blank';
        document.body.appendChild(form);
        form.submit();
    }
</script>

<style>
    /* Prevent page scrolling */
    html,
    body {
        overflow: hidden;
        height: 100vh;
    }

    .page-content {
        overflow: hidden !important;
    }

    /* Custom scrollbar for panels */
    .col-xs-5::-webkit-scrollbar,
    .col-xs-7::-webkit-scrollbar {
        width: 6px;
    }

    .col-xs-5::-webkit-scrollbar-track,
    .col-xs-7::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .col-xs-5::-webkit-scrollbar-thumb,
    .col-xs-7::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .col-xs-5::-webkit-scrollbar-thumb:hover,
    .col-xs-7::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    #chartdivsale {
        width: 100%;
        height: 450px;
    }

    #chartdivvendor {
        width: 100%;
        height: 450px;
    }

    #chartdivcust {
        width: 100%;
        height: 500px;
    }

    #chartdiv {
        width: 100%;
        height: 280px;
    }

    .divst {
        height: 60px;
        width: 110px;
        text-align: center;
    }

    /* Optimize table spacing */
    .no_items {
        margin-bottom: 8px;
    }

    .no_items thead th {
        padding: 8px 10px !important;
        font-size: 13px;
    }

    .no_items tbody td {
        padding: 6px 10px !important;
    }


    #chartdiv1 {
        width: 50%;
        height: 500px;
        font-size: 11px;
    }

    #chartdiv2 {
        width: 100%;
        min-height: 500px;
        font-size: 11px;
    }

    .amcharts-pie-slice {
        transform: scale(1);
        transform-origin: 50% 50%;
        transition-duration: 0.3s;
        transition: all .3s ease-out;
        -webkit-transition: all .3s ease-out;
        -moz-transition: all .3s ease-out;
        -o-transition: all .3s ease-out;
        cursor: pointer;
        box-shadow: 0 0 30px 0 #000;
    }

    .amcharts-pie-slice:hover {
        transform: scale(1.1);
        filter: url(#shadow);
    }

    td {
        padding: 3px !important;
    }

    .dotted_hr {
        border-top: 1px dotted #E2E2E2;
    }

    .myCard {
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        border-radius: 4px !important;
        padding: 15px 0px 15px 0px;
    }

    .myCard .badge {
        position: relative;
        top: -34px;
        right: -127px;
        padding: 6px;
        border-radius: 50px;
    }

    .myCard img {
        width: 45px !important;
        margin-left: -25px;
    }

    .myCard .link {
        text-align: right !important;
        margin-right: 10px !important;
    }

    .main-icons {
        background: none repeat scroll 0 0 #FFFFFF;
        border-radius: 10px;
        box-shadow: 0 0 45px -24px inset;
        margin: 7px;
        padding: 10px 0;
        text-align: center;
        height: 90px;
    }

    .showme1 {
        display: none;

    }

    .showhim1:hover .showme1 {
        display: block;
    }

    .showme {
        display: none;

    }

    .showhim:hover .showme {
        display: block;
    }

    .menu-icon {
        font-size: 40px;
    }

    .main-icons {
        text-align: center;
        margin-bottom: 20px;
    }

    .main-icons a {
        text-decoration: none;
        color: #333;
        /* Text color */
    }

    .main-icons a:hover .menu-icon {
        color: #FF5722;
        /* Icon hover color */
    }

    .main-icons h6 {
        margin-top: 10px;
        font-size: 0.99rem;
        color: #555;
    }

    .main-icons h6:hover {
        color: #FF5722;
        /* Icon hover color */
    }

    .dashboard {
        display: flex;
        flex-wrap: wrap;
        padding: 0px;
    }

    .card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin: 10px;
        padding: 20px;
        flex: 1 1 calc(25% - 40px);
        text-align: center;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card i {
        font-size: 2rem;
        margin-bottom: 10px;
        color: #4CAF50;
    }

    .card h3 {
        margin: 10px 0;
        font-size: 1.5rem;
        color: #333;
    }

    .card p {
        font-size: 1rem;
        color: #777;
    }

    .card .stat {
        font-size: 2rem;
        font-weight: bold;
        color: #4CAF50;
    }

    .infobox {
        display: inline-block;
        width: auto;
        height: auto;
        color: #555;
        background-color: #FFF;
        box-shadow: none;
        margin: -1px 0 0 -1px;
        padding: 8px 3px 6px 9px;
        border: 1px dotted;
        border-color: #D8D8D8 !important;
        vertical-align: middle;
        text-align: left;
        position: relative;
    }

    .info-box {
        display: block;
        /*min-height: 60px;*/
        background: #fff;
        width: 100%;
        box-shadow: 0 0 0 0 rgba(90, 113, 208, 0.11), 0 4px 16px 0 rgba(167, 175, 183, 0.33);
        border-radius: 0.35rem;
        margin-bottom: 10px;
        border-radius: 2px;
        transition: all .3s cubic-bezier(.25, .8, .25, 1);
        cursor: pointer;
        border: solid 1px #dde4eb;
    }

    .info-box a {
        color: #333;
        text-decoration: none;
        transition: all 0.3s linear;
        width: 100%;
        display: block;
        padding: 10px;
    }

    .info-box:hover {
        box-shadow: 0 5px 10px rgba(0, 0, 0, .25), 0 10px 10px rgba(0, 0, 0, .22);
        transform: translateY(-4px) scale(1.02);
    }

    .info-box-content {
        padding: 5px 10px;
        margin-left: 10px;
    }

    .info-box-number {
        display: block;
        font-family: 'Roboto-Bold';
        font-size: 18px;
    }

    .info-box-text {
        display: block;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-transform: uppercase;
    }

    .count {
        float: right;
        position: static;
        margin-top: -50px;
        font-size: xx-large;
    }

    .dashboard-header {
        margin: 10px 15px 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e0e0e0;
        font-family: 'Poppins', sans-serif;
    }

    .dashboard-title-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: -4%;
    }

    .dashboard-logo {
        width: 60px;
        height: 60px;
        object-fit: contain;
        border: none;
        border-radius: 0;
    }

    .dashboard-title {
        font-size: 22px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    .dashboard-subtitle {
        font-size: 12px;
        color: #7f8c8d;
        font-weight: 400;
    }

    /* Optimize infobox spacing */
    .infobox {
        margin: -1px 0 0 -1px !important;
        padding: 6px 3px 4px 9px !important;
    }

    .infobox-data-number {
        font-size: 20px !important;
    }

    .infobox-content {
        font-size: 11px !important;
    }

    /* Meetings Section Styles */
    .meetings-container {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e8eaed;
        padding: 15px;
    }

    .meetings-header {
        margin-bottom: 15px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e0e0e0;
    }

    .meetings-header h3 {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    .meetings-header h3 i {
        color: #3498db;
        margin-right: 8px;
        font-size: 15px;
    }

    .meetings-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-top: 10px;
    }

    .meeting-card {
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid #e8eaed;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
        max-height: 180px;
    }

    .meeting-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #3498db, #2980b9);
        transition: all 0.3s ease;
    }

    .meeting-card:hover {
        border-color: #3498db;
        background: #fafbfc;
    }

    .meeting-card:hover::before {
        height: 3px;
    }

    /* Status-specific styling */
    .meeting-card.status-scheduled {
        border-left: 3px solid #3498db;
    }

    .meeting-card.status-scheduled::before {
        background: #3498db;
    }

    .meeting-card.status-ongoing {
        border-left: 3px solid #27ae60;
        animation: glow 2s ease-in-out infinite;
    }

    .meeting-card.status-ongoing::before {
        background: #27ae60;
    }

    .meeting-card.status-completed {
        border-left: 3px solid #95a5a6;
        opacity: 0.9;
    }

    .meeting-card.status-completed::before {
        background: #95a5a6;
    }

    .meeting-card.status-cancelled {
        border-left: 3px solid #e74c3c;
        opacity: 0.85;
    }

    .meeting-card.status-cancelled::before {
        background: #e74c3c;
    }

    @keyframes glow {

        0%,
        100% {
            border-left-color: #27ae60;
        }

        50% {
            border-left-color: #2ecc71;
        }
    }

    .meeting-card-header {
        padding: 8px 12px;
        background: #f8f9fa;
        border-bottom: 1px solid #e8eaed;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .meeting-title {
        font-size: 12px;
        font-weight: 600;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 1;
        min-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .meeting-title i {
        color: #3498db;
        font-size: 12px;
    }

    .meeting-type-badge {
        font-size: 8px;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .meeting-type-badge.type-online {
        background: #e3f2fd;
        color: #1565c0;
    }

    .meeting-type-badge.type-general {
        background: #f3e5f5;
        color: #6a1b9a;
    }

    .meeting-card-body {
        padding: 8px 12px;
        background: #ffffff;
    }

    .meeting-info-row {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 5px;
        font-size: 10px;
        color: #495057;
        padding: 2px 0;
    }

    .meeting-info-row i {
        width: 14px;
        height: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3498db;
        font-size: 9px;
    }

    .meeting-info-row span {
        font-weight: 400;
    }

    .meeting-info-row:last-child {
        margin-bottom: 0;
    }

    .meeting-card-footer {
        padding: 6px 12px;
        background: #f8f9fa;
        border-top: 1px solid #e8eaed;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }

    .meeting-status-badge {
        font-size: 8px;
        padding: 3px 8px;
        border-radius: 10px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        text-transform: uppercase;
        letter-spacing: 0.2px;
    }

    .meeting-status-badge i {
        font-size: 8px;
    }

    .status-scheduled .meeting-status-badge {
        background: #e3f2fd;
        color: #1565c0;
    }

    .status-ongoing .meeting-status-badge {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .status-completed .meeting-status-badge {
        background: #eceff1;
        color: #546e7a;
    }

    .status-cancelled .meeting-status-badge {
        background: #ffebee;
        color: #c62828;
    }

    .meeting-view-btn {
        font-size: 9px;
        color: #fff;
        background: #3498db;
        text-decoration: none;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 12px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        text-transform: uppercase;
        letter-spacing: 0.2px;
    }

    .meeting-view-btn:hover {
        background: #2980b9;
        text-decoration: none;
        color: #fff;
    }

    .meeting-view-btn i {
        font-size: 10px;
        transition: transform 0.3s ease;
    }

    .meeting-view-btn:hover i {
        transform: translateX(3px);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .meetings-grid {
            grid-template-columns: 1fr;
        }

        .meeting-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .meeting-card-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (min-width: 769px) and (max-width: 1024px) {
        .meetings-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1025px) {
        .meetings-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1400px) {
        .meetings-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>
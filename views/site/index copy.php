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

<div class="page-content" style="padding: 1px; width: 100%; margin: 7px;">
    <div class="row">
        <div class="col-xs-12" style="margin-top: 2%;">

            <!-- Left Panel -->
            <div class="col-xs-5 dashicons">
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

                    <!-- Quick Links -->
                    <div class="row no-padding" style="margin-top : 12px; width: 106%;">
                        <div class="col-xs-12">
                            <table id="table_bug_report"
                                class="table table-striped table-bordered table-hover no_items">
                                <thead>
                                    <tr>
                                        <th colspan="5">Quick links (Reports) <i class="fa fa-link"
                                                style="color:grey"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $counter = 0;
                                    foreach ($reports as $index => $report) {
                                        if ($counter % 3 == 0) echo '<tr>';
                                        echo '<td><a href="index.php?r=reporting/index&' . htmlspecialchars($report['feature_id']) . '"  target="_blank">' . htmlspecialchars($report['name']) . '</a></td>';
                                        if ($counter % 3 == 2 || $index == count($reports) - 1) echo '</tr>';
                                        $counter++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

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

                    <!-- Recent Staff -->
                    <?php if (isset($recent_staff) && !empty($recent_staff)): ?>
                    <div class="row no-padding" style="margin-top : 12px; width: 106%;">
                        <div class="col-xs-12">
                            <table class="table table-striped table-bordered table-hover no_items">
                                <thead>
                                    <tr>
                                        <th colspan="2">Recent Staff <i class="fa fa-users" style="color:grey"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_staff as $staff): ?>
                                    <tr>
                                        <td style="width: 70%;"><?= htmlspecialchars($staff['name']) ?></td>
                                        <td style="width: 30%; font-size: 11px; color: #999;">
                                            <?= date('M d, Y', strtotime($staff['created_at'])) ?>
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
            <div class="col-xs-7">
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

                    <?php if ($can_subject_groups): ?>
                    <div class="col-xs-2 menu-icon_main main-icons">
                        <a href="index.php?r=academics/subjectgroup" target="_blank">
                            <img src="system_images/subject_group.png" style="height: 47px;" draggable="false"
                                alt="Subject Groups">
                            <h6>Subject Groups</h6>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($can_exams): ?>
                    <div class="col-xs-2 menu-icon_main main-icons">
                        <a href="index.php?r=examination/index" target="_blank">
                            <img src="system_images/exams.png" style="height: 47px;" draggable="false" alt="Exams List">
                            <h6>Exams List</h6>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($can_exam_results): ?>
                    <div class="col-xs-2 menu-icon_main main-icons">
                        <a href="index.php?r=examination/result" target="_blank">
                            <img src="system_images/exam_result.png" style="height: 47px;" draggable="false"
                                alt="Exam Result">
                            <h6>Exam Result</h6>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($can_syllabus): ?>
                    <div class="col-xs-2 menu-icon_main main-icons">
                        <a href="index.php?r=lessonplans/status" target="_blank">
                            <img src="system_images/syllabus.png" style="height: 47px;" draggable="false"
                                alt="Syllabus">
                            <h6>Syllabus</h6>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($can_attendance): ?>
                    <div class="col-xs-2 menu-icon_main main-icons">
                        <a href="index.php?r=humanresource/studentattendance" target="_blank">
                            <img src="system_images/attendance.png" style="height: 47px;" draggable="false"
                                alt="Attendance">
                            <h6>Attendance</h6>
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
                        <a href="index.php?r=reporting/index" target="_blank">
                            <img src="system_images/reporting.png" style="height: 47px;" draggable="false"
                                alt="Reporting">
                            <h6>Reporting</h6>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($can_front_office_setup): ?>
                    <div class="col-xs-2 main-icons">
                        <a href="index.php?r=frontoffice/setup" target="_blank">
                            <img src="system_images/frontoffice.png" style="height: 47px;" draggable="false"
                                alt="Setup Front Office">
                            <h6>Setup Front Office</h6>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($can_settings): ?>
                    <div class="col-xs-2 main-icons">
                        <a href="index.php?r=config/index" target="_blank">
                            <img src="system_images/settings.png" style="height: 47px;" draggable="false"
                                alt="Settings">
                            <h6>Settings</h6>
                        </a>
                    </div>
                    <?php endif; ?>
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
    margin: 20px 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e0e0e0;
    font-family: 'Poppins', sans-serif;
}

.dashboard-title-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-top: -6%;
}

.dashboard-logo {
    width: 75px;
    height: 75px;
    object-fit: contain;
    border: none;
    border-radius: 0;
}

.dashboard-title {
    font-size: 26px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.dashboard-subtitle {
    font-size: 14px;
    color: #7f8c8d;
    font-weight: 400;
}
</style>
<?php

/**
 * @author Prakash S
 * @copyright 2017
 */

namespace app\components;

use Yii;


use yii\base\Component;
use yii\base\InvalidConfigException;

class Permissions extends Component
{
    public function getMenus($active = null, $role_id = null)
    {

        $role_id = Yii::$app->session->get('user_array')['role_id'];
        $modules = Yii::$app->db->createCommand("SELECT * FROM modules WHERE 1=1 AND type=1  AND active=1 ORDER BY order_by ASC")->queryAll();
        // echo json_encode($modules);
        // exit;
        $moduleList = [];
        foreach ($modules as $module) {
            $submenus = [];

            // Module has no features - get direct module permissions
            $permissions = Yii::$app->db->createCommand("
                        SELECT * FROM permissions
                        WHERE module_id = :module_id
                        AND feature_id IS NULL
                        AND role_id = :role_id")
                ->bindValue(':module_id', $module['id'])
                ->bindValue(':role_id', $role_id)
                ->queryOne();

            if ($permissions && $permissions['can_view']) {
                $moduleList[] = [
                    'id' => $module['id'],
                    'module_id' => $module['id'],
                    'name' => $module['name'],
                    'title' => $module['name'],
                    'link' => $module['link'],
                    'icon' => $module['icon'],
                    'is_active' => (int) $module['active'],
                    'active' => false,
                    'submenus' => [],
                    'permission_id' => $permissions['id'],
                    'can_view' => (int) $permissions['can_view'],
                    'can_add' => (int) $permissions['can_add'],
                    'can_edit' => (int) $permissions['can_edit'],
                    'can_delete' => (int) $permissions['can_delete'],
                ];
            }
        }

        return $moduleList;
    }
    public function getTopbar($active = null, $role_id = null)
    {
        $role_id = Yii::$app->session->get('user_array')['role_id'];
        $school_id = Yii::$app->session->get('user_array')['school_id'] ?? null;

        // Build query with school_id filter
        $query = "SELECT * FROM modules WHERE type=2 AND active=1";
        $params = [];

        if ($school_id) {
            $query .= " AND (school_id = :school_id OR school_id IS NULL)";
            $params[':school_id'] = $school_id;
        }

        $query .= " ORDER BY order_by ASC";

        $command = Yii::$app->db->createCommand($query);
        foreach ($params as $key => $value) {
            $command->bindValue($key, $value);
        }
        $modules = $command->queryAll();

        $moduleList = [];
        foreach ($modules as $module) {
            $permissions = Yii::$app->db->createCommand(
                "SELECT * FROM permissions
                        WHERE module_id = :module_id
                        AND feature_id IS NULL
                        AND role_id = :role_id"
            )
                ->bindValue(':module_id', $module['id'])
                ->bindValue(':role_id', $role_id)
                ->queryOne();

            // Only include modules with permissions and can_view = 1
            if (!empty($permissions) && $permissions['can_view'] == 1) {
                $moduleList[] = [
                    'id' => $module['id'],
                    'title' => $module['name'],
                    'name' => $module['name'],
                    'description' => $module['description'] ?? $module['name'],
                    'is_active' => (int)$module['active'],
                    'link' => $module['link'],
                    'icon' => $module['icon'],
                    'active' => (int)$module['active'],
                    'order_by' => $module['order_by'] ?? 0,
                    'can_view' => (int)$permissions['can_view'],
                    'can_add' => (int)$permissions['can_add'],
                    'can_edit' => (int)$permissions['can_edit'],
                    'can_delete' => (int)$permissions['can_delete'],
                ];
            }
        }
        return $moduleList;
    }
    private function formatMenuItems($items)
    {
        $menuMap = [];
        foreach ($items as $item) {
            $menuMap[$item['id']] = [
                'id' => $item['id'],
                'title' => $item['title'],
                'link' => $item['link'],
                'icon' => $item['icon'],
                'parent_id' => $item['parent_id'],
                'active' => false,
                'is_active' => (int) $item['is_active'],
                'view' => (int) $item['view'],
                'update' => (int) $item['update'],
                'delete' => (int) $item['delete'],
                'submenus' => [],
            ];
        }

        // Organize items into a tree structure
        $formatted = [];
        foreach ($menuMap as $id => &$item) {
            if ($item['parent_id'] === null) {
                $formatted[] = &$item;
            } else {
                // Submenu item
                if (isset($menuMap[$item['parent_id']])) {
                    $menuMap[$item['parent_id']]['submenus'][] = &$item;
                }
            }
        }

        return $formatted;
    }
    public function getPermission($module_id)
    {

        $user = Yii::$app->session->get('user_array');

        // If no session exists, redirect to index.php
        if ($user === null) {
            return Yii::$app->response->redirect(['site/index'])->send();
        }

        // If user exists, get role_id safely
        $role_id = $user['role_id'] ?? null;


        $moduleFeatures = Yii::$app->db->createCommand("SELECT * FROM modules_features WHERE  is_active = 1 AND module_id = :module_id ORDER BY order_by ASC")
            ->bindValue(':module_id', $module_id)->queryAll();

        // If module has features, process them
        if (!empty($moduleFeatures)) {
            foreach ($moduleFeatures as $feature) {
                $permissions = Yii::$app->db->createCommand("
                                SELECT * FROM permissions
                                WHERE module_id = :module_id
                                AND feature_id = :feature_id
                                AND role_id = :role_id")
                    ->bindValue(':module_id', $module_id)
                    ->bindValue(':feature_id', $feature['id'])
                    ->bindValue(':role_id', $role_id)
                    ->queryOne();

                if ($permissions && $permissions['can_view']) {
                    $submenus[] = $feature;
                }
            }
            if (!empty($submenus)) {
                return $submenus;
            }
        }
        return [];
    }

    public function getNavbar() {}

    public function checkMethod($action)
    {
        $actions = [
            'students/index',
            'students/student',
            'students/savestudent',
            'students/import',
            'students/disabled',
            'students/categories',
            'students/createlogin',
            'students/student_history',

            'fee/index',
            'fee/collectfee',
            'fee/group',
            'fee/type',
            'fee/discount',
            'fee/feepayment',

            'income/income',
            'income/head',
            'income/index',

            'expense/expense',
            'expense/head',
            'expense/index',

            'examination/grade',
            'examination/group',
            'examination/design',
            'examination/marksheet',
            'examination/index',
            'examination/addexammarks',
            'examination/division',
            'examination/schedule',
            'examination/type',

            'academics/timetable',
            'academics/index',
            'academics/index',
            'academics/index',
            'academics/promotestudents',

            'humanresource/dashboard',
            'humanresource/index',
            'humanresource/staff',
            'humanresource/new_staff',
            'humanresource/staff_profile',
            'humanresource/attendance',
            'humanresource/leaverequests',
            'humanresource/payroll',
            'humanresource/generatepayroll',
            'humanresource/payrollapproval',
            'humanresource/unlockpayroll',
            'humanresource/bulkupdatepayrollstatus',
            'humanresource/studentattendance',
            'humanresource/attendancereport',
            'humanresource/createlogin',
            'humanresource/disablestaff',
            'humanresource/addleave',
            'humanresource/updatebankdetails',

            // Payroll Management
            'humanresource/savepayrollbulk',
            'humanresource/payslippdf',
            'humanresource/payslips',
            'humanresource/getallpayrolls',
            'humanresource/getpayrollperiods',
            'humanresource/printpayroll',
            'humanresource/revertpayroll',
            'humanresource/payrolldetails',
            'humanresource/payroll_details',
            'humanresource/payroll_list',
            'humanresource/updatepayrollstatus',
            'humanresource/deletepayroll',

            // Payroll Settings & Configuration
            'humanresource/getpayrollsettings',
            'humanresource/savepayrollsettings',
            'humanresource/deletepayrollsettings',
            'humanresource/savepayrollapprovalconfig',
            'humanresource/savepayrollconfig',
            'humanresource/savetaxslab',
            'humanresource/deletetaxslab',
            'humanresource/calculatetax',
            'humanresource/getpayrollbymonthyear',

            // Allowances Management
            'humanresource/saveallowancetype',
            'humanresource/updateallowancetype',
            'humanresource/deleteallowancetype',

            // Deductions Management
            'humanresource/savedeductiontype',
            'humanresource/updatedeductiontype',
            'humanresource/deletedeductiontype',
            'humanresource/leavetypes',
            'humanresource/saveleavetype',
            'humanresource/updateleavetype',
            'humanresource/deleteleavetype',
            'humanresource/newleave',
            'humanresource/applyleave',
            'humanresource/leaverequests',
            'humanresource/getapplicants',
            'humanresource/getavailedleaves',
            'humanresource/getleavestatus',
            'humanresource/saveleaveapplication',
            'humanresource/leaveapprovals',
            'humanresource/deleteleaveapprovalconfig',
            'humanresource/saveleaveapprovalconfig',
            'humanresource/getsystemusers',
            'humanresource/getpendingleaveapprovals',
            'humanresource/leaves',
            'humanresource/deletleave',
            'humanresource/deleteleave',
            'humanresource/searchleaves',
            'humanresource/getleavedetails',
            'humanresource/saveleaveapproval',
            'humanresource/searchleaverequests',


            'recruitment/dashboard',
            'recruitment/vacancies',
            'recruitment/deletevacancy',
            'recruitment/applicants',
            'recruitment/addapplicant',
            'recruitment/deleteapplicant',
            'recruitment/viewapplicant',
            'recruitment/shortlisted',
            'recruitment/interviews',
            'recruitment/deleteinterview',

            'behaviour/assignincident',
            'behaviour/incident',

            'fee/master',
            'fee/index',
            'fee/type',
            'fee/group',
            'fee/discount',
            'fee/master',
            'fee/assigngroup',
            'fee/collectfee',
            'fee/feedetails',
            'fee/feepayment',
            'fee/reminders',
            'fee/send-reminder',
            'fee/settings',
            'fee/check-fee-settings',
            'fee/load-class-config',
            'fee/save-class-config',
            'fee/configure-class-fees',
            'fee/cleanup-duplicates',
            'fee/generate-monthly-fees',
            'fee/mark-overdue-fees',
            'fee/truncate-fees',


            'behaviour/index',
            'settings/calendar',

            'academics/index',
            'academics/timetable',
            'academics/session',
            'academics/classes',
            'academics/sections',
            'academics/subjects',
            'academics/assignclassteacher',
            'academics/subjectgroup',

            // Academic Controller Module (Module ID: 7)
            'academic/dashboard',
            'academic/subjects',
            'academic/classes',
            'academic/sections',
            'academic/timetable',
            'academic/assignclassteacher',
            'academic/teachertimetable',
            'academic/subjectgroup',
            'academic/promotestudents',
            'academic/reports',
            'academic/attendance-dashboard',
            'academic/attendancedashboard',
            'academic/save-subject',
            'academic/delete-subject',
            'academic/save-class',
            'academic/delete-class',
            'academic/save-section',
            'academic/delete-section',
            'academic/period-wise-report',
            'academic/todays-timetable',
            'academic/overall-timetable',
            'academic/assign-students',
            'academic/teacherstimetable',
            'academic/class-details',
            'academic/class-timetable',
            'academic/period-utilization',
            'academic/todays-schedule',
            'academic/subjects-list',
            'academic/subject-teacher',
            'academic/classes-sections',
            'academic/teacher-workload',
            'academic/teacher-timetable',
            'academic/teacher-subjects',
            'academic/teacher-classes',
            'academic/teacher-sections',

            // Academic Reports Module Actions
            'academic/load-report',
            'academic/getacademicreports',
            'academic/academic-overview-report',
            'academic/academicoverviewreport',
            'academic/classes-summary-report',
            'academic/classessummaryreport',
            'academic/student-distribution-report',
            'academic/studentdistributionreport',
            'academic/class-timetable-report',
            'academic/classtimetablereport',
            'academic/teacher-workload-report',
            'academic/teacherworkloadreport',
            'academic/period-utilization-report',
            'academic/periodutilizationreport',
            'academic/todays-schedule-report',
            'academic/todaysschedulereport',
            'academic/subjects-list-report',
            'academic/subjectslistreport',
            'academic/subject-teacher-report',
            'academic/subjectteacherreport',
            'academic/classes-sections-report',
            'academic/classessectionsreport',
            'academic/class-capacity-report',
            'academic/classcapacityreport',
            'academic/sections-report',
            'academic/sectionsreport',
            'academic/class-teacher-assignments-report',
            'academic/classteacherassignmentsreport',
            'academic/subject-groups-report',
            'academic/subjectgroupsreport',
            'academic/student-promotions-report',
            'academic/studentpromotionsreport',
            'academic/class-lesson-plans',

            'library/index',
            'library/book',
            'library/return',
            'library/addmember',
            'library/addstudent',
            'library/importbooks',


            'frontoffice/admissionenquiry',
            'frontoffice/enquiry',
            'frontoffice/visitors',
            'frontoffice/visitor',
            'frontoffice/phonecalllogs',
            'frontoffice/calllog',
            'frontoffice/setup',
            'frontoffice/complaints',
            'frontoffice/managecomplaint',

            'examination/index',
            'examination/grade',
            'examination/group',
            'examination/addexam',
            'examination/addexammarks',
            'examination/getexamsubjects',
            'examination/getexamstudents',

            'examination/addstudents',
            'examination/teacherremarks',
            'examination/design',
            'examination/marksheet',
            'examination/marks',
            'examination/division',
            'examination/schedule',
            'examination/result',
            'examination/dashboard',
            'examination/exams',
            'examination/results',
            'examination/get-exam',
            'examination/save-exam',
            'examination/delete-exam',
            'examination/save-paper',
            'examination/delete-paper',
            'examination/get-results',
            'examination/save-result',
            'examination/delete-result',
            'examination/publish-results',
            'examination/get-sections',
            'examination/get-students',
            'examination/configuration',
            'examination/save-exam-type',
            'examination/delete-exam-type',
            'examination/save-exam-pattern',
            'examination/delete-exam-pattern',
            'examination/save-grading-scheme',
            'examination/delete-grading-scheme',
            'examination/save-passing-criteria',
            'examination/delete-passing-criteria',
            'examination/get-exam-calendar',

            'lessonplans/copy',
            'lessonplans/status',
            'lessonplans/lesson',
            'lessonplans/topic',

            'documentation/index',
            'documentation/create-folder',
            'documentation/update-folder',
            'documentation/delete-folder',
            'documentation/upload-document',
            'documentation/delete-document',
            'documentation/move-document',
            'documentation/download',
            'documentation/search',
            'documentation/get-roles',
            'documentation/get-users-by-role',

            'reporting/index',
            'reporting/getreports',
            'reporting/student_report',
            'reporting/student_classsection',
            'reporting/logindetails',
            'reporting/student_classsubject',
            'reporting/student_profile',
            'reporting/payroll',
            'reporting/onlineclasses',
            'reporting/getmeetingdetails',
            'reporting/exportmeetingreport',
            'reporting/onlineclasses_data',
            'reporting/activities',
            'reporting/activities_data',
            'reporting/fee_details',


            'reporting/attendance_report',
            'reporting/attendance_type_report',
            'reporting/daily_attendance_report',
            'reporting/student_daywise_attendance_report',
            'reporting/staff_daywise_attendance_report',
            'reporting/staff_attendance_report',
            'reporting/staff_report',
            'reporting/rank',
            'reporting/getexams',
            'reporting/student_history',


            'onlineclass/index',
            'onlineclass/room',
            'onlineclass/new_meeting',
            'onlineclass/changepermission',
            'onlineclass/getmembers',
            'onlineclass/join',
            'onlineclass/registerpeer',
            'onlineclass/unregisterpeer',
            'onlineclass/peers',
            'onlineclass/muteall',
            'onlineclass/removeparticipant',
            'onlineclass/updateparticipantstatus',
            'onlineclass/getparticipantstatus',
            'onlineclass/checkrecordingstatus',
            'onlineclass/startrecording',
            'onlineclass/leavelog',
            'onlineclass/webrtcsignal',
            'onlineclass/cleanupsignals',
            'onlineclass/uploadrecording',
            'onlineclass/recordings',
            'onlineclass/getrecordings',
            'onlineclass/deleterecording',
            'onlineclass/deleterecordingsbydate',
            'onlineclass/deleteallrecordings',
            'onlineclass/addcomment',
            'onlineclass/getcomments',
            'onlineclass/deletecomment',
            'onlineclass/sendmessage',
            'onlineclass/getmessages',
            'onlineclass/checkadmin',
            'onlineclass/endmeeting',
            'onlineclass/cancelmeeting',
            'onlineclass/reschedulemeeting',
            'onlineclass/autostartmeetings',
            'onlineclass/createrecurringmeetings',
            'onlineclass/toggleautostart',
            'onlineclass/meetinghistory',
            'onlineclass/testfiledeletion',
            'onlineclass/getsettings',
            'onlineclass/updatesettings',
            'onlineclass/getglobalsettings',
            'onlineclass/saveglobalsettings',
            'onlineclass/verifypassword',

            // Online Payments Module (Module ID: 124)
            'onlinepayments/dashboard',
            'onlinepayments/admindashboard',
            'onlinepayments/pendingapprovals',
            'onlinepayments/initiate',
            'onlinepayments/get-gateways',
            'onlinepayments/getgateways',
            'onlinepayments/filter-transactions',
            'onlinepayments/filtertransactions',
            'onlinepayments/callback',
            'onlinepayments/approvepayment',
            'onlinepayments/rejectpayment',
            'onlinepayments/settings',
            'onlinepayments/savegatewaysettings',
            'onlinepayments/getgatewaysettings',
            'onlinepayments/deletegatewaysetting',
            'onlinepayments/stripe-checkout',
            'onlinepayments/stripecheckout',
            'onlinepayments/easypaisa-checkout',
            'onlinepayments/easypaisacheckout',
            'onlinepayments/jazzcash-checkout',
            'onlinepayments/jazzcashcheckout',
            'onlinepayments/meezan-checkout',
            'onlinepayments/meezancheckout',
            'onlinepayments/process-stripe-payment',
            'onlinepayments/processstripepayment',
            'onlinepayments/process-easypaisa-payment',
            'onlinepayments/processeasypaisapayment',
            'onlinepayments/verify-easypaisa-otp',
            'onlinepayments/verifyeasypaisaotp',
            'onlinepayments/process-jazzcash-payment',
            'onlinepayments/processjazzcashpayment',
            'onlinepayments/verify-jazzcash-payment',
            'onlinepayments/verifyjazzcashpayment',
            'onlinepayments/process-meezan-payment',
            'onlinepayments/processmeezanpayment',



            'support/index',
            'support/getcounts',
            'support/loadsection',
            'support/create',
            'support/update',
            'support/reply',
            'support/delete',
            'support/search',


            'calendar/index',
            'calendar/getevents',
            'calendar/getusermeetings',

            'chatting/index',
            'chatting/get-messages',
            'chatting/get-new-messages',
            'chatting/send-message',
            'chatting/upload-file',
            'chatting/get-unread-count',
            'chatting/mark-as-read',
            'chatting/delete-message',
            'chatting/get-user-status',
            'chatting/typing',
            'chatting/set-offline',
            'chatting/get-user-info',
            'chatting/get-filtered-users',
            'chatting/get-sections',
            'chatting/get-classes',

            'crm/dashboard',
            'crm/leads',
            'crm/add-lead',
            'crm/edit-lead',
            'crm/save-lead',
            'crm/update-lead',
            'crm/get-lead',
            'crm/delete-lead',
            'crm/convert-lead',
            'crm/contacts',
            'crm/add-contact',
            'crm/edit-contact',
            'crm/save-contact',
            'crm/update-contact',
            'crm/get-contact',
            'crm/delete-contact',
            'crm/accounts',
            'crm/add-account',
            'crm/edit-account',
            'crm/save-account',
            'crm/update-account',
            'crm/get-account',
            'crm/delete-account',
            'crm/opportunities',
            'crm/add-opportunity',
            'crm/edit-opportunity',
            'crm/save-opportunity',
            'crm/update-opportunity',
            'crm/get-opportunity',
            'crm/delete-opportunity',
            'crm/pipeline',
            'crm/update-stage',
            'crm/activities',
            'crm/add-activity',
            'crm/edit-activity',
            'crm/save-activity',
            'crm/update-activity',
            'crm/get-activity',
            'crm/delete-activity',
            'crm/complete-activity',
            'crm/cases',
            'crm/add-case',
            'crm/edit-case',
            'crm/delete-case',
            'crm/resolve-case',
            'crm/reports',
            'crm/getcrmreports',
            'crm/leads-summary-report',
            'crm/leads-by-status-report',
            'crm/leads-by-source-report',
            'crm/lead-conversion-report',
            'crm/contacts-summary-report',
            'crm/contacts-by-type-report',
            'crm/contacts-by-account-report',
            'crm/accounts-summary-report',
            'crm/accounts-by-type-report',
            'crm/accounts-by-revenue-report',
            'crm/opportunities-summary-report',
            'crm/opportunities-by-stage-report',
            'crm/sales-forecast-report',
            'crm/won-lost-report',
            'crm/activities-summary-report',
            'crm/activities-by-type-report',
            'crm/activities-by-status-report',
            'crm/overdue-activities-report',
            'crm/cases-summary-report',
            'crm/cases-by-status-report',
            'crm/cases-by-priority-report',
            'crm/resolved-cases-report',
            'crm/campaigns-summary-report',
            'crm/campaign-roi-report',
            'crm/campaign-performance-report',
            'crm/forecast',
            'crm/emails',
            'crm/campaigns',
            'crm/documents',
            'crm/sync-metaleads',

            // Noticeboard Module (Module ID: 127)
            'noticeboard/dashboard',
            'noticeboard/index',
            'noticeboard/view',
            'noticeboard/create',
            'noticeboard/edit',
            'noticeboard/delete',
            'noticeboard/toggle-pin',
            'noticeboard/togglepin',
            'noticeboard/toggle-status',
            'noticeboard/togglestatus',

            // Syllabus Module (Module ID: 29)
            'syllabus/dashboard',
            'syllabus/index',
            'syllabus/create',
            'syllabus/syllabus-map',
            'syllabus/resources',
            'syllabus/approval',
            'syllabus/progress',
            'syllabus/view',
            'syllabus/delete',
            'syllabus/get-subjects',
            'syllabus/get-sections',
            'syllabus/save-mapping',
            'syllabus/get-mapping',
            'syllabus/delete-mapping',
            'syllabus/upload-resource',
            'syllabus/delete-resource',
            'syllabus/download-resource',
            'syllabus/update-progress',
            'syllabus/submit-for-approval',
            'syllabus/sample-data',
            'syllabus/get-subjects-by-class',
            // Legacy/Alternative action names (for backward compatibility)
            'syllabus/create-lesson-plan',
            'syllabus/manage-lesson-plans',
            'syllabus/lesson-plans',
            'syllabus/view-lesson-plan',
            'syllabus/edit-lesson-plan',
            'syllabus/delete-lesson-plan',
            'syllabus/syllabus-mapping',
            'syllabus/lesson-resources',
            'syllabus/approve-lesson',
            'syllabus/reject-lesson',
            'syllabus/progress-tracking',
            'syllabus/save-lesson-plan',
            'syllabus/get-lesson-plan',
            'syllabus/get-topics',
        ];
        return in_array($action, $actions);
    }
}

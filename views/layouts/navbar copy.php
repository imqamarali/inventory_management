<?php

use yii\helpers\Html;
use yii\helpers\Url;
// Initialize variables at the top before any HTML output
$role = Yii::$app->Component->CheckRole();
$cms_settings = Yii::$app->Component->School();
$active_session = Yii::$app->Component->AciveSession();
$user_first_name = Yii::$app->session->get('user_array')['first_name'] ?? 'User';
$user_initial = strtoupper(substr($user_first_name, 0, 1));

// Get current school information
$school_id = Yii::$app->session->get('user_array')['school_id'] ?? null;
$current_school = null;
if ($school_id) {
    try {
        $current_school = Yii::$app->db->createCommand('SELECT * FROM school WHERE school_id = :school_id')
            ->bindValue(':school_id', $school_id)
            ->queryOne();
    } catch (\Exception $e) {
        // If navbar_color column doesn't exist yet, handle gracefully
        $current_school = null;
    }
}

$school_name = $current_school['school_name'] ?? 'Online Quran Academy';
$school_motto = $current_school['motto'] ?? 'Learning Management System';
$school_logo = $current_school['logo'] ?? 'images/logos/home.png';
$navbar_color = $current_school['navbar_color'] ?? '#0f4c29';

// Get student modules if user is a student using Permissions component with role-based checks
$user_role_id = Yii::$app->session->get('user_array')['role_id'] ?? null;
$is_student = ($user_role_id == 4);
$student_modules = [];
$unread_notices_count = 0;
$unread_messages_count = 0;

if ($is_student) {
    try {
        // Use getTopbar() which applies role-based permissions (can_view check)
        $student_modules = Yii::$app->Permissions->getTopbar();

        // Sort by order_by if available
        usort($student_modules, function ($a, $b) {
            $orderA = $a['order_by'] ?? 999;
            $orderB = $b['order_by'] ?? 999;
            if ($orderA == $orderB) {
                return strcmp($a['name'] ?? '', $b['name'] ?? '');
            }
            return $orderA - $orderB;
        });

        // Get unread notices count for badge (module_id = 112)
        $user_id = Yii::$app->session->get('user_array')['id'] ?? null;
        if ($user_id) {
            try {
                $student_data = Yii::$app->db->createCommand(
                    "SELECT s.student_id, s.class_id, s.section_id
                     FROM students s
                     WHERE s.student_id = (SELECT referance FROM system_users WHERE id = :user_id)
                     AND s.school_id = :school_id"
                )->bindValues([
                    ':user_id' => $user_id,
                    ':school_id' => $school_id
                ])->queryOne();

                if ($student_data) {
                    $unread_notices_count = Yii::$app->db->createCommand(
                        "SELECT COUNT(*) FROM noticeboard n
                         LEFT JOIN noticeboard_views nv ON n.id = nv.noticeboard_id 
                                  AND nv.user_id = :user_id
                         WHERE n.is_active = 1
                         AND n.is_deleted = 0
                         AND n.school_id = :school_id
                         AND (
                             n.target_audience = 'all' 
                             OR n.target_audience = 'students'
                             OR (
                                 n.target_audience = 'specific' 
                                 AND (
                                     FIND_IN_SET(:class_id, n.target_class_ids) > 0
                                     OR FIND_IN_SET(:section_id, n.target_section_ids) > 0
                                 )
                             )
                         )
                         AND n.start_date <= CURDATE()
                         AND (n.end_date IS NULL OR n.end_date >= CURDATE())
                         AND nv.id IS NULL"
                    )->bindValues([
                        ':user_id' => $user_id,
                        ':school_id' => $school_id,
                        ':class_id' => $student_data['class_id'],
                        ':section_id' => $student_data['section_id']
                    ])->queryScalar() ?: 0;
                }
            } catch (\Exception $e) {
                $unread_notices_count = 0;
            }

            // Get unread messages count for chats badge
            try {
                $unread_messages_count = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM chat_messages
                     WHERE receiver_id = :user_id
                     AND is_read = 0
                     AND is_deleted_by_receiver = 0"
                )->bindValue(':user_id', $user_id)->queryScalar() ?: 0;
            } catch (\Exception $e) {
                $unread_messages_count = 0;
            }
        }
    } catch (\Exception $e) {
        // Log error if needed, but don't break the page
        $student_modules = [];
    }
}

// Calculate lighter shade for gradient
$hex = str_replace('#', '', $navbar_color);
$r = hexdec(substr($hex, 0, 2));
$g = hexdec(substr($hex, 2, 2));
$b = hexdec(substr($hex, 4, 2));
$lighter_color = sprintf("#%02x%02x%02x", min(255, $r + 30), min(255, $g + 30), min(255, $b + 30));
?>

<div id="navbar" class="navbar navbar-default ace-save-state" style="background: <?= $navbar_color ?>;">
    <div class="navbar-container ace-save-state" id="navbar-container">


        <!-- Google Fonts for Islamic Branding -->
        <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">

        <style>
            /* Modern Navbar Styling */
            #navbar {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                width: 100%;
                z-index: 1000;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                margin: 0;
            }

            /* Ensure main container starts right after navbar - CSS fallback */
            #main-container {
                padding-top: 52px !important;
                margin-top: 0 !important;
            }

            #navbar-container {
                min-width: 1200px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }


            .navbar-right-menu {
                display: flex;
                align-items: center;
                gap: 20px;
                padding: 10px 15px;
            }

            .navbar-icon-link {
                color: rgba(255, 255, 255, 0.9);
                font-size: 20px;
                transition: all 0.3s ease;
                /* padding: 8px 12px; */
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
            }

            .navbar-icon-link:hover {
                color: #ffd700;
                background: rgba(255, 255, 255, 0.1);
                transform: translateY(-2px);
                text-decoration: none;
            }

            /* Leave Approval Notification Badge */
            .leave-approval-notification {
                position: relative;
            }

            .leave-approval-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                background: #ff4444;
                color: white;
                border-radius: 50%;
                width: 18px;
                height: 18px;
                font-size: 10px;
                font-weight: bold;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 2px solid rgba(255, 255, 255, 0.9);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
                animation: pulse 2s infinite;
            }

            .leave-approval-badge.hidden {
                display: none;
            }

            /* Unread Notice Badge */
            .unread-notice-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                background: #4dabf7;
                color: white;
                border-radius: 50%;
                min-width: 18px;
                height: 18px;
                font-size: 10px;
                font-weight: bold;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 2px solid rgba(255, 255, 255, 0.9);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
                animation: pulse 2s infinite;
                padding: 0 4px;
            }

            /* Unread Messages Badge */
            .unread-messages-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                background: #25d366;
                color: white;
                border-radius: 50%;
                min-width: 18px;
                height: 18px;
                font-size: 10px;
                font-weight: bold;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 2px solid rgba(255, 255, 255, 0.9);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
                animation: pulse 2s infinite;
                padding: 0 4px;
                z-index: 10;
            }

            .unread-messages-badge.hidden {
                display: none;
            }

            @keyframes pulse {

                0%,
                100% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.1);
                }
            }

            /* WhatsApp Notification Toggle States */
            #whatsappNotificationToggle.active #whatsappIcon {
                color: #25D366;
            }

            #whatsappNotificationToggle.inactive #whatsappIcon {
                color: rgba(255, 255, 255, 0.4);
            }

            #whatsappNotificationToggle.active:hover #whatsappIcon {
                color: #1fb855;
            }

            /* Profile Circle with Initial */
            .profile-circle {
                width: 30px;
                height: 30px;
                border-radius: 50%;
                background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 18px;
                color: #0f4c29;
                border: 2px solid rgba(255, 255, 255, 0.3);
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                overflow: hidden;
                flex-shrink: 0;
            }

            .profile-circle img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 50%;
                display: block;
            }

            .profile-circle:hover {
                transform: scale(1.1);
                box-shadow: 0 4px 12px rgba(255, 215, 0, 0.4);
            }

            .profile-dropdown {
                position: relative;
            }

            .profile-menu {
                position: absolute;
                top: 100%;
                right: 0;
                margin-top: 10px;
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                min-width: 200px;
                opacity: 0;
                visibility: hidden;
                transform: translateY(-10px);
                transition: all 0.3s ease;
                z-index: 1000;
                overflow: hidden;
            }

            .profile-dropdown:hover .profile-menu {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }

            .profile-menu-header {
                background: <?= $lighter_color ?>;
                color: white;
                padding: 15px;
                text-align: center;
            }

            .profile-menu-header h4 {
                margin: 0;
                font-size: 16px;
                font-weight: 600;
            }

            .profile-menu-header p {
                margin: 5px 0 0 0;
                font-size: 12px;
                opacity: 0.9;
            }

            .profile-menu-item {
                padding: 12px 20px;
                color: #333;
                display: flex;
                align-items: center;
                gap: 10px;
                transition: all 0.2s ease;
                border-bottom: 1px solid #f0f0f0;
            }

            .profile-menu-item:hover {
                background: #f8f9fa;
                color: #0f4c29;
                text-decoration: none;
            }

            .profile-menu-item i {
                width: 20px;
                text-align: center;
            }

            /* Mobile menu items - only show on mobile */
            .mobile-menu-items {
                border-bottom: 2px solid #f0f0f0;
                margin-bottom: 5px;
            }

            @media (min-width: 769px) {
                .mobile-menu-items {
                    display: none !important;
                }
            }

            @media (max-width: 768px) {
                .mobile-menu-items {
                    display: block;
                }
            }

            .logo-container {
                display: flex;
                align-items: center;
                gap: 16px;
                padding: 8px 0;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .logo-container:hover {
                text-decoration: none;
                transform: translateX(2px);
            }

            .logo-icon-circle {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                border: 2px solid rgba(255, 215, 0, 0.6);
                box-shadow: 0 3px 12px rgba(0, 0, 0, 0.2),
                    0 0 0 1px rgba(255, 255, 255, 0.1),
                    inset 0 2px 4px rgba(255, 215, 0, 0.2);
                transition: all 0.3s ease;
                overflow: hidden;
                padding: 4px;
                position: relative;
            }

            .logo-icon-circle::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                transform: rotate(45deg);
                transition: all 0.5s ease;
            }

            .logo-icon-circle:hover::before {
                animation: shine 1.5s infinite;
            }

            @keyframes shine {
                0% {
                    transform: translateX(-100%) translateY(-100%) rotate(45deg);
                }

                100% {
                    transform: translateX(100%) translateY(100%) rotate(45deg);
                }
            }

            .logo-icon-circle img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                position: relative;
                z-index: 1;
            }

            .logo-icon-circle:hover {
                transform: scale(1.08) rotate(5deg);
                box-shadow: 0 6px 20px rgba(255, 215, 0, 0.5),
                    0 0 0 3px rgba(255, 215, 0, 0.3),
                    inset 0 2px 4px rgba(255, 215, 0, 0.3);
            }

            .school-info-container {
                display: flex;
                align-items: center;
                padding: 8px 20px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                margin-left: 20px;
            }

            .school-name {
                color: rgba(255, 255, 255, 0.95);
                font-size: 14px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .school-name i {
                color: rgba(255, 215, 0, 0.9);
            }

            .brand-info {
                display: flex;
                flex-direction: row;
                align-items: center;
                gap: 12px;
                position: relative;
                min-width: 0;
                width: 100%;
                overflow: hidden;
            }

            .brand-separator {
                color: rgba(255, 255, 255, 0.6);
                font-size: 18px;
                font-weight: 300;
                margin: 0 4px;
            }

            .brand-text {
                color: rgba(255, 255, 255, 0.98);
                font-size: 14px;
                letter-spacing: 0.8px;
                /*font-family: 'Amiri', serif;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
            position: relative;
            display: inline-block;
            background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0.9) 50%,rgba(255, 255, 255, 0.9) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s ease;
            white-space: nowrap; */
            }

            .logo-container:hover .brand-text {
                text-shadow: 0 2px 12px rgba(0, 0, 0, 0.4),
                    0 0 30px rgba(255, 215, 0, 0.4);
                transform: translateX(2px);
            }

            .brand-subtitle {
                color: rgba(255, 255, 255, 0.85);
                font-size: 13px;
                font-weight: 400;
                letter-spacing: 0.5px;
                text-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
                opacity: 0.9;
                transition: all 0.3s ease;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 100%;
            }

            .logo-container:hover .brand-subtitle {
                opacity: 1;
                color: rgba(255, 255, 255, 0.95);
            }

            .brand-wrapper {
                display: flex;
                align-items: center;
                gap: 16px;
                padding-left: 10px;
                /* max-width: 300px; */
                width: 100%;
                overflow: hidden;
            }

            .session-chips-container {
                display: flex;
                flex-direction: row;
                gap: 8px;
                align-items: center;
            }

            @media (max-width: 768px) {
                .navbar-right-menu {
                    gap: 8px;
                    flex-wrap: wrap;
                }

                /* Hide all navbar action icons on mobile */
                .navbar-icon-link {
                    display: none !important;
                }

                .profile-circle {
                    width: 35px;
                    height: 35px;
                    font-size: 16px;
                }

                .session-chip {
                    font-size: 8px;
                    padding: 3px 6px;
                    gap: 3px;
                }

                .session-chip i {
                    font-size: 9px;
                }

                .session-chip span {
                    font-size: 8px;
                }

                #navbar-container {
                    min-width: 100%;
                }

                .logo-icon-circle {
                    width: 35px;
                    height: 35px;
                }

                .brand-info {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 2px;
                }

                .brand-separator {
                    display: none;
                }

                .brand-text {
                    font-size: 14px;
                }

                .brand-subtitle {
                    font-size: 11px;
                }

                .brand-separator {
                    font-size: 14px;
                }

                .brand-info {
                    gap: 8px;
                }

                .brand-wrapper {
                    gap: 10px;
                    padding-left: 5px;
                    max-width: 200px;
                }

                .session-chip-mobile {
                    display: none;
                }
            }

            @media (max-width: 480px) {
                .brand-info {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 2px;
                }

                .brand-text {
                    font-size: 12px;
                    line-height: 1.2;
                }

                .brand-subtitle {
                    font-size: 8px;
                    line-height: 1.2;
                }

                .brand-separator {
                    display: none;
                }

                .brand-wrapper {
                    max-width: 135px;
                }

                .session-chip-mobile {
                    display: none;
                }

            }

            .session-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                border-radius: 999px;
                background: #eef2ff;
                color: #4338ca;
                border: 1px solid #c7d2fe;
                font-weight: 600;
                font-size: 12px;
            }

            .session-chip i {
                font-size: 14px;
            }
        </style>
        <div class="navbar-header pull-left">
            <a href="index.php?r=" class="navbar-brand">
                <small style="font-size:medium;"><?= htmlspecialchars($school_name) ?></small>
                <span class="brand-separator">|</span>
                <span class="brand-subtitle"><?= htmlspecialchars($school_motto) ?></span>
            </a>
        </div>


        <!-- Right Side: Icons and Profile -->
        <div class="navbar-buttons navbar-header pull-right" role="navigation" style="flex-shrink: 0;">
            <div class="navbar-right-menu">
                <!-- Role Chip -->
                <?php
                $role_name = 'Unknown';
                if ($user_role_id) {
                    try {
                        $role_data = Yii::$app->db->createCommand("SELECT name FROM roles WHERE id = :id")
                            ->bindValue(':id', $user_role_id)
                            ->queryOne();
                        $role_name = $role_data['name'] ?? 'Unknown';
                    } catch (\Exception $e) {
                        $role_name = 'Unknown';
                    }
                }
                ?>
                <div class="session-chips-container">
                    <div class="session-chip session-chip-mobile">
                        <i class="fa fa-user"></i>
                        <span>
                            <?= Html::encode($role_name) ?>
                        </span>
                    </div>
                </div>
                <?php if (Yii::$app->session->get('user_array')['role_id'] != 4): ?>
                    <!-- WhatsApp Notifications Toggle -->
                    <a href="javascript:void(0)" id="whatsappNotificationToggle" class="navbar-icon-link"
                        title="Toggle Chat Notifications" onclick="toggleChatNotifications()">
                        <i class="fa fa-whatsapp" id="whatsappIcon"></i>
                        <span id="notificationBadge" style="display: none; position: absolute; top: -5px; right: -5px; 
                          width: 8px; height: 8px; background: #25D366; border-radius: 50%; 
                          box-shadow: 0 0 4px #25D366;"></span>
                        <span id="unreadCountBadge" style="display: none; position: absolute; top: -8px; right: -8px; 
                          min-width: 18px; height: 18px; background: #ff4444; color: white; border-radius: 10px; 
                          font-size: 10px; font-weight: bold; padding: 2px 5px; line-height: 14px; text-align: center;
                          box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                    </a>

                    <!-- Leave Approval Notification -->
                    <a href="javascript:void(0)" id="leaveApprovalNotification"
                        class="navbar-icon-link leave-approval-notification" title="Pending Leave Approvals"
                        onclick="openLeaveApproval()">
                        <i class="fa fa-calendar-check-o"></i>
                        <span id="leaveApprovalBadge" class="leave-approval-badge hidden" title="Pending Approvals">0</span>
                    </a>

                    <!-- Ticket Icon -->
                    <a href="index.php?r=support/index" class="navbar-icon-link" title="Support Tickets">
                        <i class="fa fa-ticket"></i>
                    </a>

                    <!-- Settings Icon -->
                    <a href="index.php?r=config/" class="navbar-icon-link" title="Settings">
                        <i class="fa fa-cog"></i>
                    </a>
                <?php endif; ?>

                <!-- Profile Dropdown -->
                <div class="profile-dropdown">
                    <?php
                    $school_id = Yii::$app->Component->School_id();
                    $user_array = Yii::$app->session->get('user_array');
                    $user_id = $user_array['id'] ?? null;
                    $student_data = Yii::$app->db->createCommand(
                        "SELECT s.*, c.class_name, sec.section_name, sch.school_name
                            FROM students s
                            LEFT JOIN classes c ON s.class_id = c.id
                            LEFT JOIN sections sec ON s.section_id = sec.id
                            LEFT JOIN school sch ON s.school_id = sch.school_id
                            WHERE s.student_id = (
                                SELECT referance FROM system_users WHERE id = :user_id
                            ) AND s.school_id = :school_id"
                    )->bindValues([
                        ':user_id' => $user_id,
                        ':school_id' => $school_id
                    ])->queryOne();
                    $photo_path = $student_data['photo_path'] ?? null;
                    $profile_photo_url = !empty($photo_path) ? Url::to('@web/' . $photo_path, true) : null;
                    $initials = strtoupper(substr($student_data['first_name'] ?? 'S', 0, 1) . substr($student_data['last_name'] ?? 'T', 0, 1));
                    ?>

                    <div class="profile-circle" title="<?= $user_first_name ?>">
                        <?php if ($profile_photo_url): ?>
                            <img src="<?= $profile_photo_url ?>" alt="Profile Picture"
                                onerror="this.style.display='none'; this.parentElement.textContent='<?= $initials ?>';">
                        <?php else: ?>
                            <?= $initials ?>
                        <?php endif; ?>
                    </div>
                    <div class="profile-menu">
                        <div class="profile-menu-header">
                            <h4><?= $user_first_name ?></h4>
                            <p>Welcome back!</p>
                        </div>

                        <!-- Mobile Menu Items (shown only on mobile) -->
                        <?php if ($user_role_id != 4): ?>
                            <div class="mobile-menu-items">
                                <a href="javascript:void(0)" class="profile-menu-item"
                                    onclick="toggleChatNotifications(); return false;">
                                    <i class="fa fa-whatsapp" style="color: #25D366;"></i>
                                    <span>Chat Notifications</span>
                                    <span id="chatStatusText"
                                        style="margin-left: auto; font-size: 11px; color: #999;">OFF</span>
                                </a>

                                <a href="index.php?r=support/index" class="profile-menu-item">
                                    <i class="fa fa-ticket" style="color: #478fca;"></i>
                                    <span>Support Tickets</span>
                                </a>

                                <a href="index.php?r=config/" class="profile-menu-item">
                                    <i class="fa fa-cog" style="color: #87b87f;"></i>
                                    <span>Settings</span>
                                </a>
                            </div>
                        <?php endif; ?>

                        <a href="index.php?r=config/profile" class="profile-menu-item">
                            <i class="fa fa-user"></i>
                            <span>My Profile</span>
                        </a>
                        <?= \yii\helpers\Html::a(
                            '<i class="fa fa-power-off"></i><span>Logout</span>',
                            ['site/logout', 'csrf' => Yii::$app->request->csrfToken],
                            [
                                'data-method' => 'post',
                                'data-params' => [
                                    Yii::$app->request->csrfParam => Yii::$app->request->csrfToken,
                                ],
                                'class' => 'profile-menu-item'
                            ]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /.navbar-container -->
</div>

<!-- Student Modules Bar (Below Navbar) -->
<?php if ($is_student && !empty($student_modules)): ?>
    <div id="student-modules-bar" class="student-modules-bar"
        style="position: fixed; top: 52px; left: 0; right: 0; z-index: 998; background:#fafafa box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-top: 1px solid rgba(255,255,255,0.1);">
        <div class="student-modules-container"
            style="max-width: 100%; overflow-x: auto; overflow-y: hidden; padding: 10px 20px;">
            <div style="display: flex; gap: 8px; align-items: center; min-width: fit-content;">
                <?php foreach ($student_modules as $module):
                    // Only display modules with can_view permission
                    if (empty($module['can_view']) || $module['can_view'] != 1) {
                        continue;
                    }

                    // Clean up icon class (remove 'ftlayer' and other non-icon classes)
                    $icon_class = trim($module['icon'] ?? 'fa fa-circle');
                    $icon_parts = explode(' ', $icon_class);
                    $icon_class = implode(' ', array_filter($icon_parts, function ($part) {
                        return strpos($part, 'fa-') === 0 || $part === 'fa' || strpos($part, 'ace-icon') === 0;
                    }));
                    if (empty($icon_class)) {
                        $icon_class = 'fa fa-circle';
                    }

                    // Prepare link
                    $link = trim($module['link'] ?? '');
                    $href = !empty($link) ? 'index.php?r=' . htmlspecialchars($link) : '#';
                    $module_name = htmlspecialchars($module['name'] ?? $module['title'] ?? 'Module');
                    $module_desc = htmlspecialchars($module['description'] ?? $module_name);
                    $module_id = $module['id'] ?? 0;

                    // Check if this is the Notice Board module (id = 112)
                    $show_notice_badge = ($module_id == 112 && $unread_notices_count > 0);

                    // Check if this is the Chats module (check by link)
                    $is_chats_module = (stripos($link, 'student/chats') !== false || stripos($link, 'chats') !== false);
                    $show_chats_badge = ($is_chats_module && $unread_messages_count > 0);
                ?>
                    <a href="<?= $href ?>" class="student-module-item" title="<?= $module_desc ?>"
                        style="display: flex; flex-direction: column; align-items: center; gap: 4px; 
                padding: 8px 12px; border-radius: 8px; text-decoration: none; transition: all 0.3s ease;
                 min-width: 70px; background: #fafafa; border: 1px solid rgba(5, 5, 5, 0.15); 
                 white-space: nowrap; position: relative;"
                        <?php if ($is_chats_module): ?>data-module="chats" <?php endif; ?>>
                        <i class="<?= htmlspecialchars($icon_class) ?>"
                            style="font-size: 18px; color: black;"></i>
                        <span
                            style="font-size: 10px; color:black; font-weight: 500; text-align: center; line-height: 1.2;">
                            <?= $module_name ?>
                        </span>
                        <?php if ($show_notice_badge): ?>
                            <span class="unread-notice-badge"
                                title="<?= $unread_notices_count ?> unread notice<?= $unread_notices_count > 1 ? 's' : '' ?>">
                                <?= $unread_notices_count > 99 ? '99+' : $unread_notices_count ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($is_chats_module): ?>
                            <span class="unread-messages-badge <?= $unread_messages_count > 0 ? '' : 'hidden' ?>"
                                id="unreadMessagesBadge"
                                title="<?= $unread_messages_count ?> unread message<?= $unread_messages_count > 1 ? 's' : '' ?>">
                                <?= $unread_messages_count > 99 ? '99+' : $unread_messages_count ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <style>
        .student-modules-bar {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .student-module-item:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 255, 255, 0.3) !important;
        }

        .student-module-item i {
            transition: transform 0.3s ease;
        }

        .student-module-item:hover i {
            transform: scale(1.15);
            color: #ffd700 !important;
        }

        .student-modules-container::-webkit-scrollbar {
            height: 4px;
        }

        .student-modules-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
        }

        .student-modules-container::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }

        .student-modules-container::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .student-module-item {
                min-width: 60px !important;
                padding: 6px 8px !important;
            }

            .student-module-item i {
                font-size: 16px !important;
            }

            .student-module-item span {
                font-size: 9px !important;
            }
        }
    </style>

    <script>
        // Adjust layout when student modules bar is present
        (function() {
            function adjustLayoutForStudentModules() {
                var studentBar = document.getElementById('student-modules-bar');
                var mainContainer = document.getElementById('main-container');
                var sidebar = document.getElementById('sidebar');
                var mainContent = document.querySelector('.main-content1');
                var navbar = document.getElementById('navbar');

                if (studentBar && navbar) {
                    // Get actual rendered heights
                    var navbarHeight = navbar.offsetHeight || 52;
                    var studentBarHeight = studentBar.offsetHeight || 60;
                    var totalHeight = navbarHeight + studentBarHeight;

                    // Ensure minimum height
                    if (totalHeight < 100) {
                        totalHeight = 112; // Fallback minimum
                    }

                    if (mainContainer) {
                        mainContainer.style.paddingTop = totalHeight + 'px';
                    }

                    // Only adjust sidebar if it exists and is visible (not for students)
                    if (sidebar && sidebar.offsetParent !== null) {
                        sidebar.style.top = totalHeight + 'px';
                    }

                    if (mainContent) {
                        mainContent.style.top = totalHeight + 'px';
                        // Adjust height to account for top position
                        mainContent.style.height = 'calc(100vh - ' + totalHeight + 'px)';
                    }
                }
            }

            // Run multiple times to ensure proper calculation
            function initLayout() {
                adjustLayoutForStudentModules();
                setTimeout(adjustLayoutForStudentModules, 50);
                setTimeout(adjustLayoutForStudentModules, 100);
                setTimeout(adjustLayoutForStudentModules, 300);
            }

            // Run on load and after delays
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initLayout);
            } else {
                initLayout();
            }

            // Also run when window loads (after images/fonts)
            window.addEventListener('load', function() {
                setTimeout(adjustLayoutForStudentModules, 100);
            });

            window.addEventListener('resize', adjustLayoutForStudentModules);
        })();
    </script>
<?php endif; ?>

<?php
$schools = Yii::$app->db->createCommand('SELECT * FROM school')->queryAll();
?>


<div class="modal fade" id="multiBranchSwitchModal" tabindex="-1" role="dialog"
    aria-labelledby="multiBranchSwitchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="index.php?r=config/changeschool" method="post" id="changeschool">
            <div class="modal-content">
                <div class="widget-box" style="margin: 0px;">
                    <div class="widget-header">
                        <h4 class="widget-title">Schools</h4>
                        <div id="cancel" class="widget-toolbar" style="cursor: pointer;padding: 7px;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" style="color: red;">&times;</span>
                            </button>
                        </div>

                        <!-- Correct the form attribute here -->
                        <div class="widget-toolbar" style="padding: 0px 10px">
                            <button type="submit" class="ace-icon fa fa-check icon-on-right bigger-110"
                                form="changeschool" style="cursor: pointer; background: transparent; border:
                                            none;"></button>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div style="padding: 8pxa 10px">
                        <select class="chosen-select" data-placeholder="School" name="school">
                            <option value="">Select School</option>

                            <?php foreach ($schools as $school): ?>
                                <option value="<?= htmlspecialchars($school['school_id']) ?>">
                                    <?= htmlspecialchars($school['school_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
    // Leave Approval Notification System
    let leaveApprovalCheckInterval = null;
    let currentPendingApprovals = [];

    // Check for pending leave approvals
    function checkPendingLeaveApprovals() {
        $.ajax({
            url: 'index.php?r=humanresource/getpendingleaveapprovals',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.count > 0) {
                    currentPendingApprovals = response.approvals;
                    updateLeaveApprovalBadge(response.count);
                } else {
                    currentPendingApprovals = [];
                    updateLeaveApprovalBadge(0);
                }
            },
            error: function() {
                // Silently fail - don't show errors for background checks
            }
        });
    }

    // Update leave approval badge
    function updateLeaveApprovalBadge(count) {
        const badge = document.getElementById('leaveApprovalBadge');
        const icon = document.getElementById('leaveApprovalNotification');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
                if (icon) {
                    icon.setAttribute('title', count + ' Pending Leave Approval' + (count > 1 ? 's' : ''));
                }
            } else {
                badge.classList.add('hidden');
                if (icon) {
                    icon.setAttribute('title', 'Pending Leave Approvals');
                }
            }
        }
    }

    // Open leave approval - redirect to first pending leave
    function openLeaveApproval() {
        if (currentPendingApprovals.length > 0) {
            const firstLeave = currentPendingApprovals[0];
            window.location.href = 'index.php?r=humanresource/leaverequests&leave_id=' + firstLeave.leave_id;
        } else {
            // If no pending, just go to leave requests page
            window.location.href = 'index.php?r=humanresource/leaverequests';
        }
    }

    // Start checking for pending approvals every 30 seconds
    $(document).ready(function() {
        // Check immediately on page load
        checkPendingLeaveApprovals();

        // Then check every 30 seconds
        leaveApprovalCheckInterval = setInterval(checkPendingLeaveApprovals, 30000);
    });

    // Calculate navbar height and set main-container padding to match exactly (no gap)
    (function() {
        function setMainContainerPadding() {
            var navbar = document.getElementById('navbar');
            var mainContainer = document.getElementById('main-container');

            if (navbar && mainContainer) {
                // Get the actual rendered height of the navbar
                var navbarHeight = navbar.offsetHeight || navbar.clientHeight;

                // Ensure we have a valid height
                if (navbarHeight > 0) {
                    mainContainer.style.paddingTop = navbarHeight + 'px';
                    mainContainer.style.marginTop = '0';
                }
            }
        }

        // Multiple methods to ensure it runs at the right time
        function initPadding() {
            setMainContainerPadding();

            // Also try after a short delay to ensure DOM is fully rendered
            setTimeout(setMainContainerPadding, 10);
            setTimeout(setMainContainerPadding, 100);
        }

        // Set on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPadding);
        } else {
            // DOM already loaded, run immediately and after a short delay
            initPadding();
        }

        // Recalculate on window resize (in case navbar height changes)
        window.addEventListener('resize', setMainContainerPadding);

        // Also recalculate when images/fonts load (in case they affect navbar height)
        window.addEventListener('load', setMainContainerPadding);
    })();

    // Update unread messages count badge for students
    <?php if ($is_student): ?>
            (function() {
                function updateUnreadMessagesCount() {
                    var chatsModule = document.querySelector('[data-module="chats"]');
                    if (!chatsModule) return;

                    $.ajax({
                        url: '<?= Url::to(['student/get-unread-count']) ?>',
                        method: 'POST',
                        success: function(response) {
                            if (response.success) {
                                var count = response.count || 0;
                                var badge = document.getElementById('unreadMessagesBadge');

                                if (count > 0) {
                                    // Create badge if it doesn't exist
                                    if (!badge) {
                                        badge = document.createElement('span');
                                        badge.id = 'unreadMessagesBadge';
                                        badge.className = 'unread-messages-badge';
                                        chatsModule.appendChild(badge);
                                    }

                                    // Update badge content
                                    badge.textContent = count > 99 ? '99+' : count;
                                    badge.title = count + ' unread message' + (count > 1 ? 's' : '');
                                    badge.classList.remove('hidden');
                                } else {
                                    // Hide badge if count is 0
                                    if (badge) {
                                        badge.classList.add('hidden');
                                    }
                                }
                            }
                        },
                        error: function() {
                            // Silently fail - don't show errors for background updates
                        }
                    });
                }

                // Update immediately on page load
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() {
                        updateUnreadMessagesCount();
                        // Update every 10 seconds
                        setInterval(updateUnreadMessagesCount, 10000);
                    });
                } else {
                    updateUnreadMessagesCount();
                    // Update every 10 seconds
                    setInterval(updateUnreadMessagesCount, 10000);
                }
            })();
    <?php endif; ?>
</script>
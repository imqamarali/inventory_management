<?php
// Get navbar colors to match the navbar styling
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

// Calculate lighter shade for gradient (same as navbar.php)
$hex = str_replace('#', '', $navbar_color);
$r = hexdec(substr($hex, 0, 2));
$g = hexdec(substr($hex, 2, 2));
$b = hexdec(substr($hex, 4, 2));
$lighter_color = sprintf("#%02x%02x%02x", min(255, $r + 30), min(255, $g + 30), min(255, $b + 30));
?>

<style>
    /* Desktop Sidebar - Vertical */
    @media (min-width: 768px) {
        #sidebar {
            position: fixed !important;
            top: 47px !important;
            bottom: 0px;
            left: 0 !important;
            bottom: 60px !important;
            height: calc(100vh) !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            z-index: 100 !important;
            width: 190px !important;
        }

        #sidebar .nav-list {
            display: block !important;
        }

        .mobile-modules-bar {
            display: none !important;
        }
    }

    /* Mobile Sidebar - Horizontal Bar (like Student Modules) */
    @media (max-width: 767px) {
        #sidebar {
            display: none !important;
        }

        .mobile-modules-bar {
            position: fixed;
            top: 52px;
            left: 0;
            right: 0;
            z-index: 998;
            background: linear-gradient(135deg, <?= $navbar_color ?> 0%, <?= $lighter_color ?> 100%);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            animation: slideDown 0.3s ease-out;
            min-height: 60px;
        }

        .mobile-modules-container {
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            padding: 10px 15px;
        }

        .mobile-modules-grid {
            display: flex;
            gap: 8px;
            align-items: center;
            min-width: fit-content;
        }

        .mobile-module-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            min-width: 70px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            white-space: nowrap;
            position: relative;
        }

        .mobile-module-item:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 255, 255, 0.3) !important;
            text-decoration: none;
        }

        .mobile-module-item i {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.95);
            transition: transform 0.3s ease;
        }

        .mobile-module-item:hover i {
            transform: scale(1.15);
            color: #ffd700 !important;
        }

        .mobile-module-item span {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            text-align: center;
            line-height: 1.2;
        }

        .mobile-modules-container::-webkit-scrollbar {
            height: 4px;
        }

        .mobile-modules-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
        }

        .mobile-modules-container::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }

        .mobile-modules-container::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Adjust main container and content for mobile bar */
        #main-container {
            padding-top: 112px !important;
            margin-top: 0 !important;
        }

        .main-content,
        .main-content1 {
            margin-top: 5px !important;
            padding-top: 0 !important;
            position: relative !important;
            top: 0 !important;
        }

        .page-content {
            padding-top: 10px;
        }

        /* Ensure breadcrumbs don't get hidden */
        .breadcrumbs {
            margin-top: 0 !important;
        }

        /* Main content inner */
        .main-content-inner {
            padding-top: 0 !important;
        }
    }

    @media (max-width: 480px) {
        .mobile-module-item {
            min-width: 60px !important;
            padding: 6px 8px !important;
        }

        .mobile-module-item i {
            font-size: 16px !important;
        }

        .mobile-module-item span {
            font-size: 9px !important;
        }
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
</style>

<!-- Desktop Sidebar - Traditional Vertical -->
<div id="sidebar" class="sidebar responsive ace-save-state">


    <ul class="nav nav-list" style="margin-bottom: 70px">

        <?php

        // Fetch menu items from the permissions class
        $sidebarItems = Yii::$app->Permissions->getMenus();

        // Function to render the menu items
        function renderMenuItems($items)
        {
            foreach ($items as $item) {
                if ($item['can_view'] == '0') continue;
                $allSubmenusRestricted = true;

                // Check if all submenus are restricted
                if (!empty($item['submenus'])) {
                    foreach ($item['submenus'] as $submenu) {
                        if ($submenu['can_view'] == '1') {
                            $allSubmenusRestricted = false;
                            break;
                        }
                    }
                }

                // Start rendering the menu item
                echo '<li class="' . ($item['active'] ? 'active' : '') . '">';

                // If the item is restricted, set href to javascript:void(0)
                $href = ($item['can_view'] == '0') ? 'javascript:void(0)' : 'index.php?r=' . $item['link'];

                // Check if the item has a valid link, or if it's restricted
                echo '<a href="' . $href . '" class="' . (!empty($item['submenus']) ? 'dropdown-toggle' : '') . '">';

                echo '<i class="menu-icon ' . $item['icon'] . '"></i>';
                echo '<span class="menu-text">' . $item['title'];

                // If all submenus are restricted, show lock icon in the main menu
                if ($allSubmenusRestricted && !empty($item['submenus'])) {
                    echo '<span title="" class="badge badge-transparent tooltip-error" data-original-title="Permissions Restricted">
                        <i class="ace-icon fa fa-lock red bigger-60"></i>
                    </span>';
                }

                // If the item itself is restricted, show lock icon
                if ($item['can_view'] == '0') {
                    echo '<span title="" class="badge badge-transparent tooltip-error" data-original-title="Permissions Restricted">
                        <i class="ace-icon fa fa-lock red bigger-60"></i>
                    </span>';
                }

                echo '</span>';

                // If there are submenus and the item is not restricted, show dropdown arrow
                echo (!empty($item['submenus']) && $item['can_view'] == '1') ? '<b class="arrow fa fa-angle-down"></b>' : '';
                echo '</a>';
                echo '<b class="arrow"></b>';

                // Check for submenus and render them recursively
                if (!empty($item['submenus'])) {
                    echo '<ul class="submenu">';
                    renderMenuItems($item['submenus']);
                    echo '</ul>'; // Closing submenu
                }

                echo '</li>'; // Closing main menu item
            }
        }

        // Render the sidebar items
        renderMenuItems($sidebarItems);
        ?>
        <!-- <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
            <i id="sidebar-toggle-icon" class="ace-save-state ace-icon fa fa-angle-double-left"
                data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
        </div> -->
    </ul>

</div>

<!-- Mobile Modules Bar (Horizontal - Like Student Modules Bar) -->
<div class="mobile-modules-bar">
    <div class="mobile-modules-container">
        <div class="mobile-modules-grid">
            <?php
            // Get all menu items for mobile display
            $sidebarItems = Yii::$app->Permissions->getMenus();

            // Function to extract all menu items (including submenus) as flat list
            function extractAllMenuItems($items, &$flatList = [])
            {
                foreach ($items as $item) {
                    if ($item['can_view'] == '1') {
                        $flatList[] = $item;
                    }
                    // Also extract submenus
                    if (!empty($item['submenus'])) {
                        extractAllMenuItems($item['submenus'], $flatList);
                    }
                }
                return $flatList;
            }

            $allMenuItems = extractAllMenuItems($sidebarItems);

            foreach ($allMenuItems as $module):
                // Clean up icon class
                $icon_class = trim($module['icon'] ?? 'fa fa-circle');
                $icon_parts = explode(' ', $icon_class);
                $icon_class = implode(' ', array_filter($icon_parts, function ($part) {
                    return strpos($part, 'fa-') === 0 || $part === 'fa' || strpos($part, 'menu-icon') === 0;
                }));
                if (empty($icon_class)) {
                    $icon_class = 'fa fa-circle';
                }

                // Prepare link
                $link = trim($module['link'] ?? '');
                $href = !empty($link) ? 'index.php?r=' . htmlspecialchars($link) : '#';
                $module_name = htmlspecialchars($module['title'] ?? $module['name'] ?? 'Module');
                $module_desc = htmlspecialchars($module['description'] ?? $module_name);
            ?>
                <a href="<?= $href ?>" class="mobile-module-item" title="<?= $module_desc ?>">
                    <i class="<?= $icon_class ?>"></i>
                    <span><?= $module_name ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    // Adjust layout for mobile modules bar
    (function() {
        function adjustLayoutForMobileModules() {
            var mobileBar = document.querySelector('.mobile-modules-bar');
            var navbar = document.getElementById('navbar');

            // Only adjust on mobile
            if (window.innerWidth < 768) {
                if (mobileBar && navbar) {
                    var navbarHeight = navbar.offsetHeight || 52;
                    var mobileBarHeight = mobileBar.offsetHeight || 60;
                    var totalHeight = navbarHeight + mobileBarHeight;

                    // Ensure minimum height
                    if (totalHeight < 100) {
                        totalHeight = 112; // Fallback
                    }

                    console.log('Mobile layout: navbar=' + navbarHeight + 'px, bar=' + mobileBarHeight + 'px, total=' +
                        totalHeight + 'px');

                    // Apply to all possible main containers
                    var containers = [
                        document.getElementById('main-container'),
                        document.querySelector('.main-content'),
                        document.querySelector('.main-content1')
                    ];

                    containers.forEach(function(container) {
                        if (container) {
                            container.style.marginTop = totalHeight + 'px';
                            container.style.paddingTop = '0';
                        }
                    });
                }
            } else if (window.innerWidth >= 768) {
                // Reset on desktop
                var containers = [
                    document.getElementById('main-container'),
                    document.querySelector('.main-content'),
                    document.querySelector('.main-content1')
                ];

                containers.forEach(function(container) {
                    if (container) {
                        container.style.marginTop = '';
                        container.style.paddingTop = '';
                    }
                });
            }
        }

        // Run multiple times to ensure proper calculation
        function initLayout() {
            adjustLayoutForMobileModules();
            setTimeout(adjustLayoutForMobileModules, 50);
            setTimeout(adjustLayoutForMobileModules, 100);
            setTimeout(adjustLayoutForMobileModules, 200);
            setTimeout(adjustLayoutForMobileModules, 500);
        }

        // Run on load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initLayout);
        } else {
            initLayout();
        }

        // Run on various events
        window.addEventListener('resize', adjustLayoutForMobileModules);
        window.addEventListener('load', function() {
            setTimeout(adjustLayoutForMobileModules, 100);
            setTimeout(adjustLayoutForMobileModules, 500);
        });
        window.addEventListener('orientationchange', function() {
            setTimeout(adjustLayoutForMobileModules, 100);
        });
    })();
</script>
<div id="sidebar" class="sidebar responsive ace-save-state">


    <div class="sidebar-shortcuts" id="sidebar-shortcuts">
        <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
            <button class="btn btn-success">
                <i class="ace-icon fa fa-signal"></i>
            </button>

            <button class="btn btn-info">
                <i class="ace-icon fa fa-pencil"></i>
            </button>

            <button class="btn btn-warning">
                <a href="index.php?r=site/icons">
                    <i class="ace-icon fa fa-users" style="color: white"></i>
                </a>
            </button>

            <button class="btn btn-danger">
                <a href="index.php?r=config/">
                    <i class="ace-icon fa fa-cogs" style="color: white"></i>
                </a>
            </button>

        </div>

        <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
            <span class="btn btn-success"></span>

            <span class="btn btn-info"></span>

            <span class="btn btn-warning"></span>

            <span class="btn btn-danger"></span>
        </div>
    </div>
    <!-- /.sidebar-shortcuts -->

    <ul class="nav nav-list">

        <?php


        echo '<li class="">';
        echo '<a href="index.php?r=site/links" class="">';
        echo '<i class="menu-icon fa fa-external-link"></i>';
        echo '<span class="menu-text">Quick Links</span>';
        echo '<b class="arrow glyphicon glyphicon-list"></b>';
        echo '</a>';
        echo '<b class="arrow"></b>';

        // Check for submenus and render them recursively
        if (!empty($item['submenus'])) {
            echo '<ul class="submenu">';
            renderMenuItems($item['submenus']);
            echo '</ul>'; // Closing submenu
        }

        echo '</li>'; // Closing main menu item
        // Fetch menu items from the permissions class
        $sidebarItems = Yii::$app->Permissions->getMenus();

// Function to render the menu items
function renderMenuItems($items)
{
    foreach ($items as $item) {
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
        <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
            <i id="sidebar-toggle-icon" class="ace-save-state ace-icon fa fa-angle-double-left"
                data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
        </div>
    </ul>

</div>
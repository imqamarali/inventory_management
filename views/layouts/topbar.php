<div class="main-container ace-save-state" id="main-container">
    <script type="text/javascript">
        try {
            ace.settings.loadState('main-container')
        } catch (e) {}
    </script>


    <style>
        #sidebar1 .nav-list>li>a {
            font-size: 10px;
            /* Smaller text */
            padding: 6px 10px;
            /* Smaller padding */
        }

        #sidebar1 .menu-icon {
            font-size: 14px;
            /* Smaller icon */
            font-weight: bolder;
        }

        #sidebar1 .submenu>li>a {
            font-size: 11px;
            padding: 5px 12px;
        }

        #sidebar1 .menu-text {
            margin-left: 5px;
            font-weight: bolder;
        }
    </style>

    <div id="sidebar1" class="sidebar h-sidebar navbar-collapse collapse ace-save-state">
        <ul class="nav nav-list">

            <?php
            $sidebarItems = Yii::$app->Permissions->getTopbar();

            function renderMenuItems1($items)
            {
                foreach ($items as $item) {
                    echo '<li>';
                    if ($item['link']) {
                        if ($item['can_view'] == '1') {
                            echo '<a href="index.php?r=' . $item['link'] . '" class="' . (!empty($item['submenus']) ? 'dropdown-toggle' : '') . '">';
                        } else {
                            echo '<a href="javascript:void(0)" class="' . (!empty($item['submenus']) ? 'dropdown-toggle' : '') . '">';
                        }
                    } else {

                        echo '<a href="' . $item['link'] . '" class="' . (!empty($item['submenus']) ? 'dropdown-toggle' : '') . '">';
                    }
                    echo '<i class="menu-icon ' . $item['icon'] . '"></i>';
                    echo '<span class="menu-text">' . $item['title'];
                    if ($item['can_view'] == '0') {
                        echo '<span title="" class="badge badge-transparent tooltip-error" data-original-title="Permissions Restricted">
                                <i class="ace-icon fa fa-lock red bigger-130"></i>
                            </span>';
                    }
                    // <i class="ace-icon fa fa-exclamation-triangle red bigger-130"></i>
                    echo '</span>';
                    echo !empty($item['submenus']) ? '<b class="arrow fa fa-angle-down"></b>' : '';
                    echo '</a>';
                    echo '<b class="arrow"></b>';

                    if (!empty($item['submenus'])) {
                        echo '<ul class="submenu">';
                        renderMenuItems($item['submenus']);
                        echo '</ul>';
                    }

                    echo '</li>';
                }
            }
            renderMenuItems1($sidebarItems);
            ?>
        </ul>
    </div>


</div>
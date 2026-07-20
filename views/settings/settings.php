<?php
use yii\helpers\Html;

if (!isset($modules)) {
    $modules = [];
}

// Dashboard color scheme
$navbarColor = '#0f4c29';
$accentColor = '#3498db';
?>
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li class="active">Settings</li>
                <li class="active">System Configurations</li>
            </ul>
        </div>
        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="widget-box" style="border-top: 4px solid; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        
                        <div class="widget-body">
                            <div class="widget-main padding-0">
                                <div class="row" style="margin: 0;">
                                    <!-- Left Menu -->
                                    <div class="col-sm-3" style="padding: 0; border-right: 1px solid #e8e8e8; min-height: 600px; width: 220px">
                                        <div class="settings-menu" style="border-radius: 0; margin: 0; padding: 0;">
                                            <ul class="nav nav-pills nav-stacked" id="settingsMenu" style="padding: 0; margin: 0;">
                                                <?php foreach ($modules as $index => $module):
                                                    $active = $index === 0 ? 'active' : '';
                                                ?>
                                                <li class="<?= $active ?>">
                                                    <a href="javascript:void(0)"
                                                       data-tab="<?= htmlspecialchars($module['controller']) ?>"
                                                       onclick="loadSettingsTab('<?= htmlspecialchars($module['controller']) ?>', this)"
                                                       style="border: none; border-left: 4px solid transparent; margin: 0; padding: 7px 6px; border-radius: 0; background: #fafafa;">
                                                        <i class="ace-icon <?= htmlspecialchars($module['icon']) ?>" style="width: 18px;"></i>
                                                        <span style="margin-left: 8px;"><?= htmlspecialchars($module['name']) ?></span>
                                                    </a>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Right Content -->
                                    <div class="col-sm-9">
                                        <div id="settingsContent" style="min-height: 500px;">
                                            <div class="text-center" style="padding: 60px 20px;">
                                                <i class="ace-icon fa fa-spinner fa-spin" style="font-size: 48px; color: <?= $navbarColor ?>;"></i>
                                                <p style="margin-top: 20px; color: #666; font-size: 14px;">Loading settings...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-menu {
    background: #fafafa;
    border-radius: 0;
    padding: 0;
    margin: 0;
}

.settings-menu-header {
    background-color: #0f4c29;
    color: white;
    padding: 15px;
    font-weight: 600;
}

.settings-menu .nav-pills > li > a {
    border-radius: 0;
    padding: 12px 15px;
    margin-bottom: 0;
    background: #fafafa;
    border: none;
    border-left: 4px solid transparent;
    color: #333;
    transition: all 0.3s ease;
    border-bottom: 1px solid #e8e8e8;
    font-weight: 500;
}

.settings-menu .nav-pills > li > a:hover {
    background: #f0f8ff;
    border-left-color: #0f4c29;
    color: #0f4c29;
}

.settings-menu .nav-pills > li.active > a {
    background: #e3f2fd;
    border-left-color: #0f4c29;
    color: #0f4c29;
    font-weight: 600;
}

.settings-menu .nav-pills > li > a i {
    margin-right: 8px;
    width: 18px;
    color: inherit;
}

.alert {
    border-radius: 4px;
    margin-bottom: 15px;
    border-left: 4px solid;
}

.alert-success {
    border-left-color: #2ecc71;
}

.alert-danger {
    border-left-color: #e74c3c;
}

.alert-warning {
    border-left-color: #f39c12;
}

.alert-info {
    border-left-color: #3498db;
}
</style>

<script>
function loadSettingsTab(controller, element) {
    // Update active menu item
    document.querySelectorAll('#settingsMenu li').forEach(li => {
        li.classList.remove('active');
    });
    element.closest('li').classList.add('active');

    // Load content
    const contentDiv = document.getElementById('settingsContent');
    contentDiv.innerHTML = '<div class="text-center" style="padding: 60px 20px;"><i class="ace-icon fa fa-spinner fa-spin" style="font-size: 48px; color: #0f4c29;"></i><p style="margin-top: 20px; color: #666;">Loading...</p></div>';

    fetch('index.php?r=' + controller, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(data => {
        // Extract scripts and HTML separately
        const temp = document.createElement('div');
        temp.innerHTML = data;

        // Get all script tags
        const scripts = temp.querySelectorAll('script');
        const scriptTexts = [];

        scripts.forEach(script => {
            scriptTexts.push(script.textContent);
            script.remove();
        });

        // Insert HTML without scripts
        contentDiv.innerHTML = temp.innerHTML;

        // Execute scripts in order
        scriptTexts.forEach(scriptText => {
            try {
                new Function(scriptText)();
            } catch (error) {
                console.error('Script execution error:', error);
            }
        });

        // Reinitialize Bootstrap tooltips
        if (typeof $ !== 'undefined' && $.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    })
    .catch(error => {
        contentDiv.innerHTML = '<div class="alert alert-danger" style="margin: 20px;"><i class="ace-icon fa fa-exclamation-triangle"></i> <strong>Error!</strong> Failed to load settings. Please try again.</div>';
        console.error('Error:', error);
    });
}

// Load first tab on page load
document.addEventListener('DOMContentLoaded', function() {
    const firstLink = document.querySelector('#settingsMenu li:first-child a');
    if (firstLink) {
        firstLink.click();
    }
});
</script>
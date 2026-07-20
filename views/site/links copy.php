<?php
use yii\helpers\Html;
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .module-actions {
        position: absolute;
        top: 5px;
        right: 10px;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .dd-item:hover .module-actions {
        opacity: 1;
    }

    .dd2-content {
        position: relative;
        padding-right: 80px;
    }

    .submenu-actions {
        float: right;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .submenu-item:hover .submenu-actions {
        opacity: 1;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Home</a>
                </li>
                <li class="active">Modules Management</li>
            </ul>
            <div class="nav-search" style="float: right;">
                <button type="button" class="btn btn-primary btn-sm" onclick="showAddModuleModal()">
                    <i class="fa fa-plus"></i> Add Module
                </button>
            </div>
        </div>

        <div class="page-content">
            <div class="widget-box" id="widget-box-1">
                <div class="widget-header widget-header-blue widget-header-flat">
                    <h4 class="widget-title lighter">
                        <i class="ace-icon fa fa-th-large"></i>
                        Modules & Features Management
                    </h4>
                    <div class="widget-toolbar">
                        <span class="badge badge-info"><?= count($modules) ?> Modules</span>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-sm-4">
                                <div class="dd dd-draghandle">
                                    <ol class="dd-list">
                                        <?php
                                        usort($modules, function ($a, $b) {
                                            return ($a['order_by'] ?? 0) <=> ($b['order_by'] ?? 0);
                                        });
                                        $third = ceil(count($modules) / 3);
                                        $leftModules = array_slice($modules, 0, $third);
                                        $middleModules = array_slice($modules, $third, $third);
                                        $rightModules = array_slice($modules, $third * 2);
                                        
                                        foreach ($leftModules as $module) {
                                            echo '
                                            <li class="dd-item dd2-item dd-collapsed" data-id="' . $module['id'] . '">
                                                <div class="dd-handle dd2-handle">
                                                    <i class="normal-icon ' . $module['icon'] . ' bigger-130"></i>
                                                    <i class="drag-icon ace-icon fa fa-arrows bigger-125"></i>
                                                </div>
                                                <div class="dd2-content">
                                                    ' . $module['name'] . '
                                                    <div class="module-actions">
                                                        <button type="button" class="btn btn-xs btn-success" onclick="showAddFeatureModal(' . $module['id'] . ', \'' . htmlspecialchars($module['name'], ENT_QUOTES) . '\')" title="Add Sub-Module">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-xs btn-danger" onclick="deleteModule(' . $module['id'] . ')" title="Delete Module">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>';

                                            if (!empty($module['submenus'])) {
                                                echo '<ol class="submenu-list" style="display: none;">';
                                                foreach ($module['submenus'] as $submenu) {
                                                    $featureId = $submenu['feature_id'] ?? 0;
                                                    echo '
                                                    <li class="submenu-item">
                                                        <a href="index.php?r=' . $submenu['link'] . '" class="submenu-link">
                                                            <i class="' . $submenu['icon'] . '"></i>
                                                            ' . $submenu['title'] . '
                                                        </a>
                                                        <span class="submenu-actions">
                                                            <button type="button" class="btn btn-xs btn-danger" onclick="deleteFeature(' . $featureId . ', event)" title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </span>
                                                    </li>';
                                                }
                                                echo '</ol>';
                                            }

                                            echo '</li>';
                                        }
                                        ?>
                                    </ol>
                                </div>
                            </div>

                            <!-- Middle Column -->
                            <div class="col-sm-4">
                                <div class="dd dd-draghandle">
                                    <ol class="dd-list">
                                        <?php
                                        foreach ($middleModules as $module) {
                                            echo '
                                            <li class="dd-item dd2-item dd-collapsed" data-id="' . $module['id'] . '">
                                                <div class="dd-handle dd2-handle">
                                                    <i class="normal-icon ' . $module['icon'] . ' bigger-130"></i>
                                                    <i class="drag-icon ace-icon fa fa-arrows bigger-125"></i>
                                                </div>
                                                <div class="dd2-content">
                                                    ' . $module['name'] . '
                                                    <div class="module-actions">
                                                        <button type="button" class="btn btn-xs btn-success" onclick="showAddFeatureModal(' . $module['id'] . ', \'' . htmlspecialchars($module['name'], ENT_QUOTES) . '\')" title="Add Sub-Module">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-xs btn-danger" onclick="deleteModule(' . $module['id'] . ')" title="Delete Module">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>';

                                            if (!empty($module['submenus'])) {
                                                echo '<ol class="submenu-list" style="display: none;">';
                                                foreach ($module['submenus'] as $submenu) {
                                                    $featureId = $submenu['feature_id'] ?? 0;
                                                    echo '
                                                    <li class="submenu-item">
                                                        <a href="index.php?r=' . $submenu['link'] . '" class="submenu-link">
                                                            <i class="' . $submenu['icon'] . '"></i>
                                                            ' . $submenu['title'] . '
                                                        </a>
                                                        <span class="submenu-actions">
                                                            <button type="button" class="btn btn-xs btn-danger" onclick="deleteFeature(' . $featureId . ', event)" title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </span>
                                                    </li>';
                                                }
                                                echo '</ol>';
                                            }

                                            echo '</li>';
                                        }
                                        ?>
                                    </ol>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-sm-4">
                                <div class="dd dd-draghandle">
                                    <ol class="dd-list">
                                        <?php
                                        foreach ($rightModules as $module) {
                                            echo '
                                            <li class="dd-item dd2-item dd-collapsed" data-id="' . $module['id'] . '">
                                                <div class="dd-handle dd2-handle">
                                                    <i class="normal-icon ' . $module['icon'] . ' bigger-130"></i>
                                                    <i class="drag-icon ace-icon fa fa-arrows bigger-125"></i>
                                                </div>
                                                <div class="dd2-content">
                                                    ' . $module['name'] . '
                                                    <div class="module-actions">
                                                        <button type="button" class="btn btn-xs btn-success" onclick="showAddFeatureModal(' . $module['id'] . ', \'' . htmlspecialchars($module['name'], ENT_QUOTES) . '\')" title="Add Sub-Module">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-xs btn-danger" onclick="deleteModule(' . $module['id'] . ')" title="Delete Module">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>';
                                            
                                            if (!empty($module['submenus'])) {
                                                echo '<ol class="submenu-list" style="display: none;">';
                                                foreach ($module['submenus'] as $submenu) {
                                                    $featureId = $submenu['feature_id'] ?? 0;
                                                    echo '
                                                    <li class="submenu-item">
                                                        <a href="index.php?r=' . $submenu['link'] . '" class="submenu-link">
                                                            <i class="' . $submenu['icon'] . '"></i>
                                                            ' . $submenu['title'] . '
                                                        </a>
                                                        <span class="submenu-actions">
                                                            <button type="button" class="btn btn-xs btn-danger" onclick="deleteFeature(' . $featureId . ', event)" title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </span>
                                                    </li>';
                                                }
                                                echo '</ol>';
                                            }

                                            echo '</li>';
                                        }
                                        ?>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Include JavaScript Libraries -->
            <script src="assets/js/jquery-2.1.4.min.js"></script>
            <script type="text/javascript">
                if ('ontouchstart' in document.documentElement) document.write(
                    "<script src='assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
            </script>
            <script src="assets/js/bootstrap.min.js"></script>
            <script src="assets/js/jquery.nestable.min.js"></script>
            <script src="assets/js/ace-elements.min.js"></script>
            <script src="assets/js/ace.min.js"></script>

            <script type="text/javascript">
                const SCHOOL_ID = <?= $schoolId ?>;
                const ALL_ROLES = <?= json_encode($roles) ?>;

                jQuery(function($) {
                    // Initialize the nestable plugin for all columns
                    $('.dd').nestable();

                    // Initially set all modules to collapsed
                    $('.dd-item').each(function() {
                        var $li = $(this);
                        $li.find('.submenu-list').hide();
                        $li.find('.expand-btn').show();
                        $li.find('.collapse-btn').hide();
                    });

                    // Toggle collapse/expand functionality
                    $('.expand-btn').on('click', function() {
                        var $li = $(this).closest('.dd-item');
                        $li.removeClass('dd-collapsed');
                        $li.find('.submenu-list').slideDown();
                        $li.find('.expand-btn').hide();
                        $li.find('.collapse-btn').show();
                    });

                    $('.collapse-btn').on('click', function() {
                        var $li = $(this).closest('.dd-item');
                        $li.addClass('dd-collapsed');
                        $li.find('.submenu-list').slideUp();
                        $li.find('.collapse-btn').hide();
                        $li.find('.expand-btn').show();
                    });

                    // Prevent links inside handles from being clicked during drag
                    $('.dd-handle a').on('mousedown', function(e) {
                        e.stopPropagation();
                    });

                    // Tooltip initialization
                    $('[data-rel="tooltip"]').tooltip();

                    // Event listener for when the order changes
                    $('.dd').on('change', function() {
                        var leftOrder = $('.col-sm-4 .dd').eq(0).nestable('serialize');
                        var middleOrder = $('.col-sm-4 .dd').eq(1).nestable('serialize');
                        var rightOrder = $('.col-sm-4 .dd').eq(2).nestable('serialize');
                        var order = leftOrder.concat(middleOrder, rightOrder);

                        $.ajax({
                            url: 'index.php?r=config/sortmodule',
                            method: 'POST',
                            data: {
                                order: order
                            },
                            success: function(response) {
                                console.log('Order updated successfully:', response);
                            },
                            error: function(xhr, status, error) {
                                console.error('Error updating order:', error);
                            }
                        });
                    });
                });

                /**
                 * Show Add Module Modal
                 */
                function showAddModuleModal() {
                    const rolesHtml = buildRolePermissionsHTML();

                    Swal.fire({
                        title: '<i class="fa fa-cube"></i> Add New Module',
                        html: `
                            <div style="text-align: left;">
                                <div class="form-group">
                                    <label>Module Name <span class="text-danger">*</span></label>
                                    <input type="text" id="module_name" class="form-control" placeholder="e.g., Library Management">
                                </div>
                                
                                <div class="form-group">
                                    <label>Icon Class</label>
                                    <input type="text" id="module_icon" class="form-control" placeholder="e.g., fa fa-book" value="fa fa-cube">
                                    <small class="text-muted">Font Awesome icon class</small>
                                </div>
                                
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea id="module_description" class="form-control" rows="2" placeholder="Brief description"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Link</label>
                                    <input type="text" id="module_link" class="form-control" placeholder="e.g., library/index">
                                    <small class="text-muted">Leave empty if module has sub-modules</small>
                                </div>
                                
                                <div class="form-group">
                                    <label>Display Order</label>
                                    <input type="number" id="module_order" class="form-control" value="99">
                                </div>

                                <h5 style="margin: 20px 0 15px 0; border-bottom: 2px solid #478FCA; padding-bottom: 8px; color: #478FCA;">
                                    <i class="fa fa-lock"></i> Role Permissions
                                </h5>
                                ${rolesHtml}
                            </div>
                        `,
                        width: '700px',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fa fa-save"></i> Create Module',
                        cancelButtonText: '<i class="fa fa-times"></i> Cancel',
                        confirmButtonColor: '#478FCA',
                        preConfirm: () => {
                            const name = $('#module_name').val().trim();

                            if (!name) {
                                Swal.showValidationMessage('Module name is required');
                                return false;
                            }

                            return {
                                action: 'add_module',
                                module_name: name,
                                module_icon: $('#module_icon').val().trim(),
                                module_description: $('#module_description').val().trim(),
                                module_link: $('#module_link').val().trim(),
                                module_order: $('#module_order').val(),
                                role_permissions: collectRolePermissions()
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            saveModule(result.value);
                        }
                    });
                }

                /**
                 * Show Add Feature Modal
                 */
                function showAddFeatureModal(moduleId, moduleName) {
                    const rolesHtml = buildRolePermissionsHTML();

                    Swal.fire({
                        title: '<i class="fa fa-puzzle-piece"></i> Add Sub-Module',
                        html: `
                            <div style="text-align: left;">
                                <div class="alert alert-info" style="font-size: 12px; margin-bottom: 15px;">
                                    <strong>Module:</strong> ${moduleName}
                                </div>
                                
                                <div class="form-group">
                                    <label>Feature Name <span class="text-danger">*</span></label>
                                    <input type="text" id="feature_name" class="form-control" placeholder="e.g., Issue Books">
                                </div>
                                
                                <div class="form-group">
                                    <label>Icon Class</label>
                                    <input type="text" id="feature_icon" class="form-control" placeholder="e.g., fa fa-circle-o" value="fa fa-circle-o">
                                </div>
                                
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea id="feature_description" class="form-control" rows="2" placeholder="Brief description"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Link <span class="text-danger">*</span></label>
                                    <input type="text" id="feature_link" class="form-control" placeholder="e.g., library/issue">
                                </div>
                                
                                <div class="form-group">
                                    <label>Display Order</label>
                                    <input type="number" id="feature_order" class="form-control" value="99">
                                </div>

                                <h5 style="margin: 20px 0 15px 0; border-bottom: 2px solid #87B87F; padding-bottom: 8px; color: #87B87F;">
                                    <i class="fa fa-lock"></i> Role Permissions
                                </h5>
                                ${rolesHtml}
                            </div>
                        `,
                        width: '700px',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fa fa-save"></i> Create Feature',
                        cancelButtonText: '<i class="fa fa-times"></i> Cancel',
                        confirmButtonColor: '#87B87F',
                        preConfirm: () => {
                            const name = $('#feature_name').val().trim();
                            const link = $('#feature_link').val().trim();

                            if (!name) {
                                Swal.showValidationMessage('Feature name is required');
                                return false;
                            }

                            if (!link) {
                                Swal.showValidationMessage('Link is required');
                                return false;
                            }

                            return {
                                action: 'add_feature',
                                module_id: moduleId,
                                feature_name: name,
                                feature_icon: $('#feature_icon').val().trim(),
                                feature_description: $('#feature_description').val().trim(),
                                feature_link: link,
                                feature_order: $('#feature_order').val(),
                                role_permissions: collectRolePermissions()
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            saveFeature(result.value);
                        }
                    });
                }

                /**
                 * Build Role Permissions HTML
                 */
                function buildRolePermissionsHTML() {
                    let html = '<div style="max-height: 300px; overflow-y: auto; padding: 10px; background: #f8f9fa; border-radius: 6px;">';

                    ALL_ROLES.forEach(role => {
                        html += `
                            <div style="background: white; padding: 15px; margin-bottom: 12px; border-radius: 6px; border-left: 4px solid #478FCA;">
                                <div style="margin-bottom: 10px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #333; display: flex; align-items: center;">
                                        <input type="checkbox" class="role-enable-checkbox" data-role-id="${role.id}" 
                                               onchange="toggleRolePermissions(this)" 
                                               style="margin-right: 10px; width: 18px; height: 18px; cursor: pointer; accent-color: #478FCA;">
                                        ${role.name}
                                    </label>
                                    ${role.description ? `<small style="color: #666; margin-left: 28px;">${role.description}</small>` : ''}
                                </div>
                                
                                <div class="role-permissions" data-role-id="${role.id}" style="margin-left: 28px; display: none;">
                                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                                        <label style="font-size: 12px; cursor: pointer;">
                                            <input type="checkbox" class="perm-checkbox" data-perm="view" data-role="${role.id}" style="margin-right: 5px;">
                                            <i class="fa fa-eye text-info"></i> View
                                        </label>
                                        <label style="font-size: 12px; cursor: pointer;">
                                            <input type="checkbox" class="perm-checkbox" data-perm="add" data-role="${role.id}" style="margin-right: 5px;">
                                            <i class="fa fa-plus text-success"></i> Add
                                        </label>
                                        <label style="font-size: 12px; cursor: pointer;">
                                            <input type="checkbox" class="perm-checkbox" data-perm="edit" data-role="${role.id}" style="margin-right: 5px;">
                                            <i class="fa fa-edit text-warning"></i> Edit
                                        </label>
                                        <label style="font-size: 12px; cursor: pointer;">
                                            <input type="checkbox" class="perm-checkbox" data-perm="delete" data-role="${role.id}" style="margin-right: 5px;">
                                            <i class="fa fa-trash text-danger"></i> Delete
                                        </label>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    html += '</div>';
                    return html;
                }

                /**
                 * Toggle role permissions visibility
                 */
                function toggleRolePermissions(checkbox) {
                    const roleId = $(checkbox).data('role-id');
                    const permDiv = $(`.role-permissions[data-role-id="${roleId}"]`);

                    if (checkbox.checked) {
                        permDiv.slideDown();
                        // Auto-check 'View' permission
                        permDiv.find(`.perm-checkbox[data-perm="view"]`).prop('checked', true);
                    } else {
                        permDiv.slideUp();
                        // Uncheck all permissions
                        permDiv.find('.perm-checkbox').prop('checked', false);
                    }
                }

                /**
                 * Collect role permissions from form
                 */
                function collectRolePermissions() {
                    const permissions = {};

                    $('.role-enable-checkbox:checked').each(function() {
                        const roleId = $(this).data('role-id');
                        permissions[roleId] = {
                            enabled: true,
                            can_view: $(`.perm-checkbox[data-role="${roleId}"][data-perm="view"]`).is(':checked') ? 1 : 0,
                            can_add: $(`.perm-checkbox[data-role="${roleId}"][data-perm="add"]`).is(':checked') ? 1 : 0,
                            can_edit: $(`.perm-checkbox[data-role="${roleId}"][data-perm="edit"]`).is(':checked') ? 1 : 0,
                            can_delete: $(`.perm-checkbox[data-role="${roleId}"][data-perm="delete"]`).is(':checked') ? 1 : 0
                        };
                    });

                    return permissions;
                }

                /**
                 * Save Module
                 */
                function saveModule(data) {
                    $.ajax({
                        url: 'index.php?r=site/links',
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to create module', 'error');
                        }
                    });
                }

                /**
                 * Save Feature
                 */
                function saveFeature(data) {
                    $.ajax({
                        url: 'index.php?r=site/links',
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to create feature', 'error');
                        }
                    });
                }

                /**
                 * Delete Module
                 */
                function deleteModule(moduleId) {
                    Swal.fire({
                        title: 'Delete Module?',
                        text: 'This will also delete all permissions for this module',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d15b47',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'index.php?r=site/links',
                                type: 'POST',
                                data: {
                                    action: 'delete_module',
                                    module_id: moduleId
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Deleted!',
                                            text: response.message,
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire('Error', response.message, 'error');
                                    }
                                }
                            });
                        }
                    });
                }

                /**
                 * Delete Feature
                 */
                function deleteFeature(featureId, event) {
                    event.preventDefault();
                    event.stopPropagation();

                    Swal.fire({
                        title: 'Delete Feature?',
                        text: 'This will also delete all permissions for this feature',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d15b47',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'index.php?r=site/links',
                                type: 'POST',
                                data: {
                                    action: 'delete_feature',
                                    feature_id: featureId
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Deleted!',
                                            text: response.message,
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire('Error', response.message, 'error');
                                    }
                                }
                            });
                        }
                    });
                }
            </script>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="row">
        <div class="col-xs-16">
            <div class="row">
                <div class="col-sm-3">
                    <div class="widget-box widget-color-blue2">
                        <div class="widget-header">
                            <h4 class="widget-title lighter smaller">
                                <?php echo htmlspecialchars($permissions['role_name']); ?>
                                <span class="smaller-80">(Dashboard Statistics)</span>
                            </h4>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main padding-8">
                                <ul id="tree2">
                                    <li>
                                        <i class="icon fa fa-folder"></i>
                                        <span class="folder">Dashboard Statistics</span>
                                        <ul>
                                            <?php foreach ($permissions['dashboard_permissions'] as $item): ?>
                                                <li>
                                                    <i class="icon fa fa-dashboard"></i>
                                                    <span
                                                        class="folder"><?php echo htmlspecialchars($item['label']); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-9">
                    <div class="widget-box widget-color-green2">
                        <div class="widget-header">
                            <h4 class="widget-title lighter smaller">
                                Dashboard Permissions
                                <span class="smaller-80">(Manage visibility)</span>
                            </h4>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main padding-8">
                                <div class="dashboard-permissions-grid"
                                    style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                                    <?php foreach ($permissions['dashboard_permissions'] as $item): ?>
                                        <div class="dashboard-permission-item"
                                            style="background: #fff; border: 1px solid #E3E9ED; border-radius: 4px; padding: 15px; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s;">
                                            <div style="margin-bottom: 10px;">
                                                <div
                                                    style="font-weight: 600; font-size: 13px; color: #333; margin-bottom: 5px;">
                                                    <?php echo htmlspecialchars($item['label']); ?>
                                                </div>
                                            </div>
                                            <div style="text-align: center;">
                                                <label
                                                    class="btn btn-sm btn-white btn-info <?php echo ($item['is_visible'] === 1) ? 'active' : ''; ?>"
                                                    style="width: 100%;">
                                                    <input type="checkbox" class="permission-checkbox"
                                                        data-stat-type="<?php echo htmlspecialchars($item['stat_type']); ?>"
                                                        name="permissions[<?php echo htmlspecialchars($item['stat_type']); ?>][is_visible]"
                                                        <?php echo ($item['is_visible'] === 1) ? 'checked' : ''; ?>
                                                        onchange="handleCheckboxChange(this, '<?php echo htmlspecialchars($item['permission_id'] ?? ''); ?>', '<?php echo htmlspecialchars($item['stat_type']); ?>');" />
                                                    <i
                                                        class="icon-only ace-icon fa fa-<?php echo ($item['is_visible'] === 1) ? 'eye' : 'eye-slash'; ?> bigger-110"></i>
                                                    <span style="margin-left: 5px;">
                                                        <?php echo ($item['is_visible'] === 1) ? 'Visible' : 'Hidden'; ?>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.page-content -->

<script type="text/javascript">
    if ("ontouchstart" in document.documentElement)
        document.write(
            "<script src='assets/js/jquery.mobile.custom.min.js'>" +
            "<" +
            "/script>"
        );
</script>
<script src="assets/js/bootstrap.min.js"></script>

<!-- page specific plugin scripts -->
<script src="acsets/js/tree.min.js"></script>

<!-- ace scripts -->
<script src="assets/js/ace-elements.min.js"></script>
<script src="assets/js/ace.min.js"></script>
<style>
    .dashboard-permission-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-color: #87B87F;
    }

    @media (max-width: 991px) {
        .dashboard-permissions-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    @media (max-width: 767px) {
        .dashboard-permissions-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>
<script>
    function handleCheckboxChange(checkbox, permission_id, stat_type) {
        var isChecked = checkbox.checked;
        var data = {
            permission_id: permission_id,
            stat_type: stat_type,
            is_visible: isChecked ? 1 : 0,
            role: <?php echo (int)($_REQUEST['id'] ?? 0) ?>,
            _csrf: $('meta[name="csrf-token"]').attr('content')
        };

        // Update icon and text immediately
        var label = $(checkbox).closest('label');
        var icon = label.find('i.fa');
        var span = label.find('span');

        if (isChecked) {
            label.addClass('active');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
            span.text('Visible');
        } else {
            label.removeClass('active');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
            span.text('Hidden');
        }

        $.ajax({
            url: 'index.php?r=modules/update_dashboard',
            type: 'POST',
            data: data,
            success: function(response) {
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error(error);
                // Revert on error - toggle back
                checkbox.checked = !checkbox.checked;
                // Update UI based on reverted state
                var label = $(checkbox).closest('label');
                var icon = label.find('i.fa');
                var span = label.find('span');

                if (checkbox.checked) {
                    label.addClass('active');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    span.text('Visible');
                } else {
                    label.removeClass('active');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    span.text('Hidden');
                }
            }
        });
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.permission-checkbox').on('change', function() {
            $(this).closest('form').submit();
        });
    });
</script>

<script src="assets/js/tree.min.js"></script>

<script type="text/javascript">
    function extractTreeData(ulElement) {
        const data = {};
        $(ulElement).children('li').each(function() {
            const $this = $(this);
            const folderText = $this.children('span.folder').text();
            const icon = $this.children('i.icon').text();

            if (folderText) {
                const itemClass = 'tree-branch tree-selected';
                data[folderText] = {
                    text: folderText,
                    type: 'folder',
                    'icon-class': icon || 'fa fa-refresh',
                    class: itemClass,
                    additionalParameters: {
                        children: extractTreeData($this.children('ul'))
                    }
                };
            } else {
                const itemText = $this.text().trim();
                if (itemText) {
                    data[itemText] = {
                        text: itemText,
                        type: 'item',
                        'icon-class': 'item',
                    };
                }
            }
        });
        return data;
    }

    jQuery(function($) {
        const treeData = extractTreeData($('#tree2'));
        const sampleData = {
            dataSource2: function(options, callback) {
                let $data = null;
                if (!("text" in options) && !("type" in options)) {
                    $data = treeData;
                    callback({
                        data: $data
                    });
                    return;
                } else if ("type" in options && options.type == "folder") {
                    $data = options.additionalParameters?.children || {};
                }

                if ($data != null) {
                    setTimeout(function() {
                        callback({
                            data: $data
                        });
                    }, parseInt(Math.random() * 500) + 200);
                }
            }
        };

        $("#tree2").ace_tree({
            dataSource: sampleData["dataSource2"],
            loadingHTML: '<div class="tree-loading"><i class="ace-icon fa fa-refresh fa-spin blue"></i></div>',
            "open-icon": "ace-icon fa fa-folder-open",
            "close-icon": "ace-icon fa fa-folder",
            itemSelect: true,
            folderSelect: true,
            multiSelect: true,
            "selected-icon": null,
            "unselected-icon": null,
            "folder-open-icon": "ace-icon tree-plus",
            "folder-close-icon": "ace-icon tree-minus",
        });
    });
</script>

<script>
    // Function to hide messages after 6 seconds
    setTimeout(function() {
        var successMessage = document.getElementById('success-message');
        var errorMessage = document.getElementById('error-message');

        if (successMessage) {
            successMessage.style.transition = "opacity 0.5s ease";
            successMessage.style.opacity = 0;
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 500);
        }

        if (errorMessage) {
            errorMessage.style.transition = "opacity 0.5s ease";
            errorMessage.style.opacity = 0;
            setTimeout(function() {
                errorMessage.style.display = 'none';
            }, 500);
        }
    }, 6000);
</script>
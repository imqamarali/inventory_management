<div class="page-content">
    <div class="row">
        <div class="col-xs-16">
            <div class="row">
                <div class="col-sm-3">
                    <div class="widget-box widget-color-blue2">
                        <div class="widget-header">
                            <h4 class="widget-title lighter smaller">
                                <?php echo htmlspecialchars($school_permissions['role_name']); ?>
                                <span class="smaller-80">(Settings)</span>
                            </h4>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main padding-8">
                                <ul id="settings-tree">
                                    <?php foreach ($school_permissions['school_permissions'] as $setting): ?>
                                    <li>
                                        <i class="icon fa fa-cog"></i>
                                        <span class="folder"><?php echo htmlspecialchars($setting['title']); ?></span>
                                        <ul>
                                            <li>
                                                <i class="icon fa fa-pencil-square-o"></i>
                                                <span class="folder">Permissions</span>
                                                <ul>
                                                    <?php foreach (['can_view' => 'View', 'can_edit' => 'Edit', 'can_delete' => 'Delete'] as $type => $label): ?>
                                                    <li>
                                                        <input type="checkbox" class="permission-checkbox"
                                                            data-setting="<?php echo htmlspecialchars($setting['school_id']); ?>"
                                                            data-type="<?php echo $type; ?>"
                                                            <?php echo $setting[$type] === 1 ? 'checked' : ''; ?>
                                                            onchange="handlePermissionChange('<?php echo $type; ?>', this, '<?php echo htmlspecialchars($setting['school_id']); ?>')">
                                                        <?php echo htmlspecialchars($label); ?>
                                                    </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-9">
                    <div class="widget-box widget-color-green2">
                        <div class="widget-header">
                            <h4 class="widget-title lighter smaller">
                                Update <?php echo htmlspecialchars($school_permissions['role_name']); ?> Settings
                                <span class="smaller-80">(Manage permissions)</span>
                            </h4>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main padding-2">
                                <table id="permissions-table" class="table table-striped table-bordered table-hover">
                                    <thead class="thin-border-bottom">
                                        <tr>
                                            <th><i class="ace-icon fa fa-file"></i> Setting</th>
                                            <th>Active</th>
                                            <th>Permissions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($school_permissions['school_permissions'] as $setting): ?>
                                        <tr id="<?= htmlspecialchars($setting['school_id']); ?>">
                                            <td><?php echo htmlspecialchars($setting['title']); ?></td>
                                            <td>
                                                <label
                                                    class="btn btn-sm btn-white btn-info <?php echo ($setting['active'] === 1) ? 'active' : ''; ?>">
                                                    <input type="checkbox" class="permission-checkbox"
                                                        data-setting="<?php echo htmlspecialchars($setting['school_id']); ?>"
                                                        name="active"
                                                        <?php echo ($setting['active'] === 1) ? 'checked' : ''; ?>
                                                        onchange="handlePermissionChange('active', this, '<?php echo htmlspecialchars($setting['school_id']); ?>');" />
                                                    <i class="icon-only ace-icon fa fa-check-square-o bigger-110"></i>
                                                </label>
                                            </td>
                                            <td>
                                                <?php foreach (['can_add' => 'View', 'can_view' => 'View', 'can_edit' => 'Edit', 'can_delete' => 'Delete'] as $type => $label): ?>
                                                <label
                                                    class="btn btn-sm btn-white btn-info <?php echo ($setting[$type] === 1) ? 'active' : ''; ?>">
                                                    <input type="checkbox" class="permission-checkbox"
                                                        data-setting="<?php echo htmlspecialchars($setting['school_id']); ?>"
                                                        data-type="<?php echo $type; ?>"
                                                        <?php echo ($setting[$type] === 1) ? 'checked' : ''; ?>
                                                        onchange="handlePermissionChange('<?php echo $type; ?>', this, '<?php echo htmlspecialchars($setting['school_id']); ?>');" />

                                                    <i class="icon-only ace-icon fa fa-<?php
                                                                                                echo ($type === 'can_add' ? 'plus' : ($type === 'can_view' ? 'eye' : ($type === 'can_edit' ? 'pencil' : 'trash')));
                                                                                                ?> bigger-110"></i>
                                                </label>
                                                <?php endforeach; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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
<script src="assets/js/tree.min.js"></script>

<!-- ace scripts -->
<script src="assets/js/ace-elements.min.js"></script>
<script src="assets/js/ace.min.js"></script>
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
    const treeData = extractTreeData($('#settings-tree')); // Update the ID
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

    // Initialize ace_tree
    $("#settings-tree").ace_tree({ // Ensure you're targeting the correct tree element
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
function handlePermissionChange(permissionType, checkbox, settingId) {
    var isChecked = checkbox.checked;
    var data = {
        type: permissionType,
        status: isChecked ? 1 : 0, // Send 1 if checked, otherwise 0
        school_id: settingId,
        role_id: <?php echo json_encode($school_permissions['role_id']); ?>,
        _csrf: $('meta[name="csrf-token"]').attr('content') // Ensure the CSRF token is sent
    };

    console.log(data); // Debugging output

    $.ajax({
        url: 'index.php?r=modules/updateschoolpermission', // Adjust URL to match your controller action
        type: 'POST',
        data: data,
        success: function(response) {
            console.log(response);
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}

// Function to hide messages after 6 seconds
setTimeout(function() {
    var successMessage = document.getElementById('success-message');
    var errorMessage = document.getElementById('error-message');

    if (successMessage) {
        successMessage.style.transition = "opacity 0.5s ease";
        successMessage.style.opacity = 0; // Fade out effect
        setTimeout(function() {
            successMessage.style.display = 'none'; // Remove it from the layout
        }, 500); // Wait for fade out to complete
    }

    if (errorMessage) {
        errorMessage.style.transition = "opacity 0.5s ease";
        errorMessage.style.opacity = 0; // Fade out effect
        setTimeout(function() {
            errorMessage.style.display = 'none'; // Remove it from the layout
        }, 500); // Wait for fade out to complete
    }
}, 6000); // 6 seconds
</script>
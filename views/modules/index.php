<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Home</a>
                </li>
                <li class="active">System Modules</li>
            </ul><!-- /.breadcrumb -->
        </div>
        <div class="page-content">
            <div class="row">
                <div class="col-xs-16">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="widget-box widget-color-blue2">
                                <div class="widget-header">
                                    <h4 class="widget-title lighter smaller">
                                        <?php echo htmlspecialchars($permissions['role_name'] ?? ''); ?>
                                        <span class="smaller-80">(Modules and Features)</span>
                                    </h4>

                                </div>
                                <div class="widget-body"><?php if (count($permissions) > 0) { ?>

                                    <div class="widget-main padding-8">
                                        <ul id="tree2">

                                            <?php foreach ($permissions['modules'] as $item): ?>
                                            <li>
                                                <i class="icon"><?php echo htmlspecialchars($item['icon']); ?></i>
                                                <span
                                                    class="folder"><?php echo htmlspecialchars($item['title']); ?></span>
                                                <ul>
                                                    <?php foreach ($item['submenus'] as $feature): ?>
                                                    <li>
                                                        <i class="icon fa fa-pencil-square-o"></i>
                                                        <span
                                                            class="folder"><?php echo htmlspecialchars($feature['title']); ?></span>
                                                        <ul>

                                                            <?php foreach (['can_add' => 'Add', 'can_view' => 'View', 'can_edit' => 'Update', 'can_delete' => 'Delete'] as $type => $label): ?>
                                                            <div data-toggle="buttons"
                                                                class="btn-group btn-overlap btn-corner">
                                                                <li>
                                                                    <input type="checkbox" class="permission-checkbox"
                                                                        data-module="<?php echo htmlspecialchars($item['module_id']); ?>"
                                                                        data-feature="<?php echo htmlspecialchars($feature['feature_id']); ?>"
                                                                        data-type="<?php echo $type; ?>"
                                                                        <?php echo $feature[$type] === 1 ? 'checked' : ''; ?>
                                                                        onchange="handleCheckboxChange('<?php echo $type; ?>', this, '<?php echo htmlspecialchars($item['module_id']); ?>', '<?php echo htmlspecialchars($feature['feature_id']); ?>', 2)">
                                                                    <?php echo htmlspecialchars($label); ?>
                                                                </li>
                                                            </div>
                                                            <?php endforeach; ?>


                                                        </ul>
                                                    </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </li>
                                            <?php endforeach; ?>

                                        </ul>
                                    </div><?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-9">
                            <div class="widget-box widget-color-green2">
                                <div class="widget-header">
                                    <h4 class="widget-title lighter smaller">
                                        Add/Update Role
                                        <span class="smaller-80">(Manage permissions)</span>
                                    </h4>
                                    <div class="widget-toolbar">
                                        <a href="index.php?r=modules/">
                                            <i class="ace-icon fa fa-refresh" style="color: white;"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-2">
                                        <div class="widget-body">
                                            <div class="widget-main no-padding">
                                                <?php if (count($permissions) > 0) { ?>
                                                <table id="permissions-table"
                                                    class="table table-striped table-bordered table-hover">
                                                    <thead class="thin-border-bottom">
                                                        <tr>
                                                            <th><i class="ace-icon fa fa-file"></i> Module</th>
                                                            <th> Active</th>
                                                            <th><i>@</i> Feature</th>
                                                            <th class="hidden-480">Permissions
                                                                <span class="smaller-80">(Active, Add, View, Update,
                                                                    Delete)</span>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        <?php foreach ($permissions['modules'] as $module): ?>
                                                        <?php $features = $module['submenus']; ?>
                                                        <?php foreach ($features as $index => $feature): ?>
                                                        <tr id="<?= htmlspecialchars($module['module_id']); ?>">
                                                            <?php if ($index === 0): ?>
                                                            <td rowspan="<?php echo count($features); ?>">
                                                                <?php echo htmlspecialchars($module['title']); ?>
                                                            </td>
                                                            <td rowspan="<?php echo count($features); ?>">
                                                                <div data-toggle="buttons"
                                                                    class="btn-group btn-overlap btn-corner">
                                                                    <label
                                                                        class="btn btn-sm btn-white btn-info <?php echo ($module['is_active'] === 1) ? 'active' : ''; ?>">
                                                                        <input type="checkbox"
                                                                            class="permission-checkbox"
                                                                            data-module="<?php echo htmlspecialchars($module['module_id']); ?>"
                                                                            name="active"
                                                                            <?php echo ($module['is_active'] === 1) ? 'checked' : ''; ?>
                                                                            onchange="handleCheckboxChange('active', this, '<?php echo htmlspecialchars($module['module_id']); ?>', '', 1);" />
                                                                        <i
                                                                            class="icon-only ace-icon fa fa-check-square-o bigger-110"></i>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <?php endif; ?>

                                                            <td>
                                                                <?php echo htmlspecialchars($feature['title']); ?>
                                                            </td>
                                                            <td>
                                                                <div data-toggle="buttons"
                                                                    class="btn-group btn-overlap btn-corner">
                                                                    <?php foreach (['can_add' => 'Add', 'can_view' => 'View', 'can_edit' => 'Update', 'can_delete' => 'Delete'] as $type => $label): ?>
                                                                    <label
                                                                        class="btn btn-sm btn-white btn-info <?php echo ($feature[$type] === 1) ? 'active' : ''; ?>">
                                                                        <input type="checkbox"
                                                                            class="permission-checkbox"
                                                                            data-module="<?php echo htmlspecialchars($module['module_id']); ?>"
                                                                            data-feature="<?php echo htmlspecialchars($feature['feature_id']); ?>"
                                                                            name="permissions[<?php echo $module['module_id']; ?>][<?php echo $feature['feature_id']; ?>][<?php echo $type; ?>]"
                                                                            <?php echo ($feature[$type] === 1) ? 'checked' : ''; ?>
                                                                            onchange="handleCheckboxChange('<?php echo $type; ?>', this, '<?php echo htmlspecialchars($module['module_id']); ?>', '<?php echo htmlspecialchars($feature['permission_id']); ?>', 2);" />
                                                                        <i
                                                                            class="icon-only ace-icon fa fa-<?php echo ($type === 'is_active') ? 'check-square-o' : ($type === 'can_add' ? 'plus' : ($type === 'can_view' ? 'eye' : ($type === 'can_edit' ? 'refresh' : 'trash'))); ?> bigger-110"></i>
                                                                    </label>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                        <?php endforeach; ?>

                                                    </tbody>
                                                </table><?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.page-content -->
    </div>
</div>


<script src="assets/js/bootstrap.min.js"></script>

<!-- page specific plugin scripts -->
<script src="assets/js/tree.min.js"></script>

<!-- ace scripts -->
<script src="assets/js/ace-elements.min.js"></script>
<script src="assets/js/ace.min.js"></script>
<script>
function extractTreeData(ulElement) {
    const data = [];
    $(ulElement).children('li').each(function() {
        const $this = $(this);
        const folderText = $this.children('span.folder').text().trim();
        const icon = $this.children('i.icon').text();

        if (folderText) {
            const childrenData = extractTreeData($this.children('ul'));
            data.push({
                text: folderText,
                type: 'folder',
                'icon-class': icon || 'fa fa-refresh',
                additionalParameters: {
                    children: childrenData.length ? childrenData : {}
                }
            });
        } else {
            const itemText = $this.text().trim();
            if (itemText) {
                data.push({
                    text: itemText,
                    type: 'item',
                    'icon-class': 'item',
                });
            }
        }
    });
    return data;
}
// jQuery to handle tree rendering and click event
jQuery(function($) {
    const treeData = extractTreeData($('#tree2')); // Extract tree data
    const sampleData = {
        dataSource2: function(options, callback) {
            let $data = null;
            if (!("text" in options) && !("type" in options)) {
                $data = treeData;
                callback({
                    data: $data
                });
                return;
            } else if ("type" in options && options.type === "folder") {
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

    // Initialize the tree and add the on click (selected.fu.tree) event handler
    $("#tree2").ace_tree({
        dataSource: sampleData["dataSource2"],
        loadingHTML: '<div class="tree-loading"><i class="ace-icon fa fa-refresh fa-spin blue"></i></div>',
        "open-icon": "ace-icon fa fa-folder-open",
        "close-icon": "ace-icon fa fa-folder",
        itemSelect: true,
        folderSelect: true,
        multiSelect: false,
        "selected-icon": null,
        "unselected-icon": null,
        "folder-open-icon": "ace-icon tree-plus",
        "folder-close-icon": "ace-icon tree-minus",
    }).on('selected.fu.tree', function(event, data) {
        if (data && data.selected && data.selected.length > 0) {
            const selectedNode = data.selected[0];
            const selectedModuleTitle = selectedNode.text; // Get the folder text

            console.log("Selected Module:", selectedModuleTitle); // Debugging

            // Call the function to update the table
            renderModuleInTable(selectedModuleTitle);
        }
    });
});


function renderModuleInTable(selectedModuleTitle) {
    const tableBody = $('#permissions-table tbody');
    tableBody.empty();

    var permissions = <?= json_encode($permissions['modules'] ?? null) ?? [] ?>;
    const module = permissions.find(mod => mod.title.trim() === selectedModuleTitle);

    if (module) {
        const features = module.submenus;

        features.forEach((feature, index) => {
            const row = $('<tr>', {
                id: module.module_id
            }); // Create a new table row with the module ID

            // Append the module title and active status only for the first feature
            if (index === 0) {
                row.append(`<td rowspan="${features.length}">${module.title}</td>`);
                row.append(`<td rowspan="${features.length}">
                    <div data-toggle="buttons" class="btn-group btn-overlap btn-corner">
                        <label class="btn btn-sm btn-white btn-info ${module.is_active === 1 ? 'active' : ''}">
                            <input type="checkbox" class="permission-checkbox" 
                                data-module="${module.module_id}" 
                                name="active" 
                                ${module.is_active === 1 ? 'checked' : ''} 
                                onchange="handleCheckboxChange('active', this, '${module.module_id}', '', 1);" />
                            <i class="icon-only ace-icon fa fa-check-square-o bigger-110"></i>
                        </label>
                    </div>
                </td>`);
            }

            // Append feature title and permissions
            row.append(`<td>${feature.title}</td>`);

            let permissionsColumn = '<td> <div data-toggle="buttons" class="btn-group btn-overlap btn-corner">';
            // Loop through permissions and add checkboxes
            ['can_add', 'can_view', 'can_edit', 'can_delete'].forEach(type => {
                permissionsColumn += `
                    <label class="btn btn-sm btn-white btn-info ${feature[type] === 1 ? 'active' : ''}">
                        <input type="checkbox" class="permission-checkbox" 
                            data-module="${module.module_id}" 
                            data-feature="${feature.feature_id}" 
                            name="permissions[${module.module_id}][${feature.feature_id}][${type}]" 
                            ${feature[type] === 1 ? 'checked' : ''} 
                            onchange="handleCheckboxChange('${type}', this, '${module.module_id}', '${feature.feature_id}', 2);" />
                        <i class="icon-only ace-icon fa fa-${type === 'can_add' ? 'plus' : type === 'can_view' ? 'eye' : type === 'can_edit' ? 'refresh' : 'trash'} bigger-110"></i>
                    </label>`;
            });
            permissionsColumn += '</div></td>';

            row.append(permissionsColumn); // Append the permissions column to the row
            tableBody.append(row); // Add the row to the table body
        });
    } else {
        console.error('No matching module found for:', selectedModuleTitle);
    }
}
</script>
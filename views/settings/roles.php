<?php

if (!isset($roles)) {
    $roles = [];
}

?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=settings/settings">Home</a>
                </li>
                <li class="active">Roles & Permissions</li>
                <div class="nav-search" id="nav-search">
                    <div class="exam-quick-actions-group">
                        <a class="btn btn-sm btn-white btn-primary"
                            style="font-size: 12px; cursor:pointer;"
                            onclick="openRoleModal()">
                            <i class="ace-icon fa fa-plus"></i>
                            Add New Role
                        </a>
                    </div>
                </div>
            </ul>
        </div>

        <div class="page-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="widget-body">
                            <div class="widget-main padding-12">
                                <?php if (count($roles) == 0) { ?>
                                    <div class="alert alert-info text-center">
                                        <i class="ace-icon fa fa-info-circle fa-3x" style="color: #6FB3E0;"></i>
                                        <h4 style="margin-top: 15px;">No Roles Found</h4>
                                        <p>Start by adding your first role using the button above</p>
                                    </div>
                                <?php } else { ?>
                                    <div class="row" id="roles_container">
                                        <?php foreach ($roles as $key => $item):
                                            $userCount = $item['user_count'] ?? 0;
                                            $statusClass = $userCount > 0 ? 'label-warning' : 'label-success';
                                        ?>
                                            <div class="col-md-5 col-sm-6 role-item">
                                                <div class="class-card">
                                                    <div class="class-header">
                                                        <div style="flex: 1;">
                                                            <div class="class-name">
                                                                <i class="fa fa-shield" style="margin-right: 8px;"></i>
                                                                <?php echo htmlspecialchars($item['name']); ?>
                                                            </div>
                                                        </div>

                                                        <div class="btn-group">
                                                            <button type="button"
                                                                onclick="openRoleModal(<?php echo htmlspecialchars(json_encode($item)); ?>)"
                                                                title="Edit Role">
                                                                <i class="ace-icon fa fa-pencil"></i>
                                                            </button>
                                                            &nbsp;&nbsp;|&nbsp;&nbsp;
                                                            <button type="button"
                                                                onclick="<?php echo $userCount > 0 ? 'alert(\'Cannot delete: Users assigned to this role\')' : 'deleteRole(' . $item['id'] . ')'; ?>"
                                                                title="<?php echo $userCount > 0 ? 'Cannot delete: Users assigned' : 'Delete Role'; ?>"
                                                                <?php echo $userCount > 0 ? 'disabled' : ''; ?>>
                                                                <i class="ace-icon fa fa-trash"></i>
                                                            </button>
                                                        </div>

                                                    </div>

                                                    <div class="class-stats">

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-users"></i>
                                                            <span>Users Assigned: <?php echo $userCount; ?></span>
                                                        </div>

                                                        <div class="stat-item">
                                                            <i class="ace-icon fa fa-status"></i>
                                                            <span>Status:
                                                                <?php if ($userCount > 0): ?>
                                                                    <span class="label label-warning">In Use</span>
                                                                <?php else: ?>
                                                                    <span class="label label-success">Available</span>
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>

                                        <?php endforeach; ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>



<?php

use yii\helpers\Html;

/** @var array $controllersInfo */
/** @var array|null $selectedController */
/** @var string|null $selectedControllerId */
/** @var array $selectedTableStats */

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .super-system-page .page-content {
        padding: 10px 15px !important;
    }

    .super-summary-row {
        margin-bottom: 15px;
    }

    .super-summary-card {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        border-top: 3px solid #438EB9;
        margin-bottom: 15px;
        transition: all 0.2s ease-in-out;
    }

    .super-summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
    }

    .super-summary-header {
        padding: 10px 15px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 14px;
        color: #2d3748;
    }

    .super-summary-header i {
        color: #438EB9;
    }

    .super-summary-body {
        padding: 10px 15px 12px;
        font-size: 12px;
    }

    .super-summary-table {
        width: 100%;
        margin: 0;
        font-size: 12px;
    }

    .super-summary-table th {
        width: 38%;
        font-weight: 600;
        color: #6c757d;
        padding: 4px 4px 4px 0;
        border: none;
        text-align: left;
        white-space: nowrap;
    }

    .super-summary-table td {
        padding: 4px 0;
        border: none;
        color: #2d3748;
    }

    .super-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
        margin-top: 5px;
    }

    .super-controllers-widget .btn-group .btn {
        margin: 2px 3px;
        font-size: 11px;
    }

    .super-controllers-widget .btn.active {
        border-color: #438EB9;
        box-shadow: 0 0 0 1px rgba(67, 142, 185, 0.15);
    }

    @media (max-width: 767px) {
        .super-summary-row {
            margin-bottom: 10px;
        }
    }
</style>

<div class="main-content super-system-page">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=site/index">Home</a>
                </li>
                <li class="active">Controllers Database Overview</li>
            </ul>
        </div>

        <div class="page-content">
            <?php if (!empty($controllersInfo)): ?>
                <div class="row super-summary-row">
                    <div class="col-xs-12">
                        <div class="widget-box super-controllers-widget" style="margin-bottom: 10px;">
                            <div class="widget-header widget-header-small widget-header widget-header-small-flat">
                                <h4 class="widget-title">
                                    <i class="ace-icon fa fa-bolt orange"></i>
                                    Controllers Quick View
                                </h4>
                            </div>
                            <div class="widget-body">
                                <div class="widget-main padding-8">
                                    <div class="btn-group">
                                        <?php foreach ($controllersInfo as $ctrl): ?>
                                            <?php
                                            $isActive = ($selectedControllerId === $ctrl['id']);
                                            $btnClass = $isActive
                                                ? 'btn btn-sm btn-white btn-primary active'
                                                : 'btn btn-sm btn-white btn-info';
                                            ?>
                                            <button type="button" class="<?= Html::encode($btnClass) ?> super-controller-btn"
                                                data-controller="<?= Html::encode($ctrl['id']) ?>">
                                                <i class="ace-icon fa fa-cube"></i>
                                                <?= Html::encode($ctrl['title']) ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($selectedController)): ?>
                <div class="row super-summary-row">
                    <div class="col-xs-12">
                        <div class="super-summary-card">
                            <div class="super-summary-header">
                                <div style="flex: 1; display: flex; align-items: center; gap: 8px;">
                                    <i class="ace-icon fa fa-code"></i>
                                    <span>
                                        Controller:
                                        <span id="super-current-controller-title">
                                            <?= Html::encode($selectedController['title'] ?? $selectedControllerId) ?>
                                        </span>
                                    </span>
                                </div>
                                <div class="btn-group btn-group-xs super-mode-toggle">
                                    <button type="button" class="btn btn-primary active" data-mode="db">
                                        Database Overview
                                    </button>
                                    <button type="button" class="btn btn-white" data-mode="test">
                                        Test Run
                                    </button>
                                </div>
                            </div>
                            <div class="super-summary-body">
                                <div id="super-db-overview">
                                    <div id="super-tables-container">
                                        <table class="table table-striped table-bordered table-hover"
                                            style="font-size: 12px; margin-bottom: 5px;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40px;">#</th>
                                                    <th style="width: 260px;">Table</th>
                                                    <th style="width: 140px;">Total Records</th>
                                                    <th>Last Record Datetime &amp; Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="super-tables-body">
                                                <tr>
                                                    <td colspan="4">
                                                        <span class="text-muted">Select a controller above to load
                                                            tables.</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div id="super-test-run" style="display: none; margin-top: 10px;">
                                    <table class="table table-striped table-bordered table-hover"
                                        style="font-size: 12px; margin-bottom: 5px;">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th style="width: 220px;">Action</th>
                                                <th>Route</th>
                                                <th style="width: 140px;">Result</th>
                                                <th style="width: 80px;">Run</th>
                                            </tr>
                                        </thead>
                                        <tbody id="super-actions-body">
                                            <tr>
                                                <td colspan="5">
                                                    <span class="text-muted">Click "Test Run" to load actions for this
                                                        controller.</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <p style="font-size: 11px; color: #777; margin: 4px 0 0;">
                                    <i class="fa fa-info-circle"></i>
                                    Tables and stats are detected by scanning controller PHP files for SQL keywords
                                    (SELECT / INSERT / UPDATE / DELETE). Last record time is based on common datetime
                                    columns such as <code>created_at</code> or <code>created_on</code> where available.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div><!-- /.page-content -->
    </div>
</div><!-- /.main-content -->

<script>
    (function($) {
        function showToastMessage(message) {
            if (typeof showToast === 'function') {
                showToast(message);
            } else {
                alert(message);
            }
        }

        function escapeHtml(text) {
            return $('<div>').text(text == null ? '' : text).html();
        }

        var superCurrentControllerId = null;

        function loadControllerTables(controllerId) {
            if (!controllerId) return;

            superCurrentControllerId = controllerId;

            // Update active button
            $('.super-controller-btn').removeClass('btn-primary active').addClass('btn-info');
            $('.super-controller-btn[data-controller="' + controllerId + '"]')
                .removeClass('btn-info')
                .addClass('btn-primary active');

            var $tbody = $('#super-tables-body');
            $tbody.html(
                '<tr><td colspan="4"><span class="text-muted">Loading tables...</span></td></tr>'
            );

            $.ajax({
                url: 'index.php?r=super/get-controller-tables',
                method: 'GET',
                dataType: 'json',
                data: {
                    controller: controllerId
                },
                success: function(res) {
                    if (!res.success) {
                        $tbody.html(
                            '<tr><td colspan="4"><span class="text-danger">' +
                            escapeHtml(res.message || 'Failed to load tables.') +
                            '</span></td></tr>'
                        );
                        return;
                    }

                    $('#super-current-controller-title').text(
                        res.controllerTitle || controllerId
                    );

                    var tables = res.tables || [];
                    if (!tables.length) {
                        $tbody.html(
                            '<tr><td colspan="4"><span class="text-muted">No valid database tables detected for this controller.</span></td></tr>'
                        );
                        return;
                    }

                    var html = '';
                    tables.forEach(function(t, idx) {
                        html += '<tr>';
                        html += '<td>' + (idx + 1) + '</td>';
                        html += '<td><strong>' + escapeHtml(t.name) + '</strong></td>';
                        html += '<td>' + (t.rows != null ? parseInt(t.rows, 10) :
                            '<span class="text-muted">—</span>') + '</td>';
                        html += '<td>';
                        if (t.lastInsertedAt) {
                            html += escapeHtml(t.lastInsertedAt) + '&nbsp; ';
                        } else {
                            html += '<span class="text-muted">—</span>&nbsp; ';
                        }
                        html += '&nbsp;';
                        html +=
                            '<button type="button" class="btn btn-xs btn-white btn-info super-view-table" ' +
                            'data-controller="' + escapeHtml(res.controllerId) + '" ' +
                            'data-table="' + escapeHtml(t.name) + '">' +
                            '<i class="ace-icon fa fa-search"></i> View</button> ';
                        html +=
                            '<button type="button" class="btn btn-xs btn-white btn-danger super-truncate-table" ' +
                            'data-controller="' + escapeHtml(res.controllerId) + '" ' +
                            'data-table="' + escapeHtml(t.name) + '">' +
                            '<i class="ace-icon fa fa-trash"></i> Truncate</button>';
                        html += '</td>';
                        html += '</tr>';
                    });

                    $tbody.html(html);
                },
                error: function() {
                    $tbody.html(
                        '<tr><td colspan="4"><span class="text-danger">Error while loading tables.</span></td></tr>'
                    );
                }
            });
        }

        // Controller button click (load tables via AJAX)
        $(document).on('click', '.super-controller-btn', function(e) {
            e.preventDefault();
            var controllerId = $(this).data('controller');
            loadControllerTables(controllerId);

            // When changing controller, default back to DB overview mode
            $('.super-mode-toggle button[data-mode="db"]').addClass('btn-primary active').removeClass(
                'btn-white');
            $('.super-mode-toggle button[data-mode="test"]').addClass('btn-white').removeClass(
                'btn-primary active');
            $('#super-db-overview').show();
            $('#super-test-run').hide();
        });

        // Mode toggle (DB overview / Test Run)
        $(document).on('click', '.super-mode-toggle button', function(e) {
            e.preventDefault();
            var mode = $(this).data('mode');
            if (!superCurrentControllerId) {
                showToastMessage('Please select a controller first.');
                return;
            }

            $('.super-mode-toggle button').removeClass('btn-primary active').addClass('btn-white');
            $(this).addClass('btn-primary active').removeClass('btn-white');

            if (mode === 'db') {
                $('#super-db-overview').show();
                $('#super-test-run').hide();
            } else {
                $('#super-db-overview').hide();
                $('#super-test-run').show();
                loadControllerActions(superCurrentControllerId);
            }
        });

        function loadControllerActions(controllerId) {
            var $tbody = $('#super-actions-body');
            $tbody.html(
                '<tr><td colspan="5"><span class="text-muted">Loading actions...</span></td></tr>'
            );

            $.ajax({
                url: 'index.php?r=super/get-controller-actions',
                method: 'GET',
                dataType: 'json',
                data: {
                    controller: controllerId
                },
                success: function(res) {
                    if (!res.success) {
                        $tbody.html(
                            '<tr><td colspan="5"><span class="text-danger">' +
                            escapeHtml(res.message || 'Failed to load actions.') +
                            '</span></td></tr>'
                        );
                        return;
                    }

                    var actions = res.actions || [];
                    if (!actions.length) {
                        $tbody.html(
                            '<tr><td colspan="5"><span class="text-muted">No actions found for this controller.</span></td></tr>'
                        );
                        return;
                    }

                    var html = '';
                    actions.forEach(function(a, idx) {
                        html += '<tr data-controller="' + escapeHtml(res.controllerId) +
                            '" data-action="' + escapeHtml(a.id) + '">';
                        html += '<td>' + (idx + 1) + '</td>';
                        html += '<td><strong>' + escapeHtml(a.method) + '</strong></td>';
                        html += '<td><code>' + escapeHtml(a.route) + '</code></td>';
                        html +=
                            '<td class="super-action-result"><span class="text-muted">Not run</span></td>';
                        html += '<td>';
                        html +=
                            '<button type="button" class="btn btn-xs btn-white btn-primary super-run-action">' +
                            '<i class="ace-icon fa fa-play"></i> Run</button>';
                        html += '</td>';
                        html += '</tr>';
                    });

                    $tbody.html(html);
                },
                error: function() {
                    $tbody.html(
                        '<tr><td colspan="5"><span class="text-danger">Error while loading actions.</span></td></tr>'
                    );
                }
            });
        }

        // Initial load for default controller (if present)
        $(function() {
            var defaultController = <?= json_encode($selectedControllerId) ?>;
            if (defaultController) {
                loadControllerTables(defaultController);
            }
        });

        // Load table data and show in SweetAlert modal
        $(document).on('click', '.super-view-table', function(e) {
            e.preventDefault();

            var $btn = $(this);
            var controller = $btn.data('controller');
            var table = $btn.data('table');

            if (!controller || !table) {
                return;
            }

            $.ajax({
                url: 'index.php?r=super/get-table-data',
                method: 'GET',
                dataType: 'json',
                data: {
                    controller: controller,
                    table: table
                },
                success: function(res) {
                    if (!res.success) {
                        showToastMessage(res.message || 'Failed to load table data.');
                        return;
                    }

                    var columns = res.columns || [];
                    var rows = res.rows || [];
                    var pkField = res.pk || null;

                    var html = '<div style="max-height: 60vh; overflow:auto; text-align:left;">';
                    html +=
                        '<table class="table table-striped table-bordered table-hover" style="font-size: 12px;">';
                    html += '<thead><tr>';
                    columns.forEach(function(col) {
                        html += '<th>' + escapeHtml(col) + '</th>';
                    });
                    html += '<th style="width: 80px;">Actions</th>';
                    html += '</tr></thead><tbody>';

                    rows.forEach(function(row) {
                        var pkValue = pkField && row.hasOwnProperty(pkField) ? row[
                            pkField] : null;
                        html += '<tr data-pk-field="' + escapeHtml(pkField || '') +
                            '" data-pk-value="' + escapeHtml(pkValue) +
                            '" data-controller="' + escapeHtml(controller) +
                            '" data-table="' + escapeHtml(table) + '">';
                        columns.forEach(function(col) {
                            var cellValue = row.hasOwnProperty(col) ? row[col] : '';
                            html += '<td class="super-table-cell" ' +
                                'data-column="' + escapeHtml(col) + '" ' +
                                'data-pk-field="' + escapeHtml(pkField || '') +
                                '" ' +
                                'data-pk-value="' + escapeHtml(pkValue) + '" ' +
                                'data-controller="' + escapeHtml(controller) +
                                '" ' +
                                'data-table="' + escapeHtml(table) + '">' +
                                escapeHtml(cellValue) +
                                '</td>';
                        });
                        html += '<td>';
                        if (pkField && pkValue !== null && pkValue !== '') {
                            html +=
                                '<button type="button" class="btn btn-xs btn-danger super-delete-row" ' +
                                'data-pk-field="' + escapeHtml(pkField) + '" ' +
                                'data-pk-value="' + escapeHtml(pkValue) + '" ' +
                                'data-controller="' + escapeHtml(controller) + '" ' +
                                'data-table="' + escapeHtml(table) + '">' +
                                '<i class="ace-icon fa fa-trash"></i>' +
                                '</button>';
                        } else {
                            html +=
                                '<span class="text-muted" style="font-size:11px;">N/A</span>';
                        }
                        html += '</td>';
                        html += '</tr>';
                    });

                    html += '</tbody></table></div>';

                    Swal.fire({
                        title: 'Table: ' + escapeHtml(table),
                        html: html,
                        width: '90%',
                        showConfirmButton: true,
                        confirmButtonText: 'Close'
                    });
                },
                error: function() {
                    showToastMessage('Error while loading table data.');
                }
            });
        });

        // Inline edit on double-click
        $(document).on('dblclick', '.super-table-cell', function() {
            var $cell = $(this);
            if ($cell.data('editing')) {
                return;
            }

            var controller = $cell.data('controller');
            var table = $cell.data('table');
            var column = $cell.data('column');
            var pkField = $cell.data('pkField');
            var pkValue = $cell.data('pkValue');

            if (!controller || !table || !column || !pkField || pkValue === undefined) {
                return;
            }

            var original = $cell.text();
            $cell.data('editing', true);
            $cell.attr('contenteditable', 'true').focus();

            function finishEdit(save) {
                $cell.removeAttr('contenteditable');
                $cell.data('editing', false);
                $cell.off('blur.superEdit keydown.superEdit');

                var newValue = $cell.text();

                if (!save || newValue === original) {
                    $cell.text(original);
                    return;
                }

                $.ajax({
                    url: 'index.php?r=super/update-cell',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        controller: controller,
                        table: table,
                        column: column,
                        pk_field: pkField,
                        pk_value: pkValue,
                        value: newValue
                    },
                    success: function(res) {
                        if (!res.success) {
                            showToastMessage(res.message || 'Failed to update cell.');
                            $cell.text(original);
                        } else {
                            showToastMessage('Cell updated.');
                        }
                    },
                    error: function() {
                        showToastMessage('Error while updating cell.');
                        $cell.text(original);
                    }
                });
            }

            $cell.on('blur.superEdit', function() {
                finishEdit(true);
            });

            $cell.on('keydown.superEdit', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    finishEdit(true);
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    finishEdit(false);
                }
            });
        });

        // Delete row from modal
        $(document).on('click', '.super-delete-row', function(e) {
            e.preventDefault();

            var $btn = $(this);
            var $tr = $btn.closest('tr');

            var controller = $btn.data('controller');
            var table = $btn.data('table');
            var pkField = $btn.data('pkField');
            var pkValue = $btn.data('pkValue');

            if (!controller || !table || !pkField || pkValue === undefined) {
                return;
            }

            if (!confirm('Delete this row permanently?')) {
                return;
            }

            $.ajax({
                url: 'index.php?r=super/delete-row',
                method: 'POST',
                dataType: 'json',
                data: {
                    controller: controller,
                    table: table,
                    pk_field: pkField,
                    pk_value: pkValue
                },
                success: function(res) {
                    if (!res.success) {
                        showToastMessage(res.message || 'Failed to delete row.');
                    } else {
                        $tr.remove();
                        showToastMessage('Row deleted.');
                    }
                },
                error: function() {
                    showToastMessage('Error while deleting row.');
                }
            });
        });

        // Run a controller action (Test Run)
        $(document).on('click', '.super-run-action', function(e) {
            e.preventDefault();

            var $btn = $(this);
            var $tr = $btn.closest('tr');
            var controller = $tr.data('controller');
            var action = $tr.data('action');
            var $resultCell = $tr.find('.super-action-result');

            if (!controller || !action) {
                return;
            }

            $btn.prop('disabled', true).html('<i class="ace-icon fa fa-spinner fa-spin"></i>');
            $resultCell.html('<span class="text-muted">Running...</span>');

            $.ajax({
                url: 'index.php?r=super/run-action-test',
                method: 'POST',
                dataType: 'json',
                data: {
                    controller: controller,
                    action: action
                },
                success: function(res) {
                    if (!res || res.success === false) {
                        $resultCell.html(
                            '<span class="text-danger">Error: ' + escapeHtml((res && res
                                .message) || 'Failed') + '</span>'
                        );
                        return;
                    }

                    if (res.status === 'ok') {
                        $resultCell.html(
                            '<span class="label label-success">OK</span> ' +
                            '<small>(' + escapeHtml(String(res.durationMs || '')) +
                            ' ms)</small>'
                        );
                    } else {
                        $resultCell.html(
                            '<span class="label label-danger">Error</span> ' +
                            '<small>' + escapeHtml(res.message || 'Failed') + '</small>'
                        );
                    }
                },
                error: function(xhr) {
                    var msg = 'Request failed';
                    if (xhr && xhr.responseText) {
                        msg += ': ' + xhr.responseText.substring(0, 120);
                    }
                    $resultCell.html(
                        '<span class="text-danger">' + escapeHtml(msg) + '</span>'
                    );
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="ace-icon fa fa-play"></i> Run');
                }
            });
        });

        // Truncate table from main view
        $(document).on('click', '.super-truncate-table', function(e) {
            e.preventDefault();

            var $btn = $(this);
            var controller = $btn.data('controller');
            var table = $btn.data('table');

            if (!controller || !table) {
                return;
            }

            Swal.fire({
                title: 'Truncate table?',
                html: 'This will permanently delete <strong>all records</strong> from <code>' +
                    escapeHtml(table) + '</code>.<br>Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, truncate it',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    url: 'index.php?r=super/truncate-table',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        controller: controller,
                        table: table
                    },
                    success: function(res) {
                        if (!res.success) {
                            showToastMessage(res.message || 'Failed to truncate table.');
                        } else {
                            // Use server message so user can see if a fallback (DELETE + AUTO_INCREMENT reset)
                            // was used instead of a native TRUNCATE.
                            showToastMessage(res.message || 'Table truncated.');
                            location.reload();
                        }
                    },
                    error: function() {
                        showToastMessage('Error while truncating table.');
                    }
                });
            });
        });
    })(jQuery);
</script>
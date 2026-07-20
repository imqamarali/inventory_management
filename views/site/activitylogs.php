<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Activity Logs';
?>

<style>
.activity-logs-container {
    padding: 20px;
}

.filters-section {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
    font-size: 14px;
}

.filter-group input,
.filter-group select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.filter-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #218838;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.stats-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.stat-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #007bff;
}

.stat-label {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.logs-table-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-header h2 {
    margin: 0;
    font-size: 20px;
    color: #333;
}

.logs-table {
    width: 100%;
    border-collapse: collapse;
}

.logs-table thead {
    background: #f8f9fa;
}

.logs-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #dee2e6;
    font-size: 14px;
}

.logs-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.logs-table tbody tr:hover {
    background: #f8f9fa;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
}

.badge-login {
    background: #d4edda;
    color: #155724;
}

.badge-logout {
    background: #f8d7da;
    color: #721c24;
}

.badge-create {
    background: #cce5ff;
    color: #004085;
}

.badge-update {
    background: #fff3cd;
    color: #856404;
}

.badge-delete {
    background: #f8d7da;
    color: #721c24;
}

.badge-view {
    background: #d1ecf1;
    color: #0c5460;
}

.badge-export {
    background: #e2e3e5;
    color: #383d41;
}

.badge-default {
    background: #e9ecef;
    color: #495057;
}

.pagination {
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #eee;
}

.pagination-info {
    font-size: 14px;
    color: #666;
}

.pagination-controls {
    display: flex;
    gap: 5px;
}

.pagination-controls a,
.pagination-controls span {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
    font-size: 14px;
}

.pagination-controls a:hover {
    background: #f8f9fa;
}

.pagination-controls .active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.pagination-controls .disabled {
    opacity: 0.5;
    pointer-events: none;
}

.details-cell {
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.details-cell:hover {
    white-space: normal;
    word-wrap: break-word;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 600;
    color: #333;
}

.user-role {
    font-size: 12px;
    color: #666;
}

.ip-address {
    font-family: 'Courier New', monospace;
    font-size: 12px;
    color: #666;
}

.cleanup-section {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.cleanup-section h3 {
    margin: 0 0 15px 0;
    font-size: 18px;
    color: #333;
}

.cleanup-form {
    display: flex;
    gap: 10px;
    align-items: flex-end;
}

.no-data {
    padding: 40px;
    text-align: center;
    color: #666;
    font-size: 16px;
}
</style>

<div class="activity-logs-container">
    <h1 style="margin-bottom: 20px; color: #333;">
        <i class="fa fa-history"></i> Activity Logs
    </h1>

    <!-- Statistics Section -->
    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-value"><?= number_format($totalLogs) ?></div>
            <div class="stat-label">Total Logs</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $page ?> / <?= $totalPages ?></div>
            <div class="stat-label">Current Page</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= count($logs) ?></div>
            <div class="stat-label">Logs on This Page</div>
        </div>
    </div>

    <!-- Cleanup Section (Super Admin Only) -->
    <?php if (Yii::$app->Component->CheckRole() == 1): ?>
    <div class="cleanup-section">
        <h3><i class="fa fa-trash"></i> Cleanup Old Logs</h3>
        <div class="cleanup-form">
            <div class="filter-group" style="flex: 0 0 200px;">
                <label>Delete logs older than:</label>
                <select id="cleanup-days" class="form-control">
                    <option value="30">30 days</option>
                    <option value="60">60 days</option>
                    <option value="90" selected>90 days</option>
                    <option value="180">180 days</option>
                    <option value="365">1 year</option>
                </select>
            </div>
            <button class="btn btn-danger" onclick="cleanupLogs()">
                <i class="fa fa-trash"></i> Delete Old Logs
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filters Section -->
    <div class="filters-section">
        <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #333;">
            <i class="fa fa-filter"></i> Filters
        </h3>
        <form method="get" action="<?= Url::to(['site/activitylogs']) ?>">
            <div class="filters-grid">
                <div class="filter-group">
                    <label>User:</label>
                    <select name="user_id" class="form-control">
                        <option value="">All Users</option>
                        <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>"
                            <?= isset($filters['user_id']) && $filters['user_id'] == $user['id'] ? 'selected' : '' ?>>
                            <?= Html::encode($user['name']) ?> (<?= Html::encode($user['username']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Module:</label>
                    <select name="module" class="form-control">
                        <option value="">All Modules</option>
                        <?php foreach ($modules as $module): ?>
                        <?php if (!empty($module['module'])): ?>
                        <option value="<?= Html::encode($module['module']) ?>"
                            <?= isset($filters['module']) && $filters['module'] == $module['module'] ? 'selected' : '' ?>>
                            <?= Html::encode(ucfirst($module['module'])) ?>
                        </option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Activity Type:</label>
                    <select name="activitytype" class="form-control">
                        <option value="">All Types</option>
                        <?php foreach ($activityTypes as $type): ?>
                        <?php if (!empty($type['activitytype'])): ?>
                        <option value="<?= Html::encode($type['activitytype']) ?>"
                            <?= isset($filters['activitytype']) && $filters['activitytype'] == $type['activitytype'] ? 'selected' : '' ?>>
                            <?= Html::encode(ucfirst($type['activitytype'])) ?>
                        </option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Date From:</label>
                    <input type="date" name="date_from" class="form-control"
                        value="<?= isset($filters['date_from']) ? $filters['date_from'] : '' ?>">
                </div>

                <div class="filter-group">
                    <label>Date To:</label>
                    <input type="date" name="date_to" class="form-control"
                        value="<?= isset($filters['date_to']) ? $filters['date_to'] : '' ?>">
                </div>

                <div class="filter-group">
                    <label>Search:</label>
                    <input type="text" name="search" class="form-control" placeholder="Search activity or user..."
                        value="<?= isset($filters['search']) ? Html::encode($filters['search']) : '' ?>">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Apply Filters
                </button>
                <a href="<?= Url::to(['site/activitylogs']) ?>" class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> Reset
                </a>
                <a href="<?= Url::to(['site/exportlogs'] + array_merge([''], $filters)) ?>" class="btn btn-success">
                    <i class="fa fa-download"></i> Export to CSV
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="logs-table-container">
        <div class="table-header">
            <h2>Activity Logs</h2>
            <div class="filter-group" style="margin-bottom: 0;">
                <select onchange="changePerPage(this.value)" class="form-control">
                    <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25 per page</option>
                    <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50 per page</option>
                    <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100 per page</option>
                    <option value="200" <?= $perPage == 200 ? 'selected' : '' ?>>200 per page</option>
                </select>
            </div>
        </div>

        <?php if (empty($logs)): ?>
        <div class="no-data">
            <i class="fa fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 10px;"></i>
            <p>No activity logs found.</p>
        </div>
        <?php else: ?>
        <table class="logs-table">
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th style="width: 150px;">Date & Time</th>
                    <th style="width: 150px;">User</th>
                    <th>Activity</th>
                    <th style="width: 120px;">Type</th>
                    <th style="width: 100px;">Module</th>
                    <th style="width: 80px;">Ref ID</th>
                    <th style="width: 120px;">IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= $log['id'] ?></td>
                    <td>
                        <div style="font-size: 13px;">
                            <div><?= date('M d, Y', strtotime($log['datetime'])) ?></div>
                            <div style="color: #666;"><?= date('h:i A', strtotime($log['datetime'])) ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="user-info">
                            <span class="user-name"><?= Html::encode($log['user_name'] ?? 'System') ?></span>
                            <span class="user-role"><?= Html::encode($log['role_name'] ?? 'N/A') ?></span>
                        </div>
                    </td>
                    <td class="details-cell" title="<?= Html::encode($log['activity']) ?>">
                        <?= Html::encode($log['activity']) ?>
                        <?php if (!empty($log['additional_data'])): ?>
                        <div style="font-size: 11px; color: #999; margin-top: 4px;">
                            <?php
                                        $additionalData = json_decode($log['additional_data'], true);
                                        if (is_array($additionalData)) {
                                            echo Html::encode(json_encode($additionalData, JSON_UNESCAPED_SLASHES));
                                        }
                                        ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                                $activityType = strtolower($log['activitytype']);
                                $badgeClass = 'badge-default';
                                if (strpos($activityType, 'login') !== false) {
                                    $badgeClass = 'badge-login';
                                } elseif (strpos($activityType, 'logout') !== false) {
                                    $badgeClass = 'badge-logout';
                                } elseif (strpos($activityType, 'create') !== false) {
                                    $badgeClass = 'badge-create';
                                } elseif (strpos($activityType, 'update') !== false) {
                                    $badgeClass = 'badge-update';
                                } elseif (strpos($activityType, 'delete') !== false) {
                                    $badgeClass = 'badge-delete';
                                } elseif (strpos($activityType, 'view') !== false) {
                                    $badgeClass = 'badge-view';
                                } elseif (strpos($activityType, 'export') !== false) {
                                    $badgeClass = 'badge-export';
                                }
                                ?>
                        <span class="badge <?= $badgeClass ?>">
                            <?= Html::encode(ucfirst($log['activitytype'])) ?>
                        </span>
                    </td>
                    <td><?= Html::encode($log['module'] ?? '-') ?></td>
                    <td><?= Html::encode($log['refid'] ?? '-') ?></td>
                    <td>
                        <span class="ip-address"><?= Html::encode($log['ip_address'] ?? 'N/A') ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <div class="pagination-info">
                Showing <?= (($page - 1) * $perPage) + 1 ?> to <?= min($page * $perPage, $totalLogs) ?> of
                <?= number_format($totalLogs) ?> logs
            </div>
            <div class="pagination-controls">
                <?php if ($page > 1): ?>
                <a
                    href="<?= Url::to(array_merge(['site/activitylogs'], $filters, ['page' => 1, 'per_page' => $perPage])) ?>">
                    <i class="fa fa-angle-double-left"></i> First
                </a>
                <a
                    href="<?= Url::to(array_merge(['site/activitylogs'], $filters, ['page' => $page - 1, 'per_page' => $perPage])) ?>">
                    <i class="fa fa-angle-left"></i> Previous
                </a>
                <?php else: ?>
                <span class="disabled"><i class="fa fa-angle-double-left"></i> First</span>
                <span class="disabled"><i class="fa fa-angle-left"></i> Previous</span>
                <?php endif; ?>

                <?php
                    // Show page numbers
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);

                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                <?php if ($i == $page): ?>
                <span class="active"><?= $i ?></span>
                <?php else: ?>
                <a
                    href="<?= Url::to(array_merge(['site/activitylogs'], $filters, ['page' => $i, 'per_page' => $perPage])) ?>">
                    <?= $i ?>
                </a>
                <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <a
                    href="<?= Url::to(array_merge(['site/activitylogs'], $filters, ['page' => $page + 1, 'per_page' => $perPage])) ?>">
                    Next <i class="fa fa-angle-right"></i>
                </a>
                <a
                    href="<?= Url::to(array_merge(['site/activitylogs'], $filters, ['page' => $totalPages, 'per_page' => $perPage])) ?>">
                    Last <i class="fa fa-angle-double-right"></i>
                </a>
                <?php else: ?>
                <span class="disabled">Next <i class="fa fa-angle-right"></i></span>
                <span class="disabled">Last <i class="fa fa-angle-double-right"></i></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function changePerPage(perPage) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('per_page', perPage);
    urlParams.set('page', 1); // Reset to page 1
    window.location.search = urlParams.toString();
}

function cleanupLogs() {
    const days = document.getElementById('cleanup-days').value;

    if (!confirm(
            `Are you sure you want to delete all activity logs older than ${days} days? This action cannot be undone.`
            )) {
        return;
    }

    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Deleting...';
    btn.disabled = true;

    // Send AJAX request
    fetch('<?= Url::to(['site/deleteoldlogs']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
            },
            body: 'days=' + days
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(error => {
            alert('An error occurred: ' + error.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}
</script>
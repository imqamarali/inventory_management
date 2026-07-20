<?php
$db = Yii::$app->db;

// Get ticket details
$ticket = $db->createCommand(
    "SELECT t.*, 
            CONCAT(su.first_name, ' ', su.last_name) as created_by_name,
            CONCAT(au.first_name, ' ', au.last_name) as assigned_to_name,
            su.email as created_by_email
     FROM tickets t
     LEFT JOIN system_users su ON t.created_by = su.id
     LEFT JOIN system_users au ON t.assigned_to = au.id
     WHERE t.id = :id",
    [':id' => $ticket_id]
)->queryOne();

if (!$ticket) {
    echo '<div style="padding: 40px; text-align: center;">
            <h3>Ticket not found</h3>
            <p>The ticket you are looking for does not exist.</p>
          </div>';
    return;
}

// Get ticket files
$files = $db->createCommand(
    "SELECT * FROM ticket_files WHERE ticket_id = :id ORDER BY uploaded_at DESC",
    [':id' => $ticket_id]
)->queryAll();

// Get ticket replies
$replies = $db->createCommand(
    "SELECT tr.*, CONCAT(su.first_name, ' ', su.last_name) as replied_by_name, su.email as replied_by_email
     FROM ticket_replies tr
     LEFT JOIN system_users su ON tr.replied_by = su.id
     WHERE tr.ticket_id = :id
     ORDER BY tr.replied_at ASC",
    [':id' => $ticket_id]
)->queryAll();

// Get ticket activity logs
$logs = $db->createCommand(
    "SELECT tl.*, CONCAT(su.first_name, ' ', su.last_name) as user_name
     FROM ticket_logs tl
     LEFT JOIN system_users su ON tl.user_id = su.id
     WHERE tl.ticket_id = :id
     ORDER BY tl.created_at DESC
     LIMIT 50",
    [':id' => $ticket_id]
)->queryAll();

// Get list of employees for reassignment
$employees = $db->createCommand("
    SELECT id, CONCAT(first_name, ' ', last_name,' (', (SELECT roles.name FROM roles WHERE id = role_id),')')  as name
    FROM `system_users` ORDER BY role_id;")->queryAll();
?>

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.ticket-view-wrapper {
    margin: -30px;
    padding: 12px;
    background: #f8fafc;
    min-height: 100vh;
}

.ticket-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 12px;
    max-width: 1600px;
    margin: 0 auto;
}

/* Left Column Styles */
.ticket-main-column {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.ticket-header-section {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.ticket-main-title {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 10px;
    line-height: 1.3;
}

.ticket-header-meta {
    display: flex;
    gap: 16px;
    font-size: 12px;
    color: #64748b;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.ticket-header-meta i {
    margin-right: 4px;
    color: #94a3b8;
}

.ticket-badges {
    display: flex;
    gap: 6px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.ticket-description-section {
    background: #f8fafc;
    padding: 12px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    margin-top: 10px;
}

.ticket-description-text {
    color: #334155;
    line-height: 1.5;
    white-space: pre-wrap;
    font-size: 13px;
}

.files-section {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.file-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    margin-bottom: 8px;
    transition: all 0.2s;
}

.file-item:hover {
    border-color: #cbd5e1;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.replies-section {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.section-title {
    font-size: 15px;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.section-title i {
    color: #3b82f6;
    font-size: 14px;
}

.reply-item {
    border-left: 3px solid #3b82f6;
    padding: 12px;
    margin-bottom: 10px;
    background: #f8fafc;
    border-radius: 0 6px 6px 0;
    transition: all 0.2s;
}

.reply-item:hover {
    background: #f1f5f9;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
}

.reply-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.reply-author {
    font-weight: 600;
    color: #0f172a;
    font-size: 13px;
}

.reply-author i {
    color: #3b82f6;
    margin-right: 4px;
    font-size: 12px;
}

.reply-time {
    font-size: 11px;
    color: #94a3b8;
}

.reply-message {
    color: #334155;
    line-height: 1.5;
    white-space: pre-wrap;
    font-size: 13px;
}

/* Right Column Styles */
.ticket-sidebar {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.sidebar-section {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 12px;
}

.sidebar-title {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.sidebar-title i {
    color: #3b82f6;
    font-size: 13px;
}

.info-item {
    margin-bottom: 14px;
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-label {
    font-size: 10px;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.info-value {
    font-size: 13px;
    color: #0f172a;
}

.form-group {
    margin-bottom: 12px;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 6px;
}

.form-control {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 13px;
    transition: all 0.2s;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

textarea.form-control {
    resize: vertical;
    min-height: 90px;
}

.btn-action {
    padding: 8px 14px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-action:hover {
    background: #f1f5f9;
    transform: translateY(-1px);
}

.btn-primary {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
    width: 100%;
    justify-content: center;
}

.btn-primary:hover {
    background: #2563eb;
    border-color: #2563eb;
    transform: none;
}

.btn-danger {
    background: #ef4444;
    color: white;
    border-color: #ef4444;
}

.btn-danger:hover {
    background: #dc2626;
    border-color: #dc2626;
}

.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #3b82f6;
    text-decoration: none;
    margin-bottom: 12px;
    font-size: 13px;
    font-weight: 500;
    padding: 6px 10px;
    border-radius: 5px;
    transition: all 0.2s;
}

.back-button:hover {
    background: #eff6ff;
    color: #2563eb;
}

.divider {
    height: 1px;
    background: #e2e8f0;
    margin: 12px 0;
}

/* Activity Log Styles */
.activity-section {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.activity-item {
    display: flex;
    gap: 10px;
    padding: 10px;
    border-left: 2px solid #e2e8f0;
    margin-bottom: 8px;
    transition: all 0.2s;
}

.activity-item:hover {
    background: #f8fafc;
    border-left-color: #94a3b8;
}

.activity-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    flex-shrink: 0;
    margin-top: 2px;
}

.activity-icon.updated {
    background: #dbeafe;
    color: #1e40af;
}

.activity-icon.created {
    background: #dcfce7;
    color: #166534;
}

.activity-icon.replied {
    background: #fef3c7;
    color: #92400e;
}

.activity-content {
    flex: 1;
    font-size: 12px;
}

.activity-user {
    font-weight: 600;
    color: #0f172a;
}

.activity-action {
    color: #64748b;
    margin-top: 2px;
}

.activity-change {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 4px;
    padding: 4px 8px;
    background: #f1f5f9;
    border-radius: 4px;
    font-size: 11px;
}

.activity-old {
    color: #ef4444;
    text-decoration: line-through;
}

.activity-new {
    color: #10b981;
    font-weight: 600;
}

.activity-time {
    font-size: 10px;
    color: #94a3b8;
    margin-top: 4px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .ticket-layout {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .ticket-view-wrapper {
        padding: 8px;
    }

    .ticket-header-section,
    .sidebar-section,
    .files-section,
    .replies-section,
    .activity-section {
        padding: 12px;
    }
}
</style>

<div class="ticket-view-wrapper">
    <a href="javascript:void(0)" class="back-button" onclick="window.history.back()">
        <i class="fa fa-arrow-left"></i> Back to tickets
    </a>

    <div class="ticket-layout">
        <!-- LEFT COLUMN: Main Ticket Content -->
        <div class="ticket-main-column">
            <!-- Ticket Header -->
            <div class="ticket-header-section">
                <div class="ticket-main-title">
                    <?= htmlspecialchars($ticket['title']) ?>
                    <span style="color: #64748b; font-size: 14px; font-weight: normal;">#<?= $ticket['id'] ?></span>
                </div>

                <div class="ticket-header-meta">
                    <span><i class="fa fa-user"></i>
                        <strong><?= htmlspecialchars($ticket['created_by_name'] ?? 'Unknown') ?></strong></span>
                    <span><i class="fa fa-clock-o"></i>
                        <?= date('F d, Y h:i A', strtotime($ticket['created_at'])) ?></span>
                    <span><i class="fa fa-refresh"></i> Updated
                        <?= date('M d, Y h:i A', strtotime($ticket['updated_at'])) ?></span>
                </div>

                <div class="ticket-badges">
                    <span class="badge badge-priority-<?= strtolower($ticket['priority']) ?>">
                        Priority: <?= $ticket['priority'] ?>
                    </span>
                    <span class="badge badge-status-<?= strtolower(str_replace(' ', '-', $ticket['status'])) ?>">
                        Status: <?= $ticket['status'] ?>
                    </span>
                    <span class="badge badge-category">
                        <?= $ticket['category'] ?>
                    </span>
                </div>

                <div class="ticket-description-section">
                    <div class="ticket-description-text">
                        <?= htmlspecialchars($ticket['description']) ?>
                    </div>
                </div>

                <?php if (!empty($ticket['requester_name']) || !empty($ticket['requester_email'])): ?>
                <div class="divider"></div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <?php if (!empty($ticket['requester_name'])): ?>
                    <div>
                        <div class="info-label">Requester Name</div>
                        <div class="info-value"><?= htmlspecialchars($ticket['requester_name']) ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($ticket['requester_email'])): ?>
                    <div>
                        <div class="info-label">Requester Email</div>
                        <div class="info-value"><?= htmlspecialchars($ticket['requester_email']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Attachments -->
            <?php if (!empty($files)): ?>
            <div class="files-section">
                <div class="section-title"><i class="fa fa-paperclip"></i> Attachments</div>
                <?php foreach ($files as $file): ?>
                <div class="file-item">
                    <i class="fa fa-file-o" style="font-size: 16px; color: #64748b;"></i>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 12px;"><?= htmlspecialchars($file['file_name']) ?>
                        </div>
                        <div style="font-size: 11px; color: #64748b;">
                            <?= date('M d, Y h:i A', strtotime($file['uploaded_at'])) ?>
                        </div>
                    </div>
                    <a href="<?= Yii::getAlias('@web') . '/' . $file['file_path'] ?>" target="_blank"
                        class="btn-action">
                        <i class="fa fa-download"></i> Download
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Replies -->
            <?php if (!empty($replies)): ?>
            <div class="replies-section">
                <div class="section-title"><i class="fa fa-comments"></i> Conversation (<?= count($replies) ?>)</div>
                <?php foreach ($replies as $reply): ?>
                <div class="reply-item">
                    <div class="reply-header">
                        <div class="reply-author">
                            <i class="fa fa-user-circle"></i>
                            <?= htmlspecialchars($reply['replied_by_name'] ?? 'Unknown') ?>
                        </div>
                        <div class="reply-time">
                            <?= date('M d, Y h:i A', strtotime($reply['replied_at'])) ?>
                        </div>
                    </div>
                    <div class="reply-message">
                        <?= htmlspecialchars($reply['message']) ?>
                    </div>
                    <?php if (!empty($reply['file'])): ?>
                    <div style="margin-top: 12px;">
                        <a href="<?= Yii::getAlias('@web') . '/' . $reply['file'] ?>" target="_blank"
                            class="btn-action">
                            <i class="fa fa-paperclip"></i> View Attachment
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Activity Log -->
            <div class="activity-section">
                <div class="section-title"><i class="fa fa-history"></i> Activity Log</div>
                <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $log): ?>
                <div class="activity-item">
                    <div class="activity-icon <?= htmlspecialchars($log['action']) ?>">
                        <i
                            class="fa fa-<?= $log['action'] === 'created' ? 'plus' : ($log['action'] === 'replied' ? 'reply' : 'pencil') ?>"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-user"><?= htmlspecialchars($log['user_name'] ?? 'System') ?></div>
                        <div class="activity-action">
                            <?php if ($log['field_name']): ?>
                            Changed <strong><?= ucfirst(str_replace('_', ' ', $log['field_name'])) ?></strong>
                            <div class="activity-change">
                                <span class="activity-old"><?= htmlspecialchars($log['old_value']) ?></span>
                                <i class="fa fa-arrow-right" style="font-size: 10px; color: #64748b;"></i>
                                <span class="activity-new"><?= htmlspecialchars($log['new_value']) ?></span>
                            </div>
                            <?php else: ?>
                            <?= ucfirst($log['action']) ?> this ticket
                            <?php endif; ?>
                        </div>
                        <div class="activity-time">
                            <i class="fa fa-clock-o"></i> <?= date('M d, Y h:i A', strtotime($log['created_at'])) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 12px;">
                    <i class="fa fa-info-circle" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
                    No activity logs yet. Changes will appear here.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT COLUMN: Sidebar with Actions & Reply Form -->
        <div class="ticket-sidebar">
            <!-- Ticket Properties -->
            <div class="sidebar-section">
                <div class="sidebar-title"><i class="fa fa-cog"></i> Ticket Properties</div>

                <?php if (in_array($ticket['status'], ['Solved', 'Closed'])): ?>
                <div
                    style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 6px; padding: 10px; margin-bottom: 12px; font-size: 12px; color: #92400e;">
                    <i class="fa fa-lock"></i> <strong>Ticket Locked</strong>
                    <div style="margin-top: 4px; font-size: 11px;">
                        This ticket is <?= $ticket['status'] ?> and cannot be modified (except status).
                    </div>
                </div>
                <?php endif; ?>

                <div class="info-item">
                    <div class="info-label">Assigned To</div>
                    <div class="info-value">
                        <select id="assign-select-<?= $ticket['id'] ?>" class="form-control"
                            onchange="updateTicketField(<?= $ticket['id'] ?>, 'assigned_to', this.value)">
                            <option value="">-- Unassigned --</option>
                            <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>"
                                <?= ($ticket['assigned_to'] == $emp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($emp['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <select class="form-control"
                            onchange="updateTicketField(<?= $ticket['id'] ?>, 'status', this.value)">
                            <?php
                            $statuses = ['Open', 'Pending', 'On hold', 'Solved', 'Closed'];
                            foreach ($statuses as $status): ?>
                            <option value="<?= $status ?>" <?= ($ticket['status'] == $status) ? 'selected' : '' ?>>
                                <?= $status ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Priority</div>
                    <div class="info-value">
                        <select class="form-control"
                            onchange="updateTicketField(<?= $ticket['id'] ?>, 'priority', this.value)">
                            <?php
                            $priorities = ['Low', 'Medium', 'High'];
                            foreach ($priorities as $priority): ?>
                            <option value="<?= $priority ?>"
                                <?= ($ticket['priority'] == $priority) ? 'selected' : '' ?>>
                                <?= $priority ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="divider"></div>

                <button class="btn-action btn-danger" style="width: 100%; justify-content: center;"
                    onclick="deleteTicket(<?= $ticket['id'] ?>)">
                    <i class="fa fa-trash"></i> Delete Ticket
                </button>
            </div>

            <!-- Add Reply Form -->
            <div class="sidebar-section">
                <div class="sidebar-title"><i class="fa fa-reply"></i> Add Reply</div>
                <form id="reply-form-<?= $ticket['id'] ?>" onsubmit="submitReply(event, <?= $ticket['id'] ?>)">
                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />
                    <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>" />

                    <div class="form-group">
                        <label>Your Reply *</label>
                        <textarea class="form-control" name="message" rows="6" required
                            placeholder="Type your reply here..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Attach File (optional)</label>
                        <input type="file" class="form-control" name="file" />
                    </div>

                    <button type="submit" class="btn-action btn-primary">
                        <i class="fa fa-send"></i> Send Reply
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Track if ticket is locked
let ticketLocked = <?= in_array($ticket['status'], ['Solved', 'Closed']) ? 'true' : 'false' ?>;

// Initialize page
$(document).ready(function() {
    if (ticketLocked) {
        lockTicketFields();
    }
});

function updateTicketField(ticketId, field, value) {
    // Prevent updates if ticket is locked (except for status field)
    if (ticketLocked && field !== 'status') {
        Swal.fire({
            icon: 'warning',
            title: 'Ticket Locked',
            text: 'This ticket is closed and cannot be modified.',
            toast: true,
            position: 'top-end',
            showConfirmButton: true
        });
        return;
    }

    $.ajax({
        url: 'index.php?r=support/update',
        method: 'POST',
        data: {
            _csrf: '<?= Yii::$app->request->getCsrfToken() ?>',
            ticket_id: ticketId,
            field: field,
            value: value
        },
        success: function(response) {
            if (response.success) {
                const ticket = response.ticket;

                // Update UI badges
                if (ticket) {
                    // Update status badge
                    if (field === 'status') {
                        const statusClass = 'badge-status-' + ticket.status.toLowerCase().replace(/ /g,
                            '-');
                        $('.ticket-badges .badge').filter(function() {
                            return $(this).text().includes('Status:');
                        }).attr('class', 'badge ' + statusClass).text('Status: ' + ticket.status);
                    }

                    // Update priority badge
                    if (field === 'priority') {
                        const priorityClass = 'badge-priority-' + ticket.priority.toLowerCase();
                        $('.ticket-badges .badge').filter(function() {
                            return $(this).text().includes('Priority:');
                        }).attr('class', 'badge ' + priorityClass).text('Priority: ' + ticket.priority);
                    }

                    // Update category badge
                    if (field === 'category') {
                        $('.ticket-badges .badge-category').text(ticket.category);
                    }
                }

                // Add new activity log entry to timeline
                if (response.latestLog) {
                    addActivityLogEntry(response.latestLog);
                }

                // Lock fields if ticket is now Solved or Closed
                if (response.shouldLock) {
                    ticketLocked = true;
                    lockTicketFields();

                    Swal.fire({
                        icon: 'info',
                        title: 'Ticket Locked',
                        text: 'This ticket is now ' + ticket.status +
                            ' and has been locked from further modifications.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000
                    });
                }

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: response.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: true
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: error,
                toast: true,
                position: 'top-end',
                showConfirmButton: true
            });
        }
    });
}

function lockTicketFields() {
    // Disable all dropdowns except status
    $('.sidebar-section select').not('[onchange*="status"]').prop('disabled', true).css({
        'background-color': '#f1f5f9',
        'cursor': 'not-allowed',
        'opacity': '0.6'
    });

    // Disable reply form
    $('form[id^="reply-form-"] textarea, form[id^="reply-form-"] input[type="file"]').prop('disabled', true).css({
        'background-color': '#f1f5f9',
        'cursor': 'not-allowed',
        'opacity': '0.6'
    });

    $('form[id^="reply-form-"] button[type="submit"]').prop('disabled', true).css({
        'background-color': '#cbd5e1',
        'cursor': 'not-allowed',
        'opacity': '0.6'
    }).html('<i class="fa fa-lock"></i> Reply Disabled');

    // Disable delete button
    $('.btn-danger').prop('disabled', true).css({
        'background-color': '#cbd5e1',
        'cursor': 'not-allowed',
        'opacity': '0.6',
        'border-color': '#cbd5e1'
    });

    // Add locked indicator badge
    if (!$('.ticket-locked-badge').length) {
        $('.ticket-badges').append(
            '<span class="badge ticket-locked-badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fcd34d;">' +
            '<i class="fa fa-lock"></i> Locked' +
            '</span>'
        );
    }

    // Add locked warning in sidebar if not exists
    if (!$('.locked-warning').length) {
        $('.sidebar-section .sidebar-title').after(
            '<div class="locked-warning" style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 6px; padding: 10px; margin-bottom: 12px; font-size: 12px; color: #92400e;">' +
            '<i class="fa fa-lock"></i> <strong>Ticket Locked</strong>' +
            '<div style="margin-top: 4px; font-size: 11px;">' +
            'This ticket is closed and cannot be modified (except status).' +
            '</div>' +
            '</div>'
        );
    }
}

function addActivityLogEntry(log) {
    // Determine icon based on action
    let icon = 'pencil';
    let actionClass = 'updated';
    if (log.action === 'created') {
        icon = 'plus';
        actionClass = 'created';
    } else if (log.action === 'replied') {
        icon = 'reply';
        actionClass = 'replied';
    }

    // Format the activity change display
    let activityAction = '';
    if (log.field_name) {
        const fieldName = log.field_name.replace(/_/g, ' ');
        activityAction =
            'Changed <strong>' + fieldName.charAt(0).toUpperCase() + fieldName.slice(1) + '</strong>' +
            '<div class="activity-change">' +
            '<span class="activity-old">' + (log.old_value || 'None') + '</span>' +
            '<i class="fa fa-arrow-right" style="font-size: 10px; color: #64748b;"></i>' +
            '<span class="activity-new">' + log.new_value + '</span>' +
            '</div>';
    } else {
        activityAction = log.action.charAt(0).toUpperCase() + log.action.slice(1) + ' this ticket';
    }

    // Format timestamp
    const timestamp = new Date(log.created_at.replace(/-/g, '/'));
    const timeStr = timestamp.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });

    // Create activity item HTML
    const activityHTML =
        '<div class="activity-item" style="animation: slideIn 0.3s ease-out;">' +
        '<div class="activity-icon ' + actionClass + '">' +
        '<i class="fa fa-' + icon + '"></i>' +
        '</div>' +
        '<div class="activity-content">' +
        '<div class="activity-user">' + (log.user_name || 'System') + '</div>' +
        '<div class="activity-action">' + activityAction + '</div>' +
        '<div class="activity-time">' +
        '<i class="fa fa-clock-o"></i> ' + timeStr +
        '</div>' +
        '</div>' +
        '</div>';

    // Remove empty state if exists
    $('.activity-section').find('div[style*="text-align: center"]').remove();

    // Prepend to activity section (newest first) after the title
    $('.activity-section .section-title').after(activityHTML);
}

// Add CSS animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);

function deleteTicket(ticketId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'index.php?r=support/delete',
                method: 'POST',
                data: {
                    _csrf: '<?= Yii::$app->request->getCsrfToken() ?>',
                    ticket_id: ticketId
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        });

                        // Reload to recent tickets after short delay
                        setTimeout(() => {
                            const contentArea = $('#ticket-content-area');
                            $.ajax({
                                url: 'index.php?r=support/loadsection',
                                method: 'GET',
                                data: {
                                    section: 'recent'
                                },
                                success: function(response) {
                                    contentArea.html(response);
                                }
                            });
                        }, 1500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: error
                    });
                }
            });
        }
    });
}

function submitReply(event, ticketId) {
    event.preventDefault();

    // Prevent replies if ticket is locked
    if (ticketLocked) {
        Swal.fire({
            icon: 'warning',
            title: 'Ticket Locked',
            text: 'Cannot reply to a closed ticket.',
            toast: true,
            position: 'top-end',
            showConfirmButton: true
        });
        return false;
    }

    const form = event.target;
    const formData = new FormData(form);

    $.ajax({
        url: 'index.php?r=support/reply',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Reply Added!',
                    text: response.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });

                // Reload ticket view after short delay to show new reply
                setTimeout(() => {
                    const contentArea = $('#ticket-content-area');
                    $.ajax({
                        url: 'index.php?r=support/loadsection',
                        method: 'GET',
                        data: {
                            section: 'ticket-' + ticketId
                        },
                        success: function(response) {
                            contentArea.html(response);
                        }
                    });
                }, 1500);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: error
            });
        }
    });
}
</script>
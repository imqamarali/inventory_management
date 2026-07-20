<!-- Font Awesome Icons -->
<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

<style>
    .ticket-list-container {
        margin: -30px;
        padding: 20px;
    }

    .ticket-list-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #1e293b;
    }

    .ticket-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .ticket-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }

    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 8px;
    }

    .ticket-title {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
        flex: 1;
    }

    .ticket-id {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }

    .ticket-meta {
        display: flex;
        gap: 16px;
        font-size: 13px;
        color: #64748b;
        margin-bottom: 8px;
    }

    .ticket-description {
        font-size: 14px;
        color: #475569;
        margin-bottom: 12px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .ticket-footer {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-priority-high {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-priority-medium {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-priority-low {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-status-open {
        background: #dcfce7;
        color: #166534;
    }

    .badge-status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-status-hold {
        background: #fed7aa;
        color: #9a3412;
    }

    .badge-status-solved {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-status-closed {
        background: #e5e7eb;
        color: #374151;
    }

    .badge-category {
        background: #f3f4f6;
        color: #4b5563;
    }
</style>

<div class="ticket-list-container">
    <h2 class="ticket-list-title"><?= htmlspecialchars($title ?? 'Tickets') ?></h2>

    <?php if (empty($tickets)): ?>
        <?php include('nothingfound.php'); ?>
    <?php else: ?>
        <?php foreach ($tickets as $ticket): ?>
            <div class="ticket-card" onclick="loadTicketView(<?= $ticket['id'] ?>)">
                <div class="ticket-header">
                    <div class="ticket-title"><?= htmlspecialchars($ticket['title']) ?></div>
                    <div class="ticket-id">#<?= $ticket['id'] ?></div>
                </div>

                <div class="ticket-meta">
                    <span><i class="fa fa-user"></i> <?= htmlspecialchars($ticket['created_by_name'] ?? 'Unknown') ?></span>
                    <span><i class="fa fa-clock-o"></i> <?= date('M d, Y', strtotime($ticket['created_at'])) ?></span>
                    <?php if (!empty($ticket['assigned_to_name'])): ?>
                        <span><i class="fa fa-user-circle"></i> Assigned to:
                            <?= htmlspecialchars($ticket['assigned_to_name']) ?></span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($ticket['description'])): ?>
                    <div class="ticket-description">
                        <?= htmlspecialchars($ticket['description']) ?>
                    </div>
                <?php endif; ?>

                <div class="ticket-footer">
                    <span class="badge badge-priority-<?= strtolower($ticket['priority']) ?>">
                        <?= $ticket['priority'] ?>
                    </span>
                    <span class="badge badge-status-<?= strtolower(str_replace(' ', '-', $ticket['status'])) ?>">
                        <?= $ticket['status'] ?>
                    </span>
                    <span class="badge badge-category">
                        <?= $ticket['category'] ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    function loadTicketView(ticketId) {
        const contentArea = $('#ticket-content-area');

        // Loading shimmer
        contentArea.html(`
        <h2 class="shimmer large"></h2>
        <div class="shimmer"></div>
        <div class="shimmer medium"></div>
    `);

        // Load ticket view
        setTimeout(() => {
            $.ajax({
                url: 'index.php?r=support/loadsection',
                method: 'GET',
                data: {
                    section: 'ticket-' + ticketId
                },
                success: function(response) {
                    contentArea.html(response);
                },
                error: function(xhr, status, error) {
                    contentArea.html('<h2>Error</h2><p>Unable to load ticket.</p>');
                    console.error(error);
                }
            });
        }, 300);
    }
</script>
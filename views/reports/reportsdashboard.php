<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-line-chart"></i>
                Reports Center
                <small>Complete Reports Overview</small>
            </h3>
        </div>

        <div>
            <button id="refreshDashboard">
                <i class="fa fa-refresh"></i>
                Refresh
            </button>
        </div>
    </div>

    <div class="stats-grid" id="reportsGrid">

        <!-- Reports will be loaded here via AJAX -->

    </div>

</div>

<script>
$(function() {
    loadReportsDashboard();

    $('#refreshDashboard').click(function() {
        loadReportsDashboard();
    });
});

function loadReportsDashboard() {
    $.ajax({
        url: "<?= Yii::$app->urlManager->createUrl('reports/reports-dashboard-data') ?>",
        type: "POST",
        dataType: "json",
        data: { flag: "load_dashboard" },
        timeout: 5000,
        success: function(response) {
            if (response.success) {
                renderReportCards(response.reports);
            } else {
                showError(response.message || 'Failed to load reports');
            }
        },
        error: function(xhr, status, error) {
            if (status === 'timeout') {
                showError('Request timed out. Please try again.');
            } else {
                showError('Network error: ' + (xhr.status || 'Unknown error'));
            }
        }
    });
}

function renderReportCards(reports) {
    const grid = $('#reportsGrid');
    grid.empty();

    const colors = ['blue', 'green', 'orange', 'red', 'purple', 'teal', 'pink', 'indigo'];

    $.each(reports, function(index, report) {
        const color = colors[index % colors.length];
        const iconClass = report.icon || 'fa-file-text';

        const card = $(`
            <div class="stat-card ${color}">

                <div class="stat-header">

                    <span class="stat-title">
                        ${report.name}
                    </span>

                    <div class="stat-icon">
                        <i class="fa ${iconClass}"></i>
                    </div>

                </div>

                <div class="stat-value">
                    ${(report.count || 0).toLocaleString()}
                </div>

                <div class="stat-subtitle">
                    Records Available
                </div>

            </div>
        `);

        grid.append(card);
    });
}

function showError(message) {
    const alert = $(`<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-circle"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`);
    $(document.body).prepend(alert);
    setTimeout(() => alert.fadeOut(), 5000);
}
</script>

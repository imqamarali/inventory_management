<?php

use yii\helpers\Html;
use yii\helpers\Url;

if (!isset($modules) && empty($modules)) {
    $modules = [];
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .reports-breadcrumb {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 20px;
    }

    .breadcrumb-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .breadcrumb-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .filter-group input, .filter-group select {
        height: 32px;
        font-size: 12px;
        padding: 6px 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
    }

    .action-btn {
        height: 32px;
        padding: 6px 12px;
        font-size: 12px;
        border: 1px solid #ddd;
        border-radius: 3px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .action-btn:hover {
        background: #f0f0f0;
    }

    .action-btn.primary {
        background: #0f4c29;
        color: white;
        border-color: #0f4c29;
    }

    .action-btn.primary:hover {
        background: #0a3620;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <!-- Breadcrumbs with Filters -->
        <div class="reports-breadcrumb">
            <div class="breadcrumb-left">
                <i class="ace-icon fa fa-home"></i>
                <a href="index.php?r=inventory/dashboard">Home</a>
                <span>/</span>
                <a href="index.php?r=inventory/reports" style="color: #0f4c29; font-weight: 600;">
                    <i class="fa fa-file-text"></i> Reports
                </a>
            </div>

            <div class="breadcrumb-right">
                <div class="filter-group">
                    <i class="fa fa-search" style="color: #999;"></i>
                    <input type="text" id="reportSearch" class="form-control"
                        placeholder="Search reports..." style="min-width: 200px;">

                    <select id="reportType" class="form-control" style="min-width: 150px;">
                        <option value="">All Report Types</option>
                        <option value="Inventory">Inventory</option>
                        <option value="Purchase">Purchase</option>
                        <option value="Sales">Sales</option>
                        <option value="Financial">Financial</option>
                        <option value="Warehouse">Warehouse</option>
                    </select>

                    <button class="action-btn" title="Search" onclick="filterReports()">
                        <i class="fa fa-search"></i> Search
                    </button>
                </div>

                <button class="action-btn primary" title="Export" onclick="exportReport()">
                    <i class="fa fa-download"></i> Export
                </button>
            </div>
        </div>

        <!-- Report Modules Navigation -->
        <div class="nav-search" id="nav-search" style="padding: 15px;">
            <div class="exam-quick-actions-group">
                <?php foreach ($modules as $module): ?>
                    <button type="button"
                        class="btn btn-sm btn-white btn-primary ajax-module"
                        data-url="<?= Url::to([$module['controller']])?>"
                        data-module="<?= htmlspecialchars($module['name']) ?>"
                        style="font-size:12px;margin-left:4px;margin-bottom:4px;">
                        <i class="<?= Html::encode($module['icon']) ?>"></i>
                        <?= Html::encode($module['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Module Content Area -->
        <div id="module-content" style="margin-top: 20px;">
        </div>
    </div>
</div>


<script>
$(document).ready(function(){

    $('.ajax-module').on('click', function(e){
        e.preventDefault();
        $('#module-content').html("");
        let url = $(this).data('url');
        $('.ajax-module').removeClass('active');
        $(this).addClass('active');
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html',
            beforeSend:function(){
                $('#module-content').html(
                    '<div class="text-center" style="padding:40px;">' +
                    '<i class="fa fa-spinner fa-spin fa-2x"></i>' +
                    '<br>Loading...' +
                    '</div>'
                );
            },
            success:function(response){
                $('#module-content').html(response);
            },
            error:function(xhr){
                $('#module-content').html(
                    '<div class="alert alert-danger">' +
                    'Unable to load module.' +
                    '</div>'
                );
                console.log(xhr.responseText);
            }
        });
    });
    $('.ajax-module:first').trigger('click');
});

function filterReports() {
    const searchText = document.getElementById('reportSearch').value.toLowerCase();
    const reportType = document.getElementById('reportType').value;

    document.querySelectorAll('.ajax-module').forEach(button => {
        let moduleName = button.dataset.module.toLowerCase();
        let show = true;

        if (searchText && !moduleName.includes(searchText)) {
            show = false;
        }

        if (reportType && !moduleName.includes(reportType.toLowerCase())) {
            show = false;
        }

        button.style.display = show ? '' : 'none';
    });
}

function exportReport() {
    let activeButton = document.querySelector('.ajax-module.active');
    if (!activeButton) {
        alert('Please select a report first');
        return;
    }

    let reportName = activeButton.dataset.module || 'Report';
    let timestamp = new Date().toISOString().split('T')[0];

    Swal.fire({
        title: 'Export Report',
        html: `
            <div style="text-align: left;">
                <p><strong>Report:</strong> ${reportName}</p>
                <p><strong>Date:</strong> ${timestamp}</p>
                <p style="margin-top: 15px; color: #666; font-size: 12px;">
                    <i class="fa fa-info-circle"></i>
                    The report data will be exported as CSV format.
                </p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fa fa-download"></i> Export CSV',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f4c29'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Export Started',
                text: 'Your report is being exported. The download should start shortly.',
                timer: 2000,
                showConfirmButton: false
            });

            // Trigger CSV download (placeholder for actual export logic)
            console.log('Exporting ' + reportName + ' as CSV...');
        }
    });
}

// Add event listeners for real-time filtering
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('reportSearch').addEventListener('keyup', filterReports);
    document.getElementById('reportType').addEventListener('change', filterReports);
});
</script>



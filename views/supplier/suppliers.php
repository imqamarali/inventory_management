<?php

use yii\helpers\Html;
use yii\helpers\Url;

if (!isset($modules) && empty($modules)) {
    $modules = [];
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .exam-stat-card {
        background: linear-gradient(135deg, #eff3f8 0%, #e8edf2 100%);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
        min-height: 100px;
    }

    .row .col-xs-12.col-sm-6.col-md-4.col-lg-2 {
        display: flex;
        flex-direction: column;
    }

    .exam-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.12);
    }

    .exam-stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: rgba(71, 137, 202, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #2263b1;
    }

    .exam-stat-value {
        font-size: 26px;
        font-weight: 700;
        color: #0b2641;
    }

    .exam-stat-label {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6b7a8c;
        margin-top: 2px;
    }

    .exam-card {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #ffffff;
    }

    .exam-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
    }

    .exam-card-header {
        padding: 16px 18px;
        border-bottom: 1px solid #edf1f7;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .exam-card-body {
        padding: 18px;
        font-size: 13px;
        color: #4b5563;
    }

    .exam-card-footer {
        padding: 14px 18px;
        border-top: 1px solid #edf1f7;
        background: #f8fafc;
        font-size: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        padding: 6px 12px;
        border-radius: 999px;
        font-weight: 600;
    }

    .pill-primary {
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
    }

    .pill-success {
        background: rgba(34, 197, 94, 0.1);
        color: #059669;
    }

    .pill-warning {
        background: rgba(234, 179, 8, 0.12);
        color: #b45309;
    }

    .timeline {
        position: relative;
        padding-left: 22px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        top: 4px;
        left: 7px;
        width: 2px;
        height: calc(100% - 8px);
        background: #e2e8f0;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 16px;
        padding-left: 16px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-dot {
        position: absolute;
        left: -22px;
        top: 6px;
        width: 12px;
        height: 12px;
        border-radius: 999px;
        background: #2563eb;
        border: 3px solid #fff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .results-table thead th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #f1f5f9;
        color: #475569;
    }

    .results-table tbody td {
        font-size: 12px;
        vertical-align: middle;
    }

    .exam-quick-actions .widget-main {
        padding: 16px;
    }

    .exam-quick-actions-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .exam-quick-actions-group .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 12px;
        border-radius: 999px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .exam-quick-actions-group .btn i {
        font-size: 14px;
    }

    .exam-quick-actions-group .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.15);
    }

    /* Calendar styles */
    #examCalendar {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 10px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }

    .fc .fc-toolbar-title {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
    }

    .fc .fc-daygrid-event {
        border-radius: 6px;
        padding: 2px 4px;
        border: 1px solid rgba(59, 130, 246, 0.25);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .empty-state p {
        margin: 0;
        font-size: 14px;
    }

    .widget-box {
        margin-bottom: 20px;
    }

    .widget-main {
        min-height: 100px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .page-content {
        padding-bottom: 20px;
    }

    @media (max-width: 768px) {
        .exam-stat-card {
            margin-bottom: 12px;
        }
    }

    /* Top Performers Single Row Avatar View */
    .performers-row {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 0;
        padding: 20px 0;
        overflow-x: auto;
        overflow-y: visible;
        position: relative;
    }

    .performers-row::-webkit-scrollbar {
        height: 6px;
    }

    .performers-row::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .performers-row::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .performer-avatar-item {
        position: relative;
        margin-left: -12px;
        transition: all 0.3s ease;
        cursor: pointer;
        z-index: 1;
    }

    .performer-avatar-item:first-child {
        margin-left: 0;
    }

    .performer-avatar-item:hover {
        z-index: 9999;
        transform: translateY(-8px) scale(1.1);
    }

    .performer-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-weight: 700;
        font-size: 16px;
        border: 3px solid #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        position: relative;
        z-index: 2;
    }

    .performer-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .performer-tooltip {
        position: absolute;
        bottom: calc(100% + 12px);
        left: 50%;
        transform: translateX(-50%);
        background: #1e293b;
        color: #ffffff;
        padding: 12px 16px;
        border-radius: 8px;
        min-width: 200px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        pointer-events: none;
        z-index: 99999;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        white-space: normal;
    }

    .performer-tooltip::after {
        content: '';
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
    }

    .performer-tooltip.tooltip-above::after {
        top: 100%;
        border-top-color: #1e293b;
    }

    .performer-tooltip.tooltip-below::after {
        bottom: 100%;
        border-bottom-color: #1e293b;
    }

    .tooltip-name {
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 10px;
        color: #ffffff;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        padding-bottom: 8px;
    }

    .tooltip-stats {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .tooltip-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
    }

    .tooltip-stat-label {
        color: #cbd5e1;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .tooltip-stat-value {
        font-weight: 700;
        color: #ffffff;
    }

    .tooltip-percentage {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        text-align: center;
    }

    .tooltip-percentage-value {
        font-size: 18px;
        font-weight: 700;
        color: #10b981;
    }

    /* Overlapping Avatar Styles */
    .exam-performers-mini {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        position: relative;
        padding-left: 0;
    }

    .performer-mini-item {
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .performer-avatar-mini {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-weight: 700;
        font-size: 13px;
        border: 3px solid #ffffff;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .performer-mini-item:hover .performer-avatar-mini {
        transform: scale(1.2) translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        border-width: 4px;
        z-index: 1000;
    }

    .performer-mini-item:hover {
        z-index: 1000 !important;
    }

    .performer-tooltip-mini {
        position: fixed;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #ffffff;
        padding: 14px 16px;
        border-radius: 8px;
        min-width: 220px;
        max-width: 280px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
        z-index: 99999;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        font-size: 12px;
        white-space: normal;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transform-origin: bottom center;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <div class="nav-search" id="nav-search">
                    <div class="exam-quick-actions-group">
                        <?php foreach ($modules as $module): ?>
                            <button type="button"
                                class="btn btn-sm btn-white btn-primary ajax-module"
                                data-url="<?= Url::to([$module['controller']]) ?>"
                                style="font-size:12px;margin-left:4px;margin-bottom:4px;">
                                <i class="<?= Html::encode($module['icon']) ?>"></i>
                                <?= Html::encode($module['name']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </ul>
        </div>
        <div id="module-content">
             
        </div>
           
    </div>
</div>

<script>
$(document).ready(function(){
    $('.ajax-module').on('click', function(e){
        e.preventDefault();
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
</script>
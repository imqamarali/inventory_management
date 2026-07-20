<?php

/**
 * Coming Soon Template for Student Features
 * This template is used by all student module views
 */

use yii\helpers\Html;

$title = $title ?? 'Feature';
$icon = $icon ?? 'fa fa-clock-o';
$description = $description ?? 'This feature is coming soon.';
?>

<div class="page-content">
    <div class="row">
        <div class="col-xs-12">
            <div class="coming-soon-container"
                style="max-width: 600px; margin: 0 auto; text-align: center;  background: white; border-radius: 12px;">
                <div class="coming-soon-icon"
                    style="width: 120px; height: 120px; margin: 0 auto 30px; background: linear-gradient(135deg, #438EB9 0%, #2E7CB5 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 30px rgba(67, 142, 185, 0.3);">
                    <i class="<?= $icon ?>" style="font-size: 50px; color: white;"></i>
                </div>

                <h2 style="color: #2E7CB5; font-size: 28px; font-weight: 700; margin-bottom: 15px;">
                    <?= Html::encode($title) ?>
                </h2>

                <p style="color: #6c757d; font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
                    <?= Html::encode($description) ?>
                </p>

                <div class="coming-soon-badge"
                    style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #438EB9 0%, #2E7CB5 100%); color: white; border-radius: 50px; font-weight: 600; font-size: 14px; box-shadow: 0 4px 15px rgba(67, 142, 185, 0.3);">
                    <i class="fa fa-hourglass-half"></i> Coming Soon
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    .coming-soon-icon {
        animation: pulse 2s ease-in-out infinite;
    }
</style>

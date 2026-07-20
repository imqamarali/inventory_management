<?php

use yii\helpers\Html;

/** @var array $controllerInfo */
/** @var string $tableName */
/** @var array $columns */
/** @var array $rows */

$controllerTitle = $controllerInfo['title'] ?? $controllerInfo['id'] ?? 'Controller';

?>

<div class="main-content super-system-page">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=site/index">Home</a>
                </li>
                <li>
                    <a href="index.php?r=super/index">
                        Controllers Database Overview
                    </a>
                </li>
                <li class="active">
                    <?= Html::encode($controllerTitle) ?> / <?= Html::encode($tableName) ?>
                </li>
            </ul>
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="super-summary-card">
                        <div class="super-summary-header">
                            <i class="ace-icon fa fa-table"></i>
                            <span>
                                Table:
                                <?= Html::encode($tableName) ?>
                            </span>
                        </div>
                        <div class="super-summary-body" style="overflow-x: auto;">
                            <?php if (!empty($columns) && !empty($rows)): ?>
                                <table class="table table-striped table-bordered table-hover" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <?php foreach ($columns as $col): ?>
                                                <th><?= Html::encode($col) ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rows as $row): ?>
                                            <tr>
                                                <?php foreach ($columns as $col): ?>
                                                    <td><?= Html::encode($row[$col] ?? '') ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <p style="font-size: 11px; color: #777; margin: 4px 0 0;">
                                    Showing latest 50 records ordered by the first column.
                                </p>
                            <?php elseif (!empty($columns)): ?>
                                <p style="font-size: 12px; color: #777; margin: 0;">
                                    <i class="fa fa-info-circle"></i>
                                    No records found in this table.
                                </p>
                            <?php else: ?>
                                <p style="font-size: 12px; color: #d9534f; margin: 0;">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    Could not load table structure.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div><!-- /.main-content -->
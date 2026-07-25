<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'System Management';
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li><i class="ace-icon fa fa-home home-icon"></i> <a href="index.php?r=inventory/dashboard">Home</a></li>
                <li class="active">System Management</li>
            </ul>
        </div>

        <div class="widget-main" style="padding: 20px;">
            <h2>System Management Console</h2>
            <p>Manage database backups, perform system tests, and monitor performance.</p>
            <hr />

            <div class="row" style="margin-top: 30px;">
                <!-- Database Backup Card -->
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <div style="background: white; border: 1px solid #ddd; border-radius: 4px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                            <i class="fa fa-database" style="font-size: 32px; color: #2196f3; margin-right: 15px;"></i>
                            <h3 style="margin: 0;">Database Backup</h3>
                        </div>
                        <p>Create, manage, and restore database backups. Automatic pre-restore backups ensure data safety.</p>
                        <div style="background: #f5f5f5; padding: 10px; border-radius: 3px; margin-bottom: 15px; font-size: 12px;">
                            <strong>Features:</strong>
                            <ul style="margin: 5px 0; padding-left: 20px;">
                                <li>One-click backup creation</li>
                                <li>Multiple backup management</li>
                                <li>Safe restore with pre-backup</li>
                                <li>Download backups</li>
                            </ul>
                        </div>
                        <a href="index.php?r=system/backup" class="btn btn-primary" style="width: 100%;">
                            <i class="fa fa-arrow-right"></i> Go to Backup Manager
                        </a>
                    </div>
                </div>

                <!-- System Performance Card -->
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <div style="background: white; border: 1px solid #ddd; border-radius: 4px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                            <i class="fa fa-line-chart" style="font-size: 32px; color: #4caf50; margin-right: 15px;"></i>
                            <h3 style="margin: 0;">System Performance</h3>
                        </div>
                        <p>Run comprehensive system tests to check database health, screen rendering, and performance metrics.</p>
                        <div style="background: #f5f5f5; padding: 10px; border-radius: 3px; margin-bottom: 15px; font-size: 12px;">
                            <strong>Tests Available:</strong>
                            <ul style="margin: 5px 0; padding-left: 20px;">
                                <li>Database connectivity</li>
                                <li>Screen rendering tests</li>
                                <li>CRUD operation tests</li>
                                <li>Performance metrics</li>
                                <li>Data integrity checks</li>
                            </ul>
                        </div>
                        <a href="index.php?r=system/systemperformance" class="btn btn-success" style="width: 100%;">
                            <i class="fa fa-arrow-right"></i> Go to Performance Tests
                        </a>
                    </div>
                </div>
            </div>

            <!-- Info Section -->
            <div style="margin-top: 30px; background: #e8f4f8; padding: 20px; border-radius: 4px; border-left: 4px solid #2196f3;">
                <h4>System Information</h4>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>PHP Version:</strong> <?= phpversion() ?></p>
                        <p><strong>MySQL Version:</strong> <?php
                            try {
                                $db = \Yii::$app->db;
                                $version = $db->createCommand('SELECT VERSION()')->queryScalar();
                                echo $version;
                            } catch (\Exception $e) {
                                echo 'Unable to fetch';
                            }
                        ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></p>
                        <p><strong>Current Time:</strong> <?= date('Y-m-d H:i:s') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div id="toastBox"></div>
    </div>
</div>

<style>
    .widget-main {
        background: white;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
</style>

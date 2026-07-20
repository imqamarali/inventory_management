<?php
use yii\helpers\Html;

if (!isset($modules)) $modules = [];
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li class="active">Reports Center</li>
            </ul>
        </div>

        <div style="padding:20px;">
            <h3 style="margin-bottom:20px; color:#333;">
                <i class="ace-icon fa fa-report"></i> Report Center
            </h3>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px;">
                <?php foreach ($modules as $module) { ?>
                    <div style="background:#fff; border:1px solid #ddd; border-radius:6px; overflow:hidden; transition:all 0.3s; cursor:pointer;"
                         onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'"
                         onmouseout="this.style.boxShadow='none'"
                         onclick="window.location.href='index.php?r=<?= Html::encode($module['controller']) ?>'">

                        <div style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding:40px 20px; text-align:center; color:#fff;">
                            <i class="<?= $module['icon'] ?>" style="font-size:48px; display:block; margin-bottom:10px;"></i>
                            <h4 style="margin:0; font-size:18px; font-weight:600;"><?= Html::encode($module['name']) ?></h4>
                        </div>

                        <div style="padding:15px; text-align:center;">
                            <p style="margin:0; color:#666; font-size:13px;">Click to view this report</p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<style>
    .main-content {
        background:#f5f5f5;
        min-height:100vh;
    }
</style>

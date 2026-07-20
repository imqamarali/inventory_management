<?php

use yii\helpers\Html;
use yii\helpers\Url;
if(!isset($modules) && empty($modules))
{
    $modules=[];
}
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb"> 
                <div class="nav-search" id="nav-search">
                    <div class="exam-quick-actions-group"> 
                        <?php foreach ($modules as $module): ?>
                            <a href="<?= Url::to([$module['controller']]) ?>"
                               class="btn btn-sm btn-white btn-primary ajax-module"
                               style="font-size:12px;margin-left:4px;margin-bottom:4px;">
                                <i class="<?= $module['icon'] ?>"></i>
                                <?= Html::encode($module['name']) ?>
                            </a>
                        <?php endforeach; ?> 
                    </div>
                </div>
            </ul>
        </div> 
        <div class="page-content">
            <div class="row">
                <div id="module-content"> 
                </div>
            </div>
        </div>

    </div>
</div>


<script>
$(document).ready(function () {

    $('.ajax-module').on('click', function (e) {
        e.preventDefault();

        var url = $(this).attr('href');

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html',

            beforeSend: function () {
                $('#module-content').html(
                    '<div class="text-center" style="padding:20px;">' +
                    '<i class="fa fa-spinner fa-spin"></i> Loading...' +
                    '</div>'
                );
            },

            success: function (response) {
                $('#module-content').html(response);
            },

            error: function (xhr, status, error) {
                $('#module-content').html(
                    '<div class="alert alert-danger">' +
                    'Unable to load data.' +
                    '</div>'
                );
                console.log(error);
            }
        });

    });

});
</script>



<?php

use yii\helpers\Html;
use yii\helpers\Url;

if (!isset($modules) && empty($modules)) {
    $modules = [];
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="main-content">
    <div class="main-content-inner">
        <div class="" id="breadcrumbs">
            <ul class="">
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
        <?php if(isset($twolines)){ ?> 
            <div id="module-content" style="margin-top: 70px;"></div>
        <?php }else{    ?>
            <div id="module-content" style="margin-top: 40px;"></div>
        <?php } ?>
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
</script>

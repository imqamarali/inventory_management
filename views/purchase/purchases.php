<?php use yii\helpers\Html; use yii\helpers\Url; if(!isset($modules))$modules=[]; ?>
<div class="container-fluid pt-4">
    <div class="row mb-4">
        <div class="col"><h2><i class="fa fa-shopping-cart"></i> Purchase Management</h2></div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="row" id="moduleCards"><?php foreach($modules as $m):?><div class="col-md-3 mb-3"><div class="card h-100 module-card" onclick="loadModule('<?=Yii::$app->urlManager->createUrl($m['controller']??'')?>','<?=Html::encode($m['name']??'')?>','<?=Html::encode($m['icon']??'')?>')" style="cursor:pointer;"><div class="card-body text-center"><h5 class="card-icon" style="font-size:2.5em;"><i class="<?=Html::encode($m['icon']??'')?>"></i></h5><h6 class="card-title"><?=Html::encode($m['name']??'')?></h6><small class="text-muted"><?=Html::encode($m['description']??'')?></small></div></div></div><?php endforeach;?></div>
        </div>
    </div>
    <div id="moduleContent" style="margin-top:30px;display:none;"></div>
</div>
<script>
function htmlEscape(t){if(!t)return'';const m={'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};return String(t).replace(/[&<>"']/g,c=>m[c]);}
function loadModule(url,name,icon){if(!url)return;$('#moduleContent').html('<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading...</p></div>').show();$.ajax({url:url,type:'GET',dataType:'html',timeout:5000,success:function(r){$('#moduleContent').html(r);$('html,body').animate({scrollTop:$('#moduleContent').offset().top-100},500);},error:function(x,s){$('#moduleContent').html('<div class="alert alert-danger"><i class="fa fa-warning"></i> '+htmlEscape(s==='timeout'?'Timeout loading '+name:'Error loading '+name)+'</div>');}});}
</script>
<style>
.module-card:hover{transform:translateY(-5px);box-shadow:0 5px 15px rgba(0,0,0,0.2);transition:all 0.3s ease;}
.module-card{border:1px solid #e0e0e0;background:#f9f9f9;}
</style>
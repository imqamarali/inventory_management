<?php
use yii\helpers\Html;
if (!isset($units)) {
    $units = [];
}
?>
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li class="active">Units</li>
                <li style="width:50%;text-align:center;">
                    <input type="text" id="unitSearch" class="form-control"
                        placeholder="Search Units..."
                        style="display:inline-block;width:300px;height:28px;font-size:12px;">
                </li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary"
                                style="font-size:12px;cursor:pointer;"
                                onclick="openUnitModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add New Unit
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="widget-body">
                        <div class="widget-main padding-12">
                            <?php if(count($units)==0){ ?>
                                <div class="alert alert-info text-center">
                                    <i class="ace-icon fa fa-info-circle fa-3x" style="color:#6FB3E0;"></i>
                                    <h4 style="margin-top:15px;">No Units Found</h4>
                                    <p>Start by adding your first unit using the button above</p>
                                </div>
                            <?php }else{ ?>
                                <div class="row" id="units_container">
                                    <?php foreach($units as $item){ ?>
                                        <div class="col-md-4 col-sm-6 session-item unit-item" data-id="<?= $item['id'] ?>">
                                            <div class="class-card">
                                                <div class="class-header">
                                                    <div style="flex:1;">
                                                        <div class="class-name">
                                                            <i class="fa fa-balance-scale" style="margin-right:8px;"></i>
                                                            <?= htmlspecialchars($item['unit_name']??"NA") ?>
                                                        </div>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button type="button" onclick='openUnitModal(<?= json_encode($item) ?>)' title="Edit Unit">
                                                            <i class="ace-icon fa fa-pencil"></i>
                                                        </button>
                                                        &nbsp;&nbsp;|&nbsp;&nbsp;
                                                        <button type="button" onclick="deleteUnit(<?= $item['id'] ?>)" title="Delete Unit">
                                                            <i class="ace-icon fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="class-stats">
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-tag"></i>
                                                        <span>Short Name: <?= htmlspecialchars($item['short_name']??"NA") ?></span>
                                                    </div>
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-align-left"></i>
                                                        <span>Description: <?= htmlspecialchars($item['description']??"NA") ?></span>
                                                    </div>
                                                </div>
                                                <div style="margin-top:10px;padding-top:10px;border-top:1px solid #E3E9ED;">
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-calendar"></i>
                                                        <span>Created: <?= htmlspecialchars($item['created_at']??"NA") ?></span>
                                                    </div>
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-refresh"></i>
                                                        <span>Updated: <?= htmlspecialchars($item['updated_at']??"NA") ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('unitSearch').addEventListener('keyup',function(){
    let value=this.value.toLowerCase();
    document.querySelectorAll('.unit-item').forEach(function(item){
        item.style.display=item.innerText.toLowerCase().includes(value)?'':'none';
    });
});
function openUnitModal(unitData=null){
    const isEdit=unitData!==null;
    const title=isEdit?'Update Unit':'New Unit';
    const id=isEdit?(unitData.id||''):'';
    const unitName=isEdit?(unitData.unit_name||''):'';
    const shortName=isEdit?(unitData.short_name||''):'';
    const description=isEdit?(unitData.description||''):'';
    const isActive=isEdit&&(unitData.is_active==1||unitData.is_active=='1');

    Swal.fire({
        title:title,
        html:`
        <form style="text-align:left;">
        <input type="hidden" id="swal_unit_id" value="${id}">
        <div class="row">
        <div class="col-md-6">
        <label>Unit Name <span class="text-danger">*</span></label>
        <input type="text" id="swal_unit_name" class="form-control" value="${unitName}">
        </div>
        <div class="col-md-6">
        <label>Short Name</label>
        <input type="text" id="swal_short_name" class="form-control" value="${shortName}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-12">
        <label>Description</label>
        <input type="text" id="swal_description" class="form-control" value="${description}">
        </div>
        </div>
        <div class="form-group" style="margin-top:10px;">
        <label>
        <input type="checkbox" id="swal_active" ${isActive?'checked':''}>
        Active
        </label>
        </div>
        </form>`,
        width:'700px',
        showCancelButton:true,
        confirmButtonText:isEdit?'<i class="ace-icon fa fa-save"></i> Update Unit':'<i class="ace-icon fa fa-save"></i> Create Unit',
        cancelButtonText:'<i class="ace-icon fa fa-times"></i> Cancel',
        confirmButtonColor:'#87B87F',
        cancelButtonColor:'#6c757d',
        preConfirm:()=>{
            let name=document.getElementById('swal_unit_name').value.trim();
            if(!name){
                Swal.showValidationMessage('Unit name is required');
                return false;
            }
            return {
                id:document.getElementById('swal_unit_id').value,
                unit_name:name,
                short_name:document.getElementById('swal_short_name').value,
                description:document.getElementById('swal_description').value,
                active:document.getElementById('swal_active').checked
            };
        }
    }).then((result)=>{
        if(result.isConfirmed&&result.value){
            saveUnit(result.value);
        }
    });
}
function saveUnit(formData){
    Swal.fire({
        title:'Processing...',
        showConfirmButton:false,
        allowOutsideClick:false,
        didOpen:()=>{Swal.showLoading();}
    });
    const data=new FormData();
    data.append('_csrf','<?= Yii::$app->request->getCsrfToken() ?>');
    data.append('id',formData.id);
    data.append('unit_name',formData.unit_name);
    data.append('short_name',formData.short_name);
    data.append('description',formData.description);
    if(formData.active){
        data.append('is_active','1');
    }
    fetch('index.php?r=products/units',{
        method:'POST',
        body:data
    })
    .then(response=>response.json())
    .then(data=>{
        if(data.success){
            Swal.fire({
                icon:'success',
                title:'Success!',
                text:data.message,
                timer:1500,
                showConfirmButton:false
            }).then(()=>{
                $('.ajax-module.active').trigger('click');
            });
        }else{
            Swal.fire('Error',data.message,'error');
        }
    });
}
function deleteUnit(id){
    Swal.fire({
        title:'Are you sure?',
        text:'Unit will be deleted.',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#d33',
        cancelButtonColor:'#3085d6',
        confirmButtonText:'Yes, delete it!'
    }).then((result)=>{
        if(result.isConfirmed){
            const data=new FormData();
            data.append('_csrf','<?= Yii::$app->request->getCsrfToken() ?>');
            data.append('id',id);
            data.append('delete','1');
            fetch('index.php?r=products/units',{
                method:'POST',
                body:data
            })
            .then(response=>response.json())
            .then(data=>{
                if(data.success){
                    Swal.fire({
                        icon:'success',
                        title:'Success!',
                        text:data.message,
                        timer:1500,
                        showConfirmButton:false
                    }).then(()=>{
                        $('.ajax-module.active').trigger('click');
                    });
                }else{
                    Swal.fire('Error',data.message,'error');
                }
            });
        }
    });
}
</script>
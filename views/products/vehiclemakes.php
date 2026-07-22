<?php
use yii\helpers\Html;
if (!isset($vehiclemakes)) {
    $vehiclemakes = [];
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
                <li class="active">Vehicle Makes</li>
                <li style="width:50%;text-align:center;">
                    <input type="text" id="vehicleMakeSearch" class="form-control"
                        placeholder="Search Vehicle Makes..."
                        style="display:inline-block;width:300px;height:28px;font-size:12px;">
                </li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary"
                                style="font-size:12px;cursor:pointer;"
                                onclick="openVehicleMakeModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add New Vehicle Make
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
                            <?php if (count($vehiclemakes) == 0) { ?>
                                <div class="alert alert-info text-center">
                                    <i class="ace-icon fa fa-info-circle fa-3x" style="color:#6FB3E0;"></i>
                                    <h4 style="margin-top:15px;">No Vehicle Makes Found</h4>
                                    <p>Start by adding your first vehicle make using the button above</p>
                                </div>
                            <?php } else { ?>
                                <div class="row" id="vehiclemakes_container">
                                    <?php foreach ($vehiclemakes as $item): ?>
                                        <div class="col-md-4 col-sm-6 session-item vehiclemake-item">
                                            <div class="class-card">
                                                <div class="class-header">
                                                    <div style="flex:1;">
                                                        <div class="class-name">
                                                            <i class="fa fa-car" style="margin-right:8px;"></i>
                                                            <?= htmlspecialchars($item['make_name']??"NA") ?>
                                                        </div>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            onclick="openVehicleMakeModal(<?= htmlspecialchars(json_encode($item)) ?>)"
                                                            title="Edit Vehicle Make">
                                                            <i class="ace-icon fa fa-pencil"></i>
                                                        </button>
                                                        &nbsp;&nbsp;|&nbsp;&nbsp;
                                                        <button type="button"
                                                            onclick="deleteVehicleMake(<?= $item['id'] ?>)"
                                                            title="Delete Vehicle Make">
                                                            <i class="ace-icon fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="class-stats">
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-barcode"></i>
                                                        <span>Code: <?= htmlspecialchars($item['make_code']??"NA") ?></span>
                                                    </div>
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-globe"></i>
                                                        <span>Country: <?= htmlspecialchars($item['country']??"NA") ?></span>
                                                    </div>
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-link"></i>
                                                        <span>Website: <?= htmlspecialchars($item['website']??"NA") ?></span>
                                                    </div>
                                                </div>
                                                <div style="margin-top:10px;padding-top:10px;border-top:1px solid #E3E9ED;">
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-sticky-note"></i>
                                                        <span>Notes: <?= htmlspecialchars($item['notes']??"NA") ?></span>
                                                    </div>
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-calendar"></i>
                                                        <span>Created: <?= htmlspecialchars($item['created_at']??"NA") ?></span>
                                                    </div>
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-refresh"></i>
                                                        <span>Updated: <?= htmlspecialchars($item['updated_at']??"NA") ?></span>
                                                    </div>
                                                </div>

                                                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #E3E9ED; display: flex; justify-content: space-around;">

                                                    <div class="stat-mini">
                                                        <div style="font-size: 18px; font-weight: bold; color: #2196F3;">
                                                            <?= $item['total_products'] ?? 0 ?>
                                                        </div>
                                                        <div style="font-size: 12px; color: #666;">Total Products</div>
                                                    </div>

                                                    <div class="stat-mini">
                                                        <div style="font-size: 18px; font-weight: bold; color: #4CAF50;">
                                                            <?= $item['active_products'] ?? 0 ?>
                                                        </div>
                                                        <div style="font-size: 12px; color: #666;">Active</div>
                                                    </div>

                                                    <div class="stat-mini">
                                                        <div style="font-size: 18px; font-weight: bold; color: #FF9800;">
                                                            <?= $item['inactive_products'] ?? 0 ?>
                                                        </div>
                                                        <div style="font-size: 12px; color: #666;">Inactive</div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
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
document.getElementById('vehicleMakeSearch').addEventListener('keyup',function(){
    let value=this.value.toLowerCase();
    document.querySelectorAll('.vehiclemake-item').forEach(function(item){
        item.style.display=item.innerText.toLowerCase().includes(value)?'':'none';
    });
});
function openVehicleMakeModal(vehicleMakeData=null){
    const isEdit=vehicleMakeData!==null;
    const title=isEdit?'Update Vehicle Make':'New Vehicle Make';
    const id=isEdit?(vehicleMakeData.id||''):'';
    const makeName=isEdit?(vehicleMakeData.make_name||''):'';
    const makeCode=isEdit?(vehicleMakeData.make_code||''):'';
    const country=isEdit?(vehicleMakeData.country||''):'';
    const website=isEdit?(vehicleMakeData.website||''):'';
    const notes=isEdit?(vehicleMakeData.notes||''):'';
    const isActive=isEdit&&(vehicleMakeData.is_active==1||vehicleMakeData.is_active=='1');

    Swal.fire({
        title:title,
        html:`
        <form style="text-align:left;">
        <input type="hidden" id="swal_vehicle_make_id" value="${id}">
        <div class="row">
        <div class="col-md-6">
        <label>Make Name <span class="text-danger">*</span></label>
        <input type="text" id="swal_make_name" class="form-control" value="${makeName}">
        </div>
        <div class="col-md-6">
        <label>Make Code</label>
        <input type="text" id="swal_make_code" class="form-control" value="${makeCode}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
        <label>Country</label>
        <input type="text" id="swal_country" class="form-control" value="${country}">
        </div>
        <div class="col-md-6">
        <label>Website</label>
        <input type="text" id="swal_website" class="form-control" value="${website}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-12">
        <label>Notes</label>
        <input type="text" id="swal_notes" class="form-control" value="${notes}">
        </div>
        </div>
        <div class="form-group" style="margin-top:10px;">
        <label>
        <input type="checkbox" id="swal_active" ${isActive?'checked':''}>
        Active
        </label>
        </div>
        </form>
        `,
        width:'700px',
        showCancelButton:true,
        confirmButtonText:isEdit?'<i class="ace-icon fa fa-save"></i> Update Vehicle Make':'<i class="ace-icon fa fa-save"></i> Create Vehicle Make',
        cancelButtonText:'<i class="ace-icon fa fa-times"></i> Cancel',
        confirmButtonColor:'#87B87F',
        cancelButtonColor:'#6c757d',
        focusConfirm:false,
        preConfirm:()=>{
            const name=document.getElementById('swal_make_name').value.trim();
            if(!name){
                Swal.showValidationMessage('Make name is required');
                return false;
            }
            return {
                id:document.getElementById('swal_vehicle_make_id').value,
                make_name:name,
                make_code:document.getElementById('swal_make_code').value,
                country:document.getElementById('swal_country').value,
                website:document.getElementById('swal_website').value,
                notes:document.getElementById('swal_notes').value,
                active:document.getElementById('swal_active').checked
            };
        }
    }).then(result=>{
        if(result.isConfirmed&&result.value){
            saveVehicleMake(result.value);
        }
    });
}

function saveVehicleMake(formData){
    Swal.fire({
        title:'Processing...',
        text:'Please wait',
        allowOutsideClick:false,
        showConfirmButton:false,
        didOpen:()=>{Swal.showLoading();}
    });

    const data=new FormData();
    data.append('_csrf','<?= Yii::$app->request->getCsrfToken() ?>');
    data.append('id',formData.id);
    data.append('make_name',formData.make_name);
    data.append('make_code',formData.make_code);
    data.append('country',formData.country);
    data.append('website',formData.website);
    data.append('notes',formData.notes);
    if(formData.active){
        data.append('is_active','1');
    }

    fetch('index.php?r=products/vehiclemakes',{
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
    })
    .catch(()=>{
        Swal.fire('Error','An error occurred. Please try again.','error');
    });
}

function deleteVehicleMake(id){
    Swal.fire({
        title:'Are you sure?',
        text:'Vehicle make will be deleted.',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#d33',
        cancelButtonColor:'#3085d6',
        confirmButtonText:'Yes, delete it!'
    }).then(result=>{
        if(result.isConfirmed){
            const data=new FormData();
            data.append('_csrf','<?= Yii::$app->request->getCsrfToken() ?>');
            data.append('id',id);
            data.append('delete','1');

            fetch('index.php?r=products/vehiclemakes',{
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
<?php
use yii\helpers\Html;
if (!isset($vehiclemodels)) {
    $vehiclemodels = [];
}
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
                <li class="active">Vehicle Models</li>
                <li style="width:50%;text-align:center;">
                    <input type="text" id="vehicleModelSearch" class="form-control"
                        placeholder="Search Vehicle Models..."
                        style="display:inline-block;width:300px;height:28px;font-size:12px;">
                </li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary"
                                style="font-size:12px;cursor:pointer;"
                                onclick="openVehicleModelModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add New Vehicle Model
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
                            <?php if (count($vehiclemodels) == 0) { ?>
                                <div class="alert alert-info text-center">
                                    <i class="ace-icon fa fa-info-circle fa-3x" style="color:#6FB3E0;"></i>
                                    <h4 style="margin-top:15px;">No Vehicle Models Found</h4>
                                    <p>Start by adding your first vehicle model using the button above</p>
                                </div>
                            <?php } else { ?>
                                <div class="row" id="vehiclemodels_container">
                                    <?php foreach ($vehiclemodels as $item): ?>
                                        <div class="col-md-4 col-sm-6 session-item vehiclemodel-item">
                                            <div class="class-card">
                                                <div class="class-header">
                                                    <div style="flex:1;">
                                                        <div class="class-name">
                                                            <i class="fa fa-car" style="margin-right:8px;"></i>
                                                            <?= htmlspecialchars($item['model_name']??"NA") ?>
                                                        </div>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            onclick="openVehicleModelModal(<?= htmlspecialchars(json_encode($item)) ?>)"
                                                            title="Edit Vehicle Model">
                                                            <i class="ace-icon fa fa-pencil"></i>
                                                        </button>
                                                        &nbsp;&nbsp;|&nbsp;&nbsp;
                                                        <button type="button"
                                                            onclick="deleteVehicleModel(<?= $item['id'] ?>)"
                                                            title="Delete Vehicle Model">
                                                            <i class="ace-icon fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="class-stats">
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-car"></i>
                                                        <span>Make: <?= htmlspecialchars($item['make_name']??"NA") ?></span>
                                                    </div>
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-barcode"></i>
                                                        <span>Code: <?= htmlspecialchars($item['model_code']??"NA") ?></span>
                                                    </div>
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-calendar"></i>
                                                        <span>Year: <?= htmlspecialchars($item['model_year']??"NA") ?></span>
                                                    </div>
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-cogs"></i>
                                                        <span>Engine: <?= htmlspecialchars($item['engine_type']??"NA") ?></span>
                                                    </div>
                                                </div>
                                                <div style="margin-top:10px;padding-top:10px;border-top:1px solid #E3E9ED;">
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-tachometer"></i>
                                                        <span>Capacity: <?= htmlspecialchars($item['engine_capacity']??"NA") ?></span>
                                                    </div>
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-flash"></i>
                                                        <span>Fuel: <?= htmlspecialchars($item['fuel_type']??"NA") ?></span>
                                                    </div>
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-gears"></i>
                                                        <span>Transmission: <?= htmlspecialchars($item['transmission']??"NA") ?></span>
                                                    </div>
                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-sticky-note"></i>
                                                        <span>Notes: <?= htmlspecialchars($item['notes']??"NA") ?></span>
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
    
document.getElementById('vehicleModelSearch').addEventListener('keyup',function(){
    let value=this.value.toLowerCase();
    document.querySelectorAll('.vehiclemodel-item').forEach(function(item){
        item.style.display=item.innerText.toLowerCase().includes(value)?'':'none';
    });
});
function openVehicleModelModal(vehicleModelData=null){
    const isEdit=vehicleModelData!==null;
    const title=isEdit?'Update Vehicle Model':'New Vehicle Model';
    const id=isEdit?(vehicleModelData.id||''):'';
    const makeId=isEdit?(vehicleModelData.make_id||''):'';
    const modelName=isEdit?(vehicleModelData.model_name||''):'';
    const modelCode=isEdit?(vehicleModelData.model_code||''):'';
    const modelYear=isEdit?(vehicleModelData.model_year||''):'';
    const engineType=isEdit?(vehicleModelData.engine_type||''):'';
    const engineCapacity=isEdit?(vehicleModelData.engine_capacity||''):'';
    const fuelType=isEdit?(vehicleModelData.fuel_type||''):'';
    const transmission=isEdit?(vehicleModelData.transmission||''):'';
    const notes=isEdit?(vehicleModelData.notes||''):'';
    const isActive=isEdit&&(vehicleModelData.is_active==1||vehicleModelData.is_active=='1');

    Swal.fire({
        title:title,
        html:`
        <form style="text-align:left;">
        <input type="hidden" id="swal_vehicle_model_id" value="${id}">
        <div class="row">
        <div class="col-md-6">
        <label>Vehicle Make <span class="text-danger">*</span></label>
        <select id="swal_make_id" class="form-control">
        <?php foreach($vehiclemakes as $make): ?>
        <option value="<?= $make['id'] ?>" ${makeId=='<?= $make['id'] ?>'?'selected':''}>
        <?= htmlspecialchars($make['make_name']) ?>
        </option>
        <?php endforeach; ?>
        </select>
        </div>
        <div class="col-md-6">
        <label>Model Name <span class="text-danger">*</span></label>
        <input type="text" id="swal_model_name" class="form-control" value="${modelName}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
        <label>Model Code</label>
        <input type="text" id="swal_model_code" class="form-control" value="${modelCode}">
        </div>
        <div class="col-md-6">
        <label>Model Year</label>
        <input type="tezt" id="swal_model_year" class="form-control" value="${modelYear}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
        <label>Engine Type</label>
        <input type="text" id="swal_engine_type" class="form-control" value="${engineType}">
        </div>
        <div class="col-md-6">
        <label>Engine Capacity</label>
        <input type="text" id="swal_engine_capacity" class="form-control" value="${engineCapacity}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
        <label>Fuel Type</label>
        <input type="text" id="swal_fuel_type" class="form-control" value="${fuelType}">
        </div>
        <div class="col-md-6">
        <label>Transmission</label>
        <input type="text" id="swal_transmission" class="form-control" value="${transmission}">
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
        confirmButtonText:isEdit?'<i class="ace-icon fa fa-save"></i> Update Vehicle Model':'<i class="ace-icon fa fa-save"></i> Create Vehicle Model',
        cancelButtonText:'<i class="ace-icon fa fa-times"></i> Cancel',
        confirmButtonColor:'#87B87F',
        cancelButtonColor:'#6c757d',
        focusConfirm:false,
        preConfirm:()=>{
            const name=document.getElementById('swal_model_name').value.trim();
            if(!name){
                Swal.showValidationMessage('Model name is required');
                return false;
            }
            return {
                id:document.getElementById('swal_vehicle_model_id').value,
                make_id:document.getElementById('swal_make_id').value,
                model_name:name,
                model_code:document.getElementById('swal_model_code').value,
                model_year:document.getElementById('swal_model_year').value,
                engine_type:document.getElementById('swal_engine_type').value,
                engine_capacity:document.getElementById('swal_engine_capacity').value,
                fuel_type:document.getElementById('swal_fuel_type').value,
                transmission:document.getElementById('swal_transmission').value,
                notes:document.getElementById('swal_notes').value,
                active:document.getElementById('swal_active').checked
            };
        }
    }).then(result=>{
        if(result.isConfirmed&&result.value){
            saveVehicleModel(result.value);
        }
    });
}

function saveVehicleModel(formData){
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
    data.append('make_id',formData.make_id);
    data.append('model_name',formData.model_name);
    data.append('model_code',formData.model_code);
    data.append('model_year',formData.model_year);
    data.append('engine_type',formData.engine_type);
    data.append('engine_capacity',formData.engine_capacity);
    data.append('fuel_type',formData.fuel_type);
    data.append('transmission',formData.transmission);
    data.append('notes',formData.notes);

    if(formData.active){
        data.append('is_active','1');
    }

    fetch('index.php?r=products/vehiclemodels',{
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

function deleteVehicleModel(id){
    Swal.fire({
        title:'Are you sure?',
        text:'Vehicle model will be deleted.',
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

            fetch('index.php?r=products/vehiclemodels',{
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
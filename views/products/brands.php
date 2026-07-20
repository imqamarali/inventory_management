<?php
use yii\helpers\Html;

if (!isset($brands)) {
    $brands = [];
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
                <li class="active">Brands</li>
                <li style="width:50%;text-align:center;">
                    <input type="text" id="brandSearch" class="form-control"
                        placeholder="Search Brands..."
                        style="display:inline-block;width:300px;height:28px;font-size:12px;">
                </li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary"
                                style="font-size:12px;cursor:pointer;"
                                onclick="openBrandModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add New Brand
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
                            <?php if (count($brands)==0) { ?>
                                <div class="alert alert-info text-center">
                                    <i class="ace-icon fa fa-info-circle fa-3x" style="color:#6FB3E0;"></i>
                                    <h4 style="margin-top:15px;">No Brands Found</h4>
                                    <p>Start by adding your first brand using the button above</p>
                                </div>
                            <?php } else { ?>
                                <div class="row" id="brands_container">
                                    <?php foreach ($brands as $item): 
                                        $isActive=$item['is_active']==1;
                                        $statusClass=$isActive?'label-success':'label-danger';
                                        $statusText=$isActive?'Active':'Inactive';
                                    ?>
                                    <div class="col-md-4 col-sm-6 session-item brand-item">
                                        <div class="class-card">
                                            <div class="class-header">
                                                <div style="flex:1;">
                                                    <div class="class-name">
                                                        <i class="fa fa-tags" style="margin-right:8px;"></i>
                                                        <?= htmlspecialchars($item['brand_name']??"N/A") ?>
                                                        <span class="label <?= $statusClass ?>" style="margin-left:8px;font-size:10px;">
                                                            <?= $statusText ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="btn-group">
                                                    <button type="button"
                                                        onclick="openBrandModal(<?= htmlspecialchars(json_encode($item)) ?>)"
                                                        title="Edit Brand">
                                                        <i class="ace-icon fa fa-pencil"></i>
                                                    </button>
                                                    &nbsp;&nbsp;|&nbsp;&nbsp;
                                                    <button type="button"
                                                        onclick="deleteBrand(<?= $item['id'] ?>)"
                                                        title="Delete Brand">
                                                        <i class="ace-icon fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="class-stats">
                                                <div class="stat-item">
                                                    <i class="ace-icon fa fa-barcode"></i>
                                                    <span>Code: <?= htmlspecialchars($item['brand_code']??"N/A") ?></span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="ace-icon fa fa-globe"></i>
                                                    <span>Website: <?= htmlspecialchars($item['website']??"N/A") ?></span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="ace-icon fa fa-envelope"></i>
                                                    <span>Email: <?= htmlspecialchars($item['email']??"N/A") ?></span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="ace-icon fa fa-phone"></i>
                                                    <span>Phone: <?= htmlspecialchars($item['phone']??"N/A") ?></span>
                                                </div>
                                            </div>
                                            <div style="margin-top:10px;padding-top:10px;border-top:1px solid #E3E9ED;">
                                                
                                                <div class="stat-item">
                                                    <i class="ace-icon fa fa-comment"></i>
                                                    <span>Notes: <?= htmlspecialchars($item['notes']??"N/A") ?></span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="ace-icon fa fa-calendar"></i>
                                                    <span>Created: <?= htmlspecialchars($item['created_at']??"N/A") ?></span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="ace-icon fa fa-refresh"></i>
                                                    <span>Updated: <?= htmlspecialchars($item['updated_at']??"N/A") ?></span>
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
document.getElementById('brandSearch').addEventListener('keyup',function(){
    let value=this.value.toLowerCase();
    document.querySelectorAll('.brand-item').forEach(function(item){
        item.style.display=item.innerText.toLowerCase().includes(value)?'':'none';
    });
});

function openBrandModal(brandData=null){
    const isEdit=brandData!==null;
    const title=isEdit?'Update Brand':'New Brand';
    const id=isEdit?(brandData.id||''):'';
    const brandName=isEdit?(brandData.brand_name||''):'';
    const brandCode=isEdit?(brandData.brand_code||''):'';
    const description=isEdit?(brandData.description||''):'';
    const website=isEdit?(brandData.website||''):'';
    const email=isEdit?(brandData.email||''):'';
    const phone=isEdit?(brandData.phone||''):'';
    const notes=isEdit?(brandData.notes||''):'';
    const isActive=isEdit&&(brandData.is_active==1||brandData.is_active=='1');

    Swal.fire({
        title:title,
        html:`
        <form id="brandForm" style="text-align:left;">
        <input type="hidden" id="swal_brand_id" value="${id}">
        <div class="row">
        <div class="col-md-6">
        <label>Brand Name <span class="text-danger">*</span></label>
        <input type="text" id="swal_brand_name" class="form-control" value="${brandName}">
        </div>
        <div class="col-md-6">
        <label>Brand Code</label>
        <input type="text" id="swal_brand_code" class="form-control" value="${brandCode}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
        <label>Website</label>
        <input type="text" id="swal_website" class="form-control" value="${website}">
        </div>
        <div class="col-md-6">
        <label>Email</label>
        <input type="email" id="swal_email" class="form-control" value="${email}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
        <label>Phone</label>
        <input type="text" id="swal_phone" class="form-control" value="${phone}">
        </div>
        <div class="col-md-6">
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
        confirmButtonText:isEdit?'<i class="ace-icon fa fa-save"></i> Update Brand':'<i class="ace-icon fa fa-save"></i> Create Brand',
        cancelButtonText:'<i class="ace-icon fa fa-times"></i> Cancel',
        confirmButtonColor:'#87B87F',
        cancelButtonColor:'#6c757d',
        focusConfirm:false,
        preConfirm:()=>{
            const name=document.getElementById('swal_brand_name').value.trim();
            if(!name){
                Swal.showValidationMessage('Brand name is required');
                return false;
            }
            return {
                id:document.getElementById('swal_brand_id').value,
                brand_name:name,
                brand_code:document.getElementById('swal_brand_code').value,
                website:document.getElementById('swal_website').value,
                email:document.getElementById('swal_email').value,
                phone:document.getElementById('swal_phone').value,
                notes:document.getElementById('swal_notes').value,
                active:document.getElementById('swal_active').checked
            };
        }
    }).then((result)=>{
        if(result.isConfirmed&&result.value){
            saveBrand(result.value);
        }
    });
}

function saveBrand(formData){
    Swal.fire({
        title:'Processing...',
        text:'Please wait',
        allowOutsideClick:false,
        showConfirmButton:false,
        didOpen:()=>{
            Swal.showLoading();
        }
    });

    const data=new FormData();
    data.append('_csrf','<?= Yii::$app->request->getCsrfToken() ?>');
    data.append('id',formData.id);
    data.append('brand_name',formData.brand_name);
    data.append('brand_code',formData.brand_code);
    data.append('website',formData.website);
    data.append('email',formData.email);
    data.append('phone',formData.phone);
    data.append('notes',formData.notes);

    if(formData.active){
        data.append('is_active','1');
    }

    fetch('index.php?r=products/brands',{
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
            Swal.fire({
                icon:'error',
                title:'Error!',
                text:data.message
            });
        }
    });
}
function deleteBrand(id) {
    Swal.fire({
        title:'Are you sure?',
        text:'Brand will be deleted.',
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
            fetch('index.php?r=products/brands',{
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
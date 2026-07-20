<?php use yii\helpers\Html; if(!isset($suppliers))$suppliers=[];if(!isset($orders))$orders=[]; ?>
<div class="container-fluid pt-4">
    <div class="row mb-4">
        <div class="col"><h3><i class="fa fa-shopping-cart"></i> Purchase Orders</h3></div>
        <div class="col-auto"><button class="btn btn-primary btn-sm" onclick="openPOModal()"><i class="fa fa-plus"></i> New Order</button></div>
    </div>
    <div id="alerts-po"></div>
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3" onsubmit="return false;">
                <div class="col-md-4"><input type="text" class="form-control form-control-sm" placeholder="Search by PO #" id="poSearch"></div>
                <div class="col-md-4"><select class="form-select form-select-sm" id="supplierFilter"><option value="">All Suppliers</option><?php foreach($suppliers as $s):?><option value="<?=$s['id']?>"><?=Html::encode($s['company_name'])?></option><?php endforeach;?></select></div>
                <div class="col-md-4"><button class="btn btn-sm btn-primary" onclick="filterPO()"><i class="fa fa-search"></i> Filter</button></div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="poTable">
                <thead class="table-light"><tr><th>PO#</th><th>Supplier</th><th>Date</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
                <tbody id="poRows"><?php if(empty($orders)):?><tr><td colspan="6" class="text-center text-muted">No purchase orders</td></tr><?php else: foreach($orders as $o):?><tr><td><?=Html::encode($o['po_number']??'')?></td><td><?=Html::encode($o['company_name']??'')?></td><td><?=Html::encode($o['order_date']??'')?></td><td>$<?=number_format($o['total_amount']??0,2)?></td><td><span class="badge bg-info"><?=Html::encode($o['status']??'Pending')?></span></td><td><button class="btn btn-xs btn-primary" onclick="editPO(<?=$o['id']?>)"><i class="fa fa-edit"></i></button></td></tr><?php endforeach; endif;?></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="poModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5>Purchase Order</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <form id="poForm">
                <input type="hidden" id="poId" name="id">
                <div class="mb-2"><label class="form-label">Supplier<span class="text-danger">*</span></label><select class="form-select form-select-sm" id="poSupplier" name="supplier_id"><?php foreach($suppliers as $s):?><option value="<?=$s['id']?>"><?=Html::encode($s['company_name'])?></option><?php endforeach;?></select></div>
                <div class="mb-2"><label class="form-label">Date<span class="text-danger">*</span></label><input type="date" class="form-control form-control-sm" id="poDate" name="order_date"></div>
                <div class="mb-2"><label class="form-label">Amount<span class="text-danger">*</span></label><input type="number" class="form-control form-control-sm" id="poAmount" name="total_amount" step="0.01"></div>
                <div class="mb-2"><label class="form-label">Status</label><select class="form-select form-select-sm" id="poStatus" name="status"><option>Pending</option><option>Approved</option><option>Received</option></select></div>
            </form>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button><button type="button" class="btn btn-primary btn-sm" onclick="savePO()">Save</button></div>
    </div></div>
</div>
<script>
function htmlEscape(t){if(!t)return'';const m={'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};return String(t).replace(/[&<>"']/g,c=>m[c]);}
const Storage={set:(k,v)=>{try{localStorage.setItem(k,v);}catch(e){console.warn('Storage blocked:',e.message);}},get:(k)=>{try{return localStorage.getItem(k);}catch(e){console.warn('Storage blocked:',e.message);return null;}}};
function openPOModal(){$('#poId').val('');$('#poForm')[0].reset();new bootstrap.Modal($('#poModal')).show();}
function editPO(id){if(!id)return;$.post('<?=Yii::$app->urlManager->createUrl("purchase/purchaseorders")?>',{id:id},function(r){if(r.success){$('#poId').val(r.data?.id||'');$('#poSupplier').val(r.data?.supplier_id||'');$('#poDate').val(r.data?.order_date||'');$('#poAmount').val(r.data?.total_amount||0);$('#poStatus').val(r.data?.status||'Pending');new bootstrap.Modal($('#poModal')).show();}},{'json':false});return false;}
function savePO(){let d=$('#poForm').serialize();$.ajax({url:'<?=Yii::$app->urlManager->createUrl("purchase/purchaseorders")?>',type:'POST',data:d,dataType:'json',timeout:5000,success:function(r){if(r.success){location.reload();}else showAlert(r.message,'danger');},error:function(x,s){showAlert(s==='timeout'?'Timeout':'Network error','danger');},complete:function(){bootstrap.Modal.getInstance($('#poModal')[0])?.hide();}});}
function filterPO(){let s=$('#poSearch').val();let sup=$('#supplierFilter').val();Storage.set('po_search',s);Storage.set('po_supplier',sup);}
function showAlert(m,type){const a=$(`<div class="alert alert-${type} alert-dismissible fade show"><i class="fa fa-info-circle"></i> ${htmlEscape(m)}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);$('#alerts-po').html(a);setTimeout(()=>a.fadeOut(),5000);}
</script>
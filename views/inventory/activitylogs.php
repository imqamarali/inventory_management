<?php
use yii\helpers\Html;
if (!isset($modules)) $modules = [];
if (!isset($activities)) $activities = [];
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs" style="margin-bottom: 12px;">
            <ul class="breadcrumb" style="width:100%;margin:0;">
                <li><i class="ace-icon fa fa-home"></i> <a href="index.php?r=inventory/dashboard">Home</a></li>
                <li class="active">Activity Logs</li>
            </ul>
        </div>

        <div style="padding: 12px; background: #f9f9f9; border-radius: 4px; margin-bottom: 12px;">
            <div class="row" style="margin: 0;">
                <div class="col-md-2" style="padding: 4px;">
                    <label style="font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">From Date</label>
                    <input type="date" id="dateFrom" class="form-control" style="padding: 6px; font-size: 12px;">
                </div>
                <div class="col-md-2" style="padding: 4px;">
                    <label style="font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">To Date</label>
                    <input type="date" id="dateTo" class="form-control" style="padding: 6px; font-size: 12px;">
                </div>
                <div class="col-md-2" style="padding: 4px;">
                    <label style="font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">Module</label>
                    <select id="module" class="form-control" style="padding: 6px; font-size: 12px;">
                        <option value="">All Modules</option>
                        <?php foreach ($modules as $mod): ?>
                            <option value="<?= Html::encode($mod) ?>"><?= Html::encode($mod) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2" style="padding: 4px;">
                    <label style="font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">Activity</label>
                    <select id="activity" class="form-control" style="padding: 6px; font-size: 12px;">
                        <option value="">All Activities</option>
                        <?php foreach ($activities as $act): ?>
                            <option value="<?= Html::encode($act) ?>"><?= Html::encode($act) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3" style="padding: 4px;">
                    <label style="font-size: 11px; font-weight: bold; display: block; margin-bottom: 4px;">Search (IP/User)</label>
                    <input type="text" id="search" class="form-control" placeholder="Search..." style="padding: 6px; font-size: 12px;">
                </div>
                <div class="col-md-1" style="padding: 4px; text-align: center; margin-top: 20px;">
                    <button type="button" class="btn btn-sm btn-primary" onclick="loadActivityLogs()" style="width: 100%;">
                        <i class="fa fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </div>

        <div class="widget-box">
            <div class="widget-header" style="padding: 12px 15px; background: #f5f5f5; border-bottom: 1px solid #e3e9f3;">
                <h4 class="widget-title" style="margin: 0; font-size: 13px;"><i class="fa fa-history"></i> Activity Logs</h4>
            </div>
            <div class="widget-body" style="padding: 12px;">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-striped table-bordered table-hover" id="logsTable" style="font-size: 11px; margin-bottom: 0;">
                        <thead>
                            <tr style="background: #f9f9f9;">
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Activity</th>
                                <th>Module</th>
                                <th>Type</th>
                                <th>IP</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="logsBody">
                            <tr><td colspan="7" class="text-center" style="padding: 20px;"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="paginationArea" style="text-align: center; margin-top: 12px; padding: 8px;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
loadActivityLogs();
function loadActivityLogs(page=1){
    const data=new FormData();
    data.append('_csrf','<?= Yii::$app->request->getCsrfToken() ?>');
    data.append('page',page);
    data.append('per_page',30);
    data.append('date_from',document.getElementById('dateFrom').value);
    data.append('date_to',document.getElementById('dateTo').value);
    data.append('module',document.getElementById('module').value);
    data.append('activity',document.getElementById('activity').value);
    data.append('search',document.getElementById('search').value);
    fetch('index.php?r=inventory/activitylogs',{method:'POST',body:data})
    .then(r=>r.json())
    .then(res=>{
        if(res.success){renderLogs(res.logs);renderPagination(res.page,res.totalPages);}
        else document.getElementById('logsBody').innerHTML='<tr><td colspan="7" class="text-center">Error loading logs</td></tr>';
    }).catch(e=>{
        console.error(e);
        document.getElementById('logsBody').innerHTML='<tr><td colspan="7" class="text-center">Error loading logs</td></tr>';
    });
}
function renderLogs(logs){
    let html='';
    if(logs.length===0){html='<tr><td colspan="7" class="text-center" style="padding:20px;">No activity logs found</td></tr>';}
    else{logs.forEach(log=>{
        const color={create:'label-success',update:'label-warning',delete:'label-danger',view:'label-primary',login:'label-success'}[log.activitytype?.toLowerCase()]||'label-default';
        html+=`<tr><td><strong>${log.formatted_date}</strong><br><small>${log.formatted_time}</small></td><td><i class="fa fa-user"></i> ${log.user_name}</td><td><strong>${log.activity||'-'}</strong></td><td><span class="label label-info" style="font-size:10px;">${log.module||'-'}</span></td><td><span class="label ${color}" style="font-size:10px;">${log.activitytype||'-'}</span></td><td><small>${log.ip_address||'-'}</small></td><td><button class="btn btn-xs" onclick="showDetails('${escapeHtml(JSON.stringify(log))}')" title="Details"><i class="fa fa-info-circle"></i></button></td></tr>`;
    });}
    document.getElementById('logsBody').innerHTML=html;
}
function renderPagination(page,total){
    let html='';
    const max=5,start=Math.max(1,page-Math.floor(max/2)),end=Math.min(total,start+max-1);
    if(start>1)html+=`<button class="btn btn-sm" onclick="loadActivityLogs(1)">First</button> `;
    if(page>1)html+=`<button class="btn btn-sm" onclick="loadActivityLogs(${page-1})">Prev</button> `;
    for(let i=start;i<=end;i++)html+=i===page?`<button class="btn btn-sm btn-primary">${i}</button> `:`<button class="btn btn-sm" onclick="loadActivityLogs(${i})">${i}</button> `;
    if(page<total)html+=`<button class="btn btn-sm" onclick="loadActivityLogs(${page+1})">Next</button> `;
    if(end<total)html+=`<button class="btn btn-sm" onclick="loadActivityLogs(${total})">Last</button>`;
    document.getElementById('paginationArea').innerHTML=html;
}
function escapeHtml(t){return t.replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));}
function showDetails(d){
    try{const log=JSON.parse(d);const html=`<table style="width:100%;text-align:left"><tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>Date:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3">${log.formatted_date} ${log.formatted_time}</td></tr><tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>User:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3">${log.user_name}</td></tr><tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>Activity:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3">${log.activity}</td></tr><tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>Module:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3">${log.module}</td></tr><tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>Reference ID:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3">${log.refid||'-'}</td></tr><tr><td style="padding:8px;border-bottom:1px solid #e3e9f3"><strong>IP Address:</strong></td><td style="padding:8px;border-bottom:1px solid #e3e9f3"><code>${log.ip_address}</code></td></tr>${log.additional_data_decoded?`<tr><td style="padding:8px;"><strong>Changes:</strong></td><td style="padding:8px;"><pre style="background:#f9f9f9;padding:8px;border-radius:4px;max-height:200px;overflow:auto;font-size:11px;">${JSON.stringify(log.additional_data_decoded,null,2)}</pre></td></tr>`:''}</table>`;Swal.fire({title:'Activity Details',html:html,width:'600px'});}catch(e){Swal.fire('Error','Failed to parse log details','error');}}
document.getElementById('dateFrom').addEventListener('change',()=>loadActivityLogs());
document.getElementById('dateTo').addEventListener('change',()=>loadActivityLogs());
document.getElementById('module').addEventListener('change',()=>loadActivityLogs());
document.getElementById('activity').addEventListener('change',()=>loadActivityLogs());
</script>

<style>
.btn{padding:6px 12px;border:1px solid #ddd;border-radius:4px;cursor:pointer;font-size:12px;background:#fff;transition:all 0.2s;}
.btn-primary{background:#2196F3;color:#fff;border-color:#2196F3;}
.btn:hover{opacity:0.8;}
.label{display:inline-block;padding:4px 8px;border-radius:3px;font-weight:bold;}
.label-success{background:#4CAF50;color:#fff;}
.label-warning{background:#FF9800;color:#fff;}
.label-danger{background:#f44336;color:#fff;}
.label-info{background:#2196F3;color:#fff;}
.label-primary{background:#007bff;color:#fff;}
.label-default{background:#e3e9f3;color:#333;}
.form-control{border:1px solid #ddd;border-radius:4px;padding:6px;}
.form-control:focus{outline:0;border-color:#2196F3;box-shadow:0 0 5px rgba(33,150,243,0.3);}
.widget-box{border:1px solid #e3e9f3;border-radius:4px;background:#fff;}
.widget-header{background:#f5f5f5;border-radius:4px 4px 0 0;}
.text-center{text-align:center;}
table{width:100%;border-collapse:collapse;}
th,td{padding:8px;text-align:left;border-bottom:1px solid #e3e9f3;}
th{background:#f9f9f9;font-weight:bold;}
tr:hover{background:#fafafa;}
code{background:#f5f5f5;padding:2px 6px;border-radius:3px;font-family:monospace;font-size:11px;}
</style>

<?php
/**
 * GOODS RECEIVING VIEW
 * ================================================================================
 * PURPOSE: Record physical receipt of goods from purchase orders (GRN module)
 *
 * FUNCTIONALITY:
 * - Create Goods Receiving Notes (GRN) for incoming shipments
 * - Link to purchase orders being fulfilled
 * - Record actual received quantities vs ordered
 * - Set receiving date and payment terms
 * - Track receipt status (Pending, Completed)
 * - Generate GRN numbers automatically
 * - Support for multiple shipments per PO
 * - Filter by supplier, warehouse, PO, date range
 * - Search by GRN number
 *
 * DATA MANAGEMENT:
 * - Stores in: inventory_goods_receiving table
 * - Foreign key: references inventory_purchase_orders
 * - Records: grn_number, purchase_order_id, supplier_id, warehouse_id,
 *            receiving_date, reference_no (supplier ref), invoice_no,
 *            status, remarks
 * - Status: Pending, Completed
 * - Triggers inventory_stock updates for received quantities
 *
 * FINANCE INTEGRATION:
 * - Goods receipt is the trigger point for:
 *   • Inventory capitalization (Assets)
 *   • Expense/COGS recognition
 *   • Accounts Payable recording (upon invoice receipt)
 * - GRN status determines inventory valuation date
 * - Receipt timing affects cash flow and financial statements
 * - Creates audit trail for asset acquisition
 * ================================================================================
 */

use yii\helpers\Html;

$this->title = 'Goods Receiving';

if(!isset($goodsReceiving))$goodsReceiving=[];
if(!isset($purchaseOrders))$purchaseOrders=[];
if(!isset($suppliers))$suppliers=[];
if(!isset($warehouses))$warehouses=[];
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=purchase/purchasedashboard">Home</a>
                </li>
                <li class="active">Goods Receiving</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openGrnModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Goods Receiving
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <form id="grn_search" onsubmit="return false;">

                <input type="text" name="grn_number" id="grn_number" class="new-input" style="width:14%;" placeholder="GRN Number">

                <select name="purchase_order_id" id="purchase_order_id" class="new-input" style="width:14%;">
                    <option value="">All PO</option>
                    <?php foreach ($purchaseOrders as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['po_number']) ?></option>
                    <?php } ?>
                </select>

                <select name="supplier_id" id="supplier_id" class="new-input" style="width:14%;">
                    <option value="">All Suppliers</option>
                    <?php foreach ($suppliers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['company_name']) ?></option>
                    <?php } ?>
                </select>

                <select name="warehouse_id" id="warehouse_id" class="new-input" style="width:14%; display:none">
                    <option value="">All Warehouses</option>
                    <?php foreach ($warehouses as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['warehouse_name']) ?></option>
                    <?php } ?>
                </select>

                <select name="status" id="status" class="new-input" style="width:12%;">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>

                <input type="date" name="from_date" id="from_date" class="new-input" style="width:11%;">
                <input type="date" name="to_date" id="to_date" class="new-input" style="width:11%;">

                <input type="text" name="per_page" id="per_page" value="20" class="new-input" style="width:6%;" placeholder="Records?">

                <input type="button" class="btn btn-primary"
                    onclick="searchform()"
                    value="Search"
                    style="height:30px;padding:0;margin-top:-3px;" />

            </form>
        </div>

        <div class="widget-main">

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="grn_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>GRN Number</th>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Warehouse</th>
                            <th>Receiving Date</th>
                            <th>Reference No</th>
                            <th>Invoice No</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($goodsReceiving as $key => $item) { ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= Html::encode($item['grn_number']) ?></td>
                                <td><?= Html::encode($item['po_number']) ?></td>
                                <td><?= Html::encode($item['company_name']) ?></td>
                                <td><?= Html::encode($item['warehouse_name']) ?></td>
                                <td><?= Html::encode($item['receiving_date']) ?></td>
                                <td><?= Html::encode($item['reference_no']) ?></td>
                                <td><?= Html::encode($item['invoice_no']) ?></td>
                                <td><?= grnStatusBadgeServer($item['status']) ?></td>
                                <td>
                                    <button onclick='openGrnModal(<?= json_encode($item) ?>)' title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    |
                                    <button onclick="printGoodsReceiving(<?= $item['id'] ?>)" title="Print PDF">
                                        <i class="fa fa-print"></i>
                                    </button>
                                    |
                                    <button onclick="deleteGrn(<?= $item['id'] ?>)" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div id="paginationArea" class="text-center"></div>

            </div>

        </div>

    </div>
</div>

<?php
function grnStatusBadgeServer($status)
{
    $map = ['Pending' => 'warning', 'Completed' => 'success', 'Cancelled' => 'danger'];
    $cls = $map[$status] ?? 'default';
    return '<span class="label label-' . $cls . '">' . Html::encode($status) . '</span>';
}
?>

<style>
    .swal2-popup.swal-wide-popup {
        width: 900px !important;
        max-width: 95vw !important;
    }
    .swal2-popup.swal-wide-popup .swal2-html-container {
        max-height: none !important;
        overflow: visible !important;
    }
</style>

<script>
    if (typeof purchaseOrders === 'undefined' || !purchaseOrders) {
        var purchaseOrders = <?= json_encode($purchaseOrders) ?>;
    }
    if (typeof suppliers === 'undefined' || !suppliers) {
        var suppliers = <?= json_encode($suppliers) ?>;
    }
    if (typeof warehouses === 'undefined' || !warehouses) {
        var warehouses = <?= json_encode($warehouses) ?>;
    }
</script>

<script>
    searchform();

    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading Goods Receiving...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'load');
        data.append('grn_number', $('#grn_number').val());
        data.append('purchase_order_id', $('#purchase_order_id').val());
        data.append('supplier_id', $('#supplier_id').val());
        data.append('warehouse_id', $('#warehouse_id').val());
        data.append('status', $('#status').val());
        data.append('from_date', $('#from_date').val());
        data.append('to_date', $('#to_date').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=purchase/goodsreceiving', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();

                if (res.success) {
                    renderGrn(res.goodsReceiving);
                    renderPagination(res.page, res.totalPages);
                } else {
                    Swal.fire('Error', res.message || 'Failed to load goods receiving.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.close();
                Swal.fire('Error', 'Unable to load data!', 'error');
            });

    }

    function grnStatusBadge(status) {
        const map = {
            'Pending': 'warning',
            'Completed': 'success',
            'Cancelled': 'danger'
        };
        const cls = map[status] || 'default';
        return '<span class="label label-' + cls + '">' + status + '</span>';
    }

    function renderGrn(rows) {

        let html = '';

        if (rows.length == 0) {

            html = `
        <tr>
            <td colspan="10" class="text-center">
                No Goods Receiving Found
            </td>
        </tr>`;

        } else {

            rows.forEach(function(item, index) {

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.grn_number}</td>
                <td>${item.po_number??''}</td>
                <td>${item.company_name??''}</td>
                <td>${item.warehouse_name??''}</td>
                <td>${item.receiving_date??''}</td>
                <td>${item.reference_no??''}</td>
                <td>${item.invoice_no??''}</td>
                <td>${grnStatusBadge(item.status)}</td>
                <td>
                    <button onclick='openGrnModal(${JSON.stringify(item)})' title="Edit">
                        <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button onclick="printGoodsReceiving(${item.id})" title="Print PDF">
                        <i class="fa fa-print" style="color: #27ae60;"></i>
                    </button>
                    |
                    <button onclick="deleteGrn(${item.id})" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;
            });

        }

        $('#grn_table tbody').html(html);

    }

    function renderPagination(page, totalPages) {
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `
        <button
            class="${i==page?'btn-primary':'btn-default'}"
            onclick="searchform(${i})">
            ${i}
        </button>`;
        }
        $('#paginationArea').html(html);
    }

    function openGrnModal(grnData = null) {
        const isEdit = grnData !== null;
        const id = isEdit ? grnData.id : '';
        const poId = isEdit ? grnData.purchase_order_id : '';
        const supplierId = isEdit ? grnData.supplier_id : '';
        const warehouseId = isEdit ? grnData.warehouse_id : '';
        const receivingDate = isEdit ? grnData.receiving_date : '';
        // Reference number will be auto-generated on save
        const referenceNo = isEdit ? (grnData.reference_no ?? '(auto-generated)') : '(auto-generated)';
        const invoiceNo = isEdit ? (grnData.invoice_no ?? '') : '';
        const status = isEdit ? grnData.status : 'Pending';
        const remarks = isEdit ? (grnData.remarks ?? '') : '';

        let poOptions = '<option value="">Select Purchase Order</option>';
        purchaseOrders.forEach(function(item) {
            poOptions += `<option value="${item.id}" ${item.id==poId?'selected':''}>${item.po_number}</option>`;
        });

        let supplierOptions = '<option value="">Select Supplier</option>';
        suppliers.forEach(function(item) {
            supplierOptions += `<option value="${item.id}" ${item.id==supplierId?'selected':''}>${item.company_name}</option>`;
        });

        let warehouseOptions = '';
        warehouses.forEach(function(item) {
            warehouseOptions += `<option value="${item.id}" ${item.id==warehouseId?'selected':''}>${item.warehouse_name}</option>`;
        });

        const statusList = ['Pending', 'Completed', 'Cancelled'];
        let statusOptions = '';
        statusList.forEach(function(s) {
            statusOptions += `<option value="${s}" ${s==status?'selected':''}>${s}</option>`;
        });

        Swal.fire({
            title: isEdit ? 'Update Goods Receiving' : 'Add Goods Receiving',
            width: '900px',
            customClass: {
                popup: 'swal-wide-popup'
            },
            didOpen: () => {
                $('#swal_po').chosen({
                    width: '100%',
                    search_contains: true
                });
                $('#swal_supplier').chosen({
                    width: '100%',
                    search_contains: true
                });
                $('#swal_warehouse').chosen({
                    width: '100%',
                    search_contains: true
                });
            },
            html: `
                <form id="grnForm">

                <input type="hidden" id="swal_id" value="${id}">

                <div class="row">
                <div class="col-md-4">
                <label>Purchase Order</label>
                <select id="swal_po" class="form-control chzn-select-modal">
                ${poOptions}
                </select>
                </div>

                <div class="col-md-4">
                <label>Supplier</label>
                <select id="swal_supplier" class="form-control chzn-select-modal">
                ${supplierOptions}
                </select>
                </div>

                <div class="col-md-4">
                <label>Warehouse</label>
                <select id="swal_warehouse" class="form-control chzn-select-modal">
                ${warehouseOptions}
                </select>
                </div>
                </div>

                <div class="row">
                <div class="col-md-4">
                <label>Receiving Date</label>
                <input type="date" id="swal_receiving_date" class="form-control" value="${receivingDate}">
                </div>

                <div class="col-md-4">
                <label>Reference No <span style="color: #888; font-size: 12px;">(Auto-generated)</span></label>
                <input type="text" id="swal_reference_no" class="form-control" value="${referenceNo}" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                </div>

                <div class="col-md-4">
                <label>Invoice No</label>
                <input type="text" id="swal_invoice_no" class="form-control" value="${invoiceNo}">
                </div>
                </div>

                <div class="row">
                <div class="col-md-4">
                <label>Status</label>
                <select id="swal_status" class="form-control">
                ${statusOptions}
                </select>
                </div>

                <div class="col-md-8">
                <label>Remarks</label>
                <input type="text" id="swal_remarks" class="form-control" value="${remarks}">
                </div>
                </div>

                </form>
                `,
            showCancelButton: true,
            confirmButtonText: isEdit ? 'Update GRN' : 'Save GRN',
            confirmButtonColor: '#87B87F',
            cancelButtonText: 'Cancel',

            preConfirm: () => {

                if ($('#swal_po').val() == '' || $('#swal_supplier').val() == '' || $('#swal_warehouse').val() == '' || $('#swal_receiving_date').val() == '') {
                    Swal.showValidationMessage('Purchase Order, Supplier, Warehouse and Receiving Date are required');
                    return false;
                }

                return {
                    id: $('#swal_id').val(),
                    purchase_order_id: $('#swal_po').val(),
                    supplier_id: $('#swal_supplier').val(),
                    warehouse_id: $('#swal_warehouse').val(),
                    receiving_date: $('#swal_receiving_date').val(),
                    // reference_no is auto-generated by server
                    invoice_no: $('#swal_invoice_no').val(),
                    status: $('#swal_status').val(),
                    remarks: $('#swal_remarks').val(),
                    flag: 'save'
                };

            }

        }).then(function(result) {

            if (result.isConfirmed) {
                saveGrn(result.value);
            }

        });

    }

    function saveGrn(formData) {

        Swal.fire({
            title: 'Processing...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');

        Object.keys(formData).forEach(function(key) {
            data.append(key, formData[key]);
        });

        fetch('index.php?r=purchase/goodsreceiving', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {

                if (res.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('.ajax-module.active').trigger('click');
                    });

                } else {

                    Swal.fire('Error', res.message, 'error');

                }

            })
            .catch(() => {
                Swal.fire('Error', 'Unable to save data.', 'error');
            });

    }

    function printGoodsReceiving(id) {
        const url = 'index.php?r=documents/goodsreceiving&id=' + id;
        window.open(url, '_blank');
    }

    function deleteGrn(id) {

        Swal.fire({
            title: 'Are you sure?',
            text: 'Goods Receiving record will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete'
        }).then(function(result) {

            if (!result.isConfirmed) {
                return;
            }

            const data = new FormData();

            data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
            data.append('flag', 'delete');
            data.append('id', id);

            fetch('index.php?r=purchase/goodsreceiving', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(res => {

                    if (res.success) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('.ajax-module.active').trigger('click');
                        });

                    } else {

                        Swal.fire('Error', res.message, 'error');

                    }

                })
                .catch(() => {
                    Swal.fire('Error', 'Unable to delete record.', 'error');
                });

        });

    }
</script>

<?php

use yii\helpers\Html;

if (!isset($stocks)) $stocks = [];
if (!isset($warehouses)) $warehouses = [];
if (!isset($categories)) $categories = [];
if (!isset($brands)) $brands = [];
if (!isset($products)) $products = [];
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=stock/dashboard">Home</a>
                </li>
                <li class="active">Inventory Current Stock</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="openStockModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Current Stock
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div style="padding-top:10px;padding-left:13px;">
            <form id="stock_search" method="get" action="index.php">
                <input type="hidden" name="r" value="stock/inventorycurrentstock">

                <input type="text" name="keyword" id="keyword"
                    value="<?= Html::encode(Yii::$app->request->get('keyword')) ?>"
                    class="new-input" style="width:18%;" placeholder="Product / SKU / Barcode">

                <input type="text" name="per_page" id="per_page"
                    value="<?= $perPage ?? '50' ?>"
                    class="new-input" style="width:8%;" placeholder="Records?">

                <select name="warehouse_id" id="warehouse_id" class="new-input" style="width:15%;">
                    <option value="">All Warehouses</option>
                    <?php foreach ($warehouses as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['warehouse_name']) ?></option>
                    <?php } ?>
                </select>

                <select name="category_id" id="category_id" class="new-input" style="width:15%;">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['category_name']) ?></option>
                    <?php } ?>
                </select>

                <select name="brand_id" id="brand_id" class="new-input" style="width:15%;">
                    <option value="">All Brands</option>
                    <?php foreach ($brands as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= Html::encode($row['brand_name']) ?></option>
                    <?php } ?>
                </select>

                <select name="stock_status" id="stock_status" class="new-input" style="width:12%;">
                    <option value="">Stock Status</option>
                    <option value="available">Available</option>
                    <option value="low">Low Stock</option>
                    <option value="out">Out Of Stock</option>
                </select>

                <input type="button" class="btn btn-primary"
                    onclick="searchform()"
                    value="Search"
                    style="height:30px;padding:0;margin-top:-3px;" />

            </form>
        </div>

        <div class="widget-main">
            <?php if (count($stocks) == 0) { ?>

                <div class="alert alert-info text-center">
                    <i class="ace-icon fa fa-database fa-3x" style="color:#6FB3E0;"></i>
                    <h4 style="margin-top:15px;">No Stock Found</h4>
                    <p>No inventory stock records available.</p>
                </div>

            <?php } else { ?>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="stock_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Warehouse</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Quantity</th>
                                <th>Reserved</th>
                                <th>Available</th>
                                <th>Average Cost</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stocks as $key => $item) { ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td>
                                        <i class="fa fa-cube"></i>
                                        <?= Html::encode($item['product_name']) ?><br>
                                        <small><?= Html::encode($item['sku']) ?></small>
                                    </td>
                                    <td><?= Html::encode($item['warehouse_name']) ?></td>
                                    <td><?= Html::encode($item['category_name']) ?></td>
                                    <td><?= Html::encode($item['brand_name']) ?></td>

                                    <td><?= number_format($item['quantity'], 2) ?></td>
                                    <td><?= number_format($item['reserved_quantity'], 2) ?></td>
                                    <td><?= number_format($item['available_quantity'], 2) ?></td>
                                    <td><?= number_format($item['average_cost'], 2) ?></td>

                                    <td>
                                        <?php if ($item['quantity'] <= 0) { ?>
                                            <span class="label label-danger">Out</span>
                                        <?php } elseif ($item['quantity'] <= $item['reorder_level']) { ?>
                                            <span class="label label-warning">Low</span>
                                        <?php } else { ?>
                                            <span class="label label-success">Available</span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <button onclick='openStockModal(<?= json_encode($item) ?>)'>
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        |
                                        <button onclick="deleteStock(<?= $item['id'] ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <div id="paginationArea" class="text-center"></div>

                </div>

            <?php } ?>

        </div>

    </div>
</div>

<script>
    if (typeof warehouses === 'undefined' || !warehouses) {
        var warehouses = <?= json_encode($warehouses) ?>;
    }

    if (typeof products === 'undefined' || !products) {
        var products = <?= json_encode($products) ?>;
    }
    if (typeof brands === 'undefined' || !brands) {
        var brands = <?= json_encode($brands) ?>;
    }
    if (typeof categories === 'undefined' || !categories) {
        var categories = <?= json_encode($categories) ?>;
    }
</script>


<script>
    setTimeout(function() {
        $('.chzn-select').chosen({
            search_contains: true,
            no_results_text: "No record found"
        });
    }, 500);
</script>
<script>
    searchform();
    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading Stock...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'search');
        data.append('keyword', $('#keyword').val());
        data.append('warehouse_id', $('#warehouse_id').val());
        data.append('category_id', $('#category_id').val());
        data.append('brand_id', $('#brand_id').val());
        data.append('stock_status', $('#stock_status').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=stock/inventorycurrentstock', {
            method: 'POST',
            body: data
        })
        .then(res => res.json())
        .then(res => {
            Swal.close();

            if (res.success) {
                renderStock(res.stocks);
                renderPagination(res.page, res.total_pages);
            } else {
                Swal.fire('Error', res.message || 'Failed to load stock.', 'error');
            }
        })
        .catch(error => {
            console.error(error);
            Swal.close();
            Swal.fire('Error', 'Unable to load data!', 'error');
        });

    }

    function renderStock(rows) {

        let html = '';

        if (rows.length == 0) {

            html = `
        <tr>
            <td colspan="11" class="text-center">
                No Stock Found
            </td>
        </tr>`;

        } else {

            rows.forEach(function(item, index) {

                let status = '<span class="label label-success">Available</span>';

                if (parseFloat(item.quantity) <= 0) {
                    status = '<span class="label label-danger">Out</span>';
                } else if (parseFloat(item.quantity) <= parseFloat(item.reorder_level)) {
                    status = '<span class="label label-warning">Low</span>';
                }

                html += `
            <tr>
                <td>${index+1}</td>
                <td>${item.product_name}<br><small>${item.sku??''}</small></td>
                <td>${item.warehouse_name}</td>
                <td>${item.category_name??''}</td>
                <td>${item.brand_name??''}</td>
                <td>${item.quantity}</td>
                <td>${item.reserved_quantity}</td>
                <td>${item.available_quantity}</td>
                <td>${item.average_cost}</td>
                <td>${status}</td>
                <td>
                    <button onclick='openStockModal(${JSON.stringify(item)})'>
                        <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button onclick="deleteStock(${item.id})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;
            });

        }

        $('#stock_table tbody').html(html);

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

    function openStockModal(stockData = null) {
        const isEdit = stockData !== null;
        const id = isEdit ? stockData.id : '';
        const warehouseId = isEdit ? stockData.warehouse_id : '';
        const productId = isEdit ? stockData.product_id : '';
        const quantity = isEdit ? stockData.quantity : 0;
        const reserved = isEdit ? stockData.reserved_quantity : 0;
        const averageCost = isEdit ? stockData.average_cost : 0;
        const purchasePrice = isEdit ? stockData.last_purchase_price : 0;
        // let warehouseOptions = '<option value="">Select Warehouse</option>';
        let warehouseOptions = '';
        warehouses.forEach(function(item) {
            warehouseOptions += `<option value="${item.id}" ${item.id==warehouseId?'selected':''}>${item.warehouse_name}</option>`;
        });

        let productOptions = '<option value="">Select Product</option>';

        products.forEach(function(item) {
            productOptions += `<option value="${item.id}" ${item.id==productId?'selected':''}>${item.product_name}</option>`;
        });
        
        function loadProductInfo(productId) {
            const product = products.find(p => p.id == productId);
            if (!product) return;
            $('#swal_unit').val(product.unit_name);
            $('#swal_purchase_price').val(product.purchase_price);
            $('#swal_selling_price').val(product.selling_price);
            calculateAverageCost();
        }
        function calculateAverageCost() {
            let qty = parseFloat($('#swal_quantity').val()) || 0;
            let purchase = parseFloat($('#swal_purchase_price').val()) || 0;
            $('#swal_average_cost').val((qty * purchase).toFixed(2));

        }
        $('#swal_product').on('change', function () {

            $.get('index.php?r=stock/get-product-info', {
                id: $(this).val()
            }, function (res) {

                $('#swal_unit').val(res.unit_name);
                $('#swal_purchase_price').val(res.purchase_price);
                $('#swal_selling_price').val(res.selling_price);

                calculateAverageCost();

            }, 'json');

        });
        Swal.fire({
            title: isEdit ? 'Update Stock' : 'Add Stock',
            width: '800px',
            didOpen: () => {
                $('#swal_warehouse').chosen({
                    width:'100%',
                    search_contains:true
                });
                $('#swal_product').chosen({
                    width:'100%',
                    search_contains:true
                });
                loadProductInfo($('#swal_product').val());
                $('#swal_product').on('change', function () {
                    loadProductInfo($(this).val());
                });
                $('#swal_quantity').on('input', calculateAverageCost);
                $('#swal_purchase_price').on('input', calculateAverageCost);
            },
            html: `
                <form id="stockForm">

                <input type="hidden" id="swal_id" value="${id}">

                <div class="row">
                <div class="col-md-6">
                <label>Warehouse</label>
                <select id="swal_warehouse" class="form-control chzn-select-modal">
                ${warehouseOptions}
                </select>
                </div>

                <div class="col-md-6">
                <label>Product</label>
                <select id="swal_product" class="form-control chzn-select-modal">
                ${productOptions}
                </select>
                </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Unit</label>
                        <input type="text" id="swal_unit" class="form-control" readonly>
                    </div>

                    <div class="col-md-4">
                        <label>Purchase Price</label>
                        <input type="number" readonly id="swal_purchase_price" class="form-control" value="${purchasePrice}">
                    </div>

                    <div class="col-md-4">
                        <label>Selling Price</label>
                        <input type="number"  readonly id="swal_selling_price" class="form-control" readonly>
                    </div>
                </div>
                <div class="row">
                <div class="col-md-6">
                <label>Quantity</label>
                <input type="number" id="swal_quantity" class="form-control" value="${quantity}">
                </div>

                <div class="col-md-6">
                <label>Reserved Quantity</label>
                <input type="number" id="swal_reserved" class="form-control" value="${reserved}">
                </div>
                </div>

                <div class="row">
                <div class="col-md-6">
                <label>Average Cost</label>
                <input type="number" id="swal_average_cost" class="form-control" value="${averageCost}">
                </div>

                <div class="col-md-6">
                <label>Last Purchase Price</label>
                <input type="number" id="swal_purchase_price" class="form-control" value="${purchasePrice}">
                </div>
                </div>

                </form>
                `,
            showCancelButton: true,
            confirmButtonText: isEdit ? 'Update Stock' : 'Save Stock',
            confirmButtonColor: '#87B87F',
            cancelButtonText: 'Cancel',

            preConfirm: () => {

                if ($('#swal_warehouse').val() == '' || $('#swal_product').val() == '') {
                    Swal.showValidationMessage('Warehouse and Product are required');
                    return false;
                }

                return {
                    id: $('#swal_id').val(),
                    warehouse_id: $('#swal_warehouse').val(),
                    product_id: $('#swal_product').val(),
                    quantity: $('#swal_quantity').val(),
                    reserved_quantity: $('#swal_reserved').val(),
                    average_cost: $('#swal_average_cost').val(),
                    last_purchase_price: $('#swal_purchase_price').val(),
                    flag: isEdit ? 'update' : 'create'
                };

            }

        }).then(function(result) {

            if (result.isConfirmed) {
                saveStock(result.value);
            }

        });

    }

    function saveStock(formData) {

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

        fetch('index.php?r=stock/inventorycurrentstock', {
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

    function deleteStock(id) {

        Swal.fire({
            title: 'Are you sure?',
            text: 'Stock record will be deleted.',
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

            fetch('index.php?r=stock/inventorycurrentstock', {
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
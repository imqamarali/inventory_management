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
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Reserved</th>
                                <th class="text-right">Available</th>
                                <th class="text-right">Average Cost</th>
                                <th class="text-right">Sold Qty</th>
                                <th class="text-right">Sold Amount</th>
                                <th class="text-right">Remaining Amount</th>
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

                                    <td class="text-right"><?= number_format($item['quantity'], 2) ?></td>
                                    <td class="text-right"><?= number_format($item['reserved_quantity'], 2) ?></td>
                                    <td class="text-right"><?= number_format($item['available_quantity'], 2) ?></td>
                                    <td class="text-right"><?= number_format($item['average_cost'], 2) ?></td>
                                    <td class="text-right"><?= number_format($item['sold_quantity'] ?? 0, 2) ?></td>
                                    <td class="text-right"><?= number_format($item['sold_amount'] ?? 0, 2) ?></td>
                                    <td class="text-right"><span id="remaining-<?= $item['id'] ?>"><?= number_format($item['remaining_amount'] ?? 0, 2) ?></span></td>

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
                                        <button onclick='viewProductStats(<?= json_encode($item) ?>)' title="View Details">
                                            <i class="fa fa-eye"></i>
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
    function htmlEscape(text) {
        if (!text) return '';
        const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

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
            body: data,
            signal: AbortSignal.timeout(5000)
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
            if (error.name === 'AbortError') {
                Swal.fire('Error', 'Request timed out. Please try again.', 'error');
            } else {
                Swal.fire('Error', 'Unable to load data!', 'error');
            }
        });
    }

    function renderStock(rows) {
        let html = '';
        if (rows.length == 0) {
            html = `<tr><td colspan="14" class="text-center">No Stock Found</td></tr>`;
        } else {
            rows.forEach(function(item, index) {
                let status = '<span class="label label-success">Available</span>';
                if (parseFloat(item.quantity) <= 0) {
                    status = '<span class="label label-danger">Out</span>';
                } else if (parseFloat(item.quantity) <= parseFloat(item.reorder_level)) {
                    status = '<span class="label label-warning">Low</span>';
                }
                html += `<tr>
                    <td>${index+1}</td>
                    <td>${htmlEscape(item.product_name)}<br><small>${htmlEscape(item.sku??'')}</small></td>
                    <td>${htmlEscape(item.warehouse_name)}</td>
                    <td>${htmlEscape(item.category_name??'')}</td>
                    <td>${htmlEscape(item.brand_name??'')}</td>
                    <td class="text-right">${parseFloat(item.quantity??0).toFixed(2)}</td>
                    <td class="text-right">${parseFloat(item.reserved_quantity??0).toFixed(2)}</td>
                    <td class="text-right">${parseFloat(item.available_quantity??0).toFixed(2)}</td>
                    <td class="text-right">${parseFloat(item.average_cost??0).toFixed(2)}</td>
                    <td class="text-right">${parseFloat(item.sold_quantity??0).toFixed(2)}</td>
                    <td class="text-right">${parseFloat(item.sold_amount??0).toFixed(2)}</td>
                    <td class="text-right">${parseFloat(item.remaining_amount??0).toFixed(2)}</td>
                    <td>${status}</td>
                    <td>
                        <button onclick='viewProductStats(${JSON.stringify(item).replace(/'/g, "&apos;")})' title="View Details">
                            <i class="fa fa-eye"></i>
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
        let warehouseOptions = '';
        warehouses.forEach(function(item) {
            warehouseOptions += `<option value="${item.id}" ${item.id==warehouseId?'selected':''}>${htmlEscape(item.warehouse_name)}</option>`;
        });

        let productOptions = '<option value="">Select Product</option>';
        products.forEach(function(item) {
            productOptions += `<option value="${item.id}" ${item.id==productId?'selected':''}>${htmlEscape(item.product_name)}</option>`;
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
            body: data,
            signal: AbortSignal.timeout(5000)
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
                Swal.fire('Error', res.message || 'Failed to save stock', 'error');
            }
        })
        .catch(error => {
            if (error.name === 'AbortError') {
                Swal.fire('Error', 'Request timed out. Please try again.', 'error');
            } else {
                Swal.fire('Error', 'Unable to save data.', 'error');
            }
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
                body: data,
                signal: AbortSignal.timeout(5000)
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
                    Swal.fire('Error', res.message || 'Failed to delete stock', 'error');
                }
            })
            .catch(error => {
                if (error.name === 'AbortError') {
                    Swal.fire('Error', 'Request timed out. Please try again.', 'error');
                } else {
                    Swal.fire('Error', 'Unable to delete record.', 'error');
                }
            });

        });

    }

    function viewProductStats(stockData) {
        const productId = stockData.product_id;
        const isActive = stockData.is_active == 1 ? true : false;

        // Function to show the loading dialog
        const showLoading = () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
            }
        };

        // Wait for Swal to be available (max 3 seconds)
        let waitAttempts = 0;
        const maxAttempts = 30; // 3 seconds with 100ms intervals

        const waitForSwal = setInterval(() => {
            waitAttempts++;
            if (typeof Swal !== 'undefined') {
                clearInterval(waitForSwal);
                showLoading();
                fetchProductStats(stockData, productId, isActive);
            } else if (waitAttempts >= maxAttempts) {
                clearInterval(waitForSwal);
                alert('Unable to load product details. Please refresh the page and try again.');
            }
        }, 100);
    }

    function fetchProductStats(stockData, productId, isActive) {
        // Fetch product statistics from backend
        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'get_stats');
        data.append('product_id', productId);

        fetch('index.php?r=stock/inventorycurrentstock', {
            method: 'POST',
            body: data,
            signal: AbortSignal.timeout(5000)
        })
        .then(res => res.json())
        .then(res => {
            Swal.close();
            if (res.success) {
                const stats = res.stats;
                showProductStatsModal(stockData, stats, isActive);
            } else {
                Swal.fire('Error', res.message || 'Failed to load product statistics', 'error');
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Error:', error);
            Swal.fire('Error', 'Unable to load data', 'error');
        });
    }

    function showProductStatsModal(stockData, stats, isActive) {
        const activeCheckbox = isActive ? 'checked' : '';
        const activeLabel = isActive ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';

        Swal.fire({
            title: 'Product Details',
            width: '700px',
            html: `
                <div style="text-align: left; padding: 20px;">
                    <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                        <h4 style="margin-top: 0; margin-bottom: 10px;">${htmlEscape(stockData.product_name)}</h4>
                        <p style="margin: 5px 0;"><strong>SKU:</strong> ${htmlEscape(stockData.sku ?? 'N/A')}</p>
                        <p style="margin: 5px 0;"><strong>Barcode:</strong> ${htmlEscape(stockData.barcode ?? 'N/A')}</p>
                        <p style="margin: 5px 0;"><strong>Category:</strong> ${htmlEscape(stockData.category_name ?? 'N/A')}</p>
                        <p style="margin: 5px 0;"><strong>Brand:</strong> ${htmlEscape(stockData.brand_name ?? 'N/A')}</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <h5 style="margin-bottom: 10px; border-bottom: 2px solid #ddd; padding-bottom: 5px;">Stock Information</h5>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <p style="margin: 0; color: #666; font-size: 12px;">Current Quantity</p>
                                <p style="margin: 0; font-size: 18px; font-weight: bold; color: #333;">${parseFloat(stockData.quantity ?? 0).toFixed(2)}</p>
                            </div>
                            <div>
                                <p style="margin: 0; color: #666; font-size: 12px;">Reserved</p>
                                <p style="margin: 0; font-size: 18px; font-weight: bold; color: #ff9800;">${parseFloat(stockData.reserved_quantity ?? 0).toFixed(2)}</p>
                            </div>
                            <div>
                                <p style="margin: 0; color: #666; font-size: 12px;">Available</p>
                                <p style="margin: 0; font-size: 18px; font-weight: bold; color: #4caf50;">${parseFloat(stockData.available_quantity ?? 0).toFixed(2)}</p>
                            </div>
                            <div>
                                <p style="margin: 0; color: #666; font-size: 12px;">Average Cost</p>
                                <p style="margin: 0; font-size: 18px; font-weight: bold; color: #333;">PKR ${parseFloat(stockData.average_cost ?? 0).toFixed(2)}</p>
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <h5 style="margin-bottom: 10px; border-bottom: 2px solid #ddd; padding-bottom: 5px;">Sales Statistics</h5>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <p style="margin: 0; color: #666; font-size: 12px;">Total Sales Quantity</p>
                                <p style="margin: 0; font-size: 18px; font-weight: bold; color: #2196f3;">${parseFloat(stats.total_sold_qty ?? 0).toFixed(2)}</p>
                            </div>
                            <div>
                                <p style="margin: 0; color: #666; font-size: 12px;">Total Sales Amount</p>
                                <p style="margin: 0; font-size: 18px; font-weight: bold; color: #2196f3;">PKR ${parseFloat(stats.total_sold_amount ?? 0).toFixed(2)}</p>
                            </div>
                            <div>
                                <p style="margin: 0; color: #666; font-size: 12px;">Total Purchase Qty</p>
                                <p style="margin: 0; font-size: 18px; font-weight: bold; color: #673ab7;">${parseFloat(stats.total_purchase_qty ?? 0).toFixed(2)}</p>
                            </div>
                            <div>
                                <p style="margin: 0; color: #666; font-size: 12px;">Total Purchase Amount</p>
                                <p style="margin: 0; font-size: 18px; font-weight: bold; color: #673ab7;">PKR ${parseFloat(stats.total_purchase_amount ?? 0).toFixed(2)}</p>
                            </div>
                        </div>
                    </div>

                    <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                        <h5 style="margin-top: 0; margin-bottom: 10px;">Product Status</h5>
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" id="product_active_toggle" ${activeCheckbox} style="width: 18px; height: 18px; cursor: pointer;">
                            <span>Mark as Active</span>
                            <span id="active_status_badge" style="margin-left: auto;">${activeLabel}</span>
                        </label>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Update Status',
            cancelButtonText: 'Close',
            preConfirm: () => {
                const isChecked = document.getElementById('product_active_toggle').checked;
                return {
                    product_id: productId,
                    is_active: isChecked ? 1 : 0
                };
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                updateProductStatus(result.value);
            }
        });
    }

    function updateProductStatus(data) {
        Swal.fire({
            title: 'Updating...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const formData = new FormData();
        formData.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        formData.append('flag', 'update_status');
        formData.append('product_id', data.product_id);
        formData.append('is_active', data.is_active);

        fetch('index.php?r=stock/inventorycurrentstock', {
            method: 'POST',
            body: formData,
            signal: AbortSignal.timeout(5000)
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
                    searchform();
                });
            } else {
                Swal.fire('Error', res.message || 'Failed to update status', 'error');
            }
        })
        .catch(error => {
            if (error.name === 'AbortError') {
                Swal.fire('Error', 'Request timed out. Please try again.', 'error');
            } else {
                Swal.fire('Error', 'Unable to update status', 'error');
            }
        });
    }
</script>
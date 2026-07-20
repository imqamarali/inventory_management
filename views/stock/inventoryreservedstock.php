<?php

use yii\helpers\Html;

if (!isset($stocks)) {
    $stocks = [];
}
if (!isset($warehouses)) {
    $warehouses = [];
}
if (!isset($categories)) {
    $categories = [];
}
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
                    <a href="index.php?r=stock/dashboard">Home</a>
                </li>
                <li class="active">Reserved Stock</li>
            </ul>
        </div>
        <div style="padding-top:10px;padding-left:13px;">
            <form id="stock_search" method="get" action="index.php">
                <input type="hidden" name="r" value="stock/inventoryreservedstock">

                <input type="text"
                    name="keyword"
                    id="keyword"
                    class="new-input"
                    style="width:20%"
                    placeholder="Product / SKU"
                    value="<?= Html::encode(Yii::$app->request->get('keyword')) ?>">

                <input type="text"
                    name="per_page"
                    id="per_page"
                    class="new-input"
                    style="width:10%"
                    placeholder="Records?"
                    value="<?= $perPage ?? 10 ?>">

                <select name="warehouse_id" id="warehouse_id" class="new-input" style="width:18%;">
                    <option value="">All Warehouses</option>
                    <?php foreach ($warehouses as $row) { ?>
                        <option value="<?= $row['id'] ?>" <?= Yii::$app->request->get('warehouse_id') == $row['id'] ? 'selected' : ''; ?>>
                            <?= Html::encode($row['warehouse_name']) ?>
                        </option>
                    <?php } ?>
                </select>

                <select name="category_id" id="category_id" class="new-input" style="width:18%;">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $row) { ?>
                        <option value="<?= $row['id'] ?>" <?= Yii::$app->request->get('category_id') == $row['id'] ? 'selected' : ''; ?>>
                            <?= Html::encode($row['category_name']) ?>
                        </option>
                    <?php } ?>
                </select>

                <select name="brand_id" id="brand_id" class="new-input" style="width:18%;">
                    <option value="">All Brands</option>
                    <?php foreach ($brands as $row) { ?>
                        <option value="<?= $row['id'] ?>" <?= Yii::$app->request->get('brand_id') == $row['id'] ? 'selected' : ''; ?>>
                            <?= Html::encode($row['brand_name']) ?>
                        </option>
                    <?php } ?>
                </select>

                <input type="button"
                    class="btn btn-primary"
                    value="Search"
                    onclick="searchform()"
                    style="height:30px;padding:0;margin-top:-3px;">

            </form>
        </div>

        <div class="widget-main">
            <?php if (count($stocks) == 0) { ?>

                <div class="alert alert-info text-center">
                    <i class="ace-icon fa fa-info-circle fa-3x" style="color:#6FB3E0;"></i>
                    <h4 style="margin-top:15px;">No Reserved Stock Found</h4>
                    <p>No reserved stock records are available.</p>
                </div>

            <?php } else { ?>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="stock_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Warehouse</th>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Total Qty</th>
                                <th>Reserved</th>
                                <th>Available</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stocks as $key => $item) { ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><?= Html::encode($item['warehouse_name']) ?></td>
                                    <td><?= Html::encode($item['product_name']) ?></td>
                                    <td><?= Html::encode($item['sku']) ?></td>
                                    <td><?= number_format($item['quantity'], 2) ?></td>
                                    <td><span class="label label-warning"><?= number_format($item['reserved_quantity'], 2) ?></span></td>
                                    <td><span class="label label-success"><?= number_format($item['available_quantity'], 2) ?></span></td>
                                    <td>
                                        <button type="button" onclick='openReservedStockModal(<?= htmlspecialchars(json_encode($item), ENT_QUOTES) ?>)'>
                                            <i class="fa fa-pencil"></i>
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
</script>
<script> 
      
    searchform();
    function searchform(page = 1) {

        Swal.fire({
            title: 'Loading...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'search');
        data.append('keyword', $('#keyword').val());
        data.append('warehouse_id', $('#warehouse_id').val());
        data.append('category_id', $('#category_id').val());
        data.append('brand_id', $('#brand_id').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);

        fetch('index.php?r=stock/inventoryreservedstock', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderStocks(res.stocks);
                    renderPagination(res.page, res.total_pages);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.close();
                Swal.fire('Error', 'Unable to load data.', 'error');
            });

    }

    function renderStocks(stocks) {
        let html = '';
        if (stocks.length == 0) {
            html = '<tr><td colspan="8" class="text-center">No Reserved Stock Found</td></tr>';
        } else {
            stocks.forEach(function(item, index) {
                html += `
                    <tr>
                    <td>${index+1}</td>
                    <td>${item.warehouse_name}</td>
                    <td>${item.product_name}</td>
                    <td>${item.sku??''}</td>
                    <td>${parseFloat(item.quantity).toFixed(2)}</td>
                    <td><span class="label label-warning">${parseFloat(item.reserved_quantity).toFixed(2)}</span></td>
                    <td><span class="label label-success">${parseFloat(item.available_quantity).toFixed(2)}</span></td>
                    <td>
                    <button onclick='openReservedStockModal(${JSON.stringify(item)})'>
                    <i class="fa fa-pencil"></i>
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

    function openReservedStockModal(stock = null) {

        const id = stock ? stock.id : '';
        const reserved = stock ? stock.reserved_quantity : '';

        Swal.fire({
            title: 'Update Reserved Stock',
            html: `
                        <input type="hidden" id="stock_id" value="${id}">
                        <div class="form-group" style="text-align:left;">
                        <label>Reserved Quantity</label>
                        <input type="number" id="reserved_quantity" class="form-control" value="${reserved}" min="0" step="0.01">
                        </div>`,
            showCancelButton: true,
            confirmButtonText: '<i class="fa fa-save"></i> Update',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const qty = parseFloat(document.getElementById('reserved_quantity').value);
                const total = parseFloat(stock.quantity);

                if (isNaN(qty) || qty <= 0) {
                    Swal.showValidationMessage('Reserved quantity must be greater than 0');
                    return false;
                }

                if (qty >= total) {
                    Swal.showValidationMessage('Reserved quantity must be less than total quantity');
                    return false;
                }

                return {
                    id: document.getElementById('stock_id').value,
                    reserved_quantity: qty
                };
            }
        }).then(result => {
            if (result.isConfirmed && result.value) {
                saveReservedStock(result.value);
            }
        });

    }

    function saveReservedStock(formData) {

        Swal.fire({
            title: 'Processing...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const data = new FormData();

        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'update');
        data.append('id', formData.id);
        data.append('reserved_quantity', formData.reserved_quantity);

        fetch('index.php?r=stock/inventoryreservedstock', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {

                if (res.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
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

                Swal.fire(
                    'Error',
                    'Unable to update reserved stock.',
                    'error'
                );

            });

    }
</script>
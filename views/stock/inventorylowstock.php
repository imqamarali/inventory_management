<?php

use yii\helpers\Html;

if (!isset($warehouses)) $warehouses = [];
if (!isset($categories)) $categories = [];
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=stock/dashboard">Home</a>
                </li>
                <li class="active">Low Stock Items</li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <input type="text" id="keyword" class="new-input" style="width:20%"
                placeholder="Product / SKU">

            <select id="warehouse_id" class="new-input" style="width:20%">
                <option value="">All Warehouses</option>
                <?php foreach ($warehouses as $w) { ?>
                    <option value="<?= $w['id'] ?>"><?= Html::encode($w['warehouse_name']) ?></option>
                <?php } ?>
            </select>

            <select id="category_id" class="new-input" style="width:20%">
                <option value="">All Categories</option>
                <?php foreach ($categories as $c) { ?>
                    <option value="<?= $c['id'] ?>"><?= Html::encode($c['category_name']) ?></option>
                <?php } ?>
            </select>

            <input type="number" id="per_page" class="new-input" style="width:10%" value="10" placeholder="Records">

            <input type="button" class="btn btn-primary" onclick="searchLowStock()" value="Search"
                style="height:30px;padding:0;margin-top:-3px;">
        </div>

        <div class="widget-main">
            <div class="alert alert-warning" style="margin-top:10px;">
                <i class="ace-icon fa fa-warning"></i>
                Showing stock items whose quantity has fallen to or below their reorder level. This is a read-only report;
                to change stock levels use Current Stock, Stock Adjustment, or Stock Movement.
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="lowstock_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Warehouse</th>
                            <th>Category</th>
                            <th>Current Qty</th>
                            <th>Minimum Stock</th>
                            <th>Reorder Level</th>
                            <th>Suggested Reorder Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
                <div id="paginationArea" class="text-center"></div>
            </div>
        </div>

    </div>
</div>

<script>
    setTimeout(function() {
        $('.chzn-select').chosen({
            search_contains: true,
            no_results_text: "No record found"
        });
    }, 500);

    searchLowStock();

    function searchLowStock(page = 1) {

        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'search');
        data.append('page', page);
        data.append('per_page', $('#per_page').val());
        data.append('keyword', $('#keyword').val());
        data.append('warehouse_id', $('#warehouse_id').val());
        data.append('category_id', $('#category_id').val());

        fetch('index.php?r=stock/inventorylowstock', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderLowStock(res.data);
                    renderPagination(res.page, res.total, res.limit);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.close();
                Swal.fire('Error', 'Unable to load low stock report.', 'error');
            });

    }

    function renderLowStock(rows) {
        let html = '';
        if (rows.length == 0) {
            html = '<tr><td colspan="8" class="text-center">No Low Stock Items Found</td></tr>';
        } else {
            rows.forEach(function(item, i) {
                html += `
                    <tr>
                    <td>${i+1}</td>
                    <td>${item.product_name}<br><small>${item.sku??''}</small></td>
                    <td>${item.warehouse_name}</td>
                    <td>${item.category_name??''}</td>
                    <td><span class="label label-warning">${parseFloat(item.quantity).toFixed(2)}</span></td>
                    <td>${parseFloat(item.minimum_stock??0).toFixed(2)}</td>
                    <td>${parseFloat(item.reorder_level??0).toFixed(2)}</td>
                    <td>${parseFloat(item.suggested_reorder_qty??0).toFixed(2)}</td>
                    </tr>`;
            });
        }
        $('#lowstock_table tbody').html(html);
    }

    function renderPagination(page, total, limit) {
        let pages = Math.ceil(total / limit);
        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `
                <button class="${i==page?'btn-primary':'btn-default'}"
                onclick="searchLowStock(${i})">
                ${i}
                </button>`;
        }
        $('#paginationArea').html(html);
    }
</script>

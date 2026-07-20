<?php

use yii\helpers\Html;

if (!isset($categories)) $categories = [];
if (!isset($brands)) $brands = [];
?>

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=stock/dashboard">Home</a>
                </li>
                <li class="active">Reorder Report</li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <input type="text" id="keyword" class="new-input" style="width:20%" placeholder="Product / SKU">

            <select id="category_id" class="chzn-select" style="width:20%">
                <option value="">All Categories</option>
                <?php foreach ($categories as $c) { ?>
                    <option value="<?= $c['id'] ?>"><?= Html::encode($c['category_name']) ?></option>
                <?php } ?>
            </select>

            <select id="brand_id" class="chzn-select" style="width:20%">
                <option value="">All Brands</option>
                <?php foreach ($brands as $b) { ?>
                    <option value="<?= $b['id'] ?>"><?= Html::encode($b['brand_name']) ?></option>
                <?php } ?>
            </select>

            <input type="number" id="per_page" class="new-input" style="width:10%" value="10" placeholder="Records">

            <input type="button" class="btn btn-primary" onclick="searchReorder()" value="Search"
                style="height:30px;padding:0;margin-top:-3px;">
        </div>

        <div class="widget-main">
            <div class="alert alert-info" style="margin-top:10px;">
                <i class="ace-icon fa fa-info-circle"></i>
                Company-wide view of products whose total stock across all warehouses has fallen to or below their reorder
                level, with a suggested reorder quantity up to maximum stock. Read-only.
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="reorder_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Current Qty</th>
                            <th>Minimum</th>
                            <th>Reorder Level</th>
                            <th>Maximum</th>
                            <th>Suggested Reorder Qty</th>
                            <th>Purchase Price</th>
                            <th>Estimated Reorder Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="11" class="text-center">Loading...</td>
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

    searchReorder();

    function searchReorder(page = 1) {

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
        data.append('category_id', $('#category_id').val());
        data.append('brand_id', $('#brand_id').val());

        fetch('index.php?r=stock/inventoryreorderreport', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderReorder(res.data);
                    renderPagination(res.page, res.total, res.limit);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.close();
                Swal.fire('Error', 'Unable to load reorder report.', 'error');
            });

    }

    function renderReorder(rows) {
        let html = '';
        if (rows.length == 0) {
            html = '<tr><td colspan="11" class="text-center">No Products Need Reordering</td></tr>';
        } else {
            rows.forEach(function(item, i) {
                html += `
                    <tr>
                    <td>${i+1}</td>
                    <td>${item.product_name}<br><small>${item.sku??''}</small></td>
                    <td>${item.category_name??''}</td>
                    <td>${item.brand_name??''}</td>
                    <td><span class="label label-warning">${parseFloat(item.current_quantity).toFixed(2)}</span></td>
                    <td>${parseFloat(item.minimum_stock??0).toFixed(2)}</td>
                    <td>${parseFloat(item.reorder_level??0).toFixed(2)}</td>
                    <td>${parseFloat(item.maximum_stock??0).toFixed(2)}</td>
                    <td><b>${parseFloat(item.suggested_reorder_qty??0).toFixed(2)}</b></td>
                    <td>${parseFloat(item.purchase_price??0).toFixed(2)}</td>
                    <td>${parseFloat(item.estimated_reorder_cost??0).toFixed(2)}</td>
                    </tr>`;
            });
        }
        $('#reorder_table tbody').html(html);
    }

    function renderPagination(page, total, limit) {
        let pages = Math.ceil(total / limit);
        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `
                <button class=" ${i==page?'btn-primary':'btn-default'}"
                onclick="searchReorder(${i})">
                ${i}
                </button>`;
        }
        $('#paginationArea').html(html);
    }
</script>

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
                <li class="active">Stock Valuation</li>
            </ul>
        </div>

        <div style="padding-top:10px;padding-left:13px;">
            <input type="text" id="keyword" class="new-input" style="width:20%" placeholder="Product / SKU">

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

            <input type="button" class="btn btn-primary" onclick="searchValuation()" value="Search"
                style="height:30px;padding:0;margin-top:-3px;">
        </div>

        <div class="widget-main">

            <div class="row" style="margin-top:10px;">
                <div class="col-md-4">
                    <div class="alert alert-info text-center">
                        <div style="font-size:12px;">Total Stock Value</div>
                        <div style="font-size:20px;font-weight:bold;" id="summary_stock_value">PKR 0</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success text-center">
                        <div style="font-size:12px;">Total Potential Sales Value</div>
                        <div style="font-size:20px;font-weight:bold;" id="summary_sales_value">PKR 0</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning text-center">
                        <div style="font-size:12px;">Total Products</div>
                        <div style="font-size:20px;font-weight:bold;" id="summary_total_products">0</div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="ace-icon fa fa-info-circle"></i>
                This is a read-only valuation report generated from current stock and average cost.
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="valuation_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Warehouse</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Quantity</th>
                            <th>Average Cost</th>
                            <th>Stock Value</th>
                            <th>Selling Price</th>
                            <th>Potential Sales Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="10" class="text-center">Loading...</td>
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

    searchValuation();

    function searchValuation(page = 1) {

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

        fetch('index.php?r=stock/inventorystockvaluation', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderValuation(res.data);
                    renderSummary(res.summary);
                    renderPagination(res.page, res.total, res.limit);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.close();
                Swal.fire('Error', 'Unable to load stock valuation report.', 'error');
            });

    }

    function renderSummary(summary) {
        if (!summary) return;
        $('#summary_stock_value').text('PKR ' + Number(summary.total_stock_value || 0).toLocaleString(undefined, {maximumFractionDigits: 2}));
        $('#summary_sales_value').text('PKR ' + Number(summary.total_potential_sales_value || 0).toLocaleString(undefined, {maximumFractionDigits: 2}));
        $('#summary_total_products').text(Number(summary.total_products || 0).toLocaleString());
    }

    function renderValuation(rows) {
        let html = '';
        if (rows.length == 0) {
            html = '<tr><td colspan="10" class="text-center">No Stock Found</td></tr>';
        } else {
            rows.forEach(function(item, i) {
                html += `
                    <tr>
                    <td>${i+1}</td>
                    <td>${item.product_name}<br><small>${item.sku??''}</small></td>
                    <td>${item.warehouse_name}</td>
                    <td>${item.category_name??''}</td>
                    <td>${item.brand_name??''}</td>
                    <td>${parseFloat(item.quantity).toFixed(2)}</td>
                    <td>${parseFloat(item.average_cost).toFixed(2)}</td>
                    <td><b>${parseFloat(item.stock_value).toFixed(2)}</b></td>
                    <td>${parseFloat(item.selling_price??0).toFixed(2)}</td>
                    <td>${parseFloat(item.potential_sales_value??0).toFixed(2)}</td>
                    </tr>`;
            });
        }
        $('#valuation_table tbody').html(html);
    }

    function renderPagination(page, total, limit) {
        let pages = Math.ceil(total / limit);
        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `
                <button class="${i==page?'btn-primary':'btn-default'}"
                onclick="searchValuation(${i})">
                ${i}
                </button>`;
        }
        $('#paginationArea').html(html);
    }
</script>

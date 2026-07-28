<?php
use yii\helpers\Html;

if (!isset($products)) {
    $products = [];
}
if (!isset($categories)) {
    $categories = [];
}
if (!isset($brands)) {
    $brands = [];
}
if (!isset($models)) {
    $models = [];
}
if (!isset($units)) {
    $units = [];
}
?>

<style>
    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .search-filter-group {
        display: flex;
        gap: 10px;
        flex: 1;
        min-width: 300px;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-filter-group input, .search-filter-group select {
        height: 36px;
        font-size: 13px;
        padding: 8px 12px;
        min-width: 150px;
    }

    .products-table-container {
        overflow-x: auto;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .products-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    .products-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .products-table thead th {
        padding: 15px;
        font-weight: 600;
        text-align: left;
        font-size: 13px;
        border-bottom: 2px solid #667eea;
    }

    .products-table tbody tr {
        border-bottom: 1px solid #E8EDF2;
        transition: background-color 0.2s ease;
    }

    .products-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .products-table tbody tr.filtered-out {
        display: none;
    }

    .products-table td {
        padding: 12px 15px;
        font-size: 13px;
        color: #333;
    }

    .category-badge {
        display: inline-block;
        background: #e3f2fd;
        color: #1976d2;
        padding: 4px 8px;
        border-radius: 3px;
        font-weight: 500;
    }

    .brand-badge {
        display: inline-block;
        background: #f3e5f5;
        color: #6a1b9a;
        padding: 4px 8px;
        border-radius: 3px;
        font-weight: 500;
    }

    .price-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-weight: 500;
    }

    .price-badge.cost {
        background: #fff3e0;
        color: #e65100;
    }

    .price-badge.sell {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .sku-code {
        font-family: 'Courier New', monospace;
        background: #f5f5f5;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 12px;
    }

    .stock-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 600;
    }

    .stock-badge.low {
        background: #ffebee;
        color: #c62828;
    }

    .stock-badge.medium {
        background: #fff3e0;
        color: #e65100;
    }

    .stock-badge.high {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .action-buttons button {
        padding: 6px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
    }

    .edit-btn {
        background: #4CAF50;
        color: white;
    }

    .edit-btn:hover {
        background: #45a049;
    }

    .delete-btn {
        background: #f44336;
        color: white;
    }

    .delete-btn:hover {
        background: #da190b;
    }

    .stats-row {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        justify-content: flex-start;
        flex-wrap: wrap;
    }

    .stat-card {
        background: white;
        padding: 15px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        flex: 1;
        max-width: 200px;
        text-align: center;
        min-width: 150px;
    }

    .stat-card .stat-value {
        font-size: 24px;
        font-weight: 600;
        color: #667eea;
    }

    .stat-card .stat-label {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }

    .no-data-alert {
        text-align: center;
        padding: 40px;
    }

    .no-data-alert i {
        color: #6FB3E0;
        margin-bottom: 15px;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li class="active"><i class="fa fa-cubes"></i> Products</li>
            </ul>
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <!-- Statistics -->
                    <div class="stats-row">
                        <div class="stat-card">
                            <div class="stat-value"><?= count($products) ?></div>
                            <div class="stat-label">Total Products</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?= count($categories) ?></div>
                            <div class="stat-label">Categories</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?= count($brands) ?></div>
                            <div class="stat-label">Brands</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="totalStock">0</div>
                            <div class="stat-label">Total Stock</div>
                        </div>
                    </div>

                    <!-- Search & Filter Section -->
                    <div class="products-header" style="margin-bottom: 20px;">
                        <div class="search-filter-group">
                            <i class="fa fa-search" style="color: #999;"></i>
                            <input type="text" id="productNameSearch" class="form-control"
                                placeholder="Search by product name...">

                            <input type="text" id="skuSearch" class="form-control"
                                placeholder="Search by SKU...">

                            <select id="categoryFilter" class="form-control" style="max-width: 180px;">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['category_name']) ?>">
                                        <?= htmlspecialchars($cat['category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select id="brandFilter" class="form-control" style="max-width: 180px;">
                                <option value="">All Brands</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= htmlspecialchars($brand['brand_name']) ?>">
                                        <?= htmlspecialchars($brand['brand_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <button id="clearFilters" class="btn btn-sm btn-default" title="Clear all filters">
                                <i class="fa fa-times"></i> Clear
                            </button>
                        </div>

                        <div>
                            <button class="btn btn-sm btn-primary" onclick="openProductModal()">
                                <i class="fa fa-plus"></i> Add Product
                            </button>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="products-table-container">
                        <?php if (count($products) == 0) { ?>
                            <div class="alert alert-info no-data-alert">
                                <i class="fa fa-info-circle fa-3x"></i>
                                <h4 style="margin-top:15px;">No Products Found</h4>
                                <p>Start by adding your first product using the button above</p>
                            </div>
                        <?php } else { ?>
                            <table class="products-table">
                                <thead>
                                    <tr>
                                        <th style="width: 150px;">Product Name</th>
                                        <th style="width: 120px;">SKU</th>
                                        <th style="width: 120px;">Category</th>
                                        <th style="width: 120px;">Brand</th>
                                        <th style="width: 100px;">Purchase Price</th>
                                        <th style="width: 100px;">Selling Price</th>
                                        <th style="width: 90px;">Stock</th>
                                        <th style="width: 100px;">Min/Max</th>
                                        <th style="width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTable">
                                    <?php foreach ($products as $item): ?>
                                        <tr class="product-row"
                                            data-product-name="<?= htmlspecialchars(strtolower($item['product_name'] ?? '')) ?>"
                                            data-sku="<?= htmlspecialchars(strtolower($item['sku'] ?? '')) ?>"
                                            data-category="<?= htmlspecialchars($item['category_name'] ?? '') ?>"
                                            data-brand="<?= htmlspecialchars($item['brand_name'] ?? '') ?>">

                                            <td>
                                                <strong><?= htmlspecialchars($item['product_name'] ?? 'N/A') ?></strong>
                                            </td>
                                            <td>
                                                <span class="sku-code"><?= htmlspecialchars($item['sku'] ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <span class="category-badge">
                                                    <i class="fa fa-tag"></i> <?= htmlspecialchars($item['category_name'] ?? '-') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="brand-badge">
                                                    <i class="fa fa-certificate"></i> <?= htmlspecialchars($item['brand_name'] ?? '-') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="price-badge cost">
                                                    <i class="fa fa-plus-circle"></i> PKR <?= number_format($item['purchase_price'] ?? 0, 2) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="price-badge sell">
                                                    <i class="fa fa-check-circle"></i> PKR <?= number_format($item['selling_price'] ?? 0, 2) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="stock-badge <?= ($item['quantity'] ?? 0) <= ($item['minimum_stock'] ?? 10) ? 'low' : (($item['quantity'] ?? 0) <= ($item['maximum_stock'] ?? 50) ? 'medium' : 'high') ?>">
                                                    <?= number_format($item['quantity'] ?? 0) ?> units
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    Min: <strong><?= $item['minimum_stock'] ?? '-' ?></strong><br>
                                                    Max: <strong><?= $item['maximum_stock'] ?? '-' ?></strong>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="edit-btn" onclick="openProductModal(<?= htmlspecialchars(json_encode($item)) ?>)" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="delete-btn" onclick="deleteProduct(<?= $item['id'] ?>)" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Real-time search and filter
    function applyFilters() {
        const productNameText = document.getElementById('productNameSearch').value.toLowerCase();
        const skuText = document.getElementById('skuSearch').value.toLowerCase();
        const categoryFilter = document.getElementById('categoryFilter').value;
        const brandFilter = document.getElementById('brandFilter').value;

        let visibleCount = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            let show = true;

            // Product name filter
            if (productNameText && !row.dataset.productName.includes(productNameText)) {
                show = false;
            }

            // SKU filter
            if (skuText && !row.dataset.sku.includes(skuText)) {
                show = false;
            }

            // Category filter
            if (categoryFilter && row.dataset.category !== categoryFilter) {
                show = false;
            }

            // Brand filter
            if (brandFilter && row.dataset.brand !== brandFilter) {
                show = false;
            }

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        // Update results message
        const table = document.getElementById('productsTable');
        if (visibleCount === 0 && document.querySelectorAll('.product-row').length > 0) {
            if (!document.getElementById('noResultsMessage')) {
                const msg = document.createElement('tr');
                msg.id = 'noResultsMessage';
                msg.innerHTML = '<td colspan="9" style="text-align: center; padding: 20px;">No products match your search criteria</td>';
                table.appendChild(msg);
            }
        } else {
            const msg = document.getElementById('noResultsMessage');
            if (msg) msg.remove();
        }
    }

    // Event listeners for filters
    document.getElementById('productNameSearch').addEventListener('keyup', applyFilters);
    document.getElementById('skuSearch').addEventListener('keyup', applyFilters);
    document.getElementById('categoryFilter').addEventListener('change', applyFilters);
    document.getElementById('brandFilter').addEventListener('change', applyFilters);

    document.getElementById('clearFilters').addEventListener('click', function() {
        document.getElementById('productNameSearch').value = '';
        document.getElementById('skuSearch').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('brandFilter').value = '';
        applyFilters();
    });

    function openProductModal(productData = null) {
        // Implementation for opening product modal
        alert('Open product modal');
    }

    function deleteProduct(id) {
        // Implementation for deleting product
        alert('Delete product with ID: ' + id);
    }
</script>

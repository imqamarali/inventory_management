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
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li class="active">Products</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary"
                                style="font-size:12px;cursor:pointer;"
                                onclick="openProductModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add New Product
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div style="padding-top: 10px;padding-left: 13px;">
            <form id="members_search" name="members_search" method="get" action="index.php">
                <input type="hidden" name="r" value="products/productlist">
                <div id="" class="errorMessage" style="display: none; color:#F00; font-weight:bold;"></div>
                <div class="float-left">
                    <input type="text"
                        value="<?= Html::encode(Yii::$app->request->get('product_name')) ?>"
                        name="product_name"
                        id="product_name"
                        class="new-input"
                        style="width:17%"
                        placeholder="Enter Product Name">
                    <input type="text"
                        value="<?= Html::encode(Yii::$app->request->get('sku')) ?>"
                        name="sku"
                        id="sku"
                        class="new-input"
                        style="width:14%"
                        placeholder="Enter SKU">
                    <input type="text" value="<?= $perPage ?? "50" ?>" name="per_page"
                        id="per_page" style="width:10%" class="new-input" placeholder="Records?" />

                    <select name="category_id[]" id="category_id" class="chzn-select" multiple="" style="width:20%">
                        <option value="">Select Catrgories</option>
                        <?php foreach ($categories as $cat) { ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= Yii::$app->request->get('category_id') == $cat['id'] ? 'selected' : '' ?>>
                                <?= Html::encode($cat['category_name']) ?>
                            </option>
                        <?php } ?>
                    </select>
                    <select name="brand_id[]" id="brand_id" class="chzn-select" multiple="" style="width:15%">
                        <option value="">All Brands</option>
                        <?php foreach ($brands as $row) { ?>
                            <option value="<?= $row['id'] ?>"
                                <?= Yii::$app->request->get('brand_id') == $row['id'] ? 'selected' : '' ?>>
                                <?= Html::encode($row['brand_name']) ?>
                            </option>
                        <?php } ?>
                    </select>
                    <select name="model_id[]" id="model_id" class="chzn-select" multiple="" style="width:15%">
                        <option value="">All Units</option>
                        <?php foreach ($models as $row) { ?>
                            <option value="<?= $row['id'] ?>"
                                <?= Yii::$app->request->get('model_id') == $row['id'] ? 'selected' : '' ?>>
                                <?= Html::encode($row['model_name']) ?>
                            </option>
                        <?php } ?>
                    </select>
                    <input type="button" class="btn btn-primary" onclick="searchform()" value="Search" style="height: 30px;padding: 0px;margin-top: -3px;" />
            </form>
        </div>
    </div>
    <div class="widget-main">
        <?php if (count($products) == 0) { ?>
            <div class="alert alert-info text-center">
                <i class="ace-icon fa fa-info-circle fa-3x" style="color:#6FB3E0;"></i>
                <h4 style="margin-top:15px;">No Products Found</h4>
                <p>Start by adding your first product using the button above</p>
            </div>
        <?php } else { ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="products_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Unit</th>
                            <th>Purchase Price</th>
                            <th>Selling Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $key => $item): ?>
                            <tr class="product-item">
                                <td><?= $key + 1 ?></td>
                                <td>
                                    <i class="fa fa-cube"></i>
                                    <?= htmlspecialchars($item['product_name'] ?? "NA") ?>
                                </td>
                                <td><?= htmlspecialchars($item['sku'] ?? "NA") ?></td>
                                <td><?= htmlspecialchars($item['category_name'] ?? "NA") ?></td>
                                <td><?= htmlspecialchars($item['brand_name'] ?? "NA") ?></td>
                                <td><?= htmlspecialchars($item['model_name'] ?? "NA") ?></td>
                                <td><?= htmlspecialchars($item['unit_name'] ?? "NA") ?></td>
                                <td><?= htmlspecialchars($item['purchase_price'] ?? "NA") ?></td>
                                <td><?= htmlspecialchars($item['selling_price'] ?? "NA") ?></td>
                                <td>
                                    Min: <?= htmlspecialchars($item['minimum_stock'] ?? "NA") ?><br>
                                    Max: <?= htmlspecialchars($item['maximum_stock'] ?? "NA") ?>
                                </td>
                                <td>
                                    <?php if ($item['is_active'] == 1) { ?>
                                        <span class="label label-success">
                                            <i class="fa fa-check"></i> Active
                                        </span>
                                    <?php } else { ?>
                                        <span class="label label-danger">
                                            <i class="fa fa-times"></i> Inactive
                                        </span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <button type="button"
                                        onclick="openProductModal(<?= htmlspecialchars(json_encode($item)) ?>)"
                                        title="Edit Product">
                                        <i class="ace-icon fa fa-pencil"></i>
                                    </button>
                                    &nbsp;|&nbsp;
                                    <button type="button"
                                        onclick="deleteProduct(<?= $item['id'] ?>)"
                                        title="Delete Product">
                                        <i class="ace-icon fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div id="paginationArea" class="text-center"></div>
            </div>
        <?php } ?>
    </div>

</div>
</div>
<script>
    let categories = <?= json_encode($categories) ?>;
    let brands = <?= json_encode($brands) ?>;
    let models = <?= json_encode($models) ?>;
    let units = <?= json_encode($units) ?>;
</script>

<script>
    setTimeout(function() {
        $('.chzn-select').each(function() {

            if ($(this).hasClass('chosen-container')) {
                return;
            }

            $(this).chosen({
                search_contains: true,
                no_results_text: "No record found"
            });

        });

    }, 500);

    function searchform(page = 1) {
        Swal.fire({
            title: 'Loading Products...',
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
        data.append('product_name', $('#product_name').val());
        data.append('sku', $('#sku').val());
        data.append('per_page', $('#per_page').val());
        data.append('page', page);
        ($('#category_id').val() || []).forEach(function(v) {
            data.append('category_id[]', v);
        });
        ($('#brand_id').val() || []).forEach(function(v) {
            data.append('brand_id[]', v);
        });
        ($('#unit_id').val() || []).forEach(function(v) {
            data.append('unit_id[]', v);
        });

        fetch('index.php?r=products/productlist', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    renderProducts(res.products);
                    renderPagination(res.page, res.total_pages);
                } else {
                    Swal.fire('Error', res.message || 'No data found', 'error');
                }
            })
            .catch(function() {
                Swal.close();
                Swal.fire(
                    'Error',
                    'Unable to fetch products.',
                    'error'
                );
            });
    }

    function renderProducts(products) {
        let html = '';
        if (products.length === 0) {
            html = `
        <tr>
            <td colspan="11" class="text-center">
                No Products Found
            </td>
        </tr>`;
        } else {

            products.forEach(function(item, index) {
                html += `
            <tr> 
                <td>${index+1}</td>
                <td>${item.product_name}</td>
                <td>${item.sku ?? ''}</td>
                <td>${item.category_name ?? ''}</td>
                <td>${item.brand_name ?? ''}</td>
                <td>${item.model_name ?? ''}</td>
                <td>${item.unit_name ?? ''}</td>
                <td>${item.purchase_price}</td>
                <td>${item.selling_price}</td>
                <td>
                    Min : ${item.minimum_stock}<br>
                    Max : ${item.maximum_stock}
                </td>
                <td>
                    ${item.is_active==1
                        ?'<span class="label label-success">Active</span>'
                        :'<span class="label label-danger">Inactive</span>'
                    }
                </td>
                <td>
                    <button
                        onclick='openProductModal(${JSON.stringify(item)})'>
                        <i class="fa fa-pencil"></i>
                    </button>
                    |
                    <button
                        onclick="deleteProduct(${item.id})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;
            });
        }
        $('#products_table tbody').html(html);
    }

    function renderPagination(page, totalPages) {
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `
        <button
            class=" ${i==page?'btn-primary':'btn-default'}"
            onclick="searchform(${i})">
            ${i}
        </button>
        `;
        }
        $('#paginationArea').html(html);

    }

    function openProductModal(productData = null) {
        const isEdit = productData !== null;
        const title = isEdit ? 'Update Product' : 'New Product';
        const id = isEdit ? (productData.id || '') : '';
        const categoryId = isEdit ? (productData.category_id || '') : '';
        const brandId = isEdit ? (productData.brand_id || '') : '';
        const modelId = isEdit ? (productData.model_id || '') : '';
        const unitId = isEdit ? (productData.unit_id || '') : '';
        const name = isEdit ? (productData.product_name || '') : '';
        const sku = isEdit ? (productData.sku || '') : '';
        const barcode = isEdit ? (productData.barcode || '') : '';
        const description = isEdit ? (productData.description || '') : '';
        const purchase = isEdit ? (productData.purchase_price || '') : '';
        const selling = isEdit ? (productData.selling_price || '') : '';
        const min = isEdit ? (productData.minimum_stock || '') : '';
        const max = isEdit ? (productData.maximum_stock || '') : '';
        const reorder = isEdit ? (productData.reorder_level || '') : '';
        const weight = isEdit ? (productData.weight || '') : '';
        const length = isEdit ? (productData.length || '') : '';
        const width = isEdit ? (productData.width || '') : '';
        const height = isEdit ? (productData.height || '') : '';
        const active = isEdit && (productData.is_active == 1 || productData.is_active == '1');

        let categoryOptions = '<option value="">Select Category</option>';
        categories.forEach(item => {
            categoryOptions += `<option value="${item.id}" ${item.id==categoryId?'selected':''}>${item.category_name}</option>`;
        });

        let brandOptions = '<option value="">Select Brand</option>';
        brands.forEach(item => {
            brandOptions += `<option value="${item.id}" ${item.id==brandId?'selected':''}>${item.brand_name}</option>`;
        });
        let modelOptions = '<option value="">Select Model</option>';
        models.forEach(item => {
            modelOptions += `<option value="${item.id}" ${item.id==modelId?'selected':''}>${item.model_name}</option>`;
        });
        let unitOptions = '<option value="">Select Unit</option>';
        units.forEach(item => {
            unitOptions += `<option value="${item.id}" ${item.id==unitId?'selected':''}>${item.unit_name}</option>`;
        });

        Swal.fire({
            title: title,
            html: `
        <form id="productForm" style="text-align:left;">
        <input type="hidden" id="swal_product_id" value="${id}">
        <div class="row">
        <div class="col-md-4">
        <label>Product Name <span class="text-danger">*</span></label>
        <input type="text" id="swal_product_name" class="form-control" value="${name}">
        </div>
        <div class="col-md-4">
        <label>SKU</label>
        <input type="text" id="swal_sku" class="form-control" value="${sku}">
        </div>
        <div class="col-md-4">
        <label>Category</label>
        <select id="swal_category_id" class="form-control">${categoryOptions}</select>
        </div>
        </div>
        <div class="row">
        <div class="col-md-4">
        <label>Brand</label>
        <select id="swal_brand_id" class="form-control">${brandOptions}</select>
        </div>
        <div class="col-md-4">
        <label>Model</label>
        <select id="swal_model_id" class="form-control">${modelOptions}</select>
        </div>
        <div class="col-md-4">
        <label>Unit</label>
        <select id="swal_unit_id" class="form-control">${unitOptions}</select>
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
        <label>Barcode</label>
        <input type="text" id="swal_barcode" class="form-control" value="${barcode}">
        </div>
        <div class="col-md-6">
        <label>Description</label>
        <input type="text" id="swal_description" class="form-control" value="${description}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
        <label>Purchase Price</label>
        <input type="number" id="swal_purchase_price" class="form-control" value="${purchase}">
        </div>
        <div class="col-md-6">
        <label>Selling Price</label>
        <input type="number" id="swal_selling_price" class="form-control" value="${selling}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-4">
        <label>Minimum Stock</label>
        <input type="number" id="swal_minimum_stock" class="form-control" value="${min}">
        </div>
        <div class="col-md-4">
        <label>Maximum Stock</label>
        <input type="number" id="swal_maximum_stock" class="form-control" value="${max}">
        </div>
        <div class="col-md-4">
        <label>Reorder Level</label>
        <input type="number" id="swal_reorder_level" class="form-control" value="${reorder}">
        </div>
        </div>
        <div class="row">
        <div class="col-md-3">
        <label>Weight</label>
        <input type="text" id="swal_weight" class="form-control" value="${weight}">
        </div>
        <div class="col-md-3">
        <label>Length</label>
        <input type="text" id="swal_length" class="form-control" value="${length}">
        </div>
        <div class="col-md-3">
        <label>Width</label>
        <input type="text" id="swal_width" class="form-control" value="${width}">
        </div>
        <div class="col-md-3">
        <label>Height</label>
        <input type="text" id="swal_height" class="form-control" value="${height}">
        </div>
        </div>
        <div class="form-group" style="margin-top:10px;">
        <label>
        <input type="checkbox" id="swal_active" ${active?'checked':''}>
        Active
        </label>
        </div>
        </form>`,
            width: '800px',
            showCancelButton: true,
            confirmButtonText: isEdit ? '<i class="ace-icon fa fa-save"></i> Update Product' : '<i class="ace-icon fa fa-save"></i> Create Product',
            cancelButtonText: '<i class="ace-icon fa fa-times"></i> Cancel',
            confirmButtonColor: '#87B87F',
            cancelButtonColor: '#6c757d',
            focusConfirm: false,
            preConfirm: () => {
                const productName = document.getElementById('swal_product_name').value.trim();
                if (!productName) {
                    Swal.showValidationMessage('Product name is required');
                    return false;
                }
                return {
                    id: document.getElementById('swal_product_id').value,
                    category_id: document.getElementById('swal_category_id').value,
                    brand_id: document.getElementById('swal_brand_id').value,
                    model_id: document.getElementById('swal_model_id').value,
                    unit_id: document.getElementById('swal_unit_id').value,
                    product_name: productName,
                    sku: document.getElementById('swal_sku').value,
                    barcode: document.getElementById('swal_barcode').value,
                    description: document.getElementById('swal_description').value,
                    purchase_price: document.getElementById('swal_purchase_price').value,
                    selling_price: document.getElementById('swal_selling_price').value,
                    minimum_stock: document.getElementById('swal_minimum_stock').value,
                    maximum_stock: document.getElementById('swal_maximum_stock').value,
                    reorder_level: document.getElementById('swal_reorder_level').value,
                    weight: document.getElementById('swal_weight').value,
                    length: document.getElementById('swal_length').value,
                    width: document.getElementById('swal_width').value,
                    height: document.getElementById('swal_height').value,
                    active: document.getElementById('swal_active').checked
                };
            }
        }).then(result => {
            if (result.isConfirmed && result.value) {
                saveProduct(result.value);
            }
        });
    }

    function saveProduct(formData) {
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

        Object.keys(formData).forEach(key => {
            if (key != 'active') {
                data.append(key, formData[key]);
            }
        });

        if (formData.active) {
            data.append('is_active', '1');
        }

        fetch('index.php?r=products/productlist', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('.ajax-module.active').trigger('click');
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'An error occurred. Please try again.', 'error');
            });
    }

    function deleteProduct(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Product will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if (result.isConfirmed) {
                const data = new FormData();
                data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
                data.append('id', id);
                data.append('delete', '1');

                fetch('index.php?r=products/productlist', {
                        method: 'POST',
                        body: data
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                $('.ajax-module.active').trigger('click');
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
            }
        });
    }
</script>
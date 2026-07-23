<?php

use yii\helpers\Html;

if (!isset($categories)) {
    $categories = [];
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
                <li class="active">Categories</li>
                <li style="width:50%;text-align:center;">
                    <input type="text" id="categorySearch" class="form-control"
                        placeholder="Search Categories..."
                        style="display:inline-block;width:300px;height:28px;font-size:12px;">
                </li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary"
                                style="font-size:12px;cursor:pointer;"
                                onclick="openCategoryModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add New Category
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="widget-body">
                        <div class="widget-main padding-12">
                            <?php if (count($categories) == 0) { ?>
                                <div class="alert alert-info text-center">
                                    <i class="ace-icon fa fa-info-circle fa-3x" style="color: #6FB3E0;"></i>
                                    <h4 style="margin-top: 15px;">No Categories Found</h4>
                                    <p>Start by adding your first category using the button above</p>
                                </div>
                            <?php } else { ?>
                                <div class="row" id="categories_container">
                                    <?php foreach ($categories as $key => $item):
                                    ?>
                                        <div class="col-md-4 col-sm-6 session-item category-item">
                                            <div class="class-card">
                                                <div class="class-header">
                                                    <div style="flex: 1;">
                                                        <div class="class-name">
                                                            <i class="fa fa-tags" style="margin-right: 8px;"></i>
                                                            <?php echo htmlspecialchars($item['category_name']??''); ?>
                                                        </div>
                                                    </div>

                                                    <div class="btn-group">
                                                        <button type="button"
                                                            onclick="openCategoryModal(<?php echo htmlspecialchars(json_encode($item)); ?>)"
                                                            title="Edit Category">
                                                            <i class="ace-icon fa fa-pencil"></i>
                                                        </button>
                                                        &nbsp;&nbsp;|&nbsp;&nbsp;
                                                        <button type="button"
                                                            onclick="deleteCategory(<?php echo $item['id']; ?>)"
                                                            title="Delete Category">
                                                            <i class="ace-icon fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="class-stats">

                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-barcode"></i>
                                                        <span>Code: <?php echo htmlspecialchars($item['category_code']??''); ?></span>
                                                    </div>

                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-sitemap"></i>
                                                        <span>Parent ID: <?php echo htmlspecialchars($item['parent_id'] ?? 'None'); ?></span>
                                                    </div>

                                                </div>

                                                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #E3E9ED;">

                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-align-left"></i>
                                                        <span>Description: <?php echo htmlspecialchars($item['description']??''); ?></span>
                                                    </div>

                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-calendar"></i>
                                                        <span>Created: <?php echo htmlspecialchars($item['created_at']??''); ?></span>
                                                    </div>

                                                    <div class="stat-item">
                                                        <i class="ace-icon fa fa-refresh"></i>
                                                        <span>Updated: <?php echo htmlspecialchars($item['updated_at']??''); ?></span>
                                                    </div>

                                                </div>

                                                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #E3E9ED; display: flex; justify-content: space-around;">

                                                    <div class="stat-mini">
                                                        <div style="font-size: 18px; font-weight: bold; color: #2196F3;">
                                                            <?php echo $item['total_products']; ?>
                                                        </div>
                                                        <div style="font-size: 12px; color: #666;">Total Products</div>
                                                    </div>

                                                    <div class="stat-mini">
                                                        <div style="font-size: 18px; font-weight: bold; color: #4CAF50;">
                                                            <?php echo $item['active_products']; ?>
                                                        </div>
                                                        <div style="font-size: 12px; color: #666;">Active</div>
                                                    </div>

                                                    <div class="stat-mini">
                                                        <div style="font-size: 18px; font-weight: bold; color: #FF9800;">
                                                            <?php echo $item['inactive_products']; ?>
                                                        </div>
                                                        <div style="font-size: 12px; color: #666;">Inactive</div>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('categorySearch').addEventListener('keyup', function () {
        let value = this.value.toLowerCase();
        document.querySelectorAll('.category-item').forEach(function (item) {
            let text = item.innerText.toLowerCase();
            item.style.display = text.includes(value) ? '' : 'none';
        });
    });
    function openCategoryModal(categoryData = null) {
        const isEdit = categoryData !== null;
        const title = isEdit ? 'Update Category' : 'New Category';
        const categoryId = isEdit ? (categoryData.id || '') : '';
        const parentId = isEdit ? (categoryData.parent_id || '') : '';
        const categoryName = isEdit ? (categoryData.category_name || '') : '';
        const categoryCode = isEdit ? (categoryData.category_code || '') : '';
        const description = isEdit ? (categoryData.description || '') : '';
        const isActive = isEdit && (categoryData.is_active == 1 || categoryData.is_active == '1');

        Swal.fire({
            title: title,
            html: `
        <form id="categoryForm" style="text-align:left;">
        <input type="hidden" id="swal_category_id" value="${categoryId}">

        <div class="row">
        <div class="col-md-6">
        <label>Category Name <span class="text-danger">*</span></label>
        <input type="text" id="swal_category_name" class="form-control" value="${categoryName}">
        </div>

        <div class="col-md-6">
        <label>Category Code <span class="text-danger">*</span></label>
        <input type="text" id="swal_category_code" class="form-control" value="${categoryCode}">
        </div>
        </div>

        <div class="row">
        <div class="col-md-6">
        <label>Parent Category</label>
        <input type="number" id="swal_parent_id" class="form-control" value="${parentId}">
        </div>

        <div class="col-md-6">
        <label>Description</label>
        <input type="text" id="swal_description" class="form-control" value="${description}">
        </div>
        </div>

        <div class="form-group" style="margin-top:10px;">
        <label>
        <input type="checkbox" id="swal_active" ${isActive ? 'checked' : ''}>
        Active
        </label>
        </div>

        </form>
        `,
            width: '700px',
            showCancelButton: true,
            confirmButtonText: isEdit ? '<i class="ace-icon fa fa-save"></i> Update Category' : '<i class="ace-icon fa fa-save"></i> Create Category',
            cancelButtonText: '<i class="ace-icon fa fa-times"></i> Cancel',
            confirmButtonColor: '#87B87F',
            cancelButtonColor: '#6c757d',
            focusConfirm: false,
            preConfirm: () => {

                const name = document.getElementById('swal_category_name').value.trim();
                const code = document.getElementById('swal_category_code').value.trim();

                if (!name) {
                    Swal.showValidationMessage('Category name is required');
                    return false;
                }

                if (!code) {
                    Swal.showValidationMessage('Category code is required');
                    return false;
                }

                return {
                    id: document.getElementById('swal_category_id').value,
                    parent_id: document.getElementById('swal_parent_id').value,
                    category_name: name,
                    category_code: code,
                    description: document.getElementById('swal_description').value,
                    active: document.getElementById('swal_active').checked
                };
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                saveCategory(result.value);
            }
        });
    }

    function saveCategory(formData) {
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
        data.append('id', formData.id);
        data.append('parent_id', formData.parent_id);
        data.append('category_name', formData.category_name);
        data.append('category_code', formData.category_code);
        data.append('description', formData.description);

        if (formData.active) {
            data.append('is_active', '1');
        }

        fetch('index.php?r=products/categories', {
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

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message
                    });

                }

            })
            .catch(error => {

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.'
                });

            });
    }

    function deleteCategory(id) {

        Swal.fire({
            title: 'Are you sure?',
            text: 'Category will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {

            if (result.isConfirmed) {

                const data = new FormData();

                data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
                data.append('id', id);
                data.append('delete', '1');

                fetch('index.php?r=products/categories', {
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
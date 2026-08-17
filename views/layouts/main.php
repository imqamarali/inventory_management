<?php

if (Yii::$app->session->hasFlash('toast')) {
    $toastMessage = Yii::$app->session->getFlash('toast');
    $js = "showToast('$toastMessage');";
    $this->registerJs($js, \yii\web\View::POS_READY);
}

use app\assets\AppAsset;

AppAsset::register($this);
$csrfToken = Yii::$app->request->csrfToken;

?>
<?php

if (Yii::$app->session->hasFlash('toast')) {
    $toastMessage = Yii::$app->session->getFlash('toast');
    $this->registerJs("showToast('$toastMessage');");
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta charset="utf-8" />
    <title><?= isset($this->title) ? $this->title . ' - Inventory System' : 'Inventory System' ?></title>
    <meta name="description" content="overview &amp; stats" />
    <link href="<?= Yii::$app->request->baseUrl . '/images/logos/webixPK.png' ?>" rel="icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken); ?>">
    <?= $this->render('head'); ?>
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/grid.css">
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/dashboard.css">
    
    
</head>

<?php
// Check if user is student for body class
$user_role_id = Yii::$app->session->get('user_array')['role_id'] ?? null;
$is_student = ($user_role_id == 4);
$body_class = $is_student ? 'no-skin student-role' : 'no-skin';
?>

<body class="<?= $body_class ?>">
    <?php $this->beginBody() ?>
    <?= $this->render('navbar'); ?>

    <div class="main-container ace-save-state" id="main-container">
        <script type="text/javascript">
            try {
                ace.settings.loadState('main-container')
            } catch (e) {}
        </script>
        <?php $role = Yii::$app->Component->CheckRole(); ?>
        <?php
        // Hide sidebar for students
        if (!$is_student) {
            echo $this->render('sidebar');
        }
        ?>

        <div class="main-content1">
            <div class="main-content1-inner">
            
            <?= $content ?? null ?>
            </div>
        </div><!-- /.main-content1 -->

        <?php if (empty($this->params['hideFooter'])): ?>
            <?= $this->render('footer'); ?>
        <?php endif; ?>
        <div id="toastBox"></div>

        <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
            <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
        </a>
    </div>
    <?php $this->endBody() ?>

    <!-- SweetAlert2 from CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Define the toolbar HTML
        const toolbarHTML = `
                        <div class="dt-buttons btn-group btn-group2" style="float: right;padding: 3px;border-bottom: 1.5px solid #669fc7;">
                            <a class="dt-buttons buttons-copy" tabindex="0" href="#" title="Copy" style="padding: 10px;">
                                <span><i class="fa fa-files-o"></i></span>
                            </a>
                            <a class="dt-buttons buttons-excel" tabindex="0" href="#" title="Excel"  style="padding: 10px;">
                                <span><i class="fa fa-file-excel-o"></i></span>
                            </a>
                            <a class="dt-buttons buttons-csv " tabindex="0" href="#" title="CSV"  style="padding: 10px;">
                                <span><i class="fa fa-file-text-o"></i></span>
                            </a>
                            <a class="dt-buttons buttons-pdf  btn-pdf" tabindex="0" href="#" title="PDF"  style="padding: 10px;">
                                <span><i class="fa fa-file-pdf-o"></i></span>
                            </a>
                            <a class="dt-buttons buttons-print" tabindex="0" href="#" title="Print"  style="padding: 10px;">
                                <span><i class="fa fa-print"></i></span>
                            </a>
                        </div>
                        <div class="dataTables_filter" style="float: left;">
                            <label>
                                <input type="search" class="table-search" placeholder="Search..." autocomplete="off"
                                    style="border: none; border-bottom: 1.5px solid #669fc7; width: 135%;">
                            </label>
                        </div>`;

        // Find all tables with the specified class
        const tables = document.querySelectorAll(
            "table.table.table-striped.table-bordered.table-hover, table.table-bordered"
        );
        tables.forEach((table, index) => {
            return;
            // Skip tables with the class 'no_items'
            if (table.classList.contains('no_items')) {
                return; // Skip this table and move to the next one
            }

            if (table.classList.contains('no_search')) {
                return; // Skip this table and move to the next one
            }

            // Create a container for the toolbar
            const toolbarContainer = document.createElement("div");
            toolbarContainer.className = "table-toolbar-container";
            toolbarContainer.innerHTML = toolbarHTML;

            // Insert the toolbar before the table
            table.parentNode.insertBefore(toolbarContainer, table);

            // Add event listener for search functionality
            const searchInput = toolbarContainer.querySelector(".table-search");
            searchInput.addEventListener("input", function() {
                const filterText = this.value.toLowerCase();
                const rows = table.querySelectorAll("tbody tr");
                rows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    row.style.display = rowText.includes(filterText) ? "" : "none";
                });
            });

            // Add functionality for copy, Excel, CSV, PDF, and print
            const buttons = toolbarContainer.querySelectorAll(".dt-buttons");
            buttons.forEach(button => {
                button.addEventListener("click", function(event) {
                    event.preventDefault();
                    const action = this.title.toLowerCase();
                    handleTableAction(action, table);
                });
            });
        });

        // Function to handle actions (copy, Excel, CSV, PDF, print)
        function handleTableAction(action, table) {
            const tableHtml = table.outerHTML;

            switch (action) {
                case "copy":
                    copyTableToClipboard(tableHtml);
                    alert("Table copied to clipboard!");
                    break;
                case "excel":
                    downloadTableAsFile(tableHtml, "table.xlsx",
                        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
                    break;
                case "csv":
                    const csv = convertTableToCSV(table);
                    downloadTableAsFile(csv, "table.csv", "text/csv");
                    break;
                case "pdf":
                    alert("PDF export functionality can be implemented with libraries like jsPDF.");
                    break;
                case "print":
                    printTable(tableHtml);
                    break;
            }
        }

        // Function to copy table HTML to clipboard
        function copyTableToClipboard(html) {
            const tempDiv = document.createElement("div");
            tempDiv.innerHTML = html;
            document.body.appendChild(tempDiv);
            const range = document.createRange();
            range.selectNode(tempDiv);
            window.getSelection().addRange(range);
            document.execCommand("copy");
            document.body.removeChild(tempDiv);
        }

        // Function to convert table to CSV format
        function convertTableToCSV(table) {
            const rows = table.querySelectorAll("tr");
            return Array.from(rows)
                .map(row => Array.from(row.cells).map(cell => `"${cell.textContent.trim()}"`).join(","))
                .join("\n");
        }

        // Function to download file
        function downloadTableAsFile(content, fileName, mimeType) {
            const blob = new Blob([content], {
                type: mimeType
            });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = fileName;
            link.click();
        }

        // Function to print table
        function printTable(html) {
            const printWindow = window.open("", "_blank");
            printWindow.document.write("<html><head><title>Print Table</title></head><body>");
            printWindow.document.write(html);
            printWindow.document.write("</body></html>");
            printWindow.document.close();
            printWindow.print();
        }
    </script>


    <script>
        // Helper: Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        } 
        $(document).ready(function() { 
 
            function adjustMainContentPosition() {
                var sidebar = $('#sidebar');
                var mainContent = $('.main-content1');

                if (sidebar.length && mainContent.length) {
                    if (sidebar.hasClass('menu-min')) {
                        mainContent.css({
                            'left': '43px',
                            'width': 'calc(100% - 43px)'
                        });
                    } else {
                        mainContent.css({
                            'left': '190px',
                            'width': 'calc(100% - 190px)'
                        });
                    }
                }
            }
 
            adjustMainContentPosition();
 
            $(document).on('collapse.ace.sidebar expand.ace.sidebar', function() {
                setTimeout(adjustMainContentPosition, 50);
            }); 
            $('#sidebar-collapse').on('click', function() {
                setTimeout(adjustMainContentPosition, 350); // Wait for animation
            });
 
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        setTimeout(adjustMainContentPosition, 50);
                    }
                });
            });

            var sidebarElement = document.getElementById('sidebar');
            if (sidebarElement) {
                observer.observe(sidebarElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }
        });
    </script>
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/chosen.jquery.min.js"></script>

    <!-- Product Search Script -->
    <script>
        $(document).ready(function() {
            let searchTimeout;

            // Check if element exists before initializing
            if ($('#product_search_select').length > 0) {
                // Wait for DOM to be ready, then initialize Chosen
                setTimeout(function() {
                    // Initialize Chosen select ONLY for navbar search
                    $('#product_search_select').chosen({
                        width: '280px',
                        search_contains: true,
                        no_results_text: 'No products found',
                        placeholder_text_single: 'Search Product by Name or SKU...'
                    });

                    console.log('✓ Chosen initialized for product search');

                    // Get the Chosen container and search input
                    const chosenContainer = $('#product_search_select').next('.chosen-container');
                    const searchInput = chosenContainer.find('.chosen-search input');

                    console.log('✓ Search input found:', searchInput.length > 0);

                    if (searchInput.length > 0) {
                        // Handle search input in Chosen
                        searchInput.on('keyup', function(e) {
                            clearTimeout(searchTimeout);
                            const query = $(this).val().trim();

                            console.log('→ Typing in search:', query);

                            if (query.length < 2) {
                                return;
                            }

                            searchTimeout = setTimeout(() => {
                                console.log('→ Searching for:', query);

                                $.ajax({
                                    url: 'index.php?r=inventory/search-products',
                                    type: 'POST',
                                    data: {
                                        query: query,
                                        _csrf: $('meta[name="csrf-token"]').attr('content')
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        console.log('✓ Response received:', response);

                                        if (response.success && response.products && response.products.length > 0) {
                                            // Remove all options except the first one
                                            $('#product_search_select').find('option:not(:first)').remove();

                                            response.products.forEach(product => {
                                                $('#product_search_select').append(
                                                    `<option value="${product.id}">${product.product_name} (SKU: ${product.sku || 'N/A'})</option>`
                                                );
                                            });

                                            $('#product_search_select').trigger('chosen:updated');
                                            console.log('✓ Dropdown updated with', response.products.length, 'products');
                                        } else {
                                            console.log('→ No products found');
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('✗ AJAX Error:', error);
                                        console.error('Response:', xhr.responseText);
                                    }
                                });
                            }, 300);
                        });
                    } else {
                        console.error('✗ Search input not found!');
                    }

                    // Handle product selection
                    $('#product_search_select').on('change', function() {
                        const productId = $(this).val();
                        console.log('→ Product selected, ID:', productId);

                        if (productId) {
                            loadProductDetailsModal(productId);
                            // Reset the dropdown after selection
                            setTimeout(() => {
                                $('#product_search_select').val('').trigger('chosen:updated');
                                console.log('✓ Dropdown reset');
                            }, 500);
                        }
                    });

                }, 500); // Wait for Chosen library to fully load
            } else {
                console.warn('⚠ Product search select element not found in DOM');
            }
        });

        function loadProductDetailsModal(productId) {
            $.ajax({
                url: 'index.php?r=inventory/product-details',
                type: 'POST',
                data: {
                    id: productId,
                    _csrf: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showProductDetailsModal(response.product, response.stock, response.purchase, response.sales);
                    } else {
                        Swal.fire('Error', response.message || 'Failed to load product details', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Unable to load product details', 'error');
                }
            });
        }

        function showProductDetailsModal(product, stock, purchase, sales) {
            const totalQuantity = stock.total_quantity || 0;
            const soldQuantity = sales.total_sold || 0;
            const remainingQuantity = totalQuantity - soldQuantity;
            const totalStockValue = stock.total_purchase_value || 0;
            const totalPurchaseCost = purchase.total_purchase_cost || 0;
            const totalSoldPrice = sales.total_sold_value || 0;
            const avgPurchaseCost = purchase.total_purchased > 0 ? totalPurchaseCost / purchase.total_purchased : 0;
            const avgSellingPrice = soldQuantity > 0 ? totalSoldPrice / soldQuantity : 0;
            const totalProfit = totalSoldPrice - (avgPurchaseCost * soldQuantity);

            const html = `
                <div style="width: 100%; padding: 12px; font-family: 'Poppins', sans-serif;">
                    <!-- Product Header -->
                    <div style="margin-bottom: 15px; border-bottom: 3px solid #667eea; padding-bottom: 10px;">
                        <h2 style="margin: 0 0 6px 0; font-size: 28px; font-weight: 700; color: #333;">${product.product_name}</h2>
                        <p style="margin: 0; font-size: 12px; color: #666;">SKU: <strong>${product.sku || 'N/A'}</strong></p>
                    </div>

                    <!-- Basic Information Row -->
                    <div style="margin-bottom: 10px;">
                        <h4 style="margin: 0 0 8px 0; color: #333; font-size: 13px;"><i class="fa fa-info-circle"></i> Basic Information</h4>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                            <div style="background: #f8f9fa; padding: 10px; border-radius: 6px;">
                                <p style="margin: 0 0 4px 0; font-size: 11px; color: #666;"><strong>Category</strong></p>
                                <p style="margin: 0; font-size: 12px;">${product.category_name || 'N/A'}</p>
                            </div>
                            <div style="background: #f8f9fa; padding: 10px; border-radius: 6px;">
                                <p style="margin: 0 0 4px 0; font-size: 11px; color: #666;"><strong>Brand</strong></p>
                                <p style="margin: 0; font-size: 12px;">${product.brand_name || 'N/A'}</p>
                            </div>
                            <div style="background: #f8f9fa; padding: 10px; border-radius: 6px;">
                                <p style="margin: 0 0 4px 0; font-size: 11px; color: #666;"><strong>Model</strong></p>
                                <p style="margin: 0; font-size: 12px;">${product.model_name || 'N/A'}</p>
                            </div>
                            <div style="background: #f8f9fa; padding: 10px; border-radius: 6px;">
                                <p style="margin: 0 0 4px 0; font-size: 11px; color: #666;"><strong>Unit</strong></p>
                                <p style="margin: 0; font-size: 12px;">${product.unit_name || 'N/A'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Information Row -->
                    <div style="margin-bottom: 10px;">
                        <h4 style="margin: 0 0 8px 0; color: #558b2f; font-size: 13px;"><i class="fa fa-cubes"></i> Stock Information</h4>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                            <div style="background: #f1f8e9; padding: 10px; border-radius: 6px; text-align: center;">
                                <p style="margin: 0 0 4px 0; font-size: 11px; color: #558b2f;"><strong>Total Qty</strong></p>
                                <p style="margin: 0; font-size: 16px; font-weight: bold; color: #558b2f;">${parseFloat(totalQuantity).toLocaleString('en-PK', {maximumFractionDigits: 0})}</p>
                            </div>
                            <div style="background: #ffebee; padding: 10px; border-radius: 6px; text-align: center;">
                                <p style="margin: 0 0 4px 0; font-size: 11px; color: #d32f2f;"><strong>Sold Qty</strong></p>
                                <p style="margin: 0; font-size: 16px; font-weight: bold; color: #d32f2f;">${parseFloat(soldQuantity).toLocaleString('en-PK', {maximumFractionDigits: 0})}</p>
                            </div>
                            <div style="background: #e3f2fd; padding: 10px; border-radius: 6px; text-align: center;">
                                <p style="margin: 0 0 4px 0; font-size: 11px; color: #1976d2;"><strong>Remaining Qty</strong></p>
                                <p style="margin: 0; font-size: 16px; font-weight: bold; color: #1976d2;">${parseFloat(remainingQuantity).toLocaleString('en-PK', {maximumFractionDigits: 0})}</p>
                            </div>
                            <div style="background: #fce4ec; padding: 10px; border-radius: 6px; text-align: center;">
                                <p style="margin: 0 0 4px 0; font-size: 11px; color: #c2185b;"><strong>Stock Value</strong></p>
                                <p style="margin: 0; font-size: 13px; font-weight: bold; color: #c2185b;">PKR ${parseFloat(totalStockValue).toLocaleString('en-PK', {maximumFractionDigits: 0})}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Row -->
                    <div style="margin-bottom: 10px;">
                        <h4 style="margin: 0 0 8px 0; color: #1976d2; font-size: 13px;"><i class="fa fa-dollar"></i> Pricing</h4>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                            <div style="background: #e3f2fd; padding: 10px; border-radius: 6px;">
                                <p style="margin: 0 0 4px 0; font-size: 11px; color: #1976d2;"><strong>Purchase Price</strong></p>
                                <p style="margin: 0; font-size: 12px;">PKR ${parseFloat(product.purchase_price || 0).toLocaleString('en-PK', {maximumFractionDigits: 0})}</p>
                            </div>
                            <div style="background: #e8f5e9; padding: 10px; border-radius: 6px;">
                                <p style="margin: 0 0 4px 0; font-size: 11px; color: #388e3c;"><strong>Selling Price</strong></p>
                                <p style="margin: 0; font-size: 12px;">PKR ${parseFloat(product.selling_price || 0).toLocaleString('en-PK', {maximumFractionDigits: 0})}</p>
                            </div>
                            <div style="background: #fff3cd; padding: 10px; border-radius: 6px;">
                                <p style="margin: 0 0 4px 0; font-size: 11px; color: #f57f17;"><strong>Profit/Unit</strong></p>
                                <p style="margin: 0; font-size: 12px;">PKR ${parseFloat((product.selling_price - product.purchase_price) || 0).toLocaleString('en-PK', {maximumFractionDigits: 0})}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Summary Row -->
                    <div>
                        <h4 style="margin: 0 0 8px 0; color: #c2185b; font-size: 13px;"><i class="fa fa-chart-bar"></i> Financial Summary</h4>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                            <div style="background: #fce4ec; padding: 10px; border-radius: 6px; text-align: center;">
                                <p style="margin: 0 0 3px 0; font-size: 10px; color: #c2185b;"><strong>Purchased Cost</strong></p>
                                <p style="margin: 0; font-size: 12px; font-weight: bold; color: #c2185b;">PKR ${parseFloat(totalPurchaseCost).toLocaleString('en-PK', {maximumFractionDigits: 0})}</p>
                            </div>
                            <div style="background: #e8f5e9; padding: 10px; border-radius: 6px; text-align: center;">
                                <p style="margin: 0 0 3px 0; font-size: 10px; color: #388e3c;"><strong>Sold Revenue</strong></p>
                                <p style="margin: 0; font-size: 12px; font-weight: bold; color: #388e3c;">PKR ${parseFloat(totalSoldPrice).toLocaleString('en-PK', {maximumFractionDigits: 0})}</p>
                            </div>
                            <div style="background: #e3f2fd; padding: 10px; border-radius: 6px; text-align: center;">
                                <p style="margin: 0 0 3px 0; font-size: 10px; color: #1976d2;"><strong>Avg Cost/Unit</strong></p>
                                <p style="margin: 0; font-size: 12px; font-weight: bold; color: #1976d2;">PKR ${parseFloat(avgPurchaseCost).toLocaleString('en-PK', {maximumFractionDigits: 0})}</p>
                            </div>
                            <div style="background: #fff3cd; padding: 10px; border-radius: 6px; text-align: center;">
                                <p style="margin: 0 0 3px 0; font-size: 10px; color: #f57f17;"><strong>Total Profit</strong></p>
                                <p style="margin: 0; font-size: 12px; font-weight: bold; color: #f57f17;">PKR ${parseFloat(totalProfit).toLocaleString('en-PK', {maximumFractionDigits: 0})}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            Swal.fire({
                title: '',
                html: html,
                width: '1000px',
                heightAuto: false,
                allowOutsideClick: true,
                showConfirmButton: true,
                confirmButtonText: 'Close',
                customClass: {
                    popup: 'compact-modal'
                }
            });
        }

        // Handle navbar button clicks - redirect to dashboard if function doesn't exist
        function navbarClickAction(action) {
            let functionName = '';
            switch(action) {
                case 'product':
                    functionName = 'openProductModal';
                    break;
                case 'stock':
                    functionName = 'openStockModal';
                    break;
                case 'supplier':
                    functionName = 'openSupplierModal';
                    break;
                case 'customer':
                    functionName = 'openCustomerModal';
                    break;
                case 'po':
                    functionName = 'loadOrder';
                    break;
                case 'so':
                    functionName = 'openOrderModal';
                    break;
            }

            // Check if function exists and call it
            if (typeof window[functionName] === 'function') {
                if (action === 'po') {
                    window[functionName]();
                } else {
                    window[functionName]();
                }
            } else {
                // If function doesn't exist, redirect to dashboard with action parameter
                window.location.href = 'index.php?r=inventory/dashboard&action=' + action;
            }
        }
    </script>
</body>

</html>
<?php $this->endPage() ?>
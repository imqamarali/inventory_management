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

    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/css/sweetalert2.min.css">
    <script src="<?= Yii::$app->request->baseUrl ?>/js/sweetalert2.all.min.js"></script>
    
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
</body>

</html>
<?php $this->endPage() ?>
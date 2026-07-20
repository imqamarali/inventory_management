<?php
if (Yii::$app->session->hasFlash('toast')) {
    $toastMessage = Yii::$app->session->getFlash('toast');
    $js = "showToast('$toastMessage');";
    $this->registerJs($js, \yii\web\View::POS_READY);
}

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

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
    <title>HRMS</title>
    <meta name="description" content="overview &amp; stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <?= $this->render('head'); ?>
    <style>
        body {
            zoom: 90%;
        }

        #toastBox {
            position: fixed;
            top: 30px;
            right: 30px;
            display: flex;
            align-items: flex-end;
            flex-direction: column;
            overflow: hidden;
            padding: 20px;
            z-index: 1000;
        }

        .toast {
            font-size: smaller;
            width: 350px;
            height: 47px;
            background: #fff;
            font-weight: 500;
            margin: 4px 0;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            position: relative;
        }

        .toast i {
            margin: 0 20px;
            font-size: 30px;
            color: green;
        }

        .toast.error i {
            color: red;
        }

        .toast.invalid i {
            color: orange;
        }

        .toast::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 5px;
            background: green;
            animation: anim 3s linear forwards;
        }

        @keyframes anim {
            100% {
                width: 0;
            }
        }

        .toast.error::after {
            background: red;
        }

        .toast.invalid::after {
            background: orange;
        }
    </style>
</head>
<body class="no-skin">
    <?php $this->beginBody() ?>
    <div class="main-container ace-save-state" id="main-container">
        <div class="main-content">
            <div class="main-content-inner">
                <?= $content ?>
            </div>
        </div><!-- /.main-content -->

        <div class="main-content">
            <div class="main-content-inner">
                <?= $this->render('footer'); ?>
            </div>
        </div><!-- /.main-content -->
        <div id="toastBox"></div>

        <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
            <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
        </a>
    </div><!-- /.main-container -->

    <?php $this->endBody() ?>
    <script>
        let toastBox = document.getElementById('toastBox');

        function showToast(message) {
            // Check if a toast with the same message already exists
            if ([...toastBox.children].some(toast => toast.textContent.includes(message))) {
                return; // Exit if the toast is already showing
            }
            let toast = document.createElement('div');
            toast.classList.add('toast');
            toast.innerHTML = '<i class="fa fa-bullhorn"></i> ' + message;
            toastBox.appendChild(toast);
            if (message.includes('error')) {
                toast.classList.add('error');
            }
            if (message.includes('Invalid')) {
                toast.classList.add('invalid');
            }
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
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
</body>
</html>
<?php $this->endPage() ?>
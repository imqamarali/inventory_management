
<?php

if(!isset($csrfToken))
{
    $csrfToken = null;
}
if(!isset($isSuperAdmin))
{
    $isSuperAdmin = null;
}

?>

<div class="page-content">

    <div class="dashboard-header">
        <div>
            <h3>
                <i class="fa fa-credit-card"></i>
                Payment History
                <small>Payment Overview & Analytics</small>
            </h3>
        </div>

        <div style="display: flex; gap: 10px;">
            <button id="payInvoiceBtn">
                <i class="fa fa-credit-card"></i>
                Pay Invoice
            </button>
            <button id="refreshDashboard">
                <i class="fa fa-refresh"></i>
                Refresh
            </button>
        </div>
    </div>

    <div class="stats-grid">

        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Total Months</span>
                <div class="stat-icon">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
            <div class="stat-value" id="total_months">0</div>
            <div class="stat-subtitle">Invoice Records</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Paid Amount</span>
                <div class="stat-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value" id="paid_amount">PKR 0</div>
            <div class="stat-subtitle">Completed Payments</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-header">
                <span class="stat-title">Remaining Amount</span>
                <div class="stat-icon">
                    <i class="fa fa-hourglass"></i>
                </div>
            </div>
            <div class="stat-value" id="remaining_amount">PKR 0</div>
            <div class="stat-subtitle">Pending Payment</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Next Due</span>
                <div class="stat-icon">
                    <i class="fa fa-calendar-check-o"></i>
                </div>
            </div>
            <div class="stat-value" id="next_due_date">-</div>
            <div class="stat-subtitle">Payment Due Date</div>
        </div>

        <div class="stat-card teal">
            <div class="stat-header">
                <span class="stat-title">Unpaid Invoices</span>
                <div class="stat-icon">
                    <i class="fa fa-file"></i>
                </div>
            </div>
            <div class="stat-value" id="unpaid_count">0</div>
            <div class="stat-subtitle">Awaiting Payment</div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <span class="stat-title">Paid Invoices</span>
                <div class="stat-icon">
                    <i class="fa fa-check-square"></i>
                </div>
            </div>
            <div class="stat-value" id="paid_count">0</div>
            <div class="stat-subtitle">Completed</div>
        </div>

    </div>

    <div class="row" style="margin-top:15px;">

        <div class="col-md-12">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-list"></i>
                    Latest Invoices
                </h4>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover">

                        <thead>

                            <tr>

                                <th>Invoice #</th>
                                <th>Contract</th>
                                <th>Invoice Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Documents</th>
                                <th class="text-right">Amount</th>
                                <th>Actions</th>

                            </tr>

                        </thead>

                        <tbody id="latestInvoices">

                            <tr>

                                <td colspan="8" class="text-center">
                                    Loading...
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

    // Initialize API configuration (from controller)
    const paymentApiUrl = "<?php echo addslashes(isset($paymentApiUrl) ? $paymentApiUrl : ""); ?>";
    const printInvoiceUrl = "<?php echo addslashes(isset($printInvoiceUrl) ? $printInvoiceUrl : ""); ?>";
    const csrfToken = "<?php echo addslashes(isset($csrfToken) ? $csrfToken : ""); ?>";
    const csrfParam = "<?php echo addslashes(isset($csrfParam) ? $csrfParam : ""); ?>";
    const isSuperAdmin = <?php echo $isSuperAdmin ? 'true' : 'false'; ?>;

    $(function() {

        loadDashboard();

        $("#payInvoiceBtn").click(function() {
            let ajaxData = {
                flag: "get_current_invoice",
                "_csrf": "<?php echo addslashes($csrfToken??""); ?>"
            };

            $.ajax({
                url: "<?php echo addslashes($paymentApiUrl??""); ?>",
                type: "POST",
                dataType: "json",
                data: ajaxData,
                success: function(response) {
                    if (response.success && response.invoice) {
                        openPayInvoiceModal(response.invoice);
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'No Invoice',
                            text: response.message || 'No invoice available for payment.'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to load invoice.'
                    });
                }
            });
        });

        $("#refreshDashboard").click(function() {

            loadDashboard();

        });

    });


    function loadDashboard() {

        showDashboardLoading();

        let dashboardData = {
            flag: "load_dashboard",
            "_csrf": "<?php echo addslashes($csrfToken??""); ?>"
        };

        $.ajax({
            url: "<?php echo addslashes($paymentApiUrl??""); ?>",
            type: "POST",
            dataType: "json",
            data: dashboardData,

            success: function(response) {

                hideDashboardLoading();

                if (response.success) {

                    loadStatistics(response.stats);
                    loadLatestInvoices(response.latestInvoices);

                } else {

                    alert(response.message);

                }

            },

            error: function() {

                hideDashboardLoading();

                alert("Unable to load dashboard.");

            }

        });

    }



    function showDashboardLoading() {

        $(".stat-value").each(function() {

            $(this)
                .addClass("loading")
                .html("&nbsp;&nbsp;&nbsp;&nbsp;");

        });

    }



    function hideDashboardLoading() {

        $(".stat-value").removeClass("loading");

    }



    function loadStatistics(stats) {

        animateCounter("#total_months", stats.total_months);
        animateCurrency("#paid_amount", stats.paid_amount);
        animateCurrency("#remaining_amount", stats.remaining_amount);
        animateCounter("#unpaid_count", stats.unpaid_count);
        animateCounter("#paid_count", stats.paid_count);

        // Set next due date (no animation)
        if (stats.next_due_date) {
            var dueDate = new Date(stats.next_due_date);
            var formattedDate = dueDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            $("#next_due_date").text(formattedDate);
        }

    }



    function animateCounter(id, value) {
        value = (value == null || isNaN(value)) ? 0 : Number(value);


        $({

            count: 0

        }).animate({

            count: value

        }, {

            duration: 700,

            easing: "swing",

            step: function() {

                $(id).text(Math.floor(this.count).toLocaleString());

            },

            complete: function() {

                $(id).text(Number(value).toLocaleString());

            }

        });

    }



    function animateCurrency(id, value) {
        value = (value == null || isNaN(value)) ? 0 : Number(value);


        $({

            count: 0

        }).animate({

            count: value

        }, {

            duration: 700,

            easing: "swing",

            step: function() {

                $(id).text("PKR " + Math.floor(this.count).toLocaleString());

            },

            complete: function() {

                $(id).text("PKR " + Number(value).toLocaleString());

            }

        });

    }

    function loadLatestInvoices(data) {

        let html = "";

        if (data.length == 0) {

            html += "<tr>";
            html += "<td colspan='8' class='text-center'>No Invoices Found.</td>";
            html += "</tr>";

        } else {

            $.each(data, function(i, row) {

                html += "<tr>";

                html += "<td><strong>" + row.invoice_number + "</strong></td>";

                html += "<td>" + row.contract_name + "</td>";

                html += "<td>" + row.invoice_date + "</td>";

                html += "<td><strong style='color: #e74c3c;'>" + row.due_date + "</strong></td>";

                let statusColor = row.payment_status === 'paid' ? 'success' : (row.payment_status === 'partial' ? 'warning' : (row.payment_status === 'pending_approval' ? 'info' : 'danger'));
                html += "<td><span class='label label-" + statusColor + "' style='display: block; text-align: center;'>" + row.payment_status.toUpperCase().replace('_', ' ') + "</span></td>";

                let docCount = row.document_count ? row.document_count : 0;
                let docHtml = docCount > 0 ? '<button  onclick="viewDocuments(' + row.id + ')" title="View Documents"><i class="fa fa-file"></i> ' + docCount + ' file(s)</button>' : '<span style="color: #7f8c8d;">-</span>';
                html += "<td style='text-align: center;'>" + docHtml + "</td>";

                html += "<td class='text-right'>PKR " + Number(row.amount).toLocaleString() + "</td>";

                let actionHtml = '';
                if (row.payment_status === 'paid') {
                    actionHtml = '<div style="display: flex; gap: 5px;"><button onclick="printPaymentReceipt(' + row.id + ')" title="Print Payment Receipt" style="cursor: pointer;"><i class="fa fa-print"></i> Receipt</button></div>';
                } else if (row.payment_status === 'pending_approval') {
                    if (isSuperAdmin) {
                        actionHtml = '<button onclick="openApprovalModal(' + row.id + ', \'' + row.invoice_number + '\')" title="Update Payment"><i class="fa fa-edit"></i> Update</button>';
                    } else {
                        actionHtml = '<button  disabled title="Pending Approval"><i class="fa fa-hourglass"></i> Verifying...</button>';
                    }
                } else {
                    actionHtml = '<button onclick="openPayInvoiceModalFromId(' + row.id + ')" title="Pay Invoice"><i class="fa fa-money"></i> Pay</button>';
                }
                html += "<td style='text-align: center;'>" + actionHtml + "</td>";

                html += "</tr>";

            });

        }

        $("#latestInvoices").html(html);

    }

    function openPayInvoiceModalFromId(invoiceId) {
        let invoiceData = {
            flag: "get_invoice_by_id",
            invoice_id: invoiceId,
            "_csrf": "<?php echo addslashes($csrfToken??""); ?>"
        };

        $.ajax({
            url: "<?php echo addslashes($paymentApiUrl??""); ?>",
            type: "POST",
            dataType: "json",
            data: invoiceData,
            success: function(response) {
                if (response.success && response.invoice) {
                    openPayInvoiceModal(response.invoice);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Unable to load invoice details.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to load invoice details.'
                });
            }
        });
    }

    function buildPaymentHistoryHTML(invoice) {
        if (!invoice.payment_history || invoice.payment_history.length === 0) {
            return '';
        }

        let html = '<div style="margin-top: 20px; border-top: 2px solid #e8e8e8; padding-top: 20px;">';
        html += '<h4 style="color:#2c3e50;margin:0 0 15px;font-size:14px; font-weight: 600;">Payment History & Comments</h4>';

        invoice.payment_history.forEach(function(payment, idx) {
            let paymentDate = payment.created_at ? new Date(payment.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'}) : 'N/A';
            let amount = Number(payment.amount || 0).toLocaleString();
            let method = (payment.payment_method || 'N/A').replace(/_/g, ' ').toUpperCase();
            let transId = payment.transaction_id ? '| <strong>ID:</strong> ' + payment.transaction_id : '';

            html += '<div style="background:#f8f9fa;padding:12px;margin-bottom:10px;border-radius:4px;border-left:4px solid #3498db;">';
            html += '<div style="font-size:12px;color:#7f8c8d;text-transform:uppercase;font-weight:600;margin-bottom:6px;">Payment #' + (idx + 1) + ' - ' + paymentDate + '</div>';
            html += '<div style="font-size:13px;color:#2c3e50;margin-bottom:8px;">';
            html += '<strong>Amount:</strong> PKR ' + amount + ' | <strong>Method:</strong> ' + method + ' ' + transId;
            html += '</div>';

            if (payment.user_comments) {
                html += '<div style="font-size:12px;color:#555;background:#fff;padding:8px;border-radius:3px;margin-top:6px;">';
                html += '<strong style="color:#2c3e50;">Your Comments:</strong><br/>' + payment.user_comments;
                html += '</div>';
            }

            if (payment.approval_comments) {
                html += '<div style="font-size:12px;color:#27ae60;background:#f0fdf4;padding:8px;border-radius:3px;margin-top:6px;">';
                html += '<strong style="color:#27ae60;">✓ Admin Approval Comments:</strong><br/>' + payment.approval_comments;
                html += '</div>';
            }

            html += '</div>';
        });

        html += '</div>';
        return html;
    }

    function handleFilePreview() {
        let fileInput = document.getElementById('paymentProof');
        if (!fileInput || fileInput.files.length === 0) {
            return;
        }

        let file = fileInput.files[0];
        if (!file) return;

        // Validate file type
        let allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please upload only JPG, PNG, or PDF files.'
            });
            fileInput.value = '';
            document.getElementById('fileStatusContainer').style.display = 'none';
            return;
        }

        let fileSize = file.size || 0;
        let maxFileSize = 5 * 1024 * 1024;

        if (fileSize > maxFileSize) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'File exceeds 5MB limit. Please choose a smaller file.'
            });
            fileInput.value = '';
            document.getElementById('fileStatusContainer').style.display = 'none';
            return;
        }

        let isImage = file.type.startsWith('image/');
        let isPDF = file.type === 'application/pdf';
        let reader = new FileReader();

        reader.onload = function(e) {
            showProofPreviewModal(e.target.result, file, isImage, isPDF);
        };

        reader.readAsDataURL(file);
    }

    function showProofPreviewModal(dataUrl, file, isImage, isPDF) {
        let previewContent = '';

        if (isImage) {
            previewContent = '<img src="' + dataUrl + '" style="max-width: 100%; max-height: 500px; border-radius: 4px; border: 1px solid #ddd; cursor: pointer;" onclick="viewFullImage(this.src)" title="Click to expand">';
        } else if (isPDF) {
            previewContent = '<iframe src="' + dataUrl + '" style="width: 100%; height: 500px; border-radius: 4px; border: 1px solid #ddd;"></iframe>' +
                           '<div style="margin-top: 10px; padding: 10px; background: #f0f0f0; border-radius: 4px; text-align: center;">' +
                           '<p style="margin: 0; font-size: 12px; color: #7f8c8d;"><i class="fa fa-file-pdf-o"></i> ' + file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)</p>' +
                           '</div>';
        }

        // Create custom overlay modal that doesn't close parent
        let overlay = document.createElement('div');
        overlay.id = 'proofPreviewOverlay';
        overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';

        let modal = document.createElement('div');
        modal.style.cssText = 'background: white; border-radius: 8px; padding: 30px; max-width: 700px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3);';

        let content = '<div>' +
                     '<h3 style="margin: 0 0 20px; color: #2c3e50; font-size: 18px; text-align: center;"><strong>Verify Payment Proof</strong></h3>' +
                     '<div style="margin: 20px 0; background: #f9f9f9; padding: 15px; border-radius: 4px; border: 1px solid #e8e8e8;">' + previewContent + '</div>' +
                     '<div style="display: flex; gap: 10px; justify-content: center; margin-top: 25px;">' +
                     '<button id="confirmProofBtn" style="padding: 10px 25px; background: #27ae60; color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 14px;">✓ OK - Keep This File</button>' +
                     '<button id="cancelProofBtn" style="padding: 10px 25px; background: #95a5a6; color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 14px;">✕ Cancel - Upload New</button>' +
                     '</div>' +
                     '</div>';

        modal.innerHTML = content;
        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        // Confirm button
        document.getElementById('confirmProofBtn').addEventListener('click', function() {
            document.getElementById('fileStatusName').textContent = file.name;
            document.getElementById('fileStatusContainer').style.display = 'block';
            overlay.remove();
        });

        // Cancel button
        document.getElementById('cancelProofBtn').addEventListener('click', function() {
            document.getElementById('paymentProof').value = '';
            document.getElementById('fileStatusContainer').style.display = 'none';
            overlay.remove();
        });

        // Close on overlay click
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                document.getElementById('paymentProof').value = '';
                document.getElementById('fileStatusContainer').style.display = 'none';
                overlay.remove();
            }
        });
    }

    function viewFullImage(src) {
        Swal.fire({
            title: 'Payment Proof Preview',
            imageUrl: src,
            imageWidth: 600,
            imageHeight: 'auto',
            showConfirmButton: true,
            confirmButtonText: 'Close',
            confirmButtonColor: '#0f4c29'
        });
    }

function submitPaymentWithFile(invoiceId, maxAmount, transactionId, fileObj) {
        let files = [fileObj];
        let comments = document.getElementById('paymentComments') ? document.getElementById('paymentComments').value || '' : '';

        if (!invoiceId || invoiceId <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Invoice',
                text: 'Invoice ID is invalid.'
            });
            return;
        }

        if (!maxAmount || maxAmount <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Amount',
                text: 'Invoice amount is invalid.'
            });
            return;
        }

        if (!files || files.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Payment Proof Required',
                text: 'Please upload payment proof.'
            });
            return;
        }

        for (let i = 0; i < files.length; i++) {
            if (!files[i]) continue;
            let fileSize = files[i].size || 0;
            let maxFileSize = 5 * 1024 * 1024;

            if (fileSize > maxFileSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: (files[i].name || 'File') + ' exceeds 5MB limit.'
                });
                return;
            }
        }

        let formData = new FormData();
        formData.append('flag', 'upload_proof');
        formData.append('invoice_id', invoiceId);
        formData.append('payment_amount', maxAmount);
        formData.append('comments', comments || '');
        formData.append('transaction_id', transactionId || '');
        formData.append('_csrf', "<?php echo addslashes($csrfToken??""); ?>");

        for (let i = 0; i < files.length; i++) {
            if (files[i]) {
                formData.append('documents[]', files[i]);
            }
        }

        Swal.fire({
            title: '⏳ Verifying Payment',
            html: '<div style="margin: 20px 0;"><div class="spinner-border" role="status" style="color: #3498db;"><span class="sr-only"></span></div><p style="margin-top: 15px; color: #7f8c8d;">Please wait while your payment is being verified...</p></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "<?php echo addslashes($paymentApiUrl??""); ?>",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '✓ Payment Submitted',
                        html: '<div style="text-align: left; line-height: 1.8;"><p>Your payment has been submitted successfully.</p><p style="color: #3498db; font-weight: 600;">Status: Pending Verification</p><p style="color: #7f8c8d; font-size: 13px;">Our admin team will verify your payment shortly. You will be notified once verification is complete.</p></div>',
                        confirmButtonColor: '#27ae60'
                    }).then(() => {
                        loadDashboard();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Failed to submit payment. Please try again.'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while submitting payment. Please try again.'
                });
            }
        });
    }

    function openPayInvoiceModal(invoice) {
        // Check if invoice is already paid
        if (invoice.payment_status === 'paid') {
            Swal.fire({
                icon: 'success',
                title: '✓ Already Paid',
                html: '<div style="text-align: left; line-height: 1.8;"><p><strong>Invoice #' + invoice.invoice_number + '</strong></p><p>This invoice has been fully paid.</p><p style="color: #27ae60; font-weight: 600;">Status: PAID</p><p style="color: #7f8c8d; font-size: 13px;">You can print or download this invoice from the actions menu.</p></div>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#27ae60'
            });
            return;
        }

        // Check if verification is in process
        if (invoice.payment_status === 'pending_approval') {
            Swal.fire({
                icon: 'info',
                title: '⏳ Invoice Verification in Process',
                html: '<div style="text-align: left; line-height: 1.8;"><p><strong>Invoice #' + invoice.invoice_number + '</strong></p><p>Your payment is currently being verified by our admin team.</p><p style="color: #3498db; font-weight: 600;">Status: PENDING APPROVAL</p><p style="color: #7f8c8d; font-size: 13px;">Please wait for confirmation. You will be notified once the verification is complete.</p></div>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3498db'
            });
            return;
        }

        let paymentHtml = `
            <div style="text-align: left; margin: 20px 0;">
                <div style="margin-bottom:16px;">
                    <h4 style="color:#2c3e50;margin:0 0 10px;font-size:15px;">
                        Invoice Details
                    </h4>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">

                        <div style="background:#f8f9fa;padding:8px;border-radius:4px;border-left:3px solid #3498db;">
                            <div style="font-size:11px;color:#7f8c8d;text-transform:uppercase;font-weight:600;margin-bottom:2px;">
                                Invoice Number
                            </div>
                            <div style="font-size:13px;font-weight:600;color:#2c3e50;">
                                ${invoice.invoice_number}
                            </div>
                        </div>

                        <div style="background:#f8f9fa;padding:8px;border-radius:4px;border-left:3px solid #9b59b6;">
                            <div style="font-size:11px;color:#7f8c8d;text-transform:uppercase;font-weight:600;margin-bottom:2px;">
                                Contract
                            </div>
                            <div style="font-size:13px;font-weight:600;color:#2c3e50;">
                                ${invoice.contract_name}
                            </div>
                        </div>

                        <div style="background:#f8f9fa;padding:8px;border-radius:4px;border-left:3px solid #1abc9c;">
                            <div style="font-size:11px;color:#7f8c8d;text-transform:uppercase;font-weight:600;margin-bottom:2px;">
                                Invoice Date
                            </div>
                            <div style="font-size:13px;font-weight:600;color:#2c3e50;">
                                ${invoice.invoice_date}
                            </div>
                        </div>

                        <div style="background:#f8f9fa;padding:8px;border-radius:4px;border-left:3px solid #e74c3c;">
                            <div style="font-size:11px;color:#7f8c8d;text-transform:uppercase;font-weight:600;margin-bottom:2px;">
                                Due Date
                            </div>
                            <div style="font-size:13px;font-weight:700;color:#e74c3c;">
                                ${invoice.due_date}
                            </div>
                        </div>

                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">

                        <div style="background:#fdf5f7;padding:8px;border-radius:4px;border-left:3px solid #f39c12;">
                            <div style="font-size:11px;color:#7f8c8d;text-transform:uppercase;font-weight:600;margin-bottom:2px;">
                                Total Amount
                            </div>
                            <div style="font-size:14px;font-weight:700;color:#2c3e50;">
                                PKR ${Number(invoice.amount).toLocaleString()}
                            </div>
                        </div>

                        <div style="background:#f0fdf4;padding:8px;border-radius:4px;border-left:3px solid #27ae60;">
                            <div style="font-size:11px;color:#7f8c8d;text-transform:uppercase;font-weight:600;margin-bottom:2px;">
                                Paid Amount
                            </div>
                            <div style="font-size:14px;font-weight:700;color:#27ae60;">
                                PKR ${invoice.paid_amount ? Number(invoice.paid_amount).toLocaleString() : '0'}
                            </div>
                        </div>

                    </div>

                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin: 20px 0;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 13px;">Full Payment Amount (PKR) *</label>
                        <input type="text" id="paymentAmount" placeholder="Full payment amount" style="width: 100%; padding: 10px; border: 2px solid #e74c3c; border-radius: 4px; background-color: #fdf5f7; font-size: 14px; font-weight: bold; color: #e74c3c;" readonly value="PKR ${Number(invoice.remaining_amount || 0).toLocaleString()}">
                        <small style="color: #e74c3c; display: block; margin-top: 5px; font-weight: 600;">Full payment required</small>
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 13px;">Transaction ID (Reference/UTR) *</label>
                        <input type="text" id="transactionId" placeholder="UTR, Cheque No, Reference ID" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                        <small style="color: #7f8c8d; display: block; margin-top: 5px;">e.g., UTR number or Cheque number</small>
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 13px;">Upload Payment Proof *</label>
                        <input type="file" id="paymentProof" accept=".pdf,.jpg,.jpeg,.png" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
                        <small style="color: #7f8c8d; display: block; margin-top: 5px;">JPG, PNG, PDF only (Max 5MB)</small>
                    </div>
                </div>

                <div id="fileStatusContainer" style="margin: 15px 0; padding: 12px; background: #f0fdf4; border-radius: 4px; border-left: 4px solid #27ae60; display: none;">
                    <p style="margin: 0; font-size: 13px; color: #27ae60;"><strong>✓ File Ready:</strong> <span id="fileStatusName"></span></p>
                </div>

                <div style="margin: 20px 0;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 500;">Comments (Optional):</label>
                    <textarea id="paymentComments" placeholder="Add any remarks or comments about this payment..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; min-height: 80px; font-family: Arial, sans-serif; resize: vertical;"></textarea>
                    <small style="color: #7f8c8d; display: block; margin-top: 5px;">Your comments will be visible to the admin for verification</small>
                </div>

                ${buildPaymentHistoryHTML(invoice)}
            </div>
        `;

        Swal.fire({
            html: paymentHtml,
            width:"900px",
            showCancelButton: true,
            confirmButtonText: 'Submit Payment',
            confirmButtonColor: '#27ae60',
            cancelButtonText: 'Cancel',
            didOpen: function() {
                document.getElementById('paymentAmount').value = 'PKR ' + Number(invoice.remaining_amount || 0).toLocaleString();

                let fileInput = document.getElementById('paymentProof');
                if (fileInput) {
                    fileInput.addEventListener('change', handleFilePreview);
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let transactionId = (document.getElementById('transactionId').value || '').trim();

                if (!transactionId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Transaction ID Required',
                        text: 'Please enter a transaction ID or reference number.'
                    });
                    return;
                }

                let fileInput = document.getElementById('paymentProof');
                if (!fileInput || fileInput.files.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Proof Required',
                        text: 'Please upload at least one payment proof document.'
                    });
                    return;
                }

                let file = fileInput.files[0];
                submitPaymentWithFile(invoice.id, invoice.remaining_amount, transactionId, file);
            }
        });
    }

    function submitPayment(invoiceId, maxAmount) {
        let files = document.getElementById('paymentProof').files;
        let transactionId = (document.getElementById('transactionId').value || '').trim();

        // Full payment only validation
        if (!invoiceId || invoiceId <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Invoice',
                text: 'Invoice ID is invalid.'
            });
            return;
        }

        if (!maxAmount || maxAmount <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Amount',
                text: 'Invoice amount is invalid.'
            });
            return;
        }

        if (!files || files.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Payment Proof Required',
                text: 'Please upload payment proof to complete the full payment of PKR ' + Number(maxAmount || 0).toLocaleString()
            });
            return;
        }

        // Prepare FormData (Full payment)
        let formData = new FormData();
        let comments = document.getElementById('paymentComments').value || '';
        let transactionId1 = document.getElementById('transactionId').value || '';

        formData.append('flag', 'upload_proof');
        formData.append('invoice_id', invoiceId);
        formData.append('payment_amount', maxAmount);
        formData.append('comments', comments || '');
        formData.append('transaction_id', transactionId1 || '');
        formData.append('_csrf', "<?php echo addslashes($csrfToken??""); ?>");

        // Validate files
        for (let i = 0; i < files.length; i++) {
            if (!files[i]) {
                continue;
            }

            let fileSize = files[i].size || 0;
            let maxFileSize = 5 * 1024 * 1024; // 5MB

            if (fileSize > maxFileSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: (files[i].name || 'File') + ' exceeds 5MB limit.'
                });
                return;
            }
            formData.append('documents[]', files[i]);
        }

        // Final validation: ensure we have at least one file
        if (formData.getAll('documents[]').length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'No Valid Files',
                text: 'Please upload at least one valid payment proof document.'
            });
            return;
        }

        Swal.fire({
            title: '⏳ Invoice Verification in Process',
            html: '<div style="margin: 20px 0;"><div class="spinner-border" role="status" style="color: #3498db;"><span class="sr-only"></span></div><p style="margin-top: 15px; color: #7f8c8d;">Please wait while your payment is being verified...</p></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "<?php echo addslashes($paymentApiUrl??""); ?>",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'info',
                        title: '✓ Invoice Verification in Process',
                        html: '<div style="text-align: left; line-height: 1.8;"><p>Your payment submission has been received.</p><p style="color: #3498db; font-weight: 600;">Status: Pending Approval</p><p style="color: #7f8c8d; font-size: 13px;">Our admin team will verify your payment and update the status shortly.</p></div>',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3498db'
                    }).then(() => {
                        loadDashboard();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Unable to upload payment proof.'
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while uploading payment proof.'
                });
            }
        });
    }

    function printInvoice(invoiceId) {
        window.open("<?php echo addslashes($printInvoiceUrl??""); ?>" + "?id=" + invoiceId, '_blank');
    }

    function printPaymentReceipt(invoiceId) {
        window.open("index.php?r=documents/billpaymentprint&id=" + invoiceId, '_blank');
    }

    function openApprovalModal(invoiceId, invoiceNumber) {
        let approvalHtml = `
            <div style="text-align: left; margin: 20px 0;">
                <div style="margin-bottom: 20px;">
                    <h4 style="color: #2c3e50; margin: 0 0 10px;">Invoice #${invoiceNumber}</h4>
                    <p style="color: #7f8c8d; margin: 0;">Review payment proof and approve or reject</p>
                </div>

                <div style="margin: 20px 0;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 500;">Admin Comments (Optional):</label>
                    <textarea id="approvalComments" placeholder="Add comments for approval or rejection..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; min-height: 100px; font-family: Arial, sans-serif; resize: vertical;"></textarea>
                    <small style="color: #7f8c8d; display: block; margin-top: 5px;">Your comments will be visible to the user</small>
                </div>
            </div>
        `;

        Swal.fire({
            title: 'Approve or Reject Payment',
            html: approvalHtml,
            width: "600px",
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'Approve',
            denyButtonText: 'Reject',
            confirmButtonColor: '#27ae60',
            denyButtonColor: '#e74c3c',
            cancelButtonText: 'Cancel',
            didOpen: function() {
                // Focus on comments textarea
                setTimeout(() => document.getElementById('approvalComments').focus(), 100);
            }
        }).then((result) => {
            let comments = document.getElementById('approvalComments').value || '';

            if (result.isConfirmed) {
                approvePayment(invoiceId, comments);
            } else if (result.isDenied) {
                rejectPayment(invoiceId, comments);
            }
        });
    }

    function approvePayment(invoiceId, comments) {
        Swal.fire({
            title: 'Approving...',
            icon: 'info',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "<?php echo addslashes($paymentApiUrl??""); ?>",
            type: 'POST',
            dataType: 'json',
            data: {
                flag: 'approve_payment',
                invoice_id: invoiceId,
                comments: comments,
                "_csrf": "<?php echo addslashes($csrfToken??""); ?>"
            },
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: 'Payment has been approved successfully.',
                        confirmButtonColor: '#27ae60'
                    }).then(() => {
                        loadDashboard();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to approve payment.'
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while approving payment.'
                });
            }
        });
    }

    function rejectPayment(invoiceId, comments) {
        Swal.fire({
            title: 'Rejecting...',
            icon: 'info',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "<?php echo addslashes($paymentApiUrl??""); ?>",
            type: 'POST',
            dataType: 'json',
            data: {
                flag: 'reject_payment',
                invoice_id: invoiceId,
                comments: comments,
                "_csrf": "<?php echo addslashes($csrfToken??""); ?>"
            },
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Rejected!',
                        text: 'Payment has been rejected. User can resubmit with new proof.',
                        confirmButtonColor: '#e74c3c'
                    }).then(() => {
                        loadDashboard();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to reject payment.'
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while rejecting payment.'
                });
            }
        });
    }

    function viewDocuments(invoiceId) {
        $.ajax({
            url: "<?php echo addslashes($paymentApiUrl??""); ?>",
            type: 'POST',
            dataType: 'json',
            data: {
                flag: 'get_invoice_documents',
                invoice_id: invoiceId,
                "_csrf": "<?php echo addslashes($csrfToken??""); ?>"
            },
            success: function(response) {
                if (response.success && response.documents && response.documents.length > 0) {
                    let transactionId = response.transaction_id ? response.transaction_id : '';
                    let userComment = response.user_comment ? response.user_comment : '';
                    let adminComment = response.admin_comment ? response.admin_comment : '';
                    displayDocumentsModal(response.invoiceNumber, response.documents, transactionId, userComment, adminComment);
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Documents',
                        text: 'No payment proof documents found for this invoice.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to load documents.'
                });
            }
        });
    }

    function displayDocumentsModal(invoiceNumber, documents, transactionId, userComment, adminComment) {
        let docList = '<div style="text-align: left;">';

        // Display Transaction ID if available
        if (transactionId) {
            docList += '<div style="margin-bottom: 20px; padding: 12px; background: #ecf0f1; border-left: 4px solid #3498db; border-radius: 3px;">' +
                      '<strong style="color: #2c3e50;">Transaction ID:</strong> <span style="font-family: monospace; color: #e74c3c;">' + transactionId + '</span>' +
                      '</div>';
        }

        // Display User Comments if available
        if (userComment) {
            docList += '<div style="margin-bottom: 20px; padding: 12px; background: #e8f4f8; border-left: 4px solid #27ae60; border-radius: 3px;">' +
                      '<strong style="color: #27ae60;"><i class="fa fa-user"></i> Your Comments:</strong>' +
                      '<div style="margin-top: 8px; color: #2c3e50; font-size: 13px; line-height: 1.6;">' + userComment + '</div>' +
                      '</div>';
        }

        // Display Admin Comments if available
        if (adminComment) {
            docList += '<div style="margin-bottom: 20px; padding: 12px; background: #f0e6f6; border-left: 4px solid #8e44ad; border-radius: 3px;">' +
                      '<strong style="color: #8e44ad;"><i class="fa fa-shield"></i> Admin Comments:</strong>' +
                      '<div style="margin-top: 8px; color: #2c3e50; font-size: 13px; line-height: 1.6;">' + adminComment + '</div>' +
                      '</div>';
        }

        // Display Documents
        docList += '<div style="margin-top: 20px; border-top: 1px solid #bdc3c7; padding-top: 15px;">' +
                  '<strong style="color: #2c3e50; display: block; margin-bottom: 12px;">Payment Documents:</strong>' +
                  '<ul style="list-style-type: disc; padding-left: 25px; margin: 0;">';

        documents.forEach(function(doc, index) {
            let docPath = doc.document_file ? doc.document_file : '';
            let docName = doc.document_name ? doc.document_name : 'Document ' + (index + 1);
            let docType = doc.document_type ? doc.document_type.toUpperCase() : 'FILE';
            let uploadDate = doc.created_at ? new Date(doc.created_at).toLocaleDateString() : '';
            let statusClass = doc.verification_status === 'verified' ? 'success' : (doc.verification_status === 'rejected' ? 'danger' : 'warning');
            let statusBadge = '<span style="display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; margin-left: 8px; background-color: ' +
                            (statusClass === 'success' ? '#d4edda; color: #155724;' : (statusClass === 'danger' ? '#f8d7da; color: #721c24;' : '#fff3cd; color: #856404;')) +
                            '">' + doc.verification_status.toUpperCase() + '</span>';

            let downloadLink = docPath ? '<a href="' + docPath + '" target="_blank" style="margin-left: 10px; color: #3498db; text-decoration: none;"><i class="fa fa-download"></i> Download</a>' : '';

            docList += '<li style="margin-bottom: 12px; line-height: 1.6;">' +
                      '<strong>' + docName + '</strong> ' +
                      '<span style="color: #7f8c8d; font-size: 12px;">(' + docType + ')</span>' +
                      downloadLink + statusBadge +
                      '<div style="color: #7f8c8d; font-size: 12px; margin-top: 4px;">' +
                      'Uploaded: ' + uploadDate +
                      '</div>' +
                      '</li>';
        });

        docList += '</ul></div></div>';

        Swal.fire({
            title: 'Payment Documents - Invoice #' + invoiceNumber,
            html: docList,
            width: '750px',
            confirmButtonText: 'Close',
            confirmButtonColor: '#3498db'
        });
    }

</script>

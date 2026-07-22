<!--
================================================================================
PAYMENT HISTORY VIEW
================================================================================
PURPOSE: Comprehensive payment overview with KPIs, trends, and invoice tracking

FUNCTIONALITY:
- Display key payment metrics and statistics
- Show payment status distribution
- Track payment history and trends
- Display monthly payment information
- List recent invoices and payment status
- Track payment amounts and due dates
- Provide quick navigation to payment modules

DATA DISPLAYED:
- Total invoice records
- Paid amount and remaining amount
- Next payment due date
- Payment status breakdown
- Monthly payment trends
- Recent invoices and payment status

================================================================================
-->

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
            <button id="payInvoiceBtn" style="background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 500;">
                <i class="fa fa-credit-card"></i>
                Pay Invoice
            </button>
            <button id="refreshDashboard" style="background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 500;">
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

        <div class="col-md-6">

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
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-right">Amount</th>
                                <th>Actions</th>

                            </tr>

                        </thead>

                        <tbody id="latestInvoices">

                            <tr>

                                <td colspan="5" class="text-center">
                                    Loading...
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="dashboard-box">

                <h4>
                    <i class="fa fa-clock-o"></i>
                    Pending Payments
                </h4>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover">

                        <thead>

                            <tr>

                                <th>Invoice #</th>
                                <th>Contract</th>
                                <th>Due Date</th>
                                <th class="text-right">Amount</th>

                            </tr>

                        </thead>

                        <tbody id="pendingPayments">

                            <tr>

                                <td colspan="4" class="text-center">
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

    $(function() {

        loadDashboard();

        $("#refreshDashboard").click(function() {

            loadDashboard();

        });

    });


    function loadDashboard() {

        showDashboardLoading();

        $.ajax({

            url: "<?= Yii::$app->urlManager->createUrl('payment/payment-history') ?>",
            type: "POST",
            dataType: "json",

            data: {
                flag: "load_dashboard",
                "<?= Yii::$app->request->csrfParam ?>": "<?= Yii::$app->request->getCsrfToken() ?>"
            },

            success: function(response) {

                hideDashboardLoading();

                if (response.success) {

                    loadStatistics(response.stats);
                    loadLatestInvoices(response.latestInvoices);
                    loadPendingPayments(response.pendingPayments);

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
            html += "<td colspan='6' class='text-center'>No Invoices Found.</td>";
            html += "</tr>";

        } else {

            $.each(data, function(i, row) {

                html += "<tr>";

                html += "<td>" + row.invoice_number + "</td>";

                html += "<td>" + row.contract_name + "</td>";

                html += "<td>" + row.invoice_date + "</td>";

                html += "<td><span class='label label-" + (row.payment_status === 'paid' ? 'success' : (row.payment_status === 'partial' ? 'warning' : 'danger')) + "'>" + row.payment_status.toUpperCase() + "</span></td>";

                html += "<td class='text-right'>PKR " + Number(row.amount).toLocaleString() + "</td>";

                let actionHtml = '';
                if (row.payment_status === 'paid') {
                    actionHtml = '<button class="btn btn-xs btn-info" onclick="printInvoice(' + row.id + ')" title="Print Invoice"><i class="fa fa-print"></i></button>';
                } else {
                    actionHtml = '<button class="btn btn-xs btn-warning" onclick="openPayInvoiceModalFromId(' + row.id + ')" title="Pay Invoice"><i class="fa fa-money"></i></button>';
                }
                html += "<td style='text-align: center;'>" + actionHtml + "</td>";

                html += "</tr>";

            });

        }

        $("#latestInvoices").html(html);

    }

    function loadPendingPayments(data) {

        let html = "";

        if (data.length == 0) {

            html += "<tr>";
            html += "<td colspan='4' class='text-center'>No Pending Payments.</td>";
            html += "</tr>";

        } else {

            $.each(data, function(i, row) {

                html += "<tr>";

                html += "<td>" + row.invoice_number + "</td>";

                html += "<td>" + row.contract_name + "</td>";

                html += "<td>" + row.due_date + "</td>";

                html += "<td class='text-right'>PKR " + Number(row.remaining_amount).toLocaleString() + "</td>";

                html += "</tr>";

            });

        }

        $("#pendingPayments").html(html);

    }

    // Pay Invoice Button Click
    $("#payInvoiceBtn").click(function() {
        $.ajax({
            url: "<?= Yii::$app->urlManager->createUrl('payment/payment-history') ?>",
            type: "POST",
            dataType: "json",
            data: {
                flag: "get_current_invoice",
                "<?= Yii::$app->request->csrfParam ?>": "<?= Yii::$app->request->getCsrfToken() ?>"
            },
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

    function openPayInvoiceModalFromId(invoiceId) {
        $.ajax({
            url: "<?= Yii::$app->urlManager->createUrl('payment/payment-history') ?>",
            type: "POST",
            dataType: "json",
            data: {
                flag: "get_invoice_by_id",
                invoice_id: invoiceId,
                "<?= Yii::$app->request->csrfParam ?>": "<?= Yii::$app->request->getCsrfToken() ?>"
            },
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

    function openPayInvoiceModal(invoice) {
        // Check if invoice is already paid
        if (invoice.payment_status === 'paid') {
            Swal.fire({
                icon: 'success',
                title: 'Already Paid',
                text: 'This invoice has already been paid in full.',
                confirmButtonText: 'OK'
            });
            return;
        }

        let paymentHtml = `
            <div style="text-align: left; margin: 20px 0;">
                <div style="margin-bottom: 20px;">
                    <strong>Invoice Details:</strong>
                    <table style="width: 100%; margin-top: 10px; border-collapse: collapse;">
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 8px; font-weight: 500;">Invoice Number:</td>
                            <td style="padding: 8px;">${invoice.invoice_number}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 8px; font-weight: 500;">Contract:</td>
                            <td style="padding: 8px;">${invoice.contract_name}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 8px; font-weight: 500;">Invoice Date:</td>
                            <td style="padding: 8px;">${invoice.invoice_date}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 8px; font-weight: 500;">Due Date:</td>
                            <td style="padding: 8px;">${invoice.due_date}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 8px; font-weight: 500;">Total Amount:</td>
                            <td style="padding: 8px; color: #2c3e50; font-weight: bold;">PKR ${Number(invoice.amount).toLocaleString()}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 8px; font-weight: 500;">Paid Amount:</td>
                            <td style="padding: 8px; color: #27ae60;">${invoice.paid_amount ? 'PKR ' + Number(invoice.paid_amount).toLocaleString() : 'PKR 0'}</td>
                        </tr>
                        <tr style="border-bottom: 2px solid #e74c3c; background-color: #fdeee9;">
                            <td style="padding: 8px; font-weight: 500;">Remaining Amount:</td>
                            <td style="padding: 8px; color: #e74c3c; font-weight: bold;">PKR ${Number(invoice.remaining_amount).toLocaleString()}</td>
                        </tr>
                    </table>
                </div>

                <div style="margin: 20px 0;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 500;">Payment Amount (PKR):</label>
                    <input type="number" id="paymentAmount" placeholder="Enter payment amount" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" min="0" max="${invoice.remaining_amount}" value="${invoice.remaining_amount}">
                    <small style="color: #7f8c8d; display: block; margin-top: 5px;">Max: PKR ${Number(invoice.remaining_amount).toLocaleString()}</small>
                </div>

                <div style="margin: 20px 0;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 500;">Upload Payment Proof:</label>
                    <input type="file" id="paymentProof" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <small style="color: #7f8c8d; display: block; margin-top: 5px;">Supported: PDF, JPG, PNG, DOC, DOCX (Max 5MB each)</small>
                </div>
            </div>
        `;

        Swal.fire({
            title: 'Pay Invoice',
            html: paymentHtml,
            width:"1200px",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Submit Payment',
            confirmButtonColor: '#27ae60',
            cancelButtonText: 'Cancel',
            didOpen: function() {
                // Auto-fill payment amount with remaining amount
                document.getElementById('paymentAmount').value = invoice.remaining_amount;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitPayment(invoice.id, invoice.remaining_amount);
            }
        });
    }

    function submitPayment(invoiceId, maxAmount) {
        let paymentAmount = parseFloat(document.getElementById('paymentAmount').value);
        let files = document.getElementById('paymentProof').files;

        if (!paymentAmount || paymentAmount <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Amount',
                text: 'Please enter a valid payment amount.'
            });
            return;
        }

        if (paymentAmount > maxAmount) {
            Swal.fire({
                icon: 'error',
                title: 'Amount Exceeds Limit',
                text: 'Payment amount cannot exceed remaining amount of PKR ' + Number(maxAmount).toLocaleString()
            });
            return;
        }

        if (files.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'No Files',
                text: 'Please upload at least one payment proof document.'
            });
            return;
        }

        // Prepare FormData
        let formData = new FormData();
        formData.append('flag', 'upload_proof');
        formData.append('invoice_id', invoiceId);
        formData.append('payment_amount', paymentAmount);
        formData.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->getCsrfToken() ?>');

        for (let i = 0; i < files.length; i++) {
            if (files[i].size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: files[i].name + ' exceeds 5MB limit.'
                });
                return;
            }
            formData.append('documents[]', files[i]);
        }

        Swal.fire({
            title: 'Processing...',
            html: '<div class="spinner-border" role="status"><span class="sr-only"></span></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "<?= Yii::$app->urlManager->createUrl('payment/payment-history') ?>",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Payment proof uploaded successfully. Awaiting verification.',
                        confirmButtonText: 'OK'
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
        window.open("<?= Yii::$app->urlManager->createUrl('payment/print-invoice') ?>?id=" + invoiceId, '_blank');
    }

</script>

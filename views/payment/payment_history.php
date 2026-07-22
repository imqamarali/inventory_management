<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="page-content">

    <!-- Dashboard Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div>
            <h3 style="margin: 0; color: #333; font-size: 24px;">
                <i class="fa fa-credit-card" style="margin-right: 10px;"></i>
                Payment History
                <small style="display: block; font-size: 12px; color: #999; font-weight: normal; margin-top: 5px;">Payment Overview & Analytics</small>
            </h3>
        </div>

        <div style="display: flex; gap: 10px;">
            <button id="refreshDashboard" onclick="searchPayments()" style="padding: 10px 15px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; font-size: 13px; transition: all 0.3s;">
                <i class="fa fa-refresh" style="margin-right: 5px;"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">

        <!-- Total Months Card -->
        <div style="padding: 20px; border-radius: 8px; color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <span style="font-size: 12px; font-weight: 600; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">Total Months</span>
                <div style="font-size: 28px; opacity: 0.6;"><i class="fa fa-calendar"></i></div>
            </div>
            <div style="font-size: 26px; font-weight: bold; margin: 10px 0;" id="total_months"><?= $stats['total_months'] ?></div>
            <div style="font-size: 11px; opacity: 0.85; font-weight: 500;">Invoice Records</div>
        </div>

        <!-- Paid Amount Card -->
        <div style="padding: 20px; border-radius: 8px; color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <span style="font-size: 12px; font-weight: 600; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">Paid Amount</span>
                <div style="font-size: 28px; opacity: 0.6;"><i class="fa fa-check-circle"></i></div>
            </div>
            <div style="font-size: 26px; font-weight: bold; margin: 10px 0;" id="paid_amount">Rs. <?= number_format($stats['paid_amount'], 2) ?></div>
            <div style="font-size: 11px; opacity: 0.85; font-weight: 500;">Completed Payments</div>
        </div>

        <!-- Remaining Amount Card -->
        <div style="padding: 20px; border-radius: 8px; color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <span style="font-size: 12px; font-weight: 600; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">Remaining Amount</span>
                <div style="font-size: 28px; opacity: 0.6;"><i class="fa fa-hourglass"></i></div>
            </div>
            <div style="font-size: 26px; font-weight: bold; margin: 10px 0;" id="remaining_amount">Rs. <?= number_format($stats['remaining_amount'], 2) ?></div>
            <div style="font-size: 11px; opacity: 0.85; font-weight: 500;">Pending Payment</div>
        </div>

        <!-- Next Due Date Card -->
        <div style="padding: 20px; border-radius: 8px; color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <span style="font-size: 12px; font-weight: 600; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">Next Due</span>
                <div style="font-size: 28px; opacity: 0.6;"><i class="fa fa-calendar-check-o"></i></div>
            </div>
            <div style="font-size: 26px; font-weight: bold; margin: 10px 0;" id="next_due_date">
                <?php if ($stats['next_due_date']): ?>
                    <?= date('M d, Y', strtotime($stats['next_due_date'])) ?>
                <?php else: ?>
                    <small>No Pending</small>
                <?php endif; ?>
            </div>
            <div style="font-size: 11px; opacity: 0.85; font-weight: 500;">Payment Due Date</div>
        </div>

    </div>

    <!-- Section Title -->
    <div style="margin-top: 30px; margin-bottom: 20px; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px;">
        <h4 style="color: #333; font-weight: 600; margin: 0;">
            <i class="fa fa-table" style="margin-right: 8px; color: #667eea;"></i>
            Payment Records
        </h4>
    </div>

    <!-- Dashboard Box -->
    <div class="row">
        <div class="col-md-12">
            <div style="background: white; border-radius: 8px; padding: 25px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">

                <!-- Search & Filter Section -->
                <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                    <form id="payment_search" onsubmit="return false;">

                        <input type="text" name="invoice_number" id="invoice_number" placeholder="Invoice #" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px; margin-right: 8px; margin-bottom: 10px; width: 18%;">

                        <select name="status" id="status" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px; margin-right: 8px; margin-bottom: 10px; width: 15%;">
                            <option value="">All Status</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="partial">Partial</option>
                            <option value="paid">Paid</option>
                        </select>

                        <input type="date" name="from_date" id="from_date" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px; margin-right: 8px; margin-bottom: 10px; width: 14%;">
                        <input type="date" name="to_date" id="to_date" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px; margin-right: 8px; margin-bottom: 10px; width: 14%;">

                        <input type="text" name="per_page" id="per_page" value="20" placeholder="Records?" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px; margin-right: 8px; margin-bottom: 10px; width: 6%;">

                        <input type="button" class="btn btn-primary"
                            onclick="searchPayments()"
                            value="Search"
                            style="height:32px;padding:0 20px;margin-left:5px;cursor:pointer;" />

                    </form>
                </div>

                <!-- Payment Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="payment_table" style="font-size: 13px; margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice #</th>
                                <th>Contract</th>
                                <th>Invoice Date</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Paid Amount</th>
                                <th>Remaining</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>

                    <div id="paginationArea" class="text-center" style="margin-top: 15px;"></div>

                </div>

            </div>
        </div>
    </div>

</div>

<!-- Upload Payment Proof Modal -->
<div id="uploadProofModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="ace-icon fa fa-upload"></i> Upload Payment Proof
                </h4>
            </div>
            <div class="modal-body">
                <form id="uploadProofForm">
                    <input type="hidden" id="invoiceIdForUpload">
                    <div class="form-group">
                        <label>Select Files (Payment Proof):</label>
                        <input type="file" id="proofFiles" class="form-control" multiple accept="image/*,.pdf" required>
                        <small class="form-text text-muted">JPG, PNG, PDF (Max 5MB each)</small>
                    </div>
                    <div class="form-group">
                        <label>Comments (Optional):</label>
                        <textarea id="proofComments" class="form-control" rows="3" placeholder="Add comments..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitProofUpload()">
                    <i class="ace-icon fa fa-upload"></i> Upload
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(function() {
        searchPayments();
    });

    function searchPayments(page = 1) {
        showLoading();

        const data = new FormData();
        data.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        data.append('flag', 'load');
        data.append('invoice_number', document.getElementById('invoice_number').value);
        data.append('status', document.getElementById('status').value);
        data.append('from_date', document.getElementById('from_date').value);
        data.append('to_date', document.getElementById('to_date').value);
        data.append('per_page', document.getElementById('per_page').value);
        data.append('page', page);

        fetch('index.php?r=payment/payment-history', {
            method: 'POST',
            body: data
        })
        .then(res => res.json())
        .then(res => {
            hideLoading();
            if (res.success) {
                renderPayments(res.invoices);
                renderPagination(res.page, res.totalPages, 'searchPayments');
            } else {
                showError(res.message || 'Failed to load');
            }
        })
        .catch(error => {
            hideLoading();
            console.error(error);
            showError('Error loading payment history');
        });
    }

    function renderPayments(invoices) {
        const tbody = document.querySelector('#payment_table tbody');
        tbody.innerHTML = '';

        if (invoices.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">No payment records found</td></tr>';
            return;
        }

        invoices.forEach((invoice, index) => {
            const statusClass = 'label-' + (invoice.payment_status === 'paid' ? 'success' : (invoice.payment_status === 'partial' ? 'warning' : 'danger'));
            const statusText = (invoice.payment_status || 'unpaid').toUpperCase();

            let actionBtn = `
                <button class="btn btn-xs btn-info" onclick="viewInvoiceDetails(${invoice.id})" style="margin-right: 5px;">
                    <i class="fa fa-eye"></i>
                </button>
            `;

            if (invoice.payment_status !== 'paid') {
                actionBtn += ` <button class="btn btn-xs btn-warning" onclick="uploadProof(${invoice.id})">
                    <i class="fa fa-upload"></i>
                </button>`;
            }

            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${invoice.invoice_number}</strong></td>
                    <td>${invoice.contract_name}</td>
                    <td>${new Date(invoice.invoice_date).toLocaleDateString()}</td>
                    <td>${new Date(invoice.due_date).toLocaleDateString()}</td>
                    <td>Rs. ${parseFloat(invoice.amount).toFixed(2)}</td>
                    <td>Rs. ${parseFloat(invoice.paid_amount || 0).toFixed(2)}</td>
                    <td>Rs. ${parseFloat(invoice.remaining_amount || 0).toFixed(2)}</td>
                    <td><span class="label ${statusClass}">${statusText}</span></td>
                    <td>${actionBtn}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    function renderPagination(page, totalPages, callback = 'searchPayments') {
        const paginationArea = document.getElementById('paginationArea');
        paginationArea.innerHTML = '';

        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            const btnClass = i === page ? 'btn-primary' : 'btn-default';
            const btn = document.createElement('button');
            btn.className = `btn btn-xs ${btnClass}`;
            btn.innerHTML = i;
            btn.style.marginRight = '5px';
            btn.onclick = () => window[callback](i);
            paginationArea.appendChild(btn);
        }
    }

    function uploadProof(invoiceId) {
        document.getElementById('invoiceIdForUpload').value = invoiceId;
        document.getElementById('uploadProofForm').reset();
        jQuery('#uploadProofModal').modal('show');
    }

    function submitProofUpload() {
        const invoiceId = document.getElementById('invoiceIdForUpload').value;
        const files = document.getElementById('proofFiles').files;
        const comments = document.getElementById('proofComments').value;

        if (!invoiceId || files.length === 0) {
            alert('Please select at least one file');
            return;
        }

        const formData = new FormData();
        formData.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        formData.append('flag', 'upload_proof');
        formData.append('invoice_id', invoiceId);
        formData.append('comments', comments);

        for (let i = 0; i < files.length; i++) {
            formData.append('documents[]', files[i]);
        }

        fetch('index.php?r=payment/payment-history', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success!', data.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error(error);
            alert('Error uploading proof');
        });
    }

    function viewInvoiceDetails(invoiceId) {
        alert('Invoice Details: ' + invoiceId);
    }

    function showLoading() {
        $(".stat-value").each(function() {
            $(this).addClass("loading").html("&nbsp;&nbsp;&nbsp;&nbsp;");
        });
    }

    function hideLoading() {
        $(".stat-value").removeClass("loading");
    }

    function showError(message) {
        const alert = $(`<div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 20px;">
            <i class="fa fa-exclamation-circle"></i> ${message}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>`);
        $(document.body).prepend(alert);
        setTimeout(() => alert.fadeOut(), 5000);
    }
</script>

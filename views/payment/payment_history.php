<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="page-content">

    <!-- Dashboard Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h3 style="margin: 0; color: #333; font-size: 26px; font-weight: 600;">
                <i class="fa fa-credit-card" style="margin-right: 10px;"></i>
                Payment History
                <span style="font-size: 14px; color: #999; margin-left: 10px;">Payment Overview & Analytics</span>
            </h3>
        </div>

        <div style="display: flex; gap: 10px;">
            <button id="refreshDashboard" onclick="searchPayments()" style="padding: 8px 16px; background: white; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; font-size: 13px; transition: all 0.3s; color: #333;">
                <i class="fa fa-refresh" style="margin-right: 5px;"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Stats Cards Section - Horizontal Layout -->
    <div style="display: flex; gap: 15px; margin-bottom: 40px; overflow-x: auto; padding-bottom: 10px;">

        <!-- Total Months Card -->
        <div style="flex: 0 0 auto; width: 180px; background: white; border-left: 4px solid #667eea; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <span style="font-size: 11px; color: #999; text-transform: uppercase; font-weight: 500; letter-spacing: 0.5px;">Total Months</span>
                <i class="fa fa-calendar" style="font-size: 20px; color: #667eea; opacity: 0.6;"></i>
            </div>
            <div style="font-size: 24px; font-weight: bold; color: #333; margin: 10px 0;" id="total_months"><?= $stats['total_months'] ?></div>
            <div style="font-size: 11px; color: #999;">Invoice Records</div>
        </div>

        <!-- Paid Amount Card -->
        <div style="flex: 0 0 auto; width: 180px; background: white; border-left: 4px solid #4CAF50; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <span style="font-size: 11px; color: #999; text-transform: uppercase; font-weight: 500; letter-spacing: 0.5px;">Paid Amount</span>
                <i class="fa fa-check-circle" style="font-size: 20px; color: #4CAF50; opacity: 0.6;"></i>
            </div>
            <div style="font-size: 18px; font-weight: bold; color: #333; margin: 10px 0;" id="paid_amount">PKR <?= number_format($stats['paid_amount'], 0) ?></div>
            <div style="font-size: 11px; color: #999;">Completed Payments</div>
        </div>

        <!-- Remaining Amount Card -->
        <div style="flex: 0 0 auto; width: 180px; background: white; border-left: 4px solid #ff9800; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <span style="font-size: 11px; color: #999; text-transform: uppercase; font-weight: 500; letter-spacing: 0.5px;">Remaining</span>
                <i class="fa fa-hourglass" style="font-size: 20px; color: #ff9800; opacity: 0.6;"></i>
            </div>
            <div style="font-size: 18px; font-weight: bold; color: #333; margin: 10px 0;" id="remaining_amount">PKR <?= number_format($stats['remaining_amount'], 0) ?></div>
            <div style="font-size: 11px; color: #999;">Pending Payment</div>
        </div>

        <!-- Next Due Date Card -->
        <div style="flex: 0 0 auto; width: 180px; background: white; border-left: 4px solid #9C27B0; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <span style="font-size: 11px; color: #999; text-transform: uppercase; font-weight: 500; letter-spacing: 0.5px;">Next Due</span>
                <i class="fa fa-calendar-check-o" style="font-size: 20px; color: #9C27B0; opacity: 0.6;"></i>
            </div>
            <div style="font-size: 14px; font-weight: bold; color: #333; margin: 10px 0;" id="next_due_date">
                <?php if ($stats['next_due_date']): ?>
                    <?= date('M d, Y', strtotime($stats['next_due_date'])) ?>
                <?php else: ?>
                    <small>No Pending</small>
                <?php endif; ?>
            </div>
            <div style="font-size: 11px; color: #999;">Payment Due</div>
        </div>

    </div>

    <!-- Section Title - Payment Records -->
    <div style="margin-bottom: 20px;">
        <h4 style="color: #333; font-weight: 600; margin: 0; font-size: 16px;">
            <i class="fa fa-table" style="margin-right: 8px; color: #667eea;"></i>
            Payment Records
        </h4>
    </div>

    <!-- Dashboard Box - Table -->
    <div style="background: white; border-radius: 4px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">

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

                <input type="text" name="per_page" id="per_page" value="20" placeholder="Records?" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px; margin-right: 8px; margin-bottom: 10px; width: 8%;">

                <input type="button" class="btn btn-primary"
                    onclick="searchPayments()"
                    value="Search"
                    style="height:32px;padding:0 20px;cursor:pointer;" />

            </form>
        </div>

        <!-- Payment Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="payment_table" style="font-size: 13px; margin-bottom: 0;">
                <thead style="background-color: #f5f5f5;">
                    <tr>
                        <th style="border-top: 2px solid #ddd;">#</th>
                        <th style="border-top: 2px solid #ddd;">Invoice #</th>
                        <th style="border-top: 2px solid #ddd;">Contract</th>
                        <th style="border-top: 2px solid #ddd;">Invoice Date</th>
                        <th style="border-top: 2px solid #ddd;">Due Date</th>
                        <th style="border-top: 2px solid #ddd;">Amount</th>
                        <th style="border-top: 2px solid #ddd;">Paid Amount</th>
                        <th style="border-top: 2px solid #ddd;">Remaining</th>
                        <th style="border-top: 2px solid #ddd;">Status</th>
                        <th style="border-top: 2px solid #ddd;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded here -->
                </tbody>
            </table>

            <div id="paginationArea" class="text-center" style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;"></div>

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

<script>
    $(function() {
        searchPayments();
    });

    function searchPayments(page = 1) {
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
            if (res.success) {
                renderPayments(res.invoices);
                renderPagination(res.page, res.totalPages, 'searchPayments');
            } else {
                showError(res.message || 'Failed to load');
            }
        })
        .catch(error => {
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
                    <td>PKR ${parseFloat(invoice.amount).toLocaleString('en-US', {maximumFractionDigits: 0})}</td>
                    <td>PKR ${parseFloat(invoice.paid_amount || 0).toLocaleString('en-US', {maximumFractionDigits: 0})}</td>
                    <td>PKR ${parseFloat(invoice.remaining_amount || 0).toLocaleString('en-US', {maximumFractionDigits: 0})}</td>
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

        if (!invoiceId || files.length === 0) {
            alert('Please select at least one file');
            return;
        }

        const formData = new FormData();
        formData.append('_csrf', '<?= Yii::$app->request->getCsrfToken() ?>');
        formData.append('flag', 'upload_proof');
        formData.append('invoice_id', invoiceId);
        formData.append('comments', document.getElementById('proofComments').value);

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
                showError('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error(error);
            showError('Error uploading proof');
        });
    }

    function viewInvoiceDetails(invoiceId) {
        alert('Invoice Details: ' + invoiceId);
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

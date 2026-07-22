<?php
use yii\helpers\Html;
use yii\helpers\Url;
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
            <button id="refreshDashboard" onclick="searchPayments()">
                <i class="fa fa-refresh"></i>
                Refresh
            </button>
        </div>
    </div>

    <div class="stats-grid">

        <!-- Total Months -->
        <div class="stat-card blue">

            <div class="stat-header">

                <span class="stat-title">
                    Total Months
                </span>

                <div class="stat-icon">
                    <i class="fa fa-calendar"></i>
                </div>

            </div>

            <div class="stat-value" id="total_months">
                <?= $stats['total_months'] ?>
            </div>

            <div class="stat-subtitle">
                Invoice Records
            </div>

        </div>


        <!-- Paid Amount -->
        <div class="stat-card green">

            <div class="stat-header">

                <span class="stat-title">
                    Paid Amount
                </span>

                <div class="stat-icon">
                    <i class="fa fa-check-circle"></i>
                </div>

            </div>

            <div class="stat-value" id="paid_amount">
                Rs. <?= number_format($stats['paid_amount'], 2) ?>
            </div>

            <div class="stat-subtitle">
                Completed Payments
            </div>

        </div>




        <!-- Remaining Amount -->
        <div class="stat-card orange">

            <div class="stat-header">

                <span class="stat-title">
                    Remaining Amount
                </span>

                <div class="stat-icon">
                    <i class="fa fa-hourglass"></i>
                </div>

            </div>

            <div class="stat-value" id="remaining_amount">
                Rs. <?= number_format($stats['remaining_amount'], 2) ?>
            </div>

            <div class="stat-subtitle">
                Pending Payment
            </div>

        </div>




        <!-- Next Payment Due -->
        <div class="stat-card purple">

            <div class="stat-header">

                <span class="stat-title">
                    Next Due
                </span>

                <div class="stat-icon">
                    <i class="fa fa-calendar-check-o"></i>
                </div>

            </div>

            <div class="stat-value" id="next_due_date">
                <?php if ($stats['next_due_date']): ?>
                    <?= date('M d, Y', strtotime($stats['next_due_date'])) ?>
                <?php else: ?>
                    <small>No Pending</small>
                <?php endif; ?>
            </div>

            <div class="stat-subtitle">
                Payment Due Date
            </div>

        </div>

    </div>

    <!-- Payment Records Section -->
    <div class="section-title">
        <h4><i class="fa fa-table"></i> Payment Records</h4>
    </div>

    <div class="row">

        <div class="col-md-12">

            <div class="dashboard-box">

                <!-- Search & Filter Section -->
                <div class="dashboard-filters" style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                    <form id="payment_search" onsubmit="return false;">

                        <input type="text" name="invoice_number" id="invoice_number" class="new-input" style="width:18%;" placeholder="Invoice #">

                        <select name="status" id="status" class="new-input" style="width:15%;">
                            <option value="">All Status</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="partial">Partial</option>
                            <option value="paid">Paid</option>
                        </select>

                        <input type="date" name="from_date" id="from_date" class="new-input" style="width:14%;">
                        <input type="date" name="to_date" id="to_date" class="new-input" style="width:14%;">

                        <input type="text" name="per_page" id="per_page" value="20" class="new-input" style="width:6%;" placeholder="Records?">

                        <input type="button" class="btn btn-primary"
                            onclick="searchPayments()"
                            value="Search"
                            style="height:30px;padding:0 10px;margin-top:-3px;margin-left:5px;" />

                    </form>
                </div>

                <!-- Payment Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="payment_table">
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

                    <div id="paginationArea" class="text-center"></div>

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

<style>
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .dashboard-header h3 {
        margin: 0;
        color: #333;
        font-size: 24px;
    }

    .dashboard-header h3 small {
        display: block;
        font-size: 12px;
        color: #999;
        font-weight: normal;
        margin-top: 5px;
    }

    .dashboard-header button {
        padding: 10px 15px;
        background: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.3s;
    }

    .dashboard-header button:hover {
        background: #e8e8e8;
        border-color: #999;
    }

    .dashboard-header button i {
        margin-right: 5px;
    }

    .dashboard-box {
        background: white;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .dashboard-box h4 {
        margin: 0 0 20px 0;
        color: #333;
        font-size: 16px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 15px;
    }

    .dashboard-box h4 i {
        margin-right: 8px;
        color: #667eea;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
        padding: 0 0;
    }

    .stat-card {
        padding: 20px;
        border-radius: 8px;
        color: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }

    .stat-card.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-card.green { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); }
    .stat-card.orange { background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); }
    .stat-card.purple { background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%); }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .stat-title {
        font-size: 12px;
        font-weight: 600;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-icon {
        font-size: 28px;
        opacity: 0.6;
    }

    .stat-value {
        font-size: 26px;
        font-weight: bold;
        margin: 10px 0;
    }

    .stat-subtitle {
        font-size: 11px;
        opacity: 0.85;
        font-weight: 500;
    }

    .new-input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 12px;
        margin-right: 8px;
        margin-bottom: 10px;
    }

    .new-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 5px rgba(102, 126, 234, 0.2);
    }

    .section-title {
        margin-top: 30px;
        margin-bottom: 20px;
        border-bottom: 2px solid #e0e0e0;
        padding-bottom: 10px;
    }

    .section-title h4 {
        color: #333;
        font-weight: 600;
        margin: 0;
    }

    .section-title i {
        margin-right: 8px;
        color: #667eea;
    }

    .badge-paid { background-color: #4CAF50; color: white; }
    .badge-unpaid { background-color: #F44336; color: white; }
    .badge-partial { background-color: #FF9800; color: white; }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        font-size: 13px;
        margin-bottom: 0;
    }

    .table tbody tr:hover {
        background-color: #f9f9f9;
    }

    .loading {
        opacity: 0.6;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 0.6; }
        50% { opacity: 1; }
    }
</style>

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
            const statusClass = 'badge-' + (invoice.payment_status || 'unpaid');
            const statusText = (invoice.payment_status || 'unpaid').toUpperCase();

            let actionBtn = `
                <button class="btn btn-xs btn-info" onclick="viewInvoiceDetails(${invoice.id})">
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
            btn.onclick = () => window[callback](i);
            paginationArea.appendChild(btn);
            paginationArea.appendChild(document.createTextNode(' '));
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
        const alert = $(`<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`);
        $(document.body).prepend(alert);
        setTimeout(() => alert.fadeOut(), 5000);
    }
</script>

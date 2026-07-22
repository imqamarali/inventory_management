<?php
use yii\helpers\Html;
use yii\helpers\Url;

if (!isset($contract)) {
    $contract = [];
}
if (!isset($invoices)) {
    $invoices = [];
}
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=settings/settings">Settings</a>
                </li>
                <li class="active">System Plan</li>
            </ul>
            <div class="nav-search">
                <button type="button"  id="truncateDataBtn" style="display: none;">
                    <i class="fa fa-trash"></i> Truncate Data
                </button>
            </div>
        </div>

        <div class="page-content">

            <div class="tabbable">
                <ul class="nav nav-tabs" id="systemPlanTabs" style="margin-bottom: 20px;">
                    <li class="active">
                        <a data-toggle="tab" href="#contract-info" aria-expanded="true">
                            <i class="green ace-icon fa fa-file-contract bigger-120"></i>
                            Contract Information
                        </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#monthly-invoices" aria-expanded="false">
                            <i class="blue ace-icon fa fa-file-pdf-o bigger-120"></i>
                            Monthly Invoices
                        </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#payment-management" aria-expanded="false">
                            <i class="orange ace-icon fa fa-credit-card bigger-120"></i>
                            Payment Management
                        </a>
                    </li>
                </ul>

                <div class="tab-content">

                    <!-- CONTRACT INFORMATION TAB -->
                    <div id="contract-info" class="tab-pane fade active in">
                        <div class="widget-body">
                            <div class="widget-main padding-12">
                                <form id="contractForm" method="POST" class="form-horizontal">
                                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                                    <input type="hidden" name="flag" value="save_contract">
                                    <?php if (!empty($contract)): ?>
                                        <input type="hidden" name="contract_id" value="<?= htmlspecialchars($contract['id']) ?>">
                                    <?php endif; ?>

                                    <!-- Section 1: Contract Details -->
                                    <h4 style="border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
                                        <i class="ace-icon fa fa-file-contract"></i> Contract Details
                                    </h4>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Contract Name *</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="contract_name"
                                                   value="<?= htmlspecialchars($contract['contract_name'] ?? '') ?>"
                                                   placeholder="System Contract Name" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Contract Type *</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="contract_type" required>
                                                <option value="monthly" <?= ($contract['contract_type'] ?? 'monthly') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                                <option value="yearly" <?= ($contract['contract_type'] ?? '') === 'yearly' ? 'selected' : '' ?>>Yearly</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">System Status</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="system_status">
                                                <option value="active" <?= ($contract['system_status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                                <option value="inactive" <?= ($contract['system_status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                <option value="suspended" <?= ($contract['system_status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                                <option value="expired" <?= ($contract['system_status'] ?? '') === 'expired' ? 'selected' : '' ?>>Expired</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Section 2: Contractor Information -->
                                    <h4 style="border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-top: 30px; margin-bottom: 20px;">
                                        <i class="ace-icon fa fa-user"></i> Contractor Information
                                    </h4>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Contractor Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="contractor_name"
                                                   value="<?= htmlspecialchars($contract['contractor_name'] ?? '') ?>"
                                                   placeholder="Full Name">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">CNIC</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="contractor_cnic"
                                                   value="<?= htmlspecialchars($contract['contractor_cnic'] ?? '') ?>"
                                                   placeholder="00000-0000000-0">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Phone</label>
                                        <div class="col-sm-9">
                                            <input type="tel" class="form-control" name="contractor_phone"
                                                   value="<?= htmlspecialchars($contract['contractor_phone'] ?? '') ?>"
                                                   placeholder="+92-300-0000000">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control" name="contractor_email"
                                                   value="<?= htmlspecialchars($contract['contractor_email'] ?? '') ?>"
                                                   placeholder="email@example.com">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Address</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="contractor_address" rows="2"><?= htmlspecialchars($contract['contractor_address'] ?? '') ?></textarea>
                                        </div>
                                    </div>

                                    <!-- Section 3: Important Dates -->
                                    <h4 style="border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-top: 30px; margin-bottom: 20px;">
                                        <i class="ace-icon fa fa-calendar"></i> Important Dates
                                    </h4>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Installation Date</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control" name="installation_date"
                                                   value="<?= htmlspecialchars($contract['installation_date'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Contract Start Date</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control" name="contract_start_date"
                                                   value="<?= htmlspecialchars($contract['contract_start_date'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Contract End Date</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control" name="contract_end_date"
                                                   value="<?= htmlspecialchars($contract['contract_end_date'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Monthly Due Date (Day)</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" name="monthly_due_date" min="1" max="31"
                                                   value="<?= htmlspecialchars($contract['monthly_due_date'] ?? 1) ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Max Extension Days</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" name="maximum_extension_days" min="0"
                                                   value="<?= htmlspecialchars($contract['maximum_extension_days'] ?? 15) ?>">
                                        </div>
                                    </div>

                                    <!-- Section 4: Financial Details -->
                                    <h4 style="border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-top: 30px; margin-bottom: 20px;">
                                        <i class="ace-icon fa fa-money"></i> Financial Details
                                    </h4>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Monthly Charges</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" name="monthly_charges" step="0.01"
                                                   value="<?= htmlspecialchars($contract['monthly_charges'] ?? 0) ?>"
                                                   placeholder="0.00">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Yearly Charges</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" name="yearly_charges" step="0.01"
                                                   value="<?= htmlspecialchars($contract['yearly_charges'] ?? 0) ?>"
                                                   placeholder="0.00">
                                        </div>
                                    </div>

                                    <!-- Section 5: Descriptions -->
                                    <h4 style="border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-top: 30px; margin-bottom: 20px;">
                                        <i class="ace-icon fa fa-file-text"></i> Descriptions & Policies
                                    </h4>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Contract Description</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="contract_description" rows="3"><?= htmlspecialchars($contract['contract_description'] ?? '') ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Policy Description</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="policy_description" rows="3"><?= htmlspecialchars($contract['policy_description'] ?? '') ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Contractor Info</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="contractor_info" rows="3"><?= htmlspecialchars($contract['contractor_info'] ?? '') ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Full Description</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="full_description" rows="3"><?= htmlspecialchars($contract['full_description'] ?? '') ?></textarea>
                                            <small class="text-muted">This will be displayed on PDF reports</small>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"></label>
                                        <div class="col-sm-9">
                                            <button type="submit" class="btn btn-lg btn-success">
                                                <i class="ace-icon fa fa-save"></i> Save Contract
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- MONTHLY INVOICES TAB -->
                    <div id="monthly-invoices" class="tab-pane fade">
                        <div class="widget-body">
                            <div class="widget-main padding-12">
                                <div style="margin-bottom: 15px;">
                                    <button type="button"  id="generateInvoiceBtn">
                                        <i class="ace-icon fa fa-plus"></i> Generate Current Month Invoice
                                    </button>
                                </div>

                                <?php if (empty($invoices)): ?>
                                    <div class="alert alert-info text-center">
                                        <i class="ace-icon fa fa-file-pdf-o fa-3x" style="color:#6FB3E0;"></i>
                                        <h4 style="margin-top: 15px;">No Invoices Found</h4>
                                        <p>No monthly invoices generated yet. Click the button above to generate one.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Invoice #</th>
                                                    <th>Month</th>
                                                    <th class="text-right">Amount</th>
                                                    <th>Due Date</th>
                                                    <th>Invoice Status</th>
                                                    <th>Payment Status</th>
                                                    <th class="text-right">Paid</th>
                                                    <th class="text-right">Remaining</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($invoices as $invoice): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($invoice['invoice_number']) ?></td>
                                                        <td><?= htmlspecialchars($invoice['invoice_month']) ?></td>
                                                        <td class="text-right"><?= number_format($invoice['amount'], 2) ?></td>
                                                        <td><?= htmlspecialchars($invoice['due_date']) ?></td>
                                                        <td>
                                                            <span class="label label-<?= $invoice['invoice_status'] === 'paid' ? 'success' : ($invoice['invoice_status'] === 'overdue' ? 'danger' : 'warning') ?>">
                                                                <?= htmlspecialchars($invoice['invoice_status']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="label label-<?= $invoice['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                                                <?= htmlspecialchars($invoice['payment_status']) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-right"><?= number_format($invoice['paid_amount'], 2) ?></td>
                                                        <td class="text-right"><?= number_format($invoice['remaining_amount'], 2) ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-xs btn-info" onclick="viewInvoice(<?= $invoice['id'] ?>)">
                                                                <i class="ace-icon fa fa-eye"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- PAYMENT MANAGEMENT TAB -->
                    <div id="payment-management" class="tab-pane fade">
                        <div class="widget-body">
                            <div class="widget-main padding-12">
                                <div class="alert alert-info">
                                    <i class="ace-icon fa fa-info-circle"></i>
                                    <strong>Payment Management:</strong> Upload payment proofs for verification by Super Admin.
                                </div>

                                <?php
                                $pendingInvoices = array_filter($invoices, function($inv) {
                                    return $inv['payment_status'] !== 'paid';
                                });
                                ?>

                                <?php if (empty($pendingInvoices)): ?>
                                    <div class="alert alert-success text-center">
                                        <i class="ace-icon fa fa-check-circle fa-3x" style="color:#5cb85c;"></i>
                                        <h4 style="margin-top: 15px;">All Paid</h4>
                                        <p>No pending payments. All invoices are paid up.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Invoice #</th>
                                                    <th class="text-right">Amount</th>
                                                    <th>Due Date</th>
                                                    <th>Payment Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($pendingInvoices as $invoice): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($invoice['invoice_number']) ?></td>
                                                        <td class="text-right"><?= number_format($invoice['amount'], 2) ?></td>
                                                        <td><?= htmlspecialchars($invoice['due_date']) ?></td>
                                                        <td>
                                                            <span class="label label-warning">
                                                                <?= htmlspecialchars($invoice['payment_status']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-xs btn-success" onclick="uploadPaymentProof(<?= $invoice['id'] ?>)">
                                                                <i class="ace-icon fa fa-upload"></i> Upload Proof
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Helper function to show alert when SweetAlert is ready
function showAlert(title, text, icon = 'info') {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            timer: 2000,
            timerProgressBar: true
        });
    } else {
        alert(title + ': ' + text);
    }
}

// Handle contract form submission
document.getElementById('contractForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    // Debug: log form data
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }

    fetch('index.php?r=settings/systemplan', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);

        if (data.success) {
            // If new contract was created, update the hidden contract_id
            if (data.contract_id) {
                const contractIdInput = form.querySelector('input[name="contract_id"]');
                if (!contractIdInput) {
                    const newInput = document.createElement('input');
                    newInput.type = 'hidden';
                    newInput.name = 'contract_id';
                    newInput.value = data.contract_id;
                    form.appendChild(newInput);
                } else {
                    contractIdInput.value = data.contract_id;
                }
            }

            showAlert('Success!', data.message, 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert('Error!', data.message || 'Failed to save contract', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error!', 'An error occurred while saving the contract', 'error');
    });
});

// Generate invoice
document.getElementById('generateInvoiceBtn').addEventListener('click', function() {
    const contractId = document.querySelector('input[name="contract_id"]') ? document.querySelector('input[name="contract_id"]').value : null;

    if (!contractId) {
        showAlert('Error!', 'Please save the contract first.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->getCsrfToken() ?>');
    formData.append('flag', 'generate_invoices');
    formData.append('contract_id', contractId);

    fetch('index.php?r=settings/systemplan', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Success!', data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('Error!', data.message, 'error');
        }
    });
});

// Upload payment proof
function uploadPaymentProof(invoiceId) {
    if (typeof Swal === 'undefined') {
        alert('SweetAlert library not loaded. Please try again.');
        return;
    }

    Swal.fire({
        title: 'Upload Payment Proof',
        html: `
            <div style="text-align: left;">
                <div class="form-group">
                    <label>Payment Date:</label>
                    <input type="date" id="proofDate" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Payment Method:</label>
                    <select id="paymentMethod" class="form-control" required>
                        <option value="">Select Method</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="check">Check</option>
                        <option value="online">Online Payment</option>
                        <option value="cash">Cash</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Amount Paid:</label>
                    <input type="number" id="proofAmount" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Transaction ID (if applicable):</label>
                    <input type="text" id="transactionId" class="form-control" placeholder="e.g., UTR, Cheque No.">
                </div>
                <div class="form-group">
                    <label>Proof Document (JPG, PNG, or PDF):</label>
                    <input type="file" id="proofFile" class="form-control" required accept="image/*,application/pdf">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        allowOutsideClick: false,
        preConfirm: function() {
            const proofDate = document.getElementById('proofDate').value;
            const paymentMethod = document.getElementById('paymentMethod').value;
            const proofAmount = document.getElementById('proofAmount').value;
            const transactionId = document.getElementById('transactionId').value;
            const proofFile = document.getElementById('proofFile').files[0];

            if (!proofDate || !paymentMethod || !proofAmount || !proofFile) {
                Swal.showValidationError('Please fill all required fields.');
                return false;
            }

            const formData = new FormData();
            formData.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->getCsrfToken() ?>');
            formData.append('flag', 'upload_payment_proof');
            formData.append('invoice_id', invoiceId);
            formData.append('proof_date', proofDate);
            formData.append('payment_method', paymentMethod);
            formData.append('amount', proofAmount);
            formData.append('transaction_id', transactionId);
            formData.append('document', proofFile);

            return fetch('index.php?r=settings/systemplan', {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (!data.success) {
                    Swal.showValidationError(data.message);
                    return false;
                }
                return data;
            });
        }
    }).then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Payment proof uploaded successfully. It will be verified soon.',
                timer: 2000
            });
            setTimeout(() => location.reload(), 2000);
        }
    });
}

function viewInvoice(invoiceId) {
    showAlert('Info', 'Invoice view functionality will be implemented.', 'info');
}

// Check if user is Super Admin and show truncate button
function checkSuperAdminStatus() {
    const userId = <?= Yii::$app->user->id ?? 'null' ?>;
    if (!userId) return;

    fetch('index.php?r=settings/systemplan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: '<?= Yii::$app->request->csrfParam ?>=<?= Yii::$app->request->getCsrfToken() ?>&flag=check_admin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.is_super_admin) {
            document.getElementById('truncateDataBtn').style.display = 'inline-block';
        }
    })
    .catch(err => console.log('Could not check admin status:', err));
}

// Handle truncate button click
document.getElementById('truncateDataBtn').addEventListener('click', function() {
    if (typeof Swal === 'undefined') {
        alert('Please confirm: Delete all invoices and payment proofs?');
        if (!confirm('This action cannot be undone. Proceed?')) return;
    } else {
        Swal.fire({
            title: 'Truncate Data?',
            text: 'This will delete all monthly invoices and payment management data. This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Truncate',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
                performTruncate();
            }
        });
        return;
    }
    performTruncate();
});

function performTruncate() {
    const formData = new FormData();
    formData.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->getCsrfToken() ?>');
    formData.append('flag', 'truncate_data');

    fetch('index.php?r=settings/systemplan', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Success!', data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('Error!', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error!', 'An error occurred', 'error');
    });
}

// Check admin status on page load
jQuery(document).ready(function() {
    checkSuperAdminStatus();
});
</script>

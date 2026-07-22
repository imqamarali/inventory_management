<?php
use yii\helpers\Html;
use yii\helpers\Url;

$pendingInfo = Yii::$app->session->get('pending_invoice_info');
$userRole = Yii::$app->session->get('user_array')['role_id'] ?? null;

if (empty($pendingInfo)) {
    return;
}

// Check if user is Super Admin
$isSuperAdmin = Yii::$app->db->createCommand(
    "SELECT COUNT(*) FROM roles sr
     JOIN system_users su ON su.role_id = sr.id
     WHERE sr.name = 'Super Admin' AND su.id = :user_id"
)->bindValue(':user_id', Yii::$app->user->id)->queryScalar() > 0;
?>

<div id="pendingInvoiceModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #f44336; color: white;">
                <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-exclamation-triangle"></i> Pending Invoice Payment
                </h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>Action Required!</strong> You have pending invoice(s) that require payment or verification.
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead style="background-color: #f5f5f5;">
                            <tr>
                                <th>Invoice</th>
                                <th>Month</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Days Remaining</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingInfo as $invoice): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($invoice['invoice_number']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($invoice['invoice_month']) ?></td>
                                    <td>
                                        <strong><?= number_format($invoice['amount'], 2) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($invoice['due_date']) ?></td>
                                    <td>
                                        <?php
                                        $daysRemaining = $invoice['days_remaining'];
                                        $badgeClass = $daysRemaining <= 0 ? 'badge-danger' : ($daysRemaining <= 5 ? 'badge-warning' : 'badge-info');
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= $daysRemaining <= 0 ? 'OVERDUE' : $daysRemaining . ' days' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-success" onclick="uploadPaymentProof(<?= $invoice['invoice_id'] ?>)">
                                            <i class="fa fa-upload"></i> Upload Proof
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="panel panel-info" style="margin-top: 20px;">
                    <div class="panel-heading">
                        <h4 class="panel-title">Payment Instructions</h4>
                    </div>
                    <div class="panel-body">
                        <ol style="line-height: 1.8;">
                            <li>Prepare proof of payment (receipt, bank confirmation, etc.)</li>
                            <li>Click "Upload Proof" button for the invoice you've paid</li>
                            <li>Select the payment date, method, and upload the proof document</li>
                            <li>Your payment will be verified by the Super Administrator</li>
                            <li>Once verified, your system access will be fully restored</li>
                        </ol>
                    </div>
                </div>

                <?php if ($isSuperAdmin): ?>
                <div class="panel panel-success" style="margin-top: 20px;">
                    <div class="panel-heading">
                        <h4 class="panel-title">Super Admin: Payment Management</h4>
                    </div>
                    <div class="panel-body">
                        <a href="<?= Url::to(['settings/systemplan']) ?>" class="btn btn-primary">
                            <i class="fa fa-cog"></i> Go to Payment Management
                        </a>
                        <p style="margin-top: 10px; font-size: 12px;" class="text-muted">
                            As Super Admin, you can verify and approve payment proofs.
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="closeInvoiceModal()">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function uploadPaymentProof(invoiceId) {
    if (typeof Swal === 'undefined') {
        alert('SweetAlert library not loaded. Please try again.');
        return;
    }

    Swal.fire({
        title: 'Upload Payment Proof',
        html: `
            <form id="paymentProofForm" style="text-align: left;">
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
                <small class="text-muted">Maximum file size: 5MB</small>
            </form>
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
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        }
    });
}

function closeInvoiceModal() {
    jQuery('#pendingInvoiceModal').modal('hide');
}

// Show modal automatically on page load if there are pending invoices
jQuery(function() {
    jQuery('#pendingInvoiceModal').modal({
        backdrop: 'static',
        keyboard: false
    });
});
</script>

<style>
#pendingInvoiceModal .modal-header {
    border-color: #f44336;
}

#pendingInvoiceModal .modal-body {
    max-height: 500px;
    overflow-y: auto;
}

#pendingInvoiceModal .badge {
    padding: 5px 10px;
    font-size: 11px;
    font-weight: bold;
}

#pendingInvoiceModal .badge-danger {
    background-color: #e74c3c;
}

#pendingInvoiceModal .badge-warning {
    background-color: #f39c12;
}

#pendingInvoiceModal .badge-info {
    background-color: #3498db;
}
</style>

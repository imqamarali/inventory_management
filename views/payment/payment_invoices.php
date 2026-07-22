<?php
use yii\helpers\Html;
use yii\helpers\Url;

if (!isset($paymentProofs)) {
    $paymentProofs = [];
}
if (!isset($verifiedProofs)) {
    $verifiedProofs = [];
}
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Dashboard</a>
                </li>
                <li class="active">Payment Invoice Management</li>
            </ul>
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="widget-body">
                        <div class="widget-main padding-12">
                            <!-- Pending Payment Proofs -->
                            <div class="row">
                                <div class="col-xs-12">
                                    <h4 style="color: #e74c3c;">
                                        <i class="fa fa-hourglass-half"></i>
                                        Pending Payment Proofs (<?= count($paymentProofs) ?>)
                                    </h4>

                                    <?php if (empty($paymentProofs)): ?>
                                        <div class="alert alert-success">
                                            <i class="fa fa-check-circle"></i>
                                            No pending payment proofs for verification.
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive" style="overflow-x: auto;">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead style="background-color: #f5f5f5;">
                                                    <tr>
                                                        <th>Proof #</th>
                                                        <th>Invoice #</th>
                                                        <th>Amount</th>
                                                        <th>Payment Date</th>
                                                        <th>Method</th>
                                                        <th>Document</th>
                                                        <th>Submitted</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($paymentProofs as $proof): ?>
                                                        <tr>
                                                            <td>
                                                                <strong><?= htmlspecialchars($proof['proof_number']) ?></strong>
                                                            </td>
                                                            <td><?= htmlspecialchars($proof['invoice_number']) ?></td>
                                                            <td><?= number_format($proof['amount'], 2) ?></td>
                                                            <td><?= htmlspecialchars($proof['proof_date']) ?></td>
                                                            <td>
                                                                <span class="label label-info">
                                                                    <?= ucfirst(str_replace('_', ' ', $proof['payment_method'])) ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($proof['document_file'])): ?>
                                                                    <a href="<?= Url::to('@web/' . $proof['document_file']) ?>" target="_blank" class="btn btn-xs btn-info">
                                                                        <i class="fa fa-file"></i> View
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="text-muted">N/A</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <small><?= date('d M Y', strtotime($proof['created_at'])) ?></small>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-xs btn-success" onclick="approveProof(<?= $proof['id'] ?>)">
                                                                    <i class="fa fa-check"></i> Approve
                                                                </button>
                                                                <button type="button" class="btn btn-xs btn-danger" onclick="rejectProof(<?= $proof['id'] ?>)">
                                                                    <i class="fa fa-times"></i> Reject
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

                            <hr style="margin: 30px 0;">

                            <!-- Verified/Rejected Proofs -->
                            <div class="row">
                                <div class="col-xs-12">
                                    <h4 style="color: #27ae60;">
                                        <i class="fa fa-check-double"></i>
                                        Verified & Rejected Proofs (<?= count($verifiedProofs) ?>)
                                    </h4>

                                    <?php if (empty($verifiedProofs)): ?>
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i>
                                            No verified or rejected proofs yet.
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive" style="overflow-x: auto;">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead style="background-color: #f5f5f5;">
                                                    <tr>
                                                        <th>Proof #</th>
                                                        <th>Invoice #</th>
                                                        <th>Amount</th>
                                                        <th>Payment Date</th>
                                                        <th>Status</th>
                                                        <th>Verified By</th>
                                                        <th>Verified Date</th>
                                                        <th>Details</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($verifiedProofs as $proof): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($proof['proof_number']) ?></td>
                                                            <td><?= htmlspecialchars($proof['invoice_number']) ?></td>
                                                            <td><?= number_format($proof['amount'], 2) ?></td>
                                                            <td><?= htmlspecialchars($proof['proof_date']) ?></td>
                                                            <td>
                                                                <?php
                                                                $statusClass = $proof['verification_status'] === 'verified' ? 'success' : 'danger';
                                                                $statusText = $proof['verification_status'] === 'verified' ? 'Verified' : 'Rejected';
                                                                ?>
                                                                <span class="label label-<?= $statusClass ?>">
                                                                    <?= $statusText ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if ($proof['verified_by']) {
                                                                    $verifier = Yii::$app->db->createCommand(
                                                                        "SELECT first_name, last_name FROM system_users WHERE id = :id"
                                                                    )->bindValue(':id', $proof['verified_by'])->queryOne();
                                                                    echo htmlspecialchars(($verifier['first_name'] ?? '') . ' ' . ($verifier['last_name'] ?? ''));
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <small><?= date('d M Y H:i', strtotime($proof['verified_at'])) ?></small>
                                                            </td>
                                                            <td>
                                                                <?php if ($proof['verification_status'] === 'rejected' && $proof['rejection_reason']): ?>
                                                                    <button type="button" class="btn btn-xs btn-info" onclick="showRejectionReason('<?= htmlspecialchars($proof['rejection_reason']) ?>')">
                                                                        <i class="fa fa-eye"></i> Reason
                                                                    </button>
                                                                <?php endif; ?>
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
</div>

<script>
function approveProof(proofId) {
    swal({
        title: 'Approve Payment Proof?',
        text: 'This will mark the payment proof as verified and update the invoice status.',
        type: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve'
    }, function() {
        const formData = new FormData();
        formData.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->getCsrfToken() ?>');
        formData.append('flag', 'verify_proof');
        formData.append('proof_id', proofId);
        formData.append('action', 'approve');

        fetch('index.php?r=payment/payment-invoices', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                swal('Success!', data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                swal('Error!', data.message, 'error');
            }
        });
    });
}

function rejectProof(proofId) {
    swal({
        title: 'Reject Payment Proof',
        input: 'textarea',
        inputPlaceholder: 'Enter reason for rejection...',
        showCancelButton: true,
        confirmButtonText: 'Reject',
        inputValidator: (value) => {
            if (!value) {
                return 'Please provide a reason for rejection.';
            }
        }
    }).then(function(result) {
        if (result.value) {
            const formData = new FormData();
            formData.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->getCsrfToken() ?>');
            formData.append('flag', 'verify_proof');
            formData.append('proof_id', proofId);
            formData.append('action', 'reject');
            formData.append('rejection_reason', result.value);

            fetch('index.php?r=payment/payment-invoices', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal('Success!', data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    swal('Error!', data.message, 'error');
                }
            });
        }
    });
}

function showRejectionReason(reason) {
    swal({
        title: 'Rejection Reason',
        text: reason,
        type: 'info'
    });
}
</script>

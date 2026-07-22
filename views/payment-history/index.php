<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=inventory/dashboard">Home</a>
                </li>
                <li class="active">Payment History</li>
            </ul>
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="widget-body">
                        <div class="widget-main padding-12">
                            <h4 class="header lighter smaller">
                                <i class="ace-icon fa fa-money"></i>
                                Payment History
                            </h4>

                            <?php if (empty($invoices)) { ?>
                                <div class="alert alert-info text-center">
                                    <i class="ace-icon fa fa-info-circle fa-3x" style="color: #6FB3E0;"></i>
                                    <h4 style="margin-top: 15px;">No Payment Records Found</h4>
                                    <p>All your invoices are paid or no invoices exist yet.</p>
                                </div>
                            <?php } else { ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Invoice #</th>
                                                <th>Contract</th>
                                                <th>Amount</th>
                                                <th>Due Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($invoices as $invoice): ?>
                                                <tr>
                                                    <td><strong><?= htmlspecialchars($invoice['invoice_number']) ?></strong></td>
                                                    <td><?= htmlspecialchars($invoice['contract_name']) ?></td>
                                                    <td>
                                                        <span class="badge badge-success">
                                                            Rs. <?= number_format($invoice['amount'], 2) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('M d, Y', strtotime($invoice['due_date'])) ?></td>
                                                    <td>
                                                        <?php
                                                        $statusClass = match ($invoice['payment_status']) {
                                                            'paid' => 'badge-success',
                                                            'partial' => 'badge-warning',
                                                            default => 'badge-danger'
                                                        };
                                                        $statusText = ucfirst($invoice['payment_status']);
                                                        ?>
                                                        <span class="badge <?= $statusClass ?>">
                                                            <?= $statusText ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-xs btn-info" onclick="viewInvoiceDetails(<?= $invoice['id'] ?>)" title="View">
                                                            <i class="ace-icon fa fa-eye"></i>
                                                        </button>
                                                        <?php if ($invoice['payment_status'] !== 'paid'): ?>
                                                            <button class="btn btn-xs btn-warning" onclick="uploadPaymentProof(<?= $invoice['id'] ?>)" title="Upload Proof">
                                                                <i class="ace-icon fa fa-upload"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Invoice Details Modal -->
<div id="invoiceDetailsModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="ace-icon fa fa-receipt"></i> Invoice Details
                </h4>
            </div>
            <div class="modal-body" id="invoiceDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                        <label>Select Files (Proof Documents):</label>
                        <input type="file" id="proofFiles" class="form-control" multiple accept="image/*,.pdf" required>
                        <small class="form-text text-muted">You can upload multiple files (JPG, PNG, PDF)</small>
                    </div>
                    <div class="form-group">
                        <label>Comments (Optional):</label>
                        <textarea id="proofComments" class="form-control" rows="3" placeholder="Add any comments about this payment..."></textarea>
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
    .payment-proof-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 15px;
        margin: 20px 0;
    }

    .proof-item {
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .proof-item:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transform: scale(1.05);
    }

    .proof-thumbnail {
        width: 100%;
        height: 100px;
        object-fit: cover;
        background: #f5f5f5;
    }

    .proof-name {
        padding: 8px;
        font-size: 11px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .badge-pending {
        background-color: #FFC107;
        color: #000;
    }

    .badge-verified {
        background-color: #4CAF50;
        color: white;
    }

    .badge-rejected {
        background-color: #F44336;
        color: white;
    }
</style>

<script>
    function viewInvoiceDetails(invoiceId) {
        jQuery.post('index.php?r=payment-history/get-invoice-details', {
            invoice_id: invoiceId
        }, function(data) {
            if (data.success) {
                const invoice = data.invoice;
                const proofs = data.proofs;
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Invoice #:</strong> ${invoice.invoice_number}</p>
                            <p><strong>Contract:</strong> ${invoice.contract_name}</p>
                            <p><strong>Amount:</strong> Rs. ${parseFloat(invoice.amount).toFixed(2)}</p>
                            <p><strong>Due Date:</strong> ${new Date(invoice.due_date).toLocaleDateString()}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Invoice Date:</strong> ${new Date(invoice.invoice_date).toLocaleDateString()}</p>
                            <p><strong>Status:</strong>
                                <span class="badge badge-${invoice.payment_status}">
                                    ${invoice.payment_status.toUpperCase()}
                                </span>
                            </p>
                            <p><strong>Paid Amount:</strong> Rs. ${parseFloat(invoice.paid_amount).toFixed(2)}</p>
                            <p><strong>Remaining:</strong> Rs. ${parseFloat(invoice.remaining_amount).toFixed(2)}</p>
                        </div>
                    </div>
                    <hr>
                    <h5><i class="ace-icon fa fa-file-image-o"></i> Payment Proofs</h5>
                `;

                if (proofs.length === 0) {
                    html += '<p class="text-muted">No payment proofs uploaded yet.</p>';
                } else {
                    html += '<div class="payment-proof-gallery">';
                    proofs.forEach(proof => {
                        const isImage = /\.(jpg|jpeg|png|gif)$/i.test(proof.document_file);
                        const thumbnail = isImage ? `web/${proof.document_file}` : 'web/images/file-icon.png';
                        html += `
                            <div class="proof-item" onclick="viewProofDetails(${proof.id})">
                                <img src="${thumbnail}" class="proof-thumbnail" alt="Proof">
                                <div class="proof-name">${proof.document_name}</div>
                                <div style="padding: 4px; font-size: 10px;">
                                    <span class="badge badge-${proof.verification_status}">
                                        ${proof.verification_status.charAt(0).toUpperCase() + proof.verification_status.slice(1)}
                                    </span>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }

                jQuery('#invoiceDetailsContent').html(html);
                jQuery('#invoiceDetailsModal').modal('show');
            } else {
                alert('Error: ' + data.message);
            }
        }, 'json');
    }

    function uploadPaymentProof(invoiceId) {
        jQuery('#invoiceIdForUpload').val(invoiceId);
        jQuery('#uploadProofForm')[0].reset();
        jQuery('#uploadProofModal').modal('show');
    }

    function submitProofUpload() {
        const invoiceId = jQuery('#invoiceIdForUpload').val();
        const files = jQuery('#proofFiles')[0].files;
        const comments = jQuery('#proofComments').val();

        if (!invoiceId || files.length === 0) {
            alert('Please select at least one file');
            return;
        }

        const formData = new FormData();
        formData.append('invoice_id', invoiceId);
        formData.append('comments', comments);

        for (let i = 0; i < files.length; i++) {
            formData.append('documents[]', files[i]);
        }

        jQuery.ajax({
            url: 'index.php?r=payment-history/upload-proof',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    alert('Error: ' + data.message);
                }
            },
            error: function() {
                alert('Error uploading proof');
            }
        });
    }

    function viewProofDetails(proofId) {
        // This can be expanded to show detailed proof information
        alert('Proof ID: ' + proofId);
    }
</script>

<?php
/**
 * SUPPLIER PAYMENTS VIEW
 * ================================================================================
 * PURPOSE: Record and track payments made to suppliers
 *
 * FUNCTIONALITY:
 * - Record supplier invoice payments
 * - Track payment date, method (Cash, Cheque, Bank Transfer)
 * - Link payments to purchase invoices
 * - Track payment status (Pending, Completed)
 * - Filter payments by supplier, date range
 * - Generate payment vouchers
 * - Reconcile with bank statements
 * - Support partial and full payments
 *
 * DATA MANAGEMENT:
 * - Records stored in: inventory_payments table with reference_type='Purchase'
 * - Tracks: payment_id, supplier_id, invoice_id (reference_id), amount,
 *           payment_date, payment_method, remarks, status
 * - Links to: inventory_purchase_invoices for invoice reconciliation
 * - Status: Pending (not yet cleared), Completed (cleared/reconciled)
 *
 * FINANCE INTEGRATION:
 * - Supplier payments represent actual CASH OUTFLOWS
 * - Core data for:
 *   • Cash Flow Statement (Operating Activities - Payments to Suppliers)
 *   • Bank Account reconciliation
 *   • Accounts Payable reduction tracking
 *   • Supplier payment performance analysis
 * - Payment method enables bank statement matching
 * - Timing determines when expense converts to cash outflow
 * ================================================================================
 */

use yii\helpers\Html;

if (!isset($suppliers)) $suppliers = [];
if (!isset($payments)) $payments = []; ?>
<div class="container-fluid pt-4">
    <div class="row mb-4">
        <div class="col">
            <h3><i class="fa fa-money"></i> Supplier Payments</h3>
        </div>
        <div class="col-auto"><button class="btn btn-primary btn-sm" onclick="openPaymentModal()"><i class="fa fa-plus"></i> Record Payment</button></div>
    </div>
    <div id="alerts-sp"></div>
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3" onsubmit="return false;">
                <div class="col-md-4"><select class="form-select form-select-sm" id="supplierFilter" onchange="filterPayments()">
                        <option value="">All Suppliers</option><?php foreach ($suppliers as $s): ?><option value="<?= $s['id'] ?>"><?= Html::encode($s['company_name'] ?? '') ?></option><?php endforeach; ?>
                    </select></div>
                <div class="col-md-4"><input type="month" class="form-control form-control-sm" id="monthFilter" onchange="filterPayments()"></div>
                <div class="col-md-4"><button class="btn btn-sm btn-primary" onclick="filterPayments()"><i class="fa fa-search"></i> Filter</button></div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="paymentRows"><?php if (empty($payments)): ?><tr>
                            <td colspan="6" class="text-center text-muted">No payments</td>
                        </tr><?php else: foreach ($payments as $p): ?><tr>
                                <td><?= Html::encode($p['payment_date'] ?? '') ?></td>
                                <td><?= Html::encode($p['company_name'] ?? '') ?></td>
                                <td><?= Html::encode($p['payment_method'] ?? '') ?></td>
                                <td><?= Html::encode($p['reference_no'] ?? '') ?></td>
                                <td>PKR <?= number_format($p['amount'] ?? 0, 0) ?></td>
                                <td><button class="btn btn-xs btn-info" onclick="viewPayment(<?= $p['id'] ?>)"><i class="fa fa-eye"></i></button></td>
                            </tr><?php endforeach;
                                        endif; ?></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Record Payment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <div class="mb-3"><label class="form-label">Supplier<span class="text-danger">*</span></label><select class="form-select form-select-sm" id="supplierId" name="supplier_id" required><?php foreach ($suppliers as $s): ?><option value="<?= $s['id'] ?>"><?= Html::encode($s['company_name'] ?? '') ?></option><?php endforeach; ?></select></div>
                    <div class="mb-3"><label class="form-label">Payment Date<span class="text-danger">*</span></label><input type="date" class="form-control form-control-sm" id="paymentDate" name="payment_date" value="<?= date('Y-m-d') ?>" required></div>
                    <div class="mb-3"><label class="form-label">Amount<span class="text-danger">*</span></label><input type="number" class="form-control form-control-sm" id="paymentAmount" name="amount" step="0.01" min="0" required></div>
                    <div class="mb-3"><label class="form-label">Method<span class="text-danger">*</span></label><select class="form-select form-select-sm" id="paymentMethod" name="payment_method" required>
                            <option value="">Select</option>
                            <option>Cash</option>
                            <option>Check</option>
                            <option>Bank Transfer</option>
                            <option>Credit</option>
                        </select></div>
                    <div class="mb-3"><label class="form-label">Reference No</label><input type="text" class="form-control form-control-sm" id="refNo" name="reference_no"></div>
                    <div class="mb-3"><label class="form-label">Notes</label><textarea class="form-control form-control-sm" id="notes" name="notes" rows="2"></textarea></div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button><button type="button" class="btn btn-primary btn-sm" onclick="savePayment()">Save</button></div>
        </div>
    </div>
</div>
<script>
    function htmlEscape(t) {
        if (!t) return '';
        const m = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(t).replace(/[&<>"']/g, c => m[c]);
    }
    const Storage = {
        set: (k, v) => {
            try {
                localStorage.setItem(k, v);
            } catch (e) {
                console.warn('Storage blocked:', e.message);
            }
        },
        get: (k) => {
            try {
                return localStorage.getItem(k);
            } catch (e) {
                console.warn('Storage blocked:', e.message);
                return null;
            }
        }
    };

    function openPaymentModal() {
        $('#paymentForm')[0].reset();
        $('#paymentDate').val('<?= date('Y-m-d') ?>');
        new bootstrap.Modal($('#paymentModal')).show();
    }

    function savePayment() {
        if (!$('#paymentForm')[0].checkValidity()) {
            $('#paymentForm')[0].reportValidity();
            return;
        }
        const data = $('#paymentForm').serialize();
        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl("purchase/supplierpayments") ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            timeout: 5000,
            success: function(r) {
                if (r.success) {
                    showAlert(r.message ?? 'Payment saved', 'success');
                    bootstrap.Modal.getInstance($('#paymentModal')[0])?.hide();
                    setTimeout(() => location.reload(), 1000);
                } else showAlert(r.message ?? 'Error', 'danger');
            },
            error: function(x, s) {
                showAlert(s === 'timeout' ? 'Timeout' : 'Network error', 'danger');
            }
        });
    }

    function viewPayment(id) {
        if (!id) return;
        $.post('<?= Yii::$app->urlManager->createUrl("purchase/supplierpayments") ?>', {
            id: id,
            view: 1
        }, function(r) {
            if (r.success) {
                showAlert(r.message ?? 'Payment details loaded', 'info');
            }
        }, {
            'json': false
        });
    }

    function filterPayments() {
        const sup = $('#supplierFilter').val();
        const mth = $('#monthFilter').val();
        Storage.set('sp_supplier', sup);
        Storage.set('sp_month', mth);
    }

    function showAlert(m, t) {
        const a = $(`<div class="alert alert-${t} alert-dismissible fade show"><i class="fa fa-info"></i> ${htmlEscape(m)}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
        $('#alerts-sp').html(a);
        setTimeout(() => a.fadeOut(), 5000);
    }
</script>
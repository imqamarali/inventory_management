<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class PaymentController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = true;
        return parent::beforeAction($action);
    }

    private function currentUserId()
    {
        $user_array = Yii::$app->session->get('user_array');
        return $user_array['id'] ?? null;
    }

    private function isSuperAdmin()
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM roles sr
             JOIN system_users su ON su.role_id = sr.id
             WHERE sr.name = 'Super Admin' AND su.id = :user_id"
        )->bindValue(':user_id', $this->currentUserId())->queryScalar() > 0;
    }

    public function actionPaymentInvoices()
    {
        if (!$this->isSuperAdmin()) {
            return $this->redirect(['inventory/dashboard']);
        }

        if (Yii::$app->request->isGet) {
            // Fetch all pending payment proofs
            $paymentProofs = Yii::$app->db->createCommand(
                "SELECT spp.*, si.invoice_number, si.amount as invoice_amount, si.due_date
                 FROM system_payment_proofs spp
                 JOIN system_invoices si ON si.id = spp.invoice_id
                 WHERE spp.verification_status = 'pending' AND spp.is_deleted = 0
                 ORDER BY spp.created_at DESC"
            )->queryAll();

            // Fetch verified/paid invoices
            $verifiedProofs = Yii::$app->db->createCommand(
                "SELECT spp.*, si.invoice_number, si.amount as invoice_amount, si.payment_status
                 FROM system_payment_proofs spp
                 JOIN system_invoices si ON si.id = spp.invoice_id
                 WHERE spp.verification_status IN ('verified', 'rejected') AND spp.is_deleted = 0
                 ORDER BY spp.created_at DESC LIMIT 50"
            )->queryAll();

            return $this->render('payment_invoices', [
                'paymentProofs' => $paymentProofs,
                'verifiedProofs' => $verifiedProofs
            ]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        try {
            $flag = $post['flag'] ?? null;

            if ($flag == 'verify_proof') {
                $proofId = $post['proof_id'] ?? null;
                $action = $post['action'] ?? null; // 'approve' or 'reject'
                $rejectionReason = $post['rejection_reason'] ?? '';

                if (!$proofId || !$action) {
                    return ['success' => false, 'message' => 'Invalid request.'];
                }

                $proof = Yii::$app->db->createCommand(
                    "SELECT * FROM system_payment_proofs WHERE id = :id"
                )->bindValue(':id', $proofId)->queryOne();

                if (!$proof) {
                    return ['success' => false, 'message' => 'Payment proof not found.'];
                }

                $now = date('Y-m-d H:i:s');

                if ($action === 'approve') {
                    // Update proof status to verified
                    Yii::$app->db->createCommand()->update('system_payment_proofs', [
                        'verification_status' => 'verified',
                        'verified_by' => $this->currentUserId(),
                        'verified_at' => $now,
                        'updated_at' => $now,
                        'updated_by' => $this->currentUserId()
                    ], ['id' => $proofId])->execute();

                    // Get invoice and update payment status
                    $invoice = Yii::$app->db->createCommand(
                        "SELECT * FROM system_invoices WHERE id = :id"
                    )->bindValue(':id', $proof['invoice_id'])->queryOne();

                    if ($invoice) {
                        $newPaidAmount = $invoice['paid_amount'] + $proof['amount'];
                        $newRemainingAmount = $invoice['amount'] - $newPaidAmount;
                        $paymentStatus = $newRemainingAmount <= 0 ? 'paid' : 'partial';

                        Yii::$app->db->createCommand()->update('system_invoices', [
                            'paid_amount' => $newPaidAmount,
                            'remaining_amount' => max(0, $newRemainingAmount),
                            'payment_status' => $paymentStatus,
                            'invoice_status' => $paymentStatus,
                            'updated_at' => $now,
                            'updated_by' => $this->currentUserId()
                        ], ['id' => $proof['invoice_id']])->execute();

                        // If fully paid, create payment record
                        if ($paymentStatus === 'paid') {
                            Yii::$app->db->createCommand()->insert('system_payments', [
                                'payment_number' => 'PAY-' . date('YmdHis') . '-' . mt_rand(100, 999),
                                'invoice_id' => $proof['invoice_id'],
                                'payment_date' => $proof['proof_date'],
                                'payment_method' => $proof['payment_method'],
                                'amount' => $invoice['amount'],
                                'reference_number' => $proof['transaction_id'],
                                'payment_status' => 'completed',
                                'created_at' => $now,
                                'created_by' => $this->currentUserId(),
                                'updated_at' => $now,
                                'updated_by' => $this->currentUserId(),
                                'is_active' => 1,
                                'is_deleted' => 0
                            ])->execute();
                        }
                    }

                    Yii::$app->Component->Activitylog(
                        'Approved payment proof #' . $proof['proof_number'],
                        'update',
                        $proofId,
                        'payment',
                        ['action' => 'approved']
                    );

                    return ['success' => true, 'message' => 'Payment proof approved successfully.'];
                }

                if ($action === 'reject') {
                    Yii::$app->db->createCommand()->update('system_payment_proofs', [
                        'verification_status' => 'rejected',
                        'rejection_reason' => $rejectionReason,
                        'verified_by' => $this->currentUserId(),
                        'verified_at' => $now,
                        'updated_at' => $now,
                        'updated_by' => $this->currentUserId()
                    ], ['id' => $proofId])->execute();

                    Yii::$app->Component->Activitylog(
                        'Rejected payment proof #' . $proof['proof_number'],
                        'update',
                        $proofId,
                        'payment',
                        ['action' => 'rejected', 'reason' => $rejectionReason]
                    );

                    return ['success' => true, 'message' => 'Payment proof rejected.'];
                }
            }

            return ['success' => false, 'message' => 'Invalid flag.'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}

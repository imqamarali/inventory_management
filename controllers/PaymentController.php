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
                 ORDER BY spp.created_at DESC LIMIT 1000"
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

    public function actionPaymentHistory()
    {
        $user_id = $this->currentUserId();
        $role_id = Yii::$app->session->get('user_array')['role_id'] ?? null;
        $isSuperAdmin = $this->isSuperAdmin();

        if (Yii::$app->request->isPost) {
            $flag = Yii::$app->request->post('flag');
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($flag === 'load_dashboard') {
                return $this->loadDashboardData();
            } elseif ($flag === 'load') {
                return $this->loadPaymentHistory();
            } elseif ($flag === 'upload_proof') {
                return $this->uploadPaymentProof();
            } elseif ($flag === 'get_current_invoice') {
                return $this->getCurrentInvoice();
            } elseif ($flag === 'get_invoice_by_id') {
                return $this->getInvoiceById();
            } elseif ($flag === 'approve_payment') {
                return $this->approvePayment();
            } elseif ($flag === 'reject_payment') {
                return $this->rejectPayment();
            } elseif ($flag === 'get_invoice_documents') {
                return $this->getInvoiceDocuments();
            }
        }

        // Get stats for display
        $stats = $this->getPaymentStats();

        return $this->render('payment_history', [
            'stats' => $stats,
            'isSuperAdmin' => $isSuperAdmin,
            'role_id' => $role_id,
            'user_id' => $user_id,
            'paymentApiUrl' => Yii::$app->urlManager->createUrl('payment/payment-history'),
            'printInvoiceUrl' => Yii::$app->urlManager->createUrl('payment/print-invoice'),
            'csrfToken' => Yii::$app->request->getCsrfToken(),
            'csrfParam' => Yii::$app->request->csrfParam
        ]);
    }

    private function loadDashboardData()
    {
        $db = Yii::$app->db;

        // Get basic stats
        $stats = $db->createCommand(
            "SELECT
                COUNT(DISTINCT si.id) as total_months,
                COALESCE(SUM(CASE WHEN si.payment_status = 'paid' THEN si.amount ELSE 0 END), 0) as paid_amount,
                COALESCE(SUM(CASE WHEN si.payment_status IN ('unpaid', 'partial') THEN si.remaining_amount ELSE 0 END), 0) as remaining_amount,
                COUNT(CASE WHEN si.payment_status = 'unpaid' THEN 1 END) as unpaid_count,
                COUNT(CASE WHEN si.payment_status = 'paid' THEN 1 END) as paid_count
             FROM system_invoices si
             WHERE si.is_deleted = 0"
        )->queryOne();

        $nextDue = $db->createCommand(
            "SELECT MIN(due_date) as next_due_date
             FROM system_invoices
             WHERE payment_status IN ('unpaid', 'partial')
             AND is_deleted = 0"
        )->queryOne();

        $stats['next_due_date'] = $nextDue['next_due_date'] ?? null;

        // Latest invoices with document count
        $latestInvoices = $db->createCommand(
            "SELECT si.id, si.invoice_number, sc.contract_name, si.invoice_date, si.payment_status, si.amount, si.due_date, si.remaining_amount,
                    COUNT(spp.id) as document_count
             FROM system_invoices si
             JOIN system_contracts sc ON sc.id = si.contract_id
             LEFT JOIN system_payment_proofs spp ON spp.invoice_id = si.id AND spp.is_deleted = 0
             WHERE si.is_deleted = 0
             GROUP BY si.id
             ORDER BY si.created_at DESC
             LIMIT 1000"
        )->queryAll();

        // Pending payments
        $pendingPayments = $db->createCommand(
            "SELECT si.id, si.invoice_number, sc.contract_name, si.due_date, si.remaining_amount
             FROM system_invoices si
             JOIN system_contracts sc ON sc.id = si.contract_id
             WHERE si.payment_status IN ('unpaid', 'partial')
             AND si.is_deleted = 0
             ORDER BY si.due_date ASC
             LIMIT 10"
        )->queryAll();

        return [
            'success' => true,
            'stats' => $stats,
            'latestInvoices' => $latestInvoices,
            'pendingPayments' => $pendingPayments
        ];
    }

    private function getPaymentStats()
    {
        $db = Yii::$app->db;

        $stats = $db->createCommand(
            "SELECT
                COUNT(DISTINCT si.id) as total_invoices,
                COUNT(CASE WHEN si.payment_status = 'paid' THEN 1 END) as paid_count,
                COALESCE(SUM(CASE WHEN si.payment_status = 'paid' THEN si.amount ELSE 0 END), 0) as paid_amount,
                COALESCE(SUM(CASE WHEN si.payment_status IN ('unpaid', 'partial') THEN si.remaining_amount ELSE 0 END), 0) as remaining_amount
             FROM system_invoices si
             WHERE si.is_deleted = 0"
        )->queryOne();

        // Get next payment due
        $nextDue = $db->createCommand(
            "SELECT MIN(due_date) as next_due_date
             FROM system_invoices
             WHERE payment_status IN ('unpaid', 'partial')
             AND is_deleted = 0"
        )->queryOne();

        return [
            'total_months' => (int)($stats['total_invoices'] ?? 0),
            'paid_amount' => (float)($stats['paid_amount'] ?? 0),
            'remaining_amount' => (float)($stats['remaining_amount'] ?? 0),
            'next_due_date' => $nextDue['next_due_date'] ?? null
        ];
    }

    private function loadPaymentHistory()
    {
        $db = Yii::$app->db;
        $page = (int)Yii::$app->request->post('page', 1);
        $perPage = (int)Yii::$app->request->post('per_page', 1000);
        $offset = ($page - 1) * $perPage;

        $query = "SELECT si.*, sc.contract_name
                  FROM system_invoices si
                  JOIN system_contracts sc ON sc.id = si.contract_id
                  WHERE si.is_deleted = 0
                  ORDER BY si.created_at DESC
                  LIMIT " . $perPage . " OFFSET " . $offset;

        $invoices = $db->createCommand($query)->queryAll();

        $total = $db->createCommand("SELECT COUNT(*) FROM system_invoices WHERE is_deleted = 0")->queryScalar();
        $totalPages = ceil($total / $perPage);

        return [
            'success' => true,
            'invoices' => $invoices,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'isSuperAdmin' => $this->isSuperAdmin()
        ];
    }

    private function uploadPaymentProof()
    {
        $invoiceId = Yii::$app->request->post('invoice_id');
        $comments = Yii::$app->request->post('comments', '');
        $userId = $this->currentUserId();

        if (!$invoiceId || empty($_FILES['documents']['name'])) {
            return ['success' => false, 'message' => 'Invoice and documents required'];
        }

        try {
            $uploadDir = Yii::getAlias('@webroot/uploads/payment_proofs/');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileCount = is_array($_FILES['documents']['name']) ? count($_FILES['documents']['name']) : 1;
            $proofUploads = [];

            for ($i = 0; $i < $fileCount; $i++) {
                $fileName = is_array($_FILES['documents']['name']) ? $_FILES['documents']['name'][$i] : $_FILES['documents']['name'];
                $fileTmp = is_array($_FILES['documents']['tmp_name']) ? $_FILES['documents']['tmp_name'][$i] : $_FILES['documents']['tmp_name'];
                $fileSize = is_array($_FILES['documents']['size']) ? $_FILES['documents']['size'][$i] : $_FILES['documents']['size'];

                if ($fileSize > 5 * 1024 * 1024) {
                    throw new \Exception('File size exceeds 5MB limit');
                }

                $uniqueFileName = time() . '_' . uniqid() . '_' . basename($fileName);
                $uploadPath = $uploadDir . $uniqueFileName;

                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    $proofNumber = 'PROOF-' . date('YmdHis') . '-' . random_int(1000, 9999);

                    Yii::$app->db->createCommand(
                        "INSERT INTO system_payment_proofs
                        (invoice_id, proof_number, proof_date, document_file, document_name, verification_status, comments, created_by, created_at)
                        VALUES (:invoice_id, :proof_number, NOW(), :document_file, :document_name, 'pending', :comments, :created_by, NOW())"
                    )
                        ->bindValue(':invoice_id', $invoiceId)
                        ->bindValue(':proof_number', $proofNumber)
                        ->bindValue(':document_file', 'uploads/payment_proofs/' . $uniqueFileName)
                        ->bindValue(':document_name', $fileName)
                        ->bindValue(':comments', $comments)
                        ->bindValue(':created_by', $userId)
                        ->execute();

                    $proofUploads[] = ['file_name' => $fileName, 'proof_number' => $proofNumber];
                }
            }

            // Update invoice status to pending_approval
            $now = date('Y-m-d H:i:s');
            Yii::$app->db->createCommand(
                "UPDATE system_invoices SET payment_status = 'pending_approval', updated_at = :updated_at, updated_by = :updated_by WHERE id = :id"
            )->bindValue(':id', $invoiceId)
             ->bindValue(':updated_at', $now)
             ->bindValue(':updated_by', $userId)
             ->execute();

            // Log activity
            Yii::$app->Component->Activitylog(
                'Submitted payment for invoice #' . $invoiceId,
                'create',
                $invoiceId,
                'payment',
                ['action' => 'payment_submitted', 'comments' => $comments]
            );

            return [
                'success' => true,
                'message' => 'Payment proof uploaded. Awaiting verification.',
                'proofs' => $proofUploads
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function getCurrentInvoice()
    {
        $db = Yii::$app->db;

        // Get current month invoice
        $invoice = $db->createCommand(
            "SELECT si.*, sc.contract_name
             FROM system_invoices si
             JOIN system_contracts sc ON sc.id = si.contract_id
             WHERE si.is_deleted = 0
             AND MONTH(si.invoice_date) = MONTH(NOW())
             AND YEAR(si.invoice_date) = YEAR(NOW())
             LIMIT 1"
        )->queryOne();

        if ($invoice) {
            return [
                'success' => true,
                'invoice' => $invoice
            ];
        }

        return [
            'success' => false,
            'message' => 'No invoice found for current month.'
        ];
    }

    private function getInvoiceById()
    {
        $invoiceId = Yii::$app->request->post('invoice_id');
        $db = Yii::$app->db;

        if (!$invoiceId) {
            return [
                'success' => false,
                'message' => 'Invoice ID is required.'
            ];
        }

        // Get invoice by ID
        $invoice = $db->createCommand(
            "SELECT si.*, sc.contract_name
             FROM system_invoices si
             JOIN system_contracts sc ON sc.id = si.contract_id
             WHERE si.id = :id AND si.is_deleted = 0
             LIMIT 1"
        )->bindValue(':id', $invoiceId)->queryOne();

        if ($invoice) {
            return [
                'success' => true,
                'invoice' => $invoice
            ];
        }

        return [
            'success' => false,
            'message' => 'Invoice not found.'
        ];
    }

    public function actionPrintInvoice()
    {
        $invoiceId = Yii::$app->request->get('id');

        if (!$invoiceId) {
            return $this->redirect(['inventory/dashboard']);
        }

        $invoice = Yii::$app->db->createCommand(
            "SELECT si.*, sc.contract_name, sc.contract_description, sc.contract_policy
             FROM system_invoices si
             JOIN system_contracts sc ON sc.id = si.contract_id
             WHERE si.id = :id AND si.is_deleted = 0"
        )->bindValue(':id', $invoiceId)->queryOne();

        if (!$invoice) {
            return $this->redirect(['inventory/dashboard']);
        }

        return $this->render('invoice_print', ['invoice' => $invoice]);
    }

    private function approvePayment()
    {
        $invoiceId = Yii::$app->request->post('invoice_id');
        $comments = Yii::$app->request->post('comments', '');
        $userId = $this->currentUserId();

        if (!$invoiceId) {
            return ['success' => false, 'message' => 'Invoice ID is required.'];
        }

        try {
            $now = date('Y-m-d H:i:s');

            // Update invoice payment_status to 'paid'
            Yii::$app->db->createCommand(
                "UPDATE system_invoices SET payment_status = 'paid', updated_at = :updated_at, updated_by = :updated_by WHERE id = :id"
            )->bindValue(':id', $invoiceId)
             ->bindValue(':updated_at', $now)
             ->bindValue(':updated_by', $userId)
             ->execute();

            // Update the last payment proof verification status to 'verified'
            Yii::$app->db->createCommand(
                "UPDATE system_payment_proofs SET verification_status = 'verified', verified_by = :verified_by, verified_at = :verified_at, updated_at = :updated_at, updated_by = :updated_by WHERE invoice_id = :invoice_id ORDER BY created_at DESC LIMIT 1"
            )->bindValue(':invoice_id', $invoiceId)
             ->bindValue(':verified_by', $userId)
             ->bindValue(':verified_at', $now)
             ->bindValue(':updated_at', $now)
             ->bindValue(':updated_by', $userId)
             ->execute();

            // Log activity
            Yii::$app->Component->Activitylog(
                'Approved payment for invoice #' . $invoiceId,
                'update',
                $invoiceId,
                'payment',
                ['action' => 'payment_approved', 'admin_comments' => $comments]
            );

            return [
                'success' => true,
                'message' => 'Payment approved successfully.'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function rejectPayment()
    {
        $invoiceId = Yii::$app->request->post('invoice_id');
        $comments = Yii::$app->request->post('comments', '');
        $userId = $this->currentUserId();

        if (!$invoiceId) {
            return ['success' => false, 'message' => 'Invoice ID is required.'];
        }

        try {
            $now = date('Y-m-d H:i:s');

            // Revert invoice payment_status back to 'unpaid'
            Yii::$app->db->createCommand(
                "UPDATE system_invoices SET payment_status = 'unpaid', updated_at = :updated_at, updated_by = :updated_by WHERE id = :id"
            )->bindValue(':id', $invoiceId)
             ->bindValue(':updated_at', $now)
             ->bindValue(':updated_by', $userId)
             ->execute();

            // Update the last payment proof verification status to 'rejected' with reason
            Yii::$app->db->createCommand(
                "UPDATE system_payment_proofs SET verification_status = 'rejected', rejection_reason = :rejection_reason, verified_by = :verified_by, verified_at = :verified_at, updated_at = :updated_at, updated_by = :updated_by WHERE invoice_id = :invoice_id ORDER BY created_at DESC LIMIT 1"
            )->bindValue(':invoice_id', $invoiceId)
             ->bindValue(':rejection_reason', $comments)
             ->bindValue(':verified_by', $userId)
             ->bindValue(':verified_at', $now)
             ->bindValue(':updated_at', $now)
             ->bindValue(':updated_by', $userId)
             ->execute();

            // Log activity
            Yii::$app->Component->Activitylog(
                'Rejected payment for invoice #' . $invoiceId,
                'update',
                $invoiceId,
                'payment',
                ['action' => 'payment_rejected', 'rejection_reason' => $comments]
            );

            return [
                'success' => true,
                'message' => 'Payment rejected. User can resubmit with new proof.'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function getInvoiceDocuments()
    {
        $invoiceId = Yii::$app->request->post('invoice_id');

        if (!$invoiceId) {
            return ['success' => false, 'message' => 'Invoice ID is required.'];
        }

        try {
            // Get invoice number
            $invoice = Yii::$app->db->createCommand(
                "SELECT invoice_number FROM system_invoices WHERE id = :id AND is_deleted = 0"
            )->bindValue(':id', $invoiceId)->queryOne();

            if (!$invoice) {
                return ['success' => false, 'message' => 'Invoice not found.'];
            }

            // Get all payment proof documents for this invoice
            $documents = Yii::$app->db->createCommand(
                "SELECT id, proof_number, document_file, document_name, document_type, verification_status, created_at
                 FROM system_payment_proofs
                 WHERE invoice_id = :invoice_id AND is_deleted = 0
                 ORDER BY created_at DESC"
            )->bindValue(':invoice_id', $invoiceId)->queryAll();

            if (!$documents) {
                return ['success' => false, 'message' => 'No documents found.'];
            }

            return [
                'success' => true,
                'invoiceNumber' => $invoice['invoice_number'],
                'documents' => $documents
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}

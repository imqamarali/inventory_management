<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class PaymentHistoryController extends Controller
{
    public function beforeAction($action)
    {
        // Check if user is logged in
        if (Yii::$app->user->isGuest) {
            $this->redirect(['site/login']);
            return false;
        }

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $user_array = Yii::$app->session->get('user_array');
        $user_id = $user_array['id'] ?? null;
        $role_id = $user_array['role_id'] ?? null;

        // Get all unpaid invoices for this user (non-Super Admin only)
        $invoices = [];
        if ($role_id) {
            $isSuperAdmin = Yii::$app->db->createCommand(
                "SELECT COUNT(*) FROM roles sr
                 JOIN system_users su ON su.role_id = sr.id
                 WHERE sr.name = 'Super Admin' AND su.id = :user_id"
            )->bindValue(':user_id', $user_id)->queryScalar() > 0;

            if (!$isSuperAdmin) {
                $invoices = Yii::$app->db->createCommand(
                    "SELECT si.*, sc.contract_name, sc.monthly_charges
                     FROM system_invoices si
                     JOIN system_contracts sc ON sc.id = si.contract_id
                     WHERE si.payment_status IN ('unpaid', 'partial')
                     AND si.is_deleted = 0
                     ORDER BY si.created_at DESC"
                )->queryAll();
            } else {
                $invoices = Yii::$app->db->createCommand(
                    "SELECT si.*, sc.contract_name, sc.monthly_charges,
                            su.username, su.first_name, su.last_name
                     FROM system_invoices si
                     JOIN system_contracts sc ON sc.id = si.contract_id
                     LEFT JOIN system_users su ON su.id = si.created_by
                     WHERE si.is_deleted = 0
                     ORDER BY si.created_at DESC"
                )->queryAll();
            }
        }

        return $this->render('index', compact('invoices', 'role_id', 'user_id'));
    }

    public function actionGetInvoiceDetails()
    {
        if (Yii::$app->request->isPost) {
            $invoice_id = Yii::$app->request->post('invoice_id');

            if (!$invoice_id) {
                return json_encode(['success' => false, 'message' => 'Invoice ID required']);
            }

            $invoice = Yii::$app->db->createCommand(
                "SELECT si.*, sc.contract_name, sc.monthly_charges
                 FROM system_invoices si
                 JOIN system_contracts sc ON sc.id = si.contract_id
                 WHERE si.id = :id AND si.is_deleted = 0"
            )->bindValue(':id', $invoice_id)->queryOne();

            $proofs = Yii::$app->db->createCommand(
                "SELECT * FROM system_payment_proofs
                 WHERE invoice_id = :invoice_id AND is_deleted = 0
                 ORDER BY created_at DESC"
            )->bindValue(':invoice_id', $invoice_id)->queryAll();

            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'invoice' => $invoice,
                'proofs' => $proofs
            ];
        }

        return json_encode(['success' => false]);
    }

    public function actionUploadProof()
    {
        if (Yii::$app->request->isPost) {
            $invoice_id = Yii::$app->request->post('invoice_id');
            $user_array = Yii::$app->session->get('user_array');
            $user_id = $user_array['id'] ?? null;

            $uploadedFiles = $_FILES['documents'] ?? [];
            $comments = Yii::$app->request->post('comments', '');

            if (!$invoice_id || empty($uploadedFiles['name'])) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Invoice ID and documents required'];
            }

            try {
                $proofUploads = [];
                $uploadDir = Yii::getAlias('@webroot/uploads/payment_proofs/');
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Handle multiple file uploads
                $fileCount = is_array($uploadedFiles['name']) ? count($uploadedFiles['name']) : 1;
                for ($i = 0; $i < $fileCount; $i++) {
                    $fileName = is_array($uploadedFiles['name']) ? $uploadedFiles['name'][$i] : $uploadedFiles['name'];
                    $fileTmp = is_array($uploadedFiles['tmp_name']) ? $uploadedFiles['tmp_name'][$i] : $uploadedFiles['tmp_name'];
                    $fileType = is_array($uploadedFiles['type']) ? $uploadedFiles['type'][$i] : $uploadedFiles['type'];
                    $fileSize = is_array($uploadedFiles['size']) ? $uploadedFiles['size'][$i] : $uploadedFiles['size'];

                    // Validate file
                    $maxSize = 5 * 1024 * 1024; // 5MB
                    if ($fileSize > $maxSize) {
                        throw new \Exception('File size exceeds 5MB limit');
                    }

                    // Generate unique filename
                    $uniqueFileName = time() . '_' . uniqid() . '_' . basename($fileName);
                    $uploadPath = $uploadDir . $uniqueFileName;

                    if (move_uploaded_file($fileTmp, $uploadPath)) {
                        $proofNumber = 'PROOF-' . date('YmdHis') . '-' . random_int(1000, 9999);

                        Yii::$app->db->createCommand(
                            "INSERT INTO system_payment_proofs
                            (invoice_id, proof_number, proof_date, document_file, document_name, document_type,
                             description, verification_status, created_by, created_at)
                            VALUES
                            (:invoice_id, :proof_number, NOW(), :document_file, :document_name, :document_type,
                             :description, 'pending', :created_by, NOW())"
                        )
                            ->bindValue(':invoice_id', $invoice_id)
                            ->bindValue(':proof_number', $proofNumber)
                            ->bindValue(':document_file', 'uploads/payment_proofs/' . $uniqueFileName)
                            ->bindValue(':document_name', $fileName)
                            ->bindValue(':document_type', $fileType)
                            ->bindValue(':description', $comments)
                            ->bindValue(':created_by', $user_id)
                            ->execute();

                        $proofUploads[] = [
                            'id' => Yii::$app->db->getLastInsertID(),
                            'file_name' => $fileName,
                            'proof_number' => $proofNumber
                        ];
                    }
                }

                // Update invoice status to pending verification
                Yii::$app->db->createCommand(
                    "UPDATE system_invoices SET payment_status = 'partial' WHERE id = :id"
                )->bindValue(':id', $invoice_id)->execute();

                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Payment proof uploaded successfully. Awaiting verification.',
                    'proofs' => $proofUploads
                ];
            } catch (\Exception $e) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => $e->getMessage()];
            }
        }

        return json_encode(['success' => false]);
    }

    public function actionVerifyProof()
    {
        if (Yii::$app->request->isPost) {
            $user_array = Yii::$app->session->get('user_array');
            $user_id = $user_array['id'] ?? null;

            // Check if user is Super Admin
            $isSuperAdmin = Yii::$app->db->createCommand(
                "SELECT COUNT(*) FROM roles sr
                 JOIN system_users su ON su.role_id = sr.id
                 WHERE sr.name = 'Super Admin' AND su.id = :user_id"
            )->bindValue(':user_id', $user_id)->queryScalar() > 0;

            if (!$isSuperAdmin) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Unauthorized'];
            }

            $proof_id = Yii::$app->request->post('proof_id');
            $status = Yii::$app->request->post('status'); // approved, rejected
            $comment = Yii::$app->request->post('comment', '');

            try {
                $proof = Yii::$app->db->createCommand(
                    "SELECT * FROM system_payment_proofs WHERE id = :id"
                )->bindValue(':id', $proof_id)->queryOne();

                if (!$proof) {
                    throw new \Exception('Proof not found');
                }

                if ($status === 'approved') {
                    Yii::$app->db->createCommand(
                        "UPDATE system_payment_proofs
                         SET verification_status = 'verified', verified_by = :user_id, verified_at = NOW()
                         WHERE id = :id"
                    )
                        ->bindValue(':user_id', $user_id)
                        ->bindValue(':id', $proof_id)
                        ->execute();

                    // Check if all proofs are verified, then mark invoice as paid
                    $unverifiedCount = Yii::$app->db->createCommand(
                        "SELECT COUNT(*) FROM system_payment_proofs
                         WHERE invoice_id = :invoice_id AND verification_status != 'verified' AND is_deleted = 0"
                    )->bindValue(':invoice_id', $proof['invoice_id'])->queryScalar();

                    if ($unverifiedCount == 0) {
                        Yii::$app->db->createCommand(
                            "UPDATE system_invoices SET payment_status = 'paid' WHERE id = :id"
                        )->bindValue(':id', $proof['invoice_id'])->execute();
                    }
                } else {
                    Yii::$app->db->createCommand(
                        "UPDATE system_payment_proofs
                         SET verification_status = 'rejected', verified_by = :user_id, verified_at = NOW(), rejection_reason = :reason
                         WHERE id = :id"
                    )
                        ->bindValue(':user_id', $user_id)
                        ->bindValue(':reason', $comment)
                        ->bindValue(':id', $proof_id)
                        ->execute();
                }

                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => true, 'message' => 'Proof ' . $status . 'ed successfully'];
            } catch (\Exception $e) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => $e->getMessage()];
            }
        }

        return json_encode(['success' => false]);
    }
}

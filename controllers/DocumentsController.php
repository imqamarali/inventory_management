<?php

namespace app\controllers;

use Yii;
use TCPDF;
use yii\filters\AccessControl;
use yii\web\Controller;

class DocumentsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (Yii::$app->session->has('user_array') == NULL) {
            $this->redirect(['site/index']);
            return false;
        }
        return parent::beforeAction($action);
    }

    private function getSetting($key, $default = null)
    {
        $value = Yii::$app->db->createCommand("SELECT setting_value FROM inventory_settings WHERE setting_key=:key AND is_deleted=0")
            ->bindValue(':key', $key)->queryScalar();
        return $value !== false ? $value : $default;
    }

    private function getCompanyInfo()
    {
        return [
            'company_name' => $this->getSetting('company_name', 'Your Company Name'),
            'company_address' => $this->getSetting('company_address', 'Company Address'),
            'company_phone' => $this->getSetting('company_phone', 'Phone'),
            'company_email' => $this->getSetting('company_email', 'Email'),
        ];
    }

    private function addPaymentStatusWatermark(&$pdf, $status)
    {
        // Determine watermark text and color based on payment status
        $watermarkText = strtoupper($status ?? 'PENDING');

        // Set color and opacity based on payment status
        switch (strtolower($status ?? '')) {
            case 'paid':
                // Green for Paid
                $pdf->SetTextColor(34, 139, 34);
                $alpha = 0.30;
                break;
            case 'partially paid':
                // Orange for Partially Paid
                $pdf->SetTextColor(255, 140, 0);
                $alpha = 0.30;
                break;
            case 'unpaid':
                // Red for Unpaid
                $pdf->SetTextColor(220, 20, 60);
                $alpha = 0.30;
                break;
            case 'draft':
                // Gray for Draft
                $pdf->SetTextColor(128, 128, 128);
                $alpha = 0.25;
                break;
            default:
                // Default gray
                $pdf->SetTextColor(100, 100, 100);
                $alpha = 0.25;
        }

        // Save current settings
        $pdf->SetAlpha($alpha);
        $pdf->SetFont('times', 'B', 65);

        // Get page dimensions
        $pageWidth = $pdf->getPageWidth();
        $pageHeight = $pdf->getPageHeight();

        // Exact center position
        $x = $pageWidth / 2;
        $y = $pageHeight / 2;

        // Rotate text 45 degrees (text direction from top to right)
        $pdf->StartTransform();
        $pdf->Rotate(45, $x, $y);
        $pdf->SetXY($x - 30, $y - 30);
        $pdf->Cell(80, 40, $watermarkText, 0, 0, 'C', false);
        $pdf->StopTransform();

        // Restore opacity and text color
        $pdf->SetAlpha(1);
        $pdf->SetTextColor(0, 0, 0);
    }

    public function actionPurchaseorder()
    {
        try {
            $poId = (int)Yii::$app->request->get('id');

            if ($poId <= 0) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Invalid Purchase Order ID');
            }

            $db = Yii::$app->db;

            $po = $db->createCommand("
                SELECT po.*, s.company_name, s.email, s.phone, s.address,
                       w.warehouse_name
                FROM inventory_purchase_orders po
                LEFT JOIN inventory_suppliers s ON s.id = po.supplier_id
                LEFT JOIN inventory_warehouses w ON w.id = po.warehouse_id
                WHERE po.id = :id AND po.is_deleted = 0
                LIMIT 1
            ", [':id' => $poId])->queryOne();

            if (!$po) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Purchase Order not found');
            }

            $items = $db->createCommand("
                SELECT poi.*, p.product_name, p.sku, u.unit_name
                FROM inventory_purchase_order_items poi
                LEFT JOIN inventory_products p ON p.id = poi.product_id
                LEFT JOIN inventory_units u ON u.id = p.unit_id
                WHERE poi.purchase_order_id = :id AND poi.is_deleted = 0
                ORDER BY poi.id
            ", [':id' => $poId])->queryAll();

            // Get linked purchase invoice and payment history
            $invoice = null;
            $paymentHistory = [];
            $invoice = $db->createCommand("
                SELECT * FROM inventory_purchase_invoices
                WHERE purchase_order_id = :id AND is_deleted = 0
                LIMIT 1
            ", [':id' => $poId])->queryOne();

            if ($invoice) {
                $paymentHistory = $db->createCommand("
                    SELECT * FROM inventory_purchase_invoice_payments
                    WHERE purchase_invoice_id = :id
                    ORDER BY payment_date DESC
                ", [':id' => $invoice['id']])->queryAll();
            }

            $this->generatePurchaseOrderPDF($po, $items, $invoice, $paymentHistory);
        } catch (\Exception $e) {
            Yii::$app->response->format = 'html';
            return $this->renderContent('Error: ' . $e->getMessage());
        }
    }

    public function actionSalesorder()
    {
        try {
            $soId = (int)Yii::$app->request->get('id');

            if ($soId <= 0) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Invalid Sales Order ID');
            }

            $db = Yii::$app->db;

            $so = $db->createCommand("
                SELECT so.*, c.company_name, c.first_name, c.last_name, c.email, c.phone, c.address,
                       w.warehouse_name
                FROM inventory_sales_orders so
                LEFT JOIN inventory_customers c ON c.id = so.customer_id
                LEFT JOIN inventory_warehouses w ON w.id = so.warehouse_id
                WHERE so.id = :id AND so.is_deleted = 0
                LIMIT 1
            ", [':id' => $soId])->queryOne();

            if (!$so) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Sales Order not found');
            }

            $items = $db->createCommand("
                SELECT
                    soi.id,
                    soi.product_id,
                    soi.quantity,
                    soi.unit_price,
                    COALESCE(soi.discount, 0) as discount_amount,
                    COALESCE(soi.tax, 0) as tax_amount,
                    soi.total as total_amount,
                    p.product_name,
                    p.sku,
                    u.unit_name
                FROM inventory_sales_order_items soi
                LEFT JOIN inventory_products p ON p.id = soi.product_id
                LEFT JOIN inventory_units u ON u.id = p.unit_id
                WHERE soi.sales_order_id = :id AND soi.is_deleted = 0
                ORDER BY soi.id
            ", [':id' => $soId])->queryAll();

            // Get linked sales invoice and payment history
            $invoice = null;
            $paymentHistory = [];
            $invoice = $db->createCommand("
                SELECT * FROM inventory_sales_invoices
                WHERE sales_order_id = :id AND is_deleted = 0
                LIMIT 1
            ", [':id' => $soId])->queryOne();

            if ($invoice) {
                $paymentHistory = $db->createCommand("
                    SELECT * FROM inventory_sale_invoice_payments
                    WHERE sale_invoice_id = :id
                    ORDER BY payment_date DESC
                ", [':id' => $invoice['id']])->queryAll();
            }

            $this->generateSalesOrderPDF($so, $items, $invoice, $paymentHistory);
        } catch (\Exception $e) {
            Yii::$app->response->format = 'html';
            return $this->renderContent('Error: ' . $e->getMessage());
        }
    }

    public function actionGoodsreceiving()
    {
        try {
            $grId = (int)Yii::$app->request->get('id');

            if ($grId <= 0) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Invalid Goods Receiving ID');
            }

            $db = Yii::$app->db;

            $gr = $db->createCommand("
                SELECT gr.*, s.company_name, s.email, s.phone, s.address,
                       po.po_number, w.warehouse_name
                FROM inventory_goods_receiving gr
                LEFT JOIN inventory_suppliers s ON s.id = gr.supplier_id
                LEFT JOIN inventory_purchase_orders po ON po.id = gr.purchase_order_id
                LEFT JOIN inventory_warehouses w ON w.id = gr.warehouse_id
                WHERE gr.id = :id AND gr.is_deleted = 0
                LIMIT 1
            ", [':id' => $grId])->queryOne();

            if (!$gr) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Goods Receiving not found');
            }

            // Get all PO items for this goods receiving
            $poId = $gr['purchase_order_id'];
            $poItems = [];
            if ($poId) {
                $poItems = $db->createCommand("
                    SELECT poi.*, p.product_name, p.sku, u.unit_name
                    FROM inventory_purchase_order_items poi
                    LEFT JOIN inventory_products p ON p.id = poi.product_id
                    LEFT JOIN inventory_units u ON u.id = p.unit_id
                    WHERE poi.purchase_order_id = :id AND poi.is_deleted = 0
                    ORDER BY poi.id
                ", [':id' => $poId])->queryAll();
            }

            // Get received items for this GRN
            $grItems = $db->createCommand("
                SELECT gri.*, p.product_name, p.sku, u.unit_name
                FROM inventory_goods_receiving_items gri
                LEFT JOIN inventory_products p ON p.id = gri.product_id
                LEFT JOIN inventory_units u ON u.id = gri.unit_id
                WHERE gri.goods_receiving_id = :id
                ORDER BY gri.id
            ", [':id' => $grId])->queryAll();

            $this->generateGoodsReceivingPDF($gr, $poItems, $grItems);
        } catch (\Exception $e) {
            Yii::$app->response->format = 'html';
            return $this->renderContent('Error: ' . $e->getMessage());
        }
    }

    public function actionPurchaseinvoice()
    {
        try {
            $invId = (int)Yii::$app->request->get('id');

            if ($invId <= 0) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Invalid Invoice ID');
            }

            $db = Yii::$app->db;

            $invoice = $db->createCommand("
                SELECT pi.*, s.company_name, s.email, s.phone, s.address,
                       po.po_number, po.status AS po_status, w.warehouse_name
                FROM inventory_purchase_invoices pi
                LEFT JOIN inventory_suppliers s ON s.id = pi.supplier_id
                LEFT JOIN inventory_purchase_orders po ON po.id = pi.purchase_order_id
                LEFT JOIN inventory_warehouses w ON w.id = po.warehouse_id
                WHERE pi.id = :id AND pi.is_deleted = 0
                LIMIT 1
            ", [':id' => $invId])->queryOne();

            if (!$invoice) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Invoice not found');
            }

            // Get all PO items for this invoice
            $poId = $invoice['purchase_order_id'];
            $poItems = [];
            if ($poId) {
                $poItems = $db->createCommand("
                    SELECT poi.*, p.product_name, p.sku, u.unit_name
                    FROM inventory_purchase_order_items poi
                    LEFT JOIN inventory_products p ON p.id = poi.product_id
                    LEFT JOIN inventory_units u ON u.id = p.unit_id
                    WHERE poi.purchase_order_id = :id AND poi.is_deleted = 0
                    ORDER BY poi.id
                ", [':id' => $poId])->queryAll();
            }

            // Get invoice items
            $invoiceItems = $db->createCommand("
                SELECT pii.*, p.product_name, p.sku, u.unit_name
                FROM inventory_purchase_invoice_items pii
                LEFT JOIN inventory_products p ON p.id = pii.product_id
                LEFT JOIN inventory_units u ON u.id = pii.unit_id
                WHERE pii.purchase_invoice_id = :id
                ORDER BY pii.id
            ", [':id' => $invId])->queryAll();

            // Get payment history
            $paymentHistory = $db->createCommand("
                SELECT *
                FROM inventory_purchase_invoice_payments
                WHERE purchase_invoice_id = :id
                ORDER BY payment_date, created_at
            ", [':id' => $invId])->queryAll();

            $this->generatePurchaseInvoicePDF($invoice, $poItems, $invoiceItems, $paymentHistory);
        } catch (\Exception $e) {
            Yii::$app->response->format = 'html';
            return $this->renderContent('Error: ' . $e->getMessage());
        }
    }

    public function renderContent($content)
    {
        return '<html><body style="font-family: Arial; padding: 20px;">' . htmlspecialchars($content) . '</body></html>';
    }

    private function generatePurchaseOrderPDF($po, $items, $invoice = null, $paymentHistory = [])
    {
        $company = $this->getCompanyInfo();
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // Add diagonal payment status watermark if invoice exists
        if ($invoice && isset($invoice['status'])) {
            $this->addPaymentStatusWatermark($pdf, $invoice['status']);
        }

        // Header
        $pdf->SetFont('times', 'B', 32);
        $pdf->SetXY(15, 15);
        $pdf->Cell(15, 15, 'Purchase Order Receipt', 0, 0, 'L');

        // Company info on right
        $pdf->SetFont('times', '', 9);
        $pdf->SetXY(135, 15);
        $pdf->MultiCell(60, 4,
            $company['company_name'] . "\n" .
            $company['company_address'] . "\n" .
            $company['company_phone'] . "\n" .
            $company['company_email'],
            0, 'L'
        );

        $pdf->Ln(8);

        // Receipt Number and Date
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Receipt Number #', 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(30, 6, $po['po_number'], 0, 0);
        $pdf->SetX(135);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(30, 6, 'Receipt Date', 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(30, 6, date('m/d/Y', strtotime($po['order_date'])), 0, 1);

        $pdf->Ln(3);

        // Supplier Details Header
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'Supplier Details', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 10);

        $pdf->Cell(50, 6, 'Name', 1, 0, 'L');
        $pdf->Cell(0, 6, $po['company_name'] ?? 'N/A', 1, 1, 'L');

        $pdf->Cell(50, 6, 'Address', 1, 0, 'L');
        $pdf->Cell(0, 6, $po['address'] ?? 'N/A', 1, 1, 'L');

        $pdf->Cell(50, 6, 'Email', 1, 0, 'L');
        $pdf->Cell(50, 6, $po['email'] ?? 'N/A', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Phone', 1, 0, 'C');
        $pdf->Cell(0, 6, $po['phone'] ?? 'N/A', 1, 1, 'L');

        $pdf->Ln(3);

        // Items Header
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'Product or Service', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(80, 6, 'Description', 1, 0, 'L');
        $pdf->Cell(30, 6, 'Cost', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Quantity', 1, 0, 'C');
        $pdf->Cell(0, 6, 'Amount', 1, 1, 'R');

        $pdf->SetFont('times', '', 9);

        // Items
        foreach ($items as $item) {
            $pdf->Cell(80, 6, substr($item['product_name'] ?? 'N/A', 0, 30), 1, 0, 'L');
            $pdf->Cell(30, 6, number_format($item['unit_price'], 2), 1, 0, 'R');
            $pdf->Cell(30, 6, number_format($item['quantity'], 2), 1, 0, 'R');
            $pdf->Cell(0, 6, number_format($item['line_total'], 2), 1, 1, 'R');
        }

        // Empty rows
        for ($i = 0; $i < 6; $i++) {
            $pdf->Cell(80, 6, '', 1, 0, 'L');
            $pdf->Cell(30, 6, '', 1, 0, 'R');
            $pdf->Cell(30, 6, '', 1, 0, 'R');
            $pdf->Cell(0, 6, '', 1, 1, 'R');
        }

        $pdf->Ln(2);

        // Totals on right side
        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Sub Total', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 6, number_format($po['subtotal'] ?? 0, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Discount', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 6, number_format($po['discount'] ?? 0, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Shipping', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 6, number_format($po['freight'] ?? 0, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(50, 6, 'Total Due', 0, 0, 'R');
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(0, 6, number_format($po['grand_total'] ?? 0, 2), 0, 1, 'R');

        // Display paid amount and balance if invoice exists
        if ($invoice) {
            $paidAmount = (float)($invoice['paid_amount'] ?? 0);
            $grandTotal = (float)($invoice['grand_total'] ?? 0);
            $balanceAmount = $grandTotal - $paidAmount;

            $pdf->SetX(120);
            $pdf->SetFont('times', 'B', 10);
            $pdf->Cell(50, 6, 'Paid Amount', 0, 0, 'R');
            $pdf->SetFont('times', '', 10);
            $pdf->Cell(0, 6, number_format($paidAmount, 2), 0, 1, 'R');

            // Highlight remaining balance if > 0
            if ($balanceAmount > 0) {
                $pdf->SetX(120);
                $pdf->SetFont('times', 'B', 11);
                $pdf->SetFillColor(255, 200, 200);
                $pdf->Cell(50, 6, 'Remaining Balance', 0, 0, 'R', true);
                $pdf->SetFont('times', 'B', 11);
                $pdf->Cell(0, 6, number_format($balanceAmount, 2), 0, 1, 'R', true);
                $pdf->SetFillColor(255, 255, 255);
            } else {
                $pdf->SetX(120);
                $pdf->SetFont('times', 'B', 11);
                $pdf->SetFillColor(200, 255, 200);
                $pdf->Cell(50, 6, 'Balance', 0, 0, 'R', true);
                $pdf->SetFont('times', 'B', 11);
                $pdf->Cell(0, 6, 'PAID', 0, 1, 'R', true);
                $pdf->SetFillColor(255, 255, 255);
            }
        }

        // Payment History section (Purchase Invoice)
        if (!empty($paymentHistory)) {
            $pdf->Ln(5);

            // Check if we need a new page for payment history
            $currentY = $pdf->GetY();
            $pageHeight = $pdf->getPageHeight();
            $estimatedHeight = 40 + (count($paymentHistory) * 6); // Estimate height needed

            if ($currentY + $estimatedHeight > $pageHeight - 20) {
                $pdf->AddPage();
            }

            $pdf->SetFont('times', 'B', 10);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 6, 'Payment History', 1, 1, 'L', true);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('times', 'B', 8);
            $pdf->Cell(30, 6, 'Payment Date', 1, 0, 'C');
            $pdf->Cell(30, 6, 'Amount Paid', 1, 0, 'R');
            $pdf->Cell(30, 6, 'Cumulative', 1, 0, 'R');
            $pdf->Cell(30, 6, 'Remaining', 1, 0, 'R');
            $pdf->Cell(0, 6, 'Remarks', 1, 1, 'L');

            $pdf->SetFont('times', '', 7);

            // Sort payments by date (oldest first) for cumulative calculation
            $sortedPayments = $paymentHistory;
            usort($sortedPayments, function($a, $b) {
                return strtotime($a['payment_date']) - strtotime($b['payment_date']);
            });

            $cumulativePaid = 0;
            $grandTotal = (float)($invoice['grand_total'] ?? 0);

            foreach ($sortedPayments as $payment) {
                $paymentAmount = (float)($payment['paid_amount'] ?? 0);
                $cumulativePaid += $paymentAmount;
                $remainingBalance = $grandTotal - $cumulativePaid;

                $pdf->Cell(30, 6, date('m/d/Y', strtotime($payment['payment_date'] ?? 'now')), 1, 0, 'C');
                $pdf->Cell(30, 6, number_format($paymentAmount, 2), 1, 0, 'R');
                $pdf->Cell(30, 6, number_format($cumulativePaid, 2), 1, 0, 'R');
                $pdf->Cell(30, 6, number_format(max(0, $remainingBalance), 2), 1, 0, 'R');
                $pdf->Cell(0, 6, substr($payment['remarks'] ?? '', 0, 30), 1, 1, 'L');
            }

            // Summary row
            $pdf->SetFont('times', 'B', 8);
            $pdf->SetFillColor(220, 220, 220);
            $paidAmount = (float)($invoice['paid_amount'] ?? 0);
            $balanceAmount = $grandTotal - $paidAmount;
            $pdf->Cell(30, 6, 'Total', 1, 0, 'C', true);
            $pdf->Cell(30, 6, number_format($paidAmount, 2), 1, 0, 'R', true);
            $pdf->Cell(30, 6, number_format($paidAmount, 2), 1, 0, 'R', true);
            $pdf->Cell(30, 6, number_format(max(0, $balanceAmount), 2), 1, 0, 'R', true);
            $pdf->Cell(0, 6, count($paymentHistory) . ' payment(s)', 1, 1, 'L', true);
            $pdf->SetFillColor(255, 255, 255);

            $pdf->Ln(2);
        }

        // Policy box
        $pdf->Ln(5);
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(0, 5, 'Policy:', 0, 1, 'L');
        $pdf->SetFont('times', '', 8);
        $pdf->SetXY(15, $pdf->GetY());
        $pdf->MultiCell(0, 4, "Accepted Payments: Cash (PKR), Cards, JazzCash, Easypaisa & Bank Transfer.", 1, 'L');

        $pdf->Ln(8);

        // Thank you message
        $pdf->SetFont('times', 'B', 12);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'Thank you for your purchase!', 1, 1, 'C', true);

        $pdf->Output('PO-Receipt-' . $po['po_number'] . '.pdf', 'I');
    }

    private function generateSalesOrderPDF($so, $items, $invoice = null, $paymentHistory = [])
    {
        $company = $this->getCompanyInfo();
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // Add diagonal payment status watermark if invoice exists
        if ($invoice && isset($invoice['status'])) {
            $this->addPaymentStatusWatermark($pdf, $invoice['status']);
        }

        // Header
        $pdf->SetFont('times', 'B', 32);
        $pdf->SetXY(15, 15);
        $pdf->Cell(15, 15, 'Sales Order Receipt', 0, 0, 'L');

        // Company info on right
        $pdf->SetFont('times', '', 9);
        $pdf->SetXY(135, 15);
        $pdf->MultiCell(60, 4,
            $company['company_name'] . "\n" .
            $company['company_address'] . "\n" .
            $company['company_phone'] . "\n" .
            $company['company_email'],
            0, 'L'
        );

        $pdf->Ln(8);

        // Receipt Number and Date
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Receipt Number #', 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(30, 6, $so['order_number'] ?? 'N/A', 0, 0);
        $pdf->SetX(135);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(30, 6, 'Receipt Date', 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(30, 6, date('m/d/Y', strtotime($so['order_date'] ?? 'now')), 0, 1);

        $pdf->Ln(3);

        // Customer Details Header
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'Customer Details', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 10);

        $pdf->Cell(50, 6, 'Name', 1, 0, 'L');
        $customerName = $so['company_name'] ?: trim(($so['first_name'] ?? '') . ' ' . ($so['last_name'] ?? ''));
        $pdf->Cell(0, 6, $customerName ?? 'N/A', 1, 1, 'L');

        $pdf->Cell(50, 6, 'Address', 1, 0, 'L');
        $pdf->Cell(0, 6, $so['address'] ?? 'N/A', 1, 1, 'L');

        $pdf->Cell(50, 6, 'Email', 1, 0, 'L');
        $pdf->Cell(50, 6, $so['email'] ?? 'N/A', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Phone', 1, 0, 'C');
        $pdf->Cell(0, 6, $so['phone'] ?? 'N/A', 1, 1, 'L');

        $pdf->Ln(3);

        // Items Header
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'Line Items', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', 'B', 8);
        $pdf->Cell(50, 6, 'Description', 1, 0, 'L');
        $pdf->Cell(18, 6, 'Qty', 1, 0, 'C');
        $pdf->Cell(18, 6, 'Unit Price', 1, 0, 'R');
        $pdf->Cell(18, 6, 'Discount', 1, 0, 'R');
        $pdf->Cell(15, 6, 'Tax', 1, 0, 'R');
        $pdf->Cell(0, 6, 'Amount', 1, 1, 'R');

        $pdf->SetFont('times', '', 8);

        // Items
        foreach ($items as $item) {
            $pdf->Cell(50, 6, substr($item['product_name'] ?? 'N/A', 0, 25), 1, 0, 'L');
            $pdf->Cell(18, 6, number_format($item['quantity'] ?? 0, 2), 1, 0, 'C');
            $pdf->Cell(18, 6, number_format($item['unit_price'] ?? 0, 2), 1, 0, 'R');
            $pdf->Cell(18, 6, number_format($item['discount_amount'] ?? 0, 2), 1, 0, 'R');
            $pdf->Cell(15, 6, number_format($item['tax_amount'] ?? 0, 2), 1, 0, 'R');
            $pdf->Cell(0, 6, number_format($item['total_amount'] ?? 0, 2), 1, 1, 'R');
        }

        // Empty rows
        for ($i = 0; $i < 6; $i++) {
            $pdf->Cell(50, 6, '', 1, 0, 'L');
            $pdf->Cell(18, 6, '', 1, 0, 'C');
            $pdf->Cell(18, 6, '', 1, 0, 'R');
            $pdf->Cell(18, 6, '', 1, 0, 'R');
            $pdf->Cell(15, 6, '', 1, 0, 'R');
            $pdf->Cell(0, 6, '', 1, 1, 'R');
        }

        $pdf->Ln(2);

        // Totals on right side
        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Sub Total', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 6, number_format($so['subtotal'] ?? 0, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Discount', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 6, number_format($so['discount'] ?? 0, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Tax', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 6, number_format($so['tax'] ?? 0, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Shipping', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 6, number_format($so['shipping'] ?? 0, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(50, 6, 'Total Due', 0, 0, 'R');
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(0, 6, number_format($so['grand_total'] ?? 0, 2), 0, 1, 'R');

        $pdf->Ln(5);

        // Payment History Section (if invoice exists and has payments)
        if ($invoice && !empty($paymentHistory)) {
            $paidAmount = (float)($invoice['paid_amount'] ?? 0);
            $grandTotal = (float)($invoice['grand_total'] ?? 0);
            $balanceAmount = $grandTotal - $paidAmount;

            $pdf->SetFont('times', 'B', 10);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 6, 'Payment History', 1, 1, 'L', true);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('times', 'B', 8);
            $pdf->Cell(35, 6, 'Payment Date', 1, 0, 'C');
            $pdf->Cell(30, 6, 'Amount Paid', 1, 0, 'R');
            $pdf->Cell(30, 6, 'Cumulative', 1, 0, 'R');
            $pdf->Cell(30, 6, 'Remaining', 1, 0, 'R');
            $pdf->Cell(0, 6, 'Remarks', 1, 1, 'L');

            $pdf->SetFont('times', '', 7);

            // Sort payments by date (oldest first) for cumulative calculation
            $sortedPayments = $paymentHistory;
            usort($sortedPayments, function($a, $b) {
                return strtotime($a['payment_date']) - strtotime($b['payment_date']);
            });

            $cumulativePaid = 0;
            foreach ($sortedPayments as $payment) {
                $paymentAmount = (float)($payment['paid_amount'] ?? 0);
                $cumulativePaid += $paymentAmount;
                $remainingBalance = $grandTotal - $cumulativePaid;

                $pdf->Cell(35, 6, date('m/d/Y', strtotime($payment['payment_date'] ?? 'now')), 1, 0, 'C');
                $pdf->Cell(30, 6, number_format($paymentAmount, 2), 1, 0, 'R');
                $pdf->Cell(30, 6, number_format($cumulativePaid, 2), 1, 0, 'R');
                $pdf->Cell(30, 6, number_format(max(0, $remainingBalance), 2), 1, 0, 'R');
                $pdf->Cell(0, 6, substr($payment['remarks'] ?? '', 0, 35), 1, 1, 'L');
            }

            // Summary row
            $pdf->SetFont('times', 'B', 8);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(35, 6, 'Total', 1, 0, 'C', true);
            $pdf->Cell(30, 6, number_format($paidAmount, 2), 1, 0, 'R', true);
            $pdf->Cell(30, 6, number_format($paidAmount, 2), 1, 0, 'R', true);
            $pdf->Cell(30, 6, number_format(max(0, $balanceAmount), 2), 1, 0, 'R', true);
            $pdf->Cell(0, 6, count($paymentHistory) . ' payment(s)', 1, 1, 'L', true);
            $pdf->SetFillColor(255, 255, 255);

            $pdf->Ln(2);
        }

        // Policy box
        $pdf->Ln(5);
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(0, 5, 'Policy:', 0, 1, 'L');
        $pdf->SetFont('times', '', 8);
        $pdf->SetXY(15, $pdf->GetY());
        $pdf->MultiCell(0, 4, "Accepted Payments: Cash (PKR), Cards, JazzCash, Easypaisa & Bank Transfer.", 1, 'L');

        $pdf->Ln(8);

        // Thank you message
        $pdf->SetFont('times', 'B', 12);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'Thank you for your purchase!', 1, 1, 'C', true);

        $pdf->Output('SO-Receipt-' . ($so['order_number'] ?? 'UNKNOWN') . '.pdf', 'I');
    }

    private function generateGoodsReceivingPDF($gr, $poItems, $grItems)
    {
        $company = $this->getCompanyInfo();
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // Header
        $pdf->SetFont('times', 'B', 32);
        $pdf->Cell(100, 15, 'Receipt', 0, 0, 'L');

        // Company info on right
        $pdf->SetFont('times', '', 9);
        $pdf->SetXY(135, 15);
        $pdf->MultiCell(60, 4,
            $company['company_name'] . "\n" .
            $company['company_address'] . "\n" .
            $company['company_phone'] . "\n" .
            $company['company_email'],
            0, 'L'
        );

        $pdf->Ln(8);

        // Receipt Number and Date
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Receipt Number #', 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(30, 6, $gr['grn_number'] ?? 'N/A', 0, 0);
        $pdf->SetX(135);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(30, 6, 'Receipt Date', 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(30, 6, date('m/d/Y', strtotime($gr['receiving_date'] ?? 'now')), 0, 1);

        $pdf->Ln(1);

        // PO Number and Warehouse
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(50, 5, 'PO Number:', 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(40, 5, $gr['po_number'] ?? 'N/A', 0, 0);
        $pdf->SetX(135);
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(30, 5, 'Warehouse:', 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 5, $gr['warehouse_name'] ?? 'N/A', 0, 1);

        $pdf->Ln(2);

        // Supplier Details Header
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'Supplier Details', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 10);

        $pdf->Cell(50, 6, 'Name', 1, 0, 'L');
        $pdf->Cell(0, 6, $gr['company_name'] ?? 'N/A', 1, 1, 'L');

        $pdf->Cell(50, 6, 'Address', 1, 0, 'L');
        $pdf->Cell(0, 6, $gr['address'] ?? 'N/A', 1, 1, 'L');

        $pdf->Cell(50, 6, 'Email', 1, 0, 'L');
        $pdf->Cell(50, 6, $gr['email'] ?? 'N/A', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Phone', 1, 0, 'C');
        $pdf->Cell(0, 6, $gr['phone'] ?? 'N/A', 1, 1, 'L');

        $pdf->Ln(3);

        // Items Header - Display all PO items
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'Product or Service', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', 'B', 8);
        $pdf->Cell(60, 6, 'Description', 1, 0, 'L');
        $pdf->Cell(18, 6, 'PO Qty', 1, 0, 'C');
        $pdf->Cell(18, 6, 'Received', 1, 0, 'C');
        $pdf->Cell(18, 6, 'Accepted', 1, 0, 'C');
        $pdf->Cell(18, 6, 'Rejected', 1, 0, 'C');
        $pdf->Cell(0, 6, 'Unit', 1, 1, 'C');

        $pdf->SetFont('times', '', 8);

        // Display all PO items with received quantities
        foreach ($poItems as $poItem) {
            $received = 0;
            $accepted = 0;
            $rejected = 0;

            // Find matching GR item to get received quantities
            foreach ($grItems as $grItem) {
                if ($grItem['product_id'] == $poItem['product_id']) {
                    $received = $grItem['received_quantity'] ?? 0;
                    $accepted = $grItem['accepted_quantity'] ?? 0;
                    $rejected = $grItem['rejected_quantity'] ?? 0;
                    break;
                }
            }

            $pdf->Cell(60, 6, substr($poItem['product_name'] ?? 'N/A', 0, 30), 1, 0, 'L');
            $pdf->Cell(18, 6, number_format($poItem['quantity'] ?? 0, 2), 1, 0, 'C');
            $pdf->Cell(18, 6, number_format($received, 2), 1, 0, 'C');
            $pdf->Cell(18, 6, number_format($accepted, 2), 1, 0, 'C');
            $pdf->Cell(18, 6, number_format($rejected, 2), 1, 0, 'C');
            $pdf->Cell(0, 6, $poItem['unit_name'] ?? 'PCS', 1, 1, 'C');
        }

        // Empty rows for additional items
        for ($i = 0; $i < 6; $i++) {
            $pdf->Cell(60, 6, '', 1, 0, 'L');
            $pdf->Cell(18, 6, '', 1, 0, 'C');
            $pdf->Cell(18, 6, '', 1, 0, 'C');
            $pdf->Cell(18, 6, '', 1, 0, 'C');
            $pdf->Cell(18, 6, '', 1, 0, 'C');
            $pdf->Cell(0, 6, '', 1, 1, 'C');
        }

        $pdf->Ln(2);

        // Payment Method
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(0, 6, 'Payment Method: Credit Card ☐  Cash ☐  Other ☐', 1, 1, 'L');

        $pdf->Ln(2);

        // Status and additional info on right side
        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Status', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 6, $gr['status'] ?? 'N/A', 0, 1, 'R');

        $pdf->Ln(5);

        // Thank you message
        $pdf->SetFont('times', 'B', 12);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'Goods Received Successfully!', 1, 1, 'C', true);

        $pdf->Output('GRN-' . ($gr['grn_number'] ?? 'UNKNOWN') . '.pdf', 'I');
    }

    private function generatePurchaseInvoicePDF($invoice, $poItems, $invoiceItems, $paymentHistory = [])
    {
        $company = $this->getCompanyInfo();
        $paidAmount = $invoice['paid_amount'] ?? 0;
        $grandTotal = $invoice['grand_total'] ?? 0;
        $balanceAmount = $grandTotal - $paidAmount;

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // Add diagonal payment status watermark
        $this->addPaymentStatusWatermark($pdf, $invoice['status'] ?? 'PENDING');

        // Header - Receipt title on left
        $pdf->SetXY(15, 16);
        $pdf->SetFont('times', 'B', 32);
        $pdf->Cell(100, 12, 'Receipt', 0, 1, 'L');

        // Company info on right
        $pdf->SetFont('times', '', 9);
        $pdf->SetXY(135, 20);
        $pdf->MultiCell(60, 4,
            $company['company_name'] . "\n" .
            $company['company_address'] . "\n" .
            $company['company_phone'] . "\n" .
            $company['company_email'],
            0, 'L'
        );

        // PINV # on new line below Receipt title
        $pdf->SetFont('times', '', 10);
        $pdf->SetXY(15, 30);
        $pdf->Cell(100, 5, 'PINV # ' . ($invoice['invoice_no'] ?? 'N/A'), 0, 0, 'L');

        $pdf->Ln(8);

        // Receipt Number and Date
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Receipt Number #', 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(30, 6, $invoice['invoice_no'] ?? 'N/A', 0, 0);
        $pdf->SetX(135);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(30, 6, 'Receipt Date', 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(30, 6, date('m/d/Y', strtotime($invoice['invoice_date'] ?? 'now')), 0, 1);

        $pdf->Ln(1);

        // PO Number and Warehouse
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(50, 5, 'PO Number:', 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(40, 5, $invoice['po_number'] ?? 'N/A', 0, 0);
        $pdf->SetX(135);
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(30, 5, 'Status:', 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 5, $invoice['status'] ?? 'N/A', 0, 1);

        $pdf->Ln(1);

        // Due Date and Warehouse
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(50, 5, 'Due Date:', 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(40, 5, date('m/d/Y', strtotime($invoice['due_date'] ?? 'now')), 0, 0);
        $pdf->SetX(135);
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(30, 5, 'Warehouse:', 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 5, $invoice['warehouse_name'] ?? 'N/A', 0, 1);

        $pdf->Ln(1);

        // Supplier Details Header
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'Supplier Details', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 10);

        $pdf->Cell(50, 6, 'Name', 1, 0, 'L');
        $pdf->Cell(0, 6, $invoice['company_name'] ?? 'N/A', 1, 1, 'L');

        $pdf->Cell(50, 6, 'Address', 1, 0, 'L');
        $pdf->Cell(0, 6, $invoice['address'] ?? 'N/A', 1, 1, 'L');

        $pdf->Cell(50, 6, 'Email', 1, 0, 'L');
        $pdf->Cell(50, 6, $invoice['email'] ?? 'N/A', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Phone', 1, 0, 'C');
        $pdf->Cell(0, 6, $invoice['phone'] ?? 'N/A', 1, 1, 'L');

        $pdf->Ln(3);

        // Items Header - Display all PO items
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'All Purchase Order Items', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', 'B', 8);
        $pdf->Cell(50, 6, 'Description', 1, 0, 'L');
        $pdf->Cell(18, 6, 'PO Qty', 1, 0, 'C');
        $pdf->Cell(18, 6, 'Invoice Qty', 1, 0, 'C');
        $pdf->Cell(18, 6, 'Unit Price', 1, 0, 'R');
        $pdf->Cell(18, 6, 'Discount', 1, 0, 'R');
        $pdf->Cell(15, 6, 'Tax', 1, 0, 'R');
        $pdf->Cell(0, 6, 'Amount', 1, 1, 'R');

        $pdf->SetFont('times', '', 8);

        // Display all PO items with invoice quantities
        foreach ($poItems as $poItem) {
            $invoicedQty = 0;
            $unitPrice = $poItem['unit_price'] ?? 0;
            $discount = 0;
            $tax = 0;
            $amount = 0;

            // Find matching invoice item to get invoiced quantities and price
            foreach ($invoiceItems as $invItem) {
                if ($invItem['product_id'] == $poItem['product_id']) {
                    $invoicedQty = $invItem['quantity'] ?? 0;
                    $unitPrice = $invItem['unit_price'] ?? $poItem['unit_price'] ?? 0;
                    $discount = $invItem['discount_amount'] ?? 0;
                    $tax = $invItem['tax_amount'] ?? 0;
                    $amount = $invItem['total_amount'] ?? 0;
                    break;
                }
            }

            $pdf->Cell(50, 6, substr($poItem['product_name'] ?? 'N/A', 0, 25), 1, 0, 'L');
            $pdf->Cell(18, 6, number_format($poItem['quantity'] ?? 0, 2), 1, 0, 'C');
            $pdf->Cell(18, 6, number_format($invoicedQty, 2), 1, 0, 'C');
            $pdf->Cell(18, 6, number_format($unitPrice, 2), 1, 0, 'R');
            $pdf->Cell(18, 6, number_format($discount, 2), 1, 0, 'R');
            $pdf->Cell(15, 6, number_format($tax, 2), 1, 0, 'R');
            $pdf->Cell(0, 6, number_format($amount, 2), 1, 1, 'R');
        }

        // Empty rows for additional items
        for ($i = 0; $i < 6; $i++) {
            $pdf->Cell(50, 6, '', 1, 0, 'L');
            $pdf->Cell(18, 6, '', 1, 0, 'C');
            $pdf->Cell(18, 6, '', 1, 0, 'C');
            $pdf->Cell(18, 6, '', 1, 0, 'R');
            $pdf->Cell(18, 6, '', 1, 0, 'R');
            $pdf->Cell(15, 6, '', 1, 0, 'R');
            $pdf->Cell(0, 6, '', 1, 1, 'R');
        }

        $pdf->Ln(2);

        // Totals on right side
        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Sub Total', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 6, number_format($invoice['subtotal'] ?? 0, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Discount', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        // Support both old (discount_amount) and new (discount) field names
        $discount = $invoice['discount_amount'] ?? $invoice['discount'] ?? 0;
        $pdf->Cell(0, 6, number_format($discount, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Tax', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        // Support both old (tax_amount) and new (tax) field names
        $tax = $invoice['tax_amount'] ?? $invoice['tax'] ?? 0;
        $pdf->Cell(0, 6, number_format($tax, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(50, 6, 'Total Due', 0, 0, 'R');
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(0, 6, number_format($grandTotal, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Paid Amount', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 6, number_format($paidAmount, 2), 0, 1, 'R');

        // Highlight remaining balance if > 0
        if ($balanceAmount > 0) {
            $pdf->SetX(120);
            $pdf->SetFont('times', 'B', 11);
            $pdf->SetFillColor(255, 200, 200);
            $pdf->Cell(50, 6, 'Remaining Balance', 0, 0, 'R', true);
            $pdf->SetFont('times', 'B', 11);
            $pdf->Cell(0, 6, number_format($balanceAmount, 2), 0, 1, 'R', true);
            $pdf->SetFillColor(255, 255, 255);
        } else {
            $pdf->SetX(120);
            $pdf->SetFont('times', 'B', 11);
            $pdf->SetFillColor(200, 255, 200);
            $pdf->Cell(50, 6, 'Balance', 0, 0, 'R', true);
            $pdf->SetFont('times', 'B', 11);
            $pdf->Cell(0, 6, 'PAID', 0, 1, 'R', true);
            $pdf->SetFillColor(255, 255, 255);
        }

        $pdf->Ln(5);

        // Payment History Section
        if (!empty($paymentHistory)) {
            $pdf->SetFont('times', 'B', 10);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 6, 'Payment History', 1, 1, 'L', true);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('times', 'B', 9);
            $pdf->Cell(40, 6, 'Payment Date', 1, 0, 'C');
            $pdf->Cell(40, 6, 'Amount Paid', 1, 0, 'R');
            $pdf->Cell(0, 6, 'Remarks', 1, 1, 'L');

            $pdf->SetFont('times', '', 8);
            foreach ($paymentHistory as $payment) {
                $pdf->Cell(40, 6, date('m/d/Y', strtotime($payment['payment_date'] ?? 'now')), 1, 0, 'C');
                $pdf->Cell(40, 6, number_format($payment['paid_amount'] ?? 0, 2), 1, 0, 'R');
                $pdf->Cell(0, 6, substr($payment['remarks'] ?? '', 0, 50), 1, 1, 'L');
            }

            $pdf->Ln(2);
        }

        // Thank you message
        $pdf->SetFont('times', 'B', 12);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'Thank you for your business!', 1, 1, 'C', true);

        $pdf->Output('INV-' . ($invoice['invoice_no'] ?? 'UNKNOWN') . '.pdf', 'I');
    }

    public function actionSalesinvoice()
    {
        try {
            $invoiceId = (int)Yii::$app->request->get('id');

            if ($invoiceId <= 0) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Invalid Sales Invoice ID');
            }

            $db = Yii::$app->db;

            $invoice = $db->createCommand("
                SELECT si.*, c.company_name, c.first_name, c.last_name, c.email, c.phone, c.address,
                       so.order_number, w.warehouse_name
                FROM inventory_sales_invoices si
                LEFT JOIN inventory_customers c ON c.id = si.customer_id
                LEFT JOIN inventory_sales_orders so ON so.id = si.sales_order_id
                LEFT JOIN inventory_warehouses w ON w.id = si.warehouse_id
                WHERE si.id = :id AND si.is_deleted = 0
                LIMIT 1
            ", [':id' => $invoiceId])->queryOne();

            if (!$invoice) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Sales Invoice not found');
            }

            // Try to get items from invoice items first, then fall back to sales order items or POS items
            $items = $db->createCommand("
                SELECT sii.*, p.product_name, p.sku
                FROM inventory_sale_invoice_items sii
                LEFT JOIN inventory_products p ON p.id = sii.product_id
                WHERE sii.sales_invoice_id = :id AND sii.is_deleted = 0
                ORDER BY sii.id
            ", [':id' => $invoiceId])->queryAll();

            // If no invoice items found, try sales order items
            if (empty($items) && $invoice['sales_order_id']) {
                $items = $db->createCommand("
                    SELECT
                        soi.id,
                        soi.product_id,
                        soi.quantity,
                        soi.unit_price,
                        COALESCE(soi.discount, 0) as discount_amount,
                        COALESCE(soi.tax, 0) as tax_amount,
                        soi.total as total_amount,
                        p.product_name,
                        p.sku
                    FROM inventory_sales_order_items soi
                    LEFT JOIN inventory_products p ON p.id = soi.product_id
                    WHERE soi.sales_order_id = :sales_order_id AND soi.is_deleted = 0
                    ORDER BY soi.id
                ", [':sales_order_id' => $invoice['sales_order_id']])->queryAll();
            }

            // If still no items found, try to get from linked POS sale (for POS invoices)
            // POS invoices have the POS sale ID in the remarks field
            if (empty($items) && $invoice['remarks']) {
                // Extract POS ID from remarks: "Auto-generated from POS Sale ID: 123"
                if (preg_match('/POS Sale ID:\s*(\d+)/', $invoice['remarks'], $matches)) {
                    $posSaleId = (int)$matches[1];
                    $posSale = $db->createCommand("
                        SELECT items FROM inventory_pos_sales
                        WHERE id = :id AND is_deleted = 0
                    ", [':id' => $posSaleId])->queryOne();

                    if ($posSale && !empty($posSale['items'])) {
                        $posItems = json_decode($posSale['items'], true);
                        if (is_array($posItems)) {
                            // Normalize POS item fields to match database item format
                            foreach ($posItems as &$item) {
                                // POS items use: discount, tax, total
                                // Database items use: discount_amount, tax_amount, total_amount
                                $item['discount_amount'] = $item['discount'] ?? 0;
                                $item['tax_amount'] = $item['tax'] ?? 0;
                                $item['total_amount'] = $item['total'] ?? 0;
                            }
                            $items = $posItems;
                        }
                    }
                }
            }

            $paymentHistory = $db->createCommand("
                SELECT * FROM inventory_sale_invoice_payments
                WHERE sale_invoice_id = :id
                ORDER BY payment_date DESC
            ", [':id' => $invoiceId])->queryAll();

            $this->generateSalesInvoicePDF($invoice, $items, $paymentHistory);
        } catch (\Exception $e) {
            Yii::$app->response->format = 'html';
            return $this->renderContent('Error: ' . $e->getMessage());
        }
    }

    public function actionPosreceipt()
    {
        try {
            $posId = (int)Yii::$app->request->get('id');

            if ($posId <= 0) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Invalid POS Transaction ID');
            }

            $db = Yii::$app->db;

            // Try to get from inventory_pos_sales first
            $pos = $db->createCommand("
                SELECT ps.*, c.company_name, c.first_name, c.last_name, c.customer_type, c.phone,
                       w.warehouse_name
                FROM inventory_pos_sales ps
                LEFT JOIN inventory_customers c ON c.id = ps.customer_id
                LEFT JOIN inventory_warehouses w ON w.id = ps.warehouse_id
                WHERE ps.id = :id AND ps.is_deleted = 0
                LIMIT 1
            ", [':id' => $posId])->queryOne();

            if (!$pos) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('POS Transaction not found');
            }

            // Decode items from JSON
            $items = [];
            if (is_string($pos['items'])) {
                $decoded = json_decode($pos['items'], true);
                $items = is_array($decoded) ? $decoded : [];
            } else if (is_array($pos['items'])) {
                $items = $pos['items'];
            }

            // Get payment history if table exists
            $paymentHistory = [];
            try {
                $paymentHistory = $db->createCommand("
                    SELECT * FROM inventory_pos_payment_history
                    WHERE pos_sales_id = :id
                    ORDER BY payment_date DESC
                ", [':id' => $posId])->queryAll();
            } catch (\Exception $e) {
                // Table might not exist, skip payment history
            }

            $this->generatePOSReceiptPDF($pos, $items, $paymentHistory);
        } catch (\Exception $e) {
            Yii::$app->response->format = 'html';
            return $this->renderContent('Error: ' . $e->getMessage());
        }
    }

    private function generateSalesInvoicePDF($invoice, $items, $paymentHistory = [])
    {
        $company = $this->getCompanyInfo();
        $paidAmount = $invoice['paid_amount'] ?? 0;
        $grandTotal = $invoice['grand_total'] ?? 0;
        $balanceAmount = $grandTotal - $paidAmount;

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // Add diagonal payment status watermark
        $this->addPaymentStatusWatermark($pdf, $invoice['status'] ?? 'PENDING');

        // Header - Receipt title on left
        $pdf->SetXY(15, 16);
        $pdf->SetFont('times', 'B', 32);
        $pdf->Cell(100, 12, 'Sales Invoice', 0, 1, 'L');

        // Company info on right
        $pdf->SetFont('times', '', 9);
        $pdf->SetXY(135, 20);
        $pdf->MultiCell(60, 4,
            $company['company_name'] . "\n" .
            $company['company_address'] . "\n" .
            $company['company_phone'] . "\n" .
            $company['company_email'],
            0, 'L'
        );

        // SINV # on new line below title
        $pdf->SetFont('times', '', 10);
        $pdf->SetXY(15, 30);
        $pdf->Cell(100, 5, 'SINV # ' . ($invoice['invoice_no'] ?? 'N/A'), 0, 0, 'L');

        $pdf->Ln(8);

        // Receipt Number and Date
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Invoice Number #', 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(30, 6, $invoice['invoice_no'] ?? 'N/A', 0, 0);
        $pdf->SetX(135);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(30, 6, 'Invoice Date', 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(30, 6, date('m/d/Y', strtotime($invoice['invoice_date'] ?? 'now')), 0, 1);

        $pdf->Ln(1);

        // Sales Order Number and Status
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(50, 5, 'Sales Order:', 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(40, 5, $invoice['order_number'] ?? 'N/A', 0, 0);
        $pdf->SetX(135);
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(30, 5, 'Status:', 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 5, $invoice['status'] ?? 'N/A', 0, 1);

        $pdf->Ln(1);

        // Due Date
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(50, 5, 'Due Date:', 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(40, 5, date('m/d/Y', strtotime($invoice['due_date'] ?? 'now')), 0, 1);

        $pdf->Ln(1);

        // Customer Details Header
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'Customer Details', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 10);

        $customerName = $invoice['company_name'] ?: trim(($invoice['first_name'] ?? '') . ' ' . ($invoice['last_name'] ?? ''));
        $pdf->Cell(50, 6, 'Name', 1, 0, 'L');
        $pdf->Cell(0, 6, $customerName ?? 'N/A', 1, 1, 'L');

        $pdf->Cell(50, 6, 'Address', 1, 0, 'L');
        $pdf->Cell(0, 6, $invoice['address'] ?? 'N/A', 1, 1, 'L');

        $pdf->Cell(50, 6, 'Email', 1, 0, 'L');
        $pdf->Cell(50, 6, $invoice['email'] ?? 'N/A', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Phone', 1, 0, 'C');
        $pdf->Cell(0, 6, $invoice['phone'] ?? 'N/A', 1, 1, 'L');

        $pdf->Ln(3);

        // Items Header
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'Line Items', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', 'B', 8);
        $pdf->Cell(50, 6, 'Description', 1, 0, 'L');
        $pdf->Cell(18, 6, 'Qty', 1, 0, 'C');
        $pdf->Cell(18, 6, 'Unit Price', 1, 0, 'R');
        $pdf->Cell(18, 6, 'Discount', 1, 0, 'R');
        $pdf->Cell(15, 6, 'Tax', 1, 0, 'R');
        $pdf->Cell(0, 6, 'Amount', 1, 1, 'R');

        $pdf->SetFont('times', '', 8);

        // Display items
        foreach ($items as $item) {
            $pdf->Cell(50, 6, substr($item['product_name'] ?? 'N/A', 0, 25), 1, 0, 'L');
            $pdf->Cell(18, 6, number_format($item['quantity'] ?? 0, 2), 1, 0, 'C');
            $pdf->Cell(18, 6, number_format($item['unit_price'] ?? 0, 2), 1, 0, 'R');
            // Support both old (discount_amount) and new (discount) field names
            $discount = $item['discount_amount'] ?? $item['discount'] ?? 0;
            $tax = $item['tax_amount'] ?? $item['tax'] ?? 0;
            $total = $item['total_amount'] ?? $item['total'] ?? 0;
            $pdf->Cell(18, 6, number_format($discount, 2), 1, 0, 'R');
            $pdf->Cell(15, 6, number_format($tax, 2), 1, 0, 'R');
            $pdf->Cell(0, 6, number_format($total, 2), 1, 1, 'R');
        }

        // Empty rows for additional items
        for ($i = 0; $i < 6; $i++) {
            $pdf->Cell(50, 6, '', 1, 0, 'L');
            $pdf->Cell(18, 6, '', 1, 0, 'C');
            $pdf->Cell(18, 6, '', 1, 0, 'R');
            $pdf->Cell(18, 6, '', 1, 0, 'R');
            $pdf->Cell(15, 6, '', 1, 0, 'R');
            $pdf->Cell(0, 6, '', 1, 1, 'R');
        }

        $pdf->Ln(2);

        // Totals on right side
        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Sub Total', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 6, number_format($invoice['subtotal'] ?? 0, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Discount', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        // Support both old (discount_amount) and new (discount) field names
        $discount = $invoice['discount_amount'] ?? $invoice['discount'] ?? 0;
        $pdf->Cell(0, 6, number_format($discount, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Tax', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        // Support both old (tax_amount) and new (tax) field names
        $tax = $invoice['tax_amount'] ?? $invoice['tax'] ?? 0;
        $pdf->Cell(0, 6, number_format($tax, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(50, 6, 'Total Due', 0, 0, 'R');
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(0, 6, number_format($grandTotal, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Paid Amount', 0, 0, 'R');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 6, number_format($paidAmount, 2), 0, 1, 'R');

        // Highlight remaining balance if > 0
        if ($balanceAmount > 0) {
            $pdf->SetX(120);
            $pdf->SetFont('times', 'B', 11);
            $pdf->SetFillColor(255, 200, 200);
            $pdf->Cell(50, 6, 'Remaining Balance', 0, 0, 'R', true);
            $pdf->SetFont('times', 'B', 11);
            $pdf->Cell(0, 6, number_format($balanceAmount, 2), 0, 1, 'R', true);
            $pdf->SetFillColor(255, 255, 255);
        } else {
            $pdf->SetX(120);
            $pdf->SetFont('times', 'B', 11);
            $pdf->SetFillColor(200, 255, 200);
            $pdf->Cell(50, 6, 'Balance', 0, 0, 'R', true);
            $pdf->SetFont('times', 'B', 11);
            $pdf->Cell(0, 6, 'PAID', 0, 1, 'R', true);
            $pdf->SetFillColor(255, 255, 255);
        }

        $pdf->Ln(5);

        // Payment History Section
        if (!empty($paymentHistory)) {
            // Check if we need a new page for payment history (Sales Invoice)
            $currentY = $pdf->GetY();
            $pageHeight = $pdf->getPageHeight();
            $estimatedHeight = 50 + (count($paymentHistory) * 6); // Estimate height needed

            if ($currentY + $estimatedHeight > $pageHeight - 20) {
                $pdf->AddPage();
            }

            $pdf->SetFont('times', 'B', 10);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 6, 'Payment History', 1, 1, 'L', true);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('times', 'B', 8);
            $pdf->Cell(35, 6, 'Payment Date', 1, 0, 'C');
            $pdf->Cell(30, 6, 'Amount Paid', 1, 0, 'R');
            $pdf->Cell(30, 6, 'Cumulative', 1, 0, 'R');
            $pdf->Cell(30, 6, 'Remaining', 1, 0, 'R');
            $pdf->Cell(0, 6, 'Remarks', 1, 1, 'L');

            $pdf->SetFont('times', '', 7);

            // Sort payments by date (oldest first) for cumulative calculation
            $sortedPayments = $paymentHistory;
            usort($sortedPayments, function($a, $b) {
                return strtotime($a['payment_date']) - strtotime($b['payment_date']);
            });

            $cumulativePaid = 0;
            foreach ($sortedPayments as $payment) {
                $paymentAmount = (float)($payment['paid_amount'] ?? 0);
                $cumulativePaid += $paymentAmount;
                $remainingBalance = $grandTotal - $cumulativePaid;

                $pdf->Cell(35, 6, date('m/d/Y', strtotime($payment['payment_date'] ?? 'now')), 1, 0, 'C');
                $pdf->Cell(30, 6, number_format($paymentAmount, 2), 1, 0, 'R');
                $pdf->Cell(30, 6, number_format($cumulativePaid, 2), 1, 0, 'R');
                $pdf->Cell(30, 6, number_format(max(0, $remainingBalance), 2), 1, 0, 'R');
                $pdf->Cell(0, 6, substr($payment['remarks'] ?? '', 0, 35), 1, 1, 'L');
            }

            // Summary row
            $pdf->SetFont('times', 'B', 8);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(35, 6, 'Total', 1, 0, 'C', true);
            $pdf->Cell(30, 6, number_format($paidAmount, 2), 1, 0, 'R', true);
            $pdf->Cell(30, 6, number_format($paidAmount, 2), 1, 0, 'R', true);
            $pdf->Cell(30, 6, number_format(max(0, $balanceAmount), 2), 1, 0, 'R', true);
            $pdf->Cell(0, 6, count($paymentHistory) . ' payment(s)', 1, 1, 'L', true);
            $pdf->SetFillColor(255, 255, 255);

            $pdf->Ln(2);
        }

        // Policy box
        $pdf->Ln(5);
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(0, 5, 'Policy:', 0, 1, 'L');
        $pdf->SetFont('times', '', 8);
        $pdf->SetXY(15, $pdf->GetY());
        $pdf->MultiCell(0, 4, "Accepted Payments: Cash (PKR), Cards, JazzCash, Easypaisa & Bank Transfer.", 1, 'L');

        $pdf->Ln(8);

        // Thank you message
        $pdf->SetFont('times', 'B', 12);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'Thank you for your business!', 1, 1, 'C', true);

        $pdf->Output('SINV-' . ($invoice['invoice_no'] ?? 'UNKNOWN') . '.pdf', 'I');
    }

    private function generatePOSReceiptPDF($pos, $items, $paymentHistory = [])
    {
        $company = $this->getCompanyInfo();

        // Ensure items is an array
        if (is_string($items)) {
            $items = json_decode($items, true) ?? [];
        }
        if (!is_array($items)) {
            $items = [];
        }

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // Determine payment status for watermark
        $grandTotal = (float)($pos['grand_total'] ?? 0);
        $paidAmount = (float)($pos['paid_amount'] ?? 0);
        $status = ($paidAmount >= $grandTotal) ? 'Paid' : (($paidAmount > 0) ? 'Partially Paid' : 'Unpaid');

        // Add diagonal payment status watermark
        $this->addPaymentStatusWatermark($pdf, $status);

        // Header - Receipt title on left
        $pdf->SetXY(15, 16);
        $pdf->SetFont('times', 'B', 32);
        $pdf->Cell(100, 12, 'POS Receipt', 0, 1, 'L');

        // Company info on right
        $pdf->SetFont('times', '', 9);
        $pdf->SetXY(135, 20);
        $pdf->MultiCell(60, 4,
            $company['company_name'] . "\n" .
            $company['company_address'] . "\n" .
            $company['company_phone'] . "\n" .
            $company['company_email'],
            0, 'L'
        );

        // Receipt # on new line below title
        $pdf->SetFont('times', '', 10);
        $pdf->SetXY(15, 30);
        $pdf->Cell(100, 5, 'POS # ' . ($pos['pos_no'] ?? 'N/A'), 0, 0, 'L');

        $pdf->Ln(8);

        // Receipt Number and Date
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(50, 6, 'Receipt Number #', 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(30, 6, $pos['pos_no'] ?? 'N/A', 0, 0);
        $pdf->SetX(135);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(30, 6, 'Receipt Date', 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(30, 6, date('m/d/Y', strtotime($pos['sale_date'] ?? 'now')), 0, 1);

        $pdf->Ln(1);

        // Payment Method and Warehouse
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(50, 5, 'Payment Method:', 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(40, 5, $pos['payment_method'] ?? 'Cash', 0, 0);
        $pdf->SetX(135);
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(30, 5, 'Warehouse:', 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 5, $pos['warehouse_name'] ?? 'N/A', 0, 1);

        $pdf->Ln(1);

        // Customer Details Header
        $customerName = ($pos['customer_type'] == 'Walk-in')
            ? 'Walk-in Customer'
            : ($pos['company_name'] ?: trim(($pos['first_name'] ?? '') . ' ' . ($pos['last_name'] ?? '')));

        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'Customer Details', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 10);

        $pdf->Cell(50, 6, 'Name', 1, 0, 'L');
        $pdf->Cell(0, 6, $customerName ?? 'N/A', 1, 1, 'L');

        if ($pos['phone'] ?? false) {
            $pdf->Cell(50, 6, 'Phone', 1, 0, 'L');
            $pdf->Cell(0, 6, $pos['phone'] ?? 'N/A', 1, 1, 'L');
        }

        $pdf->Ln(3);

        // Items Header
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'Line Items', 1, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', 'B', 8);
        $pdf->Cell(50, 6, 'Description', 1, 0, 'L');
        $pdf->Cell(18, 6, 'Qty', 1, 0, 'C');
        $pdf->Cell(18, 6, 'Unit Price', 1, 0, 'R');
        $pdf->Cell(18, 6, 'Discount', 1, 0, 'R');
        $pdf->Cell(15, 6, 'Tax', 1, 0, 'R');
        $pdf->Cell(0, 6, 'Amount', 1, 1, 'R');

        $pdf->SetFont('times', '', 8);

        // Display items
        foreach ($items as $item) {
            $qty = (float)($item['quantity'] ?? 0);
            $unitPrice = (float)($item['unit_price'] ?? 0);
            $discount = (float)($item['discount'] ?? 0);
            $tax = (float)($item['tax'] ?? 0);
            $total = ($qty * $unitPrice) - $discount + $tax;

            $pdf->Cell(50, 6, substr($item['product_name'] ?? 'N/A', 0, 25), 1, 0, 'L');
            $pdf->Cell(18, 6, number_format($qty, 2), 1, 0, 'C');
            $pdf->Cell(18, 6, number_format($unitPrice, 2), 1, 0, 'R');
            $pdf->Cell(18, 6, number_format($discount, 2), 1, 0, 'R');
            $pdf->Cell(15, 6, number_format($tax, 2), 1, 0, 'R');
            $pdf->Cell(0, 6, number_format($total, 2), 1, 1, 'R');
        }

        // Empty rows for additional items
        for ($i = 0; $i < 5; $i++) {
            $pdf->Cell(50, 6, '', 1, 0, 'L');
            $pdf->Cell(18, 6, '', 1, 0, 'C');
            $pdf->Cell(18, 6, '', 1, 0, 'R');
            $pdf->Cell(18, 6, '', 1, 0, 'R');
            $pdf->Cell(15, 6, '', 1, 0, 'R');
            $pdf->Cell(0, 6, '', 1, 1, 'R');
        }

        $pdf->Ln(2);

        // Financial Summary
        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(50, 6, 'Sub Total', 0, 0, 'R', true);
        $pdf->SetFont('times', '', 11);
        $pdf->Cell(0, 6, number_format($pos['subtotal'] ?? 0, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(50, 6, 'Discount', 0, 0, 'R', true);
        $pdf->SetFont('times', '', 11);
        $pdf->Cell(0, 6, number_format($pos['discount_amount'] ?? 0, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(50, 6, 'Tax', 0, 0, 'R', true);
        $pdf->SetFont('times', '', 11);
        $pdf->Cell(0, 6, number_format($pos['tax_amount'] ?? 0, 2), 0, 1, 'R');

        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 11);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(50, 6, 'Grand Total', 0, 0, 'R', true);
        $pdf->Cell(0, 6, number_format($pos['grand_total'] ?? 0, 2), 0, 1, 'R', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetX(120);
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(50, 6, 'Paid Amount', 0, 0, 'R', true);
        $pdf->SetFont('times', '', 11);
        $pdf->Cell(0, 6, number_format($pos['paid_amount'] ?? 0, 2), 0, 1, 'R');

        $balanceAmount = ($pos['grand_total'] ?? 0) - ($pos['paid_amount'] ?? 0);
        if ($balanceAmount > 0) {
            $pdf->SetX(120);
            $pdf->SetFont('times', 'B', 11);
            $pdf->SetFillColor(255, 200, 200);
            $pdf->Cell(50, 6, 'Remaining Balance', 0, 0, 'R', true);
            $pdf->SetFont('times', 'B', 11);
            $pdf->Cell(0, 6, number_format($balanceAmount, 2), 0, 1, 'R', true);
            $pdf->SetFillColor(255, 255, 255);
        } else {
            $pdf->SetX(120);
            $pdf->SetFont('times', 'B', 11);
            $pdf->SetFillColor(200, 255, 200);
            $pdf->Cell(50, 6, 'Balance', 0, 0, 'R', true);
            $pdf->SetFont('times', 'B', 11);
            $pdf->Cell(0, 6, 'PAID', 0, 1, 'R', true);
            $pdf->SetFillColor(255, 255, 255);
        }

        $pdf->Ln(5);

        // Payment History Section
        if (!empty($paymentHistory)) {
            $pdf->SetFont('times', 'B', 10);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 6, 'Payment History', 1, 1, 'L', true);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('times', 'B', 8);
            $pdf->Cell(30, 6, 'Payment Date', 1, 0, 'C');
            $pdf->Cell(28, 6, 'Amount Paid', 1, 0, 'R');
            $pdf->Cell(28, 6, 'Cumulative', 1, 0, 'R');
            $pdf->Cell(28, 6, 'Remaining', 1, 0, 'R');
            $pdf->Cell(0, 6, 'Payment Method', 1, 1, 'L');

            $pdf->SetFont('times', '', 7);

            // Sort payments by date (oldest first)
            $sortedPayments = $paymentHistory;
            usort($sortedPayments, function($a, $b) {
                return strtotime($a['payment_date']) - strtotime($b['payment_date']);
            });

            $grandTotal = (float)($pos['grand_total'] ?? 0);
            $cumulativePaid = 0;
            foreach ($sortedPayments as $payment) {
                $paymentAmount = (float)($payment['paid_amount'] ?? 0);
                $cumulativePaid += $paymentAmount;
                $remainingBalance = $grandTotal - $cumulativePaid;

                $pdf->Cell(30, 6, date('m/d/Y', strtotime($payment['payment_date'] ?? 'now')), 1, 0, 'C');
                $pdf->Cell(28, 6, number_format($paymentAmount, 2), 1, 0, 'R');
                $pdf->Cell(28, 6, number_format($cumulativePaid, 2), 1, 0, 'R');
                $pdf->Cell(28, 6, number_format(max(0, $remainingBalance), 2), 1, 0, 'R');
                $pdf->Cell(0, 6, substr($payment['payment_method'] ?? '', 0, 25), 1, 1, 'L');
            }

            // Summary row
            $pdf->SetFont('times', 'B', 8);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(30, 6, 'Total', 1, 0, 'C', true);
            $paidAmount = (float)($pos['paid_amount'] ?? 0);
            $pdf->Cell(28, 6, number_format($paidAmount, 2), 1, 0, 'R', true);
            $pdf->Cell(28, 6, number_format($paidAmount, 2), 1, 0, 'R', true);
            $pdf->Cell(28, 6, number_format(max(0, $grandTotal - $paidAmount), 2), 1, 0, 'R', true);
            $pdf->Cell(0, 6, count($paymentHistory) . ' payment(s)', 1, 1, 'L', true);
            $pdf->SetFillColor(255, 255, 255);

            $pdf->Ln(2);
        }

        // Thank you message
        $pdf->SetFont('times', 'B', 12);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'Thank you for your business!', 1, 1, 'C', true);

        $pdf->Output('POS-' . ($pos['pos_no'] ?? 'UNKNOWN') . '.pdf', 'I');
    }

    public function actionContractprint()
    {
        try {
            $contractId = (int)Yii::$app->request->get('id');

            if ($contractId <= 0) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Invalid Contract ID');
            }

            $db = Yii::$app->db;

            $contract = $db->createCommand(
                "SELECT * FROM system_contracts WHERE id = :id AND is_deleted = 0 LIMIT 1"
            )->bindValue(':id', $contractId)->queryOne();

            if (!$contract) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Contract not found');
            }

            $this->generateContractPDF($contract);
        } catch (\Exception $e) {
            Yii::$app->response->format = 'html';
            return $this->renderContent('Error: ' . $e->getMessage());
        }
    }

    private function generateContractPDF($contract)
    {
        $company = $this->getCompanyInfo();
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(12, 12, 12);
        $pdf->SetAutoPageBreak(false, 12);
        $pdf->AddPage();

        // Header with company info on right
        $pdf->SetFont('times', 'B', 20);
        $pdf->SetXY(12, 12);
        $pdf->Cell(110, 10, 'System Contract Document', 0, 0, 'L');

        // Company info on right
        $pdf->SetFont('times', 'B', 9);
        $pdf->SetXY(130, 12);
        $pdf->MultiCell(58, 3.2,
            strtoupper($company['company_name']) . "\n" .
            $company['company_address'] . "\n" .
            $company['company_phone'] . "\n" .
            $company['company_email'],
            0, 'L'
        );

        $pdf->SetFont('times', 'I', 9);
        $pdf->SetXY(12, 32);
        $pdf->Cell(0, 5, 'Contract Number: ' . htmlspecialchars($contract['contract_number']), 0, 1, 'L');

        $pdf->Ln(2);

        // Contract Details Section
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(15, 76, 41);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, '1. CONTRACT DETAILS', 0, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->SetFillColor(245, 245, 245);

        // Two columns layout
        $col1Width = 95;
        $col2Width = 95;

        // Row 1
        $pdf->Cell(30, 5, 'Contract Name:', 0, 0, 'L', true);
        $pdf->Cell($col1Width - 30, 5, htmlspecialchars(substr($contract['contract_name'] ?? 'N/A', 0, 35)), 0, 0, 'L', true);
        $pdf->Cell(30, 5, 'Contract Type:', 0, 0, 'L', true);
        $contractType = $contract['contract_type'] === 'yearly' ? 'YEARLY' : 'MONTHLY';
        $pdf->Cell(0, 5, $contractType, 0, 1, 'L', true);

        // Row 2
        $pdf->Cell(30, 5, 'Contract No:', 0, 0, 'L', true);
        $pdf->Cell($col1Width - 30, 5, htmlspecialchars($contract['contract_number'] ?? 'N/A'), 0, 0, 'L', true);
        $pdf->Cell(30, 5, 'System Status:', 0, 0, 'L', true);
        $statusColor = strtolower($contract['system_status']) === 'active' ? '✓ ACTIVE' : strtoupper($contract['system_status']);
        $pdf->Cell(0, 5, $statusColor, 0, 1, 'L', true);

        $pdf->Ln(2);

        // Contractor Information Section
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(15, 76, 41);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, '2. CONTRACTOR INFORMATION', 0, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->SetFillColor(245, 245, 245);

        // Row 1
        $pdf->Cell(30, 5, 'Name:', 0, 0, 'L', true);
        $pdf->Cell($col1Width - 30, 5, htmlspecialchars(substr($contract['contractor_name'] ?? 'N/A', 0, 35)), 0, 0, 'L', true);
        $pdf->Cell(30, 5, 'CNIC/ID:', 0, 0, 'L', true);
        $pdf->Cell(0, 5, htmlspecialchars($contract['contractor_cnic'] ?? 'N/A'), 0, 1, 'L', true);

        // Row 2
        $pdf->Cell(30, 5, 'Email:', 0, 0, 'L', true);
        $pdf->Cell($col1Width - 30, 5, htmlspecialchars(substr($contract['contractor_email'] ?? 'N/A', 0, 35)), 0, 0, 'L', true);
        $pdf->Cell(30, 5, 'Phone:', 0, 0, 'L', true);
        $pdf->Cell(0, 5, htmlspecialchars($contract['contractor_phone'] ?? 'N/A'), 0, 1, 'L', true);

        // Address (full width)
        $pdf->Cell(30, 5, 'Address:', 0, 0, 'L', true);
        $pdf->Cell(0, 5, htmlspecialchars(substr($contract['contractor_address'] ?? 'N/A', 0, 60)), 0, 1, 'L', true);

        $pdf->Ln(2);

        // Important Dates Section
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(15, 76, 41);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, '3. IMPORTANT DATES', 0, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->SetFillColor(245, 245, 245);

        // Row 1
        $instDate = $contract['installation_date'] ? date('d M, Y', strtotime($contract['installation_date'])) : 'N/A';
        $pdf->Cell(30, 5, 'Installation:', 0, 0, 'L', true);
        $pdf->Cell($col1Width - 30, 5, $instDate, 0, 0, 'L', true);
        $startDate = $contract['contract_start_date'] ? date('d M, Y', strtotime($contract['contract_start_date'])) : 'N/A';
        $pdf->Cell(30, 5, 'Start Date:', 0, 0, 'L', true);
        $pdf->Cell(0, 5, $startDate, 0, 1, 'L', true);

        // Row 2
        $endDate = $contract['contract_end_date'] ? date('d M, Y', strtotime($contract['contract_end_date'])) : 'N/A';
        $pdf->Cell(30, 5, 'End Date:', 0, 0, 'L', true);
        $pdf->Cell($col1Width - 30, 5, $endDate, 0, 0, 'L', true);
        $pdf->Cell(30, 5, 'Due Date:', 0, 0, 'L', true);
        $pdf->Cell(0, 5, $contract['monthly_due_date'] . ' of month', 0, 1, 'L', true);

        // Row 3
        $pdf->Cell(30, 5, 'Extension Days:', 0, 0, 'L', true);
        $pdf->Cell($col1Width - 30, 5, $contract['maximum_extension_days'] . ' days', 0, 0, 'L', true);
        $pdf->Cell(30, 5, '', 0, 0, 'L', true);
        $pdf->Cell(0, 5, '', 0, 1, 'L', true);

        $pdf->Ln(2);

        // Financial Details Section
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(15, 76, 41);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, '4. FINANCIAL DETAILS', 0, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->SetFillColor(245, 245, 245);

        // Row 1
        $pdf->Cell(30, 5, 'Monthly Charges:', 0, 0, 'L', true);
        $pdf->Cell($col1Width - 30, 5, 'PKR ' . number_format($contract['monthly_charges'], 2), 0, 0, 'L', true);
        $pdf->Cell(30, 5, 'Yearly Charges:', 0, 0, 'L', true);
        $pdf->Cell(0, 5, 'PKR ' . number_format($contract['yearly_charges'], 2), 0, 1, 'L', true);

        $pdf->Ln(2);

        // Descriptions Section (Compact)
        $descriptions = [];
        if (!empty($contract['contract_description'])) {
            $descriptions[] = 'Contract: ' . substr($contract['contract_description'], 0, 50) . (strlen($contract['contract_description']) > 50 ? '...' : '');
        }
        if (!empty($contract['policy_description'])) {
            $descriptions[] = 'Policy: ' . substr($contract['policy_description'], 0, 50) . (strlen($contract['policy_description']) > 50 ? '...' : '');
        }

        if (!empty($descriptions)) {
            $pdf->SetFont('times', 'B', 10);
            $pdf->SetFillColor(15, 76, 41);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 6, '5. ADDITIONAL INFORMATION', 0, 1, 'L', true);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('times', '', 8);
            $pdf->SetFillColor(245, 245, 245);
            foreach ($descriptions as $desc) {
                $pdf->Cell(0, 4, $desc, 0, 1, 'L', true);
            }
            $pdf->Ln(1);
        }

        // Signature Section
        $currentY = $pdf->GetY();
        $pageHeight = $pdf->getPageHeight();

        // Check if we need new page for signatures
        if ($currentY + 30 > $pageHeight - 15) {
            $pdf->AddPage();
            $pdf->SetMargins(12, 12, 12);
        }

        $pdf->Ln(4);

        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(15, 76, 41);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'AUTHORIZED SIGNATURES', 0, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 9);
        $pdf->Ln(2);

        // Developer/System Signature
        $pdf->Cell(95, 4, 'System Developer/Manager', 0, 0, 'C');
        $pdf->Cell(95, 4, 'Contractor/Client', 0, 1, 'C');

        // Signature lines
        $pdf->Cell(95, 18, '', 1, 0, 'C');
        $pdf->Cell(95, 18, '', 1, 1, 'C');

        $pdf->SetFont('times', '', 8);
        $pdf->Cell(95, 4, 'Name & Signature', 0, 0, 'C');
        $pdf->Cell(95, 4, 'Name & Signature', 0, 1, 'C');

        $pdf->Ln(2);

        $pdf->Cell(95, 4, 'Date: _______________', 0, 0, 'C');
        $pdf->Cell(95, 4, 'Date: _______________', 0, 1, 'C');

        // Footer
        $pdf->Ln(3);
        $pdf->SetFont('times', 'B', 8);
        $pdf->SetFillColor(50, 50, 50);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 5, 'Generated on: ' . date('d M, Y \a\t H:i:s') . ' | Inventory Management System', 0, 1, 'C', true);

        $pdf->Output('CONTRACT-' . $contract['contract_number'] . '.pdf', 'I');
    }

    public function actionBillpaymentprint()
    {
        try {
            $invoiceId = (int)Yii::$app->request->get('id');

            if ($invoiceId <= 0) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Invalid Invoice ID');
            }

            $db = Yii::$app->db;

            $invoice = $db->createCommand(
                "SELECT si.*, sc.contract_name FROM system_invoices si
                 LEFT JOIN system_contracts sc ON sc.id = si.contract_id
                 WHERE si.id = :id AND si.is_deleted = 0 LIMIT 1"
            )->bindValue(':id', $invoiceId)->queryOne();

            if (!$invoice) {
                Yii::$app->response->format = 'html';
                return $this->renderContent('Invoice not found');
            }

            // Get payment records for this invoice
            $payments = $db->createCommand(
                "SELECT sp.* FROM system_payment_proofs sp
                 WHERE sp.invoice_id = :id AND sp.is_deleted = 0
                 ORDER BY sp.created_at DESC"
            )->bindValue(':id', $invoiceId)->queryAll();

            $this->generateBillPaymentPDF($invoice, $payments);
        } catch (\Exception $e) {
            Yii::$app->response->format = 'html';
            return $this->renderContent('Error: ' . $e->getMessage());
        }
    }

    private function generateBillPaymentPDF($invoice, $payments = [])
    {
        $company = $this->getCompanyInfo();
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // Header with company info
        $pdf->SetFont('times', 'B', 24);
        $pdf->SetXY(15, 15);
        $pdf->Cell(110, 10, 'PAYMENT RECEIPT', 0, 0, 'L');

        // Company info on right
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetXY(130, 15);
        $pdf->MultiCell(55, 4,
            strtoupper($company['company_name']) . "\n" .
            $company['company_address'] . "\n" .
            $company['company_phone'] . "\n" .
            $company['company_email'],
            0, 'L'
        );

        $pdf->SetFont('times', 'I', 10);
        $pdf->SetXY(15, 35);
        $pdf->Cell(0, 5, 'Invoice #: ' . htmlspecialchars($invoice['invoice_number']), 0, 1, 'L');

        $pdf->Ln(3);

        // Invoice Summary Section
        $pdf->SetFont('times', 'B', 11);
        $pdf->SetFillColor(15, 76, 41);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 7, 'INVOICE INFORMATION', 0, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->SetFillColor(245, 245, 245);

        // Two columns
        $col1Width = 95;

        // Row 1
        $pdf->Cell(30, 6, 'Invoice #:', 0, 0, 'L', true);
        $pdf->Cell($col1Width - 30, 6, htmlspecialchars($invoice['invoice_number'] ?? 'N/A'), 0, 0, 'L', true);
        $pdf->Cell(30, 6, 'Invoice Date:', 0, 0, 'L', true);
        $invoiceDate = $invoice['invoice_date'] ? date('M d, Y', strtotime($invoice['invoice_date'])) : 'N/A';
        $pdf->Cell(0, 6, $invoiceDate, 0, 1, 'L', true);

        // Row 2
        $pdf->Cell(30, 6, 'Contract:', 0, 0, 'L', true);
        $pdf->Cell($col1Width - 30, 6, htmlspecialchars(substr($invoice['contract_name'] ?? 'N/A', 0, 30)), 0, 0, 'L', true);
        $pdf->Cell(30, 6, 'Due Date:', 0, 0, 'L', true);
        $dueDate = $invoice['due_date'] ? date('M d, Y', strtotime($invoice['due_date'])) : 'N/A';
        $pdf->Cell(0, 6, $dueDate, 0, 1, 'L', true);

        $pdf->Ln(2);

        // Amount Information Section
        $pdf->SetFont('times', 'B', 11);
        $pdf->SetFillColor(15, 76, 41);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 7, 'PAYMENT DETAILS', 0, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->SetFillColor(245, 245, 245);

        // Row 1 - Amounts
        $totalAmount = (float)($invoice['amount'] ?? 0);
        $paidAmount = (float)($invoice['paid_amount'] ?? 0);
        $pdf->Cell(30, 6, 'Total Amount:', 0, 0, 'L', true);
        $pdf->Cell($col1Width - 30, 6, 'PKR ' . number_format($totalAmount, 2), 0, 0, 'L', true);
        $pdf->Cell(30, 6, 'Paid Amount:', 0, 0, 'L', true);
        $pdf->SetFillColor(200, 255, 200);
        $pdf->Cell(0, 6, 'PKR ' . number_format($paidAmount, 2), 0, 1, 'L', true);

        // Row 2 - Remaining
        $remaining = $totalAmount - $paidAmount;
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(30, 6, 'Remaining:', 0, 0, 'L', true);
        $pdf->Cell($col1Width - 30, 6, 'PKR ' . number_format(max(0, $remaining), 2), 0, 0, 'L', true);
        $pdf->Cell(30, 6, 'Status:', 0, 0, 'L', true);
        $statusText = $remaining <= 0 ? 'PAID' : 'UNPAID';
        $pdf->Cell(0, 6, $statusText, 0, 1, 'L', true);

        $pdf->Ln(2);

        // Payment History Section
        if (!empty($payments)) {
            $pdf->SetFont('times', 'B', 11);
            $pdf->SetFillColor(15, 76, 41);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 7, 'PAYMENT HISTORY', 0, 1, 'L', true);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('times', 'B', 10);
            $pdf->SetFillColor(220, 220, 220);

            // Table header
            $pdf->Cell(35, 6, 'Payment Date', 1, 0, 'C', true);
            $pdf->Cell(30, 6, 'Amount', 1, 0, 'R', true);
            $pdf->Cell(35, 6, 'Method', 1, 0, 'C', true);
            $pdf->Cell(0, 6, 'Transaction ID', 1, 1, 'L', true);

            $pdf->SetFont('times', '', 9);
            $pdf->SetFillColor(245, 245, 245);

            // Table rows
            foreach ($payments as $payment) {
                $payDate = $payment['proof_date'] ? date('M d, Y', strtotime($payment['proof_date'])) : 'N/A';
                $paymentAmount = (float)($payment['amount'] ?? 0);
                $pdf->Cell(35, 6, $payDate, 1, 0, 'C', true);
                $pdf->Cell(30, 6, 'PKR ' . number_format($paymentAmount, 2), 1, 0, 'R', true);
                $pdf->Cell(35, 6, ucfirst(str_replace('_', ' ', $payment['payment_method'] ?? 'N/A')), 1, 0, 'C', true);
                $pdf->Cell(0, 6, substr($payment['transaction_id'] ?? 'N/A', 0, 20), 1, 1, 'L', true);
            }

            $pdf->Ln(2);
        }

        // Payment Method & Details
        if (!empty($payments)) {
            $latestPayment = $payments[0];
            $pdf->SetFont('times', 'B', 11);
            $pdf->SetFillColor(15, 76, 41);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 7, 'LATEST PAYMENT INFORMATION', 0, 1, 'L', true);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('times', '', 10);
            $pdf->SetFillColor(245, 245, 245);

            // Payment method info
            $pdf->Cell(30, 6, 'Payment Method:', 0, 0, 'L', true);
            $pdf->Cell($col1Width - 30, 6, ucfirst(str_replace('_', ' ', $latestPayment['payment_method'] ?? 'N/A')), 0, 0, 'L', true);
            $pdf->Cell(30, 6, 'Proof Date:', 0, 0, 'L', true);
            $proofDate = $latestPayment['proof_date'] ? date('M d, Y', strtotime($latestPayment['proof_date'])) : 'N/A';
            $pdf->Cell(0, 6, $proofDate, 0, 1, 'L', true);

            if (!empty($latestPayment['transaction_id'])) {
                $pdf->Cell(30, 6, 'Transaction ID:', 0, 0, 'L', true);
                $pdf->Cell(0, 6, htmlspecialchars($latestPayment['transaction_id']), 0, 1, 'L', true);
            }

            $pdf->Ln(2);

            // User Comments Section
            if (!empty($latestPayment['comments'])) {
                $pdf->SetFont('times', 'B', 11);
                $pdf->SetFillColor(15, 76, 41);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(0, 7, 'USER COMMENTS', 0, 1, 'L', true);

                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('times', '', 10);
                $pdf->SetFillColor(245, 245, 245);
                $pdf->MultiCell(0, 5, htmlspecialchars($latestPayment['comments']), 1, 'L', true);
                $pdf->Ln(2);
            }

            // Admin Comments Section
            if (!empty($latestPayment['admin_comments'])) {
                $pdf->SetFont('times', 'B', 11);
                $pdf->SetFillColor(15, 76, 41);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(0, 7, 'APPROVAL COMMENTS', 0, 1, 'L', true);

                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('times', '', 10);
                $pdf->SetFillColor(200, 255, 200);
                $pdf->MultiCell(0, 5, htmlspecialchars($latestPayment['admin_comments']), 1, 'L', true);
                $pdf->Ln(2);
            }
        }

        // Verification Status
        $pdf->SetFont('times', 'B', 11);
        $pdf->SetFillColor(15, 76, 41);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 7, 'VERIFICATION STATUS', 0, 1, 'L', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->SetFillColor(200, 255, 200);

        $verificationStatus = !empty($payments) ? 'PAYMENT VERIFIED' : 'PAYMENT VERIFIED';
        $pdf->Cell(0, 6, '✓ ' . $verificationStatus, 0, 1, 'L', true);

        $pdf->Ln(3);

        // Terms & Conditions
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(0, 6, 'Terms & Conditions:', 0, 1, 'L', true);

        $pdf->SetFont('times', '', 9);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->MultiCell(0, 4,
            "• This is an official payment receipt generated from the Inventory Management System.\n" .
            "• Payment verification is completed by the authorized personnel.\n" .
            "• All payment proofs have been verified and approved.\n" .
            "• For any queries, please contact the administration team.",
            1, 'L'
        );

        $pdf->Ln(3);

        // Footer
        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(15, 76, 41);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, 'Generated on: ' . date('M d, Y \a\t H:i:s') . ' | Inventory Management System', 0, 1, 'C', true);

        $pdf->Output('PAYMENT-RECEIPT-' . $invoice['invoice_number'] . '.pdf', 'I');
    }
}

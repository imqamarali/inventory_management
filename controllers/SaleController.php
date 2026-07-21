<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class SaleController extends Controller
{
    private function currentUserId()
    {
        $user_array = Yii::$app->session->get('user_array');
        return $user_array['id'] ?? null;
    }
    private function jsonResponse($success, $message, $data = [])
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return array_merge([
            'success' => $success,
            'message' => $message,
        ], $data);
    }
    private function generateDocNo($prefix)
    {
        return $prefix . '-' . date('Ymd') . '-' . date('His') . '-' . mt_rand(100, 999);
    }

    private function generateSaleInvoiceNumber()
    {
        $db = Yii::$app->db;
        $count = $db->createCommand("SELECT COUNT(*) + 1 FROM inventory_sales_invoices")->queryScalar();
        return 'SINV-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    private function recordInvoicePayment($invoiceId, $paidAmount, $oldPaidAmount = 0, $remarks = 'Initial Payment', $user_id = null)
    {
        $db = Yii::$app->db;
        $user_id = $user_id ?? $this->currentUserId();

        // Calculate payment difference
        $paymentDifference = $paidAmount - $oldPaidAmount;

        // Always create payment record if there's any payment (including initial)
        if ($paymentDifference > 0) {
            $db->createCommand()->insert(
                'inventory_sale_invoice_payments',
                [
                    'sale_invoice_id' => $invoiceId,
                    'paid_amount' => $paymentDifference,
                    'payment_date' => date('Y-m-d'),
                    'remarks' => $remarks,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $user_id
                ]
            )->execute();

            return true;
        }

        return false;
    }

    private function postSaleToGL($sales_order_id, $invoice_no, $grand_total, $user_id)
    {
        $db = Yii::$app->db;

        try {
            // Get default sales account from settings
            $salesAccountId = $db->createCommand(
                "SELECT setting_value FROM inventory_settings WHERE setting_key='default_sales_account' AND is_deleted=0"
            )->queryScalar();

            if (!$salesAccountId) {
                \Yii::warning("postSaleToGL: Sales account not configured");
                return false; // Sales account not configured
            }

            // Debit: Accounts Receivable Account (Customer receivable tracking)
            $arAccountId = $db->createCommand(
                "SELECT id FROM inventory_accounts WHERE account_code='1200' AND is_deleted=0 LIMIT 1"
            )->queryScalar();

            // If AR account doesn't exist, try to create it
            if (!$arAccountId) {
                $db->createCommand()->insert('inventory_accounts', [
                    'account_code' => '1200',
                    'account_name' => 'Accounts Receivable',
                    'account_type' => 'Asset',
                    'current_balance' => 0,
                    'is_active' => 1,
                    'is_deleted' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ])->execute();

                $arAccountId = $db->getLastInsertID();
                \Yii::warning("postSaleToGL: Created missing AR account (ID: " . $arAccountId . ")");
            }

            if (!$arAccountId) {
                \Yii::warning("postSaleToGL: Could not create AR account");
                return false;
            }

            $transactionNo = 'SALE-' . $invoice_no;

            // Credit: Sales Revenue Account
            $db->createCommand()->insert('inventory_transactions', [
                'transaction_no' => $transactionNo . '-CR',
                'transaction_date' => date('Y-m-d'),
                'reference_type' => 'Sale',
                'reference_id' => $sales_order_id,
                'account_id' => $salesAccountId,
                'transaction_type' => 'Credit',
                'amount' => $grand_total,
                'remarks' => 'Sale recorded - Invoice: ' . $invoice_no,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $user_id,
                'is_active' => 1,
                'is_deleted' => 0
            ])->execute();

            // Debit: Accounts Receivable
            $db->createCommand()->insert('inventory_transactions', [
                'transaction_no' => $transactionNo . '-DR',
                'transaction_date' => date('Y-m-d'),
                'reference_type' => 'Sale',
                'reference_id' => $sales_order_id,
                'account_id' => $arAccountId,
                'transaction_type' => 'Debit',
                'amount' => $grand_total,
                'remarks' => 'Account Receivable - Invoice: ' . $invoice_no,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $user_id,
                'is_active' => 1,
                'is_deleted' => 0
            ])->execute();

            // Update account balance for Sales Revenue
            $db->createCommand()->update('inventory_accounts', [
                'current_balance' => new \yii\db\Expression('current_balance + ' . $grand_total)
            ], ['id' => $salesAccountId])->execute();

            // Update account balance for Accounts Receivable
            $db->createCommand()->update('inventory_accounts', [
                'current_balance' => new \yii\db\Expression('current_balance + ' . $grand_total)
            ], ['id' => $arAccountId])->execute();

            return true;
        } catch (\Exception $e) {
            \Yii::error("postSaleToGL error: " . $e->getMessage());
            return false;
        }
    }

    private function postSalePaymentToGL($sales_order_id, $invoice_no, $paid_amount, $user_id)
    {
        $db = Yii::$app->db;

        try {
            // Get default cash/bank account from settings
            $cashAccountId = $db->createCommand(
                "SELECT setting_value FROM inventory_settings WHERE setting_key='default_cash_account' AND is_deleted=0"
            )->queryScalar();

            // If no cash account configured, try to find the main cash account (usually 1100)
            if (!$cashAccountId) {
                $cashAccountId = $db->createCommand(
                    "SELECT id FROM inventory_accounts WHERE account_code='1100' AND is_deleted=0 LIMIT 1"
                )->queryScalar();
            }

            // If still no cash account, try to create it
            if (!$cashAccountId) {
                $db->createCommand()->insert('inventory_accounts', [
                    'account_code' => '1100',
                    'account_name' => 'Cash at Bank',
                    'account_type' => 'Asset',
                    'current_balance' => 0,
                    'is_active' => 1,
                    'is_deleted' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ])->execute();

                $cashAccountId = $db->getLastInsertID();
                \Yii::warning("postSalePaymentToGL: Created missing Cash account (ID: " . $cashAccountId . ")");
            }

            // Get AR account
            $arAccountId = $db->createCommand(
                "SELECT id FROM inventory_accounts WHERE account_code='1200' AND is_deleted=0 LIMIT 1"
            )->queryScalar();

            // If AR account doesn't exist, try to create it
            if (!$arAccountId) {
                $db->createCommand()->insert('inventory_accounts', [
                    'account_code' => '1200',
                    'account_name' => 'Accounts Receivable',
                    'account_type' => 'Asset',
                    'current_balance' => 0,
                    'is_active' => 1,
                    'is_deleted' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ])->execute();

                $arAccountId = $db->getLastInsertID();
                \Yii::warning("postSalePaymentToGL: Created missing AR account (ID: " . $arAccountId . ")");
            }

            if (!$cashAccountId || !$arAccountId) {
                \Yii::error("postSalePaymentToGL: Could not get/create required accounts");
                return false;
            }

            $transactionNo = 'PAYMENT-' . $invoice_no;

            // Debit: Cash/Bank Account
            $db->createCommand()->insert('inventory_transactions', [
                'transaction_no' => $transactionNo . '-DR',
                'transaction_date' => date('Y-m-d'),
                'reference_type' => 'Sale',
                'reference_id' => $sales_order_id,
                'account_id' => $cashAccountId,
                'transaction_type' => 'Debit',
                'amount' => $paid_amount,
                'remarks' => 'Sale Payment Received - Invoice: ' . $invoice_no,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $user_id,
                'is_active' => 1,
                'is_deleted' => 0
            ])->execute();

            // Credit: Accounts Receivable (reduce AR)
            $db->createCommand()->insert('inventory_transactions', [
                'transaction_no' => $transactionNo . '-CR',
                'transaction_date' => date('Y-m-d'),
                'reference_type' => 'Sale',
                'reference_id' => $sales_order_id,
                'account_id' => $arAccountId,
                'transaction_type' => 'Credit',
                'amount' => $paid_amount,
                'remarks' => 'Sale Payment - Reduce AR - Invoice: ' . $invoice_no,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $user_id,
                'is_active' => 1,
                'is_deleted' => 0
            ])->execute();

            // Update account balances
            $db->createCommand()->update('inventory_accounts', [
                'current_balance' => new \yii\db\Expression('current_balance + ' . $paid_amount)
            ], ['id' => $cashAccountId])->execute();

            $db->createCommand()->update('inventory_accounts', [
                'current_balance' => new \yii\db\Expression('current_balance - ' . $paid_amount)
            ], ['id' => $arAccountId])->execute();

            return true;
        } catch (\Exception $e) {
            \Yii::error("postSalePaymentToGL error: " . $e->getMessage());
            return false;
        }
    }

    private function createSaleInvoiceFromPos($pos_sales_id, $customer_id, $subtotal, $discount_amount, $tax_amount, $grand_total, $paid_amount, $user_id)
    {
        $db = Yii::$app->db;

        // Get POS Sale number to link with invoice
        $posSale = $db->createCommand("
            SELECT pos_no FROM inventory_pos_sales WHERE id = :id
        ")->bindValue(':id', $pos_sales_id)->queryOne();

        $invoice_no = $this->generateSaleInvoiceNumber();
        $remaining_balance = $grand_total - $paid_amount;
        $status = ($paid_amount >= $grand_total) ? 'Paid' : (($paid_amount > 0) ? 'Partial' : 'Unpaid');

        $db->createCommand()->insert(
            'inventory_sales_invoices',
            [
                'sales_order_id' => null,
                'invoice_no' => $invoice_no,
                'customer_id' => $customer_id,
                'invoice_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+30 days')),
                'subtotal' => $subtotal,
                'discount_amount' => $discount_amount,
                'tax_amount' => $tax_amount,
                'grand_total' => $grand_total,
                'paid_amount' => $paid_amount,
                'remaining_balance' => $remaining_balance,
                'status' => $status,
                'remarks' => 'Auto-generated from POS Sale ID: ' . $pos_sales_id . ' | POS No: ' . ($posSale['pos_no'] ?? 'N/A'),
                'created_at' => date('Y-m-d H:i:s'),
                'is_active' => 1,
                'is_deleted' => 0
            ]
        )->execute();

        $invoice_id = $db->getLastInsertID();

        // Create payment history if there's a payment
        if ($paid_amount > 0) {
            $db->createCommand()->insert(
                'inventory_sale_invoice_payments',
                [
                    'sale_invoice_id' => $invoice_id,
                    'paid_amount' => $paid_amount,
                    'payment_date' => date('Y-m-d'),
                    'remarks' => 'POS Payment',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $user_id
                ]
            )->execute();

            // Post POS payment to GL
            $this->postSalePaymentToGL($pos_sales_id, $invoice_no, $paid_amount, $user_id);
        }

        // Auto-update invoice status if fully paid (for POS, invoice status updates to Paid when balance is zero)
        if ($remaining_balance <= 0) {
            $db->createCommand()->update(
                'inventory_sales_invoices',
                ['status' => 'Paid', 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => $invoice_id]
            )->execute();
        }

        return $invoice_no;
    }

    private function createSaleInvoiceFromSalesOrder($sales_order_id, $subtotal, $discount_amount, $tax_amount, $grand_total, $user_id, $paid_amount = 0)
    {
        $db = Yii::$app->db;

        // Get the sales order to get order date, delivery date, and customer
        $order = $db->createCommand("
            SELECT * FROM inventory_sales_orders WHERE id = :id
        ")->bindValue(':id', $sales_order_id)->queryOne();

        if (!$order) {
            return null;
        }

        $invoice_no = $this->generateSaleInvoiceNumber();
        $remaining_balance = $grand_total - $paid_amount;
        $status = ($paid_amount >= $grand_total) ? 'Paid' : (($paid_amount > 0) ? 'Partially Paid' : 'Draft');

        $db->createCommand()->insert(
            'inventory_sales_invoices',
            [
                'sales_order_id' => $sales_order_id,
                'invoice_no' => $invoice_no,
                'customer_id' => $order['customer_id'],
                'invoice_date' => $order['order_date'],
                'due_date' => $order['delivery_date'] ?? date('Y-m-d', strtotime('+30 days')),
                'subtotal' => $subtotal,
                'discount' => $discount_amount,
                'tax' => $tax_amount,
                'grand_total' => $grand_total,
                'paid_amount' => $paid_amount,
                'remaining_balance' => max(0, $remaining_balance),
                'status' => $status,
                'notes' => 'Auto-generated from Sales Order ID: ' . $sales_order_id,
                'created_at' => date('Y-m-d H:i:s'),
                'is_active' => 1,
                'is_deleted' => 0
            ]
        )->execute();

        $invoice_id = $db->getLastInsertID();

        // Create payment history record if there's an initial payment
        if ($paid_amount > 0) {
            $this->recordInvoicePayment($invoice_id, $paid_amount, 0, 'Initial Payment - Sales Order', $user_id);

            // Post payment to GL (reduce AR, increase cash)
            $this->postSalePaymentToGL($sales_order_id, $invoice_no, $paid_amount, $user_id);
        }

        // Post sale to GL when invoice is created
        $this->postSaleToGL($sales_order_id, $invoice_no, $grand_total, $user_id);

        // Auto-update sales order and invoice status if fully paid (remaining balance is zero)
        if ($remaining_balance <= 0) {
            $db->createCommand()->update(
                'inventory_sales_orders',
                ['order_status' => 'Completed', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $user_id],
                ['id' => $sales_order_id]
            )->execute();

            $db->createCommand()->update(
                'inventory_sales_invoices',
                ['status' => 'Paid', 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => $invoice_id]
            )->execute();
        }

        return $invoice_no;
    }

    private function saveSalesOrder($post, $user_id, $sales_order_id = null)
    {
        if (empty($post['customer_id']) || empty($post['warehouse_id'])) {
            return $this->jsonResponse(false, 'Customer and warehouse are required.');
        }

        $items = $post['items'] ?? [];
        if (!is_array($items)) {
            $items = json_decode($items, true);
        }
        if (empty($items)) {
            return $this->jsonResponse(false, 'At least one item is required.');
        }

        $isNewOrder = empty($sales_order_id); // Store whether this is new or update
        $originalSalesOrderId = $sales_order_id; // Save original ID before it gets reassigned
        $trans = Yii::$app->db->beginTransaction();

        try {

            $customer_id = $post['customer_id'];
            $warehouse_id = $post['warehouse_id'];

            $subtotal = 0;
            $discountTotal = 0;
            $taxTotal = 0;
            foreach ($items as $item) {
                if (empty($item['product_id']) || empty($item['quantity'])) {
                    continue;
                }
                $qty = (float)$item['quantity'];
                $price = (float)($item['unit_price'] ?? 0);
                $disc = (float)($item['discount'] ?? 0);
                $tax = (float)($item['tax'] ?? 0);
                $subtotal += $qty * $price;
                $discountTotal += $disc;
                $taxTotal += $tax;
            }
            $shipping = (float)($post['shipping'] ?? 0);
            $grandTotal = $subtotal - $discountTotal + $taxTotal + $shipping;
            $paidAmount = (float)($post['paid_amount'] ?? 0);

            if ($sales_order_id) {

                $this->reverseSalesOrderStockEffect($sales_order_id, $user_id);

                Yii::$app->db->createCommand()->update(
                    'inventory_sales_orders',
                    [
                        'customer_id' => $customer_id,
                        'warehouse_id' => $warehouse_id,
                        'order_date' => $post['order_date'] ?? date('Y-m-d'),
                        'delivery_date' => $post['delivery_date'] ?? null,
                        'order_status' => $post['order_status'] ?? 'Draft',
                        'payment_status' => $post['payment_status'] ?? 'Pending',
                        'subtotal' => $subtotal,
                        'discount' => $discountTotal,
                        'tax' => $taxTotal,
                        'shipping' => $shipping,
                        'grand_total' => $grandTotal,
                        'notes' => $post['notes'] ?? null,
                        'updated_by' => $user_id
                    ],
                    ['id' => $sales_order_id]
                )->execute();

                Yii::$app->db->createCommand()->update(
                    'inventory_sales_order_items',
                    ['is_deleted' => 1],
                    ['sales_order_id' => $sales_order_id]
                )->execute();

                // Update linked invoice if paid_amount changed
                if ($paidAmount > 0) {
                    $invoice =Yii::$app->db->createCommand(
                        "SELECT id, paid_amount FROM inventory_sales_invoices WHERE sales_order_id = :id AND is_deleted = 0 LIMIT 1"
                    )->bindValue(':id', $sales_order_id)->queryOne();

                    if ($invoice) {
                        $oldPaidAmount = (float)($invoice['paid_amount'] ?? 0);
                        $remainingBalance = $grandTotal - $paidAmount;

                        Yii::$app->db->createCommand()->update(
                            'inventory_sales_invoices',
                            [
                                'paid_amount' => $paidAmount,
                                'remaining_balance' => max(0, $remainingBalance),
                                'status' => $paidAmount >= $grandTotal ? 'Paid' : 'Unpaid',
                                'updated_at' => date('Y-m-d H:i:s')
                            ],
                            ['id' => $invoice['id']]
                        )->execute();

                        // Create payment history record if payment increased
                        $paymentDifference = $paidAmount - $oldPaidAmount;
                        if ($paymentDifference > 0) {
                            Yii::$app->db->createCommand()->insert(
                                'inventory_sale_invoice_payments',
                                [
                                    'sale_invoice_id' => $invoice['id'],
                                    'paid_amount' => $paymentDifference,
                                    'payment_date' => date('Y-m-d'),
                                    'remarks' => 'Partial Payment - Sales Order Update',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => $user_id
                                ]
                            )->execute();

                            // Auto-update sales order status to Completed if fully paid
                            if ($remainingBalance <= 0) {
                                Yii::$app->db->createCommand()->update(
                                    'inventory_sales_orders',
                                    ['order_status' => 'Completed', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $user_id],
                                    ['id' => $sales_order_id]
                                )->execute();
                            }
                        }
                    }
                }

            } else {

                Yii::$app->db->createCommand()->insert(
                    'inventory_sales_orders',
                    [
                        'order_number' => $this->generateDocNo('SO'),
                        'customer_id' => $customer_id,
                        'warehouse_id' => $warehouse_id,
                        'order_date' => $post['order_date'] ?? date('Y-m-d'),
                        'delivery_date' => $post['delivery_date'] ?? null,
                        'order_status' => $post['order_status'] ?? 'Draft',
                        'payment_status' => $post['payment_status'] ?? 'Pending',
                        'subtotal' => $subtotal,
                        'discount' => $discountTotal,
                        'tax' => $taxTotal,
                        'shipping' => $shipping,
                        'grand_total' => $grandTotal,
                        'notes' => $post['notes'] ?? null,
                        'created_by' => $user_id,
                        'updated_by' => $user_id
                    ]
                )->execute();

                $sales_order_id = Yii::$app->db->getLastInsertID();
            }

            foreach ($items as $item) {

                if (empty($item['product_id']) || empty($item['quantity'])) {
                    continue;
                }

                $product_id = $item['product_id'];
                $qty = (float)$item['quantity'];
                $price = (float)($item['unit_price'] ?? 0);
                $disc = (float)($item['discount'] ?? 0);
                $tax = (float)($item['tax'] ?? 0);
                $total = ($qty * $price) - $disc + $tax;

                Yii::$app->db->createCommand()->insert(
                    'inventory_sales_order_items',
                    [
                        'sales_order_id' => $sales_order_id,
                        'product_id' => $product_id,
                        'quantity' => $qty,
                        'delivered_quantity' => 0,
                        'remaining_quantity' => $qty,
                        'unit_price' => $price,
                        'discount' => $disc,
                        'tax' => $tax,
                        'total' => $total,
                        'remarks' => $item['remarks'] ?? null,
                        'created_by' => $user_id,
                        'updated_by' => $user_id
                    ]
                )->execute();

                // Reserve stock for this order so it can't be oversold while pending delivery.
                $stock = Yii::$app->db->createCommand("
                    SELECT *
                    FROM inventory_stock
                    WHERE warehouse_id=:warehouse
                    AND product_id=:product
                    AND is_deleted=0
                ")->bindValues([
                    ':warehouse' => $warehouse_id,
                    ':product' => $product_id
                ])->queryOne();

                if ($stock) {

                    $newReserved = $stock['reserved_quantity'] + $qty;

                    Yii::$app->db->createCommand()->update(
                        'inventory_stock',
                        [
                            'reserved_quantity' => $newReserved,
                            'available_quantity' => $stock['quantity'] - $newReserved,
                            'updated_by' => $user_id
                        ],
                        ['id' => $stock['id']]
                    )->execute();
                } else {

                    Yii::$app->db->createCommand()->insert(
                        'inventory_stock',
                        [
                            'warehouse_id' => $warehouse_id,
                            'product_id' => $product_id,
                            'quantity' => 0,
                            'reserved_quantity' => $qty,
                            'available_quantity' => -$qty,
                            'created_by' => $user_id,
                            'updated_by' => $user_id
                        ]
                    )->execute();
                }
            }

            // Auto-generate or update Sale Invoice
            if ($isNewOrder) {
                // This is a new order - create invoice with initial payment if provided
                $invoiceNo = $this->createSaleInvoiceFromSalesOrder($sales_order_id, $subtotal, $discountTotal, $taxTotal, $grandTotal, $user_id, $paidAmount);

                // Get the sales order number
                $orderData = Yii::$app->db->createCommand("
                    SELECT order_number FROM inventory_sales_orders WHERE id = :id
                ")->bindValue(':id', $sales_order_id)->queryOne();

                $trans->commit();

                $message = 'Sales Order ' . ($orderData['order_number'] ?? 'created') . ' and Invoice ' . ($invoiceNo ?? 'created') . ' successfully!';
                return $this->jsonResponse(true, $message, [
                    'id' => $sales_order_id,
                    'order_number' => $orderData['order_number'] ?? null,
                    'invoice_no' => $invoiceNo
                ]);
            } else {
                // This is an update - update or create invoice
                $existingInvoice = Yii::$app->db->createCommand("
                    SELECT id FROM inventory_sales_invoices
                    WHERE sales_order_id = :sales_order_id AND is_deleted = 0 LIMIT 1
                ")->bindValue(':sales_order_id', $originalSalesOrderId)->queryOne();

                if ($existingInvoice) {
                    // Update existing invoice
                    Yii::$app->db->createCommand()->update(
                        'inventory_sales_invoices',
                        [
                            'subtotal' => $subtotal,
                            'discount_amount' => $discountTotal,
                            'tax_amount' => $taxTotal,
                            'grand_total' => $grandTotal,
                            'updated_at' => date('Y-m-d H:i:s')
                        ],
                        ['id' => $existingInvoice['id']]
                    )->execute();
                } else {
                    // Create new invoice if it doesn't exist
                    $this->createSaleInvoiceFromSalesOrder($originalSalesOrderId, $subtotal, $discountTotal, $taxTotal, $grandTotal, $user_id, $paidAmount);
                }

                $trans->commit();

                // Get order and invoice numbers
                $orderData = Yii::$app->db->createCommand("
                    SELECT so.order_number, si.invoice_no
                    FROM inventory_sales_orders so
                    LEFT JOIN inventory_sales_invoices si ON si.sales_order_id = so.id
                    WHERE so.id = :id
                ")->bindValue(':id', $originalSalesOrderId)->queryOne();

                return $this->jsonResponse(true, 'Sales Order ' . ($orderData['order_number'] ?? 'updated') . ' and Invoice updated successfully!', [
                    'id' => $originalSalesOrderId,
                    'order_number' => $orderData['order_number'] ?? null,
                    'invoice_no' => $orderData['invoice_no'] ?? null
                ]);
            }

        } catch (\Exception $e) {

            $trans->rollBack();

            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function reverseSalesOrderStockEffect($sales_order_id, $user_id)
    {
        $order = Yii::$app->db->createCommand("
            SELECT *
            FROM inventory_sales_orders
            WHERE id=:id
        ")->bindValue(':id', $sales_order_id)->queryOne();

        if (!$order) {
            return;
        }

        $items = Yii::$app->db->createCommand("
            SELECT *
            FROM inventory_sales_order_items
            WHERE sales_order_id=:id
            AND is_deleted=0
        ")->bindValue(':id', $sales_order_id)->queryAll();

        foreach ($items as $item) {

            $stock = Yii::$app->db->createCommand("
                SELECT *
                FROM inventory_stock
                WHERE warehouse_id=:warehouse
                AND product_id=:product
                AND is_deleted=0
            ")->bindValues([
                ':warehouse' => $order['warehouse_id'],
                ':product' => $item['product_id']
            ])->queryOne();

            if (!$stock) {
                continue;
            }

            // Only the portion not yet delivered was ever reserved.
            $remaining = max(0, $item['quantity'] - $item['delivered_quantity']);
            $newReserved = max(0, $stock['reserved_quantity'] - $remaining);

            Yii::$app->db->createCommand()->update(
                'inventory_stock',
                [
                    'reserved_quantity' => $newReserved,
                    'available_quantity' => $stock['quantity'] - $newReserved,
                    'updated_by' => $user_id
                ],
                ['id' => $stock['id']]
            )->execute();
        }
    }

    private function deleteSalesOrder($id, $user_id)
    {
        $trans = Yii::$app->db->beginTransaction();

        try {

            $this->reverseSalesOrderStockEffect($id, $user_id);

            Yii::$app->db->createCommand()->update(
                'inventory_sales_order_items',
                [
                    'is_deleted' => 1,
                    'updated_by' => $user_id
                ],
                ['sales_order_id' => $id]
            )->execute();

            $result = Yii::$app->db->createCommand()->update(
                'inventory_sales_orders',
                [
                    'is_deleted' => 1,
                    'is_active' => 0,
                    'updated_by' => $user_id,
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                ['id' => $id]
            )->execute();

            $trans->commit();

            return $result
                ? $this->jsonResponse(true, 'Data Deleted successfully!')
                : $this->jsonResponse(false, 'Failed to delete sales order.');

        } catch (\Exception $e) {

            $trans->rollBack();

            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function savePosSale($post, $user_id)
    {
        if (empty($post['warehouse_id'])) {
            return $this->jsonResponse(false, 'Warehouse is required.');
        }

        $items = $post['items'] ?? [];
        if (!is_array($items)) {
            $items = json_decode($items, true);
        }
        if (empty($items)) {
            return $this->jsonResponse(false, 'At least one item is required.');
        }

        $trans = Yii::$app->db->beginTransaction();

        try {

            $warehouse_id = $post['warehouse_id'];
            $customer_id = !empty($post['customer_id']) ? $post['customer_id'] : null;

            // Handle Walk-in customer creation
            if ($post['customer_type'] ?? null === 'Walk-in') {
                $customer_name = $post['customer_name'] ?? 'Walk-in Customer';
                $customer_email = $post['customer_email'] ?? null;
                $customer_phone = $post['customer_phone'] ?? null;
                $customer_reference = $post['customer_reference'] ?? null;

                Yii::$app->db->createCommand()->insert(
                    'inventory_customers',
                    [
                        'customer_code' => 'WLK-' . date('YmdHis'),
                        'customer_type' => 'Walk-in',
                        'first_name' => $customer_name,
                        'last_name' => '',
                        'email' => $customer_email,
                        'phone' => $customer_phone,
                        'remarks' => $customer_reference,
                        'is_active' => 1,
                        'is_deleted' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                )->execute();

                $customer_id = Yii::$app->db->getLastInsertID();
            }

            $subtotal = 0;
            $discountTotal = 0;
            $taxTotal = 0;
            $lineItems = [];

            foreach ($items as $item) {

                if (empty($item['product_id']) || empty($item['quantity'])) {
                    continue;
                }

                $product = Yii::$app->db->createCommand("
                    SELECT id,product_name,sku
                    FROM inventory_products
                    WHERE id=:id
                ")->bindValue(':id', $item['product_id'])->queryOne();

                $qty = (float)$item['quantity'];
                $price = (float)($item['unit_price'] ?? 0);
                $disc = (float)($item['discount'] ?? 0);
                $tax = (float)($item['tax'] ?? 0);
                $total = ($qty * $price) - $disc + $tax;

                $subtotal += $qty * $price;
                $discountTotal += $disc;
                $taxTotal += $tax;

                $lineItems[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $product['product_name'] ?? null,
                    'sku' => $product['sku'] ?? null,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount' => $disc,
                    'tax' => $tax,
                    'total' => $total
                ];

                // POS sales are completed instantly, so deduct real stock right away.
                $stock = Yii::$app->db->createCommand("
                    SELECT *
                    FROM inventory_stock
                    WHERE warehouse_id=:warehouse
                    AND product_id=:product
                    AND is_deleted=0
                ")->bindValues([
                    ':warehouse' => $warehouse_id,
                    ':product' => $item['product_id']
                ])->queryOne();

                $newQty = $stock ? $stock['quantity'] - $qty : -$qty;
                $reserved = $stock ? $stock['reserved_quantity'] : 0;

                if ($stock) {

                    Yii::$app->db->createCommand()->update(
                        'inventory_stock',
                        [
                            'quantity' => $newQty,
                            'available_quantity' => $newQty - $reserved,
                            'updated_by' => $user_id
                        ],
                        ['id' => $stock['id']]
                    )->execute();
                } else {

                    Yii::$app->db->createCommand()->insert(
                        'inventory_stock',
                        [
                            'warehouse_id' => $warehouse_id,
                            'product_id' => $item['product_id'],
                            'quantity' => $newQty,
                            'reserved_quantity' => 0,
                            'available_quantity' => $newQty,
                            'created_by' => $user_id,
                            'updated_by' => $user_id
                        ]
                    )->execute();
                }
            }

            $grandTotal = $subtotal - $discountTotal + $taxTotal;
            $paidAmount = (float)($post['paid_amount'] ?? $grandTotal);
            $remainingBalance = $grandTotal - $paidAmount;
            $changeAmount = max(0, $paidAmount - $grandTotal);
            $posNo = $this->generateDocNo('POS');

            // Determine payment status
            $paymentStatus = 'Unpaid';
            if ($paidAmount >= $grandTotal) {
                $paymentStatus = 'Paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'Partial';
            }

            Yii::$app->db->createCommand()->insert(
                'inventory_pos_sales',
                [
                    'pos_no' => $posNo,
                    'customer_id' => $customer_id,
                    'warehouse_id' => $warehouse_id,
                    'sale_date' => date('Y-m-d H:i:s'),
                    'items' => json_encode($lineItems),
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountTotal,
                    'tax_amount' => $taxTotal,
                    'grand_total' => $grandTotal,
                    'paid_amount' => $paidAmount,
                    'remaining_balance' => $remainingBalance,
                    'change_amount' => $changeAmount,
                    'payment_method' => $post['payment_method'] ?? 'Cash',
                    'payment_status' => $paymentStatus,
                    'status' => 'Completed',
                    'remarks' => $post['remarks'] ?? null,
                    'created_by' => $user_id,
                    'updated_by' => $user_id
                ]
            )->execute();

            $posSaleId = Yii::$app->db->getLastInsertID();

            // If there's a payment, record it in payment history
            if ($paidAmount > 0) {
                Yii::$app->db->createCommand()->insert(
                    'inventory_pos_payment_history',
                    [
                        'pos_sales_id' => $posSaleId,
                        'paid_amount' => $paidAmount,
                        'payment_date' => date('Y-m-d'),
                        'payment_method' => $post['payment_method'] ?? 'Cash',
                        'remarks' => 'Initial payment',
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $user_id
                    ]
                )->execute();
            }

            Yii::$app->db->createCommand()->insert(
                'inventory_stock_movements',
                [
                    'movement_no' => $this->generateDocNo('MOV'),
                    'warehouse_id' => $warehouse_id,
                    'product_id' => $lineItems[0]['product_id'] ?? null,
                    'reference_type' => 'Sale',
                    'reference_id' => $posSaleId,
                    'movement_type' => 'OUT',
                    'quantity' => $subtotal > 0 ? array_sum(array_column($lineItems, 'quantity')) : 0,
                    'unit_cost' => 0,
                    'total_cost' => $grandTotal,
                    'remarks' => 'POS Sale ' . $posNo,
                    'movement_date' => date('Y-m-d H:i:s'),
                    'created_by' => $user_id,
                    'updated_by' => $user_id
                ]
            )->execute();

            // Create Sale Invoice automatically from POS Sale
            $invoiceNo = $this->createSaleInvoiceFromPos(
                $posSaleId,
                $customer_id,
                $subtotal,
                $discountTotal,
                $taxTotal,
                $grandTotal,
                $paidAmount,
                $user_id
            );

            $trans->commit();

            $message = 'POS Sale ' . $posNo . ' and Invoice ' . $invoiceNo . ' created successfully!';
            return $this->jsonResponse(true, $message, [
                'id' => $posSaleId,
                'pos_no' => $posNo,
                'invoice_no' => $invoiceNo,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount
            ]);

        } catch (\Exception $e) {

            $trans->rollBack();

            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    private function deletePosSale($id, $user_id)
    {
        $trans = Yii::$app->db->beginTransaction();

        try {

            $sale = Yii::$app->db->createCommand("
                SELECT *
                FROM inventory_pos_sales
                WHERE id=:id
            ")->bindValue(':id', $id)->queryOne();

            if (!$sale) {
                return $this->jsonResponse(false, 'POS Sale not found.');
            }

            $items = json_decode($sale['items'] ?? '[]', true);

            foreach ($items as $item) {

                $stock = Yii::$app->db->createCommand("
                    SELECT *
                    FROM inventory_stock
                    WHERE warehouse_id=:warehouse
                    AND product_id=:product
                    AND is_deleted=0
                ")->bindValues([
                    ':warehouse' => $sale['warehouse_id'],
                    ':product' => $item['product_id']
                ])->queryOne();

                if (!$stock) {
                    continue;
                }

                $newQty = $stock['quantity'] + $item['quantity'];

                Yii::$app->db->createCommand()->update(
                    'inventory_stock',
                    [
                        'quantity' => $newQty,
                        'available_quantity' => $newQty - $stock['reserved_quantity'],
                        'updated_by' => $user_id
                    ],
                    ['id' => $stock['id']]
                )->execute();
            }

            Yii::$app->db->createCommand()->update(
                'inventory_stock_movements',
                [
                    'is_deleted' => 1,
                    'updated_by' => $user_id
                ],
                [
                    'reference_id' => $id,
                    'reference_type' => 'Sale'
                ]
            )->execute();

            $result = Yii::$app->db->createCommand()->update(
                'inventory_pos_sales',
                [
                    'is_deleted' => 1,
                    'is_active' => 0,
                    'status' => 'Cancelled',
                    'updated_by' => $user_id,
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                ['id' => $id]
            )->execute();

            $trans->commit();

            return $result
                ? $this->jsonResponse(true, 'Data Deleted successfully!')
                : $this->jsonResponse(false, 'Failed to delete POS sale.');

        } catch (\Exception $e) {

            $trans->rollBack();

            return $this->jsonResponse(false, $e->getMessage());
        }
    }

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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (Yii::$app->session->has('user_array') == NULL) {
            $this->redirect(['site/index']);
            return false;
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    
    
    public function actionSalesdashboard()
    {
        if (Yii::$app->request->isGet) {
            return $this->renderPartial('salesdashboard');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {

            $post = Yii::$app->request->post();

            if (!isset($post['flag']) || $post['flag'] != 'load_dashboard') {
                return [
                    'success' => false,
                    'message' => 'Invalid request.'
                ];
            }

            $db = Yii::$app->db;

            $stats = [];

            $stats['total_sales_orders'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_sales_orders
                WHERE is_deleted=0
            ")->queryScalar();

            $stats['draft_sales_orders'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_sales_orders
                WHERE is_deleted=0
                AND order_status='Draft'
            ")->queryScalar();

            $stats['confirmed_sales_orders'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_sales_orders
                WHERE is_deleted=0
                AND order_status='Confirmed'
            ")->queryScalar();

            $stats['dispatched_sales_orders'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_sales_orders
                WHERE is_deleted=0
                AND order_status='Dispatched'
            ")->queryScalar();

            $stats['delivered_sales_orders'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_sales_orders
                WHERE is_deleted=0
                AND order_status='Delivered'
            ")->queryScalar();

            $stats['cancelled_sales_orders'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_sales_orders
                WHERE is_deleted=0
                AND order_status='Cancelled'
            ")->queryScalar();

            $stats['total_sales_value'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(grand_total),0)
                FROM inventory_sales_orders
                WHERE is_deleted=0
            ")->queryScalar();

            $stats['total_pos_sales'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_pos_sales
                WHERE is_deleted=0
            ")->queryScalar();

            $stats['total_pos_value'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(grand_total),0)
                FROM inventory_pos_sales
                WHERE is_deleted=0
            ")->queryScalar();

            $stats['total_invoices'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_sales_invoices
                WHERE is_deleted=0
            ")->queryScalar();

            $stats['unpaid_invoices'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_sales_invoices
                WHERE is_deleted=0
                AND status IN ('Unpaid','Partial')
            ")->queryScalar();

            $stats['unpaid_invoice_amount'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(grand_total),0)
                FROM inventory_sales_invoices
                WHERE is_deleted=0
                AND status IN ('Unpaid','Partial')
            ")->queryScalar();

            $stats['total_customers'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_customers
                WHERE is_deleted=0
                AND is_active=1
            ")->queryScalar();

            $stats['total_payment_received'] = (float)$db->createCommand("
                SELECT IFNULL(SUM(amount),0)
                FROM inventory_payments
                WHERE is_deleted=0
                AND reference_type='Sale'
            ")->queryScalar();

            $stats['total_sales_returns'] = (int)$db->createCommand("
                SELECT COUNT(*)
                FROM inventory_sales_returns
                WHERE is_deleted=0
            ")->queryScalar();

            $statusChart = $db->createCommand("
                SELECT
                    order_status,
                    COUNT(*) total
                FROM inventory_sales_orders
                WHERE is_deleted=0
                GROUP BY order_status
                ORDER BY total DESC
            ")->queryAll();

            $customerChart = $db->createCommand("
                SELECT
                    c.company_name,
                    c.first_name,
                    c.last_name,
                    IFNULL(SUM(so.grand_total),0) total
                FROM inventory_sales_orders so
                LEFT JOIN inventory_customers c
                    ON c.id=so.customer_id
                WHERE so.is_deleted=0
                GROUP BY so.customer_id,c.company_name,c.first_name,c.last_name
                ORDER BY total DESC
                LIMIT 10
            ")->queryAll();

            $monthlySales = $db->createCommand("
                SELECT
                    DATE_FORMAT(order_date,'%b %Y') month,
                    IFNULL(SUM(grand_total),0) total
                FROM inventory_sales_orders
                WHERE is_deleted=0
                GROUP BY YEAR(order_date),MONTH(order_date)
                ORDER BY YEAR(order_date),MONTH(order_date)
            ")->queryAll();

            $latestSalesOrders = $db->createCommand("
                SELECT
                    so.order_number,
                    c.company_name,
                    c.first_name,
                    c.last_name,
                    so.order_date,
                    so.order_status,
                    so.grand_total
                FROM inventory_sales_orders so
                LEFT JOIN inventory_customers c
                    ON c.id=so.customer_id
                WHERE so.is_deleted=0
                ORDER BY so.order_date DESC
                LIMIT 10
            ")->queryAll();

            $latestPosSales = $db->createCommand("
                SELECT
                    ps.pos_no,
                    c.company_name,
                    c.first_name,
                    c.last_name,
                    ps.sale_date,
                    ps.status,
                    ps.grand_total
                FROM inventory_pos_sales ps
                LEFT JOIN inventory_customers c
                    ON c.id=ps.customer_id
                WHERE ps.is_deleted=0
                ORDER BY ps.sale_date DESC
                LIMIT 10
            ")->queryAll();

            return [
                'success' => true,
                'stats' => $stats,
                'statusChart' => $statusChart,
                'customerChart' => $customerChart,
                'monthlySales' => $monthlySales,
                'latestSalesOrders' => $latestSalesOrders,
                'latestPosSales' => $latestPosSales
            ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSalesorders()
    {
        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {

                if (isset($post['flag']) && $post['flag'] == 'load') {

                    $page = max(1, (int)($post['page'] ?? 1));
                    $perPage = max(10, (int)($post['per_page'] ?? 20));
                    $offset = ($page - 1) * $perPage;

                    $customer_id = trim($post['customer_id'] ?? '');
                    $warehouse_id = trim($post['warehouse_id'] ?? '');
                    $order_status = trim($post['order_status'] ?? '');
                    $payment_status = trim($post['payment_status'] ?? '');
                    $order_number = trim($post['order_number'] ?? '');
                    $from_date = trim($post['from_date'] ?? '');
                    $to_date = trim($post['to_date'] ?? '');

                    $where = " WHERE so.is_deleted=0 ";
                    $params = [];

                    if ($customer_id != '') {
                        $where .= " AND so.customer_id=:customer_id ";
                        $params[':customer_id'] = $customer_id;
                    }

                    if ($warehouse_id != '') {
                        $where .= " AND so.warehouse_id=:warehouse_id ";
                        $params[':warehouse_id'] = $warehouse_id;
                    }

                    if ($order_status != '') {
                        $where .= " AND so.order_status=:order_status ";
                        $params[':order_status'] = $order_status;
                    }

                    if ($payment_status != '') {
                        $where .= " AND so.payment_status=:payment_status ";
                        $params[':payment_status'] = $payment_status;
                    }

                    if ($order_number != '') {
                        $where .= " AND so.order_number LIKE :order_number ";
                        $params[':order_number'] = '%' . $order_number . '%';
                    }

                    if ($from_date != '') {
                        $where .= " AND so.order_date>=:from_date ";
                        $params[':from_date'] = $from_date;
                    }

                    if ($to_date != '') {
                        $where .= " AND so.order_date<=:to_date ";
                        $params[':to_date'] = $to_date;
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_sales_orders so
                        {$where}
                    ", $params)->queryScalar();

                    $salesOrders = Yii::$app->db->createCommand("
                        SELECT
                            so.*,
                            c.company_name,
                            c.first_name,
                            c.last_name,
                            w.warehouse_name
                        FROM inventory_sales_orders so
                        LEFT JOIN inventory_customers c
                            ON c.id=so.customer_id
                        LEFT JOIN inventory_warehouses w
                            ON w.id=so.warehouse_id
                        {$where}
                        ORDER BY so.id DESC
                        LIMIT {$offset},{$perPage}
                    ", $params)->queryAll();

                    return [
                        'success' => true,
                        'salesOrders' => $salesOrders,
                        'page' => $page,
                        'perPage' => $perPage,
                        'total' => $total,
                        'totalPages' => ceil($total / $perPage)
                    ];
                }

                if (isset($post['flag']) && $post['flag'] == 'save') {

                    $db = Yii::$app->db;
                    $transaction = $db->beginTransaction();

                    try {

                        $id = (int)($post['id'] ?? 0);

                        if ($id > 0) {
                            // Check if order is confirmed - prevent updates
                            $existingOrder = $db->createCommand(
                                "SELECT order_status FROM inventory_sales_orders WHERE id = :id"
                            )->bindValue(':id', $id)->queryOne();

                            if ($existingOrder && $existingOrder['order_status'] === 'Confirmed') {
                                $transaction->rollBack();
                                return [
                                    'success' => false,
                                    'message' => 'This Sales Order is Confirmed and cannot be edited.'
                                ];
                            }
                        }

                        $data = [
                            'customer_id' => $post['customer_id'],
                            'warehouse_id' => $post['warehouse_id'],
                            'order_date' => $post['order_date'],
                            'delivery_date' => $post['delivery_date'] ?? null,
                            'order_status' => $post['order_status'],
                            'payment_status' => $post['payment_status'],
                            'subtotal' => $post['subtotal'],
                            'discount' => $post['discount'] ?? 0,
                            'tax' => $post['tax'] ?? 0,
                            'shipping' => $post['shipping'] ?? 0,
                            'grand_total' => $post['grand_total'],
                            'notes' => $post['notes'] ?? null,
                            'updated_at' => date('Y-m-d H:i:s')
                        ];

                        if ($id > 0) {

                            $db->createCommand()->update(
                                'inventory_sales_orders',
                                $data,
                                ['id' => $id]
                            )->execute();

                            $salesOrderId = $id;
                        } else {

                            $data['order_number'] = $this->generateDocNo('SO');
                            $data['created_at'] = date('Y-m-d H:i:s');
                            $data['is_active'] = 1;
                            $data['is_deleted'] = 0;

                            $db->createCommand()->insert(
                                'inventory_sales_orders',
                                $data
                            )->execute();

                            $salesOrderId = $db->getLastInsertID();
                        }

                        $transaction->commit();

                        return [
                            'success' => true,
                            'message' => 'Sales Order saved successfully.',
                            'id' => $salesOrderId
                        ];
                    } catch (\Exception $e) {

                        $transaction->rollBack();

                        return [
                            'success' => false,
                            'message' => $e->getMessage()
                        ];
                    }
                }

                if (isset($post['flag']) && $post['flag'] == 'updateStatus') {

                    $db = Yii::$app->db;
                    $orderId = (int)($post['id'] ?? 0);
                    $newStatus = trim($post['status'] ?? '');

                    if ($orderId <= 0 || empty($newStatus)) {
                        return ['success' => false, 'message' => 'Invalid Order ID or status.'];
                    }

                    // Update order status
                    $db->createCommand()->update(
                        'inventory_sales_orders',
                        ['order_status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')],
                        ['id' => $orderId]
                    )->execute();

                    return [
                        'success' => true,
                        'message' => 'Sale Order status updated to ' . $newStatus . ' successfully.'
                    ];
                }

                if (isset($post['flag']) && $post['flag'] == 'delete') {

                    Yii::$app->db->createCommand()->update(
                        'inventory_sales_orders',
                        [
                            'is_deleted' => 1,
                            'updated_at' => date('Y-m-d H:i:s')
                        ],
                        ['id' => $post['id']]
                    )->execute();

                    return [
                        'success' => true,
                        'message' => 'Sales Order deleted successfully.'
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Invalid request.'
                ];
            } catch (\Exception $e) {

                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        $customers = Yii::$app->db->createCommand("
            SELECT id,customer_code,customer_type,company_name,first_name,last_name
            FROM inventory_customers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY first_name
        ")->queryAll();

        $warehouses = Yii::$app->db->createCommand("
            SELECT id,warehouse_name
            FROM inventory_warehouses
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY warehouse_name
        ")->queryAll();

        $salesOrders = Yii::$app->db->createCommand("
            SELECT
                so.*,
                c.company_name,
                c.first_name,
                c.last_name,
                w.warehouse_name
            FROM inventory_sales_orders so
            LEFT JOIN inventory_customers c
                ON c.id=so.customer_id
            LEFT JOIN inventory_warehouses w
                ON w.id=so.warehouse_id
            WHERE so.is_deleted=0
            ORDER BY so.id DESC
            LIMIT 20
        ")->queryAll();

        return $this->renderPartial('salesorders', [
            'salesOrders' => $salesOrders,
            'customers' => $customers,
            'warehouses' => $warehouses
        ]);
    }

    public function actionSaleorder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->getQueryParam('flag') ?? Yii::$app->request->post('flag') ?? '';
        $db = Yii::$app->db;

        try {
            if ($post == 'get_products') {
                $warehouseId = Yii::$app->request->getQueryParam('warehouse_id');
                $products = $db->createCommand("
                    SELECT
                        p.id,
                        p.product_name,
                        p.sku,
                        p.selling_price,
                        COALESCE(s.available_quantity, 0) as available_quantity
                    FROM inventory_products p
                    LEFT JOIN inventory_stock s ON s.product_id=p.id AND s.warehouse_id=:warehouse_id
                    WHERE p.is_deleted=0 AND p.is_active=1 AND COALESCE(s.available_quantity, 0) > 0
                    ORDER BY p.product_name ASC
                ")->bindValue(':warehouse_id', $warehouseId)->queryAll();

                return ['success' => true, 'products' => $products];
            }

            if ($post == 'get_items') {
                $id = Yii::$app->request->getQueryParam('id');
                $items = $db->createCommand("
                    SELECT
                        i.product_id,
                        i.quantity,
                        i.unit_price,
                        i.discount,
                        i.tax,
                        i.total,
                        p.product_name,
                        p.sku,
                        s.available_quantity
                    FROM inventory_sales_order_items i
                    INNER JOIN inventory_products p ON p.id=i.product_id
                    LEFT JOIN inventory_stock s ON s.product_id=p.id
                    WHERE i.sales_order_id=:id AND i.is_deleted=0
                ")->bindValue(':id', $id)->queryAll();

                return ['success' => true, 'items' => $items];
            }

            if ($post == 'create' || $post == 'update') {
                $postData = Yii::$app->request->post();
                $id = $postData['id'] ?? '';
                $isEdit = !empty($id);
                $userId = $this->currentUserId();

                $customerId = $postData['customer_id'] ?? '';
                $customerName = $postData['customer_name'] ?? '';
                $customerEmail = $postData['customer_email'] ?? '';
                $customerPhone = $postData['customer_phone'] ?? '';
                $customerRef = $postData['customer_reference'] ?? '';
                $warehouseId = $postData['warehouse_id'] ?? '';
                $orderDate = $postData['order_date'] ?? date('Y-m-d');
                $deliveryDate = $postData['delivery_date'] ?? '';
                $orderStatus = $postData['order_status'] ?? 'Draft';
                $paymentStatus = $postData['payment_status'] ?? 'Pending';
                $discount = (float)($postData['discount'] ?? 0);
                $tax = (float)($postData['tax'] ?? 0);
                $shipping = (float)($postData['shipping'] ?? 0);
                $grandTotal = (float)($postData['grand_total'] ?? 0);
                $paidAmount = (float)($postData['paid_amount'] ?? 0);
                $notes = $postData['notes'] ?? '';
                $items = json_decode($postData['items'] ?? '[]', true);

                if (!$warehouseId || !$orderDate) {
                    return ['success' => false, 'message' => 'Warehouse and Order Date required'];
                }

                if (empty($items)) {
                    return ['success' => false, 'message' => 'Add at least one product'];
                }

                // Validate quantities against available stock
                foreach ($items as $item) {
                    $stock = $db->createCommand("
                        SELECT available_quantity FROM inventory_stock
                        WHERE product_id=:product_id AND warehouse_id=:warehouse_id
                    ")->bindValues([':product_id' => $item['product_id'], ':warehouse_id' => $warehouseId])->queryScalar();

                    if ($item['quantity'] > $stock) {
                        return ['success' => false, 'message' => "Insufficient stock for product ID {$item['product_id']}. Available: {$stock}"];
                    }
                }

                if ($isEdit) {
                    // Check if order is Completed - prevent updates
                    $order = $db->createCommand(
                        "SELECT order_status FROM inventory_sales_orders WHERE id = :id AND is_deleted = 0"
                    )->bindValue(':id', $id)->queryOne();

                    if ($order && $order['order_status'] === 'Completed') {
                        return ['success' => false, 'message' => 'Cannot update a Completed sales order. Please create a new order or contact admin.'];
                    }

                    // Update existing order
                    $remainingBalance = $grandTotal - $paidAmount;
                    $db->createCommand()->update('inventory_sales_orders', [
                        'customer_id' => $customerId ?: null,
                        'warehouse_id' => $warehouseId,
                        'order_date' => $orderDate,
                        'delivery_date' => $deliveryDate ?: null,
                        'order_status' => $orderStatus,
                        'payment_status' => $paymentStatus,
                        'subtotal' => $grandTotal - $tax - $shipping + $discount,
                        'discount' => $discount,
                        'tax' => $tax,
                        'shipping' => $shipping,
                        'grand_total' => $grandTotal,
                        'paid_amount' => $paidAmount,
                        'remaining_balance' => $remainingBalance,
                        'notes' => $notes,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => $userId
                    ], ['id' => $id])->execute();

                    // Delete and re-add items
                    $db->createCommand()->update('inventory_sales_order_items', ['is_deleted' => 1], ['sales_order_id' => $id])->execute();

                    foreach ($items as $item) {
                        $db->createCommand()->insert('inventory_sales_order_items', [
                            'sales_order_id' => $id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'discount' => $item['discount'],
                            'tax' => $item['tax'],
                            'total' => $item['total'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $userId,
                            'is_deleted' => 0
                        ])->execute();

                        // Update remaining quantity in stock
                        $this->updateRemainingQuantity($db, $item['product_id'], $warehouseId, $item['quantity']);
                    }

                    // Update or create invoice
                    $invoiceNumber = '';
                    try {
                        $this->ensureInvoiceTables($db);
                        $this->updateSalesInvoice($db, $id);
                        $invoiceData = $db->createCommand("
                            SELECT invoice_no FROM inventory_sales_invoices WHERE sales_order_id = :order_id AND is_deleted = 0 LIMIT 1
                        ")->bindValue(':order_id', $id)->queryOne();
                        $invoiceNumber = $invoiceData['invoice_no'] ?? '';
                    } catch (\Exception $invoiceError) {
                        \Yii::warning('Invoice update failed: ' . $invoiceError->getMessage());
                    }

                    $soData = $db->createCommand("SELECT order_number FROM inventory_sales_orders WHERE id = :id")->bindValue(':id', $id)->queryOne();
                    $message = 'Sale Order ' . ($soData['order_number'] ?? '') . ' updated successfully';
                    if ($invoiceNumber) {
                        $message .= ' | Invoice: ' . $invoiceNumber;
                    }
                    return ['success' => true, 'message' => $message, 'order_number' => $soData['order_number'] ?? '', 'invoice_no' => $invoiceNumber];
                } else {
                    // Create new order
                    $orderNumber = 'SO-' . date('YmdHis') . '-' . rand(100, 999);

                    $remainingBalance = $grandTotal - $paidAmount;
                    $db->createCommand()->insert('inventory_sales_orders', [
                        'order_number' => $orderNumber,
                        'customer_id' => $customerId ?: null,
                        'warehouse_id' => $warehouseId,
                        'order_date' => $orderDate,
                        'delivery_date' => $deliveryDate ?: null,
                        'order_status' => $orderStatus,
                        'payment_status' => $paymentStatus,
                        'subtotal' => $grandTotal - $tax - $shipping + $discount,
                        'discount' => $discount,
                        'tax' => $tax,
                        'shipping' => $shipping,
                        'grand_total' => $grandTotal,
                        'paid_amount' => $paidAmount,
                        'remaining_balance' => $remainingBalance,
                        'notes' => $notes,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $userId,
                        'is_deleted' => 0
                    ])->execute();

                    $orderId = $db->lastInsertID;

                    foreach ($items as $item) {
                        $db->createCommand()->insert('inventory_sales_order_items', [
                            'sales_order_id' => $orderId,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'discount' => $item['discount'],
                            'tax' => $item['tax'],
                            'total' => $item['total'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $userId,
                            'is_deleted' => 0
                        ])->execute();

                        // Update remaining quantity in stock
                        $this->updateRemainingQuantity($db, $item['product_id'], $warehouseId, $item['quantity']);
                    }

                    // Create invoice for the sales order
                    $invoiceNumber = '';
                    try {
                        $invoiceId = $this->createSalesInvoice($db, $orderId);
                        if ($invoiceId) {
                            $invoiceData = $db->createCommand("
                                SELECT invoice_no FROM inventory_sales_invoices WHERE id = :id
                            ")->bindValue(':id', $invoiceId)->queryOne();
                            $invoiceNumber = $invoiceData['invoice_no'] ?? '';
                        }
                    } catch (\Exception $invoiceError) {
                        \Yii::warning('Invoice creation failed: ' . $invoiceError->getMessage());
                    }

                    $message = 'Sale Order ' . $orderNumber . ' created successfully';
                    if ($invoiceNumber) {
                        $message .= ' | Invoice: ' . $invoiceNumber;
                    }
                    return ['success' => true, 'message' => $message, 'order_id' => $orderId, 'order_number' => $orderNumber, 'invoice_no' => $invoiceNumber];
                }
            }

            return ['success' => false, 'message' => 'Invalid request'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    } 
    private function createSalesInvoice($db, $salesOrderId)
    {
        $so = $db->createCommand("
            SELECT * FROM inventory_sales_orders WHERE id=:id
        ")->bindValue(':id', $salesOrderId)->queryOne();

        if (!$so) return false;

        // Check if invoice already exists - if so, return it
        $existingInvoice = $db->createCommand("
            SELECT id FROM inventory_sales_invoices
            WHERE sales_order_id = :order_id AND is_deleted = 0 LIMIT 1
        ")->bindValue(':order_id', $salesOrderId)->queryScalar();

        if ($existingInvoice) {
            return $existingInvoice;
        }

        // Get default sales account from settings
        $accountId = $db->createCommand(
            "SELECT setting_value FROM inventory_settings WHERE setting_key='default_sales_account' AND is_deleted=0"
        )->queryScalar();

        $invoiceNumber = 'INV-' . date('YmdHis') . '-' . rand(100, 999);

        $invoiceData = [
            'invoice_no' => $invoiceNumber,
            'sales_order_id' => $salesOrderId,
            'customer_id' => $so['customer_id'] ?: null,
            'warehouse_id' => $so['warehouse_id'],
            'account_id' => $accountId ?: null,
            'invoice_date' => $so['order_date'],
            'due_date' => date('Y-m-d', strtotime($so['order_date'] . ' + 30 days')),
            'subtotal' => $so['subtotal'],
            'discount' => $so['discount'],
            'tax' => $so['tax'],
            'shipping' => $so['shipping'],
            'grand_total' => $so['grand_total'],
            'paid_amount' => $so['paid_amount'],
            'remaining_balance' => $so['remaining_balance'],
            'status' => $this->getInvoiceStatus($so['paid_amount'], $so['grand_total']),
            'notes' => $so['notes'],
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $this->currentUserId(),
            'is_deleted' => 0
        ];

        $db->createCommand()->insert('inventory_sales_invoices', $invoiceData)->execute();
        $invoiceId = $db->getLastInsertID();

        // Copy SO items to invoice items
        $soItems = $db->createCommand("
            SELECT * FROM inventory_sales_order_items
            WHERE sales_order_id=:id AND is_deleted=0
        ")->bindValue(':id', $salesOrderId)->queryAll();

        foreach ($soItems as $item) {
            $invoiceItemData = [
                'sales_invoice_id' => $invoiceId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'],
                'tax' => $item['tax'],
                'total' => $item['total'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'is_deleted' => 0
            ];
            $db->createCommand()->insert('inventory_sales_invoice_items', $invoiceItemData)->execute();
        }

        // Create payment history if there's an initial payment
        $userId = $this->currentUserId();
        if ($so['paid_amount'] > 0) {
            $this->recordInvoicePayment($invoiceId, $so['paid_amount'], 0, 'Initial Payment - Sales Order', $userId);
        }

        // Auto-update sales order and invoice status if fully paid
        if ($so['remaining_balance'] <= 0) {
            $db->createCommand()->update(
                'inventory_sales_orders',
                ['order_status' => 'Completed', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $userId],
                ['id' => $salesOrderId]
            )->execute();

            $db->createCommand()->update(
                'inventory_sales_invoices',
                ['status' => 'Paid', 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => $invoiceId]
            )->execute();
        }

        return $invoiceId;
    }

    private function updateSalesInvoice($db, $salesOrderId)
    {
        $so = $db->createCommand("
            SELECT * FROM inventory_sales_orders WHERE id=:id
        ")->bindValue(':id', $salesOrderId)->queryOne();

        if (!$so) return false;

        // Get existing invoice
        $existingInvoice = $db->createCommand("
            SELECT id FROM inventory_sales_invoices
            WHERE sales_order_id = :order_id AND is_deleted = 0
        ")->bindValue(':order_id', $salesOrderId)->queryScalar();

        if ($existingInvoice) {
            // Update invoice header
            $db->createCommand()->update('inventory_sales_invoices', [
                'customer_id' => $so['customer_id'] ?: null,
                'warehouse_id' => $so['warehouse_id'],
                'invoice_date' => $so['order_date'],
                'due_date' => date('Y-m-d', strtotime($so['order_date'] . ' + 30 days')),
                'subtotal' => $so['subtotal'],
                'discount' => $so['discount'],
                'tax' => $so['tax'],
                'shipping' => $so['shipping'],
                'grand_total' => $so['grand_total'],
                'paid_amount' => $so['paid_amount'],
                'remaining_balance' => $so['remaining_balance'],
                'status' => $this->getInvoiceStatus($so['paid_amount'], $so['grand_total']),
                'notes' => $so['notes'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->currentUserId()
            ], ['id' => $existingInvoice])->execute();

            // Delete and re-add invoice items
            $db->createCommand()->update('inventory_sales_invoice_items', ['is_deleted' => 1], ['sales_invoice_id' => $existingInvoice])->execute();

            // Copy SO items to invoice items
            $soItems = $db->createCommand("
                SELECT * FROM inventory_sales_order_items
                WHERE sales_order_id=:id AND is_deleted=0
            ")->bindValue(':id', $salesOrderId)->queryAll();

            foreach ($soItems as $item) {
                $invoiceItemData = [
                    'sales_invoice_id' => $existingInvoice,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'],
                    'tax' => $item['tax'],
                    'total' => $item['total'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'is_deleted' => 0
                ];
                $db->createCommand()->insert('inventory_sales_invoice_items', $invoiceItemData)->execute();
            }

            // Auto-update sales order status if fully paid
            $userId = $this->currentUserId();
            if ($so['remaining_balance'] <= 0) {
                $db->createCommand()->update(
                    'inventory_sales_orders',
                    ['order_status' => 'Completed', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $userId],
                    ['id' => $salesOrderId]
                )->execute();
            }

            return $existingInvoice;
        }

        return false;
    }

    private function getInvoiceStatus($paidAmount, $grandTotal)
    {
        if ($paidAmount >= $grandTotal) {
            return 'Paid';
        } elseif ($paidAmount > 0) {
            return 'Partially Paid';
        }
        return 'Draft';
    }

    private function updateRemainingQuantity($db, $productId, $warehouseId, $quantity)
    {
        // Get current stock for the product in the warehouse
        $stock = $db->createCommand("
            SELECT id, available_quantity FROM inventory_stock
            WHERE product_id = :product_id AND warehouse_id = :warehouse_id
            LIMIT 1
        ")->bindValues([':product_id' => $productId, ':warehouse_id' => $warehouseId])->queryOne();

        if ($stock) {
            // Update remaining quantity by subtracting the order quantity
            $newAvailableQty = max(0, (float)$stock['available_quantity'] - (float)$quantity);
            $db->createCommand()->update(
                'inventory_stock',
                ['available_quantity' => $newAvailableQty, 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => $stock['id']]
            )->execute();
        }
    }

    public function actionCreatesale()
    {
        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();
            $flag = $post['flag'] ?? '';
            $user_id = $this->currentUserId();
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {

                if ($flag == 'search') {

                    $limit = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                    $page = !empty($post['page']) ? (int)$post['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    $where = "so.is_deleted=0";
                    $params = [];

                    if (!empty($post['customer_id'])) {
                        $where .= " AND so.customer_id=:customer_id";
                        $params[':customer_id'] = $post['customer_id'];
                    }

                    if (!empty($post['warehouse_id'])) {
                        $where .= " AND so.warehouse_id=:warehouse_id";
                        $params[':warehouse_id'] = $post['warehouse_id'];
                    }

                    if (!empty($post['keyword'])) {
                        $where .= " AND so.order_number LIKE :keyword";
                        $params[':keyword'] = '%' . $post['keyword'] . '%';
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_sales_orders so
                        WHERE $where
                    ")->bindValues($params)->queryScalar();

                    $rows = Yii::$app->db->createCommand("
                        SELECT
                            so.*,
                            c.company_name,
                            c.first_name,
                            c.last_name,
                            w.warehouse_name,
                            COALESCE(si.paid_amount, 0) as paid_amount
                        FROM inventory_sales_orders so
                        INNER JOIN inventory_customers c
                            ON c.id=so.customer_id
                        INNER JOIN inventory_warehouses w
                            ON w.id=so.warehouse_id
                        LEFT JOIN inventory_sales_invoices si
                            ON si.sales_order_id=so.id AND si.is_deleted=0
                        WHERE $where
                        ORDER BY so.id DESC
                        LIMIT $limit OFFSET $offset
                    ")->bindValues($params)->queryAll();

                    if (isset($post['id']) && $post['id'] != '') {

                        $items = Yii::$app->db->createCommand("
                            SELECT
                                i.product_id,
                                i.quantity,
                                i.unit_price,
                                i.discount,
                                i.tax,
                                i.total,
                                p.product_name,
                                p.sku
                            FROM inventory_sales_order_items i
                            INNER JOIN inventory_products p
                                ON p.id=i.product_id
                            WHERE i.sales_order_id=:id
                            AND i.is_deleted=0
                        ")->bindValue(':id', $post['id'])->queryAll();

                        return $this->jsonResponse(true, 'Data loaded successfully!', [
                            'data' => $rows,
                            'items' => $items,
                            'total' => (int)$total
                        ]);
                    }

                    return $this->jsonResponse(true, 'Data loaded successfully!', [
                        'data' => $rows,
                        'total' => (int)$total,
                        'page' => $page,
                        'limit' => $limit
                    ]);
                }

                if ($flag == 'create') {

                    return $this->saveSalesOrder($post, $user_id);
                }

                if ($flag == 'update') {

                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }

                    // Check if order is Completed - prevent updates
                    $order = Yii::$app->db->createCommand(
                        "SELECT order_status FROM inventory_sales_orders WHERE id = :id AND is_deleted = 0"
                    )->bindValue(':id', $post['id'])->queryOne();

                    if ($order && $order['order_status'] === 'Completed') {
                        return $this->jsonResponse(false, 'Cannot update a Completed sales order. Please create a new order or contact admin.');
                    }

                    return $this->saveSalesOrder($post, $user_id, $post['id']);
                }

                if ($flag == 'delete') {

                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }

                    // Check if order is Completed - prevent deletion
                    $order = Yii::$app->db->createCommand(
                        "SELECT order_status FROM inventory_sales_orders WHERE id = :id AND is_deleted = 0"
                    )->bindValue(':id', $post['id'])->queryOne();

                    if ($order && $order['order_status'] === 'Completed') {
                        return $this->jsonResponse(false, 'Cannot delete a Completed sales order. Please contact admin if needed.');
                    }

                    return $this->deleteSalesOrder($post['id'], $user_id);
                }

                return $this->jsonResponse(false, 'Invalid request flag.');
            } catch (\Exception $e) {

                return $this->jsonResponse(false, $e->getMessage());
            }
        }

        $customers = Yii::$app->db->createCommand("
            SELECT id,customer_code,customer_type,company_name,first_name,last_name
            FROM inventory_customers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY first_name
        ")->queryAll();

        $warehouses = Yii::$app->db->createCommand("
            SELECT id,warehouse_name
            FROM inventory_warehouses
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY warehouse_name
        ")->queryAll();

        $products = Yii::$app->db->createCommand("
            SELECT id,product_name,sku,selling_price
            FROM inventory_products
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY product_name
        ")->queryAll();

        return $this->renderPartial('createsale', [
            'customers' => $customers,
            'warehouses' => $warehouses,
            'products' => $products
        ]);
    }

    public function actionPossales()
    {
        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();
            $flag = $post['flag'] ?? '';
            $user_id = $this->currentUserId();
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {

                if ($flag == 'search') {

                    $limit = !empty($post['per_page']) ? (int)$post['per_page'] : 10;
                    $page = !empty($post['page']) ? (int)$post['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    $where = "ps.is_deleted=0";
                    $params = [];

                    if (!empty($post['warehouse_id'])) {
                        $where .= " AND ps.warehouse_id=:warehouse_id";
                        $params[':warehouse_id'] = $post['warehouse_id'];
                    }

                    if (!empty($post['customer_id'])) {
                        $where .= " AND ps.customer_id=:customer_id";
                        $params[':customer_id'] = $post['customer_id'];
                    }

                    if (!empty($post['keyword'])) {
                        $where .= " AND ps.pos_no LIKE :keyword";
                        $params[':keyword'] = '%' . $post['keyword'] . '%';
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_pos_sales ps
                        WHERE $where
                    ")->bindValues($params)->queryScalar();

                    $rows = Yii::$app->db->createCommand("
                        SELECT
                            ps.*,
                            c.company_name,
                            c.first_name,
                            c.last_name,
                            w.warehouse_name
                        FROM inventory_pos_sales ps
                        LEFT JOIN inventory_customers c
                            ON c.id=ps.customer_id
                        INNER JOIN inventory_warehouses w
                            ON w.id=ps.warehouse_id
                        WHERE $where
                        ORDER BY ps.id DESC
                        LIMIT $limit OFFSET $offset
                    ")->bindValues($params)->queryAll();

                    foreach ($rows as &$row) {
                        $row['items'] = json_decode($row['items'] ?? '[]', true);
                    }
                    unset($row);

                    return $this->jsonResponse(true, 'Data loaded successfully!', [
                        'data' => $rows,
                        'total' => (int)$total,
                        'page' => $page,
                        'limit' => $limit
                    ]);
                }

                if ($flag == 'create') {

                    return $this->savePosSale($post, $user_id);
                }

                if ($flag == 'delete') {

                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }

                    return $this->deletePosSale($post['id'], $user_id);
                }

                if ($flag == 'update' || $flag == 'update_full') {

                    if (empty($post['id'])) {
                        return $this->jsonResponse(false, 'Record id is required.');
                    }

                    $db = Yii::$app->db;
                    $id = (int)$post['id'];

                    // Get current POS sale
                    $pos = $db->createCommand("SELECT * FROM inventory_pos_sales WHERE id = :id AND is_deleted = 0", [':id' => $id])->queryOne();

                    if (!$pos) {
                        return $this->jsonResponse(false, 'POS Sale not found.');
                    }

                    // Handle update_full flag (comprehensive edit)
                    if ($flag == 'update_full') {
                        $warehouse_id = (int)($post['warehouse_id'] ?? 0);
                        $customer_id = !empty($post['customer_id']) ? (int)$post['customer_id'] : null;
                        $payment_method = trim($post['payment_method'] ?? 'Cash');
                        $items = $post['items'] ?? '[]';

                        // Calculate totals from items
                        $itemsArray = json_decode($items, true) ?? [];
                        $subtotal = 0;
                        $total_discount = 0;
                        $total_tax = 0;

                        foreach ($itemsArray as $item) {
                            $qty = (float)($item['quantity'] ?? 0);
                            $rate = (float)($item['unit_price'] ?? 0);
                            $discount = (float)($item['discount'] ?? 0);
                            $tax = (float)($item['tax'] ?? 0);

                            $subtotal += $qty * $rate;
                            $total_discount += $discount;
                            $total_tax += $tax;
                        }

                        $grand_total = $subtotal - $total_discount + $total_tax;
                    } else {
                        // Simple update - keep existing values
                        $warehouse_id = $pos['warehouse_id'];
                        $customer_id = $pos['customer_id'];
                        $payment_method = $pos['payment_method'];
                        $items = $pos['items'];
                        $subtotal = (float)$pos['subtotal'];
                        $total_discount = (float)$pos['discount_amount'];
                        $total_tax = (float)$pos['tax_amount'];
                        $grand_total = (float)$pos['grand_total'];
                    }

                    $paid_amount = (float)($post['paid_amount'] ?? 0);
                    $remaining_balance = $grand_total - $paid_amount;
                    $status = ($paid_amount >= $grand_total) ? 'Paid' : (($paid_amount > 0) ? 'Partial' : 'Unpaid');
                    $remarks = trim($post['remarks'] ?? '');

                    // Update POS sale
                    $db->createCommand("
                        UPDATE inventory_pos_sales
                        SET warehouse_id = :warehouse_id,
                            customer_id = :customer_id,
                            payment_method = :payment_method,
                            items = :items,
                            subtotal = :subtotal,
                            discount_amount = :discount_amount,
                            tax_amount = :tax_amount,
                            grand_total = :grand_total,
                            paid_amount = :paid_amount,
                            remaining_balance = :remaining_balance,
                            remarks = :remarks,
                            updated_at = NOW(),
                            updated_by = :user_id
                        WHERE id = :id
                    ", [
                        ':warehouse_id' => $warehouse_id,
                        ':customer_id' => $customer_id,
                        ':payment_method' => $payment_method,
                        ':items' => $items,
                        ':subtotal' => $subtotal,
                        ':discount_amount' => $total_discount,
                        ':tax_amount' => $total_tax,
                        ':grand_total' => $grand_total,
                        ':paid_amount' => $paid_amount,
                        ':remaining_balance' => $remaining_balance,
                        ':remarks' => $remarks,
                        ':id' => $id,
                        ':user_id' => $user_id
                    ])->execute();

                    // Update linked Sale Invoice by searching for POS number in remarks
                    $invoice = $db->createCommand("
                        SELECT id FROM inventory_sales_invoices
                        WHERE remarks LIKE :pos_no AND is_deleted = 0
                        ORDER BY id DESC LIMIT 1
                    ", [':pos_no' => '%' . $pos['pos_no'] . '%'])->queryOne();

                    if ($invoice) {
                        $db->createCommand("
                            UPDATE inventory_sales_invoices
                            SET subtotal = :subtotal,
                                discount_amount = :discount_amount,
                                tax_amount = :tax_amount,
                                grand_total = :grand_total,
                                paid_amount = :paid_amount,
                                remaining_balance = :remaining_balance,
                                status = :status,
                                updated_at = NOW(),
                                updated_by = :user_id
                            WHERE id = :id
                        ", [
                            ':subtotal' => $subtotal,
                            ':discount_amount' => $total_discount,
                            ':tax_amount' => $total_tax,
                            ':grand_total' => $grand_total,
                            ':paid_amount' => $paid_amount,
                            ':remaining_balance' => $remaining_balance,
                            ':status' => $status,
                            ':id' => $invoice['id'],
                            ':user_id' => $user_id
                        ])->execute();
                    }

                    return $this->jsonResponse(true, 'POS Sale updated successfully!', [
                        'id' => $id,
                        'paid_amount' => $paid_amount,
                        'remaining_balance' => $remaining_balance
                    ]);
                }

                return $this->jsonResponse(false, 'Invalid request flag.');
            } catch (\Exception $e) {

                return $this->jsonResponse(false, $e->getMessage());
            }
        }

        $customers = Yii::$app->db->createCommand("
            SELECT id,customer_code,customer_type,company_name,first_name,last_name
            FROM inventory_customers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY first_name
        ")->queryAll();

        $warehouses = Yii::$app->db->createCommand("
            SELECT id,warehouse_name
            FROM inventory_warehouses
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY warehouse_name
        ")->queryAll();

        $products = Yii::$app->db->createCommand("
            SELECT id,product_name,sku,selling_price
            FROM inventory_products
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY product_name
        ")->queryAll();

        return $this->renderPartial('possales', [
            'customers' => $customers,
            'warehouses' => $warehouses,
            'products' => $products
        ]);
    }

    public function actionSalesinvoices()
    {
        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {

                if (isset($post['flag']) && $post['flag'] == 'load') {

                    $page = max(1, (int)($post['page'] ?? 1));
                    $perPage = max(10, (int)($post['per_page'] ?? 20));
                    $offset = ($page - 1) * $perPage;

                    $customer_id = trim($post['customer_id'] ?? '');
                    $sales_order_id = trim($post['sales_order_id'] ?? '');
                    $status = trim($post['status'] ?? '');
                    $invoice_no = trim($post['invoice_no'] ?? '');
                    $from_date = trim($post['from_date'] ?? '');
                    $to_date = trim($post['to_date'] ?? '');

                    $where = " WHERE si.is_deleted=0 ";
                    $params = [];

                    if ($customer_id != '') {
                        $where .= " AND si.customer_id=:customer_id ";
                        $params[':customer_id'] = $customer_id;
                    }

                    if ($sales_order_id != '') {
                        $where .= " AND si.sales_order_id=:sales_order_id ";
                        $params[':sales_order_id'] = $sales_order_id;
                    }

                    if ($status != '') {
                        $where .= " AND si.status=:status ";
                        $params[':status'] = $status;
                    }

                    if ($invoice_no != '') {
                        $where .= " AND si.invoice_no LIKE :invoice_no ";
                        $params[':invoice_no'] = '%' . $invoice_no . '%';
                    }

                    if ($from_date != '') {
                        $where .= " AND si.invoice_date>=:from_date ";
                        $params[':from_date'] = $from_date;
                    }

                    if ($to_date != '') {
                        $where .= " AND si.invoice_date<=:to_date ";
                        $params[':to_date'] = $to_date;
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_sales_invoices si
                        {$where}
                    ", $params)->queryScalar();

                    $salesInvoices = Yii::$app->db->createCommand("
                        SELECT
                            si.*,
                            so.order_number,
                            so.order_status,
                            so.payment_status,
                            c.company_name,
                            c.first_name,
                            c.last_name
                        FROM inventory_sales_invoices si
                        LEFT JOIN inventory_sales_orders so
                            ON so.id=si.sales_order_id
                        LEFT JOIN inventory_customers c
                            ON c.id=si.customer_id
                        {$where}
                        ORDER BY si.id DESC
                        LIMIT {$offset},{$perPage}
                    ", $params)->queryAll();

                    return [
                        'success' => true,
                        'salesInvoices' => $salesInvoices,
                        'page' => $page,
                        'perPage' => $perPage,
                        'total' => $total,
                        'totalPages' => ceil($total / $perPage)
                    ];
                }

                // Get invoice data for sales order modal
                if (isset($post['flag']) && $post['flag'] == 'get_invoice') {
                    $salesOrderId = (int)($post['sales_order_id'] ?? 0);

                    if ($salesOrderId > 0) {
                        $invoice = Yii::$app->db->createCommand(
                            "SELECT id, invoice_no, status, paid_amount, remaining_balance, grand_total, subtotal, discount, tax FROM inventory_sales_invoices WHERE sales_order_id = :sales_order_id AND is_deleted = 0 ORDER BY created_at DESC LIMIT 1"
                        )->bindValue(':sales_order_id', $salesOrderId)->queryOne();

                        if ($invoice) {
                            return [
                                'success' => true,
                                'invoice' => $invoice,
                                'message' => 'Invoice data loaded successfully'
                            ];
                        }
                    }

                    return [
                        'success' => false,
                        'message' => 'No invoice found for this sales order'
                    ];
                }

                if (isset($post['flag']) && $post['flag'] == 'save') {

                    $db = Yii::$app->db;
                    $transaction = $db->beginTransaction();

                    try {

                        $id = (int)($post['id'] ?? 0);
                        $paidAmount = (float)($post['paid_amount'] ?? 0);

                        if ($id > 0) {

                            // Get the old invoice data for validation (on UPDATE)
                            $oldInvoice = $db->createCommand(
                                "SELECT id, status, paid_amount, remaining_balance, grand_total, sales_order_id, invoice_no FROM inventory_sales_invoices WHERE id = :id"
                            )->bindValue(':id', $id)->queryOne();

                            // Use database grand_total for UPDATE, form grand_total for CREATE
                            $grandTotal = (float)($oldInvoice['grand_total'] ?? 0);

                            // Check if invoice is Paid - prevent updates
                            if ($oldInvoice && $oldInvoice['status'] === 'Paid') {
                                throw new \Exception('Cannot update a Paid invoice. Please create a new invoice or contact admin.');
                            }

                            $oldPaidAmount = (float)($oldInvoice['paid_amount'] ?? 0);
                            $existingRemainingBalance = (float)($oldInvoice['remaining_balance'] ?? 0);
                            $dbGrandTotal = (float)($oldInvoice['grand_total'] ?? 0);

                            // VALIDATION 1: Previously paid amount cannot decrease
                            if ($paidAmount < $oldPaidAmount) {
                                throw new \Exception('Error: Previously paid amount cannot be decreased. Current paid: ' . number_format($oldPaidAmount, 2) . '. You cannot reduce it to ' . number_format($paidAmount, 2));
                            }

                            // VALIDATION 2: Paid amount cannot exceed grand total
                            if ($paidAmount > $dbGrandTotal) {
                                throw new \Exception('Error: Paid amount (' . number_format($paidAmount, 2) . ') cannot exceed invoice total (' . number_format($dbGrandTotal, 2) . ')');
                            }

                            // VALIDATION 3: Remaining balance cannot be negative
                            $calculatedRemaining = $dbGrandTotal - $paidAmount;
                            if ($calculatedRemaining < 0) {
                                throw new \Exception('Error: Remaining balance cannot be negative. Balance cannot go below 0');
                            }

                            // VALIDATION 4: Remaining balance must not be greater than existing balance
                            if ($calculatedRemaining > $existingRemainingBalance) {
                                throw new \Exception('Error: Remaining balance cannot increase. Current remaining: ' . number_format($existingRemainingBalance, 2) . '. New remaining: ' . number_format($calculatedRemaining, 2) . '. The total paid must only increase or stay the same');
                            }

                            // UPDATE: Only update payment-related fields on update
                            $updateData = [
                                'paid_amount' => $paidAmount,
                                'remaining_balance' => $calculatedRemaining,
                                'status' => ($paidAmount >= $dbGrandTotal) ? 'Paid' : (($paidAmount > 0) ? 'Partially Paid' : 'Unpaid'),
                                'notes' => $post['notes'] ?? $post['remarks'] ?? null,
                                'updated_at' => date('Y-m-d H:i:s')
                            ];

                            $db->createCommand()->update(
                                'inventory_sales_invoices',
                                $updateData,
                                ['id' => $id]
                            )->execute();

                            // If paid amount changed, create a payment history record for the difference
                            $paymentDifference = $paidAmount - $oldPaidAmount;
                            $this->recordInvoicePayment($id, $paidAmount, $oldPaidAmount, 'Partial Payment - Invoice Update');

                            // Post payment to GL if amount changed
                            if ($paymentDifference > 0) {
                                $this->postSalePaymentToGL($oldInvoice['sales_order_id'], $oldInvoice['invoice_no'], $paymentDifference, $this->currentUserId());
                            }

                            // Auto-update sales order paid amount and order status
                            if (!empty($oldInvoice['sales_order_id'])) {
                                $updateSalesOrderData = [
                                    'paid_amount' => $paidAmount,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => $this->currentUserId()
                                ];

                                // Auto-set order status to Completed if fully paid
                                if ($calculatedRemaining <= 0) {
                                    $updateSalesOrderData['order_status'] = 'Completed';
                                }

                                $db->createCommand()->update(
                                    'inventory_sales_orders',
                                    $updateSalesOrderData,
                                    ['id' => $oldInvoice['sales_order_id']]
                                )->execute();
                            }

                            $invoiceId = $id;
                        } else {

                            // CREATE: Get grand_total from form for new invoice
                            $grandTotal = (float)($post['grand_total'] ?? 0);
                            $remainingBalance = $grandTotal - $paidAmount;

                            // CREATE: Build data array for new invoice
                            $data = [
                                'sales_order_id' => $post['sales_order_id'],
                                'customer_id' => $post['customer_id'],
                                'invoice_date' => $post['invoice_date'],
                                'due_date' => $post['due_date'],
                                'subtotal' => $post['subtotal'],
                                'discount' => $post['discount'] ?? $post['discount_amount'] ?? 0,
                                'tax' => $post['tax'] ?? $post['tax_amount'] ?? 0,
                                'grand_total' => $grandTotal,
                                'paid_amount' => $paidAmount,
                                'remaining_balance' => $remainingBalance,
                                'status' => ($paidAmount >= $grandTotal) ? 'Paid' : (($paidAmount > 0) ? 'Partially Paid' : 'Unpaid'),
                                'notes' => $post['notes'] ?? $post['remarks'] ?? null,
                                'invoice_no' => $this->generateDocNo('SINV'),
                                'created_at' => date('Y-m-d H:i:s'),
                                'is_active' => 1,
                                'is_deleted' => 0
                            ];

                            $db->createCommand()->insert(
                                'inventory_sales_invoices',
                                $data
                            )->execute();

                            $invoiceId = $db->getLastInsertID();

                            // Get invoice number for GL posting
                            $invoiceNo = $data['invoice_no'];

                            // Create initial payment record if there's an initial payment
                            $this->recordInvoicePayment($invoiceId, $paidAmount, 0, 'Initial Payment - Invoice Created');

                            // Post sale to GL (record sale revenue and AR)
                            $this->postSaleToGL($post['sales_order_id'] ?? null, $invoiceNo, $grandTotal, $this->currentUserId());

                            // Post payment to GL if there's initial payment
                            if ($paidAmount > 0) {
                                $this->postSalePaymentToGL($post['sales_order_id'] ?? null, $invoiceNo, $paidAmount, $this->currentUserId());
                            }

                            // Auto-update sales order status if fully paid
                            if ($remainingBalance <= 0 && !empty($post['sales_order_id'])) {
                                $db->createCommand()->update(
                                    'inventory_sales_orders',
                                    ['order_status' => 'Completed', 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $this->currentUserId()],
                                    ['id' => $post['sales_order_id']]
                                )->execute();
                            }
                        }

                        $transaction->commit();

                        return [
                            'success' => true,
                            'message' => 'Sales Invoice saved successfully.',
                            'id' => $invoiceId
                        ];
                    } catch (\Exception $e) {

                        $transaction->rollBack();

                        return [
                            'success' => false,
                            'message' => $e->getMessage()
                        ];
                    }
                }

                if (isset($post['flag']) && $post['flag'] == 'delete') {

                    Yii::$app->db->createCommand()->update(
                        'inventory_sales_invoices',
                        [
                            'is_deleted' => 1,
                            'updated_at' => date('Y-m-d H:i:s')
                        ],
                        ['id' => $post['id']]
                    )->execute();

                    return [
                        'success' => true,
                        'message' => 'Sales Invoice deleted successfully.'
                    ];
                }

                if (isset($post['flag']) && $post['flag'] == 'get_items') {

                    $db = Yii::$app->db;
                    $invoiceId = (int)($post['id'] ?? 0);

                    if ($invoiceId <= 0) {
                        return [
                            'success' => false,
                            'message' => 'Invalid invoice ID.'
                        ];
                    }

                    // First, try to get items from invoice items table
                    $items = $db->createCommand("
                        SELECT
                            sii.id,
                            sii.product_id,
                            p.product_name,
                            sii.quantity,
                            sii.unit_price,
                            sii.discount,
                            sii.tax,
                            sii.total
                        FROM inventory_sale_invoice_items sii
                        LEFT JOIN inventory_products p ON sii.product_id = p.id
                        WHERE sii.sales_invoice_id = :invoice_id
                        AND sii.is_deleted = 0
                        ORDER BY sii.id ASC
                    ")->bindValue(':invoice_id', $invoiceId)->queryAll();

                    // If no items in invoice_items table, fallback to sales order items
                    if (empty($items)) {
                        $invoice = $db->createCommand("
                            SELECT sales_order_id FROM inventory_sales_invoices
                            WHERE id = :invoice_id AND is_deleted = 0
                        ")->bindValue(':invoice_id', $invoiceId)->queryOne();

                        if ($invoice && $invoice['sales_order_id']) {
                            $items = $db->createCommand("
                                SELECT
                                    soi.id,
                                    soi.product_id,
                                    p.product_name,
                                    soi.quantity,
                                    soi.unit_price,
                                    soi.discount,
                                    soi.tax,
                                    soi.total
                                FROM inventory_sales_order_items soi
                                LEFT JOIN inventory_products p ON soi.product_id = p.id
                                WHERE soi.sales_order_id = :sales_order_id
                                AND soi.is_deleted = 0
                                ORDER BY soi.id ASC
                            ")->bindValue(':sales_order_id', $invoice['sales_order_id'])->queryAll();
                        }
                    }

                    return [
                        'success' => true,
                        'items' => $items
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Invalid request.'
                ];
            } catch (\Exception $e) {

                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        $salesOrders = Yii::$app->db->createCommand("
            SELECT id,order_number
            FROM inventory_sales_orders
            WHERE is_deleted=0
            ORDER BY order_number
        ")->queryAll();

        $customers = Yii::$app->db->createCommand("
            SELECT id,customer_code,customer_type,company_name,first_name,last_name
            FROM inventory_customers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY first_name
        ")->queryAll();

        $salesInvoices = Yii::$app->db->createCommand("
            SELECT
                si.*,
                so.order_number,
                c.company_name,
                c.first_name,
                c.last_name
            FROM inventory_sales_invoices si
            LEFT JOIN inventory_sales_orders so
                ON so.id=si.sales_order_id
            LEFT JOIN inventory_customers c
                ON c.id=si.customer_id
            WHERE si.is_deleted=0
            ORDER BY si.id DESC
            LIMIT 20
        ")->queryAll();

        return $this->renderPartial('salesinvoices', [
            'salesInvoices' => $salesInvoices,
            'salesOrders' => $salesOrders,
            'customers' => $customers
        ]);
    }

    public function actionPendingorders()
    {
        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {

                if (isset($post['flag']) && $post['flag'] == 'load') {

                    $page = max(1, (int)($post['page'] ?? 1));
                    $perPage = max(10, (int)($post['per_page'] ?? 20));
                    $offset = ($page - 1) * $perPage;

                    $customer_id = trim($post['customer_id'] ?? '');
                    $warehouse_id = trim($post['warehouse_id'] ?? '');
                    $order_number = trim($post['order_number'] ?? '');
                    $from_date = trim($post['from_date'] ?? '');
                    $to_date = trim($post['to_date'] ?? '');

                    $where = " WHERE so.is_deleted=0 AND so.order_status IN ('Draft','Packed') ";
                    $params = [];

                    if ($customer_id != '') {
                        $where .= " AND so.customer_id=:customer_id ";
                        $params[':customer_id'] = $customer_id;
                    }

                    if ($warehouse_id != '') {
                        $where .= " AND so.warehouse_id=:warehouse_id ";
                        $params[':warehouse_id'] = $warehouse_id;
                    }

                    if ($order_number != '') {
                        $where .= " AND so.order_number LIKE :order_number ";
                        $params[':order_number'] = '%' . $order_number . '%';
                    }

                    if ($from_date != '') {
                        $where .= " AND so.order_date>=:from_date ";
                        $params[':from_date'] = $from_date;
                    }

                    if ($to_date != '') {
                        $where .= " AND so.order_date<=:to_date ";
                        $params[':to_date'] = $to_date;
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_sales_orders so
                        {$where}
                    ", $params)->queryScalar();

                    $pendingOrders = Yii::$app->db->createCommand("
                        SELECT
                            so.*,
                            c.company_name,
                            c.first_name,
                            c.last_name,
                            w.warehouse_name
                        FROM inventory_sales_orders so
                        LEFT JOIN inventory_customers c
                            ON c.id=so.customer_id
                        LEFT JOIN inventory_warehouses w
                            ON w.id=so.warehouse_id
                        {$where}
                        ORDER BY so.order_date DESC,so.id DESC
                        LIMIT {$offset},{$perPage}
                    ", $params)->queryAll();

                    return [
                        'success' => true,
                        'pendingOrders' => $pendingOrders,
                        'page' => $page,
                        'perPage' => $perPage,
                        'total' => $total,
                        'totalPages' => ceil($total / $perPage)
                    ];
                }

                if (isset($post['flag']) && $post['flag'] == 'confirm') {

                    Yii::$app->db->createCommand()->update(
                        'inventory_sales_orders',
                        [
                            'order_status' => 'Confirmed',
                            'updated_at' => date('Y-m-d H:i:s')
                        ],
                        ['id' => $post['id']]
                    )->execute();

                    return [
                        'success' => true,
                        'message' => 'Sales Order confirmed successfully.'
                    ];
                }

                if (isset($post['flag']) && $post['flag'] == 'cancel') {

                    Yii::$app->db->createCommand()->update(
                        'inventory_sales_orders',
                        [
                            'order_status' => 'Cancelled',
                            'updated_at' => date('Y-m-d H:i:s')
                        ],
                        ['id' => $post['id']]
                    )->execute();

                    return [
                        'success' => true,
                        'message' => 'Sales Order cancelled successfully.'
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Invalid request.'
                ];
            } catch (\Exception $e) {

                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        $customers = Yii::$app->db->createCommand("
            SELECT id,customer_code,customer_type,company_name,first_name,last_name
            FROM inventory_customers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY first_name
        ")->queryAll();

        $warehouses = Yii::$app->db->createCommand("
            SELECT id,warehouse_name
            FROM inventory_warehouses
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY warehouse_name
        ")->queryAll();

        $pendingOrders = Yii::$app->db->createCommand("
            SELECT
                so.*,
                c.company_name,
                c.first_name,
                c.last_name,
                w.warehouse_name
            FROM inventory_sales_orders so
            LEFT JOIN inventory_customers c
                ON c.id=so.customer_id
            LEFT JOIN inventory_warehouses w
                ON w.id=so.warehouse_id
            WHERE so.is_deleted=0
            AND so.order_status IN ('Draft','Confirmed','Packed')
            ORDER BY so.order_date DESC,so.id DESC
            LIMIT 20
        ")->queryAll();

        return $this->renderPartial('pendingorders', [
            'pendingOrders' => $pendingOrders,
            'customers' => $customers,
            'warehouses' => $warehouses
        ]);
    }

    public function actionDeliveredorders()
    {
        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {

                if (isset($post['flag']) && $post['flag'] == 'load') {

                    $page = max(1, (int)($post['page'] ?? 1));
                    $perPage = max(10, (int)($post['per_page'] ?? 20));
                    $offset = ($page - 1) * $perPage;

                    $customer_id = trim($post['customer_id'] ?? '');
                    $warehouse_id = trim($post['warehouse_id'] ?? '');
                    $order_number = trim($post['order_number'] ?? '');
                    $from_date = trim($post['from_date'] ?? '');
                    $to_date = trim($post['to_date'] ?? '');

                    $where = " WHERE so.is_deleted=0 AND so.order_status='Delivered' ";
                    $params = [];

                    if ($customer_id != '') {
                        $where .= " AND so.customer_id=:customer_id ";
                        $params[':customer_id'] = $customer_id;
                    }

                    if ($warehouse_id != '') {
                        $where .= " AND so.warehouse_id=:warehouse_id ";
                        $params[':warehouse_id'] = $warehouse_id;
                    }

                    if ($order_number != '') {
                        $where .= " AND so.order_number LIKE :order_number ";
                        $params[':order_number'] = '%' . $order_number . '%';
                    }

                    if ($from_date != '') {
                        $where .= " AND so.order_date>=:from_date ";
                        $params[':from_date'] = $from_date;
                    }

                    if ($to_date != '') {
                        $where .= " AND so.order_date<=:to_date ";
                        $params[':to_date'] = $to_date;
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_sales_orders so
                        {$where}
                    ", $params)->queryScalar();

                    $deliveredOrders = Yii::$app->db->createCommand("
                        SELECT
                            so.*,
                            c.company_name,
                            c.first_name,
                            c.last_name,
                            w.warehouse_name
                        FROM inventory_sales_orders so
                        LEFT JOIN inventory_customers c
                            ON c.id=so.customer_id
                        LEFT JOIN inventory_warehouses w
                            ON w.id=so.warehouse_id
                        {$where}
                        ORDER BY so.delivery_date DESC,so.id DESC
                        LIMIT {$offset},{$perPage}
                    ", $params)->queryAll();

                    return [
                        'success' => true,
                        'deliveredOrders' => $deliveredOrders,
                        'page' => $page,
                        'perPage' => $perPage,
                        'total' => $total,
                        'totalPages' => ceil($total / $perPage)
                    ];
                }

                if (isset($post['flag']) && $post['flag'] == 'markpaid') {

                    Yii::$app->db->createCommand()->update(
                        'inventory_sales_orders',
                        [
                            'payment_status' => 'Paid',
                            'updated_at' => date('Y-m-d H:i:s')
                        ],
                        ['id' => $post['id']]
                    )->execute();

                    return [
                        'success' => true,
                        'message' => 'Sales Order marked as paid.'
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Invalid request.'
                ];
            } catch (\Exception $e) {

                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        $customers = Yii::$app->db->createCommand("
            SELECT id,customer_code,customer_type,company_name,first_name,last_name
            FROM inventory_customers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY first_name
        ")->queryAll();

        $warehouses = Yii::$app->db->createCommand("
            SELECT id,warehouse_name
            FROM inventory_warehouses
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY warehouse_name
        ")->queryAll();

        $deliveredOrders = Yii::$app->db->createCommand("
            SELECT
                so.*,
                c.company_name,
                c.first_name,
                c.last_name,
                w.warehouse_name
            FROM inventory_sales_orders so
            LEFT JOIN inventory_customers c
                ON c.id=so.customer_id
            LEFT JOIN inventory_warehouses w
                ON w.id=so.warehouse_id
            WHERE so.is_deleted=0
            AND so.order_status='Delivered'
            ORDER BY so.delivery_date DESC,so.id DESC
            LIMIT 20
        ")->queryAll();

        return $this->renderPartial('deliveredorders', [
            'deliveredOrders' => $deliveredOrders,
            'customers' => $customers,
            'warehouses' => $warehouses
        ]);
    }

    public function actionCancelledorders()
    {
        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {

                if (isset($post['flag']) && $post['flag'] == 'load') {

                    $page = max(1, (int)($post['page'] ?? 1));
                    $perPage = max(10, (int)($post['per_page'] ?? 20));
                    $offset = ($page - 1) * $perPage;

                    $customer_id = trim($post['customer_id'] ?? '');
                    $warehouse_id = trim($post['warehouse_id'] ?? '');
                    $order_number = trim($post['order_number'] ?? '');
                    $from_date = trim($post['from_date'] ?? '');
                    $to_date = trim($post['to_date'] ?? '');

                    $where = " WHERE so.is_deleted=0 AND so.order_status='Cancelled' ";
                    $params = [];

                    if ($customer_id != '') {
                        $where .= " AND so.customer_id=:customer_id ";
                        $params[':customer_id'] = $customer_id;
                    }

                    if ($warehouse_id != '') {
                        $where .= " AND so.warehouse_id=:warehouse_id ";
                        $params[':warehouse_id'] = $warehouse_id;
                    }

                    if ($order_number != '') {
                        $where .= " AND so.order_number LIKE :order_number ";
                        $params[':order_number'] = '%' . $order_number . '%';
                    }

                    if ($from_date != '') {
                        $where .= " AND so.order_date>=:from_date ";
                        $params[':from_date'] = $from_date;
                    }

                    if ($to_date != '') {
                        $where .= " AND so.order_date<=:to_date ";
                        $params[':to_date'] = $to_date;
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_sales_orders so
                        {$where}
                    ", $params)->queryScalar();

                    $cancelledOrders = Yii::$app->db->createCommand("
                        SELECT
                            so.*,
                            c.company_name,
                            c.first_name,
                            c.last_name,
                            w.warehouse_name
                        FROM inventory_sales_orders so
                        LEFT JOIN inventory_customers c
                            ON c.id=so.customer_id
                        LEFT JOIN inventory_warehouses w
                            ON w.id=so.warehouse_id
                        {$where}
                        ORDER BY so.id DESC
                        LIMIT {$offset},{$perPage}
                    ", $params)->queryAll();

                    return [
                        'success' => true,
                        'cancelledOrders' => $cancelledOrders,
                        'page' => $page,
                        'perPage' => $perPage,
                        'total' => $total,
                        'totalPages' => ceil($total / $perPage)
                    ];
                }

                if (isset($post['flag']) && $post['flag'] == 'restore') {

                    Yii::$app->db->createCommand()->update(
                        'inventory_sales_orders',
                        [
                            'order_status' => 'Draft',
                            'updated_at' => date('Y-m-d H:i:s')
                        ],
                        ['id' => $post['id']]
                    )->execute();

                    return [
                        'success' => true,
                        'message' => 'Sales Order restored to draft.'
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Invalid request.'
                ];
            } catch (\Exception $e) {

                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        $customers = Yii::$app->db->createCommand("
            SELECT id,customer_code,customer_type,company_name,first_name,last_name
            FROM inventory_customers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY first_name
        ")->queryAll();

        $warehouses = Yii::$app->db->createCommand("
            SELECT id,warehouse_name
            FROM inventory_warehouses
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY warehouse_name
        ")->queryAll();

        $cancelledOrders = Yii::$app->db->createCommand("
            SELECT
                so.*,
                c.company_name,
                c.first_name,
                c.last_name,
                w.warehouse_name
            FROM inventory_sales_orders so
            LEFT JOIN inventory_customers c
                ON c.id=so.customer_id
            LEFT JOIN inventory_warehouses w
                ON w.id=so.warehouse_id
            WHERE so.is_deleted=0
            AND so.order_status='Cancelled'
            ORDER BY so.id DESC
            LIMIT 20
        ")->queryAll();

        return $this->renderPartial('cancelledorders', [
            'cancelledOrders' => $cancelledOrders,
            'customers' => $customers,
            'warehouses' => $warehouses
        ]);
    }

    public function actionSalesreturns()
    {
        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {

                if (isset($post['flag']) && $post['flag'] == 'load') {

                    $page = max(1, (int)($post['page'] ?? 1));
                    $perPage = max(10, (int)($post['per_page'] ?? 20));
                    $offset = ($page - 1) * $perPage;

                    $customer_id = trim($post['customer_id'] ?? '');
                    $sales_invoice_id = trim($post['sales_invoice_id'] ?? '');
                    $status = trim($post['status'] ?? '');
                    $return_no = trim($post['return_no'] ?? '');
                    $from_date = trim($post['from_date'] ?? '');
                    $to_date = trim($post['to_date'] ?? '');

                    $where = " WHERE sr.is_deleted=0 ";
                    $params = [];

                    if ($customer_id != '') {
                        $where .= " AND sr.customer_id=:customer_id ";
                        $params[':customer_id'] = $customer_id;
                    }

                    if ($sales_invoice_id != '') {
                        $where .= " AND sr.sales_invoice_id=:sales_invoice_id ";
                        $params[':sales_invoice_id'] = $sales_invoice_id;
                    }

                    if ($status != '') {
                        $where .= " AND sr.status=:status ";
                        $params[':status'] = $status;
                    }

                    if ($return_no != '') {
                        $where .= " AND sr.return_no LIKE :return_no ";
                        $params[':return_no'] = '%' . $return_no . '%';
                    }

                    if ($from_date != '') {
                        $where .= " AND sr.return_date>=:from_date ";
                        $params[':from_date'] = $from_date;
                    }

                    if ($to_date != '') {
                        $where .= " AND sr.return_date<=:to_date ";
                        $params[':to_date'] = $to_date;
                    }

                    $total = Yii::$app->db->createCommand("
                        SELECT COUNT(*)
                        FROM inventory_sales_returns sr
                        {$where}
                    ", $params)->queryScalar();

                    $salesReturns = Yii::$app->db->createCommand("
                        SELECT
                            sr.*,
                            si.invoice_no,
                            c.company_name,
                            c.first_name,
                            c.last_name
                        FROM inventory_sales_returns sr
                        LEFT JOIN inventory_sales_invoices si
                            ON si.id=sr.sales_invoice_id
                        LEFT JOIN inventory_customers c
                            ON c.id=sr.customer_id
                        {$where}
                        ORDER BY sr.id DESC
                        LIMIT {$offset},{$perPage}
                    ", $params)->queryAll();

                    return [
                        'success' => true,
                        'salesReturns' => $salesReturns,
                        'page' => $page,
                        'perPage' => $perPage,
                        'total' => $total,
                        'totalPages' => ceil($total / $perPage)
                    ];
                }

                if (isset($post['flag']) && $post['flag'] == 'save') {

                    $db = Yii::$app->db;
                    $transaction = $db->beginTransaction();

                    try {

                        $id = (int)($post['id'] ?? 0);

                        $data = [
                            'sales_invoice_id' => $post['sales_invoice_id'],
                            'customer_id' => $post['customer_id'],
                            'return_date' => $post['return_date'],
                            'reason' => $post['reason'] ?? null,
                            'subtotal' => $post['subtotal'],
                            'tax_amount' => $post['tax_amount'] ?? 0,
                            'grand_total' => $post['grand_total'],
                            'status' => $post['status'],
                            'remarks' => $post['remarks'] ?? null,
                            'updated_at' => date('Y-m-d H:i:s')
                        ];

                        if ($id > 0) {

                            $db->createCommand()->update(
                                'inventory_sales_returns',
                                $data,
                                ['id' => $id]
                            )->execute();

                            $returnId = $id;
                        } else {

                            $data['return_no'] = $this->generateDocNo('SRN');
                            $data['created_at'] = date('Y-m-d H:i:s');
                            $data['is_active'] = 1;
                            $data['is_deleted'] = 0;

                            $db->createCommand()->insert(
                                'inventory_sales_returns',
                                $data
                            )->execute();

                            $returnId = $db->getLastInsertID();
                        }

                        $transaction->commit();

                        return [
                            'success' => true,
                            'message' => 'Sales Return saved successfully.',
                            'id' => $returnId
                        ];
                    } catch (\Exception $e) {

                        $transaction->rollBack();

                        return [
                            'success' => false,
                            'message' => $e->getMessage()
                        ];
                    }
                }

                if (isset($post['flag']) && $post['flag'] == 'delete') {

                    Yii::$app->db->createCommand()->update(
                        'inventory_sales_returns',
                        [
                            'is_deleted' => 1,
                            'updated_at' => date('Y-m-d H:i:s')
                        ],
                        ['id' => $post['id']]
                    )->execute();

                    return [
                        'success' => true,
                        'message' => 'Sales Return deleted successfully.'
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Invalid request.'
                ];
            } catch (\Exception $e) {

                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        $customers = Yii::$app->db->createCommand("
            SELECT id,customer_code,customer_type,company_name,first_name,last_name
            FROM inventory_customers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY first_name
        ")->queryAll();

        $salesInvoices = Yii::$app->db->createCommand("
            SELECT id,invoice_no
            FROM inventory_sales_invoices
            WHERE is_deleted=0
            ORDER BY invoice_no
        ")->queryAll();

        $salesReturns = Yii::$app->db->createCommand("
            SELECT
                sr.*,
                si.invoice_no,
                c.company_name,
                c.first_name,
                c.last_name
            FROM inventory_sales_returns sr
            LEFT JOIN inventory_sales_invoices si
                ON si.id=sr.sales_invoice_id
            LEFT JOIN inventory_customers c
                ON c.id=sr.customer_id
            WHERE sr.is_deleted=0
            ORDER BY sr.id DESC
            LIMIT 20
        ")->queryAll();

        return $this->renderPartial('salesreturns', [
            'salesReturns' => $salesReturns,
            'salesInvoices' => $salesInvoices,
            'customers' => $customers
        ]);
    }

    public function actionSalesreports()
    {
        if (Yii::$app->request->isPost) {

            $post = Yii::$app->request->post();
            Yii::$app->response->format = Response::FORMAT_JSON;

            try {

                if (isset($post['flag']) && $post['flag'] == 'load') {

                    $from_date = trim($post['from_date'] ?? '');
                    $to_date = trim($post['to_date'] ?? '');
                    $customer_id = trim($post['customer_id'] ?? '');
                    $warehouse_id = trim($post['warehouse_id'] ?? '');
                    $status = trim($post['status'] ?? '');

                    $where = " WHERE so.is_deleted=0 ";
                    $params = [];

                    if ($from_date != '') {
                        $where .= " AND so.order_date>=:from_date ";
                        $params[':from_date'] = $from_date;
                    }

                    if ($to_date != '') {
                        $where .= " AND so.order_date<=:to_date ";
                        $params[':to_date'] = $to_date;
                    }

                    if ($customer_id != '') {
                        $where .= " AND so.customer_id=:customer_id ";
                        $params[':customer_id'] = $customer_id;
                    }

                    if ($warehouse_id != '') {
                        $where .= " AND so.warehouse_id=:warehouse_id ";
                        $params[':warehouse_id'] = $warehouse_id;
                    }

                    if ($status != '') {
                        $where .= " AND so.order_status=:status ";
                        $params[':status'] = $status;
                    }

                    $summary = Yii::$app->db->createCommand("
                        SELECT
                            COUNT(*) total_orders,
                            IFNULL(SUM(so.grand_total),0) total_amount,
                            IFNULL(AVG(so.grand_total),0) average_amount
                        FROM inventory_sales_orders so
                        {$where}
                    ", $params)->queryOne();

                    $statusSummary = Yii::$app->db->createCommand("
                        SELECT
                            so.order_status,
                            COUNT(*) total_orders,
                            IFNULL(SUM(so.grand_total),0) total_amount
                        FROM inventory_sales_orders so
                        {$where}
                        GROUP BY so.order_status
                        ORDER BY total_orders DESC
                    ", $params)->queryAll();

                    $customerSummary = Yii::$app->db->createCommand("
                        SELECT
                            c.company_name,
                            c.first_name,
                            c.last_name,
                            COUNT(so.id) total_orders,
                            IFNULL(SUM(so.grand_total),0) total_amount
                        FROM inventory_sales_orders so
                        LEFT JOIN inventory_customers c
                            ON c.id=so.customer_id
                        {$where}
                        GROUP BY so.customer_id,c.company_name,c.first_name,c.last_name
                        ORDER BY total_amount DESC
                    ", $params)->queryAll();

                    $warehouseSummary = Yii::$app->db->createCommand("
                        SELECT
                            w.warehouse_name,
                            COUNT(so.id) total_orders,
                            IFNULL(SUM(so.grand_total),0) total_amount
                        FROM inventory_sales_orders so
                        LEFT JOIN inventory_warehouses w
                            ON w.id=so.warehouse_id
                        {$where}
                        GROUP BY so.warehouse_id,w.warehouse_name
                        ORDER BY total_amount DESC
                    ", $params)->queryAll();

                    $salesReport = Yii::$app->db->createCommand("
                        SELECT
                            so.*,
                            c.company_name,
                            c.first_name,
                            c.last_name,
                            w.warehouse_name
                        FROM inventory_sales_orders so
                        LEFT JOIN inventory_customers c
                            ON c.id=so.customer_id
                        LEFT JOIN inventory_warehouses w
                            ON w.id=so.warehouse_id
                        {$where}
                        ORDER BY so.order_date DESC,so.id DESC
                    ", $params)->queryAll();

                    return [
                        'success' => true,
                        'summary' => $summary,
                        'statusSummary' => $statusSummary,
                        'customerSummary' => $customerSummary,
                        'warehouseSummary' => $warehouseSummary,
                        'salesReport' => $salesReport
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Invalid request.'
                ];
            } catch (\Exception $e) {

                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        $customers = Yii::$app->db->createCommand("
            SELECT id,customer_code,customer_type,company_name,first_name,last_name
            FROM inventory_customers
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY first_name
        ")->queryAll();

        $warehouses = Yii::$app->db->createCommand("
            SELECT id,warehouse_name
            FROM inventory_warehouses
            WHERE is_deleted=0
            AND is_active=1
            ORDER BY warehouse_name
        ")->queryAll();

        return $this->renderPartial('salesreports', [
            'customers' => $customers,
            'warehouses' => $warehouses
        ]);
    }

    
    public function actionInjectdb()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_categories ( 
                id INT AUTO_INCREMENT PRIMARY KEY,
                parent_id INT NULL,
                category_name VARCHAR(150) NOT NULL,
                category_code VARCHAR(50) UNIQUE,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT NULL,
                updated_by INT NULL,
                is_active TINYINT(1) DEFAULT 1,
                is_deleted TINYINT(1) DEFAULT 0,
                INDEX(parent_id)

            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_brands ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                brand_name VARCHAR(150) NOT NULL,
                brand_code VARCHAR(50), 
                website VARCHAR(255),
                email VARCHAR(150),
                phone VARCHAR(30), 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_units ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                unit_name VARCHAR(100),
                short_name VARCHAR(20), 
                description TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_products ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                category_id INT,
                brand_id INT,
                unit_id INT, 
                product_name VARCHAR(200) NOT NULL,
                sku VARCHAR(100) UNIQUE,
                barcode VARCHAR(150), 
                description TEXT, 
                purchase_price DECIMAL(15,2) DEFAULT 0,
                selling_price DECIMAL(15,2) DEFAULT 0, 
                minimum_stock DECIMAL(15,2) DEFAULT 0,
                maximum_stock DECIMAL(15,2) DEFAULT 0,
                reorder_level DECIMAL(15,2) DEFAULT 0, 
                product_image VARCHAR(255), 
                weight DECIMAL(10,2),
                length DECIMAL(10,2),
                width DECIMAL(10,2),
                height DECIMAL(10,2), 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0, 
                INDEX(category_id),
                INDEX(brand_id),
                INDEX(unit_id), 
                FOREIGN KEY(category_id)
                    REFERENCES inventory_categories(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(brand_id)
                    REFERENCES inventory_brands(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(unit_id)
                    REFERENCES inventory_units(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_warehouses ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                warehouse_name VARCHAR(200), 
                warehouse_code VARCHAR(50), 
                address TEXT, 
                city VARCHAR(100),
                province VARCHAR(100),
                country VARCHAR(100), 
                contact_person VARCHAR(150),
                phone VARCHAR(50),
                email VARCHAR(150), 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                warehouse_id INT NOT NULL,
                product_id INT NOT NULL, 
                quantity DECIMAL(15,2) DEFAULT 0, 
                reserved_quantity DECIMAL(15,2) DEFAULT 0, 
                available_quantity DECIMAL(15,2) DEFAULT 0, 
                average_cost DECIMAL(15,2) DEFAULT 0, 
                last_purchase_price DECIMAL(15,2) DEFAULT 0, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT,
                updated_by INT, 
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0, 
                UNIQUE(product_id,warehouse_id), 
                INDEX(product_id),
                INDEX(warehouse_id), 
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            echo "Part 1 database created successfully.";

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_suppliers ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                supplier_code VARCHAR(50) UNIQUE, 
                company_name VARCHAR(200) NOT NULL, 
                contact_person VARCHAR(150), 
                email VARCHAR(150), 
                phone VARCHAR(50), 
                mobile VARCHAR(50), 
                website VARCHAR(255), 
                tax_number VARCHAR(100), 
                payment_terms INT DEFAULT 30, 
                credit_limit DECIMAL(15,2) DEFAULT 0, 
                opening_balance DECIMAL(15,2) DEFAULT 0, 
                current_balance DECIMAL(15,2) DEFAULT 0, 
                address TEXT, 
                city VARCHAR(100), 
                province VARCHAR(100), 
                country VARCHAR(100), 
                postal_code VARCHAR(20), 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP,
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_supplier_contacts ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                supplier_id INT NOT NULL, 
                contact_name VARCHAR(150), 
                designation VARCHAR(100), 
                phone VARCHAR(50), 
                mobile VARCHAR(50), 
                email VARCHAR(150), 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(supplier_id), 
                FOREIGN KEY(supplier_id)
                    REFERENCES inventory_suppliers(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
                CREATE TABLE IF NOT EXISTS inventory_supplier_documents (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    supplier_id INT NOT NULL,
                    document_type VARCHAR(100),
                    document_name VARCHAR(255) NOT NULL,
                    document_file VARCHAR(500),
                    expiry_date DATE NULL,
                    remarks TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                        ON UPDATE CURRENT_TIMESTAMP,
                    created_by INT NULL,
                    updated_by INT NULL,
                    is_active TINYINT DEFAULT 1,
                    is_deleted TINYINT DEFAULT 0,
                    INDEX(supplier_id),
                    FOREIGN KEY(supplier_id)
                        REFERENCES inventory_suppliers(id)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
                ) ENGINE=InnoDB;
                ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_purchase_orders ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                po_number VARCHAR(100) UNIQUE, 
                supplier_id INT NOT NULL, 
                warehouse_id INT NOT NULL, 
                order_date DATE, 
                expected_date DATE, 
                status ENUM( 
                    'Draft', 
                    'Approved', 
                    'Partially Received', 
                    'Completed', 
                    'Cancelled' 
                ) DEFAULT 'Draft', 
                subtotal DECIMAL(15,2) DEFAULT 0, 
                discount DECIMAL(15,2) DEFAULT 0, 
                tax DECIMAL(15,2) DEFAULT 0, 
                freight DECIMAL(15,2) DEFAULT 0, 
                grand_total DECIMAL(15,2) DEFAULT 0, 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(supplier_id), 
                INDEX(warehouse_id), 
                FOREIGN KEY(supplier_id)
                    REFERENCES inventory_suppliers(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_purchase_order_items ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                purchase_order_id INT NOT NULL, 
                product_id INT NOT NULL, 
                quantity DECIMAL(15,2) DEFAULT 0, 
                received_quantity DECIMAL(15,2) DEFAULT 0, 
                remaining_quantity DECIMAL(15,2) DEFAULT 0, 
                unit_price DECIMAL(15,2) DEFAULT 0, 
                discount DECIMAL(15,2) DEFAULT 0, 
                tax DECIMAL(15,2) DEFAULT 0, 
                total DECIMAL(15,2) DEFAULT 0, 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(purchase_order_id), 
                INDEX(product_id), 
                FOREIGN KEY(purchase_order_id)
                    REFERENCES inventory_purchase_orders(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE, 
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_customers ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                customer_code VARCHAR(50) UNIQUE, 
                customer_type ENUM(
                    'Individual',
                    'Company'
                ) DEFAULT 'Individual', 
                company_name VARCHAR(200), 
                first_name VARCHAR(100), 
                last_name VARCHAR(100), 
                email VARCHAR(150), 
                phone VARCHAR(50), 
                mobile VARCHAR(50), 
                tax_number VARCHAR(100), 
                credit_limit DECIMAL(15,2) DEFAULT 0, 
                opening_balance DECIMAL(15,2) DEFAULT 0, 
                current_balance DECIMAL(15,2) DEFAULT 0, 
                payment_terms INT DEFAULT 0, 
                address TEXT, 
                city VARCHAR(100), 
                province VARCHAR(100), 
                country VARCHAR(100), 
                postal_code VARCHAR(20), 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0 
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_customer_contacts ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                customer_id INT NOT NULL, 
                contact_name VARCHAR(150), 
                designation VARCHAR(100), 
                email VARCHAR(150), 
                phone VARCHAR(50), 
                mobile VARCHAR(50), 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(customer_id), 
                FOREIGN KEY(customer_id)
                    REFERENCES inventory_customers(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_sales_orders ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                order_number VARCHAR(100) UNIQUE, 
                customer_id INT NOT NULL, 
                warehouse_id INT NOT NULL, 
                order_date DATE, 
                delivery_date DATE, 
                order_status ENUM( 
                    'Draft', 
                    'Confirmed', 
                    'Packed', 
                    'Dispatched',  
                    'Delivered', 
                    'Cancelled' 
                ) DEFAULT 'Draft', 
                payment_status ENUM( 
                    'Pending', 
                    'Partial', 
                    'Paid' 
                ) DEFAULT 'Pending', 
                subtotal DECIMAL(15,2) DEFAULT 0, 
                discount DECIMAL(15,2) DEFAULT 0, 
                tax DECIMAL(15,2) DEFAULT 0, 
                shipping DECIMAL(15,2) DEFAULT 0, 
                grand_total DECIMAL(15,2) DEFAULT 0, 
                notes TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(customer_id), 
                INDEX(warehouse_id), 
                FOREIGN KEY(customer_id)
                    REFERENCES inventory_customers(id)
                    ON UPDATE CASCADE, 
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_sales_order_items ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                sales_order_id INT NOT NULL, 
                product_id INT NOT NULL, 
                quantity DECIMAL(15,2) DEFAULT 0, 
                delivered_quantity DECIMAL(15,2) DEFAULT 0, 
                remaining_quantity DECIMAL(15,2) DEFAULT 0, 
                unit_price DECIMAL(15,2) DEFAULT 0, 
                discount DECIMAL(15,2) DEFAULT 0, 
                tax DECIMAL(15,2) DEFAULT 0, 
                total DECIMAL(15,2) DEFAULT 0, 
                remarks TEXT, 
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP, 
                created_by INT, 
                updated_by INT, 
                is_active TINYINT DEFAULT 1, 
                is_deleted TINYINT DEFAULT 0, 
                INDEX(sales_order_id), 
                INDEX(product_id), 
                FOREIGN KEY(sales_order_id)
                    REFERENCES inventory_sales_orders(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE, 
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
                    ON UPDATE CASCADE 
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_sales_invoices (
                id INT AUTO_INCREMENT PRIMARY KEY,
                invoice_no VARCHAR(100) UNIQUE,
                sales_order_id INT NOT NULL,
                customer_id INT NOT NULL,
                invoice_date DATE,
                due_date DATE,
                subtotal DECIMAL(15,2) DEFAULT 0,
                discount_amount DECIMAL(15,2) DEFAULT 0,
                tax_amount DECIMAL(15,2) DEFAULT 0,
                grand_total DECIMAL(15,2) DEFAULT 0,
                status ENUM(
                    'Unpaid',
                    'Partial',
                    'Paid',
                    'Cancelled'
                ) DEFAULT 'Unpaid',
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(sales_order_id),
                INDEX(customer_id),
                FOREIGN KEY(sales_order_id)
                    REFERENCES inventory_sales_orders(id)
                    ON UPDATE CASCADE,
                FOREIGN KEY(customer_id)
                    REFERENCES inventory_customers(id)
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_pos_sales (
                id INT AUTO_INCREMENT PRIMARY KEY,
                pos_no VARCHAR(100) UNIQUE,
                customer_id INT NULL,
                warehouse_id INT NOT NULL,
                sale_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                items JSON,
                subtotal DECIMAL(15,2) DEFAULT 0,
                discount_amount DECIMAL(15,2) DEFAULT 0,
                tax_amount DECIMAL(15,2) DEFAULT 0,
                grand_total DECIMAL(15,2) DEFAULT 0,
                paid_amount DECIMAL(15,2) DEFAULT 0,
                change_amount DECIMAL(15,2) DEFAULT 0,
                payment_method ENUM(
                    'Cash',
                    'Card',
                    'Bank',
                    'Online'
                ) DEFAULT 'Cash',
                status ENUM(
                    'Completed',
                    'Cancelled',
                    'Refunded'
                ) DEFAULT 'Completed',
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(customer_id),
                INDEX(warehouse_id),
                FOREIGN KEY(customer_id)
                    REFERENCES inventory_customers(id)
                    ON UPDATE CASCADE,
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_sales_returns (
                id INT AUTO_INCREMENT PRIMARY KEY,
                return_no VARCHAR(100) UNIQUE,
                sales_invoice_id INT NOT NULL,
                customer_id INT NOT NULL,
                return_date DATE,
                reason VARCHAR(255),
                subtotal DECIMAL(15,2) DEFAULT 0,
                tax_amount DECIMAL(15,2) DEFAULT 0,
                grand_total DECIMAL(15,2) DEFAULT 0,
                status ENUM(
                    'Pending',
                    'Approved',
                    'Completed',
                    'Cancelled'
                ) DEFAULT 'Pending',
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(sales_invoice_id),
                INDEX(customer_id),
                FOREIGN KEY(sales_invoice_id)
                    REFERENCES inventory_sales_invoices(id)
                    ON UPDATE CASCADE,
                FOREIGN KEY(customer_id)
                    REFERENCES inventory_customers(id)
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_movements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                movement_no VARCHAR(50) UNIQUE,
                warehouse_id INT NOT NULL,
                product_id INT NOT NULL,
                reference_type ENUM(
                    'Purchase',
                    'Sale',
                    'Transfer In',
                    'Transfer Out',
                    'Adjustment',
                    'Return Purchase',
                    'Return Sale',
                    'Opening Stock',
                    'Stock Audit'
                ) NOT NULL,
                reference_id INT NULL,
                movement_type ENUM('IN','OUT') NOT NULL,
                quantity DECIMAL(15,2) NOT NULL,
                unit_cost DECIMAL(15,2) DEFAULT 0,
                total_cost DECIMAL(15,2) DEFAULT 0,
                remarks TEXT,
                movement_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(product_id),
                INDEX(warehouse_id),
                INDEX(reference_id),
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
                    ON UPDATE CASCADE,
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_adjustments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                adjustment_no VARCHAR(50) UNIQUE,
                warehouse_id INT NOT NULL,
                adjustment_date DATE,
                adjustment_type ENUM('Increase','Decrease') NOT NULL,
                reason ENUM('Damage','Expired','Lost','Correction','Other'),
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(warehouse_id) REFERENCES inventory_warehouses(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_adjustment_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                adjustment_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity DECIMAL(15,2),
                unit_cost DECIMAL(15,2),
                total_cost DECIMAL(15,2),
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(adjustment_id)
                    REFERENCES inventory_stock_adjustments(id)
                    ON DELETE CASCADE,
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_transfers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transfer_no VARCHAR(50) UNIQUE,
                from_warehouse INT NOT NULL,
                to_warehouse INT NOT NULL,
                transfer_date DATE,
                status ENUM('Pending','In Transit','Completed','Cancelled') DEFAULT 'Pending',
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(from_warehouse) REFERENCES inventory_warehouses(id),
                FOREIGN KEY(to_warehouse) REFERENCES inventory_warehouses(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_transfer_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transfer_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity DECIMAL(15,2),
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(transfer_id)
                    REFERENCES inventory_stock_transfers(id)
                    ON DELETE CASCADE,
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_audits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                audit_no VARCHAR(50) UNIQUE,
                warehouse_id INT,
                audit_date DATE,
                status ENUM('Open','Completed') DEFAULT 'Open',
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(warehouse_id)
                    REFERENCES inventory_warehouses(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_stock_audit_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                audit_id INT NOT NULL,
                product_id INT NOT NULL,
                system_quantity DECIMAL(15,2),
                physical_quantity DECIMAL(15,2),
                variance DECIMAL(15,2),
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(audit_id)
                    REFERENCES inventory_stock_audits(id)
                    ON DELETE CASCADE,
                FOREIGN KEY(product_id)
                    REFERENCES inventory_products(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_accounts ( 
                id INT AUTO_INCREMENT PRIMARY KEY, 
                parent_id INT NULL, 
                account_code VARCHAR(50) UNIQUE, 
                account_name VARCHAR(200) NOT NULL, 
                account_type ENUM('Asset','Liability','Equity','Income','Expense') NOT NULL,
                opening_balance DECIMAL(15,2) DEFAULT 0,
                current_balance DECIMAL(15,2) DEFAULT 0,
                remarks TEXT,

                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,

                created_by INT,
                updated_by INT,

                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,

                INDEX(parent_id),

                FOREIGN KEY(parent_id)
                    REFERENCES inventory_accounts(id)
                    ON UPDATE CASCADE
                    ON DELETE SET NULL

            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transaction_no VARCHAR(100) UNIQUE,
                transaction_date DATE,
                reference_type ENUM('Purchase','Sale','Payment','Receipt','Expense','Adjustment'),
                reference_id INT,
                account_id INT NOT NULL,
                transaction_type ENUM('Debit','Credit'
                ) NOT NULL,
                amount DECIMAL(15,2) NOT NULL,
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(account_id),
                FOREIGN KEY(account_id)
                    REFERENCES inventory_accounts(id)
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_payments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                payment_no VARCHAR(100) UNIQUE,
                payment_date DATE,
                payment_type ENUM('Receive','Pay'),
                reference_type ENUM('Customer','Supplier'),
                reference_id INT,
                payment_method ENUM('Cash','Bank','Cheque','Online'),
                account_id INT,
                amount DECIMAL(15,2),
                remarks TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                FOREIGN KEY(account_id)
                    REFERENCES inventory_accounts(id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                title VARCHAR(255),
                message TEXT,
                notification_type ENUM('Info','Success','Warning','Error') DEFAULT 'Info',
                is_read TINYINT DEFAULT 0,
                read_at DATETIME NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(user_id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_logs (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                module VARCHAR(100),
                action VARCHAR(100),
                table_name VARCHAR(100),
                record_id BIGINT,
                old_data LONGTEXT,
                new_data LONGTEXT,
                ip_address VARCHAR(50),
                user_agent TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX(user_id),
                INDEX(module),
                INDEX(table_name),
                INDEX(record_id)
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_reports (
                id INT AUTO_INCREMENT PRIMARY KEY,
                report_name VARCHAR(200),
                report_type VARCHAR(100),
                generated_by INT,
                filters JSON,
                file_path VARCHAR(500),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                is_deleted TINYINT DEFAULT 0
            ) ENGINE=InnoDB;
            ")->execute();

            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_vehicle_makes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                make_name VARCHAR(150) NOT NULL,
                make_code VARCHAR(50) UNIQUE,
                country VARCHAR(100),
                website VARCHAR(255),
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0
            ) ENGINE=InnoDB;
            ")->execute();
            $db->createCommand("
            CREATE TABLE IF NOT EXISTS inventory_vehicle_models (
                id INT AUTO_INCREMENT PRIMARY KEY,
                make_id INT NOT NULL,
                model_name VARCHAR(150) NOT NULL,
                model_code VARCHAR(50),
                model_year VARCHAR(50),
                engine_type VARCHAR(100),
                engine_capacity VARCHAR(50),
                fuel_type ENUM(
                    'Petrol',
                    'Diesel',
                    'Hybrid',
                    'Electric',
                    'CNG'
                ) DEFAULT 'Petrol',
                transmission ENUM(
                    'Manual',
                    'Automatic',
                    'CVT'
                ) DEFAULT 'Manual',
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT,
                updated_by INT,
                is_active TINYINT DEFAULT 1,
                is_deleted TINYINT DEFAULT 0,
                INDEX(make_id),
                FOREIGN KEY(make_id)
                    REFERENCES inventory_vehicle_makes(id)
                    ON UPDATE CASCADE
                    ON DELETE CASCADE
            ) ENGINE=InnoDB;
            ")->execute();

            $transaction->commit();

            echo "Database created successfully.";
            exit;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo "Error: " . $e->getMessage();
            exit;
        }
    }
}

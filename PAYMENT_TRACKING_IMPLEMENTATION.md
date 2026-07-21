# Sale Order Payment Tracking & Auto-Status Updates

## Overview
Enhanced the Sale Controller to automatically record payment history and update statuses when sale orders/invoices are paid.

## Changes Made

### 1. `createSaleInvoiceFromSalesOrder()` method (lines 267-335)
**What changed:**
- Added auto-status update logic when remaining balance is zero
- When fully paid (remaining_balance <= 0):
  - Updates sale order status to "Completed"
  - Updates invoice status to "Paid"

**Code:**
```php
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
```

### 2. `saveSalesOrder()` method (lines 430-456)
**What changed:**
- Added auto-status update logic when payment increases and remaining balance becomes zero
- Maintains existing payment history recording via `recordInvoicePayment()` method

**Enhancement:**
- When payment difference > 0 and remaining_balance <= 0:
  - Automatically updates sale order status to "Completed"

### 3. `createSaleInvoiceFromPos()` method (lines 212-285)
**What changed:**
- Added GL posting for POS payments
- Added auto-status update for POS invoices when fully paid

**Code additions:**
```php
// Post POS payment to GL
if ($paid_amount > 0) {
    $this->postSalePaymentToGL($pos_sales_id, $invoice_no, $paid_amount, $user_id);
}

// Auto-update invoice status if fully paid
if ($remaining_balance <= 0) {
    $db->createCommand()->update(
        'inventory_sales_invoices',
        ['status' => 'Paid', 'updated_at' => date('Y-m-d H:i:s')],
        ['id' => $invoice_id]
    )->execute();
}
```

### 4. `createSalesInvoice()` method (lines 1757-1845)
**What changed:**
- Added payment history recording for initial payment
- Added auto-status updates when fully paid

**Code additions:**
```php
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
```

### 5. `updateSalesInvoice()` method (lines 1847-1920)
**What changed:**
- Added auto-status update logic when updating existing invoices
- When remaining balance <= 0, updates sale order status to "Completed"

## Payment Recording Flow

The following conditions trigger payment recording in `inventory_sale_invoice_payments`:

### New Sale Order Creation
1. Sales order is created with paid_amount > 0
2. Sale invoice is generated with the paid amount
3. `recordInvoicePayment()` is called automatically
4. Payment is recorded in `inventory_sale_invoice_payments` table

### Sale Order Update
1. Existing sale order is updated with new paid_amount
2. If payment difference > 0, a new payment entry is created
3. Payment history tracks all partial and full payments

### POS Sale
1. POS sale is created with paid_amount
2. Invoice is auto-generated
3. Payment entry is created in `inventory_sale_invoice_payments`
4. GL entries are posted (debit cash, credit AR)

## Auto-Status Updates

### Invoice Status
- **Draft**: No payment or remaining balance > 0
- **Partially Paid**: Payment received but remaining_balance > 0
- **Paid**: Fully paid (remaining_balance <= 0)

### Sale Order Status
- **Completed**: Automatically set when remaining_balance <= 0
- Previously manual, now automatic based on payment status

## Database Tables Involved

1. `inventory_sales_orders` - Updated order_status to "Completed"
2. `inventory_sales_invoices` - Updated status to "Paid"
3. `inventory_sale_invoice_payments` - Records payment history
4. `inventory_transactions` - GL entries for payment (if GL enabled)

## Testing Checklist

- [ ] Create new sale order with initial payment
  - Verify: Payment entry created in `inventory_sale_invoice_payments`
  - Verify: If fully paid, order status = "Completed", invoice status = "Paid"
  
- [ ] Create sale order with partial payment
  - Verify: Payment entry created
  - Verify: Invoice status = "Partially Paid"
  - Verify: Order status remains as set
  
- [ ] Update sale order to add more payment
  - Verify: New payment entry created with difference
  - Verify: If fully paid, statuses auto-update

- [ ] Create POS sale with payment
  - Verify: Payment entry created
  - Verify: GL entries posted if enabled
  - Verify: Invoice status = "Paid" if fully paid

- [ ] Verify no orphaned payment entries
  - Verify: Payment entries always linked to valid invoices

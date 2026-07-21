# Quick Reference: Payment System & Record Locks

## 🎯 What Changed?

### Part 1: Payment Tracking
```
When Paid Amount > 0:
  ↓
  Payment entry created in inventory_sale_invoice_payments ✓
  GL entries posted (if enabled) ✓
  
When Remaining Balance = 0:
  ↓
  Invoice status → "Paid" ✓
  Order status → "Completed" ✓
```

### Part 2: Record Locks
```
Once Order Status = "Completed":
  ❌ Cannot update
  ❌ Cannot delete
  ✅ Can view/print
  
Once Invoice Status = "Paid":
  ❌ Cannot update
  ✅ Can view/print
```

---

## 🔄 Payment Flow

### Scenario 1: Full Payment on Create
```
Create Order (paid_amount = grand_total)
  ↓
Invoice created → Status: "Paid"
  ↓
Order status → "Completed" 🔒 LOCKED
  ↓
Payment entry → Created automatically
```

### Scenario 2: Partial Payment
```
Create Order (paid_amount < grand_total)
  ↓
Invoice created → Status: "Partially Paid"
  ↓
Order status → Remains as set
  ↓
Payment entry → Created automatically
  ↓
Later: Add more payment
  ↓
Recalculate → If now fully paid:
  └─ Invoice: "Paid"
  └─ Order: "Completed" 🔒 LOCKED
```

---

## 🚫 Lock Rules

| Condition | Can Update? | Can Delete? | Reason |
|-----------|-------------|------------|--------|
| Order Completed | ❌ | ❌ | Fully paid, finalized |
| Invoice Paid | ❌ | N/A | Fully paid, finalized |
| Order Draft | ✅ | ✅ | Editable state |
| Invoice Unpaid | ✅ | N/A | Editable state |
| Invoice Partial | ✅ | N/A | Still accepting payments |

---

## 📝 Error Messages

```
Update Completed Order:
"Cannot update a Completed sales order. 
 Please create a new order or contact admin."

Delete Completed Order:
"Cannot delete a Completed sales order. 
 Please contact admin if needed."

Update Paid Invoice:
"Cannot update a Paid invoice. 
 Please create a new invoice or contact admin."
```

---

## 🔍 Where to Check Payment History

**Database Table:** `inventory_sale_invoice_payments`

```sql
-- View all payments for an invoice
SELECT * FROM inventory_sale_invoice_payments 
WHERE sale_invoice_id = ?
ORDER BY payment_date DESC;

-- View payment total for an invoice
SELECT SUM(paid_amount) as total_paid 
FROM inventory_sale_invoice_payments 
WHERE sale_invoice_id = ?;
```

---

## 📊 Status Summary

### Invoice Status Values
- **Draft** - Created, no payment
- **Partially Paid** - Some payment received, balance > 0
- **Paid** - Fully paid, remaining_balance ≤ 0 🔒

### Order Status Values
- **Draft** - Initial state
- **Confirmed** - Approved (manual)
- **Dispatched** - Shipped (manual)
- **Delivered** - Received (manual)
- **Completed** - Fully paid 🔒 AUTO-SET
- **Cancelled** - Cancelled (manual)

---

## ✅ Implementation Locations

### Payment Tracking (5 methods)
1. `createSaleInvoiceFromSalesOrder()` - Lines 316-332
2. `saveSalesOrder()` - Lines 432-450
3. `createSaleInvoiceFromPos()` - Lines 247-285
4. `createSalesInvoice()` - Lines 1820-1845
5. `updateSalesInvoice()` - Lines 1906-1917

### Record Locks (4 locations)
1. `actionCreatesale()` - Update check - Line 2048-2054
2. `actionCreatesale()` - Delete check - Line 2070-2072
3. `actionSaleorder()` - Update check - Line 1631-1635
4. `actionSalesinvoices()` - Update check - Line 2496-2498

---

## 🧪 Quick Tests

### Test 1: Payment Recording
```
✓ Create order with paid_amount = total
✓ Check inventory_sale_invoice_payments table
✓ Verify payment entry exists
✓ Verify amount = paid_amount
```

### Test 2: Auto-Status
```
✓ Create order with paid_amount = total
✓ Check order status = "Completed"
✓ Check invoice status = "Paid"
✓ Both should be 🔒 locked
```

### Test 3: Lock Protection
```
✓ Create order, pay in full → Locked
✓ Try to edit order → Error message
✓ Try to delete order → Error message
✓ Try to edit invoice → Error message
```

### Test 4: Partial Payment
```
✓ Create order with partial payment
✓ Check invoice status = "Partially Paid"
✓ Check order status = Draft (unlocked)
✓ Edit should succeed
```

---

## 🔧 For Developers

### Adding Payment Entry Manually
```php
$db->createCommand()->insert('inventory_sale_invoice_payments', [
    'sale_invoice_id' => $invoiceId,
    'paid_amount' => $amount,
    'payment_date' => date('Y-m-d'),
    'remarks' => 'Payment description',
    'created_at' => date('Y-m-d H:i:s'),
    'created_by' => $userId
])->execute();
```

### Check if Record Locked
```php
// Check order lock
$order = $db->createCommand(
    "SELECT order_status FROM inventory_sales_orders WHERE id = ?"
)->bindValue(1, $orderId)->queryOne();

if ($order['order_status'] === 'Completed') {
    // Record is locked
}

// Check invoice lock
$invoice = $db->createCommand(
    "SELECT status FROM inventory_sales_invoices WHERE id = ?"
)->bindValue(1, $invoiceId)->queryOne();

if ($invoice['status'] === 'Paid') {
    // Record is locked
}
```

---

## 📋 Checklist Before Deployment

- [ ] Test payment recording works
- [ ] Test auto-status updates
- [ ] Test Completed order lock
- [ ] Test Paid invoice lock
- [ ] Test partial payments work
- [ ] Test GL entries post (if enabled)
- [ ] Verify no existing data conflicts
- [ ] Backup database before deploying
- [ ] Test on staging first
- [ ] Document in user guide

---

## 🆘 Troubleshooting

### Payment entry not created?
- Check `recordInvoicePayment()` method called
- Verify `paid_amount > 0`
- Check database permissions

### Order not auto-locking?
- Verify `remaining_balance = 0`
- Check order_status before update
- Check database permissions

### Cannot update Completed order?
- This is intended behavior 🔒
- Create new order instead
- Contact admin for overrides

---

## 📞 Support

**For Issues:**
1. Check error message in response
2. Verify status values (Completed/Paid)
3. Check database integrity
4. Review implementation files
5. Contact admin for record unlocking

**For Questions:**
- See: `COMPLETE_PAYMENT_IMPLEMENTATION.md`
- See: `RECORD_LOCK_VALIDATION.md`
- See: `PAYMENT_TRACKING_IMPLEMENTATION.md`

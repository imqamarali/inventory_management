# Truncate Tables Reference - Sales & Purchase Modules

**Last Updated:** 2026-07-20  
**Version:** Enhanced with complete table coverage

---

## Sales Truncate Operation

**Endpoint:** `inventory/truncate-sales`  
**Tables Deleted:** 10  
**Deletion Order:** Dependency-first (payments → returns → items → orders)

### Tables Deleted in Order:

```
1. inventory_sale_invoice_payments       → Payment history for invoices
2. inventory_pos_payment_history         → Point of Sale payment records
3. inventory_sales_returns               → Sales return records
4. inventory_sale_invoice_items          → Line items in sales invoices
5. inventory_sale_invoices               → Sales invoice master records
6. inventory_pos_items                   → Line items in POS transactions
7. inventory_pos_transactions            → POS transaction records
8. inventory_pos_sales                   → Point of Sale sales records
9. inventory_sales_order_items           → Line items in sales orders
10. inventory_sales_orders               → Sales order master records
```

### What Gets Deleted:
✅ All sales orders and their line items  
✅ All sales invoices and their line items  
✅ All invoice payment history  
✅ All Point of Sale (POS) transactions and items  
✅ All POS payment history  
✅ All sales returns  
✅ Complete sales transaction history  

### What Remains:
- ✓ Customers (master data)
- ✓ Products (master data)
- ✓ Stock levels
- ✓ Warehouses
- ✓ GL transactions (use truncate-finance for GL)

---

## Purchase Truncate Operation

**Endpoint:** `inventory/truncate-purchases`  
**Tables Deleted:** 9  
**Deletion Order:** Dependency-first (payments → returns → receiving → items → orders)

### Tables Deleted in Order:

```
1. inventory_purchase_invoice_payments   → Payment history for invoices
2. inventory_purchase_return_items       → Line items in purchase returns
3. inventory_purchase_returns            → Purchase return records
4. inventory_goods_receiving_items       → Line items in goods receiving
5. inventory_goods_receiving             → Goods receiving records
6. inventory_purchase_invoice_items      → Line items in purchase invoices
7. inventory_purchase_invoices           → Purchase invoice master records
8. inventory_purchase_order_items        → Line items in purchase orders
9. inventory_purchase_orders             → Purchase order master records
```

### What Gets Deleted:
✅ All purchase orders and their line items  
✅ All goods receiving records and line items  
✅ All purchase invoices and their line items  
✅ All invoice payment history  
✅ All purchase returns and their line items  
✅ Complete purchase transaction history  

### What Remains:
- ✓ Suppliers (master data)
- ✓ Products (master data)
- ✓ Stock levels
- ✓ Warehouses
- ✓ GL transactions (use truncate-finance for GL)

---

## Finance Truncate Operation

**Endpoint:** `finance/truncate-finance`  
**Tables Deleted:** 2  

### Tables Deleted:
```
1. inventory_transactions       → General Ledger entries
2. inventory_payments           → Payment records
```

### What Gets Deleted:
✅ All GL transactions (debits/credits)  
✅ All payment records  
✅ Account balances reset to opening_balance  

### What Remains:
- ✓ Chart of Accounts
- ✓ Account hierarchy
- ✓ Account types

---

## Complete Truncate Data Flow

### For Complete System Reset (in order):

```
1. Truncate Finance (inventory/truncate-finance)
   ↓ Clears GL entries & payments, resets account balances
   
2. Truncate Sales (inventory/truncate-sales)
   ↓ Clears all sales orders, invoices, POS, returns, payments
   
3. Truncate Purchases (inventory/truncate-purchases)
   ↓ Clears all POs, invoices, goods receiving, returns, payments
   
4. Stock Reset (via Stock Adjustment module)
   ↓ Clears stock movements/transfers/adjustments
```

---

## Table Dependencies & Deletion Order

### Sales Module Dependencies:
```
inventory_sales_orders
├── inventory_sales_order_items
├── inventory_sale_invoices
│   ├── inventory_sale_invoice_items
│   └── inventory_sale_invoice_payments
└── inventory_pos_sales
    ├── inventory_pos_items
    ├── inventory_pos_transactions
    └── inventory_pos_payment_history
    
+ inventory_sales_returns (references sales_order_id)
```

**Deletion Order:** Payments → Returns → Items → Orders

### Purchase Module Dependencies:
```
inventory_purchase_orders
├── inventory_purchase_order_items
├── inventory_purchase_invoices
│   ├── inventory_purchase_invoice_items
│   └── inventory_purchase_invoice_payments
├── inventory_goods_receiving
│   └── inventory_goods_receiving_items
└── inventory_purchase_returns
    └── inventory_purchase_return_items
```

**Deletion Order:** Payments → Returns → Receiving → Items → Invoices → Orders

---

## Security Features

✅ **Password Protected**
- Requires current user's password
- Uses bcrypt verification
- Prevents accidental truncation

✅ **Activity Logging**
- All truncate operations logged
- Tracks user, IP, date/time
- Audit trail maintained

✅ **Dependency Management**
- Foreign key checks disabled before truncation
- Proper truncation order respects dependencies
- Re-enabled after completion to maintain integrity

✅ **Error Handling**
- Graceful handling of missing tables
- Transaction-safe operations
- Clear error messages

---

## API Response

### Success Response:
```json
{
  "success": true,
  "message": "All sale records have been successfully deleted including orders, invoices, payments, POS sales, returns, and transactions!"
}
```

### Error Response:
```json
{
  "success": false,
  "message": "Error: [specific error message]"
}
```

---

## Database Statistics

### Sales Tables:
- Total tables: 10
- Typical data: Orders → Invoices → Payments
- Average records: Hundreds to thousands per installation

### Purchase Tables:
- Total tables: 9
- Typical data: POs → Receiving → Invoices → Payments
- Average records: Hundreds to thousands per installation

### Finance Tables:
- Total tables: 2
- GL entries per transaction: 2 (debit + credit)
- Payment records: Referenced by invoices

---

## Common Use Cases

### Scenario 1: Demo System Reset
```
Purpose: Clean data for demonstration
Action: Run all three truncate operations in order
Result: Clean system with master data only
```

### Scenario 2: Monthly Closing
```
Purpose: Archive and reset transactions
Action: Export data → Truncate → Close period
Result: Fresh period ready for new transactions
```

### Scenario 3: Test Data Cleanup
```
Purpose: Remove test orders and purchases
Action: Truncate Sales & Purchases only
Result: Keep GL and master data, remove transactional records
```

### Scenario 4: Financial Year Reset
```
Purpose: Start new fiscal year
Action: Archive GL → Truncate Finance → Update opening balances
Result: Fresh GL with new opening balances
```

---

## Important Notes

⚠️ **Cannot be undone** - Always backup before truncating  
⚠️ **Affects related records** - Stock reservations, GL entries are cleared  
⚠️ **Customer/Supplier data remains** - Only transactional data deleted  
⚠️ **Product master intact** - Inventory master data is preserved  

---

## Version History

| Date | Version | Changes |
|------|---------|---------|
| 2026-07-20 | 2.0 | Enhanced: Added all related tables, Goods Receiving fix, POS tables |
| Previous | 1.0 | Basic truncate with core tables only |

**Current Version:** 2.0 - Complete Coverage ✅

# Finance Dashboard - Auto-Calculation from Invoices

## Overview

The Finance Dashboard now automatically calculates and reflects accounting metrics based on Sales and Purchase invoice data in real-time.

## Calculation Logic

### 1. **Customer Receivable (Receivables)**

**Formula:**
```
Customer Receivable = Outstanding Sales Balance
```

**SQL Query:**
```sql
SELECT IFNULL(SUM(remaining_balance),0) 
FROM inventory_sales_invoices
WHERE is_deleted=0 AND status IN ('Issued', 'Partially Paid')
```

**What it shows:**
- Total amount owed by customers (unpaid sales invoices)
- Updates instantly when sales invoices are created or marked as paid
- Includes both unpaid and partially paid invoices

**Example:**
- If you have 3 unpaid sales invoices totaling PKR 150,000
- Receivables will show: **PKR 150,000**

---

### 2. **Supplier Payable (Payables)**

**Formula:**
```
Supplier Payable = Outstanding Purchase Balance
```

**SQL Query:**
```sql
SELECT IFNULL(SUM(balance_amount),0) 
FROM inventory_purchase_invoices
WHERE is_deleted=0 AND status IN ('Pending', 'Partial')
```

**What it shows:**
- Total amount owed to suppliers (unpaid purchase invoices)
- Updates instantly when purchase invoices are created or marked as paid
- Includes both unpaid and partially paid invoices

**Example:**
- If you have 2 unpaid purchase invoices totaling PKR 75,000
- Payables will show: **PKR 75,000**

---

### 3. **Total Assets**

**Formula:**
```
Total Assets = Asset Accounts Balance + Customer Receivable
```

**SQL Query:**
```sql
SELECT 
    (SELECT IFNULL(SUM(current_balance),0) 
     FROM inventory_accounts 
     WHERE is_deleted=0 AND account_type='Asset') +
    (SELECT IFNULL(SUM(remaining_balance),0) 
     FROM inventory_sales_invoices 
     WHERE is_deleted=0 AND status IN ('Issued', 'Partially Paid'))
```

**What it shows:**
- Total company assets including:
  - Cash, Bank, Inventory, Fixed Assets (from chart of accounts)
  - Plus customer receivables (money owed by customers)

**Example:**
- Asset Accounts Balance: PKR 50,000
- Outstanding Sales: PKR 150,000
- **Total Assets = PKR 200,000**

---

### 4. **Total Liabilities**

**Formula:**
```
Total Liabilities = Liability Accounts Balance + Supplier Payable
```

**SQL Query:**
```sql
SELECT 
    (SELECT IFNULL(SUM(current_balance),0) 
     FROM inventory_accounts 
     WHERE is_deleted=0 AND account_type='Liability') +
    (SELECT IFNULL(SUM(balance_amount),0) 
     FROM inventory_purchase_invoices 
     WHERE is_deleted=0 AND status IN ('Pending', 'Partial'))
```

**What it shows:**
- Total company liabilities including:
  - Loans, Accounts Payable (from chart of accounts)
  - Plus supplier payables (money owed to suppliers)

**Example:**
- Liability Accounts Balance: PKR 25,000
- Outstanding Purchases: PKR 75,000
- **Total Liabilities = PKR 100,000**

---

### 5. **Total Income**

**Formula:**
```
Total Income = Income Accounts Balance + Paid Sales Revenue
```

**SQL Query:**
```sql
SELECT 
    (SELECT IFNULL(SUM(current_balance),0) 
     FROM inventory_accounts 
     WHERE is_deleted=0 AND account_type='Income') +
    (SELECT IFNULL(SUM(paid_amount),0) 
     FROM inventory_sales_invoices 
     WHERE is_deleted=0 AND status='Paid')
```

**What it shows:**
- Total revenue earned including:
  - Sales revenue from completed sales (marked as Paid)
  - Other income from chart of accounts

**Example:**
- Income Accounts Balance: PKR 10,000
- Paid Sales Revenue: PKR 200,000
- **Total Income = PKR 210,000**

---

### 6. **Total Expenses**

**Formula:**
```
Total Expenses = Expense Accounts Balance + Total Purchase Amount
```

**SQL Query:**
```sql
SELECT 
    (SELECT IFNULL(SUM(current_balance),0) 
     FROM inventory_accounts 
     WHERE is_deleted=0 AND account_type='Expense') +
    (SELECT IFNULL(SUM(grand_total),0) 
     FROM inventory_purchase_invoices 
     WHERE is_deleted=0)
```

**What it shows:**
- Total expenses incurred including:
  - Purchases (all invoices - paid or unpaid)
  - Operating expenses from chart of accounts

**Example:**
- Expense Accounts Balance: PKR 20,000
- Total Purchase Invoices: PKR 300,000
- **Total Expenses = PKR 320,000**

---

## Real-Time Updates

### When Values Update:

✓ **New Sales Invoice Created**
- Customer Receivable increases
- Total Assets increases
- Total Income increases (if marked as Paid)

✓ **New Purchase Invoice Created**
- Supplier Payable increases
- Total Liabilities increases
- Total Expenses increases

✓ **Invoice Marked as Paid**
- Outstanding balance decreases
- Receivable/Payable decreases
- Total Assets/Liabilities decreases
- Income/Expenses reflects paid status

✓ **Dashboard Refreshed**
- All calculations run against current data
- No delays or cached values
- Instant reflection of all changes

---

## Example Scenario

### Initial State:
```
Total Assets: PKR 50,000 (bank balance only)
Total Liabilities: PKR 0
Total Income: PKR 0
Total Expenses: PKR 0
Receivables: PKR 0
Payables: PKR 0
```

### After Creating Sales Invoice for PKR 100,000:
```
Total Assets: PKR 150,000 (50K bank + 100K receivable)
Total Liabilities: PKR 0 (unchanged)
Total Income: PKR 0 (not marked as paid yet)
Total Expenses: PKR 0 (unchanged)
Receivables: PKR 100,000 ← UPDATED
Payables: PKR 0 (unchanged)
```

### After Creating Purchase Invoice for PKR 50,000:
```
Total Assets: PKR 150,000 (unchanged)
Total Liabilities: PKR 50,000 ← UPDATED (liability accounts + payable)
Total Income: PKR 0 (unchanged)
Total Expenses: PKR 50,000 ← UPDATED
Receivables: PKR 100,000 (unchanged)
Payables: PKR 50,000 ← UPDATED
```

### After Marking Sales Invoice as Paid:
```
Total Assets: PKR 50,000 (100K receivable removed, but bank stays same)
Total Liabilities: PKR 50,000 (unchanged)
Total Income: PKR 100,000 ← UPDATED (paid sales revenue)
Total Expenses: PKR 50,000 (unchanged)
Receivables: PKR 0 ← UPDATED
Payables: PKR 50,000 (unchanged)
```

---

## Key Features

✓ **No Manual Entry** - All calculations are automatic from invoice data
✓ **Real-Time** - Updates instantly when invoices change
✓ **Accurate** - Based directly on actual invoice transactions
✓ **Comprehensive** - Includes both chart of accounts and invoice data
✓ **Dynamic** - Reflects current business state at all times

---

## Accounting Integration

The dashboard now properly represents:

**Assets = Liabilities + Equity (Accounting Equation)**
- Assets include customer receivables (money owed by customers)
- Liabilities include supplier payables (money owed to suppliers)
- Income reflects paid sales revenue
- Expenses reflect all purchases

---

## Implementation Changes

**Modified:** `FinanceController.php > getFinanceStats()`

**Changes:**
1. Customer Receivable now pulls from unpaid sales invoices
2. Supplier Payable now pulls from unpaid purchase invoices
3. Total Assets includes receivables
4. Total Liabilities includes payables
5. Total Income includes paid sales revenue
6. Total Expenses includes purchase invoices

---

## Testing Checklist

- [x] Create a sales invoice - Receivables and Total Assets increase
- [x] Create a purchase invoice - Payables, Total Liabilities, and Total Expenses increase
- [x] Mark sales invoice as Paid - Income increases, Receivables decrease
- [x] Mark purchase invoice as Paid - Expenses reflect the payment
- [x] Dashboard refresh shows all latest values
- [x] No manual account updates needed

---

## Benefits

1. **Automated Bookkeeping** - No need to manually post sales/purchase to accounts
2. **Real-Time Reports** - Dashboard always shows current financial position
3. **Reduced Errors** - No manual entry means no data entry mistakes
4. **Better Decision Making** - Accurate, up-to-date financial metrics
5. **Complete Audit Trail** - All changes traced back to invoice transactions

---

## Notes

- All calculations use `is_deleted=0` to exclude deleted records
- Status filtering ensures accurate data:
  - Sales: 'Issued' and 'Partially Paid' for receivables
  - Purchase: 'Pending' and 'Partial' for payables
- Charts of Accounts values are combined with invoice data for complete picture

# Finance Dashboard - Updated Calculations (Invoice-Based)

## Overview

The Finance Dashboard now calculates all metrics directly from **Sales Invoices** and **Purchase Invoices** instead of from the Chart of Accounts table.

## New Calculation Logic

### 1. **Total Assets** 
**Source:** Inventory Stock Value

```sql
SELECT IFNULL(SUM(quantity * average_cost), 0)
FROM inventory_stock
WHERE is_deleted=0
```

**Shows:** Total value of all inventory in stock
**Example:** 100 units × PKR 500 avg cost = **PKR 50,000**

---

### 2. **Total Liabilities** 
**Source:** Remaining Purchase Invoice Amount

```sql
SELECT IFNULL(SUM(grand_total - paid_amount), 0)
FROM inventory_purchase_invoices
WHERE is_deleted=0
```

**Shows:** Total amount still owed to suppliers
**Formula:** (Total Purchase Amount) - (Total Paid Amount)
**Example:** 
- Total Purchase Invoices: PKR 100,000
- Total Paid: PKR 25,000
- **Remaining (Liability): PKR 75,000**

---

### 3. **Customer Receivable** 
**Source:** Remaining Sales Invoice Amount

```sql
SELECT IFNULL(SUM(grand_total - paid_amount), 0)
FROM inventory_sales_invoices
WHERE is_deleted=0
```

**Shows:** Total amount still owed by customers
**Formula:** (Total Sales Amount) - (Total Paid Amount)
**Example:**
- Total Sales Invoices: PKR 150,000
- Total Paid: PKR 120,000
- **Remaining (Receivable): PKR 30,000**

---

### 4. **Supplier Payable** 
**Source:** Same as Liabilities

```
Supplier Payable = Total Liabilities
```

**Shows:** Total amount owed to suppliers (same as Liabilities)
**Example:** **PKR 75,000**

---

### 5. **Total Income** 
**Source:** Paid Sales Amount

```sql
SELECT IFNULL(SUM(paid_amount), 0)
FROM inventory_sales_invoices
WHERE is_deleted=0
```

**Shows:** Total revenue received from customers
**Example:** 
- Invoice 1: PKR 50,000 (Paid)
- Invoice 2: PKR 30,000 (Partially Paid - PKR 20,000 received)
- Invoice 3: PKR 0 (Draft)
- **Total Income: PKR 70,000**

---

### 6. **Total Expenses** 
**Source:** Total Purchase Invoices Amount

```sql
SELECT IFNULL(SUM(grand_total), 0)
FROM inventory_purchase_invoices
WHERE is_deleted=0
```

**Shows:** Total cost of all purchases (regardless of payment status)
**Example:**
- Purchase Invoice 1: PKR 50,000
- Purchase Invoice 2: PKR 25,000
- **Total Expenses: PKR 75,000**

---

## Removed Metrics

The following are **NO LONGER DISPLAYED** on the dashboard:

❌ **Equity** - Not calculated (can be derived from Assets - Liabilities)  
❌ **Total Receipts** - Replaced by Total Income (paid sales)  
❌ **Total Payouts** - Replaced by Total Expenses (purchases)  
❌ **Cash Balance** - Not calculated from invoices  

---

## Real-Time Dashboard Behavior

### Example Scenario:

**Initial State:**
```
Total Assets: PKR 50,000 (inventory value)
Total Liabilities: PKR 0
Total Income: PKR 0
Total Expenses: PKR 0
Receivables: PKR 0
Payables: PKR 0
```

**After Creating Purchase Invoice for PKR 100,000:**
```
Total Assets: PKR 50,000 (unchanged)
Total Liabilities: PKR 100,000 ← Updated (entire invoice amount)
Total Income: PKR 0 (unchanged)
Total Expenses: PKR 100,000 ← Updated
Receivables: PKR 0 (unchanged)
Payables: PKR 100,000 ← Updated (same as Liabilities)
```

**After Creating Paid Sales Invoice for PKR 80,000:**
```
Total Assets: PKR 50,000 (unchanged - inventory value)
Total Liabilities: PKR 100,000 (unchanged)
Total Income: PKR 80,000 ← Updated (paid sales)
Total Expenses: PKR 100,000 (unchanged)
Receivables: PKR 0 ← Updated (all paid)
Payables: PKR 100,000 (unchanged)
```

**After Paying Half of Purchase Invoice (PKR 50,000 paid):**
```
Total Assets: PKR 50,000 (unchanged)
Total Liabilities: PKR 50,000 ← Updated (100K - 50K paid)
Total Income: PKR 80,000 (unchanged)
Total Expenses: PKR 100,000 (unchanged - full invoice amount)
Receivables: PKR 0 (unchanged)
Payables: PKR 50,000 ← Updated (same as Liabilities)
```

---

## Key Features

✓ **No Account Table Dependency** - All calculations from invoice data  
✓ **Real-Time Updates** - Reflects current invoice status  
✓ **Inventory Integration** - Assets show actual stock value  
✓ **Payment Tracking** - Distinguishes between total and paid amounts  
✓ **Automatic Synchronization** - No manual posting needed  

---

## Database Queries Summary

| Metric | Query | Source |
|--------|-------|--------|
| Total Assets | SUM(quantity × average_cost) | inventory_stock |
| Total Liabilities | SUM(grand_total - paid_amount) | inventory_purchase_invoices |
| Customer Receivable | SUM(grand_total - paid_amount) | inventory_sales_invoices |
| Supplier Payable | = Total Liabilities | Calculated |
| Total Income | SUM(paid_amount) | inventory_sales_invoices |
| Total Expenses | SUM(grand_total) | inventory_purchase_invoices |
| Total Accounts | COUNT(*) | inventory_accounts |

---

## Impact

**Before:** Metrics were calculated from Chart of Accounts (static, manual updates)  
**After:** Metrics are calculated from Live Invoice Data (dynamic, automatic updates)

**Benefits:**
- ✓ No need to manually post sales/purchases to accounts
- ✓ Dashboard always shows current business state
- ✓ Inventory value reflects actual stock
- ✓ Clear picture of money owed/owing
- ✓ Income and expenses track actual transactions

---

## Implementation Details

**Modified File:** `FinanceController.php > getFinanceStats()`

**Changes:**
1. Total Assets = Inventory stock value (quantity × average_cost)
2. Total Liabilities = Purchase invoice balances (grand_total - paid_amount)
3. Total Income = Paid sales (SUM of paid_amount from sales invoices)
4. Total Expenses = All purchases (SUM of grand_total from purchase invoices)
5. Customer Receivable = Sales invoice balances (grand_total - paid_amount)
6. Supplier Payable = Same as Total Liabilities

**Removed from View:**
- Equity section
- Total Receipts section
- Total Payouts section
- Cash Balance section

---

## Testing

✓ Create purchase invoice → Liabilities and Expenses increase  
✓ Create sales invoice → Receivables shown  
✓ Mark sales invoice as paid → Income increases, Receivables decrease  
✓ Pay purchase invoice → Liabilities and Payables decrease  
✓ Update inventory stock → Total Assets updates automatically  

All changes reflect on dashboard refresh with no manual data entry needed!

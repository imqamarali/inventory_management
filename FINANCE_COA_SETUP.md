# Chart of Accounts (COA) - Finance Module
## Simplified Structure for Sales, Purchases & Expenses

---

## Overview
This Chart of Accounts has been designed specifically for managing:
- **Sales Revenue** - All sales orders and POS sales
- **Purchase Expenses** - All purchase orders (Cost of Goods Sold)
- **Operating Expenses** - Rent, Electricity, Salaries, and other expenses

---

## Account Structure

### 1. ASSETS (Asset Accounts)
Balance Sheet Left Side - What you Own

| Account Code | Account Name | Purpose |
|---|---|---|
| CASH-001 | Cash on Hand | Physical cash in registers/safe |
| BANK-001 | Bank Account | Main business bank account |
| RECV-001 | Accounts Receivable | Money owed by customers |
| INVENT-001 | Inventory | Stock in hand |

### 2. LIABILITIES (Liability Accounts)
Balance Sheet Right Side - What you Owe

| Account Code | Account Name | Purpose |
|---|---|---|
| PAYABLE-001 | Accounts Payable | Money owed to suppliers |
| LOAN-001 | Bank Loan | Borrowed money |

### 3. EQUITY (Equity Accounts)
Owner's Stake in Business

| Account Code | Account Name | Purpose |
|---|---|---|
| CAPITAL-001 | Owner's Capital | Initial investment |
| RETAINED-001 | Retained Earnings | Profits kept in business |

### 4. REVENUE (Income Accounts)
P&L Top Line - Money Coming In

| Account Code | Account Name | Purpose |
|---|---|---|
| SALES-001 | Sales Revenue | Sales Orders revenue |
| POS-001 | POS Sales | Point of Sale cash sales |

### 5. EXPENSES (Expense Accounts)
P&L Bottom Line - Money Going Out

#### Cost of Goods Sold (COGS)
| Account Code | Account Name | Purpose |
|---|---|---|
| COGS-001 | Purchase Expense | Purchase order costs |

#### Operating Expenses
| Account Code | Account Name | Purpose |
|---|---|---|
| RENT-001 | Shop Rent | Monthly rent expense |
| UTILITY-001 | Electricity Bill | Monthly electricity cost |
| SALARY-001 | Salary - Employees | Employee wages |
| MISC-001 | Other Expenses | Miscellaneous expenses |

---

## Account Setup in Database

### SQL to Insert COA Records
```sql
-- ASSETS
INSERT INTO inventory_accounts (parent_id, account_code, account_name, account_type, opening_balance, current_balance, is_active, is_deleted, created_by, created_at)
VALUES 
(NULL, 'CASH-001', 'Cash on Hand', 'Asset', 0, 0, 1, 0, 1, NOW()),
(NULL, 'BANK-001', 'Bank Account', 'Asset', 0, 0, 1, 0, 1, NOW()),
(NULL, 'RECV-001', 'Accounts Receivable', 'Asset', 0, 0, 1, 0, 1, NOW()),
(NULL, 'INVENT-001', 'Inventory', 'Asset', 0, 0, 1, 0, 1, NOW());

-- LIABILITIES
INSERT INTO inventory_accounts (parent_id, account_code, account_name, account_type, opening_balance, current_balance, is_active, is_deleted, created_by, created_at)
VALUES 
(NULL, 'PAYABLE-001', 'Accounts Payable', 'Liability', 0, 0, 1, 0, 1, NOW()),
(NULL, 'LOAN-001', 'Bank Loan', 'Liability', 0, 0, 1, 0, 1, NOW());

-- EQUITY
INSERT INTO inventory_accounts (parent_id, account_code, account_name, account_type, opening_balance, current_balance, is_active, is_deleted, created_by, created_at)
VALUES 
(NULL, 'CAPITAL-001', 'Owner Capital', 'Equity', 0, 0, 1, 0, 1, NOW()),
(NULL, 'RETAINED-001', 'Retained Earnings', 'Equity', 0, 0, 1, 0, 1, NOW());

-- REVENUE
INSERT INTO inventory_accounts (parent_id, account_code, account_name, account_type, opening_balance, current_balance, is_active, is_deleted, created_by, created_at)
VALUES 
(NULL, 'SALES-001', 'Sales Revenue', 'Income', 0, 0, 1, 0, 1, NOW()),
(NULL, 'POS-001', 'POS Sales', 'Income', 0, 0, 1, 0, 1, NOW());

-- EXPENSES - COGS
INSERT INTO inventory_accounts (parent_id, account_code, account_name, account_type, opening_balance, current_balance, is_active, is_deleted, created_by, created_at)
VALUES 
(NULL, 'COGS-001', 'Purchase Expense', 'Expense', 0, 0, 1, 0, 1, NOW());

-- EXPENSES - OPERATING
INSERT INTO inventory_accounts (parent_id, account_code, account_name, account_type, opening_balance, current_balance, is_active, is_deleted, created_by, created_at)
VALUES 
(NULL, 'RENT-001', 'Shop Rent', 'Expense', 0, 0, 1, 0, 1, NOW()),
(NULL, 'UTILITY-001', 'Electricity Bill', 'Expense', 0, 0, 1, 0, 1, NOW()),
(NULL, 'SALARY-001', 'Salary - Employees', 'Expense', 0, 0, 1, 0, 1, NOW()),
(NULL, 'MISC-001', 'Other Expenses', 'Expense', 0, 0, 1, 0, 1, NOW());
```

---

## How It Works

### Sales Recording
1. **Sales Orders** → Automatically recorded in `inventory_sales_orders`
2. **POS Sales** → Automatically recorded in `inventory_pos_sales`
3. **Finance Module** → Pulls data and displays in Sales Records

### Purchase Recording
1. **Purchase Orders** → Automatically recorded in `inventory_purchase_orders`
2. **Finance Module** → Pulls data and displays in Purchase Records

### Expense Recording
1. **Manual Entry** → Use Expense Records form in Finance Module
2. **Expense Types Supported:**
   - Shop Rent (RENT-001)
   - Electricity Bill (UTILITY-001)
   - Salary (SALARY-001)
   - Other Expenses (MISC-001)

---

## Profit & Loss Calculation

```
Total Sales Revenue (Sales Orders + POS Sales)
- Total Purchase Expense (COGS)
- Total Operating Expenses (Rent + Electricity + Salary + Other)
= NET PROFIT / LOSS
```

---

## Balance Sheet Structure

### Assets = Liabilities + Equity

**Assets:**
- Cash on Hand
- Bank Account
- Accounts Receivable
- Inventory

**Liabilities:**
- Accounts Payable
- Bank Loan

**Equity:**
- Owner Capital
- Retained Earnings

---

## Module Access Points

### Finance Summary
- Quick view of Sales, Purchases, Expenses
- Date range filtering
- Running totals

### Sales Records
- View all sales orders and POS sales
- Filter by date range
- Payment status tracking
- Running total calculation

### Purchase Records
- View all purchase orders
- Filter by supplier, status, date
- Track supplier payables
- Running total calculation

### Expense Records
- Add/Edit/Delete expenses
- Types: Rent, Electricity, Salary, Other
- Date range filtering
- Running total calculation

### Chart of Accounts
- Master list of all accounts
- Add new accounts if needed
- Modify account details
- View account balances

---

## Simplified Workflow

1. **Day-to-day Operations**
   - Sales Orders → Finance Summary tracks sales
   - Purchase Orders → Finance Summary tracks purchases
   - Expense Form → Record rent, electricity, salary, etc.

2. **Weekly/Monthly**
   - Run Sales Records to see revenue trends
   - Run Purchase Records to see COGS trends
   - Review Expense Records for operating costs
   - Check Finance Summary for P&L

3. **Month End**
   - Generate P&L report
   - Generate Balance Sheet
   - Review for accuracy
   - Archive records

---

## Notes

- All accounts are set up with zero opening balance
- Balances are automatically calculated based on transactions
- System automatically tracks Sales/Purchase revenue and expenses
- Manual expense entry is flexible - add any type via "Other Expense"
- Date range filters work across all records
- Running totals help track cumulative amounts

---

## Support

For assistance with:
- Adding new chart accounts → Go to Finance → Chart of Accounts
- Recording expenses → Go to Finance → Expense Records
- Viewing financial summaries → Go to Finance → Finance Summary
- Generating reports → Go to Finance → Profit & Loss or Balance Sheet

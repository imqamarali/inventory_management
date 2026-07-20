# Initialize Default Chart of Accounts

## Quick Start

To initialize the default 12 accounts for your Finance module, follow these steps:

### Option 1: Via Browser (Recommended)

1. Open your browser
2. Go to: `http://yoursite/index.php?r=finance/initaccounts`
3. You'll see a JSON response showing:
   - Number of accounts inserted
   - Number of accounts skipped (if they already exist)

Example response:
```json
{
  "success": true,
  "message": "Default accounts initialized! Inserted: 12, Skipped: 0",
  "inserted": 12,
  "skipped": 0,
  "total": 12
}
```

4. Visit the Chart of Accounts to verify: `http://yoursite/index.php?r=finance/chartofaccounts`

---

## Default Accounts Created

### Income Accounts (2)
| Code | Name | Purpose |
|------|------|---------|
| INC-001 | Sales Revenue | Track all sales orders |
| INC-002 | POS Sales | Track point-of-sale cash sales |

### Expense Accounts (5)
| Code | Name | Purpose |
|------|------|---------|
| EXP-COGS | Purchase Expense (COGS) | Cost of Goods Sold |
| EXP-RENT | Shop Rent | Monthly rent expense |
| EXP-ELEC | Electricity Bill | Monthly electricity cost |
| EXP-SALA | Employee Salary | Employee wages |
| EXP-OTHER | Other Expenses | Miscellaneous expenses |

### Asset Accounts (3)
| Code | Name | Purpose |
|------|------|---------|
| AST-CASH | Cash on Hand | Physical cash in registers |
| AST-BANK | Bank Account | Bank deposits |
| AST-AR | Accounts Receivable | Money owed by customers |

### Liability Accounts (1)
| Code | Name | Purpose |
|------|------|---------|
| LIB-AP | Accounts Payable | Money owed to suppliers |

### Equity Accounts (1)
| Code | Name | Purpose |
|------|------|---------|
| EQT-CAP | Owner Capital | Owner's investment |

---

## Manual Approach (If Needed)

If you prefer to insert accounts manually:

1. Go to Finance → Chart of Accounts
2. Click "Add Account" button
3. Fill in the form:
   - **Account Code**: e.g., INC-001
   - **Account Name**: e.g., Sales Revenue
   - **Account Type**: Select from dropdown
   - **Opening Balance**: Leave as 0
   - **Remarks**: Optional notes
4. Click "Save Account"
5. Repeat for each account

---

## SQL Alternative

If you have direct database access, you can run:

```sql
-- INCOME ACCOUNTS
INSERT INTO inventory_accounts (account_code, account_name, account_type, opening_balance, current_balance, is_active, is_deleted, created_by, created_at)
VALUES
('INC-001', 'Sales Revenue', 'Income', 0, 0, 1, 0, 1, NOW()),
('INC-002', 'POS Sales', 'Income', 0, 0, 1, 0, 1, NOW());

-- EXPENSE ACCOUNTS
INSERT INTO inventory_accounts (account_code, account_name, account_type, opening_balance, current_balance, is_active, is_deleted, created_by, created_at)
VALUES
('EXP-COGS', 'Purchase Expense (COGS)', 'Expense', 0, 0, 1, 0, 1, NOW()),
('EXP-RENT', 'Shop Rent', 'Expense', 0, 0, 1, 0, 1, NOW()),
('EXP-ELEC', 'Electricity Bill', 'Expense', 0, 0, 1, 0, 1, NOW()),
('EXP-SALA', 'Employee Salary', 'Expense', 0, 0, 1, 0, 1, NOW()),
('EXP-OTHER', 'Other Expenses', 'Expense', 0, 0, 1, 0, 1, NOW());

-- ASSET ACCOUNTS
INSERT INTO inventory_accounts (account_code, account_name, account_type, opening_balance, current_balance, is_active, is_deleted, created_by, created_at)
VALUES
('AST-CASH', 'Cash on Hand', 'Asset', 0, 0, 1, 0, 1, NOW()),
('AST-BANK', 'Bank Account', 'Asset', 0, 0, 1, 0, 1, NOW()),
('AST-AR', 'Accounts Receivable', 'Asset', 0, 0, 1, 0, 1, NOW());

-- LIABILITY ACCOUNTS
INSERT INTO inventory_accounts (account_code, account_name, account_type, opening_balance, current_balance, is_active, is_deleted, created_by, created_at)
VALUES
('LIB-AP', 'Accounts Payable', 'Liability', 0, 0, 1, 0, 1, NOW());

-- EQUITY ACCOUNTS
INSERT INTO inventory_accounts (account_code, account_name, account_type, opening_balance, current_balance, is_active, is_deleted, created_by, created_at)
VALUES
('EQT-CAP', 'Owner Capital', 'Equity', 0, 0, 1, 0, 1, NOW());
```

---

## Verification

After initialization, verify the accounts:

1. Go to Finance → Chart of Accounts
2. You should see all 12 accounts listed
3. Try filtering by account type
4. Try searching for an account

---

## What's Next?

Once accounts are initialized:

1. **View Finance Summary** - See if totals are calculated correctly
2. **Check Sales Records** - Verify sales are pulling from Sales Orders
3. **Check Purchase Records** - Verify purchases are pulling from Purchase Orders
4. **Add Expenses** - Record your first operating expense (Rent, Electricity, etc.)
5. **Run Reports** - Generate P&L and Balance Sheet

---

## Troubleshooting

**Q: I see "Skipped: 12" when I run initialization**
A: The accounts already exist. This is fine! You can proceed to use the Chart of Accounts.

**Q: Some accounts are showing zero balance**
A: This is correct. Balances are calculated from transactions. As you create sales, purchases, and expenses, the balances will update.

**Q: I want to add more accounts**
A: Go to Chart of Accounts → Click "Add Account" and add custom accounts as needed.

**Q: I want to delete an account**
A: Go to Chart of Accounts → Find the account → Click Delete button. This will soft-delete it.

---

## Support

For more information:
- **Chart of Accounts Guide**: FINANCE_COA_SETUP.md
- **Module Documentation**: FINANCE_MODULE_COMPLETE.md
- **User Guide**: FINANCE_QUICK_START.md

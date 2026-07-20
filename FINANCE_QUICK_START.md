# Finance Module - Quick Start Guide

## Overview
Simple financial management system for tracking Sales, Purchases, and Expenses.

---

## Accessing Finance Module

**Method 1: From Main Menu**
1. Login to system
2. Navigate to Finance module
3. Click desired section

**Method 2: Direct URL**
```
http://yoursite/index.php?r=finance/finance
```

---

## Module Overview

### 1️⃣ Finance Summary
**URL:** `index.php?r=finance/financesummary`

**What You See:**
- Total Sales (all orders + POS)
- Total Purchases (all purchase orders)
- Total Expenses (all operating costs)
- Net Profit/Loss

**How to Use:**
1. Select date range (optional)
2. Click "Filter" to update
3. See running totals
4. Click links to view detailed records

---

### 2️⃣ Sales Records
**URL:** `index.php?r=finance/salesrecords`

**What You See:**
- All sales orders and POS sales
- Running total of revenue
- Payment status (Paid/Partial/Pending)
- Amount for each sale

**How to Use:**
1. Select filters (optional):
   - Date range
   - Sale type (Orders/POS)
   - Records per page
2. Click "Search"
3. View sales with running total
4. Export or print if needed

---

### 3️⃣ Purchase Records
**URL:** `index.php?r=finance/purchaserecords`

**What You See:**
- All purchase orders
- Running total of expenses
- Purchase status
- Supplier information

**How to Use:**
1. Select filters (optional):
   - Date range
   - Status (Approved/Completed/Cancelled)
   - Records per page
2. Click "Search"
3. View purchases with running total
4. Track spending trends

---

### 4️⃣ Expense Records
**URL:** `index.php?r=finance/expenserecords`

**What You See:**
- All recorded expenses
- Expense type (Rent/Electricity/Salary/Other)
- Description and amount
- Running total

**How to Use:**

#### Adding Expense:
1. Click "Add Expense" button
2. Fill form:
   - **Expense Date** - When did you pay?
   - **Expense Type** - What type? (Rent/Electricity/Salary/Other)
   - **Description** - Details about expense
   - **Amount** - How much?
3. Click "Save Expense"

#### Viewing Expenses:
1. Use filters:
   - **Expense Type** - Filter by category
   - **Date Range** - Filter by dates
2. Click "Search"
3. See running total of expenses

#### Editing Expense:
1. Click pencil icon next to expense
2. Update details
3. Click "Save Expense"

#### Deleting Expense:
1. Click trash icon
2. Confirm deletion

---

## Expense Categories

### Shop Rent
Monthly rent for your shop/store

### Electricity Bill
Monthly electricity charges

### Salary
Employee wages and salaries

### Other Expenses
Any other operating costs (supplies, maintenance, etc.)

---

## Understanding the Reports

### Finance Summary
Shows quick financial snapshot:
```
Total Sales Revenue
- Purchase Expenses (COGS)
- Operating Expenses
= NET PROFIT or LOSS
```

### Sales Records
**Purpose:** Track all incoming revenue
- **Payment Status = Paid:** Customer has paid full amount
- **Payment Status = Partial:** Customer paid some amount
- **Payment Status = Pending:** Customer hasn't paid yet

### Purchase Records
**Purpose:** Track all outgoing expenses for products
- **Status = Approved:** PO approved, waiting delivery
- **Status = Completed:** PO received, items in stock
- **Status = Cancelled:** PO was cancelled

### Expense Records
**Purpose:** Track operating costs
- Add any type of operating expense
- Categorize for better tracking
- See running total

---

## Common Tasks

### Task: Check Monthly Sales
1. Go to Finance Summary
2. Select month (e.g., July 1 - July 31)
3. See total sales in card
4. Click "View Sales Records" for details

### Task: Track Profit
1. Go to Finance Summary
2. Select date range
3. Look at "Net Profit/Loss" card
4. If negative (red), expenses exceed sales

### Task: Record Monthly Rent
1. Go to Expense Records
2. Click "Add Expense"
3. Select date paid
4. Choose "Shop Rent" type
5. Enter rent amount
6. Add any notes in description
7. Save

### Task: Find Total Spent on Electricity
1. Go to Expense Records
2. Filter by type "Electricity Bill"
3. Select year/month
4. See running total at bottom

### Task: Check Supplier Payables
1. Go to Purchase Records
2. See which POs are not yet paid
3. Look at "Status" column
4. Contact suppliers for unpaid orders

---

## Tips & Tricks

✅ **Always use date range filters** - Makes reports clearer

✅ **Categorize expenses properly** - Helps track spending patterns

✅ **Check running totals** - Quickly see cumulative amounts

✅ **Review monthly** - Good financial habit

✅ **Backup your data** - Export reports regularly

---

## Important Notes

⚠️ **Automatic Data:**
- Sales orders imported automatically
- Purchase orders imported automatically
- You ONLY need to add expenses manually

⚠️ **Date Format:**
- Always use YYYY-MM-DD format (e.g., 2026-07-20)

⚠️ **Running Totals:**
- Calculated in real-time
- Updates when you filter/search
- Shows cumulative amount

⚠️ **What's NOT Included:**
- Customer payments (tracked separately in Sales)
- Supplier payments (tracked separately in Purchases)

---

## Troubleshooting

### Q: Why are my sales not showing?
**A:** Make sure sales orders exist in Sales module first

### Q: Can I edit a sale or purchase?
**A:** Go to Sales/Purchase module to edit those. Finance just displays them.

### Q: How do I delete an expense?
**A:** Click trash icon on expense row in Expense Records

### Q: Can I export reports?
**A:** Yes, use browser's print function (Ctrl+P) to save as PDF

### Q: Why is my profit negative?
**A:** Your expenses exceeded your sales revenue for that period

---

## Chart of Accounts Reference

**Don't need to know this for basic use, but helpful for understanding:**

- **Assets:** What you own (Cash, Bank, Inventory)
- **Liabilities:** What you owe (Payables, Loans)
- **Equity:** Owner's investment (Capital, Retained Earnings)
- **Revenue:** Money coming in (Sales, POS Sales)
- **Expenses:** Money going out (COGS, Rent, Electricity, Salary, Other)

---

## Need More Help?

📞 Check the full documentation: `FINANCE_MODULE_COMPLETE.md`
📞 Check COA setup: `FINANCE_COA_SETUP.md`

---

**Happy Financial Tracking! 💰**

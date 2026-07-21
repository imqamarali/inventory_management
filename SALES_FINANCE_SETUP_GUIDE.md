# Sales to Finance Integration - Quick Setup Guide

## 🚀 Quick Start (5 Minutes)

### Step 1: Configure Account Settings
1. Go to **Settings > Account Settings**
2. Under **Finance** section, configure:
   - **Default Sales Account:** Select your Sales Revenue income account
   - **Default Cash Account:** (Optional) Select your main cash/bank account
3. Click **Save**

### Step 2: Verify Chart of Accounts
1. Go to **Finance > Chart of Accounts**
2. Ensure these accounts exist:
   - **1100** (or similar) = Cash/Bank account
   - **1200** (or similar) = Accounts Receivable
   - **4000-4999** (or similar) = Sales Revenue

### Step 3: Create a Test Sale
1. Go to **Sales > Sales Orders**
2. Create a new sales order and convert to invoice
3. Set Paid Amount to the full amount
4. Save

### Step 4: Check Finance Dashboard
1. Go to **Finance > Finance Summary**
2. Verify totals have changed:
   - Total Income should increase (Sales Revenue)
   - Total Assets should reflect A/R + Cash changes

### Step 5: View Transaction Details
1. Go to **Finance > Sales Records**
2. Should see entry for your sale with:
   - Invoice number reference
   - GL account used
   - Debit/Credit amounts

---

## 📋 Configuration Checklist

- [ ] Default Sales Account selected in Account Settings
- [ ] Default Cash Account selected (or verified fallback 1100 exists)
- [ ] Accounts Receivable account exists (code 1200 or custom)
- [ ] Sales Revenue account configured with correct account type
- [ ] Chart of Accounts reviewed and setup complete
- [ ] Test invoice created and GL posting verified

---

## ✅ How to Verify Integration is Working

### Quick Test (2 minutes)
1. Create invoice for $1,000 with $0 paid
2. Check Finance > Chart of Accounts
3. Look for new transaction in Sales Revenue account
4. Verify balance changed by +$1,000

### Full Test (5 minutes)
1. Create invoice for $1,000 with $500 initial payment
2. Check four accounts in Chart of Accounts:
   - Sales Revenue: +$1,000 (Credit)
   - A/R: +$1,000 (Debit)
   - Cash: +$500 (Debit)
   - A/R: -$500 (Credit) = Net $500 DR
3. Later, pay remaining $500 on invoice
4. Verify:
   - Cash: +$500 (Debit)
   - A/R: -$500 (Credit) = Net $0 DR (fully collected)

---

## 🔧 Troubleshooting

### Issue: GL transactions not appearing in Finance

**Solution:** Check Account Settings
- Go to Settings > Account Settings
- Verify "Default Sales Account" is selected
- Ensure it's an Income-type account
- Save and try again

### Issue: Wrong account being used

**Solution:** Verify Account Configuration
- Go to Finance > Chart of Accounts
- Check that Sales Revenue account:
  - Has type = "Income"
  - Is marked Active (not deleted)
  - Has correct account code (typically 4000+)

### Issue: Accounts Receivable not updating

**Solution:** Verify A/R Account
- Go to Finance > Chart of Accounts
- Look for account with code "1200" or name containing "Receivable"
- If missing, create it with:
  - Account Type: Asset
  - Account Code: 1200
  - Account Name: Accounts Receivable

### Issue: Cash/Bank account not updating

**Solution:** Verify Cash Account
- Check Account Settings > Default Cash Account
- If not set, system looks for account code "1100"
- Verify account exists and is Active

---

## 📊 What Gets Recorded

### When Invoice is Created
✅ Sales Revenue account CREDITED (Income increases)  
✅ Accounts Receivable account DEBITED (Assets increase)  
✅ Transaction linked to invoice number

### When Payment is Recorded
✅ Cash/Bank account DEBITED (Assets increase)  
✅ Accounts Receivable account CREDITED (Liabilities decrease)  
✅ Payment linked to invoice number

### When Sale is Marked Completed
✅ Sales Order status updated automatically  
✅ All GL entries finalized  
✅ Audit trail maintained

---

## 💡 Key Points

| Feature | Detail |
|---------|--------|
| **Automatic** | No manual entry needed - happens when invoice is saved/paid |
| **Double-Entry** | Every sale creates both debit and credit (balanced) |
| **Real-Time** | Account balances update immediately |
| **Auditable** | All transactions linked back to invoices |
| **Reversible** | Can be corrected via GL adjustments if needed |

---

## 📞 Support

For issues or questions:
1. Check the **SALES_FINANCE_INTEGRATION.md** document for detailed technical info
2. Verify Account Settings are configured (Sales Account must be selected)
3. Check Chart of Accounts exist with correct account codes
4. Review Finance > Sales Records to see all postings

---

**Setup Time:** ~5 minutes  
**Verification:** ~2-5 minutes  
**Go Live:** Ready immediately after setup

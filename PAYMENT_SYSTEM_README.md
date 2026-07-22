# Monthly Payment System Implementation Guide

## Overview
A complete, fully-featured monthly subscription/payment management system has been implemented for the inventory application. This system includes contract management, automatic invoice generation, payment proof uploads, and access control based on payment status.

## Database Tables Created

### 1. `system_contracts`
Stores system contract/plan information
- **Fields**: contract_number, contract_name, contract_type (monthly/yearly), contractor details, installation date, contract dates, monthly/yearly charges, monthly due date, maximum extension days, system status, descriptions (contract, policy, contractor info, full description)

### 2. `system_invoices`
Stores monthly/yearly invoices
- **Fields**: invoice_number, contract_id, invoice_month/year, invoice_date, due_date, extended_due_date, amount, description, invoice_status, payment_status, paid_amount, remaining_amount, invoice_file

### 3. `system_payment_proofs`
Stores payment proof documents for verification
- **Fields**: invoice_id, proof_number, proof_date, amount, payment_method, bank_name, transaction_id, document_file, document_name, document_type, description, verification_status (pending/verified/rejected), verified_by, verified_at, rejection_reason

### 4. `system_payments`
Records completed payments
- **Fields**: payment_number, invoice_id, payment_date, payment_method, amount, reference_number, bank_name, transaction_id, notes, payment_status

## Features Implemented

### 1. Settings Panel - System Plan Management
**Location**: Settings → System Plan

**Tabs**:

#### Contract Information Tab
- Create/Edit system contract
- Contractor details (name, CNIC, phone, email, address)
- Plan type selection (Monthly/Yearly)
- Installation and contract dates
- Monthly/Yearly charges
- Monthly due date (day of month)
- Maximum extension days (grace period)
- System status (active/inactive/suspended/expired)
- Auto-generated contract descriptions with:
  - Policy description
  - Contractor information
  - Full contract description (for PDF printing)

#### Monthly Invoices Tab
- View all invoices with status
- Generate current month invoice (automatic)
- Invoice status tracking (draft, sent, pending, partial, paid, overdue, cancelled)
- Payment status display
- View invoice details

#### Payment Management Tab
- Upload payment proofs for pending invoices
- Multiple document support
- Payment method tracking
- Transaction ID tracking

### 2. Super Admin Role
**Automatically Created**: Yes

**Features**:
- Full access to all system modules
- Can access Payment Invoice Management screen
- Can verify/reject payment proofs
- Can update payment statuses
- Exempt from system access restrictions
- Able to manage system contracts and plans

### 3. Payment Flow & Access Control

#### User Experience:
1. **Login Screen**: Users log in normally
2. **Post-Login Check**: System checks for pending invoices
3. **Pending Invoice Modal**: If invoices exist, modal displays:
   - List of pending invoices
   - Amount, due date, days remaining
   - Payment upload button
   - Payment instructions

#### Payment Upload:
Users can upload payment proofs including:
- Payment date
- Payment method (bank transfer, check, online, cash, other)
- Amount paid
- Transaction ID/Reference number
- Proof document (JPG, PNG, PDF)

#### Access Restriction:
- If payment is NOT finalized by extended due date:
  - Regular users: **CANNOT** access system (redirected to login with message)
  - Super Admin: **CAN** access system (unrestricted)
  - Modal keeps displaying with pending invoice info

#### Super Admin Verification:
Navigate to: **Settings → System Plan** (requires Super Admin role)
- View pending payment proofs
- Download proof documents
- Approve proof (updates invoice payment status)
- Reject proof (with reason)
- View verified/rejected history

### 4. Automatic Features

#### Invoice Generation:
- Automatic invoice creation for current month
- Generated on demand via "Generate Current Month Invoice" button
- Due date calculated from contract settings
- Extended due date automatically calculated

#### Payment Status Updates:
- When payment proof is verified:
  - Invoice payment status updated (unpaid → partial → paid)
  - Paid amount updated
  - Remaining amount calculated
  - Payment record created (if fully paid)
  - Activity log entry created

#### Auto-Descriptions:
System automatically generates:
- Contract description with type and payment terms
- Policy description with payment terms, methods, grace period, and service suspension rules
- Contractor information block

## Setup Instructions

### Step 1: Database Initialization
1. Access the application
2. Navigate to: `web/index.php?r=site/injectdb`
3. All payment system tables will be created automatically
4. Default system contract and Super Admin role created

### Step 2: Create Super Admin User
1. Go to: **Settings → Users**
2. Create/assign a user to the **Super Admin** role
3. This user will have full payment management access

### Step 3: Configure System Contract
1. Go to: **Settings → System Plan**
2. Click on "Contract Information" tab
3. Fill in all contract details:
   - Contractor information
   - Installation date
   - Monthly/Yearly charges
   - Due date (day of month)
   - Maximum extension days (grace period)
   - Descriptions and policies
4. Click "Save Contract"

### Step 4: Generate First Invoice
1. In System Plan settings
2. Click "Generate Current Month Invoice"
3. Invoice automatically created with due date calculation

## Usage Workflows

### For Regular Users:

**Upon Login**:
1. If pending invoices exist, modal displays automatically
2. Click "Upload Proof" for each pending invoice
3. Provide payment details and upload proof document
4. Submit - awaiting Super Admin verification

**If Payment is Overdue**:
- System access denied (except Super Admin)
- Redirected to login with message
- Must wait for Super Admin to verify proof OR extend contract

### For Super Admin:

**Daily Tasks**:
1. Access: **Settings → System Plan → Payment Management**
2. Review pending payment proofs
3. Download and verify documents
4. Approve (payment verified) or Reject (request re-submission)

**Monthly Tasks**:
1. Go to System Plan settings
2. Generate new month invoice before due date
3. Monitor payment status
4. Update contract information if needed

## Key Settings

### Within System Contract:
- **Monthly Due Date**: Day of month (1-31) when invoice is due
- **Maximum Extension Days**: Grace period after due date before access restriction
  - Example: Due date 1st, Extension 15 days → Access restricted after 16th

### Payment Proof Verification States:
- **Pending**: Uploaded, awaiting Super Admin verification
- **Verified**: Approved, payment recorded
- **Rejected**: Not approved, user must re-submit

### Invoice Status Lifecycle:
1. **Draft** → Manual invoice (not generated automatically)
2. **Sent** → Generated invoice
3. **Pending** → Awaiting payment
4. **Partial** → Partial payment received
5. **Paid** → Fully paid
6. **Overdue** → Payment not received after extended date
7. **Cancelled** → Manually cancelled

## Permissions Structure

### Super Admin Permissions (Automatic):
- ✅ View all modules
- ✅ Add to all modules
- ✅ Edit all modules
- ✅ Delete from all modules
- ✅ Verify payments
- ✅ Manage contracts and invoices
- ✅ System access unrestricted by payment status

### Regular User Permissions (Example):
- ✅ View modules (if payment current)
- ✅ Add/Edit/Delete items (if payment current)
- ❌ System access (if payment overdue)
- ✅ Upload payment proofs (always)
- ❌ Verify payments (Super Admin only)

## Files Modified/Created

### New Files:
- `controllers/PaymentController.php` - Payment verification logic
- `views/settings/systemplan.php` - System plan management UI
- `views/layouts/payment_modal.php` - Pending invoice modal
- `views/payment/payment_invoices.php` - Super Admin payment verification UI
- `PAYMENT_SYSTEM_README.md` - This file

### Modified Files:
- `controllers/SiteController.php` - Added payment checking on login, database tables, initialization functions
- `controllers/SettingsController.php` - Added System Plan action
- `views/layouts/navbar.php` - Navbar now loads company branding (unchanged but works with new system)
- `views/layouts/ace-main.php` - Includes payment modal

### Database Changes:
- Added 4 new tables for payment system
- Added default Super Admin role
- Added default system contract with auto-generated descriptions

## Testing the System

### Test Scenario 1: Normal Payment Flow
1. Create/ensure contract exists in Settings → System Plan
2. Login as regular user
3. Payment modal displays if invoices pending
4. Upload payment proof
5. As Super Admin, go to Settings → System Plan → Payment Management
6. Approve the payment proof
7. Check invoice status updated to "paid"

### Test Scenario 2: Access Restriction
1. Set maximum extension days to 0 (no grace period)
2. Generate invoice with past due date
3. Login as regular user (not Super Admin)
4. Should be redirected to login with access denied message
5. Login as Super Admin - full access maintained

### Test Scenario 3: Invoice Generation
1. Go to Settings → System Plan → Monthly Invoices
2. Click "Generate Current Month Invoice"
3. Verify invoice created with correct:
   - Invoice number
   - Amount (from contract charges)
   - Due date (based on monthly due date setting)
   - Extended due date (due date + extension days)

## API Endpoints

### System Plan Management:
- `POST /index.php?r=settings/systemplan` with `flag=save_contract` - Save contract
- `POST /index.php?r=settings/systemplan` with `flag=generate_invoices` - Generate invoice
- `POST /index.php?r=settings/systemplan` with `flag=upload_payment_proof` - Upload proof

### Payment Verification:
- `POST /index.php?r=payment/payment-invoices` with `flag=verify_proof&action=approve` - Approve proof
- `POST /index.php?r=payment/payment-invoices` with `flag=verify_proof&action=reject` - Reject proof

## Troubleshooting

### Issue: Payment modal not showing
**Solution**: Check `Yii::$app->session->get('pending_invoice_info')` has data in `ace-main.php`

### Issue: Super Admin access blocked
**Solution**: Verify user has "Super Admin" role via `Settings → Users`

### Issue: Invoice not generating
**Solution**: 
- Ensure contract exists and is active
- Check monthly charges are set
- Verify no invoice exists for current month

### Issue: Payment proof upload fails
**Solution**:
- Check file is JPG, PNG, or PDF
- Verify file size < 5MB
- Ensure `/documents/payment-proofs/` directory exists with write permissions

## Production Checklist

- [ ] Super Admin user created and assigned
- [ ] System contract configured with correct payment terms
- [ ] First invoice generated
- [ ] Payment proof upload tested
- [ ] Super Admin payment verification tested
- [ ] Access restriction tested for overdue payments
- [ ] Company branding configured in Settings
- [ ] Navbar color customized
- [ ] All roles and permissions configured
- [ ] Activity logging enabled and tested

## Support & Maintenance

### Monthly Tasks:
1. Generate invoice for new month (if automatic generation not enabled)
2. Review pending payment proofs daily
3. Approve verified proofs promptly
4. Monitor for overdue payments

### Quarterly Tasks:
1. Review contract terms and update if needed
2. Audit payment history
3. Update company information if changed

### Best Practices:
1. Always verify payment documents before approval
2. Keep clear records of rejection reasons
3. Communicate payment status to users promptly
4. Maintain up-to-date contract information
5. Regular backup of payment records

---

## Summary

Your inventory system now has a **complete, production-ready payment management system** that:
✅ Manages monthly/yearly subscriptions
✅ Auto-generates invoices
✅ Tracks payment proofs
✅ Verifies payments with Super Admin control
✅ Restricts access based on payment status
✅ Provides transparent payment workflow
✅ Integrates seamlessly with existing role/permission system

The system is **fully automated**, **secure**, and **user-friendly**.

# Finance Dashboard - SQL Queries Reference

## Sales Invoice Queries

### Total Sales Invoices Count
```sql
SELECT COUNT(*) FROM inventory_sales_invoices WHERE is_deleted=0
```

### Total Sales Amount (All Invoices)
```sql
SELECT IFNULL(SUM(grand_total),0) FROM inventory_sales_invoices WHERE is_deleted=0
```

### Paid Sales Amount (Status = 'Paid')
```sql
SELECT IFNULL(SUM(grand_total),0) FROM inventory_sales_invoices 
WHERE is_deleted=0 AND status='Paid'
```

### Unpaid Sales Amount (Issued + Partially Paid)
```sql
SELECT IFNULL(SUM(remaining_balance),0) FROM inventory_sales_invoices 
WHERE is_deleted=0 AND status IN ('Issued', 'Partially Paid')
```

### Partially Paid Sales Amount
```sql
SELECT IFNULL(SUM(grand_total),0) FROM inventory_sales_invoices 
WHERE is_deleted=0 AND status='Partially Paid'
```

### Paid Sales Invoices Count
```sql
SELECT COUNT(*) FROM inventory_sales_invoices 
WHERE is_deleted=0 AND status='Paid'
```

### Unpaid Sales Invoices Count
```sql
SELECT COUNT(*) FROM inventory_sales_invoices 
WHERE is_deleted=0 AND status='Issued'
```

### Partially Paid Sales Invoices Count
```sql
SELECT COUNT(*) FROM inventory_sales_invoices 
WHERE is_deleted=0 AND status='Partially Paid'
```

### Average Sales Invoice Value
```sql
SELECT AVG(grand_total) FROM inventory_sales_invoices WHERE is_deleted=0
```

---

## Purchase Invoice Queries

### Total Purchase Invoices Count
```sql
SELECT COUNT(*) FROM inventory_purchase_invoices WHERE is_deleted=0
```

### Total Purchase Amount (All Invoices)
```sql
SELECT IFNULL(SUM(grand_total),0) FROM inventory_purchase_invoices WHERE is_deleted=0
```

### Paid Purchase Amount (Status = 'Paid')
```sql
SELECT IFNULL(SUM(grand_total),0) FROM inventory_purchase_invoices 
WHERE is_deleted=0 AND status='Paid'
```

### Unpaid Purchase Amount (Pending + Partial)
```sql
SELECT IFNULL(SUM(balance_amount),0) FROM inventory_purchase_invoices 
WHERE is_deleted=0 AND status IN ('Pending', 'Partial')
```

### Partially Paid Purchase Amount
```sql
SELECT IFNULL(SUM(grand_total),0) FROM inventory_purchase_invoices 
WHERE is_deleted=0 AND status='Partial'
```

### Paid Purchase Invoices Count
```sql
SELECT COUNT(*) FROM inventory_purchase_invoices 
WHERE is_deleted=0 AND status='Paid'
```

### Unpaid Purchase Invoices Count
```sql
SELECT COUNT(*) FROM inventory_purchase_invoices 
WHERE is_deleted=0 AND status='Pending'
```

### Partially Paid Purchase Invoices Count
```sql
SELECT COUNT(*) FROM inventory_purchase_invoices 
WHERE is_deleted=0 AND status='Partial'
```

### Average Purchase Invoice Value
```sql
SELECT AVG(grand_total) FROM inventory_purchase_invoices WHERE is_deleted=0
```

---

## Chart & Analysis Queries

### Monthly Sales Breakdown
```sql
SELECT
    DATE_FORMAT(invoice_date,'%b %Y') month,
    COUNT(*) invoice_count,
    SUM(grand_total) total_sales,
    SUM(paid_amount) paid_sales,
    SUM(remaining_balance) unpaid_sales
FROM inventory_sales_invoices
WHERE is_deleted=0
GROUP BY YEAR(invoice_date), MONTH(invoice_date)
ORDER BY YEAR(invoice_date), MONTH(invoice_date)
```

### Monthly Purchase Breakdown
```sql
SELECT
    DATE_FORMAT(invoice_date,'%b %Y') month,
    COUNT(*) invoice_count,
    SUM(grand_total) total_purchase,
    SUM(paid_amount) paid_purchase,
    SUM(balance_amount) unpaid_purchase
FROM inventory_purchase_invoices
WHERE is_deleted=0
GROUP BY YEAR(invoice_date), MONTH(invoice_date)
ORDER BY YEAR(invoice_date), MONTH(invoice_date)
```

### Sales by Status
```sql
SELECT
    status,
    COUNT(*) invoice_count,
    SUM(grand_total) total_amount,
    AVG(grand_total) avg_amount
FROM inventory_sales_invoices
WHERE is_deleted=0
GROUP BY status
```

### Purchase by Status
```sql
SELECT
    status,
    COUNT(*) invoice_count,
    SUM(grand_total) total_amount,
    AVG(grand_total) avg_amount
FROM inventory_purchase_invoices
WHERE is_deleted=0
GROUP BY status
```

---

## Recent Records Queries

### Recent Sales Invoices (Last 10)
```sql
SELECT
    si.id,
    si.invoice_no,
    si.invoice_date,
    si.grand_total,
    si.paid_amount,
    si.remaining_balance,
    si.status,
    c.customer_code,
    COALESCE(CONCAT(c.first_name, ' ', c.last_name), c.company_name) customer_name,
    a.account_code,
    a.account_name
FROM inventory_sales_invoices si
LEFT JOIN inventory_customers c ON si.customer_id = c.id
LEFT JOIN inventory_accounts a ON si.account_id = a.id
WHERE si.is_deleted=0
ORDER BY si.invoice_date DESC, si.id DESC
LIMIT 10
```

### Recent Purchase Invoices (Last 10)
```sql
SELECT
    pi.id,
    pi.invoice_no,
    pi.invoice_date,
    pi.grand_total,
    pi.paid_amount,
    pi.balance_amount,
    pi.status,
    s.supplier_code,
    s.supplier_name,
    a.account_code,
    a.account_name
FROM inventory_purchase_invoices pi
LEFT JOIN inventory_suppliers s ON pi.supplier_id = s.id
LEFT JOIN inventory_accounts a ON pi.account_id = a.id
WHERE pi.is_deleted=0
ORDER BY pi.invoice_date DESC, pi.id DESC
LIMIT 10
```

---

## Advanced Queries

### Sales & Purchase Comparison (Current Month)
```sql
SELECT
    DATE_FORMAT(NOW(),'%b %Y') current_month,
    (SELECT IFNULL(SUM(grand_total),0) FROM inventory_sales_invoices 
     WHERE YEAR(invoice_date)=YEAR(NOW()) AND MONTH(invoice_date)=MONTH(NOW()) AND is_deleted=0) monthly_sales,
    (SELECT IFNULL(SUM(grand_total),0) FROM inventory_purchase_invoices 
     WHERE YEAR(invoice_date)=YEAR(NOW()) AND MONTH(invoice_date)=MONTH(NOW()) AND is_deleted=0) monthly_purchases
```

### Sales Reconciliation - Paid vs Remaining
```sql
SELECT
    'Sales' type,
    SUM(grand_total) total_invoiced,
    SUM(paid_amount) total_paid,
    SUM(remaining_balance) total_outstanding,
    ROUND(SUM(paid_amount)/SUM(grand_total)*100,2) paid_percentage
FROM inventory_sales_invoices
WHERE is_deleted=0
```

### Purchase Reconciliation - Paid vs Remaining
```sql
SELECT
    'Purchase' type,
    SUM(grand_total) total_invoiced,
    SUM(paid_amount) total_paid,
    SUM(balance_amount) total_outstanding,
    ROUND(SUM(paid_amount)/SUM(grand_total)*100,2) paid_percentage
FROM inventory_purchase_invoices
WHERE is_deleted=0
```

### Overdue Invoices (Sales - Not Paid, Past Due Date)
```sql
SELECT
    si.id,
    si.invoice_no,
    si.due_date,
    DATEDIFF(CURDATE(), si.due_date) days_overdue,
    si.grand_total,
    si.remaining_balance,
    c.customer_code,
    c.customer_name
FROM inventory_sales_invoices si
LEFT JOIN inventory_customers c ON si.customer_id = c.id
WHERE si.is_deleted=0 
    AND si.status IN ('Issued', 'Partially Paid')
    AND si.due_date < CURDATE()
ORDER BY si.due_date ASC
```

### Overdue Payments (Purchase - Not Paid, Past Due Date)
```sql
SELECT
    pi.id,
    pi.invoice_no,
    pi.due_date,
    DATEDIFF(CURDATE(), pi.due_date) days_overdue,
    pi.grand_total,
    pi.balance_amount,
    s.supplier_code,
    s.supplier_name
FROM inventory_purchase_invoices pi
LEFT JOIN inventory_suppliers s ON pi.supplier_id = s.id
WHERE pi.is_deleted=0 
    AND pi.status IN ('Pending', 'Partial')
    AND pi.due_date < CURDATE()
ORDER BY pi.due_date ASC
```

### Top 5 Customers by Sales
```sql
SELECT
    c.customer_code,
    COALESCE(CONCAT(c.first_name, ' ', c.last_name), c.company_name) customer_name,
    COUNT(si.id) invoice_count,
    SUM(si.grand_total) total_sales,
    SUM(si.paid_amount) paid_amount,
    SUM(si.remaining_balance) outstanding
FROM inventory_customers c
LEFT JOIN inventory_sales_invoices si ON c.id = si.customer_id AND si.is_deleted=0
WHERE c.is_deleted=0
GROUP BY c.id
ORDER BY total_sales DESC
LIMIT 5
```

### Top 5 Suppliers by Purchases
```sql
SELECT
    s.supplier_code,
    s.supplier_name,
    COUNT(pi.id) invoice_count,
    SUM(pi.grand_total) total_purchases,
    SUM(pi.paid_amount) paid_amount,
    SUM(pi.balance_amount) outstanding
FROM inventory_suppliers s
LEFT JOIN inventory_purchase_invoices pi ON s.id = pi.supplier_id AND pi.is_deleted=0
WHERE s.is_deleted=0
GROUP BY s.id
ORDER BY total_purchases DESC
LIMIT 5
```

---

## Performance Notes

All queries use indexes on:
- `is_deleted` - For soft delete filtering
- `invoice_date` - For date-based grouping
- `status` - For status filtering
- `customer_id` / `supplier_id` - For join operations
- `account_id` - For account association joins

Expected execution times:
- Simple aggregations: 5-20ms
- Monthly groupings: 10-50ms
- Join operations with recent data: 20-100ms
- Full dashboard load: 100-300ms

## Usage Notes

1. **Real-Time Updates**: All queries execute against current data - no caching layer
2. **Deleted Records**: All queries filter `is_deleted=0` to exclude soft-deleted records
3. **NULL Handling**: `IFNULL()` used to return 0 for empty result sets
4. **Date Formatting**: `DATE_FORMAT()` used for human-readable month/year
5. **Account Tracking**: `LEFT JOIN inventory_accounts` provides account reference via `account_id`

## Integration with Controllers

These queries are implemented in `FinanceController.php`:
- `getFinanceDashboardData()` - Main entry point
- `getSalesInvoiceStats()` - Sales metrics
- `getPurchaseInvoiceStats()` - Purchase metrics
- `getMonthlySalesData()` - Sales chart data
- `getMonthlyPurchaseData()` - Purchase chart data
- `getRecentSalesInvoices()` - Recent sales list
- `getRecentPurchaseInvoices()` - Recent purchase list

Access via JSON API:
- Endpoint: `/index.php?r=inventory/financedashboardData`
- Method: GET/POST
- Returns: Complete dashboard data structure

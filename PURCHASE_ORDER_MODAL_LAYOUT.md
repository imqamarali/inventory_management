# Purchase Order Modal - New Layout

## Visual Structure

```
┌─────────────────────────────────────────────────────────────────────┐
│                      Add Purchase Order                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  [HIDDEN ROW - Warehouse automatically set to first warehouse]     │
│                                                                     │
│  ┌──────────────────┬──────────────────┬──────────────────┐        │
│  │  Supplier *      │  Status          │  GRN Status      │        │
│  │                  │                  │                  │        │
│  │ [Dropdown ▼]     │ [Dropdown ▼]     │ [Dropdown ▼]     │        │
│  └──────────────────┴──────────────────┴──────────────────┘        │
│                                                                     │
│  ┌──────────────────┬──────────────────┬──────────────────┐        │
│  │  Order Date      │  Expected Date   │  Payment Terms   │        │
│  │ [Date Input]     │ [Date Input]     │ [Text Input]     │        │
│  └──────────────────┴──────────────────┴──────────────────┘        │
│                                                                     │
│  ┌──────────────────────────────────────┬──────────────────┐        │
│  │  Remarks (full width)                │  Add Item Button │        │
│  │ [Text Input]                         │ [Button]         │        │
│  └──────────────────────────────────────┴──────────────────┘        │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │  Product   │ Qty │ Rate │ Discount │ Tax │ Total │ Remarks │   │
│  │ [Item Row 1] ... (editable fields)                         │   │
│  │ [Add more rows as needed]                                  │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  ┌──────────┬──────────┬──────────┬──────────┐                      │
│  │ Subtotal │ Discount │ Tax      │ Freight  │                      │
│  │ [readonly] [input]   [input]   [input]    │                      │
│  └──────────┴──────────┴──────────┴──────────┘                      │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │  Grand Total: [Amount]                                       │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  [Cancel] ............................ [Save Order]                 │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

## Features

### ✅ **Row 1 (Hidden)**
- **Warehouse**: Automatically set to first warehouse
- **Display**: Hidden from user view (display: none)
- **Purpose**: Ensures all items received to the same warehouse

### ✅ **Row 2 (Visible - Primary)**
- **Supplier** (col-md-4): Required field - Select from dropdown
- **Status** (col-md-4): PO Status (Draft, Approved, Partially Received, Completed, Cancelled)
- **GRN Status** (col-md-4): Goods Receiving Status (Pending, Completed, Cancelled)

### ✅ **Auto-Sync Behavior**
- When Status = "Completed" → GRN Status auto-updates to "Completed"
- When Status changes to something else → GRN Status stays as selected

### ✅ **Stock Update on Completed**
- When Status = "Completed" and saved:
  - Goods Receiving record (GRN) is created
  - Stock quantities updated in inventory_stock table
  - Weighted average costs calculated
  - Success message: "✅ Stock has been updated with received items."

## Updated Form Fields

| Position | Field | Type | Required | Auto-fill |
|----------|-------|------|----------|-----------|
| Hidden | Warehouse | Dropdown | No | Yes (First) |
| Row 2, Col 1 | Supplier | Dropdown | Yes ✓ | No |
| Row 2, Col 2 | Status | Dropdown | No | No |
| Row 2, Col 3 | GRN Status | Dropdown | No | Yes (on Completed) |
| Row 3, Col 1 | Order Date | Date | No | No |
| Row 3, Col 2 | Expected Date | Date | No | No |
| Row 3, Col 3 | Payment Terms | Text | No | No |
| Row 4 | Remarks | Text | No | No |
| Row 5+ | Items | Table | Yes ✓ | No |
| Footer | Subtotal/Discount/Tax/Freight | Input | No | Yes (Auto-calc) |
| Footer | Grand Total | Input | No | Yes (Auto-calc) |

## Testing Checklist

- [ ] Clear browser cache (Ctrl+Shift+Delete)
- [ ] Hard refresh (Ctrl+Shift+R)
- [ ] Click "Add Purchase Order"
- [ ] Verify warehouse row is hidden
- [ ] Verify Supplier, Status, GRN Status in same row
- [ ] Select a Supplier
- [ ] Change Status to "Completed"
- [ ] Verify GRN Status auto-changes to "Completed"
- [ ] Add items and quantities
- [ ] Click Save
- [ ] Verify success message with stock update confirmation
- [ ] Check inventory_stock table for updated quantities

## Code Changes

### Dashboard View (dashboard.php)
- Modified `getOrderModalHtml()` to reorganize form layout
- Updated `didOpen()` to initialize all three dropdowns
- Enhanced `saveOrder()` to handle stock updates on completion

### Controller (InventoryController.php)
- Purchase order save already handles GRN creation
- Stock update logic triggered when `update_stock=1`

---

**Layout is now clean, compact, and user-friendly!** ✨

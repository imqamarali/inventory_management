<?php
/**
 * CREATE PURCHASE ORDER VIEW
 * ================================================================================
 * PURPOSE: Create and manage purchase orders to suppliers with line items
 *
 * FUNCTIONALITY:
 * - Create new purchase orders to suppliers
 * - Add multiple products with quantities and unit prices
 * - Set delivery and payment terms
 * - Calculate subtotal, discounts, taxes, shipping, and grand total
 * - Update existing orders
 * - Search and filter purchase orders
 * - Manage order status (Pending, Approved, Completed, Cancelled)
 * - Track payment status
 *
 * DATA MANAGEMENT:
 * - Stores orders in: inventory_purchase_orders table
 * - Stores items in: inventory_purchase_order_items table
 * - Records: po_number, supplier_id, warehouse_id, order_date, expected_date,
 *            payment_terms, status, subtotal, discount, tax, freight (shipping),
 *            grand_total, notes
 * - Status: Pending, Approved, Completed, Cancelled
 * - Payment Status: Not tracked here, handled in invoicing
 *
 * FINANCE INTEGRATION:
 * - All purchase orders' grand_total is used in:
 *   • Finance Dashboard for Total Expenses/COGS calculation
 *   • Profit & Loss statement (Cost of Goods Sold section)
 *   • Supplier Payables tracking (Accounts Payable)
 *   • Monthly purchase trends and supplier analysis
 * - Payment terms enable payment scheduling and cash flow forecasting
 * - Order status progression: Pending → Approved → Completed
 * ================================================================================
 */

use yii\helpers\Html;

if (!isset($suppliers)) $suppliers = [];
if (!isset($products)) $products = [];
if (!isset($warehouses)) $warehouses = []; ?>
<div class="container-fluid pt-4">
    <div class="row mb-4">
        <div class="col">
            <h3><i class="fa fa-plus"></i> Create Purchase Order</h3>
        </div>
    </div>
    <div id="alerts-po"></div>
    <div class="card">
        <div class="card-body">
            <form id="poForm">
                <div class="row mb-3">
                    <div class="col-md-4"><label class="form-label">Supplier<span class="text-danger">*</span></label><select class="form-select" id="supplierId" name="supplier_id" required><?php foreach ($suppliers as $s): ?><option value="<?= $s['id'] ?>"><?= Html::encode($s['company_name'] ?? '') ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-4"><label class="form-label">Warehouse<span class="text-danger">*</span></label><select class="form-select" id="warehouseId" name="warehouse_id" required><?php foreach ($warehouses as $w): ?><option value="<?= $w['id'] ?>"><?= Html::encode($w['name'] ?? '') ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-4"><label class="form-label">Order Date<span class="text-danger">*</span></label><input type="date" class="form-control" id="orderDate" name="order_date" value="<?= date('Y-m-d') ?>" required></div>
                </div>
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Purchase Items</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                        <th><button type="button" class="btn btn-sm btn-success" onclick="addItemRow()"><i class="fa fa-plus"></i></button></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    <tr class="item-row">
                                        <td><select class="form-select form-select-sm product-select" name="products[]" required><?php foreach ($products as $p): ?><option value="<?= $p['id'] ?>" data-price="<?= $p['price'] ?? 0 ?>"><?= Html::encode($p['name'] ?? '') ?></option><?php endforeach; ?></select></td>
                                        <td><input type="number" class="form-control form-control-sm qty-input" name="quantities[]" min="1" value="1" required></td>
                                        <td><input type="number" class="form-control form-control-sm price-input" name="prices[]" step="0.01" min="0" readonly></td>
                                        <td><input type="number" class="form-control form-control-sm total-input" readonly></td>
                                        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeItemRow(this)"><i class="fa fa-trash"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <div class="input-group"><span class="input-group-text">Total Amount</span><input type="number" class="form-control" id="totalAmount" readonly></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12"><label class="form-label">Remarks</label><textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea></div>
                </div>
                <div class="row">
                    <div class="col"><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Create Order</button> <button type="reset" class="btn btn-secondary"><i class="fa fa-undo"></i> Reset</button></div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function htmlEscape(t) {
        if (!t) return '';
        const m = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(t).replace(/[&<>"']/g, c => m[c]);
    }
    const Storage = {
        set: (k, v) => {
            try {
                localStorage.setItem(k, v);
            } catch (e) {
                console.warn('Storage blocked:', e.message);
            }
        },
        get: (k) => {
            try {
                return localStorage.getItem(k);
            } catch (e) {
                console.warn('Storage blocked:', e.message);
                return null;
            }
        }
    };

    function addItemRow() {
        const row = $(`<tr class="item-row"><td><select class="form-select form-select-sm product-select" name="products[]" required><option value="">Select Product</option><?php foreach ($products as $p): ?><option value="<?= $p['id'] ?>" data-price="<?= $p['price'] ?? 0 ?>"><?= Html::encode($p['name'] ?? '') ?></option><?php endforeach; ?></select></td><td><input type="number" class="form-control form-control-sm qty-input" name="quantities[]" min="1" value="1" required></td><td><input type="number" class="form-control form-control-sm price-input" name="prices[]" step="0.01" min="0" readonly></td><td><input type="number" class="form-control form-control-sm total-input" readonly></td><td><button type="button" class="btn btn-sm btn-danger" onclick="removeItemRow(this)"><i class="fa fa-trash"></i></button></td></tr>`);
        $('#itemsBody').append(row);
        attachItemHandlers(row);
    }

    function removeItemRow(btn) {
        if ($('#itemsBody tr').length > 1) {
            $(btn).closest('tr').remove();
            calcTotal();
        }
    }

    function attachItemHandlers(row) {
        row.find('.product-select').on('change', function() {
            const price = $(this).find('option:selected').attr('data-price') || 0;
            $(this).closest('tr').find('.price-input').val(parseFloat(price).toFixed(2));
            calcTotal();
        });
        row.find('.qty-input, .price-input').on('change keyup', function() {
            calcTotal();
        });
    }

    function calcTotal() {
        let total = 0;
        $('.item-row').each(function() {
            const qty = parseFloat($(this).find('.qty-input').val()) || 0;
            const price = parseFloat($(this).find('.price-input').val()) || 0;
            const itemTotal = qty * price;
            $(this).find('.total-input').val(itemTotal > 0 ? itemTotal.toFixed(2) : '0');
            total += itemTotal;
        });
        $('#totalAmount').val(total > 0 ? total.toFixed(2) : '0');
    }
    $('#poForm').on('submit', function(e) {
        e.preventDefault();
        if ($('#itemsBody tr').length === 0) {
            showAlert('Add at least one item', 'warning');
            return;
        }
        const formData = new FormData(this);
        const items = [],
            quantities = [],
            prices = [];
        $('.item-row').each(function() {
            const pid = $(this).find('.product-select').val();
            if (pid) {
                items.push(pid);
                quantities.push($(this).find('.qty-input').val());
                prices.push($(this).find('.price-input').val());
            }
        });
        formData.set('products', JSON.stringify(items));
        formData.set('quantities', JSON.stringify(quantities));
        formData.set('prices', JSON.stringify(prices));
        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl("purchase/createpurchase") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            timeout: 5000,
            success: function(r) {
                if (r.success) {
                    showAlert(r.message ?? 'Order created', 'success');
                    setTimeout(() => {
                        window.location.href = '<?= Yii::$app->urlManager->createUrl("purchase/purchaseorders") ?>';
                    }, 1000);
                } else showAlert(r.message ?? 'Error creating order', 'danger');
            },
            error: function(x, s) {
                showAlert(s === 'timeout' ? 'Request timeout' : 'Network error', 'danger');
            }
        });
    });

    function showAlert(m, t) {
        const a = $(`<div class="alert alert-${t} alert-dismissible fade show"><i class="fa fa-info"></i> ${htmlEscape(m)}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
        $('#alerts-po').html(a);
        setTimeout(() => a.fadeOut(), 5000);
    }
    $(function() {
        $('#itemsBody tr:first').each(function() {
            attachItemHandlers($(this));
        });
        $('.product-select, .qty-input, .price-input').on('change keyup', calcTotal);
    });
</script>
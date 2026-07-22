<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice <?= $invoice['invoice_number'] ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background: white;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            border-bottom: 3px solid #0f4c29;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-info h1 {
            color: #0f4c29;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .company-info p {
            color: #666;
            font-size: 13px;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h2 {
            color: #0f4c29;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        .detail-section h3 {
            color: #0f4c29;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .detail-section p {
            font-size: 13px;
            margin-bottom: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th {
            background-color: #0f4c29;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            font-weight: bold;
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .totals {
            text-align: right;
            margin-bottom: 40px;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .total-row .label {
            width: 200px;
            text-align: left;
        }
        .total-row .value {
            width: 120px;
            text-align: right;
            font-weight: bold;
        }
        .grand-total {
            border-top: 2px solid #0f4c29;
            border-bottom: 2px solid #0f4c29;
            padding: 10px 0;
            background-color: #f0f0f0;
        }
        .grand-total .label {
            width: 200px;
            text-align: left;
            font-size: 14px;
            font-weight: bold;
            color: #0f4c29;
        }
        .grand-total .value {
            width: 120px;
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #0f4c29;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-partial {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }
        @media print {
            body {
                padding: 0;
            }
            .container {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>Invoice System</h1>
                <p>Professional Billing Solution</p>
                <p>Pakistan</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <p style="color: #666; margin-bottom: 5px;"><strong><?= htmlspecialchars($invoice['invoice_number']) ?></strong></p>
                <div class="status-badge <?= 'status-' . strtolower($invoice['payment_status']) ?>">
                    <?= strtoupper($invoice['payment_status']) ?>
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="detail-section">
                <h3>Bill To</h3>
                <p><strong><?= htmlspecialchars($invoice['contract_name']) ?></strong></p>
                <p><?= htmlspecialchars($invoice['contract_description']) ?></p>
            </div>
            <div class="detail-section" style="text-align: right;">
                <div style="text-align: left; display: inline-block; width: 100%; max-width: 300px;">
                    <p><strong>Invoice Date:</strong> <?= date('F j, Y', strtotime($invoice['invoice_date'])) ?></p>
                    <p><strong>Due Date:</strong> <?= date('F j, Y', strtotime($invoice['due_date'])) ?></p>
                    <p><strong>Invoice ID:</strong> <?= htmlspecialchars($invoice['invoice_number']) ?></p>
                </div>
            </div>
        </div>

        <!-- Invoice Items Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($invoice['contract_name']) ?> - Monthly Service Charges</td>
                    <td style="text-align: right;">PKR <?= number_format($invoice['amount'], 2) ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals">
            <div class="total-row">
                <div class="label">Subtotal:</div>
                <div class="value">PKR <?= number_format($invoice['amount'], 2) ?></div>
            </div>
            <div class="total-row">
                <div class="label">Tax (0%):</div>
                <div class="value">PKR 0.00</div>
            </div>
            <div class="total-row">
                <div class="label">Total Paid:</div>
                <div class="value">PKR <?= number_format($invoice['paid_amount'] ?? 0, 2) ?></div>
            </div>
            <div class="total-row grand-total">
                <div class="label">Amount Due:</div>
                <div class="value">PKR <?= number_format($invoice['remaining_amount'], 2) ?></div>
            </div>
        </div>

        <!-- Payment Terms -->
        <?php if ($invoice['contract_policy']): ?>
        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 30px;">
            <h3 style="color: #0f4c29; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px;">Terms & Conditions</h3>
            <p style="font-size: 12px; color: #666; line-height: 1.6;">
                <?= nl2br(htmlspecialchars($invoice['contract_policy'])) ?>
            </p>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>For inquiries, please contact our billing department.</p>
            <p style="margin-top: 20px;">This is an automatically generated invoice. No signature required.</p>
            <p style="color: #999; margin-top: 20px;">Generated on <?= date('F j, Y \a\t g:i A') ?></p>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>
</html>

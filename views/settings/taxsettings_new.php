<?php
use yii\helpers\Html;

if (!isset($rates)) {
    $rates = [];
}

$navbarColor = '#0f4c29';
$successColor = '#2ecc71';
$dangerColor = '#e74c3c';
$accentColor = '#3498db';
?>

<!-- Alert Container -->
<div id="alertContainer" style="margin-bottom: 15px;">
</div>

<div class="tabbable">
    <ul class="nav nav-tabs" id="taxTabs" style="border-bottom: 2px solid #e8e8e8;">
        <li class="active">
            <a data-toggle="tab" href="#tax-rates" aria-expanded="true" style="border: none; color: #333; padding: 12px 20px;">
                <i class="ace-icon fa fa-percent" style="color: <?= $accentColor ?>;"></i>
                <strong>Tax Rates List</strong>
            </a>
        </li>
        <li>
            <a data-toggle="tab" href="#tax-add" aria-expanded="false" style="border: none; color: #333; padding: 12px 20px;">
                <i class="ace-icon fa fa-plus-circle" style="color: <?= $successColor ?>;"></i>
                <strong>Add New Tax Rate</strong>
            </a>
        </li>
        <li>
            <a data-toggle="tab" href="#tax-info" aria-expanded="false" style="border: none; color: #333; padding: 12px 20px;">
                <i class="ace-icon fa fa-info-circle" style="color: #f39c12;"></i>
                <strong>Tax Guide</strong>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- TAX RATES LIST TAB -->
        <div id="tax-rates" class="tab-pane fade active in" style="padding: 20px 0;">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="taxRatesTable">
                            <thead style="background-color: #f8f9fa; border-top: 2px solid #e8e8e8;">
                                <tr>
                                    <th width="40%" style="color: #333; font-weight: 600;">Tax Name</th>
                                    <th width="20%" style="color: #333; font-weight: 600;">Rate (%)</th>
                                    <th width="20%" style="color: #333; font-weight: 600;">Status</th>
                                    <th width="20%" class="text-center" style="color: #333; font-weight: 600;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="taxRatesBody">
                                <?php if (empty($rates)): ?>
                                <tr>
                                    <td colspan="4" class="text-center" style="padding: 40px;">
                                        <i class="ace-icon fa fa-inbox fa-3x" style="color: #ccc; margin-bottom: 10px;"></i>
                                        <p style="color: #999; margin: 10px 0;">No tax rates found. Add your first tax rate to get started.</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($rates as $rate): ?>
                                    <tr class="tax-row" data-tax-id="<?= $rate['id'] ?>" style="transition: background-color 0.3s ease;">
                                        <td><strong><?= htmlspecialchars($rate['tax_name']) ?></strong></td>
                                        <td>
                                            <span class="badge" style="background-color: <?= $accentColor ?>; font-size: 12px; padding: 5px 10px;">
                                                <?= number_format($rate['tax_percentage'], 2) ?>%
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($rate['is_default'] == 1): ?>
                                            <span class="label" style="background-color: <?= $successColor ?>;"> DEFAULT</span>
                                            <?php else: ?>
                                            <span class="label" style="background-color: #95a5a6;">Available</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-xs">
                                                <button class="btn btn-info" onclick="editTaxRate(<?= $rate['id'] ?>)" title="Edit">
                                                    <i class="ace-icon fa fa-pencil"></i>
                                                </button>
                                                <button class="btn btn-danger" onclick="deleteTaxRate(<?= $rate['id'] ?>, this)" title="Delete">
                                                    <i class="ace-icon fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADD/EDIT TAX RATE TAB -->
        <div id="tax-add" class="tab-pane fade" style="padding: 20px 0;">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <div class="widget-box" style="border-top: 2px solid <?= $accentColor ?>;">
                        <div class="widget-header" style="background-color: #f8f9fa;">
                            <h4 class="widget-title" id="formTitle">
                                <i class="ace-icon fa fa-plus"></i>
                                Add New Tax Rate
                            </h4>
                        </div>
                        <div class="widget-body">
                            <form id="taxRateForm" class="form-horizontal">
                                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                                <input type="hidden" id="taxId" name="id" value="">

                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 control-label">
                                        <span class="required">*</span> Tax Name
                                    </label>
                                    <div class="col-xs-12 col-sm-9">
                                        <input type="text" class="form-control" id="taxName" name="tax_name" required
                                               placeholder="e.g., VAT, Sales Tax, GST, Service Tax">
                                        <small class="text-muted">Unique name for this tax rate</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 control-label">
                                        <span class="required">*</span> Tax Percentage
                                    </label>
                                    <div class="col-xs-12 col-sm-9">
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="taxPercentage" name="tax_percentage"
                                                   min="0" max="100" step="0.01" required
                                                   placeholder="e.g., 15.00">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                        <small class="text-muted">Enter percentage value (0-100)</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 control-label">
                                    </label>
                                    <div class="col-xs-12 col-sm-9">
                                        <label style="font-weight: 500;">
                                            <input type="checkbox" id="isDefault" name="is_default" value="1" class="ace">
                                            <span class="lbl"> Set as Default Tax Rate</span>
                                        </label>
                                        <p class="text-muted"><small>Only one tax rate can be default. Default tax will be automatically selected in new documents.</small></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 control-label">
                                    </label>
                                    <div class="col-xs-12 col-sm-9">
                                        <button type="button" class="btn btn-sm btn-success" id="submitBtn" onclick="saveTaxRate()">
                                            <i class="ace-icon fa fa-save"></i>
                                            Save Tax Rate
                                        </button>
                                        <button type="button" class="btn btn-sm btn-default" onclick="resetTaxForm()">
                                            <i class="ace-icon fa fa-times"></i>
                                            Clear Form
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAX GUIDE TAB -->
        <div id="tax-info" class="tab-pane fade" style="padding: 20px 0;">
            <div class="row">
                <div class="col-sm-12">
                    <h4 style="color: <?= $navbarColor ?>; margin-top: 0;">Tax Configuration Guide</h4>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="widget-box" style="border-top: 2px solid #3498db;">
                                <div class="widget-header" style="background-color: #f8f9fa;">
                                    <h5 class="widget-title">
                                        <i class="ace-icon fa fa-globe"></i> VAT/GST
                                    </h5>
                                </div>
                                <div class="widget-body">
                                    <p><strong>Description:</strong></p>
                                    <p>Value Added Tax (VAT) or Goods and Services Tax (GST) is applied at each stage of production or distribution.</p>
                                    <ul class="list-unstyled">
                                        <li><strong>Used in:</strong> Most countries outside USA</li>
                                        <li><strong>Typical rates:</strong> 15-20%</li>
                                        <li><strong>Recoverable:</strong> Yes, from suppliers</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="widget-box" style="border-top: 2px solid #2ecc71;">
                                <div class="widget-header" style="background-color: #f8f9fa;">
                                    <h5 class="widget-title">
                                        <i class="ace-icon fa fa-shopping-cart"></i> Sales Tax
                                    </h5>
                                </div>
                                <div class="widget-body">
                                    <p><strong>Description:</strong></p>
                                    <p>Sales tax is applied only to retail sales at the point of purchase.</p>
                                    <ul class="list-unstyled">
                                        <li><strong>Used in:</strong> United States, Canada</li>
                                        <li><strong>Typical rates:</strong> 5-10%</li>
                                        <li><strong>Recoverable:</strong> No</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 20px;">
                        <div class="col-sm-6">
                            <div class="widget-box" style="border-top: 2px solid #f39c12;">
                                <div class="widget-header" style="background-color: #f8f9fa;">
                                    <h5 class="widget-title">
                                        <i class="ace-icon fa fa-wrench"></i> Service Tax
                                    </h5>
                                </div>
                                <div class="widget-body">
                                    <p><strong>Description:</strong></p>
                                    <p>Service tax is applied specifically to service-based transactions.</p>
                                    <ul class="list-unstyled">
                                        <li><strong>Used in:</strong> India and other countries</li>
                                        <li><strong>Typical rates:</strong> 5-18%</li>
                                        <li><strong>Recoverable:</strong> Yes, input credit available</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="widget-box" style="border-top: 2px solid #e74c3c;">
                                <div class="widget-header" style="background-color: #f8f9fa;">
                                    <h5 class="widget-title">
                                        <i class="ace-icon fa fa-check"></i> Best Practices
                                    </h5>
                                </div>
                                <div class="widget-body">
                                    <ul class="list-unstyled">
                                        <li>✓ Define all tax rates applicable to your business</li>
                                        <li>✓ Set the most commonly used rate as default</li>
                                        <li>✓ Use clear, descriptive names for each tax rate</li>
                                        <li>✓ Review rates annually for compliance</li>
                                        <li>✓ Document which products/services use which tax</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info" style="margin-top: 20px; border-left: 4px solid #3498db;">
                        <i class="ace-icon fa fa-lightbulb-o"></i>
                        <strong>Important:</strong> Tax rates should be configured based on your local regulations.
                        Incorrect tax rates may result in compliance issues. Consult with a tax advisor for your specific jurisdiction.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global CSRF token
const csrfToken = '<?= Yii::$app->request->getCsrfToken() ?>';
const csrfParam = '<?= Yii::$app->request->csrfParam ?>';

// Show alert message
function showAlert(message, type = 'info') {
    const container = document.getElementById('alertContainer');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade in`;
    alertDiv.style.borderLeft = '4px solid';

    const colorMap = {
        'success': '#2ecc71',
        'danger': '#e74c3c',
        'warning': '#f39c12',
        'info': '#3498db'
    };

    alertDiv.style.borderLeftColor = colorMap[type] || colorMap['info'];

    const icons = {
        'success': 'fa-check-circle',
        'danger': 'fa-exclamation-circle',
        'warning': 'fa-warning',
        'info': 'fa-info-circle'
    };

    alertDiv.innerHTML = `
        <button type="button" class="close" onclick="this.parentElement.remove();" style="color: inherit;">&times;</button>
        <i class="ace-icon fa ${icons[type]}"></i> ${message}
    `;

    container.innerHTML = '';
    container.appendChild(alertDiv);

    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 5000);
}

// Save/Update Tax Rate
function saveTaxRate() {
    const id = document.getElementById('taxId').value;
    const name = document.getElementById('taxName').value.trim();
    const percentage = document.getElementById('taxPercentage').value;
    const isDefault = document.getElementById('isDefault').checked ? 1 : 0;

    if (!name || !percentage) {
        showAlert('Please fill in all required fields!', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append(csrfParam, csrfToken);
    formData.append('tax_name', name);
    formData.append('tax_percentage', percentage);
    formData.append('is_default', isDefault);
    if (id) {
        formData.append('id', id);
    }

    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="ace-icon fa fa-spinner fa-spin"></i> Saving...';

    fetch('index.php?r=settings/taxsettings', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ace-icon fa fa-save"></i> Save Tax Rate';

        if (data.success) {
            showAlert(data.message, 'success');

            if (!id) {
                // Add new row to table
                const tbody = document.getElementById('taxRatesBody');
                if (tbody.querySelector('tr td[colspan]')) {
                    tbody.innerHTML = ''; // Clear "no data" message
                }

                const row = document.createElement('tr');
                row.className = 'tax-row';
                row.style.transition = 'background-color 0.3s ease';
                row.innerHTML = `
                    <td><strong>${escapeHtml(name)}</strong></td>
                    <td><span class="badge" style="background-color: #3498db;">${parseFloat(percentage).toFixed(2)}%</span></td>
                    <td><span class="label" style="background-color: ${isDefault ? '#2ecc71' : '#95a5a6'};">${isDefault ? 'DEFAULT' : 'Available'}</span></td>
                    <td class="text-center">
                        <div class="btn-group btn-group-xs">
                            <button class="btn btn-info" title="Edit"><i class="ace-icon fa fa-pencil"></i></button>
                            <button class="btn btn-danger" title="Delete"><i class="ace-icon fa fa-trash"></i></button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            } else {
                // Update existing row
                const row = document.querySelector(`tr.tax-row[data-tax-id="${id}"]`);
                if (row) {
                    row.innerHTML = `
                        <td><strong>${escapeHtml(name)}</strong></td>
                        <td><span class="badge" style="background-color: #3498db;">${parseFloat(percentage).toFixed(2)}%</span></td>
                        <td><span class="label" style="background-color: ${isDefault ? '#2ecc71' : '#95a5a6'};">${isDefault ? 'DEFAULT' : 'Available'}</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-xs">
                                <button class="btn btn-info" onclick="editTaxRate(${id})" title="Edit"><i class="ace-icon fa fa-pencil"></i></button>
                                <button class="btn btn-danger" onclick="deleteTaxRate(${id}, this)" title="Delete"><i class="ace-icon fa fa-trash"></i></button>
                            </div>
                        </td>
                    `;
                }
            }

            resetTaxForm();
            // Switch to list tab
            document.querySelector('a[href="#tax-rates"]').click();
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ace-icon fa fa-save"></i> Save Tax Rate';
        console.error('Error:', error);
        showAlert('An error occurred while saving. Please try again.', 'danger');
    });
}

// Edit Tax Rate
function editTaxRate(taxId) {
    const row = document.querySelector(`tr.tax-row[data-tax-id="${taxId}"]`);
    if (!row) return;

    const cells = row.querySelectorAll('td');
    const name = cells[0].textContent.trim();
    const percentage = cells[1].textContent.match(/[\d.]+/)[0];
    const isDefault = cells[2].textContent.includes('DEFAULT');

    document.getElementById('taxId').value = taxId;
    document.getElementById('taxName').value = name;
    document.getElementById('taxPercentage').value = percentage;
    document.getElementById('isDefault').checked = isDefault;

    document.getElementById('formTitle').innerHTML = '<i class="ace-icon fa fa-edit"></i> Edit Tax Rate';
    document.getElementById('submitBtn').innerHTML = '<i class="ace-icon fa fa-save"></i> Update Tax Rate';

    // Switch to edit tab
    document.querySelector('a[href="#tax-add"]').click();
    window.scrollTo(0, 0);
}

// Delete Tax Rate
function deleteTaxRate(taxId, btn) {
    if (!confirm('Are you sure you want to delete this tax rate? This action cannot be undone.')) {
        return;
    }

    const formData = new FormData();
    formData.append(csrfParam, csrfToken);
    formData.append('id', taxId);
    formData.append('delete', 1);

    btn.disabled = true;
    btn.innerHTML = '<i class="ace-icon fa fa-spinner fa-spin"></i>';

    fetch('index.php?r=settings/taxsettings', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            const row = document.querySelector(`tr.tax-row[data-tax-id="${taxId}"]`);
            if (row) {
                row.style.opacity = '0.5';
                setTimeout(() => row.remove(), 300);
            }

            // Check if table is empty
            const tbody = document.getElementById('taxRatesBody');
            if (tbody.querySelectorAll('tr.tax-row').length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 40px;">
                            <i class="ace-icon fa fa-inbox fa-3x" style="color: #ccc; margin-bottom: 10px;"></i>
                            <p style="color: #999; margin: 10px 0;">No tax rates found. Add your first tax rate to get started.</p>
                        </td>
                    </tr>
                `;
            }
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="ace-icon fa fa-trash"></i>';
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ace-icon fa fa-trash"></i>';
        console.error('Error:', error);
        showAlert('An error occurred while deleting. Please try again.', 'danger');
    });
}

// Reset Form
function resetTaxForm() {
    document.getElementById('taxRateForm').reset();
    document.getElementById('taxId').value = '';
    document.getElementById('isDefault').checked = false;
    document.getElementById('formTitle').innerHTML = '<i class="ace-icon fa fa-plus"></i> Add New Tax Rate';
    document.getElementById('submitBtn').innerHTML = '<i class="ace-icon fa fa-save"></i> Save Tax Rate';
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize Bootstrap components
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
    }
});
</script>

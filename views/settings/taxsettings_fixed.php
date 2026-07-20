<?php
use yii\helpers\Html;
use yii\helpers\Url;

if (!isset($rates)) {
    $rates = [];
}

$navbarColor = '#0f4c29';
$accentColor = '#3498db';
$successColor = '#2ecc71';
?>

<!-- Alert Container -->
<div id="alertContainer" style="margin-bottom: 15px;"></div>

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
                                                <button class="btn btn-info" onclick="editTaxRate(<?= $rate['id'] ?>, '<?= htmlspecialchars(addslashes($rate['tax_name'])) ?>', <?= $rate['tax_percentage'] ?>, <?= $rate['is_default'] ?>)" title="Edit">
                                                    <i class="ace-icon fa fa-pencil"></i>
                                                </button>
                                                <button class="btn btn-danger" onclick="deleteTaxRate(<?= $rate['id'] ?>)" title="Delete">
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
                            <form id="taxRateForm" class="form-horizontal" method="POST" action="<?= Url::to(['settings/taxsettings']) ?>">
                                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                                <input type="hidden" id="taxId" name="id" value="">

                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 control-label">
                                        <span class="required" style="color: red;">*</span> Tax Name
                                    </label>
                                    <div class="col-xs-12 col-sm-9">
                                        <input type="text" class="form-control" id="taxName" name="tax_name" required
                                               placeholder="e.g., VAT, Sales Tax, GST, Service Tax">
                                        <small class="text-muted">Unique name for this tax rate</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 control-label">
                                        <span class="required" style="color: red;">*</span> Tax Percentage
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
                                    <p><strong>Description:</strong> Value Added Tax or Goods and Services Tax</p>
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
                                    <p><strong>Description:</strong> Applied only to retail sales at point of purchase</p>
                                    <ul class="list-unstyled">
                                        <li><strong>Used in:</strong> United States, Canada</li>
                                        <li><strong>Typical rates:</strong> 5-10%</li>
                                        <li><strong>Recoverable:</strong> No</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info" style="margin-top: 20px; border-left: 4px solid #3498db;">
                        <i class="ace-icon fa fa-lightbulb-o"></i>
                        <strong>Important:</strong> Tax rates should be configured based on your local regulations. Consult with a tax advisor for your specific jurisdiction.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Configuration
const CSRF_TOKEN = '<?= Yii::$app->request->getCsrfToken() ?>';
const CSRF_PARAM = '<?= Yii::$app->request->csrfParam ?>';
const API_URL = 'index.php?r=settings/taxsettings';

// Show alert message
function showAlert(message, type = 'info') {
    const container = document.getElementById('alertContainer');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade in`;
    alertDiv.style.borderLeft = '4px solid';
    alertDiv.style.borderRadius = '4px';

    const colorMap = {
        'success': '#2ecc71',
        'danger': '#e74c3c',
        'warning': '#f39c12',
        'info': '#3498db'
    };

    const icons = {
        'success': 'fa-check-circle',
        'danger': 'fa-exclamation-circle',
        'warning': 'fa-warning',
        'info': 'fa-info-circle'
    };

    alertDiv.style.borderLeftColor = colorMap[type] || colorMap['info'];

    alertDiv.innerHTML = `
        <button type="button" class="close" onclick="this.parentElement.remove();" style="color: inherit;">&times;</button>
        <i class="ace-icon fa ${icons[type] || icons['info']}"></i>
        <strong>${message}</strong>
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

// SAVE TAX RATE
function saveTaxRate() {
    const id = document.getElementById('taxId').value.trim();
    const name = document.getElementById('taxName').value.trim();
    const percentage = document.getElementById('taxPercentage').value.trim();
    const isDefault = document.getElementById('isDefault').checked ? 1 : 0;

    // Validation
    if (!name) {
        showAlert('Please enter tax name!', 'warning');
        return;
    }

    if (!percentage || isNaN(percentage) || percentage < 0 || percentage > 100) {
        showAlert('Please enter a valid percentage (0-100)!', 'warning');
        return;
    }

    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="ace-icon fa fa-spinner fa-spin"></i> Saving...';

    // Create FormData
    const formData = new FormData();
    formData.append(CSRF_PARAM, CSRF_TOKEN);
    formData.append('tax_name', name);
    formData.append('tax_percentage', percentage);
    if (isDefault) {
        formData.append('is_default', isDefault);
    }
    if (id) {
        formData.append('id', id);
    }

    // Send request
    fetch(API_URL, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = id ? '<i class="ace-icon fa fa-save"></i> Update Tax Rate' : '<i class="ace-icon fa fa-save"></i> Save Tax Rate';

        if (data.success) {
            showAlert(data.message, 'success');

            if (!id) {
                // ADD NEW
                const tbody = document.getElementById('taxRatesBody');

                // Remove empty message if exists
                const emptyRow = tbody.querySelector('tr td[colspan]');
                if (emptyRow) {
                    emptyRow.closest('tr').remove();
                }

                // Create new row
                const row = document.createElement('tr');
                row.className = 'tax-row';
                row.setAttribute('data-tax-id', 'new');
                row.style.transition = 'background-color 0.3s ease';
                row.innerHTML = `
                    <td><strong>${escapeHtml(name)}</strong></td>
                    <td><span class="badge" style="background-color: #3498db; font-size: 12px; padding: 5px 10px;">${parseFloat(percentage).toFixed(2)}%</span></td>
                    <td><span class="label" style="background-color: ${isDefault ? '#2ecc71' : '#95a5a6'};">${isDefault ? 'DEFAULT' : 'Available'}</span></td>
                    <td class="text-center">
                        <div class="btn-group btn-group-xs">
                            <button class="btn btn-info" onclick="alert('Please refresh to see updated ID')" title="Edit">
                                <i class="ace-icon fa fa-pencil"></i>
                            </button>
                            <button class="btn btn-danger" title="Delete">
                                <i class="ace-icon fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            } else {
                // UPDATE EXISTING
                const row = document.querySelector(`tr.tax-row[data-tax-id="${id}"]`);
                if (row) {
                    row.innerHTML = `
                        <td><strong>${escapeHtml(name)}</strong></td>
                        <td><span class="badge" style="background-color: #3498db; font-size: 12px; padding: 5px 10px;">${parseFloat(percentage).toFixed(2)}%</span></td>
                        <td><span class="label" style="background-color: ${isDefault ? '#2ecc71' : '#95a5a6'};">${isDefault ? 'DEFAULT' : 'Available'}</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-xs">
                                <button class="btn btn-info" onclick="editTaxRate(${id}, '${escapeHtml(name)}', ${percentage}, ${isDefault})" title="Edit">
                                    <i class="ace-icon fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteTaxRate(${id})" title="Delete">
                                    <i class="ace-icon fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    `;
                }
            }

            resetTaxForm();
            // Switch to list tab
            document.querySelector('a[href="#tax-rates"]').click();
        } else {
            showAlert(data.message || 'Error saving tax rate', 'danger');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = id ? '<i class="ace-icon fa fa-save"></i> Update Tax Rate' : '<i class="ace-icon fa fa-save"></i> Save Tax Rate';
        console.error('Error:', error);
        showAlert('Error: ' + error.message, 'danger');
    });
}

// EDIT TAX RATE
function editTaxRate(taxId, taxName, taxPercentage, isDefault) {
    document.getElementById('taxId').value = taxId;
    document.getElementById('taxName').value = taxName;
    document.getElementById('taxPercentage').value = taxPercentage;
    document.getElementById('isDefault').checked = isDefault == 1;

    document.getElementById('formTitle').innerHTML = '<i class="ace-icon fa fa-edit"></i> Edit Tax Rate';
    document.getElementById('submitBtn').innerHTML = '<i class="ace-icon fa fa-save"></i> Update Tax Rate';

    // Switch to edit tab
    document.querySelector('a[href="#tax-add"]').click();
    window.scrollTo(0, 0);
}

// DELETE TAX RATE
function deleteTaxRate(taxId) {
    if (!confirm('Are you sure you want to delete this tax rate? This action cannot be undone.')) {
        return;
    }

    const formData = new FormData();
    formData.append(CSRF_PARAM, CSRF_TOKEN);
    formData.append('id', taxId);
    formData.append('delete', 1);

    const btn = event.target.closest('button');
    btn.disabled = true;

    fetch(API_URL, {
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
            const rows = tbody.querySelectorAll('tr.tax-row');
            if (rows.length === 0) {
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
            showAlert(data.message || 'Error deleting tax rate', 'danger');
        }
    })
    .catch(error => {
        btn.disabled = false;
        console.error('Error:', error);
        showAlert('Error: ' + error.message, 'danger');
    });
}

// RESET FORM
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
</script>

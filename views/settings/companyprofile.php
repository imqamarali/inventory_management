<?php
use yii\helpers\Html;

if (!isset($profile)) {
    $profile = [];
}
?>
<div class="tabbable">
    <ul class="nav nav-tabs" id="companyTabs">
        <li class="active">
            <a data-toggle="tab" href="#company-basic" aria-expanded="true">
                <i class="green ace-icon fa fa-info-circle bigger-120"></i>
                Basic Information
            </a>
        </li>
        <li>
            <a data-toggle="tab" href="#company-contact" aria-expanded="false">
                <i class="blue ace-icon fa fa-phone bigger-120"></i>
                Contact Information
            </a>
        </li>
        <li>
            <a data-toggle="tab" href="#company-financial" aria-expanded="false">
                <i class="orange ace-icon fa fa-money bigger-120"></i>
                Financial Settings
            </a>
        </li>
        <li>
            <a data-toggle="tab" href="#company-logo" aria-expanded="false">
                <i class="purple ace-icon fa fa-image bigger-120"></i>
                Logo & Branding
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Basic Information -->
        <div id="company-basic" class="tab-pane fade active in">
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <form id="companyBasicForm" method="POST">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <span class="required">*</span> Company Name
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="text" class="form-control" name="company_name" id="company_name"
                                       value="<?= htmlspecialchars($profile['company_name'] ?? '') ?>"
                                       placeholder="Your Company Name" required>
                                <small class="text-muted">Legal business name</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Address</label>
                            <div class="col-xs-12 col-sm-8">
                                <textarea class="form-control" name="company_address" placeholder="Street address" rows="3"><?= htmlspecialchars($profile['company_address'] ?? '') ?></textarea>
                                <small class="text-muted">Full business address</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Tax ID / Tax Number</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="text" class="form-control" name="tax_number"
                                       value="<?= htmlspecialchars($profile['tax_number'] ?? '') ?>"
                                       placeholder="e.g., VAT ID or Tax ID">
                                <small class="text-muted">Government tax identification number</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Website</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="url" class="form-control" name="company_website"
                                       value="<?= htmlspecialchars($profile['company_website'] ?? '') ?>"
                                       placeholder="https://example.com">
                                <small class="text-muted">Your company website URL</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3"></label>
                            <div class="col-xs-12 col-sm-8">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ace-icon fa fa-save"></i>
                                    Save Basic Information
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div id="company-contact" class="tab-pane fade">
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <form id="companyContactForm" method="POST">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Phone Number</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="tel" class="form-control" name="company_phone"
                                       value="<?= htmlspecialchars($profile['company_phone'] ?? '') ?>"
                                       placeholder="+1-555-0000">
                                <small class="text-muted">Main contact phone number</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Email Address</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="email" class="form-control" name="company_email"
                                       value="<?= htmlspecialchars($profile['company_email'] ?? '') ?>"
                                       placeholder="info@example.com">
                                <small class="text-muted">Main contact email address</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3"></label>
                            <div class="col-xs-12 col-sm-8">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ace-icon fa fa-save"></i>
                                    Save Contact Information
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Financial Settings -->
        <div id="company-financial" class="tab-pane fade">
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <form id="companyFinancialForm" method="POST">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Currency</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="currency">
                                    <option value="">-- Select Currency --</option>
                                    <option value="USD" <?= ($profile['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>US Dollar (USD)</option>
                                    <option value="EUR" <?= ($profile['currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>Euro (EUR)</option>
                                    <option value="GBP" <?= ($profile['currency'] ?? '') === 'GBP' ? 'selected' : '' ?>>British Pound (GBP)</option>
                                    <option value="JPY" <?= ($profile['currency'] ?? '') === 'JPY' ? 'selected' : '' ?>>Japanese Yen (JPY)</option>
                                    <option value="INR" <?= ($profile['currency'] ?? '') === 'INR' ? 'selected' : '' ?>>Indian Rupee (INR)</option>
                                    <option value="CAD" <?= ($profile['currency'] ?? '') === 'CAD' ? 'selected' : '' ?>>Canadian Dollar (CAD)</option>
                                    <option value="AUD" <?= ($profile['currency'] ?? '') === 'AUD' ? 'selected' : '' ?>>Australian Dollar (AUD)</option>
                                    <option value="CHF" <?= ($profile['currency'] ?? '') === 'CHF' ? 'selected' : '' ?>>Swiss Franc (CHF)</option>
                                </select>
                                <small class="text-muted">Currency for transactions and reports</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Currency Symbol</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="text" class="form-control" name="currency_symbol" maxlength="3"
                                       value="<?= htmlspecialchars($profile['currency_symbol'] ?? '') ?>"
                                       placeholder="$, €, £, etc.">
                                <small class="text-muted">Symbol to display with amounts</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Fiscal Year Start</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="fiscal_year_start">
                                    <option value="">-- Select Month --</option>
                                    <option value="01" <?= ($profile['fiscal_year_start'] ?? '') === '01' ? 'selected' : '' ?>>January</option>
                                    <option value="02" <?= ($profile['fiscal_year_start'] ?? '') === '02' ? 'selected' : '' ?>>February</option>
                                    <option value="03" <?= ($profile['fiscal_year_start'] ?? '') === '03' ? 'selected' : '' ?>>March</option>
                                    <option value="04" <?= ($profile['fiscal_year_start'] ?? '') === '04' ? 'selected' : '' ?>>April</option>
                                    <option value="05" <?= ($profile['fiscal_year_start'] ?? '') === '05' ? 'selected' : '' ?>>May</option>
                                    <option value="06" <?= ($profile['fiscal_year_start'] ?? '') === '06' ? 'selected' : '' ?>>June</option>
                                    <option value="07" <?= ($profile['fiscal_year_start'] ?? '') === '07' ? 'selected' : '' ?>>July</option>
                                    <option value="08" <?= ($profile['fiscal_year_start'] ?? '') === '08' ? 'selected' : '' ?>>August</option>
                                    <option value="09" <?= ($profile['fiscal_year_start'] ?? '') === '09' ? 'selected' : '' ?>>September</option>
                                    <option value="10" <?= ($profile['fiscal_year_start'] ?? '') === '10' ? 'selected' : '' ?>>October</option>
                                    <option value="11" <?= ($profile['fiscal_year_start'] ?? '') === '11' ? 'selected' : '' ?>>November</option>
                                    <option value="12" <?= ($profile['fiscal_year_start'] ?? '') === '12' ? 'selected' : '' ?>>December</option>
                                </select>
                                <small class="text-muted">When your financial year starts</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3"></label>
                            <div class="col-xs-12 col-sm-8">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ace-icon fa fa-save"></i>
                                    Save Financial Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Logo & Branding -->
        <div id="company-logo" class="tab-pane fade">
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <form id="companyLogoForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Company Logo</label>
                            <div class="col-xs-12 col-sm-8">
                                <?php if (!empty($profile['company_logo'])): ?>
                                <div class="thumbnail" style="width: 200px; margin-bottom: 15px;">
                                    <img src="<?= htmlspecialchars($profile['company_logo']) ?>" alt="Company Logo" style="max-height: 150px;">
                                    <div class="caption text-center">
                                        <small>Current Logo</small>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <input type="file" class="form-control" id="company_logo" name="company_logo" accept="image/*">
                                <small class="text-muted">JPG, PNG, GIF, or WEBP (Max 5MB). Recommended: 200x100px or larger</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3"></label>
                            <div class="col-xs-12 col-sm-8">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ace-icon fa fa-upload"></i>
                                    Upload Logo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const isFileUpload = form.enctype === 'multipart/form-data';
        const formData = isFileUpload ? new FormData(form) : new FormData(form);

        fetch('index.php?r=settings/companyprofile', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('success', data.message);
                if (isFileUpload) {
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            } else {
                showMessage('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('danger', 'An error occurred while saving.');
        });
    });
});

function showMessage(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade in';
    alertDiv.style.marginTop = '20px';
    alertDiv.innerHTML = '<button type="button" class="close" data-dismiss="alert">&times;</button><i class="ace-icon fa fa-info-circle"></i> ' + message;

    const parent = document.querySelector('.tab-content');
    parent.insertBefore(alertDiv, parent.firstChild);

    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>

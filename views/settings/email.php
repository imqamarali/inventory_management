<?php
use yii\helpers\Html;

if (!isset($config)) {
    $config = [];
}
?>
<div class="tabbable">
    <ul class="nav nav-tabs" id="emailTabs">
        <li class="active">
            <a data-toggle="tab" href="#email-config" aria-expanded="true">
                <i class="green ace-icon fa fa-cog bigger-120"></i>
                SMTP Configuration
            </a>
        </li>
        <li>
            <a data-toggle="tab" href="#email-test" aria-expanded="false">
                <i class="blue ace-icon fa fa-envelope bigger-120"></i>
                Test Email
            </a>
        </li>
        <li>
            <a data-toggle="tab" href="#email-info" aria-expanded="false">
                <i class="purple ace-icon fa fa-info-circle bigger-120"></i>
                Help & Info
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- SMTP Configuration Tab -->
        <div id="email-config" class="tab-pane fade active in">
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <form id="emailForm" method="POST">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <span class="required">*</span> SMTP Host
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="text" class="form-control" id="email_smtp_host" name="email_smtp_host"
                                       value="<?= htmlspecialchars($config['email_smtp_host'] ?? '') ?>"
                                       placeholder="e.g., smtp.gmail.com or mail.example.com" required>
                                <small class="text-muted">SMTP server hostname</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <span class="required">*</span> SMTP Port
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="number" class="form-control" id="email_smtp_port" name="email_smtp_port"
                                       value="<?= htmlspecialchars($config['email_smtp_port'] ?? '587') ?>"
                                       placeholder="587 or 465" required>
                                <small class="text-muted">Typically 587 (TLS) or 465 (SSL)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <span class="required">*</span> Encryption
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" id="email_encryption" name="email_encryption" required>
                                    <option value="tls" <?= ($config['email_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
                                    <option value="ssl" <?= ($config['email_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
                                    <option value="none" <?= ($config['email_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>No Encryption</option>
                                </select>
                                <small class="text-muted">Select the encryption method</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <span class="required">*</span> SMTP Username
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="text" class="form-control" id="email_smtp_username" name="email_smtp_username"
                                       value="<?= htmlspecialchars($config['email_smtp_username'] ?? '') ?>"
                                       placeholder="SMTP username or email address" required>
                                <small class="text-muted">Usually your email address</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <span class="required">*</span> SMTP Password
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="password" class="form-control" id="email_smtp_password" name="email_smtp_password"
                                       placeholder="SMTP password or app password" required>
                                <small class="text-muted">Your SMTP password (not stored in plain text)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <span class="required">*</span> From Email Address
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="email" class="form-control" id="email_from_address" name="email_from_address"
                                       value="<?= htmlspecialchars($config['email_from_address'] ?? '') ?>"
                                       placeholder="sender@example.com" required>
                                <small class="text-muted">Email address shown as sender</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                From Name
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="text" class="form-control" id="email_from_name" name="email_from_name"
                                       value="<?= htmlspecialchars($config['email_from_name'] ?? '') ?>"
                                       placeholder="Your Company Name">
                                <small class="text-muted">Display name for the sender</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <label>
                                    <input type="checkbox" class="ace" id="email_smtp_enabled" name="email_smtp_enabled" value="1"
                                           <?= ($config['email_smtp_enabled'] ?? '') ? 'checked' : '' ?>>
                                    <span class="lbl"> Enable Email Notifications</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <button type="submit" class="btn btn-sm btn-success" id="emailSaveBtn">
                                    <i class="ace-icon fa fa-save"></i>
                                    Save Configuration
                                </button>
                                <button type="button" class="btn btn-sm btn-info" id="emailTestBtn">
                                    <i class="ace-icon fa fa-paper-plane"></i>
                                    Send Test Email
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Test Email Tab -->
        <div id="email-test" class="tab-pane fade">
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <div class="alert alert-info">
                        <i class="ace-icon fa fa-info-circle"></i>
                        Click the "Send Test Email" button above to test your email configuration. A test email will be sent to your SMTP username email address.
                    </div>
                    <div id="emailTestResult" style="display:none; margin-top: 20px;"></div>
                </div>
            </div>
        </div>

        <!-- Help & Info Tab -->
        <div id="email-info" class="tab-pane fade">
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <h4>Email Configuration Help</h4>

                    <div class="row">
                        <div class="col-sm-6">
                            <h5><i class="ace-icon fa fa-google"></i> Gmail Settings</h5>
                            <ul class="list-unstyled">
                                <li><strong>SMTP Host:</strong> smtp.gmail.com</li>
                                <li><strong>SMTP Port:</strong> 587 (TLS) or 465 (SSL)</li>
                                <li><strong>Username:</strong> your.email@gmail.com</li>
                                <li><strong>Password:</strong> App Password (not your Gmail password)</li>
                                <li><strong>Encryption:</strong> TLS or SSL</li>
                                <li class="text-muted"><small>Enable 2-Step Verification and generate an App Password</small></li>
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <h5><i class="ace-icon fa fa-envelope"></i> Office 365 Settings</h5>
                            <ul class="list-unstyled">
                                <li><strong>SMTP Host:</strong> smtp.office365.com</li>
                                <li><strong>SMTP Port:</strong> 587 (TLS)</li>
                                <li><strong>Username:</strong> your.email@domain.com</li>
                                <li><strong>Password:</strong> Your Office 365 password</li>
                                <li><strong>Encryption:</strong> TLS</li>
                                <li class="text-muted"><small>Basic auth must be enabled in Office 365</small></li>
                            </ul>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 20px;">
                        <div class="col-sm-6">
                            <h5><i class="ace-icon fa fa-globe"></i> Custom Server Settings</h5>
                            <ul class="list-unstyled">
                                <li><strong>SMTP Host:</strong> mail.yourdomain.com</li>
                                <li><strong>SMTP Port:</strong> 587, 465, or 25</li>
                                <li><strong>Username:</strong> user@yourdomain.com</li>
                                <li><strong>Password:</strong> Your email password</li>
                                <li><strong>Encryption:</strong> TLS, SSL, or None</li>
                                <li class="text-muted"><small>Contact your hosting provider for specific settings</small></li>
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <h5><i class="ace-icon fa fa-info-circle"></i> Tips</h5>
                            <ul class="list-unstyled">
                                <li>✓ Test your configuration before saving</li>
                                <li>✓ Use App Passwords instead of main passwords when available</li>
                                <li>✓ Ensure firewall allows outbound SMTP connections</li>
                                <li>✓ Enable "Less secure app access" if required by provider</li>
                                <li>✓ Check spam folder for test emails</li>
                                <li>✓ Keep credentials secure and up to date</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('emailSaveBtn').addEventListener('click', function(e) {
    e.preventDefault();
    const form = document.getElementById('emailForm');
    const formData = new FormData(form);

    fetch('index.php?r=settings/email', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('success', data.message, 'emailForm');
        } else {
            showMessage('danger', data.message, 'emailForm');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('danger', 'An error occurred while saving.', 'emailForm');
    });
});

document.getElementById('emailTestBtn').addEventListener('click', function() {
    const host = document.getElementById('email_smtp_host').value;
    const port = document.getElementById('email_smtp_port').value;
    const username = document.getElementById('email_smtp_username').value;
    const password = document.getElementById('email_smtp_password').value;
    const encryption = document.getElementById('email_encryption').value;
    const fromEmail = document.getElementById('email_from_address').value;

    if (!host || !port || !username || !password || !fromEmail) {
        showMessage('warning', 'Please fill in all required fields before testing.', 'emailForm');
        return;
    }

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="ace-icon fa fa-spinner fa-spin"></i> Testing...';

    const formData = new FormData();
    formData.append('flag', 'test');
    formData.append('email_smtp_host', host);
    formData.append('email_smtp_port', port);
    formData.append('email_smtp_username', username);
    formData.append('email_smtp_password', password);
    formData.append('email_encryption', encryption);
    formData.append('email_from_address', fromEmail);
    formData.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->getCsrfToken() ?>');

    fetch('index.php?r=settings/email', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ace-icon fa fa-paper-plane"></i> Send Test Email';

        const resultDiv = document.getElementById('emailTestResult');
        resultDiv.style.display = 'block';

        if (data.success) {
            resultDiv.innerHTML = '<div class="alert alert-success"><i class="ace-icon fa fa-check-circle"></i> ' + data.message + '</div>';
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger"><i class="ace-icon fa fa-exclamation-circle"></i> ' + data.message + '</div>';
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ace-icon fa fa-paper-plane"></i> Send Test Email';
        console.error('Error:', error);
        showMessage('danger', 'An error occurred while testing.', 'emailForm');
    });
});

function showMessage(type, message, parentElement) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade in';
    alertDiv.innerHTML = '<button type="button" class="close" data-dismiss="alert">&times;</button>' + message;

    const parent = document.getElementById(parentElement) || document.querySelector('#emailTabs').parentElement;
    parent.insertBefore(alertDiv, parent.firstChild);

    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>

<?php
use yii\helpers\Html;

if (!isset($config)) {
    $config = [];
}
if (!isset($config['sms_providers'])) {
    $config['sms_providers'] = [];
}
?>
<div class="tabbable">
    <ul class="nav nav-tabs" id="smsTabs">
        <li class="active">
            <a data-toggle="tab" href="#sms-config" aria-expanded="true">
                <i class="green ace-icon fa fa-cog bigger-120"></i>
                API Configuration
            </a>
        </li>
        <li>
            <a data-toggle="tab" href="#sms-test" aria-expanded="false">
                <i class="blue ace-icon fa fa-mobile bigger-120"></i>
                Test SMS
            </a>
        </li>
        <li>
            <a data-toggle="tab" href="#sms-providers" aria-expanded="false">
                <i class="purple ace-icon fa fa-building bigger-120"></i>
                Providers Info
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- SMS API Configuration Tab -->
        <div id="sms-config" class="tab-pane fade active in">
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <form id="smsForm" method="POST">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <span class="required">*</span> SMS Provider
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" id="sms_api_provider" name="sms_api_provider" required onchange="updateProviderFields()">
                                    <option value="">-- Select a Provider --</option>
                                    <?php foreach ($config['sms_providers'] as $key => $name): ?>
                                    <option value="<?= htmlspecialchars($key) ?>" <?= ($config['sms_api_provider'] ?? '') === $key ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($name) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Choose your SMS service provider</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <span class="required">*</span> API Key
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="password" class="form-control" id="sms_api_key" name="sms_api_key"
                                       placeholder="Your API Key from the provider" required>
                                <small class="text-muted">Account SID (Twilio) or API Key (Vonage/AWS)</small>
                            </div>
                        </div>

                        <div class="form-group" id="apiSecretField" style="display:none;">
                            <label class="control-label col-xs-12 col-sm-3">
                                <span class="required">*</span> API Secret/Token
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="password" class="form-control" id="sms_api_secret" name="sms_api_secret"
                                       placeholder="Your API Secret or Auth Token">
                                <small class="text-muted">Auth Token (Twilio) or Secret (Vonage)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <span class="required">*</span> Sender ID
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="text" class="form-control" id="sms_sender_id" name="sms_sender_id"
                                       value="<?= htmlspecialchars($config['sms_sender_id'] ?? '') ?>"
                                       placeholder="Your Company Name or Sender ID (max 11 chars)" required>
                                <small class="text-muted">Name or number displayed in SMS messages</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <label>
                                    <input type="checkbox" class="ace" id="sms_enabled" name="sms_enabled" value="1"
                                           <?= ($config['sms_enabled'] ?? '') ? 'checked' : '' ?>>
                                    <span class="lbl"> Enable SMS Notifications</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <button type="submit" class="btn btn-sm btn-success" id="smsSaveBtn">
                                    <i class="ace-icon fa fa-save"></i>
                                    Save Configuration
                                </button>
                                <button type="button" class="btn btn-sm btn-info" id="smsTestBtn">
                                    <i class="ace-icon fa fa-mobile"></i>
                                    Validate Credentials
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Test SMS Tab -->
        <div id="sms-test" class="tab-pane fade">
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <div class="alert alert-info">
                        <i class="ace-icon fa fa-info-circle"></i>
                        Click the "Validate Credentials" button to verify your SMS API configuration. This will check that your credentials are correct.
                    </div>

                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-3">
                            Test Phone Number
                        </label>
                        <div class="col-xs-12 col-sm-8">
                            <input type="tel" class="form-control" id="sms_test_phone"
                                   placeholder="+1234567890 or your test phone">
                            <small class="text-muted">Phone number to test SMS delivery (include country code)</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-3">
                        </label>
                        <div class="col-xs-12 col-sm-8">
                            <button type="button" class="btn btn-sm btn-primary" id="smsSendTestBtn">
                                <i class="ace-icon fa fa-paper-plane"></i>
                                Send Test SMS
                            </button>
                        </div>
                    </div>

                    <div id="smsTestResult" style="display:none; margin-top: 20px;"></div>
                </div>
            </div>
        </div>

        <!-- Providers Info Tab -->
        <div id="sms-providers" class="tab-pane fade">
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <h4>SMS Service Providers</h4>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <i class="ace-icon fa fa-phone"></i>
                                        Twilio
                                    </h5>
                                </div>
                                <div class="panel-body">
                                    <p><strong>Website:</strong> twilio.com</p>
                                    <ul class="list-unstyled">
                                        <li><strong>API Key:</strong> Account SID</li>
                                        <li><strong>API Secret:</strong> Auth Token</li>
                                        <li><strong>Sender ID:</strong> Phone number from Twilio</li>
                                        <li><strong>Cost:</strong> Pay-per-SMS model</li>
                                        <li><strong>Features:</strong> Reliable, Global coverage</li>
                                    </ul>
                                    <a href="https://www.twilio.com/console" target="_blank" class="btn btn-xs btn-primary">
                                        <i class="ace-icon fa fa-external-link"></i> Go to Twilio Console
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <i class="ace-icon fa fa-mobile"></i>
                                        Vonage (Nexmo)
                                    </h5>
                                </div>
                                <div class="panel-body">
                                    <p><strong>Website:</strong> vonage.com</p>
                                    <ul class="list-unstyled">
                                        <li><strong>API Key:</strong> API Key</li>
                                        <li><strong>API Secret:</strong> API Secret</li>
                                        <li><strong>Sender ID:</strong> Brand name or number</li>
                                        <li><strong>Cost:</strong> Competitive rates</li>
                                        <li><strong>Features:</strong> Global, 2FA support</li>
                                    </ul>
                                    <a href="https://dashboard.nexmo.com/" target="_blank" class="btn btn-xs btn-primary">
                                        <i class="ace-icon fa fa-external-link"></i> Go to Vonage Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 20px;">
                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <i class="ace-icon fa fa-aws"></i>
                                        AWS SNS
                                    </h5>
                                </div>
                                <div class="panel-body">
                                    <p><strong>Website:</strong> aws.amazon.com/sns</p>
                                    <ul class="list-unstyled">
                                        <li><strong>API Key:</strong> AWS Access Key ID</li>
                                        <li><strong>API Secret:</strong> AWS Secret Access Key</li>
                                        <li><strong>Sender ID:</strong> AWS Sender ID</li>
                                        <li><strong>Cost:</strong> Integrated with AWS billing</li>
                                        <li><strong>Features:</strong> Enterprise-grade reliability</li>
                                    </ul>
                                    <a href="https://console.aws.amazon.com/sns/" target="_blank" class="btn btn-xs btn-primary">
                                        <i class="ace-icon fa fa-external-link"></i> Go to AWS Console
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <i class="ace-icon fa fa-plug"></i>
                                        Custom API
                                    </h5>
                                </div>
                                <div class="panel-body">
                                    <p><strong>Website:</strong> Your own SMS gateway</p>
                                    <ul class="list-unstyled">
                                        <li><strong>API Key:</strong> Your API credentials</li>
                                        <li><strong>API Secret:</strong> Authentication token</li>
                                        <li><strong>Sender ID:</strong> Your sender identifier</li>
                                        <li><strong>Cost:</strong> Depends on provider</li>
                                        <li><strong>Features:</strong> Full control</li>
                                    </ul>
                                    <p class="text-muted"><small>Contact your SMS provider for specific credentials</small></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info" style="margin-top: 20px;">
                        <h5><i class="ace-icon fa fa-lightbulb-o"></i> Tips for SMS Configuration</h5>
                        <ul class="list-unstyled">
                            <li>✓ Sender ID should be 11 characters or less for best compatibility</li>
                            <li>✓ Use international format for phone numbers (+country code)</li>
                            <li>✓ Keep your API credentials secure and never share them</li>
                            <li>✓ Test with a real phone number before enabling in production</li>
                            <li>✓ Monitor your SMS balance/quota regularly</li>
                            <li>✓ Check provider rate limits and adjust batch sending accordingly</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateProviderFields() {
    const provider = document.getElementById('sms_api_provider').value;
    const secretField = document.getElementById('apiSecretField');

    if (provider === 'custom') {
        secretField.style.display = 'none';
        document.getElementById('sms_api_secret').removeAttribute('required');
    } else if (provider) {
        secretField.style.display = 'block';
        document.getElementById('sms_api_secret').setAttribute('required', 'required');
    }
}

document.getElementById('smsSaveBtn').addEventListener('click', function(e) {
    e.preventDefault();
    const form = document.getElementById('smsForm');
    const formData = new FormData(form);

    fetch('index.php?r=settings/sms', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('success', data.message, 'smsForm');
        } else {
            showMessage('danger', data.message, 'smsForm');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('danger', 'An error occurred while saving.', 'smsForm');
    });
});

document.getElementById('smsTestBtn').addEventListener('click', function() {
    const provider = document.getElementById('sms_api_provider').value;
    const apiKey = document.getElementById('sms_api_key').value;
    const apiSecret = document.getElementById('sms_api_secret').value;
    const senderId = document.getElementById('sms_sender_id').value;

    if (!provider || !apiKey || !senderId) {
        showMessage('warning', 'Please fill in required fields before validating.', 'smsForm');
        return;
    }

    if (provider !== 'custom' && !apiSecret) {
        showMessage('warning', 'API Secret is required for this provider.', 'smsForm');
        return;
    }

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="ace-icon fa fa-spinner fa-spin"></i> Validating...';

    const formData = new FormData();
    formData.append('flag', 'test');
    formData.append('sms_api_provider', provider);
    formData.append('sms_api_key', apiKey);
    formData.append('sms_api_secret', apiSecret);
    formData.append('sms_sender_id', senderId);
    formData.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->getCsrfToken() ?>');

    fetch('index.php?r=settings/sms', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ace-icon fa fa-mobile"></i> Validate Credentials';

        const resultDiv = document.getElementById('smsTestResult');
        resultDiv.style.display = 'block';

        if (data.success) {
            resultDiv.innerHTML = '<div class="alert alert-success"><i class="ace-icon fa fa-check-circle"></i> ' + data.message + '</div>';
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger"><i class="ace-icon fa fa-exclamation-circle"></i> ' + data.message + '</div>';
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ace-icon fa fa-mobile"></i> Validate Credentials';
        console.error('Error:', error);
        showMessage('danger', 'An error occurred during validation.', 'smsForm');
    });
});

document.getElementById('smsSendTestBtn').addEventListener('click', function() {
    const phone = document.getElementById('sms_test_phone').value;

    if (!phone) {
        showMessage('warning', 'Please enter a test phone number.', 'smsForm');
        return;
    }

    alert('SMS test feature requires integration with your SMS provider API. Contact support for assistance.');
});

function showMessage(type, message, parentElement) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade in';
    alertDiv.innerHTML = '<button type="button" class="close" data-dismiss="alert">&times;</button><i class="ace-icon fa fa-info-circle"></i> ' + message;

    const parent = document.getElementById(parentElement) || document.querySelector('#smsTabs').parentElement;
    parent.insertBefore(alertDiv, parent.firstChild);

    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateProviderFields();
});
</script>

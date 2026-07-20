<?php
use yii\helpers\Html;

if (!isset($settings)) {
    $settings = [];
}

// Helper function to get setting value
function getSetting($settings, $key, $default = '') {
    foreach ($settings as $setting) {
        if (isset($setting['setting_key']) && $setting['setting_key'] === $key) {
            return $setting['setting_value'] ?? $default;
        }
    }
    return $default;
}
?>
<div class="tabbable">
    <ul class="nav nav-tabs" id="generalTabs">
        <li class="active">
            <a data-toggle="tab" href="#general-system" aria-expanded="true">
                <i class="green ace-icon fa fa-cog bigger-120"></i>
                System Settings
            </a>
        </li>
        <li>
            <a data-toggle="tab" href="#general-display" aria-expanded="false">
                <i class="blue ace-icon fa fa-desktop bigger-120"></i>
                Display Settings
            </a>
        </li>
        <li>
            <a data-toggle="tab" href="#general-regional" aria-expanded="false">
                <i class="purple ace-icon fa fa-globe bigger-120"></i>
                Regional Settings
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- System Settings Tab -->
        <div id="general-system" class="tab-pane fade active in">
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <form id="systemSettingsForm" method="POST">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Application Name</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="text" class="form-control" name="app_name"
                                       value="<?= htmlspecialchars(getSetting($settings, 'app_name', 'Inventory System')) ?>"
                                       placeholder="Your Application Name">
                                <small class="text-muted">Displayed in header and emails</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Application Version</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="text" class="form-control" name="app_version"
                                       value="<?= htmlspecialchars(getSetting($settings, 'app_version', '1.0.0')) ?>"
                                       placeholder="e.g., 1.0.0">
                                <small class="text-muted">Current version of your application</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Support Email</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="email" class="form-control" name="support_email"
                                       value="<?= htmlspecialchars(getSetting($settings, 'support_email', '')) ?>"
                                       placeholder="support@example.com">
                                <small class="text-muted">Email for user support requests</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Support Phone</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="tel" class="form-control" name="support_phone"
                                       value="<?= htmlspecialchars(getSetting($settings, 'support_phone', '')) ?>"
                                       placeholder="+1-555-0000">
                                <small class="text-muted">Contact phone number</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <input type="checkbox" name="maintenance_mode" class="ace" value="1"
                                       <?= getSetting($settings, 'maintenance_mode', '') ? 'checked' : '' ?>>
                                <span class="lbl"> Enable Maintenance Mode</span>
                            </label>
                            <div class="col-xs-12 col-sm-8"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <input type="checkbox" name="debug_mode" class="ace" value="1"
                                       <?= getSetting($settings, 'debug_mode', '') ? 'checked' : '' ?>>
                                <span class="lbl"> Enable Debug Mode</span>
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <small class="text-muted">Show detailed error messages (disable in production)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <input type="checkbox" name="enable_audit_log" class="ace" value="1"
                                       <?= getSetting($settings, 'enable_audit_log', '') ? 'checked' : '' ?>>
                                <span class="lbl"> Enable Audit Logging</span>
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <small class="text-muted">Log all user actions for security</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3"></label>
                            <div class="col-xs-12 col-sm-8">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ace-icon fa fa-save"></i>
                                    Save Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Display Settings Tab -->
        <div id="general-display" class="tab-pane fade">
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <form id="displaySettingsForm" method="POST">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Theme</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="theme">
                                    <option value="light" <?= getSetting($settings, 'theme', 'light') === 'light' ? 'selected' : '' ?>>Light</option>
                                    <option value="dark" <?= getSetting($settings, 'theme', 'light') === 'dark' ? 'selected' : '' ?>>Dark</option>
                                    <option value="auto" <?= getSetting($settings, 'theme', 'light') === 'auto' ? 'selected' : '' ?>>Auto (System)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Items Per Page</label>
                            <div class="col-xs-12 col-sm-8">
                                <input type="number" class="form-control" name="items_per_page" min="10" max="100"
                                       value="<?= htmlspecialchars(getSetting($settings, 'items_per_page', '25')) ?>">
                                <small class="text-muted">Default pagination size</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <input type="checkbox" name="enable_sidebar" class="ace" value="1"
                                       <?= getSetting($settings, 'enable_sidebar', '') ? 'checked' : '' ?>>
                                <span class="lbl"> Show Sidebar by Default</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">
                                <input type="checkbox" name="show_tooltips" class="ace" value="1"
                                       <?= getSetting($settings, 'show_tooltips', '') ? 'checked' : '' ?>>
                                <span class="lbl"> Show Help Tooltips</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3"></label>
                            <div class="col-xs-12 col-sm-8">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ace-icon fa fa-save"></i>
                                    Save Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Regional Settings Tab -->
        <div id="general-regional" class="tab-pane fade">
            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-12">
                    <form id="regionalSettingsForm" method="POST">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Language</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="language">
                                    <option value="en" <?= getSetting($settings, 'language', 'en') === 'en' ? 'selected' : '' ?>>English</option>
                                    <option value="es" <?= getSetting($settings, 'language', 'en') === 'es' ? 'selected' : '' ?>>Spanish</option>
                                    <option value="fr" <?= getSetting($settings, 'language', 'en') === 'fr' ? 'selected' : '' ?>>French</option>
                                    <option value="de" <?= getSetting($settings, 'language', 'en') === 'de' ? 'selected' : '' ?>>German</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Timezone</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="timezone">
                                    <option value="UTC" <?= getSetting($settings, 'timezone', 'UTC') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                                    <option value="America/New_York">Eastern Time (US)</option>
                                    <option value="America/Chicago">Central Time (US)</option>
                                    <option value="America/Los_Angeles">Pacific Time (US)</option>
                                    <option value="Europe/London">London (GMT)</option>
                                    <option value="Europe/Paris">Paris (CET)</option>
                                    <option value="Asia/Tokyo">Tokyo (JST)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Date Format</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="date_format">
                                    <option value="Y-m-d" <?= getSetting($settings, 'date_format', 'Y-m-d') === 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD</option>
                                    <option value="m/d/Y" <?= getSetting($settings, 'date_format', 'Y-m-d') === 'm/d/Y' ? 'selected' : '' ?>>MM/DD/YYYY</option>
                                    <option value="d/m/Y" <?= getSetting($settings, 'date_format', 'Y-m-d') === 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3">Time Format</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="time_format">
                                    <option value="H:i:s" <?= getSetting($settings, 'time_format', 'H:i:s') === 'H:i:s' ? 'selected' : '' ?>>24-hour (HH:MM:SS)</option>
                                    <option value="h:i:s A" <?= getSetting($settings, 'time_format', 'H:i:s') === 'h:i:s A' ? 'selected' : '' ?>>12-hour (HH:MM:SS AM/PM)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-xs-12 col-sm-3"></label>
                            <div class="col-xs-12 col-sm-8">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ace-icon fa fa-save"></i>
                                    Save Settings
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
const CSRF_TOKEN = '<?= Yii::$app->request->getCsrfToken() ?>';
const CSRF_PARAM = '<?= Yii::$app->request->csrfParam ?>';
const API_URL = 'index.php?r=settings/generalsettings';

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const button = form.querySelector('button[type="submit"]');
        const originalHTML = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="ace-icon fa fa-spinner fa-spin"></i> Saving...';

        const formData = new FormData(form);

        // Remove CSRF from FormData if it exists and add via append
        if (formData.has(CSRF_PARAM)) {
            formData.delete(CSRF_PARAM);
        }
        formData.append(CSRF_PARAM, CSRF_TOKEN);
        formData.append('flag', 'save_bulk');

        fetch(API_URL, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message || 'Failed to save settings.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Error: ' + error.message);
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalHTML;
        });
    });
});

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    const borderColor = type === 'success' ? '#2ecc71' : '#e74c3c';

    alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade in';
    alertDiv.style.marginTop = '15px';
    alertDiv.style.borderLeft = '4px solid ' + borderColor;
    alertDiv.innerHTML = '<button type="button" class="close" data-dismiss="alert">&times;</button><i class="ace-icon fa ' + iconClass + '"></i> ' + message;

    const parent = document.querySelector('.tab-content');
    parent.insertBefore(alertDiv, parent.firstChild);

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>

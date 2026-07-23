<?php
$csrfToken = Yii::$app->request->getCsrfToken();
$csrfParam = Yii::$app->request->csrfParam;
?>

<div class="row">
    <div class="col-md-8">
        <div class="dashboard-box">
            <h4>
                <i class="fa fa-lock"></i>
                Login Authentication & 2FA Settings
            </h4>

            <div style="padding: 20px;">

                <!-- 2FA Enable/Disable Toggle -->
                <div style="margin-bottom: 25px; padding: 15px; background: #f9f9f9; border-radius: 5px; border-left: 4px solid #3498db;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <h5 style="margin: 0 0 5px 0;">
                                <i class="fa fa-shield"></i>
                                Enable Two-Factor Authentication
                            </h5>
                            <p style="margin: 0; color: #7f8c8d; font-size: 12px;">
                                Add an extra layer of security to your account
                            </p>
                        </div>
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" id="twoFactorEnabled"
                                   <?php echo isset($authSettings['two_factor_enabled']) && $authSettings['two_factor_enabled'] ? 'checked' : ''; ?>>
                            <span id="toggleLabel" style="font-weight: 600;">
                                <?php echo isset($authSettings['two_factor_enabled']) && $authSettings['two_factor_enabled'] ? '✓ Enabled' : '✗ Disabled'; ?>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Authentication Method Selection -->
                <div id="authMethodSection" style="display: <?php echo isset($authSettings['two_factor_enabled']) && $authSettings['two_factor_enabled'] ? 'block' : 'none'; ?>; margin-bottom: 25px;">
                    <h5 style="margin-bottom: 15px;">
                        <i class="fa fa-envelope"></i>
                        Select Authentication Method
                    </h5>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <!-- Email Option -->
                        <div style="padding: 15px; border: 2px solid #ddd; border-radius: 5px; cursor: pointer;"
                             id="emailMethodDiv"
                             onclick="selectAuthMethod('email')"
                             style="padding: 15px; border: 2px solid <?php echo (!isset($authSettings['auth_method']) || $authSettings['auth_method'] === 'email' || $authSettings['auth_method'] === 'both') ? '#27ae60' : '#ddd'; ?>; border-radius: 5px; cursor: pointer; background: <?php echo (!isset($authSettings['auth_method']) || $authSettings['auth_method'] === 'email' || $authSettings['auth_method'] === 'both') ? '#ecf9f0' : '#fff'; ?>;">
                            <div style="text-align: center;">
                                <i class="fa fa-envelope" style="font-size: 32px; color: #27ae60; margin-bottom: 10px; display: block;"></i>
                                <strong>Email</strong>
                                <input type="radio" name="auth_method" value="email" id="emailMethod"
                                       <?php echo (!isset($authSettings['auth_method']) || $authSettings['auth_method'] === 'email') ? 'checked' : ''; ?>
                                       style="margin-top: 10px;">
                            </div>
                        </div>

                        <!-- SMS Option -->
                        <div style="padding: 15px; border: 2px solid #ddd; border-radius: 5px; cursor: pointer;"
                             id="smsMethodDiv"
                             onclick="selectAuthMethod('sms')"
                             style="padding: 15px; border: 2px solid <?php echo isset($authSettings['auth_method']) && $authSettings['auth_method'] === 'sms' ? '#3498db' : '#ddd'; ?>; border-radius: 5px; cursor: pointer; background: <?php echo isset($authSettings['auth_method']) && $authSettings['auth_method'] === 'sms' ? '#ecf5fb' : '#fff'; ?>;">
                            <div style="text-align: center;">
                                <i class="fa fa-mobile" style="font-size: 32px; color: #3498db; margin-bottom: 10px; display: block;"></i>
                                <strong>SMS</strong>
                                <input type="radio" name="auth_method" value="sms" id="smsMethod"
                                       <?php echo isset($authSettings['auth_method']) && $authSettings['auth_method'] === 'sms' ? 'checked' : ''; ?>
                                       style="margin-top: 10px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Address Input (for Email method) -->
                <div id="emailAddressSection" style="display: <?php echo (!isset($authSettings['auth_method']) || $authSettings['auth_method'] === 'email') && isset($authSettings['two_factor_enabled']) && $authSettings['two_factor_enabled'] ? 'block' : 'none'; ?>; margin-bottom: 20px;">
                    <label for="emailAddress" style="display: block; margin-bottom: 5px; font-weight: 600;">
                        <i class="fa fa-envelope"></i>
                        Email Address for OTP
                    </label>
                    <input type="email" id="emailAddress" class="form-control"
                           placeholder="Enter your email address"
                           value="<?php echo isset($authSettings['email_address']) ? htmlspecialchars($authSettings['email_address']) : ''; ?>"
                           style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <small style="color: #7f8c8d;">You will receive login OTP codes at this email address</small>
                </div>

                <!-- Phone Number Input (for SMS method) -->
                <div id="phoneNumberSection" style="display: <?php echo isset($authSettings['auth_method']) && $authSettings['auth_method'] === 'sms' && isset($authSettings['two_factor_enabled']) && $authSettings['two_factor_enabled'] ? 'block' : 'none'; ?>; margin-bottom: 20px;">
                    <label for="phoneNumber" style="display: block; margin-bottom: 5px; font-weight: 600;">
                        <i class="fa fa-mobile"></i>
                        Phone Number for OTP
                    </label>
                    <input type="tel" id="phoneNumber" class="form-control"
                           placeholder="Enter your phone number (e.g., +923001234567)"
                           value="<?php echo isset($authSettings['phone_number']) ? htmlspecialchars($authSettings['phone_number']) : ''; ?>"
                           style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <small style="color: #7f8c8d;">You will receive login OTP codes as SMS to this number</small>
                </div>

                <!-- Save Button -->
                <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #ddd;">
                    <button id="saveLoginAuthBtn" style="padding: 10px 30px; background: #27ae60; color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">
                        <i class="fa fa-save"></i>
                        Save Settings
                    </button>
                    <span id="saveStatus" style="margin-left: 10px; display: none;"></span>
                </div>

            </div>
        </div>

        <!-- Info Box -->
        <div style="margin-top: 20px; padding: 15px; background: #ecf9f0; border-left: 4px solid #27ae60; border-radius: 4px;">
            <h5 style="margin-top: 0;">
                <i class="fa fa-info-circle"></i>
                How 2FA Works
            </h5>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>After entering your username and password, you'll be prompted to enter a one-time code (OTP)</li>
                <li>The OTP will be sent to your selected method (Email or SMS)</li>
                <li>Enter the 6-digit code within 10 minutes to complete login</li>
                <li>This adds an extra layer of security to your account</li>
            </ul>
        </div>

    </div>

    <!-- Right Column - Current Settings Display -->
    <div class="col-md-4">
        <div class="dashboard-box">
            <h4>
                <i class="fa fa-info-circle"></i>
                Current Status
            </h4>

            <div style="padding: 15px;">

                <!-- 2FA Status -->
                <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">
                    <p style="margin: 0; color: #7f8c8d; font-size: 12px;">2FA Status</p>
                    <h5 style="margin: 5px 0;">
                        <span id="statusBadge" style="display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: <?php echo isset($authSettings['two_factor_enabled']) && $authSettings['two_factor_enabled'] ? '#d4edda; color: #155724;' : '#f8d7da; color: #721c24;'; ?>">
                            <?php echo isset($authSettings['two_factor_enabled']) && $authSettings['two_factor_enabled'] ? '✓ ENABLED' : '✗ DISABLED'; ?>
                        </span>
                    </h5>
                </div>

                <!-- Authentication Method -->
                <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">
                    <p style="margin: 0; color: #7f8c8d; font-size: 12px;">Auth Method</p>
                    <h5 style="margin: 5px 0;">
                        <i class="fa <?php echo isset($authSettings['auth_method']) && $authSettings['auth_method'] === 'sms' ? 'fa-mobile' : 'fa-envelope'; ?>"></i>
                        <?php echo isset($authSettings['auth_method']) ? ucfirst($authSettings['auth_method']) : 'Email'; ?>
                    </h5>
                </div>

                <!-- Email Address -->
                <?php if (isset($authSettings['email_address']) && !empty($authSettings['email_address'])): ?>
                    <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">
                        <p style="margin: 0; color: #7f8c8d; font-size: 12px;">Email Address</p>
                        <h5 style="margin: 5px 0; word-break: break-all;">
                            <?php echo htmlspecialchars($authSettings['email_address']); ?>
                        </h5>
                    </div>
                <?php endif; ?>

                <!-- Phone Number -->
                <?php if (isset($authSettings['phone_number']) && !empty($authSettings['phone_number'])): ?>
                    <div style="margin-bottom: 20px;">
                        <p style="margin: 0; color: #7f8c8d; font-size: 12px;">Phone Number</p>
                        <h5 style="margin: 5px 0;">
                            <?php echo htmlspecialchars($authSettings['phone_number']); ?>
                        </h5>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Security Tips -->
        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
            <h5 style="margin-top: 0;">
                <i class="fa fa-lightbulb-o"></i>
                Security Tips
            </h5>
            <ul style="margin: 10px 0; padding-left: 20px; font-size: 12px;">
                <li>Keep your email & phone secure</li>
                <li>Never share your OTP codes</li>
                <li>Update your contact info regularly</li>
                <li>Use both methods for maximum security</li>
            </ul>
        </div>

    </div>
</div>

<script>
    function selectAuthMethod(method) {
        document.getElementById(method + 'Method').checked = true;
        updateDisplay();
    }

    document.getElementById('twoFactorEnabled').addEventListener('change', function() {
        updateDisplay();
    });

    document.querySelectorAll('input[name="auth_method"]').forEach(function(radio) {
        radio.addEventListener('change', updateDisplay);
    });

    function updateDisplay() {
        const enabled = document.getElementById('twoFactorEnabled').checked;
        const method = document.querySelector('input[name="auth_method"]:checked').value;

        // Show/hide auth method section
        document.getElementById('authMethodSection').style.display = enabled ? 'block' : 'none';

        // Show/hide email section
        const emailSection = document.getElementById('emailAddressSection');
        emailSection.style.display = enabled && (method === 'email') ? 'block' : 'none';

        // Show/hide phone section
        const phoneSection = document.getElementById('phoneNumberSection');
        phoneSection.style.display = enabled && (method === 'sms') ? 'block' : 'none';

        // Update label
        document.getElementById('toggleLabel').textContent = enabled ? '✓ Enabled' : '✗ Disabled';
    }

    document.getElementById('saveLoginAuthBtn').addEventListener('click', function() {
        const enabled = document.getElementById('twoFactorEnabled').checked;
        const method = document.querySelector('input[name="auth_method"]:checked').value;
        const email = document.getElementById('emailAddress').value.trim();
        const phone = document.getElementById('phoneNumber').value.trim();

        // Validation
        if (enabled) {
            if (method === 'sms' && !phone) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Phone Required',
                    text: 'Please enter your phone number for SMS authentication.'
                });
                return;
            }
            if (method === 'email' && !email) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Email Required',
                    text: 'Please enter your email address for Email authentication.'
                });
                return;
            }
        }

        // Save
        $.ajax({
            url: '<?php echo Yii::$app->urlManager->createUrl(['settings/loginauthsettings']); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                flag: 'save_auth_settings',
                two_factor_enabled: enabled ? 1 : 0,
                auth_method: method,
                phone_number: phone,
                email_address: email,
                '_csrf': '<?php echo $csrfToken; ?>'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save settings.'
                });
            }
        });
    });

    // Initialize display
    updateDisplay();
</script>

<style>
    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .form-control:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
    }
</style>

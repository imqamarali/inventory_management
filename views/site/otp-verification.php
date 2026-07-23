<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$csrfToken = Yii::$app->request->getCsrfToken();
$csrfParam = Yii::$app->request->csrfParam;

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Inventory System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .otp-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }

        .otp-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .otp-header .icon {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 15px;
            display: block;
        }

        .otp-header h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .otp-header p {
            color: #7f8c8d;
            font-size: 14px;
            line-height: 1.6;
        }

        .delivered-to {
            background: #ecf0f1;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
            margin: 15px 0;
            font-size: 13px;
            color: #2c3e50;
        }

        .otp-input-group {
            margin-bottom: 20px;
        }

        .otp-input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }

        .otp-input-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 18px;
            text-align: center;
            letter-spacing: 4px;
            transition: all 0.3s ease;
        }

        .otp-input-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.1);
        }

        .timer {
            text-align: center;
            margin: 15px 0;
            font-size: 13px;
            color: #7f8c8d;
        }

        .timer.expired {
            color: #e74c3c;
        }

        .verify-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }

        .verify-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .verify-btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
            transform: none;
        }

        .resend-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
        }

        .resend-section p {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .resend-btn {
            background: transparent;
            color: #667eea;
            border: 1px solid #667eea;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .resend-btn:hover:not(:disabled) {
            background: #f0f3ff;
        }

        .resend-btn:disabled {
            color: #bdc3c7;
            border-color: #bdc3c7;
            cursor: not-allowed;
        }

        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 13px;
            display: none;
        }

        .alert.error {
            background: #fadbd8;
            color: #922b21;
            border-left: 4px solid #e74c3c;
            display: block;
        }

        .alert.success {
            background: #d5f4e6;
            color: #0b5345;
            border-left: 4px solid #27ae60;
            display: block;
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="otp-container">
        <div class="otp-header">
            <i class="fa fa-shield icon"></i>
            <h2>Verify Your Identity</h2>
            <p>Enter the verification code sent to your <?php echo $method === 'sms' ? 'phone number' : 'email address'; ?></p>
        </div>

        <div class="delivered-to">
            <i class="fa <?php echo $method === 'sms' ? 'fa-mobile' : 'fa-envelope'; ?>"></i>
            <?php echo htmlspecialchars($delivered_to); ?>
        </div>

        <div id="alertBox" class="alert"></div>

        <form id="otpForm">
            <div class="otp-input-group">
                <label for="otpCode">
                    <i class="fa fa-key"></i>
                    Enter 6-Digit Code
                </label>
                <input type="text" id="otpCode" name="otp" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required autocomplete="off">
            </div>

            <div class="timer">
                <i class="fa fa-clock-o"></i>
                <span id="timerText">Expires in 10:00</span>
            </div>

            <button type="submit" class="verify-btn" id="verifyBtn">
                <i class="fa fa-check"></i>
                Verify OTP
            </button>

            <div class="resend-section">
                <p>Didn't receive the code?</p>
                <button type="button" class="resend-btn" id="resendBtn" disabled>
                    <i class="fa fa-redo"></i>
                    Resend Code
                </button>
            </div>
        </form>

        <div class="back-link">
            <a href="<?php echo Yii::$app->urlManager->createUrl(['site/login']); ?>">
                <i class="fa fa-arrow-left"></i>
                Back to Login
            </a>
        </div>
    </div>

    <script>
        const userId = <?php echo json_encode($user_id); ?>;
        const method = '<?php echo $method; ?>';
        const csrfToken = '<?php echo $csrfToken; ?>';
        let timeRemaining = 600; // 10 minutes
        let otpSentTime = Date.now();

        // Start countdown timer
        function startTimer() {
            const timerInterval = setInterval(() => {
                timeRemaining--;
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = timeRemaining % 60;
                document.getElementById('timerText').textContent =
                    `Expires in ${minutes}:${seconds.toString().padStart(2, '0')}`;

                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('timerText').classList.add('expired');
                    document.getElementById('verifyBtn').disabled = true;
                    showAlert('OTP has expired. Please request a new one.', 'error');
                }
            }, 1000);
        }

        // Enable resend after 30 seconds
        setTimeout(() => {
            document.getElementById('resendBtn').disabled = false;
        }, 30000);

        // Form submission
        document.getElementById('otpForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const otp = document.getElementById('otpCode').value.trim();

            if (otp.length !== 6 || !/^\d{6}$/.test(otp)) {
                showAlert('Please enter a valid 6-digit code', 'error');
                return;
            }

            document.getElementById('verifyBtn').disabled = true;

            $.ajax({
                url: '<?php echo Yii::$app->urlManager->createUrl(['settings/verify-otp']); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    user_id: userId,
                    otp: otp,
                    '_csrf': csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('OTP verified successfully! Redirecting...', 'success');
                        setTimeout(() => {
                            window.location.href = '<?php echo Yii::$app->urlManager->createUrl(['inventory/dashboard']); ?>';
                        }, 1500);
                    } else {
                        showAlert(response.message || 'Invalid OTP', 'error');
                        document.getElementById('verifyBtn').disabled = false;
                    }
                },
                error: function() {
                    showAlert('Error verifying OTP', 'error');
                    document.getElementById('verifyBtn').disabled = false;
                }
            });
        });

        // Resend OTP
        document.getElementById('resendBtn').addEventListener('click', function(e) {
            e.preventDefault();
            const btn = this;
            btn.disabled = true;

            $.ajax({
                url: '<?php echo Yii::$app->urlManager->createUrl(['settings/generate-otp']); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    user_id: userId,
                    method: method,
                    '_csrf': csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('New OTP sent to ' + response.delivered_to, 'success');
                        timeRemaining = 600;
                        document.getElementById('timerText').classList.remove('expired');
                        document.getElementById('otpCode').value = '';
                        document.getElementById('verifyBtn').disabled = false;
                        setTimeout(() => {
                            btn.disabled = false;
                        }, 30000);
                    } else {
                        showAlert(response.message || 'Failed to resend OTP', 'error');
                        btn.disabled = false;
                    }
                },
                error: function() {
                    showAlert('Error resending OTP', 'error');
                    btn.disabled = false;
                }
            });
        });

        function showAlert(message, type) {
            const alertBox = document.getElementById('alertBox');
            alertBox.textContent = message;
            alertBox.className = 'alert ' + type;
        }

        // Auto-focus next digit behavior
        document.getElementById('otpCode').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Start timer on page load
        startTimer();

        // Load jQuery if not already loaded
        if (typeof $ === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
            document.head.appendChild(script);
        }
    </script>
</body>
</html>

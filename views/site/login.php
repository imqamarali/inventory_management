<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inventory Management - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        background: linear-gradient(135deg, #2E7CB5 0%, #2C67B1 50%, #1B4E8C 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    /* Subtle Background Pattern */
    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255, 255, 255, .04) 35px, rgba(255, 255, 255, .04) 70px);
    }

    .container {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 360px;
        padding: 15px;
    }

    .login-card {
        background: #ffffff;
        border-radius: 4px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        width: 120%;
        border-top: 3px solid #2E7CB5;
    }

    .login-header {
        background: #ffffff;
        padding: 20px 25px 15px;
        text-align: center;
        border-bottom: 1px solid #e3e3e3;
    }

    .logo-container {
        display: inline-block;
        margin-bottom: 10px;
    }

    .logo-icon {
        width: 55px;
        height: 55px;
        background: #2E7CB5;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(46, 124, 181, 0.3);
        margin: 0 auto;
    }

    .system-title {
        font-size: 20px;
        font-weight: 700;
        color: #2E7CB5;
        margin: 0 0 4px 0;
        letter-spacing: -0.3px;
    }

    .system-subtitle {
        font-size: 11px;
        color: #777;
        font-weight: 400;
    }

    .login-body {
        padding: 20px 25px 18px;
    }

    .welcome-text {
        text-align: center;
        margin-bottom: 15px;
    }

    .welcome-text h3 {
        font-size: 16px;
        color: #555;
        font-weight: 600;
        margin-bottom: 3px;
    }

    .welcome-text p {
        font-size: 12px;
        color: #999;
        font-weight: 400;
    }

    .features-info {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 5px;
        margin-bottom: 15px;
        padding: 8px;
        background: #f7f9fa;
        border-radius: 3px;
        border: 1px solid #e3e3e3;
    }

    .feature-badge {
        text-align: center;
        padding: 5px 3px;
    }

    .feature-badge i {
        font-size: 15px;
        color: #2E7CB5;
        display: block;
        margin-bottom: 2px;
    }

    .feature-badge span {
        font-size: 9px;
        color: #777;
        font-weight: 500;
        display: block;
    }

    .role-badge {
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 3px;
        padding: 5px 3px;
    }

    .role-badge:hover {
        background: rgba(46, 124, 181, 0.1);
        transform: translateY(-2px);
    }

    .role-badge.active {
        background: rgba(46, 124, 181, 0.15);
        border: 1px solid #2E7CB5;
    }

    .role-badge.active i {
        color: #2C67B1;
    }

    .role-badge.active span {
        color: #2E7CB5;
        font-weight: 600;
    }

    .input-group {
        position: relative;
        margin-bottom: 15px;
    }

    .input-group label {
        display: block;
        margin-bottom: 5px;
        font-size: 13px;
        font-weight: 600;
        color: #555;
    }

    .input-group i.icon {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        color: #2E7CB5;
        font-size: 14px;
        margin-top: 10px;
    }

    .input-group .toggle-password {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #999;
        font-size: 14px;
        margin-top: 10px;
        transition: all 0.3s;
    }

    .input-group .toggle-password:hover {
        color: #2E7CB5;
    }

    .form-control {
        width: 100%;
        padding: 10px 40px;
        border: 1px solid #d5d5d5;
        border-radius: 3px;
        font-size: 13px;
        transition: all 0.3s;
        background: #ffffff;
        font-weight: 400;
        color: #555;
    }

    .form-control:focus {
        outline: none;
        border-color: #2E7CB5;
        box-shadow: 0 0 0 2px rgba(46, 124, 181, 0.1);
        background: #ffffff;
    }

    .form-control::placeholder {
        color: #bbb;
    }

    .btn-login {
        width: 100%;
        padding: 11px;
        background: #2E7CB5;
        color: white;
        border: none;
        font-size: 14px;
        font-weight: 600;
        border-radius: 3px;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 8px;
        box-shadow: 0 2px 5px rgba(46, 124, 181, 0.3);
        position: relative;
        letter-spacing: 0.3px;
    }

    .btn-login:hover {
        background: #2C67B1;
        box-shadow: 0 3px 8px rgba(46, 124, 181, 0.4);
    }

    .btn-login:active {
        transform: translateY(1px);
    }

    .error-message {
        background: #fee;
        border-left: 3px solid #dc3545;
        padding: 10px 12px;
        margin-bottom: 15px;
        border-radius: 3px;
        font-size: 12px;
        color: #dc3545;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .error-message i {
        color: #dc3545;
    }

    .footer-text {
        text-align: center;
        margin-top: 18px;
        font-size: 11px;
        color: #999;
        font-weight: 400;
    }

    .footer-text p {
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 480px) {
        .container {
            padding: 12px;
        }

        .login-body {
            padding: 18px 22px 16px;
        }

        .features-info {
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
        }

        .feature-badge {
            padding: 5px 2px;
        }

        .feature-badge i {
            font-size: 13px;
            margin-bottom: 2px;
        }

        .feature-badge span {
            font-size: 8px;
        }
    }

    /* Loading animation */
    .btn-login.loading {
        pointer-events: none;
        opacity: 0.8;
    }

    .btn-login.loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #ffffff;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Yii2 form field helper text removal */
    .help-block {
        display: none;
    }

    .has-error .form-control {
        border-color: #dc3545;
    }

    .has-error .form-control:focus {
        box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.1);
    }

    /* Lockout Timer Styles */
    .lockout-timer {
        background: #fff3cd;
        border-left: 3px solid #ff6b6b;
        padding: 15px 12px;
        margin-bottom: 15px;
        border-radius: 3px;
        font-size: 13px;
        color: #856404;
        font-weight: 500;
        text-align: center;
    }

    .timer-message {
        margin-bottom: 10px;
        font-weight: 600;
    }

    .countdown-display {
        font-size: 32px;
        font-weight: 700;
        color: #ff6b6b;
        font-family: 'Courier New', monospace;
        letter-spacing: 2px;
        margin: 10px 0;
    }

    .timer-text {
        font-size: 12px;
        color: #666;
        margin-top: 8px;
    }

    .locked-form .form-control,
    .locked-form .btn-login {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-card">
            <!-- Header Section -->
            <div class="login-header">
                <div class="logo-container">
                    <div class="logo-icon">
                        <i class="fas fa-chart-network"></i>
                    </div>
                </div>
                <h1 class="system-title">Inventory Management System</h1>
            </div>

            <!-- Body Section -->
            <div class="login-body">
                <div class="welcome-text">
                    <h3>Welcome Back</h3>
                    <p>Sign in to access your dashboard</p>
                </div>

                <!-- Role Selection -->
                <!-- class="features-info" -->
                <div>
                    <div class="feature-badge role-badge" data-role="student" data-username="superadmin"
                        data-password="superadmin321">
                        <i class="fas fa-user-shield"></i>
                        <span>Super Admin</span>
                    </div> 
                </div>

                <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= Yii::$app->session->getFlash('error') ?></span>
                </div>
                <?php endif; ?>

                <!-- Lockout Timer Display -->
                <?php if (isset($isLockedOut) && $isLockedOut && isset($lockoutEndTime)): ?>
                <div class="lockout-timer">
                    <div class="timer-message">
                        <i class="fas fa-lock"></i> Account Locked
                    </div>
                    <div class="countdown-display" id="countdownTimer">
                        05:00
                    </div>
                    <div class="timer-text">
                        Please wait before trying again
                    </div>
                </div>
                <?php endif; ?>

                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'action' => Url::to(['site/login']),
                    'options' => ['class' => isset($isLockedOut) && $isLockedOut ? 'login-form locked-form' : 'login-form', 'method' => 'post']
                ]); ?>

                <div class="input-group">
                    <label for="form-username">
                        Username
                    </label>
                    <i class="fas fa-user icon"></i>
                    <?= $form->field($model, 'username')->textInput([
                        'placeholder' => 'Enter your username',
                        'class' => 'form-control',
                        'id' => 'form-username',
                        'required' => true,
                        'autocomplete' => 'off'
                    ])->label(false) ?>
                </div>

                <div class="input-group">
                    <label for="form-password">
                        Password
                    </label>
                    <i class="fas fa-lock icon"></i>
                    <span class="toggle-password" onclick="togglePassword()" title="Show/Hide Password">
                        <i id="eye-icon" class="fas fa-eye"></i>
                    </span>
                    <?= $form->field($model, 'password')->passwordInput([
                        'placeholder' => 'Enter your password',
                        'class' => 'form-control',
                        'id' => 'form-password',
                        'required' => true,
                        'autocomplete' => 'off'
                    ])->label(false) ?>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> SIGN IN
                </button>

                <?php ActiveForm::end(); ?>

                <div class="footer-text">
                    <p>© <?= date('Y') ?> InventoryManagementSystem. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('form-password');
        const icon = document.getElementById('eye-icon');
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }

    // Role badge click handler
    document.addEventListener('DOMContentLoaded', function() {
        const roleBadges = document.querySelectorAll('.role-badge');
        const usernameInput = document.getElementById('form-username');
        const passwordInput = document.getElementById('form-password');

        roleBadges.forEach(badge => {
            badge.addEventListener('click', function() {
                // Remove active class from all badges
                roleBadges.forEach(b => b.classList.remove('active'));

                // Add active class to clicked badge
                this.classList.add('active');

                // Get credentials from data attributes
                const username = this.getAttribute('data-username');
                const password = this.getAttribute('data-password');

                // Populate form fields
                if (usernameInput) {
                    usernameInput.value = username;
                    // Trigger input event for Yii2 validation
                    usernameInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }

                if (passwordInput) {
                    passwordInput.value = password;
                    // Trigger input event for Yii2 validation
                    passwordInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }
            });
        });
    });

    // Loading state on form submit
    document.getElementById('login-form').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        btn.classList.add('loading');
        btn.innerHTML = 'Signing in...';
    });

    // Countdown Timer for Lockout
    document.addEventListener('DOMContentLoaded', function() {
        const timerElement = document.getElementById('countdownTimer');

        <?php if (isset($isLockedOut) && $isLockedOut && isset($lockoutEndTime)): ?>
        const lockoutEndTime = <?= $lockoutEndTime ?> * 1000; // Convert to milliseconds

        function updateCountdown() {
            const now = Date.now();
            const remainingMs = lockoutEndTime - now;

            if (remainingMs <= 0) {
                // Time's up - reload page
                window.location.reload();
                return;
            }

            // Calculate minutes and seconds
            const totalSeconds = Math.floor(remainingMs / 1000);
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;

            // Format as MM:SS with leading zeros
            const formattedTime =
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');

            // Update display
            if (timerElement) {
                timerElement.textContent = formattedTime;
            }
        }

        // Initial update
        updateCountdown();

        // Update every 100ms for smooth display
        setInterval(updateCountdown, 100);
        <?php endif; ?>
    });
    </script>
</body>

</html>
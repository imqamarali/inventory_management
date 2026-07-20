<?php
use yii\helpers\Html;

$this->title = 'Forgot Password';
?>

<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-md-5 mx-auto">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="text-primary"><strong>Password Reset</strong></h2>
                            <p class="text-muted">Enter your email to reset your password</p>
                        </div>

                        <?php if (Yii::$app->session->hasFlash('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?= Yii::$app->session->getFlash('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (Yii::$app->session->hasFlash('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?= Yii::$app->session->getFlash('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->getCsrfToken(); ?>">

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control form-control-lg" name="email" placeholder="Enter your email" required autofocus>
                                <small class="text-muted d-block mt-2">We'll send you a temporary password via email</small>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">Reset Password</button>

                            <div class="text-center">
                                <a href="<?= \yii\helpers\Url::to(['site/login']) ?>" class="text-decoration-none">Back to Login</a>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="alert alert-info text-center mb-0">
                            <small>A temporary password will be sent to your email. Please login and change it immediately.</small>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-white">© 2026 Inventory Management System. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</div>

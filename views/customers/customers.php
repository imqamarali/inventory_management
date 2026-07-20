<?php use yii\helpers\Html; use yii\helpers\Url; $this->title = 'Customers'; ?>
<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3"><i class="fa fa-users"></i> Customer Management</h1>
                <p class="text-muted">Manage customers, payments, sales history and outstanding balances</p>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($modules as $module): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 hover-shadow" style="cursor: pointer;" onclick="location.href='<?= Url::to(['customers/' . str_replace('customers/', '', $module['controller'])]) ?>'">
                    <div class="card-body text-center py-5">
                        <i class="fa <?= $module['icon'] ?> fa-3x text-primary mb-3"></i>
                        <h5 class="card-title"><?= Html::encode($module['name']) ?></h5>
                        <p class="text-muted small">Click to access</p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<style>
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
    transition: all 0.3s ease;
}
</style>
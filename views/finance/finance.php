<?php
/**
 * FINANCE MODULE MENU
 * ================================================================================
 * PURPOSE: Main entry point to Finance module with simplified structure
 *
 * MODULES:
 * 1. Finance Summary - Quick overview of Sales, Purchases, Expenses
 * 2. Sales Records - All sales revenue records
 * 3. Purchase Records - All purchase expenses
 * 4. Expense Records - Operating expenses (Rent, Electricity, Other)
 * 5. Chart of Accounts - Account master data
 * 6. Reports - P&L and Balance Sheet
 * ================================================================================
 */

$this->title = 'Finance Module';
?>

<div class="main-content">
    <div class="main-content-inner">

        <!-- Breadcrumbs -->
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=site/dashboard">Home</a>
                </li>
                <li class="active">Finance Module</li>
            </ul>
        </div>

        <div class="page-content">
            <!-- Title Section -->
            <div style="padding:20px;">
                <h2>Finance Management</h2>
                <p class="text-muted">Manage and track Sales Revenue, Purchase Expenses, and Operating Expenses</p>
            </div>

            <!-- Module Grid -->
            <div class="row" style="padding:20px;">
                <?php foreach ($modules as $module): ?>
                    <div class="col-sm-6 col-md-4" style="margin-bottom:20px;">
                        <a href="index.php?r=<?= $module['controller'] ?>" style="text-decoration:none;color:inherit;">
                            <div class="widget-box" style="height:150px;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;cursor:pointer;transition:all 0.3s ease;">
                                <div style="font-size:40px;color:#3366CC;margin-bottom:10px;">
                                    <i class="<?= $module['icon'] ?>"></i>
                                </div>
                                <h5 style="margin:10px 0;font-weight:bold;">
                                    <?= $module['name'] ?>
                                </h5>
                                <p style="font-size:12px;color:#999;margin:0;">
                                    Click to access
                                </p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Info Section -->
            <div style="padding:20px;">
                <div class="widget-box">
                    <div class="widget-header">
                        <h4 class="widget-title">
                            <i class="fa fa-info-circle"></i> Finance Module Guide
                        </h4>
                    </div>
                    <div class="widget-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h5><i class="fa fa-shopping-cart"></i> Sales Records</h5>
                                <p>View all sales revenue from both Sales Orders and POS Sales. Track payment status and monitor cash collection.</p>
                            </div>
                            <div class="col-sm-6">
                                <h5><i class="fa fa-shopping-bag"></i> Purchase Records</h5>
                                <p>View all purchase expenses (Cost of Goods Sold). Track purchase orders and supplier payables.</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <h5><i class="fa fa-credit-card"></i> Expense Records</h5>
                                <p>Manage operating expenses including Shop Rent, Electricity Bills, Salaries, and other expenses.</p>
                            </div>
                            <div class="col-sm-6">
                                <h5><i class="fa fa-dashboard"></i> Finance Summary</h5>
                                <p>Quick overview of total sales, purchases, and expenses. See your net profit/loss at a glance.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

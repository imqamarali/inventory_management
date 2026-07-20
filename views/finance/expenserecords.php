<?php
/**
 * EXPENSE RECORDS VIEW
 * ================================================================================
 * PURPOSE: Display all operating expenses (Rent, Electricity, Other)
 *
 * SHOWS:
 * - Expense Date
 * - Expense Type (Rent, Electricity, Other)
 * - Description
 * - Amount
 * - Status
 * - Running Total
 *
 * ALLOWS:
 * - Add new expense
 * - Edit existing expense
 * - Delete expense
 * ================================================================================
 */

use yii\helpers\Html;

$this->title = 'Expense Records';

if (!isset($expenses)) $expenses = [];
if (!isset($from_date)) $from_date = date('Y-m-01');
if (!isset($to_date)) $to_date = date('Y-m-d');
if (!isset($total)) $total = 0;
?>

<div class="main-content">
    <div class="main-content-inner">

        <!-- Breadcrumbs -->
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="index.php?r=finance/finance">Finance</a>
                </li>
                <li class="active">Expense Records</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="showExpenseModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Expense
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Filter Form -->
        <div style="padding-top:10px;padding-left:13px;padding-bottom:15px;">
            <form id="search_form" onsubmit="return false;">
                <select name="expense_type" id="expense_type" class="new-input" style="width:15%;">
                    <option value="">All Types</option>
                    <option value="Shop Rent">Shop Rent</option>
                    <option value="Electricity Bill">Electricity Bill</option>
                    <option value="Salary">Salary</option>
                    <option value="Other">Other Expense</option>
                </select>

                <input type="date" name="from_date" id="from_date" class="new-input" style="width:12%;" value="<?= $from_date ?>">
                <input type="date" name="to_date" id="to_date" class="new-input" style="width:12%;" value="<?= $to_date ?>">

                <input type="text" name="per_page" id="per_page" value="20" class="new-input" style="width:8%;" placeholder="Records">

                <button type="button" class="btn btn-primary" onclick="searchExpenseRecords()" style="height:30px;padding:5px 15px;">
                    <i class="fa fa-search"></i> Search
                </button>
            </form>
        </div>

        <!-- Records Table -->
        <div class="widget-main">
            <div style="padding:10px;font-weight:bold;">
                Total Expenses: <span class="text-warning"><?= number_format($total, 2) ?></span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:12%;">Date</th>
                            <th style="width:15%;">Expense Type</th>
                            <th style="width:25%;">Description</th>
                            <th style="width:13%;">Amount</th>
                            <th style="width:15%;">Running Total</th>
                            <th style="width:15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="records_body">
                        <?php
                        $running_total = 0;
                        foreach ($expenses as $key => $item) {
                            $running_total += $item['amount'];
                        ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= Html::encode($item['date']) ?></td>
                                <td>
                                    <span class="label label-info" style="font-size:11px;">
                                        <?= Html::encode($item['expense_type']) ?>
                                    </span>
                                </td>
                                <td><?= Html::encode($item['description']) ?></td>
                                <td class="text-right"><?= number_format($item['amount'], 2) ?></td>
                                <td class="text-right"><?= number_format($running_total, 2) ?></td>
                                <td>
                                    <button onclick='showExpenseModal(<?= json_encode($item) ?>)' class="btn btn-xs btn-info" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button onclick="deleteExpense(<?= $item['id'] ?>)" class="btn btn-xs btn-danger" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($expenses)) { ?>
                <div class="text-center" style="padding:20px;">
                    <p class="text-muted">No expense records found for the selected period.</p>
                </div>
            <?php } ?>
        </div>

    </div><!-- main-content-inner -->
</div><!-- main-content -->

<!-- Expense Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Expense</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="expense_form">
                    <input type="hidden" id="expense_id" name="id" value="">

                    <div class="form-group">
                        <label>Expense Date</label>
                        <input type="date" id="expense_date" name="expense_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="form-group">
                        <label>Expense Type</label>
                        <select id="expense_type_select" name="expense_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="Shop Rent">Shop Rent</option>
                            <option value="Electricity Bill">Electricity Bill</option>
                            <option value="Salary">Salary</option>
                            <option value="Other">Other Expense</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="expense_description" name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" id="expense_amount" name="amount" class="form-control" step="0.01" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveExpense()">Save Expense</button>
            </div>
        </div>
    </div>
</div>

<script>
function showExpenseModal(data = null) {
    if (data) {
        $('#expense_id').val(data.id);
        $('#expense_date').val(data.date);
        $('#expense_type_select').val(data.expense_type);
        $('#expense_description').val(data.description);
        $('#expense_amount').val(data.amount);
    } else {
        $('#expense_form')[0].reset();
        $('#expense_id').val('');
        $('#expense_date').val('<?= date('Y-m-d') ?>');
    }
    $('#expenseModal').modal('show');
}

function saveExpense() {
    const data = {
        id: $('#expense_id').val(),
        expense_date: $('#expense_date').val(),
        expense_type: $('#expense_type_select').val(),
        description: $('#expense_description').val(),
        amount: $('#expense_amount').val()
    };

    $.post('index.php?r=finance/expenserecords', data, function(response) {
        if (response.success) {
            alert(response.message);
            $('#expenseModal').modal('hide');
            searchExpenseRecords();
        } else {
            alert('Error: ' + response.message);
        }
    }, 'json');
}

function deleteExpense(id) {
    if (confirm('Are you sure you want to delete this expense?')) {
        $.post('index.php?r=finance/expenserecords', {
            id: id,
            delete: 1
        }, function(response) {
            if (response.success) {
                alert(response.message);
                searchExpenseRecords();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json');
    }
}

function searchExpenseRecords() {
    const from_date = document.getElementById('from_date').value;
    const to_date = document.getElementById('to_date').value;
    const expense_type = document.getElementById('expense_type').value;
    const per_page = document.getElementById('per_page').value;

    $.post('index.php?r=finance/expenserecords', {
        flag: 'search',
        from_date: from_date,
        to_date: to_date,
        expense_type: expense_type,
        per_page: per_page
    }, function(response) {
        if (response.success) {
            let html = '';
            let running = 0;
            response.records.forEach((item, idx) => {
                running += parseFloat(item.amount);
                html += `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${item.date}</td>
                        <td><span class="label label-info" style="font-size:11px;">${item.expense_type}</span></td>
                        <td>${item.description}</td>
                        <td class="text-right">${parseFloat(item.amount).toFixed(2)}</td>
                        <td class="text-right">${running.toFixed(2)}</td>
                        <td>
                            <button onclick='showExpenseModal(${JSON.stringify(item)})' class="btn btn-xs btn-info" title="Edit">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button onclick="deleteExpense(${item.id})" class="btn btn-xs btn-danger" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            $('#records_body').html(html);
        } else {
            alert('Error: ' + response.message);
        }
    }, 'json');
}
</script>

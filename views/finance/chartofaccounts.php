<?php
/**
 * CHART OF ACCOUNTS VIEW
 * ================================================================================
 * PURPOSE: Manage Chart of Accounts for Finance module
 *
 * DISPLAYS:
 * - Account Code, Name, Type, Balance
 * - Filter by account type
 * - Add/Edit/Delete accounts
 * ================================================================================
 */

use yii\helpers\Html;

$this->title = 'Chart of Accounts';

if (!isset($accounts)) $accounts = [];
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
                <li class="active">Chart of Accounts</li>
                <li style="float:right;">
                    <div class="nav-search" id="nav-search">
                        <div class="exam-quick-actions-group">
                            <a class="btn btn-sm btn-white btn-primary" style="font-size:12px;cursor:pointer;" onclick="showAccountModal()">
                                <i class="ace-icon fa fa-plus"></i>
                                Add Account
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Filter Form -->
        <div style="padding-top:10px;padding-left:13px;padding-bottom:15px;">
            <form id="search_form" onsubmit="return false;">
                <input type="text" name="keyword" id="keyword" class="new-input" style="width:20%;" placeholder="Search Account...">

                <select name="account_type" id="account_type" class="new-input" style="width:18%;">
                    <option value="">All Types</option>
                    <option value="Asset">Asset</option>
                    <option value="Liability">Liability</option>
                    <option value="Equity">Equity</option>
                    <option value="Income">Income</option>
                    <option value="Expense">Expense</option>
                </select>

                <input type="text" name="per_page" id="per_page" value="20" class="new-input" style="width:8%;" placeholder="Records">

                <button type="button" onclick="searchAccounts()" style="height:30px;padding:5px 15px;">
                    <i class="fa fa-search"></i> Search
                </button>
            </form>
        </div>

        <!-- Accounts Table -->
        <div class="widget-main">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:12%;">Code</th>
                            <th style="width:25%;">Account Name</th>
                            <th style="width:15%;">Type</th>
                            <th style="width:15%;">Balance</th>
                            <th style="width:12%;">Status</th>
                            <th style="width:16%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="accounts_body">
                        <?php foreach ($accounts as $key => $account) { ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><strong><?= Html::encode($account['account_code']) ?></strong></td>
                                <td><?= Html::encode($account['account_name']) ?></td>
                                <td>
                                    <span class="label label-info" style="font-size:11px;">
                                        <?= Html::encode($account['account_type']) ?>
                                    </span>
                                </td>
                                <td class="text-right">
                                    <strong><?= number_format($account['current_balance'], 2) ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $status_class = $account['is_active'] ? 'success' : 'danger';
                                    $status_text = $account['is_active'] ? 'Active' : 'Inactive';
                                    ?>
                                    <span class="label label-<?= $status_class ?>" style="font-size:11px;">
                                        <?= $status_text ?>
                                    </span>
                                </td>
                                <td>
                                    <button onclick='showAccountModal(<?= json_encode($account) ?>)'   title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button onclick="deleteAccount(<?= $account['id'] ?>)"   title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($accounts)) { ?>
                <div class="text-center" style="padding:30px;">
                    <p class="text-muted">No accounts found. Click "Add Account" to create one.</p>
                </div>
            <?php } ?>
        </div>

    </div><!-- main-content-inner -->
</div><!-- main-content -->

<!-- Account Modal -->
<div class="modal fade" id="accountModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Account</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="account_form">
                    <input type="hidden" id="account_id" name="id" value="">

                    <div class="form-group">
                        <label>Account Code</label>
                        <input type="text" id="account_code" name="account_code" class="form-control" required>
                        <small class="text-muted">e.g., SALES-001, RENT-001</small>
                    </div>

                    <div class="form-group">
                        <label>Account Name</label>
                        <input type="text" id="account_name" name="account_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Account Type</label>
                        <select id="account_type_select" name="account_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="Asset">Asset</option>
                            <option value="Liability">Liability</option>
                            <option value="Equity">Equity</option>
                            <option value="Income">Income</option>
                            <option value="Expense">Expense</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Opening Balance</label>
                        <input type="number" id="opening_balance" name="opening_balance" class="form-control" step="0.01" value="0">
                    </div>

                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea id="remarks" name="remarks" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveAccount()">Save Account</button>
            </div>
        </div>
    </div>
</div>

<script>
function showAccountModal(data = null) {
    if (data) {
        $('#account_id').val(data.id);
        $('#account_code').val(data.account_code);
        $('#account_name').val(data.account_name);
        $('#account_type_select').val(data.account_type);
        $('#opening_balance').val(data.opening_balance);
        $('#remarks').val(data.remarks);
    } else {
        $('#account_form')[0].reset();
        $('#account_id').val('');
    }
    $('#accountModal').modal('show');
}

function saveAccount() {
    const data = {
        id: $('#account_id').val(),
        account_code: $('#account_code').val(),
        account_name: $('#account_name').val(),
        account_type: $('#account_type_select').val(),
        opening_balance: $('#opening_balance').val(),
        remarks: $('#remarks').val()
    };

    $.post('index.php?r=finance/chartofaccounts', data, function(response) {
        if (response.success) {
            alert(response.message);
            $('#accountModal').modal('hide');
            searchAccounts();
        } else {
            alert('Error: ' + response.message);
        }
    }, 'json');
}

function deleteAccount(id) {
    if (confirm('Are you sure you want to delete this account?')) {
        $.post('index.php?r=finance/chartofaccounts', {
            id: id,
            delete: 1
        }, function(response) {
            if (response.success) {
                alert(response.message);
                searchAccounts();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json');
    }
}
searchAccounts() ;
function searchAccounts() {
    const keyword = document.getElementById('keyword').value;
    const account_type = document.getElementById('account_type').value;
    const per_page = document.getElementById('per_page').value;

    $.post('index.php?r=finance/chartofaccounts', {
        flag: 'search',
        keyword: keyword,
        account_type: account_type,
        per_page: per_page
    }, function(response) {
        if (response.success) {
            let html = '';
            response.accounts.forEach((item, idx) => {
                const typeClass = item.account_type === 'Asset' ? 'info' :
                                  item.account_type === 'Liability' ? 'danger' :
                                  item.account_type === 'Equity' ? 'primary' :
                                  item.account_type === 'Income' ? 'success' : 'warning';
                const statusClass = item.is_active ? 'success' : 'danger';
                const statusText = item.is_active ? 'Active' : 'Inactive';
                html += `
                    <tr>
                        <td>${idx + 1}</td>
                        <td><strong>${item.account_code}</strong></td>
                        <td>${item.account_name}</td>
                        <td><span class="label label-${typeClass}" style="font-size:11px;">${item.account_type}</span></td>
                        <td class="text-right"><strong>${parseFloat(item.current_balance).toFixed(2)}</strong></td>
                        <td><span class="label label-${statusClass}" style="font-size:11px;">${statusText}</span></td>
                        <td>
                            <button onclick='showAccountModal(${JSON.stringify(item)})'   title="Edit">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button onclick="deleteAccount(${item.id})"  title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            $('#accounts_body').html(html);
        } else {
            alert('Error: ' + response.message);
        }
    }, 'json');
}
</script>

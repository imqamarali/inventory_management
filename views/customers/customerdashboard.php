<?php

use yii\helpers\Html; ?>
<div class="page-content">
    <div class="dashboard-header">
        <div>
            <h3><i class="fa fa-users"></i> Customers Dashboard <small>Customer Overview</small></h3>
        </div>
        <div><button id="refreshDashboard" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i> Refresh</button></div>
    </div>
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-header"><span>Total Customers</span>
                <div class="stat-icon"><i class="fa fa-users"></i></div>
            </div>
            <div class="stat-value" id="total-customers">0</div>
            <div class="stat-subtitle">All Customers</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header"><span>Active</span>
                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="stat-value" id="active-customers">0</div>
            <div class="stat-subtitle">Active Accounts</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-header"><span>Retail</span>
                <div class="stat-icon"><i class="fa fa-shopping-cart"></i></div>
            </div>
            <div class="stat-value" id="retail-customers">0</div>
            <div class="stat-subtitle">Retail Customers</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-header"><span>Company</span>
                <div class="stat-icon"><i class="fa fa-building"></i></div>
            </div>
            <div class="stat-value" id="company-customers">0</div>
            <div class="stat-subtitle">Company Accounts</div>
        </div>
        <div class="stat-card teal">
            <div class="stat-header"><span>Receivable</span>
                <div class="stat-icon"><i class="fa fa-money"></i></div>
            </div>
            <div class="stat-value" id="total-receivable">$0</div>
            <div class="stat-subtitle">Outstanding Balance</div>
        </div>
        <div class="stat-card red">
            <div class="stat-header"><span>Credit Limit</span>
                <div class="stat-icon"><i class="fa fa-credit-card"></i></div>
            </div>
            <div class="stat-value" id="credit-limit">$0</div>
            <div class="stat-subtitle">Total Credit Limit</div>
        </div>
    </div>
    <div class="row" style="margin-top:15px;">
        <div class="col-md-12">
            <div class="dashboard-box">
                <h4><i class="fa fa-clock-o"></i> Recent Customers</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Email</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody id="recent-customers">
                            <tr>
                                <td colspan="5" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function htmlEscape(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
    $(document).ready(function() {
        loadDashboard();
        $('#refreshDashboard').click(loadDashboard);
    });

    function loadDashboard() {
        $('.stat-value').addClass('loading');
        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl("customers/customerdashboard") ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                flag: 'load_dashboard'
            },
            timeout: 5000,
            success: function(r) {
                if (r.success) {
                    $('#total-customers').text(r.stats.total_customers);
                    $('#active-customers').text(r.stats.active_customers);
                    $('#retail-customers').text(r.stats.retail_customers);
                    $('#company-customers').text(r.stats.company_customers);
                    $('#total-receivable').text('$' + parseFloat(r.stats.total_receivable).toFixed(2));
                    $('#credit-limit').text('$' + parseFloat(r.stats.total_credit_limit).toFixed(2));
                    let html = '';
                    (r.recentCustomers || []).forEach(c => {
                        html += '<tr><td>' + htmlEscape(c.customer_code) + '</td><td>' + htmlEscape(c.company_name || c.first_name + ' ' + c.last_name) + '</td><td>' + htmlEscape(c.customer_type) + '</td><td>' + htmlEscape(c.email || '') + '</td><td>' + c.created_at.substr(0, 10) + '</td></tr>';
                    });
                    $('#recent-customers').html(html || '<tr><td colspan="5" class="text-center">No customers yet</td></tr>');
                } else alert(r.message);
            },
            error: function(x, s, e) {
                alert(s === 'timeout' ? 'Request timed out' : 'Network error');
            },
            complete: function() {
                $('.stat-value').removeClass('loading');
            }
        });
    }
</script>
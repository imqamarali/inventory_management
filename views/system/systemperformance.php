<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'System Performance & Testing';
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs" id="breadcrumbs">
            <ul class="breadcrumb" style="width:100%;">
                <li><i class="ace-icon fa fa-home home-icon"></i> <a href="index.php?r=inventory/dashboard">Home</a></li>
                <li class="active">System Performance Testing</li>
            </ul>
        </div>

        <div class="widget-main" style="padding: 20px;">
            <h3>System Performance & Health Check</h3>
            <hr />

            <!-- Test Controls -->
            <div style="margin-bottom: 20px; background: #f5f5f5; padding: 15px; border-radius: 4px;">
                <h4>Select Tests to Run</h4>
                <div style="margin-bottom: 15px;">
                    <select id="test-type" class="form-control" style="width: 300px; margin-bottom: 10px;">
                        <option value="all">All Tests</option>
                        <?php foreach ($availableTests as $key => $name): ?>
                            <option value="<?= $key ?>"><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-primary" onclick="runTests()">
                    <i class="fa fa-play"></i> Run Tests
                </button>
                <button class="btn btn-default" onclick="clearResults()">
                    <i class="fa fa-times"></i> Clear Results
                </button>
                <span id="test-status" style="margin-left: 20px; font-weight: bold;"></span>
            </div>

            <!-- Results Area -->
            <div id="results-container" style="display: none;">
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-3">
                        <div style="background: #e8f5e9; padding: 15px; border-radius: 4px; text-align: center;">
                            <h3 id="summary-passed" style="color: #4caf50; margin: 0;">0</h3>
                            <p style="margin: 5px 0 0 0;">Tests Passed</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="background: #ffebee; padding: 15px; border-radius: 4px; text-align: center;">
                            <h3 id="summary-failed" style="color: #f44336; margin: 0;">0</h3>
                            <p style="margin: 5px 0 0 0;">Tests Failed</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="background: #fff3e0; padding: 15px; border-radius: 4px; text-align: center;">
                            <h3 id="summary-warnings" style="color: #ff9800; margin: 0;">0</h3>
                            <p style="margin: 5px 0 0 0;">Warnings</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="background: #e3f2fd; padding: 15px; border-radius: 4px; text-align: center;">
                            <h3 id="summary-percentage" style="color: #2196f3; margin: 0;">0%</h3>
                            <p style="margin: 5px 0 0 0;">Success Rate</p>
                        </div>
                    </div>
                </div>

                <div id="test-results"></div>
            </div>

            <!-- Help Section -->
            <div style="margin-top: 30px; background: #f0f4f8; padding: 15px; border-radius: 4px; border-left: 4px solid #2196f3;">
                <h4>Test Information</h4>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li><strong>Database Connectivity:</strong> Tests connection to MySQL database</li>
                    <li><strong>Table Structure:</strong> Verifies all tables and their column counts</li>
                    <li><strong>Sample Data Operations:</strong> Tests CRUD operations and performance</li>
                    <li><strong>Screen Rendering:</strong> Checks if all main screens render properly</li>
                    <li><strong>Controller Actions:</strong> Verifies all controller actions exist</li>
                    <li><strong>Data Integrity:</strong> Checks for data consistency and orphaned records</li>
                    <li><strong>Performance Metrics:</strong> Measures query and database performance</li>
                </ul>
            </div>
        </div>

        <div id="toastBox"></div>
    </div>
</div>

<style>
    .test-result-group {
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }
    .test-result-header {
        background: #f5f5f5;
        padding: 12px 15px;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
        cursor: pointer;
        user-select: none;
    }
    .test-result-header:hover {
        background: #efefef;
    }
    .test-result-header.success {
        background: #e8f5e9;
        border-left: 4px solid #4caf50;
    }
    .test-result-header.fail {
        background: #ffebee;
        border-left: 4px solid #f44336;
    }
    .test-result-body {
        padding: 0;
        max-height: 500px;
        overflow-y: auto;
    }
    .test-result-body.collapsed {
        display: none;
    }
    .test-item {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .test-item:last-child {
        border-bottom: none;
    }
    .test-item.pass {
        background: #f1f8e9;
    }
    .test-item.fail {
        background: #ffebee;
    }
    .test-item.warning {
        background: #fff3e0;
    }
    .status-badge {
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: bold;
        color: white;
    }
    .status-pass {
        background-color: #4caf50;
    }
    .status-fail {
        background-color: #f44336;
    }
    .status-warning {
        background-color: #ff9800;
    }
    .test-icon {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
</style>

<script>
let testResults = null;

function runTests() {
    const testType = document.getElementById('test-type').value;
    document.getElementById('test-status').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Running tests...';
    document.getElementById('results-container').style.display = 'none';

    const formData = new FormData();
    formData.append('action', 'run');
    formData.append('test_type', testType);

    fetch('index.php?r=system/systemperformance', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        testResults = data;
        displayResults(data);
        document.getElementById('test-status').innerHTML = '<span style="color: green;"><i class="fa fa-check"></i> Tests completed</span>';
    })
    .catch(err => {
        console.error('Error:', err);
        document.getElementById('test-status').innerHTML = '<span style="color: red;"><i class="fa fa-times"></i> Error running tests</span>';
        showToast('Error running tests', 'error');
    });
}

function displayResults(data) {
    if (!data.success) {
        showToast(data.message, 'error');
        return;
    }

    document.getElementById('results-container').style.display = 'block';

    // Update summary
    if (data.summary) {
        document.getElementById('summary-passed').textContent = data.summary.passed;
        document.getElementById('summary-failed').textContent = data.summary.failed;
        document.getElementById('summary-warnings').textContent = data.summary.warnings;
        document.getElementById('summary-percentage').textContent = data.summary.percentage + '%';
    }

    // Render test results
    let html = '';
    if (data.tests) {
        Object.keys(data.tests).forEach(testKey => {
            const test = data.tests[testKey];
            html += renderTestResult(testKey, test);
        });
    } else if (data.name) {
        html = renderTestResult(data.name, data);
    }

    document.getElementById('test-results').innerHTML = html;
    attachTestResultHandlers();
}

function renderTestResult(name, test) {
    const status = test.success ? 'success' : 'fail';
    const icon = test.success ? '<i class="fa fa-check-circle" style="color: #4caf50;"></i>' : '<i class="fa fa-times-circle" style="color: #f44336;"></i>';

    let testItems = '';
    if (test.tests && Array.isArray(test.tests)) {
        test.tests.forEach(item => {
            const itemStatus = item.status || 'pass';
            const statusBadgeClass = 'status-' + itemStatus;
            const itemClass = 'test-item ' + itemStatus;
            testItems += `
                <div class="${itemClass}">
                    <span>
                        <span class="test-icon">
                            ${itemStatus === 'pass' ? '<i class="fa fa-check" style="color: #4caf50;"></i>' : (itemStatus === 'fail' ? '<i class="fa fa-times" style="color: #f44336;"></i>' : '<i class="fa fa-exclamation" style="color: #ff9800;"></i>')}
                        </span>
                        <strong>${item.name}</strong>
                    </span>
                    <div style="text-align: right;">
                        <span class="status-badge ${statusBadgeClass}">${item.status.toUpperCase()}</span>
                        <div style="font-size: 12px; color: #666; margin-top: 3px;">${item.result}</div>
                    </div>
                </div>
            `;
        });
    }

    let additionalInfo = '';
    if (test.table_count) {
        additionalInfo = ` | Tables: ${test.table_count}`;
    }
    if (test.controller_count) {
        additionalInfo = ` | Controllers: ${test.controller_count}`;
    }

    return `
        <div class="test-result-group">
            <div class="test-result-header ${status}" onclick="toggleTestResults(this)">
                ${icon}
                <span style="margin-left: 10px;">${test.name || name}${additionalInfo}</span>
                <span style="float: right; font-size: 12px; color: #666;">
                    ${test.error ? `ERROR: ${test.error}` : ''}
                </span>
            </div>
            <div class="test-result-body" style="display: block;">
                ${testItems}
            </div>
        </div>
    `;
}

function toggleTestResults(header) {
    const body = header.nextElementSibling;
    body.classList.toggle('collapsed');
}

function attachTestResultHandlers() {
    // Results already displayed with inline handlers
}

function clearResults() {
    document.getElementById('results-container').style.display = 'none';
    document.getElementById('test-status').innerHTML = '';
    document.getElementById('test-results').innerHTML = '';
}

function showToast(message, type) {
    const toastBox = document.getElementById('toastBox');
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.innerHTML = `
        <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span style="margin-left: 10px;">${message}</span>
    `;
    toastBox.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}
</script>

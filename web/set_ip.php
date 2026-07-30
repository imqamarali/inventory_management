<?php

/**
 * IP Management Interface
 * 
 * This file provides a secure interface to manage IP addresses for .htaccess
 * A unique key is required to access this interface.
 */

// Static unique key - CHANGE THIS TO YOUR DESIRED KEY
define('ACCESS_KEY', 'AdminIP2024!SecureKey#987');

// Start session for authentication
session_start();

// Check if user is authenticated
$isAuthenticated = isset($_SESSION['ip_manager_authenticated']) && $_SESSION['ip_manager_authenticated'] === true;

// Handle authentication
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'authenticate') {
            $enteredKey = isset($_POST['key']) ? $_POST['key'] : '';
            if ($enteredKey === ACCESS_KEY) {
                $_SESSION['ip_manager_authenticated'] = true;
                echo json_encode(['success' => true, 'message' => 'Authentication successful']);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid key']);
                exit;
            }
        } elseif ($_POST['action'] === 'update_ips' && $isAuthenticated) {
            // Update IP addresses
            $ipsJson = isset($_POST['ips']) ? $_POST['ips'] : '[]';
            $ips = json_decode($ipsJson, true);
            if (!is_array($ips)) {
                $ips = [];
            }
            $accessMode = isset($_POST['accessMode']) ? $_POST['accessMode'] : 'whitelist';
            $denyAllByDefault = isset($_POST['denyAllByDefault']) ? $_POST['denyAllByDefault'] : 'true';

            // Filter and validate IPs
            $validIPs = [];
            foreach ($ips as $ip) {
                $ip = trim($ip);
                if (!empty($ip) && filter_var($ip, FILTER_VALIDATE_IP)) {
                    $validIPs[] = $ip;
                }
            }

            // Read current settings
            $settingsFile = dirname(__DIR__) . '/settings.php';
            $settingsContent = file_get_contents($settingsFile);

            // Update settings
            $newSettings = "<?php\n\n";
            $newSettings .= "/**\n";
            $newSettings .= " * IP Access Control Settings\n";
            $newSettings .= " * \n";
            $newSettings .= " * This file contains the IP addresses that are allowed to access the application.\n";
            $newSettings .= " * Update the IP addresses in the \$allowedIPs array below.\n";
            $newSettings .= " * \n";
            $newSettings .= " * After updating this file, run generate_htaccess.php to regenerate the .htaccess file.\n";
            $newSettings .= " */\n\n";
            $newSettings .= "return [\n";
            $newSettings .= "    // List of allowed IP addresses\n";
            $newSettings .= "    'allowedIPs' => [\n";

            foreach ($validIPs as $ip) {
                $newSettings .= "        '" . addslashes($ip) . "',\n";
            }

            $newSettings .= "        // Add more IP addresses here, one per line\n";
            $newSettings .= "        // Example: '192.168.1.100',\n";
            $newSettings .= "    ],\n";
            $newSettings .= "    \n";
            $newSettings .= "    // Access control mode: 'whitelist' or 'allow_all'\n";
            $newSettings .= "    // 'whitelist' - Only allow specified IPs\n";
            $newSettings .= "    // 'allow_all' - Allow all IPs (useful for development)\n";
            $newSettings .= "    'accessMode' => '" . addslashes($accessMode) . "',\n";
            $newSettings .= "    \n";
            $newSettings .= "    // Whether to deny all by default (only used when accessMode is 'whitelist')\n";
            $newSettings .= "    'denyAllByDefault' => " . ($denyAllByDefault === 'true' ? 'true' : 'false') . ",\n";
            $newSettings .= "];\n";

            // Write settings file
            if (file_put_contents($settingsFile, $newSettings) === false) {
                echo json_encode(['success' => false, 'message' => 'Failed to update settings.php']);
                exit;
            }

            // Regenerate .htaccess
            $htaccessPath = __DIR__ . '/.htaccess';
            $htaccessContent = "<Files \"*\" >\n\n";

            if ($accessMode === 'allow_all') {
                $htaccessContent .= "Order Deny, Allow\n";
                $htaccessContent .= "Allow from all\n\n";
            } else {
                if ($denyAllByDefault === 'true') {
                    $htaccessContent .= "Order Deny, Allow\n";
                    $htaccessContent .= "Deny from all\n\n";
                } else {
                    $htaccessContent .= "Order Deny, Allow\n";
                    $htaccessContent .= "Allow from all\n\n";
                }

                foreach ($validIPs as $ip) {
                    $htaccessContent .= "Allow from " . $ip . "\n";
                }
            }

            $htaccessContent .= "\n</Files >\n";

            if (file_put_contents($htaccessPath, $htaccessContent) === false) {
                echo json_encode(['success' => false, 'message' => 'Failed to update .htaccess file']);
                exit;
            }

            echo json_encode([
                'success' => true,
                'message' => 'IP addresses updated successfully!',
                'ipCount' => count($validIPs)
            ]);
            exit;
        } elseif ($_POST['action'] === 'logout' && $isAuthenticated) {
            unset($_SESSION['ip_manager_authenticated']);
            echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
            exit;
        }
    }
}

// Load current settings
$settingsFile = dirname(__DIR__) . '/settings.php';
$settings = [];
$currentIPs = [];
$currentAccessMode = 'whitelist';
$currentDenyAll = true;

if (file_exists($settingsFile)) {
    $settings = require $settingsFile;
    $currentIPs = isset($settings['allowedIPs']) ? $settings['allowedIPs'] : [];
    $currentAccessMode = isset($settings['accessMode']) ? $settings['accessMode'] : 'whitelist';
    $currentDenyAll = isset($settings['denyAllByDefault']) ? $settings['denyAllByDefault'] : true;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP Access Management</title>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: none;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }

        .ip-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ip-item:hover {
            background: #e9ecef;
        }

        .btn-add-ip {
            margin-top: 15px;
        }

        .access-mode-select {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-shield-alt"></i> IP Access Management</h3>
            </div>
            <div class="card-body">
                <div id="ip-management-container" style="display: none;">
                    <div class="access-mode-select">
                        <label class="form-label"><strong>Access Control Mode:</strong></label>
                        <select id="accessMode" class="form-select">
                            <option value="whitelist" <?= $currentAccessMode === 'whitelist' ? 'selected' : '' ?>>
                                Whitelist (Only specified IPs)</option>
                            <option value="allow_all" <?= $currentAccessMode === 'allow_all' ? 'selected' : '' ?>>Allow
                                All IPs</option>
                        </select>
                    </div>

                    <div id="whitelist-options"
                        style="display: <?= $currentAccessMode === 'whitelist' ? 'block' : 'none' ?>;">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="denyAllByDefault"
                                <?= $currentDenyAll ? 'checked' : '' ?>>
                            <label class="form-check-label" for="denyAllByDefault">
                                Deny all by default (only allow specified IPs)
                            </label>
                        </div>
                    </div>

                    <h5 class="mb-3">Allowed IP Addresses:</h5>
                    <div id="ip-list">
                        <!-- IP addresses will be dynamically added here -->
                    </div>

                    <button type="button" class="btn btn-primary btn-add-ip" onclick="addIPField()">
                        <i class="fas fa-plus"></i> Add IP Address
                    </button>

                    <div class="mt-4">
                        <button type="button" class="btn btn-success btn-lg w-100" onclick="saveIPs()">
                            <i class="fas fa-save"></i> Save & Update .htaccess
                        </button>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-secondary w-100" onclick="logout()">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        const currentIPs = <?= json_encode($currentIPs) ?>;
        const isAuthenticated = <?= $isAuthenticated ? 'true' : 'false' ?>;

        // Show authentication modal on page load if not authenticated
        if (!isAuthenticated) {
            Swal.fire({
                title: 'Access Required',
                text: 'Please enter the unique access key:',
                input: 'password',
                inputPlaceholder: 'Enter access key',
                showCancelButton: true,
                confirmButtonText: 'Authenticate',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Please enter the access key';
                    }
                },
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    authenticate(result.value);
                } else {
                    window.location.href = '/';
                }
            });
        } else {
            // Load IP management interface
            loadIPInterface();
        }

        function authenticate(key) {
            const formData = new FormData();
            formData.append('action', 'authenticate');
            formData.append('key', key);

            fetch('set_ip.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Authenticated!',
                            text: 'Access granted',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Authentication Failed',
                            text: data.message || 'Invalid key',
                            confirmButtonText: 'Try Again'
                        }).then(() => {
                            location.reload();
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred during authentication'
                    });
                });
        }

        function loadIPInterface() {
            document.getElementById('ip-management-container').style.display = 'block';

            // Load existing IPs
            currentIPs.forEach(ip => {
                addIPField(ip);
            });

            // If no IPs, add one empty field
            if (currentIPs.length === 0) {
                addIPField();
            }

            // Toggle whitelist options based on access mode
            document.getElementById('accessMode').addEventListener('change', function() {
                const whitelistOptions = document.getElementById('whitelist-options');
                if (this.value === 'whitelist') {
                    whitelistOptions.style.display = 'block';
                } else {
                    whitelistOptions.style.display = 'none';
                }
            });
        }

        function addIPField(value = '') {
            const ipList = document.getElementById('ip-list');
            const ipItem = document.createElement('div');
            ipItem.className = 'ip-item';
            ipItem.innerHTML = `
                <input type="text" class="form-control ip-input" value="${value}" placeholder="e.g., 192.168.1.100" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$">
                <button type="button" class="btn btn-danger btn-sm ms-2" onclick="removeIPField(this)">
                    <i class="fas fa-trash"></i> Remove
                </button>
            `;
            ipList.appendChild(ipItem);
        }

        function removeIPField(button) {
            const ipList = document.getElementById('ip-list');
            if (ipList.children.length > 1) {
                button.closest('.ip-item').remove();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Remove',
                    text: 'At least one IP field must remain'
                });
            }
        }

        function validateIP(ip) {
            const ipRegex = /^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/;
            if (!ipRegex.test(ip)) {
                return false;
            }
            const parts = ip.split('.');
            return parts.every(part => {
                const num = parseInt(part, 10);
                return num >= 0 && num <= 255;
            });
        }

        function saveIPs() {
            const ipInputs = document.querySelectorAll('.ip-input');
            const ips = [];
            const invalidIPs = [];

            ipInputs.forEach(input => {
                const ip = input.value.trim();
                if (ip) {
                    if (validateIP(ip)) {
                        ips.push(ip);
                    } else {
                        invalidIPs.push(ip);
                    }
                }
            });

            if (invalidIPs.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid IP Addresses',
                    html: 'The following IP addresses are invalid:<br>' + invalidIPs.join('<br>')
                });
                return;
            }

            const accessMode = document.getElementById('accessMode').value;
            const denyAllByDefault = document.getElementById('denyAllByDefault').checked ? 'true' : 'false';

            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we update the settings',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('action', 'update_ips');
            formData.append('ips', JSON.stringify(ips));
            formData.append('accessMode', accessMode);
            formData.append('denyAllByDefault', denyAllByDefault);

            fetch('set_ip.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            html: `IP addresses updated successfully!<br>Total IPs: ${data.ipCount}`,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to update IP addresses'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving'
                    });
                });
        }

        function logout() {
            Swal.fire({
                title: 'Logout?',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'logout');

                    fetch('set_ip.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Logged Out',
                                    text: 'You have been logged out successfully',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        });
                }
            });
        }
    </script>
</body>

</html>
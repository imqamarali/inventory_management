<?php
// Debug view to check role_id in session
$userArray = Yii::$app->session->get('user_array');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Role Information</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-box {
            background: #f0f0f0;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .field { margin: 10px 0; }
        .label { font-weight: bold; color: #333; }
        .value { color: #666; font-family: monospace; }
    </style>
</head>
<body>
    <h1>Debug: Session Role Information</h1>

    <div class="debug-box">
        <h2>Session User Array:</h2>
        <?php if ($userArray): ?>
            <div class="field">
                <span class="label">User ID:</span>
                <span class="value"><?= isset($userArray['id']) ? $userArray['id'] : 'NOT SET' ?></span>
            </div>
            <div class="field">
                <span class="label">Username:</span>
                <span class="value"><?= isset($userArray['username']) ? $userArray['username'] : 'NOT SET' ?></span>
            </div>
            <div class="field">
                <span class="label">Role ID:</span>
                <span class="value"><?= isset($userArray['role_id']) ? $userArray['role_id'] : 'NOT SET' ?></span>
            </div>
            <div class="field">
                <span class="label">Role ID == 1:</span>
                <span class="value"><?= (isset($userArray['role_id']) && $userArray['role_id'] == 1) ? 'TRUE (Super Admin)' : 'FALSE (Not Super Admin)' ?></span>
            </div>
            <div class="field">
                <span class="label">First Name:</span>
                <span class="value"><?= isset($userArray['first_name']) ? $userArray['first_name'] : 'NOT SET' ?></span>
            </div>
            <div class="field">
                <span class="label">Last Name:</span>
                <span class="value"><?= isset($userArray['last_name']) ? $userArray['last_name'] : 'NOT SET' ?></span>
            </div>
        <?php else: ?>
            <p style="color: red;"><strong>ERROR: user_array not found in session!</strong></p>
        <?php endif; ?>
    </div>

    <div class="debug-box">
        <h2>Role Information from Database:</h2>
        <?php
        try {
            if ($userArray && isset($userArray['id'])) {
                $userRole = Yii::$app->db->createCommand(
                    "SELECT su.id, su.username, su.role_id, r.name as role_name
                     FROM system_users su
                     LEFT JOIN roles r ON su.role_id = r.id
                     WHERE su.id = :user_id"
                )->bindValue(':user_id', $userArray['id'])->queryOne();

                if ($userRole) {
                    echo "<div class='field'>";
                    echo "<span class='label'>User ID:</span>";
                    echo "<span class='value'>" . $userRole['id'] . "</span>";
                    echo "</div>";

                    echo "<div class='field'>";
                    echo "<span class='label'>Username:</span>";
                    echo "<span class='value'>" . $userRole['username'] . "</span>";
                    echo "</div>";

                    echo "<div class='field'>";
                    echo "<span class='label'>Role ID:</span>";
                    echo "<span class='value'>" . $userRole['role_id'] . "</span>";
                    echo "</div>";

                    echo "<div class='field'>";
                    echo "<span class='label'>Role Name:</span>";
                    echo "<span class='value'>" . ($userRole['role_name'] ?? 'Unknown') . "</span>";
                    echo "</div>";
                }
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error querying database: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <div class="debug-box">
        <h2>All Roles in Database:</h2>
        <?php
        try {
            $allRoles = Yii::$app->db->createCommand("SELECT id, name FROM roles ORDER BY id")->queryAll();
            if ($allRoles) {
                echo "<table border='1' cellpadding='10'>";
                echo "<tr><th>Role ID</th><th>Role Name</th></tr>";
                foreach ($allRoles as $role) {
                    echo "<tr><td>" . $role['id'] . "</td><td>" . $role['name'] . "</td></tr>";
                }
                echo "</table>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error querying roles: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <div style="margin: 20px 0;">
        <a href="?r=stock/inventorydashboard" style="padding: 10px 20px; background: #2E7CB5; color: white; text-decoration: none; border-radius: 3px;">
            Go to Inventory Dashboard
        </a>
    </div>
</body>
</html>

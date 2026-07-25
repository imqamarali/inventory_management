<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Connection;
use Yii;
use PDO;

class SystemController extends Controller
{
    /**
     * Create database backup
     */
    public function actionBackup()
    {
        $this->stdout("Starting database backup...\n");

        $backupDir = Yii::getAlias('@app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = $backupDir . '/backup_' . $timestamp . '.sql';

        $db = Yii::$app->db;
        $dsn = $db->dsn;

        // Parse connection info from DSN
        preg_match('/dbname=([^;]+)/', $dsn, $dbMatches);
        preg_match('/host=([^;]+)/', $dsn, $hostMatches);

        $dbname = $dbMatches[1] ?? null;
        $host = $hostMatches[1] ?? 'localhost';
        $user = $db->username;
        $password = $db->password;

        if (!$dbname) {
            $this->stderr("Error: Could not determine database name\n");
            return ExitCode::DATAERR;
        }

        $command = "mysqldump --user=$user --password=$password --host=$host $dbname > \"$backupFile\"";

        $this->stdout("Backing up database: $dbname\n");
        $this->stdout("Backup file: $backupFile\n");

        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        if ($returnVar === 0 && file_exists($backupFile)) {
            $filesize = filesize($backupFile);
            $this->stdout("✓ Backup completed successfully!\n", $this->successColor);
            $this->stdout("  File size: " . $this->formatBytes($filesize) . "\n");
            $this->stdout("  Location: $backupFile\n");
            return ExitCode::OK;
        } else {
            $this->stderr("✗ Backup failed!\n");
            if (!empty($output)) {
                $this->stderr("Error: " . implode("\n", $output) . "\n");
            }
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * List all backups
     */
    public function actionListBackups()
    {
        $this->stdout("Available Backups:\n");
        $this->stdout(str_repeat("-", 70) . "\n");

        $backupDir = Yii::getAlias('@app/backups');
        $backups = [];

        if (is_dir($backupDir)) {
            $files = scandir($backupDir, SCANDIR_SORT_DESCENDING);
            foreach ($files as $file) {
                if (strpos($file, 'backup_') === 0 && strpos($file, '.sql') !== false) {
                    $filepath = $backupDir . '/' . $file;
                    $backups[] = [
                        'filename' => $file,
                        'size' => filesize($filepath),
                        'date' => filemtime($filepath)
                    ];
                }
            }
        }

        if (empty($backups)) {
            $this->stdout("No backups found.\n");
            return ExitCode::OK;
        }

        foreach ($backups as $idx => $backup) {
            $this->stdout(($idx + 1) . ". " . $backup['filename'] . "\n");
            $this->stdout("   Size: " . $this->formatBytes($backup['size']) . " | Date: " . date('Y-m-d H:i:s', $backup['date']) . "\n");
        }

        return ExitCode::OK;
    }

    /**
     * Restore from backup
     */
    public function actionRestore($backupFile = null)
    {
        if (!$backupFile) {
            $this->stderr("Usage: yii system/restore <backup_filename>\n");
            $this->stderr("Example: yii system/restore backup_2026-07-25_10-20-30.sql\n");
            return ExitCode::USAGE;
        }

        $backupDir = Yii::getAlias('@app/backups');
        $filePath = $backupDir . '/' . basename($backupFile);

        if (!file_exists($filePath)) {
            $this->stderr("Error: Backup file not found: $backupFile\n");
            return ExitCode::DATAERR;
        }

        // Create pre-restore backup first
        $this->stdout("Creating backup of current data before restore...\n");
        $result = $this->actionBackup();
        if ($result !== ExitCode::OK) {
            $this->stderr("Error: Failed to create pre-restore backup\n");
            return $result;
        }

        $db = Yii::$app->db;
        $dsn = $db->dsn;

        preg_match('/dbname=([^;]+)/', $dsn, $dbMatches);
        preg_match('/host=([^;]+)/', $dsn, $hostMatches);

        $dbname = $dbMatches[1] ?? null;
        $host = $hostMatches[1] ?? 'localhost';
        $user = $db->username;
        $password = $db->password;

        $this->stdout("Restoring from: $backupFile\n");

        $command = "mysql --user=$user --password=$password --host=$host $dbname < \"$filePath\"";

        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->stdout("✓ Database restored successfully!\n", $this->successColor);
            return ExitCode::OK;
        } else {
            $this->stderr("✗ Restore failed!\n");
            if (!empty($output)) {
                $this->stderr("Error: " . implode("\n", $output) . "\n");
            }
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Run system tests
     */
    public function actionTest($testType = 'all')
    {
        $this->stdout("═══════════════════════════════════════════════════════════════\n");
        $this->stdout("System Performance & Health Check\n");
        $this->stdout("═══════════════════════════════════════════════════════════════\n\n");

        $tests = [];

        if ($testType === 'all' || $testType === 'database') {
            $tests['database'] = $this->testDatabase();
        }
        if ($testType === 'all' || $testType === 'tables') {
            $tests['tables'] = $this->testTables();
        }
        if ($testType === 'all' || $testType === 'crud') {
            $tests['crud'] = $this->testCRUD();
        }
        if ($testType === 'all' || $testType === 'performance') {
            $tests['performance'] = $this->testPerformance();
        }

        $this->displayTestSummary($tests);

        return ExitCode::OK;
    }

    /**
     * Test database connection
     */
    private function testDatabase()
    {
        $this->stdout("\n[DATABASE CONNECTIVITY TEST]\n");
        $this->stdout(str_repeat("-", 50) . "\n");

        try {
            $db = Yii::$app->db;
            $connection = $db->pdo;
            $query = $connection->query('SELECT DATABASE() as db, VERSION() as version');
            $result = $query->fetch(PDO::FETCH_ASSOC);

            $this->stdout("✓ Database: " . $result['db'] . "\n");
            $this->stdout("✓ MySQL Version: " . $result['version'] . "\n");

            return true;
        } catch (\Exception $e) {
            $this->stderr("✗ Error: " . $e->getMessage() . "\n");
            return false;
        }
    }

    /**
     * Test table structure
     */
    private function testTables()
    {
        $this->stdout("\n[TABLE STRUCTURE TEST]\n");
        $this->stdout(str_repeat("-", 50) . "\n");

        try {
            $db = Yii::$app->db;
            $tables = $db->schema->getTableNames();

            $this->stdout("✓ Found " . count($tables) . " tables\n\n");

            foreach (array_slice($tables, 0, 5) as $table) {
                $columns = $db->schema->getTableSchema($table)->columns;
                $this->stdout("  - $table (" . count($columns) . " columns)\n");
            }

            if (count($tables) > 5) {
                $this->stdout("  ... and " . (count($tables) - 5) . " more tables\n");
            }

            return true;
        } catch (\Exception $e) {
            $this->stderr("✗ Error: " . $e->getMessage() . "\n");
            return false;
        }
    }

    /**
     * Test CRUD operations
     */
    private function testCRUD()
    {
        $this->stdout("\n[CRUD OPERATIONS TEST]\n");
        $this->stdout(str_repeat("-", 50) . "\n");

        try {
            $db = Yii::$app->db;

            // Test Insert
            $this->stdout("Testing INSERT... ");
            $start = microtime(true);
            $db->createCommand()->insert('inventory_customers', [
                'company_name' => 'CLI Test ' . time(),
                'first_name' => 'CLI',
                'last_name' => 'Test',
                'email' => 'cli' . time() . '@test.com',
                'phone' => '9999999999',
                'status' => 'active'
            ])->execute();
            $insertTime = (microtime(true) - $start) * 1000;
            $this->stdout("✓ " . number_format($insertTime, 2) . "ms\n");

            // Test Select
            $this->stdout("Testing SELECT... ");
            $start = microtime(true);
            $count = $db->createCommand('SELECT COUNT(*) FROM inventory_customers')->queryScalar();
            $selectTime = (microtime(true) - $start) * 1000;
            $this->stdout("✓ " . number_format($selectTime, 2) . "ms ($count records)\n");

            // Test Update
            $this->stdout("Testing UPDATE... ");
            $start = microtime(true);
            $db->createCommand()->update('inventory_customers', ['status' => 'active'], 'status = :status', [':status' => 'active'])->execute();
            $updateTime = (microtime(true) - $start) * 1000;
            $this->stdout("✓ " . number_format($updateTime, 2) . "ms\n");

            return true;
        } catch (\Exception $e) {
            $this->stderr("✗ Error: " . $e->getMessage() . "\n");
            return false;
        }
    }

    /**
     * Test performance
     */
    private function testPerformance()
    {
        $this->stdout("\n[PERFORMANCE METRICS TEST]\n");
        $this->stdout(str_repeat("-", 50) . "\n");

        try {
            $db = Yii::$app->db;

            // Query performance
            $this->stdout("Query Performance (COUNT)... ");
            $start = microtime(true);
            $count = $db->createCommand('SELECT COUNT(*) FROM inventory_sales_orders')->queryScalar();
            $queryTime = (microtime(true) - $start) * 1000;
            $this->stdout("✓ " . number_format($queryTime, 2) . "ms\n");

            // Join performance
            $this->stdout("Join Performance... ");
            $start = microtime(true);
            $joined = $db->createCommand('SELECT COUNT(*) FROM inventory_sales_orders o LEFT JOIN inventory_customers c ON o.customer_id = c.id')->queryScalar();
            $joinTime = (microtime(true) - $start) * 1000;
            $this->stdout("✓ " . number_format($joinTime, 2) . "ms\n");

            // Database size
            $this->stdout("Database Size... ");
            $tableSize = $db->createCommand("SELECT SUM(data_length + index_length) / 1024 / 1024 as size FROM information_schema.tables WHERE table_schema = DATABASE()")->queryScalar();
            $this->stdout("✓ " . number_format($tableSize, 2) . " MB\n");

            return true;
        } catch (\Exception $e) {
            $this->stderr("✗ Error: " . $e->getMessage() . "\n");
            return false;
        }
    }

    /**
     * Display test summary
     */
    private function displayTestSummary($tests)
    {
        $this->stdout("\n" . str_repeat("═", 50) . "\n");
        $this->stdout("SUMMARY\n");
        $this->stdout(str_repeat("═", 50) . "\n");

        $passed = 0;
        $failed = 0;

        foreach ($tests as $name => $result) {
            if ($result) {
                $passed++;
                $this->stdout("✓ " . ucfirst($name) . " Test: PASSED\n");
            } else {
                $failed++;
                $this->stdout("✗ " . ucfirst($name) . " Test: FAILED\n");
            }
        }

        $total = $passed + $failed;
        $percentage = $total > 0 ? round(($passed / $total) * 100, 2) : 0;

        $this->stdout("\n" . str_repeat("-", 50) . "\n");
        $this->stdout("Total: $total | Passed: $passed | Failed: $failed\n");
        $this->stdout("Success Rate: $percentage%\n");
        $this->stdout(str_repeat("═", 50) . "\n\n");
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public $successColor = ['class' => 'fg-green'];
}

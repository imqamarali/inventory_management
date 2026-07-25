# System Management Setup Instructions

This guide provides step-by-step instructions to set up and configure the System Management features including database backup/restore and performance testing.

---

## Prerequisites

✅ Already Installed:
- PHP 8.3+
- MySQL 8.4+
- Yii Framework 2
- Project Database

---

## Part 1: Windows Setup (For Backup/Restore Feature)

### Step 1: Add MySQL to System PATH

The backup/restore feature requires `mysqldump` and `mysql` commands to be available in your system PATH.

#### Option A: Using WAMP (Recommended)

1. **Locate MySQL Installation**
   - Default WAMP location: `C:\wamp64\bin\mysql\mysql8.0.31\bin`
   - (Version number may vary)

2. **Add to Windows PATH**
   - Press `Win + X`, then select "System"
   - Click "Advanced system settings"
   - Click "Environment Variables" button
   - Under "System variables", select "Path" and click "Edit"
   - Click "New" and add: `C:\wamp64\bin\mysql\mysql8.0.31\bin`
   - Click OK, OK, OK
   - Restart command prompt or IDE

3. **Verify Installation**
   ```bash
   mysqldump --version
   mysql --version
   ```

#### Option B: Manual MySQL Installation

If WAMP doesn't include MySQL:

1. Download MySQL Community Server from [mysql.com](https://www.mysql.com/downloads/)
2. Install to: `C:\Program Files\MySQL\MySQL Server 8.0`
3. Add `C:\Program Files\MySQL\MySQL Server 8.0\bin` to PATH
4. Restart terminal/IDE

### Step 2: Verify Database Credentials

Ensure your database configuration is correct in `config/db.php`:

```php
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=inventory_system',
    'username' => 'root',           // Your MySQL username
    'password' => '',                // Your MySQL password
    'charset' => 'utf8mb4',
],
```

### Step 3: Create Backups Directory

```bash
mkdir backups
```

The system will create this automatically if it doesn't exist.

---

## Part 2: Accessing System Management

### Web Interface

1. **System Dashboard**
   ```
   http://localhost/inventory_system/web/index.php?r=system/index
   ```
   - View system information
   - Quick access to all features

2. **Database Backup Manager**
   ```
   http://localhost/inventory_system/web/index.php?r=system/backup
   ```
   - Create, list, download, restore, and delete backups
   - Real-time status updates
   - Automatic pre-restore backup

3. **System Performance Testing**
   ```
   http://localhost/inventory_system/web/index.php?r=system/systemperformance
   ```
   - Run individual or all tests
   - View detailed results with metrics
   - Export results summary

---

## Part 3: CLI Commands

### Initialize Console Application

First time setup:

```bash
cd C:\wamp64\www\inventory_system
```

All subsequent commands run from this directory.

### Backup Commands

#### Create a Backup
```bash
php yii system/backup
```

**Output:**
```
Starting database backup...
Backing up database: inventory_system
Backup file: C:\wamp64\www\inventory_system\backups\backup_2026-07-25_10-20-30.sql
✓ Backup completed successfully!
  File size: 2.45 MB
  Location: C:\wamp64\www\inventory_system\backups\backup_2026-07-25_10-20-30.sql
```

#### List All Backups
```bash
php yii system/list-backups
```

**Output:**
```
Available Backups:
----------------------------------------------------------------------
1. backup_2026-07-25_10-20-30.sql
   Size: 2.45 MB | Date: 2026-07-25 10:20:30
   
2. backup_2026-07-25_09-15-45.sql
   Size: 2.40 MB | Date: 2026-07-25 09:15:45
```

#### Restore from Backup
```bash
php yii system/restore backup_2026-07-25_10-20-30.sql
```

**Process:**
1. Creates backup of current data (safety copy)
2. Restores from specified backup file
3. Shows confirmation with safety backup filename

### Testing Commands

#### Run All Tests
```bash
php yii system/test all
```

#### Run Specific Test
```bash
php yii system/test database    # Database connectivity
php yii system/test tables      # Table structure
php yii system/test crud        # CRUD operations
php yii system/test performance # Performance metrics
```

**Sample Output:**
```
═══════════════════════════════════════════════════════════════
System Performance & Health Check
═══════════════════════════════════════════════════════════════

[DATABASE CONNECTIVITY TEST]
--------------------------------------------------
✓ Database: inventory_system
✓ MySQL Version: 8.4.7

[TABLE STRUCTURE TEST]
--------------------------------------------------
✓ Found 65 tables

[CRUD OPERATIONS TEST]
--------------------------------------------------
Testing SELECT... ✓ 4.14ms (13 records)
Testing JOIN... ✓ 8.37ms (1 records)
Testing AGGREGATION... ✓ 3.05ms

[PERFORMANCE METRICS TEST]
--------------------------------------------------
Query Performance (COUNT)... ✓ 1.11ms
Join Performance... ✓ 1.27ms
Database Size... ✓ 3.36 MB

══════════════════════════════════════════════════
SUMMARY
══════════════════════════════════════════════════
✓ Database Test: PASSED
✓ Tables Test: PASSED
✓ Crud Test: PASSED
✓ Performance Test: PASSED

Total: 4 | Passed: 4 | Failed: 0
Success Rate: 100%
══════════════════════════════════════════════════
```

---

## Part 4: Scheduling Automated Backups

### Windows Task Scheduler

**Goal:** Backup database daily at 2 AM

**Steps:**

1. **Create Batch File** (`backup.bat`)
   ```batch
   @echo off
   cd C:\wamp64\www\inventory_system
   php yii system/backup
   ```
   
   Save in: `C:\wamp64\www\inventory_system\backup.bat`

2. **Create Task in Task Scheduler**
   - Open Task Scheduler (Start → Task Scheduler)
   - Right-click "Task Scheduler Library" → Create Basic Task
   - Name: `Inventory System Backup`
   - Description: `Daily database backup at 2 AM`
   - Click "Next"

3. **Set Trigger**
   - Select "Daily"
   - Set time to `02:00 AM`
   - Click "Next"

4. **Set Action**
   - Select "Start a program"
   - Program: `C:\wamp64\www\inventory_system\backup.bat`
   - Click "Next"

5. **Finish**
   - Review settings
   - Click "Finish"

**Test:** Right-click task → Run
- Should complete successfully
- Backup file should appear in `/backups/` folder

---

## Part 5: Troubleshooting

### Issue: "mysqldump is not recognized"

**Cause:** MySQL tools not in system PATH

**Solution:**
1. Verify MySQL is installed: `mysql --version`
2. If not found, follow Part 1 setup
3. After adding to PATH, restart terminal/IDE
4. Test: `mysqldump --version`

### Issue: "Access denied for user 'root'@'localhost'"

**Cause:** Wrong database credentials

**Solution:**
1. Update `config/db.php` with correct credentials
2. Verify credentials work: `mysql -u root -p -e "SELECT DATABASE()"`
3. If no password, leave `'password' => '',` empty

### Issue: Backup file size is 0 bytes

**Cause:** MySQL dump failed silently

**Solution:**
1. Check database credentials
2. Verify MySQL user has SELECT permissions
3. Run command manually to see error:
   ```bash
   mysqldump -u root -p inventory_system > test.sql
   ```

### Issue: Cannot create backups directory

**Cause:** Permission denied

**Solution:**
1. Create manually: `mkdir C:\wamp64\www\inventory_system\backups`
2. Set permissions: Right-click → Properties → Security → Edit
3. Add full permissions for current user

### Issue: Tests fail with database errors

**Cause:** Database not running or wrong credentials

**Solution:**
1. Start MySQL service: Services → MySQL → Right-click → Start
2. Verify connection: `mysql -u root -p -e "SELECT 1"`
3. Check `config/db.php` credentials

---

## Part 6: Security Recommendations

⚠️ **Important:** Follow these security best practices:

### 1. Restrict Web Access
```php
// Add to SystemController.php actionBackup()
public function beforeAction($action)
{
    if (!\Yii::$app->user->isGuest && \Yii::$app->user->identity->role === 'admin') {
        return parent::beforeAction($action);
    }
    throw new \yii\web\ForbiddenHttpException('Access denied');
}
```

### 2. Backup Storage Security
- Store backups outside web root
- Encrypt sensitive backups
- Regular backup of backups to external storage

### 3. Database User Permissions
Create dedicated backup user:
```sql
CREATE USER 'backup_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT SELECT, LOCK TABLES, SHOW VIEW ON inventory_system.* TO 'backup_user'@'localhost';
GRANT RELOAD ON *.* TO 'backup_user'@'localhost';
FLUSH PRIVILEGES;
```

Use in config:
```php
'username' => 'backup_user',
'password' => 'strong_password',
```

### 4. Audit Logging
```php
// Log all backup operations
Yii::info("Backup created by " . Yii::$app->user->identity->username, 'system');
Yii::info("Database restored from " . $filename, 'system');
```

### 5. Environment Variables
Never hardcode credentials:
```php
'username' => getenv('DB_USER'),
'password' => getenv('DB_PASSWORD'),
```

---

## Part 7: Maintenance

### Regular Tasks

**Weekly:**
- Review backup file sizes
- Delete old backups (keep last 4 weeks)

**Monthly:**
- Test restore procedure
- Verify backup completeness
- Review system test results

### Backup Rotation

```bash
# Delete backups older than 30 days (Windows)
forfiles /S /D +30 /M backup_*.sql /C "cmd /c del @path"
```

### Monitor Database Growth

Check database size:
```bash
php yii system/test performance
```

This shows current database size and helps plan backup storage.

---

## Part 8: Reference

### Files Created

```
/controllers/SystemController.php      - Web interface controller
/commands/SystemController.php          - CLI commands controller
/views/system/
  - index.php                          - Dashboard view
  - backup.php                         - Backup manager view
  - systemperformance.php              - Testing view
/backups/                              - Backup storage (auto-created)
/SYSTEM_MANAGEMENT_GUIDE.md            - Full feature guide
/SYSTEM_SETUP_INSTRUCTIONS.md          - This file
```

### Database Tables

System management affects:
- All application tables (backup/restore)
- No permanent changes to schema

### Performance Impact

- Backups: 2-5 minutes depending on database size
- Tests: 5-10 seconds for all tests
- No production impact during testing

---

## Support & Documentation

- **Full Feature Guide:** See `SYSTEM_MANAGEMENT_GUIDE.md`
- **Web Interface:** Self-documented with inline help
- **CLI Help:** Run `php yii help system/backup`

---

**Version:** 1.0
**Last Updated:** 2026-07-25

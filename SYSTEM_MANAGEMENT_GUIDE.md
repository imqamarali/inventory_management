# System Management Guide

This guide explains how to use the System Controller for database backup/restore and system performance testing.

## Table of Contents
1. [Web Interface Access](#web-interface-access)
2. [Database Backup & Restore](#database-backup--restore)
3. [System Performance Testing](#system-performance-testing)
4. [CLI Commands](#cli-commands)
5. [Security Notes](#security-notes)

---

## Web Interface Access

### Access System Management Dashboard
Navigate to: `http://localhost/inventory_system/index.php?r=system/index`

The dashboard provides quick access to:
- Database Backup Manager
- System Performance Tests
- Server Information

---

## Database Backup & Restore

### Backup Page
**URL:** `http://localhost/inventory_system/index.php?r=system/backup`

### Features
- **Create Backup**: One-click backup creation with automatic file naming
- **List Backups**: View all existing backups with file size and creation date
- **Download Backup**: Download backup files to your computer
- **Restore Backup**: Restore from any backup file
  - Automatically creates a backup of current data before restoring
  - This ensures you never lose data during restore operations
- **Delete Backup**: Remove old backup files to save space

### How to Create a Backup

1. Navigate to the Backup Manager page
2. Click **"Create New Backup"** button
3. Wait for confirmation message
4. Backup file will appear in the list with timestamp

**Format:** `backup_YYYY-MM-DD_HH-mm-ss.sql`

### How to Restore from Backup

1. Locate the backup file in the list
2. Click **"Restore"** button next to it
3. Confirm the action in the popup
4. System automatically:
   - Creates backup of current data (prefixed with current timestamp)
   - Restores database from selected backup
   - Shows confirmation with pre-restore backup filename

### Backup Storage
- Location: `/backups/` directory
- All backup files are stored as `.sql` files
- Files can be downloaded, restored, or deleted

---

## System Performance Testing

### Performance Test Page
**URL:** `http://localhost/inventory_system/index.php?r=system/systemperformance`

### Available Tests

#### 1. **Database Connectivity**
- Tests MySQL connection
- Displays database name
- Shows MySQL version
- **Status:** ✓ Pass/✗ Fail

#### 2. **Table Structure**
- Lists all tables in database
- Shows column count for each table
- Verifies table structure integrity
- **Status:** ✓ Pass/✗ Fail

#### 3. **Sample Data Operations (CRUD)**
- Tests INSERT performance
- Tests SELECT performance with record count
- Tests UPDATE performance
- Measures execution time in milliseconds
- **Status:** ✓ Pass/⚠ Warning/✗ Fail

#### 4. **Screen Rendering Test**
- Tests rendering of main application screens
- Includes:
  - Inventory Dashboard
  - Sales Dashboard
  - Sales Orders
  - Warehouse Management
  - Customer Management
- Shows render time for each screen
- **Status:** ✓ Pass/✗ Fail

#### 5. **Controller Actions**
- Verifies all controller actions exist
- Tests main functionality controllers
- **Status:** ✓ Pass/✗ Fail

#### 6. **Data Integrity**
- Checks foreign key constraints
- Detects orphaned records
- Ensures data consistency
- **Status:** ✓ Pass/⚠ Warning/✗ Fail

#### 7. **Performance Metrics**
- Query performance (COUNT operations)
- Join operation performance
- Database size calculation
- **Status:** ✓ Pass/⚠ Warning/✗ Fail

### How to Run Tests

#### Run All Tests
1. Navigate to Performance Test page
2. Select **"All Tests"** from dropdown
3. Click **"Run Tests"** button
4. View results in real-time

#### Run Specific Test
1. Select test type from dropdown
2. Click **"Run Tests"** button
3. Results display with detailed information

### Test Results Display

**Summary Cards:**
- ✓ Tests Passed (Green)
- ✗ Tests Failed (Red)
- ⚠ Warnings (Orange)
- Success Rate Percentage (Blue)

**Detailed Results:**
- Each test group shows all sub-tests
- Color-coded status indicators
- Performance metrics in milliseconds
- Click test group to expand/collapse

---

## CLI Commands

### Using Terminal/Command Prompt

#### Create Backup via CLI
```bash
cd C:\wamp64\www\inventory_system
php yii system/backup
```

**Output Example:**
```
Starting database backup...
Backing up database: inventory_system
Backup file: C:\wamp64\www\inventory_system\backups\backup_2026-07-25_10-20-30.sql
✓ Backup completed successfully!
  File size: 2.45 MB
  Location: C:\wamp64\www\inventory_system\backups\backup_2026-07-25_10-20-30.sql
```

#### List All Backups via CLI
```bash
php yii system/list-backups
```

**Output Example:**
```
Available Backups:
----------------------------------------------------------------------
1. backup_2026-07-25_10-20-30.sql
   Size: 2.45 MB | Date: 2026-07-25 10:20:30

2. backup_2026-07-25_09-15-45.sql
   Size: 2.40 MB | Date: 2026-07-25 09:15:45
```

#### Restore from Backup via CLI
```bash
php yii system/restore backup_2026-07-25_10-20-30.sql
```

**Output Example:**
```
Creating backup of current data before restore...
Starting database backup...
Backing up database: inventory_system
Backup file: C:\wamp64\www\inventory_system\backups\backup_2026-07-25_10-25-00.sql
✓ Backup completed successfully!
Restoring from: backup_2026-07-25_10-20-30.sql
✓ Database restored successfully!
```

#### Run System Tests via CLI
```bash
php yii system/test all
```

**Test Types:**
```bash
php yii system/test database        # Test database connectivity
php yii system/test tables          # Test table structure
php yii system/test crud            # Test CRUD operations
php yii system/test performance     # Test performance metrics
```

**Output Example:**
```
═══════════════════════════════════════════════════════════════
System Performance & Health Check
═══════════════════════════════════════════════════════════════

[DATABASE CONNECTIVITY TEST]
--------------------------------------------------
✓ Database: inventory_system
✓ MySQL Version: 5.7.24-0ubuntu0.18.04.1

[TABLE STRUCTURE TEST]
--------------------------------------------------
✓ Found 25 tables

  - inventory_products (12 columns)
  - inventory_customers (8 columns)
  - inventory_sales_orders (15 columns)
  - inventory_warehouse (6 columns)
  ... and 21 more tables

[CRUD OPERATIONS TEST]
--------------------------------------------------
Testing INSERT... ✓ 45.32ms
Testing SELECT... ✓ 23.15ms (2500 records)
Testing UPDATE... ✓ 156.78ms

[PERFORMANCE METRICS TEST]
--------------------------------------------------
Query Performance (COUNT)... ✓ 12.45ms
Join Performance... ✓ 34.67ms
Database Size... ✓ 5.80 MB

==================================================
SUMMARY
==================================================
✓ Database Test: PASSED
✓ Tables Test: PASSED
✓ Crud Test: PASSED
✓ Performance Test: PASSED

Total: 4 | Passed: 4 | Failed: 0
Success Rate: 100%
==================================================
```

---

## Backup File Format

Backup files are standard MySQL SQL dumps containing:
- Database structure (CREATE TABLE statements)
- All data (INSERT statements)
- Triggers, views, and procedures (if any)

### To Import Backup Manually:
```bash
mysql -u username -p database_name < backup_file.sql
```

### To Export Manually:
```bash
mysqldump -u username -p database_name > backup_file.sql
```

---

## Automation & Scheduling

### Daily Automated Backups (Optional Setup)

#### Windows Task Scheduler
1. Open Task Scheduler
2. Create Basic Task
3. Set trigger: Daily at specific time
4. Set action: Run batch script

**Script Content** (`backup.bat`):
```batch
cd C:\wamp64\www\inventory_system
php yii system/backup
```

#### Linux/Mac Cron Job
```bash
# Daily backup at 2 AM
0 2 * * * cd /path/to/inventory_system && php yii system/backup
```

---

## Troubleshooting

### Issue: Backup Creation Failed
**Possible Causes:**
- MySQL credentials incorrect
- No write permissions in `/backups/` folder
- `mysqldump` not in system PATH

**Solution:**
1. Verify MySQL credentials in `config/db.php`
2. Check folder permissions: `chmod 755 backups/`
3. Add MySQL to system PATH

### Issue: Cannot Restore Backup
**Possible Causes:**
- Backup file corrupted
- Incompatible MySQL version
- Different database structure

**Solution:**
1. Verify backup file exists and has content
2. Check MySQL version compatibility
3. Ensure backup is from same database structure

### Issue: Tests Showing Warnings
**Causes:**
- Query execution time > 100ms
- Orphaned records in database
- Large database size

**Solution:**
1. Check database indexes
2. Review data relationships
3. Run cleanup queries if needed

### Issue: Console Commands Not Working
**Solution:**
1. Verify PHP is in system PATH
2. Navigate to project directory first
3. Use full path: `php C:\wamp64\www\inventory_system\yii`

---

## Security Recommendations

⚠️ **Important:**

1. **Restrict Access**: Configure web access to system panel via authentication
   - Add role-based access control
   - Require admin login

2. **Backup Storage**: 
   - Store backups in secure location
   - Regular backup of backups to external storage
   - Encrypt sensitive backups

3. **Audit Logging**:
   - Log all backup/restore operations
   - Monitor system test results
   - Keep audit trail

4. **Database Credentials**:
   - Don't expose in public files
   - Use environment variables
   - Restrict user database permissions

---

## Additional Notes

- Backup and Restore operations require MySQL command-line tools (`mysqldump`, `mysql`)
- Performance tests measure typical operations; results vary based on data volume
- System performance tests are non-destructive (except sample CRUD which creates test records)
- All backup files are stored locally in `/backups/` folder

---

## Support

For issues or questions:
1. Check error messages in console
2. Review system logs
3. Verify database connectivity
4. Ensure proper file permissions

---

**Last Updated:** 2026-07-25
**Version:** 1.0

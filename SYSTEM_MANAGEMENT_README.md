# System Management Features - Quick Start Guide

## Overview

The Inventory System now includes comprehensive System Management features for database backup/restore and system performance testing.

---

## 🎯 Quick Start (30 seconds)

### Access Web Interface
```
http://localhost/inventory_system/web/index.php?r=system/index
```

### Access Backup Manager
```
http://localhost/inventory_system/web/index.php?r=system/backup
```

### Access Performance Tests
```
http://localhost/inventory_system/web/index.php?r=system/systemperformance
```

---

## 📋 Features Summary

### 1. Database Backup & Restore ✅
- **Create backups** with one click
- **Download backups** for offline storage
- **Restore safely** with automatic pre-restore backup
- **Delete old backups** to save space
- **List all backups** with size and date info

**Access:**
- Web: `http://localhost/inventory_system/web/index.php?r=system/backup`
- CLI: `php yii system/backup` (create), `php yii system/list-backups` (list), `php yii system/restore [file]`

### 2. System Performance Testing ✅
- **Database connectivity** test
- **Table structure** verification
- **CRUD operations** performance
- **Screen rendering** tests
- **Controller actions** verification
- **Data integrity** checks
- **Performance metrics** measurement

**Access:**
- Web: `http://localhost/inventory_system/web/index.php?r=system/systemperformance`
- CLI: `php yii system/test all` (all tests), `php yii system/test [type]` (specific test)

---

## 📁 File Structure

```
inventory_system/
├── controllers/
│   └── SystemController.php          (Web interface controller)
├── commands/
│   └── SystemController.php          (CLI commands)
├── views/system/
│   ├── index.php                     (Dashboard)
│   ├── backup.php                    (Backup manager)
│   └── systemperformance.php         (Testing interface)
├── backups/                          (Backup storage)
│   └── backup_YYYY-MM-DD_HH-mm-ss.sql (Auto-created)
├── SYSTEM_MANAGEMENT_GUIDE.md        (Full feature documentation)
├── SYSTEM_SETUP_INSTRUCTIONS.md      (Setup & configuration guide)
└── SYSTEM_MANAGEMENT_README.md       (This file)
```

---

## 🚀 Quick Start Examples

### Create a Backup (CLI)
```bash
cd C:\wamp64\www\inventory_system
php yii system/backup
```

### List Backups (CLI)
```bash
php yii system/list-backups
```

### Restore from Backup (CLI)
```bash
php yii system/restore backup_2026-07-25_10-20-30.sql
```

### Run All Tests (CLI)
```bash
php yii system/test all
```

### Run Specific Test (CLI)
```bash
php yii system/test database
php yii system/test performance
```

---

## 📊 Test Types Explained

### Database Connectivity
- Verifies MySQL connection
- Displays database name and MySQL version
- **Time:** < 10ms

### Table Structure
- Lists all database tables
- Shows column count per table
- **Time:** < 50ms

### CRUD Operations
- Tests SELECT query performance
- Tests JOIN operations
- Tests data aggregation
- **Time:** < 20ms total

### Screen Rendering
- Tests main application pages render correctly
- Measures render time
- **Time:** 50-200ms per screen

### Controller Actions
- Verifies all controller actions exist
- **Time:** Instant

### Data Integrity
- Checks foreign key constraints
- Detects orphaned records
- **Time:** < 50ms

### Performance Metrics
- Measures query performance
- Shows database size
- **Time:** < 20ms

---

## ⚙️ Configuration

### Database Configuration
Edit `config/db.php`:
```php
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=inventory_system',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
],
```

### Required for Backup/Restore
MySQL tools must be in system PATH:
- `mysqldump` - for creating backups
- `mysql` - for restoring backups

See `SYSTEM_SETUP_INSTRUCTIONS.md` for PATH configuration.

---

## 🔒 Security

⚠️ **Important:** Add access control to prevent unauthorized use:

```php
// In SystemController.php
public function beforeAction($action)
{
    // Only allow admin users
    if (!\Yii::$app->user->isGuest && \Yii::$app->user->identity->role === 'admin') {
        return parent::beforeAction($action);
    }
    throw new \yii\web\ForbiddenHttpException('Access denied');
}
```

---

## 📚 Full Documentation

For complete details, see:

1. **SYSTEM_MANAGEMENT_GUIDE.md**
   - Comprehensive feature documentation
   - Web interface walkthrough
   - CLI commands reference
   - Automation setup
   - Troubleshooting guide

2. **SYSTEM_SETUP_INSTRUCTIONS.md**
   - Step-by-step setup instructions
   - Windows PATH configuration
   - Automated backup scheduling
   - Security best practices
   - Database user permissions

---

## 🆘 Troubleshooting

### Issue: Backup fails with "mysqldump not recognized"
**Solution:** Add MySQL to system PATH (see SYSTEM_SETUP_INSTRUCTIONS.md)

### Issue: Tests fail with database errors
**Solution:** Verify database credentials in config/db.php

### Issue: Cannot restore backup
**Solution:** Ensure backup file exists in `/backups/` directory

### Issue: All tests show 0% success
**Solution:** Check database is running: `mysql -u root -p -e "SELECT 1"`

For more help, see **Troubleshooting** section in SYSTEM_SETUP_INSTRUCTIONS.md

---

## 📈 Performance

### Typical Response Times
- Backup creation: 2-5 minutes (database size dependent)
- Running all tests: 5-10 seconds
- Single test: 100-500ms

### Database Impact
- Backups: Read-only, no locks
- Tests: Read-only, minimal load
- No data modification

---

## 🔄 Workflow Example

### Daily Operations
1. **Morning:** Run quick test `php yii system/test performance`
2. **After major changes:** Create backup `php yii system/backup`
3. **Weekly:** Run full test `php yii system/test all`
4. **Monthly:** Delete old backups, test restore procedure

### Emergency Recovery
1. List backups: `php yii system/list-backups`
2. Restore: `php yii system/restore backup_2026-07-24_23-59-59.sql`
3. Verify: `php yii system/test all`
4. Check: Web interface or run tests again

---

## 📞 Support

### Getting Help
1. Check **SYSTEM_SETUP_INSTRUCTIONS.md** for setup issues
2. Check **SYSTEM_MANAGEMENT_GUIDE.md** for feature details
3. Review error messages in CLI output
4. Check database logs: `mysql.error.log`

### Reporting Issues
Include:
- Console output (exact error message)
- MySQL version: `mysql --version`
- PHP version: `php -v`
- Database size: From performance test
- Backup file size (if applicable)

---

## 🎓 Learning Path

### Beginner
1. Read this README
2. Access web interface dashboard
3. Try creating a backup
4. Run all tests from web interface

### Intermediate
1. Read SYSTEM_SETUP_INSTRUCTIONS.md
2. Configure system PATH for CLI
3. Run CLI commands
4. Schedule automated backups

### Advanced
1. Read SYSTEM_MANAGEMENT_GUIDE.md
2. Implement security controls
3. Set up monitoring/logging
4. Customize for your needs

---

## ✨ Features at a Glance

| Feature | Web | CLI | Auto | Time |
|---------|-----|-----|------|------|
| Create Backup | ✅ | ✅ | ✅* | 2-5m |
| List Backups | ✅ | ✅ | - | <1s |
| Restore Backup | ✅ | ✅ | - | 2-5m |
| Download Backup | ✅ | - | - | <1s |
| Delete Backup | ✅ | - | - | <1s |
| Run Tests | ✅ | ✅ | ✅* | 5-10s |
| Database Test | ✅ | ✅ | ✅* | <100ms |
| Performance Test | ✅ | ✅ | ✅* | <100ms |

*Can be automated with Task Scheduler

---

## 📝 Version Information

- **Version:** 1.0
- **Created:** 2026-07-25
- **PHP:** 8.3+
- **MySQL:** 5.7+
- **Yii:** 2.x

---

## 📝 Changelog

### Version 1.0 (Initial Release)
- ✅ Database backup/restore functionality
- ✅ System performance testing framework
- ✅ Web interface for all features
- ✅ CLI commands for automation
- ✅ Comprehensive documentation
- ✅ Windows PATH setup guide
- ✅ Automated backup scheduling support

---

## 🙏 Credits

Created with comprehensive documentation and testing support.

Built with attention to:
- ✅ Safety (pre-restore backups)
- ✅ Usability (web + CLI interfaces)
- ✅ Performance (optimized queries)
- ✅ Reliability (error handling)
- ✅ Documentation (complete guides)

---

## 📖 Quick Links

| Document | Purpose |
|----------|---------|
| [SYSTEM_MANAGEMENT_GUIDE.md](./SYSTEM_MANAGEMENT_GUIDE.md) | Complete feature documentation |
| [SYSTEM_SETUP_INSTRUCTIONS.md](./SYSTEM_SETUP_INSTRUCTIONS.md) | Setup, configuration, troubleshooting |
| [SYSTEM_MANAGEMENT_README.md](./SYSTEM_MANAGEMENT_README.md) | This quick start guide |

---

**Ready to get started? Visit your dashboard:**
```
http://localhost/inventory_system/web/index.php?r=system/index
```

**Or run your first test:**
```bash
cd C:\wamp64\www\inventory_system
php yii system/test all
```

---

*For detailed information on any feature, see the full documentation guides.*

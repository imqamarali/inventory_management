---
name: deployment-checklist
description: Complete production deployment checklist for inventory management system
metadata:
  type: project
---

# 🚀 DEPLOYMENT CHECKLIST - INVENTORY MANAGEMENT SYSTEM

## Pre-Deployment Phase (24 hours before)

### 1. Code Freeze & Testing ✅
- [ ] All development work committed to git
- [ ] All changes pushed to GitHub main branch
- [ ] Code reviewed by team lead
- [ ] Unit tests passing
- [ ] Integration tests passing
- [ ] Manual testing completed on staging
- [ ] No console errors or warnings
- [ ] Security scan completed (OWASP Top 10)

### 2. Database Backup ✅
- [ ] Full database backup created
- [ ] Backup verified (can be restored)
- [ ] Backup stored in secure location (AWS S3/Google Cloud)
- [ ] Database schema validated
- [ ] All migrations tested
- [ ] Database size documented

### 3. Code Review Checklist ✅
- [ ] No hardcoded credentials in code
- [ ] No debug statements (console.log, var_dump)
- [ ] Error logging configured properly
- [ ] Sensitive data not logged
- [ ] No test files in web root
- [ ] No temporary files committed
- [ ] File permissions correct (644 for files, 755 for dirs)

### 4. Performance Validation ✅
- [ ] Load testing completed (100+ concurrent users)
- [ ] Database query optimization verified
- [ ] Cache strategy implemented
- [ ] Image optimization completed
- [ ] Asset minification working
- [ ] Response times acceptable (< 2 seconds)
- [ ] PDF generation performance tested

---

## Environment Setup Phase

### 5. Server Preparation ✅
- [ ] Production server specifications verified
  - [ ] PHP 8.3+ installed
  - [ ] MySQL 8.0+ installed
  - [ ] Composer installed
  - [ ] Node.js (optional, for asset building)
  - [ ] Git installed
  - [ ] SSL certificate installed
  - [ ] 2+ GB RAM available
  - [ ] 50+ GB disk space available

- [ ] Directory structure created
  - [ ] /var/www/inventory_system
  - [ ] /var/www/inventory_system/backups
  - [ ] /var/www/inventory_system/runtime/logs
  - [ ] /var/www/inventory_system/runtime/cache

- [ ] File permissions set
  ```bash
  chmod 755 /var/www/inventory_system
  chmod 755 /var/www/inventory_system/web
  chmod 777 /var/www/inventory_system/runtime
  chmod 777 /var/www/inventory_system/web/uploads
  chmod 777 /var/www/inventory_system/backups
  ```

### 6. Database Setup ✅
- [ ] Production database created
- [ ] Database user created with limited privileges
- [ ] Database backup schedule configured
- [ ] Timezone verified (Asia/Karachi UTC+5)
- [ ] Character set set to utf8mb4
- [ ] Connection pooling configured
- [ ] Query logging enabled (temporary)
- [ ] Database replication configured (if applicable)

### 7. Web Server Configuration ✅
- [ ] Apache/Nginx configured
- [ ] Virtual host configured
- [ ] SSL certificate configured
- [ ] HTTP to HTTPS redirect configured
- [ ] Gzip compression enabled
- [ ] Cache headers configured
- [ ] Security headers added
  - [ ] X-Frame-Options: SAMEORIGIN
  - [ ] X-Content-Type-Options: nosniff
  - [ ] X-XSS-Protection: 1; mode=block
  - [ ] Strict-Transport-Security enabled

---

## Application Deployment Phase

### 8. Code Deployment ✅
- [ ] Clone repository from GitHub
  ```bash
  cd /var/www
  git clone https://github.com/imqamarali/inventory_management.git
  cd inventory_system
  git checkout main
  ```

- [ ] Install dependencies
  ```bash
  composer install --no-dev --optimize-autoloader
  npm install (if using frontend build)
  ```

- [ ] Set file permissions
  ```bash
  chmod 755 web/index.php
  chmod 755 yii
  chown -R www-data:www-data /var/www/inventory_system
  ```

- [ ] Clear cache directories
  ```bash
  rm -rf runtime/cache/*
  rm -rf web/assets/*
  ```

### 9. Environment Configuration ✅
- [ ] .env file created with production values
  - [ ] DB_HOST (production database)
  - [ ] DB_NAME (production database name)
  - [ ] DB_USER (limited privileges user)
  - [ ] DB_PASS (strong password)
  - [ ] APP_DEBUG = false
  - [ ] APP_ENV = production

- [ ] config/db.php updated
  - [ ] Database credentials correct
  - [ ] Timezone set to Asia/Karachi
  - [ ] Connection pooling enabled
  - [ ] SSL connection enabled (if applicable)

- [ ] config/web.php updated
  - [ ] timeZone = 'Asia/Karachi'
  - [ ] errorHandler configured
  - [ ] session configuration correct
  - [ ] cookie validation key correct
  - [ ] log targets configured

- [ ] Yii configuration optimized
  - [ ] Debug module disabled
  - [ ] Gii module disabled
  - [ ] Pretty URLs enabled (if desired)
  - [ ] Cache component configured

### 10. Database Migration ✅
- [ ] Database backup created before migration
- [ ] All migrations run successfully
  ```bash
  php yii migrate
  ```
- [ ] Database tables verified
- [ ] Indexes created
- [ ] Foreign keys verified
- [ ] Test data loaded (if applicable)
- [ ] Database integrity checked

---

## Security Hardening Phase

### 11. Security Configuration ✅
- [ ] CSRF protection enabled
- [ ] SQL injection prevention verified
- [ ] XSS protection verified
- [ ] File upload validation working
  - [ ] File type validation
  - [ ] File size limits enforced
  - [ ] Malware scanning enabled
- [ ] Authentication tested
  - [ ] Login working
  - [ ] Session management working
  - [ ] Password encryption verified
- [ ] Authorization tested
  - [ ] User roles working
  - [ ] Permission checks working
  - [ ] Admin functions protected

### 12. Security Hardening ✅
- [ ] Remove test files from web root
  ```bash
  rm web/check_db.php
  rm web/fix_login.php
  rm web/get_otp.php
  rm web/set_ip.php
  rm web/set_password.php
  rm web/test_*.php
  ```

- [ ] Hide sensitive files
  - [ ] .env file not in web root
  - [ ] config files not accessible via web
  - [ ] vendor directory not accessible via web

- [ ] Configure .htaccess (if Apache)
  ```apache
  <FilesMatch "\.php$">
    Deny from all
  </FilesMatch>
  ```

- [ ] Firewall rules configured
  - [ ] Only necessary ports open
  - [ ] SSH access restricted to admin IPs
  - [ ] Database access restricted to app server

- [ ] SSL/TLS configured
  - [ ] Certificate installed
  - [ ] HTTPS enforced
  - [ ] TLS 1.2+ only

---

## Application Verification Phase

### 13. Application Testing ✅
- [ ] Home page loads
- [ ] Login works
- [ ] Dashboard displays correctly
- [ ] All modules accessible
- [ ] Purchase orders can be created
- [ ] Sales orders can be created
- [ ] Payment uploads work
- [ ] PDF generation works
- [ ] Reports generate correctly
- [ ] Export to CSV works
- [ ] Timezone displays correctly (Asia/Karachi)

### 14. Feature Testing ✅
- [ ] Dashboard statistics correct
- [ ] Purchase order workflow complete
  - [ ] Create PO
  - [ ] Approve PO
  - [ ] Complete PO
  - [ ] Stock updates correctly
- [ ] Sales order workflow complete
  - [ ] Create SO
  - [ ] View order details
  - [ ] Generate invoice
- [ ] Payment system working
  - [ ] Upload payment proof
  - [ ] Verification workflow
  - [ ] Payment status updates
- [ ] Inventory tracking accurate
- [ ] User permissions enforced

### 15. Performance Testing ✅
- [ ] Page load times acceptable (< 2 seconds)
- [ ] Database queries optimized
- [ ] No N+1 queries
- [ ] Memory usage acceptable
- [ ] CPU usage acceptable
- [ ] Disk I/O acceptable
- [ ] Cache working
- [ ] Asset compression working (CSS/JS)

### 16. Logging & Monitoring ✅
- [ ] Error logging working
  - [ ] Errors written to log file
  - [ ] Critical errors trigger alerts
- [ ] Activity logging working
  - [ ] User actions logged
  - [ ] Modifications tracked
- [ ] Performance monitoring
  - [ ] Response time tracking
  - [ ] Error rate tracking
- [ ] Uptime monitoring configured
  - [ ] Health check endpoint
  - [ ] Monitoring service configured

---

## Post-Deployment Phase

### 17. Data Validation ✅
- [ ] All existing data migrated
- [ ] Data integrity verified
- [ ] Row counts match staging
- [ ] No data loss detected
- [ ] Relationships intact
- [ ] Backup of production data created

### 18. Monitoring Setup ✅
- [ ] Error notification configured
- [ ] Performance alerts configured
- [ ] Uptime monitoring running
- [ ] Log aggregation working
- [ ] Database monitoring active
- [ ] Disk space monitoring active
- [ ] CPU/Memory monitoring active

### 19. Documentation ✅
- [ ] Deployment documented
- [ ] Configuration documented
- [ ] Database schema documented
- [ ] Known issues documented
- [ ] Rollback procedure documented
- [ ] Support contacts updated

### 20. Team Notification ✅
- [ ] Deployment completed notification sent
- [ ] Users informed of go-live
- [ ] Support team briefed
- [ ] Admin team briefed
- [ ] Training scheduled (if needed)

---

## Optimization & Cleanup Phase

### 21. Production Optimization ✅
- [ ] Asset cache cleared and regenerated
  ```bash
  rm -rf web/assets/*
  ```

- [ ] Composer optimized
  ```bash
  composer install --no-dev --optimize-autoloader
  composer dump-autoload --optimize --no-dev
  ```

- [ ] Old logs cleaned
  ```bash
  rm -rf runtime/logs/*.log
  ```

- [ ] Old backups archived
  ```bash
  Keep: Latest 2 backups on server
  Archive: Older backups to cloud storage
  ```

- [ ] Image optimization verified
  - [ ] All images compressed
  - [ ] WebP format used where applicable
  - [ ] Lazy loading implemented

### 22. Backup Strategy ✅
- [ ] Daily backups scheduled
  ```bash
  0 2 * * * /var/www/inventory_system/scripts/backup.sh
  ```

- [ ] Weekly backups to cloud
  ```bash
  0 3 * * 0 /var/www/inventory_system/scripts/backup-cloud.sh
  ```

- [ ] Backup retention policy
  - [ ] Keep 7 daily backups
  - [ ] Keep 4 weekly backups
  - [ ] Keep 12 monthly backups
  - [ ] Test restore monthly

### 23. Performance Tuning ✅
- [ ] Database indexes verified
- [ ] Query cache configured
- [ ] Connection pooling optimized
- [ ] PHP opcache configured
- [ ] Static file caching configured
- [ ] CDN configured (if applicable)

### 24. Security Audit ✅
- [ ] SSL/TLS validation
- [ ] Security headers verified
- [ ] CORS policy configured
- [ ] Rate limiting configured
- [ ] DDoS protection enabled
- [ ] WAF rules configured
- [ ] Penetration testing completed

---

## Rollback Plan

### If Deployment Fails:
1. [ ] Stop all web traffic
2. [ ] Switch back to previous version
   ```bash
   git checkout <previous-commit>
   composer install --no-dev
   ```
3. [ ] Restore database from backup
4. [ ] Verify system working
5. [ ] Notify team
6. [ ] Investigate issue
7. [ ] Fix and redeploy

---

## Post-Deployment Monitoring (First 24 Hours)

### Hour 1: Critical Monitoring
- [ ] No errors in error log
- [ ] All pages loading
- [ ] Database connections stable
- [ ] Performance metrics normal

### Hour 2-24: Continuous Monitoring
- [ ] Error rate stable
- [ ] Performance metrics stable
- [ ] User reports any issues
- [ ] Backup jobs running
- [ ] Monitoring alerts working

---

## Sign-Off

**Deployment Date:** _______________

**Deployed By:** _________________________

**Verified By:** _________________________

**Production Ready:** ✅ YES / ❌ NO

**Notes:** 

_________________________________________________

_________________________________________________

_________________________________________________

---

## Emergency Contacts

- **DevOps Lead:** _________________________ (___________)
- **Database Admin:** _________________________ (___________)
- **Security Team:** _________________________ (___________)
- **Support Lead:** _________________________ (___________)

---

**Total Checklist Items:** 100+
**Estimated Deployment Time:** 2-4 hours
**Estimated Testing Time:** 1-2 hours
**Total Window:** 3-6 hours

🎉 **Deployment Successful! System is now LIVE in Production!**

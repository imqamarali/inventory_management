---
name: deployment-guide
description: Step-by-step deployment guide for production
metadata:
  type: project
---

# 📚 DEPLOYMENT GUIDE - INVENTORY MANAGEMENT SYSTEM

## Quick Start (TL;DR)

```bash
# 1. Clone and setup
cd /var/www
git clone https://github.com/imqamarali/inventory_management.git
cd inventory_system

# 2. Install dependencies
composer install --no-dev --optimize-autoloader

# 3. Optimize for production
chmod +x scripts/*.sh
./scripts/optimize-production.sh

# 4. Setup environment
cp config/.env.example config/.env
# Edit config/.env with production values

# 5. Database setup
php yii migrate

# 6. Set permissions
chown -R www-data:www-data .
chmod 755 .
chmod 777 runtime web/uploads backups

# 7. Start services
sudo service apache2 restart
sudo service mysql restart

# 8. Verify
./scripts/health-check.sh
```

---

## Detailed Deployment Process

### Phase 1: Pre-Deployment Preparation

#### 1.1 Verify System Requirements

**Server Specifications:**
```
CPU: 2+ cores (4+ recommended)
RAM: 2+ GB (4+ GB recommended)
Disk: 50+ GB free space
Network: 100 Mbps+ connection
OS: Ubuntu 20.04 LTS or CentOS 8+
```

**Required Software:**
```bash
# Check PHP version
php --version          # Should be 8.3+

# Check MySQL version
mysql --version        # Should be 8.0+

# Check Composer
composer --version

# Check Git
git --version

# Check Apache/Nginx
apache2ctl -v          # If using Apache
nginx -v               # If using Nginx
```

#### 1.2 Create System User

```bash
# Create dedicated app user
sudo useradd -m -d /var/www/inventory_system app-user
sudo usermod -aG www-data app-user
sudo usermod -aG sudo app-user
```

#### 1.3 Backup Existing Data

```bash
# If upgrading from existing installation
sudo cp -r /var/www/inventory_system /var/www/inventory_system.backup.$(date +%Y%m%d)

# Backup database
mysqldump -u root -p inventory_system > /tmp/inventory_system.backup.sql
```

---

### Phase 2: Application Deployment

#### 2.1 Clone Repository

```bash
cd /var/www
git clone https://github.com/imqamarali/inventory_management.git
cd inventory_system
git checkout main  # Ensure on main branch
```

#### 2.2 Install Dependencies

```bash
# Install production dependencies only
composer install --no-dev --optimize-autoloader

# Update autoloader for production
composer dump-autoload --optimize --no-dev

# Verify installation
composer check-platform-reqs
```

#### 2.3 Run Optimization

```bash
# Make scripts executable
chmod +x scripts/*.sh

# Run optimization
./scripts/optimize-production.sh

# Review report
cat DEPLOYMENT_REPORT.txt
```

#### 2.4 Configure Environment

```bash
# Create environment file (if using)
cp .env.example .env

# Edit with production values
nano .env
```

Edit these values:
```env
APP_ENV=production
APP_DEBUG=false
DB_HOST=localhost
DB_NAME=inventory_system
DB_USER=app_user
DB_PASS=strong_password_here
APP_URL=https://your-domain.com
```

#### 2.5 Update Configuration Files

**config/web.php:**
```php
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'timeZone' => 'Asia/Karachi',  // ✓ Already set
    'bootstrap' => ['log'],
    'components' => [
        // ... components ...
    ],
];
```

**config/db.php:**
```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=inventory_system;charset=utf8mb4',
    'username' => 'app_user',
    'password' => 'strong_password',
    'charset' => 'utf8mb4',
    'on afterOpen' => function($event) {
        $event->sender->createCommand("SET time_zone = '+05:00'")->execute();
    },
];
```

---

### Phase 3: Database Setup

#### 3.1 Create Database

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE inventory_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create dedicated user
CREATE USER 'app_user'@'localhost' IDENTIFIED BY 'strong_password_here';

# Grant permissions
GRANT ALL PRIVILEGES ON inventory_system.* TO 'app_user'@'localhost';
FLUSH PRIVILEGES;

# Exit MySQL
exit
```

#### 3.2 Run Migrations

```bash
# Navigate to app directory
cd /var/www/inventory_system

# Run migrations
php yii migrate

# Verify tables created
mysql -u app_user -p inventory_system -e "SHOW TABLES;"
```

#### 3.3 Load Initial Data (Optional)

```bash
# If you have seed data
# mysql -u app_user -p inventory_system < data/seed.sql

# Or create first admin user
php yii user/create admin admin@example.com password
```

---

### Phase 4: Web Server Configuration

#### 4.1 Apache Configuration

**Create Virtual Host:**
```bash
sudo nano /etc/apache2/sites-available/inventory.conf
```

```apache
<VirtualHost *:80>
    ServerName inventory.yourdomain.com
    ServerAlias www.inventory.yourdomain.com
    DocumentRoot /var/www/inventory_system/web
    
    # Redirect HTTP to HTTPS
    Redirect permanent / https://inventory.yourdomain.com/

    <Directory /var/www/inventory_system/web>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Yii mod_rewrite rules
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^(.*)$ index.php?$1 [L,QSA]
        </IfModule>
    </Directory>

    <Directory /var/www/inventory_system>
        Require all denied
    </Directory>

    # Error and access logs
    ErrorLog ${APACHE_LOG_DIR}/inventory-error.log
    CustomLog ${APACHE_LOG_DIR}/inventory-access.log combined

    # Security headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>

# HTTPS Configuration
<VirtualHost *:443>
    ServerName inventory.yourdomain.com
    ServerAlias www.inventory.yourdomain.com
    DocumentRoot /var/www/inventory_system/web
    
    # SSL Certificate
    SSLEngine On
    SSLCertificateFile /etc/ssl/certs/your-cert.crt
    SSLCertificateKeyFile /etc/ssl/private/your-key.key
    SSLCertificateChainFile /etc/ssl/certs/your-chain.crt

    # ... rest of configuration same as above ...
</VirtualHost>
```

**Enable the site:**
```bash
sudo a2ensite inventory.conf
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2
```

#### 4.2 Nginx Configuration

**Create Virtual Host:**
```bash
sudo nano /etc/nginx/sites-available/inventory
```

```nginx
server {
    listen 80;
    server_name inventory.yourdomain.com www.inventory.yourdomain.com;
    
    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name inventory.yourdomain.com www.inventory.yourdomain.com;
    
    root /var/www/inventory_system/web;
    index index.php;

    # SSL Certificates
    ssl_certificate /etc/ssl/certs/your-cert.crt;
    ssl_certificate_key /etc/ssl/private/your-key.key;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 10240;
    gzip_proxied expired no-cache no-store private auth;
    gzip_types text/plain text/css text/xml text/javascript 
               application/x-javascript application/xml+rss 
               application/json application/javascript;
    gzip_disable "MSIE [1-6]\.";

    # PHP configuration
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Yii rules
    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    # Block access to sensitive files
    location ~ /\. {
        deny all;
    }

    location ~ ~$ {
        deny all;
    }

    # Access logs
    access_log /var/log/nginx/inventory-access.log;
    error_log /var/log/nginx/inventory-error.log;
}
```

**Enable the site:**
```bash
sudo ln -s /etc/nginx/sites-available/inventory /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```

---

### Phase 5: File Permissions & Security

#### 5.1 Set File Ownership

```bash
# Set correct ownership
sudo chown -R www-data:www-data /var/www/inventory_system
sudo chown -R www-data:www-data /var/www/inventory_system/runtime
sudo chown -R www-data:www-data /var/www/inventory_system/web/uploads
```

#### 5.2 Set Directory Permissions

```bash
# Directories: 755
find /var/www/inventory_system -type d -exec sudo chmod 755 {} \;

# Files: 644
find /var/www/inventory_system -type f -exec sudo chmod 644 {} \;

# Writable directories: 775
sudo chmod 775 /var/www/inventory_system/runtime
sudo chmod 775 /var/www/inventory_system/web/uploads
sudo chmod 775 /var/www/inventory_system/backups

# Scripts executable
sudo chmod 755 /var/www/inventory_system/scripts/*.sh
sudo chmod 755 /var/www/inventory_system/yii
```

#### 5.3 Protect Sensitive Files

```bash
# Restrict access to config directory
sudo chown root:www-data /var/www/inventory_system/config
sudo chmod 750 /var/www/inventory_system/config
sudo chmod 640 /var/www/inventory_system/config/*.php
```

---

### Phase 6: Backup & Monitoring Setup

#### 6.1 Configure Database Backups

```bash
# Add backup script to cron
sudo crontab -e

# Add this line for daily backups at 2 AM
0 2 * * * /var/www/inventory_system/scripts/backup-database.sh

# Weekly backups to cloud (optional)
0 3 * * 0 /usr/local/bin/backup-to-s3.sh
```

#### 6.2 Configure Health Checks

```bash
# Add health check to cron (every 30 minutes)
*/30 * * * * /var/www/inventory_system/scripts/health-check.sh

# Configure log rotation
sudo nano /etc/logrotate.d/inventory
```

```
/var/www/inventory_system/runtime/logs/*.log {
    daily
    rotate 7
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
    postrotate
        systemctl restart apache2 > /dev/null 2>&1 || true
    endscript
}
```

---

### Phase 7: Verification & Testing

#### 7.1 Run Health Check

```bash
/var/www/inventory_system/scripts/health-check.sh
```

#### 7.2 Test Access

```bash
# Test web access
curl -I https://inventory.yourdomain.com

# Should return 200 OK or 302 redirect

# Test login page
curl https://inventory.yourdomain.com/web/?r=site/login | grep -o "<title>.*</title>"
```

#### 7.3 Verify Functionality

- [ ] Access home page
- [ ] Login with admin credentials
- [ ] View dashboard
- [ ] Create test purchase order
- [ ] Upload payment proof
- [ ] Generate invoice PDF
- [ ] Check timezone (should be Asia/Karachi)

#### 7.4 Performance Testing

```bash
# Install Apache Bench
sudo apt-get install apache2-utils

# Run load test (100 requests, 10 concurrent)
ab -n 100 -c 10 https://inventory.yourdomain.com/

# Expected: Response time < 500ms
```

---

### Phase 8: Post-Deployment Tasks

#### 8.1 Configure SSL Certificate Renewal

```bash
# If using Let's Encrypt
sudo certbot renew --dry-run

# Auto-renew certificate
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

#### 8.2 Setup Monitoring & Alerts

```bash
# Install monitoring tool (optional)
sudo apt-get install monit

# Configure monit to restart services if down
sudo nano /etc/monit/monitrc
```

#### 8.3 Setup Log Aggregation (Optional)

```bash
# View application logs
tail -f /var/www/inventory_system/runtime/logs/app.log

# View access logs
tail -f /var/log/apache2/inventory-access.log

# View error logs
tail -f /var/log/apache2/inventory-error.log
```

#### 8.4 Create Admin Account

```bash
# If needed, create additional admin user
cd /var/www/inventory_system
# Use Yii command or web interface
```

---

## Troubleshooting

### 404 Errors

**Problem:** Getting 404 on all routes except `/web/index.php?r=...`

**Solution:**
```bash
# Enable mod_rewrite
sudo a2enmod rewrite

# Check .htaccess file exists
ls -la /var/www/inventory_system/web/.htaccess

# Verify Apache AllowOverride is set
grep "AllowOverride" /etc/apache2/sites-available/inventory.conf
```

### Database Connection Failed

**Problem:** Cannot connect to database

**Solution:**
```bash
# Verify MySQL is running
sudo systemctl status mysql

# Check credentials in config/db.php
mysql -u app_user -p inventory_system -e "SELECT 1;"

# Check database exists
mysql -u root -p -e "SHOW DATABASES;" | grep inventory_system
```

### Permission Denied

**Problem:** Permission denied errors in logs

**Solution:**
```bash
# Fix ownership
sudo chown -R www-data:www-data /var/www/inventory_system

# Fix permissions
sudo chmod 755 /var/www/inventory_system
sudo chmod 777 /var/www/inventory_system/runtime
sudo chmod 777 /var/www/inventory_system/web/uploads
```

### High Memory Usage

**Problem:** Server running out of memory

**Solution:**
```bash
# Increase PHP memory limit
sudo nano /etc/php/8.3/apache2/php.ini

# Find and update
memory_limit = 512M  # or higher

# Restart Apache
sudo systemctl restart apache2
```

---

## Rollback Procedure

If something goes wrong:

```bash
# 1. Stop web server
sudo systemctl stop apache2

# 2. Restore from backup
sudo rm -rf /var/www/inventory_system
sudo cp -r /var/www/inventory_system.backup.YYYYMMDD /var/www/inventory_system

# 3. Restore database
mysql -u root -p inventory_system < /tmp/inventory_system.backup.sql

# 4. Start services
sudo systemctl start apache2
sudo systemctl start mysql

# 5. Verify
./scripts/health-check.sh
```

---

## Support

For issues or questions:
1. Check logs: `/var/www/inventory_system/runtime/logs/app.log`
2. Run health check: `/var/www/inventory_system/scripts/health-check.sh`
3. Review deployment checklist: `DEPLOYMENT_CHECKLIST.md`
4. Check GitHub issues: https://github.com/imqamarali/inventory_management/issues

---

**Deployment completed! Your system is now live! 🎉**

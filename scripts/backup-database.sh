#!/bin/bash

##############################################################################
# INVENTORY MANAGEMENT SYSTEM - DATABASE BACKUP SCRIPT
#
# This script creates automated database backups
# Can be scheduled via cron for daily/weekly backups
#
# Usage: ./scripts/backup-database.sh
# Cron: 0 2 * * * /var/www/inventory_system/scripts/backup-database.sh
##############################################################################

set -e

# Configuration
BACKUP_DIR="/var/www/inventory_system/backups"
DB_HOST="localhost"
DB_NAME="inventory_system"
DB_USER="root"
RETENTION_DAYS=7  # Keep backups for 7 days
LOG_FILE="$BACKUP_DIR/backup.log"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Log function
log_message() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
}

# Get database password from environment or config
if [ -z "$DB_PASS" ]; then
    # Try to read from config file
    DB_PASS=$(grep "password" /var/www/inventory_system/config/db.php | grep -oP "'=>\s*'\K[^']*" | head -1)
fi

# Create backup file
BACKUP_FILE="$BACKUP_DIR/backup_$(date +%Y-%m-%d_%H-%M-%S).sql"

log_message "Starting database backup"
log_message "Database: $DB_NAME"
log_message "Backup file: $BACKUP_FILE"

# Create backup
if [ -n "$DB_PASS" ]; then
    mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE" 2>> "$LOG_FILE"
else
    mysqldump -h "$DB_HOST" -u "$DB_USER" "$DB_NAME" > "$BACKUP_FILE" 2>> "$LOG_FILE"
fi

# Compress backup
gzip "$BACKUP_FILE"
BACKUP_FILE_GZ="$BACKUP_FILE.gz"

# Check if backup was successful
if [ -f "$BACKUP_FILE_GZ" ]; then
    SIZE=$(du -h "$BACKUP_FILE_GZ" | cut -f1)
    log_message "Backup successful: $BACKUP_FILE_GZ ($SIZE)"
    echo -e "${GREEN}✓ Backup created: $BACKUP_FILE_GZ ($SIZE)${NC}"
else
    log_message "ERROR: Backup failed"
    echo -e "${RED}✗ Backup failed${NC}"
    exit 1
fi

# Upload to cloud storage (optional - AWS S3)
# Uncomment to enable S3 backup
# aws s3 cp "$BACKUP_FILE_GZ" s3://your-bucket/inventory-backups/ --region us-east-1 2>> "$LOG_FILE"

# Clean old backups (older than RETENTION_DAYS)
log_message "Cleaning old backups (older than $RETENTION_DAYS days)"
find "$BACKUP_DIR" -name "backup_*.sql.gz" -type f -mtime +$RETENTION_DAYS -delete

log_message "Backup completed successfully"

exit 0

#!/bin/bash

##############################################################################
# INVENTORY MANAGEMENT SYSTEM - HEALTH CHECK SCRIPT
#
# This script checks system health and reports issues
# Can be scheduled via cron for continuous monitoring
#
# Usage: ./scripts/health-check.sh
##############################################################################

set -e

# Configuration
APP_ROOT="/var/www/inventory_system"
LOG_FILE="$APP_ROOT/runtime/logs/health-check.log"
ALERT_EMAIL="admin@example.com"  # Change to your email

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Health status variables
HEALTH_STATUS="OK"
ISSUES=()

# Log function
log_message() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
}

# Alert function
send_alert() {
    local subject="$1"
    local message="$2"

    # Uncomment to send email alerts
    # echo "$message" | mail -s "$subject" "$ALERT_EMAIL"

    log_message "ALERT: $subject - $message"
}

log_message "=== Health Check Started ==="

# Check 1: Disk Space
echo -e "\n${YELLOW}Checking Disk Space...${NC}"
DISK_USAGE=$(df "$APP_ROOT" | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -gt 90 ]; then
    echo -e "${RED}✗ Disk usage critical: ${DISK_USAGE}%${NC}"
    HEALTH_STATUS="CRITICAL"
    ISSUES+=("Disk usage at ${DISK_USAGE}%")
    send_alert "Critical Disk Space" "Disk usage on $APP_ROOT is ${DISK_USAGE}%"
elif [ "$DISK_USAGE" -gt 75 ]; then
    echo -e "${YELLOW}⚠ Disk usage high: ${DISK_USAGE}%${NC}"
    ISSUES+=("Disk usage at ${DISK_USAGE}%")
else
    echo -e "${GREEN}✓ Disk usage normal: ${DISK_USAGE}%${NC}"
fi

# Check 2: Log File Size
echo -e "\n${YELLOW}Checking Log Files...${NC}"
if [ -f "$APP_ROOT/runtime/logs/app.log" ]; then
    LOG_SIZE=$(du -h "$APP_ROOT/runtime/logs/app.log" | cut -f1)
    LOG_LINES=$(wc -l < "$APP_ROOT/runtime/logs/app.log")

    # Get file size in MB
    LOG_SIZE_MB=$(du -m "$APP_ROOT/runtime/logs/app.log" | cut -f1)

    if [ "$LOG_SIZE_MB" -gt 100 ]; then
        echo -e "${YELLOW}⚠ Large log file: $LOG_SIZE (${LOG_LINES} lines)${NC}"
        ISSUES+=("Large log file: $LOG_SIZE")
    else
        echo -e "${GREEN}✓ Log file size normal: $LOG_SIZE (${LOG_LINES} lines)${NC}"
    fi
else
    echo -e "${GREEN}✓ No log file yet${NC}"
fi

# Check 3: Database Connection
echo -e "\n${YELLOW}Checking Database Connection...${NC}"
if command -v mysql &> /dev/null; then
    DB_HOST="localhost"
    DB_NAME="inventory_system"
    DB_USER="root"

    # Try to connect to database
    if mysql -h "$DB_HOST" -u "$DB_USER" -e "SELECT 1" "$DB_NAME" &> /dev/null; then
        echo -e "${GREEN}✓ Database connection OK${NC}"
    else
        echo -e "${RED}✗ Database connection failed${NC}"
        HEALTH_STATUS="CRITICAL"
        ISSUES+=("Database connection failed")
        send_alert "Database Connection Failed" "Cannot connect to $DB_NAME on $DB_HOST"
    fi
else
    echo -e "${YELLOW}⚠ MySQL client not found${NC}"
fi

# Check 4: File Permissions
echo -e "\n${YELLOW}Checking File Permissions...${NC}"
if [ -w "$APP_ROOT/runtime" ] && [ -w "$APP_ROOT/web/uploads" ]; then
    echo -e "${GREEN}✓ File permissions OK${NC}"
else
    echo -e "${RED}✗ Permission issues detected${NC}"
    HEALTH_STATUS="WARNING"
    ISSUES+=("File permission issues")
fi

# Check 5: Web Server
echo -e "\n${YELLOW}Checking Web Server...${NC}"
if curl -s -o /dev/null -w "%{http_code}" "http://localhost/inventory_system/web/" | grep -q "200\|302\|301"; then
    echo -e "${GREEN}✓ Web server responding${NC}"
else
    echo -e "${RED}✗ Web server not responding${NC}"
    HEALTH_STATUS="CRITICAL"
    ISSUES+=("Web server not responding")
    send_alert "Web Server Down" "Web server at localhost/inventory_system is not responding"
fi

# Check 6: Required Files
echo -e "\n${YELLOW}Checking Required Files...${NC}"
REQUIRED_FILES=(
    "$APP_ROOT/web/index.php"
    "$APP_ROOT/config/web.php"
    "$APP_ROOT/config/db.php"
    "$APP_ROOT/yii"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓ Found: $file${NC}"
    else
        echo -e "${RED}✗ Missing: $file${NC}"
        HEALTH_STATUS="CRITICAL"
        ISSUES+=("Missing file: $file")
    fi
done

# Check 7: Backup Status
echo -e "\n${YELLOW}Checking Backup Status...${NC}"
BACKUP_DIR="$APP_ROOT/backups"
if [ -d "$BACKUP_DIR" ]; then
    LATEST_BACKUP=$(ls -t "$BACKUP_DIR"/backup_*.sql.gz 2>/dev/null | head -1)
    if [ -n "$LATEST_BACKUP" ]; then
        BACKUP_AGE=$(( ($(date +%s) - $(stat -f%m "$LATEST_BACKUP" 2>/dev/null || date +%s)) / 3600 ))
        if [ "$BACKUP_AGE" -lt 25 ]; then
            echo -e "${GREEN}✓ Recent backup found (${BACKUP_AGE}h old)${NC}"
        else
            echo -e "${YELLOW}⚠ Latest backup is ${BACKUP_AGE}h old${NC}"
            ISSUES+=("Backup is ${BACKUP_AGE}h old")
        fi
    else
        echo -e "${RED}✗ No backups found${NC}"
        ISSUES+=("No backups found")
    fi
fi

# Check 8: Asset Cache
echo -e "\n${YELLOW}Checking Asset Cache...${NC}"
if [ -d "$APP_ROOT/web/assets" ] && [ "$(ls -A "$APP_ROOT/web/assets")" ]; then
    ASSETS_SIZE=$(du -sh "$APP_ROOT/web/assets" | cut -f1)
    echo -e "${GREEN}✓ Asset cache present: $ASSETS_SIZE${NC}"
else
    echo -e "${YELLOW}⚠ Asset cache empty (will regenerate on first request)${NC}"
fi

# Final Report
echo -e "\n${YELLOW}=== Health Check Report ===${NC}"
echo -e "Status: $HEALTH_STATUS"
echo -e "Timestamp: $(date)"
echo -e "Issues Found: ${#ISSUES[@]}"

if [ ${#ISSUES[@]} -gt 0 ]; then
    echo -e "\n${RED}Issues:${NC}"
    for issue in "${ISSUES[@]}"; do
        echo "  • $issue"
    done
fi

log_message "Health check completed with status: $HEALTH_STATUS"
log_message "=== Health Check Ended ==="

# Exit with appropriate code
if [ "$HEALTH_STATUS" = "CRITICAL" ]; then
    exit 1
else
    exit 0
fi

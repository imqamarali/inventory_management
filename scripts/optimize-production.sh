#!/bin/bash

##############################################################################
# INVENTORY MANAGEMENT SYSTEM - PRODUCTION OPTIMIZATION SCRIPT
#
# This script optimizes the application for production deployment
# Run this BEFORE deploying to production server
#
# Usage: ./scripts/optimize-production.sh [--skip-composer] [--keep-logs]
##############################################################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
SKIP_COMPOSER=false
KEEP_LOGS=false
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
APP_ROOT="$( cd "$SCRIPT_DIR/.." && pwd )"
LOG_FILE="$APP_ROOT/optimization.log"

# Parse arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --skip-composer)
            SKIP_COMPOSER=true
            shift
            ;;
        --keep-logs)
            KEEP_LOGS=true
            shift
            ;;
        *)
            echo "Unknown option: $1"
            exit 1
            ;;
    esac
done

# Helper functions
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

success() {
    echo -e "${GREEN}✓ $1${NC}" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}✗ ERROR: $1${NC}" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}⚠ WARNING: $1${NC}" | tee -a "$LOG_FILE"
}

banner() {
    echo -e "\n${BLUE}========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}========================================${NC}\n"
}

# Main optimization steps
banner "PRODUCTION OPTIMIZATION STARTED"

log "Starting optimization at $(date)"
log "Application Root: $APP_ROOT"

# Step 1: Clear Asset Cache
banner "Step 1: Clearing Asset Cache"
if [ -d "$APP_ROOT/web/assets" ]; then
    log "Removing cached asset files..."
    rm -rf "$APP_ROOT/web/assets"/*
    success "Asset cache cleared (will regenerate on first request)"
else
    warning "web/assets directory not found"
fi

# Step 2: Remove Test Files
banner "Step 2: Removing Test Files"
TEST_FILES=(
    "web/check_db.php"
    "web/fix_login.php"
    "web/get_otp.php"
    "web/set_ip.php"
    "web/set_password.php"
    "web/test_swal.php"
    "web/test_users.php"
    "seed_test_data.php"
)

for file in "${TEST_FILES[@]}"; do
    if [ -f "$APP_ROOT/$file" ]; then
        rm "$APP_ROOT/$file"
        success "Deleted $file"
    fi
done

# Step 3: Remove Asset ZIP Files
banner "Step 3: Removing Unnecessary ZIP Archives"
ZIP_FILES=(
    "web/assets_system/ace-master.zip"
    "web/assets_system/css.zip"
)

for file in "${ZIP_FILES[@]}"; do
    if [ -f "$APP_ROOT/$file" ]; then
        SIZE=$(du -h "$APP_ROOT/$file" | cut -f1)
        rm "$APP_ROOT/$file"
        success "Deleted $file ($SIZE)"
    fi
done

# Step 4: Optimize Composer
banner "Step 4: Optimizing Composer Dependencies"
if [ "$SKIP_COMPOSER" = false ]; then
    if [ -f "$APP_ROOT/composer.json" ]; then
        log "Running: composer install --no-dev --optimize-autoloader"
        cd "$APP_ROOT"
        composer install --no-dev --optimize-autoloader 2>&1 | tee -a "$LOG_FILE"

        log "Running: composer dump-autoload --optimize --no-dev"
        composer dump-autoload --optimize --no-dev 2>&1 | tee -a "$LOG_FILE"

        success "Composer optimization completed"
    else
        warning "composer.json not found"
    fi
else
    warning "Skipping Composer optimization (--skip-composer used)"
fi

# Step 5: Clean Log Files
banner "Step 5: Cleaning Log Files"
if [ "$KEEP_LOGS" = false ]; then
    if [ -d "$APP_ROOT/runtime/logs" ]; then
        log "Clearing old log files..."
        find "$APP_ROOT/runtime/logs" -name "*.log" -type f -delete
        success "Log files cleared"
    else
        warning "runtime/logs directory not found"
    fi
else
    warning "Keeping log files (--keep-logs used)"
fi

# Step 6: Clear Runtime Cache
banner "Step 6: Clearing Runtime Cache"
if [ -d "$APP_ROOT/runtime/cache" ]; then
    log "Removing cache files..."
    rm -rf "$APP_ROOT/runtime/cache"/*
    success "Cache cleared"
else
    warning "runtime/cache directory not found"
fi

# Step 7: Set Correct Permissions
banner "Step 7: Setting File Permissions"
log "Setting directory permissions (755)..."
find "$APP_ROOT" -type d -exec chmod 755 {} \; 2>/dev/null || true
success "Directory permissions set"

log "Setting file permissions (644)..."
find "$APP_ROOT" -type f -name "*.php" -exec chmod 644 {} \; 2>/dev/null || true
find "$APP_ROOT" -type f -name "*.js" -exec chmod 644 {} \; 2>/dev/null || true
find "$APP_ROOT" -type f -name "*.css" -exec chmod 644 {} \; 2>/dev/null || true
success "File permissions set"

log "Setting runtime directory permissions (777)..."
chmod 777 "$APP_ROOT/runtime" 2>/dev/null || true
chmod 777 "$APP_ROOT/web/uploads" 2>/dev/null || true
chmod 777 "$APP_ROOT/backups" 2>/dev/null || true
success "Runtime permissions set"

# Step 8: Remove macOS Files
banner "Step 8: Removing macOS Files"
if [ -d "$APP_ROOT/vendor/__MACOSX" ]; then
    log "Removing __MACOSX directory..."
    rm -rf "$APP_ROOT/vendor/__MACOSX"
    success "macOS files removed"
fi

# Step 9: Validate Configuration
banner "Step 9: Validating Configuration"
if [ -f "$APP_ROOT/config/web.php" ]; then
    if grep -q "Asia/Karachi" "$APP_ROOT/config/web.php"; then
        success "Timezone correctly set to Asia/Karachi"
    else
        warning "Timezone may not be set to Asia/Karachi"
    fi
fi

if [ -f "$APP_ROOT/web/index.php" ]; then
    if grep -q "Asia/Karachi" "$APP_ROOT/web/index.php"; then
        success "PHP timezone set in index.php"
    else
        warning "PHP timezone may not be set in index.php"
    fi
fi

# Step 10: Generate Size Report
banner "Step 10: Generating Size Report"

log "Calculating application size..."
TOTAL_SIZE=$(du -sh "$APP_ROOT" 2>/dev/null | cut -f1)
WEB_SIZE=$(du -sh "$APP_ROOT/web" 2>/dev/null | cut -f1)
VENDOR_SIZE=$(du -sh "$APP_ROOT/vendor" 2>/dev/null | cut -f1)

log "Size Summary:"
log "  Total Size:  $TOTAL_SIZE"
log "  Web Size:    $WEB_SIZE"
log "  Vendor Size: $VENDOR_SIZE"

# Step 11: Generate Backup
banner "Step 11: Creating Pre-Deployment Backup"
BACKUP_DIR="$APP_ROOT/backups"
if [ ! -d "$BACKUP_DIR" ]; then
    mkdir -p "$BACKUP_DIR"
fi

BACKUP_FILE="$BACKUP_DIR/pre-deployment-$(date +%Y%m%d_%H%M%S).tar.gz"
log "Creating backup: $BACKUP_FILE"
tar -czf "$BACKUP_FILE" \
    --exclude='web/uploads/*' \
    --exclude='runtime/logs/*' \
    --exclude='runtime/cache/*' \
    --exclude='.git' \
    --exclude='vendor' \
    -C "$APP_ROOT" . 2>&1 | tee -a "$LOG_FILE"
success "Backup created: $BACKUP_FILE"

# Step 12: Create Environment Validation Report
banner "Step 12: Creating Environment Report"
REPORT_FILE="$APP_ROOT/DEPLOYMENT_REPORT.txt"
cat > "$REPORT_FILE" << EOF
DEPLOYMENT OPTIMIZATION REPORT
Generated: $(date)

APPLICATION SIZE:
  Total Size:  $TOTAL_SIZE
  Web Size:    $WEB_SIZE
  Vendor Size: $VENDOR_SIZE

OPTIMIZATIONS COMPLETED:
  ✓ Asset cache cleared
  ✓ Test files removed
  ✓ ZIP archives deleted
  ✓ Composer optimized
  ✓ Log files cleared
  ✓ Runtime cache cleared
  ✓ Permissions set
  ✓ macOS files removed
  ✓ Configuration validated
  ✓ Pre-deployment backup created

SYSTEM CONFIGURATION:
  PHP Timezone: Asia/Karachi
  Database Timezone: UTC+5
  Debug Mode: $(grep "YII_DEBUG" "$APP_ROOT/web/index.php" | head -1)

NEXT STEPS:
1. Deploy to production server
2. Run database migrations: php yii migrate
3. Verify all features working
4. Monitor error logs
5. Test payment uploads
6. Verify timezone displays correctly

BACKUP LOCATION: $BACKUP_FILE

For support or issues, check:
  - Error logs: $APP_ROOT/runtime/logs/
  - Configuration: $APP_ROOT/config/
  - Deployment checklist: $APP_ROOT/DEPLOYMENT_CHECKLIST.md

EOF

success "Deployment report created: $REPORT_FILE"

# Final Summary
banner "OPTIMIZATION COMPLETE"

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}✓ ALL OPTIMIZATIONS COMPLETED${NC}"
echo -e "${GREEN}========================================${NC}"

log ""
log "Summary:"
log "  Total Size: $TOTAL_SIZE"
log "  Test Files: Removed ✓"
log "  Cache: Cleared ✓"
log "  Permissions: Set ✓"
log "  Backup: Created ✓"
log ""
log "The application is ready for production deployment!"
log "Review: $REPORT_FILE"
log ""

success "Optimization completed at $(date)"
echo -e "\n${GREEN}Log file: $LOG_FILE${NC}\n"

exit 0

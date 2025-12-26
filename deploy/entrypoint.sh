#!/bin/bash
set -e

# Color codes for output
readonly RED='\033[0;31m'
readonly GREEN='\033[0;32m'
readonly YELLOW='\033[1;33m'
readonly NC='\033[0m' # No Color

# Logging functions
log_info() {
    echo -e "${GREEN}✓${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}⚠${NC} $1"
}

log_error() {
    echo -e "${RED}✗${NC} $1"
    exit 1
}

# Function to generate random string
generate_random_key() {
    openssl rand -base64 32 | tr -d "=+/" | cut -c1-32
}

# Function to check database connection
check_database_connection() {
    local max_attempts=30
    local attempt=1
    
    log_info "Checking database connection..."
    
    while [ $attempt -le $max_attempts ]; do
        if php artisan db:show >/dev/null 2>&1; then
            log_info "Database connection successful"
            return 0
        fi
        
        log_warn "Database connection attempt $attempt/$max_attempts failed, retrying in 2s..."
        sleep 2
        attempt=$((attempt + 1))
    done
    
    log_error "Failed to connect to database after $max_attempts attempts"
}

# Function to validate environment
validate_environment() {
    log_info "Validating environment configuration..."
    
    # Check required environment variables
    local required_vars=("APP_NAME" "APP_ENV" "DB_CONNECTION" "DB_HOST" "DB_DATABASE")
    
    for var in "${required_vars[@]}"; do
        if [ -z "${!var}" ]; then
            log_error "Required environment variable $var is not set"
        fi
    done
    
    log_info "Environment validation passed"
}

# Function to generate APP_KEY if not set
ensure_app_key() {
    # Ensure .env file exists
    if [ ! -f .env ]; then
        log_info "Creating .env from .env.example..."
        cp .env.example .env
    fi
    
    # Check if APP_KEY is set in environment or .env file
    local current_key=$(grep "^APP_KEY=" .env 2>/dev/null | cut -d= -f2)
    
    if [ -z "$current_key" ] || [ "$current_key" = "base64:" ] || [ -z "$(echo $current_key | tr -d '[:space:]')" ]; then
        log_warn "APP_KEY not set, generating..."
        
        # Generate a new key
        php artisan key:generate --force --show > /tmp/app_key.txt
        NEW_KEY=$(cat /tmp/app_key.txt)
        rm -f /tmp/app_key.txt
        
        # Update .env file with the new key
        if grep -q "^APP_KEY=" .env; then
            sed -i "s|^APP_KEY=.*|APP_KEY=$NEW_KEY|" .env
        else
            echo "APP_KEY=$NEW_KEY" >> .env
        fi
        
        # Export the key to environment for current session
        export APP_KEY="$NEW_KEY"
        
        log_info "APP_KEY generated and configured: $NEW_KEY"
    else
        log_info "APP_KEY already configured"
    fi
}

# Function to setup storage links
setup_storage() {
    log_info "Setting up storage links..."
    
    # Ensure storage directories exist with proper permissions
    mkdir -p storage/framework/{sessions,views,cache}
    mkdir -p storage/logs
    mkdir -p storage/app/public
    
    # Create supervisor log directory
    mkdir -p /var/log/supervisor
    
    # Create symbolic link for public storage
    if [ ! -L public/storage ]; then
        php artisan storage:link
        log_info "Storage link created"
    else
        log_info "Storage link already exists"
    fi
}

# Function to run database migrations
run_migrations() {
    log_info "Running database migrations..."
    
    # Check if we should run migrations
    if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
        php artisan migrate --force --no-interaction
        log_info "Migrations completed successfully"
    else
        log_warn "Migrations skipped (RUN_MIGRATIONS=false)"
    fi
}

# Function to seed database
seed_database() {
    if [ "${RUN_SEEDERS:-false}" = "true" ]; then
        log_info "Seeding database..."
        php artisan db:seed --force --no-interaction
        log_info "Database seeding completed"
    fi
}

# Function to clear and cache configurations
optimize_application() {
    log_info "Optimizing application..."
    
    # IMPORTANT: Always clear config cache first to ensure environment variables are loaded
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    # Don't clear cache:clear during initialization as Redis might not be ready yet
    
    # Cache configurations for production (after clearing to apply env vars)
    if [ "$APP_ENV" = "production" ]; then
        # Temporarily switch to file cache driver for config:cache
        # This avoids Redis connection issues during initialization
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        php artisan filament:optimize
        log_info "Application optimized for production"
    else
        log_info "Application optimized for ${APP_ENV}"
    fi
}

# Function to set proper permissions
set_permissions() {
    log_info "Setting proper file permissions..."
    
    # Set ownership
    chown -R www-data:www-data storage bootstrap/cache
    
    # Set permissions
    chmod -R 775 storage bootstrap/cache
    
    log_info "Permissions set successfully"
}

# Function to create admin user if needed
create_admin_user() {
    if [ "${CREATE_ADMIN:-false}" = "true" ] && [ -n "$ADMIN_EMAIL" ]; then
        log_info "Creating admin user..."
        php artisan make:filament-user \
            --name="${ADMIN_NAME:-Admin}" \
            --email="${ADMIN_EMAIL}" \
            --password="${ADMIN_PASSWORD:-password}" \
            || log_warn "Admin user may already exist"
    fi
}

# Function to run queue worker
start_queue_worker() {
    if [ "${QUEUE_WORKER:-false}" = "true" ]; then
        log_info "Starting queue worker..."
        php artisan queue:work --daemon &
    fi
}

# Function to run scheduled tasks
start_scheduler() {
    if [ "${SCHEDULER:-false}" = "true" ]; then
        log_info "Starting task scheduler..."
        # Add cron job for Laravel scheduler
        echo "* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1" | crontab -
        crond
    fi
}

# Main execution flow
main() {
    echo "========================================"
    echo "  TaskFlow Application Initialization  "
    echo "========================================"
    echo ""
    
    # Ensure supervisor log directory exists before anything else
    mkdir -p /var/log/supervisor /var/run
    
    # Validate environment
    validate_environment
    
    # Ensure APP_KEY is set
    ensure_app_key
    
    # Wait for database if WAIT_FOR_DB is set
    if [ "${WAIT_FOR_DB:-true}" = "true" ]; then
        check_database_connection
    fi
    
    # Setup storage
    setup_storage
    
    # Run migrations
    run_migrations
    
    # Seed database if requested
    seed_database
    
    # Optimize application
    optimize_application
    
    # Set proper permissions
    set_permissions
    
    # Create admin user if needed
    create_admin_user
    
    # Start background services
    start_queue_worker
    start_scheduler
    
    log_info "Initialization completed successfully"
    echo ""
    echo "========================================"
    echo "  Starting Application Services...     "
    echo "========================================"
    echo ""
    
    # Execute the main command (passed as arguments)
    exec "$@"
}

# Run main function
main "$@"

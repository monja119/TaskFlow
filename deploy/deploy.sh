#!/bin/bash

# ============================================
# TaskFlow Deployment Script
# ============================================
# This script automates the deployment process
# for the TaskFlow Laravel Filament application
# ============================================

set -e

# Color codes
readonly RED='\033[0;31m'
readonly GREEN='\033[0;32m'
readonly YELLOW='\033[1;33m'
readonly BLUE='\033[0;34m'
readonly MAGENTA='\033[0;35m'
readonly CYAN='\033[0;36m'
readonly NC='\033[0m' # No Color

# Configuration
readonly SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
readonly PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
readonly ENV_FILE="${SCRIPT_DIR}/.env"
readonly DOCKER_COMPOSE_FILE="${SCRIPT_DIR}/docker-compose.yml"

# Logging functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_header() {
    echo -e "\n${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${CYAN} $1${NC}"
    echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"
}

# Check if .env file exists
check_env_file() {
    if [ ! -f "$ENV_FILE" ]; then
        log_error ".env file not found!"
        log_info "Creating .env file from .env.example..."
        
        if [ -f "${SCRIPT_DIR}/.env.example" ]; then
            cp "${SCRIPT_DIR}/.env.example" "$ENV_FILE"
            log_success ".env file created. Please configure it before deploying."
            exit 1
        else
            log_error ".env.example not found!"
            exit 1
        fi
    fi
}

# Load environment variables
load_env() {
    check_env_file
    set -a
    source "$ENV_FILE"
    set +a
    log_info "Environment variables loaded"
}

# Validate required environment variables
validate_env() {
    log_header "Validating Environment"
    
    local required_vars=(
        "APP_NAME"
        "APP_ENV"
        "DB_DATABASE"
        "DB_USERNAME"
        "DB_PASSWORD"
    )
    
    local missing_vars=()
    
    for var in "${required_vars[@]}"; do
        if [ -z "${!var}" ]; then
            missing_vars+=("$var")
        fi
    done
    
    if [ ${#missing_vars[@]} -gt 0 ]; then
        log_error "Missing required environment variables:"
        for var in "${missing_vars[@]}"; do
            echo "  - $var"
        done
        exit 1
    fi
    
    log_success "Environment validation passed"
}

# Validate Docker Compose configuration
validate_compose() {
    log_header "Validating Docker Compose"
    
    if ! docker compose -f "$DOCKER_COMPOSE_FILE" config > /dev/null 2>&1; then
        log_error "Docker Compose configuration is invalid"
        docker compose -f "$DOCKER_COMPOSE_FILE" config
        exit 1
    fi
    
    log_success "Docker Compose configuration is valid"
}

# Build Docker images
build_images() {
    log_header "Building Docker Images"
    
    cd "$SCRIPT_DIR"
    docker compose build --no-cache "$@"
    
    log_success "Docker images built successfully"
}

# Pull Docker images from registry
pull_images() {
    log_header "Pulling Docker Images"
    
    cd "$SCRIPT_DIR"
    docker compose pull
    
    log_success "Docker images pulled successfully"
}

# Start services
start_services() {
    log_header "Starting Services"
    
    cd "$SCRIPT_DIR"
    
    local profiles=""
    if [ "${QUEUE_WORKER:-false}" = "true" ]; then
        profiles="$profiles --profile with-queue"
    fi
    if [ "${SCHEDULER:-false}" = "true" ]; then
        profiles="$profiles --profile with-scheduler"
    fi
    
    docker compose $profiles up -d "$@"
    
    log_success "Services started successfully"
}

# Stop services
stop_services() {
    log_header "Stopping Services"
    
    cd "$SCRIPT_DIR"
    docker compose down
    
    log_success "Services stopped successfully"
}

# Restart services
restart_services() {
    log_header "Restarting Services"
    
    stop_services
    start_services "$@"
    
    log_success "Services restarted successfully"
}

# Show service status
show_status() {
    log_header "Service Status"
    
    cd "$SCRIPT_DIR"
    docker compose ps
}

# Show logs
show_logs() {
    local service="${1:-}"
    
    cd "$SCRIPT_DIR"
    
    if [ -z "$service" ]; then
        log_header "Showing All Logs"
        docker compose logs -f --tail=100
    else
        log_header "Showing Logs for: $service"
        docker compose logs -f --tail=100 "$service"
    fi
}

# Execute command in container
exec_command() {
    local service="${1:-app}"
    shift
    local command="$@"
    
    cd "$SCRIPT_DIR"
    docker compose exec "$service" $command
}

# Run artisan command
run_artisan() {
    log_info "Running artisan command: $@"
    exec_command app php artisan "$@"
}

# Deploy with build
deploy_with_build() {
    log_header "Deploying TaskFlow (Build Mode)"
    
    load_env
    validate_env
    validate_compose
    build_images
    start_services
    show_status
    
    log_success "Deployment completed successfully!"
}

# Deploy without build (pull from registry)
deploy_with_pull() {
    log_header "Deploying TaskFlow (Pull Mode)"
    
    load_env
    validate_env
    validate_compose
    pull_images
    start_services
    show_status
    
    log_success "Deployment completed successfully!"
}

# Cleanup all resources
cleanup() {
    log_header "Cleaning Up"
    
    read -p "⚠️  This will remove all containers, volumes, and images. Continue? (y/N): " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        cd "$SCRIPT_DIR"
        
        log_info "Stopping services..."
        docker compose down -v
        
        log_info "Removing images..."
        docker compose down --rmi all
        
        log_info "Pruning system..."
        docker system prune -af --volumes
        
        log_success "Cleanup completed"
    else
        log_info "Cleanup cancelled"
    fi
}

# Backup database
backup_database() {
    log_header "Backing Up Database"
    
    load_env
    
    local backup_dir="${SCRIPT_DIR}/backups"
    local backup_file="${backup_dir}/db_backup_$(date +%Y%m%d_%H%M%S).sql"
    
    mkdir -p "$backup_dir"
    
    cd "$SCRIPT_DIR"
    docker compose exec -T db mysqldump \
        -u"${DB_USERNAME}" \
        -p"${DB_PASSWORD}" \
        "${DB_DATABASE}" > "$backup_file"
    
    log_success "Database backed up to: $backup_file"
}

# Restore database
restore_database() {
    local backup_file="$1"
    
    if [ -z "$backup_file" ] || [ ! -f "$backup_file" ]; then
        log_error "Backup file not found: $backup_file"
        exit 1
    fi
    
    log_header "Restoring Database"
    
    load_env
    
    read -p "⚠️  This will overwrite the current database. Continue? (y/N): " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        cd "$SCRIPT_DIR"
        docker compose exec -T db mysql \
            -u"${DB_USERNAME}" \
            -p"${DB_PASSWORD}" \
            "${DB_DATABASE}" < "$backup_file"
        
        log_success "Database restored from: $backup_file"
    else
        log_info "Restore cancelled"
    fi
}

# Health check
health_check() {
    log_header "Health Check"
    
    cd "$SCRIPT_DIR"
    
    local services=("db" "redis" "app")
    
    for service in "${services[@]}"; do
        if docker compose ps "$service" | grep -q "Up"; then
            local health=$(docker compose ps "$service" --format json | jq -r '.[0].Health')
            
            if [ "$health" = "healthy" ] || [ -z "$health" ]; then
                log_success "$service: Running"
            else
                log_warn "$service: Running but not healthy"
            fi
        else
            log_error "$service: Not running"
        fi
    done
}

# Update application
update() {
    log_header "Updating Application"
    
    pull_images
    restart_services
    run_artisan migrate --force
    run_artisan optimize:clear
    run_artisan optimize
    
    log_success "Application updated successfully"
}

# Show usage
show_usage() {
    cat << EOF
${CYAN}TaskFlow Deployment Script${NC}

${YELLOW}Usage:${NC}
  $0 <command> [options]

${YELLOW}Commands:${NC}
  ${GREEN}deploy${NC}              Deploy with images from registry
  ${GREEN}deploy-build${NC}        Build images locally and deploy
  ${GREEN}start${NC}               Start all services
  ${GREEN}stop${NC}                Stop all services
  ${GREEN}restart${NC}             Restart all services
  ${GREEN}status${NC}              Show service status
  ${GREEN}logs${NC} [service]      Show logs (all or specific service)
  ${GREEN}health${NC}              Run health check on all services
  ${GREEN}build${NC}               Build Docker images
  ${GREEN}pull${NC}                Pull images from registry
  ${GREEN}validate${NC}            Validate configuration files
  ${GREEN}cleanup${NC}             Remove all containers, volumes, and images
  ${GREEN}backup-db${NC}           Backup database
  ${GREEN}restore-db${NC} <file>   Restore database from backup
  ${GREEN}update${NC}              Update application (pull + migrate)
  ${GREEN}artisan${NC} <command>   Run artisan command
  ${GREEN}exec${NC} <service> <cmd> Execute command in container
  ${GREEN}help${NC}                Show this help message

${YELLOW}Examples:${NC}
  $0 deploy-build                 # Build and deploy
  $0 logs app                     # Show app logs
  $0 artisan migrate              # Run migrations
  $0 exec app bash                # Open bash in app container
  $0 backup-db                    # Backup database
  $0 restore-db backups/db.sql    # Restore database

EOF
}

# Main function
main() {
    local command="${1:-help}"
    shift || true
    
    case "$command" in
        deploy)
            deploy_with_pull "$@"
            ;;
        deploy-build)
            deploy_with_build "$@"
            ;;
        start)
            load_env
            start_services "$@"
            ;;
        stop)
            stop_services
            ;;
        restart)
            load_env
            restart_services "$@"
            ;;
        status)
            show_status
            ;;
        logs)
            show_logs "$@"
            ;;
        health)
            health_check
            ;;
        build)
            load_env
            build_images "$@"
            ;;
        pull)
            load_env
            pull_images
            ;;
        validate)
            load_env
            validate_env
            validate_compose
            ;;
        cleanup)
            cleanup
            ;;
        backup-db)
            backup_database
            ;;
        restore-db)
            restore_database "$@"
            ;;
        update)
            update
            ;;
        artisan)
            load_env
            run_artisan "$@"
            ;;
        exec)
            exec_command "$@"
            ;;
        help|--help|-h)
            show_usage
            ;;
        *)
            log_error "Unknown command: $command"
            echo ""
            show_usage
            exit 1
            ;;
    esac
}

# Run main function
main "$@"

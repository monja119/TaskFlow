.PHONY: help deploy deploy-build start stop restart status logs health build validate clean backup restore

# Variables
DEPLOY_DIR := deploy
COMPOSE := cd $(DEPLOY_DIR) && docker compose
DEPLOY_SCRIPT := $(DEPLOY_DIR)/deploy.sh

# Colors for output
GREEN := \033[0;32m
YELLOW := \033[1;33m
NC := \033[0m

help: ## Show this help message
	@echo "$(GREEN)TaskFlow Deployment Commands$(NC)"
	@echo ""
	@echo "Usage: make [target]"
	@echo ""
	@echo "Available targets:"
	@awk 'BEGIN {FS = ":.*##"; printf ""} /^[a-zA-Z_-]+:.*?##/ { printf "  $(GREEN)%-15s$(NC) %s\n", $$1, $$2 }' $(MAKEFILE_LIST)
	@echo ""
	@echo "For more commands, run: $(YELLOW)./deploy/deploy.sh help$(NC)"

deploy: ## Deploy with pre-built images from registry
	@echo "$(GREEN)Deploying with registry images...$(NC)"
	@$(DEPLOY_SCRIPT) deploy

deploy-build: ## Build locally and deploy
	@echo "$(GREEN)Building and deploying...$(NC)"
	@$(DEPLOY_SCRIPT) deploy-build

start: ## Start all services
	@echo "$(GREEN)Starting services...$(NC)"
	@$(DEPLOY_SCRIPT) start

stop: ## Stop all services
	@echo "$(YELLOW)Stopping services...$(NC)"
	@$(DEPLOY_SCRIPT) stop

restart: ## Restart all services
	@echo "$(YELLOW)Restarting services...$(NC)"
	@$(DEPLOY_SCRIPT) restart

status: ## Show service status
	@$(DEPLOY_SCRIPT) status

logs: ## Show all logs (use logs-app, logs-db, etc. for specific services)
	@$(DEPLOY_SCRIPT) logs

logs-app: ## Show application logs
	@$(DEPLOY_SCRIPT) logs app

logs-db: ## Show database logs
	@$(DEPLOY_SCRIPT) logs db

logs-redis: ## Show Redis logs
	@$(DEPLOY_SCRIPT) logs redis

health: ## Run health check
	@$(DEPLOY_SCRIPT) health

build: ## Build Docker images
	@echo "$(GREEN)Building Docker images...$(NC)"
	@$(DEPLOY_SCRIPT) build

validate: ## Validate configuration
	@echo "$(GREEN)Validating configuration...$(NC)"
	@$(DEPLOY_SCRIPT) validate

clean: ## Clean all resources (containers, volumes, images)
	@echo "$(YELLOW)Cleaning resources...$(NC)"
	@$(DEPLOY_SCRIPT) cleanup

backup: ## Backup database
	@echo "$(GREEN)Backing up database...$(NC)"
	@$(DEPLOY_SCRIPT) backup-db

shell: ## Open bash shell in app container
	@$(COMPOSE) exec app bash

shell-db: ## Open MySQL shell
	@$(COMPOSE) exec db mysql -uroot -p

artisan: ## Run artisan command (usage: make artisan CMD="migrate")
	@$(DEPLOY_SCRIPT) artisan $(CMD)

migrate: ## Run database migrations
	@$(DEPLOY_SCRIPT) artisan migrate --force

migrate-fresh: ## Fresh migration with seed
	@$(DEPLOY_SCRIPT) artisan migrate:fresh --seed --force

seed: ## Seed database
	@$(DEPLOY_SCRIPT) artisan db:seed --force

optimize: ## Optimize Laravel application
	@$(DEPLOY_SCRIPT) artisan optimize

cache-clear: ## Clear all caches
	@$(DEPLOY_SCRIPT) artisan cache:clear
	@$(DEPLOY_SCRIPT) artisan config:clear
	@$(DEPLOY_SCRIPT) artisan route:clear
	@$(DEPLOY_SCRIPT) artisan view:clear

update: ## Update application (pull + migrate)
	@echo "$(GREEN)Updating application...$(NC)"
	@$(DEPLOY_SCRIPT) update

ps: ## Show running containers
	@$(COMPOSE) ps

top: ## Show container resource usage
	@docker stats

prune: ## Prune unused Docker resources
	@docker system prune -af --volumes

setup: ## Initial setup (copy .env.example)
	@if [ ! -f $(DEPLOY_DIR)/.env ]; then \
		cp $(DEPLOY_DIR)/.env.example $(DEPLOY_DIR)/.env; \
		echo "$(GREEN)Created .env file. Please configure it before deploying.$(NC)"; \
	else \
		echo "$(YELLOW).env file already exists$(NC)"; \
	fi

test: ## Run tests
	@docker compose exec app php artisan test

test-coverage: ## Run tests with coverage
	@docker compose exec app php artisan test --coverage

install: ## Install dependencies in container
	@$(COMPOSE) exec app composer install
	@$(COMPOSE) exec app npm install

dev: ## Start development environment
	@$(COMPOSE) --profile with-queue --profile with-scheduler up -d

prod: ## Start production environment
	@$(DEPLOY_SCRIPT) deploy

down: ## Stop and remove all containers
	@$(COMPOSE) down

down-volumes: ## Stop and remove all containers and volumes
	@$(COMPOSE) down -v

watch-logs: ## Watch logs in real-time
	@$(COMPOSE) logs -f

key-generate: ## Generate new APP_KEY
	@$(COMPOSE) exec app php artisan key:generate

filament-user: ## Create Filament admin user (interactive)
	@$(COMPOSE) exec app php artisan make:filament-user

queue-work: ## Start queue worker
	@$(COMPOSE) exec app php artisan queue:work

queue-restart: ## Restart queue workers
	@$(COMPOSE) exec app php artisan queue:restart

.DEFAULT_GOAL := help

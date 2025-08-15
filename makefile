# RentalCarFlow - Makefile
# Project: Car Rental Management System
# Author: Abdelali Majri

# Colors for output
RED=\033[0;31m
GREEN=\033[0;32m
YELLOW=\033[1;33m
BLUE=\033[0;34m
NC=\033[0m # No Color

# Docker compose file
DOCKER_COMPOSE=docker-compose
DOCKER_COMPOSE_FILE=docker-compose.yml

# Services
PHP_SERVICE=php
NGINX_SERVICE=nginx
POSTGRES_SERVICE=postgres
ADMINER_SERVICE=adminer

# Default target
.DEFAULT_GOAL := help

##@ Help
help: ## Display this help message
	@echo "$(GREEN)RentalCarFlow - Available Commands$(NC)"
	@echo "=================================="
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z_0-9-]+:.*?##/ { printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Docker Management
build: ## Build Docker containers
	@echo "$(BLUE)Building Docker containers...$(NC)"
	$(DOCKER_COMPOSE) build --no-cache


up: ## Start all services
	@echo "$(GREEN)Starting all services...$(NC)"
	$(DOCKER_COMPOSE) up -d

down: ## Stop all services
	@echo "$(RED)Stopping all services...$(NC)"
	$(DOCKER_COMPOSE) down

restart: down up ## Restart all services

logs-assets:
	@echo "👀 Logs de surveillance des assets:"
	docker-compose logs -f asset-watcher

compile-assets:
	@echo "📦 Compilation manuelle des assets..."
	docker-compose exec asset-watcher php bin/console asset-map:compile

shell-watcher:
	@echo "👀 Connexion au container asset-watcher..."
	docker-compose exec asset-watcher bash

rebuild: down build up ## Rebuild and restart all services

logs: ## Show logs for all services
	$(DOCKER_COMPOSE) logs -f

logs-php: ## Show PHP container logs
	$(DOCKER_COMPOSE) logs -f $(PHP_SERVICE)

logs-nginx: ## Show Nginx container logs
	$(DOCKER_COMPOSE) logs -f $(NGINX_SERVICE)

logs-db: ## Show PostgreSQL container logs
	$(DOCKER_COMPOSE) logs -f $(POSTGRES_SERVICE)

status: ## Show containers status
	$(DOCKER_COMPOSE) ps

##@ Application Management
install: ## Install/reinstall Symfony application
	@echo "$(BLUE)Installing Symfony application...$(NC)"
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) composer install
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console doctrine:database:create --if-not-exists
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console doctrine:migrations:migrate --no-interaction
	@echo "$(GREEN)Installation completed!$(NC)"

update: ## Update Composer dependencies
	@echo "$(BLUE)Updating dependencies...$(NC)"
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) composer update

require: ## Install a new package (use: make require PACKAGE=package/name)
	@echo "$(BLUE)Installing package: $(PACKAGE)$(NC)"
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) composer require $(PACKAGE)

require-dev: ## Install a dev package (use: make require-dev PACKAGE=package/name)
	@echo "$(BLUE)Installing dev package: $(PACKAGE)$(NC)"
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) composer require --dev $(PACKAGE)

##@ Database Management
db-create: ## Create database
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console doctrine:database:create --if-not-exists

db-drop: ## Drop database
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console doctrine:database:drop --force --if-exists

db-reset: db-drop db-create ## Reset database (drop and recreate)

db-migrate: ## Run database migrations
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console doctrine:migrations:migrate --no-interaction


db-diff: ## Run database migrations
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console doctrine:migrations:diff --no-interaction

db-rollback: ## Rollback last migration
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console doctrine:migrations:migrate prev --no-interaction

db-fixtures: ## Load database fixtures
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console hautelook:fixtures:load --no-interaction

db-schema-update: ## Update database schema
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console doctrine:schema:update --force

db-backup: ## Backup database
	@echo "$(BLUE)Creating database backup...$(NC)"
	docker exec rental-car-flow-postgres pg_dump -U rental_user rental_car_flow_db > backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "$(GREEN)Database backup created!$(NC)"


# Code Style
cs-check: ## Check code style (dry-run)
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) vendor/bin/php-cs-fixer fix --dry-run --diff --verbose

cs-fix: ## Fix code style
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) vendor/bin/php-cs-fixer fix --verbose

##@ Code Generation
make-entity: ## Create new entity (use: make make-entity NAME=EntityName)
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console make:entity $(NAME)

make-controller: ## Create new controller (use: make make-controller NAME=ControllerName)
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console make:controller $(NAME)

make-form: ## Create new form (use: make make-form NAME=FormName)
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console make:form $(NAME)

make-crud: ## Create CRUD for entity (use: make make-crud NAME=EntityName)
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console make:crud $(NAME)

make-migration: ## Create new migration
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console make:migration

make-user: ## Create user entity
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console make:user

make-auth: ## Create authentication system
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console make:auth

##@ Testing
test: ## Run all tests
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/phpunit

test-unit: ## Run unit tests
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/phpunit tests/Unit

test-functional: ## Run functional tests
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/phpunit tests/Functional

test-coverage: ## Run tests with coverage
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/phpunit --coverage-html coverage

##@ Code Quality
cs-fix: ## Fix code style with PHP CS Fixer
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) vendor/bin/php-cs-fixer fix

cs-check: ## Check code style
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) vendor/bin/php-cs-fixer fix --dry-run --diff

phpstan: ## Run PHPStan analysis
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) vendor/bin/phpstan analyse

##@ Cache Management
cache-clear: ## Clear application cache
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console cache:clear

cache-warmup: ## Warmup application cache
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console cache:warmup

##@ Assets Management
assets-install: ## Install assets
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console assets:install

assets-build: ## Build assets (if using Webpack Encore)
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) npm run build

assets-watch: ## Watch assets for changes
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) npm run watch

##@ Development
shell: ## Access PHP container shell
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) bash

bash: ## Alias for shell - Access PHP container bash
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) bash

shell-db: ## Access PostgreSQL shell
	$(DOCKER_COMPOSE) exec $(POSTGRES_SERVICE) psql -U rental_user -d rental_car_flow_db

debug-router: ## Show all routes
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console debug:router

debug-container: ## Show all services
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console debug:container

about: ## Show Symfony information
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console about


##@ URLs
urls: ## Show all application URLs
	@echo "$(GREEN)RentalCarFlow URLs:$(NC)"
	@echo "$(BLUE)Application:$(NC) http://localhost:8080"
	@echo "$(BLUE)Database Admin (Adminer):$(NC) http://localhost:8081"
	@echo "$(BLUE)Database Connection:$(NC)"
	@echo "  - Server: postgres"
	@echo "  - Username: rental_user"
	@echo "  - Password: rental_pass"
	@echo "  - Database: rental_car_flow_db"

##@ Cleanup
clean: ## Clean up containers and volumes
	$(DOCKER_COMPOSE) down -v --remove-orphans
	docker system prune -f

clean-all: ## Clean everything including images
	$(DOCKER_COMPOSE) down -v --remove-orphans --rmi all
	docker system prune -a -f

.PHONY: help build up down restart rebuild logs logs-php logs-nginx logs-db status install update require require-dev db-create db-drop db-reset db-migrate db-rollback db-fixtures db-schema-update db-backup make-entity make-controller make-form make-crud make-migration make-user make-auth test test-unit test-functional test-coverage cs-fix cs-check phpstan cache-clear cache-warmup assets-install assets-build assets-watch shell shell-db debug-router debug-container about setup-entities setup-crud git-status git-add git-commit git-push deploy urls clean clean-all
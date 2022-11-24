containerName = "symfony-rest-php"
isContainerRunning := $(shell docker info > /dev/null 2>&1 && docker ps | grep $(containerName) > /dev/null 2>&1 && echo 1)
user := $(shell id -u)
group := $(shell id -g)

DOCKER :=
DOCKER_TEST := APP_ENV=test
DOCKER_COMPOSE := USER_ID=$(user) GROUP_ID=$(group) docker compose

PHP := $(DOCKER) php
PHP_TEST := $(DOCKER_TEST) php
CONSOLE := $(DOCKER) php bin/console
CONSOLE_TEST := $(DOCKER_TEST) php bin/console
COMPOSER = $(DOCKER) composer

ifeq ($(isContainerRunning), 1)
	DOCKER := @docker exec -t -u $(user):$(group) $(containerName)
	DOCKER_TEST := @docker exec -t -u $(user):$(group) $(containerName) APP_ENV=test
endif

## —— App ————————————————————————————————————————————————————————————————
network:
	@docker network create symfony_rest_network || true

build-docker: env network
	@docker-compose pull --ignore-pull-failures
	@docker-compose build --no-cache

up:
	@echo "Launching containers from project..."
	$(DOCKER_COMPOSE) up -d
	$(DOCKER_COMPOSE) ps

build-up: build-docker up

stop:
	@echo "Stopping containers from project..."
	$(DOCKER_COMPOSE) stop
	$(DOCKER_COMPOSE) ps

prune: stop
	$(DOCKER_COMPOSE) down --volumes
	$(DOCKER_COMPOSE) down --remove-orphans
	$(DOCKER_COMPOSE) rm -f

serve:
	$(DOCKER) symfony serve -d

bash:
	@docker exec -it $(containerName) bash

install-project: install reset-database generate-jwt ## First installation for setup the project

update-project: install reset-database ## update the project after a checkout on another branch or to reset the state of the project

sync: update-project test-all ## Synchronize the project with the current branch, install composer dependencies, drop DB and run all migrations, fixtures and all test

## —— 🐝 The Symfony Makefile 🐝 ——————————————————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?## .*$$)|(^## )' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Utils ——————————————————————————————————————————————————————————————————————
env:
	cp .env.dist .env

## —— Composer 🧙‍♂️ ————————————————————————————————————————————————————————————
install: composer.lock ## Install vendors according to the current composer.lock file
	$(COMPOSER) install -n

update: composer.json ## Update vendors according to the composer.json file
	$(COMPOSER) update -w

## —— Symfony ————————————————————————————————————————————————————————————————
sf:
	$(CONSOLE) $c

cc: ## Apply cache clear
	$(DOCKER) sh -c "rm -rf var/cache"
	$(CONSOLE) cache:clear
	$(DOCKER) sh -c "chmod -R 777 var/cache"

cc-test: ## Apply cache clear
	$(DOCKER) sh -c "rm -rf var/cache"
	$(CONSOLE_TEST) cache:clear
	$(DOCKER) sh -c "chmod -R 777 var/cache"

doctrine-validate:
	$(CONSOLE) doctrine:schema:validate --skip-sync $c

reset-database: drop-database database migrate # load-fixtures ## Reset database with migration

database: ## Create database if no exists
	$(CONSOLE) doctrine:database:create --if-not-exists

drop-database: ## Drop the database
	$(CONSOLE) doctrine:database:drop --force --if-exists

migration: ## Apply doctrine migration
	$(CONSOLE) make:migration

migrate: ## Apply doctrine migrate
	$(CONSOLE) doctrine:migration:migrate -n --all-or-nothing

generate-jwt: ## Generate private and public keys
	$(CONSOLE) lexik:jwt:generate-keypair --overwrite -q $c

## —— Tests ✅ ————————————————————————————————————————————————————————————
test-database: ### load database schema
	$(CONSOLE_TEST) doctrine:database:drop --if-exists --force
	$(CONSOLE_TEST) doctrine:database:create --if-not-exists
	$(CONSOLE_TEST) doctrine:migration:migrate -n --all-or-nothing

pest:
	$(PHP_TEST) ./vendor/bin/pest

pest-all: phpunit.xml* test-database
	$(PHP_TEST) ./vendor/bin/pest

test: phpunit.xml* ## Launch main functional and unit tests, stopped on failure
	$(PHP) ./vendor/bin/pest --stop-on-failure $c

test-all: phpunit.xml* test-database ## Launch main functional and unit tests
	$(PHP) ./vendor/bin/pest

test-report: phpunit.xml* test-load-fixtures ## Launch main functional and unit tests with report
	$(PHP) ./vendor/bin/pest --coverage-text --colors=never --log-junit report.xml $c

## —— Coding standards ✨ ——————————————————————————————————————————————————————
stan: ## Run PHPStan only
	$(PHP) ./vendor/bin/phpstan analyse -l 9 src --no-progress -c phpstan.neon --memory-limit 256M

ecs: ## Run ECS only
	$(PHP) ./vendor/bin/ecs check src --memory-limit 256M

ecs-fix: ## Run php-cs-fixer and fix the code.
	$(PHP) ./vendor/bin/ecs check src --fix --memory-limit 256M

# Help
TARGETS:=$(MAKEFILE_LIST)

.PHONY: help
help: ## This help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(TARGETS) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# Tests

.PHONY: test unit-test mutation_test
test: ui-test integration-test unit-test mutation_test coverage cs phpstan

ui-test: ## Run ui tests
	php bin/phpunit --group=ui

integration-test: ## Run integration tests
	php bin/phpunit --group=integration

unit-test: ## Run unit tests
	php bin/phpunit --testdox --group=unit

mutation_test: ## Run mutation tests
	php bin/infection

coverage: ## Run test coverage
	php bin/phpunit --exclude-group=ui --coverage-text --coverage-clover ./build/logs/clover.xml --coverage-xml=build/coverage/coverage-xml --log-junit=build/coverage/phpunit.junit.xml

.PHONY: check_security
check_security: ## Check for dependency vulnerabilities
	curl -H "Accept: text/plain" https://security.sensiolabs.org/check_lock -F lock=@composer.lock

# Coding Style

.PHONY: cs cs-fix cs-ci
cs: ## Check code style
	./bin/php-cs-fixer fix --dry-run --stop-on-violation --diff

cs-fix: ## Fix code style
	./bin/php-cs-fixer fix

cs-ci: ## Run Continuous Integration code style check
	./bin/php-cs-fixer fix --dry-run --using-cache=no --verbose

# Static Analysis

.PHONY: phpstan
phpstan: ## Check static analysis
	./bin/phpstan analyse src tests --level=max

# Fooscore

.PHONY: start
start: ## Starts the server
	php bin/console server:start 127.0.0.1:8080

.PHONY: stop
stop: ## Stop the server
	php bin/console server:stop

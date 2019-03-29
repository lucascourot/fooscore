# Help
TARGETS:=$(MAKEFILE_LIST)

.PHONY: help
help: ## This help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(TARGETS) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# Tests

.PHONY: test unit-test coverage property-test mutation-test
test: cs phpstan coverage
test-ci: test ui-test property-test mutation-test check_security

ui-test: vendor ## Run ui tests
	php bin/phpunit --testdox --group=ui

integration-test: vendor ## Run integration tests
	php bin/phpunit --testdox --group=integration

unit-test: vendor ## Run unit tests
	php bin/phpunit --testdox --group=unit

property-test: vendor ## Run property-based tests (PBT)
	php bin/phpunit --testdox --group=property

mutation-test: vendor ## Run mutation tests
	php bin/infection --test-framework-options="--exclude-group=property,ui"

coverage: vendor ## Run test coverage on unit and integration layers
	php bin/phpunit --exclude-group=property,ui --coverage-text --coverage-clover ./build/logs/clover.xml --coverage-xml=build/coverage/coverage-xml --log-junit=build/coverage/phpunit.junit.xml

.PHONY: check_security
check_security: ## Check for dependency vulnerabilities
	curl -H "Accept: text/plain" https://security.symfony.com/check_lock -F lock=@composer.lock

# Coding Style

.PHONY: cs cs-fix cs-ci
cs: vendor ## Check code style
	./bin/phpcs

cs-fix: vendor ## Fix code style
	./bin/phpcbf

cs-ci: vendor ## Run Continuous Integration code style check
	./bin/phpcs

# Static Analysis

.PHONY: phpstan
phpstan: vendor ## Check static analysis
	./bin/phpstan analyse src tests --level=max

# Fooscore

.PHONY: install
install: vendor

vendor: composer.json composer.lock
	composer install

.PHONY: start
start: .web-server-pid

.web-server-pid: vendor ## Starts the server
	php bin/console server:start 127.0.0.1:8080

.PHONY: stop
stop: vendor ## Stop the server
	php bin/console server:stop

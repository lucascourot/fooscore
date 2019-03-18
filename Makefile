# Help
TARGETS:=$(MAKEFILE_LIST)

.PHONY: help
help: ## This help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(TARGETS) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# Tests

.PHONY: test unit-test mutation_test
test: cs phpstan ui-test coverage mutation_test

ui-test: vendor ## Run ui tests
	php bin/phpunit --group=ui

integration-test: vendor ## Run integration tests
	php bin/phpunit --group=integration

unit-test: vendor ## Run unit tests
	php bin/phpunit --testdox --group=unit

mutation_test: vendor ## Run mutation tests
	php bin/infection --threads=4

coverage: vendor ## Run test coverage
	php bin/phpunit --exclude-group=ui --coverage-text --coverage-clover ./build/logs/clover.xml --coverage-xml=build/coverage/coverage-xml --log-junit=build/coverage/phpunit.junit.xml

.PHONY: check_security
check_security: ## Check for dependency vulnerabilities
	curl -H "Accept: text/plain" https://security.sensiolabs.org/check_lock -F lock=@composer.lock

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

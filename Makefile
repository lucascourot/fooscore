# Help
TARGETS:=$(MAKEFILE_LIST)

.PHONY: help
help: ## This help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(TARGETS) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# Fooscore

.PHONY: start
start: ## Starts the server
	php -S 127.0.0.1:8080 public/index.html

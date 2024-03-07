.DEFAULT_GOAL := help

.PHONY: help
help: ## Show help information
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

.PHONY: deps
deps: ## Install dependencies and Git hooks
	composer install
	./vendor/bin/captainhook install -f

.PHONY: lint
lint: ## Code analyse and lint
	composer run-script lint

.PHONY: test
test: ## Run tests
	composer run-script test

.PHONY: clean
clean: ## Clean up cache files
	rm -rf .pytest_cache/ vendor/

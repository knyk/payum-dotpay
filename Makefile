.PHONY: help

help: ## This help.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.DEFAULT_GOAL := help

VOLUMES=-v $(PWD):/var/www/html -v $(PWD)/docker/php.ini:/usr/local/etc/php/php.ini

fix-code:
	docker run -it --rm $(VOLUMES) knyku/php:7.4-fpm vendor/bin/php-cs-fixer fix

bash:
	docker run -it --rm $(VOLUMES) knyku/php:7.4-fpm bash

analyse:
	docker run -it --rm $(VOLUMES) knyku/php:7.4-fpm php vendor/bin/phpstan analyse -c phpstan.neon

composer-install:
	docker run -it --rm $(VOLUMES) knyku/php:7.4-fpm composer install

tests-spec:
	docker run -it --rm $(VOLUMES) knyku/php:7.4-fpm php vendor/bin/phpspec run --format=pretty

tests: composer-install
	docker run -it --rm $(VOLUMES) knyku/php:7.4-fpm vendor/bin/php-cs-fixer fix --dry-run
	docker run -it --rm $(VOLUMES) knyku/php:7.4-fpm php vendor/bin/phpspec run --format=pretty
	docker run -it --rm $(VOLUMES) knyku/php:7.4-fpm php vendor/bin/phpstan analyse -c phpstan.neon

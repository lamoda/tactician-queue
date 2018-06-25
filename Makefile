
.PHONY: default test php-cs-fix

default: test

test:
	@./vendor/bin/phpunit

php-cs-fix:
	@./vendor/bin/php-cs-fixer fix -vvv

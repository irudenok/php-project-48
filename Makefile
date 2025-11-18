install:
	composer install

validate:
	composer validate

gendiff:
	./bin/gendiff

lint:
	vendor/bin/phpcs --standard=PSR12 src bin

test:
	composer exec --verbose phpunit tests

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover=build/logs/clover.xml
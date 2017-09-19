install:
	composer install
lint:
	composer run-script phpcs -- --standard=PSR2 src bin tests/DifferTest.php
install_package:
	composer global require graywrk/gendiff:dev-master
	export PATH=$PATH:~/.config/composer/vendor/bin
test:
	vendor/bin/phpunit tests

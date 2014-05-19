# override like so: make dependencies COMPOSER=$(which composer.phar)
COMPOSER = ./build/composer.phar

.PHONY : test
test:
	php ./test/Affirm.php

.PHONY : dependencies
dependencies:
	$(COMPOSER) update --dev

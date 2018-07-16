# override like so: make dependencies COMPOSER=$(which composer.phar)
COMPOSER = ./build/composer.phar

.PHONY : all
all: test

.PHONY : validate_version
validate_version:
	python util/validate-version.py

.PHONY : test
test: dependencies validate_version
	php ./test/Affirm.php

.PHONY : dependencies
dependencies:
	$(COMPOSER) update --dev

package: validate_version
	mkdir -p ./var/
	cd ./extension && tar -cvf ../var/Affirm_Affirm-3.5.6.tgz *
	cd ./build && ./magento-tar-to-connect.phar affirm_tar_to_connect_config.php

clean:
	rm -rf ./var

# override like so: make dependencies COMPOSER=$(which composer.phar)
COMPOSER = ./build/composer.phar

.PHONY : test
test:
	php ./test/Affirm.php

.PHONY : dependencies
dependencies:
	$(COMPOSER) update --dev

package:
	mkdir -p ./var/
	cd ./extension && tar -cvf ../var/Affirm_Magento.tar *
	cd ./build && ./magento-tar-to-connect.phar affirm_tar_to_connect_config.php

clean:
	rm -rf ./var

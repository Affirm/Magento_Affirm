# override like so: make dependencies COMPOSER=$(which composer.phar)
COMPOSER = ./build/composer.phar

.PHONY : test
test: dependencies
	php ./test/Affirm.php

.PHONY : dependencies
dependencies:
	$(COMPOSER) update --dev

# create a standard tar archive of the extension directory.
#
# NotaBene(brian): The directory layout of the tar archive may not conform to
# the requirements of the Magento Connect packaging library, so this task may
# require modification.
#
.PHONY : tar
tar: bin
	cd extension && \
	tar -cvf ../bin/Magento_Affirm.tar app/ lib/

# used for generated artifacts
bin:
	mkdir bin

clean:
	rm -rf bin

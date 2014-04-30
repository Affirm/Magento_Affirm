build:
	docker build -t magento:1.4.0.1 .

run: build
	sh docker_run.sh /run.sh

i: build
	docker run -i magento:1.4.0.1 /bin/bash

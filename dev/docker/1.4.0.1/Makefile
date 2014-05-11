all: build

run: kill build
	docker run -d -p 8080:80 magento_affirm-1.4.0.1

build: extension link.sh
	docker build -t magento_affirm-1.4.0.1 .

extension:
	cp -r ../../../extension .

link.sh:
	cp ../../../dev/link.sh .

kill:
	sh docker_kill.sh

clean:
	rm -rf extension
	rm -rf link.sh

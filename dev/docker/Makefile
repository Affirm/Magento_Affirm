NAME = magento_affirm
VERSION = 0.1.0
PROJECT_ROOT = ../../

all: build tag_latest

build:
	cp -pR $(PROJECT_ROOT)/dev/link.sh 1.4.0.1
	cp -pR $(PROJECT_ROOT)/dev/link.sh 1.8.1.0
	cp -pR $(PROJECT_ROOT)/extension 1.4.0.1
	cp -pR $(PROJECT_ROOT)/extension 1.8.1.0
	docker build --rm -t $(NAME)-1401:$(VERSION) 1.4.0.1
	docker build --rm -t $(NAME)-1810:$(VERSION) 1.8.1.0

run_1401: build
	docker run -i -t -p 8080:80 $(NAME)-1401:$(VERSION)

tag_latest:
	docker tag $(NAME)-1401:$(VERSION) $(NAME)-1401:latest
	docker tag $(NAME)-1810:$(VERSION) $(NAME)-1810:latest

clean:
	rm -rf 1.4.0.1/extension
	rm -rf 1.4.0.1/link.sh
	rm -rf 1.8.1.0/extension
	rm -rf 1.8.1.0/link.sh

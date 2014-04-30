FROM tutum/lamp:latest

RUN apt-get update
RUN apt-get install -y php5-mcrypt php5-curl php5-gd

RUN rm -fr /app/*
ADD magento-1.4.0.1.tar.gz /app

RUN chmod -R 777 /app/magento/app/etc
RUN chmod -R 777 /app/magento/var
RUN chmod -R 777 /app/magento/media

RUN mkdir -p /app/magento/var/cache
RUN chmod -R 777 /app/magento/var/cache

# enable exception printing
RUN mv /app/magento/errors/local.xml.sample /app/magento/errors/local.xml

EXPOSE 80 3306

ENV MYSQL_PASS password

RUN env

ADD setup_db.sh /setup_db.sh
ADD install.sh /install.sh
ADD start_db.sh /start_db.sh
ADD stop_db.sh /stop_db.sh
RUN chmod +x /*.sh

RUN /setup_db.sh
RUN /install.sh

CMD ['/run.sh']

#
# build:  docker build -t magento:1.4.0.1 .
# run:    docker run -d -p 8080:80 magento:1.4.0.1 /run.sh
#

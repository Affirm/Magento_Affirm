FROM magento-1.8.1.0

ADD link.sh /link.sh
ADD extension /extension

RUN sh link.sh /app/magento

ADD configure_extension.php /app/magento/app/configure_extension.php
ADD config.json /app/magento/app/config.json
RUN /start_db.sh && cd /app/magento/app && php configure_extension.php && /stop_db.sh

CMD ["/run.sh"]

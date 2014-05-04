FROM magento-1.8.1.0

ADD link.sh /link.sh
ADD extension /extension

RUN sh link.sh /app/magento

CMD ["/run.sh"]

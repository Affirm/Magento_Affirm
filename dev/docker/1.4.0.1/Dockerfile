FROM magento-1.4.0.1

ADD link.sh /link.sh
ADD extension /extension

RUN sh link.sh /app/magento

CMD ["/run.sh"]

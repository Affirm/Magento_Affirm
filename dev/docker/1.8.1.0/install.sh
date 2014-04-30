#!/bin/bash
#

/start_db.sh

mysql -uadmin -ppassword -e "CREATE DATABASE IF NOT EXISTS magento"

# TODO(brian): read base URL from en
php /app/magento/install.php \
    --admin_email test@example.com \
    --admin_firstname Admin \
    --admin_lastname User \
    --admin_password m123m123 \
    --admin_username admin \
    --db_host /var/run/mysqld/mysqld.sock \
    --db_name magento \
    --db_pass password \
    --db_user admin \
    --default_currency USD \
    --license_agreement_accepted yes \
    --locale en_US \
    --secure_base_url https://localhost/ \
    --skip_url_validation yes \
    --timezone America/Los_Angeles \
    --url http://127.0.0.1:8080/magento \
    --use_rewrites no \
    --use_secure no \
    --use_secure_admin no

/stop_db.sh

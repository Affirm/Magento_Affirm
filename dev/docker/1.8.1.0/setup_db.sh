#!/bin/bash

if [ -f /.mysql_admin_created ]; then
    echo "MySQL 'admin' user already created!"
    exit 0
fi

/start_db.sh

echo "=> Creating MySQL admin user"
mysql -uroot -e "CREATE USER 'admin'@'localhost' IDENTIFIED BY 'password'"
mysql -uroot -e "GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost' WITH GRANT OPTION"

/stop_db.sh

echo "=> Done!"
touch /.mysql_admin_created

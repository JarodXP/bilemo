#!/bin/bash

#Sets ownership
sudo find /var/www/bilemo-test/public -type d -exec chown www-data {} +
sudo find /var/www/bilemo-test/public -type f -exec chown www-data {} +

#Sets permissions
sudo find /var/www/bilemo-test/ -type d -exec chmod 755 {} +
sudo find /var/www/bilemo-test/ -type f -exec chmod 644 {} +

#Set var/log/ & var/cache permission
chmod -R 777 /var/www/bilemo-test/var/log/
chmod -R 777 /var/www/bilemo-test/var/cache/
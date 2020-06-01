#!/bin/bash

#Sets ownership
sudo find /var/www/snowtricks/public -type d -exec chown www-data {} +
sudo find /var/www/snowtricks/public -type f -exec chown www-data {} +

#Sets permissions
sudo find /var/www/snowtricks/ -type d -exec chmod 755 {} +
sudo find /var/www/snowtricks/ -type f -exec chmod 644 {} +

#Set var/log/ & var/cache permission
chmod -R 777 /var/www/snowtricks/var/log/
chmod -R 777 /var/www/snowtricks/var/cache/
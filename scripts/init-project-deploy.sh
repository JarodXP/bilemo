#!/bin/bash

#Places the cursor on the project root
cd "/var/www/deployDirectory" || exit

#Calls the scripts for initialization
source "scripts/reset-database-deploy.sh"
source "scripts/reset-migrations.sh"
php bin/console doctrine:fixtures:load --no-interaction
#!/bin/bash

#Places the cursor on the project root
cd "/var/www/deployDirectory" || exit

#Defines an environement variable for the scripts
env=environment

#for the staging environment as both staging and master use the same database (demo site)
if [ $env == "dev" ]
then
    #Calls the scripts for database initialization
    source "scripts/reset-database-deploy.sh"
    source "scripts/reset-migrations.sh"

    #Loads the fixtures
    php bin/console doctrine:fixtures:load --no-interaction
fi
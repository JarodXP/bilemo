#!/bin/bash

#Resets the database
php bin/console doctrine:query:sql "DROP DATABASE IF EXISTS bilemo"
php bin/console doctrine:database:create

# Prepare database with Doctrine
php bin/console doctrine:migrations:migrate --no-interaction
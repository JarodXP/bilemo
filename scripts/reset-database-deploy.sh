#!/bin/bash

#Resets the database
php bin/console doctrine:query:sql "DROP DATABASE IF EXISTS bilemo"
php bin/console doctrine:database:create
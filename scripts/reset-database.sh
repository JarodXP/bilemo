#!/bin/bash

#Resets the database
rm -f var/data.db
php bin/console doctrine:database:create
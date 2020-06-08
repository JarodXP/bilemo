#!/bin/bash

#Resets the database
rm -f var/bilemo.db
php bin/console doctrine:database:create
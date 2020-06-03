#!/bin/bash

echo 'script is launched'

#Resets migrations files
rm -r src/Migrations/*

php bin/console doctrine:migrations:generate --no-interaction
php bin/console doctrine:migrations:diff --no-interaction

git add src/Migrations/*

php bin/console doctrine:migrations:migrate --no-interaction

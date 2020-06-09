#!/bin/bash

source "scripts/reset-database.sh"
source "scripts/reset-migrations.sh"
php bin/console doctrine:fixtures:load --no-interaction
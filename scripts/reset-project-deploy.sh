#!/bin/bash

source "scripts/reset-database-deploy.sh"
source "scripts/reset-migrations.sh"
php bin/console doctrine:fixtures:load --no-interaction
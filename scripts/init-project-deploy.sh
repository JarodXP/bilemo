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

# Moves the rsa keys to the JWT directory
cp /var/www/RSA_keys/private.pem /var/www/deployDirectory/config/jwt/private.pem
cp /var/www/RSA_keys/public.pem /var/www/deployDirectory/config/jwt/public.pem

# Gets the passphrase from RSA directory in server and writes it on the .env.local
pass=$(grep "bilemo" /var/www/RSA_keys/passphrases | cut -d: -f2)
sed -i "$ a JWT_PASSPHRASE=$pass" /var/www/deployDirectory/.env.local

#Change ownership of jwt keys directory to be readable
sudo chown -R www-data:www-data /var/www/deployDirectory/config/jwt
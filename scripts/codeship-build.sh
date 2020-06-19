#!/bin/bash

#Creates a .env.local file and sets the environment
if [ "$CI_BRANCH" = "master" ]
then
  environment='prod'
  deployDirectory='bilemo'
else
  environment='dev'
  deployDirectory='bilemo-test'
fi

#Sets the env vars in the .env.local from the CodeShip env vars
printf "%s\n" "APP_ENV=$environment" "DATABASE_URL=$DATABASE_URL" "JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem" "JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem" > .env.local

#Sets the deploy directory for scripts
sudo find appspec.yml -type f -exec sed -i "s/deployDirectory/$deployDirectory/" {} \;
sudo find ./scripts/clear-project.sh -type f -exec sed -i "s/deployDirectory/$deployDirectory/g" {} \;
sudo find ./scripts/permissions.sh -type f -exec sed -i "s/deployDirectory/$deployDirectory/g" {} \;
sudo find ./scripts/init-project-deploy.sh -type f -exec sed -i "s/deployDirectory/$deployDirectory/g" {} \;
sudo find ./scripts/init-project-deploy.sh -type f -exec sed -i "s/environment/$environment/" {} \;
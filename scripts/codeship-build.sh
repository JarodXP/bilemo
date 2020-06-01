#!/bin/bash

#Creates a .env.local file
if [ "$CI_BRANCH" = "master" ]
then
  environment='prod'
else
  environment='dev'
fi

#Sets the env vars in the .env.local from the CodeShip env vars
printf "%s\n" "APP_ENV=$environment" "DATABASE_URL=$DATABASE_URL" "MAILER_TRANSPORT=$MAILER_TRANSPORT" "MAILER_USERNAME=$MAILER_USERNAME" "MAILER_PASSWORD=$MAILER_PASSWORD" "MAILER_HOST=$MAILER_HOST" "MAILER_ENCRYPTION=$MAILER_ENCRYPTION" "MAILER_PORT=$MAILER_PORT" "MAILER_AUTH_MODE=$MAILER_AUTH_MODE" > .env.local


#Changes the name of the appspec file depending on the branch
if [ "$CI_BRANCH" = "staging" ]
then
  mv appspec.yml.staging appspec.yml
fi
if [ "$CI_BRANCH" = "master" ]
then
  mv appspec.yml.prod appspec.yml
fi
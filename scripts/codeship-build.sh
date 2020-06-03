#!/bin/bash

#Creates a .env.local file and sets the environment
if [ "$CI_BRANCH" = "master" ]
then
  environment='prod'
else
  environment='dev'
fi

#Sets the env vars in the .env.local from the CodeShip env vars
printf "%s\n" "APP_ENV=$environment" "DATABASE_URL=$DATABASE_URL" > .env.local


#Changes the name of the appspec file depending on the branch
if [ "$CI_BRANCH" = "staging" ]
then
  mv appspec.yml.staging appspec.yml
fi
if [ "$CI_BRANCH" = "master" ]
then
  mv appspec.yml.prod appspec.yml
fi
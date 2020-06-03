#Sets the directory corresponding to the branch
if [ "$CI_BRANCH" = "master" ]
then
  directory='bilemo'
else
  directory='bilemo-test'
fi

#Places the cursor on the project root
cd "/var/www/${directory}" || exit

#Calls the scripts for initialization
source "scripts/init-database-prod.sh"
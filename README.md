# Welcome to the BileMo API  

[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=JarodXP_bilemo&metric=security_rating)](https://sonarcloud.io/dashboard?id=JarodXP_bilemo) [![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=JarodXP_bilemo&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=JarodXP_bilemo)

> This API has been built following the specifications for the Openclassrooms Symfony Course - P7.

## Main constraints  

These are the main constraints for this project:  

- Access reserved to authenticated users through Oauth or JWT (JWT used)
- Follow 3 first stages of Richardson Model
- Data format: JSON
- Cache responses when possible  

## Get the project  

You can directly clone it on GitHub: https://github.com/JarodXP/bilemo/  

## Requirements for the project  

- have a webserver with PHP 7.4 or above
- have a MySQL server (only for production)

> The project is supplied with an SQLite database, so you don't need an extra MySQL server to run it locally.  

- Depending on your database, you will also need to activate the corresponding php PDO driver.

- have Composer installed  
- have a pair of ssh keys (openSSL recommended)  

## Setup your environment  

1. Move your SSH keys into the /config/jwt directory.
2. Rename your .env file into .env.local and fill in the required environment variables if needed.  
3. Run composer install

## Project initialization  

### Using the scripts

Once cloned, you can launch a script to initialize all automatically: scripts/reset-project.sh (for local development environment)

This script includes the following ones that you can also run step by step:  

1. scripts/reset-database.sh  
2. scripts/reset-migrations.sh  
3. php bin/console doctrine:fixtures:load  

### Manually  

If the scripts don't work or if you prefer setting up the project manually, follow these instructions step by step:  

1. [optional] Remove possibly existing SQLite database:  
`rm -f var/bilemo.db`  
2. Create a new database:  
`php bin/console doctrine:database:create --no-interaction`  
3. [optional] Remove possibly existing migrations:  
`rm -r src/Migrations/*`  
4. Initialize Migrations:  
`php bin/console doctrine:migrations:generate --no-interaction`  
`php bin/console doctrine:migrations:diff --no-interaction`  
`git add src/Migrations/*`  
`php bin/console doctrine:migrations:migrate --no-interaction`  
5. Load the fixtures:  
`php bin/console doctrine:fixtures:load --no-interaction`  

> The reset-project.sh scripts runs all the commands above.  
The optional commands are used to allow the script to either set or reset the project.  

## API Documentation  

Once the project installed, you can use the online demo at /api/doc.  

The documentation UI has been built using OpenAPI (Swagger) with the nelmioapidocbundle.

This latter permits:  

- any anonymous users to see the documentation,  
- members to authenticate using their credentials,  
- authenticated users to test the endpoints with a sandbox.  

## Some Bundles used for this project  

- Lexik/JWTAuthenticationBundle  

> Used for authentication with Jason Web Token (JWT).  

- Bazinga/BazingaHateoasBundle  

> Creates automatic hypertext links for Json responses

- JMS/JMSSerializerBundle  

> Dependency for the Hateoas Bundle.  
Used as main serializer in place of the Symfony Serializer.  

- Nelmio/NelmioApiDocBundle  

> Adapts the SwaggerUI (OpenAPI) for Symfony.

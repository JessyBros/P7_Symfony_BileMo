# P7_Symfony_BileMo

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/7828d6fe49e04b90b5402cf033b2d964)](https://app.codacy.com/gh/SiProdZz/P7_Symfony_BileMo?utm_source=github.com&utm_medium=referral&utm_content=SiProdZz/P7_Symfony_BileMo&utm_campaign=Badge_Grade_Settings)

PROJECT 7 BileMo - API Application with Symfony

## OBJECTIF 
<https://openclassrooms.com/fr/paths/59/projects/43/assignment>

## CONTENT PROJECT
-   UML Diagrams
-   Entity and fixtures to complete your database
-   Development project (use Issue & Pull request)

## Prerequisite in your workplace <https://www.java.com/fr/download/help/path.html>
-   Php 7.4  (x64 Non Thread Safe) or (x86 pour les versions 32 bits) <https://windows.php.net/download#php-7.4>
-   Composer  <https://getcomposer.org/download/> (to manage dependencies and libraries)
-   Symfony command <https://symfony.com/doc/current/the-fast-track/fr/1-tools.html#symfony-cli>
-   OpenSSL

### Install in your workplace PHP, Composer, Symfony and OpenSSL (variable environment)
-   exemple : <https://www.twilio.com/blog/2017/01/how-to-set-environment-variables.html>
-   Then check with next command in your terminal whatever where path :

  • <code>php -v</code>

  • <code>composer -V</code>

  • <code>symfony -V</code>

## HOW TO INSTALL

### Step 1 : Recover the project
-   Choose where you want to install the project in your Computer and open your terminal.
-   Clone it with the next command : <https://github.com/SiProdZz/P7_Symfony_BileMo.git>

### Step 2 : Connect your project at your database
-   create a file ".env.local" in the same directory as ".env" and complet it to acces at your database
    exemple : DATABASE_URL=postgres://postgres:password@127.0.0.1:5432/name_database

### Step 3 : Update composer to your project
-   use <code>composer install</code> in your terminal to install and update all dependencies and libraries 

### Step 4 : Create your database and add Fixtures
-   Open your terminal and use the next command to install and prepare your database

    • <code>symfony console doctrine:database:create</code> to create your database

    • <code>symfony console make:migration</code> to create a migration file

    • <code>symfony console doctrine:migrations:migrate</code> to create dababase table

    • <code>symfony console doctrine:fixtures:load</code> to generate fixtures in your database create already in the project

### Step 5 : Configure JWT token
-   If you got some problem you can still check the official guide to configure JWT <https://github.com/lexik/LexikJWTAuthenticationBundle/blob/HEAD/Resources/doc/index.md>

    • write your pass_phrase password in the ".env.local" file.

    • create "jwt" folder in "config" folder

    • create private.pem" file in "jwt" folder and write commande to generate private key <code>openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096</code>
    
    • create public.pem" file and in "jwt" folder write command to generate public key <code>openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout</code>

### Step 6 : Run symfony server and open your project
-   Run the project <code>symfony server:start</code>
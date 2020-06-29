# SnowTricks

[![Maintainability](https://api.codeclimate.com/v1/badges/e2e5070a934a348e3cbb/maintainability)](https://codeclimate.com/github/btolan-karudev/SnowTricks/maintainability)



SnowTricks


Requirements
PHP 7.2.5 or higher
SQL Database
Composer


Installation
Clone or download this repository
$ git clone https://github.com/btolan-karudev/SnowTricks


Install dependencies
# in project directory
$ composer install




Set up database

$ php bin/console doctrine:database:create
$ php bin/console doctrine:migrations:migrate

Launch server

❗Symfony binary required

$ symfony server:start

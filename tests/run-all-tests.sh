#!/usr/bin/env bash

# Point to test environment
export APP_ENV=test

# Drop database
php bin/console doctrine:database:drop --if-exists --force

# Creating a new database
php bin/console doctrine:database:create

# Create tables from entities
php bin/console doctrine:schema:update --force

# Ignore notifications about obsolete packages
export SYMFONY_DEPRECATIONS_HELPER=weak

# run tests
./vendor/bin/phpunit  --verbose --testdox --group test --repeat 1 tests

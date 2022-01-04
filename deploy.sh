#!/usr/bin/env bash

VERSION=$1;
# Turn on maintenance mode
./vendor/bin/sail artisan down

# Pull the latest changes from the git repository
# git reset --hard
# git clean -df
#git pull origin master
#git checkout ${VERSION}

# Install/update composer dependecies
./vendor/bin/sail composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Run database migrations
./vendor/bin/sail artisan migrate --force

# Clear caches
./vendor/bin/sail artisan cache:clear

# Clear expired password reset tokens
./vendor/bin/sail artisan auth:clear-resets

# Clear and cache routes
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan route:cache

# Clear and cache config
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan config:cache

# Clear the view cache
./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan view:cache

# Install node modules
./vendor/bin/sail npm install

# Build assets using Laravel Mix
./vendor/bin/sail npm run production

# Turn off maintenance mode
./vendor/bin/sail artisan up

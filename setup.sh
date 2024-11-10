#!/bin/bash

set -e

exec > >(tee -i setup.log) 2>&1

# Execute copy-env.sh to copy .env.example to .env
echo "Copying .env.example to .env..."
./copy-env.sh

# Check if copy-env.sh was successful, if not, exit
if [ $? -ne 0 ]; then
  echo "Error: .env file was not copied. Exiting setup."
  exit 1
fi

echo "Building Docker images and starting containers..."

docker-compose up -d --build

echo "Waiting for the services to be ready..."
sleep 10

echo "Waiting for MySQL to be ready..."

# Wait until MySQL is ready
until docker-compose exec mysql sh -c 'exec mysql -h"mysql_db" -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "SELECT 1"' &>/dev/null; do
  echo -n "."
  sleep 1
done

echo "MySQL is ready. Creating the database if it does not exist..."

docker-compose exec mysql sh -c 'exec mysql -h"mysql_db" -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -e  "DROP DATABASE IF EXISTS todoappdb; CREATE DATABASE todoappdb;"'

echo "Checking if the artisan service is running..."
docker-compose up -d artisan

echo "Waiting for the artisan service to be ready..."
sleep 10

echo "Installing dependencies..."
docker-compose run composer

echo "Running Laravel migrations..."
docker-compose exec artisan php artisan migrate --force

echo "Running Laravel seeders..."
docker-compose exec artisan php artisan db:seed --force

echo "Setup complete. Your application should be running."

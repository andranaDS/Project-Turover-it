#!/usr/bin/env bash

echo "Starting build of php:8.1-fpm-base"

echo "Login to gitlab docker registry"
docker login registry.gitlab.com

echo "Building php-ci image"
docker build -t registry.gitlab.com/agsi/free-work-back/php:8.1-fpm-base .

echo "Pushing php-ci image to docker registry"
docker push registry.gitlab.com/agsi/free-work-back/php:8.1-fpm-base
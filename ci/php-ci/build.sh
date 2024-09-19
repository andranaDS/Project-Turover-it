#!/usr/bin/env bash
if [ $# -eq 0 ]
  then
    tag='latest'
  else
    tag=$1
fi

echo "Starting build of php-ci:$tag"

echo "Login to gitlab docker registry"
docker login registry.gitlab.com

echo "Building php-ci image"
docker build -t registry.gitlab.com/agsi/free-work-back/php-ci:$tag .

echo "Pushing php-ci image to docker registry"
docker push registry.gitlab.com/agsi/free-work-back/php-ci:$tag
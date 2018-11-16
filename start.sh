#!/usr/bin/env bash

red=$'\e[1;31m'
grn=$'\e[1;32m'
blu=$'\e[1;34m'
mag=$'\e[1;35m'
cyn=$'\e[1;36m'
white=$'\e[0m'

sudo apt update
sudo apt install -y curl

echo " $red ----- Installing Pre requisites ------- $white "

sudo chmod 0777 /var/run/docker.sock
sudo docker-compose down && docker-compose up -d

sudo sleep 120s #this line is included for composer to finish the dependency installation so that test case can execute without error.


echo " $red ----- Running Intergration test cases ------- $white "
docker exec delivery_php php ./vendor/phpunit/phpunit/phpunit /var/www/html/tests/Feature/OrderControllerTest.php

echo " $red ----- Running Unit test cases ------- $white "
docker exec delivery_php php ./vendor/phpunit/phpunit/phpunit /var/www/html/tests/Unit/OrderUnitTest.php

exit 0

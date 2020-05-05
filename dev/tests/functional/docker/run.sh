#!/bin/bash

set -ex

TEST_SUITE=functional
FUNCTIONAL_DIR=$(dirname $(dirname $BASH_SOURCE[0]))
MAGENTO_HOST_NAME="mtf_php"
COMPOSER_CACHE=$HOME/.composer/cache

echo "Starting test environment"
cd $FUNCTIONAL_DIR/docker
docker-compose -p mtf up -d

echo "Installing Composer dependencies"
docker run --rm \
  --volume $FUNCTIONAL_DIR:/app --volume ${COMPOSER_HOME:-$HOME/.composer}:/tmp \
  --user $(id -u):$(id -g) \
  composer --ignore-platform-reqs install --no-interaction

echo "Installing Magento"
docker exec mtf_mysql mysql -uroot -e 'CREATE DATABASE magento;'
docker exec mtf_php php -f install.php -- \
  --license_agreement_accepted yes \
  --locale en_US --timezone "America/Los_Angeles" --default_currency USD \
  --db_host 127.0.0.1 --db_name magento --db_user root --db_pass "" \
  --url "http://${MAGENTO_HOST_NAME}/" --use_rewrites yes --use_secure no \
  --secure_base_url "http://${MAGENTO_HOST_NAME}/" --use_secure_admin no \
  --admin_lastname Owner --admin_firstname Store --admin_email "admin@example.com" \
  --admin_username admin --admin_password asd123#2*53515523 \
  --encryption_key "I2V7t7fiCIRKw9FWz4m3CStgeBG1T+ATZ0Us+W8jAIk="

echo "Launching PHPUnit runner"
docker exec -it mtf_php dev/tests/functional/run_functional.sh

echo "Stopping test environment"
cd $FUNCTIONAL_DIR/docker
docker-compose -p mtf down

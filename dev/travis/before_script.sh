#!/usr/bin/env bash

# Copyright © Magento, Inc. All rights reserved.
# http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

# prepare for test suite
case $TEST_SUITE in
    functional)
        echo "Installing Magento"
        mysql -uroot -e 'CREATE DATABASE magento;'
        php -f install.php -- \
          --license_agreement_accepted yes \
          --locale en_US --timezone "America/Los_Angeles" --default_currency USD \
          --db_host 127.0.0.1 --db_name magento --db_user root --db_pass "" \
          --url "http://${MAGENTO_HOST_NAME}/" --use_rewrites yes --use_secure no \
          --secure_base_url "http://${MAGENTO_HOST_NAME}/" --use_secure_admin no \
          --admin_lastname Owner --admin_firstname Store --admin_email "admin@example.com" \
          --admin_username admin --admin_password asd123#2*53515523 \
          --encryption_key "I2V7t7fiCIRKw9FWz4m3CStgeBG1T+ATZ0Us+W8jAIk="

        echo "Prepare functional tests for running"
        cd dev/tests/functional

        composer install && composer require se/selenium-server-standalone:2.53.1
        export DISPLAY=:1.0
        sh ./vendor/se/selenium-server-standalone/bin/selenium-server-standalone -port 4444 -host 127.0.0.1 \
            -Dwebdriver.firefox.bin=$(which firefox) -trustAllSSLCertificate &> ~/selenium.log &

        cp ./phpunit.xml.dist ./phpunit.xml
        sed -e "s?127.0.0.1?${MAGENTO_HOST_NAME}?g" --in-place ./phpunit.xml
        sed -e "s?basic?travis_acceptance?g" --in-place ./phpunit.xml
        cp ./.htaccess.sample ./.htaccess
        cd ./utils
        php -f mtf troubleshooting:check-all

        cd ../../..
        ;;
esac
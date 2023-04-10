#!/bin/bash

set -ex

export TEST_SUITE=functional


# Before Install
#./dev/travis/before_install.sh
/sbin/start-stop-daemon --start --quiet --pidfile /tmp/custom_xvfb_99.pid --make-pidfile --background --exec /usr/bin/Xvfb -- :1 -screen 0 1280x1024x24

export DISPLAY=:1.0
sh ./vendor/se/selenium-server-standalone/bin/selenium-server-standalone -port 4444 -host 127.0.0.1 \
    -Dwebdriver.firefox.bin=$(which firefox) -trustAllSSLCertificate &> ~/selenium.log &

cp ./phpunit.xml.dist ./phpunit.xml
#sed -e "s?127.0.0.1?${MAGENTO_HOST_NAME}?g" --in-place ./phpunit.xml
#sed -e "s?basic?travis_acceptance?g" --in-place ./phpunit.xml

php -f utils/generate.php

dev/tests/functional/vendor/phpunit/phpunit/phpunit -c dev/tests/$TEST_SUITE

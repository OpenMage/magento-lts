# Magento Functional Tests

This suite of tests is based on the abandoned magento/mtf project.

## Install

First, install dependencies using composer:

```sh
docker run --rm -it \
  --volume $PWD:/app --volume ${COMPOSER_HOME:-$HOME/.composer}:/tmp \
  --user $(id -u):$(id -g) \
  composer --ignore-platform-reqs install
```

Then generate tests using PHP:

```sh
docker run --rm -it \
  --volume $(realpath $PWD/../../../):/app --workdir /app \
  php:7.2 \
    php -f dev/tests/functional/utils/generate.php
```

Run tests:

```sh
docker run --rm -it \
  --volume $(realpath $PWD/../../../):/app --workdir /app \
  php:7.2 \
    dev/tests/functional/vendor/phpunit/phpunit/phpunit -c dev/tests/functional
```

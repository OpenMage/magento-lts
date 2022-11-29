# OpenMage DDEV environment

__This is work-in-progress.__

## Enable developer mode

Set environment variables here:

`.ddev/config.yaml`

```
web_environment: [
    MAGE_IS_DEVELOPER_MODE=1
]
```

## Use xDebug with PhpStorm

If xdebug works not correctly with phpstorm.

`.ddev/php/xdebug.ini`

```
[xdebug]
xdebug.mode=debug
xdebug.start_with_request=trigger
```

## Access DB in PhpStorm

DDEV changes port numbers on every restart.

If you use PhpStorms DB feature, it is helpful to use fixed port numbers. E.g. 

`.ddev/config.yaml`

```
host_db_port: 6000
```

## Setup cronjob

Run `ddev get drud/ddev-cron` first!

`.ddev/config.cron.yaml`

```
hooks:
  post-start:
    - exec: printf "SHELL=/bin/bash\n* * * * * /var/www/html/cron.sh\n" | crontab

```

## Install compass

[Compass](http://compass-style.org/) is required for editing scss-files from RWD-theme.

`.ddev/web-build/Dockerfile.ddev-compass`

```
ARG BASE_IMAGE
FROM $BASE_IMAGE
RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y -o Dpkg::Options::="--force-confold" --no-install-recommends --no-install-suggests build-essential ruby-full rubygems
RUN gem install compass
```

https://stackoverflow.com/questions/61787926/how-can-i-get-sass-compass-into-the-ddev-web-container

## Example command shortcut

`.ddev/commands/web/phpstan`

```
#!/bin/bash

## Description: run PHPStan
## Usage: phpstan
## Example: ddev phpstan <path-to-files>

php vendor/bin/phpstan analyze -c .github/phpstan.neon "$@"
```
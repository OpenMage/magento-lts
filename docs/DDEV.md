# OpenMage Environment Based on DDEV (https://ddev.com/)

## Enabling the Developer Mode

Set environment variables editing the file `.ddev/config.yaml`. If you want to enable the Developer Mode insert the following lines

```
web_environment: [
    MAGE_IS_DEVELOPER_MODE=1
]
```

## Using xDebug with PhpStorm

Run in the terminal window the following commands to enable or disable xDebug 

`ddev xdebug on`

`ddev xdebug off`

If xDebug does not work properly with PHPStorm edit the file `.ddev/php/xdebug.ini` and insert the following lines

```
[xdebug]
xdebug.mode=debug
xdebug.start_with_request=trigger
```

## Accessing the Database in PhpStorm

Please note that DDEV changes the port numbers on every restart. If you want to access the database in PHPStorm you must set up a fixed port. Edit the file `.ddev/config.yaml` and insert the following line

```
host_db_port: 6000
```

## Setting up cronjobs

It is mandatory to run first in the terminal window this command `ddev get ddev/ddev-cron`. 

By default the OpenMage cronjob is running every minute. If you want to change it edit the file `.ddev/web-build/openmage.cron`.

You can set the OpenMage cronjob using DDEV hooks, but you must comment all the lines in the file `.ddev/web-build/openmage.cron`. Edit the file `.ddev/config.yaml` and insert the following lines

```
hooks:
  post-start:
    - exec: printf "SHELL=/bin/bash\n* * * * * /var/www/html/cron.sh\n" | crontab

```

## Installing Compass (http://compass-style.org/)

Compass is required for editing SCSS files.

Edit the file `.ddev/web-build/Dockerfile.ddev-compass` and insert the following lines

```
ARG BASE_IMAGE
FROM $BASE_IMAGE
RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y -o Dpkg::Options::="--force-confold" --no-install-recommends --no-install-suggests build-essential ruby-full rubygems
RUN gem install compass
```

For more information, please visit https://stackoverflow.com/questions/61787926/how-can-i-get-sass-compass-into-the-ddev-web-container

## phpMyAdmin

Run in the terminal windows this command `ddev get ddev/ddev-phpmyadmin` and restart DDEV. To launch phpMyAdmin in the browser window run this command `ddev phpmyadmin`.

## Mailpit

To launch Mailpit in the browser window run this command `ddev launch -p`.

## Creating a custom DDEV command

Create a new file named `.ddev/commands/web/phpstan` and insert the following lines

```
#!/bin/bash

## Description: run PHPStan
## Usage: phpstan
## Example: ddev phpstan <path-to-files>

php vendor/bin/phpstan analyze -c .github/phpstan.neon "$@"
```

Run the custom command in the terminal window `ddev phpstan`.

## OpenMage Custom DDEV commands

If you want to install the Magento Sample Data run in the terminal window this command `ddev openmage-install`. You can use this command with flags, for example `ddev openmage-install -d -s -k -q`

```
-d (default values for the administrator account)
-s (sampledata installation)
-k (keeps the downloaded archive in the .ddev/.sampleData directory)
-q (quiet mode)
```

If you want to change the administrator account password run in the terminal window this command `ddev openmage-admin`.

## Useful DDEV Commands (https://ddev.readthedocs.io/en/latest/users/usage/commands)

`ddev config`, `ddev describe`

`ddev composer install`, `ddev composer update`, `ddev composer require openmage/module-mage-backup`

`ddev start`, `ddev stop`, `ddev restart`, `ddev poweroff`, `ddev list`

`ddev launch`, `ddev launch -m`

`ddev mysql`, `ddev php`, `ddev ssh`, `ddev exec`

`ddev logs`, `ddev logs -f`, `ddev logs -s db`

`ddev npm install`, `ddev npm update`

`ddev snapshot --name my_snapshot_name`, `ddev snapshot --list`, `ddev snapshot --cleanup`, `ddev snapshot restore`

`ddev import-db --src=magento_sample_data.sql`, `ddev export-db --target-db=db --file=om_db.sql.gz`, `ddev import-files --src=om_media.tar.gz`

`ddev xdebug on`, `ddev xdebug off`

`ddev get --list`, `ddev get drud/ddev/cron`

`ddev service enable`, `ddev service disable`

`ddev delete`, `ddev delete images`, `ddev clean`

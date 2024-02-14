# OpenMage Environment Based on DDEV (https://ddev.com/)

## phpMyAdmin

Run in the terminal window this command `ddev get ddev/ddev-phpmyadmin` then restart DDEV. To launch phpMyAdmin in the browser run this command in the terminal window `ddev phpmyadmin`.

## Mailpit

To launch Mailpit in the browser run this command in the terminal window `ddev mailpit`.

## Setting up cronjobs

It is mandatory to run first in the terminal window this command `ddev get ddev/ddev-cron`. 

By default the OpenMage cronjob is running every minute. If you want to change it edit the file `.ddev/web-build/openmage.cron`.

You can set the OpenMage cronjob using DDEV hooks, but you must comment all the lines in the file `.ddev/web-build/openmage.cron`. Edit the file `.ddev/config.yaml` and insert the following lines

```
hooks:
  post-start:
    - exec: printf "SHELL=/bin/bash\n* * * * * /var/www/html/cron.sh\n" | crontab

```

## Enabling the Developer Mode

Set environment variables editing the file `.ddev/config.yaml`. If you want to enable the Developer Mode insert the following lines

```
web_environment: [
    MAGE_IS_DEVELOPER_MODE=1
]
```

## Using Xdebug with PhpStorm

Run in the terminal window the following commands to enable or disable xDebug 

`ddev xdebug on`

`ddev xdebug off`

If Xdebug does not work properly with PHPStorm edit the file `.ddev/php/xdebug.ini` and insert the following lines

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

## Using Browsersync (https://github.com/ddev/ddev-browsersync)

Browsersync features live reloads, click mirroring, network throttling. Run the following commands in the terminal window

```
ddev get ddev/ddev-browsersync
ddev restart
ddev browsersync
```

## Installing Compass (http://compass-style.org/)

Compass is required for editing SCSS files.

Create a new file named `.ddev/web-build/Dockerfile.ddev-compass` and insert the following lines

```
ARG BASE_IMAGE
FROM $BASE_IMAGE
RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y -o Dpkg::Options::="--force-confold" --no-install-recommends --no-install-suggests build-essential ruby-full rubygems
RUN gem install compass
```

For more information, please visit https://stackoverflow.com/questions/61787926/how-can-i-get-sass-compass-into-the-ddev-web-container.

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

**If you want to install the `Magento Sample Data` run in the terminal window this command and follow the steps**

`ddev openmage-install`

You can use flags, for example `ddev openmage-install -d -s -k -q`

```
-d (default values for the administrator account)
-s (sampledata installation)
-k (keeps the downloaded archive in the .ddev/.sampleData directory)
-q (quiet mode)
```

**By default, running the `ddev config` command does not create an administrator account. If you want to create or update one run in the terminal window this command and follow the steps**

`ddev openmage-admin`

## Useful DDEV Commands (https://ddev.readthedocs.io/en/latest/users/usage/commands)

Run the following commands in the terminal window.

**Create or modify a DDEV project's configuration in the current directory**

`ddev config`

**Get a detailed description of a running DDEV project**

`ddev describe`

**List Projects**

`ddev list`

**Start / Stop / Restart / Completely stop all project and containers**

`ddev start`, `ddev stop`, `ddev restart`, `ddev poweroff`

**Launch a browser with the current site**

`ddev launch`

**Execute Composer commands within a web container**

`ddev composer install`, `ddev composer update`, `ddev composer require openmage/module-mage-backup`

**Run npm inside the web container**

`ddev npm install`, `ddev npm update`

**Enable or disable XDebug**

`ddev xdebug on`, `ddev xdebug off`, `ddev xdebug status`

**Create a database snapshot for one or more projects**

`ddev snapshot --name my_snapshot_name`, `ddev snapshot --list`, `ddev snapshot --cleanup`, `ddev snapshot restore`

**Import or export a SQL file into the project**

`ddev import-db --src=magento_sample_data.sql`, `ddev export-db --target-db=db --file=om_db.sql.gz`, `ddev import-files --src=om_media.tar.gz`

**Download DDEV adds-on**

`ddev get --list`, `ddev get drud/ddev/cron`

**Run MYSQL client in the database container / Run php inside the web container / Stars a shell session in a service container / Execute a shell command in the container**

`ddev mysql`, `ddev php`, `ddev ssh`, `ddev exec`

**Get the logs from your running services**

`ddev logs`, `ddev logs -f`, `ddev logs -s db`

**Enable or disable a service**

`ddev service enable`, `ddev service disable`

**Remove all information, including the database, from a project**

`ddev delete`, `ddev delete images`

**Removes items DDEV has created**

`ddev clean --dry-run -all`, `ddev clean`

## Using mkcert (https://github.com/FiloSottile/mkcert)

mkcert is a simple tool for making locally-trusted development certificates. If you use (Windows 10/11 + WSL + Docker), first install the mkcert package in Windows then copy the certificates files associated to the current user into the Linux distribution. For example,

```
From: /
To: /
```

## Installing OpenMage in the browser

If you want to install OpenMage in the browser rename or delete the `/app/etc/local.xml` file.

For the database connection use the following information

```
Host: db
Database Name: db
User Name: db
User Password: db
```

![installation](https://github.com/ADDISON74/openmage/assets/8360474/cb6a0472-7740-4e2b-bce8-fb699ca2710c)

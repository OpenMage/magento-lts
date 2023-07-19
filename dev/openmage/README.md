OpenMage Stack
===

With these stack configuration files you can have a fully operational OpenMage LTS development in ONE step!
Upgrade it to production with just a few more easy steps!

**NOTE: Use at your own risk! No warrants or guarantees are provided as to the stability, security or general quality of these environment files.**

For a more robust development environment that supports https, please consider using [ddev](https://ddev.readthedocs.io/en/stable/users/cli-usage/#magento-1-quickstart).

## Prerequisites

- Install [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/)
- Port 80 on your host must be unused. See [Environment Variables](#environment-variables) below if you need to use another port.
- Clone the OpenMage LTS repo to your location of choice (`git clone https://github.com/OpenMage/magento-lts.git`).

## Installation

Run `dev/openmage/install.sh`. That's it!

Visit [http://openmage-7f000001.nip.io/](http://openmage-7f000001.nip.io/) and start coding!

Tips
===

See [colinmollenhour/docker-openmage-dev](https://github.com/colinmollenhour/docker-openmage-dev) for more information
on the containers used in this setup, but here are some quick tips:

- You can start the cron task using `docker-compose up -d cron`.
- The `cli` service contains many useful tools like `composer`, `magerun`, `modman`, `mageconfigsync` and more.
- XDebug is enabled using `remote_connect_back=1` with `idekey=phpstorm`. Customize this in `.env` if needed as described below.

Here are some common commands you may wish to try (from the `dev/openmage` directory):

```
$ docker-compose run --rm -u $(id -u):$(id -g) cli composer show
$ docker-compose run --rm -u $(id -u):$(id -g) cli bash
$ docker-compose run --rm cli magerun sys:check
$ docker-compose run --rm cli magerun cache:clean
$ docker-compose run --rm cli magerun db:console
$ docker-compose exec mysql mysql
```

- *The cli container runs as `www-data` by default so use `-u $(id -u):$(id -g)` with composer so that the container will create/modify files with your user permissions to avoid file permission errors in your IDE.*
- *Always use `run --rm` with the cli container to avoid creating lots of orphan containers.*

Environment Variables
---

You can override some defaults using environment variables defined in a file (that you must create) at `dev/openmage/.env`.

- `ENABLE_SENDMAIL=false` - Disable the sendmail MTA
- `XDEBUG_CONFIG=...` - Override the default XDebug config
- `HOST_NAME=your-preferred-hostname`
  - `openmage-7f000001.nip.io` is used by default to resolve to `127.0.0.1`. See [nip.io](https://nip.io) for more info.
- `HOST_PORT=8888`
   - `80` is used by default 
- `ADMIN_HOST_NAME`
  - `openmage-admin-7f000001.nip.io` is used by default to resolve to `127.0.0.1`. See [nip.io](https://nip.io) for more info.
- `ADMIN_HOST_PORT`
  - `81` is used by default to avoid conflicts with the frontend port
- `ADMIN_EMAIL`
- `ADMIN_USERNAME`
- `ADMIN_PASSWORD`
- `MAGE_IS_DEVELOPER_MODE`
  - Set to 1 by default, set to 0 to disable

Wiping
---

If you want to start fresh, wipe out your installation with the following command:

```
$ docker-compose down --volumes && rm -f ../../app/etc/local.xml
```

HTTPS / Production
=====

You can easily have a multi-store SSL-protected environment using Docker with the `docker-compose-production.yml` file.

Features included out of the box:

- Free and automatic SSL provided by [Caddy](https://caddyserver.com/docs/caddyfile)
- Separate domains for frontend and admin sites
- Examples included for redirects, Basic Auth, multi-store routing
- Easily add routes to your other sites

**Do not try to run a dev environment and a production environment from the same working copy!**

1. Run the normal installation for the dev environment described above
1. Run `docker-compose down` to stop the development environment
1. `echo "COMPOSE_FILE=docker-compose-production.yml" >> .env` to make the production stack the default
1. `cp Caddyfile-sample Caddyfile` and edit the `Caddyfile` to reflect your domain names and Magento store codes
1. If you did not hard-code your admin domain name in `Caddyfile` edit `.env` and make sure it includes `ADMIN_HOST_NAME`
1. Run `docker-compose up -d` to launch your new production-ready environment!

**Backups, intrusion protection and other security features are not provided and are left up to you! This is simply a
web server configuration that adds an easy to configure and maintain SSL termination.**

Building
========

The Docker images are built using the [meanbee/docker-magento](https://github.com/meanbee/docker-magento) source files so to build new images first
clone the source files into this directory and then run `docker-compose build`. 

```
$ git clone https://github.com/meanbee/docker-magento.git
$ docker build -t openmage/php-dev:7.3-cli docker-magento/7.3/cli
$ docker push openmage/php-dev:7.3-cli
$ docker build -t openmage/php-dev:7.3-fpm docker-magento/7.3/fpm
$ docker push openmage/php-dev:7.3-fpm
$ docker build -t openmage/php-dev:7.3-apache docker-magento/7.3/apache
$ docker push openmage/php-dev:7.3-apache
```

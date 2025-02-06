---
title: Docker Compose
tags:
- Development
---

# Docker Compose

For a quick and easy way to get started developing on OpenMage, you can use the following one-line
command to install OpenMage with Docker Compose.

!!! info
    If you prefer a more robust development environment, consider using [DDEV](/developers/tools/ddev).

```bash
git clone https://github.com/OpenMage/magento-lts.git && cd magento-lts && dev/openmage/install.sh 
```

This will clone the OpenMage repository, create a new Docker Compose product named "openmage" and run the installation.
Visit [http://openmage-7f000001.nip.io/](http://openmage-7f000001.nip.io/) and start coding!

!!! info
    If you want to install the `Magento Sample Data` run the command with the environment variable `SAMPLE_DATA=1` like so:

```bash
git clone https://github.com/OpenMage/magento-lts.git && cd magento-lts && SAMPLE_DATA=1 dev/openmage/install.sh
```
## Prerequisites

- Install [Docker](https://docs.docker.com/get-docker/)
- Port 80 on your host must be unused. See [Environment Variables](#environment-variables) below if you need to use another port.

## Tips

See [colinmollenhour/docker-openmage](https://github.com/colinmollenhour/docker-openmage) for more information
on the containers used in this setup, but here are some quick tips:

- You can start the cron task using `docker compose up -d cron`.
- The `cli` service contains many useful tools like `composer`, `magerun`, `modman`, `mageconfigsync` and more.
- XDebug is enabled using `remote_connect_back=1` with `idekey=phpstorm`. Customize this in `.env` if needed as described below.

Here are some common commands you may wish to try (from the `dev/openmage` directory):

```
$ docker compose run --rm -u $(id -u):$(id -g) cli composer show
$ docker compose run --rm -u $(id -u):$(id -g) cli bash
$ docker compose run --rm cli magerun sys:check
$ docker compose run --rm cli magerun cache:clean
$ docker compose run --rm cli magerun db:console
$ docker compose exec mysql mysql
```

- *The cli container runs as `www-data` by default so use `-u $(id -u):$(id -g)` with composer so that the container will create/modify files with your user permissions to avoid file permission errors in your IDE.*
- *Always use `run --rm` with the cli container to avoid creating lots of orphan containers.*

## Environment Variables

You can override some defaults using environment variables defined in a file (that you must create) at `dev/openmage/.env`.

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
- `ADMIN_PASSWORD` (must be 14 characters or more)
- `ADMIN_FIRSTNAME`
- `ADMIN_LASTNAME`
- `MAGE_IS_DEVELOPER_MODE`
    - `1` is used by default, set to `0` to disable
- `CURRENCY`
    - `USD` is used by default
- `LOCALE`
    - `en-US` is used by default
- `TIMEZONE`
    - `America/New_York` is used by default
- `MYSQL_PORT`
    - `3306` is used by default
- `PM_MAX_CHILDREN` - Tune to your environment and needs - see [PHP-FPM configuration](https://www.php.net/manual/en/install.fpm.configuration.php)
- `PM_START_SERVERS`
- `PM_MIN_SPARE_SERVERS`
- `PM_MAX_SPARE_SERVERS`

## Uninstalling / Starting Over

If you want to start fresh, wipe out your installation with the following command (from the `dev/openmage` directory):

```
$ docker compose down --volumes && rm -f ../../app/etc/local.xml
```

## HTTPS / Production

You can easily have a multi-store SSL-protected environment using Docker with the `docker-compose-production.yml` file.

Features included out of the box:

- Free and automatic SSL provided by [Caddy](https://caddyserver.com/docs/caddyfile)
- Separate domains for frontend and admin sites
- Examples included for redirects, Basic Auth, multi-store routing
- Easily add routes to your other sites
- Root static assets (e.g. robots.txt) in a separate directory for each store view

!!! warning
    **Do not try to run a dev environment and a production environment from the same working copy!**

If using OpenMage as a composer dependency, to avoid files being overwritten by composer upon updating OpenMage,
it is recommended to copy the following files into your own project root and modify them as needed:

- `dev/openmage/docker-compose-production.yml` --> `docker-compose.yml`
- `dev/openmage/nginx-admin.conf` --> `nginx-admin.conf`
- `dev/openmage/nginx-frontend.conf` --> `nginx-frontend.conf`
- `dev/openmage/Caddyfile-sample` --> `Caddyfile`
- `pub/admin/` --> `static/admin/`
- `pub/default/{favicon.ico,robots.txt}` --> `static/default/`

Then perform the following steps:

1. `echo "COMPOSE_FILE=docker-compose-production.yml" >> .env` to make the production stack the default
1. Add `BASE_URL` and `ADMIN_URL` to your `.env` file
1. `cp Caddyfile-sample Caddyfile` and edit the `Caddyfile` to reflect your domain names and Magento store codes
1. If you did not hard-code your admin domain name in `Caddyfile` edit `.env` and make sure it includes `ADMIN_HOST_NAME`
1. Run `docker compose up -d` to launch your new production-ready environment!
1. Load your existing database into the MySQL container volume and copy an existing `local.xml` file into the `app/etc/` subdirectory of your OpenMage root (e.g. `pub/app/etc/local.xml` for composer installations with default `magento-root-dir`).
    1. OR copy `dev/openmage/install.sh` into your root directory and run it to create a fresh installation.

Environment variables supported by the `docker-compose-production.yml` file and `install.sh` which may be set in `.env`
when installing a new production environment:

- `SRC_DIR=./pub` - relative path to the OpenMage root - corresponds to the composer `magento-root-dir`
- `STATIC_DIR=./static` - relative path to the directory which contains custom static files to be served from the root - must contain a subdirectory for `admin` and each store view.
- `BASE_URL=https://frontend.example.com/` (overrides `HOST_NAME` and `HOST_PORT`)
- `ADMIN_URL=https://backend.exmaple.com/` (overrides `ADMIN_HOST_NAME` and `ADMIN_HOST_PORT`)

!!! warning
    Backups, intrusion protection and other security features are not provided and are left up to you! This is simply a
    web server configuration that adds an easy to configure and maintain SSL termination.

### Adding more store views

1. Create your new website and/or store codes in OpenMage.
2. Create new root static asset directories in your static asset directory such as `static/store1`, `static/store2`, etc...
3. Edit `Caddyfile` to map your domain name to the appropriate `runcode` and `runtype`.
4. Configure the URLs in the System > Configuration.
5. Set up your DNS and relaunch Caddy (`docker compose restart caddy`).

Mapping paths to different stores can be done using additional `reverse_proxy` declarations. See `@customfrontend` as an example.
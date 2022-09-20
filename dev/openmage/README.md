OpenMage Dev Environment
===

With these files you can have a fully operational OpenMage LTS development environment in ONE step!

**NOTE: This is not for production use!**

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

See [meanbee/docker-magento](https://github.com/meanbee/docker-magento) for more information on the containers
used in this setup, but here are some quick tips:

- You can start the cron task using `docker-compose up -d cron`.
- The `cli` service contains many useful tools like `composer`, `magerun`, `modman`, `mageconfigsync` and more.
- XDebug is enabled using `remote_connect_back=1` with `idekey=phpstorm`. Customize this in `docker-compose.yml` if needed.

Here are some common commands you may wish to try:

```
$ docker-compose run --rm cli magerun sys:check
$ docker-compose run --rm cli magerun cache:clean
$ docker-compose run --rm cli magerun db:console
$ docker-compose exec mysql mysql
```

Environment Variables
---

You can override some defaults using environment variables defined in a file that you must create at `dev/openmage/.env`.

- `HOST_NAME=your-preferred-hostname`
  - `openmage-7f000001.nip.io` is used by default to resolve to `127.0.0.1`. See [nip.io](https://nip.io) for more info.
- `HOST_PORT=8888`
   - `80` is used by default
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

Building
===

The Docker images are built using the [meanbee/docker-magento](https://github.com/meanbee/docker-magento) source files so to build new images first
clone the source files into this directory and then run `docker-compose build`.

```
$ git clone https://github.com/meanbee/docker-magento.git
$ docker build -t openmage/php-dev:7.3-cli docker-magento/7.3/cli
$ docker push openmage/php-dev:7.3-cli
$ docker build -t openmage/php-dev:7.3-apache docker-magento/7.3/apache
$ docker push openmage/php-dev:7.3-apache
```

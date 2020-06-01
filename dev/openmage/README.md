OpenMage Dev Environment
===

With these files you can have a fully operational OpenMage LTS development environment in three easy steps!

**NOTE: This is not for production use!**

For a more robust development environment that supports https, please consider using [ddev](https://ddev.readthedocs.io/en/stable/users/cli-usage/#magento-1-quickstart).

## Prerequisites

- Install [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/)
- Clone the OpenMage LTS repo to your location of choice

## Step 1

Change to this directory (`dev/openmage`) and run the following commands to start MySQL and Apache:

```
$ docker-compose pull
$ docker-compose up -d mysql apache
```

Wait about 15 seconds for MySQL to initialize. You can check the logs to make sure there are no issues:

```
$ docker-compose logs
```

## Step 2

Setup your `/etc/hosts` file to point `openmage.docker` to the correct IP address. If developing on a local instance
this might work to use `127.0.0.1`, otherwise if you are using a remote machine or virtual machine you may need to use
a different IP address.

## Step 3

You are ready to run the Magento installation!

Just run the following command to install via CLI:

```
$ docker-compose run --rm cli sudo -u www-data php install.php \
  --license_agreement_accepted yes \
  --locale en_US \
  --timezone America/New_York \
  --default_currency USD \
  --db_host mysql \
  --db_name magento \
  --db_user magento \
  --db_pass magento \
  --url 'http://openmage.docker/' \
  --use_rewrites yes \
  --use_secure no \
  --secure_base_url 'http://openmage.docker/' \
  --use_secure_admin no \
  --admin_lastname User \
  --admin_firstname OpenMage  \
  --admin_email admin@example.com \
  --admin_username admin \
  --admin_password v3ryl0ngpassw0rd
```

Or you can visit [http://openmage.docker/index.php/install/](http://openmage.docker/index.php/install/) to start the web-based installer.
Use `mysql` as the database host and use `magento` for the database name, database username and database password.

Setup is complete! Visit [http://openmage.docker/](http://openmage.docker/) and start coding!

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

Wiping
---

If you want to start fresh, wipe out your installation with the following:

```
$ docker-compose down --volumes
$ rm ../../app/etc/local.xml
```

Building
===

The Docker images are built using the [meanbee/docker-magento](https://github.com/meanbee/docker-magento) source files so to build new images first
clone the source files into this directory and then run `docker-compose build`. 

```
$ git clone https://github.com/meanbee/docker-magento.git
$ docker-compose build
```

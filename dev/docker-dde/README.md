# OpenMage Docker-Dev-Environment

## Prerequisites
- Install [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/)
- Install [DDE](https://github.com/sandstein/docker-dev-environment)
- Port 80 on your host must be unused

## Install

### Docker containers
- copy `dev/docker-dde/config/.env-sample` to `.env` and adjust it to load all containers you need
- __Note:__ when changing the php version you need to modify virtual host file too

### Apache VHOST

- copy `dev/docker-dde/config/openmage.conf` to `<path_to_dde>/config/apache-24/conf.d`

If you have successfully installed DDE start your containers with

```bash
$ dde-start
```

### Initial setup
To install OpenMage run

```bash
$ dde-cli dev/docker-dde/init-dev-environment.sh
```

### Final steps
- install with or w/o sample data
- set your admin credentials
- open [http://openmage.localhost](openmage.localhost)

#### Recommendation

- add `mailhog` to `.env`
- use a [SMTP-extension](https://github.com/aschroder/Magento-SMTP-Pro-Email-Extension])
  - host: mailhog
  - port: 1025
- check you mails at [http://mailhog.localhost](mailhog.localhost)
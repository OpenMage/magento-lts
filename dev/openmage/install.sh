#!/bin/bash
set -e
dir=$(dirname "${BASH_SOURCE[0]}")
cd $dir

# Detect "docker compose" or "docker-compose"
dc="docker compose"
if ! docker compose >/dev/null 2>&1; then
  if ! command -v docker-compose >/dev/null 2>&1 ; then
    echo "Please first install docker-compose."
  else
    dc="docker-compose"
  fi
fi
test -f .env && source .env

SRC_DIR=${SRC_DIR:-../..}
HOST_PORT=":${HOST_PORT:-80}"
test "$HOST_PORT" = ":80" && HOST_PORT=""
BASE_URL="${BASE_URL:-http://${HOST_NAME:-openmage-7f000001.nip.io}${HOST_PORT}/}"
ADMIN_HOST_PORT=":${ADMIN_HOST_PORT:-81}"
test "$ADMIN_HOST_PORT" = ":80" && ADMIN_HOST_PORT=""
ADMIN_URL="${ADMIN_URL:-http://${ADMIN_HOST_NAME:-openmage-admin-7f000001.nip.io}${ADMIN_HOST_PORT}/}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@example.com}"
ADMIN_USERNAME="${ADMIN_USERNAME:-admin}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-veryl0ngpassw0rd}"
MYSQL_DATABASE="${MYSQL_DATABASE:-openmage}"

if [[ "$1" = "--reset" ]]; then
  echo "Wiping previous installation..."
  cd $dir && $dc down --volumes --remove-orphans && rm -f ${SRC_DIR}/app/etc/local.xml
fi
if test -f ${SRC_DIR}/app/etc/local.xml; then
  echo "Already installed!";
  if [[ "$1" != "--reset" ]]; then
    mysql_server_ip=$($dc exec php-fpm getent hosts mysql | awk '{print $1}')
    if [[ -z $mysql_server_ip ]]; then
      echo "Services are not running.. Start containers with 'docker-compose up -d' or run with '--reset' to start fresh."
      exit 1
    fi
    echo "Admin URL: ${ADMIN_URL}admin"
    echo "Admin login: $ADMIN_USERNAME : $ADMIN_PASSWORD"
    echo "Frontend URL: ${BASE_URL}"
    echo "MySQL server IP: $mysql_server_ip"
    echo "To start a clean installation run: $0 --reset"
    exit 1
  fi
fi

if [[ ${#ADMIN_PASSWORD} -lt 14 ]]; then
  echo "Admin password must be at least 14 characters."
  exit 1
fi

echo "Preparing filesystem..."
mkdir -p ${SRC_DIR}/vendor
$dc run --rm --no-deps cli chgrp 33 app/etc var vendor
$dc run --rm --no-deps cli chgrp -R 33 media
$dc run --rm --no-deps cli chmod g+ws app/etc var vendor
$dc run --rm --no-deps cli mkdir -p var/cache var/log var/locks var/session
$dc run --rm --no-deps cli chmod -R g+ws media var/cache var/log var/locks var/session

echo "Starting services..."
$dc up -d mysql redis php-fpm
sleep 4
for i in $(seq 1 20); do
  sleep 1
  $dc exec mysql mysql -e 'show databases;' 2>/dev/null | grep -qF "$MYSQL_DATABASE" && break
done

echo "Installing Composer dependencies..."
$dc run --rm -u "$(id -u):$(id -g)" cli composer install --no-progress

echo "Installing OpenMage LTS..."
$dc run --rm cli php install.php \
  --license_agreement_accepted yes \
  --locale "${LOCALE:-en_US}" \
  --timezone "${TIMEZONE:-America/New_York}" \
  --default_currency "${CURRENCY:-USD}" \
  --db_host mysql \
  --db_name "$MYSQL_DATABASE" \
  --db_user "${MYSQL_USER:-openmage}" \
  --db_pass "${MYSQL_PASSWORD:-openmage}" \
  --url "$BASE_URL" \
  --use_rewrites yes \
  --use_secure "$([[ $BASE_URL == https* ]] && echo yes || echo no)" \
  --secure_base_url "$BASE_URL" \
  --use_secure_admin "$([[ $ADMIN_URL == https* ]] && echo yes || echo no)" \
  --skip_url_validation \
  --admin_firstname "${ADMIN_FIRSTNAME:-OpenMage}"  \
  --admin_lastname "${ADMIN_LASTNAME:-User}" \
  --admin_email "$ADMIN_EMAIL" \
  --admin_username "$ADMIN_USERNAME" \
  --admin_password "$ADMIN_PASSWORD"

# Update URL config to split frontend/admin
$dc exec mysql mysql -e "
INSERT INTO core_config_data (scope, scope_id, path, value) VALUES
('default',0,'admin/url/use_custom','1'),
('stores',0,'web/unsecure/base_url','$ADMIN_URL'),
('stores',0,'web/secure/base_url','$ADMIN_URL');
" "$MYSQL_DATABASE"
rm -rf ${SRC_DIR}/var/cache/*

echo "Starting web services..."
$dc up -d frontend admin cron
if command -v curl >/dev/null 2>&1; then
  for i in $(seq 1 20); do
    sleep 1
    curl --silent --fail ${BASE_URL} >/dev/null && break
  done
  curl --silent --show-error --fail ${BASE_URL} || true
fi

echo ""
echo "Setup is complete!"
echo "Admin URL: ${ADMIN_URL}admin"
echo "Admin login: $ADMIN_USERNAME : $ADMIN_PASSWORD"
echo "Frontend URL: ${BASE_URL}"
echo "MySQL server IP: $($dc exec php-fpm getent hosts mysql | awk '{print $1}')"

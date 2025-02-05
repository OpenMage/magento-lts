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

echo "Installing Composer dependencies..."
$dc run --rm -u "$(id -u):$(id -g)" cli composer install --no-progress

for i in $(seq 1 30); do
  sleep 1
  $dc exec mysql mysql -e 'show databases;' 2>/dev/null | grep -qF "$MYSQL_DATABASE" && break
  echo "Waiting for MySQL to be ready..."
done

if [[ -n "${SAMPLE_DATA:-}" ]]; then
  echo "Installing Sample Data..."

  SAMPLE_DATA_KEEP_FLAG="${SAMPLE_DATA_KEEP_FLAG:-0}"
  SAMPLE_DATA_URL=https://github.com/Vinai/compressed-magento-sample-data/raw/master/compressed-magento-sample-data-1.9.2.4.tgz
  SAMPLE_DATA_DIRECTORY="${SRC_DIR}/var/sample_data"
  SAMPLE_DATA_FILE=sample_data.tgz

  if [[ ! -d "${SAMPLE_DATA_DIRECTORY}" ]]; then
    mkdir -p "${SAMPLE_DATA_DIRECTORY}"
  fi

  if [[ ! -f "${SAMPLE_DATA_DIRECTORY}/${SAMPLE_DATA_FILE}" ]]; then
    echo "Downloading Sample Data..."
    wget "${SAMPLE_DATA_URL}" -O "${SAMPLE_DATA_DIRECTORY}/${SAMPLE_DATA_FILE}"
  fi

  echo "Uncompressing Sample Data..."
  tar xf "${SAMPLE_DATA_DIRECTORY}/${SAMPLE_DATA_FILE}" -C "${SAMPLE_DATA_DIRECTORY}"

  echo "Copying Sample Data into the OpenMage directory..."
  cp -r "${SAMPLE_DATA_DIRECTORY}"/magento-sample-data-1.9.2.4/media/* "${SRC_DIR}/media/"
  cp -r "${SAMPLE_DATA_DIRECTORY}"/magento-sample-data-1.9.2.4/skin/* "${SRC_DIR}/skin/"

  echo "Importing Sample Data into the database..."
  $dc exec -T mysql mysql ${MYSQL_DATABASE} < "${SAMPLE_DATA_DIRECTORY}"/magento-sample-data-1.9.2.4/magento_sample_data_for_1.9.2.4.sql

  # remove sample data
  if [[ ${SAMPLE_DATA_KEEP_FLAG} -eq 1 ]]; then
    echo "Removing uncompressed files..."
    rm -rf "${SAMPLE_DATA_DIRECTORY}/magento-sample-data-1.9.2.4/"
  else
    echo "Removing sample data..."
    rm -rf "${SAMPLE_DATA_DIRECTORY}"
  fi
fi

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
  --enable_charts 'yes' \
  --skip_url_validation \
  --admin_firstname "${ADMIN_FIRSTNAME:-OpenMage}"  \
  --admin_lastname "${ADMIN_LASTNAME:-User}" \
  --admin_email "$ADMIN_EMAIL" \
  --admin_username "$ADMIN_USERNAME" \
  --admin_password "$ADMIN_PASSWORD"

# Update URL config to split frontend/admin
$dc exec mysql mysql -e "
DELETE FROM core_config_data WHERE path IN ('admin/url/use_custom', 'web/unsecure/base_url', 'web/secure/base_url');
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
  curl --silent --show-error --fail ${BASE_URL} -o /dev/null || echo "Frontend test failed: ${BASE_URL}"
fi

echo ""
echo "Setup is complete!"
echo "Admin URL: ${ADMIN_URL}admin"
echo "Admin login: $ADMIN_USERNAME : $ADMIN_PASSWORD"
echo "Frontend URL: ${BASE_URL}"
echo "MySQL server IP: $($dc exec php-fpm getent hosts mysql | awk '{print $1}')"

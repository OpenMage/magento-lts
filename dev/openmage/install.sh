#!/bin/bash
set -e
dir=$(dirname "${BASH_SOURCE[0]}")
cd $dir

# Detect "docker compose" or "docker-compose"
dc="docker compose"
if ! docker compose --help >/dev/null; then
  if ! command -v docker-compose 2>&1 >/dev/null; then
    echo "Please first install docker-compose."
  else
    dc="docker-compose"
  fi
fi
test -f .env && source .env

HOST_PORT=":${HOST_PORT:-80}"
test "$HOST_PORT" = ":80" && HOST_PORT=""
BASE_URL=${BASE_URL:-"http://${HOST_NAME:-openmage-7f000001.nip.io}${HOST_PORT}/"}
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@example.com}"
ADMIN_USERNAME="${ADMIN_USERNAME:-admin}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-veryl0ngpassw0rd}"

if test -f ../../app/etc/local.xml; then
  echo "Already installed!";
  if [[ "$1" = "--reset" ]]; then
    echo "Wiping previous installation..."
    cd $dir && $dc down --volumes && rm ../../app/etc/local.xml
  else
    echo "Visit ${BASE_URL}admin and login with '$ADMIN_USERNAME' : '$ADMIN_PASSWORD'"
    echo "MySQL server IP: $($dc exec apache getent hosts mysql | awk '{print $1}')"
    echo "To start a clean installation run: $0 --reset"
    exit 1
  fi
fi

echo "Preparing filesystem..."
chmod 777 ../../app/etc ../../media ../../var
chmod g+s ../../app/etc ../../media ../../var
$dc run --rm --no-deps cli mkdir -p var/cache var/log var/locks var/session

echo "Starting services..."
$dc up -d mysql apache
sleep 4
for i in $(seq 1 20); do
  sleep 1
  $dc exec mysql mysql -e 'show databases;' 2>/dev/null | grep -qF 'openmage' && break
done

echo "Installing OpenMage LTS..."
$dc run --rm cli php install.php \
  --license_agreement_accepted yes \
  --locale en_US \
  --timezone America/New_York \
  --default_currency USD \
  --db_host mysql \
  --db_name openmage \
  --db_user openmage \
  --db_pass openmage \
  --url "$BASE_URL" \
  --use_rewrites yes \
  --use_secure no \
  --secure_base_url "$BASE_URL" \
  --use_secure_admin no \
  --skip_url_validation \
  --admin_firstname OpenMage  \
  --admin_lastname User \
  --admin_email "$ADMIN_EMAIL" \
  --admin_username "$ADMIN_USERNAME" \
  --admin_password "$ADMIN_PASSWORD"

echo ""
echo "Setup is complete!"
echo "Visit ${BASE_URL}admin and login with '$ADMIN_USERNAME' : '$ADMIN_PASSWORD'"
echo "MySQL server IP: $($dc exec apache getent hosts mysql | awk '{print $1}')"

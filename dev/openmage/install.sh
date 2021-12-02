#!/bin/bash
set -e
dir=$(dirname "${BASH_SOURCE[0]}")
cd $dir
test -f .env && source .env

chmod 777 ../../app/etc ../../media ../../var

docker-compose up -d mysql apache
sleep 4

echo "Starting services..."
for i in $(seq 1 20); do
  sleep 1
  docker exec openmage_mysql_1 mysql -e 'show databases;' 2>/dev/null | grep -qF 'openmage' && break
done

HOST_PORT=":${HOST_PORT:-80}"
test "$HOST_PORT" = ":80" && HOST_PORT=""
BASE_URL=${BASE_URL:-"http://${HOST_NAME:-openmage-7f000001.nip.io}${HOST_PORT}/"}
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@example.com}"
ADMIN_USERNAME="${ADMIN_USERNAME:-admin}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-veryl0ngpassw0rd}"

echo "Installing OpenMage LTS..."
docker-compose run --rm cli php install.php \
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
echo "MySQL server IP: $(docker exec openmage_apache_1 getent hosts mysql | awk '{print $1}')"

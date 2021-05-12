#!/bin/bash

#
# docker settings which can be modified if desired
#
DB_SCHEMA=openmage

#
# docker settings which should not be altered
#
DB_HOST=mysql-80
PROJECT_DIR=$(realpath "$(dirname "$0")"/../..)

#
# init database
#
echo "Initialising database"
echo "CREATE SCHEMA IF NOT EXISTS \`${DB_SCHEMA}\` DEFAULT CHARACTER SET utf8;" | mysql -u root -h ${DB_HOST}

#
# install dependencies
#
echo "Installing dependencies"
cd "${PROJECT_DIR}" || exit
composer install --prefer-source

read -r -p "Install sample data? [y/N]" INSTALL_SAMPLE_DATA
INSTALL_SAMPLE_DATA=${INSTALL_SAMPLE_DATA,,} # to lower
if [[ $INSTALL_SAMPLE_DATA =~ ^(yes|y) ]]; then
  if [[ ! -d "${PROJECT_DIR}/backups" ]]; then
    echo "Creating backup directory"
    mkdir -p "${PROJECT_DIR}/backups"
  fi
  cd "${PROJECT_DIR}/backups" || exit
  if [[ ! -f "${PROJECT_DIR}/backups/magento-sample-data-1.9.2.4.zip" ]]; then
    echo "Downloading sample data"
    wget  https://github.com/sreichel/magento-1-compressed-sample-data/raw/master/magento-sample-data-1.9.2.4.zip
  fi
  echo "Uncompress sample data"
  unzip  magento-sample-data-1.9.2.4.zip;
  echo "Copy sample data"
  cp -r magento-sample-data-1.9.2.4/* "${PROJECT_DIR}/";
  echo "Import sample data"
  mysql -u root -h ${DB_HOST} ${DB_SCHEMA} < "${PROJECT_DIR}/backups/magento-sample-data-1.9.2.4/magento_sample_data_for_1.9.2.4.sql"
  echo "Remove sample data"
  rm -rf magento-sample-data-1.9.2.4;
fi

cd "${PROJECT_DIR}" || exit
read -r -p "Admin user [admin]:" ADMIN_USER
ADMIN_USER=${ADMIN_USER:-admin}
read -r -p "Admin firstname [John]:" ADMIN_FIRSTNAME
ADMIN_FIRSTNAME=${ADMIN_FIRSTNAME:-John}
read -r -p "Admin lastname [Doe]:" ADMIN_LASTNAME
ADMIN_LASTNAME=${ADMIN_FIRSTNAME:-Doe}
read -r -p "Admin email [admin@example.com]:" ADMIN_EMAIL
ADMIN_EMAIL=${ADMIN_EMAIL:-admin@example.com}
read -r -p "Admin password [password123password123]:" ADMIN_PASSWORD
ADMIN_PASSWORD=${ADMIN_PASSWORD:-password123password123}

php -f install.php -- \
  --license_agreement_accepted 'yes' \
  --locale 'de_DE' \
  --timezone 'Europe/Berlin' \
  --db_host ${DB_HOST} \
  --db_name ${DB_SCHEMA} \
  --db_user 'root' \
  --db_pass '' \
  --db_prefix '' \
  --url 'http://openmage.localhost/' \
  --use_rewrites 'yes' \
  --use_secure 'no' \
  --secure_base_url '' \
  --use_secure_admin 'no' \
  --admin_username "${ADMIN_USER}" \
  --admin_lastname "${ADMIN_LASTNAME}" \
  --admin_firstname "${ADMIN_FIRSTNAME}" \
  --admin_email "${ADMIN_EMAIL}" \
  --admin_password "${ADMIN_PASSWORD}" \
  --session_save 'files' \
  --admin_frontname 'admin' \
  --backend_frontname 'admin' \
  --default_currency 'EUR' \
  --skip_url_validation 'yes'


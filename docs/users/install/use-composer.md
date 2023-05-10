# Composer Install

## Create new project

```bash
composer init
```

## Configuration

**The below options are required.** You can see all options [here](https://github.com/AydinHassan/magento-core-composer-installer#configuration).

```bash
# Allow composer to apply patches to dependencies of magento-lts
composer config --json extra.enable-patching true

# Configure Magento core composer installer to use magento-lts as the Magento source package
composer config extra.magento-core-package-type magento-source

# Configure the root directory that magento-lts will be installed to, such as "pub", "htdocs", or "www"
composer config extra.magento-root-dir pub
```

## Require `magento-core-composer-installer`

``` bash
# PHP 7
composer require "aydin-hassan/magento-core-composer-installer":"~2.0.0"

# PHP 8
composer require "aydin-hassan/magento-core-composer-installer":"^2.1.0"
```

<small>Note: be sure to select `y` if composer asks you to trust `aydin-hassan/magento-core-composer-installer`.</small>

## Require `magento-lts`

```bash
# Latest tagged v20 series release
composer require "openmage/magento-lts":"^20.0.0"

# Legacy v19 tagged release (Magento 1.9.4.x drop-in replacement supported until April 4, 2025)
composer require "openmage/magento-lts":"^19.4.0"

# Latest on "main" development branch
composer require "openmage/magento-lts":"dev-main"

# Latest on "next" development branch
composer require "openmage/magento-lts":"dev-next"
```

<small>Note: be sure to select `y` if composer asks you to trust `magento-hackathon/magento-composer-installer` or `cweagans/composer-patches`.</small>

## Optimization

When deploying to a production environment, it's recommended to optimize Composer's autoloader to speed up classes lookup time:

```bash
composer dump-autoload --optimize
```

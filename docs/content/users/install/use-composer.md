# Composer Install

## Create new project

```bash
composer init
```

## Configuration

**The below options are required.** You can see all options [here](https://github.com/AydinHassan/magento-core-composer-installer#configuration).

Allow composer to apply patches to dependencies of magento-lts
```bash
composer config --json extra.enable-patching true
```

Configure Magento core composer installer to use magento-lts as the Magento source package
```bash
composer config extra.magento-core-package-type magento-source
```

Configure root directory that magento-lts will be installed to, such as `pub`, `htdocs`, or `www`
```bash
composer config extra.magento-root-dir pub
```

## Require `magento-core-composer-installer`[^1]

PHP 7
``` bash
composer require "aydin-hassan/magento-core-composer-installer":"~2.0.0"
```

PHP 8
``` bash
composer require "aydin-hassan/magento-core-composer-installer":"^2.1.0"
```

## Require `magento-lts`[^1]

Latest tagged v20 series release
```bash
composer require "openmage/magento-lts":"^20.0.0"
```

Legacy v19 tagged release (Magento 1.9.4.x drop-in replacement supported until April 4, 2025)
```bash
composer require "openmage/magento-lts":"^19.4.0"
```

Latest on "main" development branch
```bash
composer require "openmage/magento-lts":"dev-main"
```

Latest on "next" development branch
```bash
composer require "openmage/magento-lts":"dev-next"
```

## Optimization

When deploying to a production environment, it's recommended to optimize Composer's autoloader to speed up classes lookup time:

```bash
composer dump-autoload --optimize
```

[^1]: <small>Select `y` if composer asks you to trust `magento-hackathon/magento-composer-installer` or `cweagans/composer-patches`.</small>

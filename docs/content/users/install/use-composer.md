---
tags:
- Install
---

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

## Specify PHP engine version

This is not strictly required, but if you are running composer with a different PHP version than your target environment,
specifying the engine version will ensure that the correct dependencies are installed for your target environment,
ignoring the PHP engine used to run composer.

```bash
composer config platform.php 8.4
```

## Require `magento-core-composer-installer`

=== "PHP 8"

    Only for PHP 8[^1]
    ``` bash
    composer require "aydin-hassan/magento-core-composer-installer":"~2.1.0"
    ```

=== "PHP 7"

    Only for PHP 7[^1]
    ``` bash
    composer require "aydin-hassan/magento-core-composer-installer":"^2.0.0"
    ```

=== "PHP 7/8"

    For PHP 7 and 8[^1]
    ``` bash
    composer require "aydin-hassan/magento-core-composer-installer":"~2.0.0 || ^2.1.0"
    ```

## Require `magento-lts`

=== "v20"

    Latest tagged `v20` series release[^1]
    ```bash
    composer require "openmage/magento-lts":"^20.0.0"
    ```

=== "v19"

    Legacy `v19` tagged release (Magento 1.9.4.x drop-in replacement supported until April 4, 2025)[^1]
    ```bash
    composer require "openmage/magento-lts":"^19.4.0"
    ```

=== "dev-main"

    Latest on `main` development branch[^1]
    ```bash
    composer require "openmage/magento-lts":"dev-main"
    ```

=== "dev-next"

    Latest on `next` development branch[^1]
    ```bash
    composer require "openmage/magento-lts":"dev-next"
    ```

## Optimization

When deploying to a production environment, it's recommended to optimize Composer's autoloader to speed up classes lookup time:

```bash
composer dump-autoload --optimize
```

[^1]: <small>Select `y` to trust `magento-hackathon/magento-composer-installer` or `cweagans/composer-patches`.</small>

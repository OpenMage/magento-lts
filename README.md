<p>
<a href="https://travis-ci.org/openmage/magento-lts"><img src="https://travis-ci.org/openmage/magento-lts.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/openmage/magento-lts"><img src="https://poser.pugx.org/openmage/magento-lts/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/openmage/magento-lts"><img src="https://poser.pugx.org/openmage/magento-lts/license.svg" alt="License"></a>
</p>

# Magento - Long Term Support

This repository is the home of an **unofficial** community-driven project. It's goal is to be a dependable alternative
to the Magento CE official releases which integrates improvements directly from the community while maintaining a high
level of backwards compatibility to the official releases.

**Pull requests with unofficial bug fixes and security patches from the community are encouraged and welcome!**

Though Magento does not follow [Semantic Versioning](http://semver.org/) we aim to provide a workable system for
dependency definition. Each Magento `1.<minor>.<revision>` release will get its own branch (named `1.<minor>.<revision>.x`)
that will be independently maintained with upstream patches and community bug fixes for as long as it makes sense
to do so (based on available resources). For example, Magento version `1.9.3.4` was merged into the `1.9.3.x` branch.

Note, the branches older than `1.9.4.x` and that were created before this strategy came into practice are **not maintained**.

## Requirements

- PHP 7.0+ (PHP 7.3 and OpenSSL extension strongly recommended)
- MySQL 5.6+ (8.0+ Recommended)

If using php 7.2+ then mcrypt needs to be disabled in php.ini or pecl to fallback on mcryptcompat and phpseclib. mcrypt is deprecated from 7.2+ onwards.

## Installation

### Using Composer
Download the latest archive and extract it, clone the repo, or add a composer dependency to your existing project like so:

```
composer require openmage/magento-lts":"^19.4.0"
```

To get the latest changes use:

```
composer require openmage/magento-lts":"dev-main"
```

<small>Note: `dev-main` is just an alias for current `1.9.4.x` branch and may change</small>

### Using Git

If you want to contribute to the project:

```
git init
git remote add origin https://github.com/<YOUR GIT USERNAME>/magento-lts
git pull origin master
git remote add upstream https://github.com/OpenMage/magento-lts
git pull upstream 1.9.4.x
git add -A && git commit
```

[More Information](http://openmage.github.io/magento-lts/install.html)

## Changes

Most important changes will be listed here, all other changes since `19.4.0` can be found in
[release](https://github.com/OpenMage/magento-lts/releases) notes.

### Performance
<small>ToDo: Please add performance related changes as run-time cache, ...</small>

### New Config Options
- `admin/design/use_legacy_theme`
- `admin/emails/admin_notification_email_template`
- `catalog/product_image/progressive_threshold`

### New Events
- `adminhtml_block_widget_form_init_form_values_after`
- `adminhtml_block_widget_tabs_html_before`
- `adminhtml_sales_order_create_save_before`
- `checkout_cart_product_add_before`
- `sitemap_cms_pages_generating_before`
- `sitemap_urlset_generating_before`

### New Translations

There are some new or changed translations, if you want add them to your locale pack please check:

- `app/locale/en_US/Adminhtml_LTS.csv`
- `app/locale/en_US/Core_LTS.csv`
- `app/locale/en_US/Sales_LTS.csv`

### Removed Modules
- `Mage_Compiler`
- `Mage_GoogleBase`
- `Mage_Xmlconnect`
- `Phoenix_Moneybookers`

## Development Environment with ddev
- Install [ddev](https://ddev.com/get-started/)
- Clone the repository as described in Installation -> Using Git
- Create a ddev config using ```$ ddev config``` the defaults should be good for you
- Open .ddev/config.yaml and change the php version to 7.2
- Type ```$ ddev start``` to download and start the containers
- Navigate to https://magento-lts.ddev.site
- When you are done you can stop the test system by typing ```$ ddev stop```

## PhpStorm Factory Helper

This repo includes class maps for the core Magento files in `.phpstorm.meta.php`. 
To add class maps for installed extensions, you have to install [N98-magerun](https://github.com/netz98/n98-magerun)
and run command:

```
n98-magerun dev:ide:phpstorm:meta
```

You can add additional meta files in this directory to cover your own project files. See
[PhpStorm advanced metadata](https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html)
for more information.

## Public Communication

* [Discord](https://discord.gg/EV8aNbU) (maintained by Flyingmana)

## Maintainers

* [Lee Saferite](https://github.com/LeeSaferite)
* [David Robinson](https://github.com/drobinson)
* [Daniel Fahlke aka Flyingmana](https://github.com/Flyingmana)
* [Tymoteusz Motylewski](https://github.com/tmotyl)
* [Sven Reichel](https://github.com/sreichel)

## License

- [OSL v3.0](http://opensource.org/licenses/OSL-3.0)
- [AFL v3.0](http://opensource.org/licenses/AFL-3.0)

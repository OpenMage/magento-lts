[![All Contributors](https://img.shields.io/github/all-contributors/openmage/magento-lts?color=ee8449)](#contributors)
<a href="https://packagist.org/packages/openmage/magento-lts"><img src="https://poser.pugx.org/openmage/magento-lts/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/openmage/magento-lts"><img src="https://poser.pugx.org/openmage/magento-lts/license.svg" alt="License"></a>
<a href="https://github.com/openmage/magento-lts/actions/workflows/security-php.yml"><img src="https://github.com/openmage/magento-lts/actions/workflows/security-php.yml/badge.svg" alt="PHP Security workflow Badge" /></a>
<a href="https://github.com/OpenMage/magento-lts/actions/workflows/workflow.yml"><img src="https://github.com/OpenMage/magento-lts/actions/workflows/workflow.yml/badge.svg" alt="CI workflow Badge" /></a>

# Magento - Long Term Support

This repository is the home of an **unofficial** community-driven project. It's goal is to be a dependable alternative
to the Magento CE official releases which integrates improvements directly from the community while maintaining a high
level of backwards compatibility to the official releases.

**Pull requests with bug fixes and security patches from the community are encouraged and welcome!**

---

## Table of contents

- [Releases and Versioning](#releases-and-versioning)
  - [Currently Maintained Versions](#currently-maintained-versions)
- [Requirements](#requirements)
- [Installation](#installation)
  - [Manual Install](#manual-install)
  - [Composer](#composer)
  - [Git](#git)
- [Secure your installation](#secure-your-installation)
  - [Apache .htaccess](#apache-htaccess)
  - [Nginx](#nginx)
- [Magento 1 Compatibility](#magento-1-compatibility)
- [Changes](#changes)
  - [Between Magento 1.9.4.5 and OpenMage 19.x](#between-magento-1945-and-openmage-19x)
  - [Since OpenMage 19.5.0 / 20.1.0](#since-openmage-1950--2010)
  - [New Config Options](#new-config-options)
  - [New Events](#new-events)
  - [Changes to SOAP/WSDL](#changes-to-soapwsdl)
- [Development Environment with ddev](#development-environment-with-ddev)
- [PhpStorm Factory Helper](#phpstorm-factory-helper)
- [PhpStorm File-Watcher for SCSS files](#phpstorm-file-watcher-for-scss-files)
- [Public Communication](#public-communication)
- [Maintainers](#maintainers)
- [License](#license)
- [Contributors](#contributors-)

## Releases and Versioning

This project more strictly adheres to [Semantic Versioning](http://semver.org/) compared to the original Magento version numbering system where the "1"
was essentially a fixed number. See the [Terminology](https://github.com/OpenMage/rfcs/blob/main/accepted/0002-release-schedule.md#terminology)
section of [RFC 0002 - Release Schedule](https://github.com/OpenMage/rfcs/blob/main/accepted/0002-release-schedule.md) for more information on how the terms MAJOR, MINOR and PATCH are defined and applied.

The OpenMage team and community maintains OpenMage LTS versions as follows:

- The latest MAJOR.MINOR version always receives PATCH updates.
- The latest MAJOR version always receives MINOR updates.
- The latest MAJOR.MINOR branch for each MAJOR version receives PATCH updates for at least 2 years from the time of inception of the initial MAJOR version release.

In a nutshell:

- If you want to stay on the cutting edge with the latest improvements use the latest MAJOR version.
- If you want maximum backwards compatibility and minimal upgrade hassle use the next-latest MAJOR version so that you can still receive important security/stability/regression fixes.

### Currently Maintained Versions

- 20.x is the latest MAJOR version and will receive PATCH updates until 2 years after the date that 21.x is released.
- 19.4.x will receive PATCH updates until April 4, 2025.

## Requirements

- PHP 7.4 to 8.3
- MySQL 5.7+ (8.0+ recommended) or MariaDB
- optional: Redis 5.x, 6.x and 7.0.x are supported


- PHP extension `intl` <small>since 1.9.4.19 & 20.0.17</small>
- Command `patch` 2.7+ (or `gpatch` on MacOS/HomeBrew) <small>since 1.9.5.0 & 20.1.0</small>

## Installation

### Manual Install

Download the latest [release archive](https://github.com/OpenMage/magento-lts/releases) and extract it over your existing install. **Important:** you must download the ZIP file from a tagged version on the releases page, otherwise there will be missing dependencies.

### Composer

Step 1: Create a new composer project:

```bash
composer init
```

Step 2: Configure composer. **The below options are required.** You can see all options [here](https://github.com/AydinHassan/magento-core-composer-installer#configuration).

```bash
# Allow composer to apply patches to dependencies of magento-lts
composer config --json extra.enable-patching true

# Configure Magento core composer installer to use magento-lts as the Magento source package
composer config extra.magento-core-package-type magento-source

# Configure the root directory that magento-lts will be installed to, such as "pub", "htdocs", or "www"
composer config extra.magento-root-dir pub
```

Step 3: Require `magento-core-composer-installer`:

``` bash
# PHP 7
composer require "aydin-hassan/magento-core-composer-installer":"~2.0.0"

# PHP 8
composer require "aydin-hassan/magento-core-composer-installer":"^2.1.0"
```

<small>Note: be sure to select `y` if composer asks you to trust `aydin-hassan/magento-core-composer-installer`.</small>

Step 4: Require the appropriate version of `magento-lts`:

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

When deploying to a production environment, it's recommended to optimize Composer's autoloader to speed up classes lookup time:

```bash
composer dump-autoload --optimize
```

### Git

If you want to contribute to the project:

```bash
git init
git remote add origin https://github.com/<YOUR GIT USERNAME>/magento-lts
git pull origin main
git remote add upstream https://github.com/OpenMage/magento-lts
git pull upstream main
git add -A && git commit
```

[More Information](http://openmage.github.io/magento-lts/install.html)

## Secure your installation

Don't use common paths like /admin for OpenMage Backend URL. Don't use the path in _robots.txt_ and keep it secret. You can change it from Backend (System / Configuration / Admin / Admin Base Url) or by editing _app/etc/local.xml_:

```xml
<config>
    <admin>
        <routers>
            <adminhtml>
                <args>
                    <frontName><![CDATA[admin]]></frontName>
                </args>
            </adminhtml>
        </routers>
    </admin>
</config>
```

Don't use common file names like api.php for OpenMage API URLs to prevent attacks. Don't use the new file name in _robots.txt_ and keep it secret with your partners. After renaming the file you must update the webserver configuration as follows:

### Apache .htaccess
```
RewriteRule ^api/rest api.php?type=rest [QSA,L]
```

### Nginx
```
rewrite ^/api/(\w+).*$ /api.php?type=$1 last;`
```

## Magento 1 Compatibility

OpenMage LTS 19.4.0 is the first tagged version using the OpenMage LTS version naming system and all 19.x versions are mostly backward-compatible
with Magento 1.9.4.x.

OpenMage LTS 20.x and later have more changes that may not be 100% backward-compatible, but minimizing migration and upgrade hassle for users is always
considered an important goal and factors heavily into the changes that are accepted even when accepting changes for "MAJOR" releases, described in [Releases and Versioning](#releases-and-versioning) above.

## Changes

Most important changes will be listed here, all other changes since `19.4.0` can be found in
[release](https://github.com/OpenMage/magento-lts/releases) notes.

### Between Magento 1.9.4.5 and OpenMage 19.x

- bug fixes and PHP 7.x, 8.0, 8.1 and 8.2 compatibility
- added config cache for system.xml ([#1916](https://github.com/OpenMage/magento-lts/pull/1916))
- added frontend_type color ([#2945](https://github.com/OpenMage/magento-lts/pull/2945))
- search for "NULL" in backend grids ([#1203](https://github.com/OpenMage/magento-lts/pull/1203))
- removed `lib/flex` containing unused ActionScript "file uploader" files ([#2271](https://github.com/OpenMage/magento-lts/pull/2271))
- Mage_Catalog_Model_Resource_Abstract::getAttributeRawValue() now returns `'0'` instead of `false` if the value stored in the database is `0` ([#572](https://github.com/OpenMage/magento-lts/pull/572))
- PHP extension `intl` is required
- Deprecation errors are not suppressed anymore
- removed modules:
  - `Mage_Backup` ([#2811](https://github.com/OpenMage/magento-lts/pull/2811))
  - `Mage_Compiler`
  - `Mage_GoogleBase`
  - `Mage_PageCache` ([#2258](https://github.com/OpenMage/magento-lts/pull/2258))
  - `Mage_Poll` ([#3098](https://github.com/OpenMage/magento-lts/pull/3098))
  - `Mage_Sendfriend` ([#4274](https://github.com/OpenMage/magento-lts/pull/4274))
  - `Mage_Xmlconnect`
  - `Phoenix_Moneybookers`

_If you rely on those modules you can reinstall them with composer:_
- `Mage_Backup`: `composer require openmage/module-mage-backup`
- `Mage_PageCache`: `composer require openmage/module-mage-pagecache`
- `Mage_Poll`: `composer require openmage/module-mage-poll`
- `Mage_Sendfriend`: `composer require openmage/module-mage-sendfriend`
- `Legacy frontend themes`: `composer require openmage/legacy-frontend-themes`

### Between OpenMage 19.x and 20.x

Do not use 20.x.x if you need IE support.

- removed IE conditional comments, IE styles, IE scripts and IE eot files ([#1073](https://github.com/OpenMage/magento-lts/pull/1073))
- removed frontend default themes (default, modern, iphone, german, french, blank, blue) ([#1600](https://github.com/OpenMage/magento-lts/pull/1600))
- fixed incorrect datetime in customer block (`$useTimezone` parameter) ([#1525](https://github.com/OpenMage/magento-lts/pull/1525))
- added redis as a valid option for `global/session_save` ([#1513](https://github.com/OpenMage/magento-lts/pull/1513))
- reduce needless saves by avoiding setting `_hasDataChanges` flag ([#2066](https://github.com/OpenMage/magento-lts/pull/2066))
- removed support for `global/sales/old_fields_map` defined in XML ([#921](https://github.com/OpenMage/magento-lts/pull/921))
- enabled website level config cache ([#2355](https://github.com/OpenMage/magento-lts/pull/2355))
- made overrides of Mage_Core_Model_Resource_Db_Abstract::delete respect parent api ([#1257](https://github.com/OpenMage/magento-lts/pull/1257))
- rewrote Mage_Eav_Model_Config as cache for all eav entity and attribute reads ([#2993](https://github.com/OpenMage/magento-lts/pull/2993))

For full list of changes, you can [compare tags](https://github.com/OpenMage/magento-lts/compare/1.9.4.x...20.0).

### Since OpenMage 19.5.0 / 20.1.0

PHP 7.4 is now the minimum required version.

Most of the 3rd party libraries/modules that were bundled in our repository were removed and migrated to composer dependencies.
This allows for better maintenance and upgradability.

Specifically:
- `phpseclib`, `mcrypt_compat`, `Cm_RedisSession`, `Cm_Cache_Backend_Redis`, `Pelago_Emogrifier` ([#2411](https://github.com/OpenMage/magento-lts/pull/2411))
- Zend Framework 1 ([#2827](https://github.com/OpenMage/magento-lts/pull/2827))

If your project uses OpenMage through composer then all dependencies will be managed automatically.  
If you just extracted the release zip/tarball in your project's main folder then be sure to:
- remove the old copy of aforementioned libraries from your project, you can do that with this command:
  ```bash
  rm -rf app/code/core/Zend lib/Cm lib/Credis lib/mcryptcompat lib/Pelago lib/phpseclib lib/Zend
  ```

- download the new release zip file that is named `openmage-VERSIONNUMBER.zip`, this one is built to contain the `vendor`
  folder generated by composer, with all the dependencies in it
- extract the zip file in your project's repository as you always did

We also decided to remove our Zend_DB patches (that were stored in `app/code/core/Zend`) because they were very old and
not compatible with the new implementations made by ZF1-Future, which is much more advanced and feature rich.
This may generate a problem with `Zend_Db_Select' statements that do not use 'Zend_Db_Expr' to quote expressions.
If you see SQL errors after upgrading please remember to check for this specific issue in your code.

UPS shut down their old CGI APIs so we removed the support for it from the Mage_Usa module.

### Between OpenMage 20.x and 21.x (unreleased, available on branch `next`)

- PHP 8.2 as minimum required version
- Removed scriptaculous/dragdrop.js ([#3215](https://github.com/OpenMage/magento-lts/pull/3215))
- RWD theme: updated jQuery to 3.7.1 ([#3922](https://github.com/OpenMage/magento-lts/pull/3922))
- Unified CSRF configuration ([#3147](https://github.com/OpenMage/magento-lts/pull/3147)) and added form key validation to Contacts form ([#3146](https://github.com/OpenMage/magento-lts/pull/3144))
- Removed double span element from HTML buttons ([#3123](https://github.com/OpenMage/magento-lts/pull/3123))
- Removed all deprecated Mysql4_ classes ([#2730](https://github.com/OpenMage/magento-lts/pull/2730)). If there are any old modules/extensions in your installation that use such classes, you must run `shell/rename-mysql4-class-to-resource.php` in the command line in order to convert them. Backup all files before running the script
- Removed "admin routing compatibility mode" ([#1551](https://github.com/OpenMage/magento-lts/pull/1551))

### New Config Options

- `admin/design/use_legacy_theme`
- `admin/global_search/enable`
- `admin/emails/admin_notification_email_template`
- `catalog/product_image/progressive_threshold`
- `catalog/search/search_separator`
- `dev/log/max_level`
- `newsletter/security/enable_form_key`
- `rss/admin_order/new_period`
- `sitemap/category/lastmod`
- `sitemap/page/lastmod`
- `sitemap/product/lastmod`

### New Events

- `adminhtml_block_widget_form_init_form_values_after`
- `adminhtml_block_widget_tabs_html_before`
- `adminhtml_sales_order_create_save_before`
- `checkout_cart_product_add_before`
- `core_app_run_after`
- `mage_run_installed_exception`
- `sitemap_cms_pages_generating_before`
- `sitemap_urlset_generating_before`

[Full list of events](docs/EVENTS.md)

### Changes to SOAP/WSDL

Since `19.4.17`/`20.0.15` we changed the `targetNamespace` of all the WSDL files (used in the API modules), from `Magento` to `OpenMage`.
If your custom modules extends OpenMage's APIs with a custom WSDL file and there are some hardcoded `targetNamespace="urn:Magento"` strings, your APIs may stop working.

Please replace all occurrences of 

```
targetNamespace="urn:Magento"
```
with
```
targetNamespace="urn:OpenMage"
```
or alternatively 
```
targetNamespace="urn:{{var wsdl.name}}"
```
 to avoid any problem.

To find which files need the modification you can run this command from the root directory of your project.
```bash
grep -rn 'urn:Magento' --include \*.xml
```

## Development Environment with DDEV

- Install [ddev](https://ddev.com/get-started/)
- Clone the repository as described in installation ([Git](#git))
- Create a ddev config, defaults should be good for you
  ```bash
  ddev config
  ```
- Open `.ddev/config.yaml` and change the php version to your needs
- Download and start the containers
  ```bash
  ddev start
  ```
- Open your site in browser
  ```bash
  ddev launch
  ``` 

## PhpStorm Factory Helper

This repo includes class maps for the core Magento files in `.phpstorm.meta.php`.
To add class maps for installed extensions, you have to install [N98-magerun](https://github.com/netz98/n98-magerun)
and run command:

```bash
n98-magerun.phar dev:ide:phpstorm:meta
```

You can add additional meta files in this directory to cover your own project files. See
[PhpStorm advanced metadata](https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html)
for more information.

## PhpStorm File-Watcher for SCSS files
- install SCSS
  ```bash
  npm install -g sass
  ```
- open settings `CTRL+ALT+S` and go to File Watcher
- change default setting to:
  - Arguments:
  ```
  $FileName$:$FileParentDir$/$FileNameWithoutExtension$.css
  ```
  - Output paths to refresh:
  ```
  $FileParentDir$/$FileNameWithoutExtension$.css:$FileParentDir$/$FileNameWithoutExtension$.css.map
  ```

## Public Communication

* [Discord](https://discord.gg/EV8aNbU) (maintained by Flyingmana)

## Maintainers

* [Daniel Fahlke](https://github.com/Flyingmana)
* [David Robinson](https://github.com/drobinson)
* [Fabrizio Balliano](https://github.com/fballiano)
* [Lee Saferite](https://github.com/LeeSaferite)
* [Mohamed Elidrissi](https://github.com/elidrissidev)
* [Ng Kiat Siong](https://github.com/kiatng)
* [Tymoteusz Motylewski](https://github.com/tmotyl)

## License

- [OSL v3.0](http://opensource.org/licenses/OSL-3.0)
- [AFL v3.0](http://opensource.org/licenses/AFL-3.0)

## Contributors ✨

Thanks goes to these wonderful people ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tbody>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://magento.stackexchange.com/users/46249/sv3n"><img src="https://avatars1.githubusercontent.com/u/5022236?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>sv3n</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/LeeSaferite"><img src="https://avatars3.githubusercontent.com/u/47386?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Lee Saferite</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://colin.mollenhour.com/"><img src="https://avatars3.githubusercontent.com/u/38738?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Colin Mollenhour</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/drobinson"><img src="https://avatars1.githubusercontent.com/u/455332?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>David Robinson</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://macopedia.com/"><img src="https://avatars1.githubusercontent.com/u/515397?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tymoteusz Motylewski</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://flyingmana.name/"><img src="https://avatars3.githubusercontent.com/u/237319?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Daniel Fahlke</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://overhemden.com/"><img src="https://avatars3.githubusercontent.com/u/652395?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>SNH_NL</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/spinsch"><img src="https://avatars1.githubusercontent.com/u/519865?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Marc Romano</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://www.fabian-blechschmidt.de/"><img src="https://avatars1.githubusercontent.com/u/379680?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Fabian Blechschmidt</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/Sekiphp"><img src="https://avatars2.githubusercontent.com/u/9967016?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Luboš Hubáček</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/edannenberg"><img src="https://avatars0.githubusercontent.com/u/1352794?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Erik Dannenberg</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://srcode.nl/"><img src="https://avatars2.githubusercontent.com/u/1163348?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Jeroen Boersma</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.linkedin.com/in/lfluvisotto"><img src="https://avatars3.githubusercontent.com/u/535626?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Leandro F. L.</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/kkrieger85"><img src="https://avatars2.githubusercontent.com/u/4435523?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Kevin Krieger</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/kiatng"><img src="https://avatars1.githubusercontent.com/u/1106470?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Ng Kiat Siong</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/bob2021"><img src="https://avatars0.githubusercontent.com/u/8102829?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>bob2021</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/bastienlm"><img src="https://avatars1.githubusercontent.com/u/13004368?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Bastien Lamamy</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/DmitryFursNeklo"><img src="https://avatars3.githubusercontent.com/u/6996108?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Dmitry Furs</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/rjocoleman"><img src="https://avatars0.githubusercontent.com/u/154176?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Robert Coleman</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://milandavidek.cz/"><img src="https://avatars2.githubusercontent.com/u/4263992?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Milan Davídek</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://mattdavenport.io/"><img src="https://avatars3.githubusercontent.com/u/1127393?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Matt Davenport</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/kestraly"><img src="https://avatars3.githubusercontent.com/u/13368757?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>elfling</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/henrykbrzoska"><img src="https://avatars1.githubusercontent.com/u/4395216?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>henrykb</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/empiricompany"><img src="https://avatars0.githubusercontent.com/u/5071467?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tony</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://netalico.com/"><img src="https://avatars0.githubusercontent.com/u/2094614?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Mark Lewis</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/ericseanturner"><img src="https://avatars3.githubusercontent.com/u/42879056?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Eric Sean Turner</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://willcodeforfood.github.io/"><img src="https://avatars2.githubusercontent.com/u/1639118?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Eric Seastrand</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.ambimax.de/"><img src="https://avatars1.githubusercontent.com/u/14741874?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tobias Schifftner</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://www.simonsprankel.com/"><img src="https://avatars1.githubusercontent.com/u/930199?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Simon Sprankel</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://tomlankhorst.nl/"><img src="https://avatars0.githubusercontent.com/u/675432?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tom Lankhorst</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://shirtsofholland.com/"><img src="https://avatars0.githubusercontent.com/u/11224809?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>shirtsofholland</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/sebastianwagner"><img src="https://avatars0.githubusercontent.com/u/1701745?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>sebastianwagner</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://maximehuran.fr/"><img src="https://avatars1.githubusercontent.com/u/11380627?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Maxime Huran</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/pepijnblom"><img src="https://avatars0.githubusercontent.com/u/6009489?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Pepijn</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/manuperezgo"><img src="https://avatars0.githubusercontent.com/u/8482836?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>manuperezgo</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://www.luigifab.fr/"><img src="https://avatars1.githubusercontent.com/u/31816829?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>luigifab</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/loekvangool"><img src="https://avatars0.githubusercontent.com/u/7300472?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Loek van Gool</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/kpitn"><img src="https://avatars2.githubusercontent.com/u/41059?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>kpitn</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/kalenjordan"><img src="https://avatars2.githubusercontent.com/u/1542197?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>kalenjordan</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.ioweb.gr/en"><img src="https://avatars3.githubusercontent.com/u/20220341?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>IOWEB TECHNOLOGIES</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/fplantinet"><img src="https://avatars0.githubusercontent.com/u/2428023?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Florent</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/dvdsndr"><img src="https://avatars1.githubusercontent.com/u/13637075?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>dvdsndr</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/VincentMarmiesse"><img src="https://avatars0.githubusercontent.com/u/1949412?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Vincent MARMIESSE</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://www.proxiblue.com.au/"><img src="https://avatars2.githubusercontent.com/u/4994260?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Lucas van Staden</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://zamoroka.com/"><img src="https://avatars1.githubusercontent.com/u/9164112?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>zamoroka</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/wpdevteam"><img src="https://avatars3.githubusercontent.com/u/1577103?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>wpdevteam</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://www.storefront.be/"><img src="https://avatars1.githubusercontent.com/u/71019?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Wouter Samaey</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/vovayatsyuk"><img src="https://avatars2.githubusercontent.com/u/306080?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Vova Yatsyuk</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://hydrobuilder.com/"><img src="https://avatars3.githubusercontent.com/u/1300504?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Trevor Hartman</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/trabulium"><img src="https://avatars3.githubusercontent.com/u/1046615?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Somewhere</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.schmengler-se.de/"><img src="https://avatars1.githubusercontent.com/u/367320?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Fabian Schmengler /></b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://copex.io/"><img src="https://avatars1.githubusercontent.com/u/584168?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Roman Hutterer</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.haiku.co.nz/"><img src="https://avatars2.githubusercontent.com/u/123676?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Sergei Filippov</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/samsteele"><img src="https://avatars3.githubusercontent.com/u/10742174?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Sam Steele</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://goo.gl/WCUymp"><img src="https://avatars2.githubusercontent.com/u/59101?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Ricardo Velhote</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://royduineveld.nl/"><img src="https://avatars2.githubusercontent.com/u/1703233?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Roy Duineveld</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/roberto-ebizmarts"><img src="https://avatars0.githubusercontent.com/u/51710909?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Roberto Sarmiento Pérez</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.pierre-martin.fr/"><img src="https://avatars0.githubusercontent.com/u/75968?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Pierre Martin</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/rafdol"><img src="https://avatars2.githubusercontent.com/u/20263372?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Rafał Dołgopoł</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/rafaelpatro"><img src="https://avatars0.githubusercontent.com/u/13813964?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Rafael Patro</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://copex.io/"><img src="https://avatars3.githubusercontent.com/u/1998210?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Andreas Pointner</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/paulrodriguez"><img src="https://avatars2.githubusercontent.com/u/6373764?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Paul Rodriguez</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/ollb"><img src="https://avatars0.githubusercontent.com/u/5952064?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>ollb</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/nintenic"><img src="https://avatars0.githubusercontent.com/u/1317618?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Nicholas Graham</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/mpalasis"><img src="https://avatars0.githubusercontent.com/u/37408939?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Makis Palasis</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://magento.stackexchange.com/users/5209/mbalparda"><img src="https://avatars1.githubusercontent.com/u/3997682?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Miguel Balparda</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.ecomni.nl/"><img src="https://avatars3.githubusercontent.com/u/2143634?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Mark van der Sanden</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://binarzone.com/"><img src="https://avatars1.githubusercontent.com/u/200507?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Micky Socaci</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.binaerfabrik.de/"><img src="https://avatars3.githubusercontent.com/u/7369753?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Marvin Sengera</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/kanevbg"><img src="https://avatars3.githubusercontent.com/u/11477130?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Kostadin A.</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/julienloizelet"><img src="https://avatars3.githubusercontent.com/u/20956510?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Julien Loizelet</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://maxcluster.de/"><img src="https://avatars0.githubusercontent.com/u/1112507?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Jonas Hünig</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/jaroschek"><img src="https://avatars1.githubusercontent.com/u/470290?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Stefan Jaroschek</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://jacques.sh/"><img src="https://avatars2.githubusercontent.com/u/858611?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Jacques Bodin-Hullin</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/googlygoo"><img src="https://avatars3.githubusercontent.com/u/7078871?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Wilhelm Ellmann</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/edwinkortman"><img src="https://avatars2.githubusercontent.com/u/7047894?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Edwin.</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/drago-aca"><img src="https://avatars3.githubusercontent.com/u/14777419?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>drago-aca</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/dng-dev"><img src="https://avatars0.githubusercontent.com/u/836079?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Daniel Niedergesäß</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/davis2125"><img src="https://avatars2.githubusercontent.com/u/14129105?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>J Davis</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/damien-biasotto"><img src="https://avatars0.githubusercontent.com/u/430633?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Damien Biasotto</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/cundd"><img src="https://avatars2.githubusercontent.com/u/743122?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Daniel Corn</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://www.cieslix.com/"><img src="https://avatars0.githubusercontent.com/u/6729521?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Paweł Cieślik</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/borriglione"><img src="https://avatars2.githubusercontent.com/u/465544?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>André Herrn</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/blopa"><img src="https://avatars3.githubusercontent.com/u/3838114?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Pablo Benmaman</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/aterjung"><img src="https://avatars1.githubusercontent.com/u/3084302?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>aterjung</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/altdovydas"><img src="https://avatars3.githubusercontent.com/u/8860049?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>altdovydas</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/alissonjr"><img src="https://avatars2.githubusercontent.com/u/11911917?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Alisson Júnior</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/alexkirsch"><img src="https://avatars3.githubusercontent.com/u/9553441?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Alex Kirsch</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/SnowCommerceBrand"><img src="https://avatars3.githubusercontent.com/u/37154233?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Branden</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/PofMagicfingers"><img src="https://avatars3.githubusercontent.com/u/469501?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Pof Magicfingers</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/MichaelThessel"><img src="https://avatars1.githubusercontent.com/u/2926266?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Michael Thessel</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/JonLaliberte"><img src="https://avatars3.githubusercontent.com/u/5403662?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Jonathan Laliberte</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.linkedin.com/in/ivanchepurnyi"><img src="https://avatars2.githubusercontent.com/u/866758?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Ivan Chepurnyi</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/Ig0r-M-magic42"><img src="https://avatars1.githubusercontent.com/u/22006850?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Igor</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/EliasKotlyar"><img src="https://avatars0.githubusercontent.com/u/9529505?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Elias Kotlyar</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/Hejty1"><img src="https://avatars2.githubusercontent.com/u/53661954?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Hejty1</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/Gaelle"><img src="https://avatars2.githubusercontent.com/u/112183?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Gaelle</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.martinez-frederic.fr/"><img src="https://avatars3.githubusercontent.com/u/13019288?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Frédéric MARTINEZ</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/FaustTobias"><img src="https://avatars1.githubusercontent.com/u/48201729?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tobias Faust</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/AndresInSpace"><img src="https://avatars2.githubusercontent.com/u/14356094?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>AndresInSpace</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/boesbo"><img src="https://avatars1.githubusercontent.com/u/12744378?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Francesco Boes</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/dbachmann"><img src="https://avatars1.githubusercontent.com/u/1921769?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Daniel Bachmann</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/daim2k5"><img src="https://avatars.githubusercontent.com/u/656150?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Damian Luszczymak</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://fabrizioballiano.com/"><img src="https://avatars.githubusercontent.com/u/909743?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Fabrizio Balliano</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/jouriy"><img src="https://avatars.githubusercontent.com/u/68122106?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Jouriy</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="http://www.digital-pianism.com/"><img src="https://avatars.githubusercontent.com/u/16592249?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Digital Pianism</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/justinbeaty"><img src="https://avatars.githubusercontent.com/u/51970393?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Justin Beaty</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/ADDISON74"><img src="https://avatars.githubusercontent.com/u/8360474?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>ADDISON</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://dinhe.net/~aredridel/"><img src="https://avatars.githubusercontent.com/u/2876?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Aria Stewart</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/drwilliams"><img src="https://avatars.githubusercontent.com/u/11303389?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Dean Williams</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/hhirsch"><img src="https://avatars.githubusercontent.com/u/2451426?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Henry Hirsch</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/kdckrs"><img src="https://avatars.githubusercontent.com/u/2227271?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>kdckrs</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/sicet7"><img src="https://avatars.githubusercontent.com/u/7220364?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Martin René Sørensen</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.b3-it.de/"><img src="https://avatars.githubusercontent.com/u/3726836?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Frank Rochlitzer</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://www.alterweb.nl/"><img src="https://avatars.githubusercontent.com/u/12827587?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>AlterWeb</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/Caprico85"><img src="https://avatars.githubusercontent.com/u/2081806?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Caprico</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/davidwindell"><img src="https://avatars.githubusercontent.com/u/1720090?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>David Windell</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/drashmk"><img src="https://avatars.githubusercontent.com/u/2790702?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Dragan Atanasov</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/lamskoy"><img src="https://avatars.githubusercontent.com/u/233998?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Eugene Lamskoy</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/ferdiusa"><img src="https://avatars.githubusercontent.com/u/1997982?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Ferdinand</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://focused-wescoff-bfb488.netlify.app/"><img src="https://avatars.githubusercontent.com/u/65963997?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Himanshu</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/idziakjakub"><img src="https://avatars.githubusercontent.com/u/7571848?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Jakub Idziak</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://swiftotter.com/"><img src="https://avatars.githubusercontent.com/u/1151186?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Joseph Maxwell</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.promenade.co/"><img src="https://avatars.githubusercontent.com/u/53793523?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Joshua Dickerson</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/KBortnick"><img src="https://avatars.githubusercontent.com/u/4563592?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Kevin Bortnick</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/mehdichaouch"><img src="https://avatars.githubusercontent.com/u/861701?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Mehdi Chaouch</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://www.elidrissi.dev/"><img src="https://avatars.githubusercontent.com/u/67818913?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Mohamed ELIDRISSI</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://publicus.nl/"><img src="https://avatars.githubusercontent.com/u/249633?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Justin van Elst</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/nikkuexe"><img src="https://avatars.githubusercontent.com/u/1317618?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Nicholas Graham</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://patrickschnell.de/"><img src="https://avatars.githubusercontent.com/u/1762478?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Patrick Schnell</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.cronin-tech.com/"><img src="https://avatars.githubusercontent.com/u/6902411?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Patrick Cronin</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/petrsvamberg"><img src="https://avatars.githubusercontent.com/u/54709445?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Petr Švamberg</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://rafaelcg.com/"><img src="https://avatars.githubusercontent.com/u/610598?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Rafael Corrêa Gomes</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://www.mageconsult.de/"><img src="https://avatars.githubusercontent.com/u/1145186?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Ralf Siepker</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://sunel.github.io/"><img src="https://avatars.githubusercontent.com/u/1009777?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Sunel Tr</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/ktomk"><img src="https://avatars.githubusercontent.com/u/352517?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tom Klingenberg</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/ToonSpin"><img src="https://avatars.githubusercontent.com/u/1450038?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Toon</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.wexo.dk/"><img src="https://avatars.githubusercontent.com/u/7666143?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>WEXO team</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.sandstein.de/"><img src="https://avatars.githubusercontent.com/u/23700116?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Wilfried Wolf</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/akrzemianowski"><img src="https://avatars.githubusercontent.com/u/44834491?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>akrzemianowski</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/andthink"><img src="https://avatars.githubusercontent.com/u/1862377?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>andthink</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/eetzen"><img src="https://avatars.githubusercontent.com/u/67363284?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>eetzen</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/lemundo-team"><img src="https://avatars.githubusercontent.com/u/61752623?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>lemundo-team</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/mdlonline"><img src="https://avatars.githubusercontent.com/u/5389528?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>mdlonline</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.developpeur-web-tlse.fr/"><img src="https://avatars.githubusercontent.com/u/5030086?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Benjamin MARROT</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/tmewes"><img src="https://avatars.githubusercontent.com/u/12640514?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tino Mewes</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="http://cebe.cc/"><img src="https://avatars.githubusercontent.com/u/189796?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Carsten Brandt</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/eneiasramos"><img src="https://avatars.githubusercontent.com/u/2862728?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Enéias Ramos de Melo</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/discountscott"><img src="https://avatars.githubusercontent.com/u/5454596?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Scott Moore</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/rfeese"><img src="https://avatars.githubusercontent.com/u/7074181?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Roger Feese</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/AGelzer"><img src="https://avatars.githubusercontent.com/u/34437931?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Alexander Gelzer</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/davidhiendl"><img src="https://avatars.githubusercontent.com/u/11006964?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>David Hiendl</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/gorbunovav"><img src="https://avatars.githubusercontent.com/u/2665015?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Andrey Gorbunov</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/Tomasz-Silpion"><img src="https://avatars.githubusercontent.com/u/5328659?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tomasz Gregorczyk</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://juhoholsa.com/"><img src="https://avatars.githubusercontent.com/u/15036353?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Juho Hölsä</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/seifer7"><img src="https://avatars.githubusercontent.com/u/13601073?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Kane</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/Sdfendor"><img src="https://avatars.githubusercontent.com/u/2728018?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Kevin Jakob</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/leissbua"><img src="https://avatars.githubusercontent.com/u/68073221?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Michael Leiss</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.riseart.com/"><img src="https://avatars.githubusercontent.com/u/26821235?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Marcos Steverlynck</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/ahudock"><img src="https://avatars.githubusercontent.com/u/33500977?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Andy Hudock</b></sub></a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://www.vianetz.com/"><img src="https://avatars.githubusercontent.com/u/26252058?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Christoph Massmann</b></sub></a></td>
    </tr>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/ragnese"><img src="https://avatars.githubusercontent.com/u/7927565?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Rob Agnese</b></sub></a></td>
    </tr>
  </tbody>
</table>

<!-- markdownlint-restore -->
<!-- prettier-ignore-end -->

<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!

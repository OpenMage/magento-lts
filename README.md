<p align="center">
<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
<a href="#contributors-"><img src="https://img.shields.io/badge/all_contributors-146-orange.svg?style=flat-square" alt="All Contributors"></a>
<!-- ALL-CONTRIBUTORS-BADGE:END -->
<a href="https://packagist.org/packages/openmage/magento-lts"><img src="https://poser.pugx.org/openmage/magento-lts/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/openmage/magento-lts"><img src="https://poser.pugx.org/openmage/magento-lts/license.svg" alt="License"></a>
<br />
<img src="https://github.com/openmage/magento-lts/actions/workflows/php.yml/badge.svg" alt="PHP workflow Badge" />
<img src="https://github.com/openmage/magento-lts/actions/workflows/sonar.yml/badge.svg" alt="Sonar workflow badge" />
<img src="https://github.com/openmage/magento-lts/actions/workflows/phpstan.yml/badge.svg" alt="PHPStan Static Code Analyses workflow badge" />
<img src="https://github.com/openmage/magento-lts/actions/workflows/syntax-php.yml/badge.svg" alt="PHP Syntax Check workflow badge" />
<img src="https://github.com/openmage/magento-lts/actions/workflows/phpunit.yml/badge.svg" alt="Unit Tests workflow badge" />
</p>

# Magento - Long Term Support

This repository is the home of an **unofficial** community-driven project. It's goal is to be a dependable alternative
to the Magento CE official releases which integrates improvements directly from the community while maintaining a high
level of backwards compatibility to the official releases.

**Pull requests with unofficial bug fixes and security patches from the community are encouraged and welcome!**

### Versioning

Though Magento does __not__ follow [Semantic Versioning](http://semver.org/) we aim to provide a workable system for
dependency definition. Each Magento `1.<minor>.<revision>` release will get its own branch (named `1.<minor>.<revision>.x`)
that will be independently maintained with upstream patches and community bug fixes for as long as it makes sense
to do so (based on available resources). For example, Magento version `1.9.4.5` was merged into the `1.9.4.x` branch.

## Requirements

- PHP 7.3+ (PHP 8.0 is supported)
- MySQL 5.6+ (8.0+ recommended) or MariaDB

__Please be aware that although OpenMage is compatible that one or more extensions may not be__

### Optional

- Redis 5+ (6.x recommended, latest verified compatible 6.0.7 with 20.x)

### PHP 7.2+
If using php 7.2+ then `mcrypt` needs to be disabled in `php.ini` or pecl to fallback on `mcryptcompat` and `phpseclib`. `mcrypt` is deprecated from 7.2+ onwards.

## Installation

### Using Composer

Download the latest archive and extract it, clone the repo, or add a composer dependency to your existing project like so:

```bash
composer require "openmage/magento-lts":"^19.4.0"
```

To get the latest changes use:

```bash
composer require "openmage/magento-lts":"dev-main"
```

<small>Note: `dev-main` is just an alias for current `1.9.4.x` branch and may change</small>

### Using Git

If you want to contribute to the project:

```bash
git init
git remote add origin https://github.com/<YOUR GIT USERNAME>/magento-lts
git pull origin main
git remote add upstream https://github.com/OpenMage/magento-lts
git pull upstream 1.9.4.x
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

* Apache .htaccess: `RewriteRule ^api/rest api.php?type=rest [QSA,L]`
* Nginx: `rewrite ^/api/(\w+).*$ /api.php?type=$1 last;`

## Changes

Most important changes will be listed here, all other changes since `19.4.0` can be found in
[release](https://github.com/OpenMage/magento-lts/releases) notes.

### Between Magento 1.9.4.5 and OpenMage 19.x

- bug fixes and PHP 7.x, 8.0 and 8.1 compatibility
- added config cache for system.xml [#1916](https://github.com/OpenMage/magento-lts/pull/1916)
- search for "NULL" in backend grids [#1203](https://github.com/OpenMage/magento-lts/pull/1203)
- removed modules `Mage_Compiler`, `Mage_GoogleBase`, `Mage_Xmlconnect`, `Phoenix_Moneybookers`

### Between OpenMage 19.4.18 / 20.0.16 and 19.4.19 / 20.0.17

- PHP extension `intl` is required

### Between OpenMage 19.x and 20.x

Do not use 20.x.x if you need IE support.

- removed IE conditional comments, IE styles, IE scripts and IE eot files [#1073](https://github.com/OpenMage/magento-lts/pull/1073)
- removed frontend default themes (default, modern, iphone, german, french, blank, blue) [#1600](https://github.com/OpenMage/magento-lts/pull/1600)
- fixed incorrect datetime in customer block (`$useTimezone` parameter) [#1525](https://github.com/OpenMage/magento-lts/pull/1525)
- added redis as a valid option for `global/session_save` [#1513](https://github.com/OpenMage/magento-lts/pull/1513)
- reduce needless saves by avoiding setting `_hasDataChanges` flag [#2066](https://github.com/OpenMage/magento-lts/pull/2066)
- removed support for `global/sales/old_fields_map` defined in XML [#921](https://github.com/OpenMage/magento-lts/pull/921)
- removed module `Mage_PageCache` [#2258](https://github.com/OpenMage/magento-lts/pull/2258)
- removed lib/flex containing unused ActionScript "file uploader" files [#2271](https://github.com/OpenMage/magento-lts/pull/2271)
- enabled website level config cache [#2355](https://github.com/OpenMage/magento-lts/pull/2355)

For full list of changes, you can [compare tags](https://github.com/OpenMage/magento-lts/compare/1.9.4.x...20.0).

### New Config Options

- `admin/design/use_legacy_theme`
- `admin/global_search/enable`
- `admin/emails/admin_notification_email_template`
- `catalog/product_image/progressive_threshold`
- `catalog/search/search_separator`
- `dev/log/max_level`
- `newsletter/security/enable_form_key`
- `sitemap/category/lastmod`
- `sitemap/page/lastmod`
- `sitemap/product/lastmod`

### New Events

- `adminhtml_block_widget_form_init_form_values_after`
- `adminhtml_block_widget_tabs_html_before`
- `adminhtml_sales_order_create_save_before`
- `checkout_cart_product_add_before`
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
```
grep -rn 'urn:Magento' --include \*.xml
```

## Development Environment with ddev

- Install [ddev](https://ddev.com/get-started/)
- Clone the repository as described in installation ([Using Git](https://github.com/OpenMage/magento-lts#using-git))
- Create a ddev config, defaults should be good for you
  ```bash
  $ ddev config
  ```
- Open `.ddev/config.yaml` and change the php version to your needs
- Download and start the containers
  ```bash
  $ ddev start
  ```
- Open your site in browser
  ```bash
  $ ddev launch
  ```

## PhpStorm Factory Helper

This repo includes class maps for the core Magento files in `.phpstorm.meta.php`.
To add class maps for installed extensions, you have to install [N98-magerun](https://github.com/netz98/n98-magerun)
and run command:

```
n98-magerun.phar dev:ide:phpstorm:meta
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

## Contributors ✨

Thanks goes to these wonderful people ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tr>
    <td align="center"><a href="https://magento.stackexchange.com/users/46249/sv3n"><img src="https://avatars1.githubusercontent.com/u/5022236?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>sv3n</b></sub></a></td>
    <td align="center"><a href="https://github.com/LeeSaferite"><img src="https://avatars3.githubusercontent.com/u/47386?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Lee Saferite</b></sub></a></td>
    <td align="center"><a href="http://colin.mollenhour.com/"><img src="https://avatars3.githubusercontent.com/u/38738?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Colin Mollenhour</b></sub></a></td>
    <td align="center"><a href="https://github.com/drobinson"><img src="https://avatars1.githubusercontent.com/u/455332?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>David Robinson</b></sub></a></td>
    <td align="center"><a href="https://macopedia.com/"><img src="https://avatars1.githubusercontent.com/u/515397?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tymoteusz Motylewski</b></sub></a></td>
    <td align="center"><a href="http://flyingmana.name/"><img src="https://avatars3.githubusercontent.com/u/237319?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Daniel Fahlke</b></sub></a></td>
    <td align="center"><a href="https://overhemden.com/"><img src="https://avatars3.githubusercontent.com/u/652395?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>SNH_NL</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/spinsch"><img src="https://avatars1.githubusercontent.com/u/519865?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Marc Romano</b></sub></a></td>
    <td align="center"><a href="http://www.fabian-blechschmidt.de/"><img src="https://avatars1.githubusercontent.com/u/379680?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Fabian Blechschmidt</b></sub></a></td>
    <td align="center"><a href="https://github.com/Sekiphp"><img src="https://avatars2.githubusercontent.com/u/9967016?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Luboš Hubáček</b></sub></a></td>
    <td align="center"><a href="https://github.com/edannenberg"><img src="https://avatars0.githubusercontent.com/u/1352794?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Erik Dannenberg</b></sub></a></td>
    <td align="center"><a href="http://srcode.nl/"><img src="https://avatars2.githubusercontent.com/u/1163348?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Jeroen Boersma</b></sub></a></td>
    <td align="center"><a href="https://www.linkedin.com/in/lfluvisotto"><img src="https://avatars3.githubusercontent.com/u/535626?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Leandro F. L.</b></sub></a></td>
    <td align="center"><a href="https://github.com/kkrieger85"><img src="https://avatars2.githubusercontent.com/u/4435523?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Kevin Krieger</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/kiatng"><img src="https://avatars1.githubusercontent.com/u/1106470?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Ng Kiat Siong</b></sub></a></td>
    <td align="center"><a href="https://github.com/bob2021"><img src="https://avatars0.githubusercontent.com/u/8102829?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>bob2021</b></sub></a></td>
    <td align="center"><a href="https://github.com/bastienlm"><img src="https://avatars1.githubusercontent.com/u/13004368?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Bastien Lamamy</b></sub></a></td>
    <td align="center"><a href="https://github.com/DmitryFursNeklo"><img src="https://avatars3.githubusercontent.com/u/6996108?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Dmitry Furs</b></sub></a></td>
    <td align="center"><a href="https://github.com/rjocoleman"><img src="https://avatars0.githubusercontent.com/u/154176?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Robert Coleman</b></sub></a></td>
    <td align="center"><a href="http://milandavidek.cz/"><img src="https://avatars2.githubusercontent.com/u/4263992?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Milan Davídek</b></sub></a></td>
    <td align="center"><a href="https://mattdavenport.io/"><img src="https://avatars3.githubusercontent.com/u/1127393?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Matt Davenport</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/kestraly"><img src="https://avatars3.githubusercontent.com/u/13368757?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>elfling</b></sub></a></td>
    <td align="center"><a href="https://github.com/henrykbrzoska"><img src="https://avatars1.githubusercontent.com/u/4395216?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>henrykb</b></sub></a></td>
    <td align="center"><a href="https://github.com/empiricompany"><img src="https://avatars0.githubusercontent.com/u/5071467?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tony</b></sub></a></td>
    <td align="center"><a href="https://netalico.com/"><img src="https://avatars0.githubusercontent.com/u/2094614?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Mark Lewis</b></sub></a></td>
    <td align="center"><a href="https://github.com/ericseanturner"><img src="https://avatars3.githubusercontent.com/u/42879056?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Eric Sean Turner</b></sub></a></td>
    <td align="center"><a href="https://willcodeforfood.github.io/"><img src="https://avatars2.githubusercontent.com/u/1639118?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Eric Seastrand</b></sub></a></td>
    <td align="center"><a href="https://www.ambimax.de/"><img src="https://avatars1.githubusercontent.com/u/14741874?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tobias Schifftner</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://www.simonsprankel.com/"><img src="https://avatars1.githubusercontent.com/u/930199?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Simon Sprankel</b></sub></a></td>
    <td align="center"><a href="https://tomlankhorst.nl/"><img src="https://avatars0.githubusercontent.com/u/675432?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tom Lankhorst</b></sub></a></td>
    <td align="center"><a href="https://shirtsofholland.com/"><img src="https://avatars0.githubusercontent.com/u/11224809?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>shirtsofholland</b></sub></a></td>
    <td align="center"><a href="https://github.com/sebastianwagner"><img src="https://avatars0.githubusercontent.com/u/1701745?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>sebastianwagner</b></sub></a></td>
    <td align="center"><a href="https://maximehuran.fr/"><img src="https://avatars1.githubusercontent.com/u/11380627?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Maxime Huran</b></sub></a></td>
    <td align="center"><a href="https://github.com/pepijnblom"><img src="https://avatars0.githubusercontent.com/u/6009489?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Pepijn</b></sub></a></td>
    <td align="center"><a href="https://github.com/manuperezgo"><img src="https://avatars0.githubusercontent.com/u/8482836?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>manuperezgo</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://www.luigifab.fr/"><img src="https://avatars1.githubusercontent.com/u/31816829?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>luigifab</b></sub></a></td>
    <td align="center"><a href="https://github.com/loekvangool"><img src="https://avatars0.githubusercontent.com/u/7300472?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Loek van Gool</b></sub></a></td>
    <td align="center"><a href="https://github.com/kpitn"><img src="https://avatars2.githubusercontent.com/u/41059?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>kpitn</b></sub></a></td>
    <td align="center"><a href="https://github.com/kalenjordan"><img src="https://avatars2.githubusercontent.com/u/1542197?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>kalenjordan</b></sub></a></td>
    <td align="center"><a href="https://www.ioweb.gr/en"><img src="https://avatars3.githubusercontent.com/u/20220341?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>IOWEB TECHNOLOGIES</b></sub></a></td>
    <td align="center"><a href="https://github.com/fplantinet"><img src="https://avatars0.githubusercontent.com/u/2428023?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Florent</b></sub></a></td>
    <td align="center"><a href="https://github.com/dvdsndr"><img src="https://avatars1.githubusercontent.com/u/13637075?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>dvdsndr</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/VincentMarmiesse"><img src="https://avatars0.githubusercontent.com/u/1949412?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Vincent MARMIESSE</b></sub></a></td>
    <td align="center"><a href="http://www.proxiblue.com.au/"><img src="https://avatars2.githubusercontent.com/u/4994260?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Lucas van Staden</b></sub></a></td>
    <td align="center"><a href="http://zamoroka.com/"><img src="https://avatars1.githubusercontent.com/u/9164112?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>zamoroka</b></sub></a></td>
    <td align="center"><a href="https://github.com/wpdevteam"><img src="https://avatars3.githubusercontent.com/u/1577103?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>wpdevteam</b></sub></a></td>
    <td align="center"><a href="http://www.storefront.be/"><img src="https://avatars1.githubusercontent.com/u/71019?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Wouter Samaey</b></sub></a></td>
    <td align="center"><a href="https://github.com/vovayatsyuk"><img src="https://avatars2.githubusercontent.com/u/306080?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Vova Yatsyuk</b></sub></a></td>
    <td align="center"><a href="https://hydrobuilder.com/"><img src="https://avatars3.githubusercontent.com/u/1300504?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Trevor Hartman</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/trabulium"><img src="https://avatars3.githubusercontent.com/u/1046615?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Somewhere</b></sub></a></td>
    <td align="center"><a href="https://www.schmengler-se.de/"><img src="https://avatars1.githubusercontent.com/u/367320?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Fabian Schmengler /></b></sub></a></td>
    <td align="center"><a href="https://copex.io/"><img src="https://avatars1.githubusercontent.com/u/584168?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Roman Hutterer</b></sub></a></td>
    <td align="center"><a href="https://www.haiku.co.nz/"><img src="https://avatars2.githubusercontent.com/u/123676?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Sergei Filippov</b></sub></a></td>
    <td align="center"><a href="https://github.com/samsteele"><img src="https://avatars3.githubusercontent.com/u/10742174?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Sam Steele</b></sub></a></td>
    <td align="center"><a href="https://goo.gl/WCUymp"><img src="https://avatars2.githubusercontent.com/u/59101?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Ricardo Velhote</b></sub></a></td>
    <td align="center"><a href="https://royduineveld.nl/"><img src="https://avatars2.githubusercontent.com/u/1703233?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Roy Duineveld</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/roberto-ebizmarts"><img src="https://avatars0.githubusercontent.com/u/51710909?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Roberto Sarmiento Pérez</b></sub></a></td>
    <td align="center"><a href="https://www.pierre-martin.fr/"><img src="https://avatars0.githubusercontent.com/u/75968?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Pierre Martin</b></sub></a></td>
    <td align="center"><a href="https://github.com/rafdol"><img src="https://avatars2.githubusercontent.com/u/20263372?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Rafał Dołgopoł</b></sub></a></td>
    <td align="center"><a href="https://github.com/rafaelpatro"><img src="https://avatars0.githubusercontent.com/u/13813964?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Rafael Patro</b></sub></a></td>
    <td align="center"><a href="https://copex.io/"><img src="https://avatars3.githubusercontent.com/u/1998210?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Andreas Pointner</b></sub></a></td>
    <td align="center"><a href="https://github.com/paulrodriguez"><img src="https://avatars2.githubusercontent.com/u/6373764?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Paul Rodriguez</b></sub></a></td>
    <td align="center"><a href="https://github.com/ollb"><img src="https://avatars0.githubusercontent.com/u/5952064?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>ollb</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/nintenic"><img src="https://avatars0.githubusercontent.com/u/1317618?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Nicholas Graham</b></sub></a></td>
    <td align="center"><a href="https://github.com/mpalasis"><img src="https://avatars0.githubusercontent.com/u/37408939?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Makis Palasis</b></sub></a></td>
    <td align="center"><a href="http://magento.stackexchange.com/users/5209/mbalparda"><img src="https://avatars1.githubusercontent.com/u/3997682?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Miguel Balparda</b></sub></a></td>
    <td align="center"><a href="https://www.ecomni.nl/"><img src="https://avatars3.githubusercontent.com/u/2143634?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Mark van der Sanden</b></sub></a></td>
    <td align="center"><a href="https://binarzone.com/"><img src="https://avatars1.githubusercontent.com/u/200507?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Micky Socaci</b></sub></a></td>
    <td align="center"><a href="https://www.binaerfabrik.de/"><img src="https://avatars3.githubusercontent.com/u/7369753?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Marvin Sengera</b></sub></a></td>
    <td align="center"><a href="https://github.com/kanevbg"><img src="https://avatars3.githubusercontent.com/u/11477130?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Kostadin A.</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/julienloizelet"><img src="https://avatars3.githubusercontent.com/u/20956510?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Julien Loizelet</b></sub></a></td>
    <td align="center"><a href="https://maxcluster.de/"><img src="https://avatars0.githubusercontent.com/u/1112507?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Jonas Hünig</b></sub></a></td>
    <td align="center"><a href="https://github.com/jaroschek"><img src="https://avatars1.githubusercontent.com/u/470290?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Stefan Jaroschek</b></sub></a></td>
    <td align="center"><a href="http://jacques.sh/"><img src="https://avatars2.githubusercontent.com/u/858611?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Jacques Bodin-Hullin</b></sub></a></td>
    <td align="center"><a href="https://github.com/googlygoo"><img src="https://avatars3.githubusercontent.com/u/7078871?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Wilhelm Ellmann</b></sub></a></td>
    <td align="center"><a href="https://github.com/edwinkortman"><img src="https://avatars2.githubusercontent.com/u/7047894?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Edwin.</b></sub></a></td>
    <td align="center"><a href="https://github.com/drago-aca"><img src="https://avatars3.githubusercontent.com/u/14777419?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>drago-aca</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/dng-dev"><img src="https://avatars0.githubusercontent.com/u/836079?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Daniel Niedergesäß</b></sub></a></td>
    <td align="center"><a href="https://github.com/davis2125"><img src="https://avatars2.githubusercontent.com/u/14129105?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>J Davis</b></sub></a></td>
    <td align="center"><a href="https://github.com/damien-biasotto"><img src="https://avatars0.githubusercontent.com/u/430633?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Damien Biasotto</b></sub></a></td>
    <td align="center"><a href="https://github.com/cundd"><img src="https://avatars2.githubusercontent.com/u/743122?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Daniel Corn</b></sub></a></td>
    <td align="center"><a href="http://www.cieslix.com/"><img src="https://avatars0.githubusercontent.com/u/6729521?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Paweł Cieślik</b></sub></a></td>
    <td align="center"><a href="https://github.com/borriglione"><img src="https://avatars2.githubusercontent.com/u/465544?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>André Herrn</b></sub></a></td>
    <td align="center"><a href="https://github.com/blopa"><img src="https://avatars3.githubusercontent.com/u/3838114?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Pablo Benmaman</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/aterjung"><img src="https://avatars1.githubusercontent.com/u/3084302?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>aterjung</b></sub></a></td>
    <td align="center"><a href="https://github.com/altdovydas"><img src="https://avatars3.githubusercontent.com/u/8860049?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>altdovydas</b></sub></a></td>
    <td align="center"><a href="https://github.com/alissonjr"><img src="https://avatars2.githubusercontent.com/u/11911917?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Alisson Júnior</b></sub></a></td>
    <td align="center"><a href="https://github.com/alexkirsch"><img src="https://avatars3.githubusercontent.com/u/9553441?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Alex Kirsch</b></sub></a></td>
    <td align="center"><a href="https://github.com/SnowCommerceBrand"><img src="https://avatars3.githubusercontent.com/u/37154233?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Branden</b></sub></a></td>
    <td align="center"><a href="https://github.com/PofMagicfingers"><img src="https://avatars3.githubusercontent.com/u/469501?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Pof Magicfingers</b></sub></a></td>
    <td align="center"><a href="https://github.com/MichaelThessel"><img src="https://avatars1.githubusercontent.com/u/2926266?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Michael Thessel</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/JonLaliberte"><img src="https://avatars3.githubusercontent.com/u/5403662?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Jonathan Laliberte</b></sub></a></td>
    <td align="center"><a href="https://www.linkedin.com/in/ivanchepurnyi"><img src="https://avatars2.githubusercontent.com/u/866758?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Ivan Chepurnyi</b></sub></a></td>
    <td align="center"><a href="https://github.com/Ig0r-M-magic42"><img src="https://avatars1.githubusercontent.com/u/22006850?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Igor</b></sub></a></td>
    <td align="center"><a href="https://github.com/EliasKotlyar"><img src="https://avatars0.githubusercontent.com/u/9529505?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Elias Kotlyar</b></sub></a></td>
    <td align="center"><a href="https://github.com/Hejty1"><img src="https://avatars2.githubusercontent.com/u/53661954?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Hejty1</b></sub></a></td>
    <td align="center"><a href="https://github.com/Gaelle"><img src="https://avatars2.githubusercontent.com/u/112183?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Gaelle</b></sub></a></td>
    <td align="center"><a href="https://www.martinez-frederic.fr/"><img src="https://avatars3.githubusercontent.com/u/13019288?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Frédéric MARTINEZ</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/FaustTobias"><img src="https://avatars1.githubusercontent.com/u/48201729?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tobias Faust</b></sub></a></td>
    <td align="center"><a href="https://github.com/AndresInSpace"><img src="https://avatars2.githubusercontent.com/u/14356094?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>AndresInSpace</b></sub></a></td>
    <td align="center"><a href="https://github.com/boesbo"><img src="https://avatars1.githubusercontent.com/u/12744378?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Francesco Boes</b></sub></a></td>
    <td align="center"><a href="https://github.com/dbachmann"><img src="https://avatars1.githubusercontent.com/u/1921769?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Daniel Bachmann</b></sub></a></td>
    <td align="center"><a href="https://github.com/daim2k5"><img src="https://avatars.githubusercontent.com/u/656150?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Damian Luszczymak</b></sub></a></td>
    <td align="center"><a href="http://fabrizioballiano.com/"><img src="https://avatars.githubusercontent.com/u/909743?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Fabrizio Balliano</b></sub></a></td>
    <td align="center"><a href="https://github.com/jouriy"><img src="https://avatars.githubusercontent.com/u/68122106?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Jouriy</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="http://www.digital-pianism.com/"><img src="https://avatars.githubusercontent.com/u/16592249?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Digital Pianism</b></sub></a></td>
    <td align="center"><a href="https://github.com/justinbeaty"><img src="https://avatars.githubusercontent.com/u/51970393?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Justin Beaty</b></sub></a></td>
    <td align="center"><a href="https://github.com/ADDISON74"><img src="https://avatars.githubusercontent.com/u/8360474?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>ADDISON</b></sub></a></td>
    <td align="center"><a href="http://dinhe.net/~aredridel/"><img src="https://avatars.githubusercontent.com/u/2876?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Aria Stewart</b></sub></a></td>
    <td align="center"><a href="https://github.com/drwilliams"><img src="https://avatars.githubusercontent.com/u/11303389?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Dean Williams</b></sub></a></td>
    <td align="center"><a href="https://github.com/hhirsch"><img src="https://avatars.githubusercontent.com/u/2451426?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Henry Hirsch</b></sub></a></td>
    <td align="center"><a href="https://github.com/kdckrs"><img src="https://avatars.githubusercontent.com/u/2227271?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>kdckrs</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/sicet7"><img src="https://avatars.githubusercontent.com/u/7220364?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Martin René Sørensen</b></sub></a></td>
    <td align="center"><a href="https://www.b3-it.de/"><img src="https://avatars.githubusercontent.com/u/3726836?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Frank Rochlitzer</b></sub></a></td>
    <td align="center"><a href="http://www.alterweb.nl/"><img src="https://avatars.githubusercontent.com/u/12827587?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>AlterWeb</b></sub></a></td>
    <td align="center"><a href="https://github.com/Caprico85"><img src="https://avatars.githubusercontent.com/u/2081806?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Caprico</b></sub></a></td>
    <td align="center"><a href="https://github.com/davidwindell"><img src="https://avatars.githubusercontent.com/u/1720090?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>David Windell</b></sub></a></td>
    <td align="center"><a href="https://github.com/drashmk"><img src="https://avatars.githubusercontent.com/u/2790702?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Dragan Atanasov</b></sub></a></td>
    <td align="center"><a href="https://github.com/lamskoy"><img src="https://avatars.githubusercontent.com/u/233998?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Eugene Lamskoy</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/ferdiusa"><img src="https://avatars.githubusercontent.com/u/1997982?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Ferdinand</b></sub></a></td>
    <td align="center"><a href="https://focused-wescoff-bfb488.netlify.app/"><img src="https://avatars.githubusercontent.com/u/65963997?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Himanshu</b></sub></a></td>
    <td align="center"><a href="https://github.com/idziakjakub"><img src="https://avatars.githubusercontent.com/u/7571848?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Jakub Idziak</b></sub></a></td>
    <td align="center"><a href="https://swiftotter.com/"><img src="https://avatars.githubusercontent.com/u/1151186?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Joseph Maxwell</b></sub></a></td>
    <td align="center"><a href="https://www.promenade.co/"><img src="https://avatars.githubusercontent.com/u/53793523?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Joshua Dickerson</b></sub></a></td>
    <td align="center"><a href="https://github.com/KBortnick"><img src="https://avatars.githubusercontent.com/u/4563592?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Kevin Bortnick</b></sub></a></td>
    <td align="center"><a href="https://github.com/mehdichaouch"><img src="https://avatars.githubusercontent.com/u/861701?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Mehdi Chaouch</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://www.elidrissi.dev/"><img src="https://avatars.githubusercontent.com/u/67818913?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Mohamed ELIDRISSI</b></sub></a></td>
    <td align="center"><a href="http://publicus.nl/"><img src="https://avatars.githubusercontent.com/u/249633?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Justin van Elst</b></sub></a></td>
    <td align="center"><a href="https://github.com/nikkuexe"><img src="https://avatars.githubusercontent.com/u/1317618?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Nicholas Graham</b></sub></a></td>
    <td align="center"><a href="https://patrickschnell.de/"><img src="https://avatars.githubusercontent.com/u/1762478?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Patrick Schnell</b></sub></a></td>
    <td align="center"><a href="https://www.cronin-tech.com/"><img src="https://avatars.githubusercontent.com/u/6902411?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Patrick Cronin</b></sub></a></td>
    <td align="center"><a href="https://github.com/petrsvamberg"><img src="https://avatars.githubusercontent.com/u/54709445?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Petr Švamberg</b></sub></a></td>
    <td align="center"><a href="https://rafaelcg.com/"><img src="https://avatars.githubusercontent.com/u/610598?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Rafael Corrêa Gomes</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://www.mageconsult.de/"><img src="https://avatars.githubusercontent.com/u/1145186?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Ralf Siepker</b></sub></a></td>
    <td align="center"><a href="https://sunel.github.io/"><img src="https://avatars.githubusercontent.com/u/1009777?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Sunel Tr</b></sub></a></td>
    <td align="center"><a href="https://github.com/ktomk"><img src="https://avatars.githubusercontent.com/u/352517?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tom Klingenberg</b></sub></a></td>
    <td align="center"><a href="https://github.com/ToonSpin"><img src="https://avatars.githubusercontent.com/u/1450038?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Toon</b></sub></a></td>
    <td align="center"><a href="https://www.wexo.dk/"><img src="https://avatars.githubusercontent.com/u/7666143?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>WEXO team</b></sub></a></td>
    <td align="center"><a href="https://www.sandstein.de/"><img src="https://avatars.githubusercontent.com/u/23700116?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Wilfried Wolf</b></sub></a></td>
    <td align="center"><a href="https://github.com/akrzemianowski"><img src="https://avatars.githubusercontent.com/u/44834491?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>akrzemianowski</b></sub></a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/andthink"><img src="https://avatars.githubusercontent.com/u/1862377?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>andthink</b></sub></a></td>
    <td align="center"><a href="https://github.com/eetzen"><img src="https://avatars.githubusercontent.com/u/67363284?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>eetzen</b></sub></a></td>
    <td align="center"><a href="https://github.com/lemundo-team"><img src="https://avatars.githubusercontent.com/u/61752623?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>lemundo-team</b></sub></a></td>
    <td align="center"><a href="https://github.com/mdlonline"><img src="https://avatars.githubusercontent.com/u/5389528?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>mdlonline</b></sub></a></td>
    <td align="center"><a href="https://www.developpeur-web-tlse.fr/"><img src="https://avatars.githubusercontent.com/u/5030086?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Benjamin MARROT</b></sub></a></td>
    <td align="center"><a href="https://github.com/tmewes"><img src="https://avatars.githubusercontent.com/u/12640514?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Tino Mewes</b></sub></a></td>
    <td align="center"><a href="http://cebe.cc/"><img src="https://avatars.githubusercontent.com/u/189796?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Carsten Brandt</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=cebe" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/eneiasramos"><img src="https://avatars.githubusercontent.com/u/2862728?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Enéias Ramos de Melo</b></sub></a></td>
    <td align="center"><a href="https://github.com/discountscott"><img src="https://avatars.githubusercontent.com/u/5454596?v=4" loading="lazy" width="100" alt=""/><br /><sub><b>Scott Moore</b></sub></a></td>
  </tr>
</table>

<!-- markdownlint-restore -->
<!-- prettier-ignore-end -->

<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!

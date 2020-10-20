<p align="center">
<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->

[contributors-logo]: https://img.shields.io/badge/all_contributors-100-orange.svg?style=flat-square 'Number of contributors on All-Contributors'

<!-- ALL-CONTRIBUTORS-BADGE:END -->
[![All Contributors][contributors-logo]](#contributors-)
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

Note, the branches older than `1.9.3.x` that were created before this strategy came into practice are **not maintained**.

## Installation

### Using Composer
Download the latest archive and extract it, clone the repo, or add a composer dependency to your existing project like so:

```json
"openmage/magento-lts": "1.9.4.x"
```

### Using Git
Go to `https://github.com/OpenMage/magento-lts` and fork the project. Enter your web directory and start a new git project utilizing `git init`. Set git remote to your forked repo using `git remote add origin https://github.com/<YOUR GIT USERNAME>/magento-lts`. Issue `git pull origin master`. Then add the official LTS repo as an upstream using `git remote add upstream https://github.com/OpenMage/magento-lts`. Issue `git pull upstream v19.4.4` or the latest version you want to pull. Be sure to check for conflicts before merging all in the next step. Dont forget to git commit and add files to your own forked repo.

When using git you have the ability to fix and test your own code in your own repo. In addition you can periodically sync with the official upstream Magento LTS repo.

```
git init
git remote add origin https://github.com/<YOUR GIT USERNAME>/magento-lts
git pull origin master
git remote add upstream https://github.com/OpenMage/magento-lts
git pull upstream v19.4.4
git add -A && git commit
```

[More Information](http://openmage.github.io/magento-lts/install.html)

## Requirements

- PHP 7.0+ (PHP 7.3 and OpenSSL extension strongly recommended)
- MySQL 5.6+ (8.0+ Recommended)

If using php 7.2+ then mcrypt needs to be disabled in php.ini or pecl to fallback on mcryptcompat and phpseclib. mcrypt is deprecated from 7.2+ onwards.

## Translations

There are some new or changed tranlations, if you want add them to your locale pack please check:

- `app/locale/en_US/*_LTS.csv`

## PhpStorm Factory Helper

This repo includes class maps for the core Magento files in `.phpstorm.meta.php`.
This file is generated using the following commands:

```
$ wget https://files.magerun.net/n98-magerun.phar
$ docker run --rm -u $UID -v $PWD:/var/www/html php:7.0-apache php n98-magerun.phar dev:ide:phpstorm:meta
```

You can add additional meta files in this directory to cover your own project files. See
[PhpStorm advanced metadata](https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html)
for more information.

## Development Environment with ddev
- Install [ddev](https://ddev.com/get-started/)
- Clone the repository as described in Installation -> Using Git
- Create a ddev config using ```$ ddev config``` the defaults should be good for you
- Open .ddev/config.yaml and change the php version to 7.2
- Navigate to https://magento-lts.ddev.site

## Removed Modules

- Phoenix_Moneybookers

    Re-install: `$ composer require openmage-module-fostering/phoenix_moneybookers`

## License

[OSL v3.0](http://opensource.org/licenses/OSL-3.0)

## Public Communication and online Community places

* [Discord](https://discord.gg/EV8aNbU) (maintained by Flyingmana)

## Maintainers

* [Lee Saferite](https://github.com/LeeSaferite)
* [David Robinson](https://github.com/drobinson)
* [Daniel Fahlke aka Flyingmana](https://github.com/Flyingmana)
* [Tymoteusz Motylewski](https://github.com/tmotyl)
* [Sven Reichel](https://github.com/sreichel)
* Pull requests are welcome

## Contributors ✨

Thanks goes to these wonderful people ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tr>
    <td align="center"><a href="https://magento.stackexchange.com/users/46249/sv3n"><img src="https://avatars1.githubusercontent.com/u/5022236?v=4" width="100px;" alt=""/><br /><sub><b>sv3n</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=sreichel" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/LeeSaferite"><img src="https://avatars3.githubusercontent.com/u/47386?v=4" width="100px;" alt=""/><br /><sub><b>Lee Saferite</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=LeeSaferite" title="Code">💻</a></td>
    <td align="center"><a href="http://colin.mollenhour.com/"><img src="https://avatars3.githubusercontent.com/u/38738?v=4" width="100px;" alt=""/><br /><sub><b>Colin Mollenhour</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=colinmollenhour" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/drobinson"><img src="https://avatars1.githubusercontent.com/u/455332?v=4" width="100px;" alt=""/><br /><sub><b>David Robinson</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=drobinson" title="Code">💻</a></td>
    <td align="center"><a href="https://macopedia.com/"><img src="https://avatars1.githubusercontent.com/u/515397?v=4" width="100px;" alt=""/><br /><sub><b>Tymoteusz Motylewski</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=tmotyl" title="Code">💻</a></td>
    <td align="center"><a href="http://flyingmana.name/"><img src="https://avatars3.githubusercontent.com/u/237319?v=4" width="100px;" alt=""/><br /><sub><b>Daniel Fahlke</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=Flyingmana" title="Code">💻</a></td>
    <td align="center"><a href="https://overhemden.com/"><img src="https://avatars3.githubusercontent.com/u/652395?v=4" width="100px;" alt=""/><br /><sub><b>SNH_NL</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=seansan" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/spinsch"><img src="https://avatars1.githubusercontent.com/u/519865?v=4" width="100px;" alt=""/><br /><sub><b>Marc Romano</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=spinsch" title="Code">💻</a></td>
    <td align="center"><a href="http://www.fabian-blechschmidt.de/"><img src="https://avatars1.githubusercontent.com/u/379680?v=4" width="100px;" alt=""/><br /><sub><b>Fabian Blechschmidt</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=Schrank" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/Sekiphp"><img src="https://avatars2.githubusercontent.com/u/9967016?v=4" width="100px;" alt=""/><br /><sub><b>Luboš Hubáček</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=Sekiphp" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/edannenberg"><img src="https://avatars0.githubusercontent.com/u/1352794?v=4" width="100px;" alt=""/><br /><sub><b>Erik Dannenberg</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=edannenberg" title="Code">💻</a></td>
    <td align="center"><a href="http://srcode.nl/"><img src="https://avatars2.githubusercontent.com/u/1163348?v=4" width="100px;" alt=""/><br /><sub><b>Jeroen Boersma</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=JeroenBoersma" title="Code">💻</a></td>
    <td align="center"><a href="https://www.linkedin.com/in/lfluvisotto"><img src="https://avatars3.githubusercontent.com/u/535626?v=4" width="100px;" alt=""/><br /><sub><b>Leandro F. L.</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=lfluvisotto" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/kkrieger85"><img src="https://avatars2.githubusercontent.com/u/4435523?v=4" width="100px;" alt=""/><br /><sub><b>Kevin Krieger</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=kkrieger85" title="Code">💻</a> <a href="https://github.com/OpenMage/magento-lts/commits?author=kkrieger85" title="Documentation">📖</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/kiatng"><img src="https://avatars1.githubusercontent.com/u/1106470?v=4" width="100px;" alt=""/><br /><sub><b>Ng Kiat Siong</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=kiatng" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/bob2021"><img src="https://avatars0.githubusercontent.com/u/8102829?v=4" width="100px;" alt=""/><br /><sub><b>bob2021</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=bob2021" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/bastienlm"><img src="https://avatars1.githubusercontent.com/u/13004368?v=4" width="100px;" alt=""/><br /><sub><b>Bastien Lamamy</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=bastienlm" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/DmitryFursNeklo"><img src="https://avatars3.githubusercontent.com/u/6996108?v=4" width="100px;" alt=""/><br /><sub><b>Dmitry Furs</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=DmitryFursNeklo" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/rjocoleman"><img src="https://avatars0.githubusercontent.com/u/154176?v=4" width="100px;" alt=""/><br /><sub><b>Robert Coleman</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=rjocoleman" title="Code">💻</a></td>
    <td align="center"><a href="http://milandavidek.cz/"><img src="https://avatars2.githubusercontent.com/u/4263992?v=4" width="100px;" alt=""/><br /><sub><b>Milan Davídek</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=midlan" title="Code">💻</a></td>
    <td align="center"><a href="https://mattdavenport.io/"><img src="https://avatars3.githubusercontent.com/u/1127393?v=4" width="100px;" alt=""/><br /><sub><b>Matt Davenport</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=mattdavenport" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/kestraly"><img src="https://avatars3.githubusercontent.com/u/13368757?v=4" width="100px;" alt=""/><br /><sub><b>elfling</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=kestraly" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/henrykbrzoska"><img src="https://avatars1.githubusercontent.com/u/4395216?v=4" width="100px;" alt=""/><br /><sub><b>henrykb</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=henrykbrzoska" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/empiricompany"><img src="https://avatars0.githubusercontent.com/u/5071467?v=4" width="100px;" alt=""/><br /><sub><b>Tony</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=empiricompany" title="Code">💻</a></td>
    <td align="center"><a href="https://netalico.com/"><img src="https://avatars0.githubusercontent.com/u/2094614?v=4" width="100px;" alt=""/><br /><sub><b>Mark Lewis</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=mark-netalico" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/ericseanturner"><img src="https://avatars3.githubusercontent.com/u/42879056?v=4" width="100px;" alt=""/><br /><sub><b>Eric Sean Turner</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=ericseanturner" title="Code">💻</a></td>
    <td align="center"><a href="https://willcodeforfood.github.io/"><img src="https://avatars2.githubusercontent.com/u/1639118?v=4" width="100px;" alt=""/><br /><sub><b>Eric Seastrand</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=willcodeforfood" title="Code">💻</a></td>
    <td align="center"><a href="https://www.ambimax.de/"><img src="https://avatars1.githubusercontent.com/u/14741874?v=4" width="100px;" alt=""/><br /><sub><b>Tobias Schifftner</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=tschifftner" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://www.simonsprankel.com/"><img src="https://avatars1.githubusercontent.com/u/930199?v=4" width="100px;" alt=""/><br /><sub><b>Simon Sprankel</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=sprankhub" title="Code">💻</a></td>
    <td align="center"><a href="https://tomlankhorst.nl/"><img src="https://avatars0.githubusercontent.com/u/675432?v=4" width="100px;" alt=""/><br /><sub><b>Tom Lankhorst</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=tomlankhorst" title="Code">💻</a></td>
    <td align="center"><a href="https://shirtsofholland.com/"><img src="https://avatars0.githubusercontent.com/u/11224809?v=4" width="100px;" alt=""/><br /><sub><b>shirtsofholland</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=shirtsofholl" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/sebastianwagner"><img src="https://avatars0.githubusercontent.com/u/1701745?v=4" width="100px;" alt=""/><br /><sub><b>sebastianwagner</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=sebastianwagner" title="Code">💻</a></td>
    <td align="center"><a href="https://maximehuran.fr/"><img src="https://avatars1.githubusercontent.com/u/11380627?v=4" width="100px;" alt=""/><br /><sub><b>Maxime Huran</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=maximehuran" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/pepijnblom"><img src="https://avatars0.githubusercontent.com/u/6009489?v=4" width="100px;" alt=""/><br /><sub><b>Pepijn</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=pepijnblom" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/manuperezgo"><img src="https://avatars0.githubusercontent.com/u/8482836?v=4" width="100px;" alt=""/><br /><sub><b>manuperezgo</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=manuperezgo" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://www.luigifab.fr/"><img src="https://avatars1.githubusercontent.com/u/31816829?v=4" width="100px;" alt=""/><br /><sub><b>luigifab</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=luigifab" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/loekvangool"><img src="https://avatars0.githubusercontent.com/u/7300472?v=4" width="100px;" alt=""/><br /><sub><b>Loek van Gool</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=loekvangool" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/kpitn"><img src="https://avatars2.githubusercontent.com/u/41059?v=4" width="100px;" alt=""/><br /><sub><b>kpitn</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=kpitn" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/kalenjordan"><img src="https://avatars2.githubusercontent.com/u/1542197?v=4" width="100px;" alt=""/><br /><sub><b>kalenjordan</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=kalenjordan" title="Code">💻</a></td>
    <td align="center"><a href="https://www.ioweb.gr/en"><img src="https://avatars3.githubusercontent.com/u/20220341?v=4" width="100px;" alt=""/><br /><sub><b>IOWEB TECHNOLOGIES</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=ioweb-gr" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/fplantinet"><img src="https://avatars0.githubusercontent.com/u/2428023?v=4" width="100px;" alt=""/><br /><sub><b>Florent</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=fplantinet" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/dvdsndr"><img src="https://avatars1.githubusercontent.com/u/13637075?v=4" width="100px;" alt=""/><br /><sub><b>dvdsndr</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=dvdsndr" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/VincentMarmiesse"><img src="https://avatars0.githubusercontent.com/u/1949412?v=4" width="100px;" alt=""/><br /><sub><b>Vincent MARMIESSE</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=VincentMarmiesse" title="Code">💻</a></td>
    <td align="center"><a href="http://www.proxiblue.com.au/"><img src="https://avatars2.githubusercontent.com/u/4994260?v=4" width="100px;" alt=""/><br /><sub><b>Lucas van Staden</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=ProxiBlue" title="Code">💻</a></td>
    <td align="center"><a href="http://zamoroka.com/"><img src="https://avatars1.githubusercontent.com/u/9164112?v=4" width="100px;" alt=""/><br /><sub><b>zamoroka</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=zamoroka" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/wpdevteam"><img src="https://avatars3.githubusercontent.com/u/1577103?v=4" width="100px;" alt=""/><br /><sub><b>wpdevteam</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=wpdevteam" title="Code">💻</a></td>
    <td align="center"><a href="http://www.storefront.be/"><img src="https://avatars1.githubusercontent.com/u/71019?v=4" width="100px;" alt=""/><br /><sub><b>Wouter Samaey</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=woutersamaey" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/vovayatsyuk"><img src="https://avatars2.githubusercontent.com/u/306080?v=4" width="100px;" alt=""/><br /><sub><b>Vova Yatsyuk</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=vovayatsyuk" title="Code">💻</a></td>
    <td align="center"><a href="https://hydrobuilder.com/"><img src="https://avatars3.githubusercontent.com/u/1300504?v=4" width="100px;" alt=""/><br /><sub><b>Trevor Hartman</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=viable-hartman" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/trabulium"><img src="https://avatars3.githubusercontent.com/u/1046615?v=4" width="100px;" alt=""/><br /><sub><b>Somewhere</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=trabulium" title="Code">💻</a></td>
    <td align="center"><a href="https://www.schmengler-se.de/"><img src="https://avatars1.githubusercontent.com/u/367320?v=4" width="100px;" alt=""/><br /><sub><b>Fabian Schmengler /></b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=schmengler" title="Code">💻</a></td>
    <td align="center"><a href="https://copex.io/"><img src="https://avatars1.githubusercontent.com/u/584168?v=4" width="100px;" alt=""/><br /><sub><b>Roman Hutterer</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=roman204" title="Code">💻</a></td>
    <td align="center"><a href="https://www.haiku.co.nz/"><img src="https://avatars2.githubusercontent.com/u/123676?v=4" width="100px;" alt=""/><br /><sub><b>Sergei Filippov</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=sergeifilippov" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/samsteele"><img src="https://avatars3.githubusercontent.com/u/10742174?v=4" width="100px;" alt=""/><br /><sub><b>Sam Steele</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=samsteele" title="Code">💻</a></td>
    <td align="center"><a href="https://goo.gl/WCUymp"><img src="https://avatars2.githubusercontent.com/u/59101?v=4" width="100px;" alt=""/><br /><sub><b>Ricardo Velhote</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=rvelhote" title="Code">💻</a></td>
    <td align="center"><a href="https://royduineveld.nl/"><img src="https://avatars2.githubusercontent.com/u/1703233?v=4" width="100px;" alt=""/><br /><sub><b>Roy Duineveld</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=royduin" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/roberto-ebizmarts"><img src="https://avatars0.githubusercontent.com/u/51710909?v=4" width="100px;" alt=""/><br /><sub><b>Roberto Sarmiento Pérez</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=roberto-ebizmarts" title="Code">💻</a></td>
    <td align="center"><a href="https://www.pierre-martin.fr/"><img src="https://avatars0.githubusercontent.com/u/75968?v=4" width="100px;" alt=""/><br /><sub><b>Pierre Martin</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=real34" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/rafdol"><img src="https://avatars2.githubusercontent.com/u/20263372?v=4" width="100px;" alt=""/><br /><sub><b>Rafał Dołgopoł</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=rafdol" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/rafaelpatro"><img src="https://avatars0.githubusercontent.com/u/13813964?v=4" width="100px;" alt=""/><br /><sub><b>Rafael Patro</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=rafaelpatro" title="Code">💻</a></td>
    <td align="center"><a href="https://copex.io/"><img src="https://avatars3.githubusercontent.com/u/1998210?v=4" width="100px;" alt=""/><br /><sub><b>Andreas Pointner</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=pointia" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/paulrodriguez"><img src="https://avatars2.githubusercontent.com/u/6373764?v=4" width="100px;" alt=""/><br /><sub><b>Paul Rodriguez</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=paulrodriguez" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/ollb"><img src="https://avatars0.githubusercontent.com/u/5952064?v=4" width="100px;" alt=""/><br /><sub><b>ollb</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=ollb" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/nintenic"><img src="https://avatars0.githubusercontent.com/u/1317618?v=4" width="100px;" alt=""/><br /><sub><b>Nicholas Graham</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=nintenic" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/mpalasis"><img src="https://avatars0.githubusercontent.com/u/37408939?v=4" width="100px;" alt=""/><br /><sub><b>Makis Palasis</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=mpalasis" title="Code">💻</a></td>
    <td align="center"><a href="http://magento.stackexchange.com/users/5209/mbalparda"><img src="https://avatars1.githubusercontent.com/u/3997682?v=4" width="100px;" alt=""/><br /><sub><b>Miguel Balparda</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=miguelbalparda" title="Code">💻</a></td>
    <td align="center"><a href="https://www.ecomni.nl/"><img src="https://avatars3.githubusercontent.com/u/2143634?v=4" width="100px;" alt=""/><br /><sub><b>Mark van der Sanden</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=markvds" title="Code">💻</a></td>
    <td align="center"><a href="https://binarzone.com/"><img src="https://avatars1.githubusercontent.com/u/200507?v=4" width="100px;" alt=""/><br /><sub><b>Micky Socaci</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=mickys" title="Code">💻</a></td>
    <td align="center"><a href="https://www.binaerfabrik.de/"><img src="https://avatars3.githubusercontent.com/u/7369753?v=4" width="100px;" alt=""/><br /><sub><b>Marvin Sengera</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=mSengera" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/kanevbg"><img src="https://avatars3.githubusercontent.com/u/11477130?v=4" width="100px;" alt=""/><br /><sub><b>Kostadin A.</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=kanevbg" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/julienloizelet"><img src="https://avatars3.githubusercontent.com/u/20956510?v=4" width="100px;" alt=""/><br /><sub><b>Julien Loizelet</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=julienloizelet" title="Code">💻</a></td>
    <td align="center"><a href="https://maxcluster.de/"><img src="https://avatars0.githubusercontent.com/u/1112507?v=4" width="100px;" alt=""/><br /><sub><b>Jonas Hünig</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=jonashrem" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/jaroschek"><img src="https://avatars1.githubusercontent.com/u/470290?v=4" width="100px;" alt=""/><br /><sub><b>Stefan Jaroschek</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=jaroschek" title="Code">💻</a></td>
    <td align="center"><a href="http://jacques.sh/"><img src="https://avatars2.githubusercontent.com/u/858611?v=4" width="100px;" alt=""/><br /><sub><b>Jacques Bodin-Hullin</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=jacquesbh" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/googlygoo"><img src="https://avatars3.githubusercontent.com/u/7078871?v=4" width="100px;" alt=""/><br /><sub><b>Wilhelm Ellmann</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=googlygoo" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/edwinkortman"><img src="https://avatars2.githubusercontent.com/u/7047894?v=4" width="100px;" alt=""/><br /><sub><b>Edwin.</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=edwinkortman" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/drago-aca"><img src="https://avatars3.githubusercontent.com/u/14777419?v=4" width="100px;" alt=""/><br /><sub><b>drago-aca</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=drago-aca" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/dng-dev"><img src="https://avatars0.githubusercontent.com/u/836079?v=4" width="100px;" alt=""/><br /><sub><b>Daniel Niedergesäß</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=dng-dev" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/davis2125"><img src="https://avatars2.githubusercontent.com/u/14129105?v=4" width="100px;" alt=""/><br /><sub><b>J Davis</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=davis2125" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/damien-biasotto"><img src="https://avatars0.githubusercontent.com/u/430633?v=4" width="100px;" alt=""/><br /><sub><b>Damien Biasotto</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=damien-biasotto" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/cundd"><img src="https://avatars2.githubusercontent.com/u/743122?v=4" width="100px;" alt=""/><br /><sub><b>Daniel Corn</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=cundd" title="Code">💻</a></td>
    <td align="center"><a href="http://www.cieslix.com/"><img src="https://avatars0.githubusercontent.com/u/6729521?v=4" width="100px;" alt=""/><br /><sub><b>Paweł Cieślik</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=cieslix" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/borriglione"><img src="https://avatars2.githubusercontent.com/u/465544?v=4" width="100px;" alt=""/><br /><sub><b>André Herrn</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=borriglione" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/blopa"><img src="https://avatars3.githubusercontent.com/u/3838114?v=4" width="100px;" alt=""/><br /><sub><b>Pablo Benmaman</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=blopa" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/aterjung"><img src="https://avatars1.githubusercontent.com/u/3084302?v=4" width="100px;" alt=""/><br /><sub><b>aterjung</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=aterjung" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/altdovydas"><img src="https://avatars3.githubusercontent.com/u/8860049?v=4" width="100px;" alt=""/><br /><sub><b>altdovydas</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=altdovydas" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/alissonjr"><img src="https://avatars2.githubusercontent.com/u/11911917?v=4" width="100px;" alt=""/><br /><sub><b>Alisson Júnior</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=alissonjr" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/alexkirsch"><img src="https://avatars3.githubusercontent.com/u/9553441?v=4" width="100px;" alt=""/><br /><sub><b>Alex Kirsch</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=alexkirsch" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/SnowCommerceBrand"><img src="https://avatars3.githubusercontent.com/u/37154233?v=4" width="100px;" alt=""/><br /><sub><b>Branden</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=SnowCommerceBrand" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/PofMagicfingers"><img src="https://avatars3.githubusercontent.com/u/469501?v=4" width="100px;" alt=""/><br /><sub><b>Pof Magicfingers</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=PofMagicfingers" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/MichaelThessel"><img src="https://avatars1.githubusercontent.com/u/2926266?v=4" width="100px;" alt=""/><br /><sub><b>Michael Thessel</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=MichaelThessel" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/JonLaliberte"><img src="https://avatars3.githubusercontent.com/u/5403662?v=4" width="100px;" alt=""/><br /><sub><b>Jonathan Laliberte</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=JonLaliberte" title="Code">💻</a></td>
    <td align="center"><a href="https://www.linkedin.com/in/ivanchepurnyi"><img src="https://avatars2.githubusercontent.com/u/866758?v=4" width="100px;" alt=""/><br /><sub><b>Ivan Chepurnyi</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=IvanChepurnyi" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/Ig0r-M-magic42"><img src="https://avatars1.githubusercontent.com/u/22006850?v=4" width="100px;" alt=""/><br /><sub><b>Igor</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=Ig0r-M-magic42" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/EliasKotlyar"><img src="https://avatars0.githubusercontent.com/u/9529505?v=4" width="100px;" alt=""/><br /><sub><b>Elias Kotlyar</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=EliasKotlyar" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/Hejty1"><img src="https://avatars2.githubusercontent.com/u/53661954?v=4" width="100px;" alt=""/><br /><sub><b>Hejty1</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=Hejty1" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/Gaelle"><img src="https://avatars2.githubusercontent.com/u/112183?v=4" width="100px;" alt=""/><br /><sub><b>Gaelle</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=Gaelle" title="Code">💻</a></td>
    <td align="center"><a href="https://www.martinez-frederic.fr/"><img src="https://avatars3.githubusercontent.com/u/13019288?v=4" width="100px;" alt=""/><br /><sub><b>Frédéric MARTINEZ</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=FredericMartinez" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/FaustTobias"><img src="https://avatars1.githubusercontent.com/u/48201729?v=4" width="100px;" alt=""/><br /><sub><b>Tobias Faust</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=FaustTobias" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/AndresInSpace"><img src="https://avatars2.githubusercontent.com/u/14356094?v=4" width="100px;" alt=""/><br /><sub><b>AndresInSpace</b></sub></a><br /><a href="https://github.com/OpenMage/magento-lts/commits?author=AndresInSpace" title="Code">💻</a></td>
  </tr>
</table>

<!-- markdownlint-enable -->
<!-- prettier-ignore-end -->
<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!

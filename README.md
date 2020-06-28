<p align="center">
<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
[![All Contributors](https://img.shields.io/badge/all_contributors-8-orange.svg?style=flat-square)](#contributors-)
<!-- ALL-CONTRIBUTORS-BADGE:END -->
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

Download the latest archive and extract it, clone the repo, or add a composer dependency to your existing project like so:

```json
"openmage/magento-lts": "1.9.4.x"
```

[More Information](http://openmage.github.io/magento-lts/install.html)

## Requirements

- PHP 7.0+ (PHP 7.3 and OpenSSL extension strongly recommended)
- MySQL 5.6+ (8.0+ Recommended)

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
  </tr>
</table>

<!-- markdownlint-enable -->
<!-- prettier-ignore-end -->
<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!
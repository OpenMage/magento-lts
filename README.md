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
"openmage/magento-lts": "1.9.3.x"
```

## Requirements

- PHP 5.6+ (PHP 7.1 and OpenSSL extension strongly recommended)
- MySQL 5.6+

## Translations
There are some new or changed tranlations, if you want add them to your locale pack please check:
- `app/locale/en_US/*_LTS.csv`

## License

[OSL v3.0](http://opensource.org/licenses/OSL-3.0)

## Public Communication and online Community places

* [Discord](https://discord.gg/EV8aNbU) (maintained by Flyingmana)

## Maintainers

* [Lee Saferite](https://github.com/LeeSaferite)
* [David Robinson](https://github.com/drobinson)
* [Daniel Fahlke aka Flyingmana](https://github.com/Flyingmana)
* Pull requests are welcome
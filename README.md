# Magento - Long Term Support

This repository aims to be a dependably patched archive of the Magento CE core releases. These sources should stay as close to the sources released by Magento as possible (no new features).  **However, pull requests with unofficial bug fixes and security patches from the community are definitely encouraged.** It's our goal to apply patches available from Magento as quickly as possible, but these do not always cover all known issues.

Though Magento does not follow [Semantic Versioning](http://semver.org/) we aim to provide a workable system for dependancy definition.  
Each Magento `1.<minor>.<revision>` release will get its own branch (named `1.<minor>.<revision>.x`) that will be independently maintained (for as long as it makes sense to do so) with upstream patches and community bug fixes. For example, Magento version `1.9.3.4` was merged into the `1.9.3.x` branch.

Note, the branches older than `1.9.3.x` that were created before this strategy came into practice are not maintained.


## Installation
This allows you to define your version dependencies safely in composer.json:

```json
"openmage/magento-lts": "1.9.3.x-dev"
```

## Important to notes

### PHP7
PHP7 support was added as of version 1.9.2.3. based on the Inchoo article and [described here](https://github.com/OpenMage/magento-lts/pull/62).

### Translations
There are some new or changed tranlations, if you want add them to your locale packe please check:
- `app/locale/en_US/Mage_Sales_LTS.csv`

## License
[OSL v3.0](http://opensource.org/licenses/OSL-3.0)


## Maintainers
* [Lee Saferite](https://github.com/LeeSaferite)
* [David Robinson](https://github.com/drobinson)
* [Daniel Fahlke aka Flyingmana](https://github.com/Flyingmana)
* Pull requests are welcome


## TODO
* Travis CI tests to check for newly availble patches for each version

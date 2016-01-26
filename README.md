#Applied Patches

| Patch | Commit |
| ----- | ------ |
| SUPEE-6788 | [8635d49](https://github.com/OpenMage/magento-lts/commit/8635d49fb43261a97310fffc647618a34972afd6) |
| SUPEE-7616 | [6d78f44](https://github.com/OpenMage/magento-lts/commit/6d78f44) |
| SUPEE-7405 | [88d98dd](https://github.com/OpenMage/magento-lts/commit/88d98dd) |


# Magento - Long Term Support

This repository aims to be a dependably patched archive of the Magento CE core releases. These sources should stay as close to the sources released by Magento as possible (no new features).  **However, pull requests with unofficial bug fixes and security patches from the community are definitely encouraged.** It's our goal to apply patches available from Magento as quickly as possible, but these do not always cover all known issues.

Though Magento does not follow [Semantic Versioning](http://semver.org/) we aim to provide a workable system for dependancy definition.  A release version might look something like "1.9.1.0", but there may have been some functionality added since the "1.9.0.0" release.  There might also have been some patches released with no update to the currently available sources or version number.  

Because of this, we must define a slightly different system to define each decimal place.


##\#MageVer
#####1 - UBER VERSION
######.
#####9 - Magento Major Version
######.
#####1 - Magento Minor Version
######.
#####0 - ? (maybe some patches)


Each Magento Version release will get its own branch that will be independently maintained with patches and backported bug fixes.


## Installation
This allows you to define your version dependencies safely in composer.json:

```json
"openmage/magento-lts": "1.9.2.1-dev"
```


## License
[OSL v3.0](http://opensource.org/licenses/OSL-3.0)


## Maintainers
* [Lee Saferite](https://github.com/LeeSaferite)
* [David Robinson](https://github.com/drobinson)
* Pull requests are welcome


## TODO
* Travis CI tests to check for newly availble patches for each version

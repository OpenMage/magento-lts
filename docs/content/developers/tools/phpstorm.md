---
tags:
- Development
- SCSS
---

# PhpStorm

See: https://www.jetbrains.com/phpstorm/

## Metadata Factory Helper

This repo includes class maps for the core Magento files in `.phpstorm.meta.php`.
To add class maps for installed extensions, you have to install [N98-magerun](https://github.com/netz98/n98-magerun)
and run command:

```bash
n98-magerun.phar dev:ide:phpstorm:meta
```

You can add additional meta files in this directory to cover your own project files. See
[PhpStorm advanced metadata](https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html)
for more information.

## File-Watcher for SCSS files
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
  $FilePare
  ```
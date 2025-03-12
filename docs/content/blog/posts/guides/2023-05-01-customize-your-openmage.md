---
title: Customize Your OpenMage
draft: false
date: 2023-08-17
authors:
  - colinmollenhour
categories:
  - Guides
tags:
  - Composer
  - Patches
  - Vendor
---

# Customize Your OpenMage

When working on OpenMage or any complex PHP project, you might come across issues that require patches to be applied to third-party packages or libraries.
These patches may be fixes or features for core code that you need to use immediately but have not yet been merged or formally released for any number of reasons.

In this blog post, we'll explore the benefits of using privately maintained patches and how to use Composer and the `cweagans/composer-patches` dependency to maintain
your own set of patches as well as how to generate patches.

<!-- more -->

## Benefits of Privately Maintained Patches

1. Customization - Customize third-party packages or libraries to meet the specific needs of your project. This is especially useful if you need to modify functionality that is impossible to do without modifying core code or fix bugs that are not yet addressed by the package maintainers.
2. Security - By maintaining your own patches, you can ensure that any security vulnerabilities are addressed as soon as they are discovered, without having to wait for the package maintainers to release an update.
3. Maintainability - Privately maintained patches can help ensure your patches are organized and automatically re-applied to the core code so they don't get accidentally clobbered when updating project dependencies.

## Using Composer and `cweagans/composer-patches` Dependency

Composer is a dependency manager for PHP that allows you to easily manage third-party packages and libraries in your project. However, using the `cweagans/composer-patches` package
(documentation [here](https://github.com/cweagans/composer-patches/blob/1.x/README.md)) you can also have Composer apply private patches over public dependencies. You simply define the patches in your `composer.json` file and they will be automatically applied to the
packages or libraries that you specify when a Composer updates dependencies (`install` or `update`).

Here's an example `composer.json` file with a patch defined:
    
```json    
{
  "require": {
    "vendor/package": "1.0.0"
  },
  "extra": {
    "patches": {
      "vendor/package": {
        "Fix Bug #1234": "patches/fix-bug-1234.patch"
      }
    }
  }
}
```

In this example, we're requiring the `vendor/package` package at version `1.0.0`, and applying a patch named "Fix Bug \#1234" located in the `patches/fix-bug-1234.patch` file.

To apply the patch, simply run `composer update` and Composer will apply the patch and maintain the state of whether the patch has been applied or not.

## Generating patch files

There are many ways to do this, but here are a few common ones:

### GitHub Pull Requests

If a GitHub PR already contains the code changes you need, simply download a patch file from GitHub by appending `.patch` to the PR URL. You gotta love GitHub!

For example, the patch URL for PR #3146 is: `https://github.com/OpenMage/magento-lts/pull/3146.patch`. This URL will result in a redirect so download it with your
browser or a client that follows redirects (Location header) such as `wget` or `curl -L`.

```
mkdir -p patches
echo -e "Order deny,allow\nDeny from all\n" > patches/.htaccess
curl -L https://github.com/OpenMage/magento-lts/pull/3146.patch -o var/patches/3146_Add-form-key-validation-to-Contacts-form.patch
```

Note, you can also have the patch downloaded at runtime by defining it as a URL instead of a local path.

```json
{
  "...": "...",
  "extra": {
    "patches": {
      "openmage/magento-lts": {
        "Added form key validation to Contacts form #3146": "https://github.com/OpenMage/magento-lts/pull/3146.patch"
      }
    }
  }
}
```

If it doesn't apply cleanly, apply the patch using `git apply` and fix the conflicts and then output the changes as a local patch file using one of the methods below.

### `symplify/vendor-patches`

If you are using OpenMage as a project dependency, you can use `symplify/vendor-patches` to generate a patch file easily. This tool automatically
updates your `composer.json` for you. Note, in the case of modifying OpenMage core code installed as a project dependency you will need to modify the
files in the "vendor" directory rather than the `magento-root-dir` directory (e.g. `htdocs`).

Here is an example generating patches for `app/Mage.php`:

```sh
composer require symplify/vendor-patches --dev
cp vendor/app/Mage.php vendor/app/Mage.php.old
# modify app/Mage.php to your liking
vendor/bin/vendor-patches generate
composer install
```

### Git with dirty working copy

If you have local changes on a git working copy you can simply use `git diff > patches/my-changes.patch`.

Use `git diff --cached` instead to export just the staged changes.

### Git commits

You can generate a patch from a branch or commit or range of commits using `git format-patch` or `git diff`. See the `--help` usage for more info but here are some examples:

To get a single patch with the changes from the `some-feature` branch:

```sh
git format-patch origin/main...some-feature --stdout > patches/some-feature.patch
```

Notice the three `...` which gets all changes as one patch. If you used `..` you would get one file per commit.

Use `git show <commit>` to show a single commit or `git diff <base>...<commit>` to get all changes between two commits. Consult the internet for more info.

## Testing patches

Use `git apply` or `patch` to test applying the patches to your working copy or just go for it with `composer install`.

Use `composer update --lock` to save the state of the applied patches to your `composer.lock` file.

Give it a try and see how privately maintained patches can benefit your project!

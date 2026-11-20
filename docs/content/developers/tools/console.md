---
tags:
- Development
---

# Command line (`bin/openmage`)

OpenMage ships a command line tool built on [Symfony Console](https://symfony.com/doc/current/components/console.html).
Its commands keep the names of [n98-magerun](n98-magerun.md), so deployment scripts can switch with few changes.

## Usage

From the OpenMage root of a cloned repository:

```bash
bin/openmage list
bin/openmage cache:clean config layout
bin/openmage cache:flush
```

In a project that requires `openmage/magento-lts` with Composer, run it from `vendor/bin`.
The OpenMage root is read from `extra.magento-root-dir` of the project `composer.json`; without it, it is the project root:

```bash
vendor/bin/openmage list
```

The script of the installed package, `vendor/openmage/magento-lts/bin/openmage`, finds the project in the same way.
Both expect the `vendor` directory in the project root; the `--root-dir` option sets the OpenMage root explicitly.

`list` and `help` read the module configuration files only: they work without `app/etc/local.xml` and without database.
The other commands start the application.
They need an installed OpenMage, with its `app/etc/local.xml`, and a reachable database; `sys:info` also runs before installation.

The commands create cache files as the user who runs them.
With a file cache backend, run them as the web server user, e.g. `sudo -u www-data bin/openmage cache:flush`: the web server may not be able to write files created by another user.

## Available commands

| Command                       | Description                                                                 |
|-------------------------------|-----------------------------------------------------------------------------|
| `cache:clean [type...]`       | Clean the given cache types, or all of them, as "Refresh" does in the admin |
| `cache:flush`                 | Flush the whole cache storage, as "Flush Cache Storage" does in the admin   |
| `index:list`                  | List the indexes with their status and last runtime                         |
| `index:reindex [code...]`     | Reindex the given indexes, asks for them when none is given                 |
| `index:reindex:all`           | Reindex all indexes                                                         |
| `sys:info [key]`              | Print the version, the paths, the code pools and the entity counts          |
| `admin:user:create`           | Create an admin user                                                        |
| `config:get [path]`           | Print configuration values stored in the database                           |
| `config:set <path> <value>`   | Save a configuration value in the database                                  |
| `completion [shell]`          | Print the shell completion script, a command of Symfony Console             |

`bin/openmage help <command>` lists all the arguments and options of a command.

Every command that starts the application has a `--store` option.
It sets the store the application is started in, `admin` by default, and does not filter what a command reads or writes: the `config:*` commands select a scope with `--scope` and `--scope-id`.
An unknown store code is an error.

### Errors and exit codes

A command that fails writes its error to the error output and exits with a non-zero code: 1 for the errors described on this page.
The standard output only carries the result, so a script can capture it:

```bash
BASE_URL=$(bin/openmage config:get web/unsecure/base_url --format=json)
```

### Cache

Both commands rebuild the configuration cache afterwards; `--no-reinit` skips it.
`--reinit` is accepted and changes nothing, as in n98-magerun.

```bash
bin/openmage cache:clean config layout
bin/openmage cache:flush
```

### Indexes

`index:list` prints the code, the status and the last runtime of each index; `--format` accepts `table` (default), `json` or `csv`.
`index:reindex` takes index codes separated by commas or spaces:

```bash
bin/openmage index:list
bin/openmage index:list --format=json
bin/openmage index:reindex catalog_product_price,catalog_url
bin/openmage index:reindex:all
```

```text
Started reindex of: catalog_product_price
Successfully re-indexed catalog_product_price (Runtime: 00:00:01)
Started reindex of: catalog_url
Successfully re-indexed catalog_url (Runtime: 00:00:01)
```

Without code, `index:reindex` lists the indexes and asks for their numbers or codes, e.g. `1,tag_summary`.
With `--no-interaction` (`-n`), it fails and prints the known codes instead.

An unknown code stops `index:reindex` before any reindex.
`index:reindex` and `index:reindex:all` write their progress to the standard output.
When an index fails or is locked, its error goes to the error output, the next indexes still run, and the command exits with the code 1.

### System information

`sys:info` prints the OpenMage and Magento versions, the edition, the paths, the cache backend and the code pools.
Once OpenMage is installed, it also prints the install date and the number of attributes, customers, categories and products.
The row names are in snake case, e.g. `magento_version` or `cache_backend`.

```bash
bin/openmage sys:info
bin/openmage sys:info --format=json
bin/openmage sys:info version
bin/openmage sys:info cache_backend
```

`--format` accepts `table` (default), `json` or `csv`.
With a key, `sys:info` prints the raw value of that row only and ignores `--format`; keys are case-insensitive.
The encryption key is never printed.

### Admin users

`admin:user:create` takes the user name, email, password, first name, last name and, optionally, the name of an existing role (`Administrators` by default).
It asks for the missing arguments; the password is asked with hidden input and confirmed:

```bash
bin/openmage admin:user:create
bin/openmage admin:user:create jdoe jdoe@example.com
```

The user is checked with the same rules as the admin user form, e.g. the minimum password length of the admin configuration.
The command prints each error and exits with an error when the user is invalid or the role does not exist.
The user and its role are saved in one transaction: either both are created, or none.

!!! warning
    A password given as argument stays in the shell history and is visible in the process list.
    Give it as argument only in scripts that read it from a protected source, e.g. an environment variable:
    `bin/openmage admin:user:create -n jdoe jdoe@example.com "$ADMIN_PASSWORD" John Doe`.

### Configuration

`config:get` lists the values of the `core_config_data` table, every scope included.
The default values of the `config.xml` files are not listed.
Without path, every value is listed.
The path may contain `*` wildcards, and a trailing `/` lists the children: `config:get web/` is the same as `config:get 'web/*'`.

```bash
bin/openmage config:get web/
bin/openmage config:get web/unsecure/base_url --format=json
bin/openmage config:get 'payment/*/active' --scope=websites --scope-id=base
```

```text
+-----------------------+---------+----------+---------------------------+
| path                  | scope   | scope_id | value                     |
+-----------------------+---------+----------+---------------------------+
| web/secure/base_url   | default | 0        | http://openmage-cli.test/ |
| web/seo/use_rewrites  | default | 0        | 1                         |
| web/unsecure/base_url | default | 0        | http://openmage-cli.test/ |
+-----------------------+---------+----------+---------------------------+
```

`--scope` (`default`, `websites` or `stores`) and `--scope-id` (an ID, or a website or store code together with `--scope`) filter the values.
`--format` accepts `table` (default), `json` or `csv`; the keys are `path`, `scope`, `scope_id` and `value`.
`--update-script` prints the values as `setConfigData()` calls for a setup script, `--magerun-script` as `config:set` command lines.

When no value matches, `config:get` still exits with the code 0 and its standard output is empty: `[]` in JSON, nothing in CSV and with the script options.
The table format writes a notice to the error output.
An invalid scope, an unknown website or store code and an unknown format are errors.

`--decrypt` decrypts the values that have the form of an encrypted value: base64 text of whole 8-byte cipher blocks, whose decryption with the key of `app/etc/local.xml` gives printable text.
The other values are printed as they are stored.
The check reads the value, not the field: a value encrypted with another key is printed encrypted, without notice.
With `--decrypt`, `--magerun-script` adds `--encrypt` to the lines of the decrypted values, so that replaying them encrypts the values again.

`config:set` writes the value in `core_config_data` for the `default` scope, or for the scope given by `--scope` and `--scope-id`.
It then rebuilds the configuration as the admin does after saving; `--no-reinit` skips it when several values are set in a row.
The value `NULL` stores a null value, unless `--no-null` is given.

```bash
bin/openmage config:set design/head/default_title "My shop" --scope=stores --scope-id=default
bin/openmage config:set web/unsecure/base_url https://shop.test/ --validate
bin/openmage config:set carriers/dhl/password "$DHL_PASSWORD" --encrypt
```

The path needs at least three non-empty segments, as in `section/group/field`; `--force` does not lift this check.
The website or store of `--scope-id` must exist, and the admin website and store of the `websites` and `stores` scopes (ID 0 or code `admin`) are refused.
`--force` allows the admin website and store, and a numeric ID of a website or store that does not exist.
The two-character sequences `\n` and `\r` of the value are converted to line breaks, and cannot be escaped.

With `--validate`, the backend model declared by the field in `system.xml` checks and transforms the value, as the admin configuration does: validation, encryption of protected fields, cron expressions.
The admin events `admin_system_config_section_save_after` and `admin_system_config_changed_section_<section>` are dispatched.
`--validate` fails, and saves nothing, when the path is not a field of `system.xml`, and for a field that `system.xml` saves under another path (`config_path`): the message gives that path, to set without `--validate`.
Fields uploading a file cannot be saved with `--validate`.

The command prints the path and the saved value, or `(encrypted)` in place of a value encrypted by `--encrypt` or by the backend model.

!!! warning
    Values are stored as given: `config:set` without `--validate` does not encrypt a password field.
    Give `--encrypt` to encrypt it with the key of `app/etc/local.xml`, or `--validate` when the field's backend model encrypts it; the command refuses both together on such a field.
    A secret given as value stays in the shell history and is visible in the process list, as the password of `admin:user:create`: read it from a protected source.
    `config:get --decrypt` prints the secrets in plain text, and so do the scripts of `--decrypt --magerun-script` and `--decrypt --update-script`: do not keep them in a log or a repository.
    Without `--decrypt`, these scripts keep the encrypted values, which only an installation with the same key can read.

### Differences from n98-magerun

The commands keep the names and arguments of n98-magerun, with these differences:

- Errors are written to the error output; n98-magerun writes most of them to the standard output.
- `--format` accepts `table`, `json` and `csv`, not `xml`.
- The messages of `cache:clean`, `cache:flush` and `admin:user:create` are worded differently.
- `cache:clean` and `cache:flush` start the application with the existing configuration cache; n98-magerun starts it without the cache.
- `cache:flush` exits with an error when the cache storage cannot be flushed.
- `sys:info` names its rows in snake case, e.g. `cache_backend` for `Cache Backend`, and never prints the encryption key.
- `sys:info version` prints the OpenMage version only, e.g. `20.19.1`, and a `magento_version` row is added.
- `admin:user:create` checks the user with the rules of the admin user form, and saves the user and its role in one transaction.
- `admin:user:create` gives the `Administrators` role by default; it no longer creates a `Development` role.
- `config:get` names its keys `path`, `scope`, `scope_id` and `value`, for `Path`, `Scope`, `Scope-ID` and `Value`.
- `config:get` exits with the code 0 when no value matches; n98-magerun exits with an error.
- In `config:get`, `_` and `%` in the path are no longer SQL wildcards, and `--scope-id=0` filters the values of ID 0.
- `config:get --decrypt` only decrypts the values that have the form of an encrypted value, and adds `--encrypt` to their `--magerun-script` lines.
- `--magerun-script` of `config:get` adds `--force` to the commands for the ID 0 of the `websites` and `stores` scopes.
- `config:set` refuses a path of less than three segments, and checks that the website or store of a numeric `--scope-id` exists.
- `config:set` rebuilds the configuration after saving, and adds the `--validate` and `--no-reinit` options.

## Declare a command in a module

A module declares its commands in its `etc/config.xml`, one node per command under `global/console/commands`.
The node name is the key of the declaration, `name` is the command name and `class` the command class:

```xml
<config>
    <global>
        <console>
            <commands>
                <vendor_sample_hello>
                    <name>sample:hello</name>
                    <class>Vendor\Sample\Console\HelloCommand</class>
                </vendor_sample_hello>
            </commands>
        </console>
    </global>
</config>
```

The class must extend `Symfony\Component\Console\Command\Command`.
Namespaced classes are not loaded by the OpenMage autoloader: declare them in the `autoload` section of the module's `composer.json`.
Extend `OpenMage\Console\Command\AbstractCommand` when the command needs the application: it starts `Mage::app()` before `execute()` and adds the `--store` option.

```php
<?php

declare(strict_types=1);

namespace Vendor\Sample\Console;

use Mage;
use OpenMage\Console\Command\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'sample:hello', description: 'Print the default store name')]
final class HelloCommand extends AbstractCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(Mage::app()->getDefaultStoreView()->getName());

        return Command::SUCCESS;
    }
}
```

Commands are instantiated only when they run or are listed.
A declaration whose class cannot be found is reported on the error output, and the other commands stay available.
Two declarations with different keys and the same `name` are reported on the error output too: the last one wins.

### Replace or disable a command

Another module replaces a command by declaring its key again with another class, or disables it with `disabled`:

```xml
<console>
    <commands>
        <cache_clean>
            <class>Vendor\Sample\Console\CleanCommand</class>
        </cache_clean>
        <cache_flush>
            <disabled>1</disabled>
        </cache_flush>
    </commands>
</console>
```

The module must depend on the module that declares the command (here `Mage_Core`) in its `app/etc/modules` file, so that its configuration is merged afterwards.

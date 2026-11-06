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
The OpenMage root is read from `extra.magento-root-dir` of the project `composer.json`:

```bash
vendor/bin/openmage list
```

The `--root-dir` option sets the OpenMage root explicitly.

`list` and `help` read the module configuration files only: they work without `app/etc/local.xml` and without database.
The other commands start the application, with the `admin` store unless `--store` gives another store code.

## Available commands

| Command       | Description                                                                         |
|---------------|-------------------------------------------------------------------------------------|
| `cache:clean` | Clean the given cache types, or all of them, as "Refresh" does in the admin         |
| `cache:flush` | Flush the whole cache storage, as "Flush Cache Storage" does in the admin           |

Both commands rebuild the configuration cache afterwards; `--no-reinit` skips it.

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

### Replace or disable a command

Another module replaces a command by redeclaring its key with another class, or disables it with `disabled`:

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

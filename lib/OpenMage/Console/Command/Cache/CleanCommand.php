<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console\Command\Cache;

use Override;
use Mage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Ported from the cache:clean command of n98-magerun (https://github.com/netz98/n98-magerun)
 *
 * n98-magerun: Copyright (c) netz98 GmbH, MIT license, see lib/OpenMage/Console/LICENSE-n98-magerun.txt
 */
#[AsCommand(name: 'cache:clean', description: 'Clean cache types, all of them when none is given')]
final class CleanCommand extends AbstractCacheCommand
{
    #[Override]
    protected function configure(): void
    {
        parent::configure();

        $this
            ->addArgument('type', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Cache type codes, e.g. config layout')
            ->setHelp('Cleans the entries of the given cache types, as "Refresh" does in the admin cache management.' . PHP_EOL
                . 'Use cache:flush to remove every entry of the cache storage.');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cache = Mage::app()->getCacheInstance();
        $knownTypes = array_keys($cache->getTypes());

        /** @var list<string> $types */
        $types = $input->getArgument('type');
        $unknownTypes = array_diff($types, $knownTypes);
        if ($unknownTypes !== []) {
            $this->writeError($output, sprintf(
                '<error>Unknown cache type: %s. Known types: %s.</error>',
                implode(', ', $unknownTypes),
                implode(', ', $knownTypes),
            ));

            return Command::FAILURE;
        }

        foreach ($types === [] ? $knownTypes : $types as $type) {
            $cache->cleanType($type);
            Mage::dispatchEvent('adminhtml_cache_refresh_type', ['type' => $type]);
            $output->writeln(sprintf('<info>Cache type <comment>%s</comment> cleaned</info>', $type));
        }

        $this->reinitConfig($input);

        return Command::SUCCESS;
    }
}

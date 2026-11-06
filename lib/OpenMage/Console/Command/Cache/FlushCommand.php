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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Ported from the cache:flush command of n98-magerun (https://github.com/netz98/n98-magerun)
 *
 * n98-magerun: Copyright (c) netz98 GmbH, MIT license, see lib/OpenMage/Console/LICENSE-n98-magerun.txt
 */
#[AsCommand(name: 'cache:flush', description: 'Flush the whole cache storage')]
final class FlushCommand extends AbstractCacheCommand
{
    #[Override]
    protected function configure(): void
    {
        parent::configure();

        $this->setHelp('Removes every entry of the cache storage, including entries that do not belong to OpenMage,'
            . ' as "Flush Cache Storage" does in the admin cache management.');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Mage::app()->getCacheInstance()->flush()) {
            $this->writeError($output, '<error>The cache storage could not be flushed.</error>');

            return Command::FAILURE;
        }

        Mage::dispatchEvent('adminhtml_cache_flush_all');
        $output->writeln('<info>Cache storage flushed</info>');

        $this->reinitConfig($input);

        return Command::SUCCESS;
    }
}

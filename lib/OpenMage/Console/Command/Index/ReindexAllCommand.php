<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console\Command\Index;

use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Ported from the index:reindex:all command of n98-magerun (https://github.com/netz98/n98-magerun)
 *
 * n98-magerun: Copyright (c) netz98 GmbH, MIT license, see lib/OpenMage/Console/LICENSE-n98-magerun.txt
 */
#[AsCommand(name: 'index:reindex:all', description: 'Reindex all indexes')]
final class ReindexAllCommand extends AbstractIndexCommand
{
    #[Override]
    protected function configure(): void
    {
        $this->setHelp('Rebuilds every index; when one fails or is locked, goes on with the next ones and exits with an error.');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $processes = array_values($this->getProcesses());

        return $this->executeProcesses($output, $processes) ? Command::SUCCESS : Command::FAILURE;
    }
}

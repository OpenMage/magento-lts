<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console\Command\Index;

use OpenMage\Console\Output\TableRenderer;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Ported from the index:list command of n98-magerun (https://github.com/netz98/n98-magerun)
 *
 * n98-magerun: Copyright (c) netz98 GmbH, MIT license, see lib/OpenMage/Console/LICENSE-n98-magerun.txt
 */
#[AsCommand(name: 'index:list', description: 'List the indexes with their status and last runtime')]
final class ListCommand extends AbstractIndexCommand
{
    #[Override]
    protected function configure(): void
    {
        $this->addOption('format', null, InputOption::VALUE_REQUIRED, TableRenderer::OPTION_DESCRIPTION, 'table');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $format = TableRenderer::checkFormat($input->getOption('format'));

        $rows = [];
        foreach ($this->getProcesses() as $code => $process) {
            $rows[] = [$code, (string) $process->getStatus(), $this->getRuntime($process)];
        }

        (new TableRenderer())->render($output, ['code', 'status', 'time'], $rows, $format);

        return Command::SUCCESS;
    }
}

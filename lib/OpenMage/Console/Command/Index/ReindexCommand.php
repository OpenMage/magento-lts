<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console\Command\Index;

use InvalidArgumentException;
use Mage_Index_Model_Process;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Ported from the index:reindex command of n98-magerun (https://github.com/netz98/n98-magerun)
 *
 * n98-magerun: Copyright (c) netz98 GmbH, MIT license, see lib/OpenMage/Console/LICENSE-n98-magerun.txt
 */
#[AsCommand(name: 'index:reindex', description: 'Reindex the given indexes, asks for them when none is given')]
final class ReindexCommand extends AbstractIndexCommand
{
    #[Override]
    protected function configure(): void
    {
        $this
            ->addArgument('index_code', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Index codes, separated by commas or spaces')
            ->setHelp('Rebuilds the given indexes, e.g. index:reindex catalog_product_price,catalog_url' . PHP_EOL
                . 'Without code, lists the indexes and reads their numbers or codes, e.g. "1,3".' . PHP_EOL
                . 'Fails without reindexing anything when a code is unknown; when an index fails or is locked,'
                . ' goes on with the next ones and exits with an error.');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $processes = $this->getProcesses();

        /** @var list<string> $arguments */
        $arguments = $input->getArgument('index_code');
        $codes = $this->splitCodes(implode(',', $arguments));
        if ($codes === []) {
            if (!$input->isInteractive()) {
                $this->writeError($output, sprintf(
                    '<error>No index code given. Known codes: %s.</error>',
                    implode(', ', array_keys($processes)),
                ));

                return Command::FAILURE;
            }

            $codes = $this->askForCodes($input, $output, $processes);
        }

        $unknownCodes = array_diff($codes, array_keys($processes));
        if ($unknownCodes !== []) {
            $this->writeError($output, sprintf(
                '<error>Unknown index code: %s. Known codes: %s.</error>',
                implode(', ', $unknownCodes),
                implode(', ', array_keys($processes)),
            ));

            return Command::FAILURE;
        }

        $selected = array_map(static fn(string $code): Mage_Index_Model_Process => $processes[$code], $codes);

        return $this->executeProcesses($output, $selected) ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * @return list<string>
     */
    private function splitCodes(string $codes): array
    {
        $codes = array_map(trim(...), explode(',', $codes));

        return array_values(array_unique(array_filter($codes, static fn(string $code): bool => $code !== '')));
    }

    /**
     * @param  array<string, Mage_Index_Model_Process> $processes
     * @return list<string>
     */
    private function askForCodes(InputInterface $input, OutputInterface $output, array $processes): array
    {
        $codes = array_keys($processes);
        $choices = [];
        foreach ($processes as $code => $process) {
            $choices[] = sprintf('%-40s <info>(last runtime: %s)</info>', $code, $this->getRuntime($process));
        }

        $question = new ChoiceQuestion('<question>Please select indexes, by number or code:</question> ', $choices);
        // ChoiceQuestion autocompletes with the choice labels by default, and the completed text replaces the input.
        $question->setAutocompleterValues($codes);
        $question->setValidator(function (?string $answer) use ($codes): array {
            $selected = [];
            foreach ($this->splitCodes((string) $answer) as $choice) {
                $code = ctype_digit($choice) ? ($codes[(int) $choice] ?? null) : $choice;
                if ($code === null || !in_array($code, $codes, true)) {
                    throw new InvalidArgumentException(sprintf('Invalid index: %s', $choice));
                }

                $selected[] = $code;
            }

            if ($selected === []) {
                throw new InvalidArgumentException('Select at least one index.');
            }

            return array_values(array_unique($selected));
        });

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        /** @var list<string> $answer */
        $answer = $helper->ask($input, $output, $question);

        return $answer;
    }
}

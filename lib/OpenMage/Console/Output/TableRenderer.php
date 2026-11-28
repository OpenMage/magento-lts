<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console\Output;

use InvalidArgumentException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Renders rows as a console table, JSON or CSV, for the --format option of the commands
 */
final class TableRenderer
{
    public const FORMATS = ['table', 'json', 'csv'];

    public const OPTION_DESCRIPTION = 'Output format: table, json or csv';

    /**
     * @throws InvalidArgumentException
     */
    public static function checkFormat(mixed $format): string
    {
        if (!is_string($format) || !in_array($format, self::FORMATS, true)) {
            throw new InvalidArgumentException(sprintf(
                'Unknown format "%s", expected one of: %s.',
                is_scalar($format) ? (string) $format : get_debug_type($format),
                implode(', ', self::FORMATS),
            ));
        }

        return $format;
    }

    /**
     * @param list<string>                $headers
     * @param list<list<null|int|string>> $rows
     */
    public function render(OutputInterface $output, array $headers, array $rows, string $format = 'table'): void
    {
        match (self::checkFormat($format)) {
            'json' => $this->renderJson($output, $headers, $rows),
            'csv' => $this->renderCsv($output, $headers, $rows),
            default => $this->renderTable($output, $headers, $rows),
        };
    }

    /**
     * @param list<string>                $headers
     * @param list<list<null|int|string>> $rows
     */
    private function renderTable(OutputInterface $output, array $headers, array $rows): void
    {
        $table = new Table($output);
        $table->setHeaders($headers);
        foreach ($rows as $row) {
            $table->addRow(array_map(static fn(null|int|string $cell): string => (string) $cell, $row));
        }

        $table->render();
    }

    /**
     * @param list<string>                $headers
     * @param list<list<null|int|string>> $rows
     */
    private function renderJson(OutputInterface $output, array $headers, array $rows): void
    {
        $objects = array_map(static fn(array $row): array => array_combine($headers, $row), $rows);
        $output->writeln(
            json_encode($objects, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            OutputInterface::OUTPUT_RAW,
        );
    }

    /**
     * Writes nothing, not even the header line, when there are no rows
     *
     * @param list<string>                $headers
     * @param list<list<null|int|string>> $rows
     */
    private function renderCsv(OutputInterface $output, array $headers, array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $stream = fopen('php://temp', 'r+');
        if ($stream === false) {
            throw new InvalidArgumentException('Unable to open a temporary stream for the CSV output.');
        }

        foreach ([$headers, ...$rows] as $row) {
            fputcsv($stream, $row, ',', '"', '');
        }

        rewind($stream);
        $output->write((string) stream_get_contents($stream), false, OutputInterface::OUTPUT_RAW);
        fclose($stream);
    }
}

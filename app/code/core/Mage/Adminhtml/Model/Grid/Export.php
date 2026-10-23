<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

declare(strict_types=1);

/**
 * Shared serialization and file handling for admin grid exports.
 * Grid preparation, collection loading and extension hooks stay in the blocks.
 * Magento model aliases require underscore-separated class names.
 *
 * @SuppressWarnings("php:S101")
 */
class Mage_Adminhtml_Model_Grid_Export
{
    /**
     * @param  Mage_Adminhtml_Block_Widget_Grid_Column[] $columns
     * @return list<string>
     */
    public function getHeaders(array $columns, bool $export = false): array
    {
        $headers = [];
        foreach ($columns as $column) {
            if (!$column->getIsSystem()) {
                $headers[] = $export ? $column->getExportHeader() : $column->getHeader();
            }
        }

        return $headers;
    }

    /**
     * @param  Mage_Adminhtml_Block_Widget_Grid_Column[] $columns
     * @return list<string>
     */
    public function getRow(array $columns, Varien_Object $item, bool $export = false): array
    {
        $row = [];
        foreach ($columns as $column) {
            if (!$column->getIsSystem()) {
                $row[] = $export ? $column->getRowFieldExport($item) : $column->getRowField($item);
            }
        }

        return $row;
    }

    /**
     * Preserve the string export's quoting, including its legacy backslash escaping.
     * Report labels are unescaped; report totals escape quotes only.
     */
    public function getCsvField(mixed $value, bool $escapeQuotes = true, bool $escapeBackslashes = true): string
    {
        $value = (string) $value;
        if ($escapeQuotes) {
            $value = str_replace('"', '""', $value);
        }

        if ($escapeBackslashes) {
            $value = str_replace('\\', '\\\\', $value);
        }

        return '"' . $value . '"';
    }

    /** @param array<mixed> $row */
    public function getCsvRow(array $row, bool $escape = true): string
    {
        $fields = [];
        foreach ($row as $value) {
            $fields[] = $this->getCsvField($value, $escape, $escape);
        }

        return implode(',', $fields) . "\n";
    }

    /**
     * Keep the legacy string parser distinct from the streaming file parser:
     * they use different SpreadsheetML markup and escaping.
     *
     * @param array<array<mixed>> $rows
     */
    public function getExcel(array $rows, mixed $sheetName = ''): string
    {
        $parser = new Varien_Convert_Parser_Xml_Excel();
        $parser->setVar('single_sheet', $sheetName);
        $parser->setData($rows);
        $parser->unparse();

        return $parser->getData();
    }

    /**
     * @param  callable(Varien_Io_File): void               $write
     * @return array{type: string, value: string, rm: bool}
     */
    public function writeFile(string $extension, callable $write): array
    {
        $ioFile = new Varien_Io_File();
        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $file = $path . DS . bin2hex(random_bytes(16)) . '.' . $extension;
        $ioFile->setAllowCreateFolders(true);
        $ioFile->open(['path' => $path]);

        $ioFile->streamOpen($file, 'x+');

        $complete = false;
        try {
            $ioFile->streamLock(true);
            $write($ioFile);
            $complete = true;
        } finally {
            $ioFile->streamClose();
            if (!$complete) {
                $ioFile->rm($file);
            }
        }

        return ['type' => 'filename', 'value' => $file, 'rm' => true];
    }
}

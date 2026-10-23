<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Widget;

use Mage;
use Mage_Adminhtml_Block_Widget_Grid;
use Mage_ImportExport_Model_Export_Adapter_Csv;
use OpenMage\Tests\Unit\Fixture\Adminhtml\GridExport\ExportCollection;
use OpenMage\Tests\Unit\Fixture\Adminhtml\GridExport\ExportGrid;
use OpenMage\Tests\Unit\Fixture\Adminhtml\GridExport\ExportReportGrid;
use OpenMage\Tests\Unit\OpenMageTest;
use Override;
use Varien_Io_File;
use Varien_Object;

final class GridExportTest extends OpenMageTest
{
    public static function provideExports(): array
    {
        $cases = [];
        foreach (['getCsv', 'getExcel', 'getXml', 'getCsvFile', 'getExcelFile'] as $method) {
            $cases['grid-' . $method] = [false, $method, true, true, false, false];
            $cases['grid-empty-' . $method] = [false, $method, false, false, false, false];
        }
        foreach (['getCsv', 'getExcel'] as $method) {
            $cases['report-' . $method] = [true, $method, true, true, false, false];
            $cases['report-system-first-' . $method] = [true, $method, true, true, true, false];
            $cases['report-no-totals-' . $method] = [true, $method, false, true, false, false];
            $cases['report-empty-' . $method] = [true, $method, false, false, false, false];
            $cases['report-no-subtotals-' . $method] = [true, $method, true, true, false, false, false];
        }
        $cases['grid-csv-string-formula-escaping'] = [false, 'getCsv', true, true, false, true];
        $cases['grid-csv-formula-escaping'] = [false, 'getCsvFile', true, true, false, true];
        return $cases;
    }

    /** @dataProvider provideExports */
    public function testExistingOutput(
        bool $report,
        string $method,
        bool $totals,
        bool $withRows,
        bool $systemFirst,
        bool $escapeFormulas,
        bool $subtotals = true,
    ): void {
        $store = Mage::app()->getStore();
        $path = Mage_ImportExport_Model_Export_Adapter_Csv::CONFIG_ESCAPING_FLAG;
        $original = $store->getConfig($path);
        $store->setConfig($path, $escapeFormulas ? '1' : '0');
        try {
            $grid = $report ? new ExportReportGrid($withRows, $systemFirst) : new ExportGrid($withRows);
            if ($escapeFormulas) {
                $grid->setCollection(new ExportCollection([['name' => 'Formula', 'amount' => '=SUM(A1)']]));
            }
            $grid->setCountTotals($totals);
            if ($grid instanceof ExportReportGrid) {
                $grid->setSubtotalVisibility($subtotals);
            }
            $output = self::export($grid, $method);
            $key = implode('-', [(int) $report, $method, (int) $totals, (int) $withRows, (int) $systemFirst, (int) $escapeFormulas, (int) $subtotals]);
            $json = file_get_contents(__DIR__ . '/_files/grid-export.json');
            self::assertIsString($json);
            $expected = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            self::assertSame($expected[$key], $output);
            self::assertSame(['prepare'], array_slice($grid->calls, 0, 1));
            if (!$report) {
                self::assertSame(str_ends_with($method, 'File') ? ['prepare'] : ['prepare', 'before', 'after'], $grid->calls);
            }
        } finally {
            $store->setConfig($path, $original);
        }
    }

    public static function export(Mage_Adminhtml_Block_Widget_Grid $grid, string $method, mixed $sheetName = 'Sales report'): string
    {
        $result = match ($method) {
            'getCsv' => $grid->getCsv(),
            'getCsvFile' => $grid->getCsvFile(),
            'getXml' => $grid->getXml(),
            'getExcel' => $grid->getExcel($sheetName),
            'getExcelFile' => $grid->getExcelFile($sheetName),
            default => throw new \InvalidArgumentException($method),
        };
        if (is_string($result)) {
            return $result;
        }
        self::assertSame('filename', $result['type']);
        self::assertTrue($result['rm']);
        $file = $result['value'];
        self::assertIsString($file);
        try {
            $content = file_get_contents($file);
            self::assertIsString($content);
            return $content;
        } finally {
            unlink($file);
        }
    }

    public static function provideWorksheetNames(): array
    {
        return [[''], ['0'], [null], [42], ['Résumé & "name"']];
    }

    /** @dataProvider provideWorksheetNames */
    public function testWorksheetNames(mixed $sheetName): void
    {
        $json = file_get_contents(__DIR__ . '/_files/grid-export.json');
        self::assertIsString($json);
        $expected = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        foreach (['getExcelFile', 'getExcel'] as $method) {
            if ($method === 'getExcel' && in_array($sheetName, ['', '0', null], true)) {
                $this->expectException(\TypeError::class);
                self::export(new ExportGrid(), $method, $sheetName);
            }
            $key = $method . '-sheet-' . json_encode($sheetName);
            self::assertSame($expected[$key], self::export(new ExportGrid(), $method, $sheetName));
        }
    }

    public static function providePageSizes(): array
    {
        return [[0], [1], [2], [3], [4], [5]];
    }

    /** @dataProvider providePageSizes */
    public function testFileExportsKeepPaginationAndOriginalCollection(int $count): void
    {
        $grid = new ExportGrid(false);
        $collection = new ExportCollection(array_fill(0, $count, ['name' => 'row', 'amount' => '0']));
        $grid->setCollection($collection);
        self::export($grid, 'getCsvFile');
        self::assertNotTrue($collection->isLoaded());
        self::assertCount(max(1, (int) ceil($count / 2)), $collection->trace->pages);
        foreach ($collection->trace->pages as $index => $page) {
            self::assertSame([$index + 1, 2], $page);
        }
    }

    public function testFileExportRetainsSubclassHooks(): void
    {
        $grid = new class extends ExportGrid {
            /** @var list<string> */
            public array $hooks = [];

            /** @return list<string> */
            #[Override]
            protected function _getExportHeaders(): array
            {
                $this->hooks[] = 'headers';
                return ['custom header'];
            }

            /** @return list<string> */
            #[Override]
            protected function _getExportTotals(): array
            {
                $this->hooks[] = 'totals';
                return ['custom totals'];
            }

            #[Override]
            public function _exportIterateCollection($callback, array $args): void
            {
                $this->hooks[] = $callback;
                parent::_exportIterateCollection($callback, $args);
            }

            #[Override]
            protected function _exportCsvItem(Varien_Object $item, Varien_Io_File $adapter): void
            {
                $this->hooks[] = 'csv item';
                $adapter->streamWriteCsv(['custom row']);
            }
        };
        $grid->setCountTotals();
        self::assertSame("\"custom header\"\n\"custom row\"\n\"custom row\"\n\"custom row\"\n\"custom totals\"\n", self::export($grid, 'getCsvFile'));
        self::assertSame(['headers', '_exportCsvItem', 'csv item', 'csv item', 'csv item', 'totals'], $grid->hooks);
    }
}

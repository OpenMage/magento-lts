<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Widget;

use InvalidArgumentException;
use Iterator;
use TypeError;
use RuntimeException;
use Error;
use Throwable;
use ReflectionProperty;
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
use Varien_Convert_Parser_Xml_Excel;
use Zend_Db_Select;

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

            $output = $this->export($grid, $method);
            $key = implode('-', [(int) $report, $method, (int) $totals, (int) $withRows, (int) $systemFirst, (int) $escapeFormulas, (int) $subtotals]);
            $json = file_get_contents(__DIR__ . '/_files/grid-export.json');
            self::assertIsString($json);
            $expected = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $this->assertExportSame($expected[$key], $output, $method);
            self::assertSame(['prepare'], array_slice($grid->calls, 0, 1));
            if (!$report) {
                self::assertSame(str_ends_with($method, 'File') ? ['prepare'] : ['prepare', 'before', 'after'], $grid->calls);
            } else {
                self::assertSame($withRows ? ['prepare', 'first', 'second'] : ['prepare'], $grid->calls);
            }
        } finally {
            $store->setConfig($path, $original);
        }
    }

    private function assertExportSame(string $expected, string $actual, string $method): void
    {
        if ($method === 'getExcelFile') {
            // libxml versions serialize a retained CR as either &#13; or &#xD;.
            // Normalize only that equivalent reference; preserve CR/LF and all other bytes.
            $expected = str_replace('&#13;', '&#xD;', $expected);
            $actual = str_replace('&#13;', '&#xD;', $actual);
        }

        self::assertSame($expected, $actual);
    }

    private function export(Mage_Adminhtml_Block_Widget_Grid $grid, string $method, mixed $sheetName = 'Sales report'): string
    {
        $result = match ($method) {
            'getCsv' => $grid->getCsv(),
            'getCsvFile' => $grid->getCsvFile(),
            'getXml' => $grid->getXml(),
            'getExcel' => $grid->getExcel($sheetName),
            'getExcelFile' => $grid->getExcelFile($sheetName),
            default => throw new InvalidArgumentException($method),
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

    public static function provideWorksheetNames(): Iterator
    {
        yield [''];
        yield ['0'];
        yield [null];
        yield [42];
        yield ['Résumé & "name"'];
    }

    /** @dataProvider provideWorksheetNames */
    public function testWorksheetNames(mixed $sheetName): void
    {
        $json = file_get_contents(__DIR__ . '/_files/grid-export.json');
        self::assertIsString($json);
        $expected = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        foreach (['getExcelFile', 'getExcel'] as $method) {
            if ($method === 'getExcel' && in_array($sheetName, ['', '0', null], true)) {
                $this->expectException(TypeError::class);
                $this->export(new ExportGrid(), $method, $sheetName);
            }

            $key = $method . '-sheet-' . json_encode($sheetName);
            $this->assertExportSame($expected[$key], $this->export(new ExportGrid(), $method, $sheetName), $method);
        }
    }

    public static function provideFileFormats(): Iterator
    {
        yield ['getCsvFile'];
        yield ['getExcelFile'];
    }

    public static function providePageSizes(): Iterator
    {
        foreach (range(0, 5) as $count) {
            foreach (self::provideFileFormats() as [$method]) {
                yield [$count, $method];
            }
        }
    }

    /** @dataProvider providePageSizes */
    public function testFileExportsKeepPaginationAndOriginalCollection(int $count, string $method): void
    {
        $grid = new ExportGrid(false);
        $collection = new ExportCollection(array_fill(0, $count, ['name' => 'row', 'amount' => '0']));
        $grid->setCollection($collection);
        $this->export($grid, $method);
        self::assertNotTrue($collection->isLoaded());
        self::assertCount(max(1, (int) ceil($count / 2)), $collection->trace->pages);
        foreach ($collection->trace->pages as $index => $page) {
            self::assertSame([$index + 1, 2], $page);
        }
    }

    /**
     * Subclass the real Magento grid to verify its protected extension hooks.
     *
     * @dataProvider provideFileFormats
     * @SuppressWarnings("php:S110")
     */
    public function testFileExportRetainsSubclassHooks(string $method): void
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

            #[Override]
            protected function _exportExcelItem(Varien_Object $item, Varien_Io_File $adapter, $parser = null): void
            {
                GridExportTest::assertInstanceOf(Varien_Convert_Parser_Xml_Excel::class, $parser);
                $this->hooks[] = 'excel item';
                $adapter->streamWrite($parser->getRowXml(['custom row']));
            }

        };
        $grid->setCountTotals();

        $output = $this->export($grid, $method);
        if ($method === 'getCsvFile') {
            self::assertSame("\"custom header\"\n\"custom row\"\n\"custom row\"\n\"custom row\"\n\"custom totals\"\n", $output);
            self::assertSame(['headers', '_exportCsvItem', 'csv item', 'csv item', 'csv item', 'totals'], $grid->hooks);
        } else {
            preg_match_all('/<Data ss:Type="String">(.*?)<\/Data>/s', $output, $matches);
            self::assertSame(['custom header', 'custom row', 'custom row', 'custom row', 'custom totals'], $matches[1]);
            self::assertSame(['headers', '_exportExcelItem', 'excel item', 'excel item', 'excel item', 'totals'], $grid->hooks);
        }
    }

    public static function provideStringFormats(): Iterator
    {
        yield ['getCsv'];
        yield ['getExcel'];
        yield ['getXml'];
    }

    /** @dataProvider provideStringFormats */
    public function testStringExportsClearPaginationBeforeLoading(string $method): void
    {
        $grid = new ExportGrid();
        $collection = $grid->getCollection();
        self::assertInstanceOf(ExportCollection::class, $collection);
        $collection->setPageSize(1);
        $collection->getSelect()->limit(1, 1);
        $this->export($grid, $method);
        self::assertSame(0, $collection->getPageSize());
        self::assertNull($collection->getSelect()->getPart(Zend_Db_Select::LIMIT_COUNT));
        self::assertNull($collection->getSelect()->getPart(Zend_Db_Select::LIMIT_OFFSET));
        self::assertCount(3, $collection->getItems());
    }

    public static function provideExportFailures(): Iterator
    {
        yield ['getCsvFile', new RuntimeException('Renderer failed')];
        yield ['getCsvFile', new Error('Renderer failed')];
        yield ['getExcelFile', new RuntimeException('Renderer failed')];
        yield ['getExcelFile', new Error('Renderer failed')];
    }

    /**
     * Subclass the real Magento grid to throw from its export extension point.
     *
     * @dataProvider provideExportFailures
     * @SuppressWarnings("php:S110")
     */
    public function testFailedExportsCloseAndRemovePartialFiles(string $method, Throwable $failure): void
    {
        $grid = new class extends ExportGrid {
            public Throwable $failure;

            public Varien_Io_File $adapter;

            public string $file;

            #[Override]
            public function _exportIterateCollection($callback, array $args): void
            {
                GridExportTest::assertInstanceOf(Varien_Io_File::class, $args[0]);
                $this->adapter = $args[0];
                $file = (new ReflectionProperty(Varien_Io_File::class, '_streamFileName'))->getValue($this->adapter);
                GridExportTest::assertIsString($file);
                $this->file = $file;
                parent::_exportIterateCollection($callback, $args);
                throw $this->failure;
            }
        };
        $grid->failure = $failure;
        try {
            $this->export($grid, $method);
            self::fail('The original export failure must propagate.');
        } catch (Throwable $throwable) {
            self::assertSame($failure, $throwable);
        }

        try {
            self::assertFalse($grid->adapter->streamWrite('closed'));
            self::assertFileDoesNotExist($grid->file);
        } finally {
            $grid->adapter->streamClose();
            if (is_file($grid->file)) {
                unlink($grid->file);
            }
        }
    }
}

<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Reports\Model\Resource\Report;

use Mage;
use Mage_Reports_Model_Report;
use Mage_Reports_Model_Resource_Report_Collection as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Reports\ReportsTrait;
use OpenMage\Tests\Unit\OpenMageTest;
use Zend_Date;
use Zend_Date_Exception;

class CollectionTest extends OpenMageTest
{
    use ReportsTrait;

    public const SKIP_INCOMPLETE_MESSAGE = 'Test needs to be reviewed.';

    private static Subject $subject;

    public function setUp(): void
    {
        self::$subject = Mage::getModel('reports/resource_report_collection');
    }

    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::setPeriod()
     * @group Model
     */
    public function testSetPeriod(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->setPeriod('1m'));
    }

    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::setInterval()
     * @group Model
     */
    public function testSetIntervals(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->setInterval(new Zend_Date(), new Zend_Date()));
    }

    /**
     * @dataProvider provideReportsDateIntervals
     * @group Model
     */
    public function testGetIntervals(int|string $expectedResult, string|Zend_Date $from, string|Zend_Date $to, string $period): void
    {
        self::$subject->setInterval($from, $to);
        self::$subject->setPeriod($period);

        try {
            self::assertIsArray(self::$subject->getIntervals());
        } catch (Zend_Date_Exception $exception) {
            self::assertSame("No date part in '' found.", $exception->getMessage());
        }
    }

    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::getPeriods()
     * @group Model
     */
    public function testGetPeriods(): void
    {
        self::assertIsArray(self::$subject->getPeriods());
    }

    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::getStoreIds()
     * @covers Mage_Reports_Model_Resource_Report_Collection::setStoreIds()
     * @group Model
     */
    public function testStoreIds(): void
    {
        self::$subject->setStoreIds([]);
        self::assertSame([], self::$subject->getStoreIds());
    }

    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::getSize()
     * @group Model
     */
    public function testGetSize(): void
    {
        self::assertIsInt(self::$subject->getSize());
    }
    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::setPageSize()
     * @group Model
     */
    public function testSetPageSize(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->setPageSize(1));
    }

    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::getPageSize()
     * @group Model
     */
    public function testGetPageSize(): void
    {
        self::assertNull(self::$subject->getPageSize());
    }

    /**
     * @group Model
     */
    public function testInitReport(string $modelClass = ''): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->initReport($modelClass));
    }

    /**
     * @group Model
     */
    public function testGetReportFull(): void
    {
        self::markTestSkipped(self::SKIP_INCOMPLETE_MESSAGE);
        /** @phpstan-ignore deadCode.unreachable */
        self::assertInstanceOf(Mage_Reports_Model_Report::class, self::$subject->getReportFull(1, 1));
    }

    /**
     * @group Model
     */
    public function testGetReport(): void
    {
        self::markTestSkipped(self::SKIP_INCOMPLETE_MESSAGE);
        /** @phpstan-ignore deadCode.unreachable */
        self::assertInstanceOf(Mage_Reports_Model_Report::class, self::$subject->getReport(1, 1));
    }

    /**
     * @group Model
     */
    public function testTimeShift(): void
    {
        self::markTestSkipped(self::SKIP_INCOMPLETE_MESSAGE);
        /** @phpstan-ignore deadCode.unreachable */
        self::assertSame(self::$subject->timeShift(''));
    }
}

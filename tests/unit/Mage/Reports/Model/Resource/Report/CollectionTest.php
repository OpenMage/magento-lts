<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Reports\Model\Resource\Report;

use Mage;
use Mage_Reports_Model_Report;
use Mage_Reports_Model_Resource_Report_Collection as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Reports\ReportsTrait;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    use ReportsTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('reports/resource_report_collection');
    }

    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::setPeriod()
     * @group Mage_Reports
     * @group Mage_Reports_Model
     */
    public function testSetPeriod(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->setPeriod(1));
    }

    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::setInterval()
     * @group Mage_Reports
     * @group Mage_Reports_Model
     */
    public function testIsModuleEnabled(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->setInterval(1, 1));
    }

    /**
     * @dataProvider provideReportsDateIntervals
     * @group Mage_Reports
     * @group Mage_Reports_Model
     */
    public function testGetIntervals(): void
    {
        $this->subject->setInterval(1, 1);
        $this->assertIsArray($this->subject->getIntervals());
    }

    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::getPeriods()
     * @group Mage_Reports
     * @group Mage_Reports_Model
     */
    public function testGetPeriods(): void
    {
        $this->assertIsArray($this->subject->getPeriods());
    }

    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::setStoreIds()
     * @group Mage_Reports
     * @group Mage_Reports_Model
     */
    public function testSetStoreIds(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->setStoreIds([]));
    }

    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::getStoreIds()
     * @group Mage_Reports
     * @group Mage_Reports_Model
     */
    public function testGetStoreIds(): void
    {
        $this->assertNull($this->subject->getStoreIds());
    }

    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::getSize()
     * @group Mage_Reports
     * @group Mage_Reports_Model
     */
    public function testGetSize(): void
    {
        $this->assertIsInt($this->subject->getSize());
    }
    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::setPageSize()
     * @group Mage_Reports
     * @group Mage_Reports_Model
     */
    public function testSetPageSize(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->setPageSize(1));
    }

    /**
     * @covers Mage_Reports_Model_Resource_Report_Collection::getPageSize()
     * @group Mage_Reports
     * @group Mage_Reports_Model
     */
    public function testGetPageSize(): void
    {
        $this->assertNull($this->subject->getPageSize());
    }

    /**
     * @group Mage_Reports
     * @group Mage_Reports_Model
     */
    public function testInitReport($modelClass = ''): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->initReport($modelClass));
    }

    /**
     * @group Mage_Reports
     * @group Mage_Reports_Model
     */
    public function testGetReportFull(): void
    {
        $this->markTestIncomplete();
        // @phpstan-ignore-next-line
        $this->assertInstanceOf(Mage_Reports_Model_Report::class, $this->subject->getReportFull(1, 1));
    }

    /**
     * @group Mage_Reports
     * @group Mage_Reports_Model
     */
    public function testGetReport(): void
    {
        $this->markTestIncomplete();
        // @phpstan-ignore-next-line
        $this->assertInstanceOf(Mage_Reports_Model_Report::class, $this->subject->getReport(1, 1));
    }

    /**
     * @group Mage_Reports
     * @group Mage_Reports_Model
     */
    public function testTimeShift(): void
    {
        $this->markTestIncomplete();
        // @phpstan-ignore-next-line
        $this->assertSame($this->subject->timeShift(''));
    }
}

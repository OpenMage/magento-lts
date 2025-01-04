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

namespace OpenMage\Tests\Unit\Mage\Reports\Helper;

use Mage;
use Mage_Reports_Helper_Data as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Reports\ReportsTrait;
use PHPUnit\Framework\TestCase;
use Varien_Data_Collection;

class DataTest extends TestCase
{
    use ReportsTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('reports/data');
    }

    /**
     * @covers Mage_Core_Helper_Abstract::isModuleEnabled()
     * @group Mage_Reports
     * @group Mage_Reports_Helper
     */
    public function testIsModuleEnabled(): void
    {
        $this->assertTrue($this->subject->isModuleEnabled());
    }

    /**
     * @covers Mage_Reports_Helper_Data::isReportsEnabled()
     * @group Mage_Reports
     * @group Mage_Reports_Helper
     */
    public function testIsReportsEnabled(): void
    {
        $this->assertTrue($this->subject->isReportsEnabled());
    }

    /**
     * @covers Mage_Reports_Helper_Data::getIntervals()
     * @dataProvider provideReportsDateIntervals
     * @group Mage_Reports
     * @group Mage_Reports_Helper
     */
    public function testGetIntervals($expectedResult, $from, $to, $period): void
    {
        $this->assertCount($expectedResult, $this->subject->getIntervals($from, $to, $period));
    }

    /**
     * @covers Mage_Reports_Helper_Data::prepareIntervalsCollection()
     * @dataProvider provideReportsDateIntervals
     * @doesNotPerformAssertions
     * @group Mage_Reports
     * @group Mage_Reports_Helper
     */
    public function testrepareIntervalsCollection($expectedResult, $from, $to, $period): void
    {
        $collection = new Varien_Data_Collection();
        $this->subject->prepareIntervalsCollection($collection, $from, $to, $period);
    }
}

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
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper\Dashboard;

use Mage;
use Mage_Adminhtml_Helper_Dashboard_Data as Subject;
use Mage_Core_Model_Resource_Store_Collection;
use OpenMage\Tests\Unit\OpenMageTest;

class DataTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('adminhtml/dashboard_data');
    }

    /**
     * @covers Mage_Adminhtml_Helper_Dashboard_Data::getStores()
     * @group Helper
     */
    public function testGetStores(): void
    {
        static::assertInstanceOf(Mage_Core_Model_Resource_Store_Collection::class, self::$subject->getStores());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Dashboard_Data::countStores()
     * @group Helper
     */
    public function testCountStores(): void
    {
        static::assertIsInt(self::$subject->countStores());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Dashboard_Data::getDatePeriods()
     * @group Helper
     */
    public function testGetDatePeriods(): void
    {
        $expectedResult = [
            '24h' => self::$subject->__('Last 24 Hours'),
            '7d'  => self::$subject->__('Last 7 Days'),
            '1m'  => self::$subject->__('Current Month'),
            '3m'  => self::$subject->__('Last 3 Months'),
            '6m'  => self::$subject->__('Last 6 Months'),
            '1y'  => self::$subject->__('YTD'),
            '2y'  => self::$subject->__('2YTD'),
        ];
        static::assertSame($expectedResult, self::$subject->getDatePeriods());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Dashboard_Data::getChartDataHash()
     * @group Helper
     */
    public function testGetChartDataHash(): void
    {
        static::assertIsString(self::$subject->getChartDataHash(''));
    }
}

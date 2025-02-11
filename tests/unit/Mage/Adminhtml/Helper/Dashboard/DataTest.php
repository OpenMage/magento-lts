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
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('adminhtml/dashboard_data');
    }

    /**
     * @covers Mage_Adminhtml_Helper_Dashboard_Data::getStores()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testGetStores(): void
    {
        $this->assertInstanceOf(Mage_Core_Model_Resource_Store_Collection::class, $this->subject->getStores());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Dashboard_Data::countStores()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testCountStores(): void
    {
        $this->assertIsInt($this->subject->countStores());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Dashboard_Data::getDatePeriods()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testGetDatePeriods(): void
    {
        $expectedResult = [
            '24h' => $this->subject->__('Last 24 Hours'),
            '7d'  => $this->subject->__('Last 7 Days'),
            '1m'  => $this->subject->__('Current Month'),
            '1y'  => $this->subject->__('YTD'),
            '2y'  => $this->subject->__('2YTD'),
        ];
        $this->assertSame($expectedResult, $this->subject->getDatePeriods());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Dashboard_Data::getChartDataHash()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testGetChartDataHash(): void
    {
        $this->assertIsString($this->subject->getChartDataHash(''));
    }
}

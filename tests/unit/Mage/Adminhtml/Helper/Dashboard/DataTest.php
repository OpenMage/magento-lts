<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper\Dashboard;

use Mage;
use Mage_Adminhtml_Helper_Dashboard_Data as Subject;
use Mage_Core_Model_Resource_Store_Collection;
use OpenMage\Tests\Unit\OpenMageTest;

final class DataTest extends OpenMageTest
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
        self::assertInstanceOf(Mage_Core_Model_Resource_Store_Collection::class, self::$subject->getStores());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Dashboard_Data::countStores()
     * @group Helper
     */
    public function testCountStores(): void
    {
        self::assertIsInt(self::$subject->countStores());
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
        self::assertSame($expectedResult, self::$subject->getDatePeriods());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Dashboard_Data::getChartDataHash()
     * @group Helper
     */
    public function testGetChartDataHash(): void
    {
        self::assertIsString(self::$subject->getChartDataHash(''));
    }
}

<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Csp\Helper;

use Mage;
use Mage_Core_Model_App_Area;
use Mage_Csp_Helper_Data as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class DataTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('csp/data');
    }

    /**
     * @covers Mage_Csp_Helper_Data::isEnabled()
     * @group Helper
     */
    public function testIsEnabled(): void
    {
        self::assertFalse(self::$subject->isEnabled(Mage_Core_Model_App_Area::AREA_ADMINHTML));
        self::assertFalse(self::$subject->isEnabled(Mage_Core_Model_App_Area::AREA_FRONTEND));
    }

    /**
     * @covers Mage_Csp_Helper_Data::getReportOnly()
     * @group Helper
     */
    public function testGetReportOnly(): void
    {
        self::assertTrue(self::$subject->getReportOnly(Mage_Core_Model_App_Area::AREA_ADMINHTML));
        self::assertTrue(self::$subject->getReportOnly(Mage_Core_Model_App_Area::AREA_FRONTEND));
    }

    /**
     * @covers Mage_Csp_Helper_Data::getReportUri()
     * @group Helper
     */
    public function testGetReportUri(): void
    {
        self::assertIsString(self::$subject->getReportUri(Mage_Core_Model_App_Area::AREA_ADMINHTML));
        self::assertIsString(self::$subject->getReportUri(Mage_Core_Model_App_Area::AREA_FRONTEND));
    }

    /**
     * @covers Mage_Csp_Helper_Data::shouldSplitHeaders()
     * @group Helper
     */
    public function testShouldSplitHeaders(): void
    {
        self::assertFalse(self::$subject->shouldSplitHeaders(Mage_Core_Model_App_Area::AREA_ADMINHTML));
        self::assertFalse(self::$subject->shouldSplitHeaders(Mage_Core_Model_App_Area::AREA_FRONTEND));
    }

    /**
     * @covers Mage_Csp_Helper_Data::shouldMergeMeta()
     * @group Helper
     */
    public function testShouldMergeMeta(): void
    {
        self::assertTrue(self::$subject->shouldMergeMeta(Mage_Core_Model_App_Area::AREA_ADMINHTML));
        self::assertTrue(self::$subject->shouldMergeMeta(Mage_Core_Model_App_Area::AREA_FRONTEND));
    }

    /**
     * @covers Mage_Csp_Helper_Data::getReportOnlyHeader()
     * @group Helper
     */
    public function testGetReportOnlyHeader(): void
    {
        self::assertIsString(self::$subject->getReportOnlyHeader(Mage_Core_Model_App_Area::AREA_ADMINHTML));
        self::assertIsString(self::$subject->getReportOnlyHeader(Mage_Core_Model_App_Area::AREA_FRONTEND));
    }

    /**
     * @group Helper
     */
    public function testGetPolicies(): void
    {
        self::assertIsArray(self::$subject->getPolicies());
    }

    /**
     * @group Helper
     */
    public function testGetGlobalPolicy(): void
    {
        self::assertIsArray(self::$subject->getGlobalPolicy());
    }

    /**
     * @group Helper
     */
    public function testGetAreaPolicy(): void
    {
        self::assertIsArray(self::$subject->getAreaPolicy());
    }

    /**
     * @group Helper
     */
    public function testGetStoreConfigPolicy(): void
    {
        self::assertIsArray(self::$subject->getStoreConfigPolicy());
    }
}

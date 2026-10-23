<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper;

use Override;
use Mage;
use Mage_Adminhtml_Helper_Data as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;

final class DataTest extends OpenMageTest
{
    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('adminhtml/data');
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::getPageHelpUrl()
     * @group Helper
     */
    #[IgnoreDeprecations]
    public function testGetPageHelpUrl(): void
    {
        self::assertNull(self::$subject->getPageHelpUrl());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::setPageHelpUrl()
     * @group Helper
     */
    #[IgnoreDeprecations]
    public function testSetPageHelpUrl(): void
    {
        self::assertInstanceOf(self::$subject::class, self::$subject->setPageHelpUrl());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::addPageHelpUrl()
     * @group Helper
     */
    #[IgnoreDeprecations]
    public function testAddPageHelpUrl(): void
    {
        self::assertInstanceOf(self::$subject::class, self::$subject->addPageHelpUrl(null));
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::getUrl()
     * @group Helper
     */
    public function testGetUrl(): void
    {
        self::assertIsString(Subject::getUrl());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::getCustomAdminUrl()
     * @group Helper
     */
    public function testGetCustomAdminUrl(): void
    {
        self::assertIsString(Subject::getCustomAdminUrl());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::getCurrentUserId()
     * @group Helper
     */
    public function testGetCurrentUserId(): void
    {
        self::assertFalse(self::$subject->getCurrentUserId());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::prepareFilterString()
     * @group Helper
     */
    public function testPrepareFilterString(): void
    {
        self::assertIsArray(self::$subject->prepareFilterString(''));
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::decodeFilter()
     * @group Helper
     */
    public function testDecodeFilter(): void
    {
        $string = '';
        self::$subject->decodeFilter($string);
        self::assertSame('', $string);
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::isEnabledSecurityKeyUrl()
     * @group Helper
     */
    public function testIsEnabledSecurityKeyUrl(): void
    {
        self::assertTrue(self::$subject->isEnabledSecurityKeyUrl());
    }
}

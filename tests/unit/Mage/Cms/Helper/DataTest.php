<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Helper;

use Mage;
use Mage_Cms_Helper_Data as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Filter_Template;

final class DataTest extends OpenMageTest
{
    public const TEST_STRING = '1234567890';

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('cms/data');
    }

    /**
     * @group Helper
     */
    public function testGetAllowedStreamWrappers(): void
    {
        static::assertIsArray(self::$subject->getAllowedStreamWrappers());
    }

    /**
     * @group Helper
     */
    public function testGetBlockTemplateProcessor(): void
    {
        static::assertInstanceOf(Varien_Filter_Template::class, self::$subject->getBlockTemplateProcessor());
    }

    /**
     * @group Helper
     */
    public function testGetPageTemplateProcessor(): void
    {
        static::assertInstanceOf(Varien_Filter_Template::class, self::$subject->getPageTemplateProcessor());
    }

    /**
     * @group Helper
     */
    public function testIsSwfDisabled(): void
    {
        static::assertTrue(self::$subject->isSwfDisabled());
    }
}

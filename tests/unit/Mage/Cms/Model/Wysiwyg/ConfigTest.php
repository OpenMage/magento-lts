<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Model\Wysiwyg;

use Mage;
use Mage_Cms_Model_Wysiwyg_Config as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Object;

final class ConfigTest extends OpenMageTest
{
    public const TEST_STRING = '0123456789';

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('cms/wysiwyg_config');
    }

    /**
     * @group Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetConfig(): void
    {
        self::assertInstanceOf(Varien_Object::class, self::$subject->getConfig());
    }

    /**
     * @group Model
     */
    public function testGetSkinImagePlaceholderUrl(): void
    {
        self::assertIsString(self::$subject->getSkinImagePlaceholderUrl());
    }

    /**
     * @group Model
     */
    public function testGetSkinImagePlaceholderPath(): void
    {
        self::assertIsString(self::$subject->getSkinImagePlaceholderPath());
    }

    /**
     * @group Model
     */
    public function testIsEnabled(): void
    {
        self::assertIsBool(self::$subject->isEnabled());
    }

    /**
     * @group Model
     */
    public function testIsHidden(): void
    {
        self::assertIsBool(self::$subject->isHidden());
    }
}

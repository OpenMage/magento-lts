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

namespace OpenMage\Tests\Unit\Mage\Cms\Model\Wysiwyg;

use Mage;
use Mage_Cms_Model_Wysiwyg_Config as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Object;

class ConfigTest extends OpenMageTest
{
    public const TEST_STRING = '0123456789';

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('cms/wysiwyg_config');
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetConfig(): void
    {
        static::assertInstanceOf(Varien_Object::class, self::$subject->getConfig());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetSkinImagePlaceholderUrl(): void
    {
        static::assertIsString(self::$subject->getSkinImagePlaceholderUrl());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetSkinImagePlaceholderPath(): void
    {
        static::assertIsString(self::$subject->getSkinImagePlaceholderPath());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testIsEnabled(): void
    {
        static::assertIsBool(self::$subject->isEnabled());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testIsHidden(): void
    {
        static::assertIsBool(self::$subject->isHidden());
    }
}

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

namespace OpenMage\Tests\Unit\Mage\Cms\Helper;

use Mage;
use Mage_Cms_Helper_Data as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Filter_Template;

class DataTest extends OpenMageTest
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

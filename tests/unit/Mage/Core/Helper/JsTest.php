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

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Js as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class JsTest extends OpenMageTest
{
    public const TEST_URL = 'foo';

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/js');
    }

    /**
     * @covers Mage_Core_Helper_Js::getTranslateJson()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetTranslateJson(): void
    {
        static::assertIsString(self::$subject->getTranslateJson());
    }

    /**
     * @covers Mage_Core_Helper_Js::getTranslatorScript()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetTranslatorScript(): void
    {
        static::assertIsString(self::$subject->getTranslatorScript());
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testIncludeScript(): void
    {
        static::assertStringContainsString(self::TEST_URL, self::$subject->includeScript(self::TEST_URL));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testIncludeSkinScript(): void
    {
        static::assertStringContainsString(self::TEST_URL, self::$subject->includeSkinScript(self::TEST_URL));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetDeleteConfirmJs(): void
    {
        static::assertStringStartsWith('deleteConfirm', self::$subject->getDeleteConfirmJs('foo'));
        static::assertStringStartsWith('deleteConfirm', self::$subject->getDeleteConfirmJs('foo', 'bar'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetConfirmSetLocationJs(): void
    {
        static::assertStringStartsWith('confirmSetLocation', self::$subject->getConfirmSetLocationJs('foo'));
        static::assertStringStartsWith('confirmSetLocation', self::$subject->getConfirmSetLocationJs('foo', 'bar'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetSetLocationJs(): void
    {
        $result = self::$subject->getSetLocationJs(self::TEST_URL);
        static::assertStringStartsWith('setLocation', $result);
        static::assertStringContainsString(self::TEST_URL, $result);
    }

    /**
     * @covers Mage_Core_Helper_Js::getSaveAndContinueEditJs()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetSaveAndContinueEditJs(): void
    {
        $result = self::$subject->getSaveAndContinueEditJs(self::TEST_URL);
        static::assertStringStartsWith('saveAndContinueEdit', $result);
        static::assertStringContainsString(self::TEST_URL, $result);
    }
}

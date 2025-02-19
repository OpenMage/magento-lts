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
use PHPUnit\Framework\TestCase;

class JsTest extends TestCase
{
    public const TEST_URL = 'foo';

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/js');
    }

    /**
     * @covers Mage_Core_Helper_Js::getTranslateJson()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetTranslateJson(): void
    {
        $this->assertIsString($this->subject->getTranslateJson());
    }

    /**
     * @covers Mage_Core_Helper_Js::getTranslatorScript()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetTranslatorScript(): void
    {
        $this->assertIsString($this->subject->getTranslatorScript());
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testIncludeScript(): void
    {
        $this->assertStringContainsString(self::TEST_URL, $this->subject->includeScript(self::TEST_URL));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testIncludeSkinScript(): void
    {
        $this->assertStringContainsString(self::TEST_URL, $this->subject->includeSkinScript(self::TEST_URL));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetDeleteConfirmJs(): void
    {
        $this->assertStringStartsWith('deleteConfirm', $this->subject->getDeleteConfirmJs('foo'));
        $this->assertStringStartsWith('deleteConfirm', $this->subject->getDeleteConfirmJs('foo', 'bar'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetConfirmSetLocationJs(): void
    {
        $this->assertStringStartsWith('confirmSetLocation', $this->subject->getConfirmSetLocationJs('foo'));
        $this->assertStringStartsWith('confirmSetLocation', $this->subject->getConfirmSetLocationJs('foo', 'bar'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetSetLocationJs(): void
    {
        $result = $this->subject->getSetLocationJs(self::TEST_URL);
        $this->assertStringStartsWith('setLocation', $result);
        $this->assertStringContainsString(self::TEST_URL, $result);
    }

    /**
     * @covers Mage_Core_Helper_Js::getSaveAndContinueEditJs()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetSaveAndContinueEditJs(): void
    {
        $result = $this->subject->getSaveAndContinueEditJs(self::TEST_URL);
        $this->assertStringStartsWith('saveAndContinueEdit', $result);
        $this->assertStringContainsString(self::TEST_URL, $result);
    }
}

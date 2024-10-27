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
use Mage_Core_Helper_Js;
use PHPUnit\Framework\TestCase;

class JsTest extends TestCase
{
    public Mage_Core_Helper_Js $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/js');
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetTranslateJson(): void
    {
        $this->assertIsString($this->subject->getTranslateJson());
    }

    /**
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
        $this->assertIsString($this->subject->includeScript('test'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testIncludeSkinScript(): void
    {
        $this->assertIsString($this->subject->includeSkinScript('test'));
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
        $this->assertStringStartsWith('setLocation', $this->subject->getSetLocationJs('foo'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetSaveAndContinueEditJs(): void
    {
        $this->assertStringStartsWith('saveAndContinueEdit', $this->subject->getSaveAndContinueEditJs('foo'));
    }
}

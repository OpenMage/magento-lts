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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Tax\Rate\Toolbar;

use Mage;
use Mage_Adminhtml_Block_Tax_Rate_Toolbar_Save;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class SaveTest extends TestCase
{
//    private static ?Mage_Adminhtml_Block_Tax_Rate_Toolbar_Save $subject;

    public static function setUpBeforeClass(): void
    {
//        Mage::app();
//        self::$subject = new Mage_Adminhtml_Block_Tax_Rate_Toolbar_Save();
    }

    public static function tearDownAfterClass(): void
    {
//        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Tax_Rate_Toolbar_Save::getButtonBackBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Tax
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     * @doesNotPerformAssertions
     */
    public function testGetButtonBackBlock(): void
    {
        $this->markTestIncomplete();

//        self::$subject->setLayout(new Mage_Core_Model_Layout());
//
//        $result = self::$subject->getButtonBackBlock();
//        $this->assertSame('Back', $result->getLabel());
//        $this->assertStringStartsWith('window.location.href=', $result->getOnClick());
//        $this->assertSame('back', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Tax_Rate_Toolbar_Save::getButtonDeleteBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Tax
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     * @doesNotPerformAssertions
     */
    public function testGetButtonDeleteBlock(): void
    {
        $this->markTestIncomplete();

//        self::$subject->setLayout(new Mage_Core_Model_Layout());
//
//        $result = self::$subject->getButtonDeleteBlock();
//        $this->assertSame('Delete Rate', $result->getLabel());
//        $this->assertStringStartsWith('setLocation(', $result->getOnClick());
//        $this->assertSame('delete', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Tax_Rate_Toolbar_Save::getButtonResetBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Tax
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     * @doesNotPerformAssertions
     */
    public function testGetButtonResetBlock(): void
    {
        $this->markTestIncomplete();

//        self::$subject->setLayout(new Mage_Core_Model_Layout());
//
//        $result = self::$subject->getButtonResetBlock();
//        $this->assertSame('Reset', $result->getLabel());
//        $this->assertSame('window.location.reload()', $result->getOnClick());
//        $this->assertNull($result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Tax_Rate_Toolbar_Save::getButtonSaveBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Tax
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     * @doesNotPerformAssertions
     */
    public function testGetButtonSaveBlock(): void
    {
        $this->markTestIncomplete();

//        self::$subject->setLayout(new Mage_Core_Model_Layout());
//
//        $result = self::$subject->getButtonSaveBlock();
//        $this->assertSame('Save Rate', $result->getLabel());
//        $this->assertSame('window.location.reload()', $result->getOnClick());
//        $this->assertSame('save', $result->getClass());
    }
}

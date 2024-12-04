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

namespace OpenMage\Tests\Unit\Mage\Api2\Block\Adminhtml\Roles;

use Mage;
use Mage_Api2_Block_Adminhtml_Roles_Buttons;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class ButtonsTest extends TestCase
{
    private static ?Mage_Api2_Block_Adminhtml_Roles_Buttons $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Api2_Block_Adminhtml_Roles_Buttons();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Api2_Block_Adminhtml_Roles_Buttons::getButtonBackBlock()
     * @group Mage_Api2
     * @group Mage_Api2_Block
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonBackBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonBackBlock();
        $this->assertSame('Back', $result->getLabel());
        $this->assertStringStartsWith('window.location.href=', $result->getOnClick());
        $this->assertSame('back', $result->getClass());
    }

    /**
     * @covers Mage_Api2_Block_Adminhtml_Roles_Buttons::getButtonDeleteBlock())
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Api
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonDeleteBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonDeleteBlock();
        $this->assertSame('Delete Role', $result->getLabel());
        //roleId is not set at this moment, so we set script later
        $this->assertSame('window.location.reload()', $result->getOnClick());
        $this->assertSame('delete', $result->getClass());
    }

    /**
     * @covers Mage_Api2_Block_Adminhtml_Roles_Buttons::getButtonResetBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Api
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonResetBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonResetBlock();
        $this->assertSame('Reset', $result->getLabel());
        $this->assertSame('window.location.reload()', $result->getOnClick());
        $this->assertSame('', $result->getClass());
    }

    /**
     * @covers Mage_Api2_Block_Adminhtml_Roles_Buttons::getButtonSaveBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Api
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonSaveBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonSaveBlock();
        $this->assertSame('Save Role', $result->getLabel());
        $this->assertSame('roleForm.submit(); return false;', $result->getOnClick());
        $this->assertSame('save', $result->getClass());
    }
}

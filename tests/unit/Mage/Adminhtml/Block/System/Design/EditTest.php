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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\System\Design;

use Mage;
use Mage_Adminhtml_Block_System_Design_Edit;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;
use Varien_Object;

class EditTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_System_Design_Edit $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        Mage::register('design', new Varien_Object());
        self::$subject = new Mage_Adminhtml_Block_System_Design_Edit();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_System_Design_Edit::getButtonBackBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_System
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonBackBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonBackBlock();
        $this->assertSame('Back', $result->getLabel());
        $this->assertStringStartsWith('setLocation(', $result->getOnClick());
        $this->assertSame('back', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_System_Design_Edit::getButtonSaveBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_System
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonSaveBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonSaveBlock();
        $this->assertSame('Save', $result->getLabel());
        $this->assertSame('designForm.submit()', $result->getOnClick());
        $this->assertSame('save', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_System_Design_Edit::getButtonSaveBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_System
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonDeleteBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonDeleteBlock();
        $this->assertSame('Delete', $result->getLabel());
        $this->assertStringStartsWith('setLocation(', $result->getOnClick());
        $this->assertSame('delete', $result->getClass());
    }
}

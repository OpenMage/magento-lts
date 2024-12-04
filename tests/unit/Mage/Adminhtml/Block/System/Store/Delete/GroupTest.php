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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\System\Store\Delete;

use Mage;
use Mage_Adminhtml_Block_System_Store_Delete_Group;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class GroupTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_System_Store_Delete_Group $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_System_Store_Delete_Group();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_System_Store_Delete_Group::getButtonBackBlock()
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
        $this->assertSame('cancel', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_System_Store_Delete_Group::getButtonCancelBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_System
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonCancelBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonCancelBlock();
        $this->assertSame('Cancel', $result->getLabel());
        $this->assertStringStartsWith('setLocation(', $result->getOnClick());
        $this->assertSame('cancel', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_System_Store_Delete_Group::getButtonConfirmDeleteBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_System
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonConfirmDeleteBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonConfirmDeleteBlock();
        $this->assertSame('Delete Store', $result->getLabel());
        $this->assertSame('deleteForm.submit()', $result->getOnClick());
        $this->assertSame('cancel', $result->getClass());
    }
}
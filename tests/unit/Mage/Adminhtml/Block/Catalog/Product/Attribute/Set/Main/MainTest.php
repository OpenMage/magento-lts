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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Catalog\Product\Attribute\Set\Main;

use Mage;
use Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main;
use Mage_Core_Model_Layout;
use Mage_Eav_Model_Entity_Attribute_Set;
use PHPUnit\Framework\TestCase;

class MainTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        Mage::register('current_attribute_set', new Mage_Eav_Model_Entity_Attribute_Set());

        self::$subject = new Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main::getButtonAddGroupBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Catalog
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonAddGroupBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonAddGroupBlock();
        $this->assertSame('Add New', $result->getLabel());
        $this->assertSame('editSet.addGroup();', $result->getOnClick());
        $this->assertSame('add', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main::getButtonBackBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Catalog
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
     * @covers Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main::getButtonDeleteBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Catalog
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonDeleteBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonDeleteBlock();
        $this->assertSame('Delete Attribute Set', $result->getLabel());
        $this->assertStringStartsWith("deleteConfirm('All products of this set will be deleted! Are you sure you want to delete this attribute set?'", $result->getOnClick());
        $this->assertSame('delete', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main::getButtonDeleteGroupBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Catalog
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonDeleteGroupBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonDeleteGroupBlock();
        $this->assertSame('Delete Selected Group', $result->getLabel());
        $this->assertSame('editSet.submit();', $result->getOnClick());
        $this->assertSame('delete', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main::getButtonRenameBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Catalog
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonRenameBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonRenameBlock();
        $this->assertSame('New Set Name', $result->getLabel());
        $this->assertSame('editSet.rename()', $result->getOnClick());
        $this->assertNull($result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main::getButtonResetBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Catalog
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
     * @covers Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main::getButtonSaveBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Catalog
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonSaveBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonSaveBlock();
        $this->assertSame('Save Attribute Set', $result->getLabel());
        $this->assertSame('editSet.save();', $result->getOnClick());
        $this->assertSame('save', $result->getClass());
    }
}

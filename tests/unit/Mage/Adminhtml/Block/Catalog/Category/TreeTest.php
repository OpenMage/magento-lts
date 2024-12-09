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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Catalog\Category;

use Mage;
use Mage_Adminhtml_Block_Catalog_Category_Tree;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class TreeTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Catalog_Category_Tree $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_Catalog_Category_Tree();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Category_Tree::getButtonAddRootBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Api
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonAddRootBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonAddRootBlock();
        $this->assertSame('add_root_category_button', $result->getId());
        $this->assertSame('Add Root Category', $result->getLabel());
        $this->assertStringStartsWith("addNew('", $result->getOnClick());
        $this->assertStringEndsWith("', true)", $result->getOnClick());
        $this->assertSame('add', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Category_Tree::getButtonAddSubBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Api
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonAddBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonAddSubBlock();
        $this->assertSame('add_subcategory_button', $result->getId());
        $this->assertSame('Add Subcategory', $result->getLabel());
        $this->assertStringStartsWith("addNew('", $result->getOnClick());
        $this->assertStringEndsWith("', false)", $result->getOnClick());
        $this->assertSame('add', $result->getClass());
        $this->assertIsString($result->getStyle());
    }
}

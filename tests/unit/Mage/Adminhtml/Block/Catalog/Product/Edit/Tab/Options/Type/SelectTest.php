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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Catalog\Product\Edit\Tab\Options\Type;

use Mage;
use Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Select;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Select $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Select();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Select::getButtonAddBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Catalog
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonAddBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonAddBlock();
        $this->assertSame('add_select_row_button_{{option_id}}', $result->getId());
        $this->assertSame('Add New Row', $result->getLabel());
        $this->assertNull($result->getOnClick());
        $this->assertSame('add add-select-row', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Select::getButtonDeleteBlock()
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
        $this->assertSame('delete_select_row_button', $result->getId());
        $this->assertSame('Delete Row', $result->getLabel());
        $this->assertNull($result->getOnClick());
        $this->assertSame('delete delete-select-row icon-btn', $result->getClass());
    }
}

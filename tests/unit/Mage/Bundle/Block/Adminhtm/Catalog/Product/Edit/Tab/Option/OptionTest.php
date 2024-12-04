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

namespace OpenMage\Tests\Unit\Mage\Bundle\Block\Adminhtm\Catalog\Product\Edit\Tab\Option;

use Mage;
use Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    private static ?Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option::getButtonAddSelectionBlock()
     * @group Mage_Bundle
     * @group Mage_Bundle_Block
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonAddSelectionBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonAddSelectionBlock();
        $this->assertSame('bundle_option_{{index}}_add_button', $result->getId());
        $this->assertSame('Add Selection', $result->getLabel());
        $this->assertSame('bSelection.showSearch(event)', $result->getOnClick());
        $this->assertSame('add', $result->getClass());
    }

    /**
     * @covers Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option::getButtonCloseSearchBlock()
     * @group Mage_Bundle
     * @group Mage_Bundle_Block
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonCloseSearchBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonCloseSearchBlock();
        $this->assertSame('bundle_option_{{index}}_close_button', $result->getId());
        $this->assertSame('Close', $result->getLabel());
        $this->assertSame('bSelection.closeSearch(event)', $result->getOnClick());
        $this->assertSame('back no-display', $result->getClass());
    }

    /**
     * @covers Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option::getButtonOptionDeleteBlock()
     * @group Mage_Bundle
     * @group Mage_Bundle_Block
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonOptionDeleteBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonOptionDeleteBlock();
        $this->assertSame('Delete Option', $result->getLabel());
        $this->assertSame('bOption.remove(event)', $result->getOnClick());
        $this->assertSame('delete delete-product-option', $result->getClass());
    }
}

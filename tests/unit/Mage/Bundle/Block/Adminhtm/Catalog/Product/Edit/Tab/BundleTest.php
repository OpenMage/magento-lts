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

namespace OpenMage\Tests\Unit\Mage\Bundle\Block\Adminhtm\Catalog\Product\Edit\Tab;

use Mage;
use Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class BundleTest extends TestCase
{
    private static ?Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle::getButtonAddBlock()
     * @group Mage_Bundle
     * @group Mage_Bundle_Block
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonAddBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonAddBlock();
        $this->assertSame('add_new_option', $result->getId());
        $this->assertSame('Add New Option', $result->getLabel());
        $this->assertSame('bOption.add();', $result->getOnClick());
        $this->assertSame('add', $result->getClass());
    }
}

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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Catalog\Product\Edit\Tab\Super;

use Mage;
use Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config::getButtonCreateEmptyBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Catalog
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     * @doesNotPerformAssertions
     */
    public function testGetButtonCreateEmptyBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());
        $this->markTestIncomplete();

//        $result = self::$subject->getButtonCreateEmptyBlock();
//        $this->assertSame('Create Empty', $result->getLabel());
//        $this->assertSame('superProduct.createEmptyProduct()', $result->getOnClick());
//        $this->assertSame('add', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config::getButtonCreateFromConfigurableBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Catalog
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     * @doesNotPerformAssertions
     */
    public function testGetButtonCreateFromConfigurableBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());
        $this->markTestIncomplete();

//        $result = self::$subject->getButtonCreateFromConfigurableBlock();
//        $this->assertSame('Copy From Configurable', $result->getLabel());
//        $this->assertSame('superProduct.createNewProduct()', $result->getOnClick());
//        $this->assertSame('add', $result->getClass());
    }
}

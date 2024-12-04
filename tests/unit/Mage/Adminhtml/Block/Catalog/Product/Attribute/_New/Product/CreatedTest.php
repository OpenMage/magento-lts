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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Catalog\Product\Attribute\_New\Product;

use Mage;
use Mage_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Created;
use Mage_Catalog_Model_Product;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class CreatedTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Created $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        Mage::register('product', new Mage_Catalog_Model_Product());

        self::$subject = new Mage_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Created();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Created::getButtonCloseBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Api
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonCloseBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonCloseBlock();
        $this->assertSame('Close Window', $result->getLabel());
        $this->assertSame('addAttribute(true)', $result->getOnClick());
        $this->assertNull($result->getClass());
    }
}

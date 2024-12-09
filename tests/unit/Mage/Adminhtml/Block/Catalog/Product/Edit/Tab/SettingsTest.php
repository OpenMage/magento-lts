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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Catalog\Product\Edit\Tab;

use Mage;
use Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Settings;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Settings $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Settings();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Settings::getButtonContinueBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Catalog
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonContinueBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonContinueBlock();
        $this->assertSame('Continue', $result->getLabel());
        $this->assertStringStartsWith('setSettings(', $result->getOnClick());
        $this->assertStringEndsWith("','attribute_set_id','product_type')", $result->getOnClick());
        $this->assertSame('save', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Settings::getContinueUrl()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Catalog
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetContinueUrl(): void
    {
        $this->assertIsString(self::$subject->getContinueUrl());
    }
}

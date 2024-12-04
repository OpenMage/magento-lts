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
use Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings::getButtonBackBlock()
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
     * @covers Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings::getButtonContinueBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Catalog
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonCreateFromConfigurableBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonContinueBlock();
        $this->assertSame('Continue', $result->getLabel());
        $this->assertStringStartsWith('setSuperSettings(', $result->getOnClick());
        $this->assertSame('save', $result->getClass());
    }
}

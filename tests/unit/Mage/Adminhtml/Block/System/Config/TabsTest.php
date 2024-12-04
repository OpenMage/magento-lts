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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\System\Config;

use Mage;
use Mage_Adminhtml_Block_System_Config_Tabs;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class TabsTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_System_Config_Tabs $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_System_Config_Tabs();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_System_Config_Tabs::getButtonDeleteStoreViewBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_System
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonDeleteStoreViewBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonDeleteStoreViewBlock();
        $this->assertSame('Delete Store View', $result->getLabel());
        $this->assertStringStartsWith('location.href=', $result->getOnClick());
        $this->assertSame('delete', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_System_Config_Tabs::getButtonEditStoreViewBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_System
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonEditStoreViewBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonEditStoreViewBlock();
        $this->assertSame('Edit Store View', $result->getLabel());
        $this->assertStringStartsWith('location.href=', $result->getOnClick());
        $this->assertNull($result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_System_Config_Tabs::getButtonNewStoreViewBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_System
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonNewStoreViewBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonNewStoreViewBlock();
        $this->assertSame('New Store View', $result->getLabel());
        $this->assertStringStartsWith('location.href=', $result->getOnClick());
        $this->assertSame('add', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_System_Config_Tabs::getButtonDeleteWebsiteBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_System
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonDeleteWebsiteBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonDeleteWebsiteBlock();
        $this->assertSame('Delete Website', $result->getLabel());
        $this->assertStringStartsWith('location.href=', $result->getOnClick());
        $this->assertSame('delete', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_System_Config_Tabs::getButtonEditWebsiteBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_System
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonEditWebsiteBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonEditWebsiteBlock();
        $this->assertSame('Edit Website', $result->getLabel());
        $this->assertStringStartsWith('location.href=', $result->getOnClick());
        $this->assertNull($result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_System_Config_Tabs::getButtonNewWebsiteBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_System
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonNewWebsiteBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonNewWebsiteBlock();
        $this->assertSame('New Website', $result->getLabel());
        $this->assertStringStartsWith('location.href=', $result->getOnClick());
        $this->assertSame('add', $result->getClass());
    }
}

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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Catalog\Category\Edit;

use Mage;
use Mage_Adminhtml_Block_Catalog_Category_Edit_Form;
use Mage_Catalog_Model_Category;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Catalog_Category_Edit_Form $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();

        if (!Mage::registry('category')) {
            Mage::register('category', new Mage_Catalog_Model_Category());
        }

        if (!Mage::registry('current_category')) {
            Mage::register('current_category', new Mage_Catalog_Model_Category());
        }

        self::$subject = new Mage_Adminhtml_Block_Catalog_Category_Edit_Form();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Category_Edit_Form::getButtonDeleteBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Api
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     * @doesNotPerformAssertions
     */
    public function testGetButtonDeleteBlock(): void
    {
        $this->markTestSkipped();

//        self::$subject->setLayout(new Mage_Core_Model_Layout());
//
//        $result = self::$subject->getButtonDeleteBlock();
//        $this->assertSame('Delete Category', $result->getLabel());
//        $this->assertStringStartsWith("categoryDelete('", $result->getOnClick());
//        $this->assertSame('delete', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Category_Edit_Form::getButtonResetBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Api
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     * @doesNotPerformAssertions
     */
    public function testGetButtonDeleteResetBlock(): void
    {
        $this->markTestSkipped();

        //        self::$subject->setLayout(new Mage_Core_Model_Layout());
//
//        $result = self::$subject->getButtonResetBlock();
//        $this->assertSame('Reset', $result->getLabel());
//        $this->assertStringStartsWith("categoryReset('", $result->getOnClick());
//        $this->assertSame('', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Catalog_Category_Edit_Form::getButtonSaveBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Api
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function getButtonSaveBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonSaveBlock();
        $this->assertSame('Save Category', $result->getLabel());
        $this->assertStringStartsWith("categorySubmit('", $result->getOnClick());
        $this->assertStringEndsWith("', true)", $result->getOnClick());
        $this->assertSame('save', $result->getClass());
    }
}

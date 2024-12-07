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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Newsletter\Template;

use Mage;
use Mage_Adminhtml_Block_Newsletter_Template_Edit;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class EditTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Newsletter_Template_Edit $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_Newsletter_Template_Edit();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Newsletter_Template_Edit::getButtonBackBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Newsletter
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonBackBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonBackBlock();
        $this->assertSame('Back', $result->getLabel());
        $this->assertStringStartsWith('window.location.href=', $result->getOnClick());
        $this->assertSame('back', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Newsletter_Template_Edit::getButtonDeleteBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Newsletter
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonDeleteBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonDeleteBlock();
        $this->assertSame('Delete Template', $result->getLabel());
        $this->assertSame('templateControl.deleteTemplate();', $result->getOnClick());
        $this->assertSame('delete', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Newsletter_Template_Edit::getButtonPreviewBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Newsletter
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonPreviewBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonPreviewBlock();
        $this->assertSame('Preview Template', $result->getLabel());
        $this->assertSame('templateControl.preview();', $result->getOnClick());
        $this->assertSame('task', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Newsletter_Template_Edit::getButtonResetBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Newsletter
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonResetBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonResetBlock();
        $this->assertSame('Reset', $result->getLabel());
        $this->assertSame('window.location.href = window.location.href', $result->getOnClick());
        $this->assertSame('', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Newsletter_Template_Edit::getButtonSaveBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Newsletter
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonSaveBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonSaveBlock();
        $this->assertSame('Save Template', $result->getLabel());
        $this->assertSame('templateControl.save();', $result->getOnClick());
        $this->assertSame('save', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Newsletter_Template_Edit::getButtonSaveAsBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Newsletter
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonSaveAsBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonSaveAsBlock();
        $this->assertSame('Save As', $result->getLabel());
        $this->assertSame('templateControl.saveAs();', $result->getOnClick());
        $this->assertSame('save', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Newsletter_Template_Edit::getButtonToHtmlBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Newsletter
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonToHtmlBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonToHtmlBlock();
        $this->assertSame('convert_button_back', $result->getId());
        $this->assertSame('Return HTML Version', $result->getLabel());
        $this->assertSame('templateControl.unStripTags();', $result->getOnClick());
        $this->assertSame('task', $result->getClass());
        $this->assertSame('display:none', $result->getStyle());
    }

    /**
     * @covers Mage_Adminhtml_Block_Newsletter_Template_Edit::getButtonToPlainBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Newsletter
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonToPlainBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonToPlainBlock();
        $this->assertSame('convert_button', $result->getId());
        $this->assertSame('Convert to Plain Text', $result->getLabel());
        $this->assertSame('templateControl.stripTags();', $result->getOnClick());
        $this->assertSame('task', $result->getClass());
    }
}

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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Newsletter;

use Mage;
use Mage_Adminhtml_Block_Newsletter_Problem;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class ProblemTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Newsletter_Problem $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_Newsletter_Problem();
        self::$subject->setLayout(new Mage_Core_Model_Layout());
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Newsletter_Problem::getButtonDeleteBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Newsletter
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonDeleteBlock(): void
    {
        $result = self::$subject->getButtonDeleteBlock();
        $this->assertSame('Delete Selected Problems', $result->getLabel());
        $this->assertSame('problemController.deleteSelected();', $result->getOnClick());
        $this->assertSame('delete', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Newsletter_Problem::getButtonUnsubscribeBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Newsletter
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonUnsubscribeBlock(): void
    {
        $result = self::$subject->getButtonUnsubscribeBlock();
        $this->assertSame('Unsubscribe Selected', $result->getLabel());
        $this->assertSame('problemController.unsubscribe();', $result->getOnClick());
        $this->assertNull($result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Newsletter_Problem::getUnsubscribeButtonHtml()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Newsletter
     */
    public function testGetUnsubscribeButtonHtml(): void
    {
        $this->assertIsString(self::$subject->getUnsubscribeButtonHtml());
    }

    /**
     * @covers Mage_Adminhtml_Block_Newsletter_Problem::getShowButtons()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Newsletter
     */
    public function testGetShowButtons(): void
    {
        $this->assertIsBool(self::$subject->getShowButtons());
    }
}

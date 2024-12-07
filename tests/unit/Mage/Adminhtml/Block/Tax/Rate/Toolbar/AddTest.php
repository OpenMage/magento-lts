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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Tax\Rate\Toolbar;

use Mage;
use Mage_Adminhtml_Block_Tax_Rate_Toolbar_Add;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class AddTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Tax_Rate_Toolbar_Add $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_Tax_Rate_Toolbar_Add();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Tax_Rate_Toolbar_Add::getButtonAddBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Tax
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonAddBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonAddBlock();
        $this->assertSame('Add New Tax Rate', $result->getLabel());
        $this->assertStringStartsWith('window.location.href=', $result->getOnClick());
        $this->assertSame('add', $result->getClass());
    }
}

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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Report;

use Mage;
use Mage_Adminhtml_Block_Report_Grid;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Report_Grid $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_Report_Grid();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Report_Grid::getButtonExportlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Report
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonExportlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonExportlock();
        $this->assertSame('Export', $result->getLabel());
        $this->assertStringStartsWith('id_', $result->getOnClick());
        $this->assertStringEndsWith('.doExport()', $result->getOnClick());
        $this->assertSame('task', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Report_Grid::getButtonResetFilterBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Report
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonResetFilterBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonResetFilterBlock();
        $this->assertSame('Reset Filter', $result->getLabel());
        $this->assertStringStartsWith('id_', $result->getOnClick());
        $this->assertStringEndsWith('.resetFilter()', $result->getOnClick());
        $this->assertNull($result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Report_Grid::getButtonSearchBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Report
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonSearchBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonSearchBlock();
        $this->assertSame('Search', $result->getLabel());
        $this->assertStringStartsWith('id_', $result->getOnClick());
        $this->assertStringEndsWith('.doFilter()', $result->getOnClick());
        $this->assertSame('task', $result->getClass());
    }
}

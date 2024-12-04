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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Widget;

use Generator;
use Mage;
use Mage_Adminhtml_Block_Widget_Grid;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Widget_Grid $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_Widget_Grid();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Widget_Grid::getButtonExportlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Widget
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonExportlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonExportlock();
        $this->assertSame('Export', $result->getLabel());
        $this->assertStringEndsWith('.doExport()', $result->getOnClick());
        $this->assertSame('task', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Widget_Grid::getButtonResetFilterBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Widget
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonResetFilterBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonResetFilterBlock();
        $this->assertSame('Reset Filter', $result->getLabel());
        $this->assertStringEndsWith('.resetFilter()', $result->getOnClick());
        $this->assertNull($result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Widget_Grid::getButtonSearchBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Widget
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonSearchBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonSearchBlock();
        $this->assertSame('Search', $result->getLabel());
        $this->assertStringEndsWith('.doFilter()', $result->getOnClick());
        $this->assertSame('task', $result->getClass());
    }

    /**
     * @dataProvider provideAddColumnDefaultData
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testAddColumnDefaultData(array $expectedResult, array $column): void
    {
        $this->assertSame($expectedResult, self::$subject->addColumnDefaultData($column));
    }

    public function provideAddColumnDefaultData(): Generator
    {
        yield 'empty' => [
            [],
            [],
        ];
        yield 'index entity_id' => [
            [
                'index' => 'entity_id',
                'align' => 'right',
                'type' => 'number',
                'width' => '1',
            ],
            [
                'index' => 'entity_id',
            ],
        ];
        yield 'index entity_id w/ override' => [
            [
                'index' => 'entity_id',
                'align' => 'center',
                'type' => 'text',
            ],
            [
                'index' => 'entity_id',
                'align' => 'center',
                'type' => 'text',
            ],
        ];
        yield 'type action' => [
            [
                'type' => 'action',
                'filter' => false,
                'sortable' => false,
                'width' => '40',
                'header' => 'Action',
            ],
            [
                'type' => 'action',
            ],
        ];
        yield 'type action w/ override' => [
            [
                'type' => 'action',
                'header' => 'test',
                'filter' => false,
                'sortable' => false,
                'width' => '40',
            ],
            [
                'type' => 'action',
                'header' => 'test',
            ],
        ];
        yield 'type currency' => [
            [
                'type' => 'currency',
                'align' => 'right',
                'index' => 'price',
                'header' => 'Price',
            ],
            [
                'type' => 'currency',
            ],
        ];
        yield 'type date' => [
            [
                'type' => 'date',
                'width' => '140',
            ],
            [
                'type' => 'date',
            ],
        ];
        yield 'type datetime' => [
            [
                'type' => 'datetime',
                'width' => '170',
            ],
            [
                'type' => 'datetime',
            ],
        ];
        yield 'type number' => [
            [
                'type' => 'number',
                'align' => 'right',
                'width' => '1',
            ],
            [
                'type' => 'number',
            ],
        ];
        yield 'type price' => [
            [
                'type' => 'price',
                'align' => 'right',
                'index' => 'price',
                'header' => 'Price',
            ],
            [
                'type' => 'price',
            ],
        ];
        yield 'type store' => [
            [
                'type' => 'store',
                'index' => 'store_id',
                'store_view' => true,
                'width' => '160',
                'header' => 'Store View',
            ],
            [
                'type' => 'store',
            ],
        ];
    }
}

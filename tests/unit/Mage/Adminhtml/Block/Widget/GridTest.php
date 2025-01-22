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
use Mage_Adminhtml_Block_Widget_Grid as Subject;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }

    /**
     * @dataProvider provideAddColumnDefaultData
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testAddColumnDefaultData(array $expectedResult, array $column): void
    {
        $this->assertSame($expectedResult, $this->subject->addColumnDefaultData($column));
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
        yield 'index array - ref #4475' => [
            [
                'index' => ['firstname', 'lastname'],
                'type' => 'concat',
                'separator' => ' ',
            ],
            [
                'index'     => ['firstname', 'lastname'],
                'type'      => 'concat',
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

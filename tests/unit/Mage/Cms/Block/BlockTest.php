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

namespace OpenMage\Tests\Unit\Mage\Cms\Block;

use Mage_Cms_Block_Block;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    /**
     * @dataProvider provideGetCacheKeyInfoData
     * @group Mage_Cms
     * @group Mage_Cms_Block
     */
    public function testGetCacheKeyInfo(string $blockId): void
    {
        $mock = $this->getMockBuilder(Mage_Cms_Block_Block::class)
            ->setMethods(['getBlockId'])
            ->getMock();

        $mock->expects($this->any())->method('getBlockId')->willReturn($blockId);
        $this->assertIsArray($mock->getCacheKeyInfo());
    }

    /**
     * @return array[]
     */
    public function provideGetCacheKeyInfoData(): array
    {
        return [
            'valid block ID' => [
                '2'
            ],
            'invalid block ID' => [
                '0'
            ]
        ];
    }
}

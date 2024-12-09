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

namespace OpenMage\Tests\Unit\Mage\Cms\Block\Widget;

use Generator;
use Mage;
use Mage_Cms_Block_Widget_Block;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    public Mage_Cms_Block_Widget_Block $subject;

    public function setUp(): void
    {
        Mage::app();
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->subject = new Mage_Cms_Block_Widget_Block();
    }

    /**
     * @dataProvider provideGetCacheKeyInfoData
     * @group Mage_Cms
     * @group Mage_Cms_Block
     */
    public function testGetCacheKeyInfo(string $blockId): void
    {
        $mock = $this->getMockBuilder(Mage_Cms_Block_Widget_Block::class)
            ->setMethods(['getBlockId'])
            ->getMock();

        $mock->method('getBlockId')->willReturn($blockId);
        $this->assertIsArray($mock->getCacheKeyInfo());
    }

    public function provideGetCacheKeyInfoData(): Generator
    {
        yield 'valid block ID' => [
            '2'
        ];
        yield 'invalid block ID' => [
            '0'
        ];
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Block
     */
    public function testIsRequestFromAdminArea(): void
    {
        $this->assertIsBool($this->subject->isRequestFromAdminArea());
    }
}

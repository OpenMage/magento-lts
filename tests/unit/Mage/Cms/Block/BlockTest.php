<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Block;

use Mage_Cms_Block_Block as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\NumericStringTrait;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    use NumericStringTrait;

    /**
     * @dataProvider provideNumericString
     * @group Mage_Cms
     * @group Mage_Cms_Block
     */
    public function testGetCacheKeyInfo(string $blockId): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getBlockId'])
            ->getMock();

        $mock->method('getBlockId')->willReturn($blockId);
        $this->assertIsArray($mock->getCacheKeyInfo());
    }
}

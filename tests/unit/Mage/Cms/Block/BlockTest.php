<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @dataProvider provideNumericString
 * @group Mage_Cms
 * @group Mage_Cms_Block
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Block;

use Mage_Cms_Block_Block as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\NumericStringTrait;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    use NumericStringTrait;

    
    public function testGetCacheKeyInfo(string $blockId): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getBlockId'])
            ->getMock();

        $mock->method('getBlockId')->willReturn($blockId);
        $this->assertIsArray($mock->getCacheKeyInfo());
    }
}

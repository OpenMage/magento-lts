<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @dataProvider provideNumericString
 * @group Mage_Cms
 * @group Mage_Cms_Block
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Block\Widget;

use Mage;
use Mage_Cms_Block_Widget_Block as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\NumericStringTrait;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    use NumericStringTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }


    public function testGetCacheKeyInfo(string $blockId): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getBlockId'])
            ->getMock();

        $mock->method('getBlockId')->willReturn($blockId);
        $this->assertIsArray($mock->getCacheKeyInfo());
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

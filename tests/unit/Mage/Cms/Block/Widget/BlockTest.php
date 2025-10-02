<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Block\Widget;

use Mage_Cms_Block_Widget_Block as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\NumericStringTrait;

final class BlockTest extends OpenMageTest
{
    use NumericStringTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @dataProvider provideNumericString
     * @group Block
     */
    public function testGetCacheKeyInfo(string $blockId): void
    {
        $methods = [
            'getBlockId' => $blockId,
        ];
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertIsArray($mock->getCacheKeyInfo());
    }

    /**
     * @group Block
     */
    public function testIsRequestFromAdminArea(): void
    {
        self::assertIsBool(self::$subject->isRequestFromAdminArea());
    }
}

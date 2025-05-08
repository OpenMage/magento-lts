<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Block;

use Mage_Cms_Block_Block as Subject;
use Mage_Core_Model_Store_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\NumericStringTrait;

class BlockTest extends OpenMageTest
{
    use NumericStringTrait;

    /**
     * @dataProvider provideNumericString
     * @group Block
     * @throws Mage_Core_Model_Store_Exception
     */
    public function testGetCacheKeyInfo(string $blockId): void
    {
        $methods = [
            'getBlockId' => $blockId,
        ];
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        static::assertInstanceOf(Subject::class, $mock);
        static::assertIsArray($mock->getCacheKeyInfo());
    }
}

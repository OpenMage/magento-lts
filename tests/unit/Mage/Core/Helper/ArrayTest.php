<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Array as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper\ArrayTrait;

final class ArrayTest extends OpenMageTest
{
    use ArrayTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/array');
    }

    /**
     * @covers Mage_Core_Helper_Data::getMerchantCountryCode()
     * @dataProvider provideMergeRecursiveWithoutOverwriteNumKeysData
     * @group Helper
     */
    public function testMergeRecursiveWithoutOverwriteNumKeys(array $expectedResult, array $baseArray, array $mergeArray): void
    {
        self::assertSame($expectedResult, self::$subject->mergeRecursiveWithoutOverwriteNumKeys($baseArray, $mergeArray));
    }
}

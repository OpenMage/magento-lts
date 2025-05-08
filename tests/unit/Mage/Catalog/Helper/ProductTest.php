<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Helper;

use Mage;
use Mage_Catalog_Helper_Product as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Helper\ProductTrait;

class ProductTest extends OpenMageTest
{
    use ProductTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('catalog/product');
    }

    /**
     * @group Helper
     */
    public function testCanUseCanonicalTag(): void
    {
        static::assertIsBool(self::$subject->canUseCanonicalTag());
    }

    /**
     * @dataProvider provideGetAttributeInputTypes
     * @group Helper
     */
    public function testGetAttributeInputTypes(int $expectedResult, ?string $inputType = null): void
    {
        static::assertCount($expectedResult, self::$subject->getAttributeInputTypes($inputType));
    }

    /**
     * @dataProvider provideGetAttributeBackendModelByInputType
     * @group Helper
     */
    public function testGetAttributeBackendModelByInputType(string $expectedResult, string $inputType): void
    {
        static::assertSame($expectedResult, self::$subject->getAttributeBackendModelByInputType($inputType));
    }

    /**
     * @dataProvider provideGetAttributeSourceModelByInputType
     * @group Helper
     */
    public function testGetAttributeSourceModelByInputType(string $expectedResult, string $inputType): void
    {
        static::assertSame($expectedResult, self::$subject->getAttributeSourceModelByInputType($inputType));
    }
}

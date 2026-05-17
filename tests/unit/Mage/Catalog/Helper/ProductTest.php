<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Helper;

use PHPUnit\Framework\Attributes\DataProvider;
use Override;
use Mage;
use Mage_Catalog_Helper_Product as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Helper\ProductTrait;

final class ProductTest extends OpenMageTest
{
    use ProductTrait;

    private static Subject $subject;

    #[Override]
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
        self::assertIsBool(self::$subject->canUseCanonicalTag());
    }

    /**
     * @group Helper
     */
    #[DataProvider('provideGetAttributeInputTypes')]
    public function testGetAttributeInputTypes(int $expectedResult, ?string $inputType = null): void
    {
        self::assertCount($expectedResult, self::$subject->getAttributeInputTypes($inputType));
    }

    /**
     * @group Helper
     */
    #[DataProvider('provideGetAttributeBackendModelByInputType')]
    public function testGetAttributeBackendModelByInputType(string $expectedResult, string $inputType): void
    {
        self::assertSame($expectedResult, self::$subject->getAttributeBackendModelByInputType($inputType));
    }

    /**
     * @group Helper
     */
    #[DataProvider('provideGetAttributeSourceModelByInputType')]
    public function testGetAttributeSourceModelByInputType(string $expectedResult, string $inputType): void
    {
        self::assertSame($expectedResult, self::$subject->getAttributeSourceModelByInputType($inputType));
    }
}

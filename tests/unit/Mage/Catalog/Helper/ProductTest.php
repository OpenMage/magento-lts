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
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testCanUseCanonicalTag(): void
    {
        static::assertIsBool(self::$subject->canUseCanonicalTag());
    }

    /**
     * @dataProvider provideGetAttributeInputTypes
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetAttributeInputTypes(int $expectedResult, ?string $inputType = null): void
    {
        static::assertCount($expectedResult, self::$subject->getAttributeInputTypes($inputType));
    }

    /**
     * @dataProvider provideGetAttributeBackendModelByInputType
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetAttributeBackendModelByInputType(string $expectedResult, string $inputType): void
    {
        static::assertSame($expectedResult, self::$subject->getAttributeBackendModelByInputType($inputType));
    }

    /**
     * @dataProvider provideGetAttributeSourceModelByInputType
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetAttributeSourceModelByInputType(string $expectedResult, string $inputType): void
    {
        static::assertSame($expectedResult, self::$subject->getAttributeSourceModelByInputType($inputType));
    }
}

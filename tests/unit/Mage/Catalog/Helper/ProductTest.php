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

use Generator;
use Mage;
use Mage_Catalog_Helper_Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public Mage_Catalog_Helper_Product $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('catalog/product');
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testCanUseCanonicalTag(): void
    {
        $this->assertIsBool($this->subject->canUseCanonicalTag());
    }

    /**
     * @dataProvider provideGetAttributeInputTypes
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetAttributeInputTypes(int $expectedResult, ?string $inputType = null): void
    {
        $this->assertCount($expectedResult, $this->subject->getAttributeInputTypes($inputType));
    }

    public function provideGetAttributeInputTypes(): Generator
    {
        yield 'null' => [
            2,
            null,
        ];
        yield 'invalid' => [
            0,
            'invalid',
        ];
        yield 'multiselect' => [
            1,
            'multiselect',
        ];
        yield 'boolean' => [
            1,
            'boolean',
        ];
    }

    /**
     * @dataProvider provideGetAttributeBackendModelByInputType
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetAttributeBackendModelByInputType($expectedResult, string $inputType): void
    {
        $this->assertSame($expectedResult, $this->subject->getAttributeBackendModelByInputType($inputType));
    }

    public function provideGetAttributeBackendModelByInputType(): Generator
    {
        yield 'multiselect' => [
            'eav/entity_attribute_backend_array',
            'multiselect',
        ];
    }

    /**
     * @dataProvider provideGetAttributeSourceModelByInputType
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetAttributeSourceModelByInputType($expectedResult, string $inputType): void
    {
        $this->assertSame($expectedResult, $this->subject->getAttributeSourceModelByInputType($inputType));
    }

    public function provideGetAttributeSourceModelByInputType(): Generator
    {
        yield 'boolean' => [
            'eav/entity_attribute_source_boolean',
            'boolean',
        ];
    }
}

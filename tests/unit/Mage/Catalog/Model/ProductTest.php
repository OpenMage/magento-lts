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

namespace OpenMage\Tests\Unit\Mage\Catalog\Model;

use Generator;
use Mage;
use Mage_Catalog_Model_Product;
use Mage_Catalog_Model_Product_Link;
use Mage_Catalog_Model_Product_Type_Abstract;
use Mage_Catalog_Model_Product_Url;
use Mage_Catalog_Model_Resource_Product_Collection;
use Mage_Catalog_Model_Url;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public const TEST_STRING = 'a & B, x%, ä, ö, ü';

    public Mage_Catalog_Model_Product $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('catalog/product');
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetStoreId(): void
    {
        $this->assertIsInt($this->subject->getStoreId());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetResourceCollection(): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Resource_Product_Collection::class, $this->subject->getResourceCollection());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetUrlModel(): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Url::class, $this->subject->getUrlModel());
        $this->assertInstanceOf(Mage_Catalog_Model_Product_Url::class, $this->subject->getUrlModel());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testValidate(): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Product::class, $this->subject->validate());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
//    public function testGetName(): void
//    {
//        $this->assertNull($this->subject->getName());
//        $this->assertIsString($this->subject->getName());
//    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
//    public function testGetPrice(): void
//    {
//        $this->assertIsFloat($this->subject->getPrice());
//    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testSetPriceCalculation(): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Product::class, $this->subject->setPriceCalculation());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
//    public function testGetTypeId(): void
//    {
//        $this->assertIsString($this->subject->getTypeId());
//    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetStatus(): void
    {
        $this->assertSame(1, $this->subject->getStatus());
    }

    /**
     * @dataProvider provideTypeInstanceData
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetTypeInstance(bool $singleton): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Product_Type_Abstract::class, $this->subject->getTypeInstance($singleton));
    }

    public function provideTypeInstanceData(): Generator
    {
        yield 'singleton false' => [
            true,
        ];
        yield 'singleton true' => [
            true,
        ];
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetLinkInstance(): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Product_Link::class, $this->subject->getLinkInstance());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetDefaultAttributeSetId(): void
    {
        $this->assertIsInt($this->subject->getDefaultAttributeSetId());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testAfterCommitCallback(): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Product::class, $this->subject->afterCommitCallback());
    }

    /**
     * @dataProvider provideFormatUrlKey
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testFormatUrlKey($expectedResult, ?string $locale): void
    {
        $this->subject->setLocale($locale);
        $this->assertSame($expectedResult, $this->subject->formatUrlKey(self::TEST_STRING));
    }

    public function provideFormatUrlKey(): Generator
    {
        yield 'null locale' => [
            'a-b-x-a-o-u',
            null,
        ];
        yield 'de_DE' => [
            'a-und-b-x-prozent-ae-oe-ue',
            'de_DE',
        ];
    }
}

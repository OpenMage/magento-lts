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
 * @copyright  Copyright (c) 2024-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model;

use Mage;
use Mage_Catalog_Model_Product as Subject;
use Mage_Catalog_Model_Product_Link;
use Mage_Catalog_Model_Product_Type_Abstract;
use Mage_Catalog_Model_Product_Url;
use Mage_Catalog_Model_Resource_Product_Collection;
use Mage_Catalog_Model_Url;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\BoolTrait;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\CatalogTrait;
use OpenMage\Tests\Unit\OpenMageTest;

class ProductTest extends OpenMageTest
{
    use BoolTrait;
    use CatalogTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/product');
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetStoreId(): void
    {
        static::assertIsInt(self::$subject->getStoreId());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetResourceCollection(): void
    {
        static::assertInstanceOf(Mage_Catalog_Model_Resource_Product_Collection::class, self::$subject->getResourceCollection());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetUrlModel(): void
    {
        static::assertInstanceOf(Mage_Catalog_Model_Url::class, self::$subject->getUrlModel());
        static::assertInstanceOf(Mage_Catalog_Model_Product_Url::class, self::$subject->getUrlModel());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testValidate(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->validate());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    //    public function testGetName(): void
    //    {
    //        $this->assertNull(self::$subject->getName());
    //        $this->assertIsString(self::$subject->getName());
    //    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    //    public function testGetPrice(): void
    //    {
    //        $this->assertIsFloat(self::$subject->getPrice());
    //    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testSetPriceCalculation(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->setPriceCalculation());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    //    public function testGetTypeId(): void
    //    {
    //        $this->assertIsString(self::$subject->getTypeId());
    //    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetStatus(): void
    {
        static::assertSame(1, self::$subject->getStatus());
    }

    /**
     * @dataProvider provideBool
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetTypeInstance(bool $singleton): void
    {
        static::assertInstanceOf(Mage_Catalog_Model_Product_Type_Abstract::class, self::$subject->getTypeInstance($singleton));
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetLinkInstance(): void
    {
        static::assertInstanceOf(Mage_Catalog_Model_Product_Link::class, self::$subject->getLinkInstance());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetDefaultAttributeSetId(): void
    {
        static::assertIsInt(self::$subject->getDefaultAttributeSetId());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testAfterCommitCallback(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->afterCommitCallback());
    }

    /**
     * @dataProvider provideFormatUrlKey
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testFormatUrlKey(string $expectedResult, string $locale): void
    {
        self::$subject->setLocale($locale);
        static::assertSame($expectedResult, self::$subject->formatUrlKey($this->getTestString()));
    }
}

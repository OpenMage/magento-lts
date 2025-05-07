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
use Mage_Catalog_Model_Category as Subject;
use Mage_Catalog_Model_Category_Url;
use Mage_Catalog_Model_Resource_Product_Collection;
use Mage_Catalog_Model_Url;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\CatalogTrait;
use OpenMage\Tests\Unit\OpenMageTest;

class CategoryTest extends OpenMageTest
{
    use CatalogTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/category');
    }

    /**
     * @group Model
     */
    public function testGetDefaultAttributeSetId(): void
    {
        static::assertIsInt(self::$subject->getDefaultAttributeSetId());
    }

    /**
     * @group Model
     */
    public function testGetProductCollection(): void
    {
        static::assertInstanceOf(Mage_Catalog_Model_Resource_Product_Collection::class, self::$subject->getProductCollection());
    }

    /**
     * @group Model
     */
    public function testGetAvailableSortByOptions(): void
    {
        static::assertIsArray(self::$subject->getAvailableSortByOptions());
    }

    /**
     * @group Model
     */
    public function testGetDefaultSortBy(): void
    {
        static::assertSame('position', self::$subject->getDefaultSortBy());
    }

    /**
     * @group Model
     */
    public function testValidate(): void
    {
        static::assertIsArray(self::$subject->validate());
    }

    /**
     * @group Model
     */
    public function testAfterCommitCallback(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->afterCommitCallback());
    }

    /**
     * @group Model
     */
    public function testGetUrlModel(): void
    {
        static::assertInstanceOf(Mage_Catalog_Model_Url::class, self::$subject->getUrlModel());
        static::assertInstanceOf(Mage_Catalog_Model_Category_Url::class, self::$subject->getUrlModel());
    }

    /**
     * @dataProvider provideFormatUrlKey
     * @group Model
     * @runInSeparateProcess
     */
    //    public function testGetCategoryIdUrl($expectedResult, ?string $locale): void
    //    {
    //        self::$subject->setName(self::TEST_STRING);
    //        self::$subject->setLocale($locale);
    //        $this->assertSame($expectedResult, self::$subject->getCategoryIdUrl());
    //    }

    /**
     * @dataProvider provideFormatUrlKey
     * @group Model
     */
    public function testFormatUrlKey(string $expectedResult, string $locale): void
    {
        self::$subject->setLocale($locale);
        static::assertSame($expectedResult, self::$subject->formatUrlKey($this->getTestString()));
    }
}

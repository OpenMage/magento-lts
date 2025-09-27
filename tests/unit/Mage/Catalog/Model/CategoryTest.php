<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
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

final class CategoryTest extends OpenMageTest
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
        self::assertIsInt(self::$subject->getDefaultAttributeSetId());
    }

    /**
     * @group Model
     */
    public function testGetProductCollection(): void
    {
        self::assertInstanceOf(Mage_Catalog_Model_Resource_Product_Collection::class, self::$subject->getProductCollection());
    }

    /**
     * @group Model
     */
    public function testGetAvailableSortByOptions(): void
    {
        self::assertIsArray(self::$subject->getAvailableSortByOptions());
    }

    /**
     * @group Model
     */
    public function testGetDefaultSortBy(): void
    {
        self::assertSame('position', self::$subject->getDefaultSortBy());
    }

    /**
     * @group Model
     */
    public function testValidate(): void
    {
        self::assertIsArray(self::$subject->validate());
    }

    /**
     * @group Model
     */
    public function testAfterCommitCallback(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->afterCommitCallback());
    }

    /**
     * @group Model
     */
    public function testGetUrlModel(): void
    {
        self::assertInstanceOf(Mage_Catalog_Model_Url::class, self::$subject->getUrlModel());
        self::assertInstanceOf(Mage_Catalog_Model_Category_Url::class, self::$subject->getUrlModel());
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
        self::assertSame($expectedResult, self::$subject->formatUrlKey($this->getTestString()));
    }
}

<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Catalog
 * @group Mage_Catalog_Model
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model;

use Mage;
use Mage_Catalog_Model_Category as Subject;
use Mage_Catalog_Model_Category_Url;
use Mage_Catalog_Model_Resource_Product_Collection;
use Mage_Catalog_Model_Url;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\CatalogTrait;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    use CatalogTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('catalog/category');
    }


    public function testGetDefaultAttributeSetId(): void
    {
        $this->assertIsInt($this->subject->getDefaultAttributeSetId());
    }


    public function testGetProductCollection(): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Resource_Product_Collection::class, $this->subject->getProductCollection());
    }


    public function testGetAvailableSortByOptions(): void
    {
        $this->assertIsArray($this->subject->getAvailableSortByOptions());
    }


    public function testGetDefaultSortBy(): void
    {
        $this->assertSame('position', $this->subject->getDefaultSortBy());
    }


    public function testValidate(): void
    {
        $this->assertIsArray($this->subject->validate());
    }


    public function testAfterCommitCallback(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->afterCommitCallback());
    }


    public function testGetUrlModel(): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Url::class, $this->subject->getUrlModel());
        $this->assertInstanceOf(Mage_Catalog_Model_Category_Url::class, $this->subject->getUrlModel());
    }

    /**
     * @dataProvider provideFormatUrlKey
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     * @runInSeparateProcess
     */
    //    public function testGetCategoryIdUrl($expectedResult, ?string $locale): void
    //    {
    //        $this->subject->setName(self::TEST_STRING);
    //        $this->subject->setLocale($locale);
    //        $this->assertSame($expectedResult, $this->subject->getCategoryIdUrl());
    //    }

    /**
     * @dataProvider provideFormatUrlKey
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testFormatUrlKey($expectedResult, ?string $locale): void
    {
        $this->subject->setLocale($locale);
        $this->assertSame($expectedResult, $this->subject->formatUrlKey($this->getTestString()));
    }
}

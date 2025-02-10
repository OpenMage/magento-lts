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
use Mage_Catalog_Model_Product as Subject;
use Mage_Catalog_Model_Product_Link;
use Mage_Catalog_Model_Product_Type_Abstract;
use Mage_Catalog_Model_Product_Url;
use Mage_Catalog_Model_Resource_Product_Collection;
use Mage_Catalog_Model_Url;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\BoolTrait;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\CatalogTrait;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    use BoolTrait;
    use CatalogTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('catalog/product');
    }


    public function testGetStoreId(): void
    {
        $this->assertIsInt($this->subject->getStoreId());
    }


    public function testGetResourceCollection(): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Resource_Product_Collection::class, $this->subject->getResourceCollection());
    }


    public function testGetUrlModel(): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Url::class, $this->subject->getUrlModel());
        $this->assertInstanceOf(Mage_Catalog_Model_Product_Url::class, $this->subject->getUrlModel());
    }


    public function testValidate(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->validate());
    }


    //    public function testGetName(): void
    //    {
    //        $this->assertNull($this->subject->getName());
    //        $this->assertIsString($this->subject->getName());
    //    }


    //    public function testGetPrice(): void
    //    {
    //        $this->assertIsFloat($this->subject->getPrice());
    //    }


    public function testSetPriceCalculation(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->setPriceCalculation());
    }


    //    public function testGetTypeId(): void
    //    {
    //        $this->assertIsString($this->subject->getTypeId());
    //    }


    public function testGetStatus(): void
    {
        $this->assertSame(1, $this->subject->getStatus());
    }

    /**
     * @dataProvider provideBool
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetTypeInstance(bool $singleton): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Product_Type_Abstract::class, $this->subject->getTypeInstance($singleton));
    }


    public function testGetLinkInstance(): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Product_Link::class, $this->subject->getLinkInstance());
    }


    public function testGetDefaultAttributeSetId(): void
    {
        $this->assertIsInt($this->subject->getDefaultAttributeSetId());
    }


    public function testAfterCommitCallback(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->afterCommitCallback());
    }

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

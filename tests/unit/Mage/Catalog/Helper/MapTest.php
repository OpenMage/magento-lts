<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Catalog
 * @group Mage_Catalog_Helper
 * @group runInSeparateProcess
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Helper;

use Mage;
use Mage_Catalog_Helper_Map as Subject;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('catalog/map');
    }

    
    public function testGetCategoryUrl(): void
    {
        $this->assertStringEndsWith('/catalog/seo_sitemap/category/', $this->subject->getCategoryUrl());
    }

    
    public function testGetProductUrl(): void
    {
        $this->assertStringEndsWith('/catalog/seo_sitemap/product/', $this->subject->getProductUrl());
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testGetIsUseCategoryTreeMode(): void
    {
        $this->assertIsBool($this->subject->getIsUseCategoryTreeMode());
    }
}

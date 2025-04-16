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

use Generator;
use Mage;
use Mage_Catalog_Model_Url as Subject;
use Mage_Core_Exception;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\IntOrNullTrait;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\CatalogTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Varien_Object;

class UrlTest extends TestCase
{
    use CatalogTrait;
    use IntOrNullTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('catalog/url');
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetStoreRootCategory(): void
    {
        $this->assertInstanceOf(Varien_Object::class, $this->subject->getStoreRootCategory(1));
    }

    /**
     * @dataProvider provideIntOrNull
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testRefreshRewrites(?int $storeId): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->refreshRewrites($storeId));
    }

    /**
     * @dataProvider provideGeneratePathData
     *
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGeneratePath(
        string $expectedResult,
        string $type,
        ?Varien_Object $product,
        ?Varien_Object $category,
        ?string $parentPath = null
    ): void {
        try {
            $this->assertSame($expectedResult, $this->subject->generatePath($type, $product, $category, $parentPath));
        } catch (Mage_Core_Exception $e) {
            $this->assertSame($expectedResult, $e->getMessage());
        }
    }

    public function provideGeneratePathData(): Generator
    {
        $category = new Varien_Object([
            'id'        => '999',
            'store_id'  => '1',
            'url_key'   => '',
            'name'      => 'category',

        ]);
        $product = new Varien_Object([
            'id'        => '999',
            'name'      => 'product',
        ]);

        yield 'test exception' => [
            'Please specify either a category or a product, or both.',
            'request',
            null,
            null,
        ];
        yield 'request' => [
            'product.html',
            'request',
            $product,
            $category,
        ];
        //        yield 'request w/o product' => [
        //            '-.html',
        //            'request',
        //            null,
        //            $category,
        //        ];
        yield 'target category' => [
            'catalog/category/view/id/999',
            'target',
            null,
            $category,
        ];
        yield 'target product' => [
            'catalog/product/view/id/999',
            'target',
            $product,
            $category,
        ];
    }
    /**
     * @dataProvider provideFormatUrlKey
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testFormatUrlKey($expectedResult, string $locale): void
    {
        $this->subject->setLocale($locale);
        $this->assertSame($expectedResult, $this->subject->formatUrlKey($this->getTestString()));
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     * @doesNotPerformAssertions
     */
    //    public function testGetSlugger(): void
    //    {
    //        $this->subject->getSlugger();
    //    }

    /**
     * @dataProvider provideGetSluggerConfig
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetSluggerConfig($expectedResult, string $locale): void
    {
        $result = $this->subject->getSluggerConfig($locale);

        $this->assertArrayHasKey($locale, $result);

        $this->assertArrayHasKey('%', $result[$locale]);
        $this->assertArrayHasKey('&', $result[$locale]);

        $this->assertSame($expectedResult[$locale]['%'], $result[$locale]['%']);
        $this->assertSame($expectedResult[$locale]['&'], $result[$locale]['&']);

        $this->assertSame('at', $result[$locale]['@']);
    }

    public function provideGetSluggerConfig(): Generator
    {
        yield 'de_DE' => [
            ['de_DE' => [
                '%' => 'prozent',
                '&' => 'und',
            ]],
            'de_DE',
        ];
        yield 'en_US' => [
            ['en_US' => [
                '%' => 'percent',
                '&' => 'and',
            ]],
            'en_US',
        ];
        yield 'fr_FR' => [
            ['fr_FR' => [
                '%' => 'pour cent',
                '&' => 'et',
            ]],
            'fr_FR',
        ];
    }
}

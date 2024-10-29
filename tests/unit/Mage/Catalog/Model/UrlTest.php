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
use Mage_Catalog_Model_Url;
use Mage_Core_Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Varien_Object;

class UrlTest extends TestCase
{
    public const TEST_STRING = '--a & B, x% @ ä ö ü ™--';

    public Mage_Catalog_Model_Url $subject;

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
     * @dataProvider provideRefreshRewrites
     *
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testRefreshRewrites(?int $storeId): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Url::class, $this->subject->refreshRewrites($storeId));
    }

    public function provideRefreshRewrites(): Generator
    {
        yield 'w/o storeId' => [
            null,
        ];
        yield 'w/ valid storeId' => [
            1,
        ];
        yield 'w/ invalid storeId' => [
            999,
        ];
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
        $this->assertSame($expectedResult, $this->subject->formatUrlKey(self::TEST_STRING));
    }

    public function provideFormatUrlKey(): Generator
    {
        yield 'de_DE' => [
            'a-und-b-x-prozent-at-ae-oe-ue-tm',
            'de_DE',
        ];
        yield 'en_US' => [
            'a-and-b-x-percent-at-a-o-u-tm',
            'en_US',
        ];
        yield 'es_ES' => [
            'a-et-b-x-por-ciento-at-a-o-u-tm',
            'es_ES',
        ];
        yield 'fr_FR' => [
            'a-et-b-x-pour-cent-at-a-o-u-tm',
            'fr_FR',
        ];
        yield 'it_IT' => [
            'a-e-b-x-per-cento-at-a-o-u-tm',
            'it_IT',
        ];
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetSlugger(): void
    {
        $this->assertInstanceOf(AsciiSlugger::class, $this->subject->getSlugger());
    }

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

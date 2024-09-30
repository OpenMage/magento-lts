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

use Mage;
use Mage_Catalog_Model_Url;
use Mage_Core_Exception;
use PHPUnit\Framework\TestCase;
use Varien_Object;

class UrlTest extends TestCase
{
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
     * @dataProvider provideRefreshRewritesData
     *
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testRefreshRewrites(?int $storeId): void
    {
        $this->assertInstanceOf(Mage_Catalog_Model_Url::class, $this->subject->refreshRewrites($storeId));
    }

    public function provideRefreshRewritesData(): array
    {
        return [
            'w/o storeId' => [
                null,
            ],
            'w/ storeId' => [
                2,
            ],
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

    public function provideGeneratePathData(): array
    {
        $category = new Varien_Object([
            'id'        => '999',
            'store_id'  => '1',
            'url_key'   => '',

        ]);
        $product = new Varien_Object([
            'id' => '999'
        ]);

        return [
            'exception' => [
                'Please specify either a category or a product, or both.',
                'request',
                null,
                null,
            ],
            'request' => [
                '-.html',
                'request',
                $product,
                $category,
            ],
//            'request w/o product' => [
//                '-.html',
//                'request',
//                null,
//                $category,
//            ],
            'target category' => [
                'catalog/category/view/id/999',
                'target',
                null,
                $category,
            ],
            'target product' => [
                'catalog/product/view/id/999',
               'target',
               $product,
               $category,
            ],
        ];
    }
}

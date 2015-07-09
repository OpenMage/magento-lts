<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\TestCase\Product;

use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\TestCase\Injectable;

/**
 * Base class for add related, cross sell and up sell products entity test.
 */
abstract class AbstractAddAppurtenantProductsEntityTest extends Injectable
{
    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Catalog product index page on backend.
     *
     * @var CatalogProduct
     */
    protected $catalogProductIndex;

    /**
     * Catalog product view page on backend.
     *
     * @var CatalogProductNew
     */
    protected $catalogProductNew;

    /**
     * Prepare data.
     *
     * @param FixtureFactory $fixtureFactory.
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Inject data.
     *
     * @param CatalogProduct $catalogProductIndex
     * @param CatalogProductNew $catalogProductNew
     * @return void
     */
    public function __inject(CatalogProduct $catalogProductIndex, CatalogProductNew $catalogProductNew)
    {
        $this->catalogProductIndex = $catalogProductIndex;
        $this->catalogProductNew = $catalogProductNew;
    }

    /**
     * Get product by data.
     *
     * @param string $productData
     * @param array $relatedProductsData
     * @return InjectableFixture
     */
    protected function getProductByData($productData, array $relatedProductsData)
    {
        list($fixtureName, $dataSet) = explode('::', $productData);
        $relatedProductsPresets = [];
        foreach ($relatedProductsData as $type => $presets) {
            $relatedProductsPresets[$type]['presets'] = $presets;
        }

        return $this->fixtureFactory->createByCode(
            $fixtureName,
            [
                'dataSet' => $dataSet,
                'data' => $relatedProductsPresets
            ]
        );
    }

    /**
     * Create and save product.
     *
     * @param InjectableFixture $product
     * @return void
     */
    protected function createAndSaveProduct(InjectableFixture $product)
    {
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getGridPageActionBlock()->addNew();
        $this->catalogProductNew->getProductForm()->fill($product);
        $this->catalogProductNew->getFormPageActions()->save();
    }
}

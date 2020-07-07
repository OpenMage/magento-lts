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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Category\Edit\Tab;

use Magento\Mtf\Client\Element\SimpleElement as Element;
use Mage\Adminhtml\Test\Block\Widget\Tab;
use Mage\Adminhtml\Test\Block\Catalog\Category\Edit\Tab\Product\Grid;

/**
 * Products grid of Category Products tab.
 */
class Product extends Tab
{
    /**
     * An element locator which allows to select entities in grid.
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .col-in_category';

    /**
     * Product grid locator
     *
     * @var string
     */
    protected $productGrid = '#catalog_category_products';

    /**
     * Fill category products.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        if (!isset($fields['category_products'])) {
            return $this;
        }
        $products = $fields['category_products']['source']->getProducts();
        $this->getProductGrid()->clear();
        foreach ($products as $product) {
            $this->getProductGrid()->searchAndSelect(['sku' => $product->getSku()]);
        }

        return $this;
    }

    /**
     * Returns product grid.
     *
     * @return Grid
     */
    public function getProductGrid()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Catalog\Category\Edit\Tab\Product\Grid',
            ['element' => $this->_rootElement->find($this->productGrid)]
        );
    }

    /**
     * Get data of tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        return ['category_products' => $this->getProducts($fields['category_products']['source']->getProducts())];
    }

    /**
     * Get products.
     *
     * @param array $products
     * @return array
     */
    protected function getProducts(array $products)
    {
        $result = [];
        $productGrid = $this->getProductGrid();
        foreach ($products as $key => $product) {
            if ($productGrid->isSelect(['sku' => $product->getSku()])) {
                $result[$key] = $product->getSku();
            }
        }

        return $result;
    }
}

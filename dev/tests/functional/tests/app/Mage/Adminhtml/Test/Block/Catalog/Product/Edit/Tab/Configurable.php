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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab;

use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Mage\Adminhtml\Test\Block\Widget\Tab;
use Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable\Grid;
use Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable\Attribute;
use Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable\QuickCreation;
use Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable\NewProductPopup;
use Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable\SimpleAssociatedProduct;

/**
 * Product configurable tab.
 */
class Configurable extends Tab
{
    /**
     * Product grid css selector.
     *
     * @var string
     */
    protected $grid = '#super_product_links';

    /**
     * Item attribute form xpath selector.
     *
     * @var string
     */
    protected $itemAttribute = '//li[@class="attribute" and div[normalize-space(text()) = "%s"]]';

    /**
     * Selector for simple form.
     *
     * @var string
     */
    protected $simpleForm = '#configurable_simple_form';

    /**
     * Selector for new product popup.
     *
     * @var string
     */
    protected $newProductPopup = '[id="page:main-container"]';

    /**
     * Selector for 'Create Simple Associated Product' block.
     *
     * @var string
     */
    protected $simpleAssociatedProduct = '.entry-edit fieldset.a-right';

    /**
     * Fill data to fields on tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $this->selectProducts($fields['configurable_options']['value']['products']);
        $this->fillAttributes($fields['configurable_options']['value']['attributes_data']);

        return $this;
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
        return [
            'configurable_options' => [
                'products' => $this->getProducts($fields['configurable_options']['value']['products']),
                'attributes_data' => $this->getAttributes($fields['configurable_options']['value']['attributes_data'])
            ]
        ];
    }

    /**
     * Fill attributes.
     *
     * @param array $attributes
     * @return void
     */
    public function fillAttributes(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->getItemAttributeForm($attribute['attribute_code'])->fillAttribute($attribute);
        }
    }

    /**
     * Get attributes.
     *
     * @param array $attributes
     * @return array
     */
    protected function getAttributes(array $attributes)
    {
        $result = [];
        foreach ($attributes as $key => $attribute) {
            $result[$key] = $this->getItemAttributeForm($attribute['attribute_code'])->getAttribute($attribute);
        }

        return $result;
    }

    /**
     * Select products.
     *
     * @param array $products
     * @return void
     */
    public function selectProducts(array $products)
    {
        $productGrid = $this->getProductGrid();
        foreach ($products as $product) {
            $productGrid->searchAndSelect(['sku' => $product]);
        }
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
            if ($productGrid->isSelect(['sku' => $product])) {
                $result[$key] = $product;
            }
        }

        return $result;
    }

    /**
     * Get product grid.
     *
     * @return Grid
     */
    protected function getProductGrid()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable\Grid',
            ['element' => $this->_rootElement->find($this->grid)]
        );
    }

    /**
     * Get item attribute form.
     *
     * @param string $key
     * @return Attribute
     */
    protected function getItemAttributeForm($key)
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable\Attribute',
            ['element' => $this->_rootElement->find(sprintf($this->itemAttribute, $key), Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Unselect all products.
     *
     * @return void
     */
    public function unselectAllProducts()
    {
        $this->getProductGrid()->unselectAllProducts();
    }

    /**
     * Get quick creation block.
     *
     * @return QuickCreation
     */
    public function getQuickCreationBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable\QuickCreation',
            ['element' => $this->_rootElement->find($this->simpleForm)]
        );
    }

    /**
     * Get new product popup.
     *
     * @return NewProductPopup
     */
    public function getNewProductPopup()
    {
        $this->browser->selectWindow();
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable\NewProductPopup',
            ['element' => $this->browser->find($this->newProductPopup)]
        );
    }

    /**
     * Get simple associated product block.
     *
     * @return SimpleAssociatedProduct
     */
    public function getSimpleAssociatedProductBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable\SimpleAssociatedProduct',
            ['element' => $this->_rootElement->find($this->simpleAssociatedProduct)]
        );
    }
}

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

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Configurable product handler.
 */
class ProductConfigurableHandler extends ProductHandler
{
    /**
     * Get product sku.
     *
     * @param InjectableFixture $product
     * @return string
     */
    public function getProductSku(InjectableFixture $product)
    {
        $options = $this->getCheckoutDataOptions($product, 'configurable_options');
        $productKey = $this->getProductKey($options);
        $products = $this->getAssignedProducts($product);

        return $products[$productKey]->getSku();
    }

    /**
     * Get product options.
     *
     * @param InjectableFixture $product
     * @return array
     */
    public function getProductOptions(InjectableFixture $product)
    {
        $options = $this->getCheckoutDataOptions($product, 'configurable_options');
        $productOptions = $product->getConfigurableOptions()['attributes_data'];
        $configurableOptions = (!empty($options) && !empty($productOptions))
            ? $this->getOptions($options, $productOptions, 'label', false)
            : [];

        return array_merge(parent::getProductOptions($product), $configurableOptions);
    }

    /**
     * Get assigned products.
     *
     * @param InjectableFixture $product
     * @return array
     */
    protected function getAssignedProducts(InjectableFixture $product)
    {
        return $product->getDataFieldConfig('configurable_options')['source']->getProducts();
    }

    /**
     * Get product key.
     *
     * @param array $data
     * @return string
     */
    protected function getProductKey(array $data)
    {
        $key = '';
        foreach ($data as $itemData) {
            $key .= $itemData['title'] . ':' . $itemData['value'] . ' ';
        }

        return trim($key);
    }
}

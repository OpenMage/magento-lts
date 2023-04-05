<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Fixture\ConfigurableProduct\Cart;

use Mage\Catalog\Test\Fixture\ConfigurableProduct;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Data for verify cart item block on checkout page.
 *
 * Data keys:
 *  - product (fixture data for verify)
 *
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Item extends \Mage\Catalog\Test\Fixture\Cart\Item
{

    /**
     * @constructor
     * @param FixtureInterface $product
     */
    public function __construct(FixtureInterface $product)
    {
        parent::__construct($product);

        /** @var ConfigurableProduct $product */
        $checkoutData = $product->getCheckoutData();
        $cartItem = isset($checkoutData['cartItem']) ? $checkoutData['cartItem'] : [];
        $attributesData = $product->getConfigurableOptions()['attributes_data'];
        $checkoutConfigurableOptions = isset($checkoutData['options']['configurable_options'])
            ? $checkoutData['options']['configurable_options']
            : [];

        foreach ($checkoutConfigurableOptions as $key => $checkoutConfigurableOption) {
            $attribute = $checkoutConfigurableOption['title'];
            $option = $checkoutConfigurableOption['value'];

            $checkoutConfigurableOptions[$key] = [
                'title' => isset($attributesData[$attribute]['label'])
                    ? $attributesData[$attribute]['label']
                    : $attribute,
                'value' => isset($attributesData[$attribute]['options'][$option]['label'])
                    ? $attributesData[$attribute]['options'][$option]['label']
                    : $option,
            ];
        }
        $cartItem['options'] = isset($cartItem['options'])
            ? $cartItem['options'] + $checkoutConfigurableOptions
            : $checkoutConfigurableOptions;
        $this->data = array_merge($this->data, $cartItem);
    }
}

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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Bundle\Test\Fixture\Cart;

use Mage\Bundle\Test\Fixture\BundleProduct;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Data for verify cart item block on checkout page.
 *
 * Data keys:
 *  - product (fixture data for verify)
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

        /** @var BundleProduct $product */
        $bundleSelection = $product->getBundleSelections();
        $checkoutData = $product->getCheckoutData();
        $checkoutBundleOptions = isset($checkoutData['options']['bundle_options'])
            ? $checkoutData['options']['bundle_options']
            : [];
        foreach ($checkoutBundleOptions as $checkoutOptionKey => $checkoutOption) {
            // Find option and value keys
            $attributeKey = null;
            $optionKey = null;
            $productKey = null;
            foreach ($bundleSelection as $key => $option) {
                if ($option['title'] == $checkoutOption['title']) {
                    $attributeKey = $key;
                    $optionKey = $checkoutOptionKey;
                }
                foreach ($option['assigned_products'] as $keyProduct => $product) {
                    $productKey = $keyProduct;
                }
            }
            // Prepare option data
            $bundleOptions = $bundleSelection[$attributeKey]['assigned_products'][$productKey];
            $value = $bundleOptions['sku'];
            $qty = isset($checkoutOption['value']['qty'])
                ? $checkoutOption['value']['qty']
                : $bundleOptions['selection_qty'];
            $price = number_format($checkoutData['cartItem']['options']['bundle_options'][$optionKey]['price'], 2);
            $optionData = [
                'title' => $checkoutOption['title'],
                'value' => "{$qty} x {$value} {$price}",
            ];
            $checkoutBundleOptions[$checkoutOptionKey] = $optionData;
        }
        $this->data['options'] += $checkoutBundleOptions;
    }

    /**
     * Persist fixture.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set.
     *
     * @param string $key [optional]
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings.
     *
     * @return string
     */
    public function getDataConfig()
    {
        //
    }
}

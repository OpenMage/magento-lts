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

namespace Mage\Bundle\Test\Constraint;

use Mage\Bundle\Test\Fixture\BundleProduct;
use Mage\Catalog\Test\Constraint\AssertProductPage;

/**
 * Verify displayed product price on product page(front-end) equals passed from fixture.
 */
class AssertBundleProductPage extends AssertProductPage
{
    /**
     * Verify displayed product price on product page(front-end) equals passed from fixture.
     *
     * @return string|null
     */
    protected function verifyPrice()
    {
        $errors = [];
        $priceData = $this->product->getDataFieldConfig('price')['source']->getPriceData();
        $priceBlock = $this->catalogProductView->getBundleViewBlock()->getPriceBlock();
        $priceLow = ($this->product->getPriceView() == 'Price Range')
            ? $priceBlock->getPriceFrom()
            : $priceBlock->getRegularPrice();
        $priceTo = $priceBlock->getPriceTo();

        if ($priceData['price_from'] != $priceLow) {
            $errors[] = "Bundle price 'From' on product view page is not correct:"
                . "\n$priceLow != {$priceData['price_from']}";
        }
        if ($this->product->getPriceView() == 'Price Range' && $priceData['price_to'] != $priceTo) {
            $errors[] = "Bundle price 'To' on product view page is not correct:"
                . "\n$priceTo != {$priceData['price_to']}";
        }

        return empty($errors) ? null : implode("\n", $errors);
    }
}

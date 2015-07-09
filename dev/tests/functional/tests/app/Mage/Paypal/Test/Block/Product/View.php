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

namespace Mage\Paypal\Test\Block\Product;

use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Product view block on the product page for pay pal.
 */
class View extends \Mage\Catalog\Test\Block\Product\View
{
    /**
     * 'Check out with PayPal' button
     *
     * @var string
     */
    protected $paypalCheckout = '[data-action=checkout-form-submit]';

    /**
     * Check out with PayPal.
     *
     * @param InjectableFixture $product
     * @return void
     */
    public function paypalCheckout(InjectableFixture $product)
    {
        /** @var CatalogProductSimple $product */
        $checkoutData = $product->getCheckoutData();
        if (isset($checkoutData['options'])) {
            $this->fillOptions($product);
        }
        if (isset($checkoutData['qty'])) {
            $this->setQty($checkoutData['qty']);
        }
        $this->_rootElement->find($this->paypalCheckout)->click();
    }
}

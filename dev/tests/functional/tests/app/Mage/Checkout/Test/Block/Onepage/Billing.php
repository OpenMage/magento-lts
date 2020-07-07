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

namespace Mage\Checkout\Test\Block\Onepage;

use Mage\Customer\Test\Fixture\Address;
use Mage\Customer\Test\Fixture\Customer;

/**
 * Onepage checkout billing information form.
 */
class Billing extends AbstractOnepage
{
    /**
     * Checkout loader selector.
     *
     * @var string
     */
    protected $waiterSelector = '#billing-please-wait';

    /**
     * Continue checkout button.
     *
     * @var string
     */
    protected $continue = '#billing-buttons-container button';

    /**
     * 'Ship to different address' radio button.
     *
     * @var string
     */
    protected $useForShipping = '[id="billing:use_for_shipping_no"]';

    /**
     * Fill billing address
     *
     * @param Address $billingAddress [optional]
     * @param Customer $customer [optional]
     * @param bool $isShippingAddress [optional]
     * @return void
     */
    public function fillBilling(Address $billingAddress = null, Customer $customer = null, $isShippingAddress = false)
    {
        if ($billingAddress) {
            $this->fill($billingAddress);
        }
        if ($customer) {
            if($this->browser->find('[id=\'billing:email\']')->isVisible() && ($customer->getData('email') != null)) {
                $this->fill($customer);
            }
        }
        if ($isShippingAddress) {
            $this->_rootElement->find($this->useForShipping)->click();
        }
    }
}

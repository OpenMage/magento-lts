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

namespace Mage\Checkout\Test\Constraint;

/**
 * Assert that shipping address on order page in backend is same as billing address in PayPal.
 */
class AssertOrderShippingAddressSameAsPaypalBilling extends AssertAbstractOrderAddressSameAsPaypal
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Address type.
     *
     * @var string
     */
    protected $addressType = 'Shipping';

    /**
     * Returns string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return 'Shipping address on order page in backend is same as billing address in PayPal';
    }
}

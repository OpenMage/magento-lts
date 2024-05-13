<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Constraint;

/**
 * Assert that billing address on order page in backend is same as shipping address in PayPal.
 */
class AssertOrderBillingAddressSameAsPaypalShipping extends AssertAbstractOrderAddressSameAsPaypal
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Address type.
     *
     * @var string
     */
    protected $addressType = 'Billing';

    /**
     * Returns string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return 'Billing address on order page in backend is same as shipping address in PayPal';
    }
}

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

namespace Mage\Adminhtml\Test\Block\Sales\Order\Create\Billing;

use Magento\Mtf\Block\Form;

/**
 * Adminhtml sales order billing address form.
 */
class Address extends Form
{
    /**
     * Existed addresses selector.
     *
     * @var string
     */
    protected $existedAddresses = '#order-billing_address_customer_address_id';

    /**
     * Get existing addresses.
     *
     * @return string
     */
    public function getExistingAddresses()
    {
        return explode("\n", $this->_rootElement->find($this->existedAddresses)->getText());
    }
}

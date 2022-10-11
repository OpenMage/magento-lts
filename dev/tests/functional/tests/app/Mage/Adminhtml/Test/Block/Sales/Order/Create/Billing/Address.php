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

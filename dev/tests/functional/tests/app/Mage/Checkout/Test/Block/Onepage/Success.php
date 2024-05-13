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

namespace Mage\Checkout\Test\Block\Onepage;

use Magento\Mtf\Client\Locator;
use Magento\Mtf\Block\Block;

/**
 * One page checkout success block.
 */
class Success extends Block
{
    /**
     * Determine order id if checkout was performed by guest.
     *
     * @var string
     */
    protected $orderIdGuest = '//div[contains(@class, "col-main")]//p[1][contains(text(), "Your order")]';

    /**
     * Determine order id if checkout was performed by registered customer.
     *
     * @var string
     */
    protected $orderId = 'a[href*="view/order_id"]';

    /**
     * Get Id of placed order for guest checkout.
     *
     * @return string
     */
    public function getGuestOrderId()
    {
        $this->waitForElementVisible($this->orderIdGuest, Locator::SELECTOR_XPATH);
        $orderString = $this->_rootElement->find($this->orderIdGuest, Locator::SELECTOR_XPATH)->getText();
        preg_match('/[\d]+/', $orderString, $orderId);
        return end($orderId);
    }

    /**
     * Click order id link.
     *
     * @return void
     */
    public function openOrder()
    {
        $this->_rootElement->find($this->orderId, Locator::SELECTOR_CSS)->click();
    }
}

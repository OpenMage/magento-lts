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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Sales\Order\View\Tab;

use Mage\Adminhtml\Test\Block\Sales\Order\Address;
use Mage\Adminhtml\Test\Block\Widget\Tab;
use Mage\Adminhtml\Test\Block\Sales\Order\Totals;
use Mage\Adminhtml\Test\Block\Sales\Order\View\Tab\Info as OrderInformationBlock;
use Mage\Adminhtml\Test\Block\Sales\Order\Comments;
use Magento\Mtf\Client\Locator;

/**
 * Order information tab.
 */
class Info extends Tab
{
    /**
     * Order information block.
     *
     * @var string
     */
    protected $orderInfoBlock = '#sales_order_view_tabs_order_info_content';

    /**
     * Order totals block.
     *
     * @var string
     */
    protected $orderTotalsBlock = '.order-totals';

    /**
     * Order status selector.
     *
     * @var string
     */
    protected $orderStatus = '#order_status';

    /**
     * Comments block selector.
     *
     * @var string
     */
    protected $commentsBlock = '#order_history_block';

    /**
     * Billing address block selector.
     *
     * @var string
     */
    protected $billingAddressBlock = '//*[contains(@class, "billing-address")]/ancestor::div[@class="entry-edit"][1]';

    /**
     * Shipping address block selector.
     *
     * @var string
     */
    protected $shippingAddressBlock = '//*[contains(@class, "shipping-address")]/ancestor::div[@class="entry-edit"][1]';

    /**
     * Get order information block.
     *
     * @return OrderInformationBlock
     */
    public function getOrderInfoBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\View\Tab\Info',
            ['element' => $this->_rootElement->find($this->orderInfoBlock)]
        );
    }

    /**
     * Get order totals block.
     *
     * @return Totals
     */
    public function getOrderTotalsBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Totals',
            ['element' => $this->_rootElement->find($this->orderTotalsBlock)]
        );
    }

    /**
     * Get order status from info block.
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->_rootElement->find($this->orderStatus)->getText();
    }

    /**
     * Get comments block.
     *
     * @return Comments
     */
    public function getCommentsBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Comments',
            ['element' => $this->_rootElement->find($this->commentsBlock)]
        );
    }

    /**
     * Get billing address block.
     *
     * @return Address
     */
    public function getBillingAddressBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Address',
            ['element' => $this->_rootElement->find($this->billingAddressBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get shipping address block.
     *
     * @return Address
     */
    public function getShippingAddressBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Address',
            ['element' => $this->_rootElement->find($this->shippingAddressBlock, Locator::SELECTOR_XPATH)]
        );
    }
}

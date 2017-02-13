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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Sales\Order;

use Magento\Mtf\Block\Block;

/**
 * Order actions block.
 */
class Actions extends Block
{
    /**
     * General button selector.
     *
     * @var string
     */
    protected $button = 'button[title="%s"]';

    /**
     * 'Invoice' button.
     *
     * @var string
     */
    protected $orderInvoiceButton = '[onclick*="sales_order_invoice"]';

    /**
     * 'Ship' button.
     *
     * @var string
     */
    protected $orderShipButton = '[onclick*="sales_order_shipment"]';

    /**
     * 'Credit Memo' button on the order page.
     *
     * @var string
     */
    protected $orderCreditMemoButton = '[onclick*="sales_order_creditmemo"]';

    /**
     * Cancel button on the order page.
     *
     * @var string
     */
    protected $orderCancelButton = '[onclick*="sales_order/cancel"]';

    /**
     * Reorder button on the order page.
     *
     * @var string
     */
    protected $reorderButton = '[onclick*="sales_order_create/reorder/order_id"]';

    /**
     * Check if action button is visible.
     *
     * @param string $buttonName
     * @return bool
     */
    public function isActionButtonVisible($buttonName)
    {
        return $this->_rootElement->find(sprintf($this->button, $buttonName))->isVisible();
    }

    /**
     * Invoice order.
     *
     * @return void
     */
    public function invoice()
    {
        $this->_rootElement->find($this->orderInvoiceButton)->click();
    }

    /**
     * Ship order.
     *
     * @return void
     */
    public function shipment()
    {
        $this->_rootElement->find($this->orderShipButton)->click();
    }

    /**
     * Order credit memo.
     *
     * @return void
     */
    public function refund()
    {
        $this->_rootElement->find($this->orderCreditMemoButton)->click();
    }

    /**
     * Cancel order.
     *
     * @return void
     */
    public function cancel()
    {
        $this->_rootElement->find($this->orderCancelButton)->click();
        $this->browser->acceptAlert();
    }

    /**
     * Reorder order.
     *
     * @return void
     */
    public function reorder()
    {
        $this->_rootElement->find($this->reorderButton)->click();
    }
}

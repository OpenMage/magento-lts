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

namespace Mage\Adminhtml\Test\Block\Sales\Order;

use Mage\Adminhtml\Test\Block\Sales\Order\Create\Coupons;
use Mage\Adminhtml\Test\Block\Template;
use Mage\Adminhtml\Test\Block\Sales\Order\Create\Items;
use Mage\Adminhtml\Test\Block\Sales\Order\Create\Search;
use Mage\Adminhtml\Test\Block\Sales\Order\Create\Payment;
use Mage\Adminhtml\Test\Block\Sales\Order\Create\Shipping;
use Mage\Adminhtml\Test\Block\Sales\Order\Create\Billing\Address;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Block\Block;

/**
 * Adminhtml sales order create block.
 */
class Create extends Block
{
    /**
     * Items block selector.
     *
     * @var string
     */
    protected $itemsBlock = '#order-items';

    /**
     * Backend abstract block.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Products grid selector.
     *
     * @var string
     */
    protected $searchGrid = '#order-search';

    /**
     * Billing address block selector.
     *
     * @var string
     */
    protected $billingAddress = '#order-billing_address';

    /**
     * Payment block selector.
     *
     * @var string
     */
    protected $payment = '#order-billing_method';

    /**
     * Shipping block selector.
     *
     * @var string
     */
    protected $shipping = '#order-shipping_method';

    /**
     * 'Submit Order' button.
     *
     * @var string
     */
    protected $submitOrder = '.order-totals-bottom button';

    /**
     * Coupons block selector.
     *
     * @var string
     */
    protected $coupons = '#order-coupons';

    /**
     * Getter for order selected products grid.
     *
     * @return Items
     */
    public function getItemsBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Create\Items',
            ['element' => $this->_rootElement->find($this->itemsBlock)]
        );
    }

    /**
     * Get backend abstract block.
     *
     * @return Template
     */
    public function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get products search block.
     *
     * @return Search
     */
    public function getSearchBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Create\Search',
            ['element' => $this->_rootElement->find($this->searchGrid)]
        );
    }

    /**
     * Get Billing address block.
     *
     * @return Address
     */
    public function getBillingAddressForm()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Create\Billing\Address',
            ['element' => $this->_rootElement->find($this->billingAddress)]
        );
    }

    /**
     * Get payment block.
     *
     * @return Payment
     */
    public function getPaymentBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Create\Payment',
            ['element' => $this->_rootElement->find($this->payment)]
        );
    }

    /**
     * Get shipping block.
     *
     * @return Shipping
     */
    public function getShippingBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Create\Shipping',
            ['element' => $this->_rootElement->find($this->shipping)]
        );
    }

    /**
     * Click 'Submit Order' button.
     *
     * @return void
     */
    public function submitOrder()
    {
        $this->_rootElement->find($this->submitOrder)->click();
    }

    /**
     * Get coupons block.
     *
     * @return Coupons
     */
    public function getCouponsBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Create\Coupons',
            ['element' => $this->_rootElement->find($this->coupons)]
        );
    }
}

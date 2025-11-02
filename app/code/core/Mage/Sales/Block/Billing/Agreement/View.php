<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Customer account billing agreement view block
 *
 * @package    Mage_Sales
 *
 * @method $this setAgreementCreatedAt(string $value)
 * @method $this setAgreementStatus(string $value)
 * @method $this setAgreementUpdatedAt(string $value)
 * @method $this setBackUrl(string $value)
 * @method $this setCanCancel(bool $value)
 * @method $this setCancelUrl(string $value)
 * @method $this setPaymentMethodTitle(string $value)
 * @method $this setReferenceId(string $value)
 */
class Mage_Sales_Block_Billing_Agreement_View extends Mage_Core_Block_Template
{
    /**
     * Payment methods array
     *
     * @var array
     */
    protected $_paymentMethods = [];

    /**
     * Billing Agreement instance
     *
     * @var null|Mage_Sales_Model_Billing_Agreement
     */
    protected $_billingAgreementInstance = null;

    /**
     * Related orders collection
     *
     * @var null|Mage_Sales_Model_Resource_Order_Collection
     */
    protected $_relatedOrders = null;

    /**
     * Retrieve related orders collection
     *
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function getRelatedOrders()
    {
        if (is_null($this->_relatedOrders)) {
            $this->_relatedOrders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->addFieldToFilter(
                    'state',
                    ['in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()],
                )
                ->addBillingAgreementsFilter($this->_billingAgreementInstance->getAgreementId())
                ->setOrder('created_at', 'desc');
        }

        return $this->_relatedOrders;
    }

    /**
     * Retrieve order item value by key
     *
     * @param string $key
     * @return string
     */
    public function getOrderItemValue(Mage_Sales_Model_Order $order, $key)
    {
        $escape = true;
        switch ($key) {
            case 'order_increment_id':
                $value = $order->getIncrementId();
                break;
            case 'created_at':
                $value = $this->formatDate($order->getCreatedAt(), 'short', true);
                break;
            case 'shipping_address':
                $value = $order->getShippingAddress()
                    ? $this->escapeHtml($order->getShippingAddress()->getName()) : $this->__('N/A');
                break;
            case 'order_total':
                $value = $order->formatPrice($order->getGrandTotal());
                $escape = false;
                break;
            case 'status_label':
                $value = $order->getStatusLabel();
                break;
            case 'view_url':
                $value = $this->getUrl('*/order/view', ['order_id' => $order->getId()]);
                break;
            default:
                $value = ($order->getData($key)) ?: $this->__('N/A');
        }

        return ($escape) ? $this->escapeHtml($value) : $value;
    }

    /**
     * Set pager
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        if (is_null($this->_billingAgreementInstance)) {
            $this->_billingAgreementInstance = Mage::registry('current_billing_agreement');
        }

        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager')
            ->setCollection($this->getRelatedOrders())->setIsOutputRequired(false);
        $this->setChild('pager', $pager);
        $this->getRelatedOrders()->load();

        return $this;
    }

    /**
     * Load available billing agreement methods
     *
     * @return array
     */
    protected function _loadPaymentMethods()
    {
        if (!$this->_paymentMethods) {
            /** @var Mage_Payment_Helper_Data $helper */
            $helper = $this->helper('payment');
            foreach ($helper->getBillingAgreementMethods() as $paymentMethod) {
                $this->_paymentMethods[$paymentMethod->getCode()] = $paymentMethod->getTitle();
            }
        }

        return $this->_paymentMethods;
    }

    /**
     * Set data to block
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_loadPaymentMethods();
        $this->setBackUrl($this->getUrl('*/billing_agreement/'));
        if ($this->_billingAgreementInstance) {
            $this->setReferenceId($this->_billingAgreementInstance->getReferenceId());
            $this->setCanCancel($this->_billingAgreementInstance->canCancel());
            $this->setCancelUrl(
                $this->getUrl('*/billing_agreement/cancel', [
                    '_current' => true,
                    'payment_method' => $this->_billingAgreementInstance->getMethodCode()]),
            );

            $paymentMethodTitle = $this->_billingAgreementInstance->getAgreementLabel();
            $this->setPaymentMethodTitle($paymentMethodTitle);

            $createdAt = $this->_billingAgreementInstance->getCreatedAt();
            $updatedAt = $this->_billingAgreementInstance->getUpdatedAt();
            $this->setAgreementCreatedAt(
                ($createdAt) ? $this->formatDate($createdAt, 'short', true) : $this->__('N/A'),
            );
            if ($updatedAt) {
                $this->setAgreementUpdatedAt($this->formatDate($updatedAt, 'short', true));
            }

            $this->setAgreementStatus($this->_billingAgreementInstance->getStatusLabel());
        }

        return parent::_toHtml();
    }
}

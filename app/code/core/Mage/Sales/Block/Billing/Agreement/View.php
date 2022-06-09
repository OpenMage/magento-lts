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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account billing agreement view block
 *
 * @author Magento Core Team <core@magentocommerce.com>
 *
 * @method $this setAgreementCreatedAt(string $value)
 * @method $this setAgreementUpdatedAt(string $value)
 * @method $this setAgreementStatus(string $value)
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
    protected $_paymentMethods = array();

    /**
     * Billing Agreement instance
     *
     * @var Mage_Sales_Model_Billing_Agreement
     */
    protected $_billingAgreementInstance = null;

    /**
     * Related orders collection
     *
     * @var Mage_Sales_Model_Mysql4_Order_Collection
     */
    protected $_relatedOrders = null;

    /**
     * Retrieve related orders collection
     *
     * @return Mage_Sales_Model_Mysql4_Order_Collection
     */
    public function getRelatedOrders()
    {
        if (is_null($this->_relatedOrders)) {
            $this->_relatedOrders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->addFieldToFilter(
                    'state',
                    array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates())
                )
                ->addBillingAgreementsFilter($this->_billingAgreementInstance->getAgreementId())
                ->setOrder('created_at', 'desc');
        }
        return $this->_relatedOrders;
    }

    /**
     * Retrieve order item value by key
     *
     * @param Mage_Sales_Model_Order $order
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
                $value = $this->helper('core')->formatDate($order->getCreatedAt(), 'short', true);
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
                $value = $this->getUrl('*/order/view', array('order_id' => $order->getId()));
                break;
            default:
                $value = ($order->getData($key)) ? $order->getData($key) : $this->__('N/A');
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
            foreach ($this->helper('payment')->getBillingAgreementMethods() as $paymentMethod) {
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
                $this->getUrl('*/billing_agreement/cancel', array(
                    '_current' => true,
                    'payment_method' => $this->_billingAgreementInstance->getMethodCode()))
            );

            $paymentMethodTitle = $this->_billingAgreementInstance->getAgreementLabel();
            $this->setPaymentMethodTitle($paymentMethodTitle);

            $createdAt = $this->_billingAgreementInstance->getCreatedAt();
            $updatedAt = $this->_billingAgreementInstance->getUpdatedAt();
            $this->setAgreementCreatedAt(
                ($createdAt) ? $this->helper('core')->formatDate($createdAt, 'short', true) : $this->__('N/A')
            );
            if ($updatedAt) {
                $this->setAgreementUpdatedAt(
                    $this->helper('core')->formatDate($updatedAt, 'short', true)
                );
            }
            $this->setAgreementStatus($this->_billingAgreementInstance->getStatusLabel());
        }

        return parent::_toHtml();
    }
}

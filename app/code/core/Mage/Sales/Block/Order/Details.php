<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales order details block
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Order_Details extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/details.phtml');
        $this->setOrder(Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id')));
        Mage::registry('action')->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('Order Details'));
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return Mage::getUrl('*/*/history');
    }

    /**
     * @return mixed
     */
    public function getInvoices()
    {
        return Mage::getResourceModel('sales/invoice_collection')->setOrderFilter($this->getOrder()->getId())->load();
    }

    /**
     * @return string
     */
    public function getPrintUrl()
    {
        return Mage::getUrl('*/*/print');
    }
}

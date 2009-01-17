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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales order view block
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Reorder_Sidebar extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
    	    $this->setTemplate('sales/order/history.phtml');

            $orders = Mage::getResourceModel('sales/order_collection')
                ->addAttributeToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->addAttributeToSort('created_at', 'desc')
                ->setPage(1,1);
            //TODO: add filter by current website

            $this->setOrders($orders);

        }
    }

    public function getLastOrder()
    {
        foreach ($this->getOrders() as $order) {
            return $order;
        }
        return false;
    }

    protected function _toHtml()
    {
        if (Mage::helper('sales/reorder')->isAllow() && Mage::getSingleton('customer/session')->isLoggedIn()) {
            return parent::_toHtml();
        }
        return '';
    }
}
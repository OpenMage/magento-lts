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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order view tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Retrieve available order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->hasOrder()) {
            return $this->getData('order');
        }
        if (Mage::registry('current_order')) {
            return Mage::registry('current_order');
        }
        if (Mage::registry('order')) {
            return Mage::registry('order');
        }
        Mage::throwException(Mage::helper('sales')->__('Can\'t get order instance'));
    }

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_view_tabs');
        $this->setDestElementId('sales_order_view');
        $this->setTitle(Mage::helper('sales')->__('Order View'));
    }

    protected function _beforeToHtml()
    {
//        $this->addTab('order_info', array(
//            'label'     => Mage::helper('sales')->__('Information'),
//            'title'     => Mage::helper('sales')->__('Order Information'),
//            'content'   => $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_info')->toHtml(),
//            'active'    => true
//        ));
//
//        $this->addTab('order_invoices', array(
//            'label'     => Mage::helper('catalogrule')->__('Invoices'),
//            'title'     => Mage::helper('catalogrule')->__('Order Invoices'),
//            'content'   => $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_invoices')->toHtml(),
//        ));
//
//        $this->addTab('order_creditmemos', array(
//            'label'     => Mage::helper('catalogrule')->__('Credit Memos'),
//            'title'     => Mage::helper('catalogrule')->__('Order Credit Memos'),
//            'content'   => $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_creditmemos')->toHtml(),
//        ));
//
//        if (!$this->getOrder()->getIsVirtual()) {
//            $this->addTab('order_shipments', array(
//                'label'     => Mage::helper('catalogrule')->__('Shipments'),
//                'title'     => Mage::helper('catalogrule')->__('Order Shipments'),
//                'content'   => $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_shipments')->toHtml(),
//            ));
//        }
//
//        /*$this->addTab('order_giftmessages', array(
//            'label'     => Mage::helper('catalogrule')->__('Gift Messages'),
//            'title'     => Mage::helper('catalogrule')->__('Order Gift Messages'),
//            'content'   => 'Gift Messages',
//        ));*/
//
//        $this->addTab('order_history', array(
//            'label'     => Mage::helper('catalogrule')->__('Comments History'),
//            'title'     => Mage::helper('catalogrule')->__('Order History'),
//            'content'   => $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_history')->toHtml(),
//        ));
        return parent::_beforeToHtml();
    }
}
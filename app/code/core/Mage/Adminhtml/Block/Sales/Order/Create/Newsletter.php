<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order create newsletter block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Newsletter extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_newsletter');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Newsletter Subscription');
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-newsletter';
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!Mage::getSingleton('adminhtml/quote')->getIsOldCustomer()) {
            return parent::_toHtml();
        }
        return '';
    }
}

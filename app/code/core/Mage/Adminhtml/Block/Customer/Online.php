<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml online customers page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Online extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customer/online.phtml');
    }

    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/customer_online_grid', 'customer.grid'));
        return parent::_beforeToHtml();
    }

    protected function _prepareLayout()
    {
        $this->setChild('filterForm', $this->getLayout()->createBlock('adminhtml/customer_online_filter'));
        return parent::_prepareLayout();
    }

    public function getFilterFormHtml()
    {
        return $this->getChild('filterForm')->toHtml();
    }
}

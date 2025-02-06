<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales invoices block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Invoice extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'sales_invoice';
        $this->_headerText = Mage::helper('sales')->__('Invoices');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }
}

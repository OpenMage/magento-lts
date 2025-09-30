<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales invoices block
 *
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

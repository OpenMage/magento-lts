<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales transactions block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Transactions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'sales_transactions';
        $this->_headerText = Mage::helper('sales')->__('Transactions');
        parent::__construct();
        $this->_removeButton('add');
    }
}

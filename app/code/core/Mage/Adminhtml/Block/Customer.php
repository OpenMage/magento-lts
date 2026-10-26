<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customers list block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'customer';
        $this->_headerText = Mage::helper('customer')->__('Manage Customers');
        $this->_addButtonLabel = Mage::helper('customer')->__('Add New Customer');
        parent::__construct();
    }
}

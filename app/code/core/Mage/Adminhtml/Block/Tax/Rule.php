<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Admin tax rule content block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tax_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller      = 'tax_rule';
        $this->_headerText      = Mage::helper('tax')->__('Manage Tax Rules');
        $this->_addButtonLabel  = Mage::helper('tax')->__('Add New Tax Rule');
        parent::__construct();
    }
}

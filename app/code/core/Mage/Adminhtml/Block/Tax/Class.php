<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Admin tax class content block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tax_Class extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller      = 'tax_class';
        parent::__construct();
    }

    public function setClassType($classType)
    {
        if ($classType == Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT) {
            $this->_headerText      = Mage::helper('tax')->__('Product Tax Classes');
            $this->_addButtonLabel  = Mage::helper('tax')->__('Add New Class');
        } elseif ($classType == Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER) {
            $this->_headerText      = Mage::helper('tax')->__('Customer Tax Classes');
            $this->_addButtonLabel  = Mage::helper('tax')->__('Add New Class');
        }

        $this->getChild('grid')->setClassType($classType);
        $this->setData('class_type', $classType);

        return $this;
    }
}
